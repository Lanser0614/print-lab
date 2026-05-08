# PrintLab — Technical Design Document: миграция на marketplace-дизайн

**Версия:** 1.0
**Дата:** 2026-05-07
**Автор:** инженерная команда
**Статус:** draft, на согласование

---

## 0. Главное правило (non-negotiable)

> **Конструктор НЕ ЛОМАЕМ. Новый конструктор = редизайн, а не переписывание поведения.**

Существующий конструктор (`resources/views/constructor/show.blade.php`, 1818 строк, Canvas API + слои + экспорт PNG + сабмит заявки) — это **рабочий критический путь** заявок. Любое изменение конструктора:

1. Делается в отдельной ветке `feature/constructor-v2`, **параллельно** с основным редизайном.
2. Старый конструктор остаётся доступен по фиче-флагу `CONSTRUCTOR_V2_ENABLED=false` до полного покрытия regression-тестами.
3. Перед мерджем — обязательный e2e-тест полного цикла: открыть конструктор → добавить слой (image/text/shape) → undo/redo → переключить вариант → сабмит заявки → проверка `OrderRequest` в БД (canvas_json.layers, preview_image, print_image, assets).
4. Контракт payload'а к `POST /order-requests` (`canvas_json`, `preview_image`, `print_image`, `assets[]`) **не меняется**. Если новый UI генерирует данные иначе — он обязан собирать совместимый payload.
5. Новый конструктор обязан иметь **полный функциональный паритет** с текущим: все текущие действия, состояния, ограничения, экспорт, слои, варианты, submit-flow и данные заявки должны работать аналогично. Любое отличие в поведении считается регрессией, пока оно отдельно не согласовано.
6. Разрешённая область изменений для конструктора v2 — **визуальный редизайн UI/UX и аккуратная внутренняя декомпозиция**, если она не меняет пользовательское поведение и контракт данных.
7. Все остальные страницы (главная, каталог, товар, принт) можно рефакторить **до** конструктора — они не блокируют работу заявок.

Этот пункт перекрывает любые другие требования из этого документа.

---

## 1. Цели и не-цели

### 1.1 Цели

- Привести публичные страницы (главная, каталог, товар, принт) к hi-fi референсу из `design_handoff_printlab/` (vsemayki-style маркетплейс с красно-жёлтой палитрой).
- Внедрить дизайн-систему как переиспользуемые Blade-компоненты + дизайн-токены в Tailwind theme.
- Адаптировать всё под мобильную версию с bottom tabbar и красной FAB-кнопкой.
- Постепенно (вторая волна) перевести конструктор на новую визуальную систему **с полным сохранением текущего функционала и поведения**.
- Добавить недостающий функционал маркетплейса: корзина, избранное, категории, поиск, фильтрация принтов, email-подписка.

### 1.2 Не-цели

- Полная замена стека (остаёмся на Laravel + Blade + Tailwind, **не** переходим на Next.js / Inertia / Livewire-SPA).
- Замена Filament-админки.
- Переписывание схемы БД (Product/Variant/Design/OrderRequest) — расширяем, не ломаем.
- Внедрение реальной оплаты (бизнес-сущность остаётся «заявка», не «заказ»).

### 1.3 Out of scope для v1 редизайна

- Streetwear-вариант главной (`HomeDark`) — добавляется во вторую очередь, после согласования основной темы.
- Kanva.js/Fabric.js для конструктора — текущий Canvas API и текущая модель поведения остаются источником истины.
- Реальная отправка email при подписке — пока сохранение в таблицу `subscribers`.

---

## 2. Стек и архитектурные принципы

### 2.1 Что остаётся

| Слой | Текущее | Решение |
|---|---|---|
| Backend | Laravel 12 | Остаётся |
| Admin | Filament 5 | Остаётся, без изменений в v1 |
| Frontend | Blade + Tailwind 4 + Vite | Остаётся, **без SPA** |
| Конструктор | Canvas API, vanilla JS, 1818 строк inline | Остаётся как fallback; v2 — постепенный рефакторинг |
| Локализация | `lang/ru/site.php`, `lang/uz/site.php` | Остаётся, новые ключи добавляются |

### 2.2 Что добавляется

