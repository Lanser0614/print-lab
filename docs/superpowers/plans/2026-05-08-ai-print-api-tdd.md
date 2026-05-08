# TDD-план: API генерации AI-принтов

> Статус: черновик для ревью. Код не писать, пока пользователь не одобрит этот план.

## Цель

Добавить backend API для генерации принта по текстовому промпту. Пользователь также сможет опционально передать свой рисунок, референс или логотип. Первый релиз только API: без изменений UI конструктора.

## Объём MVP

API будет:

- Принимать промпт от frontend.
- Опционально принимать reference image в формате PNG, JPEG или WebP через data URL.
- Ограничивать prompt до 250 символов.
- Ограничивать guest-пользователя до 2 успешных генераций в день.
- В dev/test использовать fake image generator без запроса в OpenAI.
- В production/staging вызывать OpenAI Images API только с backend.
- Сохранять сгенерированную картинку в публичный storage.
- Сохранять запись в базе: промпт, статус, модель, путь к картинке и metadata.
- Возвращать JSON, который позже можно будет использовать в конструкторе для добавления картинки как canvas-слоя.

API не будет:

- Добавлять кнопку или панель в конструктор.
- Делать асинхронную генерацию через очередь.
- Генерировать несколько вариантов за один запрос.
- Автоматически прикреплять результат к заказу.
- Показывать OpenAI API key в браузере.
- Делать отдельные тарифы или оплату за дополнительные генерации.

## Рекомендуемая архитектура

Для первого TDD-среза делаем небольшой синхронный endpoint:

- `POST /api/generated-prints`
- Controller принимает и валидирует request.
- Use case управляет процессом генерации.
- OpenAI client вызывает Images API через Laravel `Http`.
- Результат сохраняется в `storage/app/public/generated-prints`.
- Модель `GeneratedPrint` хранит запрос и результат.

Такой вариант проще всего покрыть тестами. Если генерация окажется слишком долгой для UX, следующим отдельным шагом переведём это на queue job со статусами `pending`, `completed`, `failed`.

## Переменные окружения

Добавить в `.env.example`:

```env
OPENAI_API_KEY=
AI_IMAGE_DRIVER=fake
OPENAI_IMAGE_MODEL=gpt-image-1-mini
```

Добавить в `config/services.php`:

```php
'openai' => [
    'image_driver' => env('AI_IMAGE_DRIVER', 'fake'),
    'api_key' => env('OPENAI_API_KEY'),
    'image_model' => env('OPENAI_IMAGE_MODEL', 'gpt-image-1-mini'),
],
```

Правило окружений:

- `local` и `testing`: `AI_IMAGE_DRIVER=fake`.
- `production` или `staging`: `AI_IMAGE_DRIVER=openai`.

Fake driver должен возвращать локальную PNG-картинку без сети и без OpenAI API key. Это нужно для dev-разработки, feature tests и ручной проверки API.

## Планируемые файлы

Создать:

- `routes/api.php` - API route для генерации принтов.
- `database/migrations/2026_05_08_000000_create_generated_prints_table.php` - таблица результатов генерации.
- `app/Models/GeneratedPrint.php` - модель AI-генерации.
- `app/Http/Requests/StoreGeneratedPrintRequest.php` - валидация входящих данных.
- `app/Http/Controllers/GeneratedPrintController.php` - API controller.
- `app/UseCases/GeneratedPrints/CreateGeneratedPrintUseCase.php` - основной сценарий генерации.
- `app/Services/Ai/ImageGenerator.php` - interface генератора изображений.
- `app/Services/Ai/FakeImageGenerator.php` - локальный fake generator для dev/test.
- `app/Services/Ai/OpenAiImageGenerator.php` - интеграция с OpenAI Images API.
- `app/Providers/AiImageServiceProvider.php` или binding в существующем provider - выбор fake/openai driver.
- `tests/Feature/GeneratedPrintApiTest.php` - feature tests для endpoint.
- `tests/Unit/OpenAiImageGeneratorTest.php` - unit tests для сервиса, если feature tests будет недостаточно.

Изменить:

- `bootstrap/app.php` - подключить `routes/api.php`.
- `config/services.php` - добавить OpenAI image config.
- `.env.example` - добавить OpenAI env vars.

## Дизайн базы данных

Таблица: `generated_prints`

Колонки:

- `id`
- `prompt` text
- `reference_image_path` nullable string
- `generated_image_path` nullable string
- `model` string
- `status` string, default `completed` для MVP
- `error_message` nullable text
- `guest_fingerprint` nullable string, index
- `metadata` nullable JSON
- `created_at`
- `updated_at`

