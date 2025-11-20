<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="col-12">
    <?php echo session()->getFlashdata('pesan'); 
    ?>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-2 col-6">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ProfilMdaModalInput">
                        <i class="fas fa-plus"></i> Tambah Data MDA
                    </button>
                </div>

                <div class="col-lg-6 col-6">
                    <h3 class="card-title">Lembaga Pendidikan MDA yang ada di Kecamatan Seri Kuala Lobam</h3>
                </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID TPQ</th>
                        <th>ID MDA</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Nama Kepala</th>
                        <th>Tempat Belajar</th>
                        <th>Tahun Berdiri</th>
                        <th>No Telpon/Hp</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($mda as $dataMda) : ?>
                        <tr>
                            <td><?= $dataMda['IdTpq'] ?></td>
                            <td><?= $dataMda['IdMda'] ?? '-' ?></td>
                            <td><?= $dataMda['NamaTpq']  ?></td>
                            <td><?= $dataMda['Alamat']  ?></td>
                            <td><?= $dataMda['KepalaSekolah']  ?></td>
                            <td><?= $dataMda['TempatBelajar']  ?></td>
                            <td><?= $dataMda['TahunBerdiri']  ?></td>
                            <td><?= $dataMda['NoHp']  ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#ProfilMdaModalEdit<?= $dataMda['IdTpq']  ?>"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#ProfilMdaModalDelete<?= $dataMda['IdTpq']  ?>"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>ID TPQ</th>
                        <th>ID MDA</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Nama Kepala</th>
                        <th>Tempat Belajar</th>
                        <th>Tahun Berdiri</th>
                        <th>No Telpon/Hp</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>

<!-- Modal Edit Data-->
<?php foreach ($mda as $dataMda) : ?>
    <div class="modal fade" id="ProfilMdaModalEdit<?= $dataMda['IdTpq'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content ">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data MDA </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('/backend/mda/update/'. $dataMda['IdTpq']) ?>" method="POST">
                        <div class="form-group">
                            <label for="FormProfilMda">ID TPQ</label>
                            <input type="number" name="IdTpq" class="form-control" id="FormProfilMda" maxlength="20" min="1" required placeholder="Ketik ID TPQ" value="<?= $dataMda['IdTpq'] ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="FormProfilMda">ID MDA</label>
                            <input type="text" name="IdMda" class="form-control" id="FormProfilMda" placeholder="Ketik ID MDA" value="<?= $dataMda['IdMda'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="FormProfilMda">Nama MDA</label>
                            <input type="text" name="NamaTpq" class="form-control" id="FormProfilMda" placeholder="Ketik Nama MDA" value="<?= $dataMda['NamaTpq'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Kelurahan/Desa</label>
                            <select class="form-control select2" style="width: 100%;" name="AlamatTpq" id="AlamatTpqEdit<?= $dataMda['IdTpq'] ?>">
                                <option value="">--Pilih--</option>
                                <option value="Tanjung Permai" <?= ($dataMda['Alamat'] == 'Tanjung Permai') ? 'selected' : '' ?>>Tanjung Permai</option>
                                <option value="Kuala Sempang" <?= ($dataMda['Alamat'] == 'Kuala Sempang') ? 'selected' : '' ?>>Kuala Sempang</option>
                                <option value="Busung" <?= ($dataMda['Alamat'] == 'Busung') ? 'selected' : '' ?>>Busung</option>
                                <option value="Teluk Sasah" <?= ($dataMda['Alamat'] == 'Teluk Sasah') ? 'selected' : '' ?>>Teluk Sasah</option>
                                <option value="Teluk Lobam" <?= ($dataMda['Alamat'] == 'Teluk Lobam') ? 'selected' : '' ?>>Teluk Lobam</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="FormProfilMda">Nama Kep. MDA</label>
                            <input type="text" name="NamaKepTpq" class="form-control" id="FormProfilMda" placeholder="Ketik Nama Kepala MDA" value="<?= $dataMda['KepalaSekolah'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="FormProfilMda">No Hp</label>
                            <input type="text" name="NoHp" class="form-control" id="FormProfilMda" placeholder="Ketik No Handphone" value="<?= $dataMda['NoHp'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="FormProfilMda">Tempat Belajar</label>
                            <input type="text" name="TempatBelajar" class="form-control" id="FormProfilMda" placeholder="Ketik Tempat Belajar" value="<?= $dataMda['TempatBelajar'] ?>">
                        </div>
                        <div class="form-group">
                            <label>Tanggal Beridiri:</label>
                            <div class="input-group date" id="DateForEdit<?= $dataMda['IdTpq'] ?>" data-target-input="nearest">
                                <input type="text" name="TanggalBerdiri" class="form-control datetimepicker-input" data-target="#DateForEdit<?= $dataMda['IdTpq'] ?>" placeholder="Tekan Icon Tanggal" value="<?= $dataMda['TahunBerdiri'] ?>" />
                                <div class="input-group-append" data-target="#DateForEdit<?= $dataMda['IdTpq'] ?>" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Delete Data-->
    <div class="modal fade" id="ProfilMdaModalDelete<?= $dataMda['IdTpq'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Data MDA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin akan menghapus data MDA berikut?</p>
                    <ul>
                        <li><strong>ID TPQ:</strong> <?= $dataMda['IdTpq'] ?></li>
                        <li><strong>Nama MDA:</strong> <?= $dataMda['NamaTpq'] ?></li>
                        <li><strong>Alamat:</strong> <?= $dataMda['Alamat'] ?></li>
                    </ul>
                    <p class="text-danger"><strong>Perhatian:</strong> Tindakan ini tidak dapat dibatalkan!</p>
                </div>
                <div class="modal-footer">
                    <form action="<?= base_url('/backend/mda/delete/' . $dataMda['IdTpq']) ?>" method="POST">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Ya, Hapus</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach ?>

