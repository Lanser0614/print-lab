<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductPrintArea;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiPrintStudioEntryPointTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_links_to_ai_print_studio(): void
    {
        $product = $this->productWithVariant();
        $variant = $product->variants->first();

        $response = $this->get(route('products.show', $product));

        $response
            ->assertOk()
            ->assertSee(__('site.product_create_with_ai'), false)
            ->assertSee(route('ai-studio.show', ['product' => $product, 'variant' => $variant->id]), false);
    }

    public function test_constructor_v2_links_to_ai_print_studio(): void
    {
        $product = $this->productWithVariant();
        $variant = $product->variants->first();

        $response = $this->get(route('constructor.v2', ['product' => $product, 'variant' => $variant->id]));

        $response
            ->assertOk()
            ->assertSee(__('site.ai_studio_short_link'), false)
            ->assertSee(e(route('ai-studio.show', ['product' => $product, 'variant' => $variant->id, 'side' => 'front'])), false);
    }

    private function productWithVariant(): Product
    {
        $product = Product::factory()->create();

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color' => 'white',
            'size' => 'M',
        ]);

        ProductPrintArea::factory()->create([
            'product_variant_id' => $variant->id,
            'side' => 'front',
        ]);

        return $product;
    }
}
