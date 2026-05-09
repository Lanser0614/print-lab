<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Database\Factories\ProductVariantFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    /** @use HasFactory<ProductVariantFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color',
        'size',
        'mockup_front_path',
        'mockup_back_path',
        'price_modifier',
        'is_active',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function printAreas(): HasMany
    {
        return $this->hasMany(ProductPrintArea::class);
    }

    public function getMockupFrontUrlAttribute(): string
    {
        return $this->assetUrl($this->mockup_front_path);
    }

    public function getMockupBackUrlAttribute(): ?string
    {
        return $this->mockup_back_path ? $this->assetUrl($this->mockup_back_path) : null;
    }

    private function assetUrl(string $path): string
    {
        return str_starts_with($path, 'http') || str_starts_with($path, '/')
            ? $path
            : Storage::url($path);
    }
}
