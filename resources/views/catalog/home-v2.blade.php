<x-layouts.public
    :title="__('site.seo_home_title')"
    :description="__('site.seo_home_description')"
    :keywords="__('site.seo_home_keywords')"
    :chrome="false"
>
    @php
        $categoryColors = ['#1f3a8a', '#e9789e', '#1a1a1a', '#7c4a2a', '#1e6b3a', '#ffd400', '#c91e1e', '#6e3aa7'];
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
            <a href="#benefits">{{ __('site.topbar_delivery') }}</a>
            <a href="#benefits">{{ __('site.topbar_payment') }}</a>
            <a href="#benefits">{{ __('site.topbar_warranty') }}</a>
            <a href="#contacts">{{ __('site.topbar_help') }}</a>
            <a href="#contacts">{{ __('site.topbar_partners') }}</a>
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
                <input name="q" placeholder="{{ __('site.header_search_placeholder') }}">
                <button>{{ __('site.header_search_button') }}</button>
            </form>
            <div class="pl-header-actions">
                <x-language-switcher class="pl-language-switcher--header" />
                @auth
                    <a href="{{ route('account.index') }}" class="pl-header-action">
                        <span aria-hidden="true">👤</span>
                        <span>{{ __('auth.my_account') }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="pl-header-action">
                        <span aria-hidden="true">👤</span>
                        <span>{{ __('site.topbar_login') }}</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <nav class="pl-nav">
        <div class="pl-container pl-nav-row">
            <a href="{{ route('catalog.index') }}" class="pl-nav-cat">☰ {{ mb_strtoupper(__('site.nav_catalog')) }}</a>
            <ul>
                <li class="active"><a href="{{ $constructorUrl }}">{{ __('site.catalog_constructor') }} <span class="pl-nav-tag">HIT</span></a></li>
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
        <section class="pl-container">
            <div class="pl-hero">
                <div class="pl-hero-main">
                    <div class="pl-hero-content">
                        <span class="pl-hero-badge">{{ __('site.home_discount_badge') }}</span>
                        <h1>{!! __('site.home_hero_title') !!}</h1>
                        <p>{{ __('site.home_hero_text') }}</p>
                        <a class="pl-hero-cta" href="{{ $constructorUrl }}">{{ __('site.home_hero_cta') }}</a>
                    </div>
                    <div class="pl-hero-shirt">
                        <img src="/mockups/tshirts/white-front.png" alt="" style="width:100%;height:100%;object-fit:contain">
                    </div>
                </div>
                <div class="pl-hero-side">
                    <a href="{{ $activeProductCategories->has('t-shirts') ? $categoryUrl('t-shirts') : route('catalog.index') }}" class="pl-hero-card red">
                        <div>
                            <h3>{!! __('site.home_couple_title') !!}</h3>
                            <p>{{ __('site.home_couple_text') }}</p>
                        </div>
                        <div class="pl-hero-card-foot">{{ __('site.home_view_cta') }}</div>
                    </a>
                    <a href="{{ $activeProductCategories->has('hoodies') ? $categoryUrl('hoodies') : route('catalog.index') }}" class="pl-hero-card dark">
                        <div>
                            <h3>{!! __('site.home_hoodie_title') !!}</h3>
                            <p>{{ __('site.home_hoodie_text') }}</p>
                        </div>
                        <div class="pl-hero-card-foot">{{ __('site.home_catalog_cta') }}</div>
                    </a>
                </div>
            </div>
        </section>

        <section class="pl-container pl-section">
            <div class="pl-section-head">
                <h2>{!! __('site.home_catalog_heading') !!}</h2>
                <a class="pl-link" href="{{ route('catalog.index') }}">{{ __('site.home_all_categories') }}</a>
            </div>
            <div class="pl-cats">
                @forelse ($productCategories as $category)
                    <a href="{{ $categoryUrl($category->slug) }}" class="pl-cat">
                        <div class="pl-cat-img">
                            <svg viewBox="0 0 400 480" style="width:100%;height:100%" xmlns="http://www.w3.org/2000/svg">
                                <path d="M120 50 L80 70 L40 130 L70 170 L100 150 L100 440 Q100 460 120 460 L280 460 Q300 460 300 440 L300 150 L330 170 L360 130 L320 70 L280 50 Q260 80 200 80 Q140 80 120 50 Z" fill="{{ $categoryColors[$loop->index % count($categoryColors)] }}" stroke="rgba(0,0,0,0.15)" stroke-width="1.5"/>
                                <path d="M155 50 Q200 90 245 50 Q230 75 200 75 Q170 75 155 50 Z" fill="rgba(0,0,0,0.08)"/>
                            </svg>
                        </div>
                        <div class="pl-cat-name">{{ $category->localizedName() }}</div>
                        <div class="pl-cat-from">{{ __('site.home_products_count', ['count' => $category->products_count]) }}</div>
                    </a>
                @empty
                    <div class="rounded-lg border border-dashed border-pl-gray-300 bg-white p-6 text-sm text-pl-gray-500">
                        {{ __('site.home_no_categories') }}
                    </div>
                @endforelse
            </div>
        </section>

        <section id="products" class="pl-container pl-section">
            <div class="pl-section-head">
                <h2>{!! __('site.home_top_sales') !!}</h2>
                <a class="pl-link" href="{{ route('catalog.index') }}">{{ __('site.home_all_hits') }}</a>
            </div>
            <div class="pl-grid">
                @forelse ($products as $product)
                    @php $variant = $product->variants->first(); @endphp
                    <a class="pl-card" href="{{ route('constructor.show', $product) }}">
                        <div class="pl-card-img">
                            <div class="pl-card-tags">
                                <span class="pl-card-tag new">new</span>
                            </div>
                            @if ($variant)
                                <img src="{{ $variant->mockup_front_url }}" alt="{{ $product->localizedName() }}" style="width:100%;height:100%;object-fit:contain" loading="lazy">
                            @else
                                <div class="rounded-lg border border-dashed border-pl-gray-300 bg-white p-6 text-sm text-pl-gray-500">{{ __('site.home_no_image') }}</div>
                            @endif
                        </div>
                        <div class="pl-card-body">
                            <div class="pl-card-name">{{ $product->localizedName() }}</div>
                            <div class="pl-card-rating"><span class="pl-stars">★★★★★</span><span>{{ __('site.home_sold_count') }}</span></div>
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
                    <div class="rounded-lg border border-dashed border-pl-gray-300 bg-white p-6 text-sm text-pl-gray-500">
                        {{ __('site.catalog_no_products') }}
                    </div>
                @endforelse
            </div>
        </section>

        <section class="pl-container">
            <div class="pl-promo">
                <div class="pl-promo-icon">✦</div>
                <div class="pl-promo-text">
                    <h3>{{ __('site.home_promo_title') }}</h3>
                    <p>{{ __('site.home_promo_text') }}</p>
                </div>
                <a class="pl-promo-cta" href="{{ $constructorUrl }}">{{ __('site.home_promo_cta') }}</a>
            </div>
        </section>

        <section id="prints" class="pl-container pl-section">
            <div class="pl-section-head">
                <h2>{!! __('site.home_popular_prints') !!}</h2>
                <a class="pl-link" href="{{ route('catalog.index') }}#prints">{{ __('site.catalog_all_prints') }}</a>
            </div>
            <div style="display:flex;gap:6px;margin-bottom:14px;flex-wrap:wrap">
                <button class="pl-c-tag active">{{ __('site.catalog_all') }}</button>
                @foreach ($printCategories as $category)
                    <a class="pl-c-tag" href="{{ $printCategoryUrl($category->slug) }}">{{ $category->localizedName() }}</a>
                @endforeach
            </div>
            <div class="pl-prints">
                @forelse ($prints as $print)
                    <a href="{{ route('prints.show', $print) }}" class="pl-print">
                        <img src="{{ Storage::disk('public')->url($print->preview_image_path) }}" alt="Print #{{ $print->item?->orderRequest?->id }}" style="width:76%;height:76%;object-fit:contain" loading="lazy">
                        <span class="pl-print-label">Print #{{ $print->item?->orderRequest?->id }}</span>
                    </a>
                @empty
                    <div class="rounded-lg border border-dashed border-pl-gray-300 bg-white p-6 text-sm text-pl-gray-500">
                        {{ __('site.catalog_no_prints') }}
                    </div>
                @endforelse
            </div>
        </section>

        <section id="benefits" class="pl-container pl-section">
            <div class="grid gap-3 md:grid-cols-4">
                @foreach ([['🚚', __('site.benefit_delivery_title'), __('site.benefit_delivery_text')], ['🖨', __('site.benefit_print_title'), __('site.benefit_print_text')], ['🛡', __('site.benefit_quality_title'), __('site.benefit_quality_text')], ['✦', __('site.benefit_designs_title'), __('site.benefit_designs_text')]] as $benefit)
                    <div class="rounded-lg border border-pl-gray-200 bg-white p-5">
                        <div class="text-2xl">{{ $benefit[0] }}</div>
                        <h3 class="mt-3 text-sm font-extrabold">{{ $benefit[1] }}</h3>
                        <p class="mt-1 text-sm text-pl-gray-500">{{ $benefit[2] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </main>

    <footer id="contacts" class="pl-footer">
        <div class="pl-container">
            <div class="pl-footer-cols">
                <div><h4>{{ __('site.nav_catalog') }}</h4><ul>@if ($activeProductCategories->has('t-shirts'))<li><a href="{{ $categoryUrl('t-shirts') }}">{{ $activeProductCategories->get('t-shirts')->localizedName() }}</a></li>@endif @if ($activeProductCategories->has('hoodies'))<li><a href="{{ $categoryUrl('hoodies') }}">{{ $activeProductCategories->get('hoodies')->localizedName() }}</a></li>@endif @if ($activeProductCategories->has('sweatshirts'))<li><a href="{{ $categoryUrl('sweatshirts') }}">{{ $activeProductCategories->get('sweatshirts')->localizedName() }}</a></li>@endif <li><a href="{{ route('catalog.index') }}#prints">{{ __('site.nav_prints') }}</a></li></ul></div>
                <div><h4>{{ __('site.footer_help') }}</h4><ul><li><a href="#benefits">{{ __('site.topbar_delivery') }}</a></li><li><a href="#benefits">{{ __('site.topbar_payment') }}</a></li><li><a href="#contacts">{{ __('site.footer_return') }}</a></li><li><a href="{{ route('catalog.index') }}">{{ __('site.footer_sizes') }}</a></li></ul></div>
                <div><h4>{{ __('site.topbar_partners') }}</h4><ul><li><a href="#contacts">{{ __('site.footer_wholesale') }}</a></li><li><a href="#contacts">{{ __('site.footer_merch') }}</a></li><li><a href="#contacts">{{ __('site.footer_designers') }}</a></li><li><a href="#contacts">{{ __('site.footer_franchise') }}</a></li></ul></div>
                <div><h4>{{ __('site.footer_about') }}</h4><ul><li><a href="{{ route('home') }}">PrintLab</a></li><li><a href="#contacts">{{ __('site.nav_contacts') }}</a></li><li><a href="#benefits">{{ __('site.footer_production') }}</a></li><li><a href="{{ route('catalog.index') }}">{{ __('site.footer_reviews') }}</a></li></ul></div>
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
