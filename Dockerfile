FROM wordpress:6.9.1-apache

ADD . /tmp/buildctx
RUN rm -rf /tmp/buildctx
COPY wp-content/ /var/www/html/wp-content/
COPY wp-config-production.php /var/www/html/wp-config.php

RUN chown -R www-data:www-data /var/www/html

CMD ["sh", "-c", "chown -R www-data:www-data /var/www/html/wp-content/uploads && apache2-foreground"]
