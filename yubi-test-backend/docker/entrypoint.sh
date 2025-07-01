#!/bin/bash

echo "Waiting for database connection..."
while ! mysqladmin ping -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent; do
    sleep 2
done
echo "Database is connected!"

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Running optimizations and migrations..."
php artisan optimize:clear
php artisan migrate --seed --force

echo "Startup script finished. Starting PHP-FPM..."
exec "$@"
