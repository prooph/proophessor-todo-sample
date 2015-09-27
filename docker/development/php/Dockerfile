FROM php:5.6-fpm

WORKDIR /tmp

# Additional extensions

RUN apt-get update \
    && apt-get install -y \
    # for intl extension
    libicu-dev \
    && rm -r /var/lib/apt/lists/*

# Z-Ray
RUN curl -L http://downloads.zend.com/zray/1208/zray-php5.6-Ubuntu-14.04-x86_64.tar.gz | tar zx \
    && mv /tmp/zray /opt/zray

# XDEBUG
RUN curl -L http://xdebug.org/files/xdebug-2.3.3.tgz | tar zx
WORKDIR xdebug-2.3.3
RUN phpize \
    && ./configure \
    && make -j \
    && make install

RUN docker-php-ext-configure mbstring --enable-mbstring \
  && docker-php-ext-configure intl --enable-intl \
  && docker-php-ext-configure opcache --enable-opcache \
  && docker-php-ext-configure pdo_mysql --with-pdo-mysql

RUN docker-php-ext-install \
       intl \
       mbstring \
       opcache \
       pdo_mysql

# Copy configuration
COPY config/php.ini /usr/local/etc/php/
COPY config/php-fpm.conf /usr/local/etc/

RUN ln -fs /opt/zray/lib/zray.so /usr/local/lib/php/extensions/no-debug-non-zts-20131226/zray.so \
    && ln -fs /opt/zray/zray.ini /usr/local/etc/php/conf.d/zray.ini \
    && chown -R www-data:www-data /opt/zray
