<?php

namespace App\Services\Telegram;

final class FakeTelegramAuthGateway implements TelegramAuthGateway
{
    public array $sentMessages = [];

    public function __construct(
        private string $botUsername = 'PrintLabUzBot',
    ) {}

    public function sendMessage(int $chatId, string $text): void
    {
        $this->sentMessages[] = ['chat_id' => $chatId, 'text' => $text];
    }

    public function getBotUsername(): string
    {
        return $this->botUsername;
    }

    public function setBotUsername(string $username): void
    {
        $this->botUsername = $username;
    }

    /**
     * @return array<string, mixed>
     */
    public function simulateStartCommand(
        string $token,
        int $telegramId = 123456789,
        ?string $firstName = 'Test',
        ?string $lastName = 'User',
        ?string $username = 'test_user',
    ): array {
        return [
            'message' => [
                'text' => '/start '.$token,
                'from' => [
                    'id' => $telegramId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                ],
            ],
        ];
    }
}
