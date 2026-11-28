<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="card shadow-sm card-outline card-success">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <div>
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-users text-success mr-2"></i>Preview Kandidat Penerima: [<?= esc($jenis['Kode']) ?>] <?= esc($jenis['NamaPenghargaan']) ?>
                </h3>
                <div class="text-muted text-sm mt-1">
                    TA: <strong><?= esc($idTahunAjaran) ?></strong> &middot;
                    Semester: <strong><?= esc($semester) ?></strong> &middot;
                    Scope: <strong><?= esc(ucfirst($scope)) ?></strong>
                    <?= $namaJenjang ? ' (' . esc($namaJenjang) . ')' : '' ?>
                </div>
            </div>
            <a href="<?= base_url('backend/penghargaan/generate') ?>" class="btn btn-secondary btn-sm ml-auto">
                <i class="fas fa-arrow-left mr-1"></i> Ubah Filter
            </a>
        </div>

        <form action="<?= base_url('backend/penghargaan/generate/store') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="IdJenis" value="<?= esc($jenis['Id']) ?>">
            <input type="hidden" name="IdTahunAjaran" value="<?= esc($idTahunAjaran) ?>">
            <input type="hidden" name="Semester" value="<?= esc($semester) ?>">
            <input type="hidden" name="Momen" value="<?= esc($momen) ?>">

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0 text-sm">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 45px;">
                                    <input type="checkbox" id="checkAll" checked>
                                </th>
                                <th class="text-center" style="width: 75px;">Rank</th>
                                <th>Nama Santri</th>
                                <th>Kelas</th>
                                <th class="text-right">Nilai Rata-Rata</th>
                                <th class="text-right">% Hadir</th>
                                <th class="text-right">Skor Gabungan</th>
                                <th>Catatan Khusus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($kandidat)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="fas fa-search fa-2x mb-2 d-block"></i>
                                        Tidak ada kandidat santri aktif yang memenuhi kriteria pada periode dan scope ini.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($kandidat as $idx => $row): ?>
                                    <tr>
                                        <td class="text-center align-middle">
                                            <input type="checkbox" class="row-check" name="selected[]" value="<?= $idx ?>" checked>
                                            <input type="hidden" name="rows[<?= $idx ?>][IdSantri]" value="<?= esc($row['IdSantri']) ?>">
                                            <input type="hidden" name="rows[<?= $idx ?>][IdKelas]" value="<?= esc($row['IdKelas']) ?>">
                                            <input type="hidden" name="rows[<?= $idx ?>][Peringkat]" value="<?= esc($row['Peringkat']) ?>">
                                            <input type="hidden" name="rows[<?= $idx ?>][NilaiRataRata]" value="<?= esc($row['NilaiRataRata']) ?>">
                                            <input type="hidden" name="rows[<?= $idx ?>][PersenKehadiran]" value="<?= esc($row['PersenKehadiran']) ?>">
                                            <input type="hidden" name="rows[<?= $idx ?>][SkorGabungan]" value="<?= esc($row['SkorGabungan']) ?>">
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-<?= $row['Peringkat'] <= 3 ? 'warning' : 'secondary' ?> px-2 py-1">
                                                #<?= esc($row['Peringkat']) ?>
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <strong><?= esc($row['NamaSantri']) ?></strong>
                                            <?php if (!empty($row['NIS'])): ?>
                                                <small class="text-muted d-block">NIS: <?= esc($row['NIS']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle"><?= esc($row['NamaKelas']) ?></td>
                                        <td class="text-right align-middle font-weight-bold"><?= number_format((float) $row['NilaiRataRata'], 2) ?></td>
                                        <td class="text-right align-middle"><?= number_format((float) $row['PersenKehadiran'], 2) ?>%</td>
                                        <td class="text-right align-middle text-primary font-weight-bold"><?= number_format((float) $row['SkorGabungan'], 2) ?></td>
                                        <td class="align-middle">
                                            <input type="text" name="rows[<?= $idx ?>][CatatanKhusus]" class="form-control form-control-sm" value="<?= esc($row['CatatanKhusus']) ?>" placeholder="Opsional...">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if (!empty($kandidat)): ?>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap">
                    <span class="text-muted text-sm mb-2 mb-md-0">
                        Ditemukan <strong><?= count($kandidat) ?></strong> kandidat penerima. Data akan disimpan dengan status <strong>Draft</strong>.
                    </span>
                    <div class="form-inline">
                        <label class="mr-2 text-sm font-weight-bold">Tanggal Sertifikat:</label>
                        <input type="date" name="TanggalSertifikat" class="form-control form-control-sm mr-2" value="<?= date('Y-m-d') ?>" required>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-check-circle mr-1"></i> Konfirmasi &amp; Simpan Draft
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkAll');
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            document.querySelectorAll('.row-check').forEach(cb => cb.checked = checkAll.checked);
        });
    }
});
</script>

<?= $this->endSection(); ?>
