@extends('layouts.app')

@section('title', 'Detail Cuti/Izin')
@section('page-title', 'Detail Pengajuan')
@section('page-subtitle', 'Informasi Lengkap Cuti & Izin')

@section('content')
<div class="py-8 max-w-4xl mx-auto">
  <div class="mb-6">
    <a href="{{ route('user.leaves.index') }}" class="link-dark text-sm">
      ← Kembali ke Daftar
    </a>
  </div>

  <div class="card">
    <div class="card-header-blue flex items-center justify-between">
      <h2 class="text-lg font-semibold">Detail Pengajuan Cuti/Izin</h2>
      @if($leave->isPending())
        <span class="badge-warning">Pending</span>
      @elseif($leave->isApproved())
        <span class="badge-success">Disetujui</span>
      @else
        <span class="badge-danger">Ditolak</span>
      @endif
    </div>

    <div class="p-6 space-y-6">
      <!-- Basic Info -->
      <div class="grid grid-cols-2 gap-6">
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Nomor Pengajuan</label>
          <p class="text-sm font-semibold text-gray-900">{{ $leave->leave_number }}</p>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Jenis</label>
          <p class="text-sm font-semibold text-gray-900">{{ $leave->leaveType->name ?? '-' }}</p>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Durasi</label>
          <p class="text-sm font-semibold text-gray-900">{{ $leave->total_days }} hari</p>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Pengajuan</label>
          <p class="text-sm font-semibold text-gray-900">{{ $leave->created_at->format('d F Y, H:i') }}</p>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Mulai</label>
          <p class="text-sm font-semibold text-gray-900">{{ $leave->start_date->format('d F Y') }}</p>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Selesai</label>
          <p class="text-sm font-semibold text-gray-900">{{ $leave->end_date->format('d F Y') }}</p>
        </div>
      </div>

      <!-- Reason -->
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-2">Alasan</label>
        <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $leave->reason }}</p>
      </div>

      <!-- Attachment -->
      @if($leave->attachment_path)
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-2">Dokumen Pendukung</label>
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

      <!-- Approval Info -->
      @if(!$leave->isPending())
      <div class="border-t pt-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Informasi Approval</h3>
        <div class="grid grid-cols-2 gap-6">
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Disetujui/Ditolak Oleh</label>
            <p class="text-sm font-semibold text-gray-900">{{ $leave->approvedBy->name ?? '-' }}</p>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal</label>
            <p class="text-sm font-semibold text-gray-900">{{ $leave->approved_at ? $leave->approved_at->format('d F Y H:i') : '-' }}</p>
          </div>
        </div>
        @if($leave->admin_notes)
        <div class="mt-4">
          <label class="block text-xs font-medium text-gray-500 mb-2">Catatan Admin</label>
          <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $leave->admin_notes }}</p>
        </div>
        @endif
        @if($leave->rejection_reason)
        <div class="mt-4">
          <label class="block text-xs font-medium text-gray-500 mb-2">Alasan Penolakan</label>
          <p class="text-sm text-red-700 bg-red-50 p-4 rounded-lg">{{ $leave->rejection_reason }}</p>
        </div>
        @endif
      </div>
      @endif
    </div>

    <div class="px-6 py-4 bg-gray-50 border-t flex justify-between">
      <a href="{{ route('user.leaves.index') }}" class="btn-secondary">Kembali</a>
      <div class="flex gap-2">
        @if($leave->isApproved() && $leave->pdf_path)
        <a href="{{ route(auth()->user()->hasRole('admin') ? 'admin.leaves.pdf' : 'leaves.pdf', $leave->id) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Download PDF
        </a>
        @endif
        @if($leave->isPending())
        <a href="{{ route('user.leaves.edit', $leave) }}" class="btn-primary">Edit</a>
        <form action="{{ route('user.leaves.destroy', $leave) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengajuan ini?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn-danger">Hapus</button>
        </form>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection


