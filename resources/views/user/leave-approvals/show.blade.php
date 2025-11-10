@extends('layouts.app')

@section('title', 'Detail Permohonan Cuti & Izin')
@section('page-title', 'Detail Permohonan Cuti & Izin')
@section('page-subtitle', 'Review & Approval')

@section('content')
<div class="py-8 w-full">
  <div class="flex flex-wrap items-end justify-between mb-6 gap-3">
    <h1 class="text-xl font-bold text-gray-900">Detail Permohonan Cuti & Izin</h1>
    <a href="{{ route('user.leave-approvals.index') }}" class="border border-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-100 text-sm">← Kembali</a>
  </div>

  <!-- Main Info Card -->
  <div class="border rounded bg-white p-5 mb-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-3 text-sm">
      <div>
        <div class="text-xs text-gray-500">Nama Pengguna</div>
        <div class="font-medium text-gray-900">{{ $leave->user->name }}</div>
        <div class="text-xs text-gray-500">{{ $leave->user->employee_id ?? '-' }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Jenis Cuti</div>
        <div class="font-semibold text-gray-900">{{ $leave->leaveType->name }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Status</div>
        <div>
          @if($leave->isPending())
            <span class="px-2 py-1 rounded text-xs font-semibold bg-yellow-100 text-yellow-700">Pending</span>
          @elseif($leave->isApproved())
            <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700">Approved</span>
          @else
            <span class="px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-700">Rejected</span>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Leave Details -->
  <div class="border rounded bg-white p-5 mb-4">
    <h3 class="font-semibold text-gray-800 mb-4 text-sm">Detail Cuti</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
      <div>
        <div class="text-xs text-gray-500 mb-1">Nomor Pengajuan</div>
        <div class="font-medium text-gray-900">{{ $leave->leave_number }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500 mb-1">Tanggal Pengajuan</div>
        <div class="text-gray-700">{{ $leave->created_at->format('d F Y, H:i') }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500 mb-1">Tanggal Mulai</div>
        <div class="font-medium text-gray-900">{{ $leave->start_date->format('d F Y') }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500 mb-1">Tanggal Selesai</div>
        <div class="font-medium text-gray-900">{{ $leave->end_date->format('d F Y') }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500 mb-1">Total Hari</div>
        <div class="font-semibold text-blue-600">{{ $leave->total_days }} hari</div>
      </div>
      <div>
      </div>
    </div>
    
    <div class="mt-4 pt-4 border-t">
      <div class="text-xs text-gray-500 mb-2">Alasan</div>
      <div class="text-sm text-gray-800 bg-gray-50 p-3 rounded">{{ $leave->reason }}</div>
    </div>

    @if($leave->attachment_path)
    <div class="mt-4 pt-4 border-t">
      <div class="text-xs text-gray-500 mb-2">Dokumen Pendukung</div>
      <div class="flex items-center justify-between bg-gray-50 p-3 rounded">
        <div class="flex items-center gap-3">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
          <span class="text-sm text-gray-700">{{ basename($leave->attachment_path) }}</span>
        </div>
        <a href="{{ \Storage::url($leave->attachment_path) }}" target="_blank" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Download</a>
      </div>
    </div>
    @endif
  </div>

  <!-- Approval Info (if approved/rejected) -->
  @if(!$leave->isPending())
    <div class="border rounded bg-white p-5 mb-4">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-800 text-sm">Informasi Approval</h3>
        @if($leave->isApproved() && $leave->pdf_path)
          <a href="{{ route(auth()->user()->hasRole('admin') ? 'admin.leaves.pdf' : 'leaves.pdf', $leave->id) }}" 
             class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Download PDF
          </a>
        @endif
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div>
          <div class="text-xs text-gray-500 mb-1">Disetujui/Ditolak Oleh</div>
          <div class="font-medium text-gray-900">{{ $leave->approvedBy->name ?? '-' }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 mb-1">Tanggal</div>
          <div class="text-gray-700">{{ $leave->approved_at ? $leave->approved_at->format('d F Y, H:i') : '-' }}</div>
        </div>
      </div>
      
      @if($leave->admin_notes)
        <div class="mt-4 pt-4 border-t">
          <div class="text-xs text-gray-500 mb-2">Catatan</div>
          <div class="text-sm text-gray-800 bg-gray-50 p-3 rounded">{{ $leave->admin_notes }}</div>
        </div>
      @endif

      @if($leave->rejection_reason)
        <div class="mt-4 pt-4 border-t">
          <div class="text-xs text-gray-500 mb-2">Alasan Penolakan</div>
          <div class="text-sm text-red-700 bg-red-50 p-3 rounded">{{ $leave->rejection_reason }}</div>
        </div>
      @endif
    </div>
  @endif

  <!-- Approval Actions (Only for pending) -->
  @if($leave->isPending())
    <div class="border rounded bg-white p-5">
      <h3 class="font-semibold text-gray-800 mb-4 text-sm">Tindakan</h3>
      <form action="{{ route('user.leave-approvals.approve', $leave) }}" method="POST" class="mb-3">
        @csrf
        <div class="mb-3">
          <label class="block text-xs text-gray-700 mb-1">Catatan (Opsional)</label>
          <textarea name="admin_notes" rows="2" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
        </div>
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded text-sm font-medium transition-colors">
          ✓ Setujui Permohonan
        </button>
      </form>

      <form action="{{ route('user.leave-approvals.reject', $leave) }}" method="POST">
        @csrf
        <div class="mb-3">
          <label class="block text-xs text-gray-700 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
          <textarea name="rejection_reason" rows="2" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Masukkan alasan penolakan..." required></textarea>
        </div>
        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded text-sm font-medium transition-colors">
          ✗ Tolak Permohonan
        </button>
      </form>
    </div>
  @endif
</div>
@endsection


