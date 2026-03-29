FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction

FROM php:8.4-fpm
WORKDIR /var/www/html

RUN docker-php-ext-install pdo pdo_mysql mysqli

COPY . .
COPY --from=vendor /app/vendor ./vendor

RUN mkdir -p /var/www/data \
    && chown -R www-data:www-data /var/www/data