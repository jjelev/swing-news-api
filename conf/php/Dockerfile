FROM php:apache

# INSTALL EXTENSIONS
RUN apt-get update

RUN docker-php-ext-install pdo_mysql

RUN pecl install xdebug && docker-php-ext-enable xdebug