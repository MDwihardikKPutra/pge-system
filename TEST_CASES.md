# Test Cases untuk Form Submission

Dokumentasi ini berisi data test untuk semua form submission yang ada di sistem.

## 1. Form Pembayaran Vendor (Vendor Payment)

### Test Case 1.1: Form Lengkap dengan Semua Field

**Status:** ✅ Test Case

**Data Input:**

-   **Vendor:** Pilih vendor dari dropdown (contoh: PT Konsultan Proyek)
-   **Project:** Pilih project dari searchable select (contoh: PLTP Ulubelu Optimization)
-   **Tipe Pembayaran:** DP (Down Payment)
-   **Tanggal Pembayaran:** 2025-11-06
-   **Nomor Invoice:** INV-2025-TEST-001
-   **Nomor PO:** PO-2025-TEST-001 (Opsional)
-   **Jumlah Pembayaran (Rp):** 50000000
-   **Deskripsi Pembayaran:** Pembayaran DP untuk jasa konsultasi engineering bulan November 2025
-   **Catatan Tambahan:** Mohon disetujui segera untuk kelancaran proyek
-   **Dokumen Pendukung:** Upload file PDF (optional)

**Expected Result:**

-   Form berhasil disubmit
-   Data tersimpan di database
-   Redirect ke halaman index dengan pesan sukses

---

### Test Case 1.2: Form Minimum Required Fields Only

**Status:** ✅ Test Case

**Data Input:**

-   **Vendor:** Pilih vendor dari dropdown
-   **Project:** Pilih project dari searchable select
-   **Tipe Pembayaran:** Termin
-   **Tanggal Pembayaran:** 2025-11-07
-   **Nomor Invoice:** INV-2025-TEST-002
-   **Jumlah Pembayaran (Rp):** 25000000
-   **Deskripsi Pembayaran:** Pembayaran termin 1
-   **Nomor PO:** (Kosong)
-   **Catatan Tambahan:** (Kosong)
-   **Dokumen Pendukung:** (Tidak di-upload)

**Expected Result:**

-   Form berhasil disubmit dengan field opsional kosong
-   Data tersimpan dengan field opsional null

---

### Test Case 1.3: Form dengan Full Payment

**Status:** ✅ Test Case

**Data Input:**

-   **Vendor:** Pilih vendor dari dropdown
-   **Project:** Pilih project dari searchable select
-   **Tipe Pembayaran:** Full Payment
-   **Tanggal Pembayaran:** 2025-11-08
-   **Nomor Invoice:** INV-2025-TEST-003
-   **Nomor PO:** PO-2025-FINAL-001
-   **Jumlah Pembayaran (Rp):** 150000000
-   **Deskripsi Pembayaran:** Pelunasan pembayaran untuk pekerjaan konstruksi PLTP
-   **Catatan Tambahan:** Dokumen lengkap sudah dilampirkan
-   **Dokumen Pendukung:** Upload multiple files (PDF, JPG)

**Expected Result:**

-   Form berhasil disubmit dengan semua dokumen
-   Multiple files berhasil di-upload

---

## 2. Form Pembelian (Purchase)

### Test Case 2.1: Pembelian Barang IT

**Status:** ✅ Test Case

**Data Input:**

-   **Project:** Pilih project dari searchable select
-   **Tipe Pembelian:** Barang
-   **Kategori:** IT & Teknologi
-   **Nama Item:** Laptop Dell XPS 15 9530
-   **Deskripsi/Spesifikasi:** Laptop Dell XPS 15 dengan Intel i7, 16GB RAM, 512GB SSD, NVIDIA RTX 4050
-   **Jumlah:** 5
-   **Satuan:** Unit
-   **Harga Satuan (Rp):** 25000000
-   **Total Harga:** (Otomatis: Rp 125.000.000)
-   **Catatan Tambahan:** Untuk keperluan tim development
-   **Dokumen Pendukung:** Upload quotation (PDF)

**Expected Result:**

-   Form berhasil disubmit
-   Total harga terhitung otomatis (5 x 25.000.000 = 125.000.000)
-   Data tersimpan dengan benar

