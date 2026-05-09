@php
    $firstVariant = $product->variants->first();
    $variants     = $product->variants;
    $productName = $product->localizedName();

    $colourMap = [
        'white'  => '#ffffff', 'белый'       => '#ffffff',
        'black'  => '#111111', 'чёрный'      => '#111111', 'черный' => '#111111',
        'grey'   => '#9ca3af', 'gray'         => '#9ca3af', 'серый'  => '#9ca3af',
        'red'    => '#ef4444', 'красный'      => '#ef4444',
        'blue'   => '#3b82f6', 'синий'        => '#3b82f6',
        'navy'   => '#1e3a5f', 'темно-синий'  => '#1e3a5f',
        'green'  => '#22c55e', 'зелёный'      => '#22c55e',
        'yellow' => '#eab308', 'жёлтый'       => '#eab308',
        'pink'   => '#ec4899', 'розовый'      => '#ec4899',
        'orange' => '#f97316', 'оранжевый'    => '#f97316',
        'purple' => '#a855f7', 'фиолетовый'   => '#a855f7',
        'beige'  => '#e5d3b3', 'бежевый'      => '#e5d3b3',
    ];

    $colours = $variants->pluck('color')->unique()->values();
    $sizes   = $variants->pluck('size')->filter()->unique()->values();

    $variantData = $variants->map(fn($v) => [
        'id'       => $v->id,
        'color'    => $v->color,
        'size'     => $v->size,
        'price'    => $product->base_price + $v->price_modifier,
        'front_url'=> $v->mockup_front_url,
        'back_url' => $v->mockup_back_url,
    ])->values();

    $isMug = $product->type === \App\Enums\ProductType::Mug;
@endphp

<x-layouts.public
    :title="$productName . ' — PrintLab'"
    :description="$product->type->label() . ' ' . $productName . '. ' . __('site.seo_home_description')"
>

