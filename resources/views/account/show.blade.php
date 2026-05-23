<x-layouts.public :title="__('auth.order_detail') . ' — PrintLab'" :chrome="false">
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
                <a href="{{ route('account.index') }}" class="pl-header-action">
                    <span aria-hidden="true">👤</span>
                    <span>{{ __('auth.my_account') }}</span>
                </a>
            </div>
        </div>
    </header>

    <nav class="pl-nav">
        <div class="pl-container pl-nav-row">
            <a href="{{ route('catalog.index') }}" class="pl-nav-cat">☰ {{ mb_strtoupper(__('site.nav_catalog')) }}</a>
            <ul>
                <li><a href="{{ route('catalog.index') }}">{{ __('site.catalog_constructor') }} <span class="pl-nav-tag">HIT</span></a></li>
                <li><a href="{{ route('catalog.index', ['category' => 't-shirts']) }}">{{ __('site.nav_catalog') }}</a></li>
                <li><a href="{{ route('catalog.index') }}#prints">{{ __('site.nav_prints') }}</a></li>
                <li><a href="{{ route('home') }}#contacts">{{ __('site.nav_contacts') }}</a></li>
            </ul>
        </div>
    </nav>

    <main class="pl-account-page">
        <section class="pl-container pl-account-detail-shell">
            <a href="{{ route('account.index') }}" class="pl-account-back">&larr; {{ __('auth.back_to_orders') }}</a>

            <article class="pl-account-detail">
                <div class="pl-account-detail__head">
                    <div>
                        <span class="pl-hero-badge">{{ __('auth.order_detail') }}</span>
                        <h1>{{ __('auth.order') }} #{{ $orderRequest->id }}</h1>
                    </div>
                    <span class="pl-account-status">{{ $orderRequest->status->label() }}</span>
                </div>

                <dl class="pl-account-meta">
                    <div>
                        <dt>{{ __('auth.customer_name') }}</dt>
                        <dd>{{ $orderRequest->customer_name }}</dd>
                    </div>
                    <div>
                        <dt>{{ __('auth.customer_phone') }}</dt>
                        <dd>{{ $orderRequest->customer_phone }}</dd>
                    </div>
                    @if ($orderRequest->customer_comment)
                        <div>
                            <dt>{{ __('auth.customer_comment') }}</dt>
                            <dd>{{ $orderRequest->customer_comment }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt>{{ __('auth.created_at') }}</dt>
                        <dd>{{ $orderRequest->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                </dl>

                @if ($orderRequest->items->isNotEmpty())
                    <div class="pl-account-items">
                        <h2>{{ __('auth.order_items') }}</h2>
                        <ul>
                            @foreach ($orderRequest->items as $item)
                                <li>
                                    <span>
                                        <strong>{{ $item->product_name_snapshot }}</strong>
                                        @if ($item->color_snapshot)
                                            <small>{{ $item->color_snapshot }}</small>
                                        @endif
                                    </span>
                                    <em>× {{ $item->quantity }}</em>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </article>
        </section>
    </main>
</x-layouts.public>
