FROM wordpress:6.9.1-apache

ARG CACHEBUST=1
RUN echo "Cache bust: $CACHEBUST"

COPY wp-content/ /tmp/wp-content/
COPY wp-config-production.php /tmp/wp-config.php
COPY .htaccess /tmp/.htaccess

CMD ["sh", "-c", "cp -a /tmp/wp-content/* /var/www/html/wp-content/ && cp /tmp/wp-config.php /var/www/html/wp-config.php && cp /tmp/.htaccess /var/www/html/.htaccess && chown -R www-data:www-data /var/www/html && apache2-foreground"]
