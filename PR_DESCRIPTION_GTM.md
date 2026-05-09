# feat: подключение Google Tag Manager

## Что делает

Подключает Google Tag Manager (контейнер `GTM-PQSQLKKL`) ко всем публичным страницам PrintLab без изменения поведения конструктора и без правок order-request flow.

ID контейнера читается из `.env` (`GTM_CONTAINER_ID`). Если переменная пустая — на странице нет ни одного запроса к `googletagmanager.com`. Это позволяет dev/test работать без сторонних скриптов.

## Зачем

- Маркетинг сможет управлять пикселями (Google Ads, Meta, Yandex.Metrica и др.) без релизов фронта.
- Аналитика на каждой публичной странице, включая страницу конструктора, чтобы видеть воронку «каталог → товар → конструктор → заявка».
- Админка в `/admin` намеренно остаётся без GTM — внутренний инструмент.

## Что внутри

### Конфигурация

- `.env.example` — добавлен `GTM_CONTAINER_ID=`.
- `config/services.php` — блок `'gtm' => ['container_id' => env('GTM_CONTAINER_ID')]`.

### Источник истины — два partial-шаблона

- `resources/views/partials/gtm-head.blade.php` — `<script>` сниппет, рендерится только если ID непустой.
- `resources/views/partials/gtm-body.blade.php` — `<noscript>` сниппет, тоже под условием.

ID нигде не захардкожен в Blade — берётся только через `config('services.gtm.container_id')`. Тест это проверяет (`test_partials_do_not_hardcode_container_id`).

### Подключение

`@include` вставлены ровно по две строки на файл (одна в `<head>`, одна сразу после `<body>`):

- `resources/views/components/layouts/public.blade.php`
- `resources/views/components/layouts/public.legacy.blade.php`
- `resources/views/constructor/show.blade.php` (v1)
- `resources/views/constructor/v2.blade.php`

Никакой другой логики в эти файлы не добавлено. В конструкторе v1 diff по GTM — это две строки `+@include(...)`.

## Что специально НЕ задеваем

В соответствии с приложением A из `docs/redesign-tdd.md`:

- `OrderRequestController@store` — не тронут.
- Контракт `POST /order-requests` (`canvas_json`, `preview_image`, `print_image`, `assets[]`) — не тронут.
- Миграции `order_requests` / `designs` — не тронуты.
- Логика конструктора (`canvas`, слои, undo/redo, drag, экспорт PNG) — не тронута.
- Filament-админка (`/admin`) — GTM туда не подключается. На это есть guard-тест.

## Тесты (TDD-first)

Сначала писались failing-тесты, потом реализация. Все 5 тестовых файлов:

| Файл | Что проверяет |
|---|---|
| `tests/Feature/GoogleTagManagerConfigTest.php` | env-переменная, ключ в `config/services.php`, дефолт `null` |
| `tests/Feature/GoogleTagManagerPartialsTest.php` | partials рендерят сниппеты при ID, и пустую строку при пустом ID; отсутствие хардкода |
| `tests/Feature/GoogleTagManagerOnPublicPagesTest.php` | GTM на `/ru`, head-сниппет выше `<meta description>` (= «as high as possible»), noscript сразу после `<body>` |
| `tests/Feature/GoogleTagManagerOnConstructorTest.php` | GTM в конструкторе v1 и v2 + guard-тест: `POST /order-requests` продолжает создавать `OrderRequest` со статусом `new` при включённом GTM |
| `tests/Feature/GoogleTagManagerNotInAdminTest.php` | `/admin/login` и `/admin` не содержат `googletagmanager.com` даже при заданном ID |

Запуск:

```bash
docker compose exec app php artisan test --filter=GoogleTagManager
```

Полный регресс:

```bash
docker compose exec app php artisan test
```

## Документация

- Создан `docs/superpowers/plans/2026-05-09-google-tag-manager-tdd.md` — TDD-план с 6 задачами и критериями приёмки.
- В `docs/project-overview.md` добавлен раздел «Аналитика» с описанием partials, layouts и переменной окружения.

## Деплой

После merge:

1. На production-сервере добавить в `.env`:
   ```env
   GTM_CONTAINER_ID=GTM-PQSQLKKL
   ```
2. Перезапустить контейнер `app` или выполнить:
   ```bash
   docker compose --env-file .env --env-file .deploy.env -f docker-compose.prod.yml exec app php artisan optimize:clear
   ```
3. Открыть https://printlab.uz, убедиться в DevTools → Network, что есть запрос `gtm.js?id=GTM-PQSQLKKL`.
4. В GTM Preview Mode проверить, что контейнер видит:
   - главную;
   - страницу товара;
   - страницу конструктора v1;
   - страницу конструктора v2 (если `CONSTRUCTOR_V2_ENABLED=true`).

## Чек-лист ревью

- [ ] `GTM_CONTAINER_ID` появился в `.env.example`, в коммитах нет реального значения.
- [ ] `partials/gtm-head.blade.php` и `partials/gtm-body.blade.php` корректно рендерят пустую строку при пустом ID.
- [ ] Diff в `resources/views/constructor/show.blade.php` состоит только из двух строк `@include`.
- [ ] `OrderRequestController@store` не изменён (проверить `git diff app/Http/Controllers/OrderRequestController.php`).
- [ ] `php artisan test` проходит полностью.
- [ ] Filament-страницы в `/admin` не содержат `googletagmanager.com`.

## Out of scope (отдельные задачи)

- Push событий в `dataLayer` (`view_item`, `add_to_cart`, `begin_checkout`, `submit_order_request`).
- Consent Mode v2 для GDPR.
- Server-Side Tagging.
- Управление GTM-ID из Filament без правки `.env`.
