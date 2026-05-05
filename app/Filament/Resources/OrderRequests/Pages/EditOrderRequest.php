<?php

namespace App\Filament\Resources\OrderRequests\Pages;

use App\Enums\OrderRequestStatus;
use App\Filament\Resources\OrderRequests\OrderRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrderRequest extends EditRecord
{
    protected static string $resource = OrderRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $status = OrderRequestStatus::from($data['status']);

        if ($status === OrderRequestStatus::Processing && ! $this->record->processing_started_at) {
            $data['processing_started_at'] = now();
        }

        if ($status === OrderRequestStatus::WaitingPayment && ! $this->record->approved_at) {
            $data['approved_at'] = now();
        }

        if ($status === OrderRequestStatus::Paid && ! $this->record->paid_at) {
            $data['paid_at'] = now();
        }

        if ($status === OrderRequestStatus::Cancelled && ! $this->record->cancelled_at) {
            $data['cancelled_at'] = now();
        }

        return $data;
    }
}
