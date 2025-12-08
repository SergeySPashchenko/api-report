<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
final class ProductResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'ProductID' => $this->ProductID,
            'Product' => $this->Product,
            'ProductName' => $this->ProductName,
            'newSystem' => $this->newSystem,
            'Visible' => $this->Visible,
            'flyer' => $this->flyer,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'brand' => new BrandResource($this->whenLoaded('brand')),
        ];
    }
}
