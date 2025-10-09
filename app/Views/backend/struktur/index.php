<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="row mb-2">
                <div class="col-sm-12 float-sm-left">
                    <button class="btn btn-primary" data-toggle="modal"
                        data-target="#StrukturLembagaNew"><i class="fas fa-edit"></i>Tambah Struktur Lembaga</button>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="tabelStrukturLembaga" class="table table-bordered table-striped">
                <thead>
                    <?php
                    $helpModel = new \App\Models\HelpFunctionModel();
                    ?>
                    <?= $headerfooter = '
                    <tr>
                        <th>No</th>
                        <th>Nama TPQ</th>
                        <th>Nama Guru</th>
                        <th>Jabatan</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>'
                    ?>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($strukturLembaga as $row) : 
                        $status = 'Aktif';
                        $statusClass = 'badge-success';
                        if ($row->TanggalAkhir && strtotime($row->TanggalAkhir) < time()) {
                            $status = 'Tidak Aktif';
                            $statusClass = 'badge-danger';
                        }
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row->NamaTpq ?></td>
                            <td><?= $row->NamaGuru ?></td>
                            <td><?= $row->NamaJabatan ?></td>
                            <td><?= date('d/m/Y', strtotime($row->TanggalStart)) ?></td>
                            <td><?= $row->TanggalAkhir ? date('d/m/Y', strtotime($row->TanggalAkhir)) : '-' ?></td>
                            <td><span class="badge <?= $statusClass ?>"><?= $status ?></span></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-toggle="modal"
                                    data-target="#StrukturLembagaEdit<?= $row->Id ?>"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-danger btn-sm"
                                    onclick="deleteDataStrukturLembaga('<?= base_url('backend/strukturlembaga/delete/' . $row->Id) ?>')">
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
<?php foreach ($strukturLembaga as $row) : ?>
    <div class="modal fade" id="StrukturLembagaEdit<?= $row->Id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content ">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formEdit<?= $row->Id ?>" action="<?= base_url('backend/strukturlembaga/store') ?>" method="POST">
                        <div class="form-group">
                            <input type="hidden" name="Id" value="<?= $row->Id ?>">
                            <input type="hidden" name="IdTpq" value="<?= $row->IdTpq ?>">
                        </div>
                        <div class="form-group">
                            <label for="IdGuru">Nama Guru</label>
                            <select name="IdGuru" class="form-control" required>
                                <option value="" disabled selected>Pilih Nama Guru</option>
                                <?php foreach ($guru as $g): ?>
                                    <option value="<?= $g['IdGuru']; ?>" <?= $row->IdGuru == $g['IdGuru'] ? 'selected' : ''; ?>>
                                        <?= $g['Nama']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="IdJabatan">Jabatan</label>
                            <select name="IdJabatan" class="form-control" required>
                                <option value="" disabled selected>Pilih Jabatan</option>
                                <?php foreach ($jabatan as $j): ?>
                                    <option value="<?= $j['IdJabatan'] ?>" <?= $row->IdJabatan == $j['IdJabatan'] ? 'selected' : '' ?>>
                                        <?= $j['NamaJabatan'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="TanggalStart">Tanggal Mulai</label>
                            <input type="date" name="TanggalStart" class="form-control" value="<?= $row->TanggalStart ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="TanggalAkhir">Tanggal Selesai (Opsional)</label>
                            <input type="date" name="TanggalAkhir" class="form-control" value="<?= $row->TanggalAkhir ?>">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="saveDataStrukturLembaga(this)"><i class="fas fa-save"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach ?>

<div class="modal fade" id="StrukturLembagaNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content ">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formNew" action="<?= base_url('backend/strukturlembaga/store') ?>" method="POST">
                    <div class="form-group">
                        <input type="hidden" name="IdTpq" value="<?= $dataTpq ?>">
                    </div>
                    <div class="form-group">
                        <label for="IdGuru">Nama Guru</label>
                        <select name="IdGuru" class="form-control" required>
                            <option value="" disabled selected>Pilih Nama Guru</option>
                            <?php foreach ($guru as $g): ?>
                                <option value="<?= $g['IdGuru']; ?>"><?= $g['Nama']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="IdJabatan">Jabatan</label>
                        <select name="IdJabatan" class="form-control" required>
                            <option value="" disabled selected>Pilih Jabatan</option>
                            <?php foreach ($jabatan as $j): ?>
                                <option value="<?= $j['IdJabatan'] ?>"><?= $j['NamaJabatan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="TanggalStart">Tanggal Mulai</label>
                        <input type="date" name="TanggalStart" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="TanggalAkhir">Tanggal Selesai (Opsional)</label>
                        <input type="date" name="TanggalAkhir" class="form-control">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="saveDataStrukturLembaga(this)"><i class="fas fa-save"></i>Simpan</button>
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
    initializeDataTableUmum("#tabelStrukturLembaga", true, true);

    function saveDataStrukturLembaga(button) {
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
    function deleteDataStrukturLembaga(url) {
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
