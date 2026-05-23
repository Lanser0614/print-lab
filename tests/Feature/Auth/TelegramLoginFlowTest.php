<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\TelegramLoginToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TelegramLoginFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_matches_main_site_chrome(): void
    {
        $this->get('/uz/login')
            ->assertOk()
            ->assertSee('pl-topbar', false)
            ->assertSee('pl-header', false)
            ->assertSee('pl-nav', false)
            ->assertSee('pl-login-card', false)
            ->assertSee('Telegram orqali kirish', false)
            ->assertSee('auth/telegram/start', false)
            ->assertSee('telegram-login-btn', false)
            ->assertSee('window.open(data.deep_link', false)
            ->assertSee('/uz/catalog', false)
            ->assertDontSee('Sevimlilar', false)
            ->assertDontSee('Savat', false);
    }

    public function test_start_endpoint_creates_pending_token_and_returns_deep_link(): void
    {
        config()->set('services.telegram.bot_username', 'PrintLabUzBot');

        $response = $this->postJson(route('auth.telegram.start'));

        $response
            ->assertOk()
            ->assertJsonPath('deep_link', fn (string $deepLink): bool => str_starts_with($deepLink, 'https://t.me/PrintLabUzBot?start='))
            ->assertJsonStructure(['token', 'deep_link', 'expires_at']);

        $this->assertDatabaseHas('telegram_login_tokens', [
            'token' => $response->json('token'),
            'status' => 'pending',
        ]);
    }

    public function test_poll_returns_confirmed_once_and_logs_user_in(): void
    {
        $user = User::factory()->create(['telegram_id' => 987654321]);
        $token = TelegramLoginToken::query()->create([
            'token' => str_repeat('a', 64),
            'status' => 'confirmed',
            'user_id' => $user->id,
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->assertFalse($token->fresh()->isExpired());

        $this->getJson(route('auth.telegram.poll', ['token' => $token->token]))
            ->assertOk()
            ->assertJsonPath('status', 'confirmed');

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('telegram_login_tokens', [
            'id' => $token->id,
            'status' => 'expired',
        ]);

        auth()->logout();

        $this->getJson(route('auth.telegram.poll', ['token' => $token->token]))
            ->assertGone()
            ->assertJsonPath('status', 'expired');
    }
}
