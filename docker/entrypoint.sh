#!/bin/sh
set -e

echo "Starting Isalendar..."

# Wait for database to be ready
echo "Waiting for database..."
until php artisan db:show > /dev/null 2>&1; do
    echo "Database is unavailable - sleeping"
    sleep 2
done

echo "Database is up!"

# Wait for Redis to be ready
echo "Waiting for Redis..."
until php -r "try { (new Redis())->connect(getenv('REDIS_HOST') ?: '127.0.0.1', getenv('REDIS_PORT') ?: 6379); echo 'OK'; } catch (Exception \$e) { exit(1); }"; do
    echo "Redis is unavailable - sleeping"
    sleep 2
done

echo "Redis is up!"

# Run migrations
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Clear and cache configuration for production
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ensure storage directories have correct permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache

echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
