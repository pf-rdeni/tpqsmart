<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <form action="<?= base_url('backend/penghargaan/ttd-config/save/' . $jenis['Id']) ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-signature text-primary mr-2"></i>
                    Atur Penandatangan: [<?= esc($jenis['Kode']) ?>] <?= esc($jenis['NamaPenghargaan']) ?>
                </h3>
                <a href="<?= base_url('backend/penghargaan/ttd-config') ?>" class="btn btn-secondary btn-sm ml-auto">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <?php
                        $s = $slots[$i] ?? [
                            'IsActive'      => $i <= 2 ? 1 : 0,
                            'SumberTtd'     => $i === 1 ? 'jabatan_kelas' : ($i === 2 ? 'jabatan_lembaga' : 'manual'),
                            'IdJabatan'     => '',
                            'NamaManual'    => '',
                            'JabatanManual' => '',
                            'LabelTtd'      => $i === 1 ? 'Wali Kelas' : ($i === 2 ? 'Kepala TPQ' : 'Mengetahui, Ketua Yayasan'),
                            'ModeTtd'       => 'qr',
                            'FileTtd'       => '',
                        ];
                        ?>
                        <div class="col-md-4">
                            <div class="card border shadow-none h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                    <strong>Penandatangan #<?= $i ?></strong>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="slotActive<?= $i ?>" name="slot_<?= $i ?>_active" value="1" <?= !empty($s['IsActive']) ? 'checked' : '' ?>>
                                        <label class="custom-control-label text-sm" for="slotActive<?= $i ?>">Aktif</label>
                                    </div>
                                </div>
                                <div class="card-body text-sm">
                                    <div class="form-group">
                                        <label>Label di Sertifikat</label>
                                        <input type="text" name="slot_<?= $i ?>_label" class="form-control form-control-sm" value="<?= esc($s['LabelTtd']) ?>" placeholder="Misal: Kepala TPQ">
                                    </div>

                                    <div class="form-group">
                                        <label>Sumber Penandatangan</label>
                                        <select name="slot_<?= $i ?>_sumber" class="form-control form-control-sm sumber-select" data-slot="<?= $i ?>">
                                            <option value="jabatan_kelas" <?= $s['SumberTtd'] === 'jabatan_kelas' ? 'selected' : '' ?>>Jabatan Kelas (Wali/Guru Kelas)</option>
                                            <option value="jabatan_lembaga" <?= $s['SumberTtd'] === 'jabatan_lembaga' ? 'selected' : '' ?>>Jabatan Lembaga (Kepala TPQ/Struktur)</option>
                                            <option value="manual" <?= $s['SumberTtd'] === 'manual' ? 'selected' : '' ?>>Input Manual (Yayasan / Pengurus)</option>
                                        </select>
                                    </div>

                                    <div class="form-group wrap-jabatan-<?= $i ?> <?= $s['SumberTtd'] === 'manual' ? 'd-none' : '' ?>">
                                        <label>Pilih Jabatan</label>
                                        <select name="slot_<?= $i ?>_jabatan" class="form-control form-control-sm">
                                            <option value="">-- Pilih Jabatan --</option>
                                            <?php foreach ($listJabatan as $jab): ?>
                                                <option value="<?= $jab['IdJabatan'] ?>" <?= (string) $s['IdJabatan'] === (string) $jab['IdJabatan'] ? 'selected' : '' ?>>
                                                    <?= esc($jab['NamaJabatan']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="wrap-manual-<?= $i ?> <?= $s['SumberTtd'] !== 'manual' ? 'd-none' : '' ?>">
                                        <div class="form-group">
                                            <label>Nama Pejabat (Manual)</label>
                                            <input type="text" name="slot_<?= $i ?>_nama_manual" class="form-control form-control-sm" value="<?= esc($s['NamaManual']) ?>" placeholder="Contoh: H. Ahmad Fauzi">
                                        </div>
                                        <div class="form-group">
                                            <label>Jabatan (Manual)</label>
                                            <input type="text" name="slot_<?= $i ?>_jabatan_manual" class="form-control form-control-sm" value="<?= esc($s['JabatanManual']) ?>" placeholder="Contoh: Ketua Yayasan">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Mode Tanda Tangan</label>
                                        <select name="slot_<?= $i ?>_mode" class="form-control form-control-sm mode-select" data-slot="<?= $i ?>">
                                            <option value="qr" <?= $s['ModeTtd'] === 'qr' ? 'selected' : '' ?>>QR Code Digital (Terverifikasi)</option>
                                            <option value="kosong" <?= $s['ModeTtd'] === 'kosong' ? 'selected' : '' ?>>Kolom Kosong (TTD Manual)</option>
                                            <option value="upload" <?= $s['ModeTtd'] === 'upload' ? 'selected' : '' ?>>Upload Gambar TTD (PNG Transparan)</option>
                                        </select>
                                    </div>

                                    <div class="form-group wrap-upload-<?= $i ?> <?= $s['ModeTtd'] !== 'upload' ? 'd-none' : '' ?>">
                                        <label>Upload File TTD (PNG disarankan)</label>
                                        <input type="file" name="slot_<?= $i ?>_file" class="form-control-file form-control-sm" accept="image/png,image/jpeg,image/webp">
                                        <?php if (!empty($s['FileTtd'])): ?>
                                            <div class="mt-2 p-1 border bg-white text-center">
                                                <img src="<?= base_url($s['FileTtd']) ?>" alt="TTD" style="max-height: 60px;">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="card-footer bg-white text-right">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-save mr-1"></i> Simpan Konfigurasi TTD
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.sumber-select').forEach(function (sel) {
        sel.addEventListener('change', function () {
            const slot = this.dataset.slot;
            const isManual = this.value === 'manual';
            document.querySelector('.wrap-jabatan-' + slot).classList.toggle('d-none', isManual);
            document.querySelector('.wrap-manual-' + slot).classList.toggle('d-none', !isManual);
        });
    });

    document.querySelectorAll('.mode-select').forEach(function (sel) {
        sel.addEventListener('change', function () {
            const slot = this.dataset.slot;
            document.querySelector('.wrap-upload-' + slot).classList.toggle('d-none', this.value !== 'upload');
        });
    });
});
</script>

<?= $this->endSection(); ?>
