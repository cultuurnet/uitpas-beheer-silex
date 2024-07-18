FROM php:7.4-apache

RUN apt-get update && apt-get install -y --no-install-recommends git zip unzip libicu-dev libtidy-dev zlib1g-dev libpng-dev libzip-dev

RUN docker-php-ext-install bcmath tidy sockets

RUN docker-php-ext-configure zip && docker-php-ext-install zip

RUN docker-php-ext-configure intl && docker-php-ext-install intl

RUN docker-php-ext-configure gd && docker-php-ext-install gd

RUN curl https://getcomposer.org/download/2.2.0/composer.phar -o /usr/local/bin/composer && chmod +x /usr/local/bin/composer

RUN echo "memory_limit=4096M" > $PHP_INI_DIR/conf.d/memory-limit.ini
RUN echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE" > $PHP_INI_DIR/conf.d/error_reporting.ini

RUN a2enmod rewrite


#install npm
RUN apt-get install -y --no-install-recommends ca-certificates curl gnupg vim libffi-dev git

RUN mkdir -p /etc/apt/keyrings

RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg

RUN echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_16.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list

RUN apt-get update && apt-get install -y --no-install-recommends nodejs

RUN npm i &

RUN npm run bower -- install &
#end npm

ENV APACHE_DOCUMENT_ROOT=/var/www/html/web
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN echo "LimitRequestFieldSize 32766" >> /etc/apache2/conf-available/security.conf

RUN pecl install xdebug-2.9.8 && docker-php-ext-enable xdebug
COPY xdebug.ini $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini
