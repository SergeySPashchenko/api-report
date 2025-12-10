<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\UnknownCustomer;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<UnknownCustomer> */
final class UnknownCustomerFactory extends Factory
{
    protected $model = UnknownCustomer::class;

    public function definition(): array
    {
        return [

        ];
    }
}
