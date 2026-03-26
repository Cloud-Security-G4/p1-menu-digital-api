#!/bin/sh
set -e

echo "Starting container..."

echo "Fixing permissions..."
chown -R www-data:www-data storage bootstrap/cache || true

echo "Ensuring storage symlink..."
if [ ! -L public/storage ]; then
    php artisan storage:link || true
fi

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

exec "$@"