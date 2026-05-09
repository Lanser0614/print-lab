<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\TelegramLoginToken;
use App\Services\Telegram\TelegramAuthGateway;
use App\Services\Telegram\FakeTelegramAuthGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TelegramWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_rejects_request_without_secret_header(): void
    {
        config()->set('services.telegram.webhook_secret', 'secret-token');

        $this->postJson(route('telegram.webhook'), [])
            ->assertUnauthorized();
    }

    public function test_webhook_rejects_request_when_secret_is_not_configured(): void
    {
        config()->set('services.telegram.webhook_secret', '');

        $this->postJson(route('telegram.webhook'), [], [
            'X-Telegram-Bot-Api-Secret-Token' => 'anything',
        ])->assertUnauthorized();
    }

    public function test_webhook_with_valid_start_token_confirms_login(): void
    {
        config()->set('services.telegram.webhook_secret', 'secret-token');

        $gateway = new FakeTelegramAuthGateway('PrintLabUzBot');
        $this->app->instance(TelegramAuthGateway::class, $gateway);

        $token = TelegramLoginToken::query()->create([
            'token' => str_repeat('b', 64),
            'status' => 'pending',
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->postJson(route('telegram.webhook'), $gateway->simulateStartCommand(
            token: $token->token,
            telegramId: 111222333,
            firstName: 'Ali',
            lastName: 'Valiyev',
            username: 'ali_v',
        ), [
            'X-Telegram-Bot-Api-Secret-Token' => 'secret-token',
        ])->assertOk();

        $this->assertDatabaseHas('users', [
            'telegram_id' => 111222333,
            'first_name' => 'Ali',
            'last_name' => 'Valiyev',
            'telegram_username' => 'ali_v',
        ]);

        $this->assertDatabaseHas('telegram_login_tokens', [
            'id' => $token->id,
            'status' => 'confirmed',
        ]);

        $this->assertCount(1, $gateway->sentMessages);
    }
}
