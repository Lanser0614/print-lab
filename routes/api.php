<?php

use App\Http\Controllers\GeneratedPrintController;
use Illuminate\Support\Facades\Route;

Route::post('/generated-prints', [GeneratedPrintController::class, 'store'])
    ->name('api.generated-prints.store');
