<?php

namespace App\Services\Telegram;

use App\Services\Telegram\Dto\TelegramUser;

interface TelegramAuthGateway
{
    public function sendMessage(int $chatId, string $text): void;

    public function getBotUsername(): string;
}
