<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class OrderItemsRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'exists:orders'],
            'product_item_id' => ['required', 'exists:product_items'],
            'idOrderItem' => ['required', 'integer'],
            'OrderID' => ['required', 'integer'],
            'ItemID' => ['required'],
            'Price' => ['required', 'numeric'],
            'Qty' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
