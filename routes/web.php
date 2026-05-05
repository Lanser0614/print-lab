<?php

use App\Http\Controllers\Admin\DesignDownloadController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ConstructorController;
use App\Http\Controllers\OrderRequestController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/language/{locale}', function (string $locale) {
    if (in_array($locale, ['ru', 'uz'], true)) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('language.switch');

Route::get('/', [CatalogController::class, 'home'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/t-shirts', [CatalogController::class, 'index'])->name('catalog.t-shirts');
Route::get('/catalog/mugs', [CatalogController::class, 'index'])->name('catalog.mugs');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/prints/{design}', [PrintController::class, 'show'])->name('prints.show');
Route::get('/constructor/{product:slug}', [ConstructorController::class, 'show'])->name('constructor.show');
Route::post('/order-requests', [OrderRequestController::class, 'store'])->name('order-requests.store');
Route::get('/order-request/success', [OrderRequestController::class, 'success'])->name('order-requests.success');

Route::middleware('auth')
    ->prefix('admin/downloads')
    ->name('admin.downloads.')
    ->group(function (): void {
        Route::get('/designs/{design}/preview', [DesignDownloadController::class, 'preview'])->name('designs.preview');
        Route::get('/designs/{design}/print', [DesignDownloadController::class, 'print'])->name('designs.print');
        Route::get('/design-assets/{asset}', [DesignDownloadController::class, 'asset'])->name('design-assets.show');
    });
