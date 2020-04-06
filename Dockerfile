FROM php:7.4-fpm-alpine

RUN apk update \
    && apk add wget \
    && apk add curl \
    && apk add autoconf \
    && apk add gcc \
    && apk add make \
    && apk add libc-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    #Set development php
    && mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini \
    #Install composer
    && wget -O /bin/composer https://getcomposer.org/download/1.9.2/composer.phar \
    && rm -rf /var/cache/apk/* \
    && chmod o+x /bin/composer \
    #Install and setings xdebug
    && pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > $PHP_INI_DIR/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=0" >> /usr/local/etc/php/conf.d/xdebug.ini \