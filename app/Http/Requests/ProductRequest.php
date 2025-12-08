<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'ProductID' => [$this->isMethod('POST') ? 'required' : 'sometimes'],
            'brand_id' => [$this->isMethod('POST') ? 'required' : 'sometimes', 'exists:brands,id'],
            'Product' => [$this->isMethod('POST') ? 'required' : 'sometimes'],
            'ProductName' => [$this->isMethod('POST') ? 'required' : 'sometimes'],
            'newSystem' => ['boolean'],
            'Visible' => ['boolean'],
            'flyer' => ['boolean'],
        ];
    }
}
