#!/bin/bash

# Copy .env file
cp .env.example .env

# Generate application key if not present
if grep -q "APP_KEY=base64:" .env; then
    echo "App key exists, skipping..."
else
    # Generate the app key
    php artisan key:generate
fi

# Set other environment configs
php artisan config:cache
php artisan route:cache
php artisan view:cache
