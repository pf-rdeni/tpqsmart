<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <!-- Row 1: Pilih Lomba & Cabang -->
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
                                    <label>Lomba</label>
                                    <select class="form-control" id="selectLomba">
                                        <option value="">-- Pilih Lomba --</option>
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
                                    <label>Cabang</label>
                                    <select class="form-control" id="selectCabang">
                                        <option value="">-- Pilih Cabang --</option>
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
                    Pilih lomba dan cabang terlebih dahulu untuk mengelola template sertifikat.
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
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
                    var html = '<option value="">-- Pilih Cabang --</option>';
                    response.data.forEach(function(item) {
                        var selected = (selectedId && item.id == selectedId) ? 'selected' : '';
                        html += '<option value="' + item.id + '" ' + selected + '>' + item.NamaCabang + '</option>';
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
