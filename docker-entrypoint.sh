#!/bin/bash
set -e

# Check if .env exists, if not, copy from .env.example
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate Laravel application key if not set
if [ -z "$(grep -E '^APP_KEY=' .env | grep -v '=$')" ]; then
    php artisan key:generate
fi

# Ensure storage directory has right permissions
chmod -R 777 /www/storage || true

# Clear Laravel cache
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

# Start Laravel server
exec php artisan serve --host=0.0.0.0 --port=8000

# Execute CMD
exec "$@"
