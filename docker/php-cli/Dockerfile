FROM php:7.4-cli-alpine

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN apk add --no-cache zip libzip-dev git
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip

RUN mkdir -p /opt/supersaas

WORKDIR /opt/supersaas