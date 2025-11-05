@extends('layouts.app')

@section('title', 'Edit Pengajuan Cuti/Izin')
@section('page-title', 'Edit Cuti/Izin')
@section('page-subtitle', 'Ubah Pengajuan Cuti & Izin')

@section('content')
<div class="py-8 max-w-3xl mx-auto">
  <form action="{{ route('user.leaves.update', $leave) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="card">
      <div class="card-header-blue">
        <h2 class="text-lg font-semibold">Edit Pengajuan Cuti/Izin</h2>
      </div>
      
      <div class="p-6 space-y-4">
        <!-- Leave Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Cuti/Izin <span class="text-red-500">*</span></label>
          <select name="leave_type_id" required class="input-primary">
            <option value="">Pilih Jenis</option>
            @foreach($leaveTypes as $type)
              <option value="{{ $type->id }}" {{ old('leave_type_id', $leave->leave_type_id) == $type->id ? 'selected' : '' }}>
                {{ $type->name }}
              </option>
            @endforeach
          </select>
          @error('leave_type_id')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Date Range -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
            <input type="date" name="start_date" value="{{ old('start_date', $leave->start_date->format('Y-m-d')) }}" required class="input-primary">
            @error('start_date')
              <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai <span class="text-red-500">*</span></label>
            <input type="date" name="end_date" value="{{ old('end_date', $leave->end_date->format('Y-m-d')) }}" required class="input-primary">
            @error('end_date')
              <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <!-- Reason -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Alasan <span class="text-red-500">*</span></label>
          <textarea name="reason" rows="4" required class="input-primary" placeholder="Jelaskan alasan pengajuan cuti/izin...">{{ old('reason', $leave->reason) }}</textarea>
          @error('reason')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Current Attachment -->
        @if($leave->attachment_path)
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Dokumen Saat Ini</label>
          <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <span class="text-sm text-gray-700">{{ basename($leave->attachment_path) }}</span>
            <a href="{{ \Storage::url($leave->attachment_path) }}" target="_blank" class="text-blue-600 hover:text-blue-700 text-xs font-medium ml-auto">Lihat</a>
          </div>
        </div>
        @endif

        <!-- New Documents -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Ganti Dokumen (Opsional)</label>
          <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png" class="input-primary">
          <p class="text-xs text-gray-500 mt-1">Upload surat dokter atau dokumen pendukung lainnya (PDF, JPG, PNG, max 5MB)</p>
          @error('attachment')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
        <a href="{{ route('user.leaves.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary-dark">Update Pengajuan</button>
      </div>
    </div>
  </form>
</div>
@endsection


