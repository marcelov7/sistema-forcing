# Use PHP 8.1 official image
FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate application key
RUN php artisan key:generate --force

# Cache configuration
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Change current user to www
USER www-data

# Expose port 8080
EXPOSE 8080

# Start PHP built-in server
CMD php artisan serve --host=0.0.0.0 --port=8080
