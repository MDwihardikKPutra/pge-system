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
<div class="py-8" x-data="spdForm">
    <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">SPD Saya</h1>
            <p class="text-xs text-gray-500 mt-1">Kelola pengajuan surat perjalanan dinas Anda</p>
        </div>
        <button @click="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors shadow-sm" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajukan SPD Baru
        </button>
    </div>

    <!-- Status Filter -->
    <div class="mb-4 flex gap-2">
        <a href="{{ route($spdRoute . '.index', ['status' => 'all']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request('status', 'all') === 'all' ? 'text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}" @if(request('status', 'all') === 'all') style="background-color: #0a1628;" @endif>
            Semua
        </a>
        <a href="{{ route($spdRoute . '.index', ['status' => 'pending']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request('status') === 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Pending
        </a>
        <a href="{{ route($spdRoute . '.index', ['status' => 'approved']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request('status') === 'approved' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Disetujui
        </a>
        <a href="{{ route($spdRoute . '.index', ['status' => 'rejected']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request('status') === 'rejected' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Ditolak
        </a>
    </div>

    @if($spds->count() > 0)
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <h2 class="text-base font-semibold text-white">Daftar SPD ({{ $spds->total() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">No. SPD</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Tujuan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Tanggal</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Total Biaya</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($spds as $spd)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $spd->spd_number }}</td>
                        <td class="px-4 py-3">
                            <div class="text-xs font-semibold text-slate-900">{{ $spd->destination }}</div>
                            <div class="text-xs text-slate-500">{{ $spd->project->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600">
                            {{ $spd->departure_date->format('d M Y') }} - {{ $spd->return_date->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs font-semibold text-slate-900">Rp {{ number_format($spd->total_cost, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($spd->isPending())
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                    Pending
                                </span>
                            @elseif($spd->isApproved())
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    ✓ Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
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
        </div>
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $spds->links() }}
        </div>
    </div>
    @else
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-12 text-center max-w-xl mx-auto">
        <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <div class="mb-2 text-base font-semibold text-slate-600">Belum Ada SPD</div>
        <p class="text-slate-500 mb-6 text-sm">Anda belum mengajukan SPD. Buat pengajuan pertama Anda untuk memulai.</p>
        <button @click="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors bg-primary-blue hover:bg-primary-blue shadow-sm mx-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajukan SPD Pertama
        </button>
    </div>
    @endif

    <!-- Modal Form -->
    @include('payment.spd.modal')
    
    <!-- Modal Preview -->
    @include('payment.spd.preview-modal')
</div>

@push('alpine-init')
<script>
// Register Alpine data component - must be defined before Alpine scans DOM
document.addEventListener('alpine:init', () => {
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
});
</script>
@endpush
@endsection

