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
                            <button onclick="if(window.spdFormComponent) window.spdFormComponent.openEditModal({{ $spd->id }})" 
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium transition-colors"
                                    @if(!$spd->isPending()) disabled @endif>
                                Edit
                            </button>
                            @if($spd->isPending())
                            <form action="{{ route($routePrefix . '.spd.destroy', $spd) }}" method="POST" class="inline" 
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
<div class="bg-white border border-slate-200 rounded-lg shadow-sm p-12 text-center">
    <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <div class="mb-2 text-base font-semibold text-slate-600">Belum Ada SPD</div>
    <p class="text-slate-500 text-sm">Anda belum mengajukan SPD. Klik tombol "Ajukan SPD" di atas untuk memulai.</p>
</div>
@endif

