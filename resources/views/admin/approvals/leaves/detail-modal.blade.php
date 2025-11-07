<!-- Detail & Approval Modal -->
<div id="leave-detail-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeLeaveDetailModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <!-- Modal Header -->
            <div class="px-6 py-5" style="background-color: #0a1628;">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-white" id="leave-detail-modal-title">Detail Permohonan Cuti</h3>
                        <p class="text-sm text-gray-300 mt-1" id="leave-detail-modal-subtitle">Review detail sebelum menyetujui atau menolak</p>
                    </div>
                    <button type="button" onclick="closeLeaveDetailModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-1 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="bg-white px-6 py-6">
                <div id="leave-detail-content">
                    <!-- Content akan diisi via JavaScript -->
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 mx-auto" style="border-color: #0a1628;"></div>
                        <p class="text-sm text-gray-500 mt-2">Memuat data...</p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div id="leave-detail-footer" class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <!-- Footer akan diisi via JavaScript -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentLeaveId = null;

function openLeaveDetailModal(id) {
    currentLeaveId = id;
    const modal = document.getElementById('leave-detail-modal');
    
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Load detail data
        loadLeaveDetailData(id);
    }
}

function closeLeaveDetailModal() {
    const modal = document.getElementById('leave-detail-modal');
    const content = document.getElementById('leave-detail-content');
    const footer = document.getElementById('leave-detail-footer');
    
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = '';
        
        // Reset content
        if (content) {
            content.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 mx-auto" style="border-color: #0a1628;"></div><p class="text-sm text-gray-500 mt-2">Memuat data...</p></div>';
        }
        if (footer) {
            footer.innerHTML = '';
        }
    }
    
    currentLeaveId = null;
}

async function loadLeaveDetailData(id) {
    const content = document.getElementById('leave-detail-content');
    const footer = document.getElementById('leave-detail-footer');
    const title = document.getElementById('leave-detail-modal-title');
    
    // Determine base URL based on current route
    const isAdminRoute = window.location.pathname.includes('/admin/');
    const baseUrl = isAdminRoute ? '/admin/approvals/leaves' : '/user/leave-approvals';
    const url = `${baseUrl}/${id}`;
    
    if (title) title.textContent = 'Detail Permohonan Cuti';
    
    try {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const result = await response.json();
        
        if (result.success && result.data) {
            const data = result.data;
            
            if (content) {
                content.innerHTML = generateLeaveDetail(data);
            }
            
            if (footer) {
                footer.innerHTML = generateLeaveApprovalFooter(id, data.status);
            }
        } else {
            if (content) {
                content.innerHTML = '<div class="text-center py-8 text-red-600">Gagal memuat data. Silakan coba lagi.</div>';
            }
        }
    } catch (error) {
        console.error('Error loading detail:', error);
        if (content) {
            content.innerHTML = '<div class="text-center py-8 text-red-600">Terjadi kesalahan saat memuat data.</div>';
        }
    }
}

function generateLeaveDetail(data) {
    return `
        <div class="space-y-6">
            <!-- Main Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <div class="text-xs text-gray-500 mb-1">Nama Pengguna</div>
                    <div class="font-medium text-gray-900">${data.user}</div>
                    <div class="text-xs text-gray-500">${data.employee_id}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Jenis Cuti</div>
                    <div class="font-semibold text-gray-900">${data.leave_type}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Status</div>
                    <div>
                        <span class="px-2 py-1 rounded text-xs font-semibold ${
                            data.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                            data.status === 'approved' ? 'bg-green-100 text-green-700' :
                            'bg-red-100 text-red-700'
                        }">${data.status === 'pending' ? 'Pending' : data.status === 'approved' ? 'Approved' : 'Rejected'}</span>
                    </div>
                </div>
            </div>

            <!-- Leave Details -->
            <div class="border rounded-lg bg-gray-50 p-4">
                <h4 class="font-semibold text-gray-800 mb-3 text-sm">Detail Cuti</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Nomor Pengajuan</div>
                        <div class="font-medium text-gray-900">${data.leave_number}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Tanggal Pengajuan</div>
                        <div class="text-gray-700">${data.created_at}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Tanggal Mulai</div>
                        <div class="font-medium text-gray-900">${data.start_date}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Tanggal Selesai</div>
                        <div class="font-medium text-gray-900">${data.end_date}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Total Hari</div>
                        <div class="font-semibold text-gray-900">${data.total_days} hari</div>
                    </div>
                    <div>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t">
                    <div class="text-xs text-gray-500 mb-2">Alasan</div>
                    <div class="text-sm text-gray-800 bg-white p-3 rounded border">${data.reason || '-'}</div>
                </div>

                ${data.attachment_url ? `
                <div class="mt-4 pt-4 border-t">
                    <div class="text-xs text-gray-500 mb-2">Dokumen Pendukung</div>
                    <div class="flex items-center justify-between bg-white p-3 rounded border">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" style="color: #0a1628;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm text-gray-700">${data.attachment_name}</span>
                        </div>
                        <a href="${data.attachment_url}" download class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download
                        </a>
                    </div>
                </div>
                ` : ''}
            </div>

            ${data.admin_notes ? `
            <div class="border rounded-lg bg-gray-50 p-4">
                <div class="text-xs text-gray-600 font-semibold mb-1">Catatan</div>
                <div class="text-sm text-gray-800">${data.admin_notes}</div>
            </div>
            ` : ''}

            ${data.rejection_reason ? `
            <div class="border border-red-200 rounded-lg bg-red-50 p-4">
                <div class="text-xs text-red-600 font-semibold mb-1">Alasan Penolakan</div>
                <div class="text-sm text-red-800">${data.rejection_reason}</div>
            </div>
            ` : ''}

            ${data.approved_at && data.approved_at !== '-' ? `
            <div class="border rounded-lg bg-gray-50 p-4 text-sm">
                <span class="text-gray-600">Diproses oleh</span> <span class="font-semibold">${data.approved_by}</span>
                <span class="text-gray-600">pada</span> <span class="font-semibold">${data.approved_at}</span>
            </div>
            ` : ''}
        </div>
    `;
}

