#!/usr/bin/env bash
# =============================================================================
# TretanInvite — Deployment Script
# Jalankan di root project setelah upload file ke server:
#   chmod +x deploy.sh
#   ./deploy.sh
# =============================================================================
set -e

echo "========================================"
echo "  TretanInvite — Deploy"
echo "========================================"

# 1. Install PHP dependencies (--no-dev untuk production)
echo ""
echo "[1/9] Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# 1b. Audit dependency untuk kerentanan keamanan (CVE)
echo ""
echo "[1b/9] Checking for known vulnerabilities (composer audit)..."
composer audit --no-dev || {
    echo "⚠️  PERINGATAN: Ditemukan kerentanan di dependency!"
    echo "     Periksa output di atas dan update dependency yang bermasalah."
    echo "     Deploy dilanjutkan — selesaikan setelah deploy."
}

# 2. Install & build front-end assets
echo ""
echo "[2/9] Building front-end assets..."
npm ci
npm run build

# 3. Generate app key jika belum ada
echo ""
echo "[3/9] Checking APP_KEY..."
php artisan key:generate --no-interaction --skip-if-set

# 4. Jalankan migrasi database
echo ""
echo "[4/9] Running database migrations..."
php artisan migrate --force

# 5. Buat symlink storage (foto profil, QRIS, bukti bayar)
echo ""
echo "[5/9] Creating storage symlink..."
php artisan storage:link

# 6. Optimize: cache config, route, view
echo ""
echo "[6/9] Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Set permission folder storage & bootstrap/cache
echo ""
echo "[7/9] Setting directory permissions..."
chmod -R 755 storage bootstrap/cache

# 8. Backup database sebelum deploy (opsional, uncomment jika perlu)
# echo ""
# echo "[8/9] Backing up database..."
# php artisan db:backup --keep=14

# 9. Restart queue worker (jika pakai queue)
# Uncomment jika pakai supervisor/systemd untuk queue:
# echo ""
# echo "[9/9] Restarting queue worker..."
# php artisan queue:restart

echo ""
echo "========================================"
echo "  Deploy selesai!"
echo "========================================"
echo ""
echo "Checklist manual yang WAJIB dilakukan:"
echo "  [ ] .env sudah diisi lengkap:"
echo "      APP_URL=https://domain-anda.com"
echo "      APP_DEBUG=false"
echo "      APP_ENV=production"
echo "      APP_TIMEZONE=Asia/Jakarta"
echo "      DB_HOST / DB_DATABASE / DB_USERNAME / DB_PASSWORD"
echo "      ADMIN_PASSWORD=<password-kuat>"
echo "      ADMIN_WHATSAPP=<nomor-WA>"
echo "      QRIS_IMAGE=images/qris.png"
echo "  [ ] File QRIS sudah diupload ke: public/images/qris.png"
echo "  [ ] Cron scheduler sudah aktif:"
echo "      * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1"
echo ""
