<?php

namespace App\Services\Telegram\Dto;

final readonly class TelegramUser
{
    public function __construct(
        public int $telegramId,
        public ?string $firstName,
        public ?string $lastName,
        public ?string $username,
        public ?string $photoUrl,
    ) {}

    public static function fromUpdate(array $from): self
    {
        return new self(
            telegramId: (int) ($from['id'] ?? 0),
            firstName: $from['first_name'] ?? null,
            lastName: $from['last_name'] ?? null,
            username: $from['username'] ?? null,
            photoUrl: null,
        );
    }
}
