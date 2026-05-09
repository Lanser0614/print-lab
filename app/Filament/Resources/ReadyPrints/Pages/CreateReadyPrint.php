<?php

namespace App\Filament\Resources\ReadyPrints\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ReadyPrints\ReadyPrintResource;

class CreateReadyPrint extends CreateRecord
{
    protected static string $resource = ReadyPrintResource::class;
}
