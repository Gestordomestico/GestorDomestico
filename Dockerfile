# Usa una imagen base de PHP-FPM para Laravel con PHP 8.3 (Alpine para ser ligera)
FROM php:8.3-fpm-alpine

# Instala dependencias del sistema operativo necesarias
RUN apk add --no-cache \
    nginx \
    postgresql-dev \
    git \
    zip \
    unzip \
    nodejs \
    npm \
    libpng \
    libjpeg-turbo \
    libwebp \
    freetype \
    icu-dev \
    build-base \
    autoconf \
    g++ \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    # ¡NUEVA ADICIÓN AQUÍ! zlib-dev es una dependencia muy común
    zlib-dev \
    # Elimina la caché de APK para reducir el tamaño de la imagen
    && rm -rf /var/cache/apk/*

# Instala y configura extensiones de PHP
# Separamos la instalación de la configuración de GD para mayor claridad y depuración
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath zip
# Configuración específica para GD después de la instalación
RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype
RUN docker-php-ext-install gd

# Limpieza después de la instalación de extensiones (opcionalmente)
RUN rm -rf /tmp/* /usr/share/doc/*

# Instala Composer globalmente en el contenedor
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Establece el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copia los archivos de tu aplicación Laravel al contenedor
COPY . .

# Instala las dependencias de Composer (solo de producción y optimizadas)
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Compila los assets de frontend (CSS/JS) si usas Laravel Mix o Vite
RUN npm install \
    && npm run build # O 'npm run production' si usas Laravel Mix

# Ejecuta las migraciones de la base de datos
RUN php artisan migrate --force

# Configura los permisos de los directorios de almacenamiento y caché de Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Exponer el puerto de PHP-FPM
EXPOSE 9000

# Comando para iniciar el servicio PHP-FPM cuando el contenedor se inicie
CMD ["php-fpm"]