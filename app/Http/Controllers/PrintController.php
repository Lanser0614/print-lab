<?php

namespace App\Http\Controllers;

use App\Enums\OrderRequestStatus;
use App\Models\Design;
use Illuminate\Contracts\View\View;

class PrintController extends Controller
{
    public function show(string $locale, Design $design): View
    {
        // Only show designs whose order is past the new/cancelled stage
        abort_unless(
            $design->relationLoaded('item') || true,
            404
        );

        $design->load(['item.product.variants', 'item.orderRequest']);

        $item         = $design->item;
        $orderRequest = $item?->orderRequest;

        abort_if(
            is_null($item) || is_null($orderRequest),
            404
        );

        abort_if(
            in_array($orderRequest->status, [
                OrderRequestStatus::New,
                OrderRequestStatus::Cancelled,
            ]),
            404
        );

        $product = $item->product;

        return view('prints.show', compact('design', 'item', 'orderRequest', 'product'));
    }
}
