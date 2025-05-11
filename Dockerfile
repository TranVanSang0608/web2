FROM php:8.1-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev dos2unix nginx \
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
# Build frontend assets
RUN npm install && npm run build
# Make sure assets directory exists and is writable
RUN mkdir -p public/build/assets && \
    chmod -R 775 public/build && \
    chown -R www-data:www-data public/build

# Add node-sass for CSS extraction
RUN npm install -g node-sass
# Copy and run the CSS extraction script
COPY extract-css.sh /usr/local/bin/extract-css.sh
RUN chmod +x /usr/local/bin/extract-css.sh && \
    dos2unix /usr/local/bin/extract-css.sh && \
    /usr/local/bin/extract-css.sh

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

# Set up Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Copy and make the entrypoint script executable
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
COPY fix-assets.sh /www/fix-assets.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh && \
    dos2unix /usr/local/bin/docker-entrypoint.sh || true && \
    chmod +x /www/fix-assets.sh && \
    dos2unix /www/fix-assets.sh || true

EXPOSE 8000

# Create supervisor configuration to run both php-fpm and nginx
RUN apt-get update && apt-get install -y supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
