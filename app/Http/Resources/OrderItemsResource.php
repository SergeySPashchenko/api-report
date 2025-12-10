<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\OrderItems;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin OrderItems */
final class OrderItemsResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'idOrderItem' => $this->idOrderItem,
            'ItemID' => $this->ItemID,
            'Price' => $this->Price,
            'Qty' => $this->Qty,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'order_id' => $this->order_id,
            'product_item_id' => $this->product_item_id,

            'order' => new OrderResource($this->whenLoaded('order')),
            'productItem' => new ProductItemResource($this->whenLoaded('productItem')),
        ];
    }
}
