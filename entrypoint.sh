#!/bin/bash

# Asegurar permisos correctos
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Limpiar cachés (opcional, útil para desarrollo)
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Ejecutar migraciones automáticamente (opcional)
# php artisan migrate --force

# Iniciar Apache en primer plano
exec apache2-foreground
