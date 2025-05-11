#!/bin/bash

# Fix assets for Render deployment

# Create storage link if not exists
php artisan storage:link || true

# Make sure public directory is writable
chmod -R 755 public

# Fix Vite manifest if it exists
if [ -f "public/build/manifest.json" ]; then
  echo "Fixing Vite manifest..."
  # Make sure manifest.json permissions are correct
  chmod 644 public/build/manifest.json
fi

# Generate app key if needed
if [ -z "$(grep -E '^APP_KEY=' .env | grep -v '=$')" ]; then
  php artisan key:generate
fi

# Clear and cache for production
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear

if [[ "$APP_ENV" == "production" ]]; then
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
fi

echo "Assets fixed for Render deployment!"
