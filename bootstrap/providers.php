<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\TelegramAuthServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    TelegramAuthServiceProvider::class,
];
