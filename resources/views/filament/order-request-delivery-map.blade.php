@php
    $lat = $record?->delivery_lat;
    $lng = $record?->delivery_lng;
    $mapId = 'delivery-map-'.($record?->id ?? 'new');
    $apiKey = config('services.yandex_maps.api_key');
@endphp

@if ($lat && $lng && $apiKey)
    <div id="{{ $mapId }}" style="height: 320px; width: 100%; overflow: hidden; border-radius: 10px; border: 1px solid rgba(148, 163, 184, 0.35);"></div>
    <script src="https://api-maps.yandex.ru/2.1/?apikey={{ urlencode($apiKey) }}&lang=ru_RU"></script>
    <script>
        (() => {
            const render = () => {
                const map = new ymaps.Map(@json($mapId), {
                    center: [Number(@json($lat)), Number(@json($lng))],
                    zoom: 15,
                    controls: ['zoomControl'],
                });
                map.geoObjects.add(new ymaps.Placemark([Number(@json($lat)), Number(@json($lng))]));
            };

            if (window.ymaps) ymaps.ready(render);
        })();
    </script>
@elseif ($lat && $lng)
    <div class="text-sm text-gray-500">Карта недоступна: не настроен Yandex Maps API key.</div>
@else
    <div class="text-sm text-gray-500">Координаты доставки не указаны.</div>
@endif
