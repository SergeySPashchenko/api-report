<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ProductItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ProductItem */
final class ProductItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ItemID' => $this->ItemID,
            'ProductName' => $this->ProductName,
            'SKU' => $this->SKU,
            'Quantity' => $this->Quantity,
            'upSell' => $this->upSell,
            'active' => $this->active,
            'offerProducts' => $this->offerProducts,
            'extraProduct' => $this->extraProduct,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'product_id' => $this->product_id,

            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
