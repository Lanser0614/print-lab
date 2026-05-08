<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'color' => fake()->randomElement(['white', 'black', 'navy']),
            'size' => fake()->randomElement(['S', 'M', 'L', 'XL']),
            'mockup_front_path' => '/mockups/tshirts/white-front.png',
            'mockup_back_path' => '/mockups/tshirts/white-back.png',
            'price_modifier' => 0,
            'is_active' => true,
        ];
    }
}