---

### Test Case 2.2: Pembelian Jasa Profesional

**Status:** ✅ Test Case

**Data Input:**

-   **Project:** Pilih project dari searchable select
-   **Tipe Pembelian:** Jasa
-   **Kategori:** Jasa Profesional
-   **Nama Item:** Jasa Konsultasi Engineering
-   **Deskripsi/Spesifikasi:** Konsultasi engineering untuk feasibility study PLTP dengan durasi 3 bulan
-   **Jumlah:** 3
-   **Satuan:** Bulan
-   **Harga Satuan (Rp):** 50000000
-   **Total Harga:** (Otomatis: Rp 150.000.000)
-   **Catatan Tambahan:** Kontrak sudah ditandatangani
-   **Dokumen Pendukung:** Upload kontrak (PDF)

**Expected Result:**

-   Form berhasil disubmit
-   Total harga terhitung otomatis
-   Data tersimpan dengan tipe jasa

---

### Test Case 2.3: Pembelian Aset Peralatan

**Status:** ✅ Test Case

**Data Input:**

-   **Project:** Pilih project dari searchable select
-   **Tipe Pembelian:** Aset
-   **Kategori:** Peralatan
-   **Nama Item:** Generator Set 1000 KVA
-   **Deskripsi/Spesifikasi:** Generator set diesel 1000 KVA untuk backup power PLTP
-   **Jumlah:** 2
-   **Satuan:** Unit
-   **Harga Satuan (Rp):** 750000000
-   **Total Harga:** (Otomatis: Rp 1.500.000.000)
-   **Catatan Tambahan:** Delivery time 4-6 minggu
-   **Dokumen Pendukung:** Upload spesifikasi teknis (PDF)

**Expected Result:**

-   Form berhasil disubmit
-   Total harga terhitung otomatis untuk nilai besar
-   Data tersimpan dengan tipe aset

---

### Test Case 2.4: Pembelian Alat Tulis Kantor

**Status:** ✅ Test Case

**Data Input:**

-   **Project:** Pilih project atau kosongkan (untuk keperluan kantor)
-   **Tipe Pembelian:** Barang
-   **Kategori:** Alat Tulis Kantor
-   **Nama Item:** Paket ATK Bulanan
-   **Deskripsi/Spesifikasi:** Paket alat tulis kantor untuk 1 bulan: kertas A4, pulpen, binder, staples, dll
-   **Jumlah:** 50
-   **Satuan:** Paket
-   **Harga Satuan (Rp):** 500000
-   **Total Harga:** (Otomatis: Rp 25.000.000)
-   **Catatan Tambahan:** (Kosong)
-   **Dokumen Pendukung:** (Tidak di-upload)

**Expected Result:**

-   Form berhasil disubmit dengan field opsional kosong
-   Total harga terhitung otomatis

---

## 3. Form SPD (Surat Perjalanan Dinas)

### Test Case 3.1: SPD dengan Satu Item Biaya

**Status:** ✅ Test Case

**Data Input:**

-   **Project:** Pilih project dari searchable select
-   **Tujuan Perjalanan:** Jakarta
-   **Keperluan/Tujuan:** Meeting dengan client untuk presentasi proposal proyek PLTP
-   **Tanggal Berangkat:** 2025-11-10
-   **Tanggal Kembali:** 2025-11-12
-   **Rincian Biaya:**
    -   Item 1:
        -   Jenis Biaya: Transport
        -   Keterangan: Tiket pesawat PP Jakarta-Bandung
        -   Jumlah (Rp): 3000000
-   **Total Biaya:** (Otomatis: Rp 3.000.000)
-   **Catatan Tambahan:** Menginap di hotel selama 2 malam
-   **Dokumen Pendukung:** Upload tiket pesawat (PDF)

**Expected Result:**

-   Form berhasil disubmit dengan 1 item biaya
-   Total biaya terhitung otomatis
-   Durasi perjalanan: 3 hari (10-12 November)

---

### Test Case 3.2: SPD dengan Multiple Item Biaya

**Status:** ✅ Test Case

**Data Input:**

