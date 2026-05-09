<?php

namespace Tests\Feature\Auth;

use App\Models\OrderRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('account.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_sees_their_order_requests_only(): void
    {
        $user = User::factory()->create();
        $ownOrder = OrderRequest::factory()->create([
            'user_id' => $user->id,
            'customer_name' => 'Own customer',
        ]);
        $otherOrder = OrderRequest::factory()->create([
            'user_id' => User::factory()->create()->id,
            'customer_name' => 'Other customer',
        ]);

        $this->actingAs($user)
            ->get(route('account.index'))
            ->assertOk()
            ->assertSee('#'.$ownOrder->id)
            ->assertSee('Own customer')
            ->assertDontSee('#'.$otherOrder->id)
            ->assertDontSee('Other customer');
    }

    public function test_authenticated_user_cannot_open_other_users_order_request(): void
    {
        $user = User::factory()->create();
        $otherOrder = OrderRequest::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);

        $this->actingAs($user)
            ->get(route('account.order-requests.show', $otherOrder))
            ->assertNotFound();
    }
}
