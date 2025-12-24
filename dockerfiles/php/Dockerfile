FROM php:8.3.13-fpm-alpine
MAINTAINER Chokoladis

WORKDIR /var/www/what_if

RUN docker-php-ext-install pdo pdo_mysql
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ENV PATH="$PATH:/usr/local/bin"

RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
        autoconf \
        build-base \
        linux-headers \
        zlib-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
        icu-dev \
        libxml2-dev \
        curl-dev; \
    apk add --no-cache \
        git \
        unzip \
        zip \
        bash \
        libzip \
        zlib \
        libpng \
        libjpeg-turbo \
        freetype \
        icu-libs \
        libxml2 \
        curl; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install \
        pdo_mysql \
        mbstring \
        xml \
        zip \
        bcmath \
        intl \
        gd \
        opcache \
        sockets \
        pcntl \
        curl; \
    pecl install redis && docker-php-ext-enable redis; \
    apk del .build-deps