<?php

namespace App\Filament\Resources\OrderRequests\Tables;

use Filament\Tables\Table;
use App\Models\OrderRequest;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use App\Enums\OrderRequestStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;

class OrderRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label('Имя')
                    ->searchable(),
                TextColumn::make('customer_phone')
                    ->label('Телефон')
                    ->searchable(),
                TextColumn::make('product_snapshot')
                    ->label('Товар')
                    ->getStateUsing(fn (OrderRequest $record): string => $record->items->first()?->product_name_snapshot ?? '-')
                    ->searchable(query: function ($query, string $search): void {
                        $query->whereHas('items', fn ($items) => $items->where('product_name_snapshot', 'like', "%{$search}%"));
                    }),
                TextColumn::make('variant_snapshot')
                    ->label('Вариант')
                    ->getStateUsing(function (OrderRequest $record): string {
                        $item = $record->items->first();

                        if (! $item) {
                            return '-';
                        }

                        return trim($item->color_snapshot.' '.($item->size_snapshot ? '/ '.$item->size_snapshot : ''));
                    }),
                SelectColumn::make('status')
                    ->label('Статус')
                    ->options(OrderRequestStatus::options())
                    ->selectablePlaceholder(false)
                    ->updateStateUsing(function (OrderRequest $record, string $state): string {
                        $status = OrderRequestStatus::from($state);
                        $data = ['status' => $status];

                        if ($status === OrderRequestStatus::Processing && ! $record->processing_started_at) {
                            $data['processing_started_at'] = now();
                        }

                        if ($status === OrderRequestStatus::WaitingPayment && ! $record->approved_at) {
                            $data['approved_at'] = now();
                        }

                        if ($status === OrderRequestStatus::Paid && ! $record->paid_at) {
                            $data['paid_at'] = now();
                        }

                        if ($status === OrderRequestStatus::Cancelled && ! $record->cancelled_at) {
                            $data['cancelled_at'] = now();
                        }

                        $record->update($data);

                        return $state;
                    })
                    ->sortable(),
                TextColumn::make('assignedAdmin.name')
                    ->label('Админ')
                    ->placeholder('-'),
                TextColumn::make('total_amount')
                    ->label('Сумма')
                    ->money('UZS', divideBy: 1)
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options(OrderRequestStatus::options()),
            ])
            ->recordActions([
                ViewAction::make()->label('Открыть'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
