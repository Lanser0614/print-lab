<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\Product;
use App\Models\Category;
use App\Enums\OrderRequestStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

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

        $item = $design->item;
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

        return view('prints.show', [
            'design' => $design,
            'item' => $item,
            'orderRequest' => $orderRequest,
            'product' => $product,
            'productCategories' => $this->categories(Category::SCOPE_PRODUCT)->get(),
            'printCategories' => $this->categories(Category::SCOPE_PRINT)->get(),
            'featuredProduct' => Product::query()
                ->where('is_active', true)
                ->with('variants')
                ->orderBy('id')
                ->first(),
        ]);
    }

    private function categories(string $scope): Builder
    {
        $query = Category::query()
            ->where('scope', $scope)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($scope === Category::SCOPE_PRODUCT) {
            $query->withCount([
                'products' => fn (Builder $query): Builder => $query->where('is_active', true),
            ]);
        }

        if ($scope === Category::SCOPE_PRINT) {
            $query->withCount([
                'readyPrints' => fn (Builder $query): Builder => $query->where('is_active', true),
            ]);
        }

        return $query;
    }
}
