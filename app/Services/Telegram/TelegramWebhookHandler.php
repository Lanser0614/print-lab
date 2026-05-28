<?php

namespace App\Services\Telegram;

use App\Models\OrderRequest;
use App\Enums\OrderRequestStatus;
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
        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);

            return;
        }

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

    /**
     * @param array<string, mixed> $callbackQuery
     */
    private function handleCallbackQuery(array $callbackQuery): void
    {
        $callbackQueryId = (string) ($callbackQuery['id'] ?? '');
        $data = (string) ($callbackQuery['data'] ?? '');

        if (! preg_match('/^order:(call|approve|reject):(\d+)$/', $data, $matches)) {
            return;
        }

        $orderRequest = OrderRequest::query()->find((int) $matches[2]);

        if (! $orderRequest) {
            $this->answerCallbackQuery($callbackQueryId, 'Заказ не найден.');

            return;
        }

        if ($matches[1] === 'call') {
            $this->answerCallbackQuery($callbackQueryId, 'Телефон: '.$orderRequest->customer_phone);

            return;
        }

        if ($matches[1] === 'approve') {
            $orderRequest->update([
                'status' => OrderRequestStatus::WaitingPayment,
                'approved_at' => $orderRequest->approved_at ?? now(),
            ]);

            $this->answerCallbackQuery($callbackQueryId, 'Заказ #'.$orderRequest->id.' одобрен.');

            return;
        }

        $orderRequest->update([
            'status' => OrderRequestStatus::Cancelled,
            'cancelled_at' => $orderRequest->cancelled_at ?? now(),
        ]);

        $this->answerCallbackQuery($callbackQueryId, 'Заказ #'.$orderRequest->id.' отклонён.');
    }

    private function answerCallbackQuery(string $callbackQueryId, string $text): void
    {
        if ($callbackQueryId === '') {
            return;
        }

        $this->gateway->answerCallbackQuery($callbackQueryId, $text);
    }
}
