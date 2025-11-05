FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libpq-dev libzip-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql pdo_pgsql zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application
WORKDIR /var/www/html
COPY . .

# Install PHP dependencies
RUN composer install --no-scripts --no-autoloader
RUN composer dump-autoload --optimize

# Permissions
RUN chown -R www-data:www-data /var/www/html/var

EXPOSE 9000
CMD ["php-fpm"]
