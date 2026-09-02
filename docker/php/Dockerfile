FROM php:8.3-cli-alpine AS vendor

WORKDIR /app

RUN apk add --no-cache git unzip icu-dev libzip-dev \
    && docker-php-ext-install intl exif zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --no-scripts

COPY . .
RUN composer dump-autoload --optimize --no-dev --no-interaction

FROM node:20-alpine AS assets

WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
COPY postcss.config.js ./
COPY tailwind.config.js ./
RUN npm run build

FROM php:8.3-fpm-alpine

WORKDIR /var/www

RUN apk add --no-cache \
    bash \
    curl \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    mysql-client \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    imagemagick-dev \
    imagemagick \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        bcmath \
        exif \
        gd \
        intl \
        pdo_mysql \
        zip \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apk del $PHPIZE_DEPS \
    && echo 'expose_php = Off' > /usr/local/etc/php/conf.d/security.ini

COPY --from=vendor /app /var/www
COPY --from=assets /app/public/build /var/www/public/build

RUN rm -rf /var/www/public/storage \
    && mkdir -p /var/www/storage/app/public \
    && ln -s /var/www/storage/app/public /var/www/public/storage

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
