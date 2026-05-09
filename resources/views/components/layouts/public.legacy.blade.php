@props([
    'title'       => null,
    'description' => null,
    'keywords'    => null,
    'ogImage'     => null,
])

@php
    $pageTitle       = $title       ?? __('site.seo_home_title');
    $pageDescription = $description ?? __('site.seo_home_description');
    $pageKeywords    = $keywords    ?? __('site.seo_home_keywords');
    $canonical       = url()->current();
    $locale          = app()->getLocale();
    $ogLocale        = $locale === 'uz' ? 'uz_UZ' : 'ru_RU';
    $localizedUrl = function (string $targetLocale): string {
        $segments = request()->segments();

        if (in_array($segments[0] ?? null, ['ru', 'uz'], true)) {
            $segments[0] = $targetLocale;
        } else {
            array_unshift($segments, $targetLocale);
        }

        $url = url(implode('/', $segments));
        $query = request()->getQueryString();

        return $query ? $url . '?' . $query : $url;
    };
@endphp

<!doctype html>
<html lang="{{ $locale }}">
<head>
    @include('partials.gtm-head')

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle }}</title>

    {{-- Basic SEO --}}
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords"    content="{{ $pageKeywords }}">
    <meta name="robots"      content="index, follow">
    <link rel="canonical"    href="{{ $canonical }}">

    {{-- Hreflang --}}
    <link rel="alternate" hreflang="ru"      href="{{ $localizedUrl('ru') }}">
    <link rel="alternate" hreflang="uz"      href="{{ $localizedUrl('uz') }}">
    <link rel="alternate" hreflang="x-default" href="{{ $localizedUrl('ru') }}">

    {{-- Open Graph --}}
    <meta property="og:type"         content="website">
    <meta property="og:site_name"    content="PrintLab">
    <meta property="og:title"        content="{{ $pageTitle }}">
    <meta property="og:description"  content="{{ $pageDescription }}">
    <meta property="og:url"          content="{{ $canonical }}">
    <meta property="og:locale"       content="{{ $ogLocale }}">
    @if ($ogImage)
        <meta property="og:image"        content="{{ $ogImage }}">
        <meta property="og:image:width"  content="1200">
        <meta property="og:image:height" content="630">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    @if ($ogImage)
        <meta name="twitter:image"   content="{{ $ogImage }}">
    @endif

    {{-- Extra per-page SEO (push from individual pages) --}}
    @stack('seo')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-zinc-50 text-zinc-950 antialiased">
@include('partials.gtm-body')

<header class="border-b border-zinc-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4">
        <a href="{{ route('home') }}" class="shrink-0 text-xl font-semibold tracking-tight">PrintLab</a>

        <nav class="flex items-center gap-4 text-sm font-medium text-zinc-700">
            <a href="{{ route('catalog.index') }}"          class="hover:text-zinc-950">{{ __('site.nav_catalog') }}</a>
            <a href="{{ route('catalog.index') }}#prints"   class="hover:text-zinc-950 hidden sm:inline">{{ __('site.nav_prints') }}</a>
            <a href="{{ route('catalog.index') }}#products" class="hover:text-zinc-950 hidden md:inline">{{ __('site.nav_create') }}</a>
            <a href="#contacts"                             class="hover:text-zinc-950 hidden sm:inline">{{ __('site.nav_contacts') }}</a>
        </nav>

        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('account.index') }}" class="text-sm font-medium text-zinc-700 hover:text-zinc-950">{{ __('auth.my_account') }}</a>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-zinc-700 hover:text-zinc-950">{{ __('site.topbar_login') }}</a>
            @endauth
            <x-language-switcher />
        </div>
    </div>
</header>

{{ $slot }}

<footer id="contacts" class="printlab-footer border-t border-zinc-200 bg-white">
    <div class="printlab-footer__content mx-auto flex max-w-7xl items-center gap-2 px-4 py-8 text-sm text-zinc-600">
        <span class="printlab-footer__pulse" aria-hidden="true"></span>
        <span>{{ __('site.footer_text') }} {{ __('site.footer_phone') }}: +998 90 123 45 67</span>
    </div>
</footer>

</body>
</html>
