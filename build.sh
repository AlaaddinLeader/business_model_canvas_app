#!/usr/bin/env bash

# Install PostgreSQL development files
apt-get update
apt-get install -y libpq-dev

# Install PHP PostgreSQL extensions
docker-php-ext-install pdo pdo_pgsql pgsql

# Install Composer dependencies
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Cache Laravel configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chmod -R 775 storage bootstrap/cache

echo "Build completed successfully!"
