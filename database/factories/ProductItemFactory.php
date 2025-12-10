<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<ProductItem>
 */
final class ProductItemFactory extends Factory
{
    protected $model = ProductItem::class;

    public function definition(): array
    {
        return [
            'ItemID' => $this->faker->randomNumber(),
            'ProductName' => $this->faker->name(),
            'SKU' => $this->faker->word(),
            'Quantity' => $this->faker->randomNumber(),
            'upSell' => $this->faker->boolean(),
            'active' => $this->faker->boolean(),
            'offerProducts' => $this->faker->word(),
            'extraProduct' => $this->faker->boolean(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'product_id' => Product::factory(),
        ];
    }
}
