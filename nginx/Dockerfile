FROM nginx:alpine

# Vyčištění a kopírování konfigu
RUN rm -rf /etc/nginx/conf.d/* /etc/nginx/nginx.conf
COPY nginx/nginx.conf /etc/nginx/nginx.conf

# Kopírování webu do /app/www (shodné s root v nginx.conf)
COPY www /app/www

# Vytvoření všech dočasných složek a logů s plnými právy
RUN mkdir -p /tmp/client_temp /tmp/proxy_temp /tmp/fastcgi_temp /tmp/uwsgi_temp /tmp/scgi_temp && \
    touch /tmp/access.log /tmp/error.log && \
    chmod -R 777 /tmp

# Přímý start bez problematických entrypoint skriptů
ENTRYPOINT []
CMD ["nginx", "-g", "daemon off;"]