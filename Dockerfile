FROM composer:2 AS composer_stage
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

FROM php:8.4-fpm-alpine

RUN apk add --no-cache libpq-dev && \
    docker-php-ext-install pdo_pgsql pgsql

WORKDIR /app

COPY . .

COPY --from=composer_stage /app/vendor ./vendor

RUN \
    sed -i '/^pid =/d' /usr/local/etc/php-fpm.conf && \
    sed -i '/^error_log =/d' /usr/local/etc/php-fpm.conf && \
    sed -i '1i [global]\npid = /tmp/php-fpm.pid\nerror_log = /tmp/php-fpm.log' /usr/local/etc/php-fpm.conf && \
    sed -i 's/^user =.*/user = root/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/^group =.*/group = root/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i '/^access.log =/d' /usr/local/etc/php-fpm.d/*.conf && \
    sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/g' /usr/local/etc/php-fpm.d/www.conf && \
    echo 'clear_env = no' >> /usr/local/etc/php-fpm.d/www.conf

COPY docker/fpm-env.conf /usr/local/etc/php-fpm.d/zzz-env.conf

COPY entrypoint.sh /entrypoint.sh
RUN sed -i 's/\r//' /entrypoint.sh && chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]