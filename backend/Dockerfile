FROM php:8.3-fpm-alpine
# php:8.3-fpm-alpine → PHP con FPM (FastCGI Process Manager)
# FPM es el modo de PHP para trabajar con NGINX

# Instalar dependencias del sistema
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    mysql-client

# Instalar extensiones PHP necesarias para Laravel
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd
# pdo_mysql → conexión a MySQL
# mbstring → strings multibyte (necesario para Laravel)
# bcmath → precisión numérica (necesario para hashing)

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .
# Copia todo el código de Laravel

RUN composer install --optimize-autoloader --no-dev
# --no-dev → no instala dependencias de desarrollo en producción
# --optimize-autoloader → genera autoloader optimizado, más rápido

# Elimina paquetes dev del caché de autodiscovery
RUN php -r "\
\$p = require 'bootstrap/cache/packages.php'; \
unset(\$p['laravel/pail'], \$p['nunomaduro/collision']); \
file_put_contents('bootstrap/cache/packages.php', '<?php return ' . var_export(\$p, true) . ';');"

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
# www-data es el usuario de PHP-FPM
# Laravel necesita escribir en storage/ y bootstrap/cache/

EXPOSE 9000
# Puerto de PHP-FPM — no es HTTP, es FastCGI
