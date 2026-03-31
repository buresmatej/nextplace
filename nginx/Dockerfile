FROM nginx:alpine

# 1. Odstraníme defaultní konfigurace
RUN rm -f /etc/nginx/conf.d/default.conf

# 2. Kopírujeme tvůj nginx.conf jako hlavní konfiguraci
COPY nginx/nginx.conf /etc/nginx/nginx.conf

# 3. Kopírujeme statické soubory
COPY www /var/www/html/www

# KLÍČOVÝ KROK:
# Aby Nginx nezkoušel chown, musíme mu vnutit, že ty složky už existují pod ním.
# Ale protože chown nejde, prostě ty složky, co chce Nginx ovládat, dopředu vyrobíme.
RUN mkdir -p /tmp/client_temp /tmp/proxy_temp /tmp/fastcgi_temp /tmp/uwsgi_temp /tmp/scgi_temp && \
    chmod -R 777 /tmp