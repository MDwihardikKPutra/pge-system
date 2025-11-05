<!-- Detail & Approval Modal -->
<div id="detail-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDetailModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <!-- Modal Header -->
            <div class="px-6 py-5 bg-gradient-to-r from-blue-600 to-blue-700">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-white" id="detail-modal-title">Detail Pengajuan</h3>
                        <p class="text-sm text-blue-100 mt-1" id="detail-modal-subtitle">Review detail sebelum menyetujui atau menolak</p>
                    </div>
                    <button type="button" onclick="closeDetailModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-1 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="bg-white px-6 py-6">
                <div id="detail-content">
                    <!-- Content akan diisi via JavaScript -->
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="text-sm text-gray-500 mt-2">Memuat data...</p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div id="detail-footer" class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <!-- Footer akan diisi via JavaScript -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentDetailType = '';
let currentDetailId = null;

function openDetailModal(type, id) {
    currentDetailType = type;
    currentDetailId = id;
    const modal = document.getElementById('detail-modal');
    
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Load detail data
        loadDetailData(type, id);
    }
}

function closeDetailModal() {
    const modal = document.getElementById('detail-modal');
    const content = document.getElementById('detail-content');
    const footer = document.getElementById('detail-footer');
    
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = '';
        
        // Reset content
        if (content) {
            content.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div><p class="text-sm text-gray-500 mt-2">Memuat data...</p></div>';
        }
        if (footer) {
            footer.innerHTML = '';
        }
    }
    
    currentDetailType = '';
    currentDetailId = null;
}

