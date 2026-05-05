<?php

namespace App\Filament\Resources\OrderRequests\Pages;

use App\Filament\Resources\OrderRequests\OrderRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrderRequest extends ViewRecord
{
    protected static string $resource = OrderRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
