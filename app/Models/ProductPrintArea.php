<?php

namespace App\Models;

use Database\Factories\ProductPrintAreaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrintArea extends Model
{
    /** @use HasFactory<ProductPrintAreaFactory> */
    use HasFactory;

    protected $fillable = ['product_variant_id', 'side', 'x', 'y', 'width', 'height', 'dpi'];

    protected function casts(): array
    {
        return [
            'x' => 'float',
            'y' => 'float',
            'width' => 'float',
            'height' => 'float',
        ];
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function productVariant(): BelongsTo
    {
        return $this->variant();
    }
}
