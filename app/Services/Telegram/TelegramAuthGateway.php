<?php

namespace App\Services\Telegram;

interface TelegramAuthGateway
{
    public function sendMessage(int $chatId, string $text): void;

    public function getBotUsername(): string;
}
