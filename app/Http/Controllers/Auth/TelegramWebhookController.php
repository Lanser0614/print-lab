<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Telegram\TelegramWebhookHandler;

class TelegramWebhookController extends Controller
{
    public function __construct(
        private TelegramWebhookHandler $handler,
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $secret = (string) config('services.telegram.webhook_secret', '');
        $requestSecret = (string) $request->header('X-Telegram-Bot-Api-Secret-Token', '');

        if ($secret === '' || ! hash_equals($secret, $requestSecret)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $this->handler->handle($request->all());

        return response()->json(['ok' => true]);
    }
}
