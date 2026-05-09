<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramLoginToken extends Model
{
    protected $fillable = [
        'token',
        'status',
        'user_id',
        'ip_address',
        'user_agent',
        'redirect_to',
        'confirmed_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function markConfirmed(int $userId): void
    {
        $this->update([
            'status' => 'confirmed',
            'user_id' => $userId,
            'confirmed_at' => now(),
        ]);
    }

    public function markExpired(): void
    {
        $this->update(['status' => 'expired']);
    }
}
