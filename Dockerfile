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
# Create .env from environment variables (as root, then change ownership)\n\
cat > /var/www/.env << EOF\n\
APP_NAME=${APP_NAME:-Laravel}\n\
APP_ENV=${APP_ENV:-production}\n\
APP_DEBUG=${APP_DEBUG:-false}\n\
APP_URL=${APP_URL:-http://localhost}\n\
DB_CONNECTION=${DB_CONNECTION:-mysql}\n\
DB_HOST=${DB_HOST:-127.0.0.1}\n\
DB_PORT=${DB_PORT:-3306}\n\
DB_DATABASE=${DB_DATABASE:-database}\n\
DB_USERNAME=${DB_USERNAME:-root}\n\
DB_PASSWORD=${DB_PASSWORD:-}\n\
MAIL_MAILER=${MAIL_MAILER:-smtp}\n\
MAIL_HOST=${MAIL_HOST:-localhost}\n\
MAIL_PORT=${MAIL_PORT:-587}\n\
MAIL_USERNAME=${MAIL_USERNAME:-}\n\
MAIL_PASSWORD=${MAIL_PASSWORD:-}\n\
MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-tls}\n\
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS:-hello@example.com}\n\
MAIL_FROM_NAME=${MAIL_FROM_NAME:-Example}\n\
CACHE_DRIVER=${CACHE_DRIVER:-file}\n\
SESSION_DRIVER=${SESSION_DRIVER:-file}\n\
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}\n\
EOF\n\
# Change ownership to www-data\n\
chown www-data:www-data /var/www/.env\n\
# Switch to www-data user for Laravel commands\n\
su -s /bin/bash www-data -c "cd /var/www && php artisan key:generate --force"\n\
su -s /bin/bash www-data -c "cd /var/www && php artisan migrate --force"\n\
su -s /bin/bash www-data -c "cd /var/www && php artisan config:cache"\n\
su -s /bin/bash www-data -c "cd /var/www && php artisan route:cache"\n\
su -s /bin/bash www-data -c "cd /var/www && php artisan view:cache"\n\
# Start server as www-data\n\
su -s /bin/bash www-data -c "cd /var/www && php artisan serve --host=0.0.0.0 --port=8080"' > /var/www/start.sh && chmod +x /var/www/start.sh

# Change ownership of startup script
RUN chown www-data:www-data /var/www/start.sh

# Expose port 8080
EXPOSE 8080

# Start with our script (as root, script handles user switching)
CMD ["/bin/bash", "/var/www/start.sh"]
