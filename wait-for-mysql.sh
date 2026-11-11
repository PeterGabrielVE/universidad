#!/bin/sh

echo "Esperando a MySQL en $DB_HOST:$DB_PORT..."

# La imagen no incluye el cliente mysql: se comprueba la conexión con PDO.
until php -r 'new PDO("mysql:host=" . getenv("DB_HOST") . ";port=" . getenv("DB_PORT"), getenv("DB_USERNAME"), getenv("DB_PASSWORD"));' > /dev/null 2>&1
do
  echo "MySQL aún no está listo. Esperando..."
  sleep 2
done

echo "MySQL está listo. Iniciando Laravel..."

php artisan config:clear
php artisan cache:clear

# Generar la clave solo la primera vez: regenerarla en cada arranque invalida
# sesiones, cookies y cualquier dato cifrado.
if ! grep -q '^APP_KEY=base64:' .env; then
  php artisan key:generate
fi

php artisan migrate --force

chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "Iniciando Apache..."
exec apache2-foreground
