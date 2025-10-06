<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Profil Data Santri</h3>
        </div>
        <div class="card-body">
            <table id="tblProfilSantri" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>IdSantri</th>
                        <th>Nama</th>
                        <th>Kelurahan/Desa</th>
                        <th>TPQ</th>
                        <th>Kelas</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataSantri as $santri) : ?>
                        <tr>
                            <td>
                                <a href="<?= base_url('backend/santri/profilDetailSantri/' . $santri['IdSantri']); ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i><span class="d-none d-md-inline">&nbsp;Lihat Profil</span>
                                </a>
                            </td>
                            <td><?= $santri['IdSantri']; ?></td>
                            <td><?= ucwords(strtolower($santri['NamaSantri'])); ?></td>
                            <td><?= ucwords(strtolower($santri['KelurahanDesa'])); ?></td>
                            <td><?= preg_replace_callback('/\b(al|el|ad|ar|at|an)-(\w+)/i', function ($matches) { return ucfirst(strtolower($matches[1])) . '-' . ucfirst($matches[2]); }, ucwords(strtolower($santri['NamaTpq']))); ?></td>
                            <td><?= $santri['NamaKelas']; ?></td>
                            <td>
                                <?php if ($santri['Status'] == "Belum Diverifikasi"): ?>
                                    <span class="badge bg-warning"><?= $santri['Status']; ?></span>
                                <?php elseif ($santri['Status'] == "Perlu Perbaikan"): ?>
                                    <span class="badge bg-danger"><?= $santri['Status']; ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?= $santri['Status']; ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>IdSantri</th>
                        <th>Nama</th>
                        <th>Kelurahan/Desa</th>
                        <th>TPQ</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    initializeDataTableWithFilter("#tblProfilSantri", true);
</script>
<?= $this->endSection(); ?>


