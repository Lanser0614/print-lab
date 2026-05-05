<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPrintArea;
use App\Models\ProductVariant;
use App\Models\ReadyPrint;
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

        $category = Category::query()->firstOrCreate([
            'slug' => 'typography',
        ], [
            'name' => 'Типографика',
        ]);

        ReadyPrint::query()->firstOrCreate([
            'slug' => 'code-lifter',
        ], [
            'category_id' => $category->id,
            'title' => 'CodeLifter',
            'image_path' => '/mockups/ready-print-code.svg',
            'is_active' => true,
        ]);

        $tshirt = Product::query()->firstOrCreate([
            'slug' => 'classic-t-shirt',
        ], [
            'name' => 'Классическая футболка',
            'type' => 't-shirt',
            'base_price' => 120000,
            'is_active' => true,
        ]);

        foreach ([
            ['white', 'M', 'images/T-Shirt/White/front.jpg', 'images/T-Shirt/White/back.jpg'],
            ['black', 'L', 'images/T-Shirt/Black/front.jpg', 'images/T-Shirt/Black/back.avif'],
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
