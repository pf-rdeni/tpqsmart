<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> Data Nilai yang Sudah Diinput
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-light">
                                <?= esc($juri_data['NamaLomba']) ?> - <?= esc($juri_data['NamaCabang']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm" id="tableNilai">
                                <thead>
                                    <tr class="bg-secondary">
                                        <th class="align-middle text-center">No</th>
                                        <th class="align-middle text-center">No Peserta</th>
                                        <th class="align-middle">Nama Santri</th>
                                        <?php foreach ($kriteria_list as $k): ?>
                                            <th class="text-center"><?= esc($k['NamaKriteria']) ?></th>
                                        <?php endforeach; ?>
                                        <th class="align-middle text-center bg-success text-white">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($peserta_scored)): ?>
                                        <tr>
                                            <td colspan="<?= 4 + count($kriteria_list) ?>" class="text-center">Belum ada nilai yang diinput</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($peserta_scored as $i => $peserta): ?>
                                            <tr>
                                                <td class="text-center"><?= $i + 1 ?></td>
                                                <td><code><?= esc($peserta['NoPeserta']) ?></code></td>
                                                <td><?= esc($peserta['NamaSantri']) ?></td>
                                                <?php 
                                                $totalNilai = 0;
                                                foreach ($kriteria_list as $k): 
                                                    $nilai = isset($peserta['nilai'][$k['id']]) ? $peserta['nilai'][$k['id']] : 0;
                                                    $totalNilai += $nilai;
                                                ?>
                                                    <td class="text-center"><strong><?= number_format($nilai, 2) ?></strong></td>
                                                <?php endforeach; ?>
                                                <td class="text-center bg-success text-white"><strong><?= number_format($totalNilai, 2) ?></strong></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="<?= base_url('backend/perlombaan/dashboardLombaJuri') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                        </a>
                        <a href="<?= base_url('backend/perlombaan/inputNilaiJuri') ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Input Nilai Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    $('#tableNilai').DataTable({
        "order": [[<?= 3 + count($kriteria_list) ?>, "desc"]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
        }
    });
});
</script>
<?= $this->endSection(); ?>
