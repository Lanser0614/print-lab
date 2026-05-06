#!/usr/bin/env bash
set -Eeuo pipefail

DOMAIN="${DOMAIN:-printlab.uz}"
WWW_DOMAIN="${WWW_DOMAIN:-www.${DOMAIN}}"
ENABLE_WWW="${ENABLE_WWW:-true}"
DEPLOY_PATH="${DEPLOY_PATH:-/var/www/printlab}"
APP_HOST="${APP_HOST:-127.0.0.1}"
APP_PORT="${APP_PORT:-8080}"
EMAIL="${EMAIL:-admin@${DOMAIN}}"
NGINX_SITE_NAME="${NGINX_SITE_NAME:-printlab}"
CERTBOT_REINSTALL="${CERTBOT_REINSTALL:-false}"

if [[ "${EUID}" -eq 0 ]]; then
    SUDO=""
else
    SUDO="sudo"
fi

log() {
    printf '[setup-domain] %s\n' "$*"
}

require_file() {
    local path="$1"

    if [[ ! -f "${path}" ]]; then
        printf 'Required file not found: %s\n' "${path}" >&2
        exit 1
    fi
}

upsert_env() {
    local file="$1"
    local key="$2"
    local value="$3"
    local escaped

    escaped="$(printf '%s' "${value}" | sed -e 's/[\/&]/\\&/g')"

    if grep -qE "^${key}=" "${file}"; then
        sed -i.bak -E "s/^${key}=.*/${key}=${escaped}/" "${file}"
    else
        printf '%s=%s\n' "${key}" "${value}" >> "${file}"
    fi
}

server_names() {
    if [[ "${ENABLE_WWW}" == "true" ]]; then
        printf '%s %s' "${DOMAIN}" "${WWW_DOMAIN}"
    else
        printf '%s' "${DOMAIN}"
    fi
}

certbot_domains() {
    if [[ "${ENABLE_WWW}" == "true" ]]; then
        printf -- '-d %s -d %s' "${DOMAIN}" "${WWW_DOMAIN}"
    else
        printf -- '-d %s' "${DOMAIN}"
    fi
}

require_file "${DEPLOY_PATH}/.env"
require_file "${DEPLOY_PATH}/docker-compose.prod.yml"

if [[ ! -f "${DEPLOY_PATH}/.deploy.env" ]]; then
    log "Warning: ${DEPLOY_PATH}/.deploy.env does not exist yet. Run the GitHub Deploy Production workflow once before restarting the stack from this script."
fi

log "Updating ${DEPLOY_PATH}/.env for ${DOMAIN}"
upsert_env "${DEPLOY_PATH}/.env" "APP_URL" "https://${DOMAIN}"
upsert_env "${DEPLOY_PATH}/.env" "HTTP_PORT" "${APP_HOST}:${APP_PORT}"

if [[ -f "${DEPLOY_PATH}/.deploy.env" ]]; then
    log "Restarting Docker frontend on ${APP_HOST}:${APP_PORT}"
    (
        cd "${DEPLOY_PATH}"
        docker compose --env-file .env --env-file .deploy.env -f docker-compose.prod.yml up -d nginx
    )
else
    log "Skipping Docker restart because .deploy.env is missing."
fi

if ! command -v nginx >/dev/null 2>&1 || ! command -v certbot >/dev/null 2>&1 || ! dpkg -s python3-certbot-nginx >/dev/null 2>&1; then
    log "Installing Nginx and Certbot packages"
    ${SUDO} apt-get update
    ${SUDO} apt-get install -y --no-install-recommends nginx certbot python3-certbot-nginx
else
    log "Nginx and Certbot are already installed"
fi

if command -v ufw >/dev/null 2>&1; then
    log "Allowing HTTP/HTTPS through UFW"
    ${SUDO} ufw allow 'Nginx Full' || true
fi

log "Writing Nginx reverse proxy config"
${SUDO} tee "/etc/nginx/sites-available/${NGINX_SITE_NAME}" >/dev/null <<NGINX
server {
    listen 80;
    server_name $(server_names);

    client_max_body_size 25m;

    location / {
        proxy_pass http://${APP_HOST}:${APP_PORT};
        proxy_http_version 1.1;
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_read_timeout 90;
    }
}
NGINX

${SUDO} ln -sfn "/etc/nginx/sites-available/${NGINX_SITE_NAME}" "/etc/nginx/sites-enabled/${NGINX_SITE_NAME}"
${SUDO} rm -f /etc/nginx/sites-enabled/default
${SUDO} nginx -t
${SUDO} systemctl reload nginx

if [[ "${CERTBOT_REINSTALL}" == "true" || ! -d "/etc/letsencrypt/live/${DOMAIN}" ]]; then
    log "Requesting Let's Encrypt certificate"
    # shellcheck disable=SC2046
    ${SUDO} certbot --nginx $(certbot_domains) \
        --non-interactive \
        --agree-tos \
        --redirect \
        --email "${EMAIL}"
else
    log "Let's Encrypt certificate already exists for ${DOMAIN}"
    ${SUDO} certbot renew --quiet || true
fi

${SUDO} nginx -t
${SUDO} systemctl reload nginx

log "Done. Open https://${DOMAIN}"
