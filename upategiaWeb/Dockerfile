FROM php:7-apache

# Instalar las extensiones necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar la extensión mysqli
RUN a2enmod rewrite

