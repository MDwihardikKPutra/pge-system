<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PGE System')</title>
    <link rel="icon" type="image/png" href="{{ asset('logopge.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('logopge.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logopge.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================
           COMPREHENSIVE DESIGN SYSTEM
           Inspired by Asana, Jira, Trello, Linear
           ============================================ */
        
        :root {
            /* Color System - Inspired by Asana/Jira */
            --color-primary: #2563eb;
            --color-primary-hover: #1d4ed8;
            --color-primary-light: #3b82f6;
            --color-secondary: #6366f1;
            
            /* Neutral Colors */
            --color-gray-50: #f9fafb;
            --color-gray-100: #f3f4f6;
            --color-gray-200: #e5e7eb;
            --color-gray-300: #d1d5db;
            --color-gray-400: #9ca3af;
            --color-gray-500: #6b7280;
            --color-gray-600: #4b5563;
            --color-gray-700: #374151;
            --color-gray-800: #1f2937;
            --color-gray-900: #111827;
            
            /* Semantic Colors */
            --color-success: #10b981;
            --color-success-light: #d1fae5;
            --color-warning: #f59e0b;
            --color-warning-light: #fef3c7;
            --color-error: #ef4444;
            --color-error-light: #fee2e2;
            --color-info: #3b82f6;
            --color-info-light: #dbeafe;
            
            /* Background & Surface */
            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
            --bg-tertiary: #f3f4f6;
            
            /* Border */
            --border-color: rgba(0, 0, 0, 0.08);
            --border-color-hover: rgba(0, 0, 0, 0.12);
            
            /* Shadows - Subtle & Professional */
            --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.1);
            
            /* Spacing Scale */
            --spacing-xs: 0.25rem;   /* 4px */
            --spacing-sm: 0.5rem;    /* 8px */
            --spacing-md: 1rem;      /* 16px */
            --spacing-lg: 1.5rem;    /* 24px */
            --spacing-xl: 2rem;      /* 32px */
            
            /* Typography */
            --font-size-xs: 0.75rem;    /* 12px */
            --font-size-sm: 0.875rem;   /* 14px */
            --font-size-base: 1rem;     /* 16px */
            --font-size-lg: 1.125rem;   /* 18px */
            --font-size-xl: 1.25rem;    /* 20px */
            --font-size-2xl: 1.5rem;    /* 24px */
            
            /* Border Radius */
            --radius-sm: 4px;
            --radius-md: 6px;
            --radius-lg: 8px;
            --radius-xl: 12px;
            --radius-full: 9999px;
            
            /* Transitions */
            --transition-fast: 0.15s ease;
            --transition-base: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 0.3s ease;
        }
        
        [x-cloak] { display: none !important; }
        
        body {
            background-color: #f8fafc; /* slate-50 */
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .sidebar-link {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .bg-active-link {
            background: #eff6ff !important; /* blue-50 */
            color: #2563eb !important; /* blue-600 */
            font-weight: 500;
        }
        
        /* ============================================
           COMPONENT SYSTEM - Cards (Project Style)
           ============================================ */
        .asana-card {
            background: #ffffff;
            border: 1px solid #e5e7eb; /* border-gray-200 */
            border-radius: 0.5rem; /* rounded-lg */
            padding: 1.25rem; /* p-5 */
            transition: all var(--transition-base);
        }
        
        .asana-card:hover {
            box-shadow: var(--shadow-sm);
        }
        
        /* ============================================
           COMPONENT SYSTEM - Tables (Project Style)
           ============================================ */
        .asana-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            background: #ffffff;
            border-radius: 0.5rem; /* rounded-lg */
            overflow: hidden;
            border: 1px solid #e5e7eb; /* border-gray-200 */
        }
        
        .asana-table thead {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .asana-table thead th {
            font-weight: 600;
            color: #111827; /* gray-900 */
            font-size: 0.75rem; /* text-xs */
            text-transform: none;
            letter-spacing: -0.01em;
            padding: 0.75rem 1rem; /* py-3 px-4 */
            text-align: left;
            background: #ffffff;
        }
        
        .asana-table tbody tr {
            transition: background-color var(--transition-fast);
            border-bottom: 1px solid #e5e7eb;
        }
        
        .asana-table tbody tr:hover {
            background-color: #f9fafb; /* gray-50 */
        }
        
        .asana-table tbody tr:last-child {
            border-bottom: none;
        }
        
        .asana-table tbody td {
            padding: 0.75rem 1rem; /* py-3 px-4 */
            color: #111827; /* gray-900 */
            font-size: 0.875rem; /* text-sm */
            font-weight: 400;
            vertical-align: middle;
        }
        
        /* ============================================
           COMPONENT SYSTEM - Buttons (Project Style)
           ============================================ */
        .asana-btn {
            border-radius: 0.5rem; /* rounded-lg */
            font-weight: 500;
            transition: all var(--transition-base);
            font-size: 0.875rem; /* text-sm */
            letter-spacing: -0.01em;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem; /* px-4 py-2 */
        }
        
        .asana-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .asana-btn:hover:not(:disabled) {
            opacity: 0.9;
        }
        
        .asana-btn:active:not(:disabled) {
            opacity: 0.8;
        }
        
        /* Button Variants */
        .asana-btn-primary {
            background: linear-gradient(to right, #3b82f6, #2563eb); /* from-blue-500 to-blue-600 */
            color: white;
        }
        
        .asana-btn-primary:hover:not(:disabled) {
            background: linear-gradient(to right, #2563eb, #1d4ed8);
        }
        
        .asana-btn-secondary {
            background: #ffffff;
            color: #374151; /* gray-700 */
            border: 1px solid #e5e7eb; /* border-gray-200 */
        }
        
        .asana-btn-secondary:hover:not(:disabled) {
            background: #f9fafb; /* gray-50 */
        }
        
        .asana-btn-ghost {
            background: transparent;
            color: #374151; /* gray-700 */
        }
        
        .asana-btn-ghost:hover:not(:disabled) {
            background: #f9fafb; /* gray-50 */
        }
        
        /* ============================================
           COMPONENT SYSTEM - Activity Items (Project Style)
           ============================================ */
        .asana-activity-item {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem; /* rounded-md */
            padding: 0.625rem; /* p-2.5 */
            margin-bottom: 0.5rem; /* space-y-2 */
            transition: all var(--transition-fast);
            cursor: pointer;
        }
        
        .asana-activity-item:hover {
            background: #f9fafb; /* gray-50 */
        }
        
        /* ============================================
           COMPONENT SYSTEM - Badges (Project Style)
           ============================================ */
        .badge-minimal {
            padding: 0.25rem 0.625rem; /* px-2.5 py-1 */
            border-radius: 9999px; /* rounded-full */
            font-size: 0.75rem; /* text-xs */
            font-weight: 500;
            letter-spacing: -0.01em;
            border: 1px solid;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        /* Badge Variants */
        .badge-success {
            background: #d1fae5; /* green-100 */
            color: #065f46; /* green-800 */
            border-color: #10b981; /* green-500 */
        }
        
        .badge-warning {
            background: #fef3c7; /* yellow-100 */
            color: #92400e; /* yellow-800 */
            border-color: #f59e0b; /* yellow-500 */
        }
        
        .badge-error {
            background: #fee2e2; /* red-100 */
            color: #991b1b; /* red-800 */
            border-color: #ef4444; /* red-500 */
        }
        
        .badge-info {
            background: #dbeafe; /* blue-100 */
            color: #1e40af; /* blue-800 */
            border-color: #3b82f6; /* blue-500 */
        }
        
        .badge-neutral {
            background: #f9fafb; /* gray-50 */
            color: #374151; /* gray-700 */
            border-color: #e5e7eb; /* gray-200 */
        }
        
        /* ============================================
           UTILITY CLASSES - Shadows
           ============================================ */
        .shadow-soft {
            box-shadow: var(--shadow-xs);
        }
        
        .shadow-soft-hover:hover {
            box-shadow: var(--shadow-md);
        }
        
        /* ============================================
           UTILITY CLASSES - Transitions & Animations
           ============================================ */
        .transition-smooth {
            transition: all var(--transition-base);
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* ============================================
           TYPOGRAPHY SYSTEM
           ============================================ */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', 'Roboto', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            line-height: 1.5;
            color: var(--color-gray-900);
        }
        
        h1, h2, h3, h4, h5, h6 {
            letter-spacing: -0.02em;
            font-weight: 600;
            line-height: 1.3;
            color: var(--color-gray-900);
        }
        
        /* ============================================
           FORM SYSTEM - Inputs (Project Style)
           ============================================ */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        input[type="month"],
        input[type="datetime-local"],
        textarea,
        select {
            border-radius: 0.5rem; /* rounded-lg */
            border: 1px solid #e5e7eb; /* border-gray-200 */
            padding: 0.375rem 0.75rem; /* px-3 py-1.5 */
            font-size: 0.875rem; /* text-sm */
            transition: all var(--transition-base);
            font-family: inherit;
            background: #ffffff;
            color: #111827; /* gray-900 */
            width: 100%;
        }
        
        input:hover,
        textarea:hover,
        select:hover {
            border-color: #d1d5db; /* gray-300 */
        }
        
        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #3b82f6; /* blue-500 */
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1); /* focus:ring-2 focus:ring-blue-500 */
        }
        
        input:disabled,
        textarea:disabled,
        select:disabled {
            background: #f9fafb; /* gray-50 */
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        /* Label styling */
        label {
            font-size: var(--font-size-xs);
            font-weight: 500;
            color: var(--color-gray-700);
            margin-bottom: var(--spacing-xs);
            display: block;
        }
        
        /* Form groups */
        .form-group {
            margin-bottom: var(--spacing-md);
        }
        
        /* ============================================
           COMPONENT SYSTEM - Alerts/Notifications
           ============================================ */
        .alert-success {
            background: var(--color-success-light);
            border: 0.5px solid var(--color-success);
            color: #065f46;
            border-radius: var(--radius-lg);
            padding: 12px 16px;
        }
        
        .alert-error {
            background: var(--color-error-light);
            border: 0.5px solid var(--color-error);
            color: #991b1b;
            border-radius: var(--radius-lg);
            padding: 12px 16px;
        }
        
        .alert-warning {
            background: var(--color-warning-light);
            border: 0.5px solid var(--color-warning);
            color: #92400e;
            border-radius: var(--radius-lg);
            padding: 12px 16px;
        }
        
        .alert-info {
            background: var(--color-info-light);
            border: 0.5px solid var(--color-info);
            color: #1e40af;
            border-radius: var(--radius-lg);
            padding: 12px 16px;
        }
        
        /* ============================================
           LAYOUT SYSTEM - Page Header
           ============================================ */
        .page-header {
            margin-bottom: var(--spacing-lg);
        }
        
        .page-title {
            font-size: var(--font-size-xl);
            font-weight: 600;
            color: var(--color-gray-900);
            letter-spacing: -0.02em;
            margin-bottom: var(--spacing-xs);
        }
        
        .page-subtitle {
            font-size: var(--font-size-sm);
            color: var(--color-gray-500);
        }
        
        /* ============================================
           UTILITY CLASSES - Spacing
           ============================================ */
        .space-y-compact > * + * {
            margin-top: var(--spacing-sm);
        }
        
        .space-y-normal > * + * {
            margin-top: var(--spacing-md);
        }
        
        /* ============================================
           COMPONENT SYSTEM - Modals (Project Style)
           ============================================ */
        .modal-overlay {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
        
        .modal-content {
            border-radius: 0.5rem; /* rounded-lg */
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            background: #ffffff;
            border: 1px solid #e5e7eb;
        }
        
        /* ============================================
           COMPONENT SYSTEM - Empty State
           ============================================ */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--color-gray-500);
        }
        
        .empty-state-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 1rem;
            opacity: 0.5;
        }
        
        /* ============================================
           COMPONENT SYSTEM - Loading Spinner
           ============================================ */
        .spinner {
            border: 2px solid var(--border-color);
            border-top-color: var(--color-primary);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 0.6s linear infinite;
        }
        
        /* ============================================
           COMPONENT SYSTEM - Filter Bar (Asana/Jira style)
           ============================================ */
        .filter-bar {
            display: flex;
            gap: var(--spacing-sm);
            flex-wrap: wrap;
            padding: var(--spacing-md);
            background: var(--bg-primary);
            border-bottom: 0.5px solid var(--border-color);
        }
        
        .filter-btn {
            padding: 6px 12px;
            border-radius: var(--radius-md);
            font-size: var(--font-size-xs);
            font-weight: 500;
            transition: all var(--transition-fast);
            border: 0.5px solid var(--border-color);
            background: var(--bg-secondary);
            color: var(--color-gray-700);
            cursor: pointer;
        }
        
        .filter-btn:hover {
            background: var(--bg-tertiary);
            border-color: var(--border-color-hover);
        }
        
        .filter-btn.active {
            background: var(--color-primary);
            color: white;
            border-color: var(--color-primary);
        }
        
        /* ============================================
           COMPONENT SYSTEM - Action Bar (Header with actions)
           ============================================ */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-md);
            background: var(--bg-primary);
            border-bottom: 0.5px solid var(--border-color);
        }
        
        /* ============================================
           DRAWER SIDEBAR STYLES
           ============================================ */
        /* Smooth drawer transition */
        #drawer-navigation {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Menu item hover effect */
        #drawer-navigation a {
            position: relative;
        }
        
        /* Active state indicator */
        #drawer-navigation a[class*="bg-blue-50"]::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: #2563eb;
            border-radius: 0 2px 2px 0;
        }
        
        /* Scrollbar styling for drawer */
        #drawer-navigation::-webkit-scrollbar {
            width: 6px;
        }
        
        #drawer-navigation::-webkit-scrollbar-track {
            background: transparent;
        }
        
        #drawer-navigation::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        #drawer-navigation::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Preview Modal Utilities - Global helper functions
        window.formatCurrency = (amount) => 'Rp ' + (parseFloat(amount || 0)).toLocaleString('id-ID');
        window.formatDate = (dateString) => dateString ? new Date(dateString).toLocaleDateString('id-ID') : '-';
        window.getStatusValue = (status) => {
            if (!status) return null;
            return typeof status === 'object' && status.value ? status.value : status;
        };
        
        // Reusable preview modal helper
        window.createPreviewHelper = (routePrefix, routeName, dataKey, errorMessage) => ({
            async openPreviewModal(id) {
                try {
                    const response = await fetch(`/${routePrefix}/${routeName}/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (!response.ok) throw new Error('Failed to fetch data');
                    
                    const data = await response.json();
                    if (data[dataKey]) {
                        this.previewData = data[dataKey];
                        this.showPreviewModal = true;
                    }
                } catch (error) {
                    console.error('Error fetching data:', error);
                    alert(errorMessage || 'Gagal memuat data');
                }
            },
            
            closePreviewModal() {
                this.showPreviewModal = false;
                this.previewData = null;
            }
        });
    </script>
    <script>
        // Project Searchable Select Alpine.js Component
        // Define before Alpine.js loads
        window.projectSearchableSelectData = function(initialDisplay = null, initialId = null) {
            return {
                searchQuery: initialDisplay || '',
                displayValue: initialDisplay || '',
                selectedId: initialId || null,
                projects: [],
                showDropdown: false,
                searching: false,
                searchTimeout: null,
                fetchController: null,

                init() {
                    // Set initial display value
                    if (initialDisplay) {
                        this.displayValue = initialDisplay;
                        this.searchQuery = initialDisplay;
                    }
                    
                    // If initialId exists but no display value, fetch project info
                    if (initialId && !initialDisplay) {
                        this.fetchProjectById(initialId);
                    }
                    
                    // Load initial projects if no selection and empty query
                    if (!initialId && this.searchQuery.length === 0) {
                        this.searchProjects();
                    }
                },

                destroy() {
                    // Cleanup: clear timeout and abort fetch requests
                    if (this.searchTimeout) {
                        clearTimeout(this.searchTimeout);
                    }
                    if (this.fetchController) {
                        this.fetchController.abort();
                    }
                },

                fetchProjectById(projectId) {
                    if (!projectId) return;
                    
                    // Abort previous request if exists
                    if (this.fetchController) {
                        this.fetchController.abort();
                    }
                    
                    // Create new AbortController for this request
                    this.fetchController = new AbortController();
                    this.searching = true;
                    
                    fetch(`{{ route('projects.search') }}?id=${encodeURIComponent(projectId)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        signal: this.fetchController.signal
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data && data.projects && data.projects.length > 0) {
                            const project = data.projects[0];
                            this.selectedId = project.id;
                            this.displayValue = project.display;
                            this.searchQuery = project.display;
                        } else {
                            // Project not found, clear selection
                            this.selectedId = null;
                            this.displayValue = '';
                            this.searchQuery = '';
                        }
                        this.searching = false;
                        this.fetchController = null;
                    })
                    .catch(error => {
                        if (error.name !== 'AbortError') {
                            console.error('Error fetching project:', error);
                        }
                        this.searching = false;
                        this.fetchController = null;
                    });
                },

                searchProjects() {
                    // Clear previous timeout
                    if (this.searchTimeout) {
                        clearTimeout(this.searchTimeout);
                    }
                    
                    // Abort previous fetch request if exists
                    if (this.fetchController) {
                        this.fetchController.abort();
                    }
                    
                    this.searchTimeout = setTimeout(() => {
                        // If query is less than 2 characters and not empty, clear results
                        if (this.searchQuery.length < 2 && this.searchQuery.length > 0) {
                            this.projects = [];
                            return;
                        }

                        // Create new AbortController for this request
                        this.fetchController = new AbortController();
                        this.searching = true;
                        const query = this.searchQuery || '';
                        const url = query.length === 0 
                            ? `{{ route('projects.search') }}?q=&limit=20`
                            : `{{ route('projects.search') }}?q=${encodeURIComponent(query)}&limit=20`;

                        fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            signal: this.fetchController.signal
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data && Array.isArray(data.projects)) {
                                this.projects = data.projects;
                            } else {
                                this.projects = [];
                            }
                            this.searching = false;
                            this.fetchController = null;
                        })
                        .catch(error => {
                            if (error.name !== 'AbortError') {
                                console.error('Error searching projects:', error);
                            }
                            this.searching = false;
                            this.projects = [];
                            this.fetchController = null;
                        });
                    }, 300); // Debounce 300ms
                },

                selectProject(project) {
                    if (!project || !project.id) return;
                    
                    this.selectedId = project.id;
                    this.displayValue = project.display || `${project.name} (${project.code})`;
                    this.searchQuery = this.displayValue;
                    this.showDropdown = false;
                    this.projects = []; // Clear dropdown results
                },

                clearSelection() {
                    this.selectedId = null;
                    this.displayValue = '';
                    this.searchQuery = '';
                    this.projects = [];
                    this.showDropdown = false;
                    // Reload projects after clearing
                    this.searchProjects();
                }
            };
        };

        // Register as Alpine.js data function
        document.addEventListener('alpine:init', () => {
            Alpine.data('projectSearchableSelect', window.projectSearchableSelectData);
        });
    </script>
    @stack('head-scripts')
    @stack('alpine-init')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.2.1/dist/flowbite.min.js"></script>
    
    <script>
        // Notification Bell Component
        function notificationBell() {
            return {
                showDropdown: false,
                notifications: [],
                unreadCount: 0,
                loading: false,
                
                init() {
                    // Only load notifications if user is authenticated
                    // Check if we're on a page that requires auth (not login page)
                    if (window.location.pathname !== '/login' && document.querySelector('meta[name="csrf-token"]')) {
                        this.loadNotifications();
                        // Auto-refresh every 30 seconds
                        setInterval(() => {
                            if (!this.showDropdown) {
                                this.loadNotifications();
                            }
                        }, 30000);
                    }
                },
                
                toggleDropdown() {
                    this.showDropdown = !this.showDropdown;
                    if (this.showDropdown) {
                        this.loadNotifications();
                    }
                },
                
                async loadNotifications() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                        const response = await fetch('{{ route("notifications.unread") }}', {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken || ''
                            },
                            credentials: 'same-origin' // Include cookies/session
                        });
                        
                        // Handle 401 (Unauthorized) gracefully - user not authenticated
                        if (response.status === 401) {
                            // User not authenticated, skip loading notifications
                            this.notifications = [];
                            this.unreadCount = 0;
                            return;
                        }
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        this.notifications = data.notifications || [];
                        this.unreadCount = data.unread_count || 0;
                    } catch (error) {
                        // Only log error if it's not a 401 (which is expected for unauthenticated users)
                        if (error.message && !error.message.includes('401')) {
                            console.error('Error loading notifications:', error);
                        }
                        this.notifications = [];
                        this.unreadCount = 0;
                    }
                },
                
                async markAsRead(notificationId) {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                        const response = await fetch(`/notifications/${notificationId}/read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        
                        if (response.status === 419) {
                            // CSRF token expired, reload page
                            window.location.reload();
                            return;
                        }
                        
                        const data = await response.json();
                        if (data.success) {
                            this.unreadCount = data.unread_count || 0;
                            // Update notification in list
                            const index = this.notifications.findIndex(n => n.id === notificationId);
                            if (index !== -1) {
                                this.notifications[index].read_at = new Date().toISOString();
                            }
                        }
                    } catch (error) {
                        console.error('Error marking notification as read:', error);
                    }
                },
                
                async markAllAsRead() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                        const response = await fetch('{{ route("notifications.read-all") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        
                        if (response.status === 419) {
                            // CSRF token expired, reload page
                            window.location.reload();
                            return;
                        }
                        
                        const data = await response.json();
                        if (data.success) {
                            this.unreadCount = 0;
                            this.notifications.forEach(n => n.read_at = new Date().toISOString());
                        }
                    } catch (error) {
                        console.error('Error marking all as read:', error);
                    }
                }
            }
        }
    </script>
    
    <!-- Global CSRF Error Handler -->
    <script>
        // Handle 419 errors globally
        document.addEventListener('DOMContentLoaded', function() {
            // Intercept form submissions
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form.tagName === 'FORM' && form.method.toLowerCase() === 'post') {
                    // Check if CSRF token exists
                    const csrfInput = form.querySelector('input[name="_token"]');
                    if (!csrfInput) {
                        // Try to get from meta tag and add to form
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        if (csrfMeta) {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = '_token';
                            hiddenInput.value = csrfMeta.content;
                            form.appendChild(hiddenInput);
                        }
                    }
                }
            });
            
            // Intercept fetch requests for 419 errors
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                return originalFetch.apply(this, args).then(response => {
                    if (response.status === 419) {
                        // CSRF token expired
                        if (confirm('Session expired. Halaman akan di-refresh untuk memperbarui token.')) {
                            window.location.reload();
                        }
                        return Promise.reject(new Error('CSRF token expired'));
                    }
                    return response;
                });
            };
        });
    </script>
</head>
<body class="min-h-screen bg-slate-50 font-sans text-gray-900">
    <div class="flex min-h-screen relative">
        <!-- Drawer Sidebar -->
        @auth
        <!-- Drawer component -->
        <div id="drawer-navigation" class="fixed top-0 left-0 z-40 w-64 h-screen overflow-hidden transition-transform duration-300 ease-in-out -translate-x-full shadow-lg" style="background-color: #0a1628; border-right: 1px solid #1e293b;" tabindex="-1" aria-labelledby="drawer-navigation-label" data-drawer-backdrop="true">
            <!-- Header with Logo and Close Button -->
            <div class="flex items-center justify-between px-4 py-3 border-b sticky top-0 z-10" style="background-color: #0a1628; border-color: #1e293b;">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logopge.png') }}" alt="PGE Logo" class="h-8 w-auto object-contain">
                </div>
                <button type="button" data-drawer-hide="drawer-navigation" aria-controls="drawer-navigation" class="text-gray-400 hover:text-white rounded-lg p-1.5 transition-colors duration-200" style="background-color: transparent;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='transparent'">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">Close menu</span>
                </button>
            </div>
            
            <!-- Navigation Menu -->
            <div class="flex-1 overflow-y-auto px-2 py-3 pb-24">
                <ul class="space-y-1">
                    <!-- Dashboard -->
                    <li>
                        @php
                            $dashboardRoute = auth()->user()->hasRole('admin') ? 'admin.dashboard' : 'user.dashboard';
                            $isActive = request()->routeIs('*.dashboard');
                        @endphp
                        <a href="{{ route($dashboardRoute) }}" data-drawer-hide="drawer-navigation" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $isActive ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ $isActive ? 'background-color: #1e293b;' : '' }}" onmouseover="if(!this.classList.contains('active')) this.style.backgroundColor='#1e293b'" onmouseout="if(!this.classList.contains('active')) this.style.backgroundColor='transparent'">
                            <svg class="w-4 h-4 flex-shrink-0 {{ $isActive ? 'text-white' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 22 21">
                                <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                                <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                            </svg>
                            <span class="truncate">Dashboard</span>
                        </a>
                    </li>

                    @php
                        $user = auth()->user();
                        $isAdmin = $user->hasRole('admin');
                        $activeModules = $user->getActiveModules();
                        
                        // Work notification status is now handled by WorkNotificationComposer
                        // Variables are automatically available: $workPlanNeedsNotification, $workRealizationNeedsNotification
                        
                        // Group modules by category
                        $administrasiModules = [];
                        $modulModules = [];
                        
                        foreach ($activeModules as $module) {
                            // Skip admin-only modules for non-admin users
                            if ($module->admin_only && !$isAdmin) continue;
                            
                            $moduleKey = $module->key;
                            
                            // Skip documentation module - only show in headbar, not in sidebar
                            if ($moduleKey === 'documentation') continue;
                            $routes = $module->routes ?? [];
                            // Use hasModuleAccess instead of permission check for better flexibility
                            $canView = $user->hasModuleAccess($moduleKey);
                            if ($isAdmin) $canView = true;
                            
                            // Generate route name based on role
                            $baseRouteName = $routes['index'] ?? null;
                            if ($baseRouteName) {
                                if ($isAdmin) {
                                    // Special handling for approval modules
                                    if ($moduleKey === 'payment-approval') {
                                        $routeName = 'admin.approvals.payments.index';
                                    } elseif ($moduleKey === 'leave-approval') {
                                        $routeName = 'admin.approvals.leaves';
                                    } elseif ($moduleKey === 'project-management') {
                                        $routeName = 'admin.project-management.index';
                                    } elseif ($moduleKey === 'ear') {
                                        $routeName = 'admin.ear';
                                    } else {
                                        $routeName = str_replace('user.', 'admin.', $baseRouteName);
                                    }
                                    if (!\Illuminate\Support\Facades\Route::has($routeName)) {
                                        $routeName = $baseRouteName;
                                    }
                                } else {
                                    // For non-admin users
                                    if ($moduleKey === 'ear') {
                                        // EAR uses admin.ear route for both admin and user
                                        $routeName = 'admin.ear';
                                    } else {
                                        $routeName = $baseRouteName;
                                    }
                                    // Check if route exists, if not try admin route
                                    if (!\Illuminate\Support\Facades\Route::has($routeName)) {
                                        $adminRoute = str_replace('user.', 'admin.', $baseRouteName);
                                        if (\Illuminate\Support\Facades\Route::has($adminRoute)) {
                                            $routeName = $adminRoute;
                                        }
                                    }
                                }
                            } else {
                                $routeName = null;
                            }
                            
                            $routeExists = $routeName && \Illuminate\Support\Facades\Route::has($routeName);
                            if ($routeExists && ($canView || $isAdmin)) {
                                // Get category from module model or fallback to config
                                $category = $module->category ?? (config("modules.list.{$moduleKey}.category") ?? 'modul');
                                
                                // Check if this module needs notification
                                $needsNotification = false;
                                if ($moduleKey === 'work-plan') {
                                    $needsNotification = $workPlanNeedsNotification;
                                } elseif ($moduleKey === 'work-realization') {
                                    $needsNotification = $workRealizationNeedsNotification;
                                }
                                
                                $moduleData = [
                                    'module' => $module,
                                    'routeName' => $routeName,
                                    'moduleKey' => $moduleKey,
                                    'needsNotification' => $needsNotification
                                ];
                                
                                if ($category === 'administrasi') {
                                    $administrasiModules[] = $moduleData;
                                } else {
                                    $modulModules[] = $moduleData;
                                }
                            }
                        }
                    @endphp

                    <!-- Administrasi Section -->
                    @if(count($administrasiModules) > 0 || auth()->user()->hasRole('admin'))
                    <li class="mt-3 mb-2">
                        <div class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider" style="color: #64748b;">Administrasi</div>
                    </li>
                    @foreach($administrasiModules as $moduleData)
                    <li>
                        @php
                            $isActive = request()->routeIs($moduleData['routeName'] . '*');
                        @endphp
                        <a href="{{ route($moduleData['routeName']) }}" data-drawer-hide="drawer-navigation" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $isActive ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ $isActive ? 'background-color: #1e293b;' : '' }}" onmouseover="if(!this.classList.contains('active')) this.style.backgroundColor='#1e293b'" onmouseout="if(!this.classList.contains('active')) this.style.backgroundColor='transparent'">
                            <span class="relative flex-shrink-0 w-4 h-4 flex items-center justify-center">
                                @php
                                    $iconType = \App\Helpers\IconHelper::getModuleIconType($moduleData['moduleKey']);
                                @endphp
                                <x-icon type="{{ $iconType }}" class="w-4 h-4 {{ $isActive ? 'text-white' : 'text-gray-400' }}" />
                                @if($moduleData['needsNotification'] ?? false)
                                <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                                @endif
                            </span>
                            <span class="flex-1 truncate flex items-center gap-2">
                                {{ $moduleData['module']->label }}
                                @if($moduleData['needsNotification'] ?? false)
                                <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1.5 text-[10px] font-semibold text-white bg-red-500 rounded-full flex-shrink-0">!</span>
                                @endif
                            </span>
                        </a>
                    </li>
                    @endforeach
                    @endif

                    <!-- Modul Section -->
                    @if(count($modulModules) > 0)
                    <li class="mt-3 mb-2">
                        <div class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider" style="color: #64748b;">Modul</div>
                    </li>
                    @foreach($modulModules as $moduleData)
                    <li>
                        @php
                            $isActive = request()->routeIs($moduleData['routeName'] . '*');
                        @endphp
                        <a href="{{ route($moduleData['routeName']) }}" data-drawer-hide="drawer-navigation" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $isActive ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ $isActive ? 'background-color: #1e293b;' : '' }}" onmouseover="if(!this.classList.contains('active')) this.style.backgroundColor='#1e293b'" onmouseout="if(!this.classList.contains('active')) this.style.backgroundColor='transparent'">
                            <span class="relative flex-shrink-0 w-4 h-4 flex items-center justify-center">
                                @php
                                    $iconType = \App\Helpers\IconHelper::getModuleIconType($moduleData['moduleKey']);
                                @endphp
                                <x-icon type="{{ $iconType }}" class="w-4 h-4 {{ $isActive ? 'text-white' : 'text-gray-400' }}" />
                                @if($moduleData['needsNotification'] ?? false)
                                <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                                @endif
                            </span>
                            <span class="flex-1 truncate flex items-center gap-2">
                                {{ $moduleData['module']->label }}
                                @if($moduleData['needsNotification'] ?? false)
                                <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1.5 text-[10px] font-semibold text-white bg-red-500 rounded-full flex-shrink-0">!</span>
                                @endif
                            </span>
                        </a>
                    </li>
                    @endforeach
                    @endif

                    <!-- Activity Log (Fitur Bawaan - Bukan Module) -->
                    <li class="mt-3 mb-2">
                        <div class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider" style="color: #64748b;">Fitur</div>
                    </li>
                    <li>
                        @php
                            $activityLogRoute = $isAdmin ? 'admin.activity-log.index' : 'user.activity-log.index';
                            $isActive = request()->routeIs('*.activity-log.*');
                        @endphp
                        <a href="{{ route($activityLogRoute) }}" data-drawer-hide="drawer-navigation" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $isActive ? 'text-white' : 'text-gray-300 hover:text-white' }}" style="{{ $isActive ? 'background-color: #1e293b;' : '' }}" onmouseover="if(!this.classList.contains('active')) this.style.backgroundColor='#1e293b'" onmouseout="if(!this.classList.contains('active')) this.style.backgroundColor='transparent'">
                            <svg class="w-4 h-4 flex-shrink-0 {{ $isActive ? 'text-white' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 18 20">
                                <path d="M17 5.923A1 1 0 0 0 16 5h-3V4a4 4 0 1 0-8 0v1H2a1 1 0 0 0-1 .923L.086 17.846A2 2 0 0 0 2.08 20h13.84a2 2 0 0 0 1.994-2.153L17 5.923ZM7 9a1 1 0 0 1-2 0V7h2v2Zm0-5a2 2 0 1 1 4 0v1H7V4Zm6 5a1 1 0 1 1-2 0V7h2v2Z"/>
                            </svg>
                            <span class="truncate">Activity Log</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- User Profile & Logout - Sticky Footer -->
            <div class="absolute bottom-0 left-0 right-0 border-t px-3 py-3" style="background-color: #0a1628; border-color: #1e293b;">
                <div class="flex items-center gap-2.5 mb-2.5">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-semibold flex-shrink-0 shadow-sm" style="background-color: #1e293b;">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-medium text-white truncate">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] truncate" style="color: #94a3b8;">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                
                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                   data-drawer-hide="drawer-navigation"
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200" style="color: #cbd5e1;" onmouseover="this.style.backgroundColor='#1e293b'; this.style.color='#ffffff';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#cbd5e1';">
                    <svg class="w-4 h-4 flex-shrink-0" style="color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 18 16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 8h11m0 0L8 4m4 4-4 4m4-11h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-3"/>
                    </svg>
                    <span class="truncate">Keluar</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </div>
        </div>
        @endauth

        <!-- Main content area -->
        <div class="flex-1 flex flex-col min-h-screen">
            @auth
            <!-- Top Header Bar -->
            <header class="sticky top-0 z-20" style="background-color: #0a1628; border-bottom: 1px solid #1e293b;">
                <div class="px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3 flex-1">
                        <!-- Hamburger Menu Button -->
                        <button type="button" data-drawer-target="drawer-navigation" data-drawer-show="drawer-navigation" aria-controls="drawer-navigation" class="text-gray-300 hover:text-white rounded-lg p-2 transition-all duration-200" style="background-color: transparent;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='transparent'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            <span class="sr-only">Toggle sidebar</span>
                        </button>
                        <div>
                            <h1 class="text-lg font-semibold text-white">@yield('page-title', 'Dashboard')</h1>
                            <p class="text-xs mt-0.5" style="color: #94a3b8;">@yield('page-subtitle', '')</p>
                        </div>
                    </div>
                    <!-- Logo PGE di Tengah -->
                    <div class="flex-1 flex items-center justify-center">
                        <img src="{{ asset('logopge.png') }}" alt="PGE Logo" class="h-10 w-auto object-contain">
                    </div>
                    <!-- Notifications & Dokumentasi & Logs, Realtime Clock -->
                    <div class="flex items-center gap-4 flex-1 justify-end">
                        <!-- Notification Bell -->
                        <div x-data="notificationBell()" class="relative">
                            <button @click="toggleDropdown()" class="relative p-1.5 text-gray-300 hover:text-white rounded-lg transition-colors" style="background-color: transparent;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='transparent'">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span x-show="unreadCount > 0" class="absolute top-0 right-0 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center" x-text="unreadCount"></span>
                            </button>
                            
                            <!-- Dropdown -->
                            <div x-show="showDropdown" 
                                 @click.away="showDropdown = false"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-hidden flex flex-col">
                                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-white">
                                    <h3 class="text-sm font-semibold text-gray-900">Notifikasi</h3>
                                    <button @click="markAllAsRead()" x-show="unreadCount > 0" class="text-xs text-gray-600 hover:text-gray-900">Tandai semua dibaca</button>
                                </div>
                                
                                <div class="overflow-y-auto flex-1">
                                    <template x-if="notifications.length === 0">
                                        <div class="p-6 text-center text-sm text-gray-500">
                                            <p>Tidak ada notifikasi</p>
                                        </div>
                                    </template>
                                    
                                    <template x-for="notification in notifications" :key="notification.id">
                                        <a :href="notification.data.url || '#'" 
                                           @click="markAsRead(notification.id)"
                                           class="block px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors"
                                           :class="notification.read_at ? 'bg-white' : 'bg-blue-50'">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center" 
                                                     :style="'background-color: ' + (notification.data.color === 'green' ? '#dcfce7' : notification.data.color === 'red' ? '#fee2e2' : '#dbeafe') + ';'">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                                              :d="(notification.data.icon && !['📄', '📋', '✅', '✈️', '🛒', '💳', '🏝️', '📁'].includes(notification.data.icon)) ? 
                                                                  (notification.data.icon === 'clipboard-document' ? 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z' :
                                                                  notification.data.icon === 'check-circle' ? 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z' :
                                                                  notification.data.icon === 'paper-airplane' ? 'M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5' :
                                                                  notification.data.icon === 'shopping-cart' ? 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z' :
                                                                  notification.data.icon === 'credit-card' ? 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z' :
                                                                  notification.data.icon === 'calendar' ? 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 005.25 9h13.5A2.25 2.25 0 0121 11.25v7.5' :
                                                                  notification.data.icon === 'folder' ? 'M2.25 12.75V12A2.25 2.25 0 014.5 9.75h6A2.25 2.25 0 0112.75 12v.75m-8.5-3A2.25 2.25 0 003.75 15h13.5A2.25 2.25 0 0021.75 12.75m-16.5 0A2.25 2.25 0 013.75 9h6m-6 0A2.25 2.25 0 001.5 6.75v-.75m0 0A2.25 2.25 0 013.75 4.5h6.379a2.251 2.251 0 011.59.659l2.122 2.121c.14.141.331.22.53.22H19.5A2.25 2.25 0 0121.75 9v.75' :
                                                                  'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z') :
                                                                  'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'"></path>
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-900 truncate" x-text="notification.data.title || 'Notifikasi'"></p>
                                                    <p class="text-xs text-gray-600 mt-1 line-clamp-2" x-text="notification.data.message || ''"></p>
                                                    <p class="text-xs text-gray-400 mt-1" x-text="notification.created_at"></p>
                                                </div>
                                                <div x-show="!notification.read_at" class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                                
                                <div class="px-4 py-2 border-t border-gray-200 text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat semua notifikasi</a>
                                </div>
                            </div>
                        </div>
                        <!-- Dokumentasi - Icon Only (Admin only) -->
                        @if(auth()->check() && auth()->user()->hasRole('admin'))
                        @php
                            $isDocActive = request()->routeIs('admin.documentation');
                        @endphp
                        <a href="{{ route('admin.documentation') }}" 
                           class="relative p-2 rounded-lg transition-colors" 
                           style="background-color: {{ $isDocActive ? '#1e293b' : 'transparent' }};"
                           onmouseover="this.style.backgroundColor='#1e293b'; this.querySelector('svg').classList.remove('text-gray-300'); this.querySelector('svg').classList.add('text-white');" 
                           onmouseout="this.style.backgroundColor='{{ $isDocActive ? '#1e293b' : 'transparent' }}'; this.querySelector('svg').classList.remove('text-white'); this.querySelector('svg').classList.add('text-gray-300');"
                           title="Dokumentasi">
                            <svg class="w-6 h-6 {{ $isDocActive ? 'text-white' : 'text-gray-300' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </a>
                        
                        <!-- Telescope - Icon Only (Admin only) -->
                        @php
                            $isTelescopeActive = request()->is('telescope*');
                        @endphp
                        <a href="{{ url('/telescope') }}" 
                           class="relative p-2 rounded-lg transition-colors" 
                           style="background-color: {{ $isTelescopeActive ? '#1e293b' : 'transparent' }};"
                           onmouseover="this.style.backgroundColor='#1e293b'; this.querySelector('svg').classList.remove('text-gray-300'); this.querySelector('svg').classList.add('text-white');" 
                           onmouseout="this.style.backgroundColor='{{ $isTelescopeActive ? '#1e293b' : 'transparent' }}'; this.querySelector('svg').classList.remove('text-white'); this.querySelector('svg').classList.add('text-gray-300');"
                           title="Telescope - Monitoring">
                            <svg class="w-6 h-6 {{ $isTelescopeActive ? 'text-white' : 'text-gray-300' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </a>
                        @endif
                        
                        <!-- Realtime Clock -->
                        <div class="text-right" x-data="{ 
                            time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
                            date: new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }),
                            init() {
                                setInterval(() => {
                                    const now = new Date();
                                    this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                                    this.date = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                                }, 1000);
                            }
                        }">
                            <div class="text-xs" style="color: #94a3b8;" x-text="date"></div>
                            <div class="text-lg font-mono font-bold text-white" x-text="time"></div>
                        </div>
                    </div>
                </div>
            </header>
            @endauth
            
            <!-- Main page content -->
            <main class="flex-1 px-3 md:px-4 lg:px-6 py-4 w-full bg-slate-50 overflow-hidden flex flex-col">
                <div class="w-full">
                @if(session('success'))
                        <div class="mb-4 alert-success flex items-center gap-3 animate-fade-in">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="flex-1 text-sm font-medium">{{ session('success') }}</span>
                            <button onclick="this.parentElement.remove()" class="ml-auto text-gray-400 hover:text-gray-600 transition-colors p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                    </div>
                @endif
                @if(session('error'))
                        <div class="mb-4 alert-error flex items-center gap-3 animate-fade-in">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="flex-1 text-sm font-medium">{{ session('error') }}</span>
                            <button onclick="this.parentElement.remove()" class="ml-auto text-gray-400 hover:text-gray-600 transition-colors p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                    </div>
                @endif
                @if($errors->any())
                        <div class="mb-4 alert-error">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-semibold">Terjadi kesalahan:</span>
                            </div>
                            <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>

