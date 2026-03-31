FROM nginx:alpine

RUN rm -rf /etc/nginx/conf.d/* /etc/nginx/nginx.conf

COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY www /app/www

RUN mkdir -p /tmp/client_temp /tmp/proxy_temp /tmp/fastcgi_temp /tmp/uwsgi_temp /tmp/scgi_temp \
    && chown -R nginx:nginx /tmp/client_temp /tmp/proxy_temp /tmp/fastcgi_temp /tmp/uwsgi_temp /tmp/scgi_temp \
    && chown -R nginx:nginx /app/www \
    && touch /tmp/access.log /tmp/error.log \
    && chown nginx:nginx /tmp/access.log /tmp/error.log

USER nginx
CMD ["nginx", "-g", "daemon off;"]