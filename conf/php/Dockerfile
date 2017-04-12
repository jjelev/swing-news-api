FROM php:apache

# INSTALL EXTENSIONS
RUN apt-get update

RUN docker-php-ext-install pdo_mysql

RUN pecl install xdebug && docker-php-ext-enable xdebug

ADD ./swing-news-api.conf /etc/apache2/sites-available/

RUN a2enmod rewrite \
    && a2ensite swing-news-api.conf \
