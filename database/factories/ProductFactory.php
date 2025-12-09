<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<Product>
 */
final class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'ProductID' => $this->faker->randomNumber(),
            'Product' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'ProductName' => $this->faker->name(),
            'newSystem' => $this->faker->boolean(),
            'Visible' => $this->faker->boolean(),
            'flyer' => $this->faker->boolean(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'brand_id' => Brand::factory(),
        ];
    }
}
