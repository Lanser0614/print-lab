<?php

namespace App\Filament\Resources\ReadyPrints\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ReadyPrints\ReadyPrintResource;

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
