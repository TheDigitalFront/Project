FROM wordpress:6.9.1-apache

COPY wp-content/themes/the-digital-front-child/ /usr/src/wordpress/wp-content/themes/the-digital-front-child/
COPY wp-content/plugins/ /usr/src/wordpress/wp-content/plugins/
COPY wp-config-production.php /var/www/html/wp-config.php
COPY .htaccess /var/www/html/.htaccess

RUN chown -R www-data:www-data /usr/src/wordpress /var/www/html
