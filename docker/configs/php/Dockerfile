#FROM php:8.2-fpm
#ARG WORKDIR
#WORKDIR ${WORKDIR}
#
#RUN apt-get -y update && apt-get -y install git unzip vim rsync
#
#RUN apt-get install -y libicu-dev libpq-dev && docker-php-ext-configure intl
#
#RUN docker-php-ext-install intl
## Встановлення розширень PostgreSQL
#RUN docker-php-ext-install pdo pgsql pdo_pgsql
#
#RUN docker-php-ext-install opcache
#
#RUN pecl install apcu && docker-php-ext-enable apcu
#
## Встановлення та активація Xdebug і YAML
#RUN pecl install xdebug && docker-php-ext-enable xdebug;
#
## Додавання конфігурації Xdebug
#COPY ./docker/configs/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
#
#ENV TERM xterm-256color
#
#RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
#
#
#CMD php-fpm
#EXPOSE 9000

# Базовий образ PHP 8.3 з FPM
FROM php:8.3-fpm

# Встановлення робочої директорії через аргумент
ARG WORKDIR
WORKDIR ${WORKDIR}

# Створення користувача 'container'
RUN #useradd -m container

# Оновлення системи та встановлення необхідних утиліт
RUN apt-get -y update && \
    apt-get -y install  \
    git  \
    unzip  \
    vim  \
    rsync  \
    libicu-dev && apt-get clean && rm -rf /var/lib/apt/lists/*  && \
    docker-php-ext-configure intl


# Встановлення розширень PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql

#RUN docker-php-ext-install opcache

RUN docker-php-ext-install opcache

RUN pecl install apcu && docker-php-ext-enable apcu

# Встановлення та активація Xdebug і YAML
RUN pecl install xdebug && docker-php-ext-enable xdebug;

# Додавання конфігурації Xdebug
COPY ./docker/configs/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Встановлення Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/bin/ --filename=composer && \
    php -r "unlink('composer-setup.php');"

# Налаштування кольорової схеми терміналу
ENV TERM xterm-256color

# Встановлення прав для користувача 'container' (додатковий захист)
RUN #chown -R container:container ${WORKDIR}

# Переключення на користувача 'container'
#USER container

# Запуск PHP-FPM
CMD php-fpm

# Відкриття порту для PHP-FPM
EXPOSE 9000