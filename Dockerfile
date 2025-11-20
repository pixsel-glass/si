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

# --- Включаємо потрібні Apache модулі ---
RUN a2enmod rewrite headers

# --- SSL-сертифікат ---
#RUN a2enmod ssl
#RUN mkdir -p /etc/apache2/ssl
#RUN openssl req -x509 -nodes -days 365 \
#    -subj "/C=UA/ST=Kyiv/L=Kyiv/O=Prodbaza/OU=IT/CN=localhost" \
#    -newkey rsa:2048 \
#    -keyout /etc/apache2/ssl/apache-selfsigned.key \
#    -out /etc/apache2/ssl/apache-selfsigned.crt
#RUN ls -l /etc/apache2/ssl

#    RUN echo '<VirtualHost *:443>\n\
#    ServerAdmin admin@localhost\n\
#    DocumentRoot /var/www/html\n\
#    SSLEngine on\n\
#    SSLCertificateFile /etc/apache2/ssl/apache-selfsigned.crt\n\
#    SSLCertificateKeyFile /etc/apache2/ssl/apache-selfsigned.key\n\
#    <Directory /var/www/html>\n\
#        AllowOverride All\n\
#        Require all granted\n\
#    </Directory>\n\
#</VirtualHost>' > /etc/apache2/sites-available/default-ssl.conf
#RUN a2ensite default-ssl
# --- END SSL-сертифікат ---

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


CMD ["apache2-foreground"]
