@if($vendorPayments->count() > 0)
<div class="bg-white border border-slate-200 rounded-lg shadow-sm">
    <div class="px-6 py-4 border-b border-slate-200 bg-purple-600">
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
                            <button onclick="if(window.vendorPaymentFormComponent) window.vendorPaymentFormComponent.openEditModal({{ $vp->id }})" 
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium transition-colors"
                                    @if(!$vp->isPending()) disabled @endif>
                                Edit
                            </button>
                            @if($vp->isPending())
                            <form action="{{ route($routePrefix . '.vendor-payments.destroy', $vp) }}" method="POST" class="inline" 
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
<div class="bg-white border border-slate-200 rounded-lg shadow-sm p-12 text-center">
    <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
    </svg>
    <div class="mb-2 text-base font-semibold text-slate-600">Belum Ada Pembayaran Vendor</div>
    <p class="text-slate-500 text-sm">Anda belum mengajukan pembayaran vendor. Klik tombol "Ajukan Pembayaran Vendor" di atas untuk memulai.</p>
</div>
@endif


