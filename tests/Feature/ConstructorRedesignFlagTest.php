<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductPrintArea;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConstructorRedesignFlagTest extends TestCase
{
    use RefreshDatabase;

    public function test_constructor_uses_legacy_view_when_v2_flag_is_disabled(): void
    {
        config()->set('printlab.constructor_v2_enabled', false);

        $product = $this->productWithVariant();

        $response = $this->get(route('constructor.show', $product));

        $response->assertOk();
        $response->assertDontSee('constructor-v2', false);
    }

    public function test_constructor_uses_v2_view_when_flag_is_enabled(): void
    {
        config()->set('printlab.constructor_v2_enabled', true);

        $product = $this->productWithVariant();

        $response = $this->get(route('constructor.show', $product));

        $response->assertOk();
        $response->assertSee('constructor-v2', false);
        $response->assertSee('v2-tool-tabs', false);
    }

    private function productWithVariant(): Product
    {
        $product = Product::factory()->create();

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
        ]);

        ProductPrintArea::query()->create([
            'product_variant_id' => $variant->id,
            'side' => 'front',
            'x' => 0.32,
            'y' => 0.27,
            'width' => 0.36,
            'height' => 0.42,
            'dpi' => 300,
        ]);

        return $product;
    }
}
