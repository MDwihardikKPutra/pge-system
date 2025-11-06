@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas Anda')

@section('content')
<div class="flex flex-col h-full" style="height: calc(100vh - 80px); overflow: hidden;">
  <!-- Welcome & Quick Actions -->
  <div class="mb-4 flex-shrink-0">
        <div class="flex items-center justify-between mb-4">
            <div>
                @php
                    $hour = now()->hour;
                    $greeting = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
                @endphp
        <h1 class="text-xl font-semibold mb-1 text-gray-900" style="letter-spacing: -0.02em;">{{ $greeting }}, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-gray-500">Ringkasan aktivitas sistem</p>
        </div>
        <!-- Month Filter -->
        <form method="GET" action="{{ route('user.dashboard') }}" class="flex items-center gap-2">
            <label class="text-xs font-medium text-gray-600 whitespace-nowrap">Bulan:</label>
            <input type="month" name="month" value="{{ request('month', $month ?? now()->format('Y-m')) }}" 
                   class="text-xs px-2 py-1.5 rounded border border-gray-300 bg-white text-gray-900 focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                   style="min-width: 120px;"
                   onchange="this.form.submit()">
            @if(request('month') && request('month') !== now()->format('Y-m'))
            <a href="{{ route('user.dashboard') }}" class="px-2 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded transition-colors">
                Reset
            </a>
            @endif
        </form>
    </div>
    
    <!-- Work Notifications Card -->
    @if(isset($workNotifications) && ($workNotifications['needs_work_plan'] || $workNotifications['needs_work_realization']))
    <div class="mb-4 space-y-2">
      @if($workNotifications['needs_work_plan'])
      <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-start gap-3">
        <div class="flex-shrink-0 mt-0.5">
          <x-icon type="information-circle" class="w-5 h-5 text-amber-600" />
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="text-sm font-semibold text-amber-900 mb-1">Belum Mengisi Rencana Kerja Hari Ini</h4>
          <p class="text-xs text-amber-700">Anda belum mengisi rencana kerja untuk hari ini. Silakan isi rencana kerja Anda melalui Aksi Cepat di sebelah kiri.</p>
        </div>
      </div>
      @endif
      
      @if($workNotifications['needs_work_realization'])
      <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 flex items-start gap-3">
        <div class="flex-shrink-0 mt-0.5">
          <x-icon type="information-circle" class="w-5 h-5 text-orange-600" />
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="text-sm font-semibold text-orange-900 mb-1">Belum Mengisi Realisasi Kerja Hari Ini</h4>
          <p class="text-xs text-orange-700">Anda belum mengisi realisasi kerja untuk hari ini. Silakan isi realisasi kerja Anda melalui Aksi Cepat di sebelah kiri sebelum jam 17:00.</p>
        </div>
      </div>
      @endif
    </div>
    @endif
  </div>

  <!-- Main Content - Two Column Layout -->
  <div class="grid grid-cols-12 gap-4 flex-1 min-h-0">
    <!-- Quick Actions Card - 3 columns -->
    <div class="col-span-12 lg:col-span-3 flex flex-col min-h-0">
      <div class="asana-card flex flex-col flex-1 min-h-0">
        <div class="flex items-center justify-between mb-4 flex-shrink-0">
          <h3 class="text-sm font-semibold text-gray-900" style="letter-spacing: -0.01em;">Aksi Cepat</h3>
        </div>
        <div class="space-y-2 overflow-y-auto flex-1 min-h-0" x-data="{}">
    @if(isset($activeModules) && $activeModules->count() > 0)
            @foreach($activeModules as $module)
              @php
                $moduleKey = $module->key;
                $canCreate = in_array($moduleKey, ['work-plan', 'work-realization', 'spd', 'purchase', 'vendor-payment', 'leave']);
              @endphp
              @if($canCreate)
                @php
                  $moduleColors = [
                    'work-plan' => 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100',
                    'work-realization' => 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100',
                    'spd' => 'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100',
                    'purchase' => 'bg-orange-50 text-orange-700 border-orange-200 hover:bg-orange-100',
                    'vendor-payment' => 'bg-pink-50 text-pink-700 border-pink-200 hover:bg-pink-100',
                    'leave' => 'bg-teal-50 text-teal-700 border-teal-200 hover:bg-teal-100',
                  ];
                  $badgeColor = $moduleColors[$moduleKey] ?? 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100';
                @endphp
                @php
                  $detailKey = match($moduleKey) {
                    'work-plan' => 'plan',
                    'work-realization' => 'realization',
                    'leave' => 'leave',
                    default => null
                  };
                @endphp
                @if($detailKey)
                <button @click="
                  console.log('Dispatching event: open-{{ $moduleKey }}-modal');
                  window.dispatchEvent(new CustomEvent('open-{{ $moduleKey }}-modal', { detail: { mode: 'create', '{{ $detailKey }}': null } }));
                " 
                        class="w-full p-3 rounded-md border transition-colors {{ $badgeColor }} text-left group">
                  <div class="flex items-center gap-2.5">
                    @php
                      $iconType = \App\Helpers\IconHelper::getModuleIconType($moduleKey);
                    @endphp
                    <x-icon type="{{ $iconType }}" class="w-5 h-5" />
                    <span class="text-xs font-medium truncate flex-1">{{ $module->label }}</span>
                    <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                  </div>
                </button>
                @else
                <button @click="window.dispatchEvent(new CustomEvent('open-{{ $moduleKey }}-modal'))" 
                        class="w-full p-3 rounded-md border transition-colors {{ $badgeColor }} text-left group">
                  <div class="flex items-center gap-2.5">
                    @php
                      $iconType = \App\Helpers\IconHelper::getModuleIconType($moduleKey);
                    @endphp
                    <x-icon type="{{ $iconType }}" class="w-5 h-5" />
                    <span class="text-xs font-medium truncate flex-1">{{ $module->label }}</span>
                    <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                        </div>
                </button>
                @endif
                @endif
            @endforeach
          @else
            <div class="text-center py-8 text-gray-400">
              <p class="text-xs">Tidak ada modul aktif</p>
            </div>
          @endif
        </div>
        </div>
    </div>

    <!-- Recent Activity - 9 columns -->
    <div class="col-span-12 lg:col-span-9 flex flex-col min-h-0">
      <div class="asana-card flex flex-col flex-1 min-h-0">
        <div class="mb-3 flex-shrink-0">
          <h3 class="text-sm font-semibold text-gray-900 mb-3">Recent Activity</h3>
        </div>
        <div class="flex-1 overflow-y-auto min-h-0 flex flex-col">
          <div class="space-y-2 flex-1" x-data="{ showPreviewModal: false, selectedActivity: null }">
                @forelse($recentActivities ?? [] as $activity)
                <div @click="selectedActivity = @js($activity); showPreviewModal = true" 
                 class="asana-activity-item cursor-pointer">
              <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                  <div class="flex-shrink-0">
                    <x-icon type="{{ $activity['icon'] }}" class="w-5 h-5" />
                            </div>
                            <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                      <span class="text-xs font-medium text-gray-900 truncate">{{ $activity['title'] }}</span>
                      @if(isset($activity['is_approval']) && $activity['is_approval'])
                      <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-700 border border-green-200">
                        Disetujui
                      </span>
                      @endif
                                    @if(isset($activity['project']) && $activity['project'] !== '-')
                                    <span class="text-xs text-gray-400">•</span>
                      <span class="text-xs text-gray-600 truncate">{{ $activity['project'] }}</span>
                      @endif
                    </div>
                    <div class="text-xs text-gray-600 truncate">{{ $activity['description'] }}</div>
                    @if(isset($activity['is_approval']) && $activity['is_approval'] && isset($activity['approved_by']))
                    <div class="text-[10px] text-gray-500 mt-0.5">
                      Disetujui oleh: {{ $activity['approved_by'] }}
                      @if(isset($activity['approved_at']))
                      • {{ $activity['approved_at'] }}
                                    @endif
                                </div>
                    @endif
                            </div>
                        </div>
                <div class="flex-shrink-0 flex items-center gap-2">
                            @if($activity['type'] === 'work-realization' && isset($activity['extra']) && is_numeric($activity['extra']))
                  <div class="flex items-center gap-1.5">
                    <div class="w-16 rounded-full h-1.5 overflow-hidden bg-gray-200">
                      <div class="h-full rounded-full bg-green-500" style="width: {{ $activity['extra'] }}%;"></div>
                                </div>
                    <span class="text-[10px] font-medium whitespace-nowrap text-gray-600">{{ $activity['extra'] }}%</span>
                            </div>
                            @elseif(isset($activity['extra']) && in_array($activity['extra'], ['pending', 'approved', 'rejected']))
                            @php
                                $statusClass = match($activity['extra']) {
                                    'pending' => 'badge-minimal badge-warning',
                                    'approved' => 'badge-minimal badge-success',
                                    'rejected' => 'badge-minimal badge-error',
                                    default => 'badge-minimal badge-neutral'
                                };
                            @endphp
                            <span class="{{ $statusClass }}">
                                {{ ucfirst($activity['extra']) }}
                            </span>
                            @endif
                            <span class="text-xs text-gray-500 whitespace-nowrap">
                    {{ \Carbon\Carbon::parse($activity['date'])->format('d M') }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
            <div class="empty-state flex-1 flex flex-col items-center justify-center min-h-full py-12">
              <div class="empty-state-icon">
                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
              </div>
              <p class="text-sm font-medium text-gray-900 mb-0.5">Tidak ada aktivitas terbaru</p>
              <p class="text-xs text-gray-500">Aktivitas akan muncul di sini setelah ada data baru</p>
                </div>
                @endforelse
    </div>

    <!-- Preview Modal -->
    <div x-show="showPreviewModal" 
         x-cloak 
         style="display: none;" 
         class="fixed inset-0 z-50 overflow-y-auto modal-overlay" 
         @click.away="showPreviewModal = false">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="showPreviewModal = false"></div>
        <div class="inline-block align-bottom modal-content text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full" @click.stop>
          <template x-if="selectedActivity">
            <div>
              <!-- Modal Header -->
              <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-gray-50" 
                         :class="selectedActivity.type === 'work-plan' ? 'text-blue-600' : 
                                 selectedActivity.type === 'work-realization' ? 'text-green-600' : 
                                 selectedActivity.type === 'spd' ? 'text-purple-600' : 
                                 selectedActivity.type === 'purchase' ? 'text-orange-600' : 
                                 selectedActivity.type === 'vendor-payment' ? 'text-pink-600' : 
                                 selectedActivity.type === 'leave' ? 'text-teal-600' : 'text-indigo-600'">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                              :d="selectedActivity.icon === 'clipboard-document' ? 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z' :
                              selectedActivity.icon === 'check-circle' ? 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z' :
                              selectedActivity.icon === 'paper-airplane' ? 'M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5' :
                              selectedActivity.icon === 'shopping-cart' ? 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z' :
                              selectedActivity.icon === 'credit-card' ? 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z' :
                              selectedActivity.icon === 'calendar' ? 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 005.25 9h13.5A2.25 2.25 0 0121 11.25v7.5' :
                              selectedActivity.icon === 'folder' ? 'M2.25 12.75V12A2.25 2.25 0 014.5 9.75h6A2.25 2.25 0 0112.75 12v.75m-8.5-3A2.25 2.25 0 003.75 15h13.5A2.25 2.25 0 0021.75 12.75m-16.5 0A2.25 2.25 0 013.75 9h6m-6 0A2.25 2.25 0 001.5 6.75v-.75m0 0A2.25 2.25 0 013.75 4.5h6.379a2.251 2.251 0 011.59.659l2.122 2.121c.14.141.331.22.53.22H19.5A2.25 2.25 0 0121.75 9v.75' :
                              'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'"></path>
                      </svg>
                    </div>
                    <div>
                      <h3 class="text-base font-semibold text-gray-900" style="letter-spacing: -0.01em;" x-text="selectedActivity.title"></h3>
                      <p class="text-xs text-gray-500 mt-0.5" x-text="selectedActivity.number || '-'"></p>
                    </div>
                  </div>
                  <button @click="showPreviewModal = false" class="text-gray-400 hover:text-gray-600 transition-colors p-1.5 hover:bg-gray-100 rounded-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>
              </div>

          <!-- Modal Content -->
          <div class="bg-white px-6 py-4 max-h-[60vh] overflow-y-auto">
            <!-- User Info -->
            <div class="mb-4 pb-4 border-b border-gray-100">
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-medium bg-gray-600">
                  <span x-text="(selectedActivity.user || 'U').charAt(0)"></span>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900" x-text="selectedActivity.user || '-'"></p>
                  <p class="text-xs text-gray-500" x-text="selectedActivity.user_email || '-'"></p>
                </div>
              </div>
            </div>
            <!-- Content based on type -->
            <template x-if="selectedActivity.type === 'work-plan'">
              <div class="space-y-3">
                <div>
                  <label class="text-xs font-medium text-gray-600">Deskripsi</label>
                  <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.full_description || '-'"></p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="text-xs font-medium text-gray-600">Project</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.project || '-'"></p>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">Lokasi</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.location || '-'"></p>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">Durasi</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="(selectedActivity.duration || 0) + ' jam'"></p>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">Tanggal</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="new Date(selectedActivity.date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})"></p>
                  </div>
                </div>
            </div>
            </template>

            <template x-if="selectedActivity.type === 'work-realization'">
              <div class="space-y-3">
                <div>
                  <label class="text-xs font-medium text-gray-600">Deskripsi</label>
                  <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.full_description || '-'"></p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="text-xs font-medium text-gray-600">Project</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.project || '-'"></p>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">Lokasi</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.location || '-'"></p>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">Durasi</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="(selectedActivity.duration || 0) + ' jam'"></p>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">Progress</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="(selectedActivity.progress || 0) + '%'"></p>
                  </div>
                </div>
            </div>
            </template>

            <template x-if="selectedActivity.type === 'spd'">
              <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="text-xs font-medium text-gray-600">Tujuan</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.destination || '-'"></p>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">Project</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.project || '-'"></p>
                  </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">Berangkat</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.departure_date || '-'"></p>
        </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Kembali</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.return_date || '-'"></p>
                  </div>
                </div>
                <div>
                  <label class="text-xs font-medium text-gray-600">Tujuan Perjalanan</label>
                  <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.purpose || '-'"></p>
                </div>
                <div>
                  <label class="text-xs font-medium text-gray-600">Total Biaya</label>
                  <p class="text-sm mt-1 font-semibold text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedActivity.total_cost || 0)"></p>
                </div>
                <div>
                  <label class="text-xs font-medium text-gray-600">Status</label>
                  <span class="inline-block mt-1 badge-minimal" 
                        :class="selectedActivity.status === 'approved' ? 'badge-success' : 
                                selectedActivity.status === 'rejected' ? 'badge-error' : 'badge-warning'"
                        x-text="(selectedActivity.status || 'pending').charAt(0).toUpperCase() + (selectedActivity.status || 'pending').slice(1)"></span>
                </div>
                <template x-if="selectedActivity.approved_by">
                  <div>
                    <label class="text-xs font-medium text-gray-600">Approved By</label>
                    <div class="flex items-center gap-1.5 mt-1">
                      <x-icon type="check" class="w-4 h-4 text-green-600" />
                      <p class="text-sm text-gray-900" x-text="selectedActivity.approved_by + (selectedActivity.approved_at ? ' (' + selectedActivity.approved_at + ')' : '')"></p>
                    </div>
            </div>
                </template>
        </div>
            </template>

            <template x-if="selectedActivity.type === 'purchase'">
              <div class="space-y-3">
                <div>
                  <label class="text-xs font-medium text-gray-600">Item</label>
                  <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.item_name || '-'"></p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="text-xs font-medium text-gray-600">Project</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.project || '-'"></p>
            </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">Jumlah</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="(selectedActivity.quantity || 0) + ' ' + (selectedActivity.unit || '')"></p>
                            </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">Harga Satuan</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedActivity.unit_price || 0)"></p>
                        </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">Total Harga</label>
                    <p class="text-sm mt-1 font-semibold text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedActivity.total_price || 0)"></p>
            </div>
        </div>
                <div>
                  <label class="text-xs font-medium text-gray-600">Catatan</label>
                  <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.notes || '-'"></p>
                </div>
                <div>
                  <label class="text-xs font-medium text-gray-600">Status</label>
                  <span class="inline-block mt-1 badge-minimal" 
                        :class="selectedActivity.status === 'approved' ? 'badge-success' : 
                                selectedActivity.status === 'rejected' ? 'badge-error' : 'badge-warning'"
                        x-text="(selectedActivity.status || 'pending').charAt(0).toUpperCase() + (selectedActivity.status || 'pending').slice(1)"></span>
                </div>
                <template x-if="selectedActivity.approved_by">
                  <div>
                    <label class="text-xs font-medium text-gray-600">Approved By</label>
                    <div class="flex items-center gap-1.5 mt-1">
                      <x-icon type="check" class="w-4 h-4 text-green-600" />
                      <p class="text-sm text-gray-900" x-text="selectedActivity.approved_by + (selectedActivity.approved_at ? ' (' + selectedActivity.approved_at + ')' : '')"></p>
                    </div>
            </div>
                </template>
                            </div>
            </template>

            <template x-if="selectedActivity.type === 'vendor-payment'">
              <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="text-xs font-medium text-gray-600">Vendor</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.vendor || '-'"></p>
                            </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">Project</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.project || '-'"></p>
                        </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">No. Invoice</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.invoice_number || '-'"></p>
                    </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">Tanggal Pembayaran</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.payment_date || '-'"></p>
            </div>
        </div>
                <div>
                  <label class="text-xs font-medium text-gray-600">Deskripsi</label>
                  <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.description_text || '-'"></p>
                </div>
                <div>
                  <label class="text-xs font-medium text-gray-600">Jumlah</label>
                  <p class="text-sm mt-1 font-semibold text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedActivity.amount || 0)"></p>
                </div>
                <div>
                  <label class="text-xs font-medium text-gray-600">Status</label>
                  <span class="inline-block mt-1 badge-minimal" 
                        :class="selectedActivity.status === 'approved' ? 'badge-success' : 
                                selectedActivity.status === 'rejected' ? 'badge-error' : 'badge-warning'"
                        x-text="(selectedActivity.status || 'pending').charAt(0).toUpperCase() + (selectedActivity.status || 'pending').slice(1)"></span>
                </div>
                <template x-if="selectedActivity.approved_by">
                  <div>
                    <label class="text-xs font-medium text-gray-600">Approved By</label>
                    <div class="flex items-center gap-1.5 mt-1">
                      <x-icon type="check" class="w-4 h-4 text-green-600" />
                      <p class="text-sm text-gray-900" x-text="selectedActivity.approved_by + (selectedActivity.approved_at ? ' (' + selectedActivity.approved_at + ')' : '')"></p>
                    </div>
            </div>
                </template>
                            </div>
            </template>

            <template x-if="selectedActivity.type === 'leave'">
              <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="text-xs font-medium text-gray-600">Jenis Cuti</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.leave_type || '-'"></p>
                            </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">Total Hari</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="(selectedActivity.total_days || 0) + ' hari'"></p>
                        </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">Tanggal Mulai</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.start_date || '-'"></p>
                    </div>
                  <div>
                    <label class="text-xs font-medium text-gray-600">Tanggal Selesai</label>
                    <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.end_date || '-'"></p>
            </div>
        </div>
                <div>
                  <label class="text-xs font-medium text-gray-600">Alasan</label>
                  <p class="text-sm mt-1 text-gray-900" x-text="selectedActivity.reason || '-'"></p>
                </div>
                <div>
                  <label class="text-xs font-medium text-gray-600">Status</label>
                  <span class="inline-block mt-1 badge-minimal" 
                        :class="selectedActivity.status === 'approved' ? 'badge-success' : 
                                selectedActivity.status === 'rejected' ? 'badge-error' : 'badge-warning'"
                        x-text="(selectedActivity.status || 'pending').charAt(0).toUpperCase() + (selectedActivity.status || 'pending').slice(1)"></span>
                </div>
                <template x-if="selectedActivity.approved_by">
                  <div>
                    <label class="text-xs font-medium text-gray-600">Approved By</label>
                    <div class="flex items-center gap-1.5 mt-1">
                      <x-icon type="check" class="w-4 h-4 text-green-600" />
                      <p class="text-sm text-gray-900" x-text="selectedActivity.approved_by + (selectedActivity.approved_at ? ' (' + selectedActivity.approved_at + ')' : '')"></p>
                    </div>
                  </div>
                </template>
              </div>
            </template>
          </div>

          <!-- Modal Footer -->
          <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
            <button @click="showPreviewModal = false" class="asana-btn asana-btn-secondary">
              Tutup
            </button>
            <template x-if="selectedActivity && selectedActivity.route">
              <a :href="selectedActivity.route" class="asana-btn asana-btn-primary">
                Lihat Detail
              </a>
            </template>
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
  </div>
</div>

<!-- Include Modals -->
@php
  $projects = \App\Helpers\CacheHelper::getProjectsDropdown();
  $vendors = \App\Models\Vendor::where('is_active', true)->orderBy('name')->get();
  $leaveTypes = \App\Models\LeaveType::where('is_active', true)->get();
@endphp

<!-- Include Modals -->
@if(isset($activeModules) && $activeModules->contains('key', 'spd'))
<div x-data="spdForm" x-init="init(); window.spdFormComponent = this;">
  @include('payment.spd.modal', ['projects' => $projects])
</div>
@endif

@if(isset($activeModules) && $activeModules->contains('key', 'purchase'))
<div x-data="purchaseForm" x-init="init(); window.purchaseFormComponent = this;">
  @include('payment.purchase.modal', ['projects' => $projects])
</div>
@endif

@if(isset($activeModules) && $activeModules->contains('key', 'vendor-payment'))
<div x-data="vendorPaymentForm" x-init="init(); window.vendorPaymentFormComponent = this;">
  @include('payment.vendor-payment.modal', ['projects' => $projects, 'vendors' => $vendors])
</div>
@endif

@if(isset($activeModules) && $activeModules->contains('key', 'work-plan'))
<div x-data="{ 
  showModal: false, 
  modalMode: 'create', 
  currentPlan: null,
  projects: @js($projects),
  planTimeWarning: '',
  closeModal() { 
    this.showModal = false; 
    this.currentPlan = null; 
    this.planTimeWarning = '';
  }
}" 
@open-work-plan-modal.window="
  console.log('Work plan modal event received', $event.detail);
  showModal = true; 
  modalMode = $event.detail?.mode || 'create'; 
  currentPlan = $event.detail?.plan || null; 
  planTimeWarning = '';
