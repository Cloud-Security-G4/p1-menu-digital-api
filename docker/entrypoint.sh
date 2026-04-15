#!/bin/sh
set -e

echo "Starting container..."

PORT=${PORT:-8080}

echo "Configuring Apache on port ${PORT}..."

# Config limpia
echo "Listen ${PORT}" > /etc/apache2/ports.conf

sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" \
    /etc/apache2/sites-available/000-default.conf

# Permisos
chown -R www-data:www-data storage bootstrap/cache || true

# Laravel init
if [ ! -L public/storage ]; then
    php artisan storage:link || true
fi

# DB init opcional (no bloqueante)
if [ "${ENABLE_DB_INIT}" = "true" ]; then
  for i in $(seq 1 10); do
    php -r "
    try {
        new PDO(
            getenv('DB_CONNECTION').':host='.getenv('DB_HOST').';dbname='.getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
    } catch (Exception \$e) {
        exit(1);
    }" && break
    sleep 3
  done

  su -s /bin/sh www-data -c "php artisan migrate --force" || true

  if [ ! -f storage/oauth-private.key ]; then
      su -s /bin/sh www-data -c "php artisan passport:keys" || true
  fi
fi

# Debug útil
echo "=== Apache config ==="
cat /etc/apache2/ports.conf

exec "$@"