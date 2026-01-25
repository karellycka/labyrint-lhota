# Dockerfile for Škola Labyrint
# PHP 8.1 with Apache for Railway.app

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

# Fix MPM configuration - disable all MPMs first, then enable only prefork
RUN a2dismod mpm_event mpm_worker mpm_prefork 2>/dev/null || true
RUN a2enmod mpm_prefork

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Configure Apache document root to public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Create necessary directories and set permissions
RUN mkdir -p storage/cache storage/logs storage/sessions public/uploads
RUN chown -R www-data:www-data storage public/uploads
RUN chmod -R 775 storage public/uploads

# Copy .htaccess for production (remove base path)
RUN if [ -f public/.htaccess.production ]; then \
    cp public/.htaccess.production public/.htaccess; \
    fi

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
