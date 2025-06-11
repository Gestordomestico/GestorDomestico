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

RUN composer install --no-dev --optimize-autoloader --prefer-dist

RUN npm install \
    && npm run build

RUN php artisan migrate --force

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]