#!/bin/sh
set -ex

mkdir -p /tmp/nette_temp /tmp/nette_log
chmod -R 777 /tmp
touch /tmp/php-fpm.log && chmod 777 /tmp/php-fpm.log

# Explicitně předej proměnné do PHP-FPM poolu
echo "env[OPENAI_BASE_URL] = $OPENAI_BASE_URL" >> /usr/local/etc/php-fpm.d/www.conf
echo "env[OPENAI_API_KEY] = $OPENAI_API_KEY" >> /usr/local/etc/php-fpm.d/www.conf
echo "env[AI_MODEL] = $AI_MODEL" >> /usr/local/etc/php-fpm.d/www.conf
echo "env[PORT] = $PORT" >> /usr/local/etc/php-fpm.d/www.conf

echo "Waiting for Postgres..."
until php -r "new PDO('pgsql:host=db;port=5432;dbname=nette_db', 'root', 'root');" 2>/dev/null; do
  echo "Postgres not ready..."
  sleep 2
done

echo "Postgres ready, executing migrations..."
php bin/console migrations:reset --no-interaction

exec php-fpm -R