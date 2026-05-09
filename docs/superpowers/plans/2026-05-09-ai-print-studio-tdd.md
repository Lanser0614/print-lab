# TDD-план: AI-студия рисунка для принта

> **Для агентной реализации:** обязательный sub-skill: `superpowers:subagent-driven-development` или `superpowers:executing-plans`. Шаги идут чекбоксами (`- [ ]`), чтобы выполнять план по порядку.

**Цель:** сделать AI-студию, где пользователь генерирует не готовую футболку, а отдельный рисунок/арт для печати. После генерации этот рисунок показывается поверх mockup футболки в зоне печати и может быть перенесен в конструктор как обычный image layer.

**Архитектура:** использовать существующий `POST /api/generated-prints` для генерации изображения. AI-студия хранит выбранный результат на frontend как `dataUrl` + metadata, показывает overlay на preview и передает результат в конструктор через `sessionStorage`. Конструктор при загрузке забирает pending AI-рисунок из `sessionStorage` и добавляет его через существующую механику image layer.

**Стек:** Laravel routes/controllers, Blade, vanilla JS внутри Blade, текущий canvas-конструктор, feature-тесты PHPUnit.

---

## Главное продуктовое решение

AI не должен генерировать “футболку с принтом”. Он должен генерировать **отдельный рисунок для печати**:

- логотип;
- иллюстрацию;
- маскота;
- надпись;
- графический арт.

Затем PrintLab показывает этот рисунок поверх футболки в зоне печати.

Почему так:

- пользователь получает реальный print asset, а не mockup;
- результат можно двигать, масштабировать и дорабатывать;
- админ получает отдельный файл для печати;
- нет эффекта “футболка внутри футболки”;
- flow становится понятнее: “опиши рисунок” → “посмотри на футболке” → “использовать в конструкторе”.

---

## Пользовательский flow

1. Пользователь на странице товара нажимает **“Создать рисунок с AI”**.
2. Открывается **AI-студия рисунка**.
3. Пользователь описывает именно рисунок, который хочет напечатать.
4. Опционально загружает логотип/референс.
5. Нажимает **“Сгенерировать рисунок”**.
6. Frontend отправляет запрос в `POST /api/generated-prints`.
7. При успехе frontend получает `image_url`, конвертирует его в `dataUrl` и показывает этот рисунок поверх mockup футболки в зоне печати.
8. Пользователь нажимает **“Использовать в конструкторе”**.
9. AI-студия сохраняет выбранный рисунок в `sessionStorage` и открывает конструктор.
10. Конструктор забирает рисунок из `sessionStorage`, добавляет его на canvas как image layer, затем очищает storage.

---

## В MVP не делаем

- генерацию нескольких вариантов за один запрос;
- историю генераций;
- оплату дополнительных генераций;
- backend-сессию чата;
- редактирование prompt через LLM-чат;
- удаление текущего inline AI-блока конструктора;
- изменение контракта `POST /api/generated-prints`.

---

### Задача 1: Route и controller для AI-студии

**Файлы:**

- Изменить: `routes/web.php`
- Изменить: `app/Http/Controllers/ConstructorController.php`
- Тест: `tests/Feature/AiPrintStudioPageTest.php`

- [ ] **Шаг 1: написать падающий тест**

Проверить, что `route('ai-studio.show', $product)` открывается, возвращает `200`, содержит:

- `window.aiStudioConfig`;
- название товара;
- `/api/generated-prints`;
- ссылку на конструктор;
- данные print area.

- [ ] **Шаг 2: запустить тест и увидеть красную фазу**

```bash
rtk php artisan test --filter=AiPrintStudioPageTest
```

Ожидаемо: тест падает, потому что route `ai-studio.show` еще не существует.

- [ ] **Шаг 3: добавить route**

Внутри локализованной группы `/{locale}` добавить:

```php
Route::get('/ai-studio/{product:slug}', [ConstructorController::class, 'aiStudio'])
    ->name('ai-studio.show');
```

