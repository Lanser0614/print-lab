<?php

namespace Tests\Feature;

use App\Enums\OrderRequestStatus;
use App\Models\OrderRequest;
use App\Models\User;
use App\UseCases\OrderRequests\TakeNextOrderRequestUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TakeNextOrderRequestUseCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_does_not_allow_admin_to_take_next_order_while_they_have_processing_order(): void
    {
        $admin = User::factory()->create();

        $current = OrderRequest::factory()->create([
            'status' => OrderRequestStatus::Processing,
            'assigned_admin_id' => $admin->id,
        ]);

        $next = OrderRequest::factory()->create([
            'status' => OrderRequestStatus::New,
            'assigned_admin_id' => null,
        ]);

        $result = app(TakeNextOrderRequestUseCase::class)->execute($admin);

        $this->assertSame($current->id, $result->id);
        $this->assertSame(OrderRequestStatus::New, $next->fresh()->status);
        $this->assertNull($next->fresh()->assigned_admin_id);
    }

    public function test_it_allows_admin_to_take_next_order_when_previous_order_is_waiting_payment(): void
    {
        $admin = User::factory()->create();

        OrderRequest::factory()->create([
            'status' => OrderRequestStatus::WaitingPayment,
            'assigned_admin_id' => $admin->id,
        ]);

        $next = OrderRequest::factory()->create([
            'status' => OrderRequestStatus::New,
            'assigned_admin_id' => null,
        ]);

        $result = app(TakeNextOrderRequestUseCase::class)->execute($admin);

        $this->assertSame($next->id, $result->id);
        $this->assertSame(OrderRequestStatus::Processing, $result->fresh()->status);
        $this->assertSame($admin->id, $result->fresh()->assigned_admin_id);
    }
}
