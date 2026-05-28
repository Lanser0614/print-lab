<?php

namespace App\Services\Telegram;

interface TelegramAuthGateway
{
    /**
     * @param array<string, mixed> $options
     */
    public function sendMessage(int|string $chatId, string $text, array $options = []): void;

    public function answerCallbackQuery(string $callbackQueryId, string $text): void;

    public function getBotUsername(): string;
}
