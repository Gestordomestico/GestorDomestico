# Stage 1: Build dependencies and assets
FROM php:8.2-fpm-alpine as builder

# Install PHP extensions and dependencies
RUN apk add --no-cache \
    nginx \
    php-json \
    php-pdo_mysql \
    php-mysqli \
    php-zip \
    php-gd \
    php-mbstring \
    php-xml \
    php-tokenizer \
    php-ctype \
    php-session \
    php-dom \
    php-exif \
    php-fileinfo \
    php-openssl \
    php-bcmath \
    php-opcache \
    php-pecl-redis \
    php-pcntl \
    php-posix \
    php-simplexml \
    php-xmlreader \
    php-xmlwriter \
    php-iconv \
    php-curl \
    php-phar \
    php-common \
    php-session \
    php-gd \
    php-intl \
    php-fileinfo \
    php-zip \
    php-sodium \
    php-tokenizer \
    php-xml \
    php-xmlreader \
    php-xmlwriter \
    php-iconv \
    php-opcache \
    php-xdebug \
    php-curl \
    php-bcmath \
    php-gmp \
    php-pdo_pgsql \
    php-pdo_sqlite # Add php-pdo_pgsql if using PostgreSQL

# Install Composer
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

# Install Node.js and npm
RUN apk add --no-cache nodejs npm

# Set working directory
WORKDIR /app

# Copy composer.json and package.json first to leverage Docker cache
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets
RUN npm install && npm run build

# Copy the application code
COPY . .

# Ensure storage permissions for Laravel
RUN chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache

# Stage 2: Production ready image
FROM php:8.2-fpm-alpine

# Install Nginx
RUN apk add --no-cache nginx

# Copy PHP-FPM configuration
COPY --from=builder /etc/php82/php-fpm.d/www.conf /etc/php82/php-fpm.d/www.conf

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Set working directory
WORKDIR /app

# Copy application code from builder stage
COPY --from=builder /app /app

# Expose port 80 (Nginx default)
EXPOSE 80

# Command to run Nginx and PHP-FPM
CMD php-fpm -D && nginx -g 'daemon off;'