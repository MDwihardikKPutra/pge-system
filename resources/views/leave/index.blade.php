@extends('layouts.app')

@php
    $isAdmin = auth()->user()->hasRole('admin');
    $routePrefix = $isAdmin ? 'admin.leaves' : 'user.leaves';
@endphp

@section('title', $isAdmin ? 'Cuti & Izin' : 'Cuti & Izin Saya')
@section('page-title', 'Cuti & Izin')
@section('page-subtitle', $isAdmin ? 'Kelola Pengajuan Cuti & Izin' : 'Kelola Pengajuan Cuti & Izin Anda')

@section('content')
<div class="py-4" x-data="leavePreview">
  <div x-data="{ 
    openLeaveModal(mode = 'create', leave = null) {
      window.dispatchEvent(new CustomEvent('open-leave-modal', {
        detail: { mode: mode, leave: leave }
      }));
    }
  }">
    <!-- Table with Integrated Filters (EAR Style) -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <!-- Header with Filters -->
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-white">Cuti & Izin</h2>
                    <p class="text-xs text-gray-300">Kelola pengajuan cuti dan izin Anda</p>
                </div>
                <button @click="openLeaveModal()" class="px-3 py-1.5 text-xs font-medium text-white rounded transition-colors bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajukan Cuti/Izin Baru
                </button>
            </div>
            
            <!-- Status Filter -->
            <div class="flex gap-2">
                <a href="{{ route($routePrefix . '.index') }}" class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Semua
                </a>
                <a href="{{ route($routePrefix . '.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('status') === 'pending' ? 'bg-yellow-500 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Pending
                </a>
                <a href="{{ route($routePrefix . '.index', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('status') === 'approved' ? 'bg-green-500 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Disetujui
                </a>
                <a href="{{ route($routePrefix . '.index', ['status' => 'rejected']) }}" class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ request('status') === 'rejected' ? 'bg-red-500 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600' }}">
                    Ditolak
                </a>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 200px);">
            @if($leaves->count() > 0)
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        @if($isAdmin)
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Pengguna</th>
                        @endif
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Tanggal Pengajuan</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Jenis</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Dari - Sampai</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Durasi</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Alasan</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
          @foreach($leaves as $leave)
          <tr class="hover:bg-slate-50">
            @if($isAdmin)
            <td class="px-4 py-3">
                <div class="text-xs font-medium text-slate-900">{{ $leave->user->name }}</div>
                <div class="text-xs text-slate-500">{{ $leave->user->email }}</div>
            </td>
            @endif
            <td class="px-4 py-3 text-xs text-slate-600">{{ $leave->created_at->format('d/m/Y') }}</td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-50 text-blue-700">
                {{ $leave->leaveType->name ?? '-' }}
              </span>
            </td>
            <td class="px-4 py-3 text-xs text-slate-600">
              {{ $leave->start_date->format('d/m/Y') }} - {{ $leave->end_date->format('d/m/Y') }}
            </td>
            <td class="px-4 py-3 text-center">
              <span class="text-xs text-slate-900">{{ $leave->total_days }} hari</span>
            </td>
            <td class="px-4 py-3 text-xs text-slate-600">{{ \Illuminate\Support\Str::limit($leave->reason, 40) }}</td>
            <td class="px-4 py-3 text-center">
              @if($leave->isPending())
                <span class="badge-minimal badge-warning">
                  Pending
                </span>
              @elseif($leave->isApproved())
                <span class="badge-minimal badge-success">
                  ✓ Disetujui
                </span>
              @elseif($leave->isRejected())
                <span class="badge-minimal badge-error">
                  ✗ Ditolak
                </span>
              @endif
            </td>
            <td class="px-4 py-3 text-center">
              <div class="flex items-center justify-center gap-2">
                <button @click="window.dispatchEvent(new CustomEvent('open-preview-modal', { detail: { leaveId: {{ $leave->id }} } }))" class="text-blue-600 hover:text-blue-800 text-xs font-medium transition-colors">
                  Preview
                </button>
                @if($leave->isPending())
                <button @click="openLeaveModal('edit', {
                  id: {{ $leave->id }},
                  leave_number: '{{ $leave->leave_number }}',
                  leave_type_id: {{ $leave->leave_type_id }},
                  start_date: '{{ $leave->start_date->format('Y-m-d') }}',
                  end_date: '{{ $leave->end_date->format('Y-m-d') }}',
                  total_days: {{ $leave->total_days }},
                  reason: {{ json_encode($leave->reason) }},
                  routePrefix: '{{ $routePrefix }}'
                })" class="text-blue-600 hover:text-blue-800 text-xs font-medium transition-colors">
                  Edit
                </button>
                @endif
              </div>
            </td>
          </tr>
                    @endforeach
                </tbody>
            </table>
            @if($leaves->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $leaves->links() }}
            </div>
            @endif
            @else
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm font-medium text-gray-900 mb-0.5">Belum Ada Pengajuan Cuti/Izin</p>
                <p class="text-xs text-gray-500 mb-6">Anda belum mengajukan cuti atau izin. Buat pengajuan pertama Anda untuk memulai.</p>
                <button @click="openLeaveModal()" class="px-3 py-1.5 text-xs font-medium text-white rounded transition-colors bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajukan Cuti/Izin Pertama
                </button>
            </div>
            @endif
        </div>
    </div>
  </div>

  <!-- Leave Request Modal -->
  <div x-data="{ 
    showModal: false, 
    modalMode: 'create', 
    currentLeave: null,
    leaveTypes: @js($leaveTypes ?? []),
    closeModal() {
      this.showModal = false;
      this.currentLeave = null;
    }
  }" 
       @open-leave-modal.window="showModal = true; modalMode = $event.detail.mode; currentLeave = $event.detail.leave">
    @include('leave.modal', ['routePrefix' => $routePrefix])
  </div>
  
  <!-- Preview Modal -->
  @include('leave.preview-modal')
