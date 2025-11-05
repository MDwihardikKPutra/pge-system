@extends('layouts.app')

@section('title', 'EAR - Employee Activity Report')
@section('page-title', 'EAR - Employee Activity Report')
@section('page-subtitle', 'Monitoring Rencana & Realisasi Kerja')

@section('content')
<div class="py-8" 
     x-data="{
         activeTab: 'plans',
         showDetailModal: false,
         loadingDetail: false,
         detailData: null,
         setTab(tab) {
             this.activeTab = tab;
         },
         loadDetail(type, id) {
             this.showDetailModal = true;
             this.loadingDetail = true;
             this.detailData = null;
             
             const url = type === 'work-plan' 
                 ? '{{ route('admin.ear.work-plan.detail', ':id') }}'.replace(':id', id)
                 : '{{ route('admin.ear.work-realization.detail', ':id') }}'.replace(':id', id);
             
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        this.detailData = data.data;
                    } else {
                        alert('Gagal memuat detail: ' + (data.message || 'Terjadi kesalahan'));
                        this.showDetailModal = false;
                    }
                    this.loadingDetail = false;
                })
                .catch(error => {
                    console.error('Error loading detail:', error);
                    alert('Gagal memuat detail. Silakan coba lagi.');
                    this.loadingDetail = false;
                    this.showDetailModal = false;
                });
         }
     }">
    <!-- Table with Integrated Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <!-- Header with Filters -->
        <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-white">EAR - Employee Activity Report</h2>
                <div class="text-xs text-gray-300 flex items-center gap-4">
                    <span>Rencana: <span class="font-semibold text-white">{{ $totalPlans }}</span></span>
                    <span>Realisasi: <span class="font-semibold text-white">{{ $totalRealizations }}</span></span>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="flex gap-2 mb-4 border-b border-slate-700">
                <button @click="setTab('plans')" 
                        class="px-4 py-2 text-sm font-medium transition-colors"
                        :class="activeTab === 'plans' ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white'">
                    Rencana Kerja ({{ $totalPlans }})
                </button>
                <button @click="setTab('realizations')" 
                        class="px-4 py-2 text-sm font-medium transition-colors"
                        :class="activeTab === 'realizations' ? 'text-white border-b-2 border-white' : 'text-gray-300 hover:text-white'">
                    Realisasi Kerja ({{ $totalRealizations }})
                </button>
            </div>
            
            <!-- Compact Filters -->
            <form method="GET" action="{{ route('admin.ear') }}" class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-300 whitespace-nowrap">Bulan:</label>
                    <input type="month" name="month" value="{{ $selectedMonth }}" 
                           class="text-xs px-2 py-1.5 rounded border border-gray-600 bg-slate-800 text-white focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                           style="min-width: 120px;">
                </div>
                
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-300 whitespace-nowrap">User:</label>
                    <select name="user_id" 
                            class="text-xs px-2 py-1.5 rounded border border-gray-600 bg-slate-800 text-white focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                            style="min-width: 150px;">
                        <option value="">Semua User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $selectedUserId == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-300 whitespace-nowrap">Project:</label>
                    <select name="project_id" 
                            class="text-xs px-2 py-1.5 rounded border border-gray-600 bg-slate-800 text-white focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                            style="min-width: 150px;">
                        <option value="">Semua Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ $selectedProjectId == $project->id ? 'selected' : '' }}>
                                {{ $project->code }} - {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-center gap-2 flex-1">
                    <label class="text-xs font-medium text-gray-300 whitespace-nowrap">Cari:</label>
                    <input type="text" name="search" value="{{ $searchQuery }}" 
                           placeholder="Cari judul, deskripsi, lokasi..."
                           class="text-xs px-3 py-1.5 rounded border border-gray-600 bg-slate-800 text-white placeholder-gray-400 focus:ring-1 focus:ring-blue-400 focus:border-blue-400 flex-1"
                           style="max-width: 300px;">
                </div>
                
                <div class="flex items-center gap-2">
                    <button type="submit" 
                            class="px-3 py-1.5 text-xs font-medium text-white rounded transition-colors bg-blue-600 hover:bg-blue-700">
                        Filter
                    </button>
                    @if(request()->has('month') || request()->has('user_id') || request()->has('project_id') || request()->has('search'))
                    <a href="{{ route('admin.ear') }}" 
                       class="px-3 py-1.5 text-xs font-medium text-gray-300 bg-slate-700 hover:bg-slate-600 rounded transition-colors">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Content -->
        <!-- Rencana Kerja Tab -->
        <div x-show="activeTab === 'plans'" class="overflow-x-auto">
            @if($workPlans->count() > 0)
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Rencana Kerja</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">User</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Project</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Lokasi</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Durasi</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($workPlans as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-900">
                            {{ \Carbon\Carbon::parse($item['date'])->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-xs font-medium text-slate-900">{{ $item['title'] }}</div>
                            <div class="text-xs text-slate-500 line-clamp-2 mt-0.5">{{ \Illuminate\Support\Str::limit($item['description'], 100) }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-xs text-slate-900">{{ $item['user'] }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($item['project_code'] || $item['project'])
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $item['project_code'] ?? $item['project'] }}
                                </span>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($item['location'] && $item['location'] !== '-')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $item['location'] }}
                                </span>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-900">
                            {{ $item['duration'] ?? 0 }}h
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <button @click="loadDetail('work-plan', {{ $item['id'] }})" 
                                    class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-white rounded transition-colors" 
                                    style="background-color: #0a1628;" 
                                    onmouseover="this.style.backgroundColor='#1e293b'" 
                                    onmouseout="this.style.backgroundColor='#0a1628'">
                                Detail
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Pagination for Work Plans -->
            @if($workPlansPaginated->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                <div class="flex items-center justify-between">
                    <div class="text-xs text-slate-600">
                        Menampilkan {{ $workPlansPaginated->firstItem() ?? 0 }} - {{ $workPlansPaginated->lastItem() ?? 0 }} dari {{ $workPlansPaginated->total() }} rencana kerja
                    </div>
                    <div class="flex items-center gap-2">
                        {{ $workPlansPaginated->appends(request()->except('plans_page'))->links() }}
                    </div>
                </div>
            </div>
            @endif
            
            @else
            <div class="py-12 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-medium">Tidak ada rencana kerja untuk periode yang dipilih</p>
                <p class="text-xs text-slate-400 mt-1">Coba ubah filter atau cari dengan kata kunci lain</p>
            </div>
            @endif
        </div>

        <!-- Realisasi Kerja Tab -->
        <div x-show="activeTab === 'realizations'" class="overflow-x-auto">
            @if($workRealizations->count() > 0)
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Realisasi Kerja</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">User</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Project</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Lokasi</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Durasi</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Progress</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-slate-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($workRealizations as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-900">
                            {{ \Carbon\Carbon::parse($item['date'])->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-xs font-medium text-slate-900">{{ $item['title'] }}</div>
                            <div class="text-xs text-slate-500 line-clamp-2 mt-0.5">{{ \Illuminate\Support\Str::limit($item['description'], 100) }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-xs text-slate-900">{{ $item['user'] }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($item['project_code'] || $item['project'])
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $item['project_code'] ?? $item['project'] }}
                                </span>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($item['location'] && $item['location'] !== '-')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $item['location'] }}
                                </span>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-900">
                            {{ $item['duration'] ?? 0 }}h
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if(isset($item['progress']) && $item['progress'] !== null)
                            <div class="flex items-center gap-1.5">
                                <div class="w-16 rounded-full h-1.5 overflow-hidden bg-slate-200">
                                    <div class="h-full rounded-full" style="width: {{ $item['progress'] }}%; background-color: #0a1628;"></div>
                                </div>
                                <span class="text-xs font-semibold" style="color: #0a1628;">{{ $item['progress'] }}%</span>
                            </div>
                            @else
                            <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <button @click="loadDetail('work-realization', {{ $item['id'] }})" 
                                    class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-white rounded transition-colors" 
                                    style="background-color: #0a1628;" 
                                    onmouseover="this.style.backgroundColor='#1e293b'" 
                                    onmouseout="this.style.backgroundColor='#0a1628'">
                                Detail
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Pagination for Work Realizations -->
            @if($workRealizationsPaginated->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                <div class="flex items-center justify-between">
                    <div class="text-xs text-slate-600">
                        Menampilkan {{ $workRealizationsPaginated->firstItem() ?? 0 }} - {{ $workRealizationsPaginated->lastItem() ?? 0 }} dari {{ $workRealizationsPaginated->total() }} realisasi kerja
                    </div>
                    <div class="flex items-center gap-2">
                        {{ $workRealizationsPaginated->appends(request()->except('realizations_page'))->links() }}
                    </div>
                </div>
            </div>
            @endif
            
            @else
            <div class="py-12 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-medium">Tidak ada realisasi kerja untuk periode yang dipilih</p>
                <p class="text-xs text-slate-400 mt-1">Coba ubah filter atau cari dengan kata kunci lain</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="showDetailModal" x-cloak style="display: none;" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         @click.away="showDetailModal = false"
         @keydown.escape.window="showDetailModal = false">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDetailModal = false"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full" @click.stop>
                <!-- Header -->
                <div class="px-6 pt-6 pb-4" style="background-color: #0a1628;">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-white" x-text="detailData ? detailData.type : 'Detail'"></h3>
                            <p class="text-xs text-gray-300 mt-0.5" x-text="detailData ? detailData.number : ''"></p>
                        </div>
                        <button @click="showDetailModal = false" class="text-gray-300 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div x-show="loadingDetail" class="px-6 py-12 text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2" style="border-color: #0a1628;"></div>
                    <p class="text-sm text-slate-500 mt-2">Memuat detail...</p>
                </div>

                <!-- Error State -->
                <div x-show="!loadingDetail && !detailData && showDetailModal" class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-red-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium text-slate-900">Gagal memuat detail</p>
                    <p class="text-xs text-slate-500 mt-1">Silakan tutup dan coba lagi</p>
                </div>

                <!-- Content -->
                <div x-show="!loadingDetail && detailData" class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                    <template x-if="detailData">
                        <div class="space-y-4">
                            <!-- Basic Information -->
                            <div>
                                <h4 class="text-sm font-semibold text-slate-700 mb-3">Informasi Dasar</h4>
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <dt class="text-xs font-medium text-slate-500">Judul</dt>
                                        <dd class="mt-1 text-sm text-slate-900" x-text="detailData.title"></dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-medium text-slate-500">Tanggal</dt>
                                        <dd class="mt-1 text-sm text-slate-900" x-text="detailData.date"></dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-medium text-slate-500">User</dt>
                                        <dd class="mt-1 text-sm text-slate-900">
                                            <div x-text="detailData.user"></div>
                                            <div class="text-xs text-slate-500" x-text="detailData.user_email"></div>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-medium text-slate-500">Project</dt>
                                        <dd class="mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800" 
                                                  x-text="detailData.project_code + ' - ' + detailData.project"></span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-medium text-slate-500">Lokasi Kerja</dt>
                                        <dd class="mt-1 text-sm text-slate-900" x-text="detailData.location"></dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-medium text-slate-500">Durasi</dt>
                                        <dd class="mt-1 text-sm text-slate-900" x-text="detailData.duration + ' jam'"></dd>
                                    </div>
                                    <template x-if="detailData.department">
                                        <div>
                                            <dt class="text-xs font-medium text-slate-500">Department</dt>
                                            <dd class="mt-1 text-sm text-slate-900" x-text="detailData.department"></dd>
                                        </div>
                                    </template>
                                    <template x-if="detailData.progress !== null && detailData.progress !== undefined">
                                        <div>
                                            <dt class="text-xs font-medium text-slate-500">Progress</dt>
                                            <dd class="mt-1">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-24 rounded-full h-2 overflow-hidden bg-slate-200">
                                                        <div class="h-full rounded-full" 
                                                             :style="'width: ' + detailData.progress + '%; background-color: #0a1628;'"></div>
                                                    </div>
                                                    <span class="text-xs font-semibold" style="color: #0a1628;" 
                                                          x-text="detailData.progress + '%'"></span>
                                                </div>
                                            </dd>
                                        </div>
                                    </template>
                                    <div>
                                        <dt class="text-xs font-medium text-slate-500">Dibuat</dt>
                                        <dd class="mt-1 text-sm text-slate-900" x-text="detailData.created_at"></dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Description -->
                            <div>
                                <h4 class="text-sm font-semibold text-slate-700 mb-2">Deskripsi</h4>
                                <p class="text-sm text-slate-900 whitespace-pre-wrap" x-text="detailData.description || '-'"></p>
                            </div>

                            <!-- Expected Output (Work Plan) -->
                            <template x-if="detailData.expected_output">
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-700 mb-2">Output yang Diharapkan</h4>
                                    <p class="text-sm text-slate-900 whitespace-pre-wrap" x-text="detailData.expected_output"></p>
                                </div>
                            </template>

                            <!-- Output Description (Work Realization) -->
                            <template x-if="detailData.output_description">
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-700 mb-2">Deskripsi Output</h4>
                                    <p class="text-sm text-slate-900 whitespace-pre-wrap" x-text="detailData.output_description"></p>
                                </div>
                            </template>

                            <!-- Related Work Plan (Work Realization) -->
                            <template x-if="detailData.related_work_plan">
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-700 mb-2">Rencana Kerja Terkait</h4>
                                    <div class="bg-slate-50 p-3 rounded-lg">
                                        <p class="text-sm font-medium text-slate-900" x-text="detailData.related_work_plan.number"></p>
                                        <p class="text-xs text-slate-500 mt-1" x-text="detailData.related_work_plan.title"></p>
                                    </div>
                                </div>
                            </template>

                            <!-- Output Files (Work Realization) -->
                            <template x-if="detailData.output_files && detailData.output_files.length > 0">
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-700 mb-2">File Output</h4>
                                    <div class="space-y-2">
                                        <template x-for="file in detailData.output_files" :key="file">
                                            <div class="flex items-center gap-2 text-sm text-slate-900">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span x-text="file"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t">
                    <button @click="showDetailModal = false" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
