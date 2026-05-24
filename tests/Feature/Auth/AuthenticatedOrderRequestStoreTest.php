<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderRequest;
use App\Models\ProductVariant;
use App\Models\ProductPrintArea;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticatedOrderRequestStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_creates_order_request_with_user_id_set(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $payload = $this->validOrderRequestPayload();

        $this->actingAs($user)
            ->postJson(route('order-requests.store'), $payload)
            ->assertCreated();

        $this->assertSame($user->id, OrderRequest::query()->firstOrFail()->user_id);
    }

    public function test_guest_user_still_creates_order_request_without_user_id(): void
    {
        Storage::fake('public');

        $this->postJson(route('order-requests.store'), $this->validOrderRequestPayload())
            ->assertCreated();

        $this->assertNull(OrderRequest::query()->firstOrFail()->user_id);
    }

    /**
     * @return array<string, mixed>
     */
    private function validOrderRequestPayload(): array
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->for($product)->create();
        ProductPrintArea::factory()->for($variant)->create([
            'side' => 'front',
            'x' => 0.32,
            'y' => 0.27,
            'width' => 0.36,
            'height' => 0.42,
        ]);

        return [
            'customer_name' => 'Doniyor',
            'customer_phone' => '+998901234567',
            'customer_comment' => 'Позвонить после 18:00',
            'customer_city' => 'Tashkent',
            'customer_address' => 'Tashkent, Chilanzar 10',
            'delivery_lat' => 41.2995,
            'delivery_lng' => 69.2401,
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
                        'fontFamily' => 'Manrope',
                        'fontSize' => 32,
                        'color' => '#111111',
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
        ];
    }

    private function fakeBase64Png(): string
    {
        return 'data:image/png;base64,'.base64_encode(base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p94AAAAASUVORK5CYII='
        ));
    }
}
