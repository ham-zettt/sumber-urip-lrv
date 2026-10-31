#!/bin/sh
set -e

echo "Starting Sumber Urip application initialization on Render..."

# Replace listen port in nginx config if PORT is assigned by Render
PORT="${PORT:-80}"
echo "Configuring Nginx to listen on port ${PORT}..."
sed -i "s/listen 80;/listen ${PORT};/g" /etc/nginx/conf.d/default.conf

# Ensure storage directories exist and have proper permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate APP_KEY if missing
if [ -z "$APP_KEY" ]; then
    echo "Warning: APP_KEY is empty. Generating temporary key..."
    php artisan key:generate --force
fi

# Cache configuration, routes, and views for production performance
echo "Caching configuration, routes, and views..."
php artisan config:cache || echo "Config cache skipped"
php artisan route:cache || echo "Route cache skipped"
php artisan view:cache || echo "View cache skipped"

# Run database migrations
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || echo "Migration failed or skipped"
fi

# Optional: seed database if SEED_ON_DEPLOY is enabled
if [ "${SEED_ON_DEPLOY:-false}" = "true" ]; then
    echo "Seeding initial database data..."
    php artisan db:seed --force || echo "Seeding skipped or failed"
fi

echo "Starting PHP-FPM and Nginx services..."
php-fpm -D
exec nginx -g "daemon off;"
