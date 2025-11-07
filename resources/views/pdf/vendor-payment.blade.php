<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Permintaan Pembayaran Vendor - {{ $vendorPayment->payment_number }}</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000000;
            background: #ffffff;
        }
        .letterhead {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000000;
        }
        .letterhead-left {
            display: table-cell;
            width: 20%;
            vertical-align: top;
            padding-right: 20px;
        }
        .letterhead-right {
            display: table-cell;
            width: 80%;
            vertical-align: top;
            text-align: left;
        }
        .logo-container {
            width: 60px;
            height: 60px;
            border: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9f9f9;
        }
        .logo-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .company-name {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
            color: #000000;
        }
        .company-full-name {
            font-size: 10pt;
            font-weight: normal;
            text-transform: none;
            margin-bottom: 2px;
            color: #333333;
        }
        .company-address {
            font-size: 9pt;
            line-height: 1.4;
            color: #555555;
            margin-top: 3px;
        }
        .document-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 12px 0 10px 0;
            color: #000000;
        }
        .document-number {
            text-align: right;
            font-size: 9pt;
            margin-bottom: 12px;
            color: #333333;
        }
        .content {
            margin: 12px 0;
            text-align: justify;
        }
        .opening {
            margin-bottom: 10px;
            text-indent: 0;
        }
        .form-section {
            margin: 10px 0;
        }
        .form-label {
            font-weight: bold;
            display: inline-block;
            min-width: 160px;
            margin-right: 8px;
        }
        .form-value {
            display: inline;
        }
        .form-row {
            margin-bottom: 6px;
            line-height: 1.5;
        }
        .description-section {
            margin: 12px 0;
        }
        .description-label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        .description-text {
            text-align: justify;
            line-height: 1.5;
            padding: 8px;
            border: 1px solid #e0e0e0;
            background: #fafafa;
            min-height: 40px;
        }
        .amount-box {
            background: #f0f8ff;
            border: 2px solid #000000;
            border-radius: 3px;
            padding: 15px;
            margin: 15px 0;
            text-align: center;
        }
        .amount-label {
            font-size: 10pt;
            color: #333333;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .amount-value {
            font-size: 16pt;
            font-weight: bold;
            color: #000000;
        }
        .closing {
            margin-top: 15px;
            text-indent: 0;
        }
        .signature-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        .signature-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .signature-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        .signature-label {
            margin-top: 40px;
            padding-top: 3px;
            border-top: 1px solid #000000;
            display: inline-block;
            min-width: 180px;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
        }
        .approval-stamp {
            position: absolute;
            right: 1.5cm;
            bottom: 1.5cm;
            text-align: center;
            padding: 8px 15px;
            border: 2px solid #28a745;
            border-radius: 3px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .approval-stamp .stamp-title {
            font-weight: bold;
            color: #28a745;
            font-size: 10pt;
            margin-bottom: 3px;
        }
        .approval-stamp .stamp-name {
            font-size: 9pt;
            color: #000000;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px solid #28a745;
            font-weight: bold;
        }
        .approval-stamp .stamp-date {
            font-size: 8pt;
            color: #666666;
            margin-top: 3px;
        }
        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 8pt;
            color: #666666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Letterhead -->
    <div class="letterhead">
        <div class="letterhead-left">
            <div class="logo-container">
                <img src="{{ public_path('logopge.png') }}" alt="PGE Logo" onerror="this.style.display='none';">
            </div>
        </div>
        <div class="letterhead-right">
            <div class="company-name">PGE</div>
            <div class="company-full-name">PT. PURI GANESHA ENGINEERING</div>
            <div class="company-address">
                Jl. Raya Bogor KM 30, Cimanggis, Depok<br>
                Jawa Barat, Indonesia<br>
                Telp: (021) 12345678 | Email: info@pge.co.id
            </div>
        </div>
    </div>

    <!-- Document Title -->
    <div class="document-title">Surat Permintaan Pembayaran Vendor</div>

    <!-- Document Number -->
    <div class="document-number">
        No. Dokumen: {{ $vendorPayment->payment_number }}<br>
        Tanggal: {{ $vendorPayment->created_at->format('d F Y') }}
    </div>

    <!-- Content -->
    <div class="content">
        <div class="opening">
            Dengan hormat,
        </div>

        <div class="opening" style="text-indent: 1cm;">
            Yang bertanda tangan di bawah ini:
        </div>

        <div class="form-section" style="margin-left: 1cm;">
            <div class="form-row">
                <span class="form-label">Nama</span>
                <span class="form-value">: {{ $vendorPayment->user->name ?? '-' }}</span>
            </div>
            <div class="form-row">
                <span class="form-label">NIK / Employee ID</span>
                <span class="form-value">: {{ $vendorPayment->user->employee_id ?? '-' }}</span>
            </div>
            <div class="form-row">
                <span class="form-label">Jabatan</span>
                <span class="form-value">: {{ $vendorPayment->user->position ?? '-' }}</span>
            </div>
            <div class="form-row">
                <span class="form-label">Departemen</span>
                <span class="form-value">: {{ $vendorPayment->user->department ?? '-' }}</span>
            </div>
            <div class="form-row">
                <span class="form-label">Project</span>
                <span class="form-value">: {{ $vendorPayment->project->name ?? '-' }} @if($vendorPayment->project && $vendorPayment->project->code)({{ $vendorPayment->project->code }})@endif</span>
            </div>
        </div>

        <div class="opening" style="text-indent: 1cm; margin-top: 10px;">
            Dengan ini mengajukan permohonan pembayaran vendor dengan rincian sebagai berikut:
        </div>

        <div class="form-section" style="margin-left: 1cm;">
            <div class="form-row">
                <span class="form-label">Vendor</span>
                <span class="form-value">: {{ $vendorPayment->vendor->name ?? '-' }}</span>
            </div>
            <div class="form-row">
                <span class="form-label">No. Invoice</span>
                <span class="form-value">: {{ $vendorPayment->invoice_number ?? '-' }}</span>
            </div>
            <div class="form-row">
                <span class="form-label">No. PO</span>
                <span class="form-value">: {{ $vendorPayment->po_number ?? '-' }}</span>
            </div>
            <div class="form-row">
                <span class="form-label">Tipe Pembayaran</span>
                <span class="form-value">: {{ ucfirst(str_replace('_', ' ', $vendorPayment->payment_type ?? '-')) }}</span>
            </div>
            <div class="form-row">
                <span class="form-label">Tanggal Pembayaran</span>
                <span class="form-value">: {{ $vendorPayment->payment_date ? $vendorPayment->payment_date->format('d F Y') : '-' }}</span>
            </div>
        </div>

        <div class="amount-box">
            <div class="amount-label">Jumlah Pembayaran</div>
            <div class="amount-value">Rp {{ number_format($vendorPayment->amount, 0, ',', '.') }}</div>
        </div>

        <div class="description-section">
            <div class="description-label">Deskripsi:</div>
            <div class="description-text">
                {{ $vendorPayment->description ?? '-' }}
            </div>
        </div>

        @if($vendorPayment->notes)
        <div class="description-section">
            <div class="description-label">Catatan:</div>
            <div class="description-text" style="background: #f0f8ff; border-color: #0066cc;">
                {{ $vendorPayment->notes }}
            </div>
        </div>
        @endif

        <div class="closing" style="margin-top: 12px;">
            Demikian surat permohonan ini saya buat dengan sebenar-benarnya. Atas perhatian dan kebijaksanaan Bapak/Ibu, saya ucapkan terima kasih.
        </div>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-left">
            <div style="margin-bottom: 10px; font-size: 10pt;">
                Hormat saya,
            </div>
            <div class="signature-label">
                {{ $vendorPayment->user->name ?? '-' }}
            </div>
        </div>
        <div class="signature-right">
            @if($vendorPayment->status->value === 'approved' && $vendorPayment->approvedBy)
            <div style="margin-bottom: 10px; font-size: 10pt;">
                Menyetujui,
            </div>
            <div class="signature-label">
                {{ $vendorPayment->approvedBy->name ?? '-' }}
            </div>
            @if($vendorPayment->approved_at)
            <div style="margin-top: 3px; font-size: 8pt; color: #666;">
                {{ $vendorPayment->approved_at->format('d F Y') }}
            </div>
            @endif
            @endif
        </div>
    </div>

    <!-- Approval Stamp -->
    @if($vendorPayment->status->value === 'approved' && $vendorPayment->approvedBy)
    <div class="approval-stamp">
        <div class="stamp-title">✓ DISETUJUI</div>
        <div class="stamp-name">{{ $vendorPayment->approvedBy->name ?? '-' }}</div>
        @if($vendorPayment->approved_at)
        <div class="stamp-date">{{ $vendorPayment->approved_at->format('d/m/Y H:i') }}</div>
        @endif
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara elektronik dan sah tanpa tanda tangan basah</p>
        <p>PT. PURI GANESHA ENGINEERING - {{ date('Y') }}</p>
    </div>
</body>
</html>
