<?php

namespace App\UseCases\Auth;

use App\Models\User;
use App\Models\OrderRequest;

final readonly class AttachGuestOrderRequestsUseCase
{
    public function execute(User $user): int
    {
        if ($user->phone === null) {
            return 0;
        }

        return OrderRequest::query()
            ->whereNull('user_id')
            ->where('customer_phone', $user->phone)
            ->update(['user_id' => $user->id]);
    }
}
