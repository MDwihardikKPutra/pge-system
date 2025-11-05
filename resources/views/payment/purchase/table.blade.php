@if($purchases->count() > 0)
<div class="bg-white border border-slate-200 rounded-lg shadow-sm">
    <div class="px-6 py-4 border-b border-slate-200 bg-green-600">
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
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $purchase->type === 'Barang' ? 'bg-blue-50 text-blue-700' : ($purchase->type === 'Jasa' ? 'bg-purple-50 text-purple-700' : 'bg-gray-50 text-gray-700') }}">
                            {{ $purchase->type }}
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
                            <button onclick="if(window.purchaseFormComponent) window.purchaseFormComponent.openEditModal({{ $purchase->id }})" 
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium transition-colors"
                                    @if(!$purchase->isPending()) disabled @endif>
                                Edit
                            </button>
                            @if($purchase->isPending())
                            <form action="{{ route($routePrefix . '.purchases.destroy', $purchase) }}" method="POST" class="inline" 
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
<div class="bg-white border border-slate-200 rounded-lg shadow-sm p-12 text-center">
    <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
    </svg>
    <div class="mb-2 text-base font-semibold text-slate-600">Belum Ada Pembelian</div>
    <p class="text-slate-500 text-sm">Anda belum mengajukan pembelian. Klik tombol "Ajukan Pembelian" di atas untuk memulai.</p>
</div>
@endif


