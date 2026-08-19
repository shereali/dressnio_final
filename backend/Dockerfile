FROM php:8.2-fpm-alpine
WORKDIR /var/www/html
RUN apk add --no-cache git curl zip unzip nodejs npm \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd bcmath pcntl
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-interaction
COPY . .
RUN cp .env.example .env 2>/dev/null || true \
    && php artisan key:generate --force \
    && php artisan storage:link 2>/dev/null || true \
    && chown -R www-data:www-data storage bootstrap/cache
EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000
