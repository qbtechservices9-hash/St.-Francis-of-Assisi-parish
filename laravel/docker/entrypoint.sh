#!/bin/sh
set -e

# Render binds to the port defined by $PORT
if [ -n "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
fi

# Run database migrations if configured
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || true
fi

# Optimize Laravel cache for production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec apache2-foreground
