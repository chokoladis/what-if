FROM php:8.2-fpm-apline
MAINTAINER Chokoladis

WORKDIR /var/www/what_if

RUN apt-get update && apt-get install -y \
    curl  \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    zlib1g-dev \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \

#COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
#
#USER root
#RUN chmod 777 -R .

#COPY . /var/www/what_if

#RUN php composer install
#RUN mv ./../../.env.example ./../../.env
#RUN php artisan migrate

#CMD ["php-fpm"]