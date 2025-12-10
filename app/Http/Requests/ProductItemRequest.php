<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ProductItemRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products'],
            'ItemID' => ['required', 'integer'],
            'ProductName' => ['required'],
            'SKU' => ['required'],
            'Quantity' => ['required', 'integer'],
            'upSell' => ['boolean'],
            'active' => ['boolean'],
            'offerProducts' => ['required'],
            'extraProduct' => ['boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
