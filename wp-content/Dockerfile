FROM wordpress:latest

# Copy your wp-content (themes, plugins, uploads) into the image
COPY wp-content/ /var/www/html/wp-content/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html/wp-content
