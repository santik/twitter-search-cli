FROM php:7.0-apache

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libmcrypt-dev \
        libicu-dev \
        libxml2-dev \
        vim \
        wget \
        unzip \
        git \
    && docker-php-ext-install -j$(nproc) iconv intl xml soap mcrypt opcache mbstring

RUN chmod -R 777 /var/www/html
COPY . /var/www/html

RUN php composer.phar install \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --no-dev \
    --prefer-dist

EXPOSE 80