<!-- Modal Background Overlay -->
<div x-show="showModal" 
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-gray-500 bg-opacity-75 z-40"
    @click="closeModal()"
    style="display: none;"></div>

<!-- Modal Dialog -->
<div x-show="showModal" 
    x-cloak
    @keydown.escape.window="closeModal()"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;">
    
    <div class="flex min-h-screen items-center justify-center p-4">
        <div @click.away="closeModal()" class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="px-6 py-5 sticky top-0 z-10 rounded-t-lg" style="background-color: #0a1628;">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-white bg-opacity-10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">
                                <span x-show="modalMode === 'create'">Realisasi Kerja Baru</span>
                                <span x-show="modalMode === 'edit'">Edit Realisasi Kerja</span>
                                <span x-show="modalMode === 'view'">Detail Realisasi Kerja</span>
                            </h3>
                            <p class="text-sm text-gray-300 mt-0.5" x-show="modalMode === 'create'">Catat pencapaian kerja harian Anda</p>
                            <p class="text-sm text-gray-300 mt-0.5" x-show="modalMode === 'edit'">Update realisasi kerja Anda</p>
                            <p class="text-sm text-gray-300 mt-0.5" x-show="modalMode === 'view'">Informasi detail realisasi kerja</p>
                        </div>
                    </div>
                    <button @click="closeModal()" class="text-gray-300 hover:text-white hover:bg-white hover:bg-opacity-10 rounded-lg p-1 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- View Mode -->
            <div x-show="modalMode === 'view' && currentRealization" class="px-6 py-4">
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Nomor Realisasi</label>
                            <p class="text-sm font-semibold text-gray-900" x-text="currentRealization?.realization_number"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal</label>
                                <p class="text-sm font-semibold text-gray-900" x-text="currentRealization?.realization_date"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Lokasi Kerja</label>
                                <p class="text-sm font-semibold text-gray-900" x-text="currentRealization?.work_location"></p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Durasi Aktual</label>
                                <p class="text-sm font-semibold text-blue-600" x-text="(currentRealization?.actual_duration_hours || 0) + ' jam'"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Progress</label>
                                <p class="text-sm font-semibold text-blue-600" x-text="(currentRealization?.progress_percentage || 0) + '%'"></p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-2">Deskripsi</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-900 whitespace-pre-wrap" x-text="currentRealization?.description"></p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3 border-t pt-4">
                    <button type="button" @click="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Tutup</button>
                    <button x-show="currentRealization?.id" @click="modalMode = 'edit'; openWorkRealizationModal('edit', currentRealization)" class="px-4 py-2 text-white rounded-lg text-sm font-medium transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">Edit</button>
                </div>
            </div>

            <!-- Create/Edit Mode -->
            <form x-show="modalMode === 'create' || modalMode === 'edit'" 
                  :action="modalMode === 'create' ? '{{ route('user.work-realizations.store') }}' : '/user/work-realizations/' + currentRealization?.id" 
                  method="POST" 
                  enctype="multipart/form-data"
                  class="px-6 py-4">
                <template x-if="modalMode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Project (Opsional)</label>
                        <div x-data="projectSearchableSelect(
                            (currentRealization?.project?.name ? (currentRealization.project.name + ' (' + currentRealization.project.code + ')') : (currentRealization?.project_name ? (currentRealization.project_name + ' (' + currentRealization.project_code + ')') : null)),
                            currentRealization?.project_id || null
                        )" class="relative">
                            <input type="hidden" name="project_id" :value="selectedId">
                            
                            <div class="relative">
                                <input 
                                    type="text"
                                    x-model="searchQuery"
                                    @input="searchProjects()"
                                    @focus="showDropdown = true"
                                    @blur="setTimeout(() => showDropdown = false, 200)"
                                    placeholder="Cari atau pilih project..."
                                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 pr-10"
                                    autocomplete="off"
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
                                                    class="w-full text-left px-4 py-2 hover:bg-green-50 focus:bg-green-50 focus:outline-none transition-colors"
                                                    :class="{ 'bg-green-50': selectedId == project.id }"
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
                        @error('project_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Realisasi <span class="text-red-500">*</span></label>
                            <input type="date" name="realization_date" 
                                   :value="currentRealization?.realization_date || ''" 
                                   :max="new Date().toISOString().split('T')[0]"
                                   required 
                                   class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                                   @change="validateRealizationTime($event)"
                                   x-ref="realizationDateInput">
                            <p class="mt-1 text-xs text-amber-600" x-show="realizationTimeWarning" x-text="realizationTimeWarning"></p>
                            @error('realization_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Kerja <span class="text-red-500">*</span></label>
                            <select name="work_location" required class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                                <option value="">Pilih Lokasi</option>
                                <option value="office" :selected="currentRealization?.work_location === 'office'">Office (Kantor)</option>
                                <option value="site" :selected="currentRealization?.work_location === 'site'">Site (Lapangan)</option>
                                <option value="wfh" :selected="currentRealization?.work_location === 'wfh'">WFH (Work From Home)</option>
                                <option value="wfa" :selected="currentRealization?.work_location === 'wfa'">WFA (Work From Anywhere)</option>
                            </select>
                            @error('work_location')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Durasi Aktual (Jam) <span class="text-red-500">*</span></label>
                            <input type="number" name="actual_duration_hours" 
                                   :value="currentRealization?.actual_duration_hours || ''" 
                                   required min="0.5" max="24" step="0.5"
                                   class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                            @error('actual_duration_hours')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Progress (%) <span class="text-red-500">*</span></label>
                            <input type="number" name="progress_percentage" 
                                   :value="currentRealization?.progress_percentage || 0" 
                                   required min="0" max="100"
                                   class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                            @error('progress_percentage')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Realisasi <span class="text-red-500">*</span></label>
                        <textarea name="description" required rows="4" 
                                  class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" 
                                  placeholder="Jelaskan secara detail apa yang telah Anda kerjakan..."
                                  x-bind:value="currentRealization?.description || ''"></textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">File Output (Opsional)</label>
                        <input type="file" name="output_files[]" multiple
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                               class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                        <p class="text-xs text-gray-500 mt-1">Maksimal 10MB per file. Format: PDF, DOC, XLS, atau gambar</p>
                        @error('output_files.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3 border-t pt-4">
                    <button type="button" @click="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 text-white rounded-lg transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">
                        <span x-show="modalMode === 'create'">Simpan Realisasi Kerja</span>
                        <span x-show="modalMode === 'edit'">Update Realisasi Kerja</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

