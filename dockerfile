# Gunakan base image PHP dengan Apache
FROM php:8.1-apache

# Salin semua file dari repo ke direktori web server di container
COPY . /var/www/html/

# (Opsional) Jika butuh install ekstensi PHP, contoh:
# RUN docker-php-ext-install mysqli pdo pdo_mysql

# Expose port 80
EXPOSE 80

# Jalankan Apache di foreground
CMD ["apache2-foreground"]
