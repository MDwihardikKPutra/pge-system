@extends('layouts.app')

@section('title', 'Project: ' . $project->name)
@section('page-title', 'Project Management')
@section('page-subtitle', $project->name . ' - ' . $project->code)

@section('content')
@php
    $managersData = $project->managers->map(fn($m) => [
        'id' => $m->id, 
        'name' => $m->name,
        'access_type' => $m->pivot->access_type ?? 'pm'
    ])->toArray();
    $availableUsersData = $isAdmin ? $allUsers->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email])->toArray() : [];
    $accessTypeLabels = [
        'pm' => 'Project Manager (Work Plans & Realizations)',
        'finance' => 'Finance (Payments Only)',
        'full' => 'Full Access (All)'
    ];
@endphp
<div class="py-8" 
     x-data="projectManagement({{ $project->id }}, {{ $isAdmin ? 'true' : 'false' }}, @js($managersData), @js($availableUsersData), '{{ $accessType ?? 'full' }}')">
    <!-- Preview Modals Container -->
    <div x-data="workPlanPreview()" 
         @open-work-plan-preview.window="previewData = $event.detail; showPreviewModal = true"
         x-show="false" style="display: none;">
        @include('work.work-plans.preview-modal')
    </div>
    
    <div x-data="workRealizationPreview()" 
         @open-work-realization-preview.window="previewData = $event.detail; showPreviewModal = true"
         x-show="false" style="display: none;">
        @include('work.work-realizations.preview-modal')
    </div>
    <!-- Header -->
    <div class="mb-6">
        @php
            $routePrefix = $isAdmin ? 'admin' : 'user';
        @endphp
        <a href="{{ route($routePrefix . '.project-management.index') }}" class="text-blue-600 hover:text-blue-800 mb-3 inline-flex items-center text-sm font-medium">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Daftar Project
        </a>
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm p-4">
            <div class="flex items-start justify-between">
