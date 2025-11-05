@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('page-subtitle', 'Kelola Pengguna & Module')

@section('content')
<div class="py-8" x-data="userManagement()">
    <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
        <h1 class="text-xl font-bold text-gray-900">Manajemen User</h1>
        <button @click="openCreateModal()" class="text-white px-4 py-2 rounded text-sm font-medium transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
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

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="border rounded bg-white px-4 py-3 hover:shadow-md transition-shadow">
            <div class="text-lg font-semibold text-blue-600">{{ $totalUsers }}</div>
            <div class="text-xs text-gray-500">Total Users</div>
        </div>
        <div class="border rounded bg-white px-4 py-3 hover:shadow-md transition-shadow">
            <div class="text-lg font-semibold text-green-600">{{ $activeUsers }}</div>
            <div class="text-xs text-gray-500">Active Users</div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border rounded">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="text-base font-semibold text-gray-800">Daftar User</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="py-2 px-3 text-left font-medium text-gray-700">ID Pengguna</th>
                        <th class="py-2 px-3 text-left font-medium text-gray-700">Nama</th>
                        <th class="py-2 px-3 text-left font-medium text-gray-700">Email</th>
                        <th class="py-2 px-3 text-left font-medium text-gray-700">Role</th>
                        <th class="py-2 px-3 text-left font-medium text-gray-700">Department</th>
                        <th class="py-2 px-3 text-left font-medium text-gray-700">Modules</th>
                        <th class="py-2 px-3 text-left font-medium text-gray-700">Status</th>
                        <th class="py-2 px-3 text-left font-medium text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-3 font-mono text-xs">{{ $user->employee_id ?? '-' }}</td>
                            <td class="py-2 px-3">
                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $user->position ?? '-' }}</div>
                            </td>
                            <td class="py-2 px-3 text-gray-600">{{ $user->email }}</td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $user->hasRole('admin') ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($user->getRoleNames()->first() ?? 'user') }}
                                </span>
                            </td>
                            <td class="py-2 px-3 text-gray-600">{{ $user->department ?? '-' }}</td>
                            <td class="py-2 px-3">
                                @if($user->hasRole('admin'))
                                    <span class="text-xs text-gray-500">Semua Module</span>
                                @else
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->modules as $module)
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs">
                                                {{ $module->icon ?? '•' }} {{ $module->label }}
                                            </span>
                                        @endforeach
                                        @if($user->modules->isEmpty())
                                            <span class="text-xs text-gray-400">Default only</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
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
                            <td colspan="8" class="py-12 text-center">
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
             x-show="showModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showModal = false"></div>

        <!-- Modal -->
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:max-w-3xl sm:w-full">
                
                <!-- Modal Header -->
                <div class="px-6 py-5 rounded-t-lg" style="background-color: #0a1628;">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <!-- Icon -->
                            <div class="flex-shrink-0 w-10 h-10 bg-white bg-opacity-10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <!-- Title -->
                            <h3 class="text-xl font-bold text-white" x-text="modalTitle"></h3>
                        </div>
                        <button type="button" 
                                @click="showModal = false"
                                class="text-gray-300 hover:text-white hover:bg-white hover:bg-opacity-10 rounded-lg p-1 transition-colors">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Content -->
                <form @submit.prevent="submitForm">
                    <div class="bg-white px-6 py-4 max-h-[calc(100vh-250px)] overflow-y-auto">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Name -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                                <input type="text" x-model="formData.name" required
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" x-model="formData.email" required
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            </div>

                            <!-- Employee ID -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ID Pengguna</label>
                                <input type="text" x-model="formData.employee_id" readonly
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-gray-50">
                            </div>

                            <!-- Password -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Password <span class="text-red-500" x-show="!editMode">*</span>
                                    <span class="text-xs text-gray-500" x-show="editMode">(Kosongkan jika tidak diubah)</span>
                                </label>
                                <input type="password" x-model="formData.password" 
                                       :required="!editMode"
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            </div>

                            <!-- Password Confirmation -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                                <input type="password" x-model="formData.password_confirmation"
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                                <select x-model="formData.role" @change="toggleModuleSection()" required
                                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                    <option value="">Pilih Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                            </div>

                            <!-- Department -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                <input type="text" x-model="formData.department"
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                                       placeholder="Contoh: IT, Operations, HR">
                            </div>

                            <!-- Position -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                                <input type="text" x-model="formData.position"
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                                <input type="text" x-model="formData.phone"
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            </div>

                            <!-- Join Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bergabung</label>
                                <input type="date" x-model="formData.join_date"
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            </div>

                            <!-- Annual Leave Quota -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kuota Cuti Tahunan</label>
                                <input type="number" x-model="formData.annual_leave_quota" min="0" max="30"
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            </div>

                            <!-- Address -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                                <textarea x-model="formData.address" rows="2"
                                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm"></textarea>
                            </div>

                            <!-- Active Status -->
                            <div class="col-span-2">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" x-model="formData.is_active" value="1"
                                           class="rounded border-gray-300">
                                    <span class="text-sm font-medium text-gray-700">Status Aktif</span>
                                </label>
                            </div>

                            <!-- Module Assignment Section (only for user role) -->
                            <div x-show="formData.role === 'user'" 
                                 x-cloak
                                 class="col-span-2 border-t pt-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        🔐 Akses Module <span class="text-xs text-gray-500 font-normal">(Tambahan)</span>
                                    </label>
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
                                        <p class="text-xs text-blue-800">
                                            <strong>Default:</strong> <strong>Rencana Kerja</strong> & <strong>Realisasi Kerja</strong> secara default sudah tercentang untuk user baru.
                                            <br>Centang/uncentang modul sesuai kebutuhan akses user.
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
                                        @php
                                            $defaultModules = ['work-plan', 'work-realization'];
                                        @endphp
                                        @foreach($assignableModules as $module)
                                            @php
                                                $isDefault = in_array($module->key, $defaultModules);
                                            @endphp
                                            <label class="inline-flex items-center gap-2 p-2 rounded border border-gray-200 {{ $isDefault ? 'bg-blue-50' : 'hover:bg-gray-50 cursor-pointer' }}" 
                                                   :class="{{ $isDefault && $isDefault ? "'cursor-not-allowed opacity-75'" : "'cursor-pointer'" }}">
                                                <input type="checkbox" 
                                                       value="{{ $module->key }}"
                                                       x-model="formData.modules"
                                                       @if($isDefault)
                                                       :disabled="true"
                                                       checked
                                                       @endif
                                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 {{ $isDefault ? 'cursor-not-allowed' : '' }}">
                                                <span class="flex items-center gap-1">
                                                    <span class="text-base">{{ $module->icon ?? '•' }}</span>
                                                    <span class="font-medium">
                                                        {{ $module->label }}
                                                        @if($isDefault)
                                                            <span class="text-xs text-blue-600">(Default)</span>
                                                        @endif
                                                    </span>
                                                </span>
                                            </label>
                                        @endforeach
                                        @if($assignableModules->isEmpty())
                                            <p class="text-xs text-gray-500 col-span-3 p-2">Tidak ada module yang bisa di-assign</p>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-3 p-2 bg-gray-50 rounded">
                                        💡 <strong>Tips:</strong> Fitur baru yang sudah di-test di admin bisa langsung di-assign ke user dengan centang modul di atas.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Error Messages -->
                        <div x-show="error" class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded text-sm" x-text="error"></div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t flex justify-end gap-2">
                        <button type="button" @click="showModal = false"
                                class="border border-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-100 text-sm font-medium">
                            Batal
                        </button>
                        <button type="submit" :disabled="loading"
                                class="text-white px-4 py-2 rounded text-sm font-medium transition-colors disabled:opacity-50" style="background-color: #0a1628;" onmouseover="if(!this.disabled) this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
                            <span x-show="!loading">Simpan</span>
                            <span x-show="loading">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function userManagement() {
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
            annual_leave_quota: 12,
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
                annual_leave_quota: 12,
                address: '',
                is_active: true,
                modules: []
            };
            this.error = null;
        },

        toggleModuleSection() {
            // Alpine.js x-show akan handle visibility otomatis
            if (this.formData.role !== 'user') {
                this.formData.modules = [];
            }
        },

        async openCreateModal() {
            this.resetForm();
            this.editMode = false;
            this.modalTitle = 'Tambah User';
            
            // Set default modules untuk user baru (work-plan & work-realization)
            this.formData.modules = ['work-plan', 'work-realization'];
            
            // Fetch auto-generated employee ID
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
        },

        async openEditModal(userId) {
            this.resetForm();
            this.editMode = true;
            this.editId = userId;
            this.modalTitle = 'Edit User';
            
            // Fetch user data
            try {
                const response = await fetch(`/admin/users/${userId}/edit`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                
                // Fill form with user data
                const userModules = data.user.modules?.map(m => m.key) || [];
                // Ensure default modules are always included
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
                    annual_leave_quota: data.user.annual_leave_quota || 12,
                    address: data.user.address || '',
                    is_active: data.user.is_active,
                    modules: allModules
                };
                
                // Toggle module section based on role
                this.toggleModuleSection();
            } catch (error) {
                this.error = 'Gagal memuat data user: ' + error.message;
            }
            
            this.showModal = true;
        },

        async submitForm() {
            this.loading = true;
            this.error = null;

            const url = this.editMode 
                ? `/admin/users/${this.editId}` 
                : '{{ route("admin.users.store") }}';
            
            const method = this.editMode ? 'PUT' : 'POST';

            try {
                const formDataToSend = { ...this.formData };
                
                // Remove password_confirmation before sending
                delete formDataToSend.password_confirmation;
                
                // If password is empty in edit mode, don't send it
                if (this.editMode && !formDataToSend.password) {
                    delete formDataToSend.password;
                }
                
                // Ensure default modules are always included
                const defaultModules = ['work-plan', 'work-realization'];
                if (!formDataToSend.modules) {
                    formDataToSend.modules = [];
                }
                formDataToSend.modules = [...new Set([...defaultModules, ...formDataToSend.modules])];

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formDataToSend)
                });

                const data = await response.json();

                if (response.ok && !data.errors) {
                    this.showModal = false;
                    window.location.reload();
                } else {
                    // Handle validation errors
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
                const response = await fetch(`/admin/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
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
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection

