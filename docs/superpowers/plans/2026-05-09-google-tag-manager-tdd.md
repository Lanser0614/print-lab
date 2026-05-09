# Google Tag Manager Integration Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Подключить Google Tag Manager (контейнер `GTM-PQSQLKKL`) ко всем публичным страницам PrintLab без дублирования кода и без изменения поведения конструктора.

**Architecture:** Два маленьких partial-шаблона (`partials/gtm-head.blade.php`, `partials/gtm-body.blade.php`) хранят официальные сниппеты GTM. Они подключаются через `@include` в каждый шаблон, у которого есть собственный `<head>`/`<body>`: основной layout, legacy layout и оба конструктора. ID контейнера выносится в `config/services.php` через переменную окружения `GTM_CONTAINER_ID`, чтобы dev/test/prod могли использовать разные контейнеры или вовсе отключать GTM. Если ID пустой — partials рендерят пустой вывод. Filament-админка не трогается.

**Tech Stack:** Laravel 12, Blade, `config()`, PHPUnit feature-тесты.

**Non-negotiable constraints (см. `docs/redesign-tdd.md` §0 и Приложение A):**
- Не редактируем `OrderRequestController@store`.
- Не меняем payload `POST /order-requests`.
- В `constructor/show.blade.php` допускаются ТОЛЬКО две строки `@include` (одна в `<head>`, одна после `<body>`). Никакой другой логики.

---

### Task 1: Конфигурация и env-переменная

**Files:**
- Modify: `.env.example`
- Modify: `config/services.php`
- Create: `tests/Feature/GoogleTagManagerConfigTest.php`

- [ ] Добавить failing-тест: `config('services.gtm.container_id')` равен значению `GTM_CONTAINER_ID` из env, по умолчанию `null`.
- [ ] Добавить failing-тест: `config('services.gtm.enabled')` возвращает `true`, когда `container_id` непустой, и `false`, когда он пустой/null.
- [ ] Добавить в `.env.example`:
  ```env
  GTM_CONTAINER_ID=
  ```
- [ ] Добавить блок в `config/services.php`:
  ```php
  'gtm' => [
      'container_id' => env('GTM_CONTAINER_ID'),
  ],
  ```
- [ ] Запустить `rtk php artisan test tests/Feature/GoogleTagManagerConfigTest.php`.

### Task 2: Partial-шаблоны GTM

**Files:**
- Create: `resources/views/partials/gtm-head.blade.php`
- Create: `resources/views/partials/gtm-body.blade.php`
- Create: `tests/Feature/GoogleTagManagerPartialsTest.php`

- [ ] Failing-тест: при `config(['services.gtm.container_id' => 'GTM-PQSQLKKL'])` рендер `partials.gtm-head` содержит `https://www.googletagmanager.com/gtm.js?id=GTM-PQSQLKKL` и `dataLayer`.
- [ ] Failing-тест: рендер `partials.gtm-body` содержит `<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PQSQLKKL"` и `display:none;visibility:hidden`.
- [ ] Failing-тест: при `config(['services.gtm.container_id' => null])` оба partial рендерят пустую строку (без `<script>`/`<noscript>`).
- [ ] Failing-тест: PrintLab-ID `GTM-PQSQLKKL` нигде в шаблонах не захардкожен (grep по `resources/views`); ID берётся только из `config()`.
- [ ] Реализовать `gtm-head.blade.php`:
  ```blade
  @php($gtmId = config('services.gtm.container_id'))
  @if ($gtmId)
  {{-- Google Tag Manager --}}
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','{{ $gtmId }}');</script>
  {{-- End Google Tag Manager --}}
  @endif
  ```
- [ ] Реализовать `gtm-body.blade.php`:
  ```blade
  @php($gtmId = config('services.gtm.container_id'))
  @if ($gtmId)
  {{-- Google Tag Manager (noscript) --}}
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  {{-- End Google Tag Manager (noscript) --}}
  @endif
  ```
- [ ] Запустить `rtk php artisan test tests/Feature/GoogleTagManagerPartialsTest.php`.

### Task 3: Подключение к публичному layout

**Files:**
- Modify: `resources/views/components/layouts/public.blade.php`
- Modify: `resources/views/components/layouts/public.legacy.blade.php`
- Create: `tests/Feature/GoogleTagManagerOnPublicPagesTest.php`

- [ ] Failing-тест: при `config(['services.gtm.container_id' => 'GTM-PQSQLKKL'])` GET `/` содержит:
  - `https://www.googletagmanager.com/gtm.js?id=GTM-PQSQLKKL` в HTML;
  - `https://www.googletagmanager.com/ns.html?id=GTM-PQSQLKKL`.
