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
        }

        .info-label {
            min-width: 200px;
            flex-shrink: 0;
        }

        .info-value {
            flex-grow: 1;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Validasi Tanda Tangan Digital</h4>
            </div>
            <div class="card-body">
                <!-- Informasi Dokumen -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">Informasi Dokumen</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Jenis Dokumen :</div>
                                    <div class="info-value"><strong><?= toTitleCase(esc($signature['JenisDokumen'])) ?></strong></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Status Validasi :</div>
                                    <div class="info-value"><strong><span class="badge bg-success"><?= esc($signature['StatusValidasi']) ?></span></strong></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Tanggal Tanda Tangan :</div>
                                    <div class="info-value"><strong><?= formatTanggalIndonesia($signature['TanggalTtd'], 'd F Y H:i') ?></strong></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Token :</div>
                                    <div class="info-value"><strong><small class="text-muted"><?= esc($signature['Token']) ?></small></strong></div>
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
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Nama Guru :</div>
                                    <div class="info-value"><strong><?= toTitleCase(esc($guru->Nama)) ?></strong></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">NIK :</div>
                                    <div class="info-value"><strong><?= 'XXXX' . substr(esc($guru->IdGuru), 4) ?></strong></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Jabatan :</div>
                                    <div class="info-value"><strong><?= toTitleCase(esc($guru->NamaJabatan)) ?></strong></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Status Saat ini:</div>
                                    <div class="info-value"><strong><span class="badge bg-success"><?= esc($guru->Status) ? 'Aktif' : 'Tidak Aktif' ?></span></strong></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Santri dan Lembaga -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">Informasi Santri dan Lembaga</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Nama Santri :</div>
                                    <div class="info-value"><strong><?= toTitleCase(esc($santri['NamaSantri'])) ?></strong></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">NIS :</div>
                                    <div class="info-value"><strong><?= esc($santri['IdSantri']) ?></strong></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Nama TPQ :</div>
                                    <div class="info-value"><strong><?= toTitleCase(esc($tpq['NamaTpq'])) ?></strong></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Alamat :</div>
                                    <div class="info-value"><strong><?= toTitleCase(esc($tpq['Alamat'])) ?></strong></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Kelas :</div>
                                    <div class="info-value"><strong><?= esc($kelas['NamaKelas']) ?></strong></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Tahun Ajaran :</div>
                                    <div class="info-value"><strong><?= convertTahunAjaran(esc($kelas['IdTahunAjaran'])) ?></strong></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Semester :</div>
                                    <div class="info-value"><strong><?= toTitleCase(esc($signature['Semester'])) ?></strong></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tanda Tangan Digital -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">Tanda Tangan Digital</h5>
                        <div class="text-center">
                            <img src="<?= esc($signature['SignatureData']) ?>" alt="Tanda Tangan Digital" class="img-fluid" style="max-height: 150px;">
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