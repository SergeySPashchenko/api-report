<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ExpensesResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Product;
use App\Services\SecureSellerService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

final class ProductController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly SecureSellerService $secureSellerService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Product::class);

        $query = Product::query()
            ->withCount(['expenses', 'orders'])
            ->with('brand')
            ->latest();

        return ProductResource::collection($query->paginate(15));
    }

    public function store(ProductRequest $request): ProductResource
    {
        $this->authorize('create', Product::class);

        $validated = $request->validated();

        // Генеруємо унікальний slug
        $validated['slug'] = $this->generateUniqueSlug(
            Str::slug(is_string($validated['ProductName']) ? $validated['ProductName'] : '')
        );

        return new ProductResource(Product::query()->create($validated));
    }

    public function show(Product $product): ProductResource
    {
        $this->authorize('view', $product);

        $product->load('brand')
            ->loadCount(['expenses', 'orders']);

        return new ProductResource($product);
    }

    public function update(ProductRequest $request, Product $product): ProductResource
    {
        $this->authorize('update', $product);

        $product->update($request->validated());

        return new ProductResource($product);
    }

    public function getProducts(): JsonResponse
    {
        try {
            $products = $this->secureSellerService->getProducts();

            return response()->json([
                'success' => true,
                'products' => $products,
                'count' => count($products),
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function syncProducts(): JsonResponse
    {
        try {
            /** @var array<int, array<string, mixed>> $products */
            $products = $this->secureSellerService->getProducts();

            $synced = 0;
            $updated = 0;
            $skipped = 0;

            $brandNames = collect($products)
                ->pluck('Brand')
                ->map(fn ($name) => empty($name) ? 'Empty' : $name)
                ->unique()
                ->toArray();

            $brandMap = Brand::query()
                ->withoutGlobalScope('user_access')
                ->whereIn('slug', array_map(fn ($name) => Str::slug(is_string($name) ? $name : ''), $brandNames))
                ->pluck('id', 'slug')
                ->toArray();

            foreach ($products as $productData) {
                if (empty($productData['Brand'])) {
                    $productData['Brand'] = 'Empty';
                }

                $brandSlug = Str::slug(is_string($productData['Brand']) ? $productData['Brand'] : '');

                if (! isset($brandMap[$brandSlug])) {
                    $skipped++;

                    continue;
                }

                $brandId = $brandMap[$brandSlug];

                $existingProduct = Product::query()
                    ->withoutGlobalScope('user_access')
                    ->where('ProductID', $productData['ProductID'])
                    ->first();

                if ($existingProduct) {
                    $existingProduct->fill([
                        'brand_id' => $brandId,
                        'Product' => $productData['Product'],
                        'ProductName' => $productData['ProductName'],
                        'newSystem' => $productData['newSystem'],
                        'Visible' => $productData['Visible'],
                        'flyer' => $productData['flyer'],
                    ]);

                    if ($existingProduct->isDirty()) {
                        $existingProduct->save();
                        $updated++;
                    }
                } else {
                    // Створюємо новий з унікальним slug
                    $baseSlug = Str::slug(is_string($productData['ProductName']) ? $productData['ProductName'] : '');
                    $uniqueSlug = $this->generateUniqueSlug($baseSlug);

                    Product::query()->create([
                        'ProductID' => $productData['ProductID'],
                        'brand_id' => $brandId,
                        'Product' => $productData['Product'],
                        'ProductName' => $productData['ProductName'],
                        'newSystem' => $productData['newSystem'],
                        'Visible' => $productData['Visible'],
                        'flyer' => $productData['flyer'],
                    ]);
                    $synced++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Products synced successfully',
                'created' => $synced,
                'updated' => $updated,
                'skipped' => $skipped,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $product->delete();

        return response()->json();
    }

    public function productExpenses(Product $product): AnonymousResourceCollection
    {
        $this->authorize('view', $product);

        $expenses = $product->expenses()->orderByDesc('ExpenseDate')->paginate();
        $totalSum = $product->expenses()->sum('Expense');

        return ExpensesResource::collection($expenses)->additional([
            'total_sum' => $totalSum,
        ]);
    }

    private function generateUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while (Product::query()
            ->withoutGlobalScope('user_access')
            ->where('slug', $slug)
            ->exists()) {
            $slug = sprintf('%s-%d', $baseSlug, $counter);
            $counter++;
        }

        return $slug;
    }
}
