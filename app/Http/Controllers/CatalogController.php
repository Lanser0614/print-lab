<?php

namespace App\Http\Controllers;

use App\Enums\OrderRequestStatus;
use App\Models\Design;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class CatalogController extends Controller
{
    public function home(): View
    {
        return view('catalog.index', [
            'products' => Product::query()->where('is_active', true)->with('variants')->limit(6)->get(),
            'prints' => $this->publishedOrderDesigns()->limit(8)->get(),
        ]);
    }

    public function index(): View
    {
        return view('catalog.index', [
            'products' => Product::query()->where('is_active', true)->with('variants')->get(),
            'prints' => $this->publishedOrderDesigns()->get(),
        ]);
    }

    private function publishedOrderDesigns(): Builder
    {
        return Design::query()
            ->select(['id', 'order_request_item_id', 'preview_image_path'])
            ->whereNotNull('preview_image_path')
            ->whereHas('item.orderRequest', function (Builder $query): void {
                $query->whereNotIn('status', [
                    OrderRequestStatus::New,
                    OrderRequestStatus::Cancelled,
                ]);
            })
            ->with([
                'item.product',
                'item.orderRequest',
            ])
            ->orderByDesc('id');
    }
}