<!-- Modal Input Data-->
<div class="modal fade" id="ProfilMdaModalInput" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content ">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data MDA Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('/backend/mda/save') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="FormProfilMdaIdTpq">ID TPQ <span class="text-danger">*</span></label>
                        <select class="form-control select2" style="width: 100%;" name="IdTpq" id="FormProfilMdaIdTpq" required>
                            <option value="">--Pilih TPQ--</option>
                            <?php if (!empty($listTpq)): ?>
                                <?php foreach ($listTpq as $tpq): ?>
                                    <option value="<?= $tpq['IdTpq'] ?>"><?= $tpq['IdTpq'] ?> - <?= $tpq['NamaTpq'] ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Tidak ada TPQ yang tersedia</option>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted">Pilih TPQ yang akan digunakan untuk MDA. Hanya TPQ yang belum memiliki MDA yang ditampilkan.</small>
                    </div>
                    <div class="form-group">
                        <label for="FormProfilMda">ID MDA</label>
                        <input type="text" name="IdMda" class="form-control" id="FormProfilMda" placeholder="Ketik ID MDA">
                    </div>
                    <div class="form-group">
                        <label for="FormProfilMda">Nama MDA <span class="text-danger">*</span></label>
                        <input type="text" name="NamaTpq" class="form-control" id="FormProfilMda" placeholder="Ketik Nama MDA" required>
                    </div>
                    <div class="form-group">
                        <label>Kelurahan/Desa</label>
                        <select class="form-control select2" style="width: 100%;" name="AlamatTpq">
                            <option value="">--Pilih--</option>
                            <option value="Tanjung Permai">Tanjung Permai</option>
                            <option value="Kuala Sempang">Kuala Sempang</option>
                            <option value="Busung">Busung</option>
                            <option value="Teluk Sasah">Teluk Sasah</option>
                            <option value="Teluk Lobam">Teluk Lobam</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="FormProfilMda">Nama Kep. MDA</label>
                        <input type="text" name="NamaKepTpq" class="form-control" id="FormProfilMda" placeholder="Ketik Nama Kepala MDA">
                    </div>
                    <div class="form-group">
                        <label for="FormProfilMda">No Hp</label>
                        <input type="text" name="NoHp" class="form-control" id="FormProfilMda" placeholder="Ketik No Handphone">
                    </div>
                    <div class="form-group">
                        <label for="FormProfilMda">Tempat Belajar</label>
                        <input type="text" name="TempatBelajar" class="form-control" id="FormProfilMda" placeholder="Ketik Tempat Belajar">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Beridiri:</label>
                        <div class="input-group date" id="DateForInput" data-target-input="nearest">
                            <input type="text" name="TanggalBerdiri" class="form-control datetimepicker-input" data-target="#DateForInput" placeholder="Tekan Icon Tanggal" />
                            <div class="input-group-append" data-target="#DateForInput" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                        <button type="reset" class="btn btn-warning"><i class="fas fa-redo"></i> Reset</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#example1').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "order": [[0, "asc"]]
    });

    // Initialize Select2 for IdTpq dropdown
    $('#FormProfilMdaIdTpq').select2({
        theme: 'bootstrap4',
        placeholder: '--Pilih TPQ--',
        allowClear: true
    });

    // Initialize Select2 for Alamat dropdown
    $('select[name="AlamatTpq"]').select2({
        theme: 'bootstrap4',
        placeholder: '--Pilih--',
        allowClear: true
    });

    // Re-initialize Select2 when modal is opened
    $('#ProfilMdaModalInput').on('shown.bs.modal', function () {
        $('#FormProfilMdaIdTpq').select2({
            theme: 'bootstrap4',
            placeholder: '--Pilih TPQ--',
            allowClear: true,
            dropdownParent: $('#ProfilMdaModalInput')
        });
        $('select[name="AlamatTpq"]').select2({
            theme: 'bootstrap4',
            placeholder: '--Pilih--',
            allowClear: true,
            dropdownParent: $('#ProfilMdaModalInput')
        });
    });

    // Initialize datetimepicker for add modal
    $('#DateForInput').datetimepicker({
        format: 'YYYY-MM-DD',
        icons: {
            time: 'far fa-clock',
            date: 'far fa-calendar',
            up: 'fas fa-arrow-up',
            down: 'fas fa-arrow-down',
            previous: 'fas fa-chevron-left',
            next: 'fas fa-chevron-right',
            today: 'far fa-calendar-check',
            clear: 'far fa-trash-alt',
            close: 'far fa-times-circle'
        }
    });

    // Initialize datetimepicker and select2 for each edit modal
    <?php foreach ($mda as $dataMda) : ?>
    $('#DateForEdit<?= $dataMda['IdTpq'] ?>').datetimepicker({
        format: 'YYYY-MM-DD',
        icons: {
            time: 'far fa-clock',
            date: 'far fa-calendar',
            up: 'fas fa-arrow-up',
            down: 'fas fa-arrow-down',
            previous: 'fas fa-chevron-left',
            next: 'fas fa-chevron-right',
            today: 'far fa-calendar-check',
            clear: 'far fa-trash-alt',
            close: 'far fa-times-circle'
        }
    });

    // Initialize Select2 for Alamat dropdown in edit modal
    $('#ProfilMdaModalEdit<?= $dataMda['IdTpq'] ?>').on('shown.bs.modal', function () {
        $('#AlamatTpqEdit<?= $dataMda['IdTpq'] ?>').select2({
            theme: 'bootstrap4',
            placeholder: '--Pilih--',
            allowClear: true,
            dropdownParent: $('#ProfilMdaModalEdit<?= $dataMda['IdTpq'] ?>')
        });
    });
    <?php endforeach ?>
});
</script>

<?= $this->endSection(); ?>

