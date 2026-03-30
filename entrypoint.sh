#!/bin/sh
set -ex


echo "Setting rights..."
chown -R nobody:nogroup /var/www/html/temp
chown -R nobody:nogroup /var/www/html/log
chmod -R 777 /var/www/html/temp
chmod -R 777 /var/www/html/log

echo "waiting for db..."
until php -r "new mysqli('db', 'root', 'root', 'nette_db');" 2>/dev/null; do
  echo "MySQL not ready..."
  sleep 2
done

echo "MySQL ready, executing migrations..."
php bin/console migrations:reset --no-interaction
rm -rf /var/www/html/temp/cache

exec php-fpm