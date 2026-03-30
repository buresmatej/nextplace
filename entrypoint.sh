#!/bin/sh
set -ex

# Musíme vytvořit složku v /tmp ručně pro jistotu před spuštěním konzole
mkdir -p /tmp/nette_temp /tmp/nette_log
chmod -R 777 /tmp/nette_temp /tmp/nette_log

echo "Waiting for Postgres..."
until php -r "new PDO('pgsql:host=db;port=5432;dbname=nette_db', 'root', 'root');" 2>/dev/null; do
  echo "Postgres not ready..."
  sleep 2
done

echo "Postgres ready, executing migrations..."
# Spustíme migrace (použijí temp v /tmp díky změně v Bootstrapu)
php bin/console migrations:reset --no-interaction

# Promazání cache v /tmp (nepovinné, ale čistší)
rm -rf /tmp/nette_temp/* || true

exec php-fpm