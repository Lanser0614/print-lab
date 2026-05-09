# TDD-план: UI для AI-принта в конструкторе

> Статус: черновик для ревью. Код не писать, пока пользователь не одобрит этот план.

## Цель

Подключить уже готовый backend endpoint `POST /api/generated-prints` к конструктору, чтобы пользователь мог ввести prompt, опционально загрузить свой логотип/референс, сгенерировать AI-принт и добавить результат на товар как обычный image layer.

## Объём MVP

UI будет:

- Показывать отдельный блок “AI-принт” в конструкторе.
- Принимать prompt максимум 250 символов.
- Показывать счётчик символов.
- Опционально принимать reference image/logo через file input.
- Отправлять `prompt` и `reference_image` в `POST /api/generated-prints`.
- Показывать состояния loading, success, validation error, daily limit, upstream error.
- После успешной генерации добавлять картинку на canvas как image layer.
- Сохранять AI layer в `canvas_json.layers`.
- Сохранять generated image в заявке через существующий `assets` payload, чтобы админ мог скачать исходник.

UI не будет:

- Делать галерею нескольких AI-вариантов.
- Делать историю всех AI-генераций пользователя.
- Добавлять оплату за дополнительные генерации.
- Делать queue polling.
- Менять backend API.
- Переписывать canvas editor.

## Текущий контекст кода

Основной текущий файл конструктора:

- `resources/views/constructor/v2.blade.php`

В нём уже есть:

- `addImage(event)` - добавляет локальный image file в canvas.
- `layers` - массив canvas-слоёв.
- `serializeLayers()` - сохраняет слои в `canvas_json.layers`.
- `collectAssets()` - отправляет image layers в order request, но только если `layer.src` начинается с `data:`.
- `refreshUI()` - обновляет layers list, right panel и canvas.
- `constructorConfig.routes.storeOrderRequest` - route для заказа.

Для AI UI нужно не ломать этот контракт. Лучше добавить маленький helper:

- `addGeneratedImageLayer({ imageUrl, fileName, sourceId })`

Он должен загрузить image URL, нарисовать его в print area и добавить layer так же, как `addImage(event)`.

## Важное решение по storage/assets

Backend AI API возвращает `image_url`, то есть URL на уже сохранённый generated image.

Но текущий `collectAssets()` отправляет в заказ только image layers с `src` в формате `data:`.

Для MVP есть 2 варианта:

### Вариант A: Конвертировать generated image URL в data URL на frontend

После успешной генерации frontend делает `fetch(image_url)`, читает blob через `FileReader`, получает data URL и кладёт его в `layer.src`.

Плюсы:

- Минимальные backend изменения.
- Текущий `collectAssets()` продолжает работать без изменения контракта заказа.
- AI-картинка попадёт в order assets как обычное загруженное изображение.

Минусы:

- Картинка дублируется: уже есть в `generated-prints`, потом ещё сохраняется как `order-requests/assets`.

### Вариант B: Расширить order request backend, чтобы он принимал generated asset id/path

Плюсы:

- Нет дубля файла.
- Более чистая модель данных.

Минусы:

- Нужно менять order request validation/use case.
- Больше backend-риска для UI-среза.

Рекомендация для MVP: **Вариант A**. Он проще, хорошо тестируется и не меняет существующий order flow.

## UI-дизайн

Добавить AI-блок в левую панель конструктора рядом с “Быстрый старт”.

Содержимое блока:

- Label: `AI-принт`
- Textarea `aiPromptInput`, `maxlength="250"`, placeholder: `Например: минимальный логотип кофейни в стиле streetwear`
- Character counter: `0/250`
- Optional file input `aiReferenceInput`, label: `Логотип / референс`
- Small preview имени выбранного файла.
- Button `Сгенерировать`
- Message area для ошибок и лимита.

Состояния:

