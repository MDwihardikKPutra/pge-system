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
        <div @click.away="closeModal()" class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="px-6 py-5 sticky top-0 z-10 rounded-t-lg" style="background-color: #0a1628;">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-white bg-opacity-10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">
                                <span x-show="modalMode === 'create'">Pengajuan Cuti & Izin</span>
                                <span x-show="modalMode === 'view'">Detail Pengajuan Cuti</span>
                            </h3>
                            <p class="text-sm text-gray-300 mt-0.5" x-show="modalMode === 'create'">Lengkapi form di bawah untuk mengajukan cuti</p>
                            <p class="text-sm text-gray-300 mt-0.5" x-show="modalMode === 'view'">Informasi detail pengajuan cuti Anda</p>
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
            <div x-show="modalMode === 'view' && currentLeave" class="px-6 py-4">
                <div class="space-y-6">
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Nomor Pengajuan</label>
                            <p class="text-sm font-semibold text-gray-900" x-text="currentLeave?.leave_number"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Jenis Cuti</label>
                            <p class="text-sm font-semibold text-gray-900" x-text="currentLeave?.leave_type?.name || '-'"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Mulai</label>
                                <p class="text-sm font-semibold text-gray-900" x-text="currentLeave?.start_date"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Selesai</label>
                                <p class="text-sm font-semibold text-gray-900" x-text="currentLeave?.end_date"></p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Total Hari</label>
                            <p class="text-sm font-semibold text-blue-600" x-text="(currentLeave?.total_days || 0) + ' hari'"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                :class="{
                                    'bg-yellow-100 text-yellow-800': currentLeave?.status === 'pending',
                                    'bg-green-100 text-green-800': currentLeave?.status === 'approved',
                                    'bg-red-100 text-red-800': currentLeave?.status === 'rejected'
                                }"
                                x-text="currentLeave?.status === 'pending' ? 'Pending' : (currentLeave?.status === 'approved' ? 'Disetujui' : 'Ditolak')"></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-2">Alasan</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-900 whitespace-pre-wrap" x-text="currentLeave?.reason"></p>
                        </div>
                    </div>
                    <div x-show="currentLeave?.status !== 'pending'" class="border-t pt-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Informasi Approval</h4>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Disetujui/Ditolak Oleh</label>
                                <p class="text-sm font-semibold text-gray-900" x-text="currentLeave?.approved_by?.name || '-'"></p>
                            </div>
                            <div x-show="currentLeave?.approved_at">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal</label>
                                <p class="text-sm text-gray-900" x-text="currentLeave?.approved_at"></p>
                            </div>
                            <div x-show="currentLeave?.admin_notes">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Catatan</label>
                                <p class="text-sm text-gray-900" x-text="currentLeave?.admin_notes"></p>
                            </div>
                            <div x-show="currentLeave?.rejection_reason">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Alasan Penolakan</label>
                                <p class="text-sm text-red-700" x-text="currentLeave?.rejection_reason"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-between gap-3 border-t pt-4">
                    <button type="button" @click="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Tutup</button>
                    <div class="flex gap-2">
                        <a x-show="currentLeave?.status === 'approved'" :href="'/user/leaves/' + currentLeave?.id + '/pdf'" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Download PDF
                        </a>
                        <button x-show="currentLeave?.status === 'pending'" @click="modalMode = 'edit'; openLeaveModal('edit', currentLeave)" class="px-4 py-2 text-white rounded-lg text-sm font-medium transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">Edit</button>
                    </div>
                </div>
            </div>

            <!-- Create Mode -->
            <form x-show="modalMode === 'create'" action="{{ route('user.leaves.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-4" x-data="{ startDate: '', endDate: '', calculateDays() { if (this.startDate && this.endDate) { const start = new Date(this.startDate); const end = new Date(this.endDate); const diffTime = Math.abs(end - start); const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; return diffDays; } return 0; } }">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Cuti <span class="text-red-500">*</span></label>
                        <select name="leave_type_id" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Pilih Jenis Cuti --</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('leave_type_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" name="start_date" x-model="startDate" required :min="new Date().toISOString().split('T')[0]" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('start_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" name="end_date" x-model="endDate" required :min="startDate || new Date().toISOString().split('T')[0]" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('end_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div x-show="startDate && endDate" class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-medium text-blue-900">Total: <span x-text="calculateDays()"></span> hari</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan <span class="text-red-500">*</span></label>
                        <textarea name="reason" required rows="4" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Jelaskan alasan pengajuan cuti Anda..."></textarea>
                        @error('reason')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dokumen Pendukung (Opsional)</label>
                        <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG, max 5MB</p>
                        @error('attachment')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3 border-t pt-4">
                    <button type="button" @click="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 text-white rounded-lg transition-colors" style="background-color: #0a1628;" onmouseover="this.style.backgroundColor='#1e293b'" onmouseout="this.style.backgroundColor='#0a1628'">Ajukan Cuti</button>
                </div>
            </form>
        </div>
    </div>
</div>

