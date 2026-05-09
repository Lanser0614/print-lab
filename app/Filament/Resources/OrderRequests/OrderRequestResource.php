<?php

namespace App\Filament\Resources\OrderRequests;

use BackedEnum;
use Filament\Tables\Table;
use App\Models\OrderRequest;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderRequests\Pages\EditOrderRequest;
use App\Filament\Resources\OrderRequests\Pages\ViewOrderRequest;
use App\Filament\Resources\OrderRequests\Pages\ListOrderRequests;
use App\Filament\Resources\OrderRequests\Pages\CreateOrderRequest;
use App\Filament\Resources\OrderRequests\Schemas\OrderRequestForm;
use App\Filament\Resources\OrderRequests\Tables\OrderRequestsTable;

class OrderRequestResource extends Resource
{
    protected static ?string $model = OrderRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'Заявка';

    protected static ?string $pluralModelLabel = 'Заявки';

    protected static ?string $navigationLabel = 'Заявки';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'assignedAdmin',
                'items.design.assets',
                'items.design.textLayers',
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return OrderRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrderRequests::route('/'),
            'create' => CreateOrderRequest::route('/create'),
            'view' => ViewOrderRequest::route('/{record}'),
            'edit' => EditOrderRequest::route('/{record}/edit'),
        ];
    }
}
