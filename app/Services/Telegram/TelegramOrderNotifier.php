<?php

namespace App\Services\Telegram;

use Throwable;
use App\Models\OrderRequest;
use Illuminate\Support\Facades\Log;

final readonly class TelegramOrderNotifier
{
    public function __construct(
        private TelegramAuthGateway $gateway,
    ) {}

    public function notifyNewOrder(OrderRequest $orderRequest): void
    {
        $channelId = config('services.telegram.merchant_channel_id');

        if (! $channelId) {
            return;
        }

        try {
            $orderRequest->loadMissing('items');

            $this->gateway->sendMessage(
                $channelId,
                $this->messageText($orderRequest),
                [
                    'reply_markup' => [
                        'inline_keyboard' => [
                            [
                                [
                                    'text' => 'Позвонить',
                                    'callback_data' => 'order:call:'.$orderRequest->id,
                                ],
                            ],
                            [
                                [
                                    'text' => 'Одобрить',
                                    'callback_data' => 'order:approve:'.$orderRequest->id,
                                ],
                                [
                                    'text' => 'Отклонить',
                                    'callback_data' => 'order:reject:'.$orderRequest->id,
                                ],
                            ],
                        ],
                    ],
                ],
            );
        } catch (Throwable $exception) {
            Log::warning('Failed to send Telegram order notification.', [
                'order_request_id' => $orderRequest->id,
                'exception_class' => $exception::class,
                'exception_message' => $this->redactBotToken($exception->getMessage()),
            ]);
        }
    }

    private function messageText(OrderRequest $orderRequest): string
    {
        $items = $orderRequest->items
            ->map(function ($item): string {
                $variant = trim($item->color_snapshot.' '.($item->size_snapshot ? '/ '.$item->size_snapshot : ''));

                return sprintf(
                    '- %s%s x %d',
                    $item->product_name_snapshot,
                    $variant ? ' ('.$variant.')' : '',
                    $item->quantity,
                );
            })
            ->implode("\n");

        return trim(implode("\n", array_filter([
            'Новый заказ #'.$orderRequest->id,
            'Клиент: '.$orderRequest->customer_name,
            'Телефон: '.$orderRequest->customer_phone,
            'Адрес: '.$orderRequest->customer_address,
            $orderRequest->customer_comment ? 'Комментарий: '.$orderRequest->customer_comment : null,
            $items ? "Товары:\n".$items : null,
        ])));
    }

    private function redactBotToken(string $message): string
    {
        $botToken = (string) config('services.telegram.bot_token', '');

        if ($botToken === '') {
            return $message;
        }

        return str_replace($botToken, '[redacted]', $message);
    }
}
