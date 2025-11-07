<x-preview-modal title="Preview SPD">
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Perjalanan</label>
                                <p class="text-sm text-gray-900" x-text="previewData?.destination || '-'"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keperluan/Tujuan</label>
                                <p class="text-sm text-gray-900" x-text="previewData?.purpose || '-'"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berangkat</label>
                                <p class="text-sm text-gray-900" x-text="formatDate(previewData?.departure_date)"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kembali</label>
                                <p class="text-sm text-gray-900" x-text="formatDate(previewData?.return_date)"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Biaya -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b">Detail Biaya</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Nama Biaya</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Deskripsi</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-700 uppercase">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="(cost, index) in (previewData?.costs || [])" :key="index">
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900" x-text="cost.name || '-'"></td>
                                            <td class="px-4 py-2 text-sm text-gray-600" x-text="cost.description || '-'"></td>
                                            <td class="px-4 py-2 text-sm text-gray-900 text-right" x-text="formatCurrency(cost.amount)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="2" class="px-4 py-2 text-sm font-semibold text-gray-900 text-right">Total</td>
                                        <td class="px-4 py-2 text-sm font-semibold text-gray-900 text-right" x-text="formatCurrency(previewData?.total_cost)"></td>
                                    </tr>
                                </tfoot>
                            </table>
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
                        <x-preview-status-badge />
                    </div>
    </div>
</x-preview-modal>

