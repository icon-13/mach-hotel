#!/bin/sh
set -e

# Render provides $PORT
: "${PORT:=10000}"

# Generate nginx config from template (insert PORT)
sed "s/__PORT__/${PORT}/g" /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

# Demo-friendly: reset sqlite each deploy (avoids half-migrated DB)
rm -f database/database.sqlite || true
touch database/database.sqlite

# Ensure Laravel can write logs (common 500 fix)
mkdir -p storage/logs || true
touch storage/logs/laravel.log || true
chmod -R 775 storage bootstrap/cache storage/logs || true

# Run migrations
php artisan migrate --force || true

# Clear caches (common 500 fix)
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

# Print last Laravel logs to Render logs (so 500 is visible)
echo "---- Last Laravel log ----"
tail -n 120 storage/logs/laravel.log || true

# Start supervisor (runs nginx + php-fpm)
exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
