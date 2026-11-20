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
                <?php if (!empty($scannedSigner)): ?>
                    <!-- Banner Penegasan Khusus Penanda Tangan yang di-Scan -->
                    <div class="alert alert-success border-success shadow-sm mb-4" style="background-color: #f0fdf4; border-left: 5px solid #22c55e;">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-certificate text-success fa-3x me-3 mt-1"></i>
                            <div class="w-100">
                                <span class="badge bg-success mb-2 px-2 py-1"><i class="fas fa-check-circle me-1"></i> TANDA TANGAN ELEKTRONIK TERVERIFIKASI RESMI</span>
                                <div class="fw-bold text-dark fs-6 mb-2">Dokumen ini telah ditandatangani secara sah secara elektronik oleh:</div>
                                <div class="row g-2 bg-white p-2 rounded border">
                                    <div class="col-sm-6">
                                        <div class="text-muted small">Nama Penanda Tangan:</div>
                                        <strong class="text-primary fs-6"><?= esc($scannedSigner['Nama']) ?></strong>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-muted small">Jabatan:</div>
                                        <strong><?= esc($scannedSigner['Jabatan'] ?: '-') ?></strong>
                                    </div>
                                    <?php if (!empty($scannedSigner['Instansi'])): ?>
                                    <div class="col-sm-6">
                                        <div class="text-muted small">Instansi / Lembaga:</div>
                                        <div><?= esc($scannedSigner['Instansi']) ?></div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-sm-6">
                                        <div class="text-muted small">Kode Verifikasi TTD:</div>
                                        <span class="badge bg-dark font-monospace"><?= esc($scannedSigner['Token']) ?></span>
                                    </div>
                                </div>
                                <div class="small text-muted mt-2">
                                    Untuk Dokumen: <strong><?= esc($surat['NomorSurat'] ?? '-') ?></strong> — <em><?= esc($surat['Hal'] ?? '-') ?></em>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-check-circle fa-2x me-3"></i>
                        <div>
                            <strong>Keabsahan Dokumen Sah:</strong>
                            <div class="small">Tanda tangan digital dan nomor surat cocok dengan basis data resmi.</div>
                        </div>
                    </div>
                <?php endif; ?>

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
                    <i class="fas fa-signature me-1"></i> Daftar Penanda Tangan pada Dokumen Ini
                </h6>

                <?php if (!empty($allSigners) && is_array($allSigners)): ?>
                    <?php foreach ($allSigners as $s): ?>
                        <?php
                        $isCurrentScanned = (!empty($scannedSigner['Token']) && ($s['token_10'] ?? '') === $scannedSigner['Token']);
                        $isQr = (($s['mode_ttd'] ?? '') === 'QR');
                        $posText = '';
                        if (!empty($s['posisi'])) {
                            if (strpos($s['posisi'], 'mengetahui') !== false) {
                                $posText = ' (Mengetahui)';
                            } elseif ($s['posisi'] === 'kanan') {
                                $posText = ' (Penanggung Jawab)';
                            } elseif ($s['posisi'] === 'kiri') {
                                $posText = ' (Pelaksana / Pembuat)';
                            } elseif ($s['posisi'] === 'tengah') {
                                $posText = ' (Penyusun)';
                            }
                        }
                        ?>
                        <div class="info-row <?= $isCurrentScanned ? 'p-2 rounded bg-light border' : '' ?>">
                            <div class="info-label">
                                <?= esc($s['jabatan'] ?: 'Penanda Tangan') ?>
                                <small class="text-muted d-block" style="font-size: 0.75rem"><?= esc($posText) ?></small>
                            </div>
                            <div class="info-value">
                                <strong><?= esc($s['nama'] ?: '-') ?></strong>
                                <?php if (!empty($s['nip']) && !empty($s['show_nip'])): ?>
                                    <div class="small text-muted">NIP/ID: <?= esc($s['nip']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($s['instansi']) && !empty($s['show_instansi'])): ?>
                                    <div class="small text-muted"><?= esc($s['instansi']) ?></div>
                                <?php endif; ?>

                                <?php if ($isCurrentScanned): ?>
                                    <span class="badge bg-success ms-1"><i class="fas fa-qrcode me-1"></i> Valid Digital (Tanda Tangan Ini)</span>
                                <?php elseif ($isQr): ?>
                                    <span class="badge bg-primary ms-1"><i class="fas fa-qrcode me-1"></i> Valid Digital</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary ms-1"><i class="fas fa-pen me-1"></i> Tanda Tangan Basah / Fisik</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
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
