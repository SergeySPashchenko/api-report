<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\BrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Services\SecureSellerService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class BrandController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private SecureSellerService $secureSellerService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Brand::class);

        return BrandResource::collection(Brand::all());
    }

    public function store(BrandRequest $request): BrandResource
    {
        $this->authorize('create', Brand::class);

        return new BrandResource(Brand::query()->create($request->validated()));
    }

    public function show(Brand $brand): BrandResource
    {
        $this->authorize('view', $brand);

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
