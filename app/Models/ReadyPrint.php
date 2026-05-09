<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Database\Factories\ReadyPrintFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReadyPrint extends Model
{
    /** @use HasFactory<ReadyPrintFactory> */
    use HasFactory;

    protected $fillable = ['category_id', 'title', 'title_translations', 'slug', 'image_path', 'is_active'];

    protected $casts = [
        'title_translations' => 'array',
    ];

    public function localizedTitle(?string $locale = null): string
    {
        return $this->localizedValue($this->title_translations, $this->title, $locale);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute(): string
    {
        return str_starts_with($this->image_path, 'http')
            ? $this->image_path
            : Storage::url($this->image_path);
    }

    private function localizedValue(?array $translations, string $fallback, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        foreach ([$locale, 'ru'] as $candidate) {
            $value = $translations[$candidate] ?? null;

            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
        }

        return $fallback;
    }
}
