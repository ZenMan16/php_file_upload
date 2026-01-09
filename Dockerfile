FROM php:8.2.14-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    libzip-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install bcmath zip

# Verify FPM config exists (this will fail the build if it's missing, which is good for debugging)
RUN ls /usr/local/etc/php-fpm.d/www.conf.default

# Activate the default config
RUN cp /usr/local/etc/php-fpm.d/www.conf.default /usr/local/etc/php-fpm.d/www.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

# Expose FPM port
EXPOSE 9000

CMD ["php-fpm"]