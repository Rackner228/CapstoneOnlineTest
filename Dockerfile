# Use the official PHP image with Apache
FROM php:8.2-apache

# 1. Enable Apache mod_rewrite
# (This is useful if you later want clean URLs, e.g. /about instead of /about.php)
RUN a2enmod rewrite

# 2. Copy the contents of the CURRENT folder (Frontendv2) into the web server
COPY . /var/www/html/

# 3. Suppress a common Apache warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 4. Tell Docker we are using Port 80
EXPOSE 80