<?php

namespace Tests\Unit\Auth;

use App\Services\Telegram\FakeTelegramAuthGateway;
use PHPUnit\Framework\TestCase;

class FakeTelegramAuthGatewayTest extends TestCase
{
    public function test_simulates_start_command(): void
    {
        $gateway = new FakeTelegramAuthGateway();

        $update = $gateway->simulateStartCommand(
            token: 'token-value',
            telegramId: 123,
            firstName: 'Ali',
            lastName: 'Valiyev',
            username: 'ali_v',
        );

        $this->assertSame('/start token-value', $update['message']['text']);
        $this->assertSame(123, $update['message']['from']['id']);
        $this->assertSame('Ali', $update['message']['from']['first_name']);
        $this->assertSame('Valiyev', $update['message']['from']['last_name']);
        $this->assertSame('ali_v', $update['message']['from']['username']);
    }
}
