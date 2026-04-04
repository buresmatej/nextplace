#!/bin/sh
set -ex

echo "=== ALL ENV VARS ==="
printenv
echo "===================="

mkdir -p /tmp/nette_temp /tmp/nette_log
chmod -R 777 /tmp

touch /tmp/php-fpm.log && chmod 777 /tmp/php-fpm.log

echo "Waiting for Postgres..."
until php -r "new PDO('pgsql:host=db;port=5432;dbname=nette_db', 'root', 'root');" 2>/dev/null; do
  echo "Postgres not ready..."
  sleep 2
done

echo "Postgres ready, executing migrations..."
php bin/console migrations:reset --no-interaction

exec php-fpm -R