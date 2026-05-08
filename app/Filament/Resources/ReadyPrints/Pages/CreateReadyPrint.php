<?php

namespace App\Filament\Resources\ReadyPrints\Pages;

use App\Filament\Resources\ReadyPrints\ReadyPrintResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReadyPrint extends CreateRecord
{
    protected static string $resource = ReadyPrintResource::class;
}
