FROM php:8.4-fpm-alpine

RUN apk add --no-cache git unzip icu-dev libzip-dev linux-headers sqlite-dev libxml2-dev oniguruma-dev \
    && docker-php-ext-install pdo_mysql pdo_sqlite intl zip dom xml xmlwriter mbstring

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
