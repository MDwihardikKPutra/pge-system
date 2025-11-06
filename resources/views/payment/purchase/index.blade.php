@extends('layouts.app')

@section('title', 'Pembelian')
@section('page-title', 'Pembelian')
@section('page-subtitle', 'Kelola Pengajuan Pembelian Anda')

@section('content')
@php
    $isAdmin = auth()->user()->hasRole('admin');
    $routePrefix = $isAdmin ? 'admin' : 'user';
    $purchaseRoute = $routePrefix . '.purchases';
@endphp
<div class="py-4" x-data="purchaseForm">
    <!-- Table with Integrated Filters (EAR Style) -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <!-- Header with Filters -->
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-white">Pembelian</h2>
                    <p class="text-xs text-gray-300">Kelola pengajuan pembelian barang/jasa Anda</p>
                </div>
                <button @click="openCreateModal()" class="px-3 py-1.5 text-xs font-medium text-white rounded transition-colors bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajukan Pembelian Baru
                </button>
            </div>
            
            <!-- Status Filter -->
            <div class="flex gap-2">
                <a href="{{ route($purchaseRoute . '.index', ['status' => 'all']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('status', 'all') === 'all' ? 'bg-blue-600 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Semua
                </a>
                <a href="{{ route($purchaseRoute . '.index', ['status' => 'pending']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('status') === 'pending' ? 'bg-yellow-500 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Pending
                </a>
                <a href="{{ route($purchaseRoute . '.index', ['status' => 'approved']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('status') === 'approved' ? 'bg-green-500 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Disetujui
                </a>
                <a href="{{ route($purchaseRoute . '.index', ['status' => 'rejected']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('status') === 'rejected' ? 'bg-red-500 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Ditolak
                </a>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 200px);">
            @if($purchases->count() > 0)
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">No. Pembelian</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Item</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Jenis</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Qty</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Total Harga</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($purchases as $purchase)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-xs text-slate-900">{{ $purchase->purchase_number }}</td>
                        <td class="px-4 py-3">
                            <div class="text-xs font-medium text-slate-900">{{ $purchase->item_name }}</div>
                            <div class="text-xs text-slate-500">{{ $purchase->project->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $purchase->type === 'barang' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                {{ ucfirst($purchase->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-slate-900">
                            {{ $purchase->quantity }} {{ $purchase->unit }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-slate-900">
                            Rp {{ number_format($purchase->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($purchase->isPending())
                                <span class="badge-minimal badge-warning">
                                    Pending
                                </span>
                            @elseif($purchase->isApproved())
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
                                <button @click="openPreviewModal({{ $purchase->id }})" 
                                        class="text-blue-600 hover:text-blue-800 text-xs font-medium transition-colors">
                                    Preview
                                </button>
                                <button @click="openEditModal({{ $purchase->id }})" 
                                        class="text-blue-600 hover:text-blue-800 text-xs font-medium transition-colors"
                                        @if(!$purchase->isPending()) disabled @endif>
                                    Edit
                                </button>
                                @if($purchase->isPending())
                                <form action="{{ route($purchaseRoute . '.destroy', $purchase) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus pembelian ini?')">
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
            @if($purchases->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $purchases->links() }}
            </div>
            @endif
            @else
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p class="text-sm font-medium text-gray-900 mb-0.5">Belum ada pembelian</p>
                <p class="text-xs text-gray-500 mb-6">Mulai dengan membuat pengajuan pembelian baru</p>
                <button @click="openCreateModal()" class="px-3 py-1.5 text-xs font-medium text-white rounded transition-colors bg-blue-600 hover:bg-blue-700">
                    Ajukan Pembelian Baru
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal Form -->
    @include('payment.purchase.modal')
    
    <!-- Modal Preview -->
    @include('payment.purchase.preview-modal')
</div>

@push('alpine-init')
<script>
// Register Alpine data component - must be executed before Alpine scans DOM
(function() {
    function registerPurchaseForm() {
        if (typeof Alpine === 'undefined') {
            console.error('Alpine.js not loaded');
            return;
        }
        
        Alpine.data('purchaseForm', () => ({
        showModal: false,
        showPreviewModal: false,
        editMode: false,
        modalTitle: 'Ajukan Pembelian Baru',
        previewData: null,
        formData: {
            id: null,
            project_id: '',
            type: '',
            category: '',
            item_name: '',
            description: '',
            quantity: 1,
            unit: '',
            unit_price: 0,
            notes: '',
            documents: null,
        },
        errors: {},
        projects: @json($projects),
        totalPriceDisplay: '0',
        
        init() {
            this.$watch('formData.quantity', () => this.updateTotalPrice());
            this.$watch('formData.unit_price', () => this.updateTotalPrice());
            this.updateTotalPrice();
        },
        
        updateTotalPrice() {
            const total = parseFloat(this.formData.quantity || 0) * parseFloat(this.formData.unit_price || 0);
            this.totalPriceDisplay = total.toLocaleString('id-ID');
            return this.totalPriceDisplay;
        },
        
        handleFileChange(event) {
            this.formData.documents = event.target.files;
        },
        
        openCreateModal() {
            this.resetForm();
            this.editMode = false;
            this.modalTitle = 'Ajukan Pembelian Baru';
            this.showModal = true;
        },
        
        async openEditModal(purchaseId) {
            this.resetForm();
            this.editMode = true;
            this.modalTitle = 'Edit Pembelian';
            
            try {
                const routePrefix = '{{ $isAdmin ? "admin" : "user" }}';
                const response = await fetch(`/${routePrefix}/purchases/${purchaseId}`);
                const data = await response.json();
                if (data.purchase) {
                    this.formData = {
                        id: data.purchase.id,
                        project_id: data.purchase.project_id || '',
                        type: data.purchase.type || 'barang',
                        category: data.purchase.category || 'kantor',
                        item_name: data.purchase.item_name || '',
                        description: data.purchase.description || '',
                        quantity: data.purchase.quantity || 1,
                        unit: data.purchase.unit || 'pcs',
                        unit_price: data.purchase.unit_price || 0,
                        notes: data.purchase.notes || '',
                    };
                }
            } catch (error) {
                console.error('Error fetching purchase:', error);
                alert('Gagal memuat data pembelian');
            }
            
            this.showModal = true;
        },
        
        resetForm() {
            this.formData = {
                id: null,
                project_id: '',
                type: '',
                category: '',
                item_name: '',
                description: '',
                quantity: 1,
                unit: '',
                unit_price: 0,
                notes: '',
                documents: null,
            };
            this.errors = {};
            this.totalPriceDisplay = '0';
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
                ? `/${routePrefix}/purchases/${this.formData.id}`
                : `/${routePrefix}/purchases`;
            const method = this.editMode ? 'PUT' : 'POST';
            
            try {
                const formData = new FormData();
                
                // Append basic fields
                if (this.formData.id) formData.append('id', this.formData.id);
                formData.append('project_id', this.formData.project_id || '');
                formData.append('type', this.formData.type);
                formData.append('category', this.formData.category);
                formData.append('item_name', this.formData.item_name);
                formData.append('description', this.formData.description);
                formData.append('quantity', this.formData.quantity);
                formData.append('unit', this.formData.unit);
                formData.append('unit_price', this.formData.unit_price);
                if (this.formData.notes) formData.append('notes', this.formData.notes);
                
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
        
        async openPreviewModal(purchaseId) {
            try {
                const routePrefix = '{{ $isAdmin ? "admin" : "user" }}';
                const response = await fetch(`/${routePrefix}/purchases/${purchaseId}`);
                const data = await response.json();
                if (data.purchase) {
                    this.previewData = data.purchase;
                    this.showPreviewModal = true;
                }
            } catch (error) {
                console.error('Error fetching purchase:', error);
                alert('Gagal memuat data pembelian');
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
        registerPurchaseForm();
    } else {
        // Strategy 2: Wait for Alpine to load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    if (typeof Alpine !== 'undefined') {
                        registerPurchaseForm();
                    }
                }, 100);
            });
        } else {
            setTimeout(() => {
                if (typeof Alpine !== 'undefined') {
                    registerPurchaseForm();
                }
            }, 100);
        }
    }
    
    // Strategy 3: Also register on alpine:init event (backup)
    document.addEventListener('alpine:init', registerPurchaseForm);
})();
</script>

@include('payment.purchase.project-select-script')
@endpush
@endsection

