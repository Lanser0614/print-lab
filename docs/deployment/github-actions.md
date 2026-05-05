# GitHub Actions Docker Deploy

Build runs automatically on pull requests and pushes to `master`.

Production deploy is manual from the GitHub Actions tab: open `Deploy Production`, click `Run workflow`, and choose the image tag.

The automatic `Build` workflow builds two Docker images and pushes them to GitHub Container Registry only after a push to `master`:

- `app`: PHP-FPM Laravel runtime.
- `nginx`: Nginx with compiled `public/` assets.

Pull request builds run tests and Docker builds, but do not push images.

The manual `Deploy Production` workflow connects to the server over SSH, pulls the selected image tags, starts the Docker Compose production stack, and runs Laravel deployment commands inside the `app` container.

The workflow writes `.deploy.env` on the server with the exact image tags used by the current deployment.

Use `latest` to deploy the newest successful `master` build, or paste a commit SHA to deploy a specific image tag.

## Required GitHub Secrets

Add these in GitHub: `Settings -> Secrets and variables -> Actions -> New repository secret`.

- `DEPLOY_HOST`: server hostname or IP address.
- `DEPLOY_USERNAME`: SSH username.
- `DEPLOY_PASSWORD`: SSH password.
- `DEPLOY_PATH`: absolute path to the Laravel project on the server, for example `/var/www/printlab`.
- `DEPLOY_PORT`: SSH port. Optional if the server uses port `22`.
- `GHCR_USERNAME`: GitHub username that can read the GHCR package.
- `GHCR_TOKEN`: GitHub token with package read access.

## Server Requirements

The server must already have:

- Docker Engine with the Docker Compose plugin.
- A configured `.env` file in `DEPLOY_PATH`.
- `DB_PASSWORD` and `MYSQL_ROOT_PASSWORD` set in `.env`.
- Port `80` available, or `HTTP_PORT` set in `.env`.

The server does not need PHP, Composer, Node, or Nginx installed directly.

## Required Server `.env`

Keep the production `.env` only on the server. At minimum, set:

```dotenv
APP_NAME=PrintLab
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://example.com

DB_CONNECTION=mysql
DB_DATABASE=printlab
DB_USERNAME=printlab
DB_PASSWORD=change-me
MYSQL_ROOT_PASSWORD=change-root-password

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
REDIS_CLIENT=phpredis
```

Generate `APP_KEY` once with:

```bash
docker compose -f docker-compose.prod.yml exec app php artisan key:generate --show
```

The workflow does not upload `.env`, `.env.*`, `node_modules`, `vendor`, or runtime storage files. Runtime uploads and Laravel storage are kept in the `storage-data` Docker volume.

To manage the production stack manually on the server, use both env files:

```bash
docker compose --env-file .env --env-file .deploy.env -f docker-compose.prod.yml ps
docker compose --env-file .env --env-file .deploy.env -f docker-compose.prod.yml logs -f
```
