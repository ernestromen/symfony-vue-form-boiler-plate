# Use an appropriate Symfony image
FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    mariadb-client 
# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql zip
WORKDIR /var/www/html
# Install necessary PHP extensions and other dependencies
COPY . /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]