FROM nginx:alpine

# Vyčištění defaultů
RUN rm -rf /etc/nginx/conf.d/* /etc/nginx/nginx.conf

# Kopírování tvé konfigurace a webu
COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY www /var/www/html/www

# Vytvoření logů s plnými právy
RUN touch /tmp/access.log /tmp/error.log && chmod 777 /tmp/access.log /tmp/error.log

# Obejít startovací skripty, které dělají chown
ENTRYPOINT []
CMD ["nginx", "-g", "daemon off; user root;"]