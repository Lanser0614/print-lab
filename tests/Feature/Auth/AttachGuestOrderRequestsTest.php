<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\OrderRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\UseCases\Auth\AttachGuestOrderRequestsUseCase;

class AttachGuestOrderRequestsTest extends TestCase
{
    use RefreshDatabase;

    public function test_attaches_guest_order_requests_with_matching_phone(): void
    {
        $user = User::factory()->create(['phone' => '+998901234567']);
        $matching = OrderRequest::factory()->create(['customer_phone' => '+998901234567', 'user_id' => null]);
        $otherPhone = OrderRequest::factory()->create(['customer_phone' => '+998901111111', 'user_id' => null]);
        $alreadyOwned = OrderRequest::factory()->create([
            'customer_phone' => '+998901234567',
            'user_id' => User::factory()->create()->id,
        ]);

        $attachedCount = app(AttachGuestOrderRequestsUseCase::class)->execute($user);

        $this->assertSame(1, $attachedCount);
        $this->assertSame($user->id, $matching->fresh()->user_id);
        $this->assertNull($otherPhone->fresh()->user_id);
        $this->assertNotSame($user->id, $alreadyOwned->fresh()->user_id);
    }
}