">
  @include('work.work-plans.modal')
</div>
@endif

@if(isset($activeModules) && $activeModules->contains('key', 'work-realization'))
<div x-data="{ 
  showModal: false, 
  modalMode: 'create', 
  currentRealization: null, 
  workPlans: [], 
  projects: @js($projects),
  realizationTimeWarning: '',
  validateRealizationTime(event) {
    this.realizationTimeWarning = '';
    const selectedDate = event.target.value;
    if (!selectedDate) return;
    
    const selected = new Date(selectedDate + 'T00:00:00');
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const now = new Date();
    
    if (selected.getTime() === today.getTime()) {
      if (now.getHours() >= 17) {
        this.realizationTimeWarning = 'Realisasi kerja hari ini harus diisi sebelum jam 17:00 (5 sore).';
      }
    }
  },
  closeModal() { 
    this.showModal = false; 
    this.currentRealization = null;
    this.realizationTimeWarning = '';
  }
}" 
@open-work-realization-modal.window="
  console.log('Work realization modal event received', $event.detail);
  showModal = true; 
  modalMode = $event.detail?.mode || 'create'; 
  currentRealization = $event.detail?.realization || null; 
  realizationTimeWarning = '';
">
  @include('work.work-realizations.modal')
</div>
@endif

@if(isset($activeModules) && $activeModules->contains('key', 'leave'))
<div x-data="{ 
  showModal: false, 
  modalMode: 'create', 
  currentLeave: null,
  leaveTypes: @js($leaveTypes),
  closeModal() { 
    this.showModal = false; 
    this.currentLeave = null; 
  }
}" 
@open-leave-modal.window="
  console.log('Leave modal event received', $event.detail);
  showModal = true; 
  modalMode = $event.detail?.mode || 'create'; 
  currentLeave = $event.detail?.leave || null;
