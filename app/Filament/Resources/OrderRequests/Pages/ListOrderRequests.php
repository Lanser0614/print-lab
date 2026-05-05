<?php

namespace App\Filament\Resources\OrderRequests\Pages;

use App\Filament\Resources\OrderRequests\OrderRequestResource;
use App\UseCases\OrderRequests\TakeNextOrderRequestUseCase;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListOrderRequests extends ListRecords
{
    protected static string $resource = OrderRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('takeNext')
                ->label('Взять следующую заявку')
                ->action(function (TakeNextOrderRequestUseCase $useCase): void {
                    $orderRequest = $useCase->execute(auth()->user());

                    $this->redirect(OrderRequestResource::getUrl('edit', ['record' => $orderRequest]));
                }),
        ];
    }
}