- [ ] **Шаг 4: добавить метод controller**

В `ConstructorController` добавить:

```php
public function aiStudio(Request $request, string $locale, Product $product): View
{
    return $this->renderConstructor($request, $product, 'constructor.ai-studio');
}
```

Так AI-студия получает те же данные, что и конструктор: `product`, `variant`, `printArea`, `side`.

- [ ] **Шаг 5: запустить тест**

```bash
rtk php artisan test --filter=AiPrintStudioPageTest
```

Ожидаемо: route найден, следующая красная фаза может быть из-за отсутствующего view.

---

### Задача 2: UI AI-студии под генерацию отдельного рисунка

**Файлы:**

- Создать/изменить: `resources/views/constructor/ai-studio.blade.php`
- Изменить: `lang/ru/site.php`
- Изменить: `lang/uz/site.php`
- Тест: `tests/Feature/AiPrintStudioPageTest.php`

- [ ] **Шаг 1: расширить падающий тест на UI**

Проверить наличие элементов:

```html
id="aiStudioChat"
id="aiStudioPrompt"
maxlength="250"
id="aiStudioReference"
id="aiStudioGenerate"
id="aiStudioPreview"
id="aiStudioPrintOverlay"
id="aiStudioVariants"
id="aiStudioOpenConstructor"
```

Проверить тексты:

- `site.ai_studio_title`
- `site.ai_studio_subtitle`
- `site.ai_studio_prompt_label`
- `site.ai_studio_generate`
- `site.ai_studio_open_constructor`

- [ ] **Шаг 2: запустить тест и увидеть красную фазу**

```bash
rtk php artisan test --filter=AiPrintStudioPageTest
```

Ожидаемо: падает из-за отсутствующего view, элементов или переводов.

- [ ] **Шаг 3: сделать Blade view**

Структура:

- верхняя панель: PrintLab, товар, вариант, ссылка назад;
- левая колонка `aiStudioChat`: prompt, reference upload, generate button, message area;
- правая колонка `aiStudioPreview`: mockup футболки, print zone, overlay-картинка `aiStudioPrintOverlay`;
- блок `aiStudioVariants`: один выбранный результат в MVP, визуально можно оставить 4 placeholder slots;
- кнопка `aiStudioOpenConstructor`: disabled/визуально вторичная до первой генерации, активная после успеха.

- [ ] **Шаг 4: добавить `window.aiStudioConfig`**

В view добавить:

```php
<script>
window.aiStudioConfig = @json([
    'product' => [
        'id' => $product->id,
        'name' => $product->localizedName(),
        'slug' => $product->slug,
    ],
    'variant' => [
        'id' => $variant->id,
        'color' => $variant->color,
        'size' => $variant->size,
        'mockup_front_url' => $variant->mockup_front_url,
        'mockup_back_url' => $variant->mockup_back_url,
    ],
    'printArea' => [
        'side' => $side,
        'x' => $printArea->x,
        'y' => $printArea->y,
        'width' => $printArea->width,
        'height' => $printArea->height,
        'unit' => 'ratio',
    ],
    'routes' => [
        'generatePrint' => url('/api/generated-prints'),
        'constructor' => route('constructor.show', [
            'product' => $product,
            'variant' => $variant->id,
            'side' => $side,
        ]),
    ],
]);
</script>
```

- [ ] **Шаг 5: обновить тексты**

В `lang/ru/site.php`:

