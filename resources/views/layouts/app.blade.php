<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PGE System')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary-dark: #0a1628;
            --color-primary-medium: #1e3a5f;
            --color-primary-light: #1e40af;
        }
        
        [x-cloak] { display: none !important; }
        
        .sidebar-link {
            transition: all 0.2s ease-in-out;
        }
        
        .bg-active-link {
            background-color: #1e40af !important;
        }
        
        .bg-primary-blue {
            background-color: #1e40af !important;
        }
        
        .text-primary-blue {
            color: #1e40af !important;
        }
        
        .border-primary-blue {
            border-color: #1e40af !important;
        }
        
        .hover\:bg-primary-blue:hover {
            background-color: #1e3a8a !important;
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
    @stack('alpine-init')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
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
                        const response = await fetch('{{ route("notifications.unread") }}', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
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
                        const response = await fetch(`/notifications/${notificationId}/read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
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
                        const response = await fetch('{{ route("notifications.read-all") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
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
</head>
<body class="min-h-screen bg-gray-100 font-sans text-gray-800" x-data="{ sidebarOpen: true }">
    <div class="flex min-h-screen relative">
        <!-- Sidebar -->
        @auth
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="h-screen bg-slate-900 shadow-xl z-30 fixed md:sticky left-0 top-0 flex flex-col transition-all duration-300" style="background-color: #0a1628;">
            <div class="px-6 py-4 flex items-center justify-center">
                <img src="{{ asset('logopge.png') }}" alt="PGE Logo" class="h-10 w-auto flex-shrink-0 object-contain">
            </div>
            
            <!-- Toggle Button -->
            <button @click="sidebarOpen = !sidebarOpen" class="absolute -right-3 top-20 bg-slate-900 border-2 border-slate-700 text-white rounded-full p-1.5 hover:bg-slate-800 transition-all z-40 shadow-lg">
                <svg x-show="sidebarOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <svg x-show="!sidebarOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            
            <nav class="flex-grow flex flex-col mt-6 overflow-y-auto">
                <ul class="flex-1 px-3 space-y-2 text-[15px]">
                    <!-- Dashboard -->
                    <li>
                        @php
                            $dashboardRoute = auth()->user()->hasRole('admin') ? 'admin.dashboard' : 'user.dashboard';
                        @endphp
                        <a href="{{ route($dashboardRoute) }}" class="sidebar-link flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('*.dashboard') ? 'bg-active-link text-white font-semibold' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            <span x-show="sidebarOpen">Beranda</span>
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
                    <li class="mt-2">
                        <div class="px-4 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider" x-show="sidebarOpen">Administrasi</div>
                        @foreach($administrasiModules as $moduleData)
                            <a href="{{ route($moduleData['routeName']) }}" class="sidebar-link flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs($moduleData['routeName'] . '*') ? 'bg-active-link text-white font-semibold' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                                <span class="text-gray-300 relative">
                                    {{ $moduleData['module']->icon ?? '•' }}
                                    @if($moduleData['needsNotification'] ?? false)
                                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full border border-slate-800"></span>
                                    @endif
                                </span>
                                <span x-show="sidebarOpen" class="transition-all flex items-center gap-2 flex-1">
                                    {{ $moduleData['module']->label }}
                                    @if($moduleData['needsNotification'] ?? false)
                                    <svg class="w-3 h-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    @endif
                                </span>
                            </a>
                        @endforeach
                    </li>
                    @endif

                    <!-- Modul Section -->
                    @if(count($modulModules) > 0)
                    <li class="mt-2">
                        <div class="px-4 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider" x-show="sidebarOpen">Modul</div>
                        @foreach($modulModules as $moduleData)
                            <a href="{{ route($moduleData['routeName']) }}" class="sidebar-link flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs($moduleData['routeName'] . '*') ? 'bg-active-link text-white font-semibold' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                                <span class="text-gray-300 relative">
                                    {{ $moduleData['module']->icon ?? '•' }}
                                    @if($moduleData['needsNotification'] ?? false)
                                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full border border-slate-800"></span>
                                    @endif
                                </span>
                                <span x-show="sidebarOpen" class="transition-all flex items-center gap-2 flex-1">
                                    {{ $moduleData['module']->label }}
                                    @if($moduleData['needsNotification'] ?? false)
                                    <svg class="w-3 h-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    @endif
                                </span>
                            </a>
                        @endforeach
                    </li>
                    @endif

                    <!-- Activity Log (Fitur Bawaan - Bukan Module) -->
                    <li class="mt-4">
                        <div class="px-4 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider" x-show="sidebarOpen">Fitur</div>
                        @php
                            $activityLogRoute = $isAdmin ? 'admin.activity-log.index' : 'user.activity-log.index';
                        @endphp
                        <a href="{{ route($activityLogRoute) }}" class="sidebar-link flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('*.activity-log.*') ? 'bg-active-link text-white font-semibold' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span x-show="sidebarOpen">Activity Log</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Profile & Logout -->
            <div class="p-4 border-t" style="border-color: #1e3a5f;">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full text-white flex items-center justify-center text-sm font-semibold" style="background-color: #1e40af;">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0" x-show="sidebarOpen">
                        <div class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                
                <div class="space-y-1">
                    <a href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                       class="sidebar-link flex items-center gap-3 px-4 py-2 rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span class="text-sm" x-show="sidebarOpen">Keluar</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                </div>
            </div>
        </aside>
        @endauth

        <!-- Main content area -->
        <div class="flex-1 flex flex-col min-h-screen">
            @auth
            <!-- Top Header Bar -->
            <header class="text-white shadow-lg sticky top-0 z-20" style="background-color: #0a1628;">
                <div class="px-4 md:px-10 lg:px-14 xl:px-20 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div>
                            <h1 class="text-lg font-semibold">@yield('page-title', 'Dashboard')</h1>
                            <p class="text-xs text-gray-400 mt-0.5">@yield('page-subtitle', '')</p>
                        </div>
                    </div>
                    <!-- Notifications & Dokumentasi & Logs, Realtime Clock -->
                    <div class="flex items-center gap-4">
                        <!-- Notification Bell -->
                        <div x-data="notificationBell()" class="relative">
                            <button @click="toggleDropdown()" class="relative p-2 text-gray-300 hover:text-white hover:bg-slate-700 rounded-lg transition-colors">
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
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-slate-200 z-50 max-h-96 overflow-hidden flex flex-col">
                                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between" style="background-color: #0a1628;">
                                    <h3 class="text-sm font-semibold text-white">Notifikasi</h3>
                                    <button @click="markAllAsRead()" x-show="unreadCount > 0" class="text-xs text-gray-300 hover:text-white">Tandai semua dibaca</button>
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
                                           class="block px-4 py-3 border-b border-slate-100 hover:bg-slate-50 transition-colors"
                                           :class="notification.read_at ? 'bg-white' : 'bg-blue-50'">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-lg" 
                                                     :style="'background-color: ' + (notification.data.color === 'green' ? '#dcfce7' : notification.data.color === 'red' ? '#fee2e2' : '#dbeafe') + ';'">
                                                    <span x-text="notification.data.icon || '📄'"></span>
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
                                
                                <div class="px-4 py-2 border-t border-slate-200 text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat semua notifikasi</a>
                                </div>
                            </div>
                        </div>
                        <!-- Dokumentasi - Icon Only (Admin only) -->
                        @if(auth()->check() && auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.documentation') }}" 
                           class="relative p-2 rounded-lg hover:bg-slate-700 transition-colors {{ request()->routeIs('admin.documentation') ? 'bg-slate-700' : '' }}" 
                           title="Dokumentasi">
                            <svg class="w-6 h-6 {{ request()->routeIs('admin.documentation') ? 'text-white' : 'text-gray-300 hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </a>
                        
                        <!-- Telescope - Icon Only (Admin only) -->
                        <a href="{{ url('/telescope') }}" 
                           class="relative p-2 rounded-lg hover:bg-slate-700 transition-colors {{ request()->is('telescope*') ? 'bg-slate-700' : '' }}" 
                           title="Telescope - Monitoring">
                            <svg class="w-6 h-6 {{ request()->is('telescope*') ? 'text-white' : 'text-gray-300 hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                            <div class="text-xs text-gray-400" x-text="date"></div>
                            <div class="text-lg font-mono font-bold" x-text="time"></div>
                        </div>
                    </div>
                </div>
            </header>
            @endauth
            
            <!-- Main page content -->
            <main class="flex-1 px-4 md:px-10 lg:px-14 xl:px-20 py-6 w-full bg-white">
                @if(session('success'))
                    <div class="mb-4 flex items-center gap-4 bg-green-100 text-green-900 px-4 py-3 rounded-lg shadow-sm">
                        <span class="font-bold">✔</span>
                        <span class="flex-1">{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="ml-auto h-8 w-8 rounded hover:bg-green-200 flex items-center justify-center">×</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 flex items-center gap-4 bg-red-100 text-red-900 px-4 py-3 rounded-lg shadow-sm">
                        <span class="font-bold">!</span>
                        <span class="flex-1">{{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" class="ml-auto h-8 w-8 rounded hover:bg-red-200 flex items-center justify-center">×</button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 bg-red-100 text-red-900 px-4 py-3 rounded-lg shadow-sm">
                        <ul class="list-disc pl-6">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>

