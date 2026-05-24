<?php

namespace App\Models;

use App\Enums\OrderRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\OrderRequestFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\UseCases\ReadyPrints\CreateReadyPrintFromOrderRequestUseCase;

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
        'delivery_lat',
        'delivery_lng',
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
            'delivery_lat' => 'decimal:7',
            'delivery_lng' => 'decimal:7',
        ];
    }

    protected static function booted(): void
    {
        static::updated(function (OrderRequest $orderRequest): void {
            if (! $orderRequest->wasChanged('status') || $orderRequest->status !== OrderRequestStatus::Ready) {
                return;
            }

            app(CreateReadyPrintFromOrderRequestUseCase::class)->handle($orderRequest);
        });
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
