# Use the official PHP image with Apache
FROM php:8.2-apache

# 1. Use the Production Configuration (Recommended)
# This disables error display to users and optimizes performance
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# 2. Enable Apache mod_rewrite (Standard)
RUN a2enmod rewrite

# 3. Copy application files
# NOTE: This respects the .dockerignore file we created!
COPY . /var/www/html/

# 4. Set Permissions (Crucial for file uploads/writing)
# Changes ownership from 'root' to the web server user 'www-data'
RUN chown -R www-data:www-data /var/www/html

# 5. Suppress Apache warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 6. Expose the port (Render detects this automatically, but good for documentation)
EXPOSE 80