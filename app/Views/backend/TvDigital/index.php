<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-link"></i> Daftar Link Display TV Digital</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalTambahLink">
                        <i class="fas fa-plus"></i> Buat Link Baru
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($links)): ?>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> Belum ada link display TV Digital. Klik tombol <strong>"Buat Link Baru"</strong> untuk memulai.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($links as $link): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card card-secondary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title font-weight-bold">
                                            <span class="text-primary text-lg mr-2"><i class="fas fa-tv"></i></span>
                                            <?= esc($link['NamaLink']) ?>
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-danger btn-xs btn-delete-link" data-id="<?= $link['Id'] ?>" title="Hapus Link">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Link Display TV:</label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm" readonly id="link-url-<?= $link['Id'] ?>" value="<?= base_url('tv/' . $link['HashKey']) ?>">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-info btn-sm btn-copy-link" data-id="<?= $link['Id'] ?>" type="button" title="Salin Link">
                                                            <i class="fas fa-copy"></i> Copy
                                                        </button>
                                                        <a href="<?= base_url('backend/tv-digital/preview/' . $link['Id']) ?>" target="_blank" class="btn btn-primary btn-sm" title="Buka di TV">
                                                            <i class="fas fa-external-link-alt"></i> Buka
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>

                                        <form class="form-config-link" data-id="<?= $link['Id'] ?>">
                                            <input type="hidden" name="IdInfografisLink" value="<?= $link['Id'] ?>">
                                            
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="text-sm">Tahun Ajaran</label>
                                                        <select class="form-control form-control-sm" name="IdTahunAjaran">
                                                            <?php foreach ($idTahunAjaranList as $ta): ?>
                                                                <option value="<?= $ta ?>" <?= ($ta == $link['IdTahunAjaran']) ? 'selected' : '' ?>>
                                                                    <?= convertTahunAjaran($ta) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="text-sm">Slideshow Interval (detik)</label>
                                                        <input type="number" min="5" max="300" class="form-control form-control-sm" name="SlideshowInterval" value="<?= $link['SlideshowInterval'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label class="text-sm">Refresh Data (menit)</label>
                                                        <input type="number" min="1" max="60" class="form-control form-control-sm" name="RefreshInterval" value="<?= $link['RefreshInterval'] ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group mb-2">
                                                <label class="text-sm d-block">Urutan & Aktifitas Tampilan Block Card:</label>
                                                <div class="list-group sortable-blocks" id="sortable-<?= $link['Id'] ?>">
                                                    <?php foreach ($link['blocks'] as $block): ?>
                                                        <div class="list-group-item d-flex justify-content-between align-items-center p-2 mb-1 border rounded block-item" data-key="<?= $block['BlockKey'] ?>" style="cursor: move;">
                                                            <div>
                                                                <span class="mr-2 text-muted"><i class="fas fa-bars"></i></span>
                                                                <span class="font-weight-500"><?= esc($blockLabels[$block['BlockKey']] ?? $block['BlockKey']) ?></span>
                                                            </div>
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input block-switch" 
                                                                       id="switch-<?= $link['Id'] ?>-<?= $block['BlockKey'] ?>" 
                                                                       data-key="<?= $block['BlockKey'] ?>"
                                                                       <?= $block['IsActive'] ? 'checked' : '' ?>>
                                                                <label class="custom-control-label" style="cursor: pointer;" for="switch-<?= $link['Id'] ?>-<?= $block['BlockKey'] ?>"></label>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between mt-3">
                                                <div class="text-muted text-xs">
                                                    <i class="fas fa-qrcode text-lg" style="cursor: pointer;" data-toggle="popover" data-trigger="hover" data-html="true" data-content="<div class='text-center'><img src='https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode(base_url('tv/' . $link['HashKey'])) ?>'><br/><small>Scan QR untuk buka di TV / Hp</small></div>"></i> QR Code
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-save"></i> Simpan Konfigurasi
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Modal Tambah Link -->
<div class="modal fade" id="modalTambahLink" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formTambahLink">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Buat Link TV Digital Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama / Label Link</label>
                        <input type="text" class="form-control" name="NamaLink" required placeholder="Contoh: TV Lobby, Ruang Guru, dll" value="TV Digital Display">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Buat Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- jQuery UI untuk Drag and Drop Sortable -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(document).ready(function() {
    // Inisialisasi Popover untuk QR Code
    $('[data-toggle="popover"]').popover();

    // Inisialisasi Sortable Drag and Drop
    $('.sortable-blocks').sortable({
        handle: '.fa-bars',
        placeholder: 'ui-state-highlight mb-1 border border-primary rounded',
        forcePlaceholderSize: true,
        update: function(event, ui) {
            // Urutan telah diubah, form-config will pick up the new order during submit
        }
    });

    // Copy Link Clipboard
    $('.btn-copy-link').click(function() {
        var id = $(this).data('id');
        var copyText = document.getElementById("link-url-" + id);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Link TV Digital berhasil disalin ke clipboard.',
            timer: 1500,
            showConfirmButton: false
        });
    });

    // Submit form tambah link
    $('#formTambahLink').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        
        $.ajax({
            url: '<?= base_url('backend/tv-digital/create-link') ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
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

    // Submit form konfigurasi link
    $('.form-config-link').submit(function(e) {
        e.preventDefault();
        var linkId = $(this).data('id');
        var idTahunAjaran = $(this).find('select[name="IdTahunAjaran"]').val();
        var slideshowInterval = $(this).find('input[name="SlideshowInterval"]').val();
        var refreshInterval = $(this).find('input[name="RefreshInterval"]').val();
        
        // Update general link info
        $.ajax({
            url: '<?= base_url('backend/tv-digital/update-link') ?>/' + linkId,
            type: 'POST',
            data: {
                IdTahunAjaran: idTahunAjaran,
                SlideshowInterval: slideshowInterval,
                RefreshInterval: refreshInterval
            }
        });

        // Collect blocks order & active status
        var blocks = [];
        var sortOrder = 1;
        $('#sortable-' + linkId + ' .block-item').each(function() {
            var blockKey = $(this).data('key');
            var isActive = $(this).find('.block-switch').is(':checked') ? 1 : 0;
            blocks.push({
                BlockKey: blockKey,
                IsActive: isActive,
                SortOrder: sortOrder
            });
            sortOrder++;
        });

        // Save block configurations
        $.ajax({
            url: '<?= base_url('backend/tv-digital/save-config') ?>',
            type: 'POST',
            data: {
                IdInfografisLink: linkId,
                blocks: blocks
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Konfigurasi berhasil disimpan.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal menyimpan konfigurasi.', 'error');
            }
        });
    });

    // Hapus Link
    $('.btn-delete-link').click(function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Link TV?',
            text: "Konfigurasi link display ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('backend/tv-digital/delete-link') ?>/' + id,
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
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
