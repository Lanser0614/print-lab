<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Pages\ViewProduct;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?string $modelLabel = 'Товар';

    protected static ?string $pluralModelLabel = 'Товары';

    protected static ?string $navigationLabel = 'Товары';

    protected static ?int $navigationSort = 10;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['variants.printAreas'])
            ->withCount('variants');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Товар')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
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
                        Select::make('type')
                            ->label('Тип')
                            ->options([
                                't-shirt' => 'Футболка',
                                'mug' => 'Кружка',
                                'hoodie' => 'Худи',
                                'other' => 'Другое',
                            ])
                            ->required()
                            ->native(false),
                        TextInput::make('base_price')
                            ->label('Базовая цена')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true),
                    ]),
                Section::make('Варианты')
                    ->schema([
                        Repeater::make('variants')
                            ->label('')
                            ->relationship('variants')
                            ->columns(2)
                            ->schema([
                                TextInput::make('color')
                                    ->label('Цвет')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('size')
                                    ->label('Размер')
                                    ->maxLength(255),
                                TextInput::make('price_modifier')
                                    ->label('Наценка')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->required(),
                                Toggle::make('is_active')
                                    ->label('Активен')
                                    ->default(true),
                                FileUpload::make('mockup_front_path')
                                    ->label('Mockup front')
                                    ->disk('public')
                                    ->directory('products/mockups')
                                    ->visibility('public')
                                    ->image()
                                    ->required()
                                    ->columnSpanFull(),
                                FileUpload::make('mockup_back_path')
                                    ->label('Mockup back')
                                    ->disk('public')
                                    ->directory('products/mockups')
                                    ->visibility('public')
                                    ->image()
                                    ->columnSpanFull(),
                                Repeater::make('printAreas')
                                    ->label('Зоны печати')
                                    ->relationship('printAreas')
                                    ->columns(3)
                                    ->defaultItems(1)
                                    ->schema([
                                        Select::make('side')
                                            ->label('Сторона')
                                            ->options([
                                                'front' => 'Front',
                                                'back' => 'Back',
                                            ])
                                            ->default('front')
                                            ->required()
                                            ->native(false),
                                        TextInput::make('x')
                                            ->label('X')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(1)
                                            ->default(0.32)
                                            ->required(),
                                        TextInput::make('y')
                                            ->label('Y')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(1)
                                            ->default(0.27)
                                            ->required(),
                                        TextInput::make('width')
                                            ->label('Width')
                                            ->numeric()
                                            ->minValue(0.01)
                                            ->maxValue(1)
                                            ->default(0.36)
                                            ->required(),
                                        TextInput::make('height')
                                            ->label('Height')
                                            ->numeric()
                                            ->minValue(0.01)
                                            ->maxValue(1)
                                            ->default(0.42)
                                            ->required(),
                                        TextInput::make('dpi')
                                            ->label('DPI')
                                            ->numeric()
                                            ->minValue(72)
                                            ->default(300)
                                            ->required(),
                                    ])
                                    ->columnSpanFull()
                                    ->addActionLabel('Добавить зону печати'),
                            ])
                            ->addActionLabel('Добавить вариант'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\ProductType ? $state->labelRu() : $state)
                    ->sortable(),
                TextColumn::make('base_price')
                    ->label('Цена')
                    ->money('UZS', divideBy: 1)
                    ->sortable(),
                TextColumn::make('variants_count')
                    ->label('Варианты')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean(),
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

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'view' => ViewProduct::route('/{record}'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
