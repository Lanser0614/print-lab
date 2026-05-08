<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductPrintArea;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintlabFeatureFlagsTest extends TestCase
{
    use RefreshDatabase;

    public function test_redesign_flags_are_disabled_in_env_example(): void
    {
        $envExample = file_get_contents(base_path('.env.example'));

        $this->assertStringContainsString('REDESIGN_V2_ENABLED=false', $envExample);
        $this->assertStringContainsString('CONSTRUCTOR_V2_ENABLED=false', $envExample);
    }

    public function test_redesign_flag_can_render_homepage_v2(): void
    {
        config()->set('printlab.redesign_v2_enabled', true);

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

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('pl-hero', false);
        $response->assertSee('Создай', false);
        $response->assertSee(route('constructor.show', $product), false);
    }
}