- Idle: кнопка активна, если prompt не пустой.
- Loading: кнопка disabled, текст `Генерация...`.
- Success: картинка добавляется на canvas, toast `AI-принт добавлен`.
- 422: показать validation message.
- 429: показать `Сегодня доступно только 2 AI-генерации`.
- 503: показать `AI-генерация пока не настроена`.
- 502/other: показать `Не удалось сгенерировать принт. Попробуйте ещё раз`.

## API call

Добавить route в `constructorConfig.routes`:

```php
'generatePrint' => url('/api/generated-prints'),
```

Frontend request:

```js
await fetch(constructorConfig.routes.generatePrint, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
  },
  body: JSON.stringify({
    prompt,
    reference_image: referenceDataUrl || null,
  }),
});
```

## Планируемые файлы

Создать:

- `tests/Feature/ConstructorAiPrintUiTest.php` - feature tests для HTML/route config.
- `tests/Feature/ConstructorAiPrintOrderPayloadTest.php` - если понадобится backend test для order payload с AI data URL.

Изменить:

- `resources/views/constructor/v2.blade.php` - UI, JS helpers, route config.
- `resources/views/constructor/show.blade.php` - только если legacy constructor должен получить тот же UI.
- `lang/ru/site.php` - тексты UI.
- `lang/uz/site.php` - тексты UI.

Рекомендация: сначала реализовать только `constructor.v2`, потому что redesign flag уже ведёт к v2 и там актуальная UI-структура. Legacy `constructor.show` трогать только если бизнесу нужен parity.

## TDD-задачи

### Задача 1: Route config доступен в constructor v2

Красный тест:

- Открыть `/constructor/v2/{product}`.
- Проверить, что HTML содержит `generatePrint`.
- Проверить, что route указывает на `/api/generated-prints`.

Ожидаемое первое падение:

- `constructorConfig.routes.generatePrint` отсутствует.

Зелёная реализация:

- Добавить `generatePrint` в `$constructorConfig['routes']`.

Команда проверки:

```bash
rtk php artisan test --filter=ConstructorAiPrintUiTest
```

### Задача 2: AI UI отображается в constructor v2

Красный тест:

- Открыть constructor v2.
- Проверить наличие:
  - `id="aiPrintPanel"`
  - `id="aiPromptInput"`
  - `maxlength="250"`
  - `id="aiGenerateBtn"`
  - `id="aiReferenceInput"`
  - `id="aiPrintMessage"`

Ожидаемое первое падение:

- AI-панель отсутствует.

Зелёная реализация:

- Добавить markup AI-панели в левую панель.
- Добавить компактный CSS под текущий стиль v2.
- Не менять canvas layout.

Команда проверки:

```bash
rtk php artisan test --filter=ConstructorAiPrintUiTest
```

### Задача 3: JS функции AI UI присутствуют

Красный тест:

- Проверить, что HTML содержит функции:
  - `handleAiGenerate`
  - `readAiReferenceImage`
  - `addGeneratedImageLayer`
  - `imageUrlToDataUrl`
  - `updateAiPromptCounter`

Ожидаемое первое падение:

- JS helpers отсутствуют.

Зелёная реализация:

- Добавить JS helpers в script блока конструктора.
- Не вызывать backend в тесте.

Команда проверки:

```bash
rtk php artisan test --filter=ConstructorAiPrintUiTest
```

### Задача 4: Prompt counter и client-side guard

Красный тест:

- HTML textarea имеет `maxlength="250"`.
- Button изначально disabled или JS guard запрещает пустой prompt.
- JS содержит обновление `aiPromptCounter`.

Ожидаемое первое падение:

- Нет counter/guard.

Зелёная реализация:

- Добавить `input` listener для prompt.
- Показывать `N/250`.
- Не отправлять запрос при пустом prompt.

Команда проверки:

```bash
rtk php artisan test --filter=ConstructorAiPrintUiTest
```

### Задача 5: Успешная генерация добавляет image layer

