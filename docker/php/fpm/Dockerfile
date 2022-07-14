FROM php:8.1-fpm-alpine

ARG uid

RUN apk add --no-cache autoconf g++ make libressl-dev rabbitmq-c-dev php-bcmath \
    &&  mkdir -p /usr/src/php/ext/amqp \
    && curl -fsSL https://pecl.php.net/get/amqp | tar xvz -C "/usr/src/php/ext/amqp" --strip 1 \
#    && pecl install amqp \
    && docker-php-ext-install amqp \
    && docker-php-ext-install sockets \
    && pecl install xdebug-3.1.5 \
#    && pecl install mongodb \
#    && rm -rf /tmp/pear \
#    && pecl install redis \
#    && docker-php-ext-install mysqli pdo pdo_mysql \
#    && docker-php-ext-enable pdo_mysql \
#    && docker-php-ext-enable xdebug \
#    && docker-php-ext-enable mongodb \
#    && docker-php-ext-enable redis
    && docker-php-ext-enable amqp \
    && docker-php-ext-enable sockets
#
#RUN apk add --no-cache postgresql-dev fcgi \
#    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
#    && docker-php-ext-install pdo_pgsql

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini \
#    change TZ to Europe/Moscow
#    && sed -i "s/;date.timezone =.*/date.timezone = Europe\/Moscow/" $PHP_INI_DIR/php.ini
    && sed -i "s/bcmath.scale = 0/bcmath.scale = 2/" $PHP_INI_DIR/php.ini
#    && sed -i "s/;extension=sockets/extension=sockets/" $PHP_INI_DIR/php.ini

COPY ./conf.d /usr/local/etc/php/conf.d

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

USER $uid

WORKDIR /var/www/app
