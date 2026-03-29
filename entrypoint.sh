#!/bin/sh

echo "Setting rights..."
chmod -R 777 /var/www/html/temp
chmod -R 777 /var/www/html/log

echo "waiting for db..."
until php -r "new mysqli('db', 'root', 'root', 'nette_db');" 2>/dev/null; do
  echo "MySQL not ready..."
  sleep 2
done

echo "MySQL ready, executing migrations..."
php bin/console migrations:reset --no-interaction

exec php-fpm