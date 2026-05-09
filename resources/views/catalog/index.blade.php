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
            <span class="pl-city">Ташкент</span>
            <span style="width:24px"></span>
            <a href="{{ route('home') }}#benefits">Доставка</a>
            <a href="{{ route('home') }}#benefits">Оплата</a>
            <a href="{{ route('home') }}#benefits">Гарантия</a>
            <a href="{{ route('home') }}#contacts">Помощь</a>
            <a href="{{ route('home') }}#contacts">Партнёрам</a>
            <span class="pl-spacer"></span>
            <span class="pl-phone">+998 90 123 45 67</span>
            <a href="{{ route('catalog.index') }}">Войти</a>
        </div>
    </div>

    <header class="pl-header">
        <div class="pl-container pl-header-row">
            <a href="{{ route('home') }}" class="pl-logo">
                <span class="pl-logo-mark">P</span>
                <span class="pl-logo-text">Print<span>Lab</span></span>
            </a>
            <form class="pl-search" action="{{ route('catalog.index') }}">
                <input name="q" placeholder="Найти принт, футболку, мем..." value="{{ request('q') }}">
                <button>Найти</button>
            </form>
            <div class="pl-header-actions">
                <x-language-switcher class="pl-language-switcher--header" />
                <a href="{{ route('catalog.index') }}" class="pl-header-action"><span aria-hidden="true">◎</span><span>Профиль</span></a>
                <a href="{{ route('catalog.index') }}" class="pl-header-action"><span aria-hidden="true">♡</span><span>Избранное</span></a>
                <a href="{{ route('catalog.index') }}" class="pl-header-action"><span aria-hidden="true">▣</span><span>Корзина</span></a>
            </div>
        </div>
    </header>

    <nav class="pl-nav">
        <div class="pl-container pl-nav-row">
            <a href="{{ route('catalog.index') }}" class="pl-nav-cat">☰ КАТАЛОГ</a>
            <ul>
                <li><a href="{{ $constructorUrl }}">Конструктор <span class="pl-nav-tag">HIT</span></a></li>
                @if ($activeProductCategories->has('t-shirts'))<li><a href="{{ $categoryUrl('t-shirts') }}">Футболки</a></li>@endif
                @if ($activeProductCategories->has('hoodies'))<li><a href="{{ $categoryUrl('hoodies') }}">Худи</a></li>@endif
                @if ($activeProductCategories->has('sweatshirts'))<li><a href="{{ $categoryUrl('sweatshirts') }}">Свитшоты</a></li>@endif
                @if ($activeProductCategories->has('longsleeves'))<li><a href="{{ $categoryUrl('longsleeves') }}">Лонгсливы</a></li>@endif
                @if ($activePrintCategories->has('memes'))<li><a href="{{ $printCategoryUrl('memes') }}">Мемы <span class="pl-nav-tag">NEW</span></a></li>@endif
                @if ($activeProductCategories->has('custom-products'))<li><a href="{{ $categoryUrl('custom-products') }}">Парные</a></li>@endif
                @if ($activeProductCategories->has('kids'))<li><a href="{{ $categoryUrl('kids') }}">Детям</a></li>@endif
                <li><a href="{{ route('catalog.index') }}">Распродажа</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <section id="products" class="pl-container pl-section">
            <div class="pl-section-head">
                <h2>Каталог <em>товаров</em></h2>
                <a class="pl-link" href="{{ route('catalog.index') }}">Все товары →</a>
            </div>

            @if ($productCategories->isNotEmpty())
                <div style="display:flex;gap:6px;margin-bottom:18px;flex-wrap:wrap">
                    <a class="pl-c-tag {{ empty($selectedProductCategory) ? 'active' : '' }}" href="{{ route('catalog.index') }}">Все</a>
                    @foreach ($productCategories as $category)
                        <a class="pl-c-tag {{ ($selectedProductCategory ?? null) === $category->slug ? 'active' : '' }}"
                           href="{{ $categoryUrl($category->slug) }}">
                            {{ $category->name }} <span>{{ $category->products_count }}</span>
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
                            <span class="pl-card-fav" aria-label="В избранное">♡</span>
                            @if ($variant)
                                <img src="{{ $variant->mockup_front_url }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:contain" loading="lazy">
                            @else
                                <div class="pl-empty-card">Нет изображения</div>
                            @endif
                        </div>
                        <div class="pl-card-body">
                            <div class="pl-card-name">{{ $product->name }}</div>
                            <div class="pl-card-rating"><span class="pl-stars">★★★★★</span><span>{{ $product->type->label() }}</span></div>
                            <div class="pl-card-price-row">
                                <span class="pl-card-price">{{ number_format($product->base_price, 0, '.', ' ') }} UZS</span>
                            </div>
                            <div class="pl-card-colors">
                                <span class="pl-card-color" style="background:#ffffff"></span>
                                <span class="pl-card-color" style="background:#1a1a1a"></span>
                                <span class="pl-card-color" style="background:#1f3a8a"></span>
                                <span class="pl-card-color" style="background:#e9789e"></span>
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
                <h2>Готовые <em>принты</em></h2>
                <a class="pl-link" href="{{ route('catalog.index') }}#prints">Все принты →</a>
            </div>

            @if ($printCategories->isNotEmpty())
                <div style="display:flex;gap:6px;margin-bottom:18px;flex-wrap:wrap">
                    <a class="pl-c-tag {{ empty($selectedPrintCategory) ? 'active' : '' }}" href="{{ route('catalog.index') }}#prints">Все</a>
                    @foreach ($printCategories as $category)
                        <a class="pl-c-tag {{ ($selectedPrintCategory ?? null) === $category->slug ? 'active' : '' }}"
                           href="{{ $printCategoryUrl($category->slug) }}">
                            {{ $category->name }} <span>{{ $category->ready_prints_count }}</span>
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
                <div><h4>Каталог</h4><ul>@if ($activeProductCategories->has('t-shirts'))<li><a href="{{ $categoryUrl('t-shirts') }}">Футболки</a></li>@endif @if ($activeProductCategories->has('hoodies'))<li><a href="{{ $categoryUrl('hoodies') }}">Худи</a></li>@endif @if ($activeProductCategories->has('sweatshirts'))<li><a href="{{ $categoryUrl('sweatshirts') }}">Свитшоты</a></li>@endif <li><a href="{{ route('catalog.index') }}#prints">Готовые принты</a></li></ul></div>
                <div><h4>Помощь</h4><ul><li><a href="{{ route('home') }}#benefits">Доставка</a></li><li><a href="{{ route('home') }}#benefits">Оплата</a></li><li><a href="{{ route('home') }}#contacts">Возврат</a></li><li><a href="{{ route('catalog.index') }}">Размеры</a></li></ul></div>
                <div><h4>Партнёрам</h4><ul><li><a href="{{ route('home') }}#contacts">Опт</a></li><li><a href="{{ route('home') }}#contacts">Мерч</a></li><li><a href="{{ route('home') }}#contacts">Дизайнерам</a></li><li><a href="{{ route('home') }}#contacts">Франшиза</a></li></ul></div>
                <div><h4>О нас</h4><ul><li><a href="{{ route('home') }}">PrintLab</a></li><li><a href="{{ route('home') }}#contacts">Контакты</a></li><li><a href="{{ route('home') }}#benefits">Производство</a></li><li><a href="{{ route('catalog.index') }}">Отзывы</a></li></ul></div>
                <div>
                    <h4>Подписка</h4>
                    <p style="margin:0 0 12px;color:#8a8a8a">Скидки, новые принты и лимитированные дропы.</p>
                    <form class="pl-search" style="height:40px">
                        <input type="email" placeholder="email@example.com">
                        <button>OK</button>
                    </form>
                </div>
            </div>
            <div class="pl-footer-bottom">
                <span>© 2026 PrintLab</span>
                <span>Футболки, кружки и индивидуальная печать</span>
            </div>
        </div>
    </footer>
</x-layouts.public>
