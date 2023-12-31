FROM nginx:stable-alpine
 
WORKDIR /etc/nginx/conf.d
 
COPY ./backend/nginx/nginx.conf .
 
RUN mv nginx.conf default.conf

WORKDIR /var/www/html

COPY ./backend/src .