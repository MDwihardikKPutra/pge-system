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
<div class="py-8" x-data="purchaseForm">
    <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Pembelian Saya</h1>
            <p class="text-xs text-gray-500 mt-1">Kelola pengajuan pembelian barang/jasa Anda</p>
        </div>
        <button @click="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors text-white transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajukan Pembelian Baru
        </button>
    </div>

    <!-- Status Filter -->
    <div class="mb-4 flex gap-2">
        <a href="{{ route($purchaseRoute . '.index', ['status' => 'all']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request('status', 'all') === 'all' ? 'text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}" @if(request('status', 'all') === 'all') style="background-color: #0a1628;" @endif>
            Semua
        </a>
        <a href="{{ route($purchaseRoute . '.index', ['status' => 'pending']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request('status') === 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Pending
        </a>
        <a href="{{ route($purchaseRoute . '.index', ['status' => 'approved']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request('status') === 'approved' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Disetujui
        </a>
        <a href="{{ route($purchaseRoute . '.index', ['status' => 'rejected']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request('status') === 'rejected' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Ditolak
        </a>
    </div>

    @if($purchases->count() > 0)
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <h2 class="text-base font-semibold text-white">Daftar Pembelian ({{ $purchases->total() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">No. Pembelian</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Item</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Jenis</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Qty</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Total Harga</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($purchases as $purchase)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $purchase->purchase_number }}</td>
                        <td class="px-4 py-3">
                            <div class="text-xs font-semibold text-slate-900">{{ $purchase->item_name }}</div>
                            <div class="text-xs text-slate-500">{{ $purchase->project->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $purchase->type === 'barang' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                {{ ucfirst($purchase->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-slate-600">
                            {{ $purchase->quantity }} {{ $purchase->unit }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs font-semibold text-slate-900">Rp {{ number_format($purchase->total_price, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($purchase->isPending())
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                    Pending
                                </span>
                            @elseif($purchase->isApproved())
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
        </div>
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $purchases->links() }}
        </div>
    </div>
    @else
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-12 text-center max-w-xl mx-auto">
        <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
        <div class="mb-2 text-base font-semibold text-slate-600">Belum Ada Pembelian</div>
        <p class="text-slate-500 mb-6 text-sm">Anda belum mengajukan pembelian. Buat pengajuan pertama Anda untuk memulai.</p>
        <button @click="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors shadow-sm mx-auto" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajukan Pembelian Pertama
        </button>
    </div>
    @endif

    <!-- Modal Form -->
    @include('payment.purchase.modal')
    
    <!-- Modal Preview -->
    @include('payment.purchase.preview-modal')
</div>

@push('alpine-init')
<script>
// Register Alpine data component - must be executed before Alpine scans DOM
document.addEventListener('alpine:init', () => {
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
            const routePrefix = '{{ $isAdmin ? "admin" : "user" }}';
            const url = this.editMode 
                ? `/${routePrefix}/purchases/${this.formData.id}`
                : `/${routePrefix}/purchases`;
            const method = this.editMode ? 'PUT' : 'POST';
            
            try {
                const formData = new FormData();
                
                // Append basic fields
                if (this.formData.id) formData.append('id', this.formData.id);
                formData.append('project_id', this.formData.project_id);
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
});
</script>
@endpush
@endsection

