FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    nginx \
    postgresql-dev \
    git \
    zip \
    unzip \
    nodejs \
    npm \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    jpeg-dev \
    icu-dev \
    build-base \
    autoconf \
    g++ \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    zlib-dev \
    && rm -rf /var/cache/apk/*

RUN docker-php-ext-install -j$(nproc) \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    intl \
    zip \
    && rm -rf /tmp/* /usr/share/doc/*

RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install -j$(nproc) gd \
    && rm -rf /tmp/* /usr/share/doc/* /var/cache/apk/*

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY . .

# ¡NUEVA LÍNEA AQUÍ! Elimina la configuración por defecto de Nginx
RUN rm -f /etc/nginx/conf.d/default.conf || true

# Copia el archivo de configuración de Nginx personalizado
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

RUN composer install --no-dev --optimize-autoloader --prefer-dist

RUN npm install \
    && npm run build

RUN php artisan migrate --force

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
EXPOSE 80

COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]