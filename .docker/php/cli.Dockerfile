FROM php:8.4-cli-alpine

RUN apk add --no-cache \
        curl-dev \
        libpq-dev \
    && docker-php-ext-install \
        curl \
        pdo_pgsql

WORKDIR /app
