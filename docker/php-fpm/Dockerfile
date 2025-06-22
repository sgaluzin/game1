FROM php:8.3-fpm-alpine

# Установка необходимых расширений и инструментов
RUN apk update \
    && apk add --no-cache unzip git zip mc

# Установка Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html
