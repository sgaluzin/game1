FROM php:8.3-fpm-alpine

# Установка необходимых расширений и инструментов
RUN apk update \
    && apk add --no-cache unzip git zip mc bash icu-dev \
    && docker-php-ext-install intl \
    && apk add --no-cache php-opcache

# Установка Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

# Установка Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | version=3.8.0 codename="alpine" bash
RUN apk add symfony-cli

WORKDIR /var/www/html
