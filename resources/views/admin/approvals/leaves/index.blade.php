@extends('layouts.app')

@section('title', 'Approval Cuti & Izin')
@section('page-title', 'Approval Cuti & Izin')
@section('page-subtitle', 'Review & Persetujuan Leave Request')

@section('content')
<div class="py-4">
    <!-- Table with Integrated Filters (EAR Style) -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <!-- Header with Filters -->
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-white">Approval Cuti & Izin</h2>
                    <p class="text-xs text-gray-300">Review dan setujui pengajuan cuti pengguna</p>
                </div>
            </div>
            
            <!-- Status Filter -->
            <div class="flex gap-2">
                @php
                    // Determine route based on current route name
                    $isAdmin = request()->routeIs('admin.*');
                    $indexRoute = $isAdmin ? 'admin.approvals.leaves' : 'user.leave-approvals.index';
                @endphp
                <a href="{{ route($indexRoute) }}" 
                   class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ $status === 'all' ? 'bg-blue-600 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Semua
                </a>
                <a href="{{ route($indexRoute, ['status' => 'pending']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ $status === 'pending' ? 'bg-yellow-500 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Pending
                </a>
                <a href="{{ route($indexRoute, ['status' => 'approved']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ $status === 'approved' ? 'bg-green-500 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Disetujui
                </a>
                <a href="{{ route($indexRoute, ['status' => 'rejected']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ $status === 'rejected' ? 'bg-red-500 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Ditolak
                </a>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 200px);">
            @if($leaveList->count() > 0)
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Pengguna</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Jenis Cuti</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Durasi</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Alasan</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($leaveList as $leave)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <div class="text-xs font-medium text-slate-900">{{ $leave->user->name }}</div>
                            <div class="text-xs text-slate-500">{{ $leave->user->employee_id ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                {{ $leave->leaveType->name }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-xs text-slate-900">{{ $leave->start_date->format('d M Y') }}</div>
                            <div class="text-xs text-slate-500">s/d {{ $leave->end_date->format('d M Y') }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs text-slate-900">{{ $leave->total_days }} hari</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-xs text-slate-900 max-w-xs truncate">{{ $leave->reason }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($leave->isPending())
                                <span class="badge-minimal badge-warning">Pending</span>
                            @elseif($leave->isApproved())
                                <span class="badge-minimal badge-success">✓ Disetujui</span>
                            @else
                                <span class="badge-minimal badge-error">✗ Ditolak</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="openLeaveDetailModal({{ $leave->id }})" class="text-xs text-blue-600 hover:text-blue-800 font-medium px-3 py-1.5 border border-blue-300 rounded hover:bg-blue-50 transition-colors">
                                Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm font-medium text-gray-900 mb-0.5">Tidak ada permohonan cuti</p>
                            <p class="text-xs text-gray-500">Tidak ada permohonan cuti yang perlu ditinjau</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($leaveList->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $leaveList->links() }}
            </div>
            @endif
            @else
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm font-medium text-gray-900 mb-0.5">Tidak ada permohonan cuti</p>
                <p class="text-xs text-gray-500">Tidak ada permohonan cuti yang perlu ditinjau</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Detail & Approval Modal -->
@include('admin.approvals.leaves.detail-modal')

<!-- Reject Modal -->
@include('admin.approvals.leaves.reject-modal')
@endsection

