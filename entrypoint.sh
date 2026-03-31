#!/bin/sh
set -ex

# Příprava složek v jediném povoleném místě
mkdir -p /tmp/nette_temp /tmp/nette_log
chmod -R 777 /tmp

# PHP log
touch /tmp/php-fpm.log && chmod 777 /tmp/php-fpm.log

echo "Waiting for Postgres..."
until php -r "new PDO('pgsql:host=db;port=5432;dbname=nette_db', 'root', 'root');" 2>/dev/null; do
  echo "Postgres not ready..."
  sleep 2
done


echo "Postgres ready, executing migrations..."
php bin/console migrations:reset --no-interaction

# Spuštění FPM (příznak -R povolí běh pod rootem v configu)
exec php-fpm -R