">
  @include('leave.modal', ['routePrefix' => 'user.leaves'])
</div>
@endif

@push('alpine-init')
@if(isset($activeModules) && $activeModules->contains('key', 'spd'))
  @include('payment.spd.dashboard-script', ['projects' => $projects])
@endif

@if(isset($activeModules) && $activeModules->contains('key', 'purchase'))
<script>
(function() {
    function registerPurchaseForm() {
        if (typeof Alpine === 'undefined') return;
        
        Alpine.data('purchaseForm', () => ({
            showModal: false,
            editMode: false,
            modalTitle: 'Ajukan Pembelian Baru',
            formData: {
                id: null,
                project_id: '',
                type: '',
                category: '',
                item_name: '',
                description: '',
                quantity: 1,
                unit: '',
                unit_price: 0,
                notes: '',
            },
            errors: {},
            projects: @json($projects),
            totalPriceDisplay: '0',
            
            init() {
                this.$watch('formData.quantity', () => this.updateTotalPrice());
                this.$watch('formData.unit_price', () => this.updateTotalPrice());
                this.updateTotalPrice();
                window.purchaseFormComponent = this;
                window.addEventListener('open-purchase-modal', () => this.openCreateModal());
            },
            
            updateTotalPrice() {
                const total = parseFloat(this.formData.quantity || 0) * parseFloat(this.formData.unit_price || 0);
                this.totalPriceDisplay = total.toLocaleString('id-ID');
            },
            
            openCreateModal() {
                this.resetForm();
                this.editMode = false;
                this.modalTitle = 'Ajukan Pembelian Baru';
                this.showModal = true;
            },
            
            resetForm() {
                this.formData = {
                    id: null,
                    project_id: '',
                    type: '',
                    category: '',
                    item_name: '',
                    description: '',
                    quantity: 1,
                    unit: '',
                    unit_price: 0,
                    notes: '',
                };
                this.errors = {};
                this.totalPriceDisplay = '0';
            },
            
            async submitForm() {
                this.errors = {};
                const projectSelectHidden = document.querySelector('[data-parent-form="purchaseForm"] .project-select-hidden');
                if (projectSelectHidden && projectSelectHidden.value) {
                    this.formData.project_id = projectSelectHidden.value;
                }
                
                try {
                    const formData = new FormData();
                    if (this.formData.project_id) formData.append('project_id', this.formData.project_id);
                    formData.append('type', this.formData.type);
                    formData.append('category', this.formData.category);
                    formData.append('item_name', this.formData.item_name);
                    formData.append('description', this.formData.description);
                    formData.append('quantity', this.formData.quantity);
                    formData.append('unit', this.formData.unit);
                    formData.append('unit_price', this.formData.unit_price);
                    if (this.formData.notes) formData.append('notes', this.formData.notes);
                    
                    const response = await fetch('/user/purchases', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    });
                    
                    const data = await response.json();
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        if (data.errors) this.errors = data.errors;
                        else alert(data.message || 'Terjadi kesalahan');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan data');
                }
            },
            
            closeModal() {
                this.showModal = false;
                this.resetForm();
            }
        }));
    }
    
    if (typeof Alpine !== 'undefined' && Alpine.version) {
        registerPurchaseForm();
    } else {
        document.addEventListener('alpine:init', registerPurchaseForm);
    }
})();
</script>
@endif

