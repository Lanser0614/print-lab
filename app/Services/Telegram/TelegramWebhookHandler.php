<?php

namespace App\Services\Telegram;

use App\Models\TelegramLoginToken;
use App\Services\Telegram\Dto\TelegramUser;
use App\UseCases\Auth\ConfirmTelegramLoginUseCase;

final readonly class TelegramWebhookHandler
{
    public function __construct(
        private TelegramAuthGateway $gateway,
        private ConfirmTelegramLoginUseCase $confirmUseCase,
    ) {}

    public function handle(array $update): void
    {
        $message = $update['message'] ?? [];

        if (! isset($message['text']) || ! str_starts_with((string) $message['text'], '/start ')) {
            return;
        }

        $tokenValue = substr((string) $message['text'], 7);
        $from = $message['from'] ?? [];

        if ($tokenValue === '' || ! isset($from['id'])) {
            return;
        }

        $token = TelegramLoginToken::query()
            ->where('token', $tokenValue)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (! $token) {
            if (isset($from['id'])) {
                $this->gateway->sendMessage((int) $from['id'], 'Ссылка устарела. Попробуйте войти снова на сайте.');
            }

            return;
        }

        $telegramUser = TelegramUser::fromUpdate($from);

        $this->confirmUseCase->execute($token, $telegramUser);

        $this->gateway->sendMessage($telegramUser->telegramId, '✅ Вход подтверждён. Возвращайтесь на сайт.');
    }
}
