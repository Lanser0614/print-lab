<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\ProductPrintArea;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductPrintArea>
 */
class ProductPrintAreaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_variant_id' => ProductVariant::factory(),
            'side' => 'front',
            'x' => 0.32,
            'y' => 0.27,
            'width' => 0.36,
            'height' => 0.42,
            'dpi' => 300,
        ];
    }
}
