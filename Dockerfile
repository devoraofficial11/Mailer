FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    && docker-php-ext-install zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project to container
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html
