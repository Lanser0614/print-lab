<?php

namespace App\Services\Telegram;

use RuntimeException;
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

    public function sendPhoto(int|string $chatId, string $photoPath, string $caption, array $options = []): void
    {
        $contents = file_get_contents($photoPath);

        if ($contents === false) {
            throw new RuntimeException('Unable to open Telegram photo attachment.');
        }

        $this->throwIfFailed(Http::attach(
            'photo',
            $contents,
            basename($photoPath),
        )->post("{$this->apiBase}/sendPhoto", [
            'chat_id' => $chatId,
            'caption' => $caption,
        ] + $this->multipartOptions($options)));
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

    /**
     * Telegram expects nested multipart fields, such as reply_markup, as JSON strings.
     *
     * @param  array<string, mixed> $options
     * @return array<string, mixed>
     */
    private function multipartOptions(array $options): array
    {
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                $options[$key] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            }
        }

        return $options;
    }
}
