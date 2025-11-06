@extends('layouts.app')

@section('title', 'SPD')
@section('page-title', 'Surat Perjalanan Dinas')
@section('page-subtitle', 'Kelola Pengajuan SPD Anda')

@section('content')
@php
    $isAdmin = auth()->user()->hasRole('admin');
    $routePrefix = $isAdmin ? 'admin' : 'user';
    $spdRoute = $routePrefix . '.spd';
@endphp
<div class="py-4" x-data="spdForm">
    <!-- Table with Integrated Filters (EAR Style) -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <!-- Header with Filters -->
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-white">SPD</h2>
                    <p class="text-xs text-gray-300">Kelola pengajuan surat perjalanan dinas Anda</p>
                </div>
                <button @click="openCreateModal()" class="px-3 py-1.5 text-xs font-medium text-white rounded transition-colors bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajukan SPD Baru
                </button>
            </div>
            
            <!-- Status Filter -->
            <div class="flex gap-2">
                <a href="{{ route($spdRoute . '.index', ['status' => 'all']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('status', 'all') === 'all' ? 'bg-blue-600 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Semua
                </a>
                <a href="{{ route($spdRoute . '.index', ['status' => 'pending']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('status') === 'pending' ? 'bg-yellow-500 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Pending
                </a>
                <a href="{{ route($spdRoute . '.index', ['status' => 'approved']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('status') === 'approved' ? 'bg-green-500 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Disetujui
                </a>
                <a href="{{ route($spdRoute . '.index', ['status' => 'rejected']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('status') === 'rejected' ? 'bg-red-500 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Ditolak
                </a>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 200px);">
            @if($spds->count() > 0)
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">No. SPD</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Tujuan</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Total Biaya</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($spds as $spd)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-xs text-slate-900">{{ $spd->spd_number }}</td>
                        <td class="px-4 py-3">
                            <div class="text-xs font-medium text-slate-900">{{ $spd->destination }}</div>
                            <div class="text-xs text-slate-500">{{ $spd->project->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-900">
                            {{ $spd->departure_date->format('d M Y') }} - {{ $spd->return_date->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-slate-900">
                            Rp {{ number_format($spd->total_cost, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($spd->isPending())
                                <span class="badge-minimal badge-warning">
                                    Pending
                                </span>
                            @elseif($spd->isApproved())
                                <span class="badge-minimal badge-success">
                                    ✓ Disetujui
                                </span>
                            @else
                                <span class="badge-minimal badge-error">
                                    ✗ Ditolak
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openPreviewModal({{ $spd->id }})" 
                                        class="text-blue-600 hover:text-blue-800 text-xs font-medium transition-colors">
                                    Preview
                                </button>
                                <button @click="openEditModal({{ $spd->id }})" 
                                        class="text-blue-600 hover:text-blue-800 text-xs font-medium transition-colors"
                                        @if(!$spd->isPending()) disabled @endif>
                                    Edit
                                </button>
                                @if($spd->isPending())
                                <form action="{{ route($spdRoute . '.destroy', $spd) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus SPD ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium transition-colors">
                                        Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($spds->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $spds->links() }}
            </div>
            @endif
            @else
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-medium text-gray-900 mb-0.5">Belum ada SPD</p>
                <p class="text-xs text-gray-500 mb-6">Mulai dengan membuat pengajuan SPD baru</p>
                <button @click="openCreateModal()" class="px-3 py-1.5 text-xs font-medium text-white rounded transition-colors bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajukan SPD Baru
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal Form -->
    @include('payment.spd.modal')
    
    <!-- Modal Preview -->
    @include('payment.spd.preview-modal')
</div>

@push('alpine-init')
<script>
// Register Alpine data component - must be defined before Alpine scans DOM
(function() {
    function registerSpdForm() {
        if (typeof Alpine === 'undefined') {
            console.error('Alpine.js not loaded');
            return;
        }
        
        Alpine.data('spdForm', () => ({
        showModal: false,
        showPreviewModal: false,
        editMode: false,
        modalTitle: 'Ajukan SPD Baru',
        previewData: null,
        formData: {
            id: null,
            project_id: '',
            destination: '',
            departure_date: '',
            return_date: '',
            purpose: '',
            costs: [{ name: '', description: '', amount: 0 }],
            notes: '',
            documents: null,
        },
        errors: {},
        projects: @json($projects),
        totalCostDisplay: '0',
        
        init() {
            this.calculateTotal();
        },
        
        calculateTotal() {
            let total = 0;
            if (this.formData.costs && this.formData.costs.length > 0) {
                this.formData.costs.forEach(cost => {
                    total += parseFloat(cost.amount || 0);
                });
            }
            this.totalCostDisplay = total.toLocaleString('id-ID');
        },
        
        addCostRow() {
            this.formData.costs.push({ name: '', description: '', amount: 0 });
        },
        
        removeCostRow(index) {
            if (this.formData.costs.length <= 1) {
                alert('Minimal harus ada 1 item biaya!');
                return;
            }
            this.formData.costs.splice(index, 1);
            this.calculateTotal();
        },
        
        handleFileChange(event) {
            this.formData.documents = event.target.files;
        },
        
        openCreateModal() {
            this.resetForm();
            this.editMode = false;
            this.modalTitle = 'Ajukan SPD Baru';
            this.showModal = true;
        },
        
        async openEditModal(spdId) {
            this.resetForm();
            this.editMode = true;
            this.modalTitle = 'Edit SPD';
            
            try {
                const routePrefix = '{{ $isAdmin ? "admin" : "user" }}';
                const response = await fetch(`/${routePrefix}/spd/${spdId}`);
                const data = await response.json();
                if (data.spd) {
                    // If SPD has costs array, use it; otherwise create from old fields
                    if (data.spd.costs && data.spd.costs.length > 0) {
                        this.formData.costs = data.spd.costs;
                    } else {
                        // Fallback: create costs from old structure
                        this.formData.costs = [];
                        if (data.spd.transport_cost > 0) {
                            this.formData.costs.push({ name: 'Transport', description: '', amount: data.spd.transport_cost || 0 });
                        }
                        if (data.spd.accommodation_cost > 0) {
                            this.formData.costs.push({ name: 'Akomodasi', description: '', amount: data.spd.accommodation_cost || 0 });
                        }
                        if (data.spd.meal_cost > 0) {
                            this.formData.costs.push({ name: 'Makan', description: '', amount: data.spd.meal_cost || 0 });
                        }
                        if (data.spd.other_cost > 0) {
                            this.formData.costs.push({ name: 'Lainnya', description: data.spd.other_cost_description || '', amount: data.spd.other_cost || 0 });
                        }
                        if (this.formData.costs.length === 0) {
                            this.formData.costs = [{ name: '', description: '', amount: 0 }];
                        }
                    }
                    
                    this.formData.id = data.spd.id;
                    this.formData.project_id = data.spd.project_id || '';
                    this.formData.destination = data.spd.destination || '';
                    this.formData.departure_date = data.spd.departure_date || '';
                    this.formData.return_date = data.spd.return_date || '';
                    this.formData.purpose = data.spd.purpose || '';
                    this.formData.notes = data.spd.notes || '';
                    
                    this.calculateTotal();
                }
            } catch (error) {
                console.error('Error fetching SPD:', error);
                alert('Gagal memuat data SPD');
            }
            
            this.showModal = true;
        },
        
        resetForm() {
            this.formData = {
                id: null,
                project_id: '',
                destination: '',
                departure_date: '',
                return_date: '',
                purpose: '',
                costs: [{ name: '', description: '', amount: 0 }],
                notes: '',
                documents: null,
            };
            this.errors = {};
            this.totalCostDisplay = '0';
        },
        
        async submitForm() {
            this.errors = {};
            
            // Sync project_id from vanilla JS project-select to Alpine formData
            const projectSelectHidden = document.querySelector('.project-select-container .project-select-hidden');
            if (projectSelectHidden && projectSelectHidden.value) {
                this.formData.project_id = projectSelectHidden.value;
            }
            
            const routePrefix = '{{ $isAdmin ? "admin" : "user" }}';
            const url = this.editMode 
                ? `/${routePrefix}/spd/${this.formData.id}`
                : `/${routePrefix}/spd`;
            const method = this.editMode ? 'PUT' : 'POST';
            
            try {
                const formData = new FormData();
                
                // Append basic fields
                if (this.formData.id) formData.append('id', this.formData.id);
                if (this.formData.project_id) formData.append('project_id', this.formData.project_id);
                formData.append('destination', this.formData.destination);
                formData.append('departure_date', this.formData.departure_date);
                formData.append('return_date', this.formData.return_date);
                formData.append('purpose', this.formData.purpose);
                if (this.formData.notes) formData.append('notes', this.formData.notes);
                
                // Append costs array
                if (this.formData.costs && this.formData.costs.length > 0) {
                    this.formData.costs.forEach((cost, index) => {
                        formData.append(`cost_name[${index}]`, cost.name || '');
                        formData.append(`cost_description[${index}]`, cost.description || '');
                        formData.append(`cost_amount[${index}]`, cost.amount || 0);
                    });
                }
                
                // Append documents if any
                if (this.formData.documents && this.formData.documents.length > 0) {
                    for (let i = 0; i < this.formData.documents.length; i++) {
                        formData.append('documents[]', this.formData.documents[i]);
                    }
                }
                
                formData.append('_method', method === 'PUT' ? 'PUT' : 'POST');
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    if (data.errors) {
                        this.errors = data.errors;
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
            }
        },
        
        closeModal() {
            this.showModal = false;
            this.resetForm();
        },
        
        async openPreviewModal(spdId) {
            try {
                const routePrefix = '{{ $isAdmin ? "admin" : "user" }}';
                const response = await fetch(`/${routePrefix}/spd/${spdId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) throw new Error('Failed to fetch data');
                
                const data = await response.json();
                if (data.spd) {
                    this.previewData = data.spd;
                    this.showPreviewModal = true;
                }
            } catch (error) {
                console.error('Error fetching SPD:', error);
                alert('Gagal memuat data SPD');
            }
        },
        
        closePreviewModal() {
            this.showPreviewModal = false;
            this.previewData = null;
        }
    }));
    }
    
    // Multiple strategies to ensure registration
    // Strategy 1: Register immediately if Alpine is already loaded
    if (typeof Alpine !== 'undefined' && Alpine.version) {
        registerSpdForm();
    } else {
        // Strategy 2: Wait for Alpine to load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    if (typeof Alpine !== 'undefined') {
                        registerSpdForm();
                    }
                }, 100);
            });
        } else {
            setTimeout(() => {
                if (typeof Alpine !== 'undefined') {
                    registerSpdForm();
                }
            }, 100);
        }
    }
    
    // Strategy 3: Also register on alpine:init event (backup)
    document.addEventListener('alpine:init', registerSpdForm);
})();
</script>

@include('payment.spd.project-select-script')
@endpush
@endsection

