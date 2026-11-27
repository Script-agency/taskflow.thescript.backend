FROM php:8.1-fpm

ARG BUILD_MODE=production

# системные зависимости
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    aria2 \
    p7zip-full \
    && docker-php-ext-install \
        pdo_mysql \
        intl \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . /app

# PHP-зависимости — с dev или без, в зависимости от BUILD_MODE
RUN if [ "$BUILD_MODE" = "dev" ]; then \
        composer install --no-interaction --optimize-autoloader --prefer-dist; \
    else \
        composer install --no-interaction --optimize-autoloader --no-dev --prefer-dist; \
    fi && composer clear-cache

# Writable application directories (a Compose bind mount may override these image permissions).

RUN mkdir -p /app/runtime /app/web/assets \
    && chown -R www-data:www-data /app/runtime /app/web/assets \
    && find /app/runtime /app/web/assets -type d -exec chmod 775 {} \; \
    && find /app/runtime /app/web/assets -type f -exec chmod 664 {} \;

USER www-data

EXPOSE 9000
CMD ["php-fpm"]