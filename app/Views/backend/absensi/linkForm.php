<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<div class="col-12">
    <!-- Card Start -->
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title"><?= isset($link) ? 'Edit Data' : 'Tambah Data Baru'; ?></h3>
            <div class="card-tools">
                <a href="<?= base_url('backend/absensi/link') ?>" class="btn btn-tool" title="Kembali">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <!-- Form Start -->
        <form action="<?= isset($link) ? base_url('backend/absensi/link/update/' . $link['Id']) : base_url('backend/absensi/link/create') ?>" method="post">
            <?= csrf_field() ?>
            <div class="card-body">
                
                <?php if (session()->get('errors')) : ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach (session()->get('errors') as $error) : ?>
                                <li><?= $error ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif ?>

                <!-- TPQ Selection -->
                <div class="form-group">
                    <label for="IdTpq">Lembaga (TPQ)</label>
                    <?php 
                        $sessionTpq = session()->get('IdTpq'); 
                    ?>
                    <select name="IdTpq" id="IdTpq" class="form-control select2" style="width: 100%;">
                        <option value="">-- Pilih Lembaga --</option>
                        <?php foreach ($tpqList as $tpq) : ?>
                            <option value="<?= $tpq['IdTpq'] ?>" 
                                <?= (old('IdTpq') == $tpq['IdTpq']) || (isset($link) && $link['IdTpq'] == $tpq['IdTpq']) || ($sessionTpq == $tpq['IdTpq']) ? 'selected' : '' ?>>
                                <?= $tpq['NamaTpq'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tahun Ajaran Selection -->
                <div class="form-group">
                    <label for="IdTahunAjaran">Tahun Ajaran</label>
                    <select name="IdTahunAjaran" id="IdTahunAjaran" class="form-control select2" style="width: 100%;">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php foreach ($tahunAjaranList as $ta) : ?>
                            <option value="<?= esc($ta['value'], 'attr') ?>" <?= (old('IdTahunAjaran') == $ta['value']) || (isset($link) && $link['IdTahunAjaran'] == $ta['value']) ? 'selected' : '' ?>>
                                <?= esc($ta['display']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php if (isset($link)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> HashKey saat ini: <strong><?= $link['HashKey'] ?></strong>
                    <br>
                    <small>Catatan: Link yang sudah dibagikan akan tetap aktif kecuali Anda me-reset key di halaman utama.</small>
                </div>
                <?php endif; ?>

            </div>
            
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>
