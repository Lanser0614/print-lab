<x-layouts.public
    :title="__('site.seo_catalog_title')"
    :description="__('site.seo_catalog_description')"
    :keywords="__('site.seo_catalog_keywords')"
    :chrome="false"
>
    @php
        $featuredProduct = $products->first();
        $constructorUrl = $featuredProduct
            ? route('constructor.show', $featuredProduct)
            : route('constructor.fallback');
        $activeProductCategories = $productCategories->keyBy('slug');
        $activePrintCategories = $printCategories->keyBy('slug');
        $categoryUrl = fn (string $slug): string => route('catalog.index', ['category' => $slug]);
        $printCategoryUrl = fn (string $slug): string => route('catalog.index', ['print_category' => $slug]) . '#prints';
    @endphp

    <div class="pl-topbar">
        <div class="pl-container" style="display:flex;align-items:center;width:100%">
            <span class="pl-city">{{ __('site.city_tashkent') }}</span>
            <span style="width:24px"></span>
            <a href="{{ route('home') }}#benefits">{{ __('site.topbar_delivery') }}</a>
            <a href="{{ route('home') }}#benefits">{{ __('site.topbar_payment') }}</a>
            <a href="{{ route('home') }}#benefits">{{ __('site.topbar_warranty') }}</a>
            <a href="{{ route('home') }}#contacts">{{ __('site.topbar_help') }}</a>
            <a href="{{ route('home') }}#contacts">{{ __('site.topbar_partners') }}</a>
            <span class="pl-spacer"></span>
            <span class="pl-phone">+998 90 123 45 67</span>
            @auth
                <a href="{{ route('account.index') }}">{{ __('auth.my_account') }}</a>
            @else
                <a href="{{ route('login') }}">{{ __('site.topbar_login') }}</a>
            @endauth
        </div>
    </div>

    <header class="pl-header">
        <div class="pl-container pl-header-row">
            <a href="{{ route('home') }}" class="pl-logo">
                <span class="pl-logo-mark">P</span>
                <span class="pl-logo-text">Print<span>Lab</span></span>
            </a>
            <form class="pl-search" action="{{ route('catalog.index') }}">
                <input name="q" placeholder="{{ __('site.header_search_placeholder') }}" value="{{ request('q') }}">
                <button>{{ __('site.header_search_button') }}</button>
            </form>
            <div class="pl-header-actions">
                <x-language-switcher class="pl-language-switcher--header" />
                @auth
                    <a href="{{ route('account.index') }}" class="pl-header-action"><span aria-hidden="true">◎</span><span>{{ __('auth.my_account') }}</span></a>
                @else
                    <a href="{{ route('login') }}" class="pl-header-action"><span aria-hidden="true">◎</span><span>{{ __('site.topbar_login') }}</span></a>
                @endauth
            </div>
        </div>
    </header>

    <nav class="pl-nav">
        <div class="pl-container pl-nav-row">
            <a href="{{ route('catalog.index') }}" class="pl-nav-cat">☰ {{ mb_strtoupper(__('site.nav_catalog')) }}</a>
            <ul>
                <li><a href="{{ $constructorUrl }}">{{ __('site.catalog_constructor') }} <span class="pl-nav-tag">HIT</span></a></li>
                @if ($activeProductCategories->has('t-shirts'))<li><a href="{{ $categoryUrl('t-shirts') }}">{{ $activeProductCategories->get('t-shirts')->localizedName() }}</a></li>@endif
                @if ($activeProductCategories->has('hoodies'))<li><a href="{{ $categoryUrl('hoodies') }}">{{ $activeProductCategories->get('hoodies')->localizedName() }}</a></li>@endif
                @if ($activeProductCategories->has('sweatshirts'))<li><a href="{{ $categoryUrl('sweatshirts') }}">{{ $activeProductCategories->get('sweatshirts')->localizedName() }}</a></li>@endif
                @if ($activeProductCategories->has('longsleeves'))<li><a href="{{ $categoryUrl('longsleeves') }}">{{ $activeProductCategories->get('longsleeves')->localizedName() }}</a></li>@endif
                @if ($activePrintCategories->has('memes'))<li><a href="{{ $printCategoryUrl('memes') }}">{{ $activePrintCategories->get('memes')->localizedName() }} <span class="pl-nav-tag">NEW</span></a></li>@endif
                @if ($activeProductCategories->has('custom-products'))<li><a href="{{ $categoryUrl('custom-products') }}">{{ $activeProductCategories->get('custom-products')->localizedName() }}</a></li>@endif
                @if ($activeProductCategories->has('kids'))<li><a href="{{ $categoryUrl('kids') }}">{{ $activeProductCategories->get('kids')->localizedName() }}</a></li>@endif
                <li><a href="{{ route('catalog.index') }}">{{ __('site.nav_sale') }}</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <section id="products" class="pl-container pl-section">
            <div class="pl-section-head">
                <h2>{!! __('site.home_catalog_heading') !!}</h2>
                <a class="pl-link" href="{{ route('catalog.index') }}">{{ __('site.catalog_all_products') }}</a>
            </div>

            @if ($productCategories->isNotEmpty())
                <div style="display:flex;gap:6px;margin-bottom:18px;flex-wrap:wrap">
                    <a class="pl-c-tag {{ empty($selectedProductCategory) ? 'active' : '' }}" href="{{ route('catalog.index') }}">{{ __('site.catalog_all') }}</a>
                    @foreach ($productCategories as $category)
                        <a class="pl-c-tag {{ ($selectedProductCategory ?? null) === $category->slug ? 'active' : '' }}"
                           href="{{ $categoryUrl($category->slug) }}">
                            {{ $category->localizedName() }} <span>{{ $category->products_count }}</span>
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="pl-grid">
                @forelse ($products as $product)
                    @php $variant = $product->variants->first(); @endphp
                    <a class="pl-card" href="{{ route('constructor.show', $product) }}">
                        <div class="pl-card-img">
                            <div class="pl-card-tags"><span class="pl-card-tag new">new</span></div>
                            @if ($variant)
                                <img src="{{ $variant->mockup_front_url }}" alt="{{ $product->localizedName() }}" style="width:100%;height:100%;object-fit:contain" loading="lazy">
                            @else
                                <div class="pl-empty-card">{{ __('site.home_no_image') }}</div>
                            @endif
                        </div>
                        <div class="pl-card-body">
                            <div class="pl-card-name">{{ $product->localizedName() }}</div>
                            <div class="pl-card-rating"><span class="pl-stars">★★★★★</span><span>{{ $product->type->label() }}</span></div>
                            <div class="pl-card-price-row">
                                <span class="pl-card-price">{{ number_format($product->base_price, 0, '.', ' ') }} UZS</span>
                            </div>
                            <div class="pl-card-colors">
                                @foreach ($product->variants->pluck('color')->filter()->unique()->take(4) as $color)
                                    <span class="pl-card-color" style="background:{{ $color }}"></span>
                                @endforeach
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="pl-empty-card">
                        {{ __('site.catalog_no_products') }}
                    </div>
                @endforelse
            </div>
        </section>

        <section id="prints" class="pl-container pl-section">
            <div class="pl-section-head">
                <h2>{{ __('site.catalog_prints_title') }}</h2>
                <a class="pl-link" href="{{ route('catalog.index') }}#prints">{{ __('site.catalog_all_prints') }}</a>
            </div>

            @if ($printCategories->isNotEmpty())
                <div style="display:flex;gap:6px;margin-bottom:18px;flex-wrap:wrap">
                    <a class="pl-c-tag {{ empty($selectedPrintCategory) ? 'active' : '' }}" href="{{ route('catalog.index') }}#prints">{{ __('site.catalog_all') }}</a>
                    @foreach ($printCategories as $category)
                        <a class="pl-c-tag {{ ($selectedPrintCategory ?? null) === $category->slug ? 'active' : '' }}"
                           href="{{ $printCategoryUrl($category->slug) }}">
                            {{ $category->localizedName() }} <span>{{ $category->ready_prints_count }}</span>
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="pl-prints">
                @forelse ($prints as $print)
                    @php
                        $item = $print->item;
                        $orderRequest = $item?->orderRequest;
                        $product = $item?->product;
                    @endphp
                    <a href="{{ route('prints.show', $print) }}" class="pl-print">
                        <img src="{{ Storage::disk('public')->url($print->preview_image_path) }}" alt="Print #{{ $orderRequest?->id }}" style="width:76%;height:76%;object-fit:contain" loading="lazy">
                        <span class="pl-print-label">{{ $item?->product_name_snapshot ?? 'PrintLab' }}</span>
                    </a>
                @empty
                    <div class="pl-empty-card">
                        {{ __('site.catalog_no_prints') }}
                    </div>
                @endforelse
            </div>
        </section>
    </main>

    <footer id="contacts" class="pl-footer">
        <div class="pl-container">
            <div class="pl-footer-cols">
                <div><h4>{{ __('site.nav_catalog') }}</h4><ul>@if ($activeProductCategories->has('t-shirts'))<li><a href="{{ $categoryUrl('t-shirts') }}">{{ $activeProductCategories->get('t-shirts')->localizedName() }}</a></li>@endif @if ($activeProductCategories->has('hoodies'))<li><a href="{{ $categoryUrl('hoodies') }}">{{ $activeProductCategories->get('hoodies')->localizedName() }}</a></li>@endif @if ($activeProductCategories->has('sweatshirts'))<li><a href="{{ $categoryUrl('sweatshirts') }}">{{ $activeProductCategories->get('sweatshirts')->localizedName() }}</a></li>@endif <li><a href="{{ route('catalog.index') }}#prints">{{ __('site.nav_prints') }}</a></li></ul></div>
                <div><h4>{{ __('site.footer_help') }}</h4><ul><li><a href="{{ route('home') }}#benefits">{{ __('site.topbar_delivery') }}</a></li><li><a href="{{ route('home') }}#benefits">{{ __('site.topbar_payment') }}</a></li><li><a href="{{ route('home') }}#contacts">{{ __('site.footer_return') }}</a></li><li><a href="{{ route('catalog.index') }}">{{ __('site.footer_sizes') }}</a></li></ul></div>
                <div><h4>{{ __('site.topbar_partners') }}</h4><ul><li><a href="{{ route('home') }}#contacts">{{ __('site.footer_wholesale') }}</a></li><li><a href="{{ route('home') }}#contacts">{{ __('site.footer_merch') }}</a></li><li><a href="{{ route('home') }}#contacts">{{ __('site.footer_designers') }}</a></li><li><a href="{{ route('home') }}#contacts">{{ __('site.footer_franchise') }}</a></li></ul></div>
                <div><h4>{{ __('site.footer_about') }}</h4><ul><li><a href="{{ route('home') }}">PrintLab</a></li><li><a href="{{ route('home') }}#contacts">{{ __('site.nav_contacts') }}</a></li><li><a href="{{ route('home') }}#benefits">{{ __('site.footer_production') }}</a></li><li><a href="{{ route('catalog.index') }}">{{ __('site.footer_reviews') }}</a></li></ul></div>
                <div>
                    <h4>{{ __('site.footer_subscription') }}</h4>
                    <p style="margin:0 0 12px;color:#8a8a8a">{{ __('site.footer_subscription_text') }}</p>
                    <form class="pl-search" style="height:40px">
                        <input type="email" placeholder="email@example.com">
                        <button>OK</button>
                    </form>
                </div>
            </div>
            <div class="pl-footer-bottom">
                <span>© 2026 PrintLab</span>
                <span>{{ __('site.footer_short_text') }}</span>
            </div>
        </div>
    </footer>
</x-layouts.public>
