<div x-show="showPreviewModal" x-cloak style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="showPreviewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closePreviewModal()"></div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div x-show="showPreviewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-6 pt-6 pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Preview Pembayaran Vendor</h3>
                    <button type="button" @click="closePreviewModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4" x-show="previewData">
                    <!-- Informasi Dasar -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b">Informasi Dasar</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. Pembayaran</label>
                                <p class="text-sm text-gray-900 font-mono" x-text="previewData?.payment_number || '-'"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                                <p class="text-sm text-gray-900" x-text="previewData?.vendor?.name || '-'"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                                <p class="text-sm text-gray-900" x-text="previewData?.project?.name || '-'"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pembayaran</label>
                                <p class="text-sm text-gray-900" x-text="previewData?.payment_type ? (previewData.payment_type === 'project' ? 'Project' : previewData.payment_type === 'kantor' ? 'Kantor' : 'Lainnya') : '-'"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. Invoice</label>
                                <p class="text-sm text-gray-900" x-text="previewData?.invoice_number || '-'"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. PO</label>
                                <p class="text-sm text-gray-900" x-text="previewData?.po_number || '-'"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembayaran</label>
                                <p class="text-sm text-gray-900" x-text="previewData?.payment_date ? new Date(previewData.payment_date).toLocaleDateString('id-ID') : '-'"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                                <p class="text-sm text-gray-900 font-semibold" x-text="'Rp ' + (previewData?.amount || 0).toLocaleString('id-ID')"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div x-show="previewData?.description">
                        <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b">Deskripsi</h3>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap" x-text="previewData?.description || '-'"></p>
                    </div>

                    <!-- Catatan -->
                    <div x-show="previewData?.notes">
                        <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b">Catatan</h3>
                        <p class="text-sm text-gray-700" x-text="previewData?.notes || '-'"></p>
                    </div>

                    <!-- Status -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b">Status</h3>
                        <div>
                            <template x-if="previewData?.status === 'pending' || (previewData?.status?.value === 'pending')">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
                                    Pending
                                </span>
                            </template>
                            <template x-if="previewData?.status === 'approved' || (previewData?.status?.value === 'approved')">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                                    ✓ Disetujui
                                </span>
                            </template>
                            <template x-if="previewData?.status === 'rejected' || (previewData?.status?.value === 'rejected')">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                                    ✗ Ditolak
                                </span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex justify-end">
                <button type="button" @click="closePreviewModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm font-medium">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>


