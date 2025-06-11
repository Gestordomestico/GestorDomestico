# Usa una imagen base de PHP-FPM para Laravel con PHP 8.3 (Alpine para ser ligera y segura)
FROM php:8.3-fpm-alpine

# --- PASO 1: INSTALACIÓN DE DEPENDENCIAS DEL SISTEMA ---
# Instala las herramientas de compilación y las librerías de desarrollo necesarias.
# Las versiones -dev son cruciales para compilar extensiones PHP.
# Agregamos dependencias comunes que suelen faltar.
RUN apk add --no-cache \
    nginx \
    postgresql-dev \
    git \
    zip \
    unzip \
    nodejs \
    npm \
    # Dependencias para GD (gráficos):
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    jpeg-dev \
    # Dependencias generales para PHP y Composer:
    icu-dev \          # Para la extensión intl
    build-base \       # Herramientas de compilación básicas (gcc, g++, make, etc.)
    autoconf \         # Herramienta de configuración automática
    g++ \              # Compilador C++
    oniguruma-dev \    # Para mbstring
    libxml2-dev \      # Para varias extensiones XML/DOM
    libzip-dev \       # Para la extensión Zip de PHP
    zlib-dev \         # Para compresión, esencial para muchas extensiones
    # Limpieza de caché de APK para reducir el tamaño final de la imagen
    && rm -rf /var/cache/apk/*

# --- PASO 2: INSTALACIÓN Y CONFIGURACIÓN DE EXTENSIONES PHP ---
# Instalamos las extensiones que Laravel comúnmente necesita.
# 'zip' se instala como una extensión separada ya que tiene una dependencia específica (libzip-dev).
# 'gd' requiere una configuración previa.
RUN docker-php-ext-install -j$(nproc) \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    intl \
    zip \
    # Limpiamos los archivos temporales después de la instalación de estas extensiones
    && rm -rf /tmp/* /usr/share/doc/*

# Configuración e instalación de GD por separado.
# Es crucial que 'jpeg-dev' y las otras libs de imagen estén instaladas antes de esto.
RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install -j$(nproc) gd \
    # Limpieza adicional después de GD
    && rm -rf /tmp/* /usr/share/doc/* /var/cache/apk/*

# --- PASO 3: INSTALACIÓN DE COMPOSER ---
# Copia el binario de Composer desde una imagen oficial de Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# --- PASO 4: COPIA DEL CÓDIGO Y CONFIGURACIÓN DE LARAVEL ---
# Establece el directorio de trabajo predeterminado dentro del contenedor
WORKDIR /var/www/html

# Copia todos los archivos de tu aplicación Laravel al contenedor.
# Optimización: Esto se hace después de instalar dependencias,
# para que los cambios en el código no invaliden las capas de dependencias.
COPY . .

# Instala las dependencias PHP de Laravel usando Composer.
# '--no-dev' para no instalar dependencias de desarrollo, '--optimize-autoloader' para rendimiento.
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# --- PASO 5: COMPILACIÓN DE ASSETS DE FRONTEND (NPM) ---
# Instala las dependencias de Node.js y compila los assets (CSS/JS) para producción.
# Esto es crucial si usas Laravel Mix o Vite para Bootstrap, etc.
# Si no tienes assets de frontend o los pre-compilas localmente, puedes omitir estas dos líneas.
RUN npm install \
    && npm run build # O 'npm run production' si usas Laravel Mix

# --- PASO 6: MIGRACIONES DE BASE DE DATOS Y PERMISOS ---
# Ejecuta las migraciones de la base de datos.
# Esto es esencial para entornos sin acceso a la shell como Render.
# Asegúrate de que tu aplicación Laravel maneje esto elegantemente (ej. no falle si las tablas ya existen).
RUN php artisan migrate --force

# Configura los permisos de los directorios de almacenamiento y caché de Laravel.
# Es vital para que la aplicación pueda escribir logs, caché, sesiones, etc.
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# --- PASO 7: CONFIGURACIÓN FINAL ---
# Expone el puerto 9000, que es donde PHP-FPM escuchará las solicitudes de Nginx.
EXPOSE 9000

# Comando por defecto que se ejecutará cuando el contenedor inicie.
# Inicia el procesador PHP-FPM.
CMD ["php-fpm"]