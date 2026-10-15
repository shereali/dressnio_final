# Laravel backend (PHP-FPM + Nginx) multi-stage build
FROM composer:2 AS vendor
WORKDIR /app
COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-reqs

FROM php:8.3-fpm-alpine
RUN apk add --no-cache nginx mysql-client icu-dev libpng-dev libjpeg-turbo-dev freetype-dev curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql opcache intl gd bcmath pcntl

WORKDIR /var/www/html
COPY backend/. .
COPY --from=vendor /app/vendor ./vendor
COPY backend/deploy/nginx.conf /etc/nginx/http.d/default.conf
COPY backend/deploy/entrypoint.sh /entrypoint.sh
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache storage/logs \
    && chmod +x /entrypoint.sh \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
