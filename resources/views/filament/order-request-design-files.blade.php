@php
    $design = $record->items->first()?->design;
@endphp

@if (! $design)
    <div class="text-sm text-gray-500">Дизайн не найден.</div>
@else
    <div class="space-y-4">
        @if ($design->preview_image_path)
            <div>
                <div class="mb-2 text-sm font-medium">Preview</div>
                <img src="{{ Storage::disk('public')->url($design->preview_image_path) }}" alt="Preview" class="max-h-96 rounded border object-contain">
                <a href="{{ route('admin.downloads.designs.preview', $design) }}" class="mt-2 inline-flex rounded bg-gray-100 px-3 py-2 text-sm font-medium text-gray-900">
                    Скачать preview PNG
                </a>
            </div>
        @endif

        @if ($design->print_image_path)
            <a href="{{ route('admin.downloads.designs.print', $design) }}" class="inline-flex rounded bg-primary-600 px-3 py-2 text-sm font-medium text-white">
                Скачать print-only PNG
            </a>
        @endif

        <div>
            <div class="mb-2 text-sm font-medium">Оригинальные изображения</div>
            @forelse ($design->assets as $asset)
                <div class="mb-2 flex items-center gap-3 rounded border p-2 text-sm">
                    @if ($asset->mime_type && str_starts_with($asset->mime_type, 'image/'))
                        <img src="{{ Storage::disk('public')->url($asset->original_file_path) }}" alt="{{ $asset->file_name }}" class="h-14 w-14 rounded border object-contain">
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="truncate font-medium">{{ $asset->file_name ?? basename($asset->original_file_path) }}</div>
                        <div class="text-gray-500">
                            {{ $asset->mime_type ?? 'unknown' }}
                            @if ($asset->size_bytes)
                                · {{ number_format($asset->size_bytes / 1024, 1) }} KB
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('admin.downloads.design-assets.show', $asset) }}" class="text-primary-600 underline">Скачать оригинал</a>
                </div>
            @empty
                <div class="text-sm text-gray-500">Оригинальные изображения не загружались.</div>
            @endforelse
        </div>
    </div>
@endif
