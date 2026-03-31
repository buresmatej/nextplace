FROM php:8.4-fpm-alpine

# 1. Instalace knihoven a obou ovladačů pro Postgres (PDO i základní pgsql pro migrace)
RUN apk add --no-cache libpq-dev && \
    docker-php-ext-install pdo_pgsql pgsql

WORKDIR /app

# 2. Kopírování kódu (předpokládáme, že vendor už máš v projektu nebo se buildí jinde)
COPY . .

# 3. Konfigurace PHP-FPM: Přesun PID a logů do /tmp a nastavení uživatele na root
RUN \
    sed -i '/^pid =/d' /usr/local/etc/php-fpm.conf && \
    sed -i '/^error_log =/d' /usr/local/etc/php-fpm.conf && \
    sed -i '1i [global]\npid = /tmp/php-fpm.pid\nerror_log = /tmp/php-fpm.log' /usr/local/etc/php-fpm.conf && \
    sed -i 's/^user =.*/user = root/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/^group =.*/group = root/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i '/^access.log =/d' /usr/local/etc/php-fpm.d/*.conf && \
    sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/g' /usr/local/etc/php-fpm.d/www.conf

# 4. Nastavení entrypointu
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]