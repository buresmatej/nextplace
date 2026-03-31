FROM nginx:alpine

# Kopírujeme naši konfiguraci jako HLAVNÍ nginx.conf
COPY nginx/nginx.conf /etc/nginx/nginx.conf

# Kopírujeme statiku
COPY www /var/www/html/www

# Pro jistotu vyčistíme defaultní configy, které by mohly dělat neplechu
RUN rm -f /etc/nginx/conf.d/default.conf