- Шрифт **Manrope** (Google Fonts) как основной интерфейсный.
- CSS-переменные дизайн-токенов в `resources/css/app.css` через `@theme`.
- Папка `resources/views/components/` с атомами и молекулами.
- `resources/css/components/` — модульные стили там, где Tailwind недостаточен (карточки товаров с hover-анимациями, сложные SVG-мокапы).

### 2.3 Принцип «прогрессивная замена»

1. **Никогда не редактируем существующий шаблон напрямую** (за исключением правок багов). Мы создаём новый шаблон рядом и переключаем роут только когда новый покрыт.
2. Старые шаблоны переименовываются в `*.legacy.blade.php` после переключения и остаются в репо ещё минимум один релиз.
3. Конструктор живёт в отдельной ветке до полной готовности (см. §0).

---

## 3. Дизайн-токены

Источник истины: `design_handoff_printlab/README.md` + `source/styles.css`.

### 3.1 Файл токенов

Создать `resources/css/tokens.css` и импортировать в `app.css`:

```css
@theme {
  --color-pl-red: #e30613;
  --color-pl-red-dark: #c00510;
  --color-pl-red-light: #ff2233;
  --color-pl-yellow: #ffd400;
  --color-pl-black: #111111;
  --color-pl-gray-900: #1a1a1a;
  --color-pl-gray-700: #4a4a4a;
  --color-pl-gray-500: #8a8a8a;
  --color-pl-gray-300: #d0d0d0;
  --color-pl-gray-200: #e5e5e5;
  --color-pl-gray-100: #f4f4f4;
  --color-pl-gray-50:  #fafafa;
  --color-pl-green: #1ea54a;
  --color-pl-orange: #ff7a00;

  --font-sans: 'Manrope', ui-sans-serif, system-ui, sans-serif;

  --radius-pl-btn: 4px;
  --radius-pl-card: 8px;
  --radius-pl-pill: 999px;

  --shadow-pl-card-hover: 0 6px 24px rgba(0, 0, 0, 0.10);
  --shadow-pl-promo: 0 12px 24px rgba(0, 0, 0, 0.12);
  --shadow-pl-fab: 0 6px 16px rgba(227, 6, 19, 0.4);
}
```

### 3.2 Цвета изделий (T-shirt palette)

Хранится как enum/таблица `shirt_colors` (см. §5.2):

```
white #ffffff, black #1a1a1a, gray #9ca3a3, red #c91e1e, blue #1f3a8a,
navy #16213e, green #1e6b3a, yellow #f3c613, pink #e9789e, lilac #6e3aa7,
orange #e8732a, beige #d6c5a8
```

### 3.3 Шрифт

Подключить через `<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800;900&display=swap">` в layout. Текущий шрифт (Instrument Sans / DM Sans) **остаётся в конструкторе** до его v2 — конфликта не будет, т.к. конструктор имеет свой `<style>` блок.

---

## 4. Дерево компонентов

### 4.1 Атомы (`resources/views/components/atoms/`)

| Компонент | Файл | Параметры |
|---|---|---|
| `<x-atoms.button>` | `button.blade.php` | `variant=primary\|secondary\|outline\|dark`, `size=sm\|md\|lg`, `as=button\|a` |
| `<x-atoms.input>` | `input.blade.php` | `type`, `name`, `placeholder`, `error` |
| `<x-atoms.badge>` | `badge.blade.php` | `variant=hit\|new\|sale\|hot`, slot |
| `<x-atoms.icon-button>` | `icon-button.blade.php` | `icon`, `count` (для badge-счётчика), `aria-label` |
| `<x-atoms.rating>` | `rating.blade.php` | `value` (0–5), `count` |
| `<x-atoms.color-dot>` | `color-dot.blade.php` | `color`, `selected`, `size` |

### 4.2 Молекулы (`resources/views/components/molecules/`)

| Компонент | Назначение |
|---|---|
| `<x-molecules.product-card>` | Карточка товара: SVG-футболка → теги → название → рейтинг → цена → точки цветов |
| `<x-molecules.category-card>` | SVG-мокап + название категории, hover-state |
| `<x-molecules.print-tile>` | Миниатюра принта в сетке популярных |
| `<x-molecules.benefit>` | Иконка + заголовок + подпись (доставка/гарантия/...) |
| `<x-molecules.search-bar>` | Поиск с красной обводкой 2px и красной кнопкой «Найти» |
| `<x-molecules.tag-filter>` | Горизонтальная плашка фильтр-тегов (Все/Мемы/Поп-культура/...) |

