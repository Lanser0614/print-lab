<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\OrderRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
            ->assertSee('pl-topbar', false)
            ->assertSee('pl-header', false)
            ->assertSee('pl-nav', false)
            ->assertSee('pl-account-page', false)
            ->assertSee('pl-account-order', false)
            ->assertSee('#'.$ownOrder->id)
            ->assertSee('Own customer')
            ->assertDontSee('#'.$otherOrder->id)
            ->assertDontSee('Other customer');
    }

    public function test_authenticated_user_sees_order_request_detail_in_storefront_chrome(): void
    {
        $user = User::factory()->create();
        $order = OrderRequest::factory()->create([
            'user_id' => $user->id,
            'customer_name' => 'Detail customer',
            'customer_phone' => '+998 90 000 00 00',
            'customer_comment' => 'Call before delivery',
        ]);

        $this->actingAs($user)
            ->get(route('account.order-requests.show', $order))
            ->assertOk()
            ->assertSee('pl-topbar', false)
            ->assertSee('pl-header', false)
            ->assertSee('pl-nav', false)
            ->assertSee('pl-account-detail', false)
            ->assertSee('#'.$order->id)
            ->assertSee('Detail customer')
            ->assertSee('+998 90 000 00 00')
            ->assertSee('Call before delivery');
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
