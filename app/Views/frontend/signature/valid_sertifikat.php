<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Sertifikat Penghargaan</title>
    <link rel="stylesheet" href="<?= base_url('template/backend/plugins/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('template/backend/dist/css/adminlte.min.css') ?>">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card card-success card-outline shadow">
                <div class="card-header bg-white text-center py-3">
                    <i class="fas fa-check-circle text-success fa-3x mb-2"></i>
                    <h4 class="font-weight-bold text-success mb-0">SERTIFIKAT TERVERIFIKASI ASLI</h4>
                    <small class="text-muted"><?= esc($penerima['NamaTpq'] ?? 'Lembaga TPQ') ?></small>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm mb-0">
                        <tr>
                            <th style="width: 38%;">Nomor Seri</th>
                            <td><code><?= esc($penerima['NomorSeri']) ?></code></td>
                        </tr>
                        <tr>
                            <th>Nama Santri</th>
                            <td><strong><?= esc($penerima['NamaSantri']) ?></strong></td>
                        </tr>
                        <tr>
                            <th>Kelas</th>
                            <td><?= esc($penerima['NamaKelas'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Penghargaan</th>
                            <td>
                                <span class="badge badge-primary"><?= esc($penerima['Kode']) ?></span>
                                <strong><?= esc($penerima['NamaPenghargaan']) ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Periode</th>
                            <td>Semester <?= esc($penerima['Semester']) ?> · TA <?= esc($penerima['IdTahunAjaran']) ?></td>
                        </tr>
                        <tr>
                            <th>Penandatangan</th>
                            <td>
                                <strong><?= esc($sigMeta['Nama'] ?? '-') ?></strong><br>
                                <small class="text-muted"><?= esc($sigMeta['Jabatan'] ?? '-') ?></small>
                            </td>
                        </tr>
                        <tr>
                            <th>Status Sertifikat</th>
                            <td>
                                <?php if (($penerima['StatusSertifikat'] ?? '') === 'final'): ?>
                                    <span class="badge badge-success"><i class="fas fa-shield-alt mr-1"></i> Resmi / Final</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Draft (Belum Dirilis)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer bg-white text-center text-muted text-sm">
                    Tanggal TTD Digital: <?= esc($signature['TanggalTtd'] ?? '-') ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
