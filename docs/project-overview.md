# Обзор проекта PrintLab

PrintLab — это Laravel-сайт для продажи товаров с индивидуальной печатью: футболок, кружек и других изделий. Проект объединяет публичный каталог, онлайн-конструктор на Canvas API, поток создания заявок и админ-панель Filament.

Главная бизнес-сущность проекта — **заявка на заказ**, а не сразу оплаченный заказ. Клиент создаёт заявку из конструктора, админ проверяет макет, связывается с клиентом, указывает сумму и переводит заявку дальше по статусам оплаты и производства.

## Основной пользовательский сценарий

1. Клиент открывает сайт.
2. На главной странице видит товары и готовые принты из обработанных заявок.
3. Открывает товар или конструктор.
4. Выбирает вариант товара и сторону печати.
5. Добавляет текст или изображение на canvas.
6. Нажимает `Заказать`.
7. Заполняет имя, телефон и комментарий.
8. Система создаёт заявку со статусом `new`.
9. Админ открывает заявку в Filament.
10. Админ видит preview, print-only файл, оригинальные assets и текстовые слои.
11. Админ меняет статус и ведёт заявку через оплату и производство.

## Публичные модули

### Каталог

Маршруты:

- `/`
- `/catalog`
- `/catalog/t-shirts`
- `/catalog/mugs`

Основные файлы:

- `app/Http/Controllers/CatalogController.php`
- `resources/views/catalog/index.blade.php`

На главной странице сначала показываются товары. Блок `Готовые принты` показывает preview-дизайны из заявок, у которых статус не `new` и не `cancelled`.

### Страница товара

Маршрут:

- `/products/{product:slug}`

Основные файлы:

- `app/Http/Controllers/ProductController.php`
- `resources/views/products/show.blade.php`

Страница нужна, чтобы клиент выбрал вариант товара перед переходом в конструктор.

### Конструктор

Маршрут:

- `/constructor/{product:slug}`

Основные файлы:

- `app/Http/Controllers/ConstructorController.php`
- `resources/views/constructor/show.blade.php`

Конструктор основан на предоставленной Canvas API реализации. Он поддерживает:

- рендер реального mockup-изображения товара;
- выбор варианта товара;
- выбор стороны;
- текстовые слои;
- image-слои;
- фигуры;
- selection на canvas;
- drag и touch interactions;
- export preview;
- export print-only;
- отправку заявки.

Mockup-изображения берутся из `product_variants`:

- `mockup_front_path`
- `mockup_back_path`

Текущие default-файлы лежат здесь:

- `storage/app/public/images/Mug`
- `storage/app/public/images/T-Shirt/White`
- `storage/app/public/images/T-Shirt/Black`

## Модуль заявок

Основные файлы:

- `app/Models/OrderRequest.php`
- `app/Models/OrderRequestItem.php`
- `app/Models/Design.php`
- `app/Models/DesignAsset.php`
- `app/Models/DesignTextLayer.php`
- `app/Http/Controllers/OrderRequestController.php`
- `app/Http/Requests/StoreOrderRequestRequest.php`
- `app/UseCases/OrderRequests/CreateOrderRequestUseCase.php`

Когда клиент отправляет форму из конструктора, backend сохраняет:

- данные клиента;
- snapshot товара и варианта;
- canvas JSON;
- preview image;
- print-only image;
- оригинальные загруженные изображения;
- извлечённые данные текстовых слоёв.

Страница успеха:

- `/order-request/success`
- `resources/views/order-requests/success.blade.php`

## Статусы заявки

Enum:

- `app/Enums/OrderRequestStatus.php`

Статусы:

- `new` — новая заявка;
- `processing` — в обработке;
- `callback_required` — нужен звонок;
- `waiting_payment` — ожидание оплаты;
- `paid` — оплачена;
- `in_production` — в производстве;
- `ready` — готова;
- `completed` — завершена;
- `cancelled` — отменена.

Только статус `processing` блокирует админа от взятия следующей новой заявки.

Логика очереди:

- `app/UseCases/OrderRequests/TakeNextOrderRequestUseCase.php`

## Админ-панель

Для админки используется Filament.

URL:

- `/admin`

Seeded admin:

