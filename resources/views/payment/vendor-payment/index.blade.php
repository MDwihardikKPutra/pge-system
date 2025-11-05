@extends('layouts.app')

@section('title', 'Pembayaran Vendor')
@section('page-title', 'Pembayaran Vendor')
@section('page-subtitle', 'Kelola Pengajuan Pembayaran Vendor Anda')

@section('content')
@php
    $isAdmin = auth()->user()->hasRole('admin');
    $routePrefix = $isAdmin ? 'admin' : 'user';
    $vpRoute = $routePrefix . '.vendor-payments';
@endphp
<div class="py-8" x-data="vendorPaymentForm">
    <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Pembayaran Vendor Saya</h1>
            <p class="text-xs text-gray-500 mt-1">Kelola pengajuan pembayaran ke vendor Anda</p>
        </div>
        <button @click="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors shadow-sm" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajukan Pembayaran Vendor Baru
        </button>
    </div>

    <!-- Status Filter -->
    <div class="mb-4 flex gap-2">
        <a href="{{ route($vpRoute . '.index', ['status' => 'all']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request('status', 'all') === 'all' ? 'text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}" @if(request('status', 'all') === 'all') style="background-color: #0a1628;" @endif>
            Semua
        </a>
        <a href="{{ route($vpRoute . '.index', ['status' => 'pending']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request('status') === 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Pending
        </a>
        <a href="{{ route($vpRoute . '.index', ['status' => 'approved']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request('status') === 'approved' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Disetujui
        </a>
        <a href="{{ route($vpRoute . '.index', ['status' => 'rejected']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request('status') === 'rejected' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Ditolak
        </a>
    </div>

    @if($vendorPayments->count() > 0)
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <h2 class="text-base font-semibold text-white">Daftar Pembayaran Vendor ({{ $vendorPayments->total() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">No. Pembayaran</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Vendor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Invoice</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Jumlah</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Tanggal</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($vendorPayments as $vp)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $vp->payment_number }}</td>
                        <td class="px-4 py-3">
                            <div class="text-xs font-semibold text-slate-900">{{ $vp->vendor->name }}</div>
                            <div class="text-xs text-slate-500">{{ $vp->project->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600">
                            <div>{{ $vp->invoice_number }}</div>
                            @if($vp->po_number)
                            <div class="text-xs text-slate-500">PO: {{ $vp->po_number }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs font-semibold text-slate-900">Rp {{ number_format($vp->amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-slate-600">
                            {{ $vp->payment_date->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($vp->isPending())
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                    Pending
                                </span>
                            @elseif($vp->isApproved())
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
                                <button @click="openPreviewModal({{ $vp->id }})" 
                                        class="text-blue-600 hover:text-blue-800 text-xs font-medium transition-colors">
                                    Preview
                                </button>
                                <button @click="openEditModal({{ $vp->id }})" 
                                        class="text-blue-600 hover:text-blue-800 text-xs font-medium transition-colors"
                                        @if(!$vp->isPending()) disabled @endif>
                                    Edit
                                </button>
                                @if($vp->isPending())
                                <form action="{{ route($vpRoute . '.destroy', $vp) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus pembayaran vendor ini?')">
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
            {{ $vendorPayments->links() }}
        </div>
    </div>
    @else
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-12 text-center max-w-xl mx-auto">
        <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <div class="mb-2 text-base font-semibold text-slate-600">Belum Ada Pembayaran Vendor</div>
        <p class="text-slate-500 mb-6 text-sm">Anda belum mengajukan pembayaran vendor. Buat pengajuan pertama Anda untuk memulai.</p>
        <button @click="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors shadow-sm mx-auto" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajukan Pembayaran Vendor Pertama
        </button>
    </div>
    @endif

    <!-- Modal Form -->
    @include('payment.vendor-payment.modal')
    
    <!-- Modal Preview -->
    @include('payment.vendor-payment.preview-modal')
</div>

@push('alpine-init')
<script>
// Register Alpine data component - must be executed before Alpine scans DOM
document.addEventListener('alpine:init', () => {
    Alpine.data('vendorPaymentForm', () => ({
        showModal: false,
        showPreviewModal: false,
        editMode: false,
        modalTitle: 'Ajukan Pembayaran Vendor Baru',
        previewData: null,
        formData: {
            id: null,
            vendor_id: '',
            project_id: '',
            payment_type: '',
            payment_date: '',
            invoice_number: '',
            po_number: '',
            amount: 0,
            description: '',
            notes: '',
            documents: null,
        },
        errors: {},
        vendors: @json($vendors),
        projects: @json($projects),
        
        openCreateModal() {
            this.resetForm();
            this.editMode = false;
            this.modalTitle = 'Ajukan Pembayaran Vendor Baru';
            this.showModal = true;
        },
        
        async openEditModal(vpId) {
            this.resetForm();
            this.editMode = true;
            this.modalTitle = 'Edit Pembayaran Vendor';
            
            try {
                const routePrefix = '{{ $isAdmin ? "admin" : "user" }}';
                const response = await fetch(`/${routePrefix}/vendor-payments/${vpId}`);
                const data = await response.json();
                if (data.vendorPayment) {
                    this.formData = {
                        id: data.vendorPayment.id,
                        vendor_id: data.vendorPayment.vendor_id || '',
                        project_id: data.vendorPayment.project_id || '',
                        payment_type: data.vendorPayment.payment_type || 'kantor',
                        payment_date: data.vendorPayment.payment_date || '',
                        invoice_number: data.vendorPayment.invoice_number || '',
                        po_number: data.vendorPayment.po_number || '',
                        amount: data.vendorPayment.amount || 0,
                        description: data.vendorPayment.description || '',
                        notes: data.vendorPayment.notes || '',
                    };
                }
            } catch (error) {
                console.error('Error fetching vendor payment:', error);
                alert('Gagal memuat data pembayaran vendor');
            }
            
            this.showModal = true;
        },
        
        resetForm() {
            this.formData = {
                id: null,
                vendor_id: '',
                project_id: '',
                payment_type: '',
                payment_date: '',
                invoice_number: '',
                po_number: '',
                amount: 0,
                description: '',
                notes: '',
                documents: null,
            };
            this.errors = {};
        },
        
        handleFileChange(event) {
            this.formData.documents = event.target.files;
        },
        
        async submitForm() {
            this.errors = {};
            const routePrefix = '{{ $isAdmin ? "admin" : "user" }}';
            const url = this.editMode 
                ? `/${routePrefix}/vendor-payments/${this.formData.id}`
                : `/${routePrefix}/vendor-payments`;
            const method = this.editMode ? 'PUT' : 'POST';
            
            try {
                const formData = new FormData();
                
                // Append basic fields
                if (this.formData.id) formData.append('id', this.formData.id);
                formData.append('vendor_id', this.formData.vendor_id);
                formData.append('project_id', this.formData.project_id);
                formData.append('payment_type', this.formData.payment_type);
                formData.append('payment_date', this.formData.payment_date);
                formData.append('invoice_number', this.formData.invoice_number);
                if (this.formData.po_number) formData.append('po_number', this.formData.po_number);
                formData.append('amount', this.formData.amount);
                formData.append('description', this.formData.description);
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
        
        async openPreviewModal(vpId) {
            try {
                const routePrefix = '{{ $isAdmin ? "admin" : "user" }}';
                const response = await fetch(`/${routePrefix}/vendor-payments/${vpId}`);
                const data = await response.json();
                if (data.vendorPayment) {
                    this.previewData = data.vendorPayment;
                    this.showPreviewModal = true;
                }
            } catch (error) {
                console.error('Error fetching vendor payment:', error);
                alert('Gagal memuat data pembayaran vendor');
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