Статусы для MVP:

- `completed` - OpenAI вернул картинку, и она сохранена.
- `failed` - OpenAI вернул ошибку или некорректный ответ.

Пока не добавляем `pending`, потому что MVP синхронный. `pending` понадобится, когда перенесём генерацию в очередь.

Guest-пользователь определяется без авторизации. Для MVP используем дневной fingerprint на основе IP, user agent и текущей даты. Fingerprint не должен быть публичным идентификатором пользователя, он нужен только для rate limit. Это не идеальная защита от обхода лимита, но достаточная для первого среза без регистрации.

Лимит:

- Максимум 2 успешные генерации в день для одного guest fingerprint.
- Failed-генерации не должны уменьшать дневной лимит.
- После превышения лимита API возвращает `429 Too Many Requests`.

## API-контракт

Request:

```http
POST /api/generated-prints
Content-Type: application/json
```

Body:

```json
{
  "prompt": "Минималистичный streetwear-логотип с чёрным котом и узбекским орнаментом",
  "reference_image": "data:image/png;base64,..."
}
```

`reference_image` необязателен.

Успешный ответ: `201 Created`

```json
{
  "data": {
    "id": 1,
    "status": "completed",
    "prompt": "Минималистичный streetwear-логотип с чёрным котом и узбекским орнаментом",
    "model": "gpt-image-1-mini",
    "image_url": "http://localhost:8000/storage/generated-prints/example.png",
    "asset": {
      "type": "generated_image",
      "file_name": "ai-print-1.png",
      "mime_type": "image/png"
    }
  }
}
```

Ошибка валидации: `422 Unprocessable Entity`

```json
{
  "message": "The prompt field is required.",
  "errors": {
    "prompt": ["The prompt field is required."]
  }
}
```

Нет OpenAI API key: `503 Service Unavailable`

```json
{
  "message": "AI print generation is not configured."
}
```

Ошибка OpenAI: `502 Bad Gateway`

```json
{
  "message": "AI print generation failed."
}
```

Превышен дневной лимит guest-пользователя: `429 Too Many Requests`

```json
{
  "message": "Daily AI print generation limit reached."
}
```

## TDD-задачи

### Задача 1: Регистрация API route

Красный тест:

- `POST /api/generated-prints` не должен возвращать 404.
- Без обязательных полей endpoint должен вернуть 422.

Ожидаемое первое падение:

- Route ещё не существует, ответ будет 404.

Зелёная реализация:

- Создать `routes/api.php`.
- Зарегистрировать API routing в `bootstrap/app.php`.
- Добавить route `POST /generated-prints`.

Команда проверки:

```bash
rtk php artisan test --filter=GeneratedPrintApiTest
```

### Задача 2: Валидация request

Красные тесты:

- Отклонять отсутствующий `prompt`.
- Отклонять пустой `prompt`.
- Отклонять `prompt` длиннее 250 символов.
- Отклонять некорректный `reference_image`.
- Принимать PNG/JPEG/WebP data URL в `reference_image`.

Ожидаемое первое падение:

- Request validation ещё не существует или route принимает некорректный payload.

Зелёная реализация:

- Создать `StoreGeneratedPrintRequest`.
- Использовать существующий `App\Support\DataUrlImage` для проверки data URL.
- Ограничить размер binary reference image. Рекомендуемый MVP-лимит: 5 MB.

Команда проверки:

```bash
rtk php artisan test --filter=GeneratedPrintApiTest
```

### Задача 3: Fake driver для dev/test

Красные тесты:

- При `config(['services.openai.image_driver' => 'fake'])` API возвращает 201 без OpenAI API key.
- HTTP-запрос в OpenAI не выполняется.
- Generated image сохраняется в public storage.
- Response содержит `data.model = fake-image-generator`.

Ожидаемое первое падение:

- Сервис генерации не существует или всегда требует OpenAI API key.

Зелёная реализация:

- Создать interface `ImageGenerator`.
- Создать `FakeImageGenerator`, который возвращает стабильную PNG-картинку из локального base64 fixture/string.
- Добавить binding по config `services.openai.image_driver`.
- Для `fake` driver не проверять `OPENAI_API_KEY`.

Команда проверки:

```bash
rtk php artisan test --filter=GeneratedPrintApiTest
```

### Задача 4: Дневной лимит guest-пользователя

Красные тесты:

- Если у guest-пользователя уже есть 2 успешные генерации за сегодня, новый request возвращает 429.
- Если у guest-пользователя есть 1 успешная генерация за сегодня, новый request разрешён.
- Failed-генерации за сегодня не считаются в лимит.
- Генерации за вчера не считаются в сегодняшний лимит.

