<?php

namespace App\Services\Telegram;

final class FakeTelegramAuthGateway implements TelegramAuthGateway
{
    public array $sentMessages = [];

    public array $sentPhotos = [];

    public array $answeredCallbackQueries = [];

    public function __construct(
        private string $botUsername = 'PrintLabUzBot',
    ) {}

    public function sendMessage(int|string $chatId, string $text, array $options = []): void
    {
        $this->sentMessages[] = ['chat_id' => $chatId, 'text' => $text, 'options' => $options];
    }

    public function sendPhoto(int|string $chatId, string $photoPath, string $caption, array $options = []): void
    {
        $this->sentPhotos[] = [
            'chat_id' => $chatId,
            'photo_path' => $photoPath,
            'caption' => $caption,
            'options' => $options,
        ];
    }

    public function answerCallbackQuery(string $callbackQueryId, string $text): void
    {
        $this->answeredCallbackQueries[] = ['callback_query_id' => $callbackQueryId, 'text' => $text];
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
