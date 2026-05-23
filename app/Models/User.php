<?php

namespace App\Models;

use Filament\Panel;
use Database\Factories\UserFactory;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        $email = strtolower((string) $this->email);

        if ($email === '') {
            return false;
        }

        return in_array($email, config('filament-admin.emails', []), true);
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'telegram_id',
        'telegram_username',
        'first_name',
        'last_name',
        'phone',
        'photo_url',
        'locale',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orderRequests(): HasMany
    {
        return $this->hasMany(OrderRequest::class);
    }

    public function telegramLoginTokens(): HasMany
    {
        return $this->hasMany(TelegramLoginToken::class);
    }
}
