# =========================
# 1. Composer stage
# =========================
FROM composer:2 AS vendor
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader \
    --no-scripts

# =========================
# 2. Runtime stage
# =========================
FROM php:8.4-apache

# Install PHP Extension Postgresql
RUN apt-get update && apt-get install -y libpq-dev \
&& docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
&& docker-php-ext-install pdo pdo_pgsql pgsql

# Configure PHP to log errors to stderr
RUN { \
    echo "log_errors = On"; \
    echo "display_errors = Off"; \
    echo "error_log = /proc/self/fd/2"; \
} > /usr/local/etc/php/conf.d/docker-logging.ini

WORKDIR /var/www/html

COPY ./docker/production/apache/default.conf /etc/apache2/sites-available/000-default.conf

# Copy app
COPY  . .
COPY --from=vendor /app/vendor ./vendor

EXPOSE 80
VOLUME ["/var/www/html/public/uploads"]
