<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Keaslian Surat Resmi - TPQ Smart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        .verification-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,.08);
            overflow: hidden;
        }
        .header-status {
            background: linear-gradient(135deg, #198754 0%, #157347 100%);
            color: #fff;
            padding: 30px 20px;
            text-align: center;
        }
        .header-status .status-icon {
            font-size: 55px;
            margin-bottom: 12px;
        }
        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px dashed #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            width: 160px;
            font-weight: 600;
            color: #6c757d;
            flex-shrink: 0;
        }
        .info-value {
            flex-grow: 1;
            font-weight: 500;
        }
        @media (max-width: 576px) {
            .info-row {
                flex-direction: column;
            }
            .info-label {
                width: 100%;
                margin-bottom: 3px;
            }
        }
    </style>
</head>
<body class="py-4">

    <div class="container" style="max-width: 720px;">
        <div class="text-center mb-3">
            <h5 class="fw-bold text-secondary text-uppercase mb-0">Sistem Informasi Administrasi TPQ Smart</h5>
            <small class="text-muted">Layanan Verifikasi Dokumen & Tanda Tangan Digital Resmi</small>
        </div>

        <div class="card verification-card">
            <!-- Header Status -->
            <div class="header-status">
                <div class="status-icon">
                    <i class="fas fa-shield-check"></i>
                </div>
                <h3 class="fw-bold mb-1">DOKUMEN ASLI TERVERIFIKASI</h3>
                <p class="mb-0 opacity-75">Surat resmi ini sah dan tercatat pada pangkalan data sistem informasi</p>
            </div>

            <!-- Body Details -->
            <div class="card-body p-4 bg-white">
                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-check-circle fa-2x me-3"></i>
                    <div>
                        <strong>Keabsahan Dokumen Sah:</strong>
                        <div class="small">Tanda tangan digital dan nomor surat cocok dengan basis data resmi.</div>
                    </div>
                </div>

                <h6 class="text-uppercase fw-bold text-primary mb-3">
                    <i class="fas fa-file-alt me-1"></i> Data Identitas Surat
                </h6>

                <div class="info-row">
                    <div class="info-label">Nomor Surat</div>
                    <div class="info-value"><strong class="text-dark fs-5"><?= esc($surat['NomorSurat'] ?? '-') ?></strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Perihal / Hal</div>
                    <div class="info-value"><strong><?= esc($surat['Hal'] ?? '-') ?></strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Surat</div>
                    <div class="info-value"><?= !empty($surat['Tanggal']) ? date('d F Y', strtotime($surat['Tanggal'])) : '-' ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Lembaga Penerbit</div>
                    <div class="info-value"><?= esc($surat['NamaTpq'] ?? 'Lembaga TPQ') ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Ditujukan Kepada</div>
                    <div class="info-value"><?= esc($surat['TujuanNama'] ?? '-') ?> <?= !empty($surat['TujuanJabatan']) ? '(' . esc($surat['TujuanJabatan']) . ')' : '' ?></div>
                </div>

                <h6 class="text-uppercase fw-bold text-primary mt-4 mb-3">
                    <i class="fas fa-signature me-1"></i> Penanda Tangan
                </h6>

                <?php if (!empty($penandaTangan1)): ?>
                    <div class="info-row">
                        <div class="info-label"><?= esc($surat['Jabatan1'] ?? 'Mengetahui') ?></div>
                        <div class="info-value">
                            <strong><?= esc($penandaTangan1['Nama'] ?? '-') ?></strong>
                            <span class="badge bg-success ms-2"><i class="fas fa-qrcode me-1"></i> Valid Digital</span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($penandaTangan2)): ?>
                    <div class="info-row">
                        <div class="info-label"><?= esc($surat['Jabatan2'] ?? 'Pembuat') ?></div>
                        <div class="info-value">
                            <strong><?= esc($penandaTangan2['Nama'] ?? '-') ?></strong>
                            <span class="badge bg-success ms-2"><i class="fas fa-qrcode me-1"></i> Valid Digital</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <div class="card-footer bg-light text-center py-3">
                <small class="text-muted">
                    Diverifikasi secara realtime pada: <?= date('d-m-Y H:i:s') ?> WIB<br>
                    Website resmi: <strong><?= base_url() ?></strong>
                </small>
            </div>
        </div>
    </div>

</body>
</html>
