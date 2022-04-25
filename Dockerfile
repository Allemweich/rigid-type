ARG PHP_BASE_IMAGE=nexus.easybell.de/templates/php-base-image:7.4
ARG PHP_BASE_IMAGE_DEV=nexus.easybell.de/templates/php-base-image:7.4-dev

FROM alpine:3 AS composer
RUN apk add --no-cache php7 php7-json php7-phar php7-iconv php7-openssl
RUN wget https://getcomposer.org/installer -O composer-installer.php
RUN php composer-installer.php --install-dir=/bin --filename=composer

FROM ${PHP_BASE_IMAGE_DEV} AS dev
RUN apt-get update && apt-get install -y git
COPY / /app
COPY --from=composer /bin/composer /bin/composer
WORKDIR /app
RUN composer install --no-plugins --no-scripts
