FROM php:7.4.0-fpm

ENV LANG "C.UTF-8"
ENV LANGUAGE "C.UTF-8"
ENV LC_ALL "C.UTF-8"

RUN curl -s https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer.phar && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

RUN apt-get update && apt-get install -y curl libpq-dev libzip-dev zip unzip git

RUN docker-php-ext-install pdo_pgsql zip sysvsem

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/

ARG UID
ARG GID

RUN groupmod -o -g ${GID} www-data && mkdir /home/www-data && usermod --home /home/www-data -o -u ${UID} www-data && chown -R www-data:www-data /home/www-data

USER www-data

WORKDIR /app