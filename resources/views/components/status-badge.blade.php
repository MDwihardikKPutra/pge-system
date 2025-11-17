@props(['status', 'statusValue' => null])

@php
    $statusValue = $statusValue ?? (is_object($status) ? $status->value : $status);
@endphp

@if($statusValue === 'pending')
    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
        Pending
    </span>
@elseif($statusValue === 'approved')
    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">
        ✓ Disetujui
    </span>
@elseif($statusValue === 'rejected')
    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">
        ✗ Ditolak
    </span>
@endif







