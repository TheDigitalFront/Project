FROM wordpress:6.7.2-apache

# Fix Apache MPM conflict (Railway injects its own MPM config)
RUN a2dismod mpm_event mpm_worker || true && a2enmod mpm_prefork

# Copy your wp-content (themes, plugins, uploads) into the image
COPY wp-content/ /var/www/html/wp-content/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html/wp-content
