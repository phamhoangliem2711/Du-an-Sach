FROM php:8.3-apache

# Cài đặt extensions PHP cần thiết
RUN docker-php-ext-install mysqli pdo pdo_mysql && \
    a2enmod rewrite

# Copy toàn bộ source code
COPY . /var/www/html/

# Copy file cấu hình Apache
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Cấp quyền cho thư mục
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

EXPOSE 80