<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
final class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'brand_id' => $this->brand_id,
            'product_id' => $this->product_id,
            'customer_id' => $this->customer_id,
            'unknown_customer_id' => $this->unknown_customer_id,
            'billing_address_id' => $this->billing_address_id,
            'shipping_address_id' => $this->shipping_address_id,
            'status' => $this->status,
            'Agent' => $this->Agent,
            'Created' => $this->Created,
            'OrderDate' => $this->OrderDate,
            'OrderNum' => $this->OrderNum,
            'OrderN' => $this->OrderN,
            'ProductTotal' => $this->ProductTotal,
            'GrandTotal' => $this->GrandTotal,
            'Shipping' => $this->Shipping,
            'PaymentGateway' => $this->PaymentGateway,
            'ShippingMethod' => $this->ShippingMethod,
            'Refund' => $this->Refund,
            'RefundAmount' => $this->RefundAmount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relationships
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'unknown_customer' => new UnknownCustomerResource($this->whenLoaded('unknownCustomer')),
            'billing_address' => new AddressResource($this->whenLoaded('billingAddress')),
            'shipping_address' => new AddressResource($this->whenLoaded('shippingAddress')),
        ];
    }
}
