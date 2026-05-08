<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPrintArea;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate([
            'email' => 'admin@printlab.test',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
        ]);

        $category = Category::query()->updateOrCreate([
            'slug' => 'typography',
        ], [
            'name' => 'Типографика',
            'scope' => Category::SCOPE_PRINT,
            'sort_order' => 80,
            'is_active' => true,
        ]);

        $productCategories = collect([
            ['slug' => 't-shirts', 'name' => 'Футболки', 'sort_order' => 10],
            ['slug' => 'hoodies', 'name' => 'Худи', 'sort_order' => 20],
            ['slug' => 'mugs', 'name' => 'Кружки', 'sort_order' => 30],
            ['slug' => 'sweatshirts', 'name' => 'Свитшоты', 'sort_order' => 40],
            ['slug' => 'longsleeves', 'name' => 'Лонгсливы', 'sort_order' => 50],
            ['slug' => 'kids', 'name' => 'Детское', 'sort_order' => 60],
            ['slug' => 'custom-products', 'name' => 'Кастом', 'sort_order' => 70],
        ])->mapWithKeys(function (array $data): array {
            $category = Category::query()->updateOrCreate([
                'slug' => $data['slug'],
            ], [
                'name' => $data['name'],
                'scope' => Category::SCOPE_PRODUCT,
                'sort_order' => $data['sort_order'],
                'is_active' => true,
            ]);

            return [$data['slug'] => $category];
        });

        foreach ([
            ['slug' => 'memes', 'name' => 'Мемы', 'sort_order' => 10],
            ['slug' => 'pop-culture', 'name' => 'Поп-культура', 'sort_order' => 20],
            ['slug' => 'anime', 'name' => 'Аниме', 'sort_order' => 30],
            ['slug' => 'sport', 'name' => 'Спорт', 'sort_order' => 40],
            ['slug' => 'games', 'name' => 'Игры', 'sort_order' => 50],
            ['slug' => 'music', 'name' => 'Музыка', 'sort_order' => 60],
            ['slug' => 'own-prints', 'name' => 'Свои принты', 'sort_order' => 70],
        ] as $data) {
            Category::query()->updateOrCreate([
                'slug' => $data['slug'],
            ], [
                'name' => $data['name'],
                'scope' => Category::SCOPE_PRINT,
                'sort_order' => $data['sort_order'],
                'is_active' => true,
            ]);
        }

        \App\Models\ReadyPrint::query()->where('slug', 'code-lifter')->delete();

        $tshirt = Product::query()->firstOrCreate([
            'slug' => 'classic-t-shirt',
        ], [
            'name' => 'Классическая футболка',
            'type' => 't-shirt',
            'base_price' => 120000,
            'is_active' => true,
        ]);

        $tshirt->categories()->syncWithoutDetaching([
            $productCategories['t-shirts']->id,
            $productCategories['custom-products']->id,
        ]);

        foreach ([
            ['white', 'M', '/mockups/tshirts/white-front.png', '/mockups/tshirts/white-back.png'],
            ['black', 'L', '/mockups/tshirts/black-front.png', '/mockups/tshirts/black-back.png'],
        ] as [$color, $size, $mockupFront, $mockupBack]) {
            $variant = ProductVariant::query()->firstOrCreate([
                'product_id' => $tshirt->id,
                'color' => $color,
                'size' => $size,
            ], [
                'mockup_front_path' => $mockupFront,
                'mockup_back_path' => $mockupBack,
                'price_modifier' => 0,
                'is_active' => true,
            ]);

            $variant->update([
                'mockup_front_path' => $mockupFront,
                'mockup_back_path' => $mockupBack,
            ]);

            ProductPrintArea::query()->firstOrCreate([
                'product_variant_id' => $variant->id,
                'side' => 'front',
            ], [
                'x' => 0.32,
                'y' => 0.27,
                'width' => 0.36,
                'height' => 0.42,
                'dpi' => 300,
            ]);

            ProductPrintArea::query()->firstOrCreate([
                'product_variant_id' => $variant->id,
                'side' => 'back',
            ], [
                'x' => 0.32,
                'y' => 0.27,
                'width' => 0.36,
                'height' => 0.42,
                'dpi' => 300,
            ]);
        }

        $mug = Product::query()->firstOrCreate([
            'slug' => 'white-mug',
        ], [
            'name' => 'Белая кружка',
            'type' => 'mug',
            'base_price' => 85000,
            'is_active' => true,
        ]);

        $mug->categories()->syncWithoutDetaching([
            $productCategories['mugs']->id,
            $productCategories['custom-products']->id,
        ]);

        $mugVariant = ProductVariant::query()->firstOrCreate([
            'product_id' => $mug->id,
            'color' => 'white',
            'size' => null,
        ], [
            'mockup_front_path' => 'images/Mug/front.jpg',
            'mockup_back_path' => 'images/Mug/back.jpeg',
            'price_modifier' => 0,
            'is_active' => true,
        ]);

        $mugVariant->update([
            'mockup_front_path' => 'images/Mug/front.jpg',
            'mockup_back_path' => 'images/Mug/back.jpeg',
        ]);

        ProductPrintArea::query()->firstOrCreate([
            'product_variant_id' => $mugVariant->id,
            'side' => 'front',
        ], [
            'x' => 0.27,
            'y' => 0.30,
            'width' => 0.46,
            'height' => 0.34,
            'dpi' => 300,
        ]);

        ProductPrintArea::query()->firstOrCreate([
            'product_variant_id' => $mugVariant->id,
            'side' => 'back',
        ], [
            'x' => 0.27,
            'y' => 0.30,
            'width' => 0.46,
            'height' => 0.34,
            'dpi' => 300,
        ]);
    }
}
