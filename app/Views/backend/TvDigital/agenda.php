<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Daftar Agenda Kegiatan</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalAgenda" id="btnTambahAgenda">
                        <i class="fas fa-plus"></i> Tambah Agenda
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($agenda)): ?>
                    <div class="alert alert-info text-center mb-0">
                        <i class="fas fa-info-circle"></i> Belum ada agenda kegiatan yang terdaftar. Klik <strong>"Tambah Agenda"</strong> untuk membuat agenda baru.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="tabelAgenda">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Waktu Pelaksanaan</th>
                                    <th>Tempat</th>
                                    <th>Keterangan</th>
                                    <th style="width: 80px;" class="text-center">Status</th>
                                    <th style="width: 150px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($agenda as $item): ?>
                                    <tr id="agenda-row-<?= $item['Id'] ?>">
                                        <td><?= $no++ ?></td>
                                        <td class="font-weight-bold"><?= esc($item['NamaKegiatan']) ?></td>
                                        <td>
                                            <i class="far fa-calendar-alt"></i> 
                                            <?= date('d M Y', strtotime($item['TanggalMulai'])) ?>
                                            <?php if (!empty($item['TanggalSelesai']) && $item['TanggalSelesai'] != $item['TanggalMulai']): ?>
                                                s/d <?= date('d M Y', strtotime($item['TanggalSelesai'])) ?>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($item['JamMulai'])): ?>
                                                <br>
                                                <i class="far fa-clock"></i> 
                                                <?= substr($item['JamMulai'], 0, 5) ?>
                                                <?php if (!empty($item['JamSelesai'])): ?>
                                                    - <?= substr($item['JamSelesai'], 0, 5) ?>
                                                <?php endif; ?> WIB
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($item['Tempat']) ?: '-' ?></td>
                                        <td><?= esc($item['Keterangan']) ?: '-' ?></td>
                                        <td class="text-center">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input switch-status-agenda" 
                                                       id="switch-agenda-<?= $item['Id'] ?>" 
                                                       data-id="<?= $item['Id'] ?>"
                                                       <?= $item['IsActive'] ? 'checked' : '' ?>>
                                                <label class="custom-control-label" for="switch-agenda-<?= $item['Id'] ?>"></label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-warning btn-sm btn-edit-agenda" 
                                                    data-id="<?= $item['Id'] ?>"
                                                    data-nama="<?= esc($item['NamaKegiatan']) ?>"
                                                    data-tglmulai="<?= $item['TanggalMulai'] ?>"
                                                    data-tglselesai="<?= $item['TanggalSelesai'] ?>"
                                                    data-jammulai="<?= $item['JamMulai'] ?>"
                                                    data-jamselesai="<?= $item['JamSelesai'] ?>"
                                                    data-tempat="<?= esc($item['Tempat']) ?>"
                                                    data-ket="<?= esc($item['Keterangan']) ?>"
                                                    title="Edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm btn-delete-agenda" data-id="<?= $item['Id'] ?>" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Modal Agenda (Tambah & Edit) -->
<div class="modal fade" id="modalAgenda" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formAgenda">
                <input type="hidden" name="Id" id="agendaId">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"><i class="fas fa-plus"></i> Tambah Agenda Kegiatan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kegiatan / Acara</label>
                        <input type="text" class="form-control" name="NamaKegiatan" id="agendaNama" required placeholder="Contoh: Isra' Mi'raj TPQ Smart">
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" class="form-control" name="TanggalMulai" id="agendaTglMulai" required value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Tanggal Selesai (Opsional)</label>
                                <input type="date" class="form-control" name="TanggalSelesai" id="agendaTglSelesai">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Jam Mulai (Opsional)</label>
                                <input type="time" class="form-control" name="JamMulai" id="agendaJamMulai">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Jam Selesai (Opsional)</label>
                                <input type="time" class="form-control" name="JamSelesai" id="agendaJamSelesai">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Tempat / Lokasi</label>
                        <input type="text" class="form-control" name="Tempat" id="agendaTempat" placeholder="Contoh: Masjid Al-Ikhlas">
                    </div>
                    <div class="form-group">
                        <label>Keterangan / Informasi Tambahan</label>
                        <textarea class="form-control" name="Keterangan" id="agendaKet" rows="3" placeholder="Informasi detail kegiatan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveAgenda">Simpan Agenda</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Reset modal saat tambah
    $('#btnTambahAgenda').click(function() {
        $('#agendaId').val('');
        $('#formAgenda')[0].reset();
        $('#modalTitle').html('<i class="fas fa-plus"></i> Tambah Agenda Kegiatan');
        $('#agendaTglMulai').val('<?= date('Y-m-d') ?>');
    });

    // Edit Handler
    $('.btn-edit-agenda').click(function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var tglmulai = $(this).data('tglmulai');
        var tglselesai = $(this).data('tglselesai');
        var jammulai = $(this).data('jammulai');
        var jamselesai = $(this).data('jamselesai');
        var tempat = $(this).data('tempat');
        var ket = $(this).data('ket');

        $('#agendaId').val(id);
        $('#agendaNama').val(nama);
        $('#agendaTglMulai').val(tglmulai);
        $('#agendaTglSelesai').val(tglselesai);
        $('#agendaJamMulai').val(jammulai);
        $('#agendaJamSelesai').val(jamselesai);
        $('#agendaTempat').val(tempat);
        $('#agendaKet').val(ket);

        $('#modalTitle').html('<i class="fas fa-edit"></i> Edit Agenda Kegiatan');
        $('#modalAgenda').modal('show');
    });

    // Form Submit (Tambah / Edit)
    $('#formAgenda').submit(function(e) {
        e.preventDefault();
        var id = $('#agendaId').val();
        var url = id === '' ? '<?= base_url('backend/tv-digital/save-agenda') ?>' : '<?= base_url('backend/tv-digital/update-agenda') ?>/' + id;
        
        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    $('#modalAgenda').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
            }
        });
    });

    // Toggle Status Switch
    $('.switch-status-agenda').change(function() {
        var id = $(this).data('id');
        var isActive = $(this).is(':checked') ? 1 : 0;
        
        $.ajax({
            url: '<?= base_url('backend/tv-digital/update-agenda') ?>/' + id,
            type: 'POST',
            data: { 
                IsActive: isActive,
                // Resend existing parameters to satisfy any validation or update constraints
                NamaKegiatan: $('#agenda-row-' + id + ' td:nth-child(2)').text(),
                TanggalMulai: '' // Will use model default or existing db info inside controller
            },
            success: function(response) {
                if (response.status !== 'success') {
                    Swal.fire('Error', 'Gagal memperbarui status.', 'error');
                }
            }
        });
    });

    // Delete Agenda
    $('.btn-delete-agenda').click(function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Agenda?',
            text: "Agenda kegiatan ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('backend/tv-digital/delete-agenda') ?>/' + id,
                    type: 'POST',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function() {
                                $('#agenda-row-' + id).fadeOut(500, function() {
                                    $(this).remove();
                                });
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
