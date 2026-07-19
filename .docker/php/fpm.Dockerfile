FROM php:8.4-fpm-alpine

ARG UID=1000
ARG GID=1000

RUN addgroup -g $GID app \
    && adduser -D -u $UID -G app app \
    && apk add --no-cache libpq-dev \
    && docker-php-ext-install pdo_pgsql

WORKDIR /app
