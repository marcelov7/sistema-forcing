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
# Create .env from environment variables\n\
echo "APP_NAME=${APP_NAME:-Laravel}" > .env\n\
echo "APP_ENV=${APP_ENV:-production}" >> .env\n\
echo "APP_DEBUG=${APP_DEBUG:-false}" >> .env\n\
echo "APP_URL=${APP_URL:-http://localhost}" >> .env\n\
echo "DB_CONNECTION=${DB_CONNECTION:-sqlite}" >> .env\n\
echo "DB_HOST=${DB_HOST:-127.0.0.1}" >> .env\n\
echo "DB_PORT=${DB_PORT:-3306}" >> .env\n\
echo "DB_DATABASE=${DB_DATABASE:-database}" >> .env\n\
echo "DB_USERNAME=${DB_USERNAME:-root}" >> .env\n\
echo "DB_PASSWORD=${DB_PASSWORD:-}" >> .env\n\
echo "MAIL_MAILER=${MAIL_MAILER:-smtp}" >> .env\n\
echo "MAIL_HOST=${MAIL_HOST:-localhost}" >> .env\n\
echo "MAIL_PORT=${MAIL_PORT:-587}" >> .env\n\
echo "MAIL_USERNAME=${MAIL_USERNAME:-}" >> .env\n\
echo "MAIL_PASSWORD=${MAIL_PASSWORD:-}" >> .env\n\
echo "MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-tls}" >> .env\n\
echo "MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS:-hello@example.com}" >> .env\n\
echo "MAIL_FROM_NAME=${MAIL_FROM_NAME:-Example}" >> .env\n\
echo "CACHE_DRIVER=${CACHE_DRIVER:-file}" >> .env\n\
echo "SESSION_DRIVER=${SESSION_DRIVER:-file}" >> .env\n\
echo "QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}" >> .env\n\
# Generate key\n\
php artisan key:generate --force\n\
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
