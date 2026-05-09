<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
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
            'scope' => Category::SCOPE_PRINT,
            'icon_svg' => null,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
