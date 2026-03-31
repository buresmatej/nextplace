#!/bin/sh
set -ex

# Vytvoření složek pro Nette v /tmp (jediné writable místo)
mkdir -p /tmp/nette_temp /tmp/nette_log /tmp/client_temp
# Nastavíme práva, aby na to viděl kdokoli (PHP i Nginx)
chmod -R 777 /tmp

# Pro jistotu vytvoříme i soubor pro logy PHP, aby si na něm FPM nevylámalo zuby
touch /tmp/php-fpm.log && chmod 777 /tmp/php-fpm.log

echo "Waiting for Postgres..."
until php -r "new PDO('pgsql:host=db;port=5432;dbname=nette_db', 'root', 'root');" 2>/dev/null; do
  echo "Postgres not ready..."
  sleep 2
done

echo "Postgres ready, executing migrations..."
# DŮLEŽITÉ: Ujisti se, že tvé Nette ví, že má používat /tmp/nette_temp!
php bin/console migrations:reset --no-interaction

exec php-fpm -R