<?php

namespace Tests\Feature;

use App\Enums\OrderRequestStatus;
use App\Models\OrderRequest;
use App\Models\Product;
use App\Models\ProductPrintArea;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GoogleTagManagerOnConstructorTest extends TestCase
{
    use RefreshDatabase;

    public function test_constructor_v1_includes_gtm_when_container_id_is_set(): void
    {
        config()->set('printlab.constructor_v2_enabled', false);
        config()->set('services.gtm.container_id', 'GTM-PQSQLKKL');

        $product = $this->productWithVariant();

        $response = $this->get(route('constructor.show', $product));

        $response->assertOk();
        // gtm.js URL is built at JS runtime; ID is passed as a JS string argument.
        $response->assertSee('https://www.googletagmanager.com/gtm.js?id=', false);
        $response->assertSee("'GTM-PQSQLKKL'", false);
        // noscript iframe is rendered server-side, full URL is present.
        $response->assertSee('https://www.googletagmanager.com/ns.html?id=GTM-PQSQLKKL', false);
    }

    public function test_constructor_v2_includes_gtm_when_container_id_is_set(): void
    {
        config()->set('printlab.constructor_v2_enabled', true);
        config()->set('services.gtm.container_id', 'GTM-PQSQLKKL');

        $product = $this->productWithVariant();

        $response = $this->get(route('constructor.show', $product));

        $response->assertOk();
        $response->assertSee('constructor-v2', false);
        $response->assertSee('https://www.googletagmanager.com/gtm.js?id=', false);
        $response->assertSee("'GTM-PQSQLKKL'", false);
        $response->assertSee('https://www.googletagmanager.com/ns.html?id=GTM-PQSQLKKL', false);
    }

    public function test_constructor_v1_does_not_include_gtm_when_container_id_is_empty(): void
    {
        config()->set('printlab.constructor_v2_enabled', false);
        config()->set('services.gtm.container_id', null);

        $product = $this->productWithVariant();

        $response = $this->get(route('constructor.show', $product));

        $response->assertOk();
        $response->assertDontSee('googletagmanager.com', false);
    }

    public function test_order_request_submission_still_works_with_gtm_enabled(): void
    {
        // Guard: GTM is purely a frontend snippet and must not affect the order request flow.
        Storage::fake('public');
        config()->set('services.gtm.container_id', 'GTM-PQSQLKKL');

        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->for($product)->create();
        ProductPrintArea::factory()->for($variant)->create([
            'side' => 'front',
            'x' => 0.32,
            'y' => 0.27,
            'width' => 0.36,
            'height' => 0.42,
        ]);

        $response = $this->postJson(route('order-requests.store'), [
            'customer_name' => 'Doniyor',
            'customer_phone' => '+998901234567',
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 1,
            'side' => 'front',
            'canvas_json' => [
                'layers' => [
                    [
                        'id' => 'text-1',
                        'type' => 'text',
                        'text' => 'PrintLab',
                        'fontFamily' => 'Syne',
                        'fontSize' => 48,
                        'color' => '#ffffff',
                        'x' => 100,
                        'y' => 120,
                        'scale' => 1,
                        'rotation' => 0,
                    ],
                ],
                'print_area' => [
                    'x' => 0.32,
                    'y' => 0.27,
                    'width' => 0.36,
                    'height' => 0.42,
                    'unit' => 'ratio',
                ],
            ],
            'preview_image' => $this->fakeBase64Png(),
            'print_image' => $this->fakeBase64Png(),
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', OrderRequestStatus::New->value);

        $this->assertSame(1, OrderRequest::query()->count());
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

    private function fakeBase64Png(): string
    {
        return 'data:image/png;base64,'.base64_encode(base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p94AAAAASUVORK5CYII='
        ));
    }
}
