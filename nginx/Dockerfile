FROM nginx:alpine

RUN rm -rf /etc/nginx/conf.d/* /etc/nginx/nginx.conf /var/cache/nginx/*

COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY www /app/www

RUN mkdir -p /tmp/client_temp /tmp/proxy_temp /tmp/fastcgi_temp /tmp/uwsgi_temp /tmp/scgi_temp \
    && chmod -R 777 /tmp \
    && touch /tmp/access.log /tmp/error.log \
    && chmod 777 /tmp/access.log /tmp/error.log

ENTRYPOINT []
CMD ["nginx", "-g", "daemon off;"]