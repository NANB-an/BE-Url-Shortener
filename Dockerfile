# Use PHP 7.4 FPM base image
FROM php:7.4-fpm

# Install system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring

# Install MongoDB PHP extension via PECL
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Install Composer (from Composer image)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files into container
COPY . .

# Install PHP dependencies via Composer
RUN composer install --no-dev --optimize-autoloader

# Set permissions for Laravel
RUN chown -R www-data:www-data \
    /var/www/storage \
    /var/www/bootstrap/cache \
    /var/www/vendor

# Expose port for Laravel’s built-in server
EXPOSE 8000

# Start Laravel app
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
