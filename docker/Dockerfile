FROM php:8.3-cli

ARG UID=1000
ARG GID=1000

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        git \
        unzip \
        libicu-dev \
        libzip-dev \
        default-mysql-client \
    && docker-php-ext-install intl pdo_mysql zip bcmath pcntl \
    && pecl install redis pcov \
    && docker-php-ext-enable redis pcov \
    && { \
        echo 'post_max_size=64M'; \
        echo 'upload_max_filesize=64M'; \
        echo 'memory_limit=256M'; \
    } > /usr/local/etc/php/conf.d/zz-printlab-limits.ini \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get update \
    && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN groupadd --gid "${GID}" app \
    && useradd --uid "${UID}" --gid app --create-home --shell /bin/bash app

USER app

EXPOSE 8000 5173
