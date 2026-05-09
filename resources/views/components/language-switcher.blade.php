@props([
    'class' => '',
])

@php
    $locale = app()->getLocale();
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

<div {{ $attributes->class(['pl-language-switcher', $class]) }}>
    <a href="{{ $localizedUrl('ru') }}" class="{{ $locale === 'ru' ? 'is-active' : '' }}">RU</a>
    <a href="{{ $localizedUrl('uz') }}" class="{{ $locale === 'uz' ? 'is-active' : '' }}">UZ</a>
</div>
