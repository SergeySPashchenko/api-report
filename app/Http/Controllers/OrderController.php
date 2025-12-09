<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\SecureSellerService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class OrderController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private SecureSellerService $secureSellerService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Order::class);

        return OrderResource::collection(Order::all());
    }

    public function store(OrderRequest $request): OrderResource
    {
        $this->authorize('create', Order::class);

        return new OrderResource(Order::query()->create($request->validated()));
    }

    public function show(Order $order): OrderResource
    {
        $this->authorize('view', $order);

        return new OrderResource($order);
    }

    public function update(OrderRequest $request, Order $order): OrderResource
    {
        $this->authorize('update', $order);

        $order->update($request->validated());

        return new OrderResource($order);
    }

    public function getExpenses(): JsonResponse
    {
        try {
            /** @var Request $request */
            $request = request();
            $dateStart = $request->input('date_start');
            $dateEnd = $request->input('date_end');

            if (empty($dateStart) && empty($dateEnd)) {
                $dateStart = date('Y-m-d', strtotime('-1 day'));
                $dateEnd = $dateStart;
            }

            if (! empty($dateStart) && empty($dateEnd)) {
                $dateEnd = $dateStart;
            }

            if (empty($dateStart) && ! empty($dateEnd)) {
                $dateStart = $dateEnd;
            }

            /** @var string $dateStart */
            /** @var string $dateEnd */
            $expenses = $this->secureSellerService->getOrders($dateStart, $dateEnd);

            return response()->json([
                'success' => true,
                'orders' => $expenses,
                'count' => count($expenses),
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch expenses',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function destroy(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);

        $order->delete();

        return response()->json();
    }
}
