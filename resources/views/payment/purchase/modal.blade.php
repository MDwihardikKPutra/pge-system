<div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full">
            <form @submit.prevent="submitForm()">
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900" x-text="modalTitle"></h3>
                        <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Informasi Dasar -->
                        <div>
                            <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b">Informasi Dasar</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <!-- Project -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Project <span class="text-red-500">*</span></label>
                                    <div class="project-select-container relative" data-parent-form="purchaseForm">
                                        <input type="hidden" name="project_id" class="project-select-hidden" required>
                                        
                                        <div class="relative">
                                            <input 
                                                type="text"
                                                class="project-select-input w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pr-10 text-sm"
                                                placeholder="Cari atau pilih project..."
                                                autocomplete="off"
                                                required
                                            >
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Dropdown Results -->
                                        <div class="project-select-dropdown absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto hidden">
                                            <div class="project-select-loading p-3 text-center text-sm text-gray-500 hidden">
                                                Mencari...
                                            </div>
                                            <div class="project-select-empty p-3 text-center text-sm text-gray-500 hidden">
                                                Tidak ada project ditemukan
                                            </div>
                                            <ul class="project-select-list py-1 hidden"></ul>
                                        </div>

                                        <!-- Clear Button -->
                                        <button 
                                            type="button"
                                            class="project-select-clear absolute right-8 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <span x-show="errors.project_id" class="text-xs text-red-600" x-text="errors.project_id?.[0]"></span>
                                </div>

                                <!-- Type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe Pembelian <span class="text-red-500">*</span></label>
                                    <select x-model="formData.type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Pilih Tipe</option>
                                        <option value="Barang">Barang</option>
                                        <option value="Jasa">Jasa</option>
                                        <option value="Aset">Aset</option>
                                    </select>
                                    <span x-show="errors.type" class="text-xs text-red-600" x-text="errors.type?.[0]"></span>
                                </div>

                                <!-- Category -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                                    <select x-model="formData.category" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Pilih Kategori</option>
                                        <option value="IT & Teknologi">IT & Teknologi</option>
                                        <option value="Alat Tulis Kantor">Alat Tulis Kantor</option>
                                        <option value="Peralatan">Peralatan</option>
                                        <option value="Perlengkapan">Perlengkapan</option>
                                        <option value="Jasa Profesional">Jasa Profesional</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                    <span x-show="errors.category" class="text-xs text-red-600" x-text="errors.category?.[0]"></span>
                                </div>

                                <!-- Item Name -->
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Item <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="formData.item_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Laptop Dell XPS 15">
                                    <span x-show="errors.item_name" class="text-xs text-red-600" x-text="errors.item_name?.[0]"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Item -->
                        <div>
                            <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b">Detail Item</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <!-- Quantity -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                                    <input type="number" x-model="formData.quantity" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="1" @input="updateTotalPrice()">
                                    <span x-show="errors.quantity" class="text-xs text-red-600" x-text="errors.quantity?.[0]"></span>
                                </div>

                                <!-- Unit -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Satuan <span class="text-red-500">*</span></label>
                                    <select x-model="formData.unit" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Pilih Satuan</option>
                                        <option value="Unit">Unit</option>
                                        <option value="Pcs">Pcs</option>
                                        <option value="Set">Set</option>
                                        <option value="Paket">Paket</option>
                                        <option value="Box">Box</option>
                                        <option value="Bulan">Bulan</option>
                                        <option value="Tahun">Tahun</option>
                                    </select>
                                    <span x-show="errors.unit" class="text-xs text-red-600" x-text="errors.unit?.[0]"></span>
                                </div>

                                <!-- Unit Price -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga Satuan (Rp) <span class="text-red-500">*</span></label>
                                    <input type="number" x-model="formData.unit_price" min="0" step="1000" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="0" @input="updateTotalPrice()">
                                    <span x-show="errors.unit_price" class="text-xs text-red-600" x-text="errors.unit_price?.[0]"></span>
                                </div>
                            </div>

                            <!-- Total Price -->
                            <div class="mt-3 bg-blue-50 border border-blue-200 rounded p-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-gray-700">Total Harga</span>
                                    <div class="text-right text-base font-bold text-blue-600" x-text="'Rp ' + totalPriceDisplay"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Description & Notes -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi / Spesifikasi <span class="text-red-500">*</span></label>
                                <textarea x-model="formData.description" rows="2" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Jelaskan detail spesifikasi, keperluan, atau alasan pembelian..."></textarea>
                                <span x-show="errors.description" class="text-xs text-red-600" x-text="errors.description?.[0]"></span>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan Tambahan (Opsional)</label>
                                <textarea x-model="formData.notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Catatan atau informasi tambahan..."></textarea>
                                <span x-show="errors.notes" class="text-xs text-red-600" x-text="errors.notes?.[0]"></span>
                            </div>
                        </div>

                        <!-- Dokumen Pendukung -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Upload Dokumen Pendukung (Opsional)</label>
                            <input type="file" @change="handleFileChange($event)" multiple accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <p class="text-xs text-gray-500 mt-1">Format: PDF, JPG, PNG (Max 2MB per file)</p>
                            <span x-show="errors.documents" class="text-xs text-red-600" x-text="errors.documents?.[0]"></span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t">
                    <button type="button" @click="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
                        <span x-text="editMode ? 'Update' : 'Ajukan Pembelian'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
