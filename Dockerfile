# STAGE 1: Build & Test
FROM php:8.2-cli AS builder
WORKDIR /app

# --- ADD THIS SECTION ---
# Install system dependencies for Composer
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*
# ------------------------

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy only the dependency files first
COPY composer.json composer.lock ./

# Install all dependencies
RUN composer install --no-interaction

# Copy the rest of the app and run tests
COPY . .

# Set the environment variables specifically for the test environment
ENV APP_ENV=dev
ENV UPLOAD_PATH=/app/uploads

# Create the folder that the test expects
RUN mkdir -p /app/uploads && chmod 777 /app/uploads

# Run the tests
RUN ./vendor/bin/pest

# ---------------------------------------------------------

# STAGE 2: Production
FROM php:8.2-apache
WORKDIR /var/www/html

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy only the application and the production vendor folder
COPY --from=builder /app /var/www/html

# Prepare the uploads directory with correct permissions
RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploads && \
    chmod -R 775 /var/www/html/uploads

# Set APP_ENV to dev so Pest can run copy/unlink logic
# Note: We'll change this to 'production' in the final K8s manifest
ENV APP_ENV=dev

# Update Apache DocumentRoot to point to the public folder
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80