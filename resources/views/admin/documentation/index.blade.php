@extends('layouts.app')

@section('title', 'Dokumentasi Sistem')
@section('page-title', 'Dokumentasi Aplikasi')
@section('page-subtitle', 'PGE System - Complete Documentation')

@section('content')
<div class="py-8" x-data="{ activeTab: 'overview' }">
    <!-- Navigation Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex gap-4 overflow-x-auto">
            <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'border-slate-600 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-all">
                Overview
            </button>
            <button @click="activeTab = 'roles'" :class="activeTab === 'roles' ? 'border-slate-600 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-all">
                User Roles
            </button>
            <button @click="activeTab = 'modules'" :class="activeTab === 'modules' ? 'border-slate-600 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-all">
                Modules
            </button>
            <button @click="activeTab = 'workflow'" :class="activeTab === 'workflow' ? 'border-slate-600 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-all">
                Workflow
            </button>
            <button @click="activeTab = 'technical'" :class="activeTab === 'technical' ? 'border-slate-600 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-all">
                Technical
            </button>
            <button @click="activeTab = 'database'" :class="activeTab === 'database' ? 'border-slate-600 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-all">
                Database
            </button>
            <button @click="activeTab = 'changelog'" :class="activeTab === 'changelog' ? 'border-slate-600 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-all">
                Changelog
            </button>
        </nav>
    </div>

    <!-- Overview Tab -->
    <div x-show="activeTab === 'overview'" class="space-y-4">
        <div class="bg-white border border-slate-200 rounded p-5">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">PGE System - Integrated Management Platform</h2>
                    <p class="text-xs text-slate-500 mt-1">Version {{ $overview['version'] }} • Laravel {{ $overview['laravel_version'] }} • MySQL • Alpine.js</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-300">Production Ready</span>
            </div>
            <p class="text-sm text-slate-600 mb-5">
                Sistem manajemen kantor komprehensif yang mengelola seluruh workflow operasional perusahaan, 
                mulai dari perencanaan kerja, realisasi, keuangan, cuti, hingga monitoring project dengan 
                modular system dan role-based access control.
            </p>

            <!-- Key Features -->
            <div class="mt-5">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Key Features</h3>
                <div class="grid md:grid-cols-2 gap-2">
                    <div class="flex gap-2 items-start p-2 rounded hover:bg-slate-50 transition-colors border border-slate-100">
                        <div class="flex-shrink-0 w-6 h-6 bg-slate-100 rounded flex items-center justify-center">
                            <svg class="w-3 h-3 text-slate-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-slate-900">Work Management</div>
                            <div class="text-xs text-slate-600">Rencana & Realisasi Kerja dengan modal forms</div>
                        </div>
                    </div>
                    <div class="flex gap-2 items-start p-2 rounded hover:bg-slate-50 transition-colors border border-slate-100">
                        <div class="flex-shrink-0 w-6 h-6 bg-slate-100 rounded flex items-center justify-center">
                            <svg class="w-3 h-3 text-slate-600" fill="currentColor" viewBox="0 0 20 20"><path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-slate-900">Manajemen Pembayaran</div>
                            <div class="text-xs text-slate-600">SPD, Pembelian, Vendor Payment dengan approval workflow</div>
                        </div>
                    </div>
                    <div class="flex gap-2 items-start p-2 rounded hover:bg-slate-50 transition-colors border border-slate-100">
                        <div class="flex-shrink-0 w-6 h-6 bg-slate-100 rounded flex items-center justify-center">
                            <svg class="w-3 h-3 text-slate-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-slate-900">Leave Management</div>
                            <div class="text-xs text-slate-600">Cuti & Izin dengan approval system</div>
                        </div>
                    </div>
                    <div class="flex gap-2 items-start p-2 rounded hover:bg-slate-50 transition-colors border border-slate-100">
                        <div class="flex-shrink-0 w-6 h-6 bg-slate-100 rounded flex items-center justify-center">
                            <svg class="w-3 h-3 text-slate-600" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-slate-900">Project Monitoring</div>
                            <div class="text-xs text-slate-600">Centralized Project Tracking & Monitoring</div>
                        </div>
                    </div>
                    <div class="flex gap-2 items-start p-2 rounded hover:bg-slate-50 transition-colors border border-slate-100">
                        <div class="flex-shrink-0 w-6 h-6 bg-slate-100 rounded flex items-center justify-center">
                            <svg class="w-3 h-3 text-slate-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-slate-900">User Management</div>
                            <div class="text-xs text-slate-600">Module Assignment & Role-based Access Control</div>
                        </div>
                    </div>
                    <div class="flex gap-2 items-start p-2 rounded hover:bg-slate-50 transition-colors border border-slate-100">
                        <div class="flex-shrink-0 w-6 h-6 bg-slate-100 rounded flex items-center justify-center">
                            <svg class="w-3 h-3 text-slate-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-slate-900">Modular System</div>
                            <div class="text-xs text-slate-600">Dynamic module assignment per user dengan granular control</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Stats -->
            <div class="mt-5 pt-4 border-t border-slate-200">
                <h3 class="text-sm font-semibold text-slate-900 mb-2">System Statistics</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-3 bg-slate-50 rounded-lg">
                        <div class="text-2xl font-bold text-slate-900">{{ $overview['total_users'] }}</div>
                        <div class="text-xs text-slate-600 mt-1">Total Users</div>
                    </div>
                    <div class="text-center p-3 bg-slate-50 rounded-lg">
                        <div class="text-2xl font-bold text-slate-900">{{ $overview['total_projects'] }}</div>
                        <div class="text-xs text-slate-600 mt-1">Total Projects</div>
                    </div>
                    <div class="text-center p-3 bg-slate-50 rounded-lg">
                        <div class="text-2xl font-bold text-slate-900">{{ $overview['total_vendors'] }}</div>
                        <div class="text-xs text-slate-600 mt-1">Total Vendors</div>
                    </div>
                </div>
            </div>

            <!-- Tech Stack -->
            <div class="mt-5 pt-4 border-t border-slate-200">
                <h3 class="text-sm font-semibold text-slate-900 mb-2">Tech Stack</h3>
                <div class="flex flex-wrap gap-2">
                    <span class="px-2 py-1 bg-slate-100 text-slate-700 text-xs font-medium rounded border border-slate-200">Laravel {{ $overview['laravel_version'] }}</span>
                    <span class="px-2 py-1 bg-slate-100 text-slate-700 text-xs font-medium rounded border border-slate-200">MySQL</span>
                    <span class="px-2 py-1 bg-slate-100 text-slate-700 text-xs font-medium rounded border border-slate-200">Tailwind CSS</span>
                    <span class="px-2 py-1 bg-slate-100 text-slate-700 text-xs font-medium rounded border border-slate-200">Alpine.js</span>
                    <span class="px-2 py-1 bg-slate-100 text-slate-700 text-xs font-medium rounded border border-slate-200">PHP {{ $overview['php_version'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Tab -->
    <div x-show="activeTab === 'roles'" class="space-y-4">
        <div class="bg-white border rounded">
            <div class="px-6 py-4 border-b style="background-color: #0a1628;"">
                <h2 class="text-base font-semibold text-white">User Roles & Permissions</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b">
                        <tr>
                            <th class="py-2 px-3 text-left font-medium text-slate-700">Role</th>
                            <th class="py-2 px-3 text-left font-medium text-slate-700">Description</th>
                            <th class="py-2 px-3 text-left font-medium text-slate-700">Capabilities</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr class="border-b hover:bg-slate-50">
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 {{ $role['name'] === 'Admin' ? 'bg-purple-500' : 'bg-blue-500' }} rounded-full"></span>
                                    <span class="font-semibold text-gray-900">{{ $role['name'] }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-gray-600">{{ $role['description'] }}</td>
                            <td class="py-3 px-3">
                                <ul class="space-y-1 text-xs text-gray-600">
                                    @foreach($role['capabilities'] as $capability)
                                    <li>• {{ $capability }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modules Tab -->
    <div x-show="activeTab === 'modules'" class="space-y-4">
        <div class="bg-white border rounded p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">System Modules</h2>
            
            <div class="space-y-6">
                @foreach($modules as $module)
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-2 h-2 style="background-color: #0a1628;" rounded-full"></span>
                        {{ $module['name'] }}
                    </h3>
                    <p class="text-sm text-gray-600 mb-2">{{ $module['description'] }}</p>
                    <ul class="text-sm text-gray-600 space-y-1 ml-4">
                        @foreach($module['features'] as $feature)
                        <li>• {{ $feature }}</li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Workflow Tab -->
    <div x-show="activeTab === 'workflow'" class="space-y-4">
        <div class="bg-white border rounded p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">System Workflows & Database Relationships</h2>
            <p class="text-sm text-gray-600 mb-6">Alur kerja sistem dan relasi antar tabel database PGE System.</p>
            
            <div class="space-y-6">
                @foreach($workflows as $key => $workflow)
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <h3 class="font-semibold text-gray-900 mb-4 text-sm">{{ $workflow['title'] }}</h3>
                        
                        <!-- Steps -->
                        @if(isset($workflow['steps']))
                            <div class="bg-white rounded p-4">
                                <ol class="space-y-2">
                                    @foreach($workflow['steps'] as $step)
                                        <li class="flex items-start gap-3 text-sm">
                                            <span class="flex-shrink-0 w-6 h-6 rounded-full style="background-color: #0a1628;" text-white flex items-center justify-center text-xs font-semibold">
                                                {{ $loop->iteration }}
                                            </span>
                                            <span class="flex-1 text-gray-700 pt-0.5">{{ substr($step, strpos($step, ' ') + 1) }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif
                        
                        <!-- Relationships -->
                        @if(isset($workflow['relationships']))
                            <div class="bg-white rounded p-6 overflow-x-auto">
                                <div class="grid grid-cols-3 gap-6 min-w-max">
                                    <!-- Column 1: Core Tables -->
                                    <div class="space-y-4">
                                        <div class="border-2 border-slate-700 rounded-lg overflow-hidden">
                                            <div class="bg-slate-700 text-white px-3 py-2 font-semibold text-sm">USERS</div>
                                            <div class="bg-white p-3 space-y-1 text-xs font-mono">
                                                <div class="flex items-center gap-2"><span class="text-yellow-600">🔑</span><span>id</span></div>
                                                <div>name</div>
                                                <div>email</div>
                                                <div>role</div>
                                            </div>
                                        </div>
                                        
                                        <div class="border-2 border-slate-700 rounded-lg overflow-hidden">
                                            <div class="bg-slate-700 text-white px-3 py-2 font-semibold text-sm">PROJECTS</div>
                                            <div class="bg-white p-3 space-y-1 text-xs font-mono">
                                                <div class="flex items-center gap-2"><span class="text-yellow-600">🔑</span><span>id</span></div>
                                                <div>name</div>
                                                <div>code</div>
                                                <div>status</div>
                                            </div>
                                        </div>
                                        
                                        <div class="border-2 border-slate-700 rounded-lg overflow-hidden">
                                            <div class="bg-slate-700 text-white px-3 py-2 font-semibold text-sm">MODULES</div>
                                            <div class="bg-white p-3 space-y-1 text-xs font-mono">
                                                <div class="flex items-center gap-2"><span class="text-yellow-600">🔑</span><span>id</span></div>
                                                <div>key</div>
                                                <div>label</div>
                                                <div>icon</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Column 2: Transaction Tables -->
                                    <div class="space-y-4">
                                        <div class="border-2 border-blue-600 rounded-lg overflow-hidden">
                                            <div class="style="background-color: #0a1628;" text-white px-3 py-2 font-semibold text-sm">SPD</div>
                                            <div class="bg-white p-3 space-y-1 text-xs font-mono">
                                                <div class="flex items-center gap-2"><span class="text-yellow-600">🔑</span><span>id</span></div>
                                                <div class="flex items-center gap-2"><span class="text-blue-600">🔗</span><span>user_id</span></div>
                                                <div class="flex items-center gap-2"><span class="text-blue-600">🔗</span><span>project_id</span></div>
                                                <div>spd_number</div>
                                                <div>status</div>
                                            </div>
                                        </div>
                                        
                                        <div class="border-2 border-green-600 rounded-lg overflow-hidden">
                                            <div class="bg-green-600 text-white px-3 py-2 font-semibold text-sm">PURCHASES</div>
                                            <div class="bg-white p-3 space-y-1 text-xs font-mono">
                                                <div class="flex items-center gap-2"><span class="text-yellow-600">🔑</span><span>id</span></div>
                                                <div class="flex items-center gap-2"><span class="text-blue-600">🔗</span><span>user_id</span></div>
                                                <div class="flex items-center gap-2"><span class="text-blue-600">🔗</span><span>project_id</span></div>
                                                <div>purchase_number</div>
                                                <div>status</div>
                                            </div>
                                        </div>
                                        
                                        <div class="border-2 border-purple-600 rounded-lg overflow-hidden">
                                            <div class="bg-purple-600 text-white px-3 py-2 font-semibold text-sm">VENDOR_PAYMENTS</div>
                                            <div class="bg-white p-3 space-y-1 text-xs font-mono">
                                                <div class="flex items-center gap-2"><span class="text-yellow-600">🔑</span><span>id</span></div>
                                                <div class="flex items-center gap-2"><span class="text-blue-600">🔗</span><span>user_id</span></div>
                                                <div class="flex items-center gap-2"><span class="text-blue-600">🔗</span><span>vendor_id</span></div>
                                                <div class="flex items-center gap-2"><span class="text-blue-600">🔗</span><span>project_id</span></div>
                                                <div>payment_number</div>
                                                <div>status</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Column 3: Work Tables -->
                                    <div class="space-y-4">
                                        <div class="border-2 border-orange-600 rounded-lg overflow-hidden">
                                            <div class="bg-orange-600 text-white px-3 py-2 font-semibold text-sm">LEAVE_REQUESTS</div>
                                            <div class="bg-white p-3 space-y-1 text-xs font-mono">
                                                <div class="flex items-center gap-2"><span class="text-yellow-600">🔑</span><span>id</span></div>
                                                <div class="flex items-center gap-2"><span class="text-blue-600">🔗</span><span>user_id</span></div>
                                                <div class="flex items-center gap-2"><span class="text-blue-600">🔗</span><span>leave_type_id</span></div>
                                                <div>leave_number</div>
                                                <div>status</div>
                                            </div>
                                        </div>
                                        
                                        <div class="border-2 border-cyan-600 rounded-lg overflow-hidden">
                                            <div class="bg-cyan-600 text-white px-3 py-2 font-semibold text-sm">WORK_PLANS</div>
                                            <div class="bg-white p-3 space-y-1 text-xs font-mono">
                                                <div class="flex items-center gap-2"><span class="text-yellow-600">🔑</span><span>id</span></div>
                                                <div class="flex items-center gap-2"><span class="text-blue-600">🔗</span><span>user_id</span></div>
                                                <div class="flex items-center gap-2"><span class="text-blue-600">🔗</span><span>project_id</span></div>
                                                <div>plan_number</div>
                                                <div>status</div>
                                            </div>
                                        </div>
                                        
                                        <div class="border-2 border-teal-600 rounded-lg overflow-hidden">
                                            <div class="bg-teal-600 text-white px-3 py-2 font-semibold text-sm">WORK_REALIZATIONS</div>
                                            <div class="bg-white p-3 space-y-1 text-xs font-mono">
                                                <div class="flex items-center gap-2"><span class="text-yellow-600">🔑</span><span>id</span></div>
                                                <div class="flex items-center gap-2"><span class="text-blue-600">🔗</span><span>user_id</span></div>
                                                <div class="flex items-center gap-2"><span class="text-blue-600">🔗</span><span>project_id</span></div>
                                                <div>realization_number</div>
                                                <div>status</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Relationship Legend -->
                                <div class="mt-6 p-4 bg-slate-50 rounded-lg">
                                    <h4 class="font-semibold text-sm text-gray-900 mb-3">Database Relationships:</h4>
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        @foreach($workflow['relationships'] as $relation => $description)
                                            <div class="flex items-start gap-2">
                                                <span class="text-blue-600">→</span>
                                                <div>
                                                    <span class="font-mono font-semibold text-gray-900">{{ $relation }}</span>
                                                    <span class="text-gray-600">: {{ $description }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Technical Tab -->
    <div x-show="activeTab === 'technical'" class="space-y-4">
        <div class="bg-white border rounded p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Technical Stack</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                @foreach($techStack as $category => $section)
<div>
                        <h3 class="font-semibold text-gray-900 mb-3 text-sm">{{ $section['name'] }}</h3>
                        <div class="space-y-1.5">
                            @foreach($section['items'] as $item)
                                <div class="flex items-start justify-between gap-3 text-xs">
                                    <div class="flex-1">
                                        <span class="font-medium text-gray-900">{{ $item['name'] }}</span>
                                        <span class="text-gray-500">- {{ $item['description'] }}</span>
                                    </div>
                                    <span class="text-gray-400 whitespace-nowrap">{{ $item['version'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Database Tab -->
    <div x-show="activeTab === 'database'" class="space-y-4">
        <div class="bg-white border rounded p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Database Schema</h2>
            
            <div class="space-y-4 text-sm">
                <div class="overflow-x-auto">
                    <h3 class="font-semibold text-gray-900 mb-2">Core Tables</h3>
                    <table class="w-full text-xs border">
                        <thead class="style="background-color: #0a1628;"">
                            <tr>
                                <th class="border px-3 py-2 text-left text-white">Table Name</th>
                                <th class="border px-3 py-2 text-left text-white">Description</th>
                                <th class="border px-3 py-2 text-left text-white">Key Columns</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border px-3 py-2 font-mono">users</td>
                                <td class="border px-3 py-2">User accounts & employee data</td>
                                <td class="border px-3 py-2">id, name, email, role, employee_id, department_id</td>
                            </tr>
                            <tr>
                                <td class="border px-3 py-2 font-mono">modules</td>
                                <td class="border px-3 py-2">Module definitions</td>
                                <td class="border px-3 py-2">id, key, label, icon, assignable_to_user, admin_only</td>
                            </tr>
                            <tr>
                                <td class="border px-3 py-2 font-mono">module_user</td>
                                <td class="border px-3 py-2">User-module assignments (pivot)</td>
                                <td class="border px-3 py-2">user_id, module_id</td>
                            </tr>
                            <tr>
                                <td class="border px-3 py-2 font-mono">projects</td>
                                <td class="border px-3 py-2">Company projects</td>
                                <td class="border px-3 py-2">id, name, code, status, budget</td>
                            </tr>
                            <tr>
                                <td class="border px-3 py-2 font-mono">vendors</td>
                                <td class="border px-3 py-2">Vendor/supplier data</td>
                                <td class="border px-3 py-2">id, name, company, contact_person</td>
                            </tr>
                            <tr>
                                <td class="border px-3 py-2 font-mono">spd</td>
                                <td class="border px-3 py-2">Surat Perjalanan Dinas</td>
                                <td class="border px-3 py-2">id, user_id, project_id, spd_number, total_cost, status</td>
                            </tr>
                            <tr>
                                <td class="border px-3 py-2 font-mono">purchases</td>
                                <td class="border px-3 py-2">Purchase requests</td>
                                <td class="border px-3 py-2">id, user_id, project_id, purchase_number, total_price, status</td>
                            </tr>
                            <tr>
                                <td class="border px-3 py-2 font-mono">vendor_payments</td>
                                <td class="border px-3 py-2">Vendor payments</td>
                                <td class="border px-3 py-2">id, user_id, vendor_id, project_id, payment_number, amount, status</td>
                            </tr>
                            <tr>
                                <td class="border px-3 py-2 font-mono">work_plans</td>
                                <td class="border px-3 py-2">Daily work plans</td>
                                <td class="border px-3 py-2">id, user_id, project_id, plan_number, plan_date, status</td>
                            </tr>
                            <tr>
                                <td class="border px-3 py-2 font-mono">work_realizations</td>
                                <td class="border px-3 py-2">Work realizations</td>
                                <td class="border px-3 py-2">id, user_id, project_id, realization_number, realization_date, status</td>
                            </tr>
                            <tr>
                                <td class="border px-3 py-2 font-mono">leave_requests</td>
                                <td class="border px-3 py-2">Employee leave requests</td>
                                <td class="border px-3 py-2">id, user_id, leave_type_id, leave_number, start_date, total_days, status</td>
                            </tr>
                            <tr>
                                <td class="border px-3 py-2 font-mono">leave_types</td>
                                <td class="border px-3 py-2">Leave type definitions</td>
                                <td class="border px-3 py-2">id, name, max_days, requires_approval</td>
                            </tr>
                            <tr>
                                <td class="border px-3 py-2 font-mono">changelogs</td>
                                <td class="border px-3 py-2">System changelog & version history</td>
                                <td class="border px-3 py-2">id, version, release_date, title, changes, category, is_major</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Database Statistics</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="p-3 bg-slate-50 rounded-lg text-center">
                            <div class="text-xl font-bold text-slate-900">{{ $dbStats['spd'] }}</div>
                            <div class="text-xs text-slate-600 mt-1">SPD</div>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-lg text-center">
                            <div class="text-xl font-bold text-slate-900">{{ $dbStats['purchases'] }}</div>
                            <div class="text-xs text-slate-600 mt-1">Purchases</div>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-lg text-center">
                            <div class="text-xl font-bold text-slate-900">{{ $dbStats['vendor_payments'] }}</div>
                            <div class="text-xs text-slate-600 mt-1">Vendor Payments</div>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-lg text-center">
                            <div class="text-xl font-bold text-slate-900">{{ $dbStats['leave_requests'] }}</div>
                            <div class="text-xs text-slate-600 mt-1">Leave Requests</div>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-lg text-center">
                            <div class="text-xl font-bold text-slate-900">{{ $dbStats['work_plans'] }}</div>
                            <div class="text-xs text-slate-600 mt-1">Work Plans</div>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-lg text-center">
                            <div class="text-xl font-bold text-slate-900">{{ $dbStats['work_realizations'] }}</div>
                            <div class="text-xs text-slate-600 mt-1">Work Realizations</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Common Status Values</h3>
                    <ul class="space-y-1 text-gray-600">
                        <li>• <strong>pending:</strong> Menunggu approval</li>
                        <li>• <strong>approved:</strong> Sudah disetujui</li>
                        <li>• <strong>rejected:</strong> Ditolak</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Changelog Tab -->
    <div x-show="activeTab === 'changelog'" class="space-y-4">
        <div class="bg-white border border-slate-200 rounded">
            <div class="px-5 py-3 border-b border-slate-200 style="background-color: #0a1628;"">
                <h2 class="text-sm font-semibold text-white">Changelog & Updates</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="py-2 px-4 text-left font-medium text-slate-700">Version</th>
                            <th class="py-2 px-4 text-left font-medium text-slate-700">Tanggal</th>
                            <th class="py-2 px-4 text-left font-medium text-slate-700">Perubahan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($changelogs as $changelog)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2 px-4">
                                <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-xs font-medium">
                                    {{ $changelog->version }}
                                    @if($changelog->is_major)
                                    <span class="ml-1 text-red-600">★</span>
                                    @endif
                                </span>
                            </td>
                            <td class="py-2 px-4 text-slate-600">{{ $changelog->release_date->format('d M Y') }}</td>
                            <td class="py-2 px-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-medium text-slate-900">{{ $changelog->title }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-xs font-medium
                                        @if($changelog->category === 'feature') bg-blue-100 text-blue-700
                                        @elseif($changelog->category === 'bugfix') bg-red-100 text-red-700
                                        @elseif($changelog->category === 'improvement') bg-green-100 text-green-700
                                        @elseif($changelog->category === 'security') bg-purple-100 text-purple-700
                                        @else bg-slate-100 text-slate-700
                                        @endif">
                                        {{ ucfirst($changelog->category) }}
                                    </span>
                                </div>
                                <div class="space-y-0.5 text-slate-600">
                                    @foreach($changelog->changes as $change)
                                    <div>• {{ $change }}</div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-8 px-4 text-center text-slate-500">
                                Belum ada changelog. Jalankan: <code class="bg-slate-100 px-2 py-1 rounded text-xs">php artisan db:seed --class=ChangelogSeeder</code>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
