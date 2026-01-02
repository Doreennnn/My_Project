#!/usr/bin/env sh
set -e

echo "==> Preparing writable directories"
mkdir -p storage bootstrap/cache database
chmod -R 775 storage bootstrap/cache database || true

# Ensure sqlite file exists if using sqlite
if [ "${DB_CONNECTION}" = "sqlite" ]; then
  if [ -z "${DB_DATABASE}" ]; then
    export DB_DATABASE="/var/www/html/database/database.sqlite"
  fi
  mkdir -p "$(dirname "$DB_DATABASE")"
  touch "$DB_DATABASE"
fi

echo "==> Clearing caches"
php artisan optimize:clear || true

echo "==> Running migrations"
php artisan migrate --force

echo "==> Seeding database (safe)"
php artisan db:seed --force || true

echo "==> Starting Apache"
exec apache2-foreground
