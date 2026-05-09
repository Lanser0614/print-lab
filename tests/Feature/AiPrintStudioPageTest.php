<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductPrintArea;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiPrintStudioPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_print_studio_page_renders_product_context(): void
    {
        $product = $this->productWithVariant();

        $response = $this->get(route('ai-studio.show', $product));

        $response
            ->assertOk()
            ->assertSee('window.aiStudioConfig', false)
            ->assertSee($product->localizedName(), false)
            ->assertSee('/api/generated-prints', false)
            ->assertSee(route('constructor.show', ['product' => $product, 'variant' => $product->variants->first()->id]), false);
    }

    public function test_ai_print_studio_page_renders_chat_and_preview_ui(): void
    {
        $product = $this->productWithVariant();

        $response = $this->get(route('ai-studio.show', $product));

        $response
            ->assertOk()
            ->assertSee('id="aiStudioChat"', false)
            ->assertSee('id="aiStudioPrompt"', false)
            ->assertSee('maxlength="250"', false)
            ->assertSee('id="aiStudioReference"', false)
            ->assertSee('id="aiStudioGenerate"', false)
            ->assertSee('id="aiStudioPreview"', false)
            ->assertSee('id="aiStudioPrintOverlay"', false)
            ->assertSee('id="aiStudioVariants"', false)
            ->assertSee('id="aiStudioMessage"', false)
            ->assertSee('id="aiStudioOpenConstructor"', false)
            ->assertSee(__('site.ai_studio_title'), false)
            ->assertSee(__('site.ai_studio_daily_limit_hint'), false)
            ->assertSee(__('site.ai_studio_open_constructor'), false);
    }

    public function test_ai_print_studio_includes_generation_overlay_javascript_helpers(): void
    {
        $product = $this->productWithVariant();

        $response = $this->get(route('ai-studio.show', $product));

        $response
            ->assertOk()
            ->assertSee('function handleAiStudioGenerate', false)
            ->assertSee('function readAiStudioReferenceImage', false)
            ->assertSee('function imageUrlToDataUrl', false)
            ->assertSee('function normalizeGeneratedImageUrl', false)
            ->assertSee('function showAiStudioResult', false)
            ->assertSee('function storeAiStudioSelection', false)
            ->assertSee('function openGeneratedPrintInConstructor', false)
            ->assertSee('printlab.pendingAiPrint', false)
            ->assertSee('fetch(window.aiStudioConfig.routes.generatePrint', false)
            ->assertSee('reference_image', false);
    }

    public function test_ai_print_studio_uses_uzbek_translations(): void
    {
        $product = $this->productWithVariant();

        $response = $this->get(route('ai-studio.show', ['locale' => 'uz', 'product' => $product]));

        $response
            ->assertOk()
            ->assertSee(__('site.ai_studio_title', [], 'uz'), false)
            ->assertSee(__('site.ai_studio_open_constructor', [], 'uz'), false)
            ->assertDontSee(__('site.ai_studio_title', [], 'ru'), false);
    }

    public function test_ai_print_studio_includes_mobile_ux_hooks(): void
    {
        $product = $this->productWithVariant();

        $response = $this->get(route('ai-studio.show', $product));

        $response
            ->assertOk()
            ->assertSee('class="studio-mobile-cta', false)
            ->assertSee('id="aiStudioMobileCta"', false)
            ->assertSee('id="aiStudioMobileOpenConstructor"', false)
            ->assertSee('aria-hidden="true"', false)
            ->assertSee('function scrollAiStudioPreviewIntoView', false)
            ->assertSee('function syncAiStudioMobileCta', false)
            ->assertSee('@media (max-width: 640px)', false)
            ->assertSee('-webkit-backdrop-filter', false)
            ->assertSee('min-height: 116px', false)
            ->assertSee('aspect-ratio: 4 / 5', false)
            ->assertSee('grid-template-columns: repeat(2, minmax(0, 1fr))', false)
            ->assertSee('min-height: min(60vh, 420px)', false);
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
