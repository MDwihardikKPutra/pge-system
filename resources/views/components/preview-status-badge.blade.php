<div>
    <template x-if="getStatusValue(previewData?.status) === 'pending'">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
            Pending
        </span>
    </template>
    <template x-if="getStatusValue(previewData?.status) === 'approved'">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">
            ✓ Disetujui
        </span>
    </template>
    <template x-if="getStatusValue(previewData?.status) === 'rejected'">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">
            ✗ Ditolak
        </span>
    </template>
</div>






