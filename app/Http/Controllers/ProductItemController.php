<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProductItemRequest;
use App\Http\Resources\ProductItemResource;
use App\Models\Product;
use App\Models\ProductItem;
use App\Services\SecureSellerService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ProductItemController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly SecureSellerService $secureSellerService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', ProductItem::class);

        return ProductItemResource::collection(ProductItem::all());
    }

    public function store(ProductItemRequest $request): ProductItemResource
    {
        $this->authorize('create', ProductItem::class);

        return new ProductItemResource(ProductItem::query()->create($request->validated()));
    }

    public function show(ProductItem $productItem): ProductItemResource
    {
        $this->authorize('view', $productItem);

        return new ProductItemResource($productItem);
    }

    public function update(ProductItemRequest $request, ProductItem $productItem): ProductItemResource
    {
        $this->authorize('update', $productItem);

        $productItem->update($request->validated());

        return new ProductItemResource($productItem);
    }

    public function getProductsItems(): JsonResponse
    {
        try {
            $productsItems = $this->secureSellerService->getProductItems();

            return response()->json([
                'success' => true,
                'producstItems' => $productsItems,
                'count' => count($productsItems),
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
            $productsItems = $this->secureSellerService->getProductItems();

            $synced = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($productsItems as $data) {

                $product = Product::query()
                    ->where('ProductID', $data['ProductID'])
                    ->withoutGlobalScope('user_access')
                    ->first();
                $existingProductsItems = ProductItem::query()
                    ->withoutGlobalScope('user_access')
                    ->where('ItemID', $data['ItemID'])
                    ->first();

                if ($existingProductsItems) {
                    $existingProductsItems->fill([
                        'product_id' => $product?->id,
                        'ItemID' => $data['ItemID'],
                        'ProductName' => $data['ProductName'],
                        'SKU' => $data['SKU'],
                        'Quantity' => $data['Quantity'],
                        'upSell' => $data['upSell'],
                        'active' => $data['active'],
                        'offerProducts' => $data['offerProducts'],
                        'extraProduct' => $data['extraProduct'],
                    ]);

                    if ($existingProductsItems->isDirty()) {
                        $existingProductsItems->save();
                        $updated++;
                    }
                } else {
                    ProductItem::query()->create([
                        'product_id' => $product?->id,
                        'ItemID' => $data['ItemID'],
                        'ProductName' => $data['ProductName'],
                        'SKU' => $data['SKU'],
                        'Quantity' => $data['Quantity'],
                        'upSell' => $data['upSell'],
                        'active' => $data['active'],
                        'offerProducts' => $data['offerProducts'],
                        'extraProduct' => $data['extraProduct'],
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

    public function destroy(ProductItem $productItem): JsonResponse
    {
        $this->authorize('delete', $productItem);

        $productItem->delete();

        return response()->json();
    }
}
