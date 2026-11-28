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

    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header bg-white">
            <h3 class="card-title font-weight-bold"><i class="fas fa-cog text-primary mr-2"></i>Pengaturan Global Modul Penghargaan</h3>
        </div>
        <form action="<?= base_url('backend/penghargaan/setting/save') ?>" method="post">
            <?= csrf_field() ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Bobot Nilai Default (%)</label>
                        <input type="number" name="BobotNilaiDefault" class="form-control" min="0" max="100" value="<?= esc($setting['BobotNilaiDefault']) ?>" required>
                        <small class="text-muted">Total Bobot Nilai + Bobot Kehadiran harus 100%.</small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Bobot Kehadiran Default (%)</label>
                        <input type="number" name="BobotHadirDefault" class="form-control" min="0" max="100" value="<?= esc($setting['BobotHadirDefault']) ?>" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Threshold Kehadiran Sempurna (%)</label>
                        <input type="number" name="ThresholdHadirSempurna" class="form-control" min="1" max="100" value="<?= esc($setting['ThresholdHadirSempurna']) ?>" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Jumlah Pemenang Default</label>
                        <input type="number" name="JumlahPemenangDefault" class="form-control" min="0" max="50" value="<?= esc($setting['JumlahPemenangDefault']) ?>" required>
                        <small class="text-muted">Isi 1 untuk Juara 1 saja, 3 untuk Top 3, atau 0 untuk semua yang memenuhi syarat.</small>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Momen Aktif Default</label>
                        <select name="MomenAktif" class="form-control">
                            <option value="ganjil" <?= $setting['MomenAktif'] === 'ganjil' ? 'selected' : '' ?>>Semester Ganjil</option>
                            <option value="genap" <?= $setting['MomenAktif'] === 'genap' ? 'selected' : '' ?>>Semester Genap</option>
                            <option value="kenaikan" <?= $setting['MomenAktif'] === 'kenaikan' ? 'selected' : '' ?>>Kenaikan Kelas</option>
                            <option value="kapansaja" <?= $setting['MomenAktif'] === 'kapansaja' ? 'selected' : '' ?>>Kapan Saja</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Format Nomor Seri Sertifikat</label>
                        <input type="text" name="FormatNomorSeri" class="form-control" value="<?= esc($setting['FormatNomorSeri']) ?>" required>
                        <small class="text-muted">Variabel tersedia: <code>{TAHUN}</code>, <code>{KODE_TPQ}</code>, <code>{SEM}</code>, <code>{BULAN}</code>, <code>{NO}</code></small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Wilayah / Tempat Penetapan Sertifikat (Opsional)</label>
                        <input type="text" name="WilayahSertifikat" class="form-control" value="<?= esc($setting['WilayahSertifikat'] ?? '') ?>" placeholder="Kosongkan untuk otomatis pakai Kelurahan/Desa TPQ">
                        <small class="text-muted">Contoh: <code>Tanjunguban</code> atau <code>Bintan Utara</code>. Tampil sebagai: <em>Wilayah, 20 Desember 2026</em>.</small>
                    </div>
                    <div class="col-md-12 form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="IsGroupByJenjang" name="IsGroupByJenjang" value="1" <?= !empty($setting['IsGroupByJenjang']) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="IsGroupByJenjang">Juara Lembaga Dikelompokkan Per Jenjang</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>