### 4.3 Организмы (`resources/views/components/organisms/`)

| Компонент | Файл | Используется на |
|---|---|---|
| `<x-organisms.top-bar>` | 32px чёрный, город+ссылки+телефон+«Войти» | везде |
| `<x-organisms.header>` | логотип + поиск + cart/wishlist/user | везде кроме конструктора v1 |
| `<x-organisms.nav>` | красная плашка КАТАЛОГ + категории | главная, каталог |
| `<x-organisms.hero>` | жёлтый баннер + 2 сайдбара | главная |
| `<x-organisms.footer>` | 5 колонок + email-подписка | везде |
| `<x-organisms.mobile-tabbar>` | bottom tabbar 64px с FAB | mobile-views |

### 4.4 Layouts (`resources/views/components/layouts/`)

- `public.blade.php` — **полная переработка** (TopBar + Header + Nav + slot + Footer + Mobile tabbar)
- `public.legacy.blade.php` — текущий layout, остаётся как fallback для конструктора v1 до его миграции
- `constructor.blade.php` — **новый**, минимальный, без TopBar/Footer (под v2)

---

## 5. Backend: новые сущности

### 5.1 Категории

```
Migration: 2026_05_XX_create_categories_table
- id, slug (unique), name_ru, name_uz, icon_svg (text), sort_order, is_active
- Pivot: category_product (category_id, product_id)
```

Модели: `app/Models/Category.php`, добавить `categories()` belongsToMany в `Product`.

Filament resource: `app/Filament/Resources/CategoryResource.php`.

### 5.2 Корзина и избранное

Гость = session-based, авторизованный = БД.

```
Migration: 2026_05_XX_create_carts_and_wishlists
- carts:        id, user_id (nullable), session_id (nullable), created_at, updated_at
- cart_items:   id, cart_id, product_id, variant_id, quantity, design_id (nullable, для конструктора), price_snapshot
- wishlists:    id, user_id (nullable), session_id (nullable)
- wishlist_items: id, wishlist_id, product_id, variant_id
```

Сервисы: `app/Services/CartService.php`, `app/Services/WishlistService.php` (resolve по user_id или session_id).

### 5.3 Подписка на email

```
Migration: 2026_05_XX_create_subscribers_table
- id, email (unique), locale, subscribed_at, unsubscribed_at (nullable), source
```

### 5.4 Расширение Product

Добавить колонки:
- `old_price` (nullable decimal) — для зачёркнутой цены в карточках
- `is_hot` boolean default false
- `is_new` boolean default false (или вычислять по `created_at < 30 days`)
- `rating_avg` decimal(2,1) nullable
- `rating_count` int default 0
- `available_colors` json nullable (массив hex для точек на карточке)

Все новые поля — **nullable / default**, существующие записи не ломаются.

### 5.5 Расширение конструктора (под v2)

В payload `canvas_json` добавить (опционально, без поломок старого):
- `product_type: 'tshirt' | 'hoodie' | 'longsleeve'`
- `view: 'front' | 'back'`
- `shirt_color_id: string`
- `size: 'XS'|'S'|'M'|'L'|'XL'|'XXL'`
- `qty: int`

Старые поля `layers[]`, `print_area` остаются. Backend парсит обе версии (наличие `product_type` → v2, иначе → v1).

---

## 6. Маршруты

### 6.1 Существующие (без изменений URL)

```
GET  /                       CatalogController@home
GET  /catalog                CatalogController@index
GET  /catalog/t-shirts       CatalogController@index
GET  /catalog/mugs           CatalogController@index
GET  /products/{slug}        ProductController@show
GET  /prints/{design}        PrintController@show
GET  /constructor/{slug}     ConstructorController@show     ← НЕ ТРОГАТЬ
POST /order-requests         OrderRequestController@store   ← НЕ ТРОГАТЬ
GET  /order-request/success  OrderRequestController@success
GET  /language/{locale}      языковой переключатель
```

### 6.2 Новые

```
GET  /catalog/{category:slug}      CatalogController@byCategory
GET  /search                       CatalogController@search   (?q=...)

POST /cart/items                   CartController@add
PATCH /cart/items/{item}           CartController@update
DELETE /cart/items/{item}          CartController@remove
GET  /cart                         CartController@show

POST /wishlist/items               WishlistController@toggle
GET  /wishlist                     WishlistController@show

POST /subscribe                    SubscriberController@store

# v2 конструктор (за фиче-флагом)
GET  /constructor/v2/{slug}        ConstructorV2Controller@show
```

`/constructor/{slug}` остаётся **прежним** до завершения v2.

---

## 7. План работ по фазам

Каждая фаза завершается мерджем в `main` без поломки prod. Конструктор v1 во всех фазах **работает без изменений**.

### Фаза 0 — подготовка (1 день)

- [ ] Создать ветку `feature/redesign-foundation`
- [ ] Завести фиче-флаг `REDESIGN_V2_ENABLED` в `.env.example`
- [ ] Подключить шрифт Manrope
- [ ] Прописать дизайн-токены в `resources/css/tokens.css`
- [ ] Скопировать текущий `public.blade.php` → `public.legacy.blade.php`
- [ ] Конструктор продолжает использовать `public.legacy.blade.php` (он его и так не использует, но проверить)

**Acceptance:** prod выглядит идентично, новых страниц нет.

### Фаза 1 — атомы и молекулы (3 дня)

- [ ] Реализовать все компоненты из §4.1 и §4.2
- [ ] Завести storybook-аналог: страница `/dev/components` (только в `APP_ENV=local`) для визуальной проверки
- [ ] Покрыть атомы PHPUnit/Pest snapshot-тестами (минимум: рендерится без ошибок при дефолтных пропсах)

**Acceptance:** все атомы и молекулы рендерятся, выглядят как референс на `/dev/components`.

### Фаза 2 — layout и главная (4 дня)

- [ ] Новый `public.blade.php` (TopBar + Header + Nav + Footer + mobile tabbar)
- [ ] Реализовать организмы из §4.3
- [ ] Новая `welcome.blade.php` или `home.blade.php` (Hero, категории, топ продаж, promo strip, популярные принты, benefits)
- [ ] `CatalogController@home` отдаёт новую главную, **только** если `REDESIGN_V2_ENABLED=true`, иначе — старую
- [ ] Локализация всех новых строк (ru + uz)

**Acceptance:** включаем флаг → главная как в дизайне; выключаем → всё по-старому. Конструктор работает в обоих режимах.

### Фаза 3 — каталог, страница товара, страница принта (3 дня)

- [ ] `catalog/index.blade.php` v2 за тем же флагом
- [ ] `products/show.blade.php` v2
- [ ] `prints/show.blade.php` v2
- [ ] Все ссылки на `/constructor/{slug}` сохраняются и ведут на **существующий** конструктор v1

**Acceptance:** обход всех страниц по флагу выглядит как референс. Кнопка «В конструктор» открывает рабочий v1-конструктор.

### Фаза 4 — backend для новых фич (4 дня)

- [ ] Миграции и модели для `categories`, `carts`, `wishlists`, `subscribers`
- [ ] CartService, WishlistService
- [ ] Контроллеры и роуты (§6.2)
- [ ] Filament resources для Category и Subscriber
- [ ] Сидер с базовыми категориями (Мемы, Поп-культура, Аниме, Спорт, Игры, Музыка, Свои принты, Кастом)
- [ ] Тесты Pest на CartService (add / update / remove / merge guest→user при логине)

**Acceptance:** API работает (postman-коллекция в `/docs/api`), миграции откатываются.

### Фаза 5 — интеграция фронта с фичами (3 дня)

- [ ] Cart-counter в Header привязан к `CartService`
- [ ] Wishlist-counter тоже
- [ ] Поиск (минимум: поиск по `product.name`)
- [ ] Фильтр-теги принтов (по `category_id` через pivot или по тегам)
- [ ] Email-подписка через `<form action="/subscribe">`

**Acceptance:** ходим как реальный пользователь — добавили в корзину, открыли `/cart`, удалили, подписались.

### Фаза 6 — конструктор v2 (8 дней, отдельная ветка `feature/constructor-v2`)

> **ВНИМАНИЕ:** до полного прохождения regression-чеклиста (§8) v2 не идёт в `main`.
> Цель фазы — сделать визуальный редизайн конструктора с полным функциональным паритетом. Новый конструктор не должен вводить другой workflow, другую схему данных или упрощённый набор возможностей.

- [ ] Создать `resources/views/constructor/v2.blade.php` параллельно текущему
- [ ] Составить карту текущих возможностей `resources/views/constructor/show.blade.php` и использовать её как обязательный checklist паритета
- [ ] Новый layout (3 колонки 280/1fr/320) на Tailwind
- [ ] Левая панель — табы Принты/Фото/Текст/Фигуры
- [ ] Центр — SVG-футболка с DOM-overlay для drag/resize (как в дизайне), но **с возможностью экспорта в PNG через тот же `canvas.toDataURL`** (рендерим на скрытый `<canvas>` перед отправкой)
- [ ] Правая панель — тип изделия / размер / qty / sticky-футер с ценой
- [ ] Mobile-вариант с bottom tabbar
- [ ] Все возможности текущего конструктора перенесены без упрощений: слои, настройки слоёв, порядок, выбор/переключение вариантов, undo/redo, очистка, загрузка ассетов, экспорт preview/print PNG и сабмит заявки
- [ ] **Контракт payload к `POST /order-requests` сохранён** (см. §0)
- [ ] Роут `/constructor/v2/{slug}` за фиче-флагом `CONSTRUCTOR_V2_ENABLED`
- [ ] Когда v2 готов: `ConstructorController@show` начинает редиректить на v2 при флаге; v1 остаётся доступен как `/constructor/v1/{slug}`

**Acceptance:** см. §8.

### Фаза 7 — финальная зачистка (1 день)

- [ ] Удалить `public.legacy.blade.php` если не используется
- [ ] Удалить фиче-флаги после стабильной недели в prod
- [ ] Обновить `docs/project-overview.md`

---

## 8. Regression-чеклист для конструктора v2

Перед мерджем `feature/constructor-v2` в `main` **обязательно** прогнать (Pest + ручной e2e). Базовое правило: v2 должен уметь всё, что умеет текущий конструктор v1. Проверка v2 выполняется сравнением с текущим `resources/views/constructor/show.blade.php`; если функция есть в v1, она должна быть в v2.

### 8.1 Функциональные

- [ ] Открытие любого товара → конструктор загружается без ошибок в console
- [ ] Переключение варианта (цвет/размер) → mockup меняется
- [ ] Переключение front/back → print area корректно перепозиционируется
- [ ] Добавление слоя image (загрузка PNG/JPG/SVG) → отображается в зоне печати
- [ ] Добавление слоя text → шрифт, цвет, жирность работают
- [ ] Добавление shape (rect/circle/star) → корректный рендер
- [ ] Drag слоя мышью → координаты обновляются
- [ ] Drag слоя touch (мобайл) → работает
- [ ] Resize за угловые хендлы → работает с aspect ratio для image
- [ ] Удаление слоя → удаляется из массива
- [ ] Undo / Redo → проходит минимум 5 шагов туда-обратно
- [ ] Clear → все слои удалены
- [ ] Все элементы управления из текущего конструктора либо перенесены в новый дизайн, либо имеют согласованный эквивалент без потери возможности
- [ ] Один и тот же пользовательский сценарий в v1 и v2 приводит к эквивалентному `canvas_json`, `preview_image`, `print_image` и `assets[]`

### 8.2 Контракт payload (критично!)

- [ ] `POST /order-requests` отправляется с теми же ключами:
  - `customer_name`, `customer_phone`, `customer_comment`
  - `product_id`, `variant_id`, `quantity`, `side`
  - `canvas_json.layers[]` с полями `id, type, name, x, y, rotation, scale, opacity, blend` + специфичные для типа
  - `canvas_json.print_area` с `x, y, width, height, unit`
  - `preview_image` (data URL PNG)
  - `print_image` (data URL PNG только зоны печати)
  - `assets[]` с `layer_id, file_name, data` для image-слоёв
- [ ] Backend (`OrderRequestController@store`) **не модифицируется** ни на байт во время этой работы
- [ ] Filament-страница заявки корректно отображает превью и слои

### 8.3 Performance

- [ ] FCP < 2s на slow 3G (Lighthouse)
- [ ] Drag не лагает на 50+ слоях
- [ ] Экспорт PNG занимает < 1s на типичном дизайне

### 8.4 Browser matrix

- [ ] Chrome 120+
- [ ] Safari 17+ (включая iOS)
- [ ] Firefox 120+
- [ ] Edge 120+

### 8.5 Тесты

