@extends('layouts.app')

@section('title', 'Realisasi Kerja')
@section('page-title', 'Realisasi Kerja')
@section('page-subtitle', 'Catat pencapaian kerja harian Anda')

@section('content')
<div class="py-4" x-data="workRealizationPreview()">
    <div x-data="{ 
        openWorkRealizationModal(mode = 'create', realization = null) {
            window.dispatchEvent(new CustomEvent('open-work-realization-modal', {
                detail: { mode: mode, realization: realization }
            }));
        }
    }">
    <!-- Table with Integrated Filters (EAR Style) -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <!-- Header with Filters -->
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-white">Realisasi Kerja</h2>
                    <p class="text-xs text-gray-300">Catat pencapaian kerja harian Anda</p>
                </div>
                <button @click="openWorkRealizationModal()" class="px-3 py-1.5 text-xs font-medium text-white rounded transition-colors bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Realisasi Kerja
                </button>
            </div>
            
            <!-- Compact Filters -->
            <form method="GET" class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-300 whitespace-nowrap">Bulan:</label>
                    <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" 
                           class="text-xs px-2 py-1.5 rounded border border-gray-600 bg-slate-800 text-white focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                           style="min-width: 120px;">
                </div>
                
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white rounded transition-colors bg-blue-600 hover:bg-blue-700">
                        Filter
                    </button>
                    @if(request('month'))
                    <a href="{{ route('user.work-realizations.index') }}" class="px-3 py-1.5 text-xs font-medium text-gray-300 bg-slate-700 hover:bg-slate-600 rounded transition-colors">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 200px);">
            @if($workRealizations->count() > 0)
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Realisasi Kerja</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">User</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Project</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Lokasi</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Durasi</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Progress</th>
                        <th class="px-4 py-2.5 text-right text-xs font-medium text-slate-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($workRealizations as $realization)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-900">
                            {{ $realization->realization_date->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-xs font-medium text-slate-900">{{ $realization->title }}</div>
                            <div class="text-xs text-slate-500 line-clamp-2">{{ \Illuminate\Support\Str::limit($realization->description, 100) }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-xs text-slate-900">{{ $realization->user->name ?? '-' }}</div>
                            @if($realization->user_id !== auth()->id())
                                <span class="text-[10px] text-blue-600">(Managed Project)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($realization->project)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $realization->project->code ?? $realization->project->name }}
                                </span>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($realization->work_location)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $realization->work_location->shortLabel() }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-900">
                            {{ $realization->actual_duration_hours ?? '-' }}h
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-1.5 mr-2">
                                    <div class="bg-green-600 h-1.5 rounded-full" style="width: {{ $realization->progress_percentage }}%"></div>
                                </div>
                                <span class="text-xs text-gray-900">{{ $realization->progress_percentage }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-medium">
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
            @if($workRealizations->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $workRealizations->links() }}
            </div>
            @endif
            @else
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-medium text-gray-900 mb-0.5">Belum ada realisasi kerja</p>
                <p class="text-xs text-gray-500 mb-6">Mulai dengan membuat realisasi kerja baru.</p>
                <button @click="openWorkRealizationModal()" class="px-3 py-1.5 text-xs font-medium text-white rounded transition-colors bg-blue-600 hover:bg-blue-700">
                    Buat Realisasi Kerja
                </button>
            </div>
            @endif
        </div>
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

