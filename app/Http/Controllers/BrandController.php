<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\BrandRequest;
use App\Http\Resources\BrandResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Services\SecureSellerService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

final class BrandController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private SecureSellerService $secureSellerService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Brand::class);

        $query = Brand::query()
            ->withCount('products')
            ->withSum(['expenses as expenses_yesterday' => fn (Builder $q) => $q->whereDate('ExpenseDate', Date::yesterday())], 'Expense')
            ->withSum(['expenses as expenses_week' => fn (Builder $q) => $q->whereBetween('ExpenseDate', [Date::now()->startOfWeek(), Date::now()->endOfWeek()])], 'Expense')
            ->withSum(['expenses as expenses_month' => fn (Builder $q) => $q->whereMonth('ExpenseDate', Date::now()->month)->whereYear('ExpenseDate', Date::now()->year)], 'Expense');

        return BrandResource::collection($query->paginate());
    }

    public function store(BrandRequest $request): BrandResource
    {
        $this->authorize('create', Brand::class);

        return new BrandResource(Brand::query()->create($request->validated()));
    }

    public function show(Brand $brand): BrandResource
    {
        $this->authorize('view', $brand);

        $brand->loadCount('products')
            ->loadSum(['expenses as expenses_yesterday' => fn (Builder $q) => $q->whereDate('ExpenseDate', Date::yesterday())], 'Expense')
            ->loadSum(['expenses as expenses_week' => fn (Builder $q) => $q->whereBetween('ExpenseDate', [Date::now()->startOfWeek(), Date::now()->endOfWeek()])], 'Expense')
            ->loadSum(['expenses as expenses_month' => fn (Builder $q) => $q->whereMonth('ExpenseDate', Date::now()->month)->whereYear('ExpenseDate', Date::now()->year)], 'Expense');

        return new BrandResource($brand);
    }

    public function update(BrandRequest $request, Brand $brand): BrandResource
    {
        $this->authorize('update', $brand);

        $brand->update($request->validated());

        return new BrandResource($brand);
    }

    public function getBrands(): JsonResponse
    {
        try {
            $brands = $this->secureSellerService->getBrands();

            return response()->json([
                'success' => true,
                'brands' => $brands,
                'count' => count($brands),
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch brands',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function syncBrands(): JsonResponse
    {
        try {
            /** @var array<int, array{Brand?: string}> $brands */
            $brands = $this->secureSellerService->getBrands();

            $synced = 0;
            $skipped = 0;

            // Extract names and normalize
            $brandNames = collect($brands)
                ->pluck('Brand')
                ->map(function ($name): string {
                    $name = is_string($name) ? $name : '';

                    return $name === ''
                        ? 'Empty'
                        : Str::title($name);
                })
                ->unique()
                ->filter()
                ->values();

            // Find existing brands
            $existingBrands = Brand::query()
                ->whereIn('name', $brandNames)
                ->pluck('name')
                ->map(fn ($name): string => is_string($name) ? $name : '')
                ->filter()
                ->flip();

            // Filter new brands
            $newBrands = $brandNames->reject(fn ($name): bool => isset($existingBrands[$name]));

            foreach ($newBrands as $brandName) {
                // We use create instead of updateOrCreate because we filtered existing ones
                // This triggers model events (slug generation)
                Brand::query()->create(['name' => $brandName]);
                $synced++;
            }

            // Count "skipped" as existing ones (technically we didn't touch them)
            $skipped = $existingBrands->count();

            return response()->json([
                'success' => true,
                'message' => 'Brands synced successfully',
                'synced' => $synced,
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

    public function brandProducts(Brand $brand): AnonymousResourceCollection
    {
        $this->authorize('view', $brand);

        return ProductResource::collection(
            $brand->products()->paginate()
        );
    }

    public function destroy(Brand $brand): JsonResponse
    {
        $this->authorize('delete', $brand);

        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Brand deleted successfully',
        ]);
    }
}
