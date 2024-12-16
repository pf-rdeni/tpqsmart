<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah">
                        <i class="fas fa-plus"></i> Tambah Materi
                    </button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="tblMateri" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Materi</th>
                                <th>Nama Materi</th>
                                <th>Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($materiPelajaran as $row): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $row['IdMateri']; ?></td>
                                    <td><?= $row['NamaMateri']; ?></td>
                                    <td><?= $row['Kategori']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                            data-target="#modal-edit<?= $row['Id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('<?= $row['Id']; ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
                <h4 class="modal-title">Tambah Materi Pelajaran</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('backend/materi/save') ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>ID Materi</label>
                        <input type="text" class="form-control" name="IdMateri" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Materi</label>
                        <input type="text" class="form-control" name="NamaMateri" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select class="form-control" name="Kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Aqidah">Aqidah</option>
                            <option value="Fiqih">Fiqih</option>
                            <option value="Al-Quran">Al-Quran</option>
                            <option value="Hadits">Hadits</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<?php foreach ($materiPelajaran as $row): ?>
    <div class="modal fade" id="modal-edit<?= $row['Id']; ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Materi Pelajaran</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?= base_url('backend/materi/update/' . $row['Id']) ?>" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>ID Materi</label>
                            <input type="text" class="form-control" name="IdMateri" value="<?= $row['IdMateri']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Materi</label>
                            <input type="text" class="form-control" name="NamaMateri" value="<?= $row['NamaMateri']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori</label>
                            <select class="form-control" name="Kategori" required>
                                <option value="Aqidah" <?= ($row['Kategori'] == 'Aqidah') ? 'selected' : ''; ?>>Aqidah</option>
                                <option value="Fiqih" <?= ($row['Kategori'] == 'Fiqih') ? 'selected' : ''; ?>>Fiqih</option>
                                <option value="Al-Quran" <?= ($row['Kategori'] == 'Al-Quran') ? 'selected' : ''; ?>>Al-Quran
                                </option>
                                <option value="Hadits" <?= ($row['Kategori'] == 'Hadits') ? 'selected' : ''; ?>>Hadits</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>


<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    // Initialize DataTable for #tblMateri
    initializeDataTableUmum("#tblMateri", true);

    // Create Delete fungsi
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Gunakan fetch untuk melakukan request delete
                fetch(`${BASE_URL}/backend/materi/delete/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Tampilkan pesan sukses
                            Swal.fire({
                                title: 'Terhapus!',
                                text: 'Data berhasil dihapus.',
                                icon: 'success',
                                timer: 2000, // Tunggu 2 detik
                                showConfirmButton: false
                            }).then(() => {
                                // Refresh halaman setelah timer selesai
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: data.message || 'Terjadi kesalahan saat menghapus data.',
                                icon: 'error',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan pada server.',
                            icon: 'error',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    });
            }
        });
    }
</script>
<?= $this->endSection(); ?>