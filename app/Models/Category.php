<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    public const SCOPE_PRODUCT = 'product';

    public const SCOPE_PRINT = 'print';

    protected $fillable = [
        'name',
        'name_translations',
        'slug',
        'scope',
        'icon_svg',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'name_translations' => 'array',
        'is_active' => 'boolean',
    ];

    public function localizedName(?string $locale = null): string
    {
        return $this->localizedValue($this->name_translations, $this->name, $locale);
    }

    public function readyPrints(): HasMany
    {
        return $this->hasMany(ReadyPrint::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
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
