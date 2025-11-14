<div align="center">

# 🚀 PGE System

**Integrated Management Platform**

_Sistem manajemen kantor terintegrasi untuk mengelola workflow operasional perusahaan_

![Version](https://img.shields.io/badge/version-1.10.0-blue?style=for-the-badge)
![Laravel](https://img.shields.io/badge/Laravel-11.31-red?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)
![Status](https://img.shields.io/badge/status-production%20ready-success?style=for-the-badge)

[Features](#-fitur-utama) • [Tech Stack](#-tech-stack) • [Installation](#-quick-start) • [Documentation](#-dokumentasi)

</div>

---

## 📖 Overview

**PGE System** adalah platform manajemen kantor terintegrasi berbasis Laravel untuk mengelola workflow operasional perusahaan, meliputi perencanaan kerja, realisasi, keuangan, cuti, dan monitoring project.

### Karakteristik

| Feature                 | Description                                              |
| ----------------------- | -------------------------------------------------------- |
| 🧩 **Modular**          | Arsitektur modular dengan enable/disable per user        |
| 🔐 **RBAC**             | Role-based access control dengan permission granular     |
| ✅ **Approval Flow**    | Sistem approval terintegrasi untuk semua jenis pengajuan |
| 📊 **Project Tracking** | Monitoring project dengan Project Manager assignment     |
| 🔔 **Real-time**        | Notifikasi real-time untuk semua event penting           |
| 📄 **PDF Generation**   | Auto-generate PDF untuk dokumen resmi                    |

---

## 🚀 Fitur Utama

### 📋 Work Management

Perencanaan dan realisasi kerja harian dengan project assignment, progress tracking, dan file attachments.

-   Work Plan dengan project assignment
-   Work Realization dengan file attachments
-   Real-time progress tracking
-   Filter berdasarkan tanggal, project, dan user

### 🏝️ Leave Management

Sistem pengajuan cuti dengan approval workflow, leave balance tracking, dan PDF certificate generation.

-   Multiple leave types (Annual, Sick, Personal, dll)
-   Leave balance tracking per user
-   Approval workflow
-   PDF certificate generation

### ✈️ SPD (Surat Perjalanan Dinas)

Business travel management dengan cost tracking, approval workflow, dan dokumen resmi.

-   Detail perjalanan lengkap
-   Dynamic cost table
-   Approval workflow
-   PDF document generation

### 🛒 Purchase Management

Procurement system dengan item management, approval workflow, dan purchase order generation.

-   Item detail dengan quantity dan harga
-   Auto-calculation total
-   Approval workflow
-   Purchase order generation

### 💳 Vendor Payment

Payment management dengan vendor database, invoice tracking, dan payment document generation.

-   Vendor database terintegrasi
-   Invoice tracking
-   Approval workflow
-   Payment document generation

### ✅ Approval System

Centralized approval system untuk Leave dan Payment (SPD, Purchase, Vendor Payment) dengan real-time notifications.

-   Leave Approval dengan real-time notifications
-   Payment Approval (SPD, Purchase, Vendor Payment)
-   Filter dan search capabilities
-   Export capabilities

### 📁 Project Management

Project tracking dengan Project Manager assignment dan multiple access levels.

-   Project Manager assignment
-   Multiple access levels (View, Work, Payment, Full)
-   Project dashboard dengan analytics
-   Progress monitoring

### 👥 User Management

User administration dengan module assignment dan role management (Admin only).

-   User CRUD operations
-   Module assignment per user
-   Role management
-   Profile management

### 🔔 Notifications

Real-time notification system untuk semua event penting.

-   New submission alerts
-   Status update notifications
-   Reminder untuk work plan & realization
-   Real-time updates tanpa refresh

### 📝 Activity Logging

Audit trail system untuk semua aktivitas dengan filter dan export capabilities.

-   Log semua aktivitas sistem
-   Filter dan search capabilities
-   Export untuk audit purposes
-   Security tracking

---

## 💻 Tech Stack

### Backend

-   **Laravel 11.31** - PHP Framework
-   **PHP 8.2+** - Server-side language
-   **MySQL 8.x** - Database

### Frontend

-   **Tailwind CSS 3.4** - Utility-first CSS framework
-   **Alpine.js 3.x** - Lightweight JavaScript framework
-   **Vite 6.0** - Build tool
-   **Blade Templates** - Server-side templating

### Packages

-   **Spatie Permission** - RBAC system
-   **Laravel Telescope** - Monitoring & debugging
-   **DomPDF** - PDF generation

---

## ⚡ Quick Start

### Requirements

-   PHP >= 8.2
-   Composer >= 2.0
-   Node.js >= 18.x
-   MySQL >= 8.0

### Installation

```bash
# Clone repository
git clone <repository-url> pge-system
cd pge-system

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database (edit .env)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pge_system
DB_USERNAME=root
DB_PASSWORD=your_password

# Run migrations & seeders
php artisan migrate
php artisan db:seed

# Build frontend
npm run build

# Start server
php artisan serve
```

### Access Application

| Role      | URL                   | Credentials                  |
| --------- | --------------------- | ---------------------------- |
| **Admin** | http://localhost:8000 | `admin@pge.com` / `password` |
| **User**  | http://localhost:8000 | `user@pge.com` / `password`  |

> ⚠️ **Important**: Change default password after first login!

---

## 📁 Module Structure

Sistem menggunakan **12 modul** dengan arsitektur modular:

### Default Modules (Always Active)

-   Work Plan
-   Work Realization

### User Assignable Modules

-   Leave
-   Leave Approval
-   SPD
-   Purchase
-   Vendor Payment
-   Payment Approval
-   Project Management
-   EAR

### Admin Only Modules

-   User Management
-   Documentation

> 💡 Admin dapat mengatur akses modul per user melalui User Management.

---

## 🔐 Roles & Permissions

### Admin Role

-   Full access ke semua modul
-   User management dengan module assignment
-   Approval untuk semua jenis submission
-   Access ke semua reports & analytics

### User Role

-   Akses terbatas berdasarkan module assignment
-   Default: Work Plan & Work Realization
-   Dapat submit requests (jika module di-assign)
-   Dapat approve (jika module approval di-assign)

---

## 📝 Workflow Examples

### Leave Request Flow

```
1. User mengajukan cuti → Status: Pending
2. Approver review → Approve / Reject
3. Notifikasi ke user → Status update
4. Generate PDF certificate (jika approved)
```

### Payment Submission Flow

```
1. User submit payment → Status: Pending
2. Notifikasi ke approver
3. Approver review → Approve / Reject dengan reason
4. Notifikasi ke user → Status update
5. Generate PDF document (jika approved)
```

### Work Management Flow

```
1. Morning: User create Work Plan
2. During day: User execute work
3. Evening: User create Work Realization
4. Project Manager: View work di Project Dashboard
```

---

## 🛠️ Development

### Start Development Server

```bash
# Option 1: Simple server
php artisan serve

# Option 2: With queue, logs, vite (recommended)
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

> 📋 Lihat `DEPLOY_CHECKLIST.md` untuk checklist lengkap.

---

## 📚 Dokumentasi

Dokumentasi lengkap tersedia di dalam aplikasi setelah login sebagai admin:

🔗 `/admin/documentation` - Dokumentasi sistem lengkap

---

## 🤝 Contributing

1. Fork repository
2. Create feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push to branch: `git push origin feature/amazing-feature`
5. Open Pull Request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 📞 Support

Untuk pertanyaan atau support:

-   📧 **Email**: support@pge.com
-   📚 **Documentation**: `/admin/documentation` (setelah login sebagai admin)

---

<div align="center">

**PGE System v1.10.0** - Built with ❤️ using Laravel

[⬆ Back to Top](#-pge-system)

</div>
