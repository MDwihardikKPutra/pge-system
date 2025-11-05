@extends('layouts.app')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log')
@section('page-subtitle', 'Semua Aktivitas Sistem')

@section('content')
<div class="py-8">
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <h2 class="text-base font-semibold text-white">Activity Log - Semua Aktivitas</h2>
                <form method="GET" action="{{ route('admin.activity-log.index') }}" class="flex flex-wrap items-center gap-2">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." class="pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-48">
                        <svg class="w-4 h-4 absolute left-2.5 top-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <select name="user_id" class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <select name="action" class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Action</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                        @endforeach
                    </select>
                    <select name="model_type" class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Tipe</option>
                        @foreach($modelTypes as $modelType)
                            <option value="{{ $modelType }}" {{ request('model_type') == $modelType ? 'selected' : '' }}>{{ class_basename($modelType) }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Dari" class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-32">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="Sampai" class="px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-32">
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white rounded transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">Filter</button>
                    @if(request()->hasAny(['search', 'user_id', 'action', 'model_type', 'date_from', 'date_to']))
                        <a href="{{ route('admin.activity-log.index') }}" class="px-3 py-1.5 text-xs font-medium border border-gray-300 text-gray-700 rounded hover:bg-gray-100 transition-colors">Reset</a>
                    @endif
                </form>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Tanggal & Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Deskripsi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Model</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($activityLogs as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-slate-700">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-slate-900">{{ $log->user->name ?? 'System' }}</span>
                                    <span class="text-xs text-slate-500">({{ $log->user->email ?? 'N/A' }})</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    {{ $log->action === 'created' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $log->action === 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $log->action === 'deleted' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $log->action === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $log->action === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ !in_array($log->action, ['created', 'updated', 'deleted', 'approved', 'rejected']) ? 'bg-slate-100 text-slate-800' : '' }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $log->description }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                @if($log->model_type)
                                    <span class="text-xs font-mono">{{ class_basename($log->model_type) }}</span>
                                    @if($log->model_id)
                                        <span class="text-xs text-slate-500">#{{ $log->model_id }}</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700 text-xs">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                <p class="text-sm">Belum ada activity log</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $activityLogs->links() }}
        </div>
    </div>
</div>
@endsection
