
# syntax=docker/dockerfile:1

FROM php:8.3-fpm-bullseye

RUN printf "deb http://deb.debian.org/debian bullseye main contrib non-free\n\
deb http://security.debian.org/debian-security bullseye-security main contrib non-free\n\
deb http://deb.debian.org/debian bullseye-updates main contrib non-free\n" > /etc/apt/sources.list

RUN apt-get update && apt-get install -y --no-install-recommends \
    bash git unzip \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    zlib1g-dev libzip-dev libonig-dev \
 && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring exif pcntl bcmath zip gd

WORKDIR /var/www/product-ticket

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-progress

COPY . .

RUN composer dump-autoload --optimize --no-dev

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
