FROM php:8.4-fpm

# Set build arguments for UID and GID
ARG HOST_UID=1000
ARG HOST_GID=1000

# Install system dependencies
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Create group and user to match host UID/GID
RUN groupadd -g ${HOST_GID} appuser \
    && useradd -u ${HOST_UID} -g appuser -m appuser

# Set permissions for Laravel directories
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R appuser:appuser /var/www/html/storage /var/www/html/bootstrap/cache

# Set PHP error logging
RUN echo "error_log = /var/log/php-fpm.log" > /usr/local/etc/php/conf.d/docker-php-errorlog.ini \
    && echo "log_errors = On" >> /usr/local/etc/php/conf.d/docker-php-errorlog.ini

# Set working directory
WORKDIR /var/www/html

# Switch to the new user
USER appuser

CMD ["php-fpm"]
