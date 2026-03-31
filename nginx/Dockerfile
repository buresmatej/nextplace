FROM nginx:alpine

# Smažeme všechno, co by mohlo Nginx provokovat k chownu
RUN rm -rf /etc/nginx/conf.d/* /etc/nginx/nginx.conf /var/cache/nginx/*

COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY www /app/www

# Vytvoříme jen logy v /tmp
RUN touch /tmp/access.log /tmp/error.log && chmod 777 /tmp/access.log /tmp/error.log

# Spuštění s vynucením uživatele "nobody" – Nginx se nebude snažit o rootovské operace
ENTRYPOINT []
CMD ["nginx", "-g", "daemon off;"]