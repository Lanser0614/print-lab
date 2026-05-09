<?php

namespace App\UseCases\Auth;

use App\Models\TelegramLoginToken;
use App\Services\Telegram\TelegramAuthGateway;

final readonly class StartTelegramLoginUseCase
{
    public function __construct(
        private TelegramAuthGateway $gateway,
    ) {}

    public function execute(?string $redirectTo = null, ?string $ipAddress = null, ?string $userAgent = null): array
    {
        $token = bin2hex(random_bytes(32));
        $ttl = (int) config('services.telegram.login_token_ttl', 300);

        TelegramLoginToken::query()->create([
            'token' => $token,
            'status' => 'pending',
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'redirect_to' => $redirectTo,
            'expires_at' => now()->addSeconds($ttl),
        ]);

        $deepLink = sprintf('https://t.me/%s?start=%s', $this->gateway->getBotUsername(), $token);

        return [
            'token' => $token,
            'deep_link' => $deepLink,
            'expires_at' => now()->addSeconds($ttl)->toIso8601String(),
        ];
    }
}