- [ ] Failing-тест: head-сниппет находится **до** любого тега `<link>` и `<meta name="description">` (то есть «как можно выше в `<head>`»). Проверка через `strpos`.
- [ ] Failing-тест: noscript-сниппет идёт **сразу после** `<body ...>` (между ними нет других непустых тегов). Проверка через regex.
- [ ] Failing-тест: при пустом `GTM_CONTAINER_ID` ни один URL `googletagmanager.com` в HTML не появляется.
- [ ] В `public.blade.php` вставить `@include('partials.gtm-head')` первой строкой после `<head>` и `@include('partials.gtm-body')` первой строкой после `<body class="...">`.
- [ ] Повторить то же самое в `public.legacy.blade.php`.
- [ ] Запустить `rtk php artisan test tests/Feature/GoogleTagManagerOnPublicPagesTest.php`.

### Task 4: Подключение к конструкторам v1 и v2

**Files:**
- Modify: `resources/views/constructor/show.blade.php`
- Modify: `resources/views/constructor/v2.blade.php`
- Create: `tests/Feature/GoogleTagManagerOnConstructorTest.php`

- [ ] Failing-тест: GET `/constructor/{slug}` для seed-продукта содержит оба сниппета GTM при заданном container_id.
- [ ] Failing-тест: GET `/constructor/v2/{slug}` (или route с фиче-флагом `CONSTRUCTOR_V2_ENABLED=true`) содержит оба сниппета GTM.
- [ ] Failing-тест: канва конструктора v1 продолжает рендериться (assert наличие `id="canvas"` или эквивалентного якоря, который есть сейчас в шаблоне). Это guard, что вставка GTM ничего не сдвинула.
- [ ] Failing-тест-guard: payload `POST /order-requests` (фабричный вызов с тем же телом, что в `OrderRequestSubmissionTest`) продолжает создавать `OrderRequest` со статусом `new`. GTM не должен влиять на flow заявки.
- [ ] В `constructor/show.blade.php` сделать ровно две правки: `@include('partials.gtm-head')` сразу после открытия `<head>` и `@include('partials.gtm-body')` сразу после открытия `<body ...>`. Никакой другой логики не добавлять.
- [ ] То же самое в `constructor/v2.blade.php`.
- [ ] Запустить `rtk php artisan test tests/Feature/GoogleTagManagerOnConstructorTest.php`.

### Task 5: Проверка, что admin не получает GTM

**Files:**
- Create: `tests/Feature/GoogleTagManagerNotInAdminTest.php`

- [ ] Failing-тест: GET `/admin/login` (или дефолтный Filament login) НЕ содержит `googletagmanager.com` ни в одной форме, даже при заданном `GTM_CONTAINER_ID`.
- [ ] Failing-тест: после авторизации seed-админом GET `/admin` НЕ содержит `googletagmanager.com`.
- [ ] Реализация: ничего не добавлять — Filament использует свой `<x-filament-panels::page>` layout, который мы не трогали; этот тест существует как regression-guard.
- [ ] Запустить `rtk php artisan test tests/Feature/GoogleTagManagerNotInAdminTest.php`.

### Task 6: Финальная проверка

**Files:**
- Все вышеперечисленные.

- [ ] Запустить `rtk php artisan test`.
- [ ] Запустить `rtk npm run build`, если затрагивались Blade и сборка может что-то закешировать.
- [ ] `rtk git status --short` — убедиться, что нет лишних правок (особенно в `OrderRequestController.php`, миграциях и `app/UseCases/OrderRequests/*`).
- [ ] `rtk git diff resources/views/constructor/show.blade.php` — diff состоит ровно из двух добавленных строк `@include`.
- [ ] Убедиться, что `GTM_CONTAINER_ID` появился в `.env.example`, но НЕ закоммичен реальный ID в `.env` локального dev-окружения.
- [ ] Обновить `docs/project-overview.md`: упомянуть, что аналитика подключается через GTM-контейнер, ID хранится в `GTM_CONTAINER_ID`.

---

## Критерии приёмки

- На любой публичной странице (главная, каталог, товар, принт, конструктор v1, конструктор v2) присутствуют оба сниппета GTM, когда `GTM_CONTAINER_ID` задан.
- При пустом `GTM_CONTAINER_ID` сниппетов нет ни на одной странице.
- В `/admin` GTM не подключён.
- ID контейнера не захардкожен в Blade — только через `config('services.gtm.container_id')`.
- `OrderRequestController@store`, миграции `order_requests`/`designs`, и payload `POST /order-requests` не изменены.
- Полный test suite проходит.

## Out of scope (отдельные задачи на потом)

- Push событий в `dataLayer` (`view_item`, `add_to_cart`, `begin_checkout`, `submit_order_request`).
- Consent Mode v2 для GDPR.
- Серверный GTM / Server-Side Tagging.
- Перенос ID в Filament-настройку, чтобы админ мог менять GTM-контейнер без деплоя.
