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

# Kompletní očista konfigurace
RUN \
    # 1. Globální konfigurace (PID a logy do /tmp)
    sed -i '/^pid =/d' /usr/local/etc/php-fpm.conf && \
    sed -i '/^error_log =/d' /usr/local/etc/php-fpm.conf && \
    sed -i '1i [global]\npid = /tmp/php-fpm.pid\nerror_log = /tmp/php-fpm.log' /usr/local/etc/php-fpm.conf && \
    \
    # 2. Pool konfigurace (www.conf): Nastavíme uživatele na root
    # FPM to vyžaduje v configu, i když to pak server ignoruje
    sed -i 's/^user =.*/user = root/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/^group =.*/group = root/g' /usr/local/etc/php-fpm.d/www.conf && \
    \
    # 3. Vyčištění logů a nastavení listen
    sed -i '/^access.log =/d' /usr/local/etc/php-fpm.d/*.conf && \
    sed -i '/^error_log =/d' /usr/local/etc/php-fpm.d/*.conf && \
    sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/g' /usr/local/etc/php-fpm.d/www.conf

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]