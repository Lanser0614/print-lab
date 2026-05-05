<x-layouts.public
    :title="__('site.seo_catalog_title')"
    :description="__('site.seo_catalog_description')"
    :keywords="__('site.seo_catalog_keywords')"
>
    <main class="mx-auto max-w-7xl px-4 py-8">
        <section id="products" class="mb-10">
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight">{{ __('site.catalog_title') }}</h1>
                    <p class="mt-2 text-zinc-600">{{ __('site.catalog_subtitle') }}</p>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($products as $product)
                    @php $variant = $product->variants->first(); @endphp
                    <article class="overflow-hidden rounded-lg border border-zinc-200 bg-white">
                        <div class="flex aspect-square items-center justify-center bg-zinc-100 p-6">
                            @if ($variant)
                                <img src="{{ $variant->mockup_front_url }}"
                                     alt="{{ $product->name }}"
                                     class="h-full w-full object-contain"
                                     loading="lazy">
                            @else
                                <div class="text-sm text-zinc-500">—</div>
                            @endif
                        </div>
                        <div class="p-5">
                            <div class="text-sm uppercase tracking-wide text-zinc-500">{{ $product->type->label() }}</div>
                            <h2 class="mt-2 text-xl font-semibold">{{ $product->name }}</h2>
                            <div class="mt-2 text-zinc-600">
                                {{ __('site.catalog_from_price', ['price' => number_format($product->base_price, 0, '.', ' ')]) }}
                            </div>
                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('products.show', $product) }}"
                                   class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-medium hover:border-zinc-500 transition-colors">
                                    {{ __('site.catalog_select') }}
                                </a>
                                <a href="{{ route('constructor.show', $product) }}"
                                   class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-medium text-white hover:bg-zinc-800 transition-colors">
                                    {{ __('site.catalog_constructor') }}
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg border border-dashed border-zinc-300 bg-white p-6 text-zinc-600">
                        {{ __('site.catalog_no_products') }}
                    </div>
                @endforelse
            </div>
        </section>

        <section id="prints">
            <h2 class="mb-4 text-2xl font-semibold tracking-tight">{{ __('site.catalog_prints_title') }}</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @forelse ($prints as $print)
                    @php
                        $item         = $print->item;
                        $orderRequest = $item?->orderRequest;
                        $product      = $item?->product;
                    @endphp
                    <article class="rounded-lg border border-zinc-200 bg-white p-4">
                        <a href="{{ route('prints.show', $print) }}" class="block mb-3 aspect-square overflow-hidden rounded-md bg-zinc-100">
                            <img src="{{ Storage::disk('public')->url($print->preview_image_path) }}"
                                 alt="Print #{{ $orderRequest?->id }}"
                                 class="h-full w-full object-cover transition-transform hover:scale-105"
                                 loading="lazy">
                        </a>
                        <div class="text-sm text-zinc-500">{{ $item?->product_name_snapshot ?? 'PrintLab' }}</div>
                        <h3 class="mt-1 font-semibold">
                            <a href="{{ route('prints.show', $print) }}" class="hover:text-zinc-600 transition-colors">
                                Print #{{ $orderRequest?->id }}
                            </a>
                        </h3>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('prints.show', $print) }}"
                               class="rounded-md border border-zinc-300 px-3 py-2 text-sm font-medium hover:border-zinc-500 transition-colors">
                                {{ __('site.catalog_select') }}
                            </a>
                            @if ($product)
                                <a href="{{ route('constructor.show', $product) }}"
                                   class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-medium text-white hover:bg-zinc-800 transition-colors">
                                    {{ __('site.catalog_constructor') }}
                                </a>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg border border-dashed border-zinc-300 bg-white p-6 text-zinc-600">
                        {{ __('site.catalog_no_prints') }}
                    </div>
                @endforelse
            </div>
        </section>
    </main>
</x-layouts.public>
