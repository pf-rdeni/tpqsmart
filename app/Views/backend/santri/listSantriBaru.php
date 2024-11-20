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
            <h5>Data Pendaftaran Santri Baru</h5>
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Profil</th>
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
                            <td>
                                <?php
                        $uploadPath = (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/' : base_url('uploads/santri/');
                                ?>
                                <img src="<?= $santri['PhotoProfil'] ? $uploadPath . $santri['PhotoProfil'] : base_url('images/no-photo.jpg'); ?>"
                                    alt="PhotoProfil"
                                    class="img-fluid popup-image"
                                    width="30"
                                    height="40"
                                    onmouseover="showPopup(this)"
                                    onmouseout="hidePopup(this)"
                                    onclick="showPopup(this)"
                                    style="cursor: pointer;">
                                <div class="image-popup" style="display: none; position: absolute; z-index: 1000;">
                                    <img src="<?= $santri['PhotoProfil'] ? $uploadPath . $santri['PhotoProfil'] : base_url('images/no-photo.jpg'); ?>"
                                        alt="PhotoProfil"
                                        width="200"
                                        height="250">
                                </div>
                                <script>
                                    function showPopup(img) {
                                        const popup = img.nextElementSibling;
                                        popup.style.display = 'block';
                                    }

                                    function hidePopup(img) {
                                        const popup = img.nextElementSibling;
                                        popup.style.display = 'none';
                                    }
                                </script>
                            </td>
                            <td><?= $santri['IdSantri']; ?></td>
                            <td><?= $santri['NamaSantri']; ?></td>
                            <td><?= $santri['KelurahanDesaSantri']; ?></td>
                            <td><?= $santri['NamaTpq']; ?></td>
                            <td><?= $santri['NamaKelas']; ?></td>
                            <td>
                                <?php if ($santri['Status'] == "BELUM DIVERIFIKASI"): ?>
                                    <span class="badge bg-warning"><?= $santri['Status']; ?></span>
                                <?php else: ?>
                                    <?php if ($santri['Status'] == "PERLU PERBAIKAN"): ?>
                                        <span class="badge bg-danger"><?= $santri['Status']; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= $santri['Status']; ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td><?= $santri['updated_at']; ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Profil</th>
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
        <!-- /.card-body -->
        <div class="card-body">
            <h5>Data Santri Baru berdasarkan TPQ</h5>
            <table id="example3" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama TPQ</th>
                        <th>Alamat TPQ</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($dataTpq as $tpq): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $tpq['NamaTpq']; ?></td>
                            <td><?= $tpq['Alamat']; ?></td>
                            <td>
                                <a href="<?= base_url('backend/santri/showSantriBaruPerKelasTpq/' . $tpq['IdTpq']); ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Nama TPQ</th>
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