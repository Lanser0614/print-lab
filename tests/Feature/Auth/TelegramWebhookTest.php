<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\OrderRequest;
use App\Enums\OrderRequestStatus;
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

    public function test_webhook_approves_order_from_callback_query(): void
    {
        config()->set('services.telegram.webhook_secret', 'secret-token');

        $gateway = new FakeTelegramAuthGateway('PrintLabUzBot');
        $this->app->instance(TelegramAuthGateway::class, $gateway);

        $orderRequest = OrderRequest::factory()->create([
            'status' => OrderRequestStatus::New,
            'approved_at' => null,
        ]);

        $this->postJson(route('telegram.webhook'), [
            'callback_query' => [
                'id' => 'callback-1',
                'data' => 'order:approve:'.$orderRequest->id,
            ],
        ], [
            'X-Telegram-Bot-Api-Secret-Token' => 'secret-token',
        ])->assertOk();

        $orderRequest->refresh();

        $this->assertSame(OrderRequestStatus::WaitingPayment, $orderRequest->status);
        $this->assertNotNull($orderRequest->approved_at);
        $this->assertSame('Заказ #'.$orderRequest->id.' одобрен.', $gateway->answeredCallbackQueries[0]['text']);
    }

    public function test_webhook_answers_order_phone_from_callback_query(): void
    {
        config()->set('services.telegram.webhook_secret', 'secret-token');

        $gateway = new FakeTelegramAuthGateway('PrintLabUzBot');
        $this->app->instance(TelegramAuthGateway::class, $gateway);

        $orderRequest = OrderRequest::factory()->create([
            'customer_phone' => '+998901234567',
        ]);

        $this->postJson(route('telegram.webhook'), [
            'callback_query' => [
                'id' => 'callback-phone',
                'data' => 'order:call:'.$orderRequest->id,
            ],
        ], [
            'X-Telegram-Bot-Api-Secret-Token' => 'secret-token',
        ])->assertOk();

        $this->assertSame('Телефон: +998901234567', $gateway->answeredCallbackQueries[0]['text']);
    }

    public function test_webhook_rejects_order_from_callback_query(): void
    {
        config()->set('services.telegram.webhook_secret', 'secret-token');

        $gateway = new FakeTelegramAuthGateway('PrintLabUzBot');
        $this->app->instance(TelegramAuthGateway::class, $gateway);

        $orderRequest = OrderRequest::factory()->create([
            'status' => OrderRequestStatus::New,
            'cancelled_at' => null,
        ]);

        $this->postJson(route('telegram.webhook'), [
            'callback_query' => [
                'id' => 'callback-2',
                'data' => 'order:reject:'.$orderRequest->id,
            ],
        ], [
            'X-Telegram-Bot-Api-Secret-Token' => 'secret-token',
        ])->assertOk();

        $orderRequest->refresh();

        $this->assertSame(OrderRequestStatus::Cancelled, $orderRequest->status);
        $this->assertNotNull($orderRequest->cancelled_at);
        $this->assertSame('Заказ #'.$orderRequest->id.' отклонён.', $gateway->answeredCallbackQueries[0]['text']);
    }
}