function generateLeaveApprovalFooter(id, status) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    // Determine base URL based on current route
    const isAdminRoute = window.location.pathname.includes('/admin/');
    const baseUrl = isAdminRoute ? '/admin/approvals/leaves' : '/user/leave-approvals';
    const pdfUrl = isAdminRoute ? `/admin/leaves/${id}/pdf` : `/user/leaves/${id}/pdf`;
    const isApproved = status === 'approved' || (status && status.value === 'approved');
    
    let pdfButton = '';
    if (isApproved) {
        pdfButton = `
            <a href="${pdfUrl}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download PDF
            </a>
        `;
    }
    
    return `
        <div class="flex justify-between items-center w-full">
            <div class="flex items-center gap-2">
                ${pdfButton}
            </div>
            <div class="flex items-center gap-2">
                ${!isApproved ? `
                <div class="flex-1 mr-4">
                    <form id="approve-leave-form-${id}" method="POST" action="${baseUrl}/${id}/approve" class="flex gap-2" onsubmit="event.preventDefault(); submitApproveLeaveForm(${id});">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="text" name="admin_notes" id="approve-notes-leave-${id}" placeholder="Catatan (Opsional)" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                            Setujui
                        </button>
                    </form>
                </div>
                <button onclick="closeLeaveDetailModal(); setTimeout(() => openLeaveRejectModal(${id}), 300);" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                    Tolak
                </button>
                ` : ''}
                <button type="button" onclick="closeLeaveDetailModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Tutup
                </button>
            </div>
        </div>
    `;
}

async function submitApproveLeaveForm(id) {
    const form = document.getElementById(`approve-leave-form-${id}`);
    const formData = new FormData(form);
    
    // Determine base URL based on current route
    const isAdminRoute = window.location.pathname.includes('/admin/');
    const baseUrl = isAdminRoute ? '/admin/approvals/leaves' : '/user/leave-approvals';
    const actionUrl = `${baseUrl}/${id}/approve`;
    
    try {
        const response = await fetch(actionUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            // Reload page to show updated status
            window.location.reload();
        } else {
            alert('Gagal menyetujui permohonan. Silakan coba lagi.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyetujui permohonan.');
    }
}

function openLeaveRejectModal(id) {
    const rejectModal = document.getElementById('leave-reject-modal');
    if (rejectModal) {
        const formId = document.getElementById('leave-reject-form-id');
        if (formId) formId.value = id;
        rejectModal.classList.remove('hidden');
        rejectModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closeLeaveRejectModal() {
    const modal = document.getElementById('leave-reject-modal');
    const form = document.getElementById('leave-reject-form');
    
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    if (form) {
        form.reset();
    }
}

function submitLeaveRejectForm(event) {
    event.preventDefault();
    const form = event.target;
    const id = document.getElementById('leave-reject-form-id').value;
    
    if (!id) {
        alert('Terjadi kesalahan. Silakan coba lagi.');
        return false;
    }
    
    // Determine base URL based on current route
    const isAdminRoute = window.location.pathname.includes('/admin/');
    const baseUrl = isAdminRoute ? '/admin/approvals/leaves' : '/user/leave-approvals';
    const actionUrl = `${baseUrl}/${id}/reject`;
    form.action = actionUrl;
    form.submit();
}


// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeLeaveDetailModal();
    }
});
</script>
@endpush

