@extends('layouts.app')

@section('title', 'Realisasi Kerja')
@section('page-title', 'Realisasi Kerja')
@section('page-subtitle', 'Catat pencapaian kerja harian Anda')

@section('content')
<div class="space-y-6" x-data="workRealizationPreview()">
    <div x-data="{ 
        openWorkRealizationModal(mode = 'create', realization = null) {
            window.dispatchEvent(new CustomEvent('open-work-realization-modal', {
                detail: { mode: mode, realization: realization }
            }));
        }
    }">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Realisasi Kerja</h1>
            <p class="text-sm text-slate-600 mt-1">Catat pencapaian kerja harian Anda</p>
        </div>
        <button @click="openWorkRealizationModal()" class="inline-flex items-center px-4 py-2 text-white text-sm font-medium rounded-lg transition-colors shadow-sm" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Realisasi Kerja
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
        <form method="GET" class="flex items-center gap-3">
            <label class="text-sm font-medium text-slate-700">Filter Bulan:</label>
            <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" 
                   class="text-sm rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium rounded-lg transition-colors">
                Filter
            </button>
            @if(request('month'))
            <a href="{{ route('user.work-realizations.index') }}" class="text-sm text-slate-600 hover:text-slate-900">
                Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        @if($workRealizations->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase">Realisasi Kerja</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase">Durasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase">Progress</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-700 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($workRealizations as $realization)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                            {{ $realization->realization_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-slate-900">{{ $realization->title }}</div>
                            <div class="text-sm text-slate-500 line-clamp-2">{{ \Illuminate\Support\Str::limit($realization->description, 100) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-slate-900">{{ $realization->user->name ?? '-' }}</div>
                            @if($realization->user_id !== auth()->id())
                                <span class="text-xs text-blue-600">(Managed Project)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($realization->project)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $realization->project->code ?? $realization->project->name }}
                                </span>
                            @else
                                <span class="text-sm text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($realization->work_location)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $realization->work_location->shortLabel() }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                            {{ $realization->actual_duration_hours ?? '-' }}h
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $realization->progress_percentage }}%"></div>
                                </div>
                                <span class="text-sm text-gray-900">{{ $realization->progress_percentage }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openPreviewModal({{ $realization->id }})" class="text-blue-600 hover:text-blue-900">
                                    Preview
                                </button>
                                @if($realization->user_id === auth()->id())
                                    <button @click="openWorkRealizationModal('edit', {
                                        id: {{ $realization->id }},
                                        realization_number: '{{ $realization->realization_number }}',
                                        realization_date: '{{ $realization->realization_date->format('Y-m-d') }}',
                                        project_id: {{ $realization->project_id ?? 'null' }},
                                        project_name: {{ $realization->project ? json_encode($realization->project->name) : 'null' }},
                                        project_code: {{ $realization->project ? json_encode($realization->project->code) : 'null' }},
                                        work_location: '{{ $realization->work_location?->value ?? '' }}',
                                        actual_duration_hours: {{ $realization->actual_duration_hours ?? 0 }},
                                        progress_percentage: {{ $realization->progress_percentage }},
                                        description: {{ json_encode($realization->description) }},
                                        output_description: {{ json_encode($realization->output_description) }},
                                        work_plan_id: {{ $realization->work_plan_id ?? 'null' }}
                                    })" class="text-indigo-600 hover:text-indigo-900">
                                        Edit
                                    </button>
                                    <form action="{{ route('user.work-realizations.destroy', $realization) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $workRealizations->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <h3 class="mt-2 text-sm font-medium text-slate-900">Belum ada realisasi kerja</h3>
            <p class="mt-1 text-sm text-slate-500">Mulai dengan membuat realisasi kerja baru.</p>
            <div class="mt-6">
                <button @click="openWorkRealizationModal()" class="inline-flex items-center px-4 py-2 text-white text-sm font-medium rounded-lg" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
                    Buat Realisasi Kerja
                </button>
            </div>
        </div>
        @endif
    </div>

    </div>
    
    <!-- Work Realization Modal -->
    <div x-data="{ 
        showModal: false, 
        modalMode: 'create', 
        currentRealization: null,
        workPlans: @js($workPlans ?? []),
        realizationTimeWarning: '',
        validateRealizationTime(event) {
            this.realizationTimeWarning = '';
            const selectedDate = event.target.value;
            if (!selectedDate) return;
            
            const selected = new Date(selectedDate + 'T00:00:00');
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const now = new Date();
            
            // Cek jika tanggal yang dipilih adalah hari ini
            if (selected.getTime() === today.getTime()) {
                // Cek apakah sudah melewati jam 17:00 (5 sore)
                if (now.getHours() >= 17) {
                    this.realizationTimeWarning = 'Realisasi kerja hari ini harus diisi sebelum jam 17:00 (5 sore).';
                }
            }
        },
        closeModal() {
            this.showModal = false;
            this.currentRealization = null;
            this.realizationTimeWarning = '';
        }
    }" 
         @open-work-realization-modal.window="showModal = true; modalMode = $event.detail.mode; currentRealization = $event.detail.realization; realizationTimeWarning = ''">
        @include('work.work-realizations.modal')
    </div>
    
    <!-- Preview Modal -->
    @include('work.work-realizations.preview-modal')
</div>

@push('alpine-init')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('workRealizationPreview', () => ({
        showPreviewModal: false,
        previewData: null,
        
        async openPreviewModal(realizationId) {
            try {
                const response = await fetch(`/user/work-realizations/${realizationId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                if (data.workRealization) {
                    this.previewData = data.workRealization;
                    this.showPreviewModal = true;
                }
            } catch (error) {
                console.error('Error fetching work realization:', error);
                alert('Gagal memuat data realisasi kerja');
            }
        },
        
        closePreviewModal() {
            this.showPreviewModal = false;
            this.previewData = null;
        }
    }));
});
</script>
@endpush
@endsection

