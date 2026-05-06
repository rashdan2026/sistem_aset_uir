FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx \
    bash \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-install pdo pdo_mysql intl mbstring zip opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . /app

RUN mkdir -p \
    /app/writable/cache \
    /app/writable/debugbar \
    /app/writable/logs \
    /app/writable/session \
    /app/writable/uploads \
    /app/public/uploads \
    /run/nginx

# Gunakan production env saat build jika tersedia
RUN if [ -f /app/.env.production ]; then cp /app/.env.production /app/.env; fi

# Jangan biarkan .env localhost ikut dipakai kalau sudah ada .env.production
RUN chown -R www-data:www-data /app

RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader --no-interaction --no-progress --ignore-platform-reqs; fi

COPY .dokku/nginx.conf /etc/nginx/http.d/default.conf

EXPOSE 8181

CMD sh -c "php-fpm -D && nginx -g 'daemon off;' -c /etc/nginx/nginx.conf"
