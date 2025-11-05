@extends('layouts.app')

@section('title', 'Ajukan Cuti/Izin Baru')
@section('page-title', 'Ajukan Cuti/Izin')
@section('page-subtitle', 'Form Pengajuan Cuti & Izin')

@section('content')
<div class="py-8 max-w-3xl mx-auto">
  <form action="{{ route('user.leaves.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="card">
      <div class="card-header-blue">
        <h2 class="text-lg font-semibold">Form Pengajuan Cuti/Izin</h2>
      </div>
      
      <div class="p-6 space-y-4">
        <!-- Leave Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Cuti/Izin <span class="text-red-500">*</span></label>
          <select name="leave_type_id" required class="input-primary">
            <option value="">Pilih Jenis</option>
            @foreach($leaveTypes as $type)
              <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
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
            <input type="date" name="start_date" value="{{ old('start_date') }}" required class="input-primary">
            @error('start_date')
              <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai <span class="text-red-500">*</span></label>
            <input type="date" name="end_date" value="{{ old('end_date') }}" required class="input-primary">
            @error('end_date')
              <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <!-- Reason -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Alasan <span class="text-red-500">*</span></label>
          <textarea name="reason" rows="4" required class="input-primary" placeholder="Jelaskan alasan pengajuan cuti/izin...">{{ old('reason') }}</textarea>
          @error('reason')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Documents -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Dokumen Pendukung (Opsional)</label>
          <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png" class="input-primary">
          <p class="text-xs text-gray-500 mt-1">Upload surat dokter atau dokumen pendukung lainnya (PDF, JPG, PNG, max 5MB)</p>
          @error('attachment')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
        <a href="{{ route('user.leaves.index') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary-dark">Ajukan Cuti/Izin</button>
      </div>
    </div>
  </form>
</div>
@endsection


