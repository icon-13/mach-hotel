FROM php:8.3-fpm

# System deps
RUN apt-get update && apt-get install -y \
    nginx supervisor git unzip zip libzip-dev \
    libsqlite3-dev libicu-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo pdo_sqlite mbstring intl zip gd \
    && rm -rf /var/lib/apt/lists/*

# ---- Node.js (for Vite build) ----
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# Install JS deps & build assets
RUN npm install && npm run build

# Permissions (fixes 500 + asset access)
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 storage bootstrap/cache

# Nginx + supervisor
COPY docker/nginx.conf.template /etc/nginx/templates/default.conf.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 10000
CMD ["/start.sh"]
