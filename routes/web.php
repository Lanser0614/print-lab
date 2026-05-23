<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\PrintController;
use App\Exceptions\AiImageGenerationFailed;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ConstructorController;
use App\Http\Controllers\OrderRequestController;
use App\Http\Controllers\Auth\TelegramLoginController;
use App\Http\Controllers\Admin\DesignDownloadController;
use App\Http\Controllers\Auth\TelegramWebhookController;

Route::get('/', function (Request $request) {
    $locale = $request->session()->get('locale', $request->cookie('locale', 'ru'));

    if (! in_array($locale, ['ru', 'uz'], true)) {
        $locale = 'ru';
    }

    return redirect()->route('home', ['locale' => $locale]);
});

Route::get('/manifest.webmanifest', function () {
    return response(file_get_contents(public_path('manifest.webmanifest')), 200, [
        'Content-Type' => 'application/manifest+json',
    ]);
});

Route::get('/sw.js', function () {
    return response(file_get_contents(public_path('sw.js')), 200, [
        'Content-Type' => 'application/javascript',
        'Service-Worker-Allowed' => '/',
    ]);
});

Route::get('/language/{locale}', function (string $locale) {
    if (in_array($locale, ['ru', 'uz'], true)) {
        session(['locale' => $locale]);
        Cookie::queue(Cookie::forever('locale', $locale));
    }

    return redirect()->route('home', ['locale' => in_array($locale, ['ru', 'uz'], true) ? $locale : 'ru']);
})->name('language.switch');

Route::prefix('{locale}')
    ->whereIn('locale', ['ru', 'uz'])
    ->group(function (): void {
        Route::get('/', [CatalogController::class, 'home'])->name('home');
        Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
        Route::get('/catalog/t-shirts', [CatalogController::class, 'index'])->name('catalog.t-shirts');
        Route::get('/catalog/mugs', [CatalogController::class, 'index'])->name('catalog.mugs');
        Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/prints/{design}', [PrintController::class, 'show'])->name('prints.show');
        Route::get('/constructor', [ConstructorController::class, 'localFallback'])->name('constructor.fallback');
        Route::get('/ai-studio/{product:slug}', [ConstructorController::class, 'aiStudio'])->name('ai-studio.show');
        Route::get('/constructor/v2/{product:slug}', [ConstructorController::class, 'v2'])->name('constructor.v2');
        Route::get('/constructor/{product:slug}', [ConstructorController::class, 'show'])->name('constructor.show');
        Route::post('/order-requests', [OrderRequestController::class, 'store'])->name('order-requests.store');
        Route::get('/order-request/success', [OrderRequestController::class, 'success'])->name('order-requests.success');

        Route::get('/login', [TelegramLoginController::class, 'show'])->name('login');
        Route::post('/auth/telegram/start', [TelegramLoginController::class, 'start'])
            ->middleware('throttle:30,1')
            ->name('auth.telegram.start');
        Route::get('/auth/telegram/poll/{token}', [TelegramLoginController::class, 'poll'])
            ->middleware('throttle:120,1')
            ->name('auth.telegram.poll');
        Route::post('/auth/logout', [TelegramLoginController::class, 'logout'])
            ->middleware('auth')
            ->name('auth.logout');

        Route::middleware('auth')->group(function (): void {
            Route::get('/account', [AccountController::class, 'index'])->name('account.index');
            Route::get('/account/order-requests/{orderRequest}', [AccountController::class, 'show'])
                ->name('account.order-requests.show');
        });
    });

Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])
    ->name('telegram.webhook');

Route::middleware('auth')
    ->prefix('admin/downloads')
    ->name('admin.downloads.')
    ->group(function (): void {
        Route::get('/designs/{design}/preview', [DesignDownloadController::class, 'preview'])->name('designs.preview');
        Route::get('/designs/{design}/print', [DesignDownloadController::class, 'print'])->name('designs.print');
        Route::get('/design-assets/{asset}', [DesignDownloadController::class, 'asset'])->name('design-assets.show');
    });

Route::get('/test', function () {
    return 'ok';
});

// Route::get('/openapi', function (\App\Services\Ai\OpenAiImageGenerator $imageGenerator) {
//
//
//    try {
//        $result = $imageGenerator->generate('My name is Doniyor Anvarov and i am php developer make for me personal logo. make more aksent to php and elephant', null);
//
//    } catch (AiImageGenerationFailed $exception) {
//        dd($exception);
//    }
//
//    $generatedImagePath = sprintf(
//        'generated-prints/%s.%s',
//        (string) str()->uuid(),
//        $result->extension,
//    );
//
//    Storage::disk('public')->put($generatedImagePath, $result->binary);
//
//    dd($generatedImagePath);
// });