-   **Project:** Pilih project dari searchable select
-   **Tujuan Perjalanan:** Surabaya
-   **Keperluan/Tujuan:** Site visit dan koordinasi dengan vendor untuk proyek PLTP
-   **Tanggal Berangkat:** 2025-11-15
-   **Tanggal Kembali:** 2025-11-18
-   **Rincian Biaya:**
    -   Item 1:
        -   Jenis Biaya: Transport
        -   Keterangan: Tiket pesawat PP + transport lokal
        -   Jumlah (Rp): 5000000
    -   Item 2:
        -   Jenis Biaya: Hotel
        -   Keterangan: Hotel 3 malam @ Rp 500.000/malam
        -   Jumlah (Rp): 1500000
    -   Item 3:
        -   Jenis Biaya: Makan
        -   Keterangan: Uang makan 4 hari @ Rp 200.000/hari
        -   Jumlah (Rp): 800000
    -   Item 4:
        -   Jenis Biaya: Lainnya
        -   Keterangan: Parkir dan tol
        -   Jumlah (Rp): 500000
-   **Total Biaya:** (Otomatis: Rp 7.800.000)
-   **Catatan Tambahan:** Perjalanan untuk site visit dan meeting dengan vendor
-   **Dokumen Pendukung:** Upload tiket pesawat dan booking hotel (PDF)

**Expected Result:**

-   Form berhasil disubmit dengan 4 item biaya
-   Total biaya terhitung otomatis (5.000.000 + 1.500.000 + 800.000 + 500.000 = 7.800.000)
-   Semua item biaya tersimpan dengan benar

---

### Test Case 3.3: SPD dengan Minimal Biaya

**Status:** ✅ Test Case

**Data Input:**

-   **Project:** Pilih project dari searchable select
-   **Tujuan Perjalanan:** Bandung
-   **Keperluan/Tujuan:** Meeting singkat dengan client
-   **Tanggal Berangkat:** 2025-11-20
-   **Tanggal Kembali:** 2025-11-20 (hari yang sama)
-   **Rincian Biaya:**
    -   Item 1:
        -   Jenis Biaya: Transport
        -   Keterangan: Bensin + tol
        -   Jumlah (Rp): 500000
-   **Total Biaya:** (Otomatis: Rp 500.000)
-   **Catatan Tambahan:** (Kosong)
-   **Dokumen Pendukung:** (Tidak di-upload)

**Expected Result:**

-   Form berhasil disubmit dengan 1 item biaya minimal
-   Total biaya terhitung otomatis
-   Durasi perjalanan: 1 hari (pulang pergi)

---

### Test Case 3.4: SPD dengan Biaya Banyak Item

**Status:** ✅ Test Case

**Data Input:**

-   **Project:** Pilih project dari searchable select
-   **Tujuan Perjalanan:** Yogyakarta
-   **Keperluan/Tujuan:** Training dan workshop geothermal engineering
-   **Tanggal Berangkat:** 2025-11-25
-   **Tanggal Kembali:** 2025-11-30
-   **Rincian Biaya:**
    -   Item 1: Transport - Tiket pesawat PP (Rp 4.000.000)
    -   Item 2: Hotel - 5 malam @ Rp 600.000 (Rp 3.000.000)
    -   Item 3: Makan - 6 hari @ Rp 250.000 (Rp 1.500.000)
    -   Item 4: Training Fee - Biaya workshop (Rp 5.000.000)
    -   Item 5: Transport Lokal - Rental mobil 5 hari (Rp 2.000.000)
    -   Item 6: Lainnya - Parking, tips, dll (Rp 500.000)
-   **Total Biaya:** (Otomatis: Rp 16.000.000)
-   **Catatan Tambahan:** Training geothermal engineering dengan sertifikat
-   **Dokumen Pendukung:** Upload tiket, booking hotel, invoice training (PDF)

**Expected Result:**

-   Form berhasil disubmit dengan 6 item biaya
-   Total biaya terhitung otomatis
-   Semua item biaya tersimpan dengan benar
-   Durasi perjalanan: 6 hari

---

