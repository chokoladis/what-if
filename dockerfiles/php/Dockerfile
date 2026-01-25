FROM node:25.2.1-alpine AS node_build
LABEL org.opencontainers.image.authors="github@chokoladis"

WORKDIR /var/www/what_if

COPY package*.json vite.config.js* ./

RUN npm install npm@11.6.2 -g && npm install npm@11.6.2
# RUN npm install --production

RUN npm ci

COPY . .
RUN npm run build

FROM php:8.3.13-fpm-alpine
LABEL org.opencontainers.image.authors="github@chokoladis"

WORKDIR /var/www/what_if

COPY ./dockerfiles/php/conf/fpm.conf /usr/local/etc/php-fpm.d/www.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ENV PATH="$PATH:/usr/local/bin"

COPY --from=node_build /var/www/what_if .

RUN set -eux; \
    apk add --virtual .build-deps \
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
            curl-dev

RUN apk add bash \
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
        pdo \
        pdo_mysql \
        mbstring \
        bcmath \
        intl \
        gd \
        opcache \
        sockets \
        pcntl \
        curl; \
    pecl install redis && docker-php-ext-enable redis; \
    apk del .build-deps; \
    apk add --no-cache ca-certificates && update-ca-certificates