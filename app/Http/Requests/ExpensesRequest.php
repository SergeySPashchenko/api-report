<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExpensesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'ExpenseDate' => ['required', 'date'],
            'Expense' => ['required'],
            'product_id' => ['required', 'exists:products'],
            'brand_id' => ['required', 'exists:brands'],
            'expense_type_id' => ['required', 'exists:expense_types'],
        ];
    }
}
