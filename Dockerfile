FROM php:8.4-fpm
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN mkdir -p /var/www/data && chmod -R 777 /var/www/data