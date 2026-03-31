FROM nginx:alpine

# 1. Vymažeme defaultní konfigy, aby nás nebrzdily
RUN rm -rf /etc/nginx/conf.d/* /etc/nginx/nginx.conf

# 2. Kopírujeme tvůj vyladěný nginx.conf
COPY nginx/nginx.conf /etc/nginx/nginx.conf

# 3. Kopírujeme statiku
COPY www /var/www/html/www

# 4. Vytvoříme složky v /tmp dopředu a dáme jim práva
RUN mkdir -p /tmp/client_temp /tmp/proxy_temp /tmp/fastcgi_temp /tmp/uwsgi_temp /tmp/scgi_temp && \
    chmod -R 777 /tmp

# KLÍČOVÝ KROK: Přepíšeme ENTRYPOINT na prázdno a spustíme nginx přímo.
# Tím se úplně vyhneme těm skriptům, co dělají chown.
ENTRYPOINT []
CMD ["nginx", "-g", "daemon off;"]