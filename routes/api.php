<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeneratedPrintController;

Route::post('/generated-prints', [GeneratedPrintController::class, 'store'])
    ->name('api.generated-prints.store');
