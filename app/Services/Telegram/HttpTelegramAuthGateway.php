<?php

namespace App\Services\Telegram;

use Illuminate\Http\Client\Response;
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

    public function sendMessage(int|string $chatId, string $text, array $options = []): void
    {
        $this->throwIfFailed(Http::post("{$this->apiBase}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
        ] + $options));
    }

    public function answerCallbackQuery(string $callbackQueryId, string $text): void
    {
        $this->throwIfFailed(Http::post("{$this->apiBase}/answerCallbackQuery", [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
        ]));
    }

    public function getBotUsername(): string
    {
        return $this->botUsername;
    }

    private function throwIfFailed(Response $response): void
    {
        $response->throw();
    }
}
