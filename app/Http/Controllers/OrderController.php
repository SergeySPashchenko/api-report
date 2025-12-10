<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\CountryCodeConverter;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\UnknownCustomer;
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

        $orders = Order::query()
            ->with(['brand', 'product', 'customer', 'unknownCustomer', 'billingAddress', 'shippingAddress'])
            ->latest()
            ->paginate(15);

        return OrderResource::collection($orders);
    }

    public function store(OrderRequest $request): OrderResource
    {
        $this->authorize('create', Order::class);

        $order = Order::query()->create($request->validated());
        $order->load(['brand', 'product', 'customer', 'unknownCustomer', 'billingAddress', 'shippingAddress']);

        return new OrderResource($order);
    }

    public function show(Order $order): OrderResource
    {
        $this->authorize('view', $order);

        $order->load(['brand', 'product', 'customer', 'unknownCustomer', 'billingAddress', 'shippingAddress']);

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
                /**
                 * @var array{
                 *     id: int,
                 *     Agent: string,
                 *     Created: string|int,
                 *     OrderDate: string|int,
                 *     OrderNum: string,
                 *     OrderN: string,
                 *     ProductTotal: string|float,
                 *     GrandTotal: string|float,
                 *     Shipping: string,
                 *     PaymentGateway: string,
                 *     ShippingMethod: string,
                 *     Refund: string,
                 *     RefundAmount: string,
                 *     BrandID: int,
                 *     Email: string,
                 *     Name: string,
                 *     Address: string,
                 *     Address2: string,
                 *     City: string,
                 *     State: string,
                 *     Zip: string,
                 *     Country: string,
                 *     Phone: string,
                 *     ShipName: string,
                 *     ShipAddress: string,
                 *     ShipAddress2: string,
                 *     ShipCity: string,
                 *     ShipState: string,
                 *     ShipZip: string,
                 *     ShipCountry: string,
                 *     ShipPhone: string
                 * } $data
                 */
                // Find relationships
                $product = Product::query()
                    ->where('ProductID', $data['BrandID'])
                    ->withoutGlobalScope('user_access')
                    ->first();

                // Handle customer (email or unknown)
                /** @var string|null $customerId */
                $customerId = null;
                /** @var string|null $unknownCustomerId */
                $unknownCustomerId = null;

                if (! empty($data['Email'])) {
                    // Customer with email
                    $customer = Customer::findOrCreateByEmail(
                        $data['Email'],
                        $data['Name'] ?: null,
                        $data['Phone'] ?: null
                    );
                    assert($customer instanceof Customer);
                    $customerId = $customer->id;
                } else {
                    // Unknown customer (no email)
                    $unknownCustomer = UnknownCustomer::findOrCreateByAddressHash(
                        $data['City'],
                        $data['State'] ?: null,
                        $data['Zip'] ?: null,
                        $data['Country'] ?: null
                    );
                    assert($unknownCustomer instanceof UnknownCustomer);
                    $unknownCustomerId = $unknownCustomer->id;
                }

                // Handle billing address
                $billingHash = Address::generateHash(
                    $data['Name'] ?: null,
                    $data['Address'] ?: null,
                    $data['Address2'] ?: null,
                    $data['City'] ?: null,
                    $data['State'] ?: null,
                    $data['Zip'] ?: null,
                    $data['Country'] ?: null,
                    $data['Phone'] ?: null
                );

                // Handle shipping address
                $shippingHash = Address::generateHash(
                    $data['ShipName'] ?: null,
                    $data['ShipAddress'] ?: null,
                    $data['ShipAddress2'] ?: null,
                    $data['ShipCity'] ?: null,
                    $data['ShipState'] ?: null,
                    $data['ShipZip'] ?: null,
                    $data['ShipCountry'] ?: null,
                    $data['ShipPhone'] ?: null
                );

                // Check if billing and shipping are the same
                $sameAddress = ($billingHash === $shippingHash);

                // Determine order status
                $status = 'completed'; // default
                if (! empty($data['Refund'])) {
                    $status = 'refund';
                } elseif ($data['BrandID'] === 0) {
                    $status = 'hold';
                }

                // Convert country names to codes
                $billingCountry = CountryCodeConverter::convert($data['Country'] ?: null);
                $shippingCountry = CountryCodeConverter::convert($data['ShipCountry'] ?: null);

                // Find or create billing address
                $billingAddress = Address::findByHashForCustomer($customerId, $unknownCustomerId, $billingHash);

                if (! $billingAddress instanceof Address) {
                    $billingAddress = Address::query()->create([
                        'customer_id' => $customerId,
                        'unknown_customer_id' => $unknownCustomerId,
                        'type' => $sameAddress ? 'both' : 'billing',
                        'name' => $data['Name'] ?: null,
                        'address' => $data['Address'] ?: null,
                        'address2' => $data['Address2'] ?: null,
                        'city' => $data['City'] ?: null,
                        'state' => $data['State'] ?: null,
                        'zip' => $data['Zip'] ?: null,
                        'country' => $billingCountry,
                        'phone' => $data['Phone'] ?: null,
                        'address_hash' => $billingHash,
                    ]);
                }

                // Handle shipping address
                $shippingAddressId = null;

                if ($sameAddress) {
                    // Use same address for both
                    $shippingAddressId = $billingAddress->id;
                } else {
                    // Find or create separate shipping address
                    $shippingAddress = Address::findByHashForCustomer($customerId, $unknownCustomerId, $shippingHash);

                    if (! $shippingAddress instanceof Address) {
                        $shippingAddress = Address::query()->create([
                            'customer_id' => $customerId,
                            'unknown_customer_id' => $unknownCustomerId,
                            'type' => 'shipping',
                            'name' => $data['ShipName'] ?: null,
                            'address' => $data['ShipAddress'] ?: null,
                            'address2' => $data['ShipAddress2'] ?: null,
                            'city' => $data['ShipCity'] ?: null,
                            'state' => $data['ShipState'] ?: null,
                            'zip' => $data['ShipZip'] ?: null,
                            'country' => $shippingCountry,
                            'phone' => $data['ShipPhone'] ?: null,
                            'address_hash' => $shippingHash,
                        ]);
                    }

                    $shippingAddressId = $shippingAddress->id;
                }

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
                        'customer_id' => $customerId,
                        'unknown_customer_id' => $unknownCustomerId,
                        'billing_address_id' => $billingAddress->id,
                        'shipping_address_id' => $shippingAddressId,
                        'status' => $status,
                        // Map internal fields
                        'external_id' => $data['id'],
                        'Agent' => $data['Agent'],
                        'Created' => date('Y-m-d H:i:s', (int) $data['Created']),
                        'OrderDate' => mb_substr((string) $data['OrderDate'], 0, 4).'-'.mb_substr((string) $data['OrderDate'], 4, 2).'-'.mb_substr((string) $data['OrderDate'], 6, 2),
                        'OrderNum' => $data['OrderNum'],
                        'OrderN' => $data['OrderN'],
                        'ProductTotal' => $data['ProductTotal'],
                        'GrandTotal' => $data['GrandTotal'],
                        'Shipping' => $data['Shipping'] ?: null,
                        'PaymentGateway' => $data['PaymentGateway'] ?: null,
                        'ShippingMethod' => $data['ShippingMethod'] ?: null,
                        'Refund' => $data['Refund'] ?: null,
                        'RefundAmount' => empty($data['RefundAmount']) ? null : (float) $data['RefundAmount'],
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
                        'customer_id' => $customerId,
                        'unknown_customer_id' => $unknownCustomerId,
                        'billing_address_id' => $billingAddress->id,
                        'shipping_address_id' => $shippingAddressId,
                        'status' => $status,
                        // Map internal fields
                        'external_id' => $data['id'],
                        'Agent' => $data['Agent'],
                        'Created' => date('Y-m-d H:i:s', (int) $data['Created']),
                        'OrderDate' => mb_substr((string) $data['OrderDate'], 0, 4).'-'.mb_substr((string) $data['OrderDate'], 4, 2).'-'.mb_substr((string) $data['OrderDate'], 6, 2),
                        'OrderNum' => $data['OrderNum'],
                        'OrderN' => $data['OrderN'],
                        'ProductTotal' => $data['ProductTotal'],
                        'GrandTotal' => $data['GrandTotal'],
                        'Shipping' => $data['Shipping'] ?: null,
                        'PaymentGateway' => $data['PaymentGateway'] ?: null,
                        'ShippingMethod' => $data['ShippingMethod'] ?: null,
                        'Refund' => $data['Refund'] ?: null,
                        'RefundAmount' => empty($data['RefundAmount']) ? null : (float) $data['RefundAmount'],
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
