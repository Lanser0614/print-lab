@php
    $previewUrl = $design->preview_image_path
        ? Storage::disk('public')->url($design->preview_image_path)
        : null;

    $productName = $item->product_name_snapshot ?? $product?->name ?? 'PrintLab';
    $colorLabel  = $item->color_snapshot ?? null;
    $sizeLabel   = $item->size_snapshot ?? null;

    $firstVariant = $product?->variants->first();
@endphp

<x-layouts.public
    :title="'Print #' . $design->id . ' — PrintLab'"
    :description="__('site.seo_home_description')"
    :og-image="$previewUrl"
>

<main class="mx-auto max-w-5xl px-4 py-8">

    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center gap-2 text-sm text-zinc-400">
        <a href="{{ route('home') }}" class="hover:text-zinc-700 transition-colors">PrintLab</a>
        <span>/</span>
        <a href="{{ route('catalog.index') }}" class="hover:text-zinc-700 transition-colors">{{ __('site.nav_catalog') }}</a>
        <span>/</span>
        <span class="text-zinc-700">Print #{{ $design->id }}</span>
    </nav>

    <div class="grid gap-10 lg:grid-cols-2">

        {{-- ══ LEFT: Preview Image ══ --}}
        <div>
            <div class="overflow-hidden rounded-2xl bg-zinc-100 aspect-square flex items-center justify-center">
                @if ($previewUrl)
                    <img src="{{ $previewUrl }}"
                         alt="Print #{{ $design->id }}"
                         class="h-full w-full object-contain p-4">
                @else
                    <div class="text-zinc-400 text-sm">—</div>
                @endif
            </div>
        </div>

        {{-- ══ RIGHT: Info ══ --}}
        <div class="flex flex-col">

            <span class="inline-flex w-fit rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-zinc-500">
                Print
            </span>

            <h1 class="mt-3 text-3xl font-bold tracking-tight text-zinc-950">
                {{ $productName }}
            </h1>

            <p class="mt-2 text-zinc-500 text-sm">Print #{{ $design->id }}</p>

            {{-- Variant details --}}
            @if ($colorLabel || $sizeLabel)
            <div class="mt-5 flex flex-wrap gap-3">
                @if ($colorLabel)
                <div class="flex items-center gap-2 rounded-xl bg-zinc-50 px-4 py-2 text-sm">
                    <span class="font-medium text-zinc-500">{{ __('site.constructor_color') }}:</span>
                    <span class="font-semibold text-zinc-900">{{ $colorLabel }}</span>
                </div>
                @endif
                @if ($sizeLabel)
                <div class="flex items-center gap-2 rounded-xl bg-zinc-50 px-4 py-2 text-sm">
                    <span class="font-medium text-zinc-500">{{ __('site.constructor_size') }}:</span>
                    <span class="font-semibold text-zinc-900">{{ $sizeLabel }}</span>
                </div>
                @endif
            </div>
            @endif

            {{-- CTA --}}
            <div class="mt-8 flex flex-col gap-3">
                @if ($product)
                    <a href="{{ route('constructor.show', ['product' => $product, 'variant' => $firstVariant?->id]) }}"
                       class="flex items-center justify-center gap-2 rounded-xl bg-zinc-950 px-6 py-4 text-base font-semibold text-white transition-all hover:bg-zinc-800 active:scale-[0.98]">
                        <span>🎨</span>
                        <span>{{ __('site.catalog_create_own') }}</span>
                    </a>
                @endif
                <a href="{{ route('catalog.index') }}"
                   class="flex items-center justify-center rounded-xl border border-zinc-200 px-6 py-3 text-sm font-medium text-zinc-600 transition-all hover:border-zinc-400 hover:text-zinc-950">
                    ← {{ __('site.nav_catalog') }}
                </a>
            </div>

            {{-- Trust badges --}}
            <div class="mt-8 grid grid-cols-3 gap-3 text-center text-xs text-zinc-500">
                <div class="rounded-xl bg-zinc-50 px-2 py-3">
                    <div class="mb-1 text-xl">🖨</div>
                    <div>{{ __('site.trust_print') }}</div>
                </div>
                <div class="rounded-xl bg-zinc-50 px-2 py-3">
                    <div class="mb-1 text-xl">📦</div>
                    <div>{{ __('site.trust_delivery') }}</div>
                </div>
                <div class="rounded-xl bg-zinc-50 px-2 py-3">
                    <div class="mb-1 text-xl">✅</div>
                    <div>{{ __('site.trust_quality') }}</div>
                </div>
            </div>

        </div>
    </div>

</main>

</x-layouts.public>
