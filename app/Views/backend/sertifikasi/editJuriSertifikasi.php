<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Form Edit Juri Sertifikasi
                    </h3>
                </div>
                <form id="formEditJuri" onsubmit="event.preventDefault(); updateJuri();">
                    <div class="card-body">
                        <input type="hidden" id="juri_id" value="<?= esc($juri['id']) ?>">
                        
                        <div class="form-group">
                            <label for="IdJuri">ID Juri <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="IdJuri" name="IdJuri" value="<?= esc($juri['IdJuri']) ?>" placeholder="Contoh: JS001" required>
                            <small class="form-text text-muted">Masukkan ID Juri (contoh: JS001, JS002, dll)</small>
                        </div>

                        <div class="form-group">
                            <label for="IdGroupMateri">Group Materi <span class="text-danger">*</span></label>
                            <select class="form-control" id="IdGroupMateri" name="IdGroupMateri" required>
                                <option value="">Pilih Group Materi</option>
                                <?php foreach ($group_materi_list as $group): ?>
                                    <option value="<?= esc($group['IdGroupMateri']) ?>" <?= ($juri['IdGroupMateri'] == $group['IdGroupMateri']) ? 'selected' : '' ?>>
                                        <?= esc($group['IdGroupMateri']) ?> - <?= esc($group['NamaMateri']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Pilih group materi untuk juri ini</small>
                        </div>

                        <div class="form-group">
                            <label for="usernameJuri">Username Juri <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="usernameJuri" name="usernameJuri" value="<?= esc($juri['usernameJuri']) ?>" placeholder="Contoh: juri.materi.pg" required>
                            <small class="form-text text-muted">Masukkan username juri (harus sesuai dengan username di sistem user)</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="resetPassword" name="resetPassword" value="true">
                                <label class="custom-control-label" for="resetPassword">
                                    <strong>Reset Password User</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Centang untuk mereset password user ke default: <strong>TpqSmart123</strong>
                            </small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                        <a href="<?= base_url('backend/sertifikasi/listJuriSertifikasi') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
    function updateJuri() {
        var juriId = $('#juri_id').val();
        var resetPassword = $('#resetPassword').is(':checked') ? 'true' : 'false';
        
        var formData = {
            IdJuri: $('#IdJuri').val(),
            IdGroupMateri: $('#IdGroupMateri').val(),
            usernameJuri: $('#usernameJuri').val(),
            resetPassword: resetPassword
        };

        // Validasi client-side
        if (!formData.IdJuri || !formData.IdGroupMateri || !formData.usernameJuri) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Semua field harus diisi'
            });
            return;
        }

        Swal.fire({
            title: 'Mengupdate...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('backend/sertifikasi/updateJuriSertifikasi') ?>/' + juriId,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '<?= base_url('backend/sertifikasi/listJuriSertifikasi') ?>';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mengupdate data'
                });
            }
        });
    }
</script>
<?= $this->endSection(); ?>

