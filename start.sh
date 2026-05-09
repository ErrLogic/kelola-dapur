#!/bin/sh
set -e

cd /app

echo "=== Kelola Service Booting Process ==="

# Ensure necessary directories and permissions
echo "[Permission] Setting up directories permission..."
mkdir -p /var/log/supervisor /var/run/supervisor /var/www/.config/caddy /var/www/.local/share/caddy
chown -R www-data:www-data /app/storage /app/bootstrap/cache /var/www
chmod -R 775 /app/storage /app/bootstrap/cache
rm -f /app/.dockerignore || true

# Seed .env if missing
if [ ! -f .env ]; then
  echo "[Init] .env not found, generating from .env.production"
  mv .env.production .env
fi

# -------------------------------------------
# Building assets
# -------------------------------------------
echo "[Build] Building static FE assets..."
npm install --silent
npm run build --silent

# -------------------------------------------
# Clear caches
# -------------------------------------------
echo "[Cache] Clearing old caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan event:clear || true
php artisan view:clear || true

# -------------------------------------------
# Rebuild caches
# -------------------------------------------
echo "[Cache] Rebuilding caches..."
php artisan config:cache || true
php artisan route:cache || true
php artisan event:cache || true
php artisan view:cache || true

# -------------------------------------------
# Start Supervisor
# -------------------------------------------
echo "[Start] Launching supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