async function loadDetailData(type, id) {
    const content = document.getElementById('detail-content');
    const footer = document.getElementById('detail-footer');
    const title = document.getElementById('detail-modal-title');
    const subtitle = document.getElementById('detail-modal-subtitle');
    
    let url = '';
    let titleText = '';
    
    if (type === 'spd') {
        url = `/admin/approvals/payments/spd/${id}`;
        titleText = 'Detail SPD';
    } else if (type === 'purchase') {
        url = `/admin/approvals/payments/purchases/${id}`;
        titleText = 'Detail Pembelian';
    } else if (type === 'vendor-payment') {
        url = `/admin/approvals/payments/vendor-payments/${id}`;
        titleText = 'Detail Pembayaran Vendor';
    }
    
    if (title) title.textContent = titleText;
    
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
                if (type === 'spd') {
                    content.innerHTML = generateSpdDetail(data);
                } else if (type === 'purchase') {
                    content.innerHTML = generatePurchaseDetail(data);
                } else if (type === 'vendor-payment') {
                    content.innerHTML = generateVendorPaymentDetail(data);
                }
            }
            
            if (footer) {
                if (data.status === 'pending') {
                    footer.innerHTML = generateApprovalFooter(type, id);
                } else {
                    footer.innerHTML = '<button type="button" onclick="closeDetailModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Tutup</button>';
                }
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

function generateSpdDetail(data) {
    return `
        <div class="space-y-6">
            <!-- Main Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <div class="text-xs text-gray-500 mb-1">Nomor SPD</div>
                    <div class="font-mono font-semibold text-gray-900">${data.spd_number}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Diajukan Oleh</div>
                    <div class="font-medium text-gray-900">${data.user}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Status</div>
                    <div>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold ${
                            data.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                            data.status === 'approved' ? 'bg-green-100 text-green-700' :
                            'bg-red-100 text-red-700'
                        }">${data.status === 'pending' ? 'Pending' : data.status === 'approved' ? 'Disetujui' : 'Ditolak'}</span>
                    </div>
                </div>
            </div>

            <!-- Trip Details & Cost -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Trip Info -->
                <div class="border rounded-lg bg-gray-50 p-4">
                    <h4 class="font-semibold text-gray-800 mb-3 text-sm">Detail Perjalanan</h4>
                    <div class="space-y-2 text-sm">
                        <div>
                            <div class="text-xs text-gray-500">Tujuan</div>
                            <div class="font-medium">${data.destination}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Keperluan</div>
                            <div>${data.purpose}</div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div>
                                <div class="text-xs text-gray-500">Berangkat</div>
                                <div>${data.departure_date}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Kembali</div>
                                <div>${data.return_date}</div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Project</div>
                            <div>${data.project}</div>
                        </div>
                    </div>
                </div>

                <!-- Cost Breakdown -->
                <div class="border rounded-lg bg-gray-50 p-4">
                    <h4 class="font-semibold text-gray-800 mb-3 text-sm">Rincian Biaya</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Transport</span>
                            <span class="font-semibold">Rp ${data.transport_cost}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Akomodasi</span>
                            <span class="font-semibold">Rp ${data.accommodation_cost}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Makan</span>
                            <span class="font-semibold">Rp ${data.meal_cost}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Lainnya</span>
                            <span class="font-semibold">Rp ${data.other_cost}</span>
                        </div>
                        ${data.other_cost_description ? `<div class="text-xs text-gray-500 mt-1">${data.other_cost_description}</div>` : ''}
                        <div class="flex justify-between pt-2 border-t font-bold">
                            <span class="text-gray-800">Total</span>
                            <span class="text-blue-600 text-lg">Rp ${data.total_cost}</span>
                        </div>
                    </div>
                </div>
            </div>

            ${data.notes ? `
            <div class="border rounded-lg bg-blue-50 p-4">
                <div class="text-xs text-gray-600 font-semibold mb-1">Catatan</div>
                <div class="text-sm text-gray-800">${data.notes}</div>
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

function generatePurchaseDetail(data) {
    return `
        <div class="space-y-6">
            <!-- Main Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <div class="text-xs text-gray-500 mb-1">Nomor Pembelian</div>
                    <div class="font-mono font-semibold text-gray-900">${data.purchase_number}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Diajukan Oleh</div>
                    <div class="font-medium text-gray-900">${data.user}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Status</div>
                    <div>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold ${
                            data.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                            data.status === 'approved' ? 'bg-green-100 text-green-700' :
                            'bg-red-100 text-red-700'
                        }">${data.status === 'pending' ? 'Pending' : data.status === 'approved' ? 'Disetujui' : 'Ditolak'}</span>
                    </div>
                </div>
            </div>

            <!-- Item Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border rounded-lg bg-gray-50 p-4">
                    <h4 class="font-semibold text-gray-800 mb-3 text-sm">Detail Item</h4>
                    <div class="space-y-2 text-sm">
                        <div>
                            <div class="text-xs text-gray-500">Item</div>
                            <div class="font-medium">${data.item_name}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Tipe</div>
                            <div>${data.type}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Kategori</div>
                            <div>${data.category}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Project</div>
                            <div>${data.project}</div>
                        </div>
                    </div>
                </div>

                <div class="border rounded-lg bg-gray-50 p-4">
                    <h4 class="font-semibold text-gray-800 mb-3 text-sm">Rincian Harga</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jumlah</span>
                            <span class="font-semibold">${data.quantity} ${data.unit}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Harga Satuan</span>
                            <span class="font-semibold">Rp ${data.unit_price}</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t font-bold">
                            <span class="text-gray-800">Total</span>
                            <span class="text-blue-600 text-lg">Rp ${data.total_price}</span>
                        </div>
                    </div>
                </div>
            </div>

            ${data.description ? `
            <div class="border rounded-lg bg-blue-50 p-4">
                <div class="text-xs text-gray-600 font-semibold mb-1">Deskripsi</div>
                <div class="text-sm text-gray-800">${data.description}</div>
            </div>
            ` : ''}

            ${data.notes ? `
            <div class="border rounded-lg bg-blue-50 p-4">
                <div class="text-xs text-gray-600 font-semibold mb-1">Catatan</div>
                <div class="text-sm text-gray-800">${data.notes}</div>
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

function generateVendorPaymentDetail(data) {
    return `
        <div class="space-y-6">
            <!-- Main Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <div class="text-xs text-gray-500 mb-1">Nomor Pembayaran</div>
                    <div class="font-mono font-semibold text-gray-900">${data.payment_number}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Diajukan Oleh</div>
                    <div class="font-medium text-gray-900">${data.user}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Status</div>
                    <div>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold ${
                            data.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                            data.status === 'approved' ? 'bg-green-100 text-green-700' :
                            'bg-red-100 text-red-700'
                        }">${data.status === 'pending' ? 'Pending' : data.status === 'approved' ? 'Disetujui' : 'Ditolak'}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border rounded-lg bg-gray-50 p-4">
                    <h4 class="font-semibold text-gray-800 mb-3 text-sm">Detail Pembayaran</h4>
                    <div class="space-y-2 text-sm">
                        <div>
                            <div class="text-xs text-gray-500">Vendor</div>
                            <div class="font-medium">${data.vendor}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">No. Invoice</div>
                            <div>${data.invoice_number}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Tanggal Pembayaran</div>
                            <div>${data.payment_date}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Project</div>
                            <div>${data.project}</div>
                        </div>
                    </div>
                </div>

                <div class="border rounded-lg bg-gray-50 p-4">
                    <h4 class="font-semibold text-gray-800 mb-3 text-sm">Jumlah</h4>
                    <div class="text-2xl font-bold text-blue-600 pt-2">
                        Rp ${data.amount}
                    </div>
                </div>
            </div>

            ${data.description ? `
            <div class="border rounded-lg bg-blue-50 p-4">
                <div class="text-xs text-gray-600 font-semibold mb-1">Deskripsi</div>
                <div class="text-sm text-gray-800">${data.description}</div>
            </div>
            ` : ''}

            ${data.notes ? `
            <div class="border rounded-lg bg-blue-50 p-4">
                <div class="text-xs text-gray-600 font-semibold mb-1">Catatan</div>
                <div class="text-sm text-gray-800">${data.notes}</div>
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

function generateApprovalFooter(type, id) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const actionUrl = `/admin/approvals/payments/${type === 'spd' ? 'spd' : type === 'purchase' ? 'purchases' : 'vendor-payments'}/${id}/approve`;
    
    return `
        <div class="flex justify-between items-center w-full">
            <div class="flex-1 mr-4">
                <form id="approve-form-${type}-${id}" method="POST" action="${actionUrl}" class="flex gap-2" onsubmit="event.preventDefault(); submitApproveForm('${type}', ${id});">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="text" name="notes" id="approve-notes-${type}-${id}" placeholder="Catatan (Opsional)" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                        Setujui
                    </button>
                </form>
            </div>
            <button onclick="closeDetailModal(); setTimeout(() => openRejectModal('${type}', ${id}), 300);" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                Tolak
            </button>
            <button type="button" onclick="closeDetailModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 ml-2">
                Tutup
            </button>
        </div>
    `;
}

async function submitApproveForm(type, id) {
    const form = document.getElementById(`approve-form-${type}-${id}`);
    const formData = new FormData(form);
    
    const actionUrl = `/admin/approvals/payments/${type === 'spd' ? 'spd' : type === 'purchase' ? 'purchases' : 'vendor-payments'}/${id}/approve`;
    
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
            alert('Gagal menyetujui pengajuan. Silakan coba lagi.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyetujui pengajuan.');
    }
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDetailModal();
    }
});
</script>
@endpush

