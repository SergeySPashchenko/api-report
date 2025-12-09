<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Expenses;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Expenses */
final class ExpensesResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ExpenseDate' => $this->ExpenseDate,
            'Expense' => $this->Expense,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'product_id' => $this->product_id,
            'brand_id' => $this->brand_id,
            'expense_type_id' => $this->expense_type_id,

            'product' => new ProductResource($this->whenLoaded('product')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'expenseType' => new ExpenseTypeResource($this->whenLoaded('expenseType')),
        ];
    }
}
