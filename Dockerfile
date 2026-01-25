# Dockerfile for Škola Labyrint
# PHP 8.1 with Apache for Railway.app
# SIMPLIFIED - avoid MPM conflicts

FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Fix Apache MPM configuration - ensure only mpm_prefork is loaded
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

# Set document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy application
WORKDIR /var/www/html
COPY . /var/www/html

# Copy production .htaccess
RUN if [ -f public/.htaccess.production ]; then \
    cp public/.htaccess.production public/.htaccess; \
    fi

# Create directories and set permissions
RUN mkdir -p storage/cache storage/logs storage/sessions public/uploads \
    && chown -R www-data:www-data storage public/uploads \
    && chmod -R 775 storage public/uploads

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
