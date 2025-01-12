<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="row mb-2">

                <div class="col-sm-12 float-sm-left">
                    <button class="btn btn-primary" data-toggle="modal"
                        data-target="#GuruKelasNew"><i class="fas fa-edit"></i>Tambah Pengaturan Guru</button>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="tabelGuruKelas" class="table table-bordered table-striped">
                <thead>
                    <?php
                    $helpModel = new \App\Models\HelpFunctionModel();
                    ?>
                    <?= $headerfooter = '
                    <tr>
                        <th>No</th>
                        <th>Nama Kelas</th>
                        <th>Nama Guru</th>
                        <th>Tahun Ajaran</th>
                        <th>Posisi</th>
                        <th>Aksi</th>
                    </tr>'
                    ?>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($guruKelas as $row) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row->NamaKelas ?></td>
                            <td><?= $row->Nama ?></td>
                            <td><?= $helpModel->convertTahunAjaran($row->IdTahunAjaran) ?></td>
                            <td><?= $row->NamaJabatan ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-toggle="modal"
                                    data-target="#GuruKelasEdit<?= $row->Id  ?>"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-danger btn-sm"
                                    onclick="deleteDataGuruKelas('<?= base_url('backend/GuruKelas/delete/' . $row->Id) ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <?= $headerfooter ?>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>

<!-- Modal Edit Data-->
<?php foreach ($guruKelas as $row) : ?>
    <div class="modal fade" id="GuruKelasEdit<?= $row->Id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content ">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formEdit<?= $row->Id ?>" action="<?= base_url('backend/GuruKelas/store') ?>" method="POST">
                        <div class="form-group">
                            <input type="hidden" name="Id" id="FormGuruKelas" value="<?= $row->Id ?>">
                            <input type="hidden" name="IdTpq" id="FormGuruKelas" value="<?= $row->IdTpq ?>">
                            <input type="hidden" name="IdTahunAjaran" id="FormGuruKelas" value="<?= $row->IdTahunAjaran ?>">
                        </div>
                        <div class="form-group">
                            <label for="FormGuruKelas">Nama Kelas</label>
                            <select name="IdKelas" class="form-control" id="FormGuruKelas">
                                <option value="" disabled selected>Pilih Nama Kelas</option>
                                <?php foreach ($helpModel->getDataKelas() as $kelas): ?>
                                    <option value="<?= $kelas['IdKelas']; ?>" <?= $row->IdKelas == $kelas['IdKelas'] ? 'selected' : ''; ?>>
                                        <?= $kelas['NamaKelas']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="FormGuruKelas">Nama Guru</label>
                            <select name="IdGuru" class="form-control" id="FormGuruKelas">
                                <option value="" disabled selected>Pilih Nama Guru</option>
                                <?php
                                foreach ($helpModel->getDataGuru($row->IdTpq) as $guru): ?>
                                    <option value="<?= $guru['IdGuru']; ?>" <?= $row->IdGuru == $guru['IdGuru'] ? 'selected' : ''; ?>>
                                        <?= $guru['Nama']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="FormGuruKelas">Posisi</label>
                            <select name="IdJabatan" class="form-control" id="FormGuruKelas">
                                <option value="" disabled selected>Pilih Sebagai</option>
                                <?php
                                foreach ($helpModel->getDataJabatan() as $Jabatan): ?>
                                    <option value="<?= $Jabatan['IdJabatan'] ?>" <?= $row->IdJabatan == $Jabatan['IdJabatan'] ? 'selected' : '' ?>>
                                        <?= $Jabatan['NamaJabatan'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="saveDataGuruKelas(this)"><i class="fas fa-save"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach ?>

<div class="modal fade" id="GuruKelasNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content ">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formNew" action="<?= base_url('backend/GuruKelas/store') ?>" method="POST">
                    <div class="form-group">
                        <input type="hidden" name="IdTpq" id="FormGuruKelas" value="<?= $dataTpq ?>">
                    </div>
                    <div class="form-group">
                        <label for="FormGuruKelas">Tahun Ajaran</label>
                        <select name="IdTahunAjaran" class="form-control" id="FormGuruKelas">
                            <option value="" disabled selected>Pilih Tahun Ajaran</option>
                            <option value="<?= $helpModel->getTahunAjaranSaatIni() ?>">Saat ini <?= $helpModel->convertTahunAjaran($helpModel->getTahunAjaranSaatIni()) ?> </option>
                            <option value="<?= $helpModel->getTahunAjaranSebelumnya($helpModel->getTahunAjaranSaatIni()) ?>">Sebelumnya <?= $helpModel->convertTahunAjaran($helpModel->getTahunAjaranSebelumnya($helpModel->getTahunAjaranSaatIni())) ?> </option>
                            <option value="<?= $helpModel->getTahuanAjaranBerikutnya($helpModel->getTahunAjaranSaatIni()) ?>">Berikutnya <?= $helpModel->convertTahunAjaran($helpModel->getTahuanAjaranBerikutnya($helpModel->getTahunAjaranSaatIni())) ?> </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="FormGuruKelas">Nama Kelas</label>
                        <select name="IdKelas" class="form-control" id="FormGuruKelas">
                            <option value="" disabled selected>Pilih Nama Kelas</option>
                            <?php foreach ($helpModel->getDataKelas() as $kelas): ?>
                                <option value="<?= $kelas['IdKelas']; ?>"><?= $kelas['NamaKelas']; ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="FormGuruKelas">Nama Guru</label>
                        <select name="IdGuru" class="form-control" id="FormGuruKelas">
                            <option value="" disabled selected>Pilih Nama Guru</option>
                            <?php
                            foreach ($helpModel->getDataGuru($dataTpq) as $guru): ?>
                                <option value="<?= $guru['IdGuru']; ?>"><?= $guru['Nama']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="FormGuruKelas">Posisi</label>
                        <select name="IdJabatan" class="form-control" id="FormGuruKelas">
                            <option value="" disabled selected>Pilih Sebagai</option>
                            <?php
                            foreach ($helpModel->getDataJabatan() as $Jabatan): ?>
                                <option value="<?= $Jabatan['IdJabatan'] ?>"><?= $Jabatan['NamaJabatan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="saveDataGuruKelas(this)"><i class="fas fa-save"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
//section scripts
<?= $this->section('scripts'); ?>
<script>
    initializeDataTableUmum("#tabelGuruKelas", true, true);

    function saveDataGuruKelas(button) {
        // Dapatkan form terdekat dari tombol yang diklik
        const form = button.closest('form');
        // Dapatkan modal terdekat dari form
        const modal = form.closest('.modal');

        // Tampilkan loading
        Swal.fire({
            title: 'Mohon Tunggu',
            html: 'Sedang memproses data...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil disimpan',
                        showConfirmButton: true,
                        timer: 2000
                    }).then(() => {
                        $(modal).modal('hide');
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan saat menyimpan data'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan pada server'
                });
                console.error('Error:', error);
            });
    }

    // Fungsi untuk hapus data
    function deleteDataGuruKelas(url) {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Mohon Tunggu',
                    html: 'Sedang menghapus data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(url, {
                        method: 'GET'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message || 'Data berhasil dihapus',
                                showConfirmButton: true,
                                timer: 2000
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Terjadi kesalahan saat menghapus data'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan pada server'
                        });
                        console.error('Error:', error);
                    });
            }
        });
    }
</script>
<?= $this->endSection(); ?>