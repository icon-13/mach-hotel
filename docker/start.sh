#!/bin/sh
set -e

: "${PORT:=10000}"

# Nginx config
sed "s/__PORT__/${PORT}/g" /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

# Ensure writable dirs
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs database || true
chown -R www-data:www-data storage bootstrap/cache database || true
chmod -R 775 storage bootstrap/cache database || true

# Fresh sqlite each deploy (demo)
rm -f database/database.sqlite || true
touch database/database.sqlite
chown www-data:www-data database/database.sqlite || true
chmod 664 database/database.sqlite || true

# Migrate + Seed as www-data
su -s /bin/sh www-data -c "php artisan migrate --force" || true
su -s /bin/sh www-data -c "php artisan db:seed --force" || true

# Clear caches
su -s /bin/sh www-data -c "php artisan config:clear" || true
su -s /bin/sh www-data -c "php artisan route:clear" || true
su -s /bin/sh www-data -c "php artisan view:clear" || true
su -s /bin/sh www-data -c "php artisan cache:clear" || true

# Quick check: confirm Vite build output exists
echo "---- Asset check ----"
ls -la public/build || true
ls -la public/build/assets || true

exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
