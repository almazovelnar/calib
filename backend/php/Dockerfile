FROM php:7.4.12-fpm-buster

WORKDIR /tmp

RUN apt-get update && apt-get install -y \
    build-essential \
    systemd \
    libpspell-dev \
    libzip-dev \
    git \
    libpq-dev \
    libonig-dev \
    libwebp-dev \
    libxpm-dev \
    libmagickwand-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    locales \
    locales-all \
    zip \
    wget \
    pkg-config \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip

RUN docker-php-ext-install pgsql mbstring zip exif pcntl pdo_pgsql pspell

RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-configure zip && docker-php-ext-install zip

RUN pecl install imagick && docker-php-ext-enable imagick

RUN pecl install -o -f redis && rm -rf /tmp/pear && docker-php-ext-enable redis

RUN pecl install xdebug-3.0.1 && docker-php-ext-enable xdebug

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

RUN wget --quiet -O /usr/local/bin/composer "https://getcomposer.org/composer-stable.phar"
RUN chmod 755 /usr/local/bin/composer
RUN chmod g+w /usr/local/bin/composer

RUN wget --quiet -O /usr/local/bin/php-cs-fixer "https://cs.symfony.com/download/php-cs-fixer-v2.phar"
RUN chmod 755 /usr/local/bin/php-cs-fixer
RUN chmod g+w /usr/local/bin/php-cs-fixer

RUN apt-get clean && rm -rf /var/lib/apt/lists/*
RUN rm -rf /var/cache/apk/*

RUN useradd -ms /bin/bash nginx
RUN chown -R nginx:nginx /var/www
RUN chmod -R g+w /var/www

WORKDIR /var/www

RUN usermod -u 22313 nginx

USER nginx

EXPOSE 9000
CMD ["php-fpm"]