```php
'ai_studio_title' => 'AI-студия рисунка',
'ai_studio_subtitle' => 'AI создаст отдельный рисунок для печати, а мы покажем его поверх футболки.',
'ai_studio_prompt_label' => 'Какой рисунок нужно напечатать?',
'ai_studio_prompt_placeholder' => 'Например: маскот пиццерии, красно-зеленый стиль, без фона',
'ai_studio_reference' => 'Логотип / референс',
'ai_studio_generate' => 'Сгенерировать рисунок',
'ai_studio_generating' => 'Генерируем рисунок...',
'ai_studio_preview' => 'Предпросмотр на футболке',
'ai_studio_preview_empty' => 'Сгенерированный рисунок появится здесь',
'ai_studio_variants' => 'Результат',
'ai_studio_open_constructor' => 'Использовать в конструкторе',
'ai_studio_short_link' => 'AI-студия',
'ai_studio_success' => 'Рисунок готов. Можно открыть его в конструкторе.',
'ai_studio_prompt_required' => 'Опишите рисунок для печати',
'ai_studio_failed' => 'Не удалось сгенерировать рисунок. Попробуйте еще раз.',
'product_create_with_ai' => 'Создать рисунок с AI',
```

В `lang/uz/site.php` добавить узбекские аналоги.

- [ ] **Шаг 6: запустить тест**

```bash
rtk php artisan test --filter=AiPrintStudioPageTest
```

Ожидаемо: тест страницы AI-студии проходит.

---

### Задача 3: Frontend flow генерации и overlay на футболке

**Файлы:**

- Изменить: `resources/views/constructor/ai-studio.blade.php`
- Тест: `tests/Feature/AiPrintStudioPageTest.php`

- [ ] **Шаг 1: написать падающий тест на JS helpers**

Проверить, что HTML AI-студии содержит функции:

```js
handleAiStudioGenerate
readAiStudioReferenceImage
imageUrlToDataUrl
normalizeGeneratedImageUrl
showAiStudioResult
storeAiStudioSelection
openGeneratedPrintInConstructor
```

Проверить, что HTML содержит ключ storage:

```js
printlab.pendingAiPrint
```

- [ ] **Шаг 2: запустить тест и увидеть красную фазу**

```bash
rtk php artisan test --filter=AiPrintStudioPageTest
```

Ожидаемо: JS helpers отсутствуют.

- [ ] **Шаг 3: реализовать генерацию**

`handleAiStudioGenerate()`:

1. читает prompt;
2. если prompt пустой, показывает `site.ai_studio_prompt_required`;
3. читает reference image как data URL;
4. отправляет `POST window.aiStudioConfig.routes.generatePrint`;
5. передает:

```json
{
  "prompt": "описание рисунка",
  "reference_image": "data:image/..." или null
}
```

6. при успехе получает `image_url`;
7. конвертирует `image_url` в `dataUrl`;
8. вызывает `showAiStudioResult(...)`.

- [ ] **Шаг 4: реализовать overlay**

`showAiStudioResult({ dataUrl, imageUrl, generatedPrintId, fileName })`:

- ставит `dataUrl` в `<img id="aiStudioPrintOverlay">`;
- показывает overlay внутри print zone;
- сохраняет выбранный результат в переменную `selectedAiStudioPrint`;
- активирует кнопку “Использовать в конструкторе”;
- показывает success message.

Overlay должен быть расположен поверх футболки внутри зоны печати. Для MVP размер: `70%` ширины print zone, `auto` height, `object-fit: contain`.

- [ ] **Шаг 5: обработать ошибки API**

Поведение:

- `422`: показать backend message или `site.ai_studio_failed`;
- `429`: использовать существующий daily-limit текст или общий failure;
- `503`: использовать existing not-configured text или общий failure;
- другое: `site.ai_studio_failed`.

- [ ] **Шаг 6: запустить тест**

```bash
rtk php artisan test --filter=AiPrintStudioPageTest
```

Ожидаемо: тесты проходят.

---

### Задача 4: Передача AI-рисунка в конструктор

**Файлы:**

- Изменить: `resources/views/constructor/ai-studio.blade.php`
- Изменить: `resources/views/constructor/v2.blade.php`
- Тест: `tests/Feature/AiPrintStudioHandoffTest.php`

- [ ] **Шаг 1: написать падающий тест на handoff**

В `AiPrintStudioHandoffTest` проверить, что AI-студия содержит:

```js
sessionStorage.setItem('printlab.pendingAiPrint'
window.location.href = window.aiStudioConfig.routes.constructor
```

