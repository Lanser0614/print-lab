<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Design;
use App\Models\Product;
use App\Models\ReadyPrint;
use App\Models\OrderRequest;
use App\Models\ProductVariant;
use App\Models\OrderRequestItem;
use App\Enums\OrderRequestStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\UseCases\ReadyPrints\CreateReadyPrintFromOrderRequestUseCase;

class ReadyPrintFromReadyOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_ready_order_with_preview_creates_ready_print(): void
    {
        $orderRequest = $this->orderRequestWithDesign(OrderRequestStatus::Ready, 'ready-prints/from-order.png');

        $readyPrint = app(CreateReadyPrintFromOrderRequestUseCase::class)->handle($orderRequest);

        $this->assertInstanceOf(ReadyPrint::class, $readyPrint);
        $this->assertDatabaseHas('ready_prints', [
            'order_request_id' => $orderRequest->id,
            'source_design_id' => $orderRequest->items->first()->design->id,
            'image_path' => 'ready-prints/from-order.png',
            'is_active' => true,
        ]);
        $this->assertSame($orderRequest->id, $readyPrint->orderRequest->id);
        $this->assertSame($orderRequest->items->first()->design->id, $readyPrint->sourceDesign->id);
    }

    public function test_status_transition_to_ready_creates_ready_print(): void
    {
        $orderRequest = $this->orderRequestWithDesign(OrderRequestStatus::Paid, 'ready-prints/transition.png');

        $orderRequest->update(['status' => OrderRequestStatus::Ready]);

        $this->assertDatabaseHas('ready_prints', [
            'order_request_id' => $orderRequest->id,
            'source_design_id' => $orderRequest->items->first()->design->id,
            'image_path' => 'ready-prints/transition.png',
        ]);
    }

    public function test_ready_print_creation_is_idempotent_per_design(): void
    {
        $orderRequest = $this->orderRequestWithDesign(OrderRequestStatus::Ready, 'ready-prints/idempotent.png');
        $useCase = app(CreateReadyPrintFromOrderRequestUseCase::class);

        $first = $useCase->handle($orderRequest);
        $second = $useCase->handle($orderRequest->fresh());

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, ReadyPrint::query()->where('source_design_id', $first->source_design_id)->count());
    }

    public function test_non_ready_order_does_not_create_ready_print(): void
    {
        $orderRequest = $this->orderRequestWithDesign(OrderRequestStatus::Paid, 'ready-prints/paid.png');

        $readyPrint = app(CreateReadyPrintFromOrderRequestUseCase::class)->handle($orderRequest);

        $this->assertNull($readyPrint);
        $this->assertDatabaseCount('ready_prints', 0);
    }

    public function test_ready_order_without_preview_does_not_create_ready_print(): void
    {
        $orderRequest = $this->orderRequestWithDesign(OrderRequestStatus::Ready, null);

        $readyPrint = app(CreateReadyPrintFromOrderRequestUseCase::class)->handle($orderRequest);

        $this->assertNull($readyPrint);
        $this->assertDatabaseCount('ready_prints', 0);
    }

    public function test_manual_ready_print_can_have_no_order_source(): void
    {
        $readyPrint = ReadyPrint::factory()->create([
            'order_request_id' => null,
            'source_design_id' => null,
        ]);

        $this->assertNull($readyPrint->orderRequest);
        $this->assertNull($readyPrint->sourceDesign);
    }

    private function orderRequestWithDesign(OrderRequestStatus $status, ?string $previewPath): OrderRequest
    {
        $product = Product::factory()->create(['name' => 'Футболка']);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);
        $orderRequest = OrderRequest::factory()->create(['status' => $status]);
        $item = OrderRequestItem::query()->create([
            'order_request_id' => $orderRequest->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'product_name_snapshot' => 'Футболка',
            'product_type_snapshot' => $product->type->value,
            'color_snapshot' => $variant->color,
            'size_snapshot' => $variant->size,
            'quantity' => 1,
        ]);

        Design::query()->create([
            'order_request_item_id' => $item->id,
            'side' => 'front',
            'canvas_json' => ['layers' => []],
            'preview_image_path' => $previewPath,
            'print_image_path' => null,
        ]);

        return $orderRequest->fresh(['items.design']);
    }
}
