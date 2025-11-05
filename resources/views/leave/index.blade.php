@extends('layouts.app')

@section('title', 'Cuti & Izin Saya')
@section('page-title', 'Cuti & Izin')
@section('page-subtitle', 'Kelola Pengajuan Cuti & Izin Anda')

@section('content')
<div class="py-8" x-data="leavePreview">
  <div class="flex flex-wrap items-center justify-between mb-6 gap-4" x-data="{ 
    openLeaveModal(mode = 'create', leave = null) {
      window.dispatchEvent(new CustomEvent('open-leave-modal', {
        detail: { mode: mode, leave: leave }
      }));
    }
  }">
    <div>
      <h1 class="text-xl font-bold text-gray-900">Cuti & Izin Saya</h1>
      <p class="text-xs text-gray-500 mt-1">Kelola pengajuan cuti dan izin Anda</p>
    </div>
    <button @click="openLeaveModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Ajukan Cuti/Izin Baru
    </button>
  </div>
  
  <!-- Filter Status -->
  <div class="mb-6">
    <div class="flex gap-2 flex-wrap">
      <a href="{{ route('user.leaves.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !request('status') ? 'text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}" @if(!request('status')) style="background-color: #0a1628;" @endif>
        Semua
      </a>
      <a href="{{ route('user.leaves.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'pending' ? 'bg-yellow-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
        Pending
      </a>
      <a href="{{ route('user.leaves.index', ['status' => 'approved']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'approved' ? 'bg-green-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
        Disetujui
      </a>
      <a href="{{ route('user.leaves.index', ['status' => 'rejected']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'rejected' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
        Ditolak
      </a>
    </div>
  </div>
  
  @if($leaves->count() > 0)
  <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
    <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
      <h2 class="text-base font-semibold text-white">Daftar Pengajuan Cuti & Izin ({{ $leaves->total() }})</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Tanggal Pengajuan</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Jenis</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Dari - Sampai</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Durasi</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Alasan</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Status</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($leaves as $leave)
          <tr class="hover:bg-slate-50 transition-colors">
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
              <span class="text-xs font-semibold text-slate-900">{{ $leave->total_days }} hari</span>
            </td>
            <td class="px-4 py-3 text-xs text-slate-600">{{ \Illuminate\Support\Str::limit($leave->reason, 40) }}</td>
            <td class="px-4 py-3 text-center">
              @if($leave->isPending())
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                  Pending
                </span>
              @elseif($leave->isApproved())
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                  ✓ Disetujui
                </span>
              @elseif($leave->isRejected())
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                  ✗ Ditolak
                </span>
              @endif
            </td>
            <td class="px-4 py-3 text-center">
              <div class="flex items-center justify-center gap-2">
                <button @click="openPreviewModal({{ $leave->id }})" class="text-blue-600 hover:text-blue-800 text-xs font-medium transition-colors">
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
                  reason: {{ json_encode($leave->reason) }}
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
    </div>
    
    <!-- Pagination -->
    @if($leaves->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">
      {{ $leaves->links() }}
    </div>
    @endif
  </div>
  @else
  <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-12 text-center max-w-xl mx-auto">
    <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <div class="mb-2 text-base font-semibold text-slate-600">Belum Ada Pengajuan Cuti/Izin</div>
    <p class="text-slate-500 mb-6 text-sm">Anda belum mengajukan cuti atau izin. Buat pengajuan pertama Anda untuk memulai.</p>
    <button @click="openLeaveModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors mx-auto" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Ajukan Cuti/Izin Pertama
    </button>
  </div>
  @endif

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
    @include('leave.modal')
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
        
        openLeaveModal(mode = 'create', leave = null) {
            window.dispatchEvent(new CustomEvent('open-leave-modal', {
                detail: { mode: mode, leave: leave }
            }));
        },
        
        async openPreviewModal(leaveId) {
            try {
                const response = await fetch(`/user/leaves/${leaveId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                if (data.leave) {
                    this.previewData = data.leave;
                    this.showPreviewModal = true;
                }
            } catch (error) {
                console.error('Error fetching leave:', error);
                alert('Gagal memuat data cuti/izin');
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

