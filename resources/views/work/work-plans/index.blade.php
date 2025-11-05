@extends('layouts.app')

@section('title', 'Rencana Kerja')
@section('page-title', 'Rencana Kerja')
@section('page-subtitle', 'Kelola rencana kerja harian Anda')

@section('content')
<div class="space-y-6" x-data="workPlanPreview()">
    <div x-data="{ 
        openWorkPlanModal(mode = 'create', plan = null) {
            window.dispatchEvent(new CustomEvent('open-work-plan-modal', {
                detail: { mode: mode, plan: plan }
            }));
        }
    }">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Rencana Kerja</h1>
            <p class="text-sm text-slate-600 mt-1">Kelola rencana kerja harian Anda</p>
        </div>
        <button @click="openWorkPlanModal()" class="inline-flex items-center px-4 py-2 text-white text-sm font-medium rounded-lg transition-colors shadow-sm" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Rencana Kerja
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
            <a href="{{ route('user.work-plans.index') }}" class="text-sm text-slate-600 hover:text-slate-900">
                Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        @if($workPlans->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase">Rencana Kerja</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-700 uppercase">Durasi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-700 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($workPlans as $plan)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                            {{ $plan->plan_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-slate-900">{{ $plan->title }}</div>
                            <div class="text-sm text-slate-500 line-clamp-2">{{ \Illuminate\Support\Str::limit($plan->description, 100) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-slate-900">{{ $plan->user->name ?? '-' }}</div>
                            @if($plan->user_id !== auth()->id())
                                <span class="text-xs text-blue-600">(Managed Project)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($plan->project)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $plan->project->code ?? $plan->project->name }}
                                </span>
                            @else
                                <span class="text-sm text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($plan->work_location)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $plan->work_location->shortLabel() }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                            {{ $plan->planned_duration_hours }}h
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openPreviewModal({{ $plan->id }})" class="text-blue-600 hover:text-blue-900">
                                    Preview
                                </button>
                                @if($plan->user_id === auth()->id())
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
        </div>
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $workPlans->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <h3 class="mt-2 text-sm font-medium text-slate-900">Belum ada rencana kerja</h3>
            <p class="mt-1 text-sm text-slate-500">Mulai dengan membuat rencana kerja baru.</p>
            <div class="mt-6">
                <button @click="openWorkPlanModal()" class="inline-flex items-center px-4 py-2 text-white text-sm font-medium rounded-lg transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
                    Buat Rencana Kerja
                </button>
            </div>
        </div>
        @endif
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

