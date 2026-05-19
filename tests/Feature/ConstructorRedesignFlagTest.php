<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductPrintArea;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    public function test_constructor_v2_mobile_panels_can_override_hidden_desktop_panel(): void
    {
        config()->set('printlab.constructor_v2_enabled', true);

        $product = $this->productWithVariant();

        $response = $this->get(route('constructor.show', $product));

        $response
            ->assertOk()
            ->assertSee('body.constructor-v2 .panel-right.mobile-open', false)
            ->assertSee('display: flex;', false)
            ->assertSee("mobileSwitchTab('layers')", false)
            ->assertSee("mobileSwitchTab('props')", false);
    }

    public function test_constructor_v2_selection_controls_include_delete_handle(): void
    {
        config()->set('printlab.constructor_v2_enabled', true);

        $product = $this->productWithVariant();

        $response = $this->get(route('constructor.show', $product));

        $response
            ->assertOk()
            ->assertSee('const DELETE_HANDLE_OFFSET', false)
            ->assertSee('function getDeleteHandlePoint', false)
            ->assertSee("return { type: 'delete' }", false)
            ->assertSee("if (selectionHit?.type === 'delete')", false);
    }

    public function test_constructor_v2_selected_layer_can_be_deleted_from_keyboard(): void
    {
        config()->set('printlab.constructor_v2_enabled', true);

        $product = $this->productWithVariant();

        $response = $this->get(route('constructor.show', $product));

        $response
            ->assertOk()
            ->assertSee('function deleteSelectedLayerFromKeyboard', false)
            ->assertSee("['Delete', 'Backspace'].includes(event.key)", false)
            ->assertSee('isTypingTarget(event.target)', false)
            ->assertSee("document.addEventListener('keydown', deleteSelectedLayerFromKeyboard)", false);
    }

    public function test_constructor_v2_text_layers_can_be_edited_inline_on_canvas(): void
    {
        config()->set('printlab.constructor_v2_enabled', true);

        $product = $this->productWithVariant();

        $response = $this->get(route('constructor.show', $product));

        $response
            ->assertOk()
            ->assertSee('inline-text-editor', false)
            ->assertSee('function startInlineTextEditing', false)
            ->assertSee('function finishInlineTextEditing', false)
            ->assertSee('function beginInlineTextEditingFromEvent', false)
            ->assertSee("canvas.addEventListener('dblclick', beginInlineTextEditingFromEvent)", false)
            ->assertSee('startInlineTextEditing(layer);', false);
    }

    public function test_constructor_success_route_points_to_new_catalog_home(): void
    {
        $product = $this->productWithVariant();

        $response = $this->get('/ru/constructor/'.$product->slug);

        $response->assertOk();

        preg_match(
            '/\\\\u0022success\\\\u0022:\\\\u0022(?<url>.*?)\\\\u0022/',
            $response->getContent(),
            $matches,
        );

        $successUrl = str_replace(['\\\\/', '\\/'], '/', $matches['url']);

        $this->assertSame('http://localhost:8000/ru', $successUrl);
        $this->assertNotSame('http://localhost:8000/ru/catalog', $successUrl);
    }

    public function test_constructor_persists_side_drafts_when_switching_sides(): void
    {
        $product = $this->productWithVariant();

        $this->get('/ru/constructor/'.$product->slug)
            ->assertOk()
            ->assertSee("function constructorDraftKey(side = constructorConfig.printArea?.side || 'front')", false)
            ->assertSee('function saveCurrentSideDraft()', false)
            ->assertSee('function restoreCurrentSideDraft()', false)
            ->assertSee('saveCurrentSideDraft();', false)
            ->assertSee('restoreCurrentSideDraft();', false)
            ->assertDontSee("constructorConfig.variant?.id || 'variant'", false);
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
