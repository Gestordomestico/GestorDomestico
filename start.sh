#!/bin/sh

# Inicia PHP-FPM en primer plano
# Usamos -F para que se mantenga en foreground, esencial para Docker
php-fpm -F &

# Permite un pequeño tiempo para que PHP-FPM inicie el socket
sleep 5

# Inicia Nginx en primer plano
nginx -g "daemon off;"