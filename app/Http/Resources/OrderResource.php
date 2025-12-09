<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
final class OrderResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
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

            'brand_id' => $this->brand_id,
            'product_id' => $this->product_id,

            'brand' => new BrandResource($this->whenLoaded('brand')),
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