@if(isset($activeModules) && $activeModules->contains('key', 'vendor-payment'))
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('vendorPaymentForm', () => ({
        showModal: false,
        editMode: false,
        modalTitle: 'Ajukan Pembayaran Vendor Baru',
        formData: {
            id: null,
            vendor_id: '',
            project_id: '',
            payment_type: '',
            payment_date: '',
            invoice_number: '',
            po_number: '',
            amount: 0,
            description: '',
            notes: '',
        },
        errors: {},
        vendors: @json($vendors),
        projects: @json($projects),
        
        init() {
            window.vendorPaymentFormComponent = this;
            window.addEventListener('open-vendor-payment-modal', () => this.openCreateModal());
        },
        
        openCreateModal() {
            this.resetForm();
            this.editMode = false;
            this.modalTitle = 'Ajukan Pembayaran Vendor Baru';
            this.showModal = true;
        },
        
        resetForm() {
            this.formData = {
                id: null,
                vendor_id: '',
                project_id: '',
                payment_type: '',
                payment_date: '',
                invoice_number: '',
                po_number: '',
                amount: 0,
                description: '',
                notes: '',
            };
            this.errors = {};
        },
        
        async submitForm() {
            this.errors = {};
            const projectSelectHidden = document.querySelector('[data-parent-form="vendorPaymentForm"] .project-select-hidden');
            if (projectSelectHidden && projectSelectHidden.value) {
                this.formData.project_id = projectSelectHidden.value;
            }
            
            try {
                const formData = new FormData();
                if (this.formData.vendor_id) formData.append('vendor_id', this.formData.vendor_id);
                if (this.formData.project_id) formData.append('project_id', this.formData.project_id);
                formData.append('payment_type', this.formData.payment_type);
                formData.append('payment_date', this.formData.payment_date);
                formData.append('invoice_number', this.formData.invoice_number);
                if (this.formData.po_number) formData.append('po_number', this.formData.po_number);
                formData.append('amount', this.formData.amount);
                formData.append('description', this.formData.description);
                if (this.formData.notes) formData.append('notes', this.formData.notes);
                
                const response = await fetch('/user/vendor-payments', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });
                
                const data = await response.json();
                if (response.ok) {
                    window.location.reload();
                } else {
                    if (data.errors) this.errors = data.errors;
                    else alert(data.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
            }
        },
        
        closeModal() {
            this.showModal = false;
            this.resetForm();
        }
    }));
});
</script>
@endif

@if(isset($activeModules) && $activeModules->contains('key', 'work-plan'))
<script>
// Work Plan uses event-based modal system, just ensure event is dispatched
</script>
@endif

@if(isset($activeModules) && $activeModules->contains('key', 'work-realization'))
<script>
// Work Realization uses event-based modal system, just ensure event is dispatched
</script>
@endif

@if(isset($activeModules) && $activeModules->contains('key', 'leave'))
<script>
// Leave uses event-based modal system, just ensure event is dispatched
</script>
@endif
@endpush

@push('scripts')
@if(isset($activeModules) && $activeModules->contains('key', 'spd'))
  @include('payment.spd.project-select-script')
@endif

@if(isset($activeModules) && $activeModules->contains('key', 'purchase'))
  @include('payment.purchase.project-select-script')
@endif

@if(isset($activeModules) && $activeModules->contains('key', 'vendor-payment'))
  @include('payment.vendor-payment.project-select-script')
@endif
@endpush

@endsection
