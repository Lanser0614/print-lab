<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRequestItem extends Model
{
    protected $fillable = [
        'order_request_id',
        'product_id',
        'product_variant_id',
        'product_name_snapshot',
        'product_type_snapshot',
        'color_snapshot',
        'size_snapshot',
        'quantity',
        'unit_price',
        'total_price',
    ];

    public function orderRequest(): BelongsTo
    {
        return $this->belongsTo(OrderRequest::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function design(): HasOne
    {
        return $this->hasOne(Design::class);
    }

    public function designs(): HasMany
    {
        return $this->hasMany(Design::class);
    }
}
