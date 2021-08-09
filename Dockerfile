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

# Instalar herramientas básicas
RUN apt-get update
RUN DEBIAN_FRONTEND=noninteractive apt-get install -y tzdata
RUN apt-get install -y curl git zip unzip nodejs

# Seteo el working directory
WORKDIR /var/www

######################## APLICACION - PROYECTO ########################

RUN git clone https://Kijote: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@github.com/Kijote/merlin.git

######################## APLICACION - COMPOSER ########################

# Instalo composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Seteo el working directory
WORKDIR /var/www/merlin

# Instalo todas las dependencias del proyecto
RUN composer install

######################## LIMPIAR APT ########################

RUN apt-get clean
RUN apt-get autoremove -y
RUN rm -rf /var/lib/apt/lists/*

######################## CONTAINER ########################

# Expongo el puerto 80
EXPOSE 80
