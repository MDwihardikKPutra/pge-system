# Fix Error 403 - Payment Approval di Deployment

## Masalah:

Error 403 "USER DOES NOT HAVE THE RIGHT PERMISSIONS" saat mengakses `/user/payment-approvals`

## Penyebab:

1. Module `payment-approval` belum di-sync ke database deployment
2. User belum di-assign module `payment-approval`

## Solusi:

### 1. Sync Modules ke Database

Jalankan perintah berikut di server deployment:

```bash
cd /data/pge-system/pge-system  # atau path sesuai deployment
php artisan db:seed --class=ModuleSeeder
```

Ini akan sync semua modules dari `config/modules.php` ke database, termasuk `payment-approval`.

### 2. Assign Module ke User

Setelah module di-sync, assign module `payment-approval` ke user yang membutuhkan akses:

**Via Admin Panel:**

1. Login sebagai admin
2. Buka "Manajemen User" (User Management)
3. Edit user yang membutuhkan akses
4. Centang module "Approval Pembayaran" (payment-approval)
5. Simpan

**Via Database (jika perlu):**

```sql
-- Cek apakah module sudah ada
SELECT * FROM modules WHERE `key` = 'payment-approval';

-- Cek user_id yang perlu di-assign
SELECT id, name, email FROM users WHERE email = 'user@example.com';

-- Assign module ke user (ganti user_id dan module_id sesuai)
INSERT INTO module_user (module_id, user_id, created_at, updated_at)
VALUES (
    (SELECT id FROM modules WHERE `key` = 'payment-approval'),
    (SELECT id FROM users WHERE email = 'user@example.com'),
    NOW(),
    NOW()
);
```

### 3. Clear Cache

Setelah sync modules, clear cache:

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### 4. Verifikasi

Cek apakah module sudah ter-sync:

```bash
php artisan tinker
```

Di dalam tinker:

```php
// Cek module payment-approval
\App\Models\Module::where('key', 'payment-approval')->first();

// Cek apakah user punya akses
$user = \App\Models\User::where('email', 'user@example.com')->first();
$user->hasModuleAccess('payment-approval'); // harus return true
```

## Checklist:

-   [ ] Module `payment-approval` sudah di-sync ke database
-   [ ] User sudah di-assign module `payment-approval`
-   [ ] Cache sudah di-clear
-   [ ] User sudah logout dan login lagi (untuk refresh session)
