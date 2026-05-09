<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GeneratedPrint extends Model
{
    use HasFactory;

    protected $fillable = [
        'prompt',
        'reference_image_path',
        'generated_image_path',
        'model',
        'status',
        'error_message',
        'guest_fingerprint',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public static function guestFingerprint(string $ip, string $userAgent, Carbon $date): string
    {
        return hash('sha256', implode('|', [
            $ip,
            $userAgent,
            $date->toDateString(),
        ]));
    }
}
