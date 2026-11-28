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
        Kosongkan kolom override jika ingin menggunakan nilai default global lembaga
        (Bobot <strong><?= esc($setting['BobotNilaiDefault']) ?>% : <?= esc($setting['BobotHadirDefault']) ?>%</strong>,
        Hadir Sempurna <strong><?= esc($setting['ThresholdHadirSempurna']) ?>%</strong>,
        Pemenang <strong>Top <?= esc($setting['JumlahPemenangDefault']) ?></strong>).
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-sliders-h text-primary mr-2"></i>Kriteria & Threshold per Jenis (TA <?= esc($idTahunAjaran) ?>)
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 text-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Kode & Jenis Penghargaan</th>
                            <th style="width: 110px;">Nilai Min</th>
                            <th style="width: 110px;">% Hadir Min</th>
                            <th style="width: 100px;">Bobot Nilai</th>
                            <th style="width: 100px;">Bobot Hadir</th>
                            <th style="width: 110px;">Jml Pemenang</th>
                            <th style="width: 110px;" class="text-center">Per Jenjang</th>
                            <th style="width: 90px;" class="text-center">Simpan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listJenis as $j): ?>
                            <?php $kr = $kriteriaMap[$j['Id']] ?? null; ?>
                            <tr>
                                <form action="<?= base_url('backend/penghargaan/kriteria/save') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="IdJenis" value="<?= $j['Id'] ?>">
                                    <input type="hidden" name="IdTahunAjaran" value="<?= esc($idTahunAjaran) ?>">
                                    <td>
                                        <span class="badge badge-light border mr-1"><?= esc($j['Kode']) ?></span>
                                        <strong><?= esc($j['NamaPenghargaan']) ?></strong>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" max="100" name="NilaiMin"
                                            class="form-control form-control-sm"
                                            value="<?= esc($kr['NilaiMin'] ?? '') ?>" placeholder="-">
                                    </td>
                                    <td>
                                        <input type="number" min="0" max="100" name="PersenHadirMin"
                                            class="form-control form-control-sm"
                                            value="<?= esc($kr['PersenHadirMin'] ?? '') ?>" placeholder="<?= esc($setting['ThresholdHadirSempurna']) ?>">
                                    </td>
                                    <td>
                                        <input type="number" min="0" max="100" name="BobotNilai"
                                            class="form-control form-control-sm"
                                            value="<?= esc($kr['BobotNilai'] ?? '') ?>" placeholder="<?= esc($setting['BobotNilaiDefault']) ?>">
                                    </td>
                                    <td>
                                        <input type="number" min="0" max="100" name="BobotHadir"
                                            class="form-control form-control-sm"
                                            value="<?= esc($kr['BobotHadir'] ?? '') ?>" placeholder="<?= esc($setting['BobotHadirDefault']) ?>">
                                    </td>
                                    <td>
                                        <input type="number" min="0" max="50" name="JumlahPemenang"
                                            class="form-control form-control-sm"
                                            value="<?= esc($kr['JumlahPemenang'] ?? '') ?>" placeholder="<?= esc($setting['JumlahPemenangDefault']) ?>">
                                    </td>
                                    <td class="text-center align-middle">
                                        <input type="checkbox" name="GroupByJenjang" value="1"
                                            <?= ($kr['GroupByJenjang'] ?? $setting['IsGroupByJenjang']) ? 'checked' : '' ?>>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="submit" class="btn btn-primary btn-xs">
                                            <i class="fas fa-save"></i> Simpan
                                        </button>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
