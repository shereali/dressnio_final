#!/bin/sh
set -e

# Ensure storage and bootstrap directories exist
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache storage/logs
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Refresh package manifest for production
php artisan package:discover --ansi || true

# Safe, non-destructive migrations ONLY
php artisan migrate --force

# Production caching
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

php-fpm -D
nginx -g 'daemon off;'
