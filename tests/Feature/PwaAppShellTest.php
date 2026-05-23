<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductPrintArea;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PwaAppShellTest extends TestCase
{
    use RefreshDatabase;

    public function test_manifest_is_available_with_required_app_fields(): void
    {
        $response = $this->get('/manifest.webmanifest');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/manifest+json');
        $response->assertJsonPath('name', 'PrintLab');
        $response->assertJsonPath('short_name', 'PrintLab');
        $response->assertJsonPath('display', 'standalone');
        $response->assertJsonPath('scope', '/');
    }

    public function test_service_worker_is_available(): void
    {
        $response = $this->get('/sw.js');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/javascript');
        $response->assertSee('printlab-pwa', false);
        $response->assertSee('networkFirst', false);
    }

    public function test_public_layout_includes_pwa_metadata_and_install_prompt(): void
    {
        config()->set('printlab.redesign_v2_enabled', true);

        $response = $this->get('/ru');

        $response->assertOk();
        $response->assertSee('<link rel="icon" href="/icons/icon.svg" type="image/svg+xml">', false);
        $response->assertSee('<link rel="icon" href="/icons/icon-192.png" type="image/png">', false);
        $response->assertSee('<link rel="manifest" href="/manifest.webmanifest">', false);
        $response->assertSee('<meta name="theme-color" content="#e30613">', false);
        $response->assertSee('<meta name="apple-mobile-web-app-capable" content="yes">', false);
        $response->assertSee('<link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">', false);
        $response->assertSee('data-pwa-install-prompt', false);
    }

    public function test_public_pages_include_mobile_app_shell(): void
    {
        config()->set('printlab.redesign_v2_enabled', true);

        $product = $this->createProductWithVariant();

        $this->get('/ru')->assertOk()->assertSee('pl-mobile-app-shell', false);
        $this->get('/ru/catalog')->assertOk()->assertSee('pl-mobile-app-shell', false);
        $this->get(route('products.show', $product))->assertOk()->assertSee('pl-mobile-app-shell', false);

        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('account.index'))
            ->assertOk()
            ->assertSee('pl-mobile-app-shell', false);
    }

    public function test_constructor_does_not_render_public_mobile_app_shell(): void
    {
        $product = $this->createProductWithVariant();

        $response = $this->get(route('constructor.show', $product));

        $response->assertOk();
        $response->assertDontSee('pl-mobile-app-shell', false);
        $response->assertSee('mobile-bottom-bar', false);
    }

    private function createProductWithVariant(): Product
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

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
