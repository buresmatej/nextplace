#!/bin/sh
set -ex

echo "Waiting for Postgres..."
# Test připojení pomocí PHP PDO k Postgresu
until php -r "new PDO('pgsql:host=db;port=5432;dbname=nette_db', 'root', 'root');" 2>/dev/null; do
  echo "Postgres not ready..."
  sleep 2
done

echo "Postgres ready, executing migrations..."
php bin/console migrations:reset --no-interaction

# Bezpečné smazání obsahu tempu bez smazání samotné složky (přidáno || true pro ignorování případných chyb)
rm -rf /var/www/html/temp/* || true

exec php-fpm