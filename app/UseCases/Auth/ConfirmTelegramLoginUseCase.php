<?php

namespace App\UseCases\Auth;

use App\Models\TelegramLoginToken;
use App\Models\User;
use App\Services\Telegram\Dto\TelegramUser;

final readonly class ConfirmTelegramLoginUseCase
{
    public function __construct(
        private AttachGuestOrderRequestsUseCase $attachGuestUseCase,
    ) {}

    public function execute(TelegramLoginToken $token, TelegramUser $telegramUser): User
    {
        $user = User::query()->firstOrCreate(
            ['telegram_id' => $telegramUser->telegramId],
            [
                'name' => $telegramUser->firstName ?? $telegramUser->username ?? 'User',
                'first_name' => $telegramUser->firstName,
                'last_name' => $telegramUser->lastName,
                'telegram_username' => $telegramUser->username,
                'photo_url' => $telegramUser->photoUrl,
                'locale' => app()->getLocale(),
            ],
        );

        if ($user->wasRecentlyCreated === false) {
            $user->update([
                'first_name' => $telegramUser->firstName ?? $user->first_name,
                'last_name' => $telegramUser->lastName ?? $user->last_name,
                'telegram_username' => $telegramUser->username ?? $user->telegram_username,
                'photo_url' => $telegramUser->photoUrl ?? $user->photo_url,
            ]);
        }

        $token->markConfirmed($user->id);

        if ($user->phone !== null) {
            $this->attachGuestUseCase->execute($user);
        }

        return $user;
    }
}