Красный тест:

- Минимальный feature test проверяет наличие JS-кода, который:
  - читает `data.image_url`
  - вызывает `addGeneratedImageLayer`
  - вызывает `refreshUI`
  - показывает success toast/message.

Дополнительная ручная проверка через browser/dev server:

- При `AI_IMAGE_DRIVER=fake` нажать “Сгенерировать”.
- Убедиться, что картинка появляется в print area.
- Убедиться, что layer появляется в списке слоёв.

Ожидаемое первое падение:

- JS не умеет добавлять generated image в canvas.

Зелёная реализация:

- `handleAiGenerate()` вызывает API.
- После success получает `image_url`.
- `imageUrlToDataUrl(image_url)` конвертирует URL в data URL.
- `addGeneratedImageLayer()` создаёт image layer с:
  - `type: 'image'`
  - `src: dataUrl`
  - `originalFileName: data.asset.file_name || 'ai-print.png'`
  - `name: 'AI-принт'`
  - `generatedPrintId: data.id`
  - `generatedImageUrl: data.image_url`
- Layer размещается по центру print area, с максимальным размером 70% print area.

Команда проверки:

```bash
rtk php artisan test --filter=ConstructorAiPrintUiTest
```

### Задача 6: Ошибки API показываются пользователю

Красный тест:

- JS содержит обработку status codes:
  - 422
  - 429
  - 503
  - 502/default

Ожидаемое первое падение:

- Ошибки не различаются.

Зелёная реализация:

- Добавить `showAiPrintMessage(message, type)`.
- На 429 показать лимит 2 генерации в день.
- На 503 показать, что AI не настроен.
- На остальные ошибки показать общий текст.

Команда проверки:

```bash
rtk php artisan test --filter=ConstructorAiPrintUiTest
```

### Задача 7: Order payload включает AI-картинку как asset

Красный тест:

- Проверить на уровне JS/HTML, что generated layer получает `src` как data URL.
- Это значит, что существующий `collectAssets()` включит AI-картинку, потому что он фильтрует `layer.src.startsWith('data:')`.

Ожидаемое первое падение:

- Generated layer хранит только URL, поэтому `collectAssets()` его пропустит.

Зелёная реализация:

- Обязательно конвертировать `image_url` в data URL перед добавлением layer.
- Сохранить `generatedImageUrl` отдельно только как metadata в layer.

Команда проверки:

```bash
rtk php artisan test --filter=ConstructorAiPrintUiTest
```

### Задача 8: Browser smoke test

Запустить dev server или использовать текущий local setup.

Ручная проверка:

- Открыть constructor v2.
- Ввести prompt до 250 символов.
- Нажать “Сгенерировать”.
- При `AI_IMAGE_DRIVER=fake` картинка появляется на товаре.
- Layer появляется в списке.
- Нажать “Заказать”.
- Убедиться, что заявка создаётся.
- В админке/DB убедиться, что asset сохранён.

Если локальная DB/Docker не подняты, зафиксировать это как блокер ручной проверки, но feature tests должны пройти.

## Критерии приёмки

UI можно считать готовым, когда:

- Constructor v2 показывает AI-панель.
- Prompt ограничен 250 символами.
- Reference/logo file можно выбрать.
- UI вызывает `POST /api/generated-prints`.
- Loading и ошибки отображаются.
- 429 limit имеет понятный текст.
- Успешный AI-result добавляется на canvas как image layer.
- Generated layer попадает в `collectAssets()` через data URL.
- Feature tests проходят.
- Если dev environment доступен, ручной browser smoke test проходит.

## Следующий отдельный этап

После MVP UI можно отдельно спланировать:

- Галерею последних AI-генераций.
- Несколько вариантов за один prompt.
- Queue/polling для долгой генерации.
- Персональные лимиты для авторизованных пользователей.
- Платные дополнительные генерации.
- Админскую аналитику стоимости AI-запросов.
