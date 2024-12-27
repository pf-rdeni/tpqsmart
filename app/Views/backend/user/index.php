<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah">
                        <i class="fas fa-plus"></i> Tambah User
                    </button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="tblUser" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Nama</th>
                                <th>UserNama</th>
                                <th>TPQ</th>
                                <th>KelurahanDesa</th>
                                <th>Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userData as $user): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" <?= $user['active'] == 1 ? 'checked' : ''; ?>>
                                    </td>
                                    <td><?= $user['Nama']; ?></td>
                                    <td><?= $user['username']; ?></td>
                                    <td><?= $user['NamaTpq']; ?></td>
                                    <td><?= $user['KelurahanDesa']; ?></td>
                                    <td><?= $user['kategori']; ?></td>
                                    <td>
                                        <button class="btn btn-warning sm-small" onclick="window.location.href='<?= site_url('user/edit/' . $user['id']); ?>'"><i class="fas fa-edit"></i> Edit</button>
                                        <button class="btn btn-danger sm-small" onclick="confirmDelete(<?= $user['id']; ?>)"><i class="fas fa-trash"></i> Hapus</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Tambah -->
<div class="modal fade" id="modal-tambah">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah User</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= site_url('user/create'); ?>" method="post">
                <div class="modal-body">
                    <div class="form-group  row">
                        <label for="username" class="col-sm-3 col-form-label">Username</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                    </div>
                    <div class="form-group
                    row">
                        <label for="password" class="col-sm-3 col-form-label">Password</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    // inisialisasi datatable
    initializeDataTableUmum("#tblUser", true);

    // fungsi delet menngunakan swal
    function confirmDelete(id) {
        const row = event.target.closest('tr');
        const nama = row.querySelector('td:nth-child(2)').innerText;
        const username = row.querySelector('td:nth-child(3)').innerText;
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            html: `Username : <strong>${username} - ${nama}</strong> akan dihapus permanen!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                fetch('<?= site_url('backend/user/delete/'); ?>' + id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message);
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