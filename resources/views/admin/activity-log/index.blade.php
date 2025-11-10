@extends('layouts.app')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log')
@section('page-subtitle', 'Semua Aktivitas Sistem')

@section('content')
<div class="py-4">
    <!-- Table with Integrated Filters (EAR Style) -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <!-- Header with Filters -->
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-white">Activity Log</h2>
                    <p class="text-xs text-gray-300">Semua Aktivitas Sistem</p>
                </div>
            </div>
            
            <!-- Compact Filters -->
            <form method="GET" action="{{ route('admin.activity-log.index') }}" class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-300 whitespace-nowrap">Cari:</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari aktivitas, user..."
                               class="text-xs px-3 py-1.5 pl-8 rounded border border-gray-600 bg-slate-800 text-white placeholder-gray-400 focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                               style="min-width: 200px;">
                        <svg class="w-4 h-4 absolute left-2.5 top-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-300 whitespace-nowrap">User:</label>
                    <select name="user_id" 
                            class="text-xs px-2 py-1.5 rounded border border-gray-600 bg-slate-800 text-white focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                            style="min-width: 150px;">
                        <option value="">Semua User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-300 whitespace-nowrap">Action:</label>
                    <select name="action" 
                            class="text-xs px-2 py-1.5 rounded border border-gray-600 bg-slate-800 text-white focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                            style="min-width: 120px;">
                        <option value="">Semua Action</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ \App\Helpers\LogHelper::getActionLabel($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-300 whitespace-nowrap">Tipe:</label>
                    <select name="model_type" 
                            class="text-xs px-2 py-1.5 rounded border border-gray-600 bg-slate-800 text-white focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                            style="min-width: 150px;">
                        <option value="">Semua Tipe</option>
                        @foreach($modelTypes as $modelType)
                            <option value="{{ $modelType }}" {{ request('model_type') == $modelType ? 'selected' : '' }}>
                                {{ class_basename($modelType) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-300 whitespace-nowrap">Dari:</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="text-xs px-2 py-1.5 rounded border border-gray-600 bg-slate-800 text-white focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                           style="min-width: 120px;">
                </div>
                
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-300 whitespace-nowrap">Sampai:</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="text-xs px-2 py-1.5 rounded border border-gray-600 bg-slate-800 text-white focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                           style="min-width: 120px;">
                </div>
                
                <div class="flex items-center gap-2">
                    <button type="submit" 
                            class="px-3 py-1.5 text-xs font-medium text-white rounded transition-colors bg-blue-600 hover:bg-blue-700">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'user_id', 'action', 'model_type', 'date_from', 'date_to']))
                    <a href="{{ route('admin.activity-log.index') }}" 
                       class="px-3 py-1.5 text-xs font-medium text-gray-300 bg-slate-700 hover:bg-slate-600 rounded transition-colors">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 200px);">
            @if($activityLogs->count() > 0)
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Tanggal & Waktu</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">User</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Action</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Model</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($activityLogs as $log)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-900">
                                <div>{{ $log->created_at->format('d M Y') }}</div>
                                <div class="text-slate-500">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($log->user)
                                    <div class="text-xs font-medium text-slate-900">{{ $log->user->name }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $log->user->email }}</div>
                                @else
                                    <span class="text-xs text-slate-400">System</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ \App\Helpers\LogHelper::getActionBadgeClass($log->action) }}">
                                    {{ \App\Helpers\LogHelper::getActionLabel($log->action) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-xs text-slate-900">{{ $log->description }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($log->model_type)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ class_basename($log->model_type) }}
                                    </span>
                                    @if($log->model_id)
                                        <span class="text-xs text-slate-500 ml-1">#{{ $log->model_id }}</span>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if($activityLogs->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                <div class="flex items-center justify-between">
                    <div class="text-xs text-slate-600">
                        Menampilkan {{ $activityLogs->firstItem() ?? 0 }} - {{ $activityLogs->lastItem() ?? 0 }} dari {{ $activityLogs->total() }} aktivitas
                    </div>
                    <div class="flex items-center gap-2">
                        {{ $activityLogs->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
            @endif
            
            @else
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-medium text-gray-900 mb-0.5">Belum ada activity log</p>
                <p class="text-xs text-gray-500">
                    @if(request()->hasAny(['search', 'user_id', 'action', 'model_type', 'date_from', 'date_to']))
                        Tidak ada aktivitas yang ditemukan untuk filter yang dipilih.
                    @else
                        Belum ada aktivitas yang tercatat di sistem.
                    @endif
                </p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
