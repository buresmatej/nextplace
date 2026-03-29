FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction

FROM php:8.4-fpm
WORKDIR /var/www/html

RUN docker-php-ext-install pdo pdo_mysql mysqli

COPY . .
COPY --from=vendor /app/vendor ./vendor

RUN mkdir -p /var/www/html/temp /var/www/html/log \
    && chmod -R 777 /var/www/html/temp \
    && chmod -R 777 /var/www/html/log

# fix setgid problému ze serveru
RUN sed -i 's/user = www-data/user = nobody/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/group = www-data/group = nogroup/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/g' /usr/local/etc/php-fpm.d/www.conf

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]