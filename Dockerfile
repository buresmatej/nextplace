# --- KROK 1: Stáhneme závislosti pomocí Composeru ---
FROM composer:2 AS composer_stage
WORKDIR /app
COPY composer.json composer.lock ./
# Stáhneme vendor (bez dev závislostí pro rychlost a čistotu)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# --- KROK 2: Finální PHP obraz ---
FROM php:8.4-fpm-alpine

RUN apk add --no-cache libpq-dev && \
    docker-php-ext-install pdo_pgsql pgsql

WORKDIR /app

# Kopírujeme kód tvé aplikace
COPY . .

# Kopírujeme SLOŽKU VENDOR z prvního kroku
COPY --from=composer_stage /app/vendor ./vendor

# Konfigurace FPM (stejná jako minule)
RUN \
    sed -i '/^pid =/d' /usr/local/etc/php-fpm.conf && \
    sed -i '/^error_log =/d' /usr/local/etc/php-fpm.conf && \
    sed -i '1i [global]\npid = /tmp/php-fpm.pid\nerror_log = /tmp/php-fpm.log' /usr/local/etc/php-fpm.conf && \
    sed -i 's/^user =.*/user = root/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/^group =.*/group = root/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i '/^access.log =/d' /usr/local/etc/php-fpm.d/*.conf && \
    sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/g' /usr/local/etc/php-fpm.d/www.conf

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]