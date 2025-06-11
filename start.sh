#!/bin/sh

# Inicia PHP-FPM escuchando en un socket Unix
php-fpm -F --pid /var/run/php-fpm.pid --fpm-config /usr/local/etc/php-fpm.conf --daemonize no &

# Permite un pequeño tiempo para que PHP-FPM inicie el socket
sleep 5

# Inicia Nginx en primer plano
nginx -g "daemon off;"