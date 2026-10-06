FROM php:8.2-apache

# Cài đặt các thư viện hệ thống cần thiết
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip

# Xóa cache để làm nhẹ image
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Cài đặt các thư viện mở rộng của PHP (pdo_mysql rất quan trọng để kết nối Aiven)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Bật module Rewrite của Apache (cần thiết cho route của Laravel)
RUN a2enmod rewrite

# Cấu hình thư mục gốc của Apache trỏ vào thư mục public của Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Copy toàn bộ mã nguồn vào container
COPY . .

# Chạy lệnh cài đặt thư viện Laravel
RUN composer install --no-dev --optimize-autoloader

# Phân quyền cho phép ghi vào thư mục storage và cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache