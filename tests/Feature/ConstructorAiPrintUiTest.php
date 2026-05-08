<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductPrintArea;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConstructorAiPrintUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_constructor_v2_exposes_ai_print_route_config(): void
    {
        $product = $this->productWithVariant();

        $response = $this->get(route('constructor.v2', $product));

        $response
            ->assertOk()
            ->assertSee('generatePrint', false)
            ->assertSee('/api/generated-prints', false);
    }

    public function test_constructor_v2_renders_ai_print_panel(): void
    {
        $product = $this->productWithVariant();

        $response = $this->get(route('constructor.v2', $product));

        $response
            ->assertOk()
            ->assertSee('id="aiPrintPanel"', false)
            ->assertSee('id="aiPromptInput"', false)
            ->assertSee('maxlength="250"', false)
            ->assertSee('id="aiPromptCounter"', false)
            ->assertSee('id="aiReferenceInput"', false)
            ->assertSee('id="aiReferencePreview"', false)
            ->assertSee('id="aiReferencePreviewImg"', false)
            ->assertSee('id="aiGenerateBtn"', false)
            ->assertSee('id="aiPrintMessage"', false)
            ->assertSee('id="mobileAiPrintPanel"', false)
            ->assertSee('id="mTabAi"', false);
    }

    public function test_constructor_v2_includes_ai_print_javascript_helpers(): void
    {
        $product = $this->productWithVariant();

        $response = $this->get(route('constructor.v2', $product));

        $response
            ->assertOk()
            ->assertSee('async function handleAiGenerate', false)
            ->assertSee('function readAiReferenceImage', false)
            ->assertSee('async function imageUrlToDataUrl', false)
            ->assertSee('function normalizeGeneratedImageUrl', false)
            ->assertSee('function addGeneratedImageLayer', false)
            ->assertSee('function updateAiPromptCounter', false)
            ->assertSee('function updateAiReferencePreview', false)
            ->assertSee('function mobileSyncAiPanel', false)
            ->assertSee('function showAiPrintMessage', false);
    }

    public function test_constructor_v2_gives_ai_print_mobile_prominence(): void
    {
        $product = $this->productWithVariant();

        $response = $this->get(route('constructor.v2', $product));

        $response
            ->assertOk()
            ->assertSee('mobile-ai-print-panel', false)
            ->assertSee('mobile-ai-print-featured', false)
            ->assertSee('AI', false)
            ->assertSee("mobileSwitchTab('ai')", false)
            ->assertSee("tabMap = { layers: 'mTabLayers', props: 'mTabProps', ai: 'mTabAi', variants: 'mTabVariants' }", false)
            ->assertSee("tab === 'ai'", false);
    }

    public function test_constructor_v2_handles_ai_print_success_and_error_states(): void
    {
        $product = $this->productWithVariant();

        $response = $this->get(route('constructor.v2', $product));

        $response
            ->assertOk()
            ->assertSee('imageUrlToDataUrl(data.image_url)', false)
            ->assertSee('normalizeGeneratedImageUrl(imageUrl)', false)
            ->assertSee('addGeneratedImageLayer', false)
            ->assertSee('generatedPrintId', false)
            ->assertSee('generatedImageUrl', false)
            ->assertSee('refreshUI()', false)
            ->assertSee('response.status === 422', false)
            ->assertSee('response.status === 429', false)
            ->assertSee('response.status === 503', false)
            ->assertSee('Сегодня доступно только 2 AI-генерации', false)
            ->assertSee('AI-генерация пока не настроена', false)
            ->assertSee('Не удалось сгенерировать принт', false);
    }

    private function productWithVariant(): Product
    {
        $product = Product::factory()->create();

        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
        ]);

        ProductPrintArea::factory()->create([
            'product_variant_id' => $variant->id,
            'side' => 'front',
        ]);

        return $product;
    }
}
