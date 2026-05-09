<?php

namespace App\Filament\Resources\OrderRequests\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\OrderRequests\OrderRequestResource;

class CreateOrderRequest extends CreateRecord
{
    protected static string $resource = OrderRequestResource::class;
}
