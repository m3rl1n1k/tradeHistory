FROM php:8.1-fpm
ARG WORKDIR
WORKDIR ${WORKDIR}

RUN apt-get -y update && apt-get -y install git unzip vim rsync
RUN apt-get install -y libicu-dev && docker-php-ext-configure intl
RUN apk add --update --no-cache --virtual .build-dependencies $PHPIZE_DEPS \
        && pecl install apcu \
        && docker-php-ext-enable apcu \
        && pecl clear-cache \
        && apk del .build-dependencies

RUN docker-php-ext-install opcache

RUN docker-php-ext-install pdo pdo_mysql intl

ENV TERM xterm-256color

RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer


CMD php-fpm
EXPOSE 9000