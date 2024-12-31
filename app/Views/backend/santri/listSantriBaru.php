<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar Santri TPQ Di Kecamatan Seri Kuala Lobam</h3>
                <a href="<?= base_url('backend/santri/createEmisStep') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i><span class="d-none d-md-inline">&nbsp;Daftar Santri Baru</span>
                </a>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <h5>Data Pendaftaran Santri Terbaru</h5>
            <table id="tblSantriBaru" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>IdSantri</th>
                        <th>Nama</th>
                        <th>Kelurahan/Desa</th>
                        <th>TPQ</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Tanggal Reg</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($dataSantri as $santri) : ?>
                        <tr>
                            <td><?= $santri['IdSantri']; ?></td>
                            <td><?= ucwords(strtolower($santri['NamaSantri'])); ?></td>
                            <td><?= ucwords(strtolower($santri['KelurahanDesa'])); ?></td>
                            <td><?= preg_replace_callback('/\b(al|el|ad|ar|at|an)-(\w+)/i', function ($matches) {
                                    return ucfirst(strtolower($matches[1])) . '-' . ucfirst($matches[2]);
                                }, ucwords(strtolower($santri['NamaTpq']))); ?></td>
                            <td><?= $santri['NamaKelas']; ?></td>
                            <td>
                                <?php if ($santri['Status'] == "Belum Diverifikasi"): ?>
                                    <span class="badge bg-warning"><?= $santri['Status']; ?></span>
                                <?php else: ?>
                                    <?php if ($santri['Status'] == "Perlu Perbaikan"): ?>
                                        <span class="badge bg-danger"><?= $santri['Status']; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= $santri['Status']; ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d-m-Y H:i:s', strtotime($santri['updated_at'])); ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>IdSantri</th>
                        <th>Nama</th>
                        <th>Kelurahan/Desa</th>
                        <th>TPQ</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Tanggal Reg</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer">
        </div>
        <!-- /.card-body -->
        <div class="card-header">
            <h5>Data Santri Baru berdasarkan TPQ</h5>
        </div>
        <div class="card-body">
            <table id="tblTpq" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nama TPQ</th>
                        <th>Jumlah Santri</th>
                        <th>Alamat TPQ</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataTpq as $tpq): ?>
                        <tr>
                            <td><?= $tpq['NamaTpq']; ?></td>
                            <td><?= $tpq['JumlahSantri']; ?></td>
                            <td><?= $tpq['Alamat']; ?></td>
                            <td>
                                <a href="<?= base_url('backend/santri/showSantriBaruPerKelasTpq/' . $tpq['IdTpq']); ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i><span class="d-none d-md-inline">&nbsp;Detail</span></a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Nama TPQ</th>
                        <th>Jumlah Santri</th>
                        <th>Alamat TPQ</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <!-- /.card -->
</div>
<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    // Initialize DataTable for #tblTpq
    initializeDataTableUmum("#tblTpq");
    initializeDataTableWithFilter("#tblSantriBaru", true, ["excel", "pdf", "print", "colvis"]);
</script>
<?= $this->endSection(); ?>