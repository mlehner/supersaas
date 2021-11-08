FROM php:7.4-cli

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN mkdir -p /opt/supersaas

WORKDIR /opt/supersaas