- `admin@printlab.test`
- `password`

### Раздел заявок

Основные файлы:

- `app/Filament/Resources/OrderRequests/OrderRequestResource.php`
- `app/Filament/Resources/OrderRequests/Tables/OrderRequestsTable.php`
- `app/Filament/Resources/OrderRequests/Schemas/OrderRequestForm.php`
- `app/Filament/Resources/OrderRequests/Pages/ListOrderRequests.php`
- `app/Filament/Resources/OrderRequests/Pages/ViewOrderRequest.php`
- `app/Filament/Resources/OrderRequests/Pages/EditOrderRequest.php`

Админ может:

- смотреть заявки;
- взять следующую заявку;
- менять статус через dropdown;
- указывать сумму;
- добавлять комментарий админа;
- видеть snapshot товара;
- видеть preview image;
- скачать preview;
- скачать print-only image;
- видеть и скачать original uploaded assets;
- смотреть текстовые слои;
- смотреть canvas JSON.

Маршруты скачивания:

- `app/Http/Controllers/Admin/DesignDownloadController.php`
- `/admin/downloads/designs/{design}/preview`
- `/admin/downloads/designs/{design}/print`
- `/admin/downloads/design-assets/{asset}`

### Раздел товаров

Основные файлы:

- `app/Filament/Resources/Products/ProductResource.php`
- `app/Filament/Resources/Products/Pages/ListProducts.php`
- `app/Filament/Resources/Products/Pages/CreateProduct.php`
- `app/Filament/Resources/Products/Pages/ViewProduct.php`
- `app/Filament/Resources/Products/Pages/EditProduct.php`

Админ может создавать и редактировать:

- товары;
- варианты товара;
- mockup front/back;
- зоны печати.

## Модели каталога

Основные модели:

- `Product`
- `ProductVariant`
- `ProductPrintArea`
- `Category`
- `ReadyPrint`

`ReadyPrint` всё ещё существует как простая сущность каталога, но публичный блок `Готовые принты` сейчас показывает дизайны из обработанных заявок.

## База данных

Основные миграции:

- `database/migrations/2026_04_26_000100_create_print_store_catalog_tables.php`
- `database/migrations/2026_04_26_000200_create_order_request_tables.php`

Seed data:

- `database/seeders/DatabaseSeeder.php`

Для разработки проект использует MySQL в Docker. SQLite-файл может существовать локально, но не является основной БД приложения.

## Docker-разработка

Запуск сервисов:

```bash
docker compose up -d --build
```

URL:

- Laravel: `http://localhost:8000`
- Filament: `http://localhost:8000/admin`
- Vite: `http://localhost:5173`

Полезные команды:

```bash
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan db:seed --force
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan test
docker compose exec app ./vendor/bin/pint
docker compose exec app ./vendor/bin/phpstan analyse
```

## Важные storage-пути

Публичные mockups:

- `storage/app/public/images`

Файлы заявок:

- `storage/app/public/order-requests/previews`
- `storage/app/public/order-requests/prints`
- `storage/app/public/order-requests/assets`

Публичный URL Laravel storage:

- `/storage/...`

## Аналитика

Подключение Google Tag Manager идёт через два partial-шаблона:

- `resources/views/partials/gtm-head.blade.php` — `<script>` для `<head>`
- `resources/views/partials/gtm-body.blade.php` — `<noscript>` сразу после `<body>`

Эти партиалы вставлены через `@include` в:

- `resources/views/components/layouts/public.blade.php`
- `resources/views/components/layouts/public.legacy.blade.php`
- `resources/views/constructor/show.blade.php`
- `resources/views/constructor/v2.blade.php`

Filament-админка (`/admin`) GTM не подключает.

ID контейнера читается из переменной окружения `GTM_CONTAINER_ID` через `config('services.gtm.container_id')`. Если переменная пустая — partials рендерят пустую строку, никаких сторонних скриптов на страницах не появляется. Это удобно для local/test окружений.

## Что не входит в текущую версию

В текущей версии не реализованы:

- online payment;
- backend для AI image generation;
- background remover;
- CMYK production pipeline;
- личные кабинеты пользователей;
- marketplace;
- multi-user collaboration.
