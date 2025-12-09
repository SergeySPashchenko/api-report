<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class OrderRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'brand_id' => ['nullable', 'exists:brands'],
            'product_id' => ['nullable', 'exists:products'],
            'Agent' => ['required'],
            'Created' => ['required', 'date'],
            'OrderDate' => ['required', 'date'],
            'OrderNum' => ['required'],
            'OrderN' => ['required'],
            'ProductTotal' => ['required', 'numeric'],
            'GrandTotal' => ['required', 'numeric'],
            'Shipping' => ['nullable'],
            'PaymentGateway' => ['nullable'],
            'ShippingMethod' => ['nullable'],
            'Refund' => ['nullable'],
            'RefundAmount' => ['nullable', 'numeric'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
