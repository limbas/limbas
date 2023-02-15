FROM php:8.0-apache
MAINTAINER Limbas GmbH <info@limbas.com>

ENV APACHE_DOCUMENT_ROOT /var/www/html/openlimbas/public

# adding needed modules
RUN apt-get update -y \
    && apt-get install -y --no-install-recommends \
    imagemagick \
    libfreetype6-dev \
    libimage-exiftool-perl \
    libjpeg-dev \
    libpng-dev \
    libpq-dev \
    libxml2-dev \
    libzip-dev \
    pdftohtml \
    zip \
    zlib1g-dev

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install \
    calendar \
    exif \
    gd \
    pdo_pgsql \
    zip \
    soap \
    && docker-php-ext-enable \
    calendar \
    exif \
    gd \
    pdo_pgsql \
    soap \
    zip \
    && a2enmod rewrite
    
    

# php.ini
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/limbas-php-ext.ini /usr/local/etc/php/conf.d/

# apache config
COPY docker/openlimbas.conf /etc/apache2/conf.d/

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# copy source
COPY ./src /var/www/html/openlimbas

COPY --chown=www-data:www-data docker/include_db_docker.lib /opt/

COPY docker/docker-entrypoint.sh /usr/local/bin/

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
