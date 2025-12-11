FROM serversideup/php:8.4-fpm-nginx

USER root

# Install intl extension using the helper script provided by serversideup
RUN install-php-extensions intl

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data