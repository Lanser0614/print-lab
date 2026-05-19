<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductPrintArea;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    public function test_constructor_v2_renders_external_chatgpt_ai_helper(): void
    {
        $product = $this->productWithVariant();

        $response = $this->get(route('constructor.v2', $product));

        $response
            ->assertOk()
            ->assertSee('external-ai-panel', false)
            ->assertSee('id="externalAiMessage"', false)
            ->assertSee('id="externalAiPromptBox"', false)
            ->assertSee('copyExternalAiPrompt()', false)
            ->assertSee('openExternalAiChat()', false)
            ->assertSee('https://chatgpt.com/', false)
            ->assertSee(__('site.constructor_external_ai_title', [], 'ru'))
            ->assertSee(__('site.constructor_external_ai_upload_result', [], 'ru'));
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
            ->assertSee('function buildExternalAiPrompt', false)
            ->assertSee('function showExternalAiPromptBox', false)
            ->assertSee('function copyExternalAiPrompt', false)
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
            ->assertSee($this->jsonTranslation('site.constructor_ai_daily_limit', 'ru'), false)
            ->assertSee($this->jsonTranslation('site.constructor_ai_not_configured', 'ru'), false)
            ->assertSee($this->jsonTranslation('site.constructor_ai_failed', 'ru'), false);
    }

    public function test_constructor_v2_renders_uzbek_ai_print_messages(): void
    {
        $product = $this->productWithVariant();

        $response = $this->get(route('constructor.v2', ['locale' => 'uz', 'product' => $product]));

        $response
            ->assertOk()
            ->assertSee($this->jsonTranslation('site.constructor_ai_daily_limit', 'uz'), false)
            ->assertSee($this->jsonTranslation('site.constructor_ai_not_configured', 'uz'), false)
            ->assertSee($this->jsonTranslation('site.constructor_ai_failed', 'uz'), false)
            ->assertDontSee($this->jsonTranslation('site.constructor_ai_daily_limit', 'ru'), false);
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

    private function jsonTranslation(string $key, string $locale): string
    {
        return json_encode(__($key, [], $locale), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }
}
