<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Expenses;
use App\Models\ExpenseType;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<Expenses>
 */
final class ExpensesFactory extends Factory
{
    protected $model = Expenses::class;

    public function definition(): array
    {
        return [
            'ExpenseDate' => Date::now(),
            'Expense' => $this->faker->word(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'product_id' => Product::factory(),
            'brand_id' => Brand::factory(),
            'expense_type_id' => ExpenseType::factory(),
        ];
    }
}
