# PGE System - Integrated Management Platform

<p align="center">
  <img src="public/logopge.png" alt="PGE System Logo" width="200">
</p>

<p align="center">
  <strong>Sistem Manajemen Kantor Terintegrasi untuk Mengelola Workflow Operasional Perusahaan</strong>
</p>

<p align="center">
  <a href="#-fitur-utama">Fitur</a> •
  <a href="#-teknologi">Teknologi</a> •
  <a href="#-instalasi">Instalasi</a> •
  <a href="#-struktur-sistem">Struktur</a> •
  <a href="#-dokumentasi">Dokumentasi</a>
</p>

---

## 📋 Daftar Isi

-   [Tentang Sistem](#-tentang-sistem)
-   [Fitur Utama](#-fitur-utama)
-   [Teknologi yang Digunakan](#-teknologi)
-   [Persyaratan Sistem](#-persyaratan-sistem)
-   [Instalasi](#-instalasi)
-   [Konfigurasi](#-konfigurasi)
-   [Struktur Sistem](#-struktur-sistem)
-   [Arsitektur & Desain](#-arsitektur--desain)
-   [Modul & Workflow](#-modul--workflow)
-   [Database & Model](#-database--model)
-   [Autentikasi & Otorisasi](#-autentikasi--otorisasi)
-   [API & Routing](#-api--routing)
-   [Testing](#-testing)
-   [Deployment](#-deployment)
-   [Troubleshooting](#-troubleshooting)
-   [Kontribusi](#-kontribusi)
-   [Lisensi](#-lisensi)

---

## 🎯 Tentang Sistem

**PGE System** adalah platform manajemen kantor komprehensif yang dibangun dengan Laravel 11 untuk mengelola seluruh workflow operasional perusahaan. Sistem ini dirancang dengan arsitektur modular yang memungkinkan admin untuk mengatur akses modul per user secara fleksibel.

### Karakteristik Utama

-   ✅ **Modular System** - Setiap fitur adalah modul terpisah yang dapat di-enable/disable per user
-   ✅ **Role-Based Access Control (RBAC)** - Menggunakan Spatie Laravel Permission untuk kontrol akses granular
-   ✅ **Shared Controllers & Views** - Tidak ada duplikasi kode antara admin dan user
-   ✅ **Approval Workflow** - Sistem approval terintegrasi untuk semua jenis submission
-   ✅ **Project Management** - Tracking dan monitoring project dengan Project Manager assignment
-   ✅ **Real-time Notifications** - Notifikasi untuk approval, submission, dan reminder
-   ✅ **PDF Generation** - Generate dokumen PDF untuk SPD, Purchase, Payment, dan Leave
-   ✅ **Activity Logging** - Audit trail lengkap untuk semua aktivitas sistem

### Versi

-   **Current Version**: v1.10.0
-   **Laravel Version**: 11.31
-   **PHP Version**: ^8.2
-   **Status**: Production Ready ✅

---

## 🚀 Fitur Utama

### 1. Work Management (Default Module)

**Work Plan & Work Realization** - Modul default yang selalu tersedia untuk semua user.

-   **Work Plan**: Perencanaan kerja harian dengan detail:

    -   Assignment ke project
    -   Deskripsi rencana kerja
    -   Lokasi kerja (Office, Field, Remote)
    -   File attachments
    -   Auto-numbering untuk tracking

-   **Work Realization**: Realisasi kerja aktual dengan:
    -   Link ke Work Plan terkait
    -   Progress tracking
    -   Deskripsi realisasi
    -   File attachments (foto, dokumen)
    -   Timestamp otomatis

**Fitur Khusus:**

-   Admin dapat melihat semua work plans/realizations
-   User hanya melihat milik sendiri
-   Project Manager dapat melihat work terkait project yang dikelola
-   Filter berdasarkan tanggal, project, dan user

### 2. Leave Management

**Cuti & Izin** - Sistem pengajuan cuti dengan approval workflow.

-   **Leave Types**: Multiple jenis cuti (Annual, Sick, Personal, dll)
-   **Leave Balance**: Tracking sisa cuti tahunan per user
-   **Leave Request**:
    -   Form pengajuan dengan attachment
    -   Validasi tanggal dan kuota
    -   Status tracking (Pending, Approved, Rejected)
-   **PDF Certificate**: Generate sertifikat cuti dalam format PDF
-   **Approval System**: Multi-level approval dengan notifikasi

**Workflow:**

1. User mengajukan cuti → Status: Pending
2. Approver review → Approve/Reject dengan reason
3. Notifikasi ke user → Status update
4. PDF certificate (jika approved)

### 3. SPD (Surat Perjalanan Dinas)

**Business Travel Management** - Sistem pengajuan perjalanan dinas.

-   **SPD Form**:
    -   Detail perjalanan (tujuan, tanggal, tujuan)
    -   Dynamic cost table (transport, akomodasi, makan, dll)
    -   Assignment ke project
    -   File attachments
-   **PDF Generation**: Generate dokumen SPD resmi
-   **Approval Workflow**: Approval dengan tracking status
-   **Cost Tracking**: Tracking biaya perjalanan dinas

### 4. Purchase Management

**Procurement System** - Sistem pengajuan pembelian barang/jasa.

-   **Purchase Request**:
    -   Detail item (nama, quantity, unit, harga)
    -   Total calculation otomatis
    -   Assignment ke project
    -   File attachments (quotation, dll)
-   **Approval Workflow**: Multi-level approval
-   **PDF Generation**: Generate dokumen purchase order
-   **Tracking**: Status tracking dari pengajuan hingga approval

### 5. Vendor Payment

**Payment Management** - Sistem pembayaran ke vendor/supplier.

-   **Payment Request**:
    -   Vendor selection
    -   Invoice details
    -   Amount dan payment method
    -   Assignment ke project
    -   File attachments (invoice, dll)
-   **Vendor Management**: Database vendor dengan detail lengkap
-   **Approval Workflow**: Approval dengan tracking
-   **PDF Generation**: Generate dokumen payment request

### 6. Approval System

**Centralized Approval** - Sistem approval terpusat untuk semua jenis submission.

#### Leave Approval

-   Daftar semua pengajuan cuti
-   Filter berdasarkan status, user, tanggal
-   Approve/Reject dengan reason
-   View detail dan attachment
-   Export data

#### Payment Approval

-   Daftar semua pengajuan pembayaran (SPD, Purchase, Vendor Payment)
-   Unified interface untuk semua jenis payment
-   Approve/Reject dengan reason
-   View detail dan attachment
-   Export data

**Fitur Approval:**

-   Permission-based access (hanya user dengan module access)
-   Real-time status update
-   Notification system
-   Audit trail lengkap

### 7. Project Management

**Project Monitoring & Tracking** - Sistem monitoring project dengan Project Manager assignment.

-   **Project Overview**:
    -   List semua project
    -   Project details (name, code, status, budget)
    -   Project Manager assignment
-   **Access Types**:
    -   **View Only**: Hanya melihat project
    -   **Work Access**: Dapat melihat work plans/realizations
    -   **Payment Access**: Dapat approve payment terkait project
    -   **Full Access**: Semua akses di atas
-   **Project Dashboard**:
    -   Work plans terkait project
    -   Work realizations terkait project
    -   Payment tracking (SPD, Purchase, Vendor Payment)
    -   Progress monitoring

### 8. EAR (Executive Activity Report)

**Reporting & Analytics** - Laporan aktivitas eksekutif.

-   Overview aktivitas harian
-   Work plan & realization summary
-   Payment summary
-   Project progress
-   Export capabilities

### 9. User Management (Admin Only)

**User Administration** - Manajemen user dengan module assignment.

-   **User CRUD**: Create, Read, Update, Delete user
-   **Module Assignment**: Assign modul ke user secara individual
-   **Role Management**: Assign role (Admin/User)
-   **Profile Management**: Edit profile user
-   **Active/Inactive**: Enable/disable user account

**Module Assignment System:**

-   Admin dapat assign modul ke user via checkbox
-   Default modules (work-plan, work-realization) selalu aktif
-   Assignable modules dapat di-enable/disable per user
-   Permission otomatis di-generate berdasarkan module assignment

### 10. Notification System

**Real-time Notifications** - Sistem notifikasi untuk semua event penting.

-   **New Submission**: Notifikasi saat ada submission baru
-   **Status Update**: Notifikasi saat status berubah (approved/rejected)
-   **Reminder**: Reminder untuk work plan dan realization
-   **Real-time Updates**: Update tanpa refresh halaman

### 11. Activity Logging

**Audit Trail** - Logging semua aktivitas sistem.

-   **User Activity**: Log aktivitas user (create, update, delete)
-   **Admin Activity**: Log aktivitas admin (approval, user management)
-   **System Activity**: Log aktivitas sistem
-   **Filter & Search**: Filter berdasarkan user, date, action
-   **Export**: Export log untuk audit

### 12. Documentation

**System Documentation** - Dokumentasi lengkap sistem.

-   Overview sistem
-   User roles & permissions
-   Module documentation
-   Workflow documentation
-   Technical documentation
-   Changelog

---

## 💻 Teknologi

### Backend Framework

-   **Laravel 11.31** - PHP Framework modern dengan fitur lengkap
-   **PHP 8.2+** - Server-side language dengan performance tinggi
-   **Composer 2.x** - Dependency manager untuk PHP

### Laravel Packages

-   **Spatie Laravel Permission (v6.12)** - RBAC system untuk role & permission management
-   **Laravel Telescope (v5.4)** - Debugging & monitoring tool
-   **Barryvdh Laravel DomPDF (v3.1)** - PDF generation library
-   **Laravel Tinker (v2.9)** - REPL untuk Laravel

### Frontend Technologies

-   **Blade Templates** - Server-side templating engine Laravel
-   **Tailwind CSS (v3.4)** - Utility-first CSS framework
-   **Alpine.js (v3.x)** - Lightweight JavaScript framework untuk interaktivitas
-   **Vite (v6.0)** - Build tool modern untuk frontend assets
-   **Axios (v1.7)** - HTTP client untuk AJAX requests

### Database & Storage

-   **MySQL 8.x** - Primary database dengan indexing untuk performance
-   **Eloquent ORM** - Database abstraction layer Laravel
-   **Migrations & Seeders** - Version control untuk database schema
-   **SQLite** - Development database (optional)

### Development Tools

-   **Laravel Pint (v1.13)** - Code style fixer
-   **PHPUnit (v11.0)** - Testing framework
-   **Laravel Pail (v1.1)** - Real-time log viewer
-   **Laravel Sail (v1.26)** - Docker development environment

---

## 📦 Persyaratan Sistem

### Server Requirements

-   **PHP**: >= 8.2
-   **Composer**: >= 2.0
-   **Node.js**: >= 18.x
-   **NPM**: >= 9.x
-   **MySQL**: >= 8.0 (atau MariaDB >= 10.3)
-   **Web Server**: Apache 2.4+ atau Nginx 1.18+

### PHP Extensions

-   BCMath
-   Ctype
-   cURL
-   DOM
-   Fileinfo
-   JSON
-   Mbstring
-   OpenSSL
-   PCRE
-   PDO
-   Tokenizer
-   XML

### Recommended

-   **Redis** - Untuk cache dan session (optional)
-   **Supervisor** - Untuk queue workers (production)
-   **OPcache** - Untuk performance (production)

---

## 🔧 Instalasi

### 1. Clone Repository

```bash
git clone <repository-url> pge-system
cd pge-system
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup

Edit file `.env` dan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pge_system
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Run Migrations & Seeders

```bash
# Run migrations
php artisan migrate

# Seed database (roles, permissions, modules, sample data)
php artisan db:seed
```

### 6. Build Frontend Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start Development Server

```bash
# Option 1: Laravel development server
php artisan serve

# Option 2: Using Laravel Sail (Docker)
./vendor/bin/sail up -d

# Option 3: Using composer dev script (with queue, logs, vite)
composer dev
```

### 8. Access Application

-   **URL**: http://localhost:8000
-   **Default Admin**:
    -   Email: admin@pge.com
    -   Password: password (ubah setelah login pertama)
-   **Default User**:
    -   Email: user@pge.com
    -   Password: password

---

## ⚙️ Konfigurasi

### Environment Variables

File `.env` berisi konfigurasi penting:

```env
# Application
APP_NAME="PGE System"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pge_system
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache
CACHE_STORE=database

# Queue
QUEUE_CONNECTION=database

# Mail (untuk notifikasi email)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="noreply@pge.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Module Configuration

File `config/modules.php` berisi konfigurasi semua modul:

```php
'list' => [
    'work-plan' => [
        'label' => 'Rencana Kerja',
        'icon' => '🗓️',
        'routes' => ['index' => 'user.work-plans.index'],
        'actions' => ['view', 'create', 'update', 'delete'],
        'assignable_to_user' => true,
        'admin_only' => false,
        'category' => 'modul',
    ],
    // ... modul lainnya
]
```

### Permission Configuration

Permissions otomatis di-generate berdasarkan konfigurasi modul. Format permission: `{action}-{module}`

Contoh:

-   `view-work-plan`
-   `create-work-plan`
-   `approve-leave`
-   `reject-payment`

---

## 🏗️ Struktur Sistem

### Directory Structure

```
pge-system/
├── app/
│   ├── Constants/          # Application constants
│   ├── Console/            # Artisan commands
│   ├── Enums/              # PHP Enums (ApprovalStatus, WorkLocation)
│   ├── Exceptions/         # Custom exceptions
│   ├── Helpers/            # Helper functions
│   ├── Http/
│   │   ├── Controllers/    # Application controllers
│   │   │   ├── Admin/      # Admin controllers
│   │   │   ├── User/       # User controllers
│   │   │   ├── Work/       # Work management controllers
│   │   │   ├── Leave/      # Leave management controllers
│   │   │   └── Payment/    # Payment controllers
│   │   ├── Middleware/     # Custom middleware
│   │   └── Requests/       # Form request validation
│   ├── Models/             # Eloquent models
│   ├── Notifications/      # Notification classes
│   ├── Observers/          # Model observers
│   ├── Policies/           # Authorization policies
│   ├── Providers/          # Service providers
│   ├── Services/           # Business logic services
│   └── Traits/             # Reusable traits
├── bootstrap/              # Bootstrap files
├── config/                 # Configuration files
├── database/
│   ├── factories/          # Model factories
│   ├── migrations/         # Database migrations
│   └── seeders/            # Database seeders
├── public/                 # Public assets
├── resources/
│   ├── css/                # CSS files
│   ├── js/                 # JavaScript files
│   └── views/              # Blade templates
├── routes/                 # Route definitions
│   ├── web.php             # Web routes
│   ├── admin.php           # Admin routes
│   └── user.php            # User routes
├── storage/                # Storage files
├── tests/                  # Test files
└── vendor/                 # Composer dependencies
```

### Controller Organization

**Admin Controllers:**

-   `Admin\DashboardController` - Admin dashboard
-   `Admin\UserManagementController` - User management
-   `Admin\ApprovalController` - Leave approval
-   `Admin\PaymentApprovalController` - Payment approval
-   `Admin\DocumentationController` - System documentation
-   `Admin\ActivityLogController` - Activity logs
-   `Admin\ProjectManagerController` - Project manager assignment

**User Controllers:**

-   `User\DashboardController` - User dashboard
-   `User\LeaveApprovalController` - Leave approval (shared view)
-   `User\PaymentApprovalController` - Payment approval (shared view)
-   `User\ProjectManagementController` - Project management
-   `User\LogController` - User activity logs

**Shared Controllers:**

-   `Work\WorkPlanController` - Work plan (shared)
-   `Work\WorkRealizationController` - Work realization (shared)
-   `Leave\LeaveController` - Leave management (shared)
-   `Payment\SpdController` - SPD management (shared)
-   `Payment\PurchaseController` - Purchase management (shared)
-   `Payment\VendorPaymentController` - Vendor payment (shared)

### Model Structure

**Core Models:**

-   `User` - User model dengan Spatie Permission
-   `Module` - Module configuration
-   `Project` - Project management
-   `ActivityLog` - Activity logging
-   `Changelog` - System changelog

**Work Management:**

-   `WorkPlan` - Work planning
-   `WorkRealization` - Work realization

**Leave Management:**

-   `LeaveRequest` - Leave requests
-   `LeaveType` - Leave types

**Payment Management:**

-   `SPD` - Surat Perjalanan Dinas
-   `Purchase` - Purchase requests
-   `VendorPayment` - Vendor payments
-   `Vendor` - Vendor database

---

## 🎨 Arsitektur & Desain

### Design Patterns

#### 1. Modular Architecture

Setiap fitur adalah modul terpisah yang dapat di-enable/disable per user. Modul dikonfigurasi di `config/modules.php` dan disimpan di database.

**Prinsip:**

-   Modul bersifat shared (tidak ada duplikasi controller/view)
-   Admin sebagai pengelola akses (assign modul ke user)
-   Permission otomatis di-generate berdasarkan module assignment

#### 2. Shared Controllers & Views

Admin dan User menggunakan controller/view yang sama untuk modul shared. Filter data dilakukan di controller berdasarkan role.

**Contoh:**

```php
// WorkPlanController - Shared
public function index()
{
    $query = WorkPlan::query();

    // Admin melihat semua, User hanya milik sendiri
    if (!auth()->user()->hasRole('admin')) {
        $query->where('user_id', auth()->id());
    }

    return view('work.work-plans.index', [
        'workPlans' => $query->get()
    ]);
}
```

#### 3. Service Layer

Business logic dipisahkan ke Service classes untuk reusability dan testability.

**Services:**

-   `LeaveService` - Leave management logic
-   `PaymentService` - Payment processing logic
-   `UserService` - User management logic
-   `WorkManagementService` - Work management logic

#### 4. Policy-Based Authorization

Laravel Policies digunakan untuk authorization di level model.

**Policies:**

-   `LeaveRequestPolicy`
-   `PurchasePolicy`
-   `SpdPolicy`
-   `VendorPaymentPolicy`
-   `WorkPlanPolicy`
-   `WorkRealizationPolicy`

#### 5. Observer Pattern

Model Observers digunakan untuk logging dan side effects.

**Observers:**

-   `ActivityLogObserver` - Log semua CRUD operations
-   `ActivityObserver` - General activity logging
-   `ProjectObserver` - Project-specific logging

### Database Design

#### Entity Relationships

```
Users (1) ──< (N) WorkPlans
Users (1) ──< (N) WorkRealizations
Users (1) ──< (N) LeaveRequests
Users (1) ──< (N) SPDs
Users (1) ──< (N) Purchases
Users (1) ──< (N) VendorPayments

Users (N) ──< (N) Modules (Many-to-Many)
Users (N) ──< (N) Projects (Many-to-Many via project_managers)

Projects (1) ──< (N) WorkPlans
Projects (1) ──< (N) WorkRealizations
Projects (1) ──< (N) SPDs
Projects (1) ──< (N) Purchases

Vendors (1) ──< (N) VendorPayments
```

#### Key Tables

-   `users` - User accounts
-   `modules` - Module configuration
-   `user_module` - User-module assignment (pivot)
-   `projects` - Project database
-   `project_managers` - Project manager assignment (pivot)
-   `work_plans` - Work planning
-   `work_realizations` - Work realization
-   `leave_requests` - Leave requests
-   `spds` - SPD requests
-   `purchases` - Purchase requests
-   `vendor_payments` - Vendor payment requests
-   `vendors` - Vendor database
-   `activity_logs` - Activity logging
-   `changelogs` - System changelog

---

## 📋 Modul & Workflow

### Module Types

#### 1. Default Modules (Always Active)

-   **work-plan** - Rencana Kerja
-   **work-realization** - Realisasi Kerja

Modul ini selalu aktif untuk semua user dan tidak dapat di-disable.

#### 2. User Assignable Modules

Modul yang dapat di-assign oleh admin ke user:

-   **leave** - Cuti & Izin
-   **leave-approval** - Daftar Cuti & Izin (approval)
-   **spd** - SPD
-   **purchase** - Pembelian
-   **vendor-payment** - Pembayaran Vendor
-   **payment-approval** - Approval Pembayaran
-   **project-management** - Project Management
-   **ear** - EAR (Executive Activity Report)

#### 3. Admin-Only Modules

Modul yang hanya dapat diakses admin:

-   **user** - Manajemen User
-   **documentation** - Dokumentasi Sistem

### Workflow Examples

#### Leave Request Workflow

```
1. User Login → Access Leave Module
2. Create Leave Request → Fill form (dates, type, reason, attachment)
3. Submit → Status: Pending
4. System → Send notification to approver
5. Approver → Review request
6. Approver → Approve/Reject with reason
7. System → Update status, send notification to user
8. User → View updated status
9. (If approved) → Generate PDF certificate
```

#### Payment Approval Workflow

```
1. User → Submit Payment Request (SPD/Purchase/Vendor Payment)
2. System → Status: Pending, send notification
3. Approver (with payment-approval module) → Review request
4. Approver → View details, attachments
5. Approver → Approve/Reject with reason
6. System → Update status, send notification
7. User → View updated status
8. (If approved) → Generate PDF document
```

#### Work Management Workflow

```
1. Morning → User creates Work Plan
   - Select project
   - Fill work details
   - Set location, attachments
2. System → Save Work Plan
3. During Day → User executes work
4. Evening → User creates Work Realization
   - Link to Work Plan
   - Fill actual work done
   - Add progress, attachments
5. System → Save Work Realization
6. Project Manager → View work in Project Dashboard
```

---

## 🔐 Autentikasi & Otorisasi

### Authentication System

**Laravel Default Authentication:**

-   Session-based authentication
-   Password hashing dengan bcrypt
-   Remember me functionality
-   Login throttling

**Login Flow:**

1. User mengakses `/login`
2. Input email & password
3. System validasi credentials
4. Check role (Admin/User)
5. Redirect ke dashboard sesuai role:
    - Admin → `/admin/dashboard`
    - User → `/user/dashboard`

### Authorization System

**Spatie Laravel Permission:**

-   Role-based access control (RBAC)
-   Permission-based authorization
-   Granular permission control

**Roles:**

-   **admin** - Full access ke semua modul dan fitur
-   **user** - Akses terbatas berdasarkan module assignment

**Permission Format:**
`{action}-{module}`

Contoh:

-   `view-work-plan`
-   `create-work-plan`
-   `approve-leave`
-   `reject-payment`

**Permission Assignment:**

-   Admin: Semua permissions otomatis
-   User: Permissions diberikan saat module di-assign

### Authorization Checks

#### 1. Route-Level Authorization

```php
Route::middleware('role:admin')->group(function () {
    // Admin-only routes
});
```

#### 2. Controller-Level Authorization

```php
public function index()
{
    // Check module access
    if (!auth()->user()->hasModuleAccess('payment-approval')) {
        abort(403, 'Anda tidak memiliki akses ke modul ini');
    }
}
```

#### 3. Policy-Based Authorization

```php
public function update(Request $request, LeaveRequest $leave)
{
    $this->authorize('update', $leave);
    // ...
}
```

#### 4. Trait-Based Authorization

```php
use App\Traits\ChecksAuthorization;

protected function canAccessResource($resourceUserId, $projectId = null)
{
    // Check if admin, owner, or project manager
}
```

---

## 🛣️ API & Routing

### Route Organization

Routes terorganisir dalam 3 file:

#### 1. `routes/web.php` - Main Routes

-   Authentication routes
-   Notification routes
-   Project search API
-   Include admin & user routes

#### 2. `routes/admin.php` - Admin Routes

Semua routes admin dengan prefix `/admin`:

-   Dashboard
-   User Management
-   Approvals (Leave, Payment)
-   Work Management
-   Leave Management
-   Payment Management (SPD, Purchase, Vendor Payment)
-   Project Management
-   Documentation
-   Activity Logs
-   EAR

#### 3. `routes/user.php` - User Routes

Semua routes user dengan prefix `/user`:

-   Dashboard
-   Work Management
-   Leave Management
-   Leave Approval (if module assigned)
-   Payment Submission (SPD, Purchase, Vendor Payment)
-   Payment Approval (if module assigned)
-   Project Management
-   Activity Logs

### Route Naming Convention

**Format:** `{role}.{resource}.{action}`

Contoh:

-   `admin.dashboard`
-   `admin.users.index`
-   `user.work-plans.index`
-   `user.leaves.create`
-   `admin.approvals.leaves.approve`

### API Endpoints

#### Project Search API

```
GET /projects/search?q={query}
```

Response:

```json
{
    "data": [
        {
            "id": 1,
            "name": "Project Name",
            "code": "PRJ-001"
        }
    ]
}
```

#### Notification API

```
GET /notifications
GET /notifications/unread
POST /notifications/{id}/read
POST /notifications/read-all
DELETE /notifications/{id}
```

---

## 🧪 Testing

### Test Structure

```
tests/
├── Feature/          # Feature tests
│   ├── LeaveTest.php
│   ├── PaymentTest.php
│   ├── WorkTest.php
│   └── UserTest.php
├── Unit/             # Unit tests
│   ├── ServiceTest.php
│   └── ModelTest.php
└── TestCase.php      # Base test case
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter LeaveTest

# Run with coverage
php artisan test --coverage
```

### Test Examples

**Feature Test:**

```php
public function test_user_can_create_leave_request()
{
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post('/user/leaves', [
        'leave_type_id' => 1,
        'start_date' => '2024-01-01',
        'end_date' => '2024-01-05',
        'reason' => 'Annual leave'
    ]);

    $response->assertStatus(302);
    $this->assertDatabaseHas('leave_requests', [
        'user_id' => $user->id
    ]);
}
```

---

## 🚀 Deployment

### Deployment Checklist

Lihat file `DEPLOY_CHECKLIST.md` untuk checklist lengkap.

#### Pre-Deployment

1. ✅ Update dependencies
2. ✅ Run tests
3. ✅ Check environment variables
4. ✅ Backup database

#### Deployment Steps

```bash
# 1. Pull latest code
git pull origin main

# 2. Install/update dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# 3. Run migrations
php artisan migrate --force

# 4. Clear and cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# 5. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

#### Post-Deployment

1. ✅ Verify application is running
2. ✅ Check logs for errors
3. ✅ Test critical features
4. ✅ Monitor performance

### Server Configuration

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name pge-system.example.com;
    root /var/www/pge-system/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Queue Workers

Untuk production, setup queue workers dengan Supervisor:

```ini
[program:pge-system-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pge-system/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/pge-system/storage/logs/worker.log
stopwaitsecs=3600
```

---

## 🔍 Troubleshooting

### Common Issues

#### 1. Permission Denied (403)

**Problem:** User tidak dapat mengakses modul

**Solution:**

-   Pastikan user sudah di-assign module di User Management
-   Check permission di database: `user_module` table
-   Clear cache: `php artisan cache:clear`

#### 2. Module Not Showing in Sidebar

**Problem:** Modul tidak muncul di sidebar

**Solution:**

-   Check module `is_active` status di database
-   Check user module assignment
-   Clear view cache: `php artisan view:clear`

#### 3. PDF Generation Error

**Problem:** Error saat generate PDF

**Solution:**

-   Check DomPDF installation: `composer show barryvdh/laravel-dompdf`
-   Check file permissions di `storage/` directory
-   Check PHP memory limit

#### 4. Queue Not Processing

**Problem:** Queue jobs tidak diproses

**Solution:**

-   Start queue worker: `php artisan queue:work`
-   Check queue connection di `.env`
-   Check failed jobs: `php artisan queue:failed`

#### 5. Session Issues

**Problem:** Session tidak persist

**Solution:**

-   Check `SESSION_DRIVER` di `.env`
-   Clear session: `php artisan session:clear`
-   Check file permissions di `storage/framework/sessions`

### Debug Mode

Untuk development, enable debug mode di `.env`:

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

**Note:** Jangan enable debug mode di production!

### Log Files

Log files berada di `storage/logs/laravel.log`:

```bash
# View logs
tail -f storage/logs/laravel.log

# Clear logs
> storage/logs/laravel.log
```

### Database Issues

```bash
# Reset database (development only!)
php artisan migrate:fresh --seed

# Check database connection
php artisan db:show

# Check migrations status
php artisan migrate:status
```

---

## 🤝 Kontribusi

### Development Workflow

1. Fork repository
2. Create feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push to branch: `git push origin feature/amazing-feature`
5. Open Pull Request

### Code Style

Project menggunakan Laravel Pint untuk code style:

```bash
# Format code
./vendor/bin/pint

# Check code style
./vendor/bin/pint --test
```

### Commit Message Convention

-   `feat:` - New feature
-   `fix:` - Bug fix
-   `docs:` - Documentation changes
-   `style:` - Code style changes
-   `refactor:` - Code refactoring
-   `test:` - Test changes
-   `chore:` - Build process or auxiliary tool changes

---

## 📄 Lisensi

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 📞 Support & Contact

Untuk pertanyaan atau support, silakan hubungi:

-   **Email**: support@pge.com
-   **Documentation**: `/admin/documentation` (setelah login sebagai admin)

---

## 🙏 Acknowledgments

-   [Laravel](https://laravel.com) - The PHP Framework
-   [Spatie](https://spatie.be) - Laravel Permission Package
-   [Tailwind CSS](https://tailwindcss.com) - CSS Framework
-   [Alpine.js](https://alpinejs.dev) - JavaScript Framework

---

<p align="center">
  <strong>PGE System v1.10.0</strong> - Built with ❤️ using Laravel
</p>
