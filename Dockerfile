# Usa una imagen base de PHP-FPM para Laravel con PHP 8.3 (Alpine para ser ligera)
FROM php:8.3-fpm-alpine

# Instala dependencias del sistema operativo necesarias para Laravel, Nginx y PostgreSQL
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
    # ¡CORRECCIÓN DE SINTAXIS AQUÍ! Asegúrate de que no haya comentarios en la misma línea que '\'
    libxml2-dev \
    libzip-dev \
    # Algunas veces, para GD avanzado o Exif, se necesita ImageMagick
    # imagemagick-dev \

    # Limpia la caché de APK para reducir el tamaño del tamaño de la imagen
    && rm -rf /var/cache/apk/*

# Instala extensiones de PHP requeridas por Laravel y para la conexión a PostgreSQL
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && rm -rf /tmp/* /usr/share/doc/*

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

# Expone el puerto de PHP-FPM
EXPOSE 9000

# Comando para iniciar el servicio PHP-FPM cuando el contenedor se inicie
CMD ["php-fpm"]