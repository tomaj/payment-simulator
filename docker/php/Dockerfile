FROM php:7.2-fpm

ENV BUILD_DEPS libxml2-dev

ENV RUN_DEPS git wget vim curl less gnupg unzip pv

RUN apt-get update && apt-get install -y ${BUILD_DEPS} ${RUN_DEPS} --no-install-recommends \
    && docker-php-ext-install soap

# OPCache

RUN docker-php-ext-install opcache

ADD opcache.ini /tmp/opcache.ini

RUN cat /tmp/opcache.ini >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# XDEBUG

RUN pecl install xdebug && docker-php-ext-enable xdebug

ADD xdebug.ini /tmp/xdebug.ini

RUN cat /tmp/xdebug.ini >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# COMPOSER

ENV COMPOSER_HOME /composer

ENV PATH /composer/vendor/bin:$PATH

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# VOLUME

VOLUME /var/www/html

RUN chmod 777 /var/www/html