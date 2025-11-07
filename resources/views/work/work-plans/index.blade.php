@extends('layouts.app')

@section('title', 'Rencana Kerja')
@section('page-title', 'Rencana Kerja')
@section('page-subtitle', 'Kelola rencana kerja harian Anda')

@section('content')
<div class="py-4" x-data="workPlanPreview()">
    <div x-data="{ 
        openWorkPlanModal(mode = 'create', plan = null) {
            window.dispatchEvent(new CustomEvent('open-work-plan-modal', {
                detail: { mode: mode, plan: plan }
            }));
        }
    }">
    <!-- Table with Integrated Filters (EAR Style) -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <!-- Header with Filters -->
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-white">Rencana Kerja</h2>
                    <p class="text-xs text-gray-300">Kelola rencana kerja harian Anda</p>
                </div>
                <button @click="openWorkPlanModal()" class="px-3 py-1.5 text-xs font-medium text-white rounded transition-colors bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Rencana Kerja
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
                    <a href="{{ route('user.work-plans.index') }}" class="px-3 py-1.5 text-xs font-medium text-gray-300 bg-slate-700 hover:bg-slate-600 rounded transition-colors">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 200px);">
            @if($workPlans->count() > 0)
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Rencana Kerja</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">User</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Project</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Lokasi</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Durasi</th>
                        <th class="px-4 py-2.5 text-right text-xs font-medium text-slate-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($workPlans as $plan)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-900">
                            {{ $plan->plan_date->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-xs font-medium text-slate-900">{{ $plan->title }}</div>
                            <div class="text-xs text-slate-500 line-clamp-2">{{ \Illuminate\Support\Str::limit($plan->description, 100) }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-xs text-slate-900">{{ $plan->user->name ?? '-' }}</div>
                            @if($plan->user_id !== auth()->id())
                                <span class="text-[10px] text-blue-600">(Managed Project)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($plan->project)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $plan->project->code ?? $plan->project->name }}
                                </span>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($plan->work_location)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $plan->work_location->shortLabel() }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-900">
                            {{ $plan->planned_duration_hours }}h
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-medium">
                            <div class="flex items-center justify-end gap-2">
                                @php
                                    $canView = auth()->user()->can('view', $plan);
                                    $canUpdate = auth()->user()->can('update', $plan);
                                    $canDelete = auth()->user()->can('delete', $plan);
                                @endphp
                                @if($canView)
                                    <button @click="openPreviewModal({{ $plan->id }})" class="text-blue-600 hover:text-blue-900">
                                        Preview
                                    </button>
                                @endif
                                @if($canUpdate)
                                    <button @click="openWorkPlanModal('edit', {
                                        id: {{ $plan->id }},
                                        work_plan_number: '{{ $plan->work_plan_number }}',
                                        plan_date: '{{ $plan->plan_date->format('Y-m-d') }}',
                                        project_id: {{ $plan->project_id ?? 'null' }},
                                        project_name: {{ $plan->project ? json_encode($plan->project->name) : 'null' }},
                                        project_code: {{ $plan->project ? json_encode($plan->project->code) : 'null' }},
                                        work_location: '{{ $plan->work_location?->value ?? '' }}',
                                        planned_duration_hours: {{ $plan->planned_duration_hours }},
                                        description: {{ json_encode($plan->description) }},
                                        expected_output: {{ json_encode($plan->expected_output) }}
                                    })" class="text-indigo-600 hover:text-indigo-900">
                                        Edit
                                    </button>
                                @endif
                                @if($canDelete)
                                    <form action="{{ route('user.work-plans.destroy', $plan) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
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
            @if($workPlans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $workPlans->links() }}
            </div>
            @endif
            @else
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-medium text-gray-900 mb-0.5">Belum ada rencana kerja</p>
                <p class="text-xs text-gray-500 mb-6">Mulai dengan membuat rencana kerja baru.</p>
                <button @click="openWorkPlanModal()" class="px-3 py-1.5 text-xs font-medium text-white rounded transition-colors bg-blue-600 hover:bg-blue-700">
                    Buat Rencana Kerja
                </button>
            </div>
            @endif
        </div>
    </div>

    </div>
    
    <!-- Work Plan Modal -->
    <div x-data="{ 
        showModal: false, 
        modalMode: 'create', 
        currentPlan: null,
        planTimeWarning: '',
        validatePlanTime(event) {
            this.planTimeWarning = '';
            const selectedDate = event.target.value;
            if (!selectedDate) return;
            
            const selected = new Date(selectedDate + 'T00:00:00');
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const now = new Date();
            
            // Cek jika tanggal yang dipilih adalah hari ini
            if (selected.getTime() === today.getTime()) {
                // Cek apakah sudah melewati jam 10:00
                if (now.getHours() >= 10) {
                    this.planTimeWarning = 'Rencana kerja hari ini harus diisi sebelum jam 10:00 pagi.';
                }
            }
        },
        closeModal() {
            this.showModal = false;
            this.currentPlan = null;
            this.planTimeWarning = '';
        }
    }" 
         @open-work-plan-modal.window="showModal = true; modalMode = $event.detail.mode; currentPlan = $event.detail.plan; planTimeWarning = ''">
        @include('work.work-plans.modal')
    </div>
    
    <!-- Preview Modal -->
    @include('work.work-plans.preview-modal')
</div>

@push('alpine-init')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('workPlanPreview', () => ({
        showPreviewModal: false,
        previewData: null,
        
        async openPreviewModal(planId) {
            try {
                const response = await fetch(`/user/work-plans/${planId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                if (data.workPlan) {
                    this.previewData = data.workPlan;
                    this.showPreviewModal = true;
                }
            } catch (error) {
                console.error('Error fetching work plan:', error);
                alert('Gagal memuat data rencana kerja');
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

