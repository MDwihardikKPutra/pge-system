@extends('layouts.app')

@section('title', 'Approval Pembayaran')
@section('page-title', 'Approval Pembayaran')
@section('page-subtitle', 'Kelola Persetujuan Pengajuan Pembayaran')

@section('content')
<div class="py-4">
    <!-- Table with Integrated Filters (EAR Style) -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <!-- Header with Filters -->
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-white">Approval Pembayaran</h2>
                    <p class="text-xs text-gray-300">Kelola persetujuan pengajuan SPD, Pembelian, dan Pembayaran Vendor</p>
                </div>
            </div>
            
            <!-- Type Tabs -->
            <div class="flex gap-2 mb-4 border-b border-slate-700">
                @php
                    // Determine route based on current route name
                    $isAdmin = request()->routeIs('admin.*');
                    $indexRoute = $isAdmin ? 'admin.approvals.payments.index' : 'user.payment-approvals.index';
                @endphp
                <a href="{{ route($indexRoute, ['type' => 'all', 'status' => request('status', 'pending')]) }}" 
                   class="px-4 py-2 text-sm font-medium transition-colors {{ request('type', 'all') === 'all' ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white' }}">
                    Semua
                </a>
                <a href="{{ route($indexRoute, ['type' => 'spd', 'status' => request('status', 'pending')]) }}" 
                   class="px-4 py-2 text-sm font-medium transition-colors {{ request('type') === 'spd' ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white' }}">
                    SPD
                </a>
                <a href="{{ route($indexRoute, ['type' => 'purchase', 'status' => request('status', 'pending')]) }}" 
                   class="px-4 py-2 text-sm font-medium transition-colors {{ request('type') === 'purchase' ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white' }}">
                    Pembelian
                </a>
                <a href="{{ route($indexRoute, ['type' => 'vendor-payment', 'status' => request('status', 'pending')]) }}" 
                   class="px-4 py-2 text-sm font-medium transition-colors {{ request('type') === 'vendor-payment' ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white' }}">
                    Pembayaran Vendor
                </a>
            </div>
            
            <!-- Status Filter -->
            <div class="flex gap-2">
                <a href="{{ route($indexRoute, ['type' => request('type', 'all'), 'status' => 'all']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('status', 'pending') === 'all' ? 'bg-blue-600 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Semua
                </a>
                <a href="{{ route($indexRoute, ['type' => request('type', 'all'), 'status' => 'pending']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('status', 'pending') === 'pending' ? 'bg-yellow-500 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Pending
                </a>
                <a href="{{ route($indexRoute, ['type' => request('type', 'all'), 'status' => 'approved']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('status') === 'approved' ? 'bg-green-500 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Disetujui
                </a>
                <a href="{{ route($indexRoute, ['type' => request('type', 'all'), 'status' => 'rejected']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('status') === 'rejected' ? 'bg-red-500 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Ditolak
                </a>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 280px);">
            <!-- SPD Approvals -->
            @if((request('type', 'all') === 'all' || request('type') === 'spd') && $spds->count() > 0)
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">SPD ({{ $spds->total() }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">No. SPD</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Pengaju</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Tujuan</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach($spds as $spd)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-900">{{ $spd->spd_number }}</td>
                            <td class="px-4 py-3 text-xs text-slate-900">{{ $spd->user->name }}</td>
                            <td class="px-4 py-3 text-xs text-slate-900">{{ $spd->destination }}</td>
                            <td class="px-4 py-3 text-center text-xs text-slate-900">Rp {{ number_format($spd->total_cost, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($spd->isPending())
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Pending</span>
                                @elseif($spd->isApproved())
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✓ Disetujui</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">✗ Ditolak</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="openDetailModal('spd', {{ $spd->id }})" class="text-xs text-blue-600 hover:text-blue-800 font-medium px-3 py-1.5 border border-blue-300 rounded hover:bg-blue-50 transition-colors">
                                    Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($spds->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $spds->links() }}
            </div>
            @endif
            @endif

            <!-- Purchase Approvals -->
            @if((request('type', 'all') === 'all' || request('type') === 'purchase') && $purchases->count() > 0)
            @if((request('type', 'all') === 'all' || request('type') === 'spd') && $spds->count() > 0)
            <div class="px-6 py-4 border-t border-gray-200"></div>
            @endif
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Pembelian ({{ $purchases->total() }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">No. Pembelian</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Pengaju</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Item</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach($purchases as $purchase)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-900">{{ $purchase->purchase_number }}</td>
                            <td class="px-4 py-3 text-xs text-slate-900">{{ $purchase->user->name }}</td>
                            <td class="px-4 py-3 text-xs text-slate-900">{{ $purchase->item_name }}</td>
                            <td class="px-4 py-3 text-center text-xs text-slate-900">Rp {{ number_format($purchase->total_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($purchase->isPending())
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Pending</span>
                                @elseif($purchase->isApproved())
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✓ Disetujui</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">✗ Ditolak</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="openDetailModal('purchase', {{ $purchase->id }})" class="text-xs text-blue-600 hover:text-blue-800 font-medium px-3 py-1.5 border border-blue-300 rounded hover:bg-blue-50 transition-colors">
                                    Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($purchases->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $purchases->links() }}
            </div>
            @endif
            @endif

            <!-- Vendor Payment Approvals -->
            @if((request('type', 'all') === 'all' || request('type') === 'vendor-payment') && $vendorPayments->count() > 0)
            @if(((request('type', 'all') === 'all' || request('type') === 'spd') && $spds->count() > 0) || ((request('type', 'all') === 'all' || request('type') === 'purchase') && $purchases->count() > 0))
            <div class="px-6 py-4 border-t border-gray-200"></div>
            @endif
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Pembayaran Vendor ({{ $vendorPayments->total() }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">No. Pembayaran</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Pengaju</th>
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Vendor</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Jumlah</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach($vendorPayments as $vp)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs text-slate-900">{{ $vp->payment_number }}</td>
                            <td class="px-4 py-3 text-xs text-slate-900">{{ $vp->user->name }}</td>
                            <td class="px-4 py-3 text-xs text-slate-900">{{ $vp->vendor->name }}</td>
                            <td class="px-4 py-3 text-center text-xs text-slate-900">Rp {{ number_format($vp->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($vp->isPending())
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Pending</span>
                                @elseif($vp->isApproved())
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✓ Disetujui</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">✗ Ditolak</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="openDetailModal('vendor-payment', {{ $vp->id }})" class="text-xs text-blue-600 hover:text-blue-800 font-medium px-3 py-1.5 border border-blue-300 rounded hover:bg-blue-50 transition-colors">
                                    Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($vendorPayments->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $vendorPayments->links() }}
            </div>
            @endif
            @endif
        </div>

            <!-- Empty State -->
            @if($spds->count() === 0 && $purchases->count() === 0 && $vendorPayments->count() === 0)
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="mb-2 text-base font-semibold text-slate-600">Tidak Ada Pengajuan</div>
                <p class="text-slate-500 text-sm">Tidak ada pengajuan pembayaran yang perlu disetujui.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Detail & Approval Modal -->
@include('admin.approvals.payments.detail-modal')

<!-- Reject Modal -->
@include('admin.approvals.payments.reject-modal')

@endsection

@push('scripts')
<script>
function openRejectModal(type, id) {
    const modal = document.getElementById('reject-modal');
    const formType = document.getElementById('reject-form-type');
    const formId = document.getElementById('reject-form-id');
    
    if (formType) formType.value = type;
    if (formId) formId.value = id;
    
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent background scroll
    }
}

function closeRejectModal() {
    const modal = document.getElementById('reject-modal');
    const form = document.getElementById('reject-form');
    
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = ''; // Restore scroll
    }
    
    if (form) {
        form.reset();
    }
}

function submitRejectForm(event) {
    event.preventDefault();
    const form = event.target;
    const type = document.getElementById('reject-form-type').value;
    const id = document.getElementById('reject-form-id').value;
    
    if (!type || !id) {
        alert('Terjadi kesalahan. Silakan coba lagi.');
        return false;
    }
    
    // Determine base URL based on current route
    const isAdminRoute = window.location.pathname.includes('/admin/');
    const baseUrl = isAdminRoute ? '/admin/approvals/payments' : '/user/payment-approvals';
    const typePath = type === 'spd' ? 'spd' : type === 'purchase' ? 'purchases' : 'vendor-payments';
    const actionUrl = `${baseUrl}/${typePath}/${id}/reject`;
    
    if (!actionUrl) {
        alert('Route tidak ditemukan. Silakan refresh halaman.');
        return false;
    }
    
    form.action = actionUrl;
    form.submit();
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeRejectModal();
    }
});
</script>
@endpush

