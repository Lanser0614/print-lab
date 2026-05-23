@php
    $featuredProduct = $featuredProduct ?? ($products ?? collect())->first();
    $createUrl = $featuredProduct
        ? route('constructor.show', $featuredProduct)
        : route('constructor.fallback');

    $tabs = [
        ['label' => __('site.pwa_tab_home'), 'icon' => '⌂', 'href' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => __('site.pwa_tab_catalog'), 'icon' => '▦', 'href' => route('catalog.index'), 'active' => request()->routeIs('catalog.*') || request()->routeIs('products.*')],
        ['label' => __('site.pwa_tab_create'), 'icon' => '+', 'href' => $createUrl, 'active' => request()->routeIs('constructor.*')],
        ['label' => __('site.pwa_tab_prints'), 'icon' => '◆', 'href' => route('catalog.index') . '#prints', 'active' => request()->routeIs('prints.*')],
        ['label' => __('site.pwa_tab_account'), 'icon' => '👤', 'href' => auth()->check() ? route('account.index') : route('login'), 'active' => request()->routeIs('account.*') || request()->routeIs('login')],
    ];
@endphp

<nav class="pl-mobile-app-shell" aria-label="{{ __('site.pwa_nav_label') }}">
    @foreach ($tabs as $tab)
        <a href="{{ $tab['href'] }}" class="pl-mobile-app-shell__item {{ $tab['active'] ? 'is-active' : '' }}">
            <span class="pl-mobile-app-shell__icon" aria-hidden="true">{{ $tab['icon'] }}</span>
            <span class="pl-mobile-app-shell__label">{{ $tab['label'] }}</span>
        </a>
    @endforeach
</nav>