</div>

@push('alpine-init')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('leavePreview', () => ({
        showPreviewModal: false,
        previewData: null,
        
        init() {
            // Listen for preview modal events from nested scopes
            window.addEventListener('open-preview-modal', (event) => {
                this.openPreviewModal(event.detail.leaveId);
            });
        },
        
        openLeaveModal(mode = 'create', leave = null) {
            window.dispatchEvent(new CustomEvent('open-leave-modal', {
                detail: { mode: mode, leave: leave }
            }));
        },
        
        async openPreviewModal(leaveId) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const routePrefix = '{{ $routePrefix }}';
                const url = routePrefix === 'admin.leaves' ? `/admin/leaves/${leaveId}` : `/user/leaves/${leaveId}`;
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken || ''
                    },
                    credentials: 'same-origin'
                });
                
                if (response.status === 419) {
                    // CSRF token expired, reload page
                    alert('Session expired. Halaman akan di-refresh.');
                    window.location.reload();
                    return;
                }
                
                if (response.status === 403) {
                    alert('Anda tidak memiliki akses untuk melihat data ini.');
                    return;
                }
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Preview data received:', data); // Debug log
                
                if (data.success && data.leave) {
                    this.previewData = data.leave;
                    this.showPreviewModal = true;
                } else if (data.leave) {
                    // Fallback for old response format
                    this.previewData = data.leave;
                    this.showPreviewModal = true;
                } else {
                    console.error('No leave data in response:', data);
                    alert('Data tidak ditemukan dalam response.');
                }
            } catch (error) {
                console.error('Error fetching leave:', error);
                alert('Gagal memuat data cuti/izin: ' + error.message);
            }
        },
        
        closePreviewModal() {
            this.showPreviewModal = false;
            this.previewData = null;
        }
    }));
});
</script>
@endpush
@endsection