И что constructor v2 содержит:

```js
loadPendingAiPrint
sessionStorage.getItem('printlab.pendingAiPrint')
sessionStorage.removeItem('printlab.pendingAiPrint')
addGeneratedImageLayer
```

- [ ] **Шаг 2: запустить тест и увидеть красную фазу**

```bash
rtk php artisan test --filter=AiPrintStudioHandoffTest
```

Ожидаемо: handoff-код отсутствует.

- [ ] **Шаг 3: сохранить выбранный рисунок в AI-студии**

Добавить:

```js
const AI_STUDIO_STORAGE_KEY = 'printlab.pendingAiPrint';
let selectedAiStudioPrint = null;

function storeAiStudioSelection() {
  if (!selectedAiStudioPrint) return false;

  sessionStorage.setItem(AI_STUDIO_STORAGE_KEY, JSON.stringify({
    dataUrl: selectedAiStudioPrint.dataUrl,
    imageUrl: selectedAiStudioPrint.imageUrl,
    generatedPrintId: selectedAiStudioPrint.generatedPrintId,
    fileName: selectedAiStudioPrint.fileName || 'ai-print.png',
    productId: window.aiStudioConfig.product.id,
    variantId: window.aiStudioConfig.variant.id,
    side: window.aiStudioConfig.printArea.side || 'front',
  }));

  return true;
}
```

- [ ] **Шаг 4: открыть конструктор**

Добавить:

```js
function openGeneratedPrintInConstructor(event) {
  if (event) event.preventDefault();
  if (!storeAiStudioSelection()) {
    showAiStudioMessage(@json(__('site.ai_studio_prompt_required')), 'error');
    return;
  }

  window.location.href = window.aiStudioConfig.routes.constructor;
}
```

Повесить на `aiStudioOpenConstructor`.

- [ ] **Шаг 5: принять рисунок в constructor v2**

В `resources/views/constructor/v2.blade.php` добавить:

```js
const AI_STUDIO_STORAGE_KEY = 'printlab.pendingAiPrint';

function loadPendingAiPrint() {
  const raw = sessionStorage.getItem(AI_STUDIO_STORAGE_KEY);
  if (!raw) return;

  sessionStorage.removeItem(AI_STUDIO_STORAGE_KEY);

  let pending;
  try {
    pending = JSON.parse(raw);
  } catch (error) {
    return;
  }

  if (!pending?.dataUrl) return;
  if (pending.productId && Number(pending.productId) !== Number(constructorConfig.product.id)) return;
  if (pending.variantId && Number(pending.variantId) !== Number(constructorConfig.variant.id)) return;

  addGeneratedImageLayer({
    dataUrl: pending.dataUrl,
    imageUrl: pending.imageUrl || null,
    fileName: pending.fileName || 'ai-print.png',
    sourceId: pending.generatedPrintId || null,
  });
}
```

Вызвать `loadPendingAiPrint()` после инициализации canvas/product image, когда `addGeneratedImageLayer` уже объявлен и `getPZ()` работает.

- [ ] **Шаг 6: запустить тест**

```bash
rtk php artisan test --filter=AiPrintStudioHandoffTest
```

Ожидаемо: handoff-тест проходит.

---

### Задача 5: Входы в AI-студию

**Файлы:**

- Изменить: `resources/views/products/show.blade.php`
- Изменить: `resources/views/constructor/v2.blade.php`
- Тест: `tests/Feature/AiPrintStudioEntryPointTest.php`

- [ ] **Шаг 1: написать/обновить падающие тесты**

Проверить:

- на странице товара есть текст `site.product_create_with_ai`;
- ссылка ведет на `route('ai-studio.show', ['product' => $product, 'variant' => $variant->id])`;
- в constructor v2 есть текст `site.ai_studio_short_link`;
- ссылка из constructor v2 ведет на AI-студию с текущим `variant` и `side`.

- [ ] **Шаг 2: запустить тест и увидеть красную фазу**

