FROM composer:latest
 
RUN addgroup -g 1000 lumen && adduser -G lumen -g lumen -s /bin/sh -D lumen

USER lumen 

WORKDIR /var/www/html
 
ENTRYPOINT [ "composer", "--ignore-platform-reqs" ]