- [ ] `tests/Feature/OrderRequestSubmissionTest.php` — фабричит payload v2 и проверяет, что заявка создаётся
- [ ] `tests/Feature/OrderRequestSubmissionTest.php::test_v1_payload_still_works` — старый формат тоже принимается
- [ ] Pest browser test (Dusk или Playwright) — happy path конструктора

---

## 9. Стратегия миграции данных

Существующие записи в БД:
- `Product`, `ProductVariant` — без изменений, новые nullable-поля.
- `OrderRequest.canvas_json` — JSON-схема обратно совместима (см. §5.5).
- `Design.preview_image_path` — без изменений.

Скрипт миграции **не нужен** — все изменения addon-only.

---

## 10. Риски и митигации

| Риск | Вероятность | Импакт | Митигация |
|---|---|---|---|
| Поломка конструктора v1 во время рефакторинга layout | Средняя | Критический | Конструктор v1 использует **отдельный** layout (он и так весь свой `<head>` рисует), не трогаем. Регулярно прогоняем e2e. |
| Расхождение payload между v1 и v2 конструктора | Средняя | Критический | Зафиксировать схему JSON в `app/Support/CanvasPayload.php` + JSON-Schema валидация в `OrderRequestController@store`. |
| Рост размера CSS из-за дизайн-системы | Низкая | Низкий | Tailwind 4 tree-shake + аудит через `vite build --mode=analyze`. |
| Manrope блокирует FCP | Низкая | Средний | `font-display: swap`, preload только weights 400/700. |
| Корзина «теряется» при логине | Средняя | Средний | Тест: `merge_guest_cart_on_login`. Хук в LoginListener. |
| Filament сломается из-за новых полей в Product | Низкая | Средний | Все новые поля nullable, старые ресурсы не трогаем; для новых полей — отдельный edit-tab. |

---

## 11. Метрики успеха

- **Технические:**
  - 0 регрессий по конструктору (заявки продолжают создаваться с той же частотой)
  - Lighthouse Performance ≥ 85 на главной (mobile)
  - Lighthouse Accessibility ≥ 95 на всех публичных страницах
  - 100% строк интерфейса локализованы (RU + UZ)

- **Продуктовые** (через 1 месяц после релиза):
  - CTR на конструктор с главной ↑ относительно baseline
  - Доля мобайл-сессий, дошедших до сабмита заявки, не падает
  - Email-подписки: ≥ N в неделю (baseline установить после запуска)

---

## 12. Открытые вопросы

1. **Авторизация:** в текущем проекте есть `auth` middleware и Login-роуты, но публичного логина нет. Нужно ли в v1 добавлять публичный логин/регистрацию (для корзины авторизованного пользователя), или гость-only с переносом в session — достаточно?
2. **Категории:** взять список из дизайна (8 штук) или согласовать с продактом?
3. **Streetwear-вариант** главной — A/B или отдельный путь `/streetwear`?
4. **Покупка в 1 клик** в дизайне — это отдельный flow от обычной корзины (без ввода адреса) или просто алиас «Заказать сейчас»?
5. **Платёжная интеграция** — остаётся вне scope, или появляется требование на v2?

Все открытые вопросы блокируют только соответствующую фазу, основной редизайн не блокируют.

---

## 13. Diff к текущему `docs/project-overview.md`

После завершения редизайна обновить:
- Раздел «Каталог» — описать категории и поиск
- Раздел «Конструктор» — описать v2 layout
- Добавить раздел «Корзина и избранное»
- Добавить раздел «Дизайн-система»

---

## Приложение A: контрольный список «как НЕ сломать конструктор»

Печатать на стене, читать перед каждым PR в этой инициативе:

1. ❌ Не редактирую `resources/views/constructor/show.blade.php` без отдельной ветки и regression-прогона.
2. ❌ Не меняю `OrderRequestController@store` без согласования.
3. ❌ Не трогаю миграции таблицы `order_requests` и `designs`.
4. ❌ Не удаляю поля из payload `canvas_json`.
5. ❌ Не переименовываю роут `/constructor/{slug}` и `/order-requests` (POST).
6. ❌ Не убираю и не упрощаю функции текущего конструктора под видом редизайна.
7. ✅ Любую новую функциональность вешаю на новые роуты / новые поля.
8. ✅ Перед мерджем — guard test: создаю `OrderRequest` через старый payload и через новый, оба должны проходить.
9. ✅ Перед релизом v2-конструктора — недельный shadow-deploy (старый по-умолчанию, новый по `?v=2`).
