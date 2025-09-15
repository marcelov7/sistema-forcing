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

# Create a simple startup script for debugging
RUN echo '#!/bin/bash\n\
echo "=== DEBUGGING STARTUP ==="\n\
echo "Current user: $(whoami)"\n\
echo "Current directory: $(pwd)"\n\
echo "Environment variables:"\n\
env | grep -E "(APP_|DB_|MAIL_)" | head -10\n\
echo "========================="\n\
\n\
# Copy .env.example to .env if it exists, or create basic .env\n\
if [ -f /var/www/.env.example ]; then\n\
    cp /var/www/.env.example /var/www/.env\n\
else\n\
    echo "APP_NAME=Laravel" > /var/www/.env\n\
    echo "APP_ENV=production" >> /var/www/.env\n\
    echo "APP_DEBUG=true" >> /var/www/.env\n\
    echo "APP_KEY=" >> /var/www/.env\n\
    echo "DB_CONNECTION=sqlite" >> /var/www/.env\n\
    echo "DB_DATABASE=/var/www/database/database.sqlite" >> /var/www/.env\n\
fi\n\
\n\
# Create database directory and file\n\
mkdir -p /var/www/database\n\
touch /var/www/database/database.sqlite\n\
chown -R www-data:www-data /var/www/database\n\
chown www-data:www-data /var/www/.env\n\
\n\
# Generate key and run basic setup\n\
cd /var/www\n\
php artisan key:generate --force || echo "Key generation failed"\n\
php artisan migrate --force || echo "Migration failed"\n\
\n\
echo "=== STARTING SERVER ==="\n\
php artisan serve --host=0.0.0.0 --port=8080' > /var/www/start.sh && chmod +x /var/www/start.sh

# Change ownership of startup script
RUN chown www-data:www-data /var/www/start.sh

# Expose port 8080
EXPOSE 8080

# Start with our script (as root, script handles user switching)
CMD ["/bin/bash", "/var/www/start.sh"]
