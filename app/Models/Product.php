<?php

namespace App\Models;

use App\Enums\ProductType;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = ['name', 'name_translations', 'slug', 'type', 'base_price', 'is_active'];

    protected $casts = [
        'name_translations' => 'array',
        'type' => ProductType::class,
    ];

    public function localizedName(?string $locale = null): string
    {
        return $this->localizedValue($this->name_translations, $this->name, $locale);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
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
