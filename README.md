# PGE System - Integrated Management Platform

<p align="center">
  <img src="public/logopge.png" alt="PGE System Logo" width="200">
</p>

<p align="center">
  <strong>Sistem Manajemen Kantor Terintegrasi untuk Mengelola Workflow Operasional Perusahaan</strong>
</p>

<p align="center">
  <strong>Version 1.10.0</strong> • Laravel 11 • Production Ready ✅
</p>

---

## 📖 Tentang

**PGE System** adalah platform manajemen kantor komprehensif yang membantu perusahaan mengelola seluruh workflow operasional, mulai dari perencanaan kerja, realisasi, keuangan, cuti, hingga monitoring project.

### Karakteristik Utama

-   ✅ **Modular System** - Setiap fitur adalah modul terpisah yang dapat di-enable/disable per user
-   ✅ **Role-Based Access** - Kontrol akses berdasarkan role (Admin/User) dengan permission granular
-   ✅ **Approval Workflow** - Sistem approval terintegrasi untuk semua jenis pengajuan
-   ✅ **Project Management** - Tracking dan monitoring project dengan Project Manager assignment
-   ✅ **Real-time Notifications** - Notifikasi untuk approval, submission, dan reminder
-   ✅ **PDF Generation** - Generate dokumen PDF untuk SPD, Purchase, Payment, dan Leave

---

## 🚀 Fitur Utama

### Work Management

Perencanaan dan realisasi kerja harian dengan assignment ke project, tracking progress, dan file attachments.

### Leave Management

Sistem pengajuan cuti dengan approval workflow, tracking sisa cuti, dan generate sertifikat PDF.

### SPD (Surat Perjalanan Dinas)

Pengajuan perjalanan dinas dengan detail biaya, approval workflow, dan generate dokumen SPD.

### Purchase Management

Sistem pengajuan pembelian barang/jasa dengan detail item, approval workflow, dan tracking status.

### Vendor Payment

Sistem pembayaran ke vendor dengan invoice tracking, approval workflow, dan generate dokumen payment.

### Approval System

Sistem approval terpusat untuk Leave dan Payment (SPD, Purchase, Vendor Payment) dengan real-time notifications.

### Project Management

Monitoring dan tracking project dengan Project Manager assignment dan berbagai level akses (View, Work, Payment, Full).

### User Management

Manajemen user dengan module assignment, role management, dan profile management (Admin only).

### Notifications

Sistem notifikasi real-time untuk semua event penting (submission baru, status update, reminder).

### Activity Logging

Audit trail lengkap untuk semua aktivitas sistem dengan filter dan export capabilities.

---

## 💻 Teknologi

**Backend:**

-   Laravel 11.31 (PHP Framework)
-   PHP 8.2+
-   MySQL 8.x

**Frontend:**

-   Blade Templates
-   Tailwind CSS 3.4
-   Alpine.js 3.x
-   Vite 6.0

**Packages:**

-   Spatie Laravel Permission (RBAC)
-   Laravel Telescope (Monitoring)
-   Barryvdh Laravel DomPDF (PDF Generation)

---

## 📦 Instalasi

### Persyaratan

-   PHP >= 8.2
-   Composer >= 2.0
-   Node.js >= 18.x
-   MySQL >= 8.0

### Langkah Instalasi

```bash
# 1. Clone repository
git clone <repository-url> pge-system
cd pge-system

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pge_system
DB_USERNAME=root
DB_PASSWORD=your_password

# 5. Run migrations & seeders
php artisan migrate
php artisan db:seed

# 6. Build frontend assets
npm run build

# 7. Start development server
php artisan serve
```

### Akses Aplikasi

-   **URL**: http://localhost:8000
-   **Default Admin**:
    -   Email: `admin@pge.com`
    -   Password: `password`
-   **Default User**:
    -   Email: `user@pge.com`
    -   Password: `password`

> ⚠️ **Penting**: Ubah password default setelah login pertama!

---

## 📁 Struktur Modul

Sistem menggunakan arsitektur modular dengan 12 modul:

**Default Modules** (selalu aktif):

-   Work Plan
-   Work Realization

**User Assignable Modules**:

-   Leave
-   Leave Approval
-   SPD
-   Purchase
-   Vendor Payment
-   Payment Approval
-   Project Management
-   EAR

**Admin Only Modules**:

-   User Management
-   Documentation

Admin dapat mengatur akses modul per user melalui User Management.

---

## 🔐 Roles & Permissions

**Admin Role:**

-   Full access ke semua modul dan fitur
-   User management dengan module assignment
-   Approval untuk semua jenis submission
-   Access ke semua reports dan analytics

**User Role:**

-   Akses terbatas berdasarkan module assignment
-   Default hanya punya Work Plan & Work Realization
-   Dapat submit requests jika module di-assign
-   Dapat approve jika module approval di-assign

---

## 📝 Workflow

### Leave Request

1. User mengajukan cuti → Status: Pending
2. Approver review → Approve/Reject
3. Notifikasi ke user → Status update
4. Generate PDF certificate (jika approved)

### Payment Submission

1. User submit payment request (SPD/Purchase/Vendor Payment)
2. Status: Pending → Notifikasi ke approver
3. Approver review → Approve/Reject dengan reason
4. Notifikasi ke user → Status update
5. Generate PDF document (jika approved)

### Work Management

1. Morning: User create Work Plan
2. During day: User execute work
3. Evening: User create Work Realization
4. Project Manager: View work di Project Dashboard

---

## 🛠️ Development

### Development Server

```bash
# Option 1: Laravel development server
php artisan serve

# Option 2: With queue, logs, and vite (recommended)
composer dev
```

### Build Assets

```bash
# Development (watch mode)
npm run dev

# Production
npm run build
```

### Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

## 🚀 Deployment

### Pre-Deployment

```bash
# Update dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Run migrations
php artisan migrate --force

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Lihat `DEPLOY_CHECKLIST.md` untuk checklist lengkap.

---

## 📚 Dokumentasi

Dokumentasi lengkap tersedia di dalam aplikasi setelah login sebagai admin:

-   `/admin/documentation` - Dokumentasi sistem lengkap

---

## 🤝 Kontribusi

1. Fork repository
2. Create feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push to branch: `git push origin feature/amazing-feature`
5. Open Pull Request

---

## 📄 Lisensi

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 📞 Support

Untuk pertanyaan atau support, silakan hubungi:

-   **Email**: support@pge.com
-   **Documentation**: `/admin/documentation` (setelah login sebagai admin)

---

<p align="center">
  <strong>PGE System v1.10.0</strong> - Built with ❤️ using Laravel
</p>
