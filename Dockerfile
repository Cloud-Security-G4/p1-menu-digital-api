FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN a2enmod rewrite

WORKDIR /var/www/html
# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .

# Luego instalar dependencias
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permisos mínimos
RUN chown -R www-data:www-data storage bootstrap/cache

# Apache apunta a public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]

EXPOSE 80