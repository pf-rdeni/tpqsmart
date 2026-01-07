<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-sitemap"></i> Perlombaan - <?= esc($lomba['NamaLomba']) ?>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-secondary mr-1" onclick="history.back()" title="Kembali ke halaman sebelumnya">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </button>
                            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAddCabang">
                                <i class="fas fa-plus"></i> Tambah Perlombaan
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tableCabang">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Perlombaan</th>
                                        <th>Kategori</th>
                                        <th>Tipe</th>
                                        <th>Batasan</th>
                                        <th>Kriteria</th>
                                        <th>Peserta</th>
                                        <th>Juri</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($cabang_list)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center">Belum ada perlombaan</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($cabang_list as $i => $cabang): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td><?= esc($cabang['NamaCabang']) ?></td>
                                                <td><span class="badge badge-info"><?= $cabang['Kategori'] ?></span></td>
                                                <td><span class="badge badge-secondary"><?= $cabang['Tipe'] ?></span></td>
                                                <td>
                                                    <?php if ((!empty($cabang['KelasMin']) && $cabang['KelasMin'] != 0) || (!empty($cabang['KelasMax']) && $cabang['KelasMax'] != 0)): ?>
                                                        <span class="badge badge-primary">Kelas</span><br>
                                                        <small><?= esc($cabang['NamaKelasMin'] ?? '?') ?> - <?= esc($cabang['NamaKelasMax'] ?? '?') ?></small>
                                                    <?php else: ?>
                                                        <span class="badge badge-info">Usia</span><br>
                                                        <small><?= $cabang['UsiaMin'] ?> - <?= $cabang['UsiaMax'] ?> Thn</small>
                                                    <?php endif; ?>
                                                    <br>
                                                    <small class="text-secondary">
                                                        <i class="fas fa-users"></i> Max: <?= $cabang['MaxPeserta'] == 0 ? 'Unlimited' : $cabang['MaxPeserta'] ?>
                                                    </small>
                                                </td>
                                                <td><?= $cabang['total_kriteria'] ?? 0 ?></td>
                                                <td><?= $cabang['total_peserta'] ?? 0 ?></td>
                                                <td><?= $cabang['total_juri'] ?? 0 ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?= base_url('backend/perlombaan/setKriteria/' . $cabang['id']) ?>" class="btn btn-info" title="Kriteria">
                                                            <i class="fas fa-list-ol"></i>
                                                        </a>
                                                        <a href="<?= base_url('backend/perlombaan/setJuri/' . $cabang['id']) ?>" class="btn btn-success" title="Juri">
                                                            <i class="fas fa-user-tie"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-warning btn-edit-cabang" 
                                                                data-id="<?= $cabang['id'] ?>"
                                                                data-nama="<?= esc($cabang['NamaCabang']) ?>"
                                                                data-kategori="<?= $cabang['Kategori'] ?>"
                                                                data-tipe="<?= $cabang['Tipe'] ?>"
                                                                data-usiamin="<?= $cabang['UsiaMin'] ?>"
                                                                data-usiamax="<?= $cabang['UsiaMax'] ?>"
                                                                data-kelasmin="<?= $cabang['KelasMin'] ?>"
                                                                data-kelasmax="<?= $cabang['KelasMax'] ?>"
                                                                data-maxpeserta="<?= $cabang['MaxPeserta'] ?>"
                                                                data-maxpertpq="<?= $cabang['MaxPerTpq'] ?? 0 ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-delete-cabang" data-id="<?= $cabang['id'] ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Add Cabang -->
<div class="modal fade" id="modalAddCabang" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Tambah Perlombaan</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formAddCabang">
                <div class="modal-body">
                    <input type="hidden" name="lomba_id" value="<?= $lomba['id'] ?>">
                    <div class="form-group">
                        <label>Nama Perlombaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="NamaCabang" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select class="form-control" name="Kategori">
                                    <option value="Campuran">Campuran</option>
                                    <option value="Putra">Putra</option>
                                    <option value="Putri">Putri</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipe</label>
                                <select class="form-control" name="Tipe">
                                    <option value="Individu">Individu</option>
                                    <option value="Kelompok">Kelompok</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Batasan Toggle -->
                    <div class="form-group">
                        <label>Batasan Peserta</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input radio-batasan" type="radio" name="Batasan" id="batasanUsiaAdd" value="Usia" checked>
                            <label class="form-check-label" for="batasanUsiaAdd">Range Usia</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input radio-batasan" type="radio" name="Batasan" id="batasanKelasAdd" value="Kelas">
                            <label class="form-check-label" for="batasanKelasAdd">Range Kelas</label>
                        </div>
                    </div>

                    <!-- Div Usia -->
                    <div class="div-usia">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Usia Min (Tahun)</label>
                                    <input type="number" class="form-control" name="UsiaMin" value="5">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Usia Max (Tahun)</label>
                                    <input type="number" class="form-control" name="UsiaMax" value="18">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Div Kelas -->
                    <div class="div-kelas" style="display:none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kelas Min</label>
                                    <select class="form-control select-kelas" name="KelasMin">
                                        <option value="">-- Pilih --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kelas Max</label>
                                    <select class="form-control select-kelas" name="KelasMax">
                                        <option value="">-- Pilih --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Max Peserta (0 = unlimited)</label>
                        <input type="number" class="form-control" name="MaxPeserta" value="0">
                    </div>
                    <div class="form-group">
                        <label>Max Per TPQ <small class="text-muted">(0 = unlimited)</small></label>
                        <input type="number" class="form-control" name="MaxPerTpq" value="0">
                        <small class="form-text text-muted">Batas peserta/kelompok yang bisa didaftarkan oleh setiap TPQ</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Cabang -->
<div class="modal fade" id="modalEditCabang" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Edit Perlombaan</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formEditCabang">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editCabangId">
                    <div class="form-group">
                        <label>Nama Perlombaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="NamaCabang" id="editNamaCabang" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select class="form-control" name="Kategori" id="editKategori">
                                    <option value="Campuran">Campuran</option>
                                    <option value="Putra">Putra</option>
                                    <option value="Putri">Putri</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipe</label>
                                <select class="form-control" name="Tipe" id="editTipe">
                                    <option value="Individu">Individu</option>
                                    <option value="Kelompok">Kelompok</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Batasan Toggle Edit -->
                    <div class="form-group">
                        <label>Batasan Peserta</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input radio-batasan-edit" type="radio" name="Batasan" id="batasanUsiaEdit" value="Usia">
                            <label class="form-check-label" for="batasanUsiaEdit">Range Usia</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input radio-batasan-edit" type="radio" name="Batasan" id="batasanKelasEdit" value="Kelas">
                            <label class="form-check-label" for="batasanKelasEdit">Range Kelas</label>
                        </div>
                    </div>

                    <!-- Div Usia Edit -->
                    <div id="divUsiaEdit">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Usia Min</label>
                                    <input type="number" class="form-control" name="UsiaMin" id="editUsiaMin">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Usia Max</label>
                                    <input type="number" class="form-control" name="UsiaMax" id="editUsiaMax">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Div Kelas Edit -->
                    <div id="divKelasEdit" style="display:none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kelas Min</label>
                                    <select class="form-control select-kelas" name="KelasMin" id="editKelasMin">
                                        <option value="">-- Pilih --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kelas Max</label>
                                    <select class="form-control select-kelas" name="KelasMax" id="editKelasMax">
                                        <option value="">-- Pilih --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Max Peserta</label>
                        <input type="number" class="form-control" name="MaxPeserta" id="editMaxPeserta">
                    </div>
                    <div class="form-group">
                        <label>Max Per TPQ <small class="text-muted">(0 = unlimited)</small></label>
                        <input type="number" class="form-control" name="MaxPerTpq" id="editMaxPerTpq">
                        <small class="form-text text-muted">Batas peserta/kelompok per TPQ</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Add cabang
    $('#formAddCabang').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '<?= base_url('backend/perlombaan/storeCabang') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            }
        });
    });

    // Load Kelas List
    $.get('<?= base_url('backend/perlombaan/getKelasList') ?>', function(response) {
        if(response.success) {
           var options = '<option value="">-- Pilih --</option>';
           response.data.forEach(function(k) {
               options += '<option value="'+k.IdKelas+'">'+k.NamaKelas+'</option>';
           });
           $('.select-kelas').html(options);
        }
    });

    // Toggle Batasan Add
    $('.radio-batasan').change(function() {
        if($(this).val() == 'Kelas') {
            $('.div-usia').hide();
            $('.div-kelas').show();
        } else {
            $('.div-usia').show();
            $('.div-kelas').hide();
        }
    });

    // Toggle Batasan Edit
    $('.radio-batasan-edit').change(function() {
        if($(this).val() == 'Kelas') {
            $('#divUsiaEdit').hide();
            $('#divKelasEdit').show();
        } else {
            $('#divUsiaEdit').show();
            $('#divKelasEdit').hide();
        }
    });

    // Edit cabang - populate modal
    $('.btn-edit-cabang').click(function() {
        var kMin = $(this).data('kelasmin');
        var kMax = $(this).data('kelasmax');
        var isKelas = (kMin != '' && kMin != 0 && kMin != null); // Check if KelasMin is set

        $('#editCabangId').val($(this).data('id'));
        $('#editNamaCabang').val($(this).data('nama'));
        $('#editKategori').val($(this).data('kategori'));
        $('#editTipe').val($(this).data('tipe'));
        $('#editUsiaMin').val($(this).data('usiamin'));
        $('#editUsiaMax').val($(this).data('usiamax'));
        $('#editKelasMin').val(kMin);
        $('#editKelasMax').val(kMax);
        $('#editMaxPeserta').val($(this).data('maxpeserta'));
        $('#editMaxPerTpq').val($(this).data('maxpertpq'));

        // Set Radio State
        if (isKelas) {
            $('#batasanKelasEdit').prop('checked', true).trigger('change');
        } else {
            $('#batasanUsiaEdit').prop('checked', true).trigger('change');
        }

        $('#modalEditCabang').modal('show');
    });

    // Update cabang
    $('#formEditCabang').submit(function(e) {
        e.preventDefault();
        var id = $('#editCabangId').val();
        $.ajax({
            url: '<?= base_url('backend/perlombaan/updateCabang') ?>/' + id,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                }
            }
        });
    });

    // Delete cabang
    $('.btn-delete-cabang').click(function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus perlombaan ini?',
            text: 'Data kriteria, peserta, dan nilai juga akan terhapus!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('backend/perlombaan/deleteCabang') ?>/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
