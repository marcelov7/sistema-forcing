# Use PHP 8.2 official image
FROM php:8.2-apache

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

# Create startup script (before changing user)
RUN echo '#!/bin/bash\n\
# Generate key if not exists\n\
if [ -z "$APP_KEY" ]; then\n\
    php artisan key:generate --force\n\
fi\n\
# Run migrations\n\
php artisan migrate --force\n\
# Cache configuration\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
# Start server\n\
php artisan serve --host=0.0.0.0 --port=8080' > /var/www/start.sh && chmod +x /var/www/start.sh

# Change ownership of startup script
RUN chown www-data:www-data /var/www/start.sh

# Change current user to www
USER www-data

# Expose port 8080
EXPOSE 8080

# Start with our script
CMD ["/bin/bash", "/var/www/start.sh"]
