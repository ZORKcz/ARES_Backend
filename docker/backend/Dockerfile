FROM php:8.2-fpm-alpine as base

WORKDIR /var/www

RUN apk update && apk upgrade

RUN apk add --update --no-cache \
    bash \
    zip \
    icu-dev

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# --> install Symfony CLI
RUN apk add --no-cache bash
ENV distro=alpine
ENV version=3.8.0
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash
RUN apk add symfony-cli
RUN mkdir /.symfony5 && chmod -R 777 /.symfony5

# This is needed, because the file is not mounted
COPY config/preload.php /var/www/config/preload.php
RUN docker-php-ext-install pdo pdo_mysql opcache intl

FROM base as dev

COPY docker/backend/config/memory-limit.ini /usr/local/etc/php/conf.d/
COPY docker/backend/config/opcache.ini /usr/local/etc/php/conf.d/

COPY docker/backend/entrypoints/entrypoint.sh /usr/bin/
RUN chmod +x /usr/bin/entrypoint.sh
ENTRYPOINT ["/usr/bin/entrypoint.sh"]

ENV PHPSTAN_PRO_WEB_PORT=11111
EXPOSE 11111

FROM dev as xdebug

RUN apk --no-cache add pcre-dev ${PHPIZE_DEPS} linux-headers \
  && pecl install xdebug \
  && docker-php-ext-enable xdebug \
  && apk del pcre-dev ${PHPIZE_DEPS} linux-headers

COPY docker/backend/config/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/