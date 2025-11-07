# Checklist Deploy - PGE System

## Setelah Git Pull, Pastikan Jalankan:

```bash
# 1. Clear semua cache Laravel
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# 2. Rebuild frontend assets
npm run build

# 3. Optimize (opsional, untuk production)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Restart PHP-FPM (jika menggunakan PHP-FPM)
sudo systemctl restart php8.4-fpm
# atau
sudo service php8.4-fpm restart

# 5. Clear Opcache (jika enabled)
php artisan opcache:clear
# atau restart web server
sudo systemctl restart nginx
# atau
sudo systemctl restart apache2
```

## Perubahan yang Sudah Dibuat:

### 1. Leave Request Edit Rules

-   ✅ Policy: Hanya leave dengan status `pending` yang bisa di-edit
-   ✅ Controller: Validasi di `edit()` dan `update()` method
-   ✅ View: Tombol edit hanya muncul untuk status `pending`
-   ✅ Modal: Form edit hanya muncul jika `currentLeave` tersedia

### 2. Payment Approval Module

-   ✅ User perlu di-assign module `payment-approval` untuk akses
-   ✅ Check di `User\PaymentApprovalController::checkAccess()`

## Troubleshooting:

### Jika perubahan belum terlihat:

1. **Clear browser cache** (Ctrl+Shift+Delete)
2. **Hard refresh** (Ctrl+F5)
3. **Check file timestamp** - pastikan file terbaru sudah ter-pull
4. **Check Laravel logs** - `storage/logs/laravel.log`
5. **Restart web server** - untuk clear opcache

### Jika masih error 403 untuk Payment Approval:

-   Pastikan user sudah di-assign module `payment-approval` di User Management
-   Check di database: `user_module` table untuk user tersebut
