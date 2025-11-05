@extends('layouts.app')

@section('title', 'Edit Realisasi Kerja')
@section('page-title', 'Edit Realisasi Kerja')
@section('page-subtitle', 'Ubah detail realisasi kerja')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('user.work-realizations.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Edit Realisasi Kerja</h1>
    </div>

    <form action="{{ route('user.work-realizations.update', $workRealization) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Information Card -->
        <div class="bg-white shadow-sm rounded-lg border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="realization_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Realisasi <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="realization_date" name="realization_date" value="{{ old('realization_date', $workRealization->realization_date->format('Y-m-d')) }}" required
                        class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    @error('realization_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="work_location" class="block text-sm font-medium text-gray-700 mb-1">
                        Lokasi Kerja <span class="text-red-500">*</span>
                    </label>
                    <select id="work_location" name="work_location" required class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                        <option value="">Pilih Lokasi</option>
                        <option value="office" {{ old('work_location', $workRealization->work_location?->value) == 'office' ? 'selected' : '' }}>Office (Kantor)</option>
                        <option value="site" {{ old('work_location', $workRealization->work_location?->value) == 'site' ? 'selected' : '' }}>Site (Lapangan)</option>
                        <option value="wfh" {{ old('work_location', $workRealization->work_location?->value) == 'wfh' ? 'selected' : '' }}>WFH (Work From Home)</option>
                        <option value="wfa" {{ old('work_location', $workRealization->work_location?->value) == 'wfa' ? 'selected' : '' }}>WFA (Work From Anywhere)</option>
                    </select>
                    @error('work_location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label for="work_plan_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Rencana Kerja Terkait (Opsional)
                </label>
                <select id="work_plan_id" name="work_plan_id" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">-- Pilih Rencana Kerja --</option>
                    @foreach($workPlans as $plan)
                        <option value="{{ $plan->id }}" {{ old('work_plan_id', $workRealization->work_plan_id) == $plan->id ? 'selected' : '' }}>
                            {{ $plan->work_plan_number }} - {{ $plan->plan_date->format('d M Y') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="actual_duration_hours" class="block text-sm font-medium text-gray-700 mb-1">
                        Durasi Aktual (Jam) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="actual_duration_hours" name="actual_duration_hours" value="{{ old('actual_duration_hours', $workRealization->actual_duration_hours) }}" 
                        required min="0.5" max="24" step="0.5"
                        class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    @error('actual_duration_hours')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="progress_percentage" class="block text-sm font-medium text-gray-700 mb-1">
                        Progress (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="progress_percentage" name="progress_percentage" value="{{ old('progress_percentage', $workRealization->progress_percentage) }}" 
                        required min="0" max="100"
                        class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    @error('progress_percentage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Description Card -->
        <div class="bg-white shadow-sm rounded-lg border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Deskripsi & Pencapaian</h2>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi Realisasi <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="4" required
                    placeholder="Jelaskan secara detail apa yang telah Anda kerjakan..."
                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">{{ old('description', $workRealization->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <label for="output_description" class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi Output (Opsional)
                </label>
                <textarea id="output_description" name="output_description" rows="3"
                    placeholder="Apa output atau hasil yang telah dicapai?"
                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">{{ old('output_description', $workRealization->output_description) }}</textarea>
            </div>

            @if($workRealization->output_files && count($workRealization->output_files) > 0)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    File Output Saat Ini
                </label>
                <div class="space-y-2">
                    @foreach($workRealization->output_files as $file)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                            <span class="text-sm text-gray-700">{{ basename($file) }}</span>
                            <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-700">
                                Lihat
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="mt-4">
                <label for="output_files" class="block text-sm font-medium text-gray-700 mb-1">
                    Tambah File Output Baru (Opsional)
                </label>
                <input type="file" id="output_files" name="output_files[]" multiple
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                <p class="text-xs text-gray-500 mt-1">Maksimal 10MB per file. Format: PDF, DOC, XLS, atau gambar</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between gap-3 pt-4">
            <a href="{{ route('user.work-realizations.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Perbarui Realisasi Kerja
            </button>
        </div>
    </form>
</div>
@endsection

