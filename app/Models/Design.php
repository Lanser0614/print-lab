<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Design extends Model
{
    protected $fillable = [
        'order_request_item_id',
        'side',
        'canvas_json',
        'preview_image_path',
        'print_image_path',
    ];

    protected function casts(): array
    {
        return ['canvas_json' => 'array'];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(OrderRequestItem::class, 'order_request_item_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(DesignAsset::class);
    }

    public function textLayers(): HasMany
    {
        return $this->hasMany(DesignTextLayer::class);
    }
}
