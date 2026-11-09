#!/bin/sh
# =============================================================
# dokku/entrypoint.sh - Sistem Aset UIR
# Generate /app/.env dari Dokku config vars (key TANPA titik)
# setiap container start, sehingga rebuild tidak membutuhkan
# .env / .env.production di dalam repository.
#
# Sumber konfigurasi (urutan prioritas):
#   1. DATABASE_URL (dari `dokku mariadb:link DBku phpaset`)
#   2. DB_HOST / DB_PORT / DB_NAME / DB_USER / DB_PASSWORD
#
# Key config vars yang dipakai:
#   CI_ENVIRONMENT (default: production)
#   BASE_URL       (default: https://siaset.webuir.com/)
#   DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD
# =============================================================

if [ -n "$DB_PASSWORD" ] || [ -n "$DATABASE_URL" ]; then
    DB_HOST_EFF="${DB_HOST:-172.17.0.3}"
    DB_PORT_EFF="${DB_PORT:-3306}"
    DB_NAME_EFF="${DB_NAME:-uir_aset}"
    DB_USER_EFF="${DB_USER:-root_uir}"
    DB_PASSWORD_EFF="${DB_PASSWORD:-}"

    # DATABASE_URL format: mysql://user:password@host:port/dbname
    if [ -n "$DATABASE_URL" ]; then
        URL="${DATABASE_URL#mysql://}"
        CRED="${URL%%@*}"
        REST="${URL#*@}"
        DB_USER_EFF="${CRED%%:*}"
        DB_PASSWORD_EFF="${CRED#*:}"
        DB_HOST_EFF="${REST%%:*}"
        REST="${REST#*:}"
        DB_PORT_EFF="${REST%%/*}"
        DB_NAME_EFF="${REST#*/}"
        echo "entrypoint: using DATABASE_URL from mariadb:link (host=$DB_HOST_EFF)"
    fi

    cat > /app/.env <<EOF
#--------------------------------------------------------------------
# AUTO-GENERATED oleh dokku/entrypoint.sh saat container start.
# JANGAN edit manual - sumber kebenaran adalah Dokku config vars:
#   dokku config:set phpaset DB_PASSWORD=... dst.
#--------------------------------------------------------------------
CI_ENVIRONMENT = ${CI_ENVIRONMENT:-production}

app.baseURL = ${BASE_URL:-https://siaset.webuir.com/}

database.default.hostname = ${DB_HOST_EFF}
database.default.port = ${DB_PORT_EFF}
database.default.database = ${DB_NAME_EFF}
database.default.username = ${DB_USER_EFF}
database.default.password = ${DB_PASSWORD_EFF}
EOF
    echo "entrypoint: /app/.env generated."
fi

# Tanpa DB_PASSWORD/DATABASE_URL: pakai /app/.env yang ada (mis. dev build)
php-fpm -D
exec nginx -g 'daemon off;' -c /etc/nginx/nginx.conf