<div>
                    <h1 class="text-xl font-bold text-slate-900">{{ $project->name }}</h1>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="px-2 py-1 rounded text-xs font-medium bg-slate-100 text-slate-700 font-mono">{{ $project->code }}</span>
                        @if($project->client)
                            <span class="text-sm text-slate-600">Client: <span class="font-medium">{{ $project->client }}</span></span>
                        @endif
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $project->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700' }}">
                            {{ $project->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    @if($project->description)
                        <p class="text-sm text-slate-600 mt-2">{{ $project->description }}</p>
                    @endif
                </div>
                @if($isAdmin)
                <div>
                    <button @click="showManagerModal = true" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Kelola Project Manager
                    </button>
                </div>
                @endif
            </div>
            @if($isAdmin && $project->managers->count() > 0)
            <div class="mt-4 pt-4 border-t border-slate-200">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs font-medium text-slate-600">Project Managers:</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($project->managers as $manager)
                    @php
                        $accessType = $manager->pivot->access_type ?? 'pm';
                        $badgeColors = [
                            'pm' => 'bg-blue-50 text-blue-700',
                            'finance' => 'bg-green-50 text-green-700',
                            'full' => 'bg-purple-50 text-purple-700'
                        ];
                        $badgeColor = $badgeColors[$accessType] ?? 'bg-blue-50 text-blue-700';
                        $accessTypeLabel = $accessTypeLabels[$accessType] ?? 'Project Manager';
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium {{ $badgeColor }}">
                        {{ $manager->name }}
                        <span class="text-xs opacity-75">({{ ucfirst($accessType) }})</span>
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-slate-200 mb-6" x-show="canAccessWork || canAccessPayments">
        <nav class="-mb-px flex gap-4">
            <button x-show="canAccessWork" @click="tab = 'work'" :class="tab === 'work' ? 'border-primary-blue text-primary-blue' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="py-3 px-1 border-b-2 font-medium text-sm transition-all" :style="tab === 'work' ? 'border-color: #1e40af; color: #1e40af;' : ''">
                Work Management
            </button>
            <button x-show="canAccessPayments" @click="tab = 'payments'" :class="tab === 'payments' ? 'border-primary-blue text-primary-blue' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="py-3 px-1 border-b-2 font-medium text-sm transition-all" :style="tab === 'payments' ? 'border-color: #1e40af; color: #1e40af;' : ''">
                Data Pembayaran
            </button>
        </nav>
    </div>

    <!-- Work Management Tab -->
    <div x-show="tab === 'work'" class="space-y-4">
        <!-- Work Plans -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
                <h2 class="text-base font-semibold text-white">Rencana Kerja @if($workPlansPaginated)({{ $workPlansPaginated->total() }})@endif</h2>
            </div>
            @if($workPlansPaginated && $workPlansPaginated->total() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">No. Plan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Deskripsi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Lokasi</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Durasi</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workPlans as $plan)
                                <tr class="border-b border-slate-200 last:border-0 hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 font-mono text-xs text-slate-900">{{ $plan->work_plan_number ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $plan->plan_date ? $plan->plan_date->format('d M Y') : '-' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $plan->user->name ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="text-xs text-slate-900">{{ \Illuminate\Support\Str::limit($plan->description ?? $plan->title ?? '-', 60) }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($plan->work_location)
                                            @php
                                                $locationLabels = [
                                                    'office' => 'Office',
                                                    'site' => 'Site',
                                                    'wfh' => 'WFH',
                                                    'wfa' => 'WFA'
                                                ];
                                                $locationValue = is_object($plan->work_location) ? $plan->work_location->value : $plan->work_location;
                                                $locationLabel = $locationLabels[$locationValue] ?? ucfirst($locationValue);
                                            @endphp
                                            <span class="px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-700">{{ $locationLabel }}</span>
                                        @else
                                            <span class="text-xs text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs text-slate-600">
                                        {{ $plan->planned_duration_hours ?? 0 }} jam
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button onclick="openWorkPlanPreviewModal({{ $plan->id }})" class="text-blue-600 hover:text-blue-800 text-xs font-medium transition-colors">
                                            Detail →
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($workPlansPaginated->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $workPlansPaginated->appends(request()->except('work_plans_page'))->links() }}
                </div>
                @endif
            @else
                <div class="py-12 text-center text-xs text-slate-400">Belum ada rencana kerja</div>
            @endif
        </div>

        <!-- Work Realizations -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
                <h2 class="text-base font-semibold text-white">Realisasi Kerja @if($workRealizationsPaginated)({{ $workRealizationsPaginated->total() }})@endif</h2>
            </div>
            @if($workRealizationsPaginated && $workRealizationsPaginated->total() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">No. Realisasi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Deskripsi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Lokasi</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Durasi</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Progress</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workRealizations as $realization)
                                <tr class="border-b border-slate-200 last:border-0 hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 font-mono text-xs text-slate-900">{{ $realization->work_realization_number ?? $realization->realization_number ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $realization->realization_date ? $realization->realization_date->format('d M Y') : '-' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $realization->user->name ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="text-xs text-slate-900">{{ \Illuminate\Support\Str::limit($realization->description ?? '-', 60) }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($realization->work_location)
                                            @php
                                                $locationLabels = [
                                                    'office' => 'Office',
                                                    'site' => 'Site',
                                                    'wfh' => 'WFH',
                                                    'wfa' => 'WFA'
                                                ];
                                                $locationValue = is_object($realization->work_location) ? $realization->work_location->value : $realization->work_location;
                                                $locationLabel = $locationLabels[$locationValue] ?? ucfirst($locationValue);
                                            @endphp
                                            <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700">{{ $locationLabel }}</span>
                                        @else
                                            <span class="text-xs text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs text-slate-600">
                                        {{ $realization->actual_duration_hours ?? 0 }} jam
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-green-600 h-1.5 rounded-full" style="width: {{ $realization->progress_percentage ?? 0 }}%"></div>
                                            </div>
                                            <span class="text-xs text-slate-600">{{ $realization->progress_percentage ?? 0 }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button onclick="openWorkRealizationPreviewModal({{ $realization->id }})" class="text-blue-600 hover:text-blue-800 text-xs font-medium transition-colors">
                                            Detail →
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($workRealizationsPaginated->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $workRealizationsPaginated->appends(request()->except('work_realizations_page'))->links() }}
                </div>
                @endif
            @else
                <div class="py-12 text-center text-xs text-slate-400">Belum ada realisasi kerja</div>
            @endif
        </div>
    </div>

    <!-- Payments Tab -->
    <div x-show="tab === 'payments'" class="space-y-4">
        <!-- SPD -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
                <h2 class="text-base font-semibold text-white">SPD @if($spdPaginated)({{ $spdPaginated->total() }})@endif</h2>
            </div>
            @if($spdPaginated && $spdPaginated->total() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">No. SPD</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Tujuan</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-700">Total Biaya</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($spd as $item)
                                <tr class="border-b border-slate-200 last:border-0 hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 font-mono text-xs text-slate-900">{{ $item->spd_number }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $item->user->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $item->destination }}</td>
                                    <td class="px-4 py-3 text-right text-slate-900 font-semibold">Rp {{ number_format($item->total_cost ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $statusValue = is_object($item->status) ? $item->status->value : $item->status;
                                        @endphp
                                        <span class="px-2 py-1 rounded text-xs font-medium
                                            {{ $statusValue === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $statusValue === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $statusValue === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($statusValue) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($spdPaginated->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $spdPaginated->appends(request()->except('spd_page'))->links() }}
                </div>
                @endif
            @else
                <div class="py-12 text-center text-xs text-slate-400">Belum ada SPD</div>
            @endif
        </div>

        <!-- Purchases -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
                <h2 class="text-base font-semibold text-white">Pembelian @if($purchasesPaginated)({{ $purchasesPaginated->total() }})@endif</h2>
            </div>
            @if($purchasesPaginated && $purchasesPaginated->total() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">No. Pembelian</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Item</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-700">Total Harga</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $item)
                                <tr class="border-b border-slate-200 last:border-0 hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 font-mono text-xs text-slate-900">{{ $item->purchase_number }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $item->user->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $item->item_name }}</td>
                                    <td class="px-4 py-3 text-right text-slate-900 font-semibold">Rp {{ number_format($item->total_price ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $statusValue = is_object($item->status) ? $item->status->value : $item->status;
                                        @endphp
                                        <span class="px-2 py-1 rounded text-xs font-medium
                                            {{ $statusValue === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $statusValue === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $statusValue === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($statusValue) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($purchasesPaginated->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $purchasesPaginated->appends(request()->except('purchases_page'))->links() }}
                </div>
                @endif
            @else
                <div class="py-12 text-center text-xs text-slate-400">Belum ada pembelian</div>
            @endif
        </div>

        <!-- Vendor Payments -->
        <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200" style="background-color: #0a1628;">
                <h2 class="text-base font-semibold text-white">Pembayaran Vendor @if($vendorPaymentsPaginated)({{ $vendorPaymentsPaginated->total() }})@endif</h2>
            </div>
            @if($vendorPaymentsPaginated && $vendorPaymentsPaginated->total() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">No. Pembayaran</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-700">Vendor</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-700">Jumlah</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-700">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendorPayments as $item)
                                <tr class="border-b border-slate-200 last:border-0 hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 font-mono text-xs text-slate-900">{{ $item->payment_number }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $item->user->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $item->vendor->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right text-slate-900 font-semibold">Rp {{ number_format($item->amount ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $statusValue = is_object($item->status) ? $item->status->value : $item->status;
                                        @endphp
                                        <span class="px-2 py-1 rounded text-xs font-medium
                                            {{ $statusValue === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $statusValue === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $statusValue === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($statusValue) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($vendorPaymentsPaginated->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $vendorPaymentsPaginated->appends(request()->except('vendor_payments_page'))->links() }}
                </div>
                @endif
            @else
                <div class="py-12 text-center text-xs text-slate-400">Belum ada pembayaran vendor</div>
            @endif
        </div>
    </div>

    <!-- Project Manager Modal (Admin only) -->
    @if($isAdmin)
    <div x-show="showManagerModal" x-cloak style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" @click.away="showManagerModal = false">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showManagerModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showManagerModal = false"></div>

            <!-- Center modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div x-show="showManagerModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Kelola Project Manager</h3>
                        <button type="button" @click="showManagerModal = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Current Project Managers -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Project Managers Saat Ini</label>
                            <div class="border rounded-lg p-3 min-h-[60px]">
                                @if($project->managers->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($project->managers as $manager)
                                        @php
                                            $accessType = $manager->pivot->access_type ?? 'pm';
                                            $accessTypeLabel = $accessTypeLabels[$accessType] ?? 'Project Manager';
                                            $badgeColors = [
                                                'pm' => 'bg-blue-50 text-blue-700',
                                                'finance' => 'bg-green-50 text-green-700',
                                                'full' => 'bg-purple-50 text-purple-700'
                                            ];
                                            $badgeColor = $badgeColors[$accessType] ?? 'bg-blue-50 text-blue-700';
                                        @endphp
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium {{ $badgeColor }}">
                                            <span>{{ $manager->name }}</span>
                                            <span class="text-xs opacity-75">({{ $accessTypeLabel }})</span>
                                            <button @click="removeManager({{ $manager->id }})" class="hover:opacity-75">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">Belum ada Project Manager yang ditugaskan</p>
                                @endif
                            </div>
                        </div>

                        <!-- Add Project Manager -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tambah Project Manager</label>
                            <div class="space-y-2">
                                <div class="flex gap-2">
                                    <select x-model="selectedUser" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Pilih User</option>
                                        <template x-for="user in availableUsers" :key="user.id">
                                            <option :value="user.id" x-text="user.name + ' (' + user.email + ')'" :disabled="managers.some(m => m.id === user.id)"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="flex gap-2">
                                    <select x-model="selectedAccessType" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="pm">Project Manager (Work Plans & Realizations)</option>
                                        <option value="finance">Finance (Payments Only)</option>
                                        <option value="full">Full Access (All)</option>
                                    </select>
                                    <button @click="assignManager()" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
                                        Tambah
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Pilih user dan tipe akses yang diinginkan</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t">
                    <button type="button" @click="showManagerModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
// Preview modal functions for project management detail page
async function openWorkPlanPreviewModal(id) {
    try {
        const response = await fetch(`/user/work-plans/${id}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if (data.workPlan) {
            // Dispatch event to open preview modal
            window.dispatchEvent(new CustomEvent('open-work-plan-preview', { detail: data.workPlan }));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Gagal memuat data rencana kerja');
    }
}

async function openWorkRealizationPreviewModal(id) {
    try {
        const response = await fetch(`/user/work-realizations/${id}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if (data.workRealization) {
            // Dispatch event to open preview modal
            window.dispatchEvent(new CustomEvent('open-work-realization-preview', { detail: data.workRealization }));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Gagal memuat data realisasi kerja');
    }
}
</script>
@endpush

@push('alpine-init')
<script>
document.addEventListener('alpine:init', () => {
    // Work Plan Preview Component
    Alpine.data('workPlanPreview', () => ({
        showPreviewModal: false,
        previewData: null,
        
        async openPreviewModal(planId) {
            try {
                const response = await fetch(`/user/work-plans/${planId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                if (data.workPlan) {
                    this.previewData = data.workPlan;
                    this.showPreviewModal = true;
                }
            } catch (error) {
                console.error('Error fetching work plan:', error);
                alert('Gagal memuat data rencana kerja');
            }
        },
        
        closePreviewModal() {
            this.showPreviewModal = false;
            this.previewData = null;
        }
    }));
    
    // Work Realization Preview Component
    Alpine.data('workRealizationPreview', () => ({
        showPreviewModal: false,
        previewData: null,
        
        async openPreviewModal(realizationId) {
            try {
                const response = await fetch(`/user/work-realizations/${realizationId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                if (data.workRealization) {
                    this.previewData = data.workRealization;
                    this.showPreviewModal = true;
                }
            } catch (error) {
                console.error('Error fetching work realization:', error);
                alert('Gagal memuat data realisasi kerja');
            }
        },
        
        closePreviewModal() {
            this.showPreviewModal = false;
            this.previewData = null;
        }
    }));
    
    Alpine.data('projectManagement', (projectId, isAdmin, managersData, availableUsersData, accessType) => {
        // Determine which tabs user can access
        const canAccessWork = isAdmin || accessType === 'pm' || accessType === 'full';
        const canAccessPayments = isAdmin || accessType === 'finance' || accessType === 'full';
        
        // Set initial tab based on access
        let initialTab = 'work';
        if (!canAccessWork && canAccessPayments) {
            initialTab = 'payments';
        }
        
        return {
        tab: initialTab,
        showManagerModal: false,
        selectedUser: '',
        selectedAccessType: 'pm',
        managers: managersData || [],
        availableUsers: availableUsersData || [],
        projectId: projectId,
        isAdmin: isAdmin,
        canAccessWork: canAccessWork,
        canAccessPayments: canAccessPayments,
        
        async loadAvailableUsers() {
            if (!this.isAdmin) return;
            
            try {
                const response = await fetch(`/admin/project-managers/projects/${this.projectId}/available-users`);
                const data = await response.json();
                this.availableUsers = data;
            } catch (error) {
                console.error('Error loading users:', error);
            }
        },
        
        async assignManager() {
            if (!this.selectedUser) {
                alert('Pilih user terlebih dahulu');
                return;
            }
            
            if (!this.selectedAccessType) {
                alert('Pilih tipe akses terlebih dahulu');
                return;
            }
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const response = await fetch(`/admin/project-managers/projects/${this.projectId}/assign`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ 
                        user_id: this.selectedUser,
                        access_type: this.selectedAccessType
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.selectedUser = '';
                    this.selectedAccessType = 'pm';
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal menambahkan Project Manager');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menambahkan Project Manager');
            }
        },
        
        async removeManager(userId) {
            if (!confirm('Apakah Anda yakin ingin menghapus Project Manager ini?')) {
                return;
            }
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const response = await fetch(`/admin/project-managers/projects/${this.projectId}/remove`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ user_id: userId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal menghapus Project Manager');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus Project Manager');
            }
        },
        
        init() {
            if (this.isAdmin && this.availableUsers.length === 0) {
                // Load available users when modal is opened
                this.$watch('showManagerModal', value => {
                    if (value && this.availableUsers.length === 0) {
                        this.loadAvailableUsers();
                    }
                });
            }
        }
        };
    });
});
</script>
@endpush

@endsection
