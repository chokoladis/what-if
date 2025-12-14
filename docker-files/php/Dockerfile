FROM php:8.2-fpm
MAINTAINER Chokoladis

WORKDIR /

RUN apt-get update && apt-get install -y \
    curl  \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    zlib1g-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \

RUN docker-php-ext-install gd
RUN docker-php-ext-install pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

USER root

RUN chmod 777 -R ./


#RUN php composer install
#RUN mv ./../../.env.example ./../../.env
#RUN php artisan migrate

#COPY ./../.. /app

#CMD ["php-fpm"]

#
#RUN #docker-php-ext-install zip
