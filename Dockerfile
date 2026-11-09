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

RUN docker-php-ext-install pdo pdo_mysql mysqli intl mbstring zip opcache

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

# Kredensial TIDAK lagi disalin dari repository (.env.production sudah
# di-untrack). Konfigurasi produksi disuplai via Dokku config vars:
#   dokku config:set phpaset CI_ENVIRONMENT=production app.baseURL=... \
#     database.default.hostname=... database.default.password=...
# CI4 BaseConfig membaca override dot-notation dari environment variables.
RUN chown -R www-data:www-data /app

RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader --no-interaction --no-progress --ignore-platform-reqs; fi

COPY .dokku/nginx.conf /etc/nginx/http.d/default.conf
COPY .dokku/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8181

# entrypoint.sh men-generate /app/.env dari Dokku config vars
# (DB_PASSWORD dsb.) sebelum menyalakan php-fpm + nginx.
CMD ["/usr/local/bin/entrypoint.sh"]
