<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductPrintArea;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AiPrintStudioHandoffTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_studio_stores_generated_artwork_before_opening_constructor(): void
    {
        $product = $this->productWithVariant();

        $response = $this->get(route('ai-studio.show', $product));

        $response
            ->assertOk()
            ->assertSee('sessionStorage.setItem(AI_STUDIO_STORAGE_KEY', false)
            ->assertSee("const AI_STUDIO_STORAGE_KEY = 'printlab.pendingAiPrint'", false)
            ->assertSee('window.location.href = window.aiStudioConfig.routes.constructor', false)
            ->assertSee('selectedAiStudioPrint', false);
    }

    public function test_constructor_v2_loads_pending_ai_artwork_from_session_storage(): void
    {
        $product = $this->productWithVariant();
        $variant = $product->variants->first();

        $response = $this->get(route('constructor.v2', ['product' => $product, 'variant' => $variant->id]));

        $response
            ->assertOk()
            ->assertSee('function loadPendingAiPrint', false)
            ->assertSee('sessionStorage.getItem(AI_STUDIO_STORAGE_KEY)', false)
            ->assertSee('sessionStorage.removeItem(AI_STUDIO_STORAGE_KEY)', false)
            ->assertSee('addGeneratedImageLayer', false);
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
