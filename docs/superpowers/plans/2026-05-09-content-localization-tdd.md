# Локализация Контента Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Добавить переводимые названия для продуктов, категорий и готовых принтов через JSON-колонки, а цвет/размер вариантов оставить обычными полями и явно показывать размер при выборе принта.

**Architecture:** Модели получают JSON-поля `name_translations` / `title_translations` и единый fallback: текущая локаль -> `ru` -> старое поле. Витрина и конструктор используют локализованные accessor-методы, админка Filament получает отдельные RU/UZ поля. `product_variants.color` остается техническим значением цвета, `product_variants.size` остается `S/M/L/XL`.

**Tech Stack:** Laravel migrations, Eloquent models, Blade, Filament forms/tables, PHPUnit feature tests.

---

### Task 1: Модельные тесты переводов

**Files:**
- Create: `tests/Feature/ContentLocalizationTest.php`
- Modify: `app/Models/Product.php`
- Modify: `app/Models/Category.php`
- Modify: `app/Models/ReadyPrint.php`

- [ ] Написать failing-тест: `Product::localizedName()` возвращает `uz`, затем fallback `ru`, затем `name`.
- [ ] Написать failing-тест: `Category::localizedName()` работает по той же схеме.
- [ ] Написать failing-тест: `ReadyPrint::localizedTitle()` работает по той же схеме.
- [ ] Добавить casts для JSON-полей и методы `localizedName()` / `localizedTitle()`.
- [ ] Запустить `rtk php artisan test tests/Feature/ContentLocalizationTest.php`.

### Task 2: Миграция JSON-колонок

**Files:**
- Create: `database/migrations/2026_05_09_000400_add_content_translation_columns.php`

- [ ] Написать failing-тест, который проверяет запись JSON-переводов в БД.
- [ ] Добавить nullable JSON-колонки:
  - `products.name_translations`
  - `categories.name_translations`
  - `ready_prints.title_translations`
- [ ] В миграции заполнить существующие записи значением `{"ru": old_value}`.
- [ ] Запустить targeted tests.

### Task 3: Витрина и конструктор

**Files:**
- Modify: `resources/views/catalog/home-v2.blade.php`
- Modify: `resources/views/catalog/index.blade.php`
- Modify: `resources/views/products/show.blade.php`
- Modify: `resources/views/prints/show.blade.php`
- Modify: `resources/views/constructor/show.blade.php`
- Modify: `resources/views/constructor/v2.blade.php`

- [ ] Написать failing feature-тест: `/uz` показывает узбекские названия продукта, категории и готового принта.
- [ ] Заменить прямые `$model->name` / `$model->title` на localized methods в пользовательских Blade.
- [ ] Убедиться, что выбор варианта показывает размер `S/M/L/XL`, а цвет можно рендерить как цветной swatch по `color`.
- [ ] Запустить targeted tests.

### Task 4: Filament админка

**Files:**
- Modify: `app/Filament/Resources/Products/ProductResource.php`
- Modify: `app/Filament/Resources/Categories/CategoryResource.php`
- Modify: `app/Filament/Resources/ReadyPrints/ReadyPrintResource.php`

- [ ] Добавить поля RU/UZ для переводов названий.
- [ ] Оставить старые `name` / `title` как обязательный fallback.
- [ ] В таблицах показывать fallback-локализованное значение.
- [ ] Для вариантов товара оставить обычные поля `color` и `size`; label уточнить, что `color` лучше хранить как HEX.

### Task 5: Проверка

- [ ] Запустить `rtk php artisan test`.
- [ ] Запустить `rtk npm run build`, если менялись Blade/CSS/JS.
- [ ] Проверить `rtk git diff --stat` и убедиться, что не включены посторонние untracked файлы.
