# syntax=docker/dockerfile:1

# ------------------------------------------------------------------------------
# Stage 1: Build dependencies
# ------------------------------------------------------------------------------
FROM composer:2 AS vendor_builder

WORKDIR /app

COPY composer.json composer.lock* ./

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# ------------------------------------------------------------------------------
# Stage 2: Production image
# ------------------------------------------------------------------------------
FROM php:8.2-apache-bookworm

# Install PHP extensions
RUN docker-php-ext-install intl mbstring mysqli

# Enable Apache modules
RUN a2enmod rewrite headers

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY --from=vendor_builder /app/vendor /var/www/html/vendor
COPY app /var/www/html/app
COPY public /var/www/html/public
COPY system /var/www/html/system
COPY writable /var/www/html/writable

# Set permissions
RUN chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 755 /var/www/html/writable

# Apache configuration for Dokku
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf \
    && echo 'DirectoryIndex index.php index.html' >> /etc/apache2/apache2.conf

# VirtualHost for Document Root
RUN cat > /etc/apache2/sites-available/000-default.conf << 'EOF'
<VirtualHost *:80>
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted

        # Rewrite rules for CodeIgniter
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php/$1 [L]
    </Directory>

    # Security headers
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
</VirtualHost>
EOF

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]

  