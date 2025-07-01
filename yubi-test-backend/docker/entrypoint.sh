#!/bin/bash

echo "Checking database connection..."
while ! mysqladmin ping -h"db" -u"crm_user" -p"crm_password" --silent; do
    echo "Waiting for database connection..."
    sleep 2
done
echo "Database is connected!"

echo "Updating storage permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Running database migrations and seeders..."
php artisan migrate --seed --force

echo "Startup script finished. Starting PHP-FPM..."
exec "$@"