<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'name' => $name,
            'name_translations' => [
                'ru' => $name,
                'uz' => $name,
            ],
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'type' => fake()->randomElement(['t-shirt', 'mug']),
            'base_price' => fake()->numberBetween(80000, 180000),
            'is_active' => true,
        ];
    }
}
