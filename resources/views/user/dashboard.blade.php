@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas Anda')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="flex items-center justify-between">
        <div>
            @php
                $hour = now()->hour;
                $greeting = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
            @endphp
            <h1 class="text-xl font-semibold mb-0.5" style="color: #0a1628;">{{ $greeting }}, {{ auth()->user()->name }} 👋</h1>
            <p class="text-xs" style="color: #0a1628;">Berikut ringkasan aktivitas Anda hari ini</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            @if(isset($moduleData['work-plan']))
            <a href="{{ route('user.work-plans.index') }}" 
               class="inline-flex items-center gap-1.5 px-3 py-2 text-white text-sm font-medium rounded-lg shadow-sm transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Rencana Kerja
            </a>
            @endif
        </div>
    </div>

    <!-- Statistics Overview Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @if(isset($moduleData['work-plan']))
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs mb-1" style="color: #0a1628;">Rencana Kerja</p>
                    <p class="text-2xl font-bold" style="color: #0a1628;">{{ $moduleData['work-plan']['count'] ?? 0 }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm" style="background-color: #0a1628;">
                    <span>🗓️</span>
                </div>
            </div>
        </div>
        @endif

        @if(isset($moduleData['work-realization']))
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs mb-1" style="color: #0a1628;">Realisasi Kerja</p>
                    <p class="text-2xl font-bold" style="color: #0a1628;">{{ $moduleData['work-realization']['count'] ?? 0 }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm" style="background-color: #0a1628;">
                    <span>✅</span>
                </div>
            </div>
        </div>
        @endif

        @if(isset($moduleData['spd']))
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs mb-1" style="color: #0a1628;">SPD</p>
                    <p class="text-2xl font-bold" style="color: #0a1628;">{{ $moduleData['spd']['count'] ?? 0 }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm" style="background-color: #0a1628;">
                    <span>✈️</span>
                </div>
            </div>
        </div>
        @endif

        @if(isset($moduleData['purchase']))
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs mb-1" style="color: #0a1628;">Pembelian</p>
                    <p class="text-2xl font-bold" style="color: #0a1628;">{{ $moduleData['purchase']['count'] ?? 0 }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm" style="background-color: #0a1628;">
                    <span>🛒</span>
                </div>
            </div>
        </div>
        @endif

        @if(isset($moduleData['vendor-payment']))
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs mb-1" style="color: #0a1628;">Pembayaran Vendor</p>
                    <p class="text-2xl font-bold" style="color: #0a1628;">{{ $moduleData['vendor-payment']['count'] ?? 0 }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm" style="background-color: #0a1628;">
                    <span>💳</span>
                </div>
            </div>
        </div>
        @endif

        @if(isset($moduleData['leave']))
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs mb-1" style="color: #0a1628;">Cuti & Izin</p>
                    <p class="text-2xl font-bold" style="color: #0a1628;">{{ $moduleData['leave']['count'] ?? 0 }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm" style="background-color: #0a1628;">
                    <span>🏝️</span>
                </div>
            </div>
        </div>
        @endif

        @if((isset($moduleData['project-management']) || isset($moduleData['project-monitoring'])))
        @php
            $projectData = $moduleData['project-management'] ?? $moduleData['project-monitoring'] ?? null;
        @endphp
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs mb-1" style="color: #0a1628;">Project</p>
                    <p class="text-2xl font-bold" style="color: #0a1628;">{{ $projectData['count'] ?? 0 }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm" style="background-color: #0a1628;">
                    <span>📁</span>
                </div>
            </div>
        </div>
        @endif

        @if(isset($moduleData['payment-approval']))
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs mb-1" style="color: #0a1628;">Approval</p>
                    <p class="text-2xl font-bold" style="color: #0a1628;">{{ $moduleData['payment-approval']['count'] ?? 0 }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm" style="background-color: #0a1628;">
                    <span>⏳</span>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Module Preview Widgets -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @if(isset($moduleData['work-plan']))
        <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between" style="background-color: #0a1628;">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🗓️</span>
                    <h3 class="text-sm font-semibold text-white">Rencana Kerja</h3>
                </div>
                <a href="{{ route('user.work-plans.index') }}" class="text-xs text-gray-300 hover:text-white font-medium">→</a>
            </div>
            <div class="p-4">
                @if($moduleData['work-plan']['recent']->count() > 0)
                    <div class="space-y-2">
                        @foreach($moduleData['work-plan']['recent']->take(5) as $plan)
                        <div class="flex items-start justify-between py-2 border-b border-slate-100 last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium truncate" style="color: #0a1628;">{{ $plan->title ?? \Illuminate\Support\Str::limit($plan->description, 50) }}</p>
                                <p class="text-[10px] text-slate-500 mt-0.5">{{ $plan->plan_date->format('d M Y') }}</p>
                            </div>
                            <button onclick="window.openWorkPlanPreview && window.openWorkPlanPreview({{ $plan->id }})" class="ml-2 text-xs whitespace-nowrap hover:underline" style="color: #0a1628;">Detail</button>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada rencana kerja</p>
                @endif
            </div>
        </div>
        @endif

        @if(isset($moduleData['work-realization']))
        <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between" style="background-color: #0a1628;">
                <div class="flex items-center gap-2">
                    <span class="text-lg">✅</span>
                    <h3 class="text-sm font-semibold text-white">Realisasi Kerja</h3>
                </div>
                <a href="{{ route('user.work-realizations.index') }}" class="text-xs text-gray-300 hover:text-white font-medium">→</a>
            </div>
            <div class="p-4">
                @if($moduleData['work-realization']['recent']->count() > 0)
                    <div class="space-y-2">
                        @foreach($moduleData['work-realization']['recent']->take(5) as $realization)
                        <div class="flex items-start justify-between py-2 border-b border-slate-100 last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium truncate" style="color: #0a1628;">{{ $realization->title ?? \Illuminate\Support\Str::limit($realization->description, 50) }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <p class="text-[10px] text-slate-500">{{ $realization->realization_date->format('d M Y') }}</p>
                                    <div class="flex items-center gap-1">
                                    <div class="w-12 bg-slate-200 rounded-full h-1.5">
                                        <div class="h-full rounded-full" style="width: {{ $realization->progress_percentage ?? 0 }}%; background-color: #0a1628;"></div>
                                    </div>
                                        <span class="text-[10px] text-slate-500">{{ $realization->progress_percentage ?? 0 }}%</span>
                                    </div>
                                </div>
                            </div>
                            <button onclick="window.openWorkRealizationPreview && window.openWorkRealizationPreview({{ $realization->id }})" class="ml-2 text-xs whitespace-nowrap hover:underline" style="color: #0a1628;">Detail</button>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada realisasi kerja</p>
                @endif
            </div>
        </div>
        @endif

        @if(isset($moduleData['spd']))
        <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between" style="background-color: #0a1628;">
                <div class="flex items-center gap-2">
                    <span class="text-lg">✈️</span>
                    <h3 class="text-sm font-semibold text-white">SPD</h3>
                </div>
                <a href="{{ route('user.spd.index') }}" class="text-xs text-gray-300 hover:text-white font-medium">→</a>
            </div>
            <div class="p-4">
                @if($moduleData['spd']['recent']->count() > 0)
                    <div class="space-y-2">
                        @foreach($moduleData['spd']['recent']->take(5) as $spd)
                        <div class="flex items-start justify-between py-2 border-b border-slate-100 last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium truncate" style="color: #0a1628;">{{ $spd->spd_number }}</p>
                                <p class="text-[10px] text-slate-500 mt-0.5">{{ $spd->destination }}</p>
                            </div>
                            <div class="ml-2 flex items-center gap-2">
                                @php
                                    $statusValue = is_object($spd->status ?? null) ? $spd->status->value : ($spd->status ?? 'pending');
                                    $statusClass = match($statusValue) {
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium {{ $statusClass }}">{{ ucfirst($statusValue) }}</span>
                                <button onclick="window.openSpdPreview && window.openSpdPreview({{ $spd->id }})" class="text-xs whitespace-nowrap hover:underline" style="color: #0a1628;">Detail</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada SPD</p>
                @endif
            </div>
        </div>
        @endif

        @if(isset($moduleData['purchase']))
        <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between" style="background-color: #0a1628;">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🛒</span>
                    <h3 class="text-sm font-semibold text-white">Pembelian</h3>
                </div>
                <a href="{{ route('user.purchases.index') }}" class="text-xs text-gray-300 hover:text-white font-medium">→</a>
            </div>
            <div class="p-4">
                @if($moduleData['purchase']['recent']->count() > 0)
                    <div class="space-y-2">
                        @foreach($moduleData['purchase']['recent']->take(5) as $purchase)
                        <div class="flex items-start justify-between py-2 border-b border-slate-100 last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium truncate" style="color: #0a1628;">{{ $purchase->purchase_number }}</p>
                                <p class="text-[10px] text-slate-500 mt-0.5">{{ $purchase->item_name }}</p>
                            </div>
                            <div class="ml-2 flex items-center gap-2">
                                @php
                                    $statusValue = is_object($purchase->status ?? null) ? $purchase->status->value : ($purchase->status ?? 'pending');
                                    $statusClass = match($statusValue) {
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium {{ $statusClass }}">{{ ucfirst($statusValue) }}</span>
                                <button onclick="window.openPurchasePreview && window.openPurchasePreview({{ $purchase->id }})" class="text-xs whitespace-nowrap hover:underline" style="color: #0a1628;">Detail</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada pembelian</p>
                @endif
            </div>
        </div>
        @endif

        @if(isset($moduleData['vendor-payment']))
        <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between" style="background-color: #0a1628;">
                <div class="flex items-center gap-2">
                    <span class="text-lg">💳</span>
                    <h3 class="text-sm font-semibold text-white">Pembayaran Vendor</h3>
                </div>
                <a href="{{ route('user.vendor-payments.index') }}" class="text-xs text-gray-300 hover:text-white font-medium">→</a>
            </div>
            <div class="p-4">
                @if($moduleData['vendor-payment']['recent']->count() > 0)
                    <div class="space-y-2">
                        @foreach($moduleData['vendor-payment']['recent']->take(5) as $payment)
                        <div class="flex items-start justify-between py-2 border-b border-slate-100 last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium truncate" style="color: #0a1628;">{{ $payment->payment_number }}</p>
                                <p class="text-[10px] text-slate-500 mt-0.5">{{ $payment->vendor->name ?? '-' }}</p>
                            </div>
                            <div class="ml-2 flex items-center gap-2">
                                @php
                                    $statusValue = is_object($payment->status ?? null) ? $payment->status->value : ($payment->status ?? 'pending');
                                    $statusClass = match($statusValue) {
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium {{ $statusClass }}">{{ ucfirst($statusValue) }}</span>
                                <button onclick="window.openVendorPaymentPreview && window.openVendorPaymentPreview({{ $payment->id }})" class="text-xs whitespace-nowrap hover:underline" style="color: #0a1628;">Detail</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada pembayaran vendor</p>
                @endif
            </div>
        </div>
        @endif

        @if(isset($moduleData['leave']))
        <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between" style="background-color: #0a1628;">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🏝️</span>
                    <h3 class="text-sm font-semibold text-white">Cuti & Izin</h3>
                </div>
                <a href="{{ route('user.leaves.index') }}" class="text-xs text-gray-300 hover:text-white font-medium">→</a>
            </div>
            <div class="p-4">
                @if($moduleData['leave']['recent']->count() > 0)
                    <div class="space-y-2">
                        @foreach($moduleData['leave']['recent']->take(5) as $leave)
                        <div class="flex items-start justify-between py-2 border-b border-slate-100 last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium truncate" style="color: #0a1628;">{{ $leave->leave_number }}</p>
                                <p class="text-[10px] text-slate-500 mt-0.5">{{ $leave->leaveType->name ?? '-' }} • {{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M Y') }}</p>
                            </div>
                            <div class="ml-2 flex items-center gap-2">
                                @php
                                    $statusValue = is_object($leave->status ?? null) ? $leave->status->value : ($leave->status ?? 'pending');
                                    $statusClass = match($statusValue) {
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium {{ $statusClass }}">{{ ucfirst($statusValue) }}</span>
                                <button onclick="window.openLeavePreview && window.openLeavePreview({{ $leave->id }})" class="text-xs whitespace-nowrap hover:underline" style="color: #0a1628;">Detail</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada pengajuan cuti</p>
                @endif
            </div>
        </div>
        @endif

        @if((isset($moduleData['project-management']) || isset($moduleData['project-monitoring'])) && ($moduleData['project-management']['recent']->count() ?? $moduleData['project-monitoring']['recent']->count() ?? 0) > 0)
        @php
            $projectData = $moduleData['project-management'] ?? $moduleData['project-monitoring'] ?? null;
        @endphp
        <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between" style="background-color: #0a1628;">
                <div class="flex items-center gap-2">
                    <span class="text-lg">📁</span>
                    <h3 class="text-sm font-semibold text-white">Project Management</h3>
                </div>
                <a href="{{ route('user.project-management.index') }}" class="text-xs text-gray-300 hover:text-white font-medium">→</a>
            </div>
            <div class="p-4">
                <div class="space-y-2">
                    @foreach($projectData['recent']->take(5) as $project)
                    <div class="flex items-start justify-between py-2 border-b border-slate-100 last:border-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium truncate" style="color: #0a1628;">{{ $project->name }}</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">{{ $project->code }}</p>
                        </div>
                        <a href="{{ route('user.project-management.show', $project) }}" class="ml-2 text-xs whitespace-nowrap hover:underline" style="color: #0a1628;">Detail</a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Global preview functions for dashboard links - redirect to index pages
window.openWorkPlanPreview = function(id) {
    window.location.href = `/user/work-plans?preview=${id}`;
};

window.openWorkRealizationPreview = function(id) {
    window.location.href = `/user/work-realizations?preview=${id}`;
};

window.openLeavePreview = function(id) {
    window.location.href = `/user/leaves?preview=${id}`;
};

window.openSpdPreview = function(id) {
    window.location.href = `/user/spd?preview=${id}`;
};

window.openPurchasePreview = function(id) {
    window.location.href = `/user/purchases?preview=${id}`;
};

window.openVendorPaymentPreview = function(id) {
    window.location.href = `/user/vendor-payments?preview=${id}`;
};
</script>
@endpush
@endsection