```bash
rtk php artisan test --filter=AiPrintStudioEntryPointTest
```

- [ ] **Шаг 3: добавить CTA на странице товара**

В `resources/views/products/show.blade.php` рядом с кнопкой конструктора добавить вторую основную кнопку:

```php
<a id="ctaAiStudio"
   href="{{ route('ai-studio.show', ['product' => $product, 'variant' => $firstVariant->id]) }}">
    {{ __('site.product_create_with_ai') }}
</a>
```

При смене варианта в JS обновлять:

```js
document.getElementById('ctaAiStudio').href = aiStudioBase + '?variant=' + v.id;
```

- [ ] **Шаг 4: добавить вход из конструктора**

В `resources/views/constructor/v2.blade.php` рядом с текущим AI-блоком добавить компактную ссылку:

```php
<a href="{{ route('ai-studio.show', ['product' => $product, 'variant' => $variant->id, 'side' => $side]) }}">
    {{ __('site.ai_studio_short_link') }}
</a>
```

Важно: текущий inline AI-блок не удалять.

- [ ] **Шаг 5: запустить тесты**

```bash
rtk php artisan test --filter=AiPrintStudioEntryPointTest
rtk php artisan test --filter=ConstructorAiPrintUiTest
```

---

### Задача 6: Prompt policy для backend генератора

**Файлы:**

- Изменить: `app/Services/Ai/OpenAiImageGenerator.php`
- Тест: `tests/Feature/GeneratedPrintApiTest.php` или новый focused test для prompt policy.

- [ ] **Шаг 1: написать падающий тест**

Проверить, что генератор/запрос к AI включает смысловую инструкцию:

- генерировать отдельный рисунок для печати;
- не генерировать футболку, одежду, mockup или product photo;
- предпочитать чистый фон или прозрачный вид, если модель это поддерживает.

- [ ] **Шаг 2: запустить тест**

```bash
rtk php artisan test --filter=GeneratedPrintApiTest
```

- [ ] **Шаг 3: обновить prompt policy**

В `OpenAiImageGenerator` добавить системную/объединенную инструкцию примерно такого смысла:

```text
Generate only the standalone artwork intended for printing on a product.
Do not generate a t-shirt, apparel mockup, product photo, hanger, model, or room scene.
The output should be centered print artwork, logo, mascot, lettering, or illustration.
Prefer a clean plain or transparent-looking background.
```

Важно: не менять публичный API `POST /api/generated-prints`.

- [ ] **Шаг 4: запустить тест**

```bash
rtk php artisan test --filter=GeneratedPrintApiTest
```

---

### Задача 7: Итоговая проверка

**Файлы:**

- Только тесты и ручная проверка.

- [ ] **Шаг 1: проверить AI-студию**

```bash
rtk php artisan test --filter=AiPrintStudio
```

- [ ] **Шаг 2: проверить старый AI UI конструктора**

```bash
rtk php artisan test --filter=ConstructorAiPrintUiTest
```

- [ ] **Шаг 3: проверить API генерации**

```bash
rtk php artisan test --filter=GeneratedPrintApiTest
```

- [ ] **Шаг 4: проверить страницу товара**

```bash
rtk php artisan test --filter=Product
```

- [ ] **Шаг 5: ручная проверка в браузере**

Открыть:

```text
http://localhost:8000/ru/products/classic-t-shirt
http://localhost:8000/ru/ai-studio/classic-t-shirt
http://localhost:8000/ru/constructor/classic-t-shirt
```

Проверить:

- кнопка “Создать рисунок с AI” видна на странице товара;
- AI-студия открывается;
- пользователь видит, что AI генерирует именно рисунок, а не футболку;
- после генерации рисунок появляется поверх футболки в зоне печати;
- кнопка “Использовать в конструкторе” открывает конструктор;
- в конструкторе AI-рисунок появляется как image layer;
- старый inline AI-блок конструктора не сломан;
- мобильная верстка не ломается.
