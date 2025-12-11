# Base PHP-FPM image
FROM php:8.3-fpm

# ------------------------
# Install system dependencies
# ------------------------
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libonig-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    mariadb-server \
    nginx \
    supervisor \
    && docker-php-ext-install pdo_mysql zip exif mbstring gd bcmath sockets \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ------------------------
# Install Composer
# ------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ------------------------
# Set working directory
# ------------------------
WORKDIR /var/www/html

# ------------------------
# Copy Laravel project
# ------------------------
COPY . .

# ------------------------
# Install PHP dependencies
# ------------------------
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ------------------------
# Set permissions
# ------------------------
RUN chown -R www-data:www-data storage bootstrap/cache

# ------------------------
# Copy Nginx config
# ------------------------
COPY docker/conf/nginx/default.conf /etc/nginx/conf.d/default.conf

# ------------------------
# Copy Supervisor config
# ------------------------
COPY docker/conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ------------------------
# Expose HTTP
# ------------------------
EXPOSE 80

# ------------------------
# Entrypoint script
# ------------------------
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

CMD ["/usr/local/bin/entrypoint.sh"]
