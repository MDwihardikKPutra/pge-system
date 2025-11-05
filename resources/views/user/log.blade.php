@extends('layouts.app')

@section('title', 'Log Aktivitas')
@section('page-title', 'Log Riwayat Pengajuan')
@section('page-subtitle', 'Timeline Aktivitas & Submissions')

@section('content')
<div class="py-8">
    <div class="bg-white border rounded">
        <div class="px-6 py-4 border-b style="background-color: #0a1628;"">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <h2 class="text-base font-semibold text-white">Timeline Aktivitas</h2>
                <form method="GET" action="{{ route('user.log') }}" class="flex flex-wrap items-center gap-2">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." class="pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-48">
                        <svg class="w-4 h-4 absolute left-2.5 top-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <select name="type" class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Jenis</option>
                        <option value="SPD" {{ request('type') == 'SPD' ? 'selected' : '' }}>SPD</option>
                        <option value="Purchase" {{ request('type') == 'Purchase' ? 'selected' : '' }}>Pembelian</option>
                        <option value="Vendor Payment" {{ request('type') == 'Vendor Payment' ? 'selected' : '' }}>Pembayaran Vendor</option>
                        <option value="Leave" {{ request('type') == 'Leave' ? 'selected' : '' }}>Cuti & Izin</option>
                    </select>
                    <select name="status" class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Dari" class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-32">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="Sampai" class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-32">
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-white text-blue-600 rounded hover:bg-gray-100 transition-colors">Filter</button>
                    @if(request()->hasAny(['search', 'type', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('user.log') }}" class="px-3 py-1.5 text-xs font-medium border border-gray-300 text-gray-700 rounded hover:bg-gray-100 transition-colors">Reset</a>
                    @endif
                    @if(request()->hasAny(['search', 'type', 'status', 'date_from', 'date_to']))
                        <span class="text-xs text-white">{{ $allActivities->count() }} hasil</span>
                    @endif
                </form>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="style="background-color: #0a1628;"">
                    <tr>
                        <th class="py-2 px-3 text-left font-medium text-white">Tanggal</th>
                        <th class="py-2 px-3 text-left font-medium text-white">Jenis</th>
                        <th class="py-2 px-3 text-left font-medium text-white">Deskripsi</th>
                        <th class="py-2 px-3 text-left font-medium text-white">Jumlah</th>
                        <th class="py-2 px-3 text-left font-medium text-white">Status</th>
                        <th class="py-2 px-3 text-left font-medium text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allActivities as $activity)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-3 text-gray-700">{{ \Carbon\Carbon::parse($activity['date'])->format('d M Y H:i') }}</td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    {{ $activity['type'] === 'SPD' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $activity['type'] === 'Purchase' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $activity['type'] === 'Vendor Payment' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                    {{ $activity['type'] === 'Leave' ? 'bg-green-100 text-green-800' : '' }}">{{ $activity['type'] }}</span>
                            </td>
                            <td class="py-2 px-3 text-gray-700">{{ $activity['description'] }}</td>
                            <td class="py-2 px-3 text-gray-700">
                                @if($activity['amount'] > 0)
                                    Rp {{ number_format($activity['amount'], 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    {{ $activity['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $activity['status'] === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $activity['status'] === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($activity['status']) }}
                                </span>
                            </td>
                            <td class="py-2 px-3">
                                <a href="{{ $activity['url'] }}" class="text-blue-600 hover:text-blue-700 text-xs">Detail →</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">Belum ada aktivitas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