Ожидаемое первое падение:

- API не проверяет дневной лимит и разрешает третью генерацию.

Зелёная реализация:

- Добавить `guest_fingerprint` в `generated_prints`.
- Считать fingerprint из IP, user agent и текущей даты.
- До вызова OpenAI считать `completed` записи за сегодня по этому fingerprint.
- Если записей уже 2, вернуть `429`.

Команда проверки:

```bash
rtk php artisan test --filter=GeneratedPrintApiTest
```

### Задача 5: Успешная генерация через OpenAI driver с mocked OpenAI response

Красный тест:

- При `config(['services.openai.image_driver' => 'openai'])`, валидном prompt и fake OpenAI response с base64 PNG:
  - API возвращает 201.
  - Создаётся строка в `generated_prints`.
  - Generated image сохраняется в `Storage::disk('public')`.
  - JSON содержит `data.image_url`.

Ожидаемое первое падение:

- Нет модели/table/use case `GeneratedPrint`.

Зелёная реализация:

- Создать migration и model.
- Создать `CreateGeneratedPrintUseCase`.
- Создать `OpenAiImageGenerator`.
- В тесте использовать `Http::fake()`, без реального запроса в OpenAI.

Команда проверки:

```bash
rtk php artisan test --filter=GeneratedPrintApiTest
```

### Задача 6: Сохранение reference image и payload для OpenAI

Красный тест:

- При валидном `reference_image`:
  - Reference image сохраняется в `generated-prints/references`.
  - `generated_prints.reference_image_path` заполнен.
  - OpenAI request содержит reference image в ожидаемом payload.

Ожидаемое первое падение:

- Reference image игнорируется.

Зелёная реализация:

- Распарсить и сохранить reference image до вызова OpenAI.
- Передать reference image content в image generator service.
- Сохранить metadata reference image.

Команда проверки:

```bash
rtk php artisan test --filter=GeneratedPrintApiTest
```

### Задача 7: Нет API key

Красный тест:

- При `AI_IMAGE_DRIVER=openai` и `config(['services.openai.api_key' => null])` валидный request возвращает 503.
- HTTP-запрос в OpenAI не выполняется.
- Успешная generated image не сохраняется.

Ожидаемое первое падение:

- Код пытается вызвать OpenAI без ключа или возвращает неправильный status code.

Зелёная реализация:

- Добавить guard на конфигурацию до вызова OpenAI.
- Вернуть понятную JSON-ошибку.

Команда проверки:

```bash
rtk php artisan test --filter=GeneratedPrintApiTest
```

### Задача 8: Обработка ошибок OpenAI

Красный тест:

- Если OpenAI возвращает 500 или malformed JSON:
  - API возвращает 502.
  - Сохраняется строка `generated_prints` со статусом `failed`.
  - Generated image path не возвращается.

Рекомендуемое MVP-поведение:

- Сохранять failed row с `status = failed` и `error_message`.

Ожидаемое первое падение:

- Exception протекает наружу как 500 или тест получает нестабильную ошибку.

Зелёная реализация:

- Поймать исключения OpenAI client в use case/controller.
- Сохранить failed generation record.
- Вернуть `502` со стабильным message.

Команда проверки:

```bash
rtk php artisan test --filter=GeneratedPrintApiTest
```

### Задача 9: Полный regression run

Запустить:

```bash
rtk php artisan test
```

Ожидаемо:

- Существующие тесты заказов продолжают проходить.
- Новые тесты API генерации проходят.

## Критерии приёмки

API можно считать готовым, когда:

- `POST /api/generated-prints` существует.
- Валидация prompt работает.
- Prompt ограничен 250 символами.
- Валидация optional reference image работает.
- Guest-пользователь может сделать максимум 2 успешные генерации в день.
- В local/testing можно генерировать через fake driver без OpenAI и без сети.
- OpenAI вызывается только когда выбран `AI_IMAGE_DRIVER=openai`.
- OpenAI API key используется только на backend.
- Тесты не вызывают реальный OpenAI API.
- Generated image сохраняется на public disk.
- Response отдаёт frontend image URL и asset metadata.
- Ошибки конфигурации и upstream ошибки возвращают стабильный JSON.
- Полный test suite проходит.

## Следующий отдельный этап

После approval и реализации API нужно сделать отдельный TDD-план для UI конструктора:

- Добавить панель “AI-принт”.
- Дать пользователю ввести prompt.
- Дать пользователю загрузить logo/reference image.
- Показать loading/error/success states.
- Добавить generated image в существующую систему canvas-слоёв.
- Включить generated image в текущий payload заявки.
