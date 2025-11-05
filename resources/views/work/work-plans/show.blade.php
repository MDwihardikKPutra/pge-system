@extends('layouts.app')

@section('title', 'Detail Rencana Kerja')
@section('page-title', 'Detail Rencana Kerja')
@section('page-subtitle', $workPlan->work_plan_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('user.work-plans.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $workPlan->title }}</h1>
                <p class="text-sm text-slate-600">Nomor: {{ $workPlan->work_plan_number }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('user.work-plans.edit', $workPlan) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
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
                    <dt class="text-sm font-medium text-gray-500">Tanggal Rencana</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $workPlan->plan_date->format('d F Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Lokasi Kerja</dt>
                    <dd class="mt-1">
                        @if($workPlan->work_location)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $workPlan->work_location->badgeColor() }}">
                                {{ $workPlan->work_location->icon() }} {{ $workPlan->work_location->label() }}
                            </span>
                        @else
                            <span class="text-sm text-gray-900">-</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Durasi Kerja</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $workPlan->planned_duration_hours }} jam</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Department</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $workPlan->department ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Description -->
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Deskripsi</h2>
            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $workPlan->description }}</p>
        </div>

        <!-- Expected Output -->
        @if($workPlan->expected_output)
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Output yang Diharapkan</h2>
            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $workPlan->expected_output }}</p>
        </div>
        @endif

        <!-- Related Realizations -->
        @if($workPlan->realizations->count() > 0)
        <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Realisasi Terkait</h2>
            <div class="space-y-2">
                @foreach($workPlan->realizations as $realization)
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $realization->title }}</p>
                                <p class="text-xs text-gray-500">{{ $realization->realization_date->format('d M Y') }}</p>
                            </div>
                            <a href="{{ route('user.work-realizations.show', $realization) }}" class="text-sm text-blue-600 hover:text-blue-900">
                                Lihat
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection


