@php
    $displayName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
    $displayName = $displayName !== '' ? $displayName : $user->name;
    $initial = mb_strtoupper(mb_substr($displayName, 0, 1));
@endphp
<x-layouts.public :title="__('auth.my_account') . ' — PrintLab'" :chrome="false">
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
                    <span aria-hidden="true">◎</span>
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
        <section class="pl-container pl-account-hero">
            <div class="pl-account-profile">
                @if ($user->photo_url)
                    <img src="{{ $user->photo_url }}" alt="" class="pl-account-avatar">
                @else
                    <div class="pl-account-avatar pl-account-avatar--fallback" aria-hidden="true">{{ $initial }}</div>
                @endif
                <div>
                    <span class="pl-hero-badge">{{ __('auth.my_account') }}</span>
                    <h1>{{ $displayName }}</h1>
                    @if ($user->telegram_username)
                        <p>@{{ $user->telegram_username }}</p>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button type="submit" class="pl-account-logout">{{ __('auth.logout') }}</button>
            </form>
        </section>

        <section class="pl-container pl-account-section">
            <div class="pl-section-head">
                <h2>{{ __('auth.my_orders') }}</h2>
                <a href="{{ route('catalog.index') }}" class="pl-link">{{ __('auth.to_catalog') }}</a>
            </div>

            @if ($orderRequests->isEmpty())
                <div class="pl-account-empty">
                    <p>{{ __('auth.no_orders') }}</p>
                    <a href="{{ route('catalog.index') }}">{{ __('auth.to_catalog') }}</a>
                </div>
            @else
                <div class="pl-account-orders">
                    @foreach ($orderRequests as $orderRequest)
                        <a href="{{ route('account.order-requests.show', $orderRequest) }}" class="pl-account-order">
                            <div class="pl-account-order__top">
                                <span class="pl-account-order__number">#{{ $orderRequest->id }}</span>
                                <span class="pl-account-status">{{ $orderRequest->status->label() }}</span>
                            </div>
                            <div class="pl-account-order__body">
                                <strong>{{ $orderRequest->customer_name }}</strong>
                                <span>{{ $orderRequest->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            @if ($orderRequest->items->isNotEmpty())
                                <p>{{ $orderRequest->items->pluck('product_name_snapshot')->implode(', ') }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </main>
</x-layouts.public>
