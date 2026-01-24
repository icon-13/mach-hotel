#!/bin/sh
set -e

: "${PORT:=10000}"

sed "s/__PORT__/${PORT}/g" /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

rm -f database/database.sqlite || true
touch database/database.sqlite

php artisan migrate --force || true

php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
