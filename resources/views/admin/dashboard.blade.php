@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'Ringkasan sistem & aktivitas pengguna')

@section('content')
<div class="py-4 h-[calc(100vh-140px)]">
  <!-- Welcome Section -->
  <div class="flex items-center justify-between mb-4">
    <div>
      @php
        $hour = now()->hour;
        $greeting = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
      @endphp
      <h1 class="text-xl font-semibold mb-0.5" style="color: #0a1628;">{{ $greeting }}, {{ auth()->user()->name }} 👋</h1>
      <p class="text-xs" style="color: #0a1628;">Berikut ringkasan aktivitas sistem hari ini</p>
    </div>
    <div class="flex flex-wrap gap-2 items-center">
      <a href="{{ route('admin.users.index') }}" 
         class="inline-flex items-center gap-1.5 px-3 py-1.5 text-white text-xs font-medium rounded-lg transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        Manajemen User
      </a>
      <a href="{{ route('admin.approvals.leaves') }}" 
         class="inline-flex items-center gap-1.5 px-3 py-1.5 text-white text-xs font-medium rounded-lg transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Approval Cuti
      </a>
      <a href="{{ route('admin.ear') }}" 
         class="inline-flex items-center gap-1.5 px-3 py-1.5 text-white text-xs font-medium rounded-lg transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        EAR
      </a>
    </div>
  </div>

  <div class="grid grid-cols-4 gap-4 h-[calc(100%-60px)]">
    <!-- Users & Modules Section (1/4) -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4 overflow-y-auto">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold" style="color: #0a1628;">Daftar User</h3>
        <span class="text-xs text-slate-500">{{ $users->count() ?? 0 }} user</span>
      </div>
      <div class="space-y-3">
        @forelse($users ?? [] as $userItem)
        <div class="p-3 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition-colors">
          <div class="flex items-start gap-2 mb-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-semibold flex-shrink-0" style="background-color: #0a1628;">
              {{ substr($userItem->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-semibold truncate" style="color: #0a1628;">{{ $userItem->name }}</p>
              <p class="text-[10px] text-slate-500 truncate">{{ $userItem->email }}</p>
            </div>
          </div>
          @if($userItem->modules && $userItem->modules->count() > 0)
          <div class="mt-2 flex flex-wrap gap-1">
            @foreach($userItem->modules as $module)
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] border border-slate-200 bg-slate-50" style="color: #0a1628;">
              <span class="text-xs">{{ $module->icon ?? '•' }}</span>
              <span class="truncate max-w-[80px]">{{ $module->label }}</span>
            </span>
            @endforeach
          </div>
          @else
          <p class="text-[10px] text-slate-400 mt-2">Tidak ada modul</p>
          @endif
        </div>
        @empty
        <div class="text-center py-8 text-slate-400">
          <p class="text-xs">Tidak ada user</p>
        </div>
        @endforelse
      </div>
    </div>

    <!-- Recent Activity Section (3/4) -->
    <div class="col-span-3 bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden flex flex-col">
      <div class="px-4 py-3 border-b border-slate-200" style="background-color: #0a1628;">
        <h3 class="text-sm font-semibold text-white">Recent Activity</h3>
      </div>
      <div class="flex-1 overflow-y-auto p-4 bg-white" x-data="{ showPreviewModal: false, selectedActivity: null }">
        <div class="space-y-2">
          @forelse($allRecentActivities ?? [] as $activity)
          <div @click="selectedActivity = @js($activity); showPreviewModal = true" class="block p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors bg-white cursor-pointer">
            <div class="flex items-center gap-3">
              <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-sm" style="background-color: #0a1628;">
                {{ $activity['icon'] }}
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-[10px] font-semibold" style="background-color: #0a1628;">
                    {{ substr($activity['user'] ?? 'U', 0, 1) }}
                  </div>
                  <span class="text-xs font-medium truncate" style="color: #0a1628;">{{ $activity['user'] }}</span>
                  <span class="text-xs text-slate-400">•</span>
                  <span class="text-xs text-slate-500">{{ $activity['title'] }}</span>
                </div>
                <p class="text-xs truncate" style="color: #0a1628;">{{ $activity['description'] }}</p>
                @if(isset($activity['project']) && $activity['project'] !== '-')
                <p class="text-[10px] text-slate-500 mt-0.5 truncate">📁 {{ $activity['project'] }}</p>
                @endif
              </div>
              <div class="flex-shrink-0 flex items-center gap-2">
                @if($activity['type'] === 'work-realization' && isset($activity['extra']))
                <div class="flex items-center gap-1.5">
                  <div class="w-16 rounded-full h-1.5 overflow-hidden bg-slate-200">
                    <div class="h-full rounded-full" style="width: {{ $activity['extra'] }}%; background-color: #0a1628;"></div>
                  </div>
                  <span class="text-[10px] font-semibold whitespace-nowrap" style="color: #0a1628;">{{ $activity['extra'] }}%</span>
                </div>
                @elseif(isset($activity['extra']) && in_array($activity['extra'], ['pending', 'approved', 'rejected']))
                @php
                  $statusClass = match($activity['extra']) {
                    'pending' => 'bg-yellow-100 text-yellow-700',
                    'approved' => 'bg-green-100 text-green-700',
                    'rejected' => 'bg-red-100 text-red-700',
                    default => 'bg-slate-100 text-slate-700'
                  };
                @endphp
                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium {{ $statusClass }}">
                  {{ ucfirst($activity['extra']) }}
                </span>
                @endif
                <span class="text-xs text-slate-500 whitespace-nowrap">
                  {{ \Carbon\Carbon::parse($activity['date'])->format('d M') }}
                </span>
              </div>
            </div>
          </div>
          @empty
          <div class="text-center py-8 text-slate-400">
            <p class="text-sm">Tidak ada aktivitas terbaru</p>
          </div>
          @endforelse
        </div>

        <!-- Preview Modal -->
        <div x-show="showPreviewModal" x-cloak style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" @click.away="showPreviewModal = false">
          <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showPreviewModal = false"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full" @click.stop>
              <template x-if="selectedActivity">
                <div>
                  <div class="px-6 pt-6 pb-4" style="background-color: #0a1628;">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-lg" style="background-color: rgba(255,255,255,0.2);">
                          <span x-text="selectedActivity.icon"></span>
                        </div>
                        <div>
                          <h3 class="text-lg font-semibold text-white" x-text="selectedActivity.title"></h3>
                          <p class="text-xs text-gray-300" x-text="selectedActivity.number"></p>
                        </div>
                      </div>
                      <button @click="showPreviewModal = false" class="text-gray-300 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                      </button>
                    </div>
                  </div>

                  <div class="bg-white px-6 py-4 max-h-[60vh] overflow-y-auto">
                    <!-- User Info -->
                    <div class="mb-4 pb-4 border-b border-slate-200">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-semibold" style="background-color: #0a1628;">
                          <span x-text="(selectedActivity.user || 'U').charAt(0)"></span>
                        </div>
                        <div>
                          <p class="text-sm font-semibold" style="color: #0a1628;" x-text="selectedActivity.user"></p>
                          <p class="text-xs text-slate-500" x-text="selectedActivity.user_email"></p>
                        </div>
                      </div>
                    </div>

                    <!-- Content based on type -->
                    <template x-if="selectedActivity.type === 'work-plan'">
                      <div class="space-y-3">
                        <div>
                          <label class="text-xs font-medium text-slate-500">Deskripsi</label>
                          <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.full_description || '-'"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                          <div>
                            <label class="text-xs font-medium text-slate-500">Project</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.project || '-'"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Lokasi</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.location || '-'"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Durasi</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="(selectedActivity.duration || 0) + ' jam'"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Tanggal</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="new Date(selectedActivity.date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})"></p>
                          </div>
                        </div>
                      </div>
                    </template>

                    <template x-if="selectedActivity.type === 'work-realization'">
                      <div class="space-y-3">
                        <div>
                          <label class="text-xs font-medium text-slate-500">Deskripsi</label>
                          <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.full_description || '-'"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                          <div>
                            <label class="text-xs font-medium text-slate-500">Project</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.project || '-'"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Lokasi</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.location || '-'"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Durasi</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="(selectedActivity.duration || 0) + ' jam'"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Progress</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="(selectedActivity.progress || 0) + '%'"></p>
                          </div>
                        </div>
                      </div>
                    </template>

                    <template x-if="selectedActivity.type === 'spd'">
                      <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                          <div>
                            <label class="text-xs font-medium text-slate-500">Tujuan</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.destination || '-'"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Project</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.project || '-'"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Berangkat</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.departure_date || '-'"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Kembali</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.return_date || '-'"></p>
                          </div>
                        </div>
                        <div>
                          <label class="text-xs font-medium text-slate-500">Tujuan Perjalanan</label>
                          <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.purpose || '-'"></p>
                        </div>
                        <div>
                          <label class="text-xs font-medium text-slate-500">Total Biaya</label>
                          <p class="text-sm mt-1 font-semibold" style="color: #0a1628;" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedActivity.total_cost || 0)"></p>
                        </div>
                        <div>
                          <label class="text-xs font-medium text-slate-500">Status</label>
                          <span class="inline-block mt-1 px-2 py-1 rounded text-xs font-medium" 
                                :class="selectedActivity.status === 'approved' ? 'bg-green-100 text-green-700' : (selectedActivity.status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')"
                                x-text="(selectedActivity.status || 'pending').charAt(0).toUpperCase() + (selectedActivity.status || 'pending').slice(1)"></span>
                        </div>
                        <template x-if="selectedActivity.approved_by">
                          <div>
                            <label class="text-xs font-medium text-slate-500">Approved By</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="'✅ ' + selectedActivity.approved_by + (selectedActivity.approved_at ? ' (' + selectedActivity.approved_at + ')' : '')"></p>
                          </div>
                        </template>
                      </div>
                    </template>

                    <template x-if="selectedActivity.type === 'purchase'">
                      <div class="space-y-3">
                        <div>
                          <label class="text-xs font-medium text-slate-500">Item</label>
                          <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.item_name || '-'"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                          <div>
                            <label class="text-xs font-medium text-slate-500">Project</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.project || '-'"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Jumlah</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="(selectedActivity.quantity || 0) + ' ' + (selectedActivity.unit || '')"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Harga Satuan</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedActivity.unit_price || 0)"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Total Harga</label>
                            <p class="text-sm mt-1 font-semibold" style="color: #0a1628;" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedActivity.total_price || 0)"></p>
                          </div>
                        </div>
                        <div>
                          <label class="text-xs font-medium text-slate-500">Catatan</label>
                          <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.notes || '-'"></p>
                        </div>
                        <div>
                          <label class="text-xs font-medium text-slate-500">Status</label>
                          <span class="inline-block mt-1 px-2 py-1 rounded text-xs font-medium" 
                                :class="selectedActivity.status === 'approved' ? 'bg-green-100 text-green-700' : (selectedActivity.status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')"
                                x-text="(selectedActivity.status || 'pending').charAt(0).toUpperCase() + (selectedActivity.status || 'pending').slice(1)"></span>
                        </div>
                        <template x-if="selectedActivity.approved_by">
                          <div>
                            <label class="text-xs font-medium text-slate-500">Approved By</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="'✅ ' + selectedActivity.approved_by + (selectedActivity.approved_at ? ' (' + selectedActivity.approved_at + ')' : '')"></p>
                          </div>
                        </template>
                      </div>
                    </template>

                    <template x-if="selectedActivity.type === 'vendor-payment'">
                      <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                          <div>
                            <label class="text-xs font-medium text-slate-500">Vendor</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.vendor || '-'"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Project</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.project || '-'"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">No. Invoice</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.invoice_number || '-'"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Tanggal Pembayaran</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.payment_date || '-'"></p>
                          </div>
                        </div>
                        <div>
                          <label class="text-xs font-medium text-slate-500">Deskripsi</label>
                          <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.description_text || '-'"></p>
                        </div>
                        <div>
                          <label class="text-xs font-medium text-slate-500">Jumlah</label>
                          <p class="text-sm mt-1 font-semibold" style="color: #0a1628;" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedActivity.amount || 0)"></p>
                        </div>
                        <div>
                          <label class="text-xs font-medium text-slate-500">Status</label>
                          <span class="inline-block mt-1 px-2 py-1 rounded text-xs font-medium" 
                                :class="selectedActivity.status === 'approved' ? 'bg-green-100 text-green-700' : (selectedActivity.status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')"
                                x-text="(selectedActivity.status || 'pending').charAt(0).toUpperCase() + (selectedActivity.status || 'pending').slice(1)"></span>
                        </div>
                        <template x-if="selectedActivity.approved_by">
                          <div>
                            <label class="text-xs font-medium text-slate-500">Approved By</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="'✅ ' + selectedActivity.approved_by + (selectedActivity.approved_at ? ' (' + selectedActivity.approved_at + ')' : '')"></p>
                          </div>
                        </template>
                      </div>
                    </template>

                    <template x-if="selectedActivity.type === 'leave'">
                      <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                          <div>
                            <label class="text-xs font-medium text-slate-500">Jenis Cuti</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.leave_type || '-'"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Total Hari</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="(selectedActivity.total_days || 0) + ' hari'"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Tanggal Mulai</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.start_date || '-'"></p>
                          </div>
                          <div>
                            <label class="text-xs font-medium text-slate-500">Tanggal Selesai</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.end_date || '-'"></p>
                          </div>
                        </div>
                        <div>
                          <label class="text-xs font-medium text-slate-500">Alasan</label>
                          <p class="text-sm mt-1" style="color: #0a1628;" x-text="selectedActivity.reason || '-'"></p>
                        </div>
                        <div>
                          <label class="text-xs font-medium text-slate-500">Status</label>
                          <span class="inline-block mt-1 px-2 py-1 rounded text-xs font-medium" 
                                :class="selectedActivity.status === 'approved' ? 'bg-green-100 text-green-700' : (selectedActivity.status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')"
                                x-text="(selectedActivity.status || 'pending').charAt(0).toUpperCase() + (selectedActivity.status || 'pending').slice(1)"></span>
                        </div>
                        <template x-if="selectedActivity.approved_by">
                          <div>
                            <label class="text-xs font-medium text-slate-500">Approved By</label>
                            <p class="text-sm mt-1" style="color: #0a1628;" x-text="'✅ ' + selectedActivity.approved_by + (selectedActivity.approved_at ? ' (' + selectedActivity.approved_at + ')' : '')"></p>
                          </div>
                        </template>
                      </div>
                    </template>
                  </div>

                  <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t">
                    <button @click="showPreviewModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                      Tutup
                    </button>
                    <a :href="selectedActivity.route" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
                      Lihat Detail
                    </a>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
