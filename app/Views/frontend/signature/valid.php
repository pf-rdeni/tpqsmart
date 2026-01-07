<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Tanda Tangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .info-row {
            display: flex;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }

        .info-label {
            min-width: 200px;
            flex-shrink: 0;
        }

        .info-value {
            flex-grow: 1;
            min-width: 0;
            /* Allow flex item to shrink below content size */
            word-break: break-word;
            /* Break long words */
            overflow-wrap: break-word;
            /* Break long words */
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .info-row {
                flex-direction: row;
                /* Tetap horizontal di mobile */
                align-items: flex-start;
            }

            .info-label {
                min-width: 140px;
                /* Lebih kecil di mobile */
                max-width: 140px;
                flex-shrink: 0;
                font-weight: 600;
                font-size: 0.9rem;
            }

            .info-value {
                flex: 1;
                min-width: 0;
                padding-left: 8px;
                font-size: 0.9rem;
            }

            .container {
                padding: 10px;
            }

            .card {
                margin: 10px 0;
            }

            .card-body {
                padding: 15px;
            }

            h5 {
                font-size: 1.1rem;
            }

            /* Token specific styling for mobile */
            .token-value {
                font-size: 0.7rem;
                word-break: break-all;
                overflow-wrap: anywhere;
                font-family: 'Courier New', monospace;
                background-color: #f8f9fa;
                padding: 6px;
                border-radius: 4px;
                display: block;
                max-width: 100%;
            }
        }

        /* Desktop token styling */
        @media (min-width: 769px) {
            .token-value {
                font-size: 0.85rem;
                word-break: break-all;
                overflow-wrap: break-word;
                font-family: 'Courier New', monospace;
                background-color: #f8f9fa;
                padding: 4px 8px;
                border-radius: 4px;
                display: inline-block;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-3 mt-md-5 px-2 px-md-3">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0" style="font-size: 1.1rem;">Validasi Tanda Tangan Digital</h4>
            </div>
            <div class="card-body">
                <!-- Informasi Dokumen -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">Informasi Dokumen</h5>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Jenis Dokumen</div>
                                    <div class="info-value"><strong><?= toTitleCase(esc($signature['JenisDokumen'])) ?></strong></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Status Validasi</div>
                                    <div class="info-value"><strong><span class="badge bg-success"><?= esc($signature['StatusValidasi']) ?></span></strong></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Tanggal Tanda Tangan</div>
                                    <div class="info-value"><strong><?= formatTanggalIndonesia($signature['TanggalTtd'], 'd F Y H:i') ?></strong></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Token</div>
                                    <div class="info-value">
                                        <span class="token-value"><?= esc($signature['Token']) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Penandatangan -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">Informasi Penandatangan</h5>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <?php if (($signature['JenisDokumen'] === 'Munaqosah' || $signature['JenisDokumen'] === 'Surat Rekomendasi') && $signature['SignatureData'] === 'Ketua FKPQ'): ?>
                                    <?php if (!empty($fkpq)): ?>
                                        <div class="info-row">
                                            <div class="info-label">Nama Ketua FKPQ</div>
                                            <div class="info-value"><strong><?= toTitleCase(esc($fkpq['KetuaFkpq'] ?? '-')) ?></strong></div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label">Jabatan</div>
                                            <div class="info-value"><strong>Ketua FKPQ</strong></div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label">Nama FKPQ</div>
                                            <div class="info-value"><strong><?= toTitleCase(esc($fkpq['NamaFkpq'] ?? '-')) ?></strong></div>
                                        </div>
                                    <?php endif; ?>
                                <?php elseif (isset($perlombaan)): ?>
                                    <div class="info-row">
                                        <div class="info-label">Nama Penandatangan</div>
                                        <div class="info-value"><strong><?= toTitleCase(esc($perlombaan['signer_name'])) ?></strong></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Jabatan</div>
                                        <div class="info-value"><strong><?= toTitleCase(esc($perlombaan['signer_jabatan'])) ?></strong></div>
                                    </div>
                                <?php else: ?>
                                    <?php if ($guru): ?>
                                        <div class="info-row">
                                            <div class="info-label">Nama Guru</div>
                                            <div class="info-value"><strong><?= toTitleCase(esc($guru->Nama ?? (is_array($guru) ? $guru['Nama'] : ''))) ?></strong></div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label">Jabatan</div>
                                            <div class="info-value"><strong><?= toTitleCase(esc($guru->NamaJabatan ?? (is_array($guru) ? ($guru['NamaJabatan'] ?? '-') : '-'))) ?></strong></div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label">Status Saat ini</div>
                                            <div class="info-value"><strong><span class="badge bg-success"><?= esc($guru->Status ?? (is_array($guru) ? ($guru['Status'] ?? 0) : 0)) ? 'Aktif' : 'Tidak Aktif' ?></span></strong></div>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Santri/Guru dan Lembaga -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2"><?= ($signature['JenisDokumen'] === 'Surat Rekomendasi') ? 'Informasi Guru dan Lembaga' : 'Informasi Santri dan Lembaga' ?></h5>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <?php if ($signature['JenisDokumen'] === 'Surat Rekomendasi' && !empty($guru)): ?>
                                    <div class="info-row">
                                        <div class="info-label">Nama Guru</div>
                                        <div class="info-value"><strong><?= toTitleCase(esc($guru['Nama'])) ?></strong></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">NIK</div>
                                        <div class="info-value"><strong>
                                                <?php
                                                $nik = esc($guru['IdGuru']);
                                                if (strlen($nik) >= 8) {
                                                    // Tampilkan 4 digit pertama dan 4 digit terakhir, sisanya disamarkan
                                                    $first = substr($nik, 0, 4);
                                                    $last = substr($nik, -4);
                                                    $masked = $first . str_repeat('*', strlen($nik) - 8) . $last;
                                                    echo $masked;
                                                } else {
                                                    echo str_repeat('*', strlen($nik));
                                                }
                                                ?>
                                            </strong></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Tempat Tugas</div>
                                        <div class="info-value"><strong><?= toTitleCase(esc($guru['TempatTugas'] ?? '-')) ?></strong></div>
                                    </div>
                                    </div>
                                <?php elseif (isset($perlombaan)): ?>
                                    <div class="info-row">
                                        <div class="info-label">Nama Event</div>
                                        <div class="info-value"><strong><?= esc($perlombaan['lomba']['NamaLomba'] ?? '-') ?></strong></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Cabang Lomba</div>
                                        <div class="info-value"><strong><?= esc($perlombaan['cabang']['NamaCabang'] ?? '-') ?></strong></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Kategori</div>
                                        <div class="info-value"><strong><?= esc($perlombaan['cabang']['Kategori'] ?? '-') ?></strong></div>
                                    </div>
                                    <?php if ($perlombaan['hasil']): ?>
                                    <div class="info-row">
                                        <div class="info-label">Nama Peserta</div>
                                        <div class="info-value"><strong><?= esc($perlombaan['hasil']['NamaSantri'] ?? '-') ?></strong></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Asal Lembaga</div>
                                        <div class="info-value"><strong><?= esc($perlombaan['hasil']['NamaTpq'] ?? '-') ?></strong></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Prestasi</div>
                                        <div class="info-value"><strong>Juara <?= esc($perlombaan['hasil']['Peringkat'] ?? '-') ?></strong></div>
                                    </div>
                                    <?php endif; ?>
                                <?php elseif (!empty($santri)): ?>
                                    <div class="info-row">
                                        <div class="info-label">Nama Santri</div>
                                        <div class="info-value"><strong><?= toTitleCase(esc($santri['NamaSantri'])) ?></strong></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Id Santri</div>
                                        <div class="info-value"><strong><?= esc($santri['IdSantri']) ?></strong></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Status Saat ini</div>
                                        <div class="info-value"><strong><span class="badge <?= esc($santri['Active']) ? 'bg-success' : 'bg-danger' ?>"><?= esc($santri['Active']) ? 'Aktif' : 'Tidak Aktif' ?></span></strong></div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($tpq)): ?>
                                    <div class="info-row">
                                        <div class="info-label">Nama TPQ</div>
                                        <div class="info-value"><strong><?= toTitleCase(esc($tpq['NamaTpq'])) ?></strong></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Alamat</div>
                                        <div class="info-value"><strong><?= toTitleCase(esc($tpq['Alamat'])) ?></strong></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12 col-md-6">
                                <?php if ($signature['JenisDokumen'] === 'Surat Rekomendasi' && $signature['SignatureData'] === 'Ketua FKPQ'): ?>
                                    <?php if (!empty($fkpq)): ?>
                                        <div class="info-row">
                                            <div class="info-label">Nama FKPQ</div>
                                            <div class="info-value"><strong><?= toTitleCase(esc($fkpq['NamaFkpq'] ?? '-')) ?></strong></div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label">Ketua FKPQ</div>
                                            <div class="info-value"><strong><?= toTitleCase(esc($fkpq['KetuaFkpq'] ?? '-')) ?></strong></div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label">Kecamatan</div>
                                            <div class="info-value"><strong><?= toTitleCase(esc($fkpq['Kecamatan'] ?? '-')) ?></strong></div>
                                        </div>
                                    <?php endif; ?>
                                <?php elseif ($signature['JenisDokumen'] === 'Munaqosah' && $signature['SignatureData'] === 'Ketua FKPQ'): ?>
                                    <div class="info-row">
                                        <div class="info-label">No. Peserta</div>
                                        <div class="info-value"><strong><?= esc($pesertaMunaqosah['NoPeserta'] ?? '-') ?></strong></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Tahun Ajaran</div>
                                        <div class="info-value"><strong><?= convertTahunAjaran(esc($signature['IdTahunAjaran'])) ?></strong></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Type Ujian</div>
                                        <div class="info-value"><strong>Munaqosah</strong></div>
                                    </div>
                                <?php else: ?>
                                    <?php if ($kelas): ?>
                                        <div class="info-row">
                                            <div class="info-label">Kelas</div>
                                            <div class="info-value"><strong><?= esc($kelas['NamaKelas']) ?></strong></div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label">Tahun Ajaran</div>
                                            <div class="info-value"><strong><?= convertTahunAjaran(esc($kelas['IdTahunAjaran'])) ?></strong></div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label">Semester</div>
                                            <div class="info-value"><strong><?= toTitleCase(esc($signature['Semester'])) ?></strong></div>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tanda Tangan Digital -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">Tanda Tangan Digital</h5>
                        <div class="row">
                            <?php if (!empty($signature['SignatureData']) && 
                                      $signature['SignatureData'] !== 'Kepsek' && 
                                      $signature['SignatureData'] !== 'Walas' && 
                                      $signature['SignatureData'] !== 'Ketua FKPQ' && 
                                      strpos($signature['JenisDokumen'], 'SertifikatLomba') === false): ?>
                                <div class="col-12 col-md-6 text-center mb-3">
                                    <h6 class="mb-2">Gambar Tanda Tangan</h6>
                                    <img src="<?= esc($signature['SignatureData']) ?>" alt="Tanda Tangan Digital" class="img-fluid" style="max-height: 150px;">
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($signature['QrCode'])): ?>
                                <div class="col-12 col-md-<?= (!empty($signature['SignatureData']) && 
                                                              $signature['SignatureData'] !== 'Kepsek' && 
                                                              $signature['SignatureData'] !== 'Walas' && 
                                                              $signature['SignatureData'] !== 'Ketua FKPQ' && 
                                                              strpos($signature['JenisDokumen'], 'SertifikatLomba') === false) ? '6' : '12' ?> text-center mb-3">
                                    <h6 class="mb-2">QR Code Validasi</h6>
                                    <?php
                                    $qrPath = FCPATH . 'uploads/qr/' . $signature['QrCode'];
                                    
                                    // Fallback: Check if file mismatch exists (e.g. DB has .png but file is .svg with prefix)
                                    if (!file_exists($qrPath)) {
                                        $altFilename = 'signature_' . $signature['Token'] . '.svg';
                                        if (file_exists(FCPATH . 'uploads/qr/' . $altFilename)) {
                                            $signature['QrCode'] = $altFilename;
                                            $qrPath = FCPATH . 'uploads/qr/' . $altFilename;
                                        }
                                    }

                                    if (file_exists($qrPath)) {
                                        $qrContent = file_get_contents($qrPath);
                                        $ext = pathinfo($signature['QrCode'], PATHINFO_EXTENSION);
                                        $mime = $ext === 'svg' ? 'image/svg+xml' : 'image/' . strtolower($ext);
                                        echo '<img src="data:' . $mime . ';base64,' . base64_encode($qrContent) . '" alt="QR Code Validasi" class="img-fluid" style="max-width: 200px; max-height: 200px;">';
                                        echo '<p class="mt-2 text-muted"><small>Scan QR code ini untuk memverifikasi tanda tangan digital</small></p>';
                                    } else {
                                        echo '<p class="text-muted"><small>QR Code tidak ditemukan</small></p>';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <small>Dokumen ini telah ditandatangani secara digital dan divalidasi oleh sistem.</small>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

</html>