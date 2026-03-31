FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction

FROM php:8.4-fpm
WORKDIR /var/www/html

# Instalace závislostí pro Postgres a rozšíření pdo_pgsql
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

COPY . .
COPY --from=vendor /app/vendor ./vendor


#RUN sed -i 's/user = www-data/user = nobody/g' /usr/local/etc/php-fpm.d/www.conf && \
#sed -i 's/group = www-data/group = nogroup/g' /usr/local/etc/php-fpm.d/www.conf && \
#    sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/g' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/g' /usr/local/etc/php-fpm.d/www.conf

COPY entrypoint.sh /entrypoint.sh
RUN sed -i 's/\r$//' /entrypoint.sh \
    && chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]