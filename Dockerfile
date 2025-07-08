FROM php:8.2-apache

# Install required build tools and libcurl to support curl extension
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    pkg-config \
    && docker-php-ext-install curl

# Copy project files into the web server root
COPY . /var/www/html/
