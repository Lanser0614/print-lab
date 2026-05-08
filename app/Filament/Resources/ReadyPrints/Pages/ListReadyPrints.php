<?php

namespace App\Filament\Resources\ReadyPrints\Pages;

use App\Filament\Resources\ReadyPrints\ReadyPrintResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReadyPrints extends ListRecords
{
    protected static string $resource = ReadyPrintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Добавить принт'),
        ];
    }
}
