FROM php:8.2-fpm-alpine

# Зависимости
RUN apk update && \
    apk add --no-cache \
    git \
    curl \
    bash \
    libzip-dev \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    mariadb-connector-c-dev \
    autoconf \
    g++ \
    make

# Расширения
RUN docker-php-ext-install pdo pdo_mysql zip && \
    docker-php-ext-configure gd --with-freetype --with-webp --with-jpeg && \
    docker-php-ext-install gd

# Redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Рабочая директория
WORKDIR /var/www

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Копирование
COPY . .

# Композер
RUN composer install --no-dev --optimize-autoloader

# Запуск
EXPOSE 9000
CMD ["php-fpm"]