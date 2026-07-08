FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && cp .env.example .env \
    && php artisan key:generate

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE ${PORT:-8000}
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
