# Imagen del intérprete PHP + Composer.

FROM php:8.3-fpm-alpine

# Composer: se copia SOLO el binario desde su imagen oficial.
# Es multi-stage: de esa imagen no arrastramos nada más que el ejecutable.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Composer avisa si corre como root; en contenedor es lo normal.
ENV COMPOSER_ALLOW_SUPERUSER=1

# Composer necesita descomprimir paquetes. Sin `unzip` cae a métodos lentos.
RUN apk add --no-cache unzip

WORKDIR /var/www/backend

# Día 8: aquí entrarán extensiones para Firestore (grpc, sodium).
#
# PRODUCCIÓN (sin bind mount) tendría además:
#   COPY . .
#   RUN composer install --no-dev --optimize-autoloader
# En desarrollo esas dos líneas NO bastan: el bind mount oculta el vendor
# que producen. Por eso el install se corre a mano dentro del contenedor.
