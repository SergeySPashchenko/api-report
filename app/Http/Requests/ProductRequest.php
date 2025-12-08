<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $product = $this->route('product');
        $productId = $product instanceof Product ? $product->id : $product;

        return [
            'ProductID' => [
                'required',
                'integer',
                Rule::unique('products', 'ProductID')->ignore($productId),
            ],
            'brand_id' => ['required', 'ulid', 'exists:brands,id'],
            'Product' => ['required', 'string', 'max:255'],
            'ProductName' => ['required', 'string', 'max:255'],
            'ProductPage' => ['required', 'string', 'max:255'],
            'site_code' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:254'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'ProductID.unique' => 'This Product ID is already in use',
            'brand_id.exists' => 'Selected brand does not exist',
            'email.email' => 'Please provide a valid email address',
        ];
    }
}
