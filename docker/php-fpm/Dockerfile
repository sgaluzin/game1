FROM php:8.3-fpm

# Установка необходимых расширений и инструментов
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        unzip \
        git \
        zip \
    && rm -rf /var/lib/apt/lists/*

# Установка Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html
