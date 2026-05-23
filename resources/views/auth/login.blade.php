@php
    $locale = app()->getLocale();
@endphp
<x-layouts.public :title="__('auth.login_title') . ' — PrintLab'" :chrome="false">
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
                <a href="{{ route('login') }}" class="pl-header-action">
                    <span aria-hidden="true">👤</span>
                    <span>{{ __('site.topbar_login') }}</span>
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

    <main class="pl-login-page">
        <section class="pl-container pl-login-shell">
            <div class="pl-login-copy">
                <span class="pl-hero-badge">{{ __('site.topbar_login') }}</span>
                <h1>{{ __('auth.login_title') }}</h1>
                <p>{{ __('auth.login_subtitle') }}</p>
            </div>

            <div class="pl-login-card">
                <div class="pl-login-icon" aria-hidden="true">P</div>
                <h2>{{ __('auth.login_title') }}</h2>
                <p>{{ __('auth.login_subtitle') }}</p>

            <a id="telegram-login-btn"
               href="#"
               class="pl-telegram-button">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                </svg>
                {{ __('auth.open_telegram') }}
            </a>

                <p class="pl-login-help">{{ __('auth.login_help') }}</p>

                <p id="login-status" class="pl-login-status hidden"></p>
            </div>
        </section>
    </main>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const btn = document.getElementById('telegram-login-btn');
                const status = document.getElementById('login-status');
                let pollInterval = null;

                fetch('{{ route('auth.telegram.start') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        btn.href = data.deep_link;
                        btn.addEventListener('click', function (e) {
                            if (pollInterval) return;
                            e.preventDefault();
                            window.open(data.deep_link, '_blank');
                            status.classList.remove('hidden');
                            status.textContent = '{{ __('auth.login_polling') }}';
                            startPolling(data.token);
                        });
                    });

                function startPolling(token) {
                    let attempts = 0;
                    const maxAttempts = 80;

                    pollInterval = setInterval(function () {
                        attempts++;
                        fetch('{{ url('/') }}/' + '{{ $locale }}' + '/auth/telegram/poll/' + token)
                            .then(r => {
                                if (r.status === 410) {
                                    clearInterval(pollInterval);
                                    status.textContent = '{{ __('auth.login_expired') }}';
                                    return;
                                }
                                return r.json();
                            })
                            .then(data => {
                                if (data && data.status === 'confirmed') {
                                    clearInterval(pollInterval);
                                    status.textContent = '{{ __('auth.login_success') }}';
                                    window.location.href = data.redirect_to;
                                }
                            });

                        if (attempts >= maxAttempts) {
                            clearInterval(pollInterval);
                            status.textContent = '{{ __('auth.login_expired') }}';
                        }
                    }, 1500);
                }
            });
        </script>
    @endpush
</x-layouts.public>
