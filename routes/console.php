<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('telegram:webhook:set', function (): int {
    $botToken = (string) config('services.telegram.bot_token', '');
    $secret = (string) config('services.telegram.webhook_secret', '');

    if ($botToken === '') {
        $this->error('TELEGRAM_BOT_TOKEN is not configured.');

        return self::FAILURE;
    }

    if ($secret === '') {
        $this->error('TELEGRAM_WEBHOOK_SECRET is not configured.');

        return self::FAILURE;
    }

    $webhookUrl = url('/telegram/webhook');
    $response = Http::post("https://api.telegram.org/bot{$botToken}/setWebhook", [
        'url' => $webhookUrl,
        'secret_token' => $secret,
    ]);

    if ($response->failed()) {
        $this->error('Telegram rejected the webhook configuration.');

        return self::FAILURE;
    }

    $this->info("Telegram webhook set to {$webhookUrl}.");

    return self::SUCCESS;
})->purpose('Register the Telegram login webhook URL');
