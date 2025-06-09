FROM php:8-fpm
RUN apt-get update && apt-get install -y libsqlite3-0 libavif-dev libpng-dev zlib1g-dev
# Install default extensions
RUN docker-php-ext-configure gd --with-avif
RUN docker-php-ext-install -j$(nproc) opcache gd
# Install pecl extensions
RUN pecl install apcu xdebug && docker-php-ext-enable apcu xdebug
# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# Up the php-fpm children limit
RUN sed -i 's/\(pm\.max_children = \).*/\148/' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's/\(pm = \).*/\1ondemand/' /usr/local/etc/php-fpm.d/www.conf
# Increase memory limit for image processing
RUN sed -i 's/\(memory_limit = \).*/\1512M/' /usr/local/etc/php/php.ini
# Enable opcache
COPY opcache.ini /usr/local/etc/php/conf.d/
# Enable xdebug
#COPY xdebug.ini /usr/local/etc/php/conf.d/
# Increase file limits
COPY limits.conf /etc/security/limits.conf
# Use socket for fpm
COPY zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
