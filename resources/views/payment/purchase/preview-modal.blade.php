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
                    <h3 class="text-lg font-semibold text-gray-900">Preview Pembelian</h3>
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                                <p class="text-sm text-gray-900" x-text="previewData?.project?.name ? (previewData.project.name + (previewData.project.code ? ' (' + previewData.project.code + ')' : '')) : '-'"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Pembelian</label>
                                <p class="text-sm text-gray-900" x-text="previewData?.type ? (previewData.type === 'Barang' ? 'Barang' : previewData.type === 'Jasa' ? 'Jasa' : previewData.type === 'Aset' ? 'Aset' : previewData.type) : '-'"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <p class="text-sm text-gray-900" x-text="previewData?.category || '-'"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Item</label>
                                <p class="text-sm text-gray-900" x-text="previewData?.item_name || '-'"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                                <p class="text-sm text-gray-900" x-text="(previewData?.quantity || 0) + ' ' + (previewData?.unit || '')"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan (Rp)</label>
                                <p class="text-sm text-gray-900" x-text="'Rp ' + (previewData?.unit_price || 0).toLocaleString('id-ID')"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi / Spesifikasi -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b">Deskripsi / Spesifikasi</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-700 whitespace-pre-wrap" x-text="previewData?.description || '-'"></p>
                        </div>
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

            <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                <div>
                    <a x-show="previewData?.status === 'approved' || previewData?.status?.value === 'approved'" 
                       :href="'{{ url('/') }}/{{ request()->routeIs('admin.*') ? 'admin' : 'user' }}/purchases/' + previewData?.id + '/pdf'" 
                       target="_blank" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download PDF
                    </a>
                </div>
                <button type="button" @click="closePreviewModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm font-medium">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>


