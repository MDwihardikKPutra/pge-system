@extends('layouts.app')

@section('title', 'Approval Pembayaran')
@section('page-title', 'Approval Pembayaran')
@section('page-subtitle', 'Kelola Persetujuan Pengajuan Pembayaran')

@section('content')
<div class="py-8">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Approval Pembayaran</h1>
        <p class="text-xs text-gray-500 mt-1">Kelola persetujuan pengajuan SPD, Pembelian, dan Pembayaran Vendor</p>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6 flex gap-2 border-b border-gray-200">
        <a href="{{ route('admin.approvals.payments.index', ['type' => 'all', 'status' => request('status', 'pending')]) }}" 
           class="px-4 py-2 text-sm font-medium border-b-2 transition-colors {{ request('type', 'all') === 'all' ? 'border-primary text-primary' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
            Semua
        </a>
        <a href="{{ route('admin.approvals.payments.index', ['type' => 'spd', 'status' => request('status', 'pending')]) }}" 
           class="px-4 py-2 text-sm font-medium border-b-2 transition-colors {{ request('type') === 'spd' ? 'border-primary text-primary' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
            SPD
        </a>
        <a href="{{ route('admin.approvals.payments.index', ['type' => 'purchase', 'status' => request('status', 'pending')]) }}" 
           class="px-4 py-2 text-sm font-medium border-b-2 transition-colors {{ request('type') === 'purchase' ? 'border-primary text-primary' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
            Pembelian
        </a>
        <a href="{{ route('admin.approvals.payments.index', ['type' => 'vendor-payment', 'status' => request('status', 'pending')]) }}" 
           class="px-4 py-2 text-sm font-medium border-b-2 transition-colors {{ request('type') === 'vendor-payment' ? 'border-primary text-primary' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
            Pembayaran Vendor
        </a>
    </div>

    <!-- Status Filter -->
    <div class="mb-4 flex gap-2">
        <a href="{{ route('admin.approvals.payments.index', ['type' => request('type', 'all'), 'status' => 'all']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request('status', 'pending') === 'all' ? 'text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}" @if(request('status', 'pending') === 'all') style="background-color: #0a1628;" @endif>
            Semua
        </a>
        <a href="{{ route('admin.approvals.payments.index', ['type' => request('type', 'all'), 'status' => 'pending']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request('status', 'pending') === 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Pending
        </a>
        <a href="{{ route('admin.approvals.payments.index', ['type' => request('type', 'all'), 'status' => 'approved']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request('status') === 'approved' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Disetujui
        </a>
        <a href="{{ route('admin.approvals.payments.index', ['type' => request('type', 'all'), 'status' => 'rejected']) }}" 
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ request('status') === 'rejected' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Ditolak
        </a>
    </div>

    <!-- SPD Approvals -->
    @if((request('type', 'all') === 'all' || request('type') === 'spd') && $spds->count() > 0)
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <h2 class="text-base font-semibold text-white">SPD ({{ $spds->total() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">No. SPD</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Pengaju</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Tujuan</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($spds as $spd)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ $spd->spd_number }}</td>
                        <td class="px-4 py-3 text-xs">{{ $spd->user->name }}</td>
                        <td class="px-4 py-3 text-xs">{{ $spd->destination }}</td>
                        <td class="px-4 py-3 text-center text-xs">Rp {{ number_format($spd->total_cost, 0, ',', '.') }}</td>
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
                            <button onclick="openDetailModal('spd', {{ $spd->id }})" class="text-xs text-blue-600 hover:text-blue-800 font-medium px-3 py-1.5 border border-blue-300 rounded-lg hover:bg-blue-50 transition-colors">
                                Detail
                            </button>
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
    @endif

    <!-- Purchase Approvals -->
    @if((request('type', 'all') === 'all' || request('type') === 'purchase') && $purchases->count() > 0)
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <h2 class="text-base font-semibold text-white">Pembelian ({{ $purchases->total() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">No. Pembelian</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Pengaju</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Item</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($purchases as $purchase)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ $purchase->purchase_number }}</td>
                        <td class="px-4 py-3 text-xs">{{ $purchase->user->name }}</td>
                        <td class="px-4 py-3 text-xs">{{ $purchase->item_name }}</td>
                        <td class="px-4 py-3 text-center text-xs">Rp {{ number_format($purchase->total_price, 0, ',', '.') }}</td>
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
                            <button onclick="openDetailModal('purchase', {{ $purchase->id }})" class="text-xs text-blue-600 hover:text-blue-800 font-medium px-3 py-1.5 border border-blue-300 rounded-lg hover:bg-blue-50 transition-colors">
                                Detail
                            </button>
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
    @endif

    <!-- Vendor Payment Approvals -->
    @if((request('type', 'all') === 'all' || request('type') === 'vendor-payment') && $vendorPayments->count() > 0)
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <h2 class="text-base font-semibold text-white">Pembayaran Vendor ({{ $vendorPayments->total() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">No. Pembayaran</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Pengaju</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Vendor</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Jumlah</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($vendorPayments as $vp)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ $vp->payment_number }}</td>
                        <td class="px-4 py-3 text-xs">{{ $vp->user->name }}</td>
                        <td class="px-4 py-3 text-xs">{{ $vp->vendor->name }}</td>
                        <td class="px-4 py-3 text-center text-xs">Rp {{ number_format($vp->amount, 0, ',', '.') }}</td>
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
                            <button onclick="openDetailModal('vendor-payment', {{ $vp->id }})" class="text-xs text-blue-600 hover:text-blue-800 font-medium px-3 py-1.5 border border-blue-300 rounded-lg hover:bg-blue-50 transition-colors">
                                Detail
                            </button>
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
    @endif

    <!-- Empty State -->
    @if($spds->count() === 0 && $purchases->count() === 0 && $vendorPayments->count() === 0)
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-12 text-center">
        <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="mb-2 text-base font-semibold text-slate-600">Tidak Ada Pengajuan</div>
        <p class="text-slate-500 text-sm">Tidak ada pengajuan pembayaran yang perlu disetujui.</p>
    </div>
    @endif
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
    
    const baseUrl = '{{ url("/") }}';
    let actionUrl = '';
    if (type === 'spd') {
        actionUrl = baseUrl + '/admin/approvals/payments/spd/' + id + '/reject';
    } else if (type === 'purchase') {
        actionUrl = baseUrl + '/admin/approvals/payments/purchases/' + id + '/reject';
    } else if (type === 'vendor-payment') {
        actionUrl = baseUrl + '/admin/approvals/payments/vendor-payments/' + id + '/reject';
    }
    
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

