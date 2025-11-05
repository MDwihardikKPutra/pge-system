@extends('layouts.app')

@section('title', 'Buat Rencana Kerja')
@section('page-title', 'Buat Rencana Kerja')
@section('page-subtitle', 'Isi detail rencana kerja Anda')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('user.work-plans.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Buat Rencana Kerja</h1>
    </div>

    <form action="{{ route('user.work-plans.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Basic Information Card -->
        <div class="bg-white shadow-sm rounded-lg border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="plan_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Rencana <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="plan_date" name="plan_date" value="{{ old('plan_date') }}" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('plan_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="work_location" class="block text-sm font-medium text-gray-700 mb-1">
                        Lokasi Kerja <span class="text-red-500">*</span>
                    </label>
                    <select id="work_location" name="work_location" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Lokasi</option>
                        <option value="office" {{ old('work_location') == 'office' ? 'selected' : '' }}>Office (Kantor)</option>
                        <option value="site" {{ old('work_location') == 'site' ? 'selected' : '' }}>Site (Lapangan)</option>
                        <option value="wfh" {{ old('work_location') == 'wfh' ? 'selected' : '' }}>WFH (Work From Home)</option>
                        <option value="wfa" {{ old('work_location') == 'wfa' ? 'selected' : '' }}>WFA (Work From Anywhere)</option>
                    </select>
                    @error('work_location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label for="planned_duration_hours" class="block text-sm font-medium text-gray-700 mb-1">
                    Durasi Kerja (Jam) <span class="text-red-500">*</span>
                </label>
                <input type="number" id="planned_duration_hours" name="planned_duration_hours" value="{{ old('planned_duration_hours', 8) }}" 
                    required min="0.5" max="24" step="0.5"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Estimasi waktu yang akan dihabiskan (0.5 - 24 jam)</p>
                @error('planned_duration_hours')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Description Card -->
        <div class="bg-white shadow-sm rounded-lg border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Deskripsi & Tujuan</h2>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi Rencana <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="4" required
                    placeholder="Jelaskan secara detail rencana kerja Anda..."
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <label for="expected_output" class="block text-sm font-medium text-gray-700 mb-1">
                    Output yang Diharapkan (Opsional)
                </label>
                <textarea id="expected_output" name="expected_output" rows="3"
                    placeholder="Apa yang diharapkan sebagai hasil dari rencana kerja ini?"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('expected_output') }}</textarea>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between gap-3 pt-4">
            <a href="{{ route('user.work-plans.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 text-white rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Simpan Rencana Kerja
            </button>
        </div>
    </form>
</div>
@endsection

