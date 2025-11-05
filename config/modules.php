<?php

return [
    // Default actions if module doesn't override
    'default_actions' => ['view', 'create', 'update', 'delete'],

    // Module registry
    // Setiap modul adalah fitur terpisah yang bisa di-enable/disable per user
    // Property 'assignable_to_user' = true berarti modul bisa di-assign ke user
    // Property 'admin_only' = true berarti modul hanya untuk admin (untuk testing fitur baru)
    'list' => [
        // ============================================
        // DEFAULT MODULES (selalu ada untuk user, tapi bisa di-assign)
        // ============================================
        'work-plan' => [
            'label' => 'Rencana Kerja',
            'icon' => '🗓️',
            'routes' => [
                'index' => 'user.work-plans.index', // Sidebar akan convert ke admin.* untuk admin
            ],
            'actions' => ['view', 'create', 'update', 'delete'],
            'assignable_to_user' => true, // Bisa di-assign, tapi default tercentang
            'admin_only' => false,
            'category' => 'modul', // Kategori: administrasi atau modul
        ],
        'work-realization' => [
            'label' => 'Realisasi Kerja',
            'icon' => '✅',
            'routes' => [
                'index' => 'user.work-realizations.index', // Sidebar akan convert ke admin.* untuk admin
            ],
            'actions' => ['view', 'create', 'update', 'delete'],
            'assignable_to_user' => true, // Bisa di-assign, tapi default tercentang
            'admin_only' => false,
            'category' => 'modul', // Kategori: administrasi atau modul
        ],

        // ============================================
        // USER ASSIGNABLE MODULES
        // ============================================
        'leave' => [
            'label' => 'Cuti & Izin',
            'icon' => '🏝️',
            'routes' => [
                'index' => 'user.leaves.index',
            ],
            'actions' => ['view', 'create', 'update', 'delete'],
            'assignable_to_user' => true, // Admin bisa assign ke user - untuk mengajukan cuti
            'admin_only' => false,
            'category' => 'modul', // Kategori: administrasi atau modul
        ],
        'leave-approval' => [
            'label' => 'Daftar Cuti & Izin',
            'icon' => '📋',
            'routes' => [
                'index' => 'user.leave-approvals.index',
            ],
            'actions' => ['view', 'approve', 'reject', 'export'],
            'assignable_to_user' => true, // Admin bisa assign ke user - untuk approval cuti
            'admin_only' => false,
            'category' => 'administrasi', // Kategori: administrasi atau modul
        ],
        'spd' => [
            'label' => 'SPD',
            'icon' => '✈️',
            'routes' => [
                'index' => 'user.spd.index', // Sidebar akan convert ke admin.spd.index untuk admin
            ],
            'actions' => ['view', 'create', 'update', 'delete'],
            'assignable_to_user' => true, // Admin bisa assign ke user - untuk mengajukan SPD
            'admin_only' => false,
            'category' => 'modul', // Kategori: administrasi atau modul
        ],
        'purchase' => [
            'label' => 'Pembelian',
            'icon' => '🛒',
            'routes' => [
                'index' => 'user.purchases.index', // Sidebar akan convert ke admin.purchases.index untuk admin
            ],
            'actions' => ['view', 'create', 'update', 'delete'],
            'assignable_to_user' => true, // Admin bisa assign ke user - untuk mengajukan pembelian
            'admin_only' => false,
            'category' => 'modul', // Kategori: administrasi atau modul
        ],
        'vendor-payment' => [
            'label' => 'Pembayaran Vendor',
            'icon' => '💳',
            'routes' => [
                'index' => 'user.vendor-payments.index', // Sidebar akan convert ke admin.vendor-payments.index untuk admin
            ],
            'actions' => ['view', 'create', 'update', 'delete'],
            'assignable_to_user' => true, // Admin bisa assign ke user - untuk mengajukan pembayaran vendor
            'admin_only' => false,
            'category' => 'modul', // Kategori: administrasi atau modul
        ],
        'payment-approval' => [
            'label' => 'Approval Pembayaran',
            'icon' => '✅',
            'routes' => [
                'index' => 'user.payment-approvals.index', // Sidebar akan convert ke admin.approvals.payments.index untuk admin
            ],
            'actions' => ['view', 'approve', 'reject', 'export'],
            'assignable_to_user' => true, // Admin bisa assign ke user - untuk approval pembayaran
            'admin_only' => false,
            'category' => 'administrasi', // Kategori: administrasi atau modul
        ],
        'project-management' => [
            'label' => 'Project Management',
            'icon' => '📁',
            'routes' => [
                'index' => 'user.project-management.index',
            ],
            'actions' => ['view'],
            'assignable_to_user' => true, // Admin bisa assign ke user
            'admin_only' => false,
            'category' => 'administrasi', // Kategori: administrasi atau modul
        ],
        'ear' => [
            'label' => 'EAR',
            'icon' => '📊',
            'routes' => [
                'index' => 'admin.ear',
            ],
            'actions' => ['view'],
            'assignable_to_user' => true, // Admin bisa assign ke user untuk monitoring
            'admin_only' => false,
            'category' => 'administrasi', // Kategori: administrasi
        ],

        // ============================================
        // ADMIN-ONLY MODULES
        // ============================================
        'user' => [
            'label' => 'Manajemen User',
            'icon' => '👥',
            'routes' => [
                'index' => 'admin.users.index',
            ],
            'actions' => ['view', 'create', 'update', 'delete'],
            'assignable_to_user' => false, // Hanya admin
            'admin_only' => true,
            'category' => 'administrasi', // Kategori: administrasi atau modul
        ],
        'documentation' => [
            'label' => 'Dokumentasi',
            'icon' => '📚',
            'routes' => [
                'index' => 'admin.documentation',
            ],
            'actions' => ['view'],
            'assignable_to_user' => false, // Hanya admin
            'admin_only' => true,
            'category' => 'administrasi', // Kategori: administrasi atau modul
        ],
    ],
];

