<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle mr-1"></i> <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-triangle mr-1"></i> <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="alert alert-info text-sm">
        <i class="fas fa-info-circle mr-1"></i>
        Atur siapa yang menandatangani sertifikat untuk setiap jenis penghargaan (1, 2, atau 3 penandatangan: misal Wali Kelas, Kepala TPQ, dan Yayasan/Pengurus). Tersedia 3 mode TTD: <strong>QR Code Digital</strong>, <strong>Kolom Kosong (Manual)</strong>, atau <strong>Upload Gambar TTD Transparan</strong>.
        <br>
        <strong>Setting Default (jika belum diatur):</strong> 2 Penandatangan dengan Mode <strong>QR Code Digital</strong> — yaitu <strong>#1 Wali Kelas</strong> (Jabatan Kelas) dan <strong>#2 Kepala TPQ</strong> (Jabatan Lembaga). Slot <strong>#3</strong> (Yayasan/Pengurus) bersifat opsional.
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h3 class="card-title font-weight-bold"><i class="fas fa-signature text-primary mr-2"></i>Konfigurasi Penandatangan per Jenis Penghargaan</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 text-sm">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 90px;">Kode</th>
                            <th>Jenis Penghargaan</th>
                            <th>Penandatangan Aktif (Slot 1 - 3)</th>
                            <th class="text-center" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listJenis as $j): ?>
                            <?php $slots = $configMap[$j['Id']] ?? []; ?>
                            <tr>
                                <td><span class="badge badge-light border font-weight-bold"><?= esc($j['Kode']) ?></span></td>
                                <td><strong><?= esc($j['NamaPenghargaan']) ?></strong></td>
                                <td>
                                    <?php if (empty($slots)): ?>
                                        <span class="text-muted font-italic">Default: #1 Wali Kelas (QR) &amp; #2 Kepala TPQ (QR)</span>
                                    <?php else: ?>
                                        <?php foreach ($slots as $s): ?>
                                            <?php
                                            $modeBadge = match ($s['ModeTtd']) {
                                                'qr'     => '<span class="badge badge-success"><i class="fas fa-qrcode"></i> QR</span>',
                                                'upload' => '<span class="badge badge-info"><i class="fas fa-image"></i> Upload</span>',
                                                default  => '<span class="badge badge-secondary"><i class="fas fa-pen"></i> Kosong</span>',
                                            };
                                            $sumberText = $s['SumberTtd'] === 'manual'
                                                ? ($s['NamaManual'] ?: $s['JabatanManual'])
                                                : ($s['NamaJabatan'] ?: $s['LabelTtd']);
                                            ?>
                                            <span class="badge badge-light border mr-1 mb-1 p-1">
                                                #<?= esc($s['UrutanTtd']) ?> <?= esc($s['LabelTtd']) ?> (<?= esc($sumberText) ?>) <?= $modeBadge ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('backend/penghargaan/ttd-config/form/' . $j['Id']) ?>" class="btn btn-primary btn-xs">
                                        <i class="fas fa-cog mr-1"></i> Atur TTD
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
