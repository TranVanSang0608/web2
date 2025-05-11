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

# Clear Laravel cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Start Laravel server
exec php artisan serve --host=0.0.0.0 --port=8000

# Execute CMD
exec "$@"
