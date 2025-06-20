#!/bin/sh

echo "Esperando a MySQL en $DB_HOST:$DB_PORT..."

until mysql -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1;" &> /dev/null
do
  echo "MySQL aún no está listo. Esperando..."
  sleep 2
done

echo "MySQL está listo. Iniciando Laravel..."

php artisan config:clear
php artisan cache:clear
php artisan key:generate
php artisan migrate --force

chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "Iniciando Apache..."
exec apache2-foreground
