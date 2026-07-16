FROM php:8.4-fpm-alpine

RUN apk add --no-cache libpq-dev \
    && docker-php-ext-install pdo_pgsql

WORKDIR /app