<main class="mx-auto max-w-6xl px-4 py-8">

    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center gap-2 text-sm text-zinc-400">
        <a href="{{ route('home') }}" class="hover:text-zinc-700 transition-colors">PrintLab</a>
        <span>/</span>
        <a href="{{ route('catalog.index') }}" class="hover:text-zinc-700 transition-colors">{{ __('site.nav_catalog') }}</a>
        <span>/</span>
        <span class="text-zinc-700">{{ $productName }}</span>
    </nav>

    <div class="grid gap-10 lg:grid-cols-2">

        {{-- ══ LEFT: Images ══ --}}
        <div>
            <div class="relative overflow-hidden rounded-2xl bg-zinc-100 aspect-square">
                <img id="mainImage"
                     src="{{ $firstVariant->mockup_front_url }}"
                     alt="{{ $productName }}"
                     class="h-full w-full object-contain p-6"
                     style="transition: opacity 0.15s ease">

                @if ($firstVariant->mockup_back_url)
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex overflow-hidden rounded-full border border-zinc-200 bg-white text-sm font-medium shadow-sm">
                    <button id="btnFront" onclick="switchSide('front')"
                            class="px-4 py-1.5 bg-zinc-950 text-white transition-colors">
                        {{ $isMug ? __('site.constructor_side_left') : __('site.constructor_side_front') }}
                    </button>
                    <button id="btnBack" onclick="switchSide('back')"
                            class="px-4 py-1.5 text-zinc-600 hover:text-zinc-950 transition-colors">
                        {{ $isMug ? __('site.constructor_side_right') : __('site.constructor_side_back') }}
                    </button>
                </div>
                @endif
            </div>

            {{-- Thumbnails --}}
            @if ($variants->count() > 1)
            <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                @foreach ($variants as $v)
                <button onclick="selectVariant({{ $v->id }})"
                        data-variant-thumb="{{ $v->id }}"
                        class="shrink-0 h-16 w-16 overflow-hidden rounded-xl border-2 bg-zinc-100 transition-all
                               {{ $loop->first ? 'border-zinc-950 scale-105' : 'border-transparent hover:border-zinc-300' }}">
                    <img src="{{ $v->mockup_front_url }}" alt="{{ $v->color }}"
                         class="h-full w-full object-contain p-1">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ══ RIGHT: Info ══ --}}
        <div class="flex flex-col">

            <span class="inline-flex w-fit rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-zinc-500">
                {{ $product->type->label() }}
            </span>

            <h1 class="mt-3 text-3xl font-bold tracking-tight text-zinc-950">{{ $productName }}</h1>

            <div class="mt-3 flex items-baseline gap-1">
                <span id="priceDisplay" class="text-2xl font-bold text-zinc-950">
                    {{ number_format($product->base_price + ($firstVariant->price_modifier ?? 0), 0, '.', ' ') }}
                </span>
                <span class="text-base font-normal text-zinc-400">UZS</span>
            </div>

            <div class="mt-6 space-y-5">

                {{-- Colour --}}
                @if ($colours->count() > 0)
                <div>
                    <p class="mb-2 text-sm font-medium text-zinc-700">
                        {{ __('site.constructor_color') }}:
                        <span id="colourLabel" class="font-semibold text-zinc-950">{{ $firstVariant->color }}</span>
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($colours as $colour)
                        @php $hex = str_starts_with($colour, '#') ? $colour : ($colourMap[strtolower($colour)] ?? '#e5e7eb'); @endphp
                        <button onclick="selectColour('{{ $colour }}')"
                                data-colour="{{ $colour }}"
                                title="{{ $colour }}"
                                class="colour-swatch relative h-9 w-9 rounded-full border-2 transition-all
                                       {{ $loop->first ? 'border-zinc-950 scale-110' : 'border-transparent hover:scale-105' }}"
                                style="background-color:{{ $hex }};box-shadow:inset 0 0 0 1px rgba(0,0,0,0.12)">
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Size --}}
                @if ($sizes->count() > 0)
                <div>
                    <p class="mb-2 text-sm font-medium text-zinc-700">
                        {{ __('site.constructor_size') }}:
                        <span id="sizeLabel" class="font-semibold text-zinc-950">{{ $firstVariant->size }}</span>
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($sizes as $size)
                        <button onclick="selectSize('{{ $size }}')"
                                data-size="{{ $size }}"
                                class="size-btn min-w-[44px] rounded-xl border-2 px-3 py-1.5 text-sm font-semibold transition-all
                                       {{ $firstVariant->size === $size ? 'border-zinc-950 bg-zinc-950 text-white' : 'border-zinc-200 text-zinc-700 hover:border-zinc-400' }}">
                            {{ $size }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>

            {{-- CTA --}}
            <div class="mt-8 flex flex-col gap-3">
                <a id="ctaConstructor"
                   href="{{ route('constructor.show', ['product' => $product, 'variant' => $firstVariant->id]) }}"
                   class="flex items-center justify-center gap-2 rounded-xl bg-zinc-950 px-6 py-4 text-base font-semibold text-white transition-all hover:bg-zinc-800 active:scale-[0.98]">
                    <span>🎨</span>
                    <span>{{ __('site.catalog_constructor') }}</span>
                </a>
                <a id="ctaAiStudio"
                   href="{{ route('ai-studio.show', ['product' => $product, 'variant' => $firstVariant->id]) }}"
                   class="flex items-center justify-center gap-2 rounded-xl bg-red-600 px-6 py-4 text-base font-semibold text-white transition-all hover:bg-red-700 active:scale-[0.98]">
                    <span>AI</span>
                    <span>{{ __('site.product_create_with_ai') }}</span>
                </a>
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

<script>
const variants       = @json($variantData);
const constructorBase = '{{ route('constructor.show', $product) }}';
const aiStudioBase = '{{ route('ai-studio.show', $product) }}';

let currentSide      = 'front';
let currentColour    = '{{ $firstVariant->color }}';
let currentSize      = '{{ $firstVariant->size ?? '' }}';
let currentVariantId = {{ $firstVariant->id }};

function findVariant(colour, size) {
    return variants.find(v => v.color === colour && (size === '' ? !v.size : v.size === size))
        || variants.find(v => v.color === colour)
        || variants[0];
}

function applyVariant(v) {
    if (!v) return;
    currentVariantId = v.id;

    // Update image with fade
    const img = document.getElementById('mainImage');
    const url = (currentSide === 'back' && v.back_url) ? v.back_url : v.front_url;
    img.style.opacity = '0';
    setTimeout(() => { img.src = url; img.style.opacity = '1'; }, 150);

    // Update price
    document.getElementById('priceDisplay').textContent =
        new Intl.NumberFormat('ru-RU').format(v.price);

    // Update CTA
    document.getElementById('ctaConstructor').href = constructorBase + '?variant=' + v.id;
    document.getElementById('ctaAiStudio').href = aiStudioBase + '?variant=' + v.id;

    // Update thumbnails
    document.querySelectorAll('[data-variant-thumb]').forEach(el => {
        const active = parseInt(el.dataset.variantThumb) === v.id;
        el.classList.toggle('border-zinc-950', active);
        el.classList.toggle('scale-105', active);
        el.classList.toggle('border-transparent', !active);
    });
}

function selectColour(colour) {
    currentColour = colour;
    document.querySelectorAll('.colour-swatch').forEach(el => {
        const active = el.dataset.colour === colour;
        el.classList.toggle('border-zinc-950', active);
        el.classList.toggle('scale-110', active);
        el.classList.toggle('border-transparent', !active);
    });
    const lbl = document.getElementById('colourLabel');
    if (lbl) lbl.textContent = colour;
    applyVariant(findVariant(colour, currentSize));
}

function selectSize(size) {
    currentSize = size;
    document.querySelectorAll('.size-btn').forEach(el => {
        const active = el.dataset.size === size;
        el.classList.toggle('border-zinc-950', active);
        el.classList.toggle('bg-zinc-950', active);
        el.classList.toggle('text-white', active);
        el.classList.toggle('border-zinc-200', !active);
        el.classList.toggle('text-zinc-700', !active);
    });
    const lbl = document.getElementById('sizeLabel');
    if (lbl) lbl.textContent = size;
    applyVariant(findVariant(currentColour, size));
}

function selectVariant(id) {
    const v = variants.find(v => v.id === id);
    if (!v) return;
    currentColour = v.color;
    currentSize   = v.size || '';

    document.querySelectorAll('.colour-swatch').forEach(el => {
        const active = el.dataset.colour === v.color;
        el.classList.toggle('border-zinc-950', active);
        el.classList.toggle('scale-110', active);
        el.classList.toggle('border-transparent', !active);
    });
    document.querySelectorAll('.size-btn').forEach(el => {
        const active = el.dataset.size === v.size;
        el.classList.toggle('border-zinc-950', active);
        el.classList.toggle('bg-zinc-950', active);
        el.classList.toggle('text-white', active);
        el.classList.toggle('border-zinc-200', !active);
        el.classList.toggle('text-zinc-700', !active);
    });

    const clbl = document.getElementById('colourLabel');
    if (clbl) clbl.textContent = v.color;
    const slbl = document.getElementById('sizeLabel');
    if (slbl && v.size) slbl.textContent = v.size;

    applyVariant(v);
}

function switchSide(side) {
    currentSide = side;
    const btnF = document.getElementById('btnFront');
    const btnB = document.getElementById('btnBack');
    if (btnF) {
        btnF.className = 'px-4 py-1.5 transition-colors ' +
            (side === 'front' ? 'bg-zinc-950 text-white' : 'text-zinc-600 hover:text-zinc-950');
        btnB.className = 'px-4 py-1.5 transition-colors ' +
            (side === 'back'  ? 'bg-zinc-950 text-white' : 'text-zinc-600 hover:text-zinc-950');
    }
    applyVariant(variants.find(v => v.id === currentVariantId));
}
</script>

</x-layouts.public>
