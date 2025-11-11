<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus"></i> Form Tambah Juri Sertifikasi
                    </h3>
                </div>
                <form id="formCreateJuri" onsubmit="event.preventDefault(); simpanJuri();">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="IdJuri">ID Juri <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="IdJuri" name="IdJuri" value="<?= esc($next_id_juri) ?>" placeholder="Contoh: JS001" required readonly>
                            <small class="form-text text-muted">ID Juri di-generate otomatis berdasarkan ID terakhir</small>
                        </div>

                        <div class="form-group">
                            <label for="IdGroupMateri">Group Materi <span class="text-danger">*</span></label>
                            <select class="form-control" id="IdGroupMateri" name="IdGroupMateri" required>
                                <option value="">Pilih Group Materi</option>
                                <?php foreach ($group_materi_list as $group): ?>
                                    <option value="<?= esc($group['IdGroupMateri']) ?>">
                                        <?= esc($group['IdGroupMateri']) ?> - <?= esc($group['NamaMateri']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Pilih group materi untuk juri ini</small>
                        </div>

                        <div class="form-group">
                            <label for="usernameJuri">Username Juri <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="usernameJuri" name="usernameJuri" placeholder="Contoh: juri.materi.pg" required readonly>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-secondary" id="btnGenerateUsername" title="Generate Username">
                                        <i class="fas fa-sync-alt"></i> Generate
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Username akan di-generate otomatis berdasarkan Group Materi yang dipilih</small>
                        </div>

                        <hr>
                        <h5 class="mb-3"><i class="fas fa-user"></i> Informasi User (Opsional)</h5>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="createUser" name="createUser" value="true" onchange="toggleUserFields()">
                                <label class="custom-control-label" for="createUser">
                                    <strong>Buat User Baru di Sistem</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Centang untuk membuat user baru di sistem dengan password default: <strong>TpqSmart123</strong>
                            </small>
                        </div>

                        <div id="userFields" style="display: none;">
                            <div class="form-group">
                                <label for="fullname">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Masukkan nama lengkap">
                                <small class="form-text text-muted">Nama lengkap untuk user baru</small>
                            </div>

                            <div class="form-group">
                                <label for="IdAuthGroup">Group User <span class="text-danger">*</span></label>
                                <select class="form-control" id="IdAuthGroup" name="IdAuthGroup">
                                    <option value="">Pilih Group User</option>
                                    <?php foreach ($auth_groups as $group): ?>
                                        <option value="<?= esc($group['id']) ?>">
                                            <?= esc($group['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Pilih group user untuk user baru</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
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

<?= $this->section('scripts'); ?>
<script>
    // Definisikan fungsi generateUsername di luar document.ready agar bisa diakses
    function generateUsername() {
        var idGroupMateri = $('#IdGroupMateri').val();

        console.log('generateUsername called, IdGroupMateri:', idGroupMateri);

        if (!idGroupMateri) {
            $('#usernameJuri').val('');
            return;
        }

        // Tampilkan loading
        $('#usernameJuri').prop('disabled', true);
        var generateBtn = $('#btnGenerateUsername');
        generateBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        // Ambil CSRF token dari meta tag atau form
        var csrfName = '<?= csrf_token() ?>';
        var csrfHash = '<?= csrf_hash() ?>';

        var formData = {
            IdGroupMateri: idGroupMateri
        };
        formData[csrfName] = csrfHash;

        $.ajax({
            url: '<?= base_url('backend/sertifikasi/generateNextUsernameJuri') ?>',
            type: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('AJAX Success Response:', response);
                $('#usernameJuri').prop('disabled', false);
                generateBtn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Generate');

                if (response.success) {
                    $('#usernameJuri').val(response.username);
                    console.log('Username generated:', response.username);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Gagal generate username'
                    });
                    $('#usernameJuri').val('');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr, status, error);
                console.error('Response Text:', xhr.responseText);

                $('#usernameJuri').prop('disabled', false);
                generateBtn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Generate');

                var errorMessage = 'Terjadi kesalahan saat generate username';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        errorMessage = 'Error: ' + xhr.status + ' - ' + error;
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
                $('#usernameJuri').val('');
            }
        });
    }

    $(document).ready(function() {
        console.log('Document ready, setting up event handlers');

        // Pastikan elemen sudah ada
        var $idGroupMateri = $('#IdGroupMateri');
        var $btnGenerate = $('#btnGenerateUsername');

        console.log('IdGroupMateri element found:', $idGroupMateri.length > 0);
        console.log('Generate button found:', $btnGenerate.length > 0);

        if ($idGroupMateri.length > 0) {
            // Event handler untuk perubahan Group Materi
            $idGroupMateri.on('change', function() {
                console.log('IdGroupMateri changed to:', $(this).val());
                generateUsername();
            });
            console.log('Change event handler attached to IdGroupMateri');
        }

        if ($btnGenerate.length > 0) {
            // Event handler untuk tombol Generate
            $btnGenerate.on('click', function() {
                console.log('Generate button clicked');
                generateUsername();
            });
            console.log('Click event handler attached to Generate button');
        }

        console.log('Event handlers setup complete');
    });

    function toggleUserFields() {
        var createUser = $('#createUser').is(':checked');
        if (createUser) {
            $('#userFields').slideDown();
            $('#fullname').prop('required', true);
            $('#IdAuthGroup').prop('required', true);
        } else {
            $('#userFields').slideUp();
            $('#fullname').prop('required', false);
            $('#IdAuthGroup').prop('required', false);
            $('#fullname').val('');
            $('#IdAuthGroup').val('');
        }
    }

    function simpanJuri() {
        var createUser = $('#createUser').is(':checked') ? 'true' : 'false';

        var formData = {
            IdJuri: $('#IdJuri').val(),
            IdGroupMateri: $('#IdGroupMateri').val(),
            usernameJuri: $('#usernameJuri').val(),
            createUser: createUser
        };

        // Validasi client-side
        if (!formData.IdJuri || !formData.IdGroupMateri || !formData.usernameJuri) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Semua field wajib harus diisi'
            });
            return;
        }

        // Jika create user dicentang, validasi field user
        if (createUser === 'true') {
            var fullname = $('#fullname').val();
            var idAuthGroup = $('#IdAuthGroup').val();

            if (!fullname || !idAuthGroup) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Nama Lengkap dan Group User harus diisi jika ingin membuat user baru'
                });
                return;
            }

            formData.fullname = fullname;
            formData.IdAuthGroup = idAuthGroup;
        }

        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('backend/sertifikasi/storeJuriSertifikasi') ?>',
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
                    text: 'Terjadi kesalahan saat menyimpan data'
                });
            }
        });
    }
</script>
<?= $this->endSection(); ?>