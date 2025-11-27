<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Informasi Proses Flow -->
            <div class="card card-info card-outline collapsed-card mb-3">
                <div class="card-header bg-info">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Informasi Proses Data Materi Pelajaran
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <h5><i class="fas fa-list-ol"></i> Cara Menggunakan Halaman Data Materi Pelajaran:</h5>
                            <ol class="mb-3">
                                <li class="mb-2">
                                    <strong>Memahami Tampilan Halaman</strong>
                                    <ul class="mt-1">
                                        <li>Halaman ini menampilkan <strong>daftar semua materi pelajaran</strong> yang tersedia di sistem</li>
                                        <li>Tabel menampilkan informasi: Materi (TPQ/FKPQ), ID Materi, Nama Materi, Kategori, dan Aksi</li>
                                        <li>Materi yang berasal dari <strong>FKPQ</strong> (pusat) tidak bisa diubah atau dihapus</li>
                                        <li>Materi yang berasal dari <strong>TPQ</strong> Anda bisa diubah atau dihapus</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    <strong>Menambah Materi Pelajaran Baru</strong>
                                    <ul class="mt-1">
                                        <li>Klik tombol <strong>"Tambah Materi"</strong> di bagian atas halaman</li>
                                        <li>Pilih <strong>Kategori</strong> dari dropdown (misalnya: Al-Quran, Aqidah, dll)</li>
                                        <li>ID Materi akan <strong>otomatis terisi</strong> setelah memilih kategori</li>
                                        <li>Masukkan <strong>Nama Materi</strong> yang ingin ditambahkan</li>
                                        <li>Klik <strong>"Simpan"</strong> untuk menyimpan materi baru</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    <strong>Mengubah Materi Pelajaran</strong>
                                    <ul class="mt-1">
                                        <li>Klik tombol <strong>"Edit"</strong> (ikon pensil) pada baris materi yang ingin diubah</li>
                                        <li>Hanya materi dari <strong>TPQ Anda</strong> yang bisa diubah</li>
                                        <li>Kategori dan ID Materi <strong>tidak bisa diubah</strong> (sudah terkunci)</li>
                                        <li>Anda hanya bisa mengubah <strong>Nama Materi</strong></li>
                                        <li>Klik <strong>"Update"</strong> untuk menyimpan perubahan</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    <strong>Menghapus Materi Pelajaran</strong>
                                    <ul class="mt-1">
                                        <li>Klik tombol <strong>"Hapus"</strong> (ikon tempat sampah) pada baris materi yang ingin dihapus</li>
                                        <li>Hanya materi dari <strong>TPQ Anda</strong> yang bisa dihapus</li>
                                        <li>Sistem akan meminta <strong>konfirmasi</strong> sebelum menghapus</li>
                                        <li>Pastikan materi tidak sedang digunakan di kelas sebelum menghapus</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    <strong>Mengatur Materi ke Kelas</strong>
                                    <ul class="mt-1">
                                        <li>Klik tombol <strong>"Daftar Materi Kelas"</strong> untuk mengatur materi ke kelas tertentu</li>
                                        <li>Di halaman tersebut, Anda bisa menentukan materi mana yang digunakan di setiap kelas</li>
                                        <li>Anda juga bisa mengatur urutan materi dan semester (Ganjil/Genap)</li>
                                    </ul>
                                </li>
                            </ol>

                            <div class="alert alert-warning mb-0">
                                <h5><i class="icon fas fa-exclamation-triangle"></i> Catatan Penting:</h5>
                                <ul class="mb-0">
                                    <li>Materi dari <strong>FKPQ</strong> adalah materi standar yang tidak bisa diubah atau dihapus</li>
                                    <li>Materi dari <strong>TPQ</strong> adalah materi khusus TPQ Anda yang bisa dikelola</li>
                                    <li>Pastikan <strong>ID Materi</strong> unik untuk setiap kategori</li>
                                    <li>Sebelum menghapus materi, pastikan materi tersebut <strong>tidak sedang digunakan</strong> di kelas manapun</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah">
                        <i class="fas fa-plus"></i> Tambah Materi
                    </button>
                    <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo base_url('backend/kelasMateriPelajaran/showMateriKelas') ?>';">
                        <i class="fas fa-list"></i> Daftar Materi Kelas
                    </button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="tblMateri" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Materi</th>
                                <th>ID Materi</th>
                                <th>Nama Materi</th>
                                <th>Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($materiPelajaran as $row): ?>
                                <tr>
                                    <td><?= !empty($row['IdTpq']) ? "TPQ " . $row['NamaTpq'] : "FKPQ"; ?></td>
                                    <td><?= $row['IdMateri']; ?></td>
                                    <td><?= $row['NamaMateri']; ?></td>
                                    <td><?= $row['Kategori']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                            data-target="#modal-edit<?= $row['Id']; ?>"
                                            <?= empty($row['IdTpq']) ? 'disabled' : '' ?>>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="<?= !empty($row['IdTpq']) ? "confirmDelete('" . $row['Id'] . "')" : 'void(0)' ?>"
                                            <?= empty($row['IdTpq']) ? 'disabled' : '' ?>>
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
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title">Tambah Materi Pelajaran</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('backend/materiPelajaran/store') ?>" method="post" id="formTambahMateri">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kategori</label>
                        <select class="form-control" name="Kategori" id="kategori" required onchange="getLastIdMateri()">
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($kategoriPelajaran as $kat): ?>
                                <option value="<?= $kat['Kategori'] ?>"><?= $kat['Kategori'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ID Materi</label>
                        <input type="text" class="form-control" name="IdMateri" id="idMateri" readonly required>
                    </div>
                    <div class="form-group">
                        <label>Nama Materi</label>
                        <input type="text" class="form-control" name="NamaMateri" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" onclick="showLoadingOnSubmit(event)">Simpan</button>
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
                <div class="modal-header bg-warning text-white">
                    <h4 class="modal-title">Edit Materi Pelajaran</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?= base_url('backend/materiPelajaran/update/' . $row['Id']) ?>" method="post" id="formEditMateri<?= $row['Id']; ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Kategori</label>
                            <select class="form-control" name="Kategori" readonly disabled>
                                <?php foreach ($kategoriPelajaran as $kat): ?>
                                    <option value="<?= $kat['Kategori'] ?>" <?= ($row['Kategori'] == $kat['Kategori']) ? 'selected' : ''; ?>>
                                        <?= $kat['Kategori'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <!-- Tambahkan hidden input untuk menyimpan nilai -->
                            <input type="hidden" name="Kategori" value="<?= $row['Kategori'] ?>">
                        </div>
                        <div class="form-group">
                            <label>ID Materi</label>
                            <input type="text" class="form-control" name="IdMateri" value="<?= $row['IdMateri']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Nama Materi</label>
                            <input type="text" class="form-control" name="NamaMateri" value="<?= $row['NamaMateri']; ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" onclick="showLoadingOnUpdate(event, <?= $row['Id']; ?>)">Update</button>
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
    initializeDataTableUmum("#tblMateri", true, true);

    // Create Delete fungsi
    function confirmDelete(id) {
        // Dapatkan informasi materi dari baris tabel
        const row = event.target.closest('tr');
        const idMateri = row.querySelector('td:nth-child(2)').textContent;
        const namaMateri = row.querySelector('td:nth-child(3)').textContent;

        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: `Data materi ID: <strong>${idMateri}</strong> Nama: <strong>${namaMateri}</strong> akan dihapus permanen!`,
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
                fetch('<?= base_url('backend/materiPelajaran/delete/') ?>' + id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
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


    function getLastIdMateri() {
        const kategori = document.getElementById('kategori').value;
        if (kategori) {
            fetch('<?= base_url('backend/materiPelajaran/getLastIdMateri') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?= csrf_header() ?>': '<?= csrf_hash() ?>' // CSRF protection
                    },
                    body: JSON.stringify({
                        kategori: kategori
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('idMateri').value = data.nextId;
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengambil ID Materi',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        } else {
            document.getElementById('idMateri').value = '';
        }
    }

    function showLoadingOnSubmit(event) {
        event.preventDefault();

        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Ambil form
        const form = document.getElementById('formTambahMateri');

        // Kirim form menggunakan fetch
        fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message || 'Data berhasil disimpan',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan saat menyimpan data');
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

    function showLoadingOnUpdate(event, id) {
        event.preventDefault();

        Swal.fire({
            title: 'Memperbarui...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Ambil form
        const form = document.getElementById('formEditMateri' + id);

        // Kirim form menggunakan fetch
        fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message || 'Data berhasil diperbarui',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan saat memperbarui data');
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
</script>
<?= $this->endSection(); ?>