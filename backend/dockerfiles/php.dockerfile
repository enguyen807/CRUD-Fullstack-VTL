FROM php:8.1-fpm-alpine
 
WORKDIR /var/www/html
 
COPY ./backend/src .
 
RUN docker-php-ext-install pdo pdo_mysql
 
RUN addgroup -g 1000 lumen && adduser -G lumen -g lumen -s /bin/sh -D lumen

USER lumen 