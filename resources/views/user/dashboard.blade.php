@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas Anda')

@section('content')
<div class="py-8">
    <!-- Welcome Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            @php
                $hour = now()->hour;
                $greeting = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
            @endphp
            <h1 class="text-xl font-semibold mb-0.5" style="color: #0a1628;">{{ $greeting }}, {{ auth()->user()->name }} 👋</h1>
            <p class="text-xs" style="color: #0a1628;">Berikut aktivitas terbaru Anda</p>
        </div>
        @if(isset($activeModules) && $activeModules->where('key', 'work-plan')->count() > 0)
        <a href="{{ route('user.work-plans.index') }}" 
           class="inline-flex items-center gap-1.5 px-3 py-2 text-white text-sm font-medium rounded-lg shadow-sm transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Rencana Kerja
        </a>
        @endif
    </div>

    <!-- Recent Activity Section -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <h3 class="text-base font-semibold text-white">Recent Activity</h3>
            <p class="text-xs text-gray-300 mt-1">Semua aktivitas dari modul yang Anda akses</p>
        </div>
        <div class="p-6" x-data="{ showPreviewModal: false, selectedActivity: null }">
            <div class="space-y-3">
                @forelse($recentActivities ?? [] as $activity)
                <div @click="selectedActivity = @js($activity); showPreviewModal = true" 
                     class="block p-4 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors bg-white cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center text-lg" style="background-color: #0a1628;">
                            {{ $activity['icon'] }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-semibold" style="color: #0a1628;">{{ $activity['title'] }}</span>
                            </div>
                            <p class="text-sm truncate" style="color: #0a1628;">{{ $activity['description'] }}</p>
                            @if(isset($activity['project']) && $activity['project'] !== '-')
                            <p class="text-xs text-slate-500 mt-1 truncate">📁 {{ $activity['project'] }} @if(isset($activity['project_code'])) ({{ $activity['project_code'] }}) @endif</p>
                            @endif
                        </div>
                        <div class="flex-shrink-0 flex items-center gap-3">
                            @if($activity['type'] === 'work-realization' && isset($activity['extra']) && is_numeric($activity['extra']))
                            <div class="flex items-center gap-2">
                                <div class="w-20 rounded-full h-2 overflow-hidden bg-slate-200">
                                    <div class="h-full rounded-full" style="width: {{ $activity['extra'] }}%; background-color: #0a1628;"></div>
                                </div>
                                <span class="text-xs font-semibold whitespace-nowrap" style="color: #0a1628;">{{ $activity['extra'] }}%</span>
                            </div>
                            @elseif(isset($activity['extra']) && in_array($activity['extra'], ['pending', 'approved', 'rejected']))
                            @php
                                $statusClass = match($activity['extra']) {
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'approved' => 'bg-green-100 text-green-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    default => 'bg-slate-100 text-slate-700'
                                };
                            @endphp
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($activity['extra']) }}
                            </span>
                            @endif
                            <span class="text-xs text-slate-500 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($activity['date'])->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-slate-400">
                    <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-sm font-medium">Belum ada aktivitas</p>
                    <p class="text-xs text-slate-400 mt-1">Aktivitas akan muncul di sini setelah Anda membuat data</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Preview Modal -->
        <div x-show="showPreviewModal" x-cloak style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" @click.away="showPreviewModal = false">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showPreviewModal = false"></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full" @click.stop>
                    <template x-if="selectedActivity">
                        <div>
                            <div class="px-6 pt-6 pb-4" style="background-color: #0a1628;">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-lg" style="background-color: rgba(255,255,255,0.2);">
                                            <span x-text="selectedActivity.icon"></span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-white" x-text="selectedActivity.title"></h3>
                                            <p class="text-xs text-gray-300" x-text="selectedActivity.number"></p>
                                        </div>
                                    </div>
                                    <button @click="showPreviewModal = false" class="text-gray-300 hover:text-white">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="bg-white px-6 py-4 max-h-[60vh] overflow-y-auto">
                                <!-- Content based on type -->
                                <template x-if="selectedActivity.type === 'work-plan'">
                                    <div class="space-y-3">
                                        <div>
                                            <label class="text-xs font-medium text-slate-500">Deskripsi</label>
                                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.full_description || '-'"></p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <template x-if="selectedActivity.project && selectedActivity.project !== '-'">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Project</label>
                                                    <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.project + (selectedActivity.project_code ? ' (' + selectedActivity.project_code + ')' : '')"></p>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.location">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Lokasi</label>
                                                    <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.location || '-'"></p>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.duration">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Durasi</label>
                                                    <p class="text-sm mt-1" style="color: #0a1628;" x-text="(selectedActivity.duration || 0) + ' jam'"></p>
                                                </div>
                                            </template>
                                            <div>
                                                <label class="text-xs font-medium text-slate-500">Tanggal</label>
                                                <p class="text-sm mt-1" style="color: #0a1628;" x-text="new Date(selectedActivity.date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="selectedActivity.type === 'work-realization'">
                                    <div class="space-y-3">
                                        <div>
                                            <label class="text-xs font-medium text-slate-500">Deskripsi</label>
                                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.full_description || '-'"></p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <template x-if="selectedActivity.project && selectedActivity.project !== '-'">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Project</label>
                                                    <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.project + (selectedActivity.project_code ? ' (' + selectedActivity.project_code + ')' : '')"></p>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.location">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Lokasi</label>
                                                    <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.location || '-'"></p>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.duration">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Durasi</label>
                                                    <p class="text-sm mt-1" style="color: #0a1628;" x-text="(selectedActivity.duration || 0) + ' jam'"></p>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.progress !== undefined">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Progress</label>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <div class="flex-1 h-2 bg-slate-200 rounded-full overflow-hidden">
                                                            <div class="h-full rounded-full" style="background-color: #0a1628;" :style="'width: ' + (selectedActivity.progress || 0) + '%'"></div>
                                                        </div>
                                                        <span class="text-xs font-semibold" style="color: #0a1628;" x-text="(selectedActivity.progress || 0) + '%'"></span>
                                                    </div>
                                                </div>
                                            </template>
                                            <div>
                                                <label class="text-xs font-medium text-slate-500">Tanggal</label>
                                                <p class="text-sm mt-1" style="color: #0a1628;" x-text="new Date(selectedActivity.date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="selectedActivity.type === 'spd'">
                                    <div class="space-y-3">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="text-xs font-medium text-slate-500">Tujuan</label>
                                                <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.destination || '-'"></p>
                                            </div>
                                            <template x-if="selectedActivity.project && selectedActivity.project !== '-'">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Project</label>
                                                    <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.project + (selectedActivity.project_code ? ' (' + selectedActivity.project_code + ')' : '')"></p>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.departure_date && selectedActivity.departure_date !== '-'">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Tanggal Berangkat</label>
                                                    <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.departure_date"></p>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.return_date && selectedActivity.return_date !== '-'">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Tanggal Kembali</label>
                                                    <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.return_date"></p>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.total_cost">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Total Biaya</label>
                                                    <p class="text-sm mt-1 font-semibold" style="color: #0a1628;" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedActivity.total_cost || 0)"></p>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.status">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Status</label>
                                                    <div class="mt-1">
                                                        <span class="inline-block px-2 py-1 rounded text-xs font-medium" 
                                                              :class="selectedActivity.status === 'pending' ? 'bg-yellow-100 text-yellow-700' : (selectedActivity.status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700')"
                                                              x-text="selectedActivity.status.charAt(0).toUpperCase() + selectedActivity.status.slice(1)"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        <template x-if="selectedActivity.purpose">
                                            <div>
                                                <label class="text-xs font-medium text-slate-500">Tujuan Perjalanan</label>
                                                <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.purpose"></p>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="selectedActivity.type === 'purchase'">
                                    <div class="space-y-3">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="text-xs font-medium text-slate-500">Item</label>
                                                <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.item_name || '-'"></p>
                                            </div>
                                            <template x-if="selectedActivity.project && selectedActivity.project !== '-'">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Project</label>
                                                    <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.project + (selectedActivity.project_code ? ' (' + selectedActivity.project_code + ')' : '')"></p>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.total_price">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Total Harga</label>
                                                    <p class="text-sm mt-1 font-semibold" style="color: #0a1628;" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedActivity.total_price || 0)"></p>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.status">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Status</label>
                                                    <div class="mt-1">
                                                        <span class="inline-block px-2 py-1 rounded text-xs font-medium" 
                                                              :class="selectedActivity.status === 'pending' ? 'bg-yellow-100 text-yellow-700' : (selectedActivity.status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700')"
                                                              x-text="selectedActivity.status.charAt(0).toUpperCase() + selectedActivity.status.slice(1)"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="selectedActivity.type === 'vendor-payment'">
                                    <div class="space-y-3">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="text-xs font-medium text-slate-500">Vendor</label>
                                                <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.vendor || '-'"></p>
                                            </div>
                                            <template x-if="selectedActivity.project && selectedActivity.project !== '-'">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Project</label>
                                                    <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.project + (selectedActivity.project_code ? ' (' + selectedActivity.project_code + ')' : '')"></p>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.amount">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Jumlah</label>
                                                    <p class="text-sm mt-1 font-semibold" style="color: #0a1628;" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedActivity.amount || 0)"></p>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.status">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Status</label>
                                                    <div class="mt-1">
                                                        <span class="inline-block px-2 py-1 rounded text-xs font-medium" 
                                                              :class="selectedActivity.status === 'pending' ? 'bg-yellow-100 text-yellow-700' : (selectedActivity.status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700')"
                                                              x-text="selectedActivity.status.charAt(0).toUpperCase() + selectedActivity.status.slice(1)"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="selectedActivity.type === 'leave'">
                                    <div class="space-y-3">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="text-xs font-medium text-slate-500">Jenis Cuti</label>
                                                <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.leave_type || '-'"></p>
                                            </div>
                                            <template x-if="selectedActivity.start_date && selectedActivity.start_date !== '-'">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Tanggal Mulai</label>
                                                    <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.start_date"></p>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.end_date && selectedActivity.end_date !== '-'">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Tanggal Selesai</label>
                                                    <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.end_date"></p>
                                                </div>
                                            </template>
                                            <template x-if="selectedActivity.status">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Status</label>
                                                    <div class="mt-1">
                                                        <span class="inline-block px-2 py-1 rounded text-xs font-medium" 
                                                              :class="selectedActivity.status === 'pending' ? 'bg-yellow-100 text-yellow-700' : (selectedActivity.status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700')"
                                                              x-text="selectedActivity.status.charAt(0).toUpperCase() + selectedActivity.status.slice(1)"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        <template x-if="selectedActivity.reason">
                                            <div>
                                                <label class="text-xs font-medium text-slate-500">Alasan</label>
                                                <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.reason"></p>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="selectedActivity.type === 'project'">
                                    <div class="space-y-3">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="text-xs font-medium text-slate-500">Kode Project</label>
                                                <p class="text-sm mt-1 font-semibold" style="color: #0a1628;" x-text="selectedActivity.project_code || '-'"></p>
                                            </div>
                                            <template x-if="selectedActivity.client">
                                                <div>
                                                    <label class="text-xs font-medium text-slate-500">Client</label>
                                                    <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.client"></p>
                                                </div>
                                            </template>
                                        </div>
                                        <template x-if="selectedActivity.full_description && selectedActivity.full_description !== '-'">
                                            <div>
                                                <label class="text-xs font-medium text-slate-500">Deskripsi</label>
                                                <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.full_description"></p>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t">
                                <button @click="showPreviewModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Tutup
                                </button>
                                <template x-if="selectedActivity.route">
                                    <a :href="selectedActivity.route" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
                                        Lihat Detail
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
