#!/bin/sh
set -e

echo "Starting container..."

echo "Fixing permissions..."
chown -R www-data:www-data storage bootstrap/cache || true

echo "Ensuring storage symlink..."
if [ ! -L public/storage ]; then
    php artisan storage:link || true
fi

echo "Configuring Apache port..."
PORT=${PORT:-80}
sed -i "s/^Listen 80$/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-available/000-default.conf
sed -i "s/\*:80/\*:$PORT/" /etc/apache2/sites-available/000-default.conf

if [ "${ENABLE_DB_INIT}" = "true" ]; then
  echo "Waiting for DB..."

  until php -r "
try {
    new PDO(
        getenv('DB_CONNECTION').':host='.getenv('DB_HOST').';dbname='.getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
} catch (Exception \$e) {
    exit(1);
}
"; do
    echo "DB not ready, retrying..."
    sleep 3
  done

  echo "Running migrations..."
  su -s /bin/sh www-data -c "php artisan migrate --force"

  echo "Ensuring Passport keys..."
  if [ ! -f storage/oauth-private.key ]; then
      su -s /bin/sh www-data -c "php artisan passport:keys"
  fi
fi

exec "$@"