<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ExpenseType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<ExpenseType>
 */
final class ExpenseTypeFactory extends Factory
{
    protected $model = ExpenseType::class;

    public function definition(): array
    {
        return [
            'ExpenseID' => $this->faker->randomNumber(),
            'Name' => $this->faker->name(),
            'slug' => $this->faker->slug(),
            'Visible' => $this->faker->boolean(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ];
    }
}
