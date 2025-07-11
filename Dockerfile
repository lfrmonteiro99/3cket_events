# Multi-stage Dockerfile optimized for Docker Buildx
# This provides better caching and faster builds

# Base stage with PHP and system dependencies
FROM php:8.2-fpm AS base

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Set working directory
WORKDIR /var/www

# Composer stage for dependency installation
FROM base AS composer

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files first for better layer caching
COPY composer.json composer.lock* ./

# Install dependencies with optimizations
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --prefer-dist

# Development stage (includes dev dependencies)
FROM composer AS development

# Install dev dependencies
RUN composer install \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --prefer-dist

# Copy application code
COPY . .

# Create necessary directories
RUN mkdir -p data tests/fixtures

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Expose port 9000 for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]

# Production stage (optimized for production)
FROM base AS production

# Copy Composer binary
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files
COPY composer.json composer.lock* ./

# Install production dependencies only
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    && composer clear-cache

# Copy application code
COPY . .

# Create necessary directories
RUN mkdir -p data tests/fixtures

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Remove composer after installation (production optimization)
RUN rm /usr/bin/composer

# Expose port 9000 for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"] 