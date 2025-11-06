@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'Ringkasan sistem & aktivitas pengguna')

@section('content')
<div class="py-6">
  <!-- Welcome & Quick Actions -->
  <div class="mb-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        @php
          $hour = now()->hour;
          $greeting = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
        @endphp
        <h1 class="text-xl font-semibold mb-1 text-gray-900" style="letter-spacing: -0.02em;">{{ $greeting }}, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-gray-500">Ringkasan aktivitas sistem hari ini</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.users.index') }}" 
           class="asana-btn asana-btn-primary px-4 py-2 text-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
          Manajemen User
        </a>
        <a href="{{ route('admin.approvals.leaves') }}" 
           class="asana-btn asana-btn-primary px-4 py-2 text-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Approval Cuti
        </a>
        <a href="{{ route('admin.approvals.payments.index') }}" 
           class="asana-btn asana-btn-primary px-4 py-2 text-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Approval Pembayaran
        </a>
      </div>
    </div>
  </div>

  <!-- Main Content - Two Column Layout -->
  <div class="grid grid-cols-12 gap-4">
    <!-- Users List - 3 columns -->
    <div class="col-span-12 lg:col-span-3">
      <div class="asana-card flex flex-col" style="max-height: calc(100vh - 200px);">
        <div class="flex items-center justify-between mb-4 flex-shrink-0">
          <h3 class="text-sm font-semibold text-gray-900" style="letter-spacing: -0.01em;">Daftar User</h3>
          <span class="text-xs text-gray-500">{{ $users->count() ?? 0 }}</span>
        </div>
        <div class="space-y-2 overflow-y-auto flex-1" style="max-height: calc(100vh - 280px);">
          @forelse($users ?? [] as $userItem)
          <div class="p-2.5 rounded-md hover:bg-gray-50 transition-colors cursor-pointer border border-transparent hover:border-gray-200">
            <div class="flex items-center gap-2.5 mb-2">
              <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-medium flex-shrink-0 bg-gray-600">
                {{ substr($userItem->name, 0, 1) }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-medium truncate text-gray-900">{{ $userItem->name }}</p>
                <p class="text-[10px] text-gray-500 truncate">{{ $userItem->email }}</p>
              </div>
            </div>
            @if($userItem->modules && $userItem->modules->count() > 0)
            <div class="mt-1.5 flex flex-wrap gap-1">
              @foreach($userItem->modules as $module)
              @php
                $moduleColors = [
                  'work-plan' => 'bg-blue-50 text-blue-700 border-blue-200',
                  'work-realization' => 'bg-green-50 text-green-700 border-green-200',
                  'spd' => 'bg-purple-50 text-purple-700 border-purple-200',
                  'purchase' => 'bg-orange-50 text-orange-700 border-orange-200',
                  'vendor-payment' => 'bg-pink-50 text-pink-700 border-pink-200',
                  'leave' => 'bg-teal-50 text-teal-700 border-teal-200',
                  'project-management' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                  'project-monitoring' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                  'payment-approval' => 'bg-amber-50 text-amber-700 border-amber-200',
                  'leave-approval' => 'bg-teal-50 text-teal-700 border-teal-200'
                ];
                $badgeColor = $moduleColors[$module->key] ?? 'bg-gray-50 text-gray-700 border-gray-200';
              @endphp
              <span class="badge-minimal {{ $badgeColor }} text-[10px] px-1.5 py-0.5 flex items-center gap-1">
                @php
                  $iconType = \App\Helpers\IconHelper::getModuleIconType($module->key);
                @endphp
                <x-icon type="{{ $iconType }}" class="w-3 h-3" />
                <span class="truncate max-w-[60px]">{{ $module->label }}</span>
              </span>
              @endforeach
            </div>
            @endif
          </div>
          @empty
          <div class="text-center py-8 text-gray-400">
            <p class="text-xs">Tidak ada user</p>
          </div>
          @endforelse
        </div>
      </div>
    </div>

    <!-- Recent Activity - 9 columns -->
    <div class="col-span-12 lg:col-span-9">
      <div class="asana-card flex flex-col" style="max-height: calc(100vh - 200px);">
        <div class="mb-3 flex-shrink-0">
          <h3 class="text-sm font-semibold text-gray-900 mb-3">Recent Activity</h3>
        </div>
        <div class="flex-1 overflow-y-auto" style="max-height: calc(100vh - 280px);">
          <div class="space-y-2" x-data="{ showPreviewModal: false, selectedActivity: null }">
            @forelse($allRecentActivities ?? [] as $activity)
            <div @click="selectedActivity = @js($activity); showPreviewModal = true" 
                 class="asana-activity-item cursor-pointer">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                  <div class="flex-shrink-0">
                    <x-icon type="{{ $activity['icon'] }}" class="w-5 h-5" />
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                      <div class="w-5 h-5 rounded-full flex items-center justify-center text-white text-[10px] font-medium bg-gray-600 flex-shrink-0">
                        {{ substr($activity['user'] ?? 'U', 0, 1) }}
                      </div>
                      <span class="text-xs font-medium text-gray-900 truncate">{{ $activity['user'] }}</span>
                      <span class="text-xs text-gray-400">•</span>
                      <span class="text-xs text-gray-600 truncate">{{ $activity['title'] }}</span>
                    </div>
                    <div class="text-xs text-gray-600 truncate">{{ $activity['description'] }}</div>
                    @if(isset($activity['project']) && $activity['project'] !== '-')
                    <div class="text-[10px] text-gray-500 mt-0.5 truncate">{{ $activity['project'] }}</div>
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
            <div class="empty-state">
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
              <div class="inline-block align-bottom bg-white modal-content text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full" @click.stop>
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
@endsection
