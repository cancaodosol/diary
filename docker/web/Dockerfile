FROM php:7.4-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip wget \
    msmtp-mta \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu xdebug-3.1.6 \
    && docker-php-ext-enable apcu xdebug \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

WORKDIR /var/www/diary

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN wget https://get.symfony.com/cli/installer -O - | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN git config --global user.email "atagohan@yahoo.co.jp" \ 
    && git config --global user.name "Hideyuki Matsui"
