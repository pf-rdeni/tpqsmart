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
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-magic text-primary mr-2"></i>Generate Penerima Penghargaan Otomatis
            </h3>
        </div>
        <form action="<?= base_url('backend/penghargaan/generate/preview') ?>" method="post">
            <?= csrf_field() ?>
            <div class="card-body">
                <div class="alert alert-info text-sm">
                    <i class="fas fa-info-circle mr-1"></i>
                    Sistem akan menghitung kandidat penerima berdasarkan <strong>santri aktif pada tabel kelas santri (Status = 1)</strong> sesuai tahun ajaran yang dipilih, kemudian menggabungkan data nilai &amp; kehadiran.
                </div>

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Tahun Ajaran</label>
                        <select name="IdTahunAjaran" class="form-control" required>
                            <?php if (!empty($tahunAjaranList)): ?>
                                <?php foreach ($tahunAjaranList as $ta): ?>
                                    <option value="<?= esc($ta['IdTahunAjaran']) ?>" <?= $idTahunAjaran == $ta['IdTahunAjaran'] ? 'selected' : '' ?>>
                                        <?= esc($ta['IdTahunAjaran']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="<?= esc($idTahunAjaran) ?>"><?= esc($idTahunAjaran) ?></option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label>Semester</label>
                        <select name="Semester" class="form-control" required>
                            <option value="Ganjil">Semester Ganjil</option>
                            <option value="Genap">Semester Genap</option>
                            <option value="Tahunan">Tahunan (Ganjil + Genap)</option>
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label>Momen Penerbitan</label>
                        <select name="Momen" class="form-control" required>
                            <option value="ganjil" <?= $setting['MomenAktif'] === 'ganjil' ? 'selected' : '' ?>>Serah Terima Rapor Ganjil</option>
                            <option value="genap" <?= $setting['MomenAktif'] === 'genap' ? 'selected' : '' ?>>Serah Terima Rapor Genap</option>
                            <option value="kenaikan" <?= $setting['MomenAktif'] === 'kenaikan' ? 'selected' : '' ?>>Kenaikan Kelas / Wisuda</option>
                            <option value="munaqosah" <?= $setting['MomenAktif'] === 'munaqosah' ? 'selected' : '' ?>>Pasca Munaqosah</option>
                            <option value="kapansaja" <?= $setting['MomenAktif'] === 'kapansaja' ? 'selected' : '' ?>>Event / Kapan Saja</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Jenis Penghargaan</label>
                        <select name="IdJenis" class="form-control" required>
                            <option value="">-- Pilih Jenis Penghargaan --</option>
                            <?php foreach ($listJenis as $j): ?>
                                <option value="<?= $j['Id'] ?>">
                                    [<?= esc($j['Kode']) ?>] <?= esc($j['NamaPenghargaan']) ?> (<?= esc(ucfirst($j['Kategori'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Scope Perhitungan</label>
                        <?php if (!empty($roleCtx['isGuruOnly'])): ?>
                            <input type="hidden" name="Scope" id="scopeSelect" value="kelas">
                            <input type="text" class="form-control" value="Per Kelas (Wali Kelas)" readonly>
                        <?php else: ?>
                            <select name="Scope" id="scopeSelect" class="form-control" required>
                                <option value="kelas">Per Kelas (Juara / Terbaik di 1 Kelas)</option>
                                <option value="jenjang">Per Jenjang (Juara Lembaga Lintas Kelas)</option>
                                <option value="semua">Semua Kelas Se-TPQ</option>
                            </select>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group" id="wrapKelas">
                        <label>Pilih Kelas</label>
                        <select name="IdKelas" class="form-control">
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($listKelas as $k): ?>
                                <option value="<?= esc($k['IdKelas']) ?>"><?= esc($k['NamaKelas']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 form-group d-none" id="wrapJenjang">
                        <label>Pilih Jenjang</label>
                        <select name="NamaJenjang" class="form-control">
                            <option value="">-- Pilih Jenjang --</option>
                            <?php foreach ($listJenjang as $nj): ?>
                                <option value="<?= esc($nj) ?>"><?= esc($nj) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($listJenjang)): ?>
                            <small class="text-danger">Belum ada jenjang terdaftar. Silakan atur di menu <a href="<?= base_url('backend/penghargaan/jenjang') ?>">Jenjang Kelas</a>.</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-right">
                <a href="<?= base_url('backend/penghargaan/dashboard') ?>" class="btn btn-secondary btn-sm mr-1">Kembali</a>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-calculator mr-1"></i> Hitung &amp; Preview Kandidat
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const scopeSelect = document.getElementById('scopeSelect');
    const wrapKelas = document.getElementById('wrapKelas');
    const wrapJenjang = document.getElementById('wrapJenjang');

    function toggleScope() {
        const val = scopeSelect.value;
        wrapKelas.classList.toggle('d-none', val !== 'kelas');
        wrapJenjang.classList.toggle('d-none', val !== 'jenjang');
    }

    scopeSelect.addEventListener('change', toggleScope);
    toggleScope();
});
</script>

<?= $this->endSection(); ?>
