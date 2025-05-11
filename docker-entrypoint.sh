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

# Run the asset fix script if it exists
if [ -f "/www/fix-assets.sh" ]; then
    chmod +x /www/fix-assets.sh
    /www/fix-assets.sh
fi

# Link storage folder (fallback)
php artisan storage:link || true

# Optimize for production
if [[ "$APP_ENV" == "production" ]]; then
    php artisan optimize:clear
    php artisan optimize
    php artisan route:cache
    php artisan config:cache 
    php artisan view:cache
fi

# Execute the main process
exec "$@"
