FROM php:8.1-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev dos2unix \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Node.js and npm
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /www

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN npm install && npm run build

# Laravel permissions and key generation
RUN php artisan key:generate --force \
    && php artisan cache:clear || true \
    && php artisan config:clear || true \
    && mkdir -p /www/storage/framework/cache/data \
    && mkdir -p /www/storage/framework/sessions \
    && mkdir -p /www/storage/framework/views \
    && mkdir -p /www/storage/logs \
    && chmod -R 777 /www/storage \
    && chown -R www-data:www-data /www

# Copy and make the entrypoint script executable
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh && \
    dos2unix /usr/local/bin/docker-entrypoint.sh || true

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]
