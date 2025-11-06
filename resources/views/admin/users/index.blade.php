@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('page-subtitle', 'Kelola Pengguna & Module')

@push('head-scripts')
<script>
// Define Alpine.js data function immediately - must be available before Alpine processes x-data
// Using IIFE to ensure it executes immediately
(function() {
    'use strict';
    window.userManagementData = function() {
    return {
        showModal: false,
        editMode: false,
        modalTitle: 'Tambah User',
        loading: false,
        error: null,
        editId: null,
        assignableModules: @json($assignableModules->pluck('key')),
        formData: {
            name: '',
            email: '',
            employee_id: '',
            password: '',
            password_confirmation: '',
            role: '',
            department: '',
            position: '',
            phone: '',
            join_date: '',
            address: '',
            is_active: true,
            modules: []
        },

        resetForm() {
            this.formData = {
                name: '',
                email: '',
                employee_id: '',
                password: '',
                password_confirmation: '',
                role: '',
                department: '',
                position: '',
                phone: '',
                join_date: '',
                address: '',
                is_active: true,
                modules: []
            };
            this.error = null;
        },

        toggleModuleSection() {
            if (this.formData.role !== 'user') {
                this.formData.modules = [];
            }
        },

        async openCreateModal() {
            console.log('openCreateModal called');
            this.resetForm();
            this.editMode = false;
            this.modalTitle = 'Tambah User';
            this.formData.modules = ['work-plan', 'work-realization'];
            
            try {
                const response = await fetch('{{ route("admin.users.create") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                if (data.employee_id) {
                    this.formData.employee_id = data.employee_id;
                }
            } catch (error) {
                console.error('Error fetching employee ID:', error);
            }
            
            this.showModal = true;
            console.log('showModal set to:', this.showModal);
        },

        async openEditModal(userId) {
            console.log('openEditModal called with userId:', userId);
            this.resetForm();
            this.editMode = true;
            this.editId = userId;
            this.modalTitle = 'Edit User';
            this.error = null;
            
            try {
                const response = await fetch(`/admin/users/${userId}/edit`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (!data.user) {
                    throw new Error('Data user tidak ditemukan');
                }
                
                const userModules = data.user.modules?.map(m => m.key) || [];
                const defaultModules = ['work-plan', 'work-realization'];
                const allModules = [...new Set([...defaultModules, ...userModules])];
                
                this.formData = {
                    name: data.user.name,
                    email: data.user.email,
                    employee_id: data.user.employee_id,
                    password: '',
                    password_confirmation: '',
                    role: data.user.role || data.user.roles?.[0]?.name || 'user',
                    department: data.user.department || '',
                    position: data.user.position || '',
                    phone: data.user.phone || '',
                    join_date: data.user.join_date || '',
                    address: data.user.address || '',
                    is_active: data.user.is_active,
                    modules: allModules
                };
                
                this.toggleModuleSection();
                this.showModal = true;
                console.log('showModal set to:', this.showModal);
            } catch (error) {
                console.error('Error loading user data:', error);
                this.error = 'Gagal memuat data user: ' + error.message;
                alert('Gagal memuat data user: ' + error.message);
            }
        },

        async submitForm() {
            this.loading = true;
            this.error = null;

            const url = this.editMode 
                ? `/admin/users/${this.editId}` 
                : '{{ route("admin.users.store") }}';

            try {
                const formDataToSend = { ...this.formData };
                delete formDataToSend.password_confirmation;
                
                if (this.editMode && !formDataToSend.password) {
                    delete formDataToSend.password;
                }
                
                const defaultModules = ['work-plan', 'work-realization'];
                if (!formDataToSend.modules) {
                    formDataToSend.modules = [];
                }
                formDataToSend.modules = [...new Set([...defaultModules, ...formDataToSend.modules])];

                const formData = new FormData();
                Object.keys(formDataToSend).forEach(key => {
                    if (formDataToSend[key] !== null && formDataToSend[key] !== undefined) {
                        if (Array.isArray(formDataToSend[key])) {
                            formDataToSend[key].forEach((item) => {
                                formData.append(`${key}[]`, item);
                            });
                        } else if (typeof formDataToSend[key] === 'boolean') {
                            formData.append(key, formDataToSend[key] ? '1' : '0');
                        } else {
                            formData.append(key, formDataToSend[key]);
                        }
                    }
                });

                if (this.editMode) {
                    formData.append('_method', 'PUT');
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showModal = false;
                    window.location.reload();
                } else {
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join(', ');
                        this.error = errorMessages;
                    } else {
                        this.error = data.message || 'Terjadi kesalahan saat menyimpan data';
                    }
                }
            } catch (error) {
                this.error = 'Terjadi kesalahan: ' + error.message;
            } finally {
                this.loading = false;
            }
        },

        async deleteUser(userId) {
            if (!confirm('Yakin ingin menghapus user ini?')) return;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                
                const response = await fetch(`/admin/users/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal menghapus user');
                }
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
            }
        }
    };
    };
    
    // Ensure function is available immediately
    console.log('userManagementData defined:', typeof window.userManagementData === 'function');
})();
</script>
@endpush

@section('content')
<div class="py-8" x-data="userManagementData()">
    <div class="flex flex-wrap items-center justify-between mb-5 gap-4">
        <h1 class="text-lg font-semibold text-gray-900" style="letter-spacing: -0.02em;">Manajemen User</h1>
        <button @click="openCreateModal()" class="asana-btn asana-btn-primary">
            + Tambah User
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Table with Integrated Header -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-white">Manajemen User</h2>
                    <p class="text-xs text-gray-300">Kelola Pengguna & Module</p>
                </div>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 200px);">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">ID Pengguna</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Position</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Department</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Phone</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Modules</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-900">{{ $user->employee_id ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900 text-xs">{{ $user->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-900">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-xs text-slate-900">{{ $user->position ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs text-slate-900">{{ $user->department ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs text-slate-900">{{ $user->phone ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="badge-minimal {{ $user->hasRole('admin') ? 'bg-purple-50 text-purple-700 border-purple-200' : 'badge-neutral' }}">
                                    {{ ucfirst($user->getRoleNames()->first() ?? 'user') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($user->hasRole('admin'))
                                    <span class="text-xs text-gray-500">Semua Module</span>
                                @else
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($user->modules as $module)
                                            @php
                                                $moduleColors = [
                                                    'work-plan' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    'work-realization' => 'bg-green-50 text-green-700 border-green-200',
                                                    'spd' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                    'purchase' => 'bg-orange-50 text-orange-700 border-orange-200',
                                                    'vendor-payment' => 'bg-pink-50 text-pink-700 border-pink-200',
                                                    'leave' => 'bg-teal-50 text-teal-700 border-teal-200',
                                                    'project-management' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                    'project-monitoring' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                    'payment-approval' => 'bg-amber-50 text-amber-700 border-amber-200'
                                                ];
                                                $badgeColor = $moduleColors[$module->key] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                                            @endphp
                                            <span class="badge-minimal {{ $badgeColor }}">
                                                @php
                                                    $iconType = \App\Helpers\IconHelper::getModuleIconType($module->key);
                                                @endphp
                                                <x-icon type="{{ $iconType }}" class="w-3 h-3 inline mr-1" />
                                                {{ $module->label }}
                                            </span>
                                        @endforeach
                                        @if($user->modules->isEmpty())
                                            <span class="text-xs text-gray-400">Default only</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="py-2 px-3">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="py-2 px-3">
                                <div class="flex gap-2">
                                    <button @click="openEditModal({{ $user->id }})" 
                                       class="border border-gray-300 text-gray-700 px-3 py-1 rounded hover:bg-gray-100 text-xs font-medium">
                                        Edit
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button @click="deleteUser({{ $user->id }})" 
                                                class="border border-red-300 text-red-700 px-3 py-1 rounded hover:bg-red-100 text-xs font-medium">
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center">
                                <div class="flex flex-col items-center text-gray-400">
                                    <div class="text-xs mb-1">Belum ada data user</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Create/Edit User -->
    <div x-show="showModal" 
         x-cloak
         @keydown.escape.window="showModal = false"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             @click="showModal = false"></div>

        <!-- Modal -->
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:max-w-6xl sm:w-full"
                 @click.stop>
                    
                    <!-- Modal Header -->
                    <div class="px-6 py-5 rounded-t-lg" style="background-color: #0a1628;">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-white bg-opacity-10 rounded-lg flex items-center justify-center">
                                    <x-icon type="user" class="w-6 h-6 text-white" />
                                </div>
                                <h3 class="text-xl font-bold text-white" x-text="modalTitle"></h3>
                            </div>
                            <button type="button" 
                                    @click="showModal = false"
                                    class="text-gray-300 hover:text-white hover:bg-white hover:bg-opacity-10 rounded-lg p-1 transition-colors">
                                <x-icon type="x-mark" class="h-6 w-6" />
                            </button>
                        </div>
                    </div>

                    <!-- Modal Content -->
                    <form @submit.prevent="submitForm">
                        <div class="bg-white px-6 py-4">
                            <div class="grid grid-cols-3 gap-x-4 gap-y-2.5">
                                <!-- Name -->
                                <div class="col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-0.5">Nama <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="formData.name" required
                                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Email -->
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-0.5">Email <span class="text-red-500">*</span></label>
                                    <input type="email" x-model="formData.email" required
                                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Employee ID -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-0.5">ID Pengguna</label>
                                    <input type="text" x-model="formData.employee_id" readonly
                                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm bg-gray-50">
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-0.5">
                                        Password <span class="text-red-500" x-show="!editMode">*</span>
                                        <span class="text-[10px] text-gray-500 block" x-show="editMode">(Kosongkan jika tidak diubah)</span>
                                    </label>
                                    <input type="password" x-model="formData.password" 
                                           :required="!editMode"
                                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Password Confirmation -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-0.5">Konfirmasi Password</label>
                                    <input type="password" x-model="formData.password_confirmation"
                                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Role -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-0.5">Role <span class="text-red-500">*</span></label>
                                    <select x-model="formData.role" @change="toggleModuleSection()" required
                                            class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Pilih Role</option>
                                        <option value="admin">Admin</option>
                                        <option value="user">User</option>
                                    </select>
                                </div>

                                <!-- Department -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-0.5">Department</label>
                                    <input type="text" x-model="formData.department"
                                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="IT, Operations, HR">
                                </div>

                                <!-- Position -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-0.5">Posisi</label>
                                    <input type="text" x-model="formData.position"
                                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-0.5">Telepon</label>
                                    <input type="text" x-model="formData.phone"
                                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Join Date -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-0.5">Tanggal Bergabung</label>
                                    <input type="date" x-model="formData.join_date"
                                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Address -->
                                <div class="col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-0.5">Alamat</label>
                                    <textarea x-model="formData.address" rows="1"
                                              class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>

                                <!-- Active Status -->
                                <div class="col-span-3">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" x-model="formData.is_active" value="1"
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                                        <span class="text-xs font-medium text-gray-700">Status Aktif</span>
                                    </label>
                                </div>

                                <!-- Module Assignment Section (only for user role) -->
                                <div x-show="formData.role === 'user'" 
                                     x-cloak
                                     class="col-span-3 border-t pt-3 mt-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                                            Akses Module <span class="text-[10px] text-gray-500 font-normal">(Tambahan)</span>
                                        </label>
                                        <div class="bg-blue-50 border border-blue-200 rounded p-2 mb-2">
                                            <p class="text-[10px] text-blue-800 leading-tight">
                                                <strong>Default:</strong> <strong>Rencana Kerja</strong> & <strong>Realisasi Kerja</strong> sudah tercentang. Centang/uncentang modul sesuai kebutuhan.
                                            </p>
                                        </div>
                                        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-1.5 text-xs">
                                            @php
                                                $defaultModules = ['work-plan', 'work-realization'];
                                            @endphp
                                            @foreach($assignableModules as $module)
                                                @php
                                                    $isDefault = in_array($module->key, $defaultModules);
                                                    $iconType = \App\Helpers\IconHelper::getModuleIconType($module->key);
                                                @endphp
                                                <label class="inline-flex items-center gap-1.5 p-1.5 rounded border border-gray-200 {{ $isDefault ? 'bg-blue-50' : 'hover:bg-gray-50 cursor-pointer' }}" 
                                                       :class="{{ $isDefault ? "'cursor-not-allowed opacity-75'" : "'cursor-pointer'" }}">
                                                    <input type="checkbox" 
                                                           value="{{ $module->key }}"
                                                           x-model="formData.modules"
                                                           @if($isDefault)
                                                           :disabled="true"
                                                           checked
                                                           @endif
                                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-3.5 h-3.5 {{ $isDefault ? 'cursor-not-allowed' : '' }}">
                                                    <span class="flex items-center gap-1">
                                                        <x-icon type="{{ $iconType }}" class="w-3 h-3" />
                                                        <span class="font-medium text-xs">
                                                            {{ $module->label }}
                                                            @if($isDefault)
                                                                <span class="text-[10px] text-blue-600">(Default)</span>
                                                            @endif
                                                        </span>
                                                    </span>
                                                </label>
                                            @endforeach
                                            @if($assignableModules->isEmpty())
                                                <p class="text-[10px] text-gray-500 col-span-3 p-1.5">Tidak ada module yang bisa di-assign</p>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-gray-500 mt-2 p-1.5 bg-gray-50 rounded flex items-start gap-1.5">
                                            <x-icon type="information-circle" class="w-3 h-3 text-blue-600 flex-shrink-0 mt-0.5" />
                                            <span><strong>Tips:</strong> Fitur baru yang sudah di-test di admin bisa langsung di-assign ke user dengan centang modul di atas.</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Error Messages -->
                            <div x-show="error" 
                                 x-cloak
                                 class="mt-3 bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded text-xs" 
                                 x-text="error"></div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end gap-2">
                            <button type="button" @click="showModal = false"
                                    class="border border-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-100 text-sm font-medium">
                                Batal
                            </button>
                            <button type="submit" :disabled="loading"
                                    class="text-white px-4 py-2 rounded text-sm font-medium transition-colors disabled:opacity-50" 
                                    style="background-color: #0a1628;" 
                                    onmouseover="if(!this.disabled) this.style.backgroundColor='#1e293b'" 
                                    onmouseout="this.style.backgroundColor='#0a1628'">
                                <span x-show="!loading">Simpan</span>
                                <span x-show="loading" x-cloak>Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
