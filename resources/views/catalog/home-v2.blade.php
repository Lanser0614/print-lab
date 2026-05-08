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
            <span class="pl-city">Ташкент</span>
            <span style="width:24px"></span>
            <a href="#benefits">Доставка</a>
            <a href="#benefits">Оплата</a>
            <a href="#benefits">Гарантия</a>
            <a href="#contacts">Помощь</a>
            <a href="#contacts">Партнёрам</a>
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
                <input name="q" placeholder="Найти принт, футболку, мем...">
                <button>Найти</button>
            </form>
            <div class="pl-header-actions">
                <a href="{{ route('catalog.index') }}" class="pl-header-action">
                    <span aria-hidden="true">◎</span>
                    <span>Профиль</span>
                </a>
                <a href="{{ route('catalog.index') }}" class="pl-header-action">
                    <span aria-hidden="true">♡</span>
                    <span>Избранное</span>
                    <span class="pl-badge">7</span>
                </a>
                <a href="{{ route('catalog.index') }}" class="pl-header-action">
                    <span aria-hidden="true">▣</span>
                    <span>Корзина</span>
                    <span class="pl-badge">3</span>
                </a>
            </div>
        </div>
    </header>

    <nav class="pl-nav">
        <div class="pl-container pl-nav-row">
            <a href="{{ route('catalog.index') }}" class="pl-nav-cat">☰ КАТАЛОГ</a>
            <ul>
                <li class="active"><a href="{{ $constructorUrl }}">Конструктор <span class="pl-nav-tag">HIT</span></a></li>
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
        <section class="pl-container">
            <div class="pl-hero">
                <div class="pl-hero-main">
                    <div class="pl-hero-content">
                        <span class="pl-hero-badge">Скидка −40% до конца недели</span>
                        <h1>Создай <em>свою</em><br>футболку за 2 минуты</h1>
                        <p>Любой принт, текст или фото на твоей футболке. Печать в день заказа, доставка по Узбекистану.</p>
                        <a class="pl-hero-cta" href="{{ $constructorUrl }}">Открыть конструктор →</a>
                    </div>
                    <div class="pl-hero-shirt">
                        <img src="/mockups/tshirts/white-front.png" alt="" style="width:100%;height:100%;object-fit:contain">
                    </div>
                </div>
                <div class="pl-hero-side">
                    <a href="{{ $activeProductCategories->has('t-shirts') ? $categoryUrl('t-shirts') : route('catalog.index') }}" class="pl-hero-card red">
                        <div>
                            <h3>Парные<br>футболки</h3>
                            <p>Для двоих от 1290 UZS</p>
                        </div>
                        <div class="pl-hero-card-foot">Смотреть →</div>
                    </a>
                    <a href="{{ $activeProductCategories->has('hoodies') ? $categoryUrl('hoodies') : route('catalog.index') }}" class="pl-hero-card dark">
                        <div>
                            <h3>Худи<br>премиум</h3>
                            <p>Плотная ткань 320 г/м²</p>
                        </div>
                        <div class="pl-hero-card-foot">Каталог →</div>
                    </a>
                </div>
            </div>
        </section>

        <section class="pl-container pl-section">
            <div class="pl-section-head">
                <h2>Каталог <em>товаров</em></h2>
                <a class="pl-link" href="{{ route('catalog.index') }}">Все категории →</a>
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
                        <div class="pl-cat-name">{{ $category->name }}</div>
                        <div class="pl-cat-from">{{ $category->products_count }} товаров</div>
                    </a>
                @empty
                    <div class="rounded-lg border border-dashed border-pl-gray-300 bg-white p-6 text-sm text-pl-gray-500">
                        Категории товаров пока не добавлены.
                    </div>
                @endforelse
            </div>
        </section>

        <section id="products" class="pl-container pl-section">
            <div class="pl-section-head">
                <h2>ТОП <em>продаж</em></h2>
                <a class="pl-link" href="{{ route('catalog.index') }}">Все хиты →</a>
            </div>
            <div class="pl-grid">
                @forelse ($products as $product)
                    @php $variant = $product->variants->first(); @endphp
                    <a class="pl-card" href="{{ route('constructor.show', $product) }}">
                        <div class="pl-card-img">
                            <div class="pl-card-tags">
                                <span class="pl-card-tag new">new</span>
                            </div>
                            <span class="pl-card-fav" aria-label="В избранное">♡</span>
                            @if ($variant)
                                <img src="{{ $variant->mockup_front_url }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:contain" loading="lazy">
                            @else
                                <div class="rounded-lg border border-dashed border-pl-gray-300 bg-white p-6 text-sm text-pl-gray-500">Нет изображения</div>
                            @endif
                        </div>
                        <div class="pl-card-body">
                            <div class="pl-card-name">{{ $product->name }}</div>
                            <div class="pl-card-rating"><span class="pl-stars">★★★★★</span><span>4.9 · 1200+ продано</span></div>
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
                    <div class="rounded-lg border border-dashed border-pl-gray-300 bg-white p-6 text-sm text-pl-gray-500">
                        Товары пока не добавлены.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="pl-container">
            <div class="pl-promo">
                <div class="pl-promo-icon">✦</div>
                <div class="pl-promo-text">
                    <h3>Создай уникальный принт прямо сейчас</h3>
                    <p>Загрузи фото, добавь текст или выбери из готовых дизайнов. Печать от 1 шт.</p>
                </div>
                <a class="pl-promo-cta" href="{{ $constructorUrl }}">В конструктор →</a>
            </div>
        </section>

        <section id="prints" class="pl-container pl-section">
            <div class="pl-section-head">
                <h2>Популярные <em>принты</em></h2>
                <a class="pl-link" href="{{ route('catalog.index') }}#prints">Все принты →</a>
            </div>
            <div style="display:flex;gap:6px;margin-bottom:14px;flex-wrap:wrap">
                <button class="pl-c-tag active">Все</button>
                @foreach ($printCategories as $category)
                    <a class="pl-c-tag" href="{{ $printCategoryUrl($category->slug) }}">{{ $category->name }}</a>
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
                        Готовые принты пока не добавлены.
                    </div>
                @endforelse
            </div>
        </section>

        <section id="benefits" class="pl-container pl-section">
            <div class="grid gap-3 md:grid-cols-4">
                @foreach ([['🚚','Быстрая доставка','По Ташкенту и регионам'], ['🖨','Печать от 1 шт.','Без минимального тиража'], ['🛡','Гарантия качества','Проверяем макет перед печатью'], ['✦','5000+ дизайнов','Мемы, игры, аниме и спорт']] as $benefit)
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
                <div><h4>Каталог</h4><ul>@if ($activeProductCategories->has('t-shirts'))<li><a href="{{ $categoryUrl('t-shirts') }}">Футболки</a></li>@endif @if ($activeProductCategories->has('hoodies'))<li><a href="{{ $categoryUrl('hoodies') }}">Худи</a></li>@endif @if ($activeProductCategories->has('sweatshirts'))<li><a href="{{ $categoryUrl('sweatshirts') }}">Свитшоты</a></li>@endif <li><a href="{{ route('catalog.index') }}#prints">Готовые принты</a></li></ul></div>
                <div><h4>Помощь</h4><ul><li><a href="#benefits">Доставка</a></li><li><a href="#benefits">Оплата</a></li><li><a href="#contacts">Возврат</a></li><li><a href="{{ route('catalog.index') }}">Размеры</a></li></ul></div>
                <div><h4>Партнёрам</h4><ul><li><a href="#contacts">Опт</a></li><li><a href="#contacts">Мерч</a></li><li><a href="#contacts">Дизайнерам</a></li><li><a href="#contacts">Франшиза</a></li></ul></div>
                <div><h4>О нас</h4><ul><li><a href="{{ route('home') }}">PrintLab</a></li><li><a href="#contacts">Контакты</a></li><li><a href="#benefits">Производство</a></li><li><a href="{{ route('catalog.index') }}">Отзывы</a></li></ul></div>
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
