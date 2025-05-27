FROM php:8.1-apache

# Install mysqli dan ekstensi penting
RUN docker-php-ext-install mysqli

# Salin file ke direktori web Apache
COPY . /var/www/html/

# Aktifkan mod_rewrite jika butuh
RUN a2enmod rewrite
