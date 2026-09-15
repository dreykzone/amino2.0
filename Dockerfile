FROM php:8.2-apache

RUN docker-php-ext-install mysqli

COPY docker/apache-security.conf /etc/apache2/conf-available/amino-security.conf
RUN a2enconf amino-security

WORKDIR /var/www/html