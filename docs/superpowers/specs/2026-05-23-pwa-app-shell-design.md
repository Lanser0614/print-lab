# Технический дизайн PrintLab PWA App Shell

Дата: 2026-05-23
Статус: черновик для ревью

## Цель

Сделать PrintLab похожим на мобильное приложение для iOS и Android без публикации в App Store или Google Play на этом этапе.

Выбранный подход: Progressive Web App (PWA) с app shell:

- пользователь может добавить PrintLab на главный экран телефона;
- установленное приложение открывается в отдельном standalone-окне, похожем на приложение;
- у PrintLab появляется своя иконка, цвет темы и launch-метаданные;
- ключевые публичные экраны используют mobile-first навигацию и app-like паттерны;
- текущий Laravel-сайт и конструктор остаются основой продукта.

## Что не входит в этот этап

На этом этапе не делаем:

- публикацию в App Store или Google Play;
- сборки на Swift, Kotlin, React Native, Expo или Capacitor;
- push-уведомления;
- полноценные offline-заказы или offline-редактирование в конструкторе;
- переписывание каталога или конструктора в отдельное SPA.

Эти вещи можно добавить позже, если появится понятная бизнес-потребность.

## Целевые платформы

### iOS

Основная цель: iPhone Safari, установка через "Add to Home Screen".

Ожидаемое поведение:

- иконка на главном экране запускает PrintLab;
- `display: standalone` открывает сайт без Safari chrome там, где iOS это поддерживает;
- Apple meta tags задают название приложения, стиль status bar и иконки;
- layout учитывает safe areas через `env(safe-area-inset-*)`.

Важное ограничение iOS:

- установка выполняется пользователем вручную через меню Share в Safari;
- install prompt и часть PWA API на iOS поддерживаются слабее, чем на Android.

### Android

Основная цель: Chrome на Android.

Ожидаемое поведение:

- браузер может показать установку приложения, если manifest и service worker валидны;
- установленная PWA открывается как standalone-приложение;
- иконка, название, theme color и start URL берутся из web manifest.

## Пользовательский опыт

### App Shell

Установленный PrintLab должен ощущаться как компактное commerce-приложение, а не как desktop-сайт в маленьком viewport.

Основной mobile shell:

- sticky bottom navigation для главных действий;
- компактный header с логотипом и доступом к аккаунту/языку;
- поиск как заметное мобильное действие на страницах каталога;
- без desktop topbar в app-like мобильном режиме;
- touch targets минимум 44px по высоте;
- safe-area padding для нижнего индикатора iPhone.

Рекомендуемые bottom tabs:

- Главная;
- Каталог;
- Создать;
- Принты;
- Аккаунт.

Tab "Создать" должен вести в конструктор для featured или выбранного продукта. Если featured product недоступен, нужно использовать существующий constructor fallback route.

### UX установки

Добавить легкий install education component:

- на Android показывать кнопку установки, когда браузер предоставляет `beforeinstallprompt`;
- на iOS показывать короткую инструкцию для Safari только если приложение еще не запущено в standalone-режиме;
- после закрытия скрывать prompt на разумный период через local storage.

Prompt не должен блокировать покупки, работу конструктора или оформление заказа.

### Standalone Mode

При запуске с главного экрана PrintLab должен:

- не зависеть от desktop-browser assumptions;
- показывать bottom navigation на публичных страницах;
- сохранять обычную навигацию по routes;
- оставлять order flow и constructor flow рабочими внутри standalone-окна.

### Конструктор

Конструктор - самый важный app-like экран, поэтому его нужно рассматривать отдельно от публичного commerce shell.

Текущее состояние:

- `resources/views/constructor/v2.blade.php` уже содержит мобильные controls;
- в нем есть mobile bottom bar для layers, properties, AI print, variants, add action и order action;
- он использует drawer-style мобильные panels вместо desktop side panels.

Дизайн-решение:

- не показывать публичную PWA bottom navigation внутри конструктора;
- оставить task-specific bottom bar конструктора главным мобильным control surface;
- убедиться, что installed PWA standalone mode не уменьшает полезную высоту canvas и не прячет bottom actions за iPhone home indicator;
- сохранить текущие constructor routes и server-injected configuration.

Мобильные требования для конструктора:

- canvas должен помещаться в доступный viewport в standalone mode;
- mobile bottom bar должен учитывать safe-area padding;
- add menu и drawer panels должны открываться выше bottom bar;
- order dialog должен помещаться на маленьких экранах без clipping;
- AI print panel должна оставаться доступной из mobile bar;
- загрузка изображения и редактирование текста должны работать с touch input;
- PWA service worker не должен отдавать stale constructor configuration после изменений продукта или варианта.

## Технический дизайн

### Web Manifest

Добавить manifest в `public/manifest.webmanifest`.

Обязательные поля:

