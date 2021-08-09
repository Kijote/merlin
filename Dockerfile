# Dockerfile para MerLiN

######################## SISTEMA OPERATIVO ########################

# Pull imagen base
FROM php:7.4-apache

ENV APACHE_DOCUMENT_ROOT /var/www/merlin/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Extensiones de PHP
RUN \
    docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure mysqli --with-mysqli=mysqlnd \
    && docker-php-ext-install pdo_mysql

# Rewrite de Apache
RUN a2enmod rewrite

# Instalar herramientas básicas
RUN apt-get update
RUN DEBIAN_FRONTEND=noninteractive apt-get install -y tzdata
RUN apt-get install -y curl git zip unzip nodejs

######################## APLICACION - COMPOSER ########################

# Instalo composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir /var/www/merlin
RUN touch merlin_home

# Seteo el working directory
WORKDIR /var/www/merlin

######################## LIMPIAR APT ########################

RUN apt-get clean
RUN apt-get autoremove -y
RUN rm -rf /var/lib/apt/lists/*

######################## CONTAINER ########################

# Expongo el puerto 80
EXPOSE 80
