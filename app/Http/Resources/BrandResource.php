<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Brand */
final class BrandResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Counts
            'products_count' => $this->whenCounted('products'),
            'expenses_count' => $this->whenCounted('expenses'),
            'orders_count' => $this->whenCounted('orders'),

            // Relationships
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'expenses' => ExpensesResource::collection($this->whenLoaded('expenses')),
            'orders' => OrderResource::collection($this->whenLoaded('orders')),
        ];
    }
}
