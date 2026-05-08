<?php

namespace App\Filament\Resources\ReadyPrints\Pages;

use App\Filament\Resources\ReadyPrints\ReadyPrintResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReadyPrint extends EditRecord
{
    protected static string $resource = ReadyPrintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
