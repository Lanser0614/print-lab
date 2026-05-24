<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Design;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderRequest;
use App\Models\ProductVariant;
use App\Models\OrderRequestItem;
use App\Enums\OrderRequestStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PrintShowPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_uzbek_print_detail_uses_public_storefront_style(): void
    {
        Category::factory()->create([
            'scope' => Category::SCOPE_PRODUCT,
            'slug' => 't-shirts',
            'name' => 'Футболки',
            'name_translations' => ['uz' => 'Futbolkalar', 'ru' => 'Футболки'],
        ]);
        Category::factory()->create([
            'scope' => Category::SCOPE_PRINT,
            'slug' => 'memes',
            'name' => 'Мемы',
            'name_translations' => ['uz' => 'Memlar', 'ru' => 'Мемы'],
        ]);

        $product = Product::factory()->create([
            'name' => 'Classic T-Shirt',
            'name_translations' => ['uz' => 'Klassik futbolka', 'ru' => 'Классическая футболка'],
        ]);
        $variant = ProductVariant::factory()->for($product)->create([
            'color' => 'black',
            'size' => 'M',
        ]);
        $orderRequest = OrderRequest::factory()->create(['status' => OrderRequestStatus::Ready]);
        $item = OrderRequestItem::query()->create([
            'order_request_id' => $orderRequest->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'product_name_snapshot' => 'Klassik futbolka',
            'product_type_snapshot' => $product->type->value,
            'color_snapshot' => 'black',
            'size_snapshot' => 'M',
            'quantity' => 1,
        ]);
        $design = Design::query()->create([
            'order_request_item_id' => $item->id,
            'side' => 'front',
            'canvas_json' => ['layers' => []],
            'preview_image_path' => 'ready-prints/example.png',
        ]);

        $response = $this->get("/uz/prints/{$design->id}");

        $response->assertOk();
        $response->assertSee('pl-header', false);
        $response->assertSee('pl-nav', false);
        $response->assertSee('pl-print-detail', false);
        $response->assertSee('Klassik futbolka');
        $response->assertSee('O‘z variantimni yaratish');
        $response->assertSee('Tayyor dizayn');
    }
}
