<?php

namespace Tests\Unit\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\TelegramLoginToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TelegramLoginTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_is_expired_after_ttl(): void
    {
        $token = TelegramLoginToken::query()->create([
            'token' => str_repeat('c', 64),
            'status' => 'pending',
            'expires_at' => now()->subSecond(),
        ]);

        $this->assertTrue($token->isExpired());
    }

    public function test_token_is_marked_confirmed(): void
    {
        $user = User::factory()->create();
        $token = TelegramLoginToken::query()->create([
            'token' => str_repeat('d', 64),
            'status' => 'pending',
            'expires_at' => now()->addMinutes(5),
        ]);

        $token->markConfirmed($user->id);

        $token->refresh();

        $this->assertTrue($token->isConfirmed());
        $this->assertSame($user->id, $token->user_id);
        $this->assertNotNull($token->confirmed_at);
    }
}
