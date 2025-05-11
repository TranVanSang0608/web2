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

# Check if we're in production environment
if [[ "$APP_ENV" == "production" ]]; then
  # Create a basic CSS file if it doesn't exist
  mkdir -p /www/public/build/assets
  if [ ! -f "/www/public/build/assets/app.css" ]; then
    cp -f /www/resources/sass/app.scss /www/public/build/assets/app.css || true
  fi
  if [ ! -f "/www/public/build/assets/app.js" ]; then
    echo "// Placeholder JS" > /www/public/build/assets/app.js || true
  fi
  chmod -R 755 /www/public/build
fi

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
