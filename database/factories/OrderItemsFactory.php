<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItems;
use App\Models\ProductItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<OrderItems>
 */
final class OrderItemsFactory extends Factory
{
    protected $model = OrderItems::class;

    public function definition(): array
    {
        return [
            'idOrderItem' => $this->faker->randomNumber(),
            'ItemID' => $this->faker->word(),
            'Price' => $this->faker->randomFloat(),
            'Qty' => $this->faker->word(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'order_id' => Order::factory(),
            'product_item_id' => ProductItem::factory(),
        ];
    }
}
