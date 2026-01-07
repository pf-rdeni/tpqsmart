<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <!-- Row 1: Pilih Kegiatan & Perlombaan -->
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-certificate"></i> Template Sertifikat</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kegiatan</label>
                                    <select class="form-control" id="selectLomba">
                                        <option value="">-- Pilih Kegiatan --</option>
                                        <?php foreach ($lomba_list as $l): ?>
                                            <?php 
                                                $tpqLabel = !empty($l['NamaTpq']) 
                                                    ? ' - TPQ ' . $l['NamaTpq'] . ' - ' . ($l['KelurahanDesa'] ?? '')
                                                    : ' - Umum';
                                            ?>
                                            <option value="<?= $l['id'] ?>" <?= ($lomba && $lomba['id'] == $l['id']) ? 'selected' : '' ?>>
                                                <?= esc($l['NamaLomba']) ?><?= $tpqLabel ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Perlombaan</label>
                                    <select class="form-control" id="selectCabang">
                                        <option value="">-- Pilih Perlombaan --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($cabang): ?>
        <!-- Row 2: Upload Template -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-upload"></i> Upload Template</h3>
                    </div>
                    <form id="formUploadTemplate" enctype="multipart/form-data">
                        <div class="card-body">
                            <!-- Panduan Pembuatan Template -->
                            <div class="card card-warning card-outline collapsed-card mb-3">
                                <div class="card-header py-2">
                                    <h6 class="card-title mb-0"><i class="fas fa-lightbulb text-warning"></i> Panduan Membuat Template</h6>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-3" style="display: none;">
                                    <h6><i class="fas fa-paint-brush text-info"></i> Rekomendasi: Buat Template di Canva</h6>
                                    <ol class="pl-3 mb-3">
                                        <li>Buka <a href="https://canva.com" target="_blank"><strong>canva.com</strong></a> dan buat desain baru</li>
                                        <li>Pilih ukuran <strong>1920 x 1080 px</strong> (landscape) atau <strong>1080 x 1920 px</strong> (portrait)</li>
                                        <li>Desain sertifikat dengan background, border, logo, dll</li>
                                        <li><strong>Jangan tambahkan teks dinamis</strong> (nama, peringkat, dll) - ini akan diatur di sistem</li>
                                        <li>Download sebagai <strong>PNG</strong> dengan kualitas tertinggi</li>
                                    </ol>
                                    <hr>
                                    <h6><i class="fas fa-check-circle text-success"></i> Tips Desain Template</h6>
                                    <ul class="pl-3 mb-0">
                                        <li><strong>Kosongkan area</strong> untuk: Nama peserta, peringkat, nama lomba, tanggal, tanda tangan</li>
                                        <li>Gunakan <strong>kontras warna</strong> yang baik agar teks terlihat jelas</li>
                                        <li>Hindari elemen dekoratif di area yang akan ditempati teks dinamis</li>
                                        <li>Untuk hasil terbaik, simpan dalam format <strong>PNG</strong> (bukan JPG)</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <input type="hidden" name="cabang_id" value="<?= $cabang['id'] ?>">
                            
                            <div class="form-group">
                                <label>Nama Template</label>
                                <input type="text" class="form-control" name="nama_template" placeholder="Template Sertifikat Lomba">
                            </div>

                            <div class="form-group">
                                <label>Orientasi</label>
                                <select class="form-control" name="orientation">
                                    <option value="landscape">Landscape (Horizontal)</option>
                                    <option value="portrait">Portrait (Vertical)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>File Template (JPG/PNG)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="templateFile" name="template_file" accept="image/jpeg,image/png" required>
                                    <label class="custom-file-label" for="templateFile">Pilih file...</label>
                                </div>
                                <small class="text-muted">Resolusi minimal 1920x1080px untuk kualitas terbaik</small>
                            </div>

                            <div id="imagePreview" class="mt-3" style="display:none;">
                                <img id="previewImg" src="" class="img-fluid" style="max-height: 300px;">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload"></i> Upload Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Current Template -->
            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-image"></i> Template Saat Ini</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($template): ?>
                            <div class="text-center">
                                <img src="<?= base_url('uploads/' . $template['FileTemplate']) ?>" 
                                     class="img-fluid mb-3" 
                                     style="max-height: 300px; border: 1px solid #ddd;">
                                <p><strong><?= esc($template['NamaTemplate']) ?></strong></p>
                                <p class="text-muted">
                                    <?= $template['Width'] ?> x <?= $template['Height'] ?>px 
                                    (<?= ucfirst($template['Orientation']) ?>)
                                </p>
                                <div class="btn-group">
                                    <a href="<?= base_url('backend/perlombaan/configure-fields/' . $template['id']) ?>" 
                                       class="btn btn-primary">
                                        <i class="fas fa-cog"></i> Konfigurasi Field
                                    </a>
                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalEditTemplate">
                                        <i class="fas fa-edit"></i> Ganti Gambar
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="deleteTemplate(<?= $template['id'] ?>)">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle"></i> Belum ada template. Silakan upload template terlebih dahulu.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Info Card -->
        <?php if (!$cabang): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Informasi</h5>
                    Pilih kegiatan dan perlombaan terlebih dahulu untuk mengelola template sertifikat.
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

