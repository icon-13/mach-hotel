#!/bin/sh
set -e
: "${PORT:=10000}"

# Nginx config
sed "s/__PORT__/${PORT}/g" /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

# Writable dirs
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs database || true
chown -R www-data:www-data storage bootstrap/cache database || true
chmod -R 775 storage bootstrap/cache database || true

# ✅ DO NOT DELETE DB (so admin changes can persist)
touch database/database.sqlite || true
chown www-data:www-data database/database.sqlite || true
chmod 664 database/database.sqlite || true

# Migrate
su -s /bin/sh www-data -c "php artisan migrate --force" || true

# ✅ Seed (SAFE because seeders won't overwrite existing rows)
su -s /bin/sh www-data -c "php artisan db:seed --force" || true

# Clear caches (prevents stale config/CSRF issues)
su -s /bin/sh www-data -c "php artisan config:clear" || true
su -s /bin/sh www-data -c "php artisan cache:clear" || true
su -s /bin/sh www-data -c "php artisan route:clear" || true
su -s /bin/sh www-data -c "php artisan view:clear" || true

exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