- `name`: `PrintLab`;
- `short_name`: `PrintLab`;
- `start_url`: localized home route или `/`;
- `scope`: `/`;
- `display`: `standalone`;
- `background_color`: `#ffffff`;
- `theme_color`: фирменный красный PrintLab;
- `icons`: PNG icons 192x192 и 512x512;
- `shortcuts`: опциональные shortcuts для каталога и конструктора.

Manifest должен быть подключен в head публичного layout.

### iOS Metadata

Добавить iOS-specific metadata в публичный layout:

- `apple-mobile-web-app-capable`;
- `apple-mobile-web-app-title`;
- `apple-mobile-web-app-status-bar-style`;
- `apple-touch-icon`;
- `theme-color`.

Viewport metadata уже есть. Ее нужно сохранить, но CSS mobile shell должен учитывать safe areas.

### Service Worker

Добавить небольшой service worker в `public/sw.js`.

Начальный scope должен быть осторожным:

- кешировать app shell assets, которые генерирует Vite;
- кешировать static icons и стабильные mockup assets;
- использовать network-first для HTML pages;
- не кешировать POST requests, API mutations, order submissions, generated print APIs и auth flows;
- не допускать stale constructor data, которые могут повлиять на точность заказа.

Service worker нужно регистрировать из `resources/js/app.js` только в production-like browser environments, где есть `navigator.serviceWorker`.

### Mobile Shell Component

Создать общий Blade partial для мобильной app-навигации, например:

`resources/views/partials/mobile-app-shell.blade.php`

Его нужно подключить на публичных commerce pages:

- главная;
- каталог;
- product/print detail pages, если они есть;
- account pages, где это уместно.

У конструктора уже есть специализированный mobile bottom bar. Не заменять его публичной bottom navigation. Страницы конструктора должны сохранить свои task-specific controls.

### CSS

Расширить `resources/css/app.css` стилями PWA/mobile shell.

Ключевые требования:

- mobile bottom nav фиксируется к viewport;
- `padding-bottom` у page content предотвращает перекрытие nav;
- safe-area поддержка через `padding-bottom: calc(... + env(safe-area-inset-bottom))`;
- desktop topbar скрывается там, где активен mobile app shell;
- текст внутри buttons и tabs не должен выходить за границы;
- текущий desktop layout должен сохраниться.

### Assets

Добавить сгенерированные или подготовленные app icons:

- `public/icons/icon-192.png`;
- `public/icons/icon-512.png`;
- `public/icons/apple-touch-icon.png`;
- опционально maskable icon для Android.

Иконки должны использовать brand mark PrintLab и оставаться читаемыми в маленьком размере.

## Data Flow

PWA shell не добавляет новые backend data contracts.

Существующие Laravel routes остаются источником истины:

- catalog и product data продолжают рендериться server-side;
- constructor configuration продолжает inject-иться server-side;
- generated print и order request APIs не меняются;
- auth/account routes не меняются.

Service worker только улучшает загрузку безопасных static resources.

## Error Handling

Ошибки service worker не должны ломать сайт.

Ожидаемое поведение:

- если service worker registration fails, логировать только в development и продолжать обычную работу;
- если cached assets недоступны, fallback на network;
- если пользователь offline на HTML page, показывать browser/network failure или простой offline page только если он явно реализован;
- никогда не ставить order submissions в offline queue на этом этапе.

## Тестирование

Автоматические проверки:

- feature test, что public layout содержит manifest и PWA meta tags;
- feature test, что manifest endpoint/file доступен;
- feature test, что service worker file доступен;
- существующие public page tests должны продолжать проходить.

Ручные/browser проверки:

- открыть главную и каталог в iPhone viewport;
- проверить, что bottom navigation не перекрывает content;
- проверить, что mobile controls конструктора работают и не заменены публичной nav;
- проверить constructor standalone viewport: canvas, add menu, drawers, AI panel и order dialog;
- проверить Android installability через Chrome Lighthouse/Application panel;
- проверить iOS home-screen metadata вручную в Safari или через inspection rendered tags.

Build checks:

- `npm run build`;
- релевантные Laravel/PHP tests для public layout, catalog, constructor и GTM.

## Rollout Plan

1. Добавить manifest, icons, iOS metadata и service worker registration.
2. Добавить shared mobile app shell partial и подключить его на публичных commerce pages.
3. Добавить CSS для mobile shell, safe areas и content spacing.
4. Добавить install prompt behavior для Android и iOS education copy.
5. Добавить tests и запустить build/test verification.
6. Вручную проверить mobile viewports и installed-app behavior.

## Открытые решения

Implementation plan должен определить:

- точный источник artwork для иконки;
- точные названия bottom tabs для русской и узбекской локализации;
- должен ли `start_url` быть `/`, `/ru` или current locale-aware route;
- добавлять ли offline fallback page в первой фазе или отложить.
