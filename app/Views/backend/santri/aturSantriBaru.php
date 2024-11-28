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
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Profil</th>
                        <th>Aksi</th>
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
                            <td>
                                <a href="<?= base_url('backend/santri/viewDetailSantriBaru/' . $santri['IdSantri']); ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i>&nbsp;Detail</a>
                                <a href="<?= base_url('backend/santri/editSantriBaru/' . $santri['IdSantri']); ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i>&nbsp;Edit</a>
                                <a href="javascript:void(0)" onclick="deleteSantri('<?= $santri['IdSantri']; ?>')" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>&nbsp;Hapus
                                </a>
                            </td>
                            <td><?= $santri['IdSantri']; ?></td>
                            <td><?= ucwords(strtolower($santri['NamaSantri'])); ?></td>
                            <td><?= ucwords(strtolower($santri['KelurahanDesa'])); ?></td>
                            <!-- Mengubah format nama TPQ -->
                            <!-- detail keterangan:
                            \b(al|el|ad)-(\w+) = kata yang diawali dengan al- atau el- atau ad- dan diikuti dengan kata lain
                            \b = kata awal
                            (al|el|ad) = al atau el atau ad
                            - = tanda hubung
                            (\w+) = kata setelah tanda hubung -->
                            <td><?= preg_replace_callback('/\b(al|el|ad)-(\w+)/i', function ($matches) {
                                    return ucfirst(strtolower($matches[1])) . '-' . ucfirst($matches[2]);
                                }, ucwords(strtolower($santri['NamaTpq']))); ?></td>
                            <td><?= $santri['NamaKelas']; ?></td>
                            <td>
                                <?php if ($santri['status'] == "Belum Diverifikasi"): ?>
                                    <span class="badge bg-warning"><?= $santri['status']; ?></span>
                                <?php else: ?>
                                    <?php if ($santri['status'] == "Perlu Perbaikan"): ?>
                                        <span class="badge bg-danger"><?= $santri['status']; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= $santri['status']; ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d-m-Y H:i:s', strtotime($santri['updated_at'])); ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Profil</th>
                        <th>Aksi</th>
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
<?= $this->section('scripts'); ?>
<script>
    function deleteSantri(IdSantri) {
        // Dapatkan nama santri dari baris tabel
        const row = event.target.closest('tr');
        const namaSantri = row.querySelector('td:nth-child(4)').textContent;

        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: `Data santri ID: <strong>${IdSantri}</strong> Nama: <strong>${namaSantri}</strong> akan dihapus permanen!`,
            icon: 'question',
            iconColor: '#d33',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('<?= base_url('backend/santri/deleteSantriBaru/') ?>' + IdSantri, {
                        method: 'DELETE'
                    })
                    .then(response => {
                        if (response.ok) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Data santri berhasil dihapus.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            throw new Error('Gagal menghapus data santri.');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Gagal!',
                            text: error.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
            }
        });
    }
</script>
<?= $this->endSection(); ?>