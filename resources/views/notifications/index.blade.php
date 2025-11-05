@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Semua notifikasi Anda')

@section('content')
<div class="py-4">
    <div class="bg-white rounded-lg shadow-sm border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Semua Notifikasi</h2>
                @if(auth()->user()->unreadNotifications()->count() > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-gray-300 hover:text-white">Tandai semua dibaca</button>
                </form>
                @endif
            </div>
        </div>
        
        <div class="divide-y divide-slate-200">
            @forelse($notifications as $notification)
            <div class="px-6 py-4 hover:bg-slate-50 transition-colors {{ $notification->read_at ? '' : 'bg-blue-50' }}">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-xl"
                         style="background-color: {{ $notification->data['color'] === 'green' ? '#dcfce7' : ($notification->data['color'] === 'red' ? '#fee2e2' : '#dbeafe') }};">
                        {{ $notification->data['icon'] ?? '📄' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-gray-900">{{ $notification->data['title'] ?? 'Notifikasi' }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                                <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if(!$notification->read_at)
                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                @endif
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-700">
                                        {{ $notification->read_at ? 'Tandai belum dibaca' : 'Tandai dibaca' }}
                                    </button>
                                </form>
                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus notifikasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-700">Hapus</button>
                                </form>
                            </div>
                        </div>
                        @if(isset($notification->data['url']))
                        <div class="mt-3">
                            <a href="{{ $notification->data['url'] }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat detail →</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center">
                <p class="text-sm text-gray-500">Tidak ada notifikasi</p>
            </div>
            @endforelse
        </div>
        
        @if($notifications->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</div>
@endsection


