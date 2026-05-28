<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Services\Telegram\HttpTelegramAuthGateway;

class TelegramHttpGatewayTest extends TestCase
{
    public function test_send_photo_serializes_reply_markup_for_multipart_request(): void
    {
        Http::fake([
            'api.telegram.org/*/sendPhoto' => Http::response(['ok' => true]),
        ]);

        $photoPath = tempnam(sys_get_temp_dir(), 'telegram-photo-');
        file_put_contents($photoPath, 'fake image');

        try {
            $gateway = new HttpTelegramAuthGateway('bot-token', 'PrintLabUzBot');

            $gateway->sendPhoto('-5178998724', $photoPath, 'Новый заказ', [
                'reply_markup' => [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Позвонить', 'callback_data' => 'order:call:1'],
                        ],
                    ],
                ],
            ]);
        } finally {
            @unlink($photoPath);
        }

        Http::assertSent(function ($request): bool {
            $body = $request->body();

            return $request->url() === 'https://api.telegram.org/botbot-token/sendPhoto'
                && $request->isMultipart()
                && str_contains($body, 'name="reply_markup"')
                && str_contains($body, '{"inline_keyboard":[[{"text":"Позвонить","callback_data":"order:call:1"}]]}');
        });
    }
}
