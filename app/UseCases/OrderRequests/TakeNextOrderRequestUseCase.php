<?php

namespace App\UseCases\OrderRequests;

use App\Models\User;
use App\Models\OrderRequest;
use App\Enums\OrderRequestStatus;
use Illuminate\Support\Facades\DB;

final readonly class TakeNextOrderRequestUseCase
{
    public function execute(User $admin): OrderRequest
    {
        return DB::transaction(function () use ($admin): OrderRequest {
            $currentProcessingOrder = OrderRequest::query()
                ->where('assigned_admin_id', $admin->id)
                ->where('status', OrderRequestStatus::Processing)
                ->lockForUpdate()
                ->first();

            if ($currentProcessingOrder) {
                return $currentProcessingOrder;
            }

            $nextOrder = OrderRequest::query()
                ->where('status', OrderRequestStatus::New)
                ->orderBy('created_at')
                ->lockForUpdate()
                ->firstOrFail();

            $nextOrder->update([
                'status' => OrderRequestStatus::Processing,
                'assigned_admin_id' => $admin->id,
                'processing_started_at' => now(),
            ]);

            return $nextOrder;
        });
    }
}
