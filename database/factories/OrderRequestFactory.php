<?php

namespace Database\Factories;

use App\Models\OrderRequest;
use App\Enums\OrderRequestStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderRequest>
 */
class OrderRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => OrderRequestStatus::New,
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->phoneNumber(),
            'customer_comment' => null,
            'currency' => 'UZS',
        ];
    }
}
