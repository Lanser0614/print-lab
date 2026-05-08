<?php

namespace App\Filament\Resources\ReadyPrints;

use App\Filament\Resources\ReadyPrints\Pages\CreateReadyPrint;
use App\Filament\Resources\ReadyPrints\Pages\EditReadyPrint;
use App\Filament\Resources\ReadyPrints\Pages\ListReadyPrints;
use App\Models\Category;
use App\Models\ReadyPrint;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ReadyPrintResource extends Resource
{
    protected static ?string $model = ReadyPrint::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $modelLabel = 'Готовый принт';

    protected static ?string $pluralModelLabel = 'Готовые принты';

    protected static ?string $navigationLabel = 'Готовые принты';

    protected static ?int $navigationSort = 30;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('category');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Принт')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Название')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state ?? ''))),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Select::make('category_id')
                            ->label('Категория')
                            ->relationship(
                                'category',
                                'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query
                                    ->where('scope', Category::SCOPE_PRINT)
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->orderBy('name'),
                            )
                            ->preload()
                            ->searchable()
                            ->native(false),
                        Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true),
                        FileUpload::make('image_path')
                            ->label('Файл принта')
                            ->disk('public')
                            ->directory('ready-prints')
                            ->visibility('public')
                            ->image()
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Превью')
                    ->disk('public')
                    ->square(),
                TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Категория')
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Категория')
                    ->relationship(
                        'category',
                        'name',
                        modifyQueryUsing: fn (Builder $query): Builder => $query
                            ->where('scope', Category::SCOPE_PRINT)
                            ->orderBy('sort_order')
                            ->orderBy('name'),
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReadyPrints::route('/'),
            'create' => CreateReadyPrint::route('/create'),
            'edit' => EditReadyPrint::route('/{record}/edit'),
        ];
    }
}
