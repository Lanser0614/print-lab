<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\ReadyPrint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'image_path' => 'prints/sample.png',
            'is_active' => true,
        ];
    }
}
