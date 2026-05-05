# PrintLab

Laravel + Filament сайт для продажи товаров с индивидуальной печатью, онлайн-конструктором и flow заявок.

## Документация

Обзор проекта и карта модулей:

- [docs/project-overview.md](docs/project-overview.md)

## Docker Development

Start Laravel, Vite, MySQL, and Redis:

```bash
docker compose up -d --build
```

URLs:

- Laravel: http://localhost:8000
- Vite: http://localhost:5173
- Filament admin: http://localhost:8000/admin

Seeded admin user:

```text
admin@printlab.test
password
```

Useful commands:

```bash
docker compose exec app php artisan test
docker compose exec app ./vendor/bin/pint
docker compose exec app ./vendor/bin/phpstan analyse
docker compose exec app php artisan migrate:fresh --seed
```
# print-lab
