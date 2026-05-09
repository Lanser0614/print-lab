<?php

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Http;

final readonly class HttpTelegramAuthGateway implements TelegramAuthGateway
{
    private string $apiBase;

    public function __construct(
        private string $botToken,
        private string $botUsername,
    ) {
        $this->apiBase = "https://api.telegram.org/bot{$botToken}";
    }

    public function sendMessage(int $chatId, string $text): void
    {
        Http::post("{$this->apiBase}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }

    public function getBotUsername(): string
    {
        return $this->botUsername;
    }
}
