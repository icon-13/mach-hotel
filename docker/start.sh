#!/bin/sh
set -e

: "${PORT:=10000}"

# Nginx config
sed "s/__PORT__/${PORT}/g" /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

# Ensure writable dirs for Laravel
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs database || true
chown -R www-data:www-data storage bootstrap/cache database || true
chmod -R 775 storage bootstrap/cache database || true

# Fresh demo DB each deploy (optional but keeps it clean)
rm -f database/database.sqlite || true
touch database/database.sqlite
chown www-data:www-data database/database.sqlite || true
chmod 664 database/database.sqlite || true

# Logs
touch storage/logs/laravel.log || true
chown www-data:www-data storage/logs/laravel.log || true
chmod 664 storage/logs/laravel.log || true

# Run migrations as www-data (avoids root-owned sqlite)
su -s /bin/sh www-data -c "php artisan migrate --force" || true

# Clear caches (safe)
su -s /bin/sh www-data -c "php artisan config:clear" || true
su -s /bin/sh www-data -c "php artisan cache:clear" || true
su -s /bin/sh www-data -c "php artisan view:clear" || true
su -s /bin/sh www-data -c "php artisan route:clear" || true

echo "---- Last Laravel log ----"
tail -n 120 storage/logs/laravel.log || true

exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
