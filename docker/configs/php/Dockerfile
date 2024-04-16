FROM php:8.2-fpm
ARG WORKDIR
WORKDIR ${WORKDIR}

RUN apt-get -y update && apt-get -y install git unzip vim rsync

RUN apt-get install -y libicu-dev && docker-php-ext-configure intl

RUN docker-php-ext-install pdo pdo_mysql intl

RUN docker-php-ext-install opcache

RUN pecl install apcu && docker-php-ext-enable apcu

ENV TERM xterm-256color

RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer


CMD php-fpm
EXPOSE 9000