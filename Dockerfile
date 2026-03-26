FROM wordpress:6.7.2-apache

# Copy  wp-content (themes, plugins, uploads) into the image
COPY wp-content/ /var/www/html/wp-content/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html/wp-content

# Fix Apache MPM conflict at runtime
RUN echo '#!/bin/bash\na2dismod mpm_event mpm_worker 2>/dev/null || true\na2enmod mpm_prefork 2>/dev/null || true\nexec docker-entrypoint.sh apache2-foreground' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]
