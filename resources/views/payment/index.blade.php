@extends('layouts.app')

@section('title', 'Pengajuan Pembayaran')
@section('page-title', 'Pengajuan Pembayaran')
@section('page-subtitle', 'Pilih jenis pengajuan pembayaran yang ingin Anda buat')

@section('content')
<div class="py-8" x-data="paymentSubmission">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- SPD Card -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">✈️</span>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Surat Perjalanan Dinas (SPD)</h3>
                <p class="text-sm text-gray-600 mb-4">Ajukan surat perjalanan dinas dengan rincian biaya perjalanan, transportasi, akomodasi, dan lainnya.</p>
                <button @click="openSpdModal()" class="inline-flex items-center gap-2 px-4 py-2 text-white rounded-lg transition-colors text-sm font-medium w-full justify-center" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Ajukan SPD</span>
                </button>
            </div>
        </div>

        <!-- Purchase Card -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">🛒</span>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Pembelian</h3>
                <p class="text-sm text-gray-600 mb-4">Ajukan pembelian barang, jasa, atau aset dengan detail spesifikasi, jumlah, dan harga satuan.</p>
                <button @click="openPurchaseModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium w-full justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Ajukan Pembelian</span>
                </button>
            </div>
        </div>

        <!-- Vendor Payment Card -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">💳</span>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Pembayaran Vendor</h3>
                <p class="text-sm text-gray-600 mb-4">Ajukan pembayaran ke vendor dengan informasi invoice, PO, dan detail pembayaran lainnya.</p>
                <button @click="openVendorPaymentModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium w-full justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Ajukan Pembayaran Vendor</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Tabs for History -->
    <div class="mt-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex gap-2">
                <button @click="activeTab = 'spd'" 
                        :class="activeTab === 'spd' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    Riwayat SPD ({{ $spdCount }})
                </button>
                <button @click="activeTab = 'purchase'" 
                        :class="activeTab === 'purchase' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    Riwayat Pembelian ({{ $purchaseCount }})
                </button>
                <button @click="activeTab = 'vendor-payment'" 
                        :class="activeTab === 'vendor-payment' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    Riwayat Pembayaran Vendor ({{ $vendorPaymentCount }})
                </button>
            </nav>
        </div>

        <!-- SPD Table -->
        <div x-show="activeTab === 'spd'" x-cloak class="mt-6">
            @include('payment.spd.table', ['spds' => $spds, 'routePrefix' => $routePrefix])
        </div>

        <!-- Purchase Table -->
        <div x-show="activeTab === 'purchase'" x-cloak class="mt-6">
            @include('payment.purchase.table', ['purchases' => $purchases, 'routePrefix' => $routePrefix])
        </div>

        <!-- Vendor Payment Table -->
        <div x-show="activeTab === 'vendor-payment'" x-cloak class="mt-6">
            @include('payment.vendor-payment.table', ['vendorPayments' => $vendorPayments, 'routePrefix' => $routePrefix])
        </div>
    </div>
</div>

<!-- Modals -->
@include('payment.spd.modal')
@include('payment.purchase.modal')
@include('payment.vendor-payment.modal')

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    // Main payment submission component
    Alpine.data('paymentSubmission', () => ({
        activeTab: '{{ $activeTab }}',
        
        openSpdModal() {
            // Trigger modal open via Alpine store or event
            window.dispatchEvent(new CustomEvent('open-spd-modal'));
        },
        
        openPurchaseModal() {
            window.dispatchEvent(new CustomEvent('open-purchase-modal'));
        },
        
        openVendorPaymentModal() {
            window.dispatchEvent(new CustomEvent('open-vendor-payment-modal'));
        }
    }));
    
    // SPD Form Component
    Alpine.data('spdForm', () => ({
        showModal: false,
        editMode: false,
        modalTitle: 'Ajukan SPD Baru',
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
            // Expose to global scope for access from paymentSubmission
            window.spdFormComponent = this;
            
            // Listen for open modal event
            window.addEventListener('open-spd-modal', () => {
                this.openCreateModal();
            });
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
                const routePrefix = '{{ $routePrefix }}';
                const response = await fetch(`/${routePrefix}/spd/${spdId}`);
                const data = await response.json();
                if (data.spd) {
                    if (data.spd.costs && Array.isArray(data.spd.costs)) {
                        this.formData.costs = data.spd.costs;
                    } else {
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
            const routePrefix = '{{ $routePrefix }}';
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
        }
    }));
    
    // Purchase Form Component
    Alpine.data('purchaseForm', () => ({
        showModal: false,
        editMode: false,
        modalTitle: 'Ajukan Pembelian Baru',
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
            window.purchaseFormComponent = this;
            
            window.addEventListener('open-purchase-modal', () => {
                this.openCreateModal();
            });
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
                const routePrefix = '{{ $routePrefix }}';
                const response = await fetch(`/${routePrefix}/purchases/${purchaseId}`);
                const data = await response.json();
                if (data.purchase) {
                    this.formData = {
                        id: data.purchase.id,
                        project_id: data.purchase.project_id || '',
                        type: data.purchase.type || '',
                        category: data.purchase.category || '',
                        item_name: data.purchase.item_name || '',
                        description: data.purchase.description || '',
                        quantity: data.purchase.quantity || 1,
                        unit: data.purchase.unit || '',
                        unit_price: data.purchase.unit_price || 0,
                        notes: data.purchase.notes || '',
                        documents: null,
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
            const routePrefix = '{{ $routePrefix }}';
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
        }
    }));
    
    // Vendor Payment Form Component
    Alpine.data('vendorPaymentForm', () => ({
        showModal: false,
        editMode: false,
        modalTitle: 'Ajukan Pembayaran Vendor Baru',
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
        
        init() {
            window.vendorPaymentFormComponent = this;
            
            window.addEventListener('open-vendor-payment-modal', () => {
                this.openCreateModal();
            });
        },
        
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
                const routePrefix = '{{ $routePrefix }}';
                const response = await fetch(`/${routePrefix}/vendor-payments/${vpId}`);
                const data = await response.json();
                if (data.vendorPayment) {
                    this.formData = {
                        id: data.vendorPayment.id,
                        vendor_id: data.vendorPayment.vendor_id || '',
                        project_id: data.vendorPayment.project_id || '',
                        payment_type: data.vendorPayment.payment_type || '',
                        payment_date: data.vendorPayment.payment_date || '',
                        invoice_number: data.vendorPayment.invoice_number || '',
                        po_number: data.vendorPayment.po_number || '',
                        amount: data.vendorPayment.amount || 0,
                        description: data.vendorPayment.description || '',
                        notes: data.vendorPayment.notes || '',
                        documents: null,
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
            const routePrefix = '{{ $routePrefix }}';
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
        }
    }));
});
</script>
@endpush

