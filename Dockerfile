FROM php:7.4-apache

# Установка зависимостей для GD
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libjpeg-dev \
    libicu-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libwebp-dev \
    libxpm-dev \
    libpq-dev \
    libc-client-dev \
    libkrb5-dev \
    libgmp-dev \
    libmagickwand-dev \
    libssl-dev \
    wget \
    mc \
    unzip \
    git \
    gnupg2 \
    ca-certificates \
    build-essential \
    --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install gd \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install \
            zip intl mbstring exif  soap pdo  imap gmp \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli



# Xdebug налаштування
RUN pecl install xdebug-3.1.6 && docker-php-ext-enable xdebug
RUN echo "zend_extension=xdebug.so\n\
xdebug.mode=develop,debug\n\
xdebug.start_with_request=yes\n\
xdebug.client_host=host.docker.internal\n\
xdebug.client_port=9003" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN apt-get -y update \
&& apt-get install -y libicu-dev \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl

RUN apt-get install -y libzip-dev zip unzip && \
    docker-php-ext-install zip

RUN wget https://getcomposer.org/installer -O - -q \
    | php -- --install-dir=/bin --filename=composer --quiet

ENV COMPOSER_ALLOW_SUPERUSER 1

#update config php
#COPY php.ini /usr/local/etc/php/
RUN echo "magic_quotes_gpc = Off;\n\
          register_globals = Off;\n\
          default_charset	= UTF-8;\n\
          memory_limit = 512M;\n\
          max_execution_time = 36000;\n\
          upload_max_filesize = 999M;\n\
          safe_mode = Off;\n\
          mysql.connect_timeout = 20;\n\
          session.auto_start = Off;\n\
          session.use_only_cookies = On;\n\
          session.use_cookies = On;\n\
          session.use_trans_sid = Off;\n\
          session.cookie_httponly = On;\n\
          session.gc_maxlifetime = 3600;\n\
          allow_url_fopen = on;" > /usr/local/etc/php/conf.d/custom.ini

# Встановлення ionCube Loader
RUN curl -sSL https://downloads.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz | tar xz -C /tmp \
    && cp /tmp/ioncube/ioncube_loader_lin_7.4.so /usr/local/lib/php/extensions/no-debug-non-zts-20190902/ \
    && echo "zend_extension=ioncube_loader_lin_7.4.so" > /usr/local/etc/php/conf.d/00-ioncube.ini

# Включение модуля rewrite для Apache
RUN a2enmod rewrite

CMD ["apache2-foreground"]
