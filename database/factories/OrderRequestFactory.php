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
            'customer_city' => 'Tashkent',
            'customer_address' => 'Tashkent, Chilanzar 10',
            'delivery_lat' => 41.2995,
            'delivery_lng' => 69.2401,
            'currency' => 'UZS',
        ];
    }
}
