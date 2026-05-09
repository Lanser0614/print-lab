<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Telegram\TelegramAuthGateway;
use App\Services\Telegram\FakeTelegramAuthGateway;
use App\Services\Telegram\HttpTelegramAuthGateway;

class TelegramAuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TelegramAuthGateway::class, function (): TelegramAuthGateway {
            $driver = config('services.telegram.driver', 'fake');

            if ($driver === 'http') {
                return new HttpTelegramAuthGateway(
                    botToken: config('services.telegram.bot_token', ''),
                    botUsername: config('services.telegram.bot_username') ?: 'PrintLabUzBot',
                );
            }

            return new FakeTelegramAuthGateway(
                botUsername: config('services.telegram.bot_username') ?: 'PrintLabUzBot',
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
