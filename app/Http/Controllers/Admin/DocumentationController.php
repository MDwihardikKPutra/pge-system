<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Changelog;
use App\Models\User;
use App\Models\Spd;
use App\Models\Purchase;
use App\Models\VendorPayment;
use App\Models\LeaveRequest;
use App\Models\WorkPlan;
use App\Models\WorkRealization;
use App\Models\Project;
use App\Models\Vendor;

class DocumentationController extends Controller
{
    public function index()
    {
        // Load changelogs from database (newest first)
        $changelogs = Changelog::orderBy('release_date', 'desc')->orderBy('id', 'desc')->get();
        
        // Overview data
        $overview = [
            'system_name' => 'PGE System - Integrated Management Platform',
            'version' => 'v1.10.0',
            'environment' => config('app.env'),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'total_users' => User::count(),
            'total_projects' => Project::count(),
            'total_vendors' => Vendor::count(),
        ];
        
        // Roles & Permissions
        $roles = [
            [
                'name' => 'Admin',
                'description' => 'Super Administrator dengan akses penuh ke seluruh sistem',
                'permissions' => 'All',
                'capabilities' => [
                    'Manage semua users dengan module assignment',
                    'Approve semua jenis submissions (SPD, Purchase, Payment, Leave, Work Plans)',
                    'Access semua reports, analytics, dan documentation',
                    'View semua data tanpa batasan',
                    'Manage system settings dan configurations',
                    'View audit trail, activity logs, dan changelog',
                    'Export semua data dalam berbagai format',
                ],
            ],
            [
                'name' => 'User',
                'description' => 'Pengguna - submit requests dan manage work milik sendiri',
                'permissions' => 'Assigned Modules',
                'capabilities' => [
                    'Submit: SPD, Purchase, Payment, Leave requests (jika module di-assign)',
                    'Create & manage daily work plans & realizations (default modules)',
                    'View own submission history dengan detail status',
                    'View assigned projects dan project details',
                    'Receive real-time approval/rejection notifications',
                    'Update own profile dan change password',
                ],
            ],
        ];
        
        // Modules
        $modules = [
            [
                'name' => 'Work Management',
                'description' => 'Rencana & Realisasi Kerja - Daily work planning & tracking',
                'features' => ['Work Plan creation', 'Work Realization tracking', 'Project assignment', 'File attachments', 'Auto-numbering'],
                'icon' => 'clipboard-list',
            ],
            [
                'name' => 'SPD Management',
                'description' => 'Surat Perjalanan Dinas - Business travel requests',
                'features' => ['Create SPD', 'Dynamic cost table', 'Approval workflow', 'PDF generation', 'Project assignment'],
                'icon' => 'plane',
            ],
            [
                'name' => 'Purchase Management',
                'description' => 'Purchase requests dan procurement',
                'features' => ['Purchase requests', 'Item details (quantity, unit, price)', 'Approval workflow', 'Document attachments'],
                'icon' => 'shopping-cart',
            ],
            [
                'name' => 'Vendor Payment',
                'description' => 'Pembayaran ke vendor/supplier',
                'features' => ['Payment requests', 'Vendor management', 'Invoice tracking', 'Approval flow'],
                'icon' => 'credit-card',
            ],
            [
                'name' => 'Leave Management',
                'description' => 'Cuti & Izin pengguna dengan approval system',
                'features' => [
                    'Leave requests dengan multiple types', 
                    'Leave balance tracking', 
                    'PDF certificate generation',
                    'Approval workflow',
                ],
                'icon' => 'calendar',
            ],
            [
                'name' => 'User Management',
                'description' => 'Employee & user administration dengan module assignment',
                'features' => ['User CRUD', 'Module assignment', 'Role assignment', 'Profile management'],
                'icon' => 'users',
            ],
            [
                'name' => 'Project Monitoring',
                'description' => 'Project tracking & monitoring',
                'features' => ['Project overview', 'Work tracking', 'Payment tracking', 'Progress monitoring'],
                'icon' => 'briefcase',
            ],
        ];
        
        // Tech Stack
        $techStack = [
            'backend' => [
                'name' => 'Backend Framework',
                'items' => [
                    ['name' => 'Laravel', 'version' => app()->version(), 'description' => 'PHP Framework'],
                    ['name' => 'PHP', 'version' => PHP_VERSION, 'description' => 'Server-side Language'],
                    ['name' => 'Composer', 'version' => '2.x', 'description' => 'Dependency Manager'],
                ],
            ],
            'packages' => [
                'name' => 'Laravel Packages',
                'items' => [
                    ['name' => 'Spatie Laravel Permission', 'version' => 'v6.x', 'description' => 'RBAC - Role & Permission management'],
                    ['name' => 'Laravel Enums', 'version' => 'Native', 'description' => 'Type-safe status & constants'],
                ],
            ],
            'frontend' => [
                'name' => 'Frontend Technologies',
                'items' => [
                    ['name' => 'Blade Templates', 'version' => 'Laravel Native', 'description' => 'Server-side templating'],
                    ['name' => 'Tailwind CSS', 'version' => 'v3.x', 'description' => 'Utility-first CSS framework'],
                    ['name' => 'Alpine.js', 'version' => 'v3.x', 'description' => 'Lightweight JavaScript framework'],
                    ['name' => 'JavaScript', 'version' => 'ES6+', 'description' => 'Modern JavaScript features'],
                ],
            ],
            'database' => [
                'name' => 'Database & Storage',
                'items' => [
                    ['name' => 'MySQL', 'version' => '8.x', 'description' => 'Primary database'],
                    ['name' => 'Database Indexing', 'version' => 'Custom', 'description' => 'Performance optimization'],
                    ['name' => 'Eloquent ORM', 'version' => 'Laravel Native', 'description' => 'Database abstraction layer'],
                    ['name' => 'Migrations & Seeders', 'version' => 'Laravel Native', 'description' => 'Version control for database'],
                ],
            ],
        ];
        
        // Database Stats
        $dbStats = [
            'spd' => Spd::count(),
            'purchases' => Purchase::count(),
            'vendor_payments' => VendorPayment::count(),
            'leave_requests' => LeaveRequest::count(),
            'work_plans' => WorkPlan::count(),
            'work_realizations' => WorkRealization::count(),
        ];
        
        // Simple Text-Based Workflows
        $workflows = [
            'authentication' => [
                'title' => 'User Authentication Flow',
                'steps' => [
                    '1. User mengakses halaman login',
                    '2. Input email dan password',
                    '3. System validasi credentials',
                    '4. Jika valid, check role user (Admin/User)',
                    '5. Redirect ke dashboard sesuai role (/admin/dashboard atau /user/dashboard)',
                    '6. Access granted - User masuk ke sistem',
                ]
            ],
            'submission' => [
                'title' => 'Submission & Approval Flow (SPD, Purchase, Payment, Leave)',
                'steps' => [
                    '1. Karyawan membuat submission baru melalui modal form',
                    '2. Mengisi form sesuai jenis submission',
                    '3. Submit form ke system via AJAX',
                    '4. System validasi data (server-side)',
                    '5. Jika valid, save ke database dengan status "Pending"',
                    '6. System kirim notifikasi ke approver',
                    '7. Approver review submission',
                    '8. Approver memutuskan: Approve atau Reject dengan reason',
                    '9. System update status di database',
                    '10. System kirim notifikasi ke karyawan (approved/rejected)',
                    '11. Process complete',
                ]
            ],
            'work_management' => [
                'title' => 'Work Plan & Realization Flow',
                'steps' => [
                    '1. Awal hari: Karyawan isi Work Plan melalui modal form',
                    '2. Pilih project dari dropdown',
                    '3. Input detail rencana kerja (deskripsi, lokasi, field)',
                    '4. Submit Work Plan → Save ke database',
                    '5. Karyawan eksekusi pekerjaan sesuai plan',
                    '6. Akhir hari: Karyawan isi Work Realization melalui modal form',
                    '7. Input realisasi aktual (deskripsi, progress, attachments)',
                    '8. Submit Realization → Save ke database',
                    '9. Data tersedia di Project Monitoring untuk tracking',
                ]
            ],
            'database' => [
                'title' => 'Database Relationships (ERD)',
                'relationships' => [
                    'USERS → SPD (One to Many)' => 'Satu user dapat membuat banyak SPD',
                    'USERS → PURCHASES (One to Many)' => 'Satu user dapat membuat banyak Purchase',
                    'USERS → VENDOR_PAYMENTS (One to Many)' => 'Satu user dapat membuat banyak Payment',
                    'USERS → LEAVE_REQUESTS (One to Many)' => 'Satu user dapat mengajukan banyak Cuti',
                    'USERS → WORK_PLANS (One to Many)' => 'Satu user dapat membuat banyak Work Plan',
                    'USERS → WORK_REALIZATIONS (One to Many)' => 'Satu user dapat membuat banyak Realization',
                    'USERS ←→ MODULES (Many to Many)' => 'User dapat memiliki banyak modules, Module dapat di-assign ke banyak users',
                    'PROJECTS → SPD (One to Many)' => 'Satu project dapat memiliki banyak SPD',
                    'PROJECTS → PURCHASES (One to Many)' => 'Satu project dapat memiliki banyak Purchase',
                    'PROJECTS → WORK_PLANS (One to Many)' => 'Satu project dapat memiliki banyak Work Plan',
                    'VENDORS → VENDOR_PAYMENTS (One to Many)' => 'Satu vendor dapat menerima banyak Payment',
                ]
            ],
        ];
        
        return view('admin.documentation.index', compact(
            'changelogs',
            'overview',
            'roles',
            'modules',
            'techStack',
            'dbStats',
            'workflows'
        ));
    }
}
