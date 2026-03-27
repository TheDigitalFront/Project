FROM wordpress:6.9.1-apache

COPY wp-content/ /var/www/html/wp-content/
COPY wp-config-production.php /var/www/html/wp-config.php

RUN chown -R www-data:www-data /var/www/html
