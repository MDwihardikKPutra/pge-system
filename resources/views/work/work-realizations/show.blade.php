@extends('layouts.app')

@section('title', 'Detail Realisasi Kerja')
@section('page-title', 'Detail Realisasi Kerja')
@section('page-subtitle', $workRealization->realization_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('user.work-realizations.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $workRealization->title }}</h1>
                <p class="text-sm text-slate-600">Nomor: {{ $workRealization->realization_number }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('user.work-realizations.edit', $workRealization) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                Edit
            </a>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="bg-white shadow-sm rounded-lg border border-slate-200 p-6 space-y-6">
        <!-- Basic Information -->
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</h2>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal Realisasi</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $workRealization->realization_date->format('d F Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Lokasi Kerja</dt>
                    <dd class="mt-1">
                        @if($workRealization->work_location)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $workRealization->work_location->badgeColor() }}">
                                {{ $workRealization->work_location->icon() }} {{ $workRealization->work_location->label() }}
                            </span>
                        @else
                            <span class="text-sm text-gray-900">-</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Durasi Aktual</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $workRealization->actual_duration_hours ?? '-' }} jam</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Progress</dt>
                    <dd class="mt-1">
                        <div class="flex items-center gap-2">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $workRealization->progress_percentage }}%"></div>
                            </div>
                            <span class="text-sm text-gray-900 font-medium">{{ $workRealization->progress_percentage }}%</span>
                        </div>
                    </dd>
                </div>
                @if($workRealization->workPlan)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Rencana Kerja Terkait</dt>
                    <dd class="mt-1">
                        <a href="{{ route('user.work-plans.show', $workRealization->workPlan) }}" class="text-sm text-blue-600 hover:text-blue-700">
                            {{ $workRealization->workPlan->work_plan_number }}
                        </a>
                    </dd>
                </div>
                @endif
            </dl>
        </div>

        <!-- Description -->
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Deskripsi</h2>
            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $workRealization->description }}</p>
        </div>

        <!-- Output Description -->
        @if($workRealization->output_description)
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Deskripsi Output</h2>
            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $workRealization->output_description }}</p>
        </div>
        @endif

        <!-- Output Files -->
        @if($workRealization->output_files && count($workRealization->output_files) > 0)
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">File Output</h2>
            <div class="space-y-2">
                @foreach($workRealization->output_files as $file)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-sm text-gray-700">{{ basename($file) }}</span>
                        </div>
                        <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            Download
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

