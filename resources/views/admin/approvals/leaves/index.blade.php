@extends('layouts.app')

@section('title', 'Approval Cuti & Izin')
@section('page-title', 'Approval Cuti & Izin')
@section('page-subtitle', 'Review & Persetujuan Leave Request')

@section('content')
<div class="py-8">
  <div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Approval Cuti & Izin</h1>
    <p class="text-xs text-gray-500 mt-1">Review dan setujui pengajuan cuti pengguna</p>
  </div>

  <!-- Filter Dropdown -->
  <div class="mb-6">
    <label class="block text-xs font-medium text-slate-700 mb-2">Filter by Status:</label>
    <select onchange="window.location.href=this.value" 
            class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 bg-white hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
      <option value="{{ route('admin.approvals.leaves') }}" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
      <option value="{{ route('admin.approvals.leaves', ['status' => 'pending']) }}" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
      <option value="{{ route('admin.approvals.leaves', ['status' => 'approved']) }}" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
      <option value="{{ route('admin.approvals.leaves', ['status' => 'rejected']) }}" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
    </select>
  </div>

  <!-- Table -->
  <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
    <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
      <h2 class="text-base font-semibold text-white">Daftar Permohonan Cuti & Izin</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Pengguna</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Jenis Cuti</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Tanggal</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Durasi</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Alasan</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Status</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($leaveList as $leave)
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="px-4 py-3">
                <div class="text-xs font-semibold text-slate-900">{{ $leave->user->name }}</div>
                <div class="text-xs text-slate-500">{{ $leave->user->employee_id ?? '-' }}</div>
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-50 text-blue-700">
                  {{ $leave->leaveType->name }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="text-xs text-slate-900">{{ $leave->start_date->format('d M Y') }}</div>
                <div class="text-xs text-slate-500">s/d {{ $leave->end_date->format('d M Y') }}</div>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="text-xs font-semibold text-slate-900">{{ $leave->total_days }} hari</span>
              </td>
              <td class="px-4 py-3">
                <div class="text-xs text-slate-600 max-w-xs truncate">{{ $leave->reason }}</div>
              </td>
              <td class="px-4 py-3 text-center">
                @if($leave->isPending())
                  <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                @elseif($leave->isApproved())
                  <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">✓ Approved</span>
                @else
                  <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">✗ Rejected</span>
                @endif
              </td>
              <td class="px-4 py-3 text-center">
                <button onclick="openLeaveDetailModal({{ $leave->id }})" class="text-xs text-white font-medium px-3 py-1.5 rounded-lg transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e3a5f'" onmouseout="this.style.backgroundColor='#0a1628'">
                  Detail
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-12 text-center">
                <div class="flex flex-col items-center text-slate-400">
                  <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  <div class="text-sm font-medium text-slate-600">Tidak ada permohonan cuti</div>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    @if($leaveList->hasPages())
      <div class="px-6 py-4 border-t border-slate-200">
        {{ $leaveList->links() }}
      </div>
    @endif
  </div>
</div>

<!-- Detail & Approval Modal -->
@include('admin.approvals.leaves.detail-modal')

<!-- Reject Modal -->
@include('admin.approvals.leaves.reject-modal')
@endsection

