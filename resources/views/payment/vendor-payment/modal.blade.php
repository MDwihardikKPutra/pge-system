<div x-show="showModal" x-cloak style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
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
                                <!-- Vendor -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Vendor <span class="text-red-500">*</span></label>
                                    <select x-model="formData.vendor_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Pilih Vendor</option>
                                        <template x-for="vendor in vendors" :key="vendor.id">
                                            <option :value="vendor.id" x-text="vendor.name + ' - ' + (vendor.company || '')"></option>
                                        </template>
                                    </select>
                                    <span x-show="errors.vendor_id" class="text-xs text-red-600" x-text="errors.vendor_id?.[0]"></span>
                                </div>

                                <!-- Project -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Project <span class="text-red-500">*</span></label>
                                    <div x-data="projectSearchableSelect(null, formData.project_id || null)" 
                                    x-init="
                                        let isInitialized = false;
                                        $watch('selectedId', value => {
                                            if (isInitialized && formData.project_id !== value) {
                                                formData.project_id = value;
                                            }
                                        });
                                        $watch('formData.project_id', value => {
                                            if (!isInitialized) {
                                                isInitialized = true;
                                                return;
                                            }
                                            if (value && selectedId !== value) {
                                                fetchProjectById(value);
                                            } else if (!value && selectedId) {
                                                clearSelection();
                                            }
                                        });
                                    "
                                    class="relative">
                                        <input type="hidden" :value="selectedId" required>
                                        
                                        <div class="relative">
                                            <input 
                                                type="text"
                                                x-model="searchQuery"
                                                @input="searchProjects()"
                                                @focus="showDropdown = true"
                                                @blur="setTimeout(() => showDropdown = false, 200)"
                                                placeholder="Cari atau pilih project..."
                                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pr-10 text-sm"
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
                                        <div x-show="showDropdown && (searching || projects.length > 0)" 
                                             x-cloak
                                             x-transition
                                             class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto">
                                            <div x-show="searching" class="p-3 text-center text-sm text-gray-500">
                                                Mencari...
                                            </div>
                                            <template x-if="!searching && projects.length === 0 && searchQuery.length > 0">
                                                <div class="p-3 text-center text-sm text-gray-500">
                                                    Tidak ada project ditemukan
                                                </div>
                                            </template>
                                            <template x-if="!searching && projects.length > 0">
                                                <ul class="py-1">
                                        <template x-for="project in projects" :key="project.id">
                                                        <li>
                                                            <button 
                                                                type="button"
                                                                @click="selectProject(project)"
                                                                class="w-full text-left px-4 py-2 hover:bg-blue-50 focus:bg-blue-50 focus:outline-none transition-colors"
                                                                :class="{ 'bg-blue-50': selectedId == project.id }"
                                                            >
                                                                <div class="font-medium text-gray-900" x-text="project.name"></div>
                                                                <div class="text-xs text-gray-500" x-text="project.code"></div>
                                                            </button>
                                                        </li>
                                                    </template>
                                                </ul>
                                        </template>
                                        </div>

                                        <!-- Clear Button -->
                                        <button 
                                            type="button"
                                            x-show="selectedId"
                                            @click="clearSelection()"
                                            class="absolute right-8 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <span x-show="errors.project_id" class="text-xs text-red-600" x-text="errors.project_id?.[0]"></span>
                                </div>

                                <!-- Payment Type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe Pembayaran <span class="text-red-500">*</span></label>
                                    <select x-model="formData.payment_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="">Pilih Tipe</option>
                                        <option value="DP">Down Payment (DP)</option>
                                        <option value="Termin">Termin</option>
                                        <option value="Pelunasan">Pelunasan</option>
                                        <option value="Full Payment">Full Payment</option>
                                    </select>
                                    <span x-show="errors.payment_type" class="text-xs text-red-600" x-text="errors.payment_type?.[0]"></span>
                                </div>

                                <!-- Payment Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Pembayaran <span class="text-red-500">*</span></label>
                                    <input type="date" x-model="formData.payment_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    <span x-show="errors.payment_date" class="text-xs text-red-600" x-text="errors.payment_date?.[0]"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Pembayaran -->
                        <div>
                            <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b">Detail Pembayaran</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <!-- Invoice Number -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Invoice <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="formData.invoice_number" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="INV-2025-0001">
                                    <span x-show="errors.invoice_number" class="text-xs text-red-600" x-text="errors.invoice_number?.[0]"></span>
                                </div>

                                <!-- PO Number -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor PO (Opsional)</label>
                                    <input type="text" x-model="formData.po_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="PO-2025-0001">
                                    <span x-show="errors.po_number" class="text-xs text-red-600" x-text="errors.po_number?.[0]"></span>
                                </div>

                                <!-- Amount -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Pembayaran (Rp) <span class="text-red-500">*</span></label>
                                    <input type="number" x-model="formData.amount" min="0" step="1000" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="0">
                                    <span x-show="errors.amount" class="text-xs text-red-600" x-text="errors.amount?.[0]"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Description & Notes -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Pembayaran <span class="text-red-500">*</span></label>
                                <textarea x-model="formData.description" rows="2" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Jelaskan detail pembayaran, item yang dibeli, atau keperluan..."></textarea>
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
                            <p class="text-xs text-gray-500 mt-1">Upload invoice, PO, atau dokumen pendukung lainnya. Format: PDF, JPG, PNG (Max 2MB per file)</p>
                            <span x-show="errors.documents" class="text-xs text-red-600" x-text="errors.documents?.[0]"></span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t">
                    <button type="button" @click="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
                        <span x-text="editMode ? 'Update' : 'Ajukan Pembayaran'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
