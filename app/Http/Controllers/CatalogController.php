<?php

namespace App\Http\Controllers;

use App\Enums\OrderRequestStatus;
use App\Models\Category;
use App\Models\Design;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class CatalogController extends Controller
{
    public function home(): View
    {
        if (config('printlab.redesign_v2_enabled')) {
            $products = Product::query()->where('is_active', true)->with(['variants', 'categories'])->limit(5)->get();

            return view('catalog.home-v2', [
                'products' => $products,
                'prints' => $this->publishedOrderDesigns()->limit(8)->get(),
                'productCategories' => $this->categories(Category::SCOPE_PRODUCT)->get(),
                'printCategories' => $this->categories(Category::SCOPE_PRINT)->get(),
                'featuredProduct' => $products->first(),
            ]);
        }

        return view('catalog.index', [
            'products' => Product::query()->where('is_active', true)->with(['variants', 'categories'])->limit(6)->get(),
            'prints' => $this->publishedOrderDesigns()->limit(8)->get(),
            'productCategories' => $this->categories(Category::SCOPE_PRODUCT)->get(),
            'printCategories' => $this->categories(Category::SCOPE_PRINT)->get(),
        ]);
    }

    public function index(): View
    {
        $productCategory = request()->query('category');
        $printCategory = request()->query('print_category');

        $products = Product::query()
            ->where('is_active', true)
            ->with(['variants', 'categories']);

        if (is_string($productCategory) && $productCategory !== '') {
            $products->whereHas('categories', function (Builder $query) use ($productCategory): void {
                $query
                    ->where('scope', Category::SCOPE_PRODUCT)
                    ->where('is_active', true)
                    ->where('slug', $productCategory);
            });
        }

        $prints = $this->publishedOrderDesigns();

        if (is_string($printCategory) && $printCategory !== '') {
            $prints->whereRaw('1 = 0');
        }

        return view('catalog.index', [
            'products' => $products->get(),
            'prints' => $prints->get(),
            'productCategories' => $this->categories(Category::SCOPE_PRODUCT)->get(),
            'printCategories' => $this->categories(Category::SCOPE_PRINT)->get(),
            'selectedProductCategory' => $productCategory,
            'selectedPrintCategory' => $printCategory,
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
