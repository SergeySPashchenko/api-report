<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
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

    public function getOrders(): JsonResponse
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

    public function syncOrders(): JsonResponse
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
            $orders = $this->secureSellerService->getOrders($dateStart, $dateEnd);

            $synced = 0;
            $updated = 0;

            foreach ($orders as $data) {
                // Find relationships
                $product = Product::query()
                    ->where('ProductID', $data['BrandID'] ?? 0)
                    ->withoutGlobalScope('user_access')
                    ->first();

                // Use 'id' from data as external_id for unique matching
                $order = Order::withTrashed()
                    ->where('external_id', $data['id'])
                    ->first();

                if ($order) {
                    $wasRestored = false;

                    if ($order->trashed()) {
                        $order->restore();
                        $wasRestored = true;
                    }

                    $order->fill([
                        'product_id' => $product?->id,
                        'brand_id' => $product?->brand_id,
                        // Map internal fields
                        'external_id' => $data['id'],
                        'Agent' => $data['Agent'],
                        'Created' => date('Y-m-d H:i:s', (int) $data['Created']),
                        'OrderDate' => substr($data['OrderDate'], 0, 4) . '-' . substr($data['OrderDate'], 4, 2) . '-' . substr($data['OrderDate'], 6, 2),
                        'OrderNum' => $data['OrderNum'],
                        'OrderN' => $data['OrderN'],
                        'ProductTotal' => $data['ProductTotal'],
                        'GrandTotal' => $data['GrandTotal'],
                        'Shipping' => $data['Shipping'] ?: null,
                        'PaymentGateway' => $data['PaymentGateway'] ?: null,
                        'ShippingMethod' => $data['ShippingMethod'] ?: null,
                        'Refund' => $data['Refund'] ?: null,
                        'RefundAmount' => !empty($data['RefundAmount']) ? (float) $data['RefundAmount'] : null,
                    ]);

                    if ($order->isDirty()) {
                        $order->save();
                        $updated++;
                    } elseif ($wasRestored) {
                        $updated++;
                    }
                } else {
                    Order::query()->create([
                        'product_id' => $product?->id,
                        'brand_id' => $product?->brand_id,
                        // Map internal fields
                        'external_id' => $data['id'],
                        'Agent' => $data['Agent'],
                        'Created' => date('Y-m-d H:i:s', (int) $data['Created']),
                        'OrderDate' => substr($data['OrderDate'], 0, 4) . '-' . substr($data['OrderDate'], 4, 2) . '-' . substr($data['OrderDate'], 6, 2),
                        'OrderNum' => $data['OrderNum'],
                        'OrderN' => $data['OrderN'],
                        'ProductTotal' => $data['ProductTotal'],
                        'GrandTotal' => $data['GrandTotal'],
                        'Shipping' => $data['Shipping'] ?: null,
                        'PaymentGateway' => $data['PaymentGateway'] ?: null,
                        'ShippingMethod' => $data['ShippingMethod'] ?: null,
                        'Refund' => $data['Refund'] ?: null,
                        'RefundAmount' => !empty($data['RefundAmount']) ? (float) $data['RefundAmount'] : null,
                    ]);
                    $synced++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Orders synced successfully',
                'created' => $synced,
                'updated' => $updated,
                'total' => count($orders),
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed',
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