## Test Case Negative / Error Handling

### Test Case N.1: Form Kosong (Validation Error)

**Status:** ✅ Test Case

**Langkah:**

1. Buka modal form
2. Langsung klik tombol submit tanpa mengisi apapun

**Expected Result:**

-   Form tidak bisa disubmit
-   Menampilkan error message untuk setiap field required yang kosong
-   Error message dalam bahasa Indonesia

---

### Test Case N.2: Input Invalid (Validation Error)

**Status:** ✅ Test Case

**Contoh untuk Vendor Payment:**

-   **Jumlah Pembayaran:** -1000 (negatif)
-   **Tanggal Pembayaran:** (kosong)

**Expected Result:**

-   Form tidak bisa disubmit
-   Menampilkan error: "Jumlah pembayaran tidak boleh negatif"
-   Menampilkan error: "Tanggal pembayaran harus diisi"

---

### Test Case N.3: File Upload Melebihi Limit

**Status:** ✅ Test Case

**Langkah:**

1. Upload file dengan ukuran > 2MB (untuk vendor payment/purchase) atau > 5MB (untuk SPD)
2. Coba submit form

**Expected Result:**

-   Form tidak bisa disubmit
-   Menampilkan error: "File terlalu besar. Maksimal 2MB/5MB per file"

---

### Test Case N.4: File Format Tidak Valid

**Status:** ✅ Test Case

**Langkah:**

1. Upload file dengan format selain PDF, JPG, JPEG, PNG (contoh: .docx, .xlsx)
2. Coba submit form

**Expected Result:**

-   Form tidak bisa disubmit
-   Menampilkan error: "Format file tidak valid. Hanya PDF, JPG, JPEG, PNG yang diperbolehkan"

---

## Checklist Testing

### Vendor Payment Form

-   [ ] Test Case 1.1: Form Lengkap
-   [ ] Test Case 1.2: Form Minimum
-   [ ] Test Case 1.3: Full Payment
-   [ ] Test Case N.1: Validation Error - Form Kosong
-   [ ] Test Case N.2: Validation Error - Input Invalid

### Purchase Form

-   [ ] Test Case 2.1: Pembelian Barang IT
-   [ ] Test Case 2.2: Pembelian Jasa
-   [ ] Test Case 2.3: Pembelian Aset
-   [ ] Test Case 2.4: Pembelian ATK
-   [ ] Test Case N.1: Validation Error - Form Kosong
-   [ ] Test Case N.2: Validation Error - Input Invalid

### SPD Form

-   [ ] Test Case 3.1: SPD 1 Item Biaya
-   [ ] Test Case 3.2: SPD Multiple Item Biaya
-   [ ] Test Case 3.3: SPD Minimal Biaya
-   [ ] Test Case 3.4: SPD Banyak Item Biaya
-   [ ] Test Case N.1: Validation Error - Form Kosong
-   [ ] Test Case N.2: Validation Error - Input Invalid

### File Upload

-   [ ] Test Case N.3: File Upload Melebihi Limit
-   [ ] Test Case N.4: File Format Tidak Valid

---

## Catatan Testing

1. **Project Selection:** Pastikan project-select component berfungsi dengan baik:

    - Search berfungsi
    - Dropdown muncul saat mengetik
    - Selection tersimpan dengan benar
    - Clear button berfungsi

2. **Auto Calculation:**

    - Purchase form: Total harga harus terhitung otomatis (quantity × unit_price)
    - SPD form: Total biaya harus terhitung otomatis (sum of all costs)

3. **Date Validation:**

    - SPD: Tanggal kembali harus >= tanggal berangkat
    - Semua date input harus format valid

4. **Modal Behavior:**

    - Modal muncul saat tombol diklik
    - Modal bisa ditutup dengan tombol X atau klik overlay
    - Form reset saat modal ditutup
    - Modal tidak muncul saat ada error validation

5. **Form Submission:**
    - Loading state saat submit
    - Success redirect ke index page
    - Error validation ditampilkan di form
    - Data tersimpan dengan benar di database

---

**Dibuat:** 2025-11-06
**Versi:** 1.0
