<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<Order>
 */
final class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'external_id' => $this->faker->randomNumber(5, false),
            'Agent' => $this->faker->word(),
            'Created' => Date::now(),
            'OrderDate' => Date::now(),
            'OrderNum' => $this->faker->word(),
            'OrderN' => $this->faker->word(),
            'ProductTotal' => $this->faker->randomFloat(),
            'GrandTotal' => $this->faker->randomFloat(),
            'Shipping' => $this->faker->word(),
            'PaymentGateway' => $this->faker->word(),
            'ShippingMethod' => $this->faker->word(),
            'Refund' => $this->faker->word(),
            'RefundAmount' => $this->faker->randomFloat(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'brand_id' => Brand::factory(),
            'product_id' => Product::factory(),
        ];
    }
}
