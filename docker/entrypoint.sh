#!/bin/sh
set -e
set -x

echo "Fixing permissions..."
chown -R www-data:www-data storage bootstrap/cache database || true

echo "Ensuring SQLite database exists..."
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

chown www-data:www-data database/database.sqlite
chmod 664 database/database.sqlite

APP_KEY_FILE=/run/secrets/app.key

mkdir -p /run/secrets

if [ ! -f "$APP_KEY_FILE" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --show > "$APP_KEY_FILE"
else
    echo "Using existing APP_KEY"
fi

export APP_KEY=$(cat "$APP_KEY_FILE")

echo "Ensuring storage symlink exists..."
if [ ! -L public/storage ]; then
    echo "Creating storage symlink..."
    php artisan storage:link || true
    chown -h www-data:www-data public/storage || true
fi

echo "Running migrations..."
su -s /bin/sh www-data -c "php artisan migrate --force"

echo "Setting up Passport..."
su -s /bin/sh www-data -c "php artisan passport:keys --force"
su -s /bin/sh www-data -c "php artisan passport:client --personal --no-interaction" || true

exec "$@"