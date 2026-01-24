FROM php:8.3-cli

# System deps + PHP extensions commonly needed by Laravel & Composer
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev \
    libsqlite3-dev \
    libicu-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo pdo_sqlite \
        mbstring \
        intl \
        zip \
        gd \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Install deps
RUN composer install --no-dev --optimize-autoloader

# Start app
CMD sh -c "php artisan migrate --force || true; php artisan serve --host 0.0.0.0 --port $PORT"
