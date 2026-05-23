<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\ReadyPrint;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReadyPrint>
 */
class ReadyPrintFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'order_request_id' => null,
            'source_design_id' => null,
            'title' => $title,
            'title_translations' => [
                'ru' => $title,
                'uz' => $title,
            ],
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'image_path' => 'prints/sample.png',
            'is_active' => true,
        ];
    }
}
