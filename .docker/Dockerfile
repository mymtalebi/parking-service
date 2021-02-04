FROM php:8.0-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends git zip unzip wget \
    && rm -rf /var/lib/apt/lists/*

# lumen packages
RUN docker-php-ext-install tokenizer

# download composer
RUN curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

COPY .docker/default.conf /etc/apache2/sites-available/000-default.conf
