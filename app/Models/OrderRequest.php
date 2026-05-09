<?php

namespace App\Models;

use App\Enums\OrderRequestStatus;
use Database\Factories\OrderRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderRequest extends Model
{
    /** @use HasFactory<OrderRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'customer_name',
        'customer_phone',
        'customer_comment',
        'customer_city',
        'customer_address',
        'assigned_admin_id',
        'total_amount',
        'currency',
        'processing_started_at',
        'approved_at',
        'cancelled_at',
        'paid_at',
        'admin_comment',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderRequestStatus::class,
            'processing_started_at' => 'datetime',
            'approved_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderRequestItem::class);
    }
}
