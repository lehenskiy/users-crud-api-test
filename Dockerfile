# Set the base image for subsequent instructions
FROM php:8.2.2-fpm

RUN apt-get update \
    && apt-get install -qq git zip curl libpq-dev libicu-dev \
    && apt-get clean \
    && docker-php-ext-install pdo pdo_pgsql pgsql intl \
    && curl --silent --show-error "https://getcomposer.org/installer" | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get install -y postgresql-client

COPY /vendor /var/www/vendor

RUN mkdir -p /var/www/var/cache \
    &&  chown -R www-data:www-data /var/www/var
