FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    bash \
    git \
    curl \
    icu-dev \
    zlib-dev \
    libpng-dev \
    oniguruma-dev \
    autoconf \
    g++ \
    make \
    libzip-dev

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    intl \
    mbstring \
    gd \
    zip

WORKDIR /var/www

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN composer require --dev phpunit/phpunit:^10 fakerphp/faker:^1.23

COPY app/ /var/www/app/
COPY public/ /var/www/public/
COPY phpunit.xml /var/www/phpunit.xml
COPY .env.example /var/www/.env

RUN addgroup -g 1000 www && adduser -G www -g www -s /bin/sh -D www \
  && chown -R www:www /var/www
USER www

CMD ["php-fpm"]
