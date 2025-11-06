@extends('layouts.app')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log')
@section('page-subtitle', 'Aktivitas Saya')

@section('content')
<div class="py-4">
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-base font-semibold text-white">Activity Log</h2>
                    <p class="text-xs text-gray-300">Aktivitas Saya</p>
                </div>
            </div>
            <form method="GET" action="{{ route('user.activity-log.index') }}" class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." class="pl-8 pr-3 py-1.5 text-xs border border-gray-600 bg-slate-800 text-white placeholder-gray-400 rounded focus:ring-1 focus:ring-blue-400 focus:border-blue-400" style="min-width: 150px;">
                    <svg class="w-4 h-4 absolute left-2.5 top-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                    <select name="action" class="px-2 py-1.5 text-xs border border-gray-600 bg-slate-800 text-white rounded focus:ring-1 focus:ring-blue-400 focus:border-blue-400" style="min-width: 120px;">
                        <option value="">Semua Action</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                        @endforeach
                    </select>
                    <select name="model_type" class="px-2 py-1.5 text-xs border border-gray-600 bg-slate-800 text-white rounded focus:ring-1 focus:ring-blue-400 focus:border-blue-400" style="min-width: 120px;">
                        <option value="">Semua Tipe</option>
                        @foreach($modelTypes as $modelType)
                            <option value="{{ $modelType }}" {{ request('model_type') == $modelType ? 'selected' : '' }}>{{ class_basename($modelType) }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Dari" class="px-2 py-1.5 text-xs border border-gray-600 bg-slate-800 text-white rounded focus:ring-1 focus:ring-blue-400 focus:border-blue-400" style="min-width: 120px;">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="Sampai" class="px-2 py-1.5 text-xs border border-gray-600 bg-slate-800 text-white rounded focus:ring-1 focus:ring-blue-400 focus:border-blue-400" style="min-width: 120px;">
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white rounded transition-colors bg-blue-600 hover:bg-blue-700">Filter</button>
                    @if(request()->hasAny(['search', 'action', 'model_type', 'date_from', 'date_to']))
                        <a href="{{ route('user.activity-log.index') }}" class="px-3 py-1.5 text-xs font-medium text-gray-300 bg-slate-700 hover:bg-slate-600 rounded transition-colors">Reset</a>
                    @endif
                </form>
        </div>
        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 200px);">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Tanggal & Waktu</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Action</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Model</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($activityLogs as $log)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-xs text-slate-900">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-xs font-medium
                                    {{ $log->action === 'created' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $log->action === 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $log->action === 'deleted' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $log->action === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $log->action === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ !in_array($log->action, ['created', 'updated', 'deleted', 'approved', 'rejected']) ? 'bg-slate-100 text-slate-800' : '' }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-900">{{ $log->description }}</td>
                            <td class="px-4 py-3 text-xs text-slate-900">
                                @if($log->model_type)
                                    <span class="font-mono">{{ class_basename($log->model_type) }}</span>
                                    @if($log->model_id)
                                        <span class="text-slate-500">#{{ $log->model_id }}</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                <p class="text-sm">Belum ada activity log</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($activityLogs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $activityLogs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
