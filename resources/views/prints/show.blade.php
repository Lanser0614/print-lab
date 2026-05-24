@php
    $previewUrl = $design->preview_image_path
        ? Storage::disk('public')->url($design->preview_image_path)
        : null;

    $productName = $item->product_name_snapshot ?? $product?->localizedName() ?? 'PrintLab';
    $colorLabel = $item->color_snapshot ?? null;
    $sizeLabel = $item->size_snapshot ?? null;
    $firstVariant = $product?->variants->first();
    $constructorProduct = $product ?? $featuredProduct;
    $constructorVariant = $firstVariant ?? $featuredProduct?->variants->first();
    $constructorUrl = $constructorProduct
        ? route('constructor.show', ['product' => $constructorProduct, 'variant' => $constructorVariant?->id])
        : route('constructor.fallback');
    $activeProductCategories = $productCategories->keyBy('slug');
    $activePrintCategories = $printCategories->keyBy('slug');
    $categoryUrl = fn (string $slug): string => route('catalog.index', ['category' => $slug]);
    $printCategoryUrl = fn (string $slug): string => route('catalog.index', ['print_category' => $slug]) . '#prints';
@endphp

<x-layouts.public
    :title="$productName . ' — PrintLab'"
    :description="__('site.print_detail_description', ['product' => $productName])"
    :og-image="$previewUrl"
    :chrome="false"
>
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
                    <a href="{{ route('account.index') }}" class="pl-header-action"><span aria-hidden="true">👤</span><span>{{ __('auth.my_account') }}</span></a>
                @else
                    <a href="{{ route('login') }}" class="pl-header-action"><span aria-hidden="true">👤</span><span>{{ __('site.topbar_login') }}</span></a>
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

    <main class="pl-print-detail-page">
        <section class="pl-container pl-section">
            <nav class="pl-print-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">PrintLab</a>
                <span>/</span>
                <a href="{{ route('catalog.index') }}#prints">{{ __('site.nav_prints') }}</a>
                <span>/</span>
                <span>{{ __('site.print_detail_title') }} #{{ $design->id }}</span>
            </nav>

            <div class="pl-print-detail">
                <div class="pl-print-preview">
                    @if ($previewUrl)
                        <img src="{{ $previewUrl }}" alt="{{ __('site.print_detail_title') }} #{{ $design->id }}">
                    @else
                        <div class="pl-empty-card">—</div>
                    @endif
                </div>

                <div class="pl-print-info">
                    <span class="pl-print-badge">{{ __('site.catalog_prints_title') }}</span>
                    <h1>{{ $productName }}</h1>
                    <p>{{ __('site.print_detail_subtitle', ['id' => $design->id]) }}</p>

                    @if ($colorLabel || $sizeLabel)
                        <dl class="pl-print-meta">
                            @if ($colorLabel)
                                <div>
                                    <dt>{{ __('site.constructor_color') }}</dt>
                                    <dd>{{ $colorLabel }}</dd>
                                </div>
                            @endif
                            @if ($sizeLabel)
                                <div>
                                    <dt>{{ __('site.constructor_size') }}</dt>
                                    <dd>{{ $sizeLabel }}</dd>
                                </div>
                            @endif
                        </dl>
                    @endif

                    <div class="pl-print-actions">
                        <a class="pl-print-primary" href="{{ $constructorUrl }}">{{ __('site.print_detail_create_cta') }}</a>
                        <a class="pl-print-secondary" href="{{ route('catalog.index') }}#prints">{{ __('site.catalog_all_prints') }}</a>
                    </div>

                    <div class="pl-print-trust">
                        <div>
                            <strong>{{ __('site.trust_print') }}</strong>
                            <span>{{ __('site.print_detail_trust_print') }}</span>
                        </div>
                        <div>
                            <strong>{{ __('site.trust_delivery') }}</strong>
                            <span>{{ __('site.print_detail_trust_delivery') }}</span>
                        </div>
                        <div>
                            <strong>{{ __('site.trust_quality') }}</strong>
                            <span>{{ __('site.print_detail_trust_quality') }}</span>
                        </div>
                    </div>
                </div>
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
