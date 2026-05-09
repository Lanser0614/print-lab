<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TelegramWebhookCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_set_command_requires_bot_token(): void
    {
        config()->set('services.telegram.bot_token', '');
        config()->set('services.telegram.webhook_secret', 'secret-token');

        $this->artisan('telegram:webhook:set')
            ->expectsOutput('TELEGRAM_BOT_TOKEN is not configured.')
            ->assertFailed();
    }

    public function test_webhook_set_command_calls_telegram_api(): void
    {
        Http::fake([
            'api.telegram.org/*/setWebhook' => Http::response(['ok' => true]),
        ]);

        config()->set('services.telegram.bot_token', 'bot-token');
        config()->set('services.telegram.webhook_secret', 'secret-token');
        $webhookUrl = url('/telegram/webhook');

        $this->artisan('telegram:webhook:set')
            ->assertSuccessful();

        Http::assertSent(fn ($request): bool => $request->url() === 'https://api.telegram.org/botbot-token/setWebhook'
            && $request['url'] === $webhookUrl
            && $request['secret_token'] === 'secret-token');
    }
}
