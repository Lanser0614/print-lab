<?php

namespace App\Filament\Resources\OrderRequests\Schemas;

use App\Models\OrderRequest;
use Filament\Schemas\Schema;
use App\Enums\OrderRequestStatus;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\View;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;

class OrderRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Клиент')
                    ->columns(2)
                    ->schema([
                        TextInput::make('customer_name')->label('Имя')->required(),
                        TextInput::make('customer_phone')->label('Телефон')->required(),
                        Textarea::make('customer_comment')->label('Комментарий')->columnSpanFull(),
                    ]),
                Section::make('Заявка')
                    ->columns(3)
                    ->schema([
                        Select::make('status')
                            ->label('Статус')
                            ->options(OrderRequestStatus::options())
                            ->native(false)
                            ->required(),
                        TextInput::make('total_amount')->label('Сумма')->numeric(),
                        Textarea::make('admin_comment')->label('Комментарий админа')->columnSpanFull(),
                    ]),
                Section::make('Товар')
                    ->columns(3)
                    ->schema([
                        Placeholder::make('product_name')->label('Товар')->content(fn (OrderRequest $record): string => $record->items->first()?->product_name_snapshot ?? '-'),
                        Placeholder::make('product_type')->label('Тип')->content(fn (OrderRequest $record): string => $record->items->first()?->product_type_snapshot ?? '-'),
                        Placeholder::make('variant')->label('Вариант')->content(function (OrderRequest $record): string {
                            $item = $record->items->first();

                            return $item ? trim($item->color_snapshot.' '.($item->size_snapshot ? '/ '.$item->size_snapshot : '')) : '-';
                        }),
                    ]),
                Section::make('Файлы дизайна')
                    ->schema([
                        View::make('filament.order-request-design-files')
                            ->viewData(fn (OrderRequest $record): array => ['record' => $record]),
                    ]),
                Section::make('Текстовые слои')
                    ->schema([
                        Placeholder::make('text_layers')
                            ->label('')
                            ->content(function (OrderRequest $record): HtmlString {
                                $layers = $record->items->flatMap(fn ($item) => $item->design?->textLayers ?? collect());

                                if ($layers->isEmpty()) {
                                    return new HtmlString('<div class="text-sm text-gray-500">Нет текстовых слоёв.</div>');
                                }

                                $rows = $layers->map(fn ($layer): string => sprintf(
                                    '<tr><td>%s</td><td>%s</td><td>%s</td><td><code>%s</code></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
                                    e($layer->text),
                                    e($layer->font_family ?? '-'),
                                    e((string) ($layer->font_size ?? '-')),
                                    e($layer->color ?? '-'),
                                    e((string) ($layer->x ?? '-')),
                                    e((string) ($layer->y ?? '-')),
                                    e((string) ($layer->scale ?? '-')),
                                    e((string) ($layer->rotation ?? '-')),
                                ))->implode('');

                                return new HtmlString('<div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr><th class="text-left">Text</th><th class="text-left">Font</th><th class="text-left">Size</th><th class="text-left">Color</th><th class="text-left">X</th><th class="text-left">Y</th><th class="text-left">Scale</th><th class="text-left">Rotation</th></tr></thead><tbody>'.$rows.'</tbody></table></div>');
                            }),
                    ]),
                Section::make('Canvas JSON')
                    ->collapsed()
                    ->schema([
                        Placeholder::make('canvas_json')
                            ->label('')
                            ->content(function (OrderRequest $record): HtmlString {
                                $design = $record->items->first()?->design;

                                return new HtmlString('<pre class="overflow-auto rounded bg-gray-950 p-4 text-xs text-gray-100">'.e(json_encode($design?->canvas_json ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)).'</pre>');
                            }),
                    ]),
            ]);
    }
}
