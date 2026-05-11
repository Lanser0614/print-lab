# PrintLab

PrintLab — Laravel 12 + Filament приложение для продажи товаров с индивидуальной печатью. Проект объединяет публичный каталог, мультиязычные страницы RU/UZ, Canvas-конструктор, AI Print Studio, Telegram-авторизацию, поток заявок и админ-панель для обработки макетов.

## Что уже есть

- Публичный каталог товаров, категорий и готовых принтов.
- URL-локализация через префиксы `/ru` и `/uz`; корневой `/` редиректит на выбранный язык.
- Страницы товара и печатного дизайна.
- Canvas-конструктор с mockup front/back, слоями текста/изображений/фигур, preview и print-only экспортом.
- AI Print Studio с handoff в конструктор и backend API для генерации изображений через fake/OpenAI driver.
- Flow заявок: клиент отправляет макет, backend сохраняет preview, print-only файл, assets, текстовые слои и canvas JSON.
- Telegram login flow, webhook и привязка гостевых заявок к пользователю после авторизации.
- Личный кабинет пользователя со списком и деталями заявок.
- Filament admin для товаров, категорий, готовых принтов и заявок.
- Google Tag Manager на публичных страницах через `GTM_CONTAINER_ID`; админка GTM не подключает.
- Docker development stack и production Docker/GitHub Actions deploy.

## Документация

- [docs/project-overview.md](docs/project-overview.md) — карта модулей, сценарии и основные файлы.
- [docs/deployment/github-actions.md](docs/deployment/github-actions.md) — production deploy через GitHub Actions и GHCR.
- [docs/superpowers/plans](docs/superpowers/plans) — планы прошлых задач: redesign, AI generation, Telegram auth, GTM, локализация.

## Быстрый старт в Docker

Поднять Laravel, Vite, MySQL и Redis:

```bash
docker compose up -d --build
```

Подготовить базу и seed data:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Админ-пользователь из seed:

```text
admin@printlab.test
password
```

Локальные URL:

- Laravel: `http://localhost:8000`
- Vite: `http://localhost:5173`
- Filament admin: `http://localhost:8000/admin`
- RU главная: `http://localhost:8000/ru`
- UZ главная: `http://localhost:8000/uz`

## Основные маршруты

- `/` — редирект на сохранённую локаль.
- `/{locale}` — главная.
- `/{locale}/catalog`, `/{locale}/catalog/t-shirts`, `/{locale}/catalog/mugs` — каталог.
- `/{locale}/products/{slug}` — товар.
- `/{locale}/prints/{design}` — публичная страница готового дизайна.
- `/{locale}/ai-studio/{slug}` — AI Print Studio.
- `/{locale}/constructor/{slug}` — основной конструктор.
- `/{locale}/constructor/v2/{slug}` — новая версия конструктора.
- `/{locale}/order-request/success` — успешная отправка заявки.
- `/{locale}/login` — Telegram login.
- `/{locale}/account` — личный кабинет.
- `/telegram/webhook` — Telegram webhook.
- `/admin` — Filament admin.

## Переменные окружения

Ключевые настройки уже есть в `.env.example`:

```dotenv
CONSTRUCTOR_V2_ENABLED=false
AI_IMAGE_DRIVER=fake
OPENAI_API_KEY=
OPENAI_IMAGE_MODEL=gpt-image-1-mini

GTM_CONTAINER_ID=

APP_LOCALE=ru
APP_FALLBACK_LOCALE=en

TELEGRAM_BOT_USERNAME=PrintLabUzBot
TELEGRAM_BOT_TOKEN=
TELEGRAM_WEBHOOK_SECRET=
TELEGRAM_AUTH_DRIVER=fake
TELEGRAM_LOGIN_TOKEN_TTL=300
```

Для local/testing окружения обычно используются `AI_IMAGE_DRIVER=fake` и `TELEGRAM_AUTH_DRIVER=fake`. Для staging/production переключайте драйверы на реальные значения и задавайте секреты вне репозитория.

## Команды разработки

```bash
docker compose exec app php artisan test
docker compose exec app ./vendor/bin/pint
docker compose exec app ./vendor/bin/phpstan analyse
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan migrate:fresh --seed
```

Frontend:

```bash
npm run dev
npm run build
```

Composer scripts для локального запуска без Docker:

```bash
composer run dev
composer run test
composer run pint
```

## Production

Production stack описан в `docker-compose.prod.yml`. GitHub Actions:

- `Build` запускается на pull requests и push в `master`, прогоняет проверки и собирает Docker images.
- `Deploy Production` запускается вручную, тянет образы из GHCR, обновляет Docker Compose stack и выполняет Laravel deploy-команды.

Детали секретов, server `.env`, домена и TLS описаны в [docs/deployment/github-actions.md](docs/deployment/github-actions.md).