<!-- Modal Edit/Ganti Template -->
<?php if ($template): ?>
<div class="modal fade" id="modalEditTemplate" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Ganti Gambar Template</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formUpdateTemplate" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="template_id" value="<?= $template['id'] ?>">
                    
                    <div class="form-group">
                        <label>Gambar Template Baru (JPG/PNG)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="updateTemplateFile" name="template_file" accept="image/jpeg,image/png" required>
                            <label class="custom-file-label" for="updateTemplateFile">Pilih file baru...</label>
                        </div>
                        <small class="text-muted">Resolusi minimal 1920x1080px untuk kualitas terbaik</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Orientasi</label>
                        <select class="form-control" name="orientation">
                            <option value="landscape" <?= ($template['Orientation'] ?? 'landscape') === 'landscape' ? 'selected' : '' ?>>Landscape (Horizontal)</option>
                            <option value="portrait" <?= ($template['Orientation'] ?? 'landscape') === 'portrait' ? 'selected' : '' ?>>Portrait (Vertical)</option>
                        </select>
                    </div>

                    <div id="updateImagePreview" class="mt-3" style="display:none;">
                        <label>Preview:</label>
                        <img id="updatePreviewImg" src="" class="img-fluid" style="max-height: 200px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
</section>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    <?php if ($cabang): ?>
    loadCabangByLomba(<?= $cabang['lomba_id'] ?>, <?= $cabang['id'] ?>);
    <?php endif; ?>

    $('#selectLomba').change(function() {
        var lombaId = $(this).val();
        if (lombaId) {
            loadCabangByLomba(lombaId);
        }
    });

    $('#selectCabang').change(function() {
        var cabangId = $(this).val();
        if (cabangId) {
            window.location.href = '<?= base_url('backend/perlombaan/template-sertifikat') ?>/' + cabangId;
        }
    });

    function loadCabangByLomba(lombaId, selectedId = null) {
        $.ajax({
            url: '<?= base_url('backend/perlombaan/getCabangByLomba') ?>/' + lombaId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var html = '<option value="">-- Pilih Perlombaan --</option>';
                    response.data.forEach(function(item) {
                        var selected = (selectedId && item.id == selectedId) ? 'selected' : '';
                        html += '<option value="' + item.id + '" ' + selected + '>' + (item.DisplayLabel || item.NamaCabang) + '</option>';
                    });
                    $('#selectCabang').html(html);
                }
            }
        });
    }

    // Preview image before upload
    $('#templateFile').change(function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#imagePreview').show();
            }
            reader.readAsDataURL(file);
            
            // Update label
            $('.custom-file-label').text(file.name);
        }
    });

    // Upload template
    $('#formUploadTemplate').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');

        $.ajax({
            url: '<?= base_url('backend/perlombaan/upload-template') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-upload"></i> Upload Template');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan saat upload', 'error');
                btn.prop('disabled', false).html('<i class="fas fa-upload"></i> Upload Template');
            }
        });
    });

    // Preview image before update
    $('#updateTemplateFile').change(function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#updatePreviewImg').attr('src', e.target.result);
                $('#updateImagePreview').show();
            }
            reader.readAsDataURL(file);
            
            // Update label
            $('#modalEditTemplate .custom-file-label').text(file.name);
        }
    });

    // Update template form
    $('#formUpdateTemplate').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: '<?= base_url('backend/perlombaan/update-template') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan saat update', 'error');
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
            }
        });
    });
});

function deleteTemplate(id) {
    Swal.fire({
        title: 'Yakin hapus template?',
        text: 'Template dan konfigurasi field akan dihapus',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('backend/perlombaan/delete-template') ?>/' + id,
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
}
</script>
<?= $this->endSection(); ?>
