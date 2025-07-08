# Use the official PHP + Apache image
FROM php:8.2-apache

# Enable curl (for Last.fm API)
RUN docker-php-ext-install curl

# Copy all project files into the web server root
COPY . /var/www/html/

# Optional: turn on Apache rewrite module if needed
RUN a2enmod rewrite
