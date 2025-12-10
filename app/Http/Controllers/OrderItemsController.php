<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\OrderItemsRequest;
use App\Http\Resources\OrderItemsResource;
use App\Models\OrderItems;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class OrderItemsController extends Controller
{
    use AuthorizesRequests;

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', OrderItems::class);

        return OrderItemsResource::collection(OrderItems::all());
    }

    public function store(OrderItemsRequest $request): OrderItemsResource
    {
        $this->authorize('create', OrderItems::class);

        return new OrderItemsResource(OrderItems::query()->create($request->validated()));
    }

    public function show(OrderItems $orderItems): OrderItemsResource
    {
        $this->authorize('view', $orderItems);

        return new OrderItemsResource($orderItems);
    }

    public function update(OrderItemsRequest $request, OrderItems $orderItems): OrderItemsResource
    {
        $this->authorize('update', $orderItems);

        $orderItems->update($request->validated());

        return new OrderItemsResource($orderItems);
    }

    public function destroy(OrderItems $orderItems): JsonResponse
    {
        $this->authorize('delete', $orderItems);

        $orderItems->delete();

        return response()->json();
    }
}
