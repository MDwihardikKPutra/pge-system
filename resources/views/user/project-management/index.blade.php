@extends('layouts.app')

@section('title', 'Project Management')
@section('page-title', 'Project Management')
@section('page-subtitle', 'Monitoring & Tracking Semua Project')

@section('content')
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

<div class="py-8" x-data="{ showCreateModal: false }">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Project Management</h1>
            <p class="text-sm text-slate-600 mt-1">Kelola dan pantau semua project</p>
        </div>
        @if($isAdmin)
        <button @click="showCreateModal = true" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Project
        </button>
        @endif
    </div>

    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <h2 class="text-base font-semibold text-white">Daftar Project</h2>
        </div>
        @if($projects->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Project</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Detail Project</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">User</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($projects as $project)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900">{{ $project->name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    <span class="font-mono">{{ $project->code }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                    @if($project->client)
                                        <div class="text-xs text-slate-600">
                                            <span class="font-medium">Client:</span> {{ $project->client }}
                                        </div>
                                    @endif
                                    @if($project->start_date || $project->end_date)
                                        <div class="text-xs text-slate-600">
                                            <span class="font-medium">Periode:</span>
                                            @if($project->start_date)
                                                {{ $project->start_date->format('d M Y') }}
                                            @endif
                                            @if($project->start_date && $project->end_date) - @endif
                                            @if($project->end_date)
                                                {{ $project->end_date->format('d M Y') }}
                                            @endif
                                        </div>
                                    @endif
                                    @if($project->description)
                                        <div class="text-xs text-slate-500 mt-1">
                                            {{ \Illuminate\Support\Str::limit($project->description, 100) }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($project->managers->count() > 0)
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($project->managers as $manager)
                                            @php
                                                $accessType = $manager->pivot->access_type ?? 'pm';
                                                $badgeColors = [
                                                    'pm' => 'bg-blue-100 text-blue-700',
                                                    'finance' => 'bg-green-100 text-green-700',
                                                    'full' => 'bg-purple-100 text-purple-700'
                                                ];
                                                $badgeColor = $badgeColors[$accessType] ?? 'bg-blue-100 text-blue-700';
                                                $accessTypeLabels = [
                                                    'pm' => 'PM',
                                                    'finance' => 'Finance',
                                                    'full' => 'Full'
                                                ];
                                                $accessTypeLabel = $accessTypeLabels[$accessType] ?? 'PM';
                                            @endphp
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium {{ $badgeColor }}" title="{{ $manager->name }} - {{ $accessTypeLabel }}">
                                                <span class="font-semibold">{{ $manager->name }}</span>
                                                <span class="text-xs opacity-75">({{ $accessTypeLabel }})</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $project->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $project->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $routePrefix = $isAdmin ? 'admin' : 'user';
                                @endphp
                                <a href="{{ route($routePrefix . '.project-management.show', $project->id) }}" 
                                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white rounded transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($projects->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $projects->links() }}
                </div>
            @endif
        @else
            @if($isAdmin)
            <div class="py-12 text-center text-slate-500">
                <svg class="w-16 h-16 mx-auto text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
                <p class="text-sm font-medium">Belum ada project</p>
                <p class="text-xs text-slate-400 mt-1">Project akan muncul di sini setelah ditambahkan</p>
            </div>
            @else
            <div class="py-12">
                <div class="max-w-md mx-auto text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background-color: #dbeafe;">
                        <svg class="w-8 h-8" style="color: #0a1628;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="mt-2 text-base font-semibold" style="color: #0a1628;">Belum Ada Project yang Dikelola</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Anda belum ditugaskan sebagai Project Manager untuk project apapun.
                    </p>
                    <div class="mt-4 p-4 rounded-lg bg-blue-50 border border-blue-200 text-left">
                        <p class="text-sm text-slate-700">
                            <strong class="font-semibold">Untuk melihat project:</strong><br>
                            Admin perlu menugaskan Anda sebagai Project Manager di project tertentu terlebih dahulu melalui halaman detail project.
                        </p>
                    </div>
                </div>
            </div>
            @endif
        @endif
    </div>

    @if($isAdmin)
    <!-- Create Project Modal -->
    <div x-show="showCreateModal" x-cloak style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showCreateModal = false"></div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <form action="{{ route('admin.project-management.store') }}" method="POST">
                @csrf
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Tambah Project Baru</h3>
                        <button type="button" @click="showCreateModal = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Project <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Contoh: PLTP Kamojang Development">
                                @error('name')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Project <span class="text-red-500">*</span></label>
                                <input type="text" name="code" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-mono" placeholder="PRJ-2024-001">
                                @error('code')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                            <input type="text" name="client" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Contoh: PT PLN (Persero)">
                            @error('client')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Jelaskan detail project..."></textarea>
                            @error('description')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                @error('start_date')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                                <input type="date" name="end_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                @error('end_date')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Project Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
                        Simpan Project
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
    @endif
</div>
@endsection
