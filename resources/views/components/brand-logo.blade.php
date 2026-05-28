@props([
    'href' => route('home'),
    'imageClass' => 'pl-logo-image',
    'ariaLabel' => 'PrintLab',
    'src' => '/brand/printlab-logo-header.png',
])

<a href="{{ $href }}" {{ $attributes->class('pl-logo') }} aria-label="{{ $ariaLabel }}">
    <img
        class="{{ $imageClass }}"
        src="{{ $src }}"
        alt="PrintLab"
        width="1350"
        height="416"
    >
</a>
