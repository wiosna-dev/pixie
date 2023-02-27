#
#--------------------------------------------------------------------------
# Image Setup
#--------------------------------------------------------------------------
#

FROM php:8.2-cli

# Set Environment Variables
ENV DEBIAN_FRONTEND noninteractive

#
#--------------------------------------------------------------------------
# Software's Installation
#--------------------------------------------------------------------------
#
# Installing tools and PHP extentions using "apt", "docker-php", "pecl",
#

# PHP EXTENSIONS
# libraries
RUN apt-get update; \
    apt-get upgrade -y; \
    apt-get install -y  \
            libcurl4-gnutls-dev \
            libpng-dev \
            libicu-dev \
            libmcrypt-dev \
            libreadline-dev \
            libldb-dev \
            libmcrypt-dev \
            libzip-dev \
            libpng-dev \
            libjpeg-dev  \
            git \
            zip;

RUN ln -s /usr/lib/x86_64-linux-gnu/libldap.so /usr/lib/libldap.so \
    && ln -s /usr/lib/x86_64-linux-gnu/liblber.so /usr/lib/liblber.so

# extensions
RUN docker-php-ext-install pdo_mysql mysqli curl intl opcache zip

# last version of XDebug 2 is 2.9.8 released on the 28th September 2020
# last version of xDebug 3 is 3.1.5 released on the 6th June 2022
ENV XDEBUG_VERSION 3.2.0

# xdebug
RUN curl -fsSL https://xdebug.org/files/xdebug-$XDEBUG_VERSION.tgz -o xdebug.tar.gz \
    && mkdir -p xdebug \
    && tar -xf xdebug.tar.gz -C xdebug --strip-components=1 \
    && rm xdebug.tar.gz \
    && ( \
    cd xdebug \
    && phpize \
    && ./configure --enable-xdebug \
    && make -j$(nproc) \
    && make install \
    ) \
    && rm -r xdebug \
    && docker-php-ext-enable xdebug

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

# phpunit
ENV PHPUNIT_VERSION 9.5.24

RUN curl -sSLJ -o /usr/local/bin/phpunit https://phar.phpunit.de/phpunit-$PHPUNIT_VERSION.phar \
        && chmod a+x /usr/local/bin/phpunit

WORKDIR /var/www
