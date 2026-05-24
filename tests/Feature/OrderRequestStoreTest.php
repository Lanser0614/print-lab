<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Design;
use App\Models\Product;
use App\Models\OrderRequest;
use App\Models\ProductVariant;
use App\Models\DesignTextLayer;
use App\Models\ProductPrintArea;
use App\Enums\OrderRequestStatus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderRequestStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_order_request_with_design_files_and_text_layers(): void
    {
        Storage::fake('public');

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
            'customer_comment' => 'Позвонить после 18:00',
            ...$this->validDeliveryData(),
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 1,
            'side' => 'front',
            'canvas_json' => [
                'layers' => [
                    [
                        'id' => 'text-1',
                        'type' => 'text',
                        'text' => 'CodeLifter',
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
            'assets' => [
                [
                    'layer_id' => 'image-1',
                    'file_name' => 'client-image.png',
                    'data' => $this->fakeBase64Png(),
                ],
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', OrderRequestStatus::New->value);

        $this->assertSame(1, OrderRequest::query()->count());
        $orderRequest = OrderRequest::query()->firstOrFail();
        $this->assertSame('Tashkent', $orderRequest->customer_city);
        $this->assertSame('Tashkent, Chilanzar 10', $orderRequest->customer_address);
        $this->assertSame('41.2995000', $orderRequest->delivery_lat);
        $this->assertSame('69.2401000', $orderRequest->delivery_lng);
        $this->assertSame(1, Design::query()->count());
        $this->assertSame(1, DesignTextLayer::query()->count());

        $design = Design::query()->firstOrFail();
        $this->assertNotNull($design->preview_image_path);
        $this->assertNotNull($design->print_image_path);
        Storage::disk('public')->assertExists($design->preview_image_path);
        Storage::disk('public')->assertExists($design->print_image_path);
        Storage::disk('public')->assertExists($design->assets()->firstOrFail()->original_file_path);
    }

    public function test_it_rejects_variant_from_another_product(): void
    {
        $product = Product::factory()->create();
        $anotherProduct = Product::factory()->create();
        $variant = ProductVariant::factory()->for($anotherProduct)->create();

        $this->postJson(route('order-requests.store'), [
            'customer_name' => 'Doniyor',
            'customer_phone' => '+998901234567',
            ...$this->validDeliveryData(),
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 1,
            'side' => 'front',
            'canvas_json' => [
                'layers' => [],
                'print_area' => [],
            ],
            'preview_image' => $this->fakeBase64Png(),
            'print_image' => $this->fakeBase64Png(),
        ])->assertUnprocessable();
    }

    public function test_it_creates_order_request_without_design_layers(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->for($product)->create();
        ProductPrintArea::factory()->for($variant)->create(['side' => 'front']);

        $this->postJson(route('order-requests.store'), [
            'customer_name' => 'Doniyor',
            'customer_phone' => '+998901234567',
            ...$this->validDeliveryData(),
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 1,
            'side' => 'front',
            'canvas_json' => [
                'layers' => [],
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
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', OrderRequestStatus::New->value);

        $this->assertSame(1, OrderRequest::query()->count());
        $this->assertSame(1, Design::query()->count());
        $this->assertSame(0, DesignTextLayer::query()->count());
    }

    public function test_it_creates_order_request_with_front_and_back_designs(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->for($product)->create();
        ProductPrintArea::factory()->for($variant)->create(['side' => 'front']);
        ProductPrintArea::factory()->for($variant)->create(['side' => 'back']);

        $this->postJson(route('order-requests.store'), [
            'customer_name' => 'Doniyor',
            'customer_phone' => '+998901234567',
            ...$this->validDeliveryData(),
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 1,
            'designs' => [
                [
                    'side' => 'front',
                    'canvas_json' => [
                        'layers' => [['id' => 'front-text', 'type' => 'text', 'text' => 'Front']],
                        'print_area' => ['x' => 0.32, 'y' => 0.27, 'width' => 0.36, 'height' => 0.42, 'unit' => 'ratio'],
                    ],
                    'preview_image' => $this->fakeBase64Png(),
                    'print_image' => $this->fakeBase64Png(),
                    'assets' => [],
                ],
                [
                    'side' => 'back',
                    'canvas_json' => [
                        'layers' => [['id' => 'back-text', 'type' => 'text', 'text' => 'Back']],
                        'print_area' => ['x' => 0.30, 'y' => 0.24, 'width' => 0.40, 'height' => 0.46, 'unit' => 'ratio'],
                    ],
                    'preview_image' => $this->fakeBase64Png(),
                    'print_image' => $this->fakeBase64Png(),
                    'assets' => [
                        [
                            'layer_id' => 'back-image',
                            'file_name' => 'back.png',
                            'data' => $this->fakeBase64Png(),
                        ],
                    ],
                ],
            ],
        ])->assertCreated();

        $this->assertSame(1, OrderRequest::query()->count());
        $this->assertSame(['back', 'front'], Design::query()->pluck('side')->sort()->values()->all());
        $this->assertSame(2, DesignTextLayer::query()->count());
        $this->assertSame(1, Design::query()->where('side', 'back')->firstOrFail()->assets()->count());
    }

    public function test_it_rejects_order_request_without_delivery_address(): void
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->for($product)->create();
        ProductPrintArea::factory()->for($variant)->create(['side' => 'front']);

        $this->postJson(route('order-requests.store'), [
            'customer_name' => 'Doniyor',
            'customer_phone' => '+998901234567',
            'customer_city' => 'Tashkent',
            'delivery_lat' => 41.2995,
            'delivery_lng' => 69.2401,
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 1,
            'side' => 'front',
            'canvas_json' => [
                'layers' => [],
                'print_area' => ['x' => 0.32, 'y' => 0.27, 'width' => 0.36, 'height' => 0.42, 'unit' => 'ratio'],
            ],
            'preview_image' => $this->fakeBase64Png(),
            'print_image' => $this->fakeBase64Png(),
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('customer_address');
    }

    public function test_it_rejects_order_request_outside_tashkent(): void
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->for($product)->create();
        ProductPrintArea::factory()->for($variant)->create(['side' => 'front']);

        $this->postJson(route('order-requests.store'), [
            'customer_name' => 'Doniyor',
            'customer_phone' => '+998901234567',
            'customer_city' => 'Tashkent',
            'customer_address' => 'Samarkand',
            'delivery_lat' => 39.6542,
            'delivery_lng' => 66.9597,
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 1,
            'side' => 'front',
            'canvas_json' => [
                'layers' => [],
                'print_area' => ['x' => 0.32, 'y' => 0.27, 'width' => 0.36, 'height' => 0.42, 'unit' => 'ratio'],
            ],
            'preview_image' => $this->fakeBase64Png(),
            'print_image' => $this->fakeBase64Png(),
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('delivery_lat');
    }

    public function test_html_order_request_redirects_to_localized_home_catalog(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->for($product)->create();
        ProductPrintArea::factory()->for($variant)->create(['side' => 'front']);

        $this->post('/ru/order-requests', [
            'customer_name' => 'Doniyor',
            'customer_phone' => '+998901234567',
            ...$this->validDeliveryData(),
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 1,
            'side' => 'front',
            'canvas_json' => [
                'layers' => [
                    ['id' => 'text-1', 'type' => 'text', 'text' => 'PrintLab'],
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
        ])->assertRedirect('/ru');
    }

    /**
     * @return array<string, mixed>
     */
    private function validDeliveryData(): array
    {
        return [
            'customer_city' => 'Tashkent',
            'customer_address' => 'Tashkent, Chilanzar 10',
            'delivery_lat' => 41.2995,
            'delivery_lng' => 69.2401,
        ];
    }

    private function fakeBase64Png(): string
    {
        return 'data:image/png;base64,'.base64_encode(base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p94AAAAASUVORK5CYII='
        ));
    }
}
