<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Konfigurasi Munaqosah</h3>
                    <div class="card-tools">
                        <?php
                        // Cek apakah user adalah admin (IdTpq = 0)
                        $isAdminUser = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));
                        ?>
                        <?php if ($isAdminUser) : ?>
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAddKonfigurasi">
                                <i class="fas fa-plus"></i> Tambah Baru
                            </button>
                        <?php else : ?>
                            <span class="badge badge-info">
                                <i class="fas fa-info-circle"></i> Gunakan tombol duplikasi untuk menerapkan konfigurasi ke TPQ Anda
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('message')) : ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('message') ?>
                        </div>
                    <?php endif; ?>

                    <table class="table table-bordered table-striped" id="konfigurasiTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID TPQ</th>
                                <th>Setting Key</th>
                                <th>Setting Value</th>
                                <th>Setting Type</th>
                                <th>Description</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php if (!empty($konfigurasi)) : ?>
                                <?php foreach ($konfigurasi as $config) : ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= $config['IdTpq'] ?></td>
                                        <td><?= $config['SettingKey'] ?></td>
                                        <td><?= $config['SettingValue'] ?></td>
                                        <td><?= $config['SettingType'] ?></td>
                                        <td><?= $config['Description'] ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php
                                                // Cek apakah user adalah admin (IdTpq = 0)
                                                $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));

                                                // Untuk row dengan IdTpq = 'default', hanya admin yang bisa edit/delete
                                                $canEditDelete = ($config['IdTpq'] !== 'default') || $isAdmin;
                                                ?>

                                                <?php if ($canEditDelete) : ?>
                                                    <button type="button" class="btn btn-warning btn-sm edit-konfigurasi-btn"
                                                        data-id="<?= $config['id'] ?>"
                                                        data-idtpq="<?= $config['IdTpq'] ?>"
                                                        data-settingkey="<?= $config['SettingKey'] ?>"
                                                        data-settingvalue="<?= $config['SettingValue'] ?>"
                                                        data-settingtype="<?= $config['SettingType'] ?>"
                                                        data-description="<?= htmlspecialchars($config['Description']) ?>"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($config['IdTpq'] === 'default') : ?>
                                                    <button type="button" class="btn btn-info btn-sm duplicate-konfigurasi-btn"
                                                        data-id="<?= $config['id'] ?>"
                                                        data-idtpq="<?= $config['IdTpq'] ?>"
                                                        data-settingkey="<?= $config['SettingKey'] ?>"
                                                        data-settingvalue="<?= $config['SettingValue'] ?>"
                                                        data-settingtype="<?= $config['SettingType'] ?>"
                                                        data-description="<?= htmlspecialchars($config['Description']) ?>"
                                                        title="Duplikasi">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($canEditDelete) : ?>
                                                    <button type="button" class="btn btn-danger btn-sm delete-konfigurasi-btn"
                                                        data-id="<?= $config['id'] ?>"
                                                        data-idtpq="<?= $config['IdTpq'] ?>"
                                                        data-settingkey="<?= $config['SettingKey'] ?>"
                                                        title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data konfigurasi</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Konfigurasi -->
<div class="modal fade" id="modalAddKonfigurasi" tabindex="-1" role="dialog" aria-labelledby="modalAddKonfigurasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddKonfigurasiLabel">Tambah Konfigurasi Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddKonfigurasi">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addIdTpq">ID TPQ <span class="text-danger">*</span></label>
                        <select class="form-control" id="addIdTpq" name="IdTpq" required>
                            <option value="">-- Pilih ID TPQ --</option>
                            <?php
                            // Check if user is admin
                            $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));
                            ?>
                            <?php if ($isAdmin) : ?>
                                <option value="default">default (Template Default)</option>
                                <option value="0">0 (Admin)</option>
                            <?php endif; ?>
                            <?php if (!empty($listTpq)) : ?>
                                <?php foreach ($listTpq as $tpq) : ?>
                                    <option value="<?= $tpq['IdTpq'] ?>"><?= $tpq['IdTpq'] ?> - <?= $tpq['NamaTpq'] ?? $tpq['IdTpq'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted">
                            <?php if ($isAdmin) : ?>
                                Pilih ID TPQ dari dropdown atau gunakan 'default' untuk template, '0' untuk admin
                            <?php else : ?>
                                ID TPQ Anda: <?= $idTpq ?> (otomatis terisi)
                            <?php endif; ?>
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="addSettingKey">Setting Key <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addSettingKey" name="SettingKey" required placeholder="NilaiMinimal, NilaiMaximal, dll">
                        <small class="form-text text-muted">Nama key pengaturan (minimal 3 karakter)</small>
                    </div>
                    <div class="form-group">
                        <label for="addSettingValue">Setting Value <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addSettingValue" name="SettingValue" required placeholder="40, 99, 100, dll">
                        <small class="form-text text-muted">Nilai pengaturan</small>
                    </div>
                    <div class="form-group">
                        <label for="addSettingType">Setting Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="addSettingType" name="SettingType" required>
                            <option value="number">number</option>
                            <option value="text">text</option>
                            <option value="boolean">boolean</option>
                            <option value="json">json</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addDescription">Description</label>
                        <textarea class="form-control" id="addDescription" name="Description" rows="3" placeholder="Keterangan pengaturan"></textarea>
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

<!-- Modal Edit Konfigurasi -->
<div class="modal fade" id="modalEditKonfigurasi" tabindex="-1" role="dialog" aria-labelledby="modalEditKonfigurasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditKonfigurasiLabel">Edit Konfigurasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditKonfigurasi">
                <input type="hidden" id="editId" name="id">
                <input type="hidden" id="editSettingTypeHidden" name="SettingType">
                <?php $isAdminEdit = ($idTpq === '0' || $idTpq === 0 || empty($idTpq)); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editIdTpq">ID TPQ</label>
                        <input type="text" class="form-control" id="editIdTpq" name="IdTpq" readonly>
                        <small class="form-text text-muted">ID TPQ tidak dapat diubah</small>
                    </div>
                    <div class="form-group">
                        <label for="editSettingKey">Setting Key</label>
                        <input type="text" class="form-control" id="editSettingKey" name="SettingKey" <?= $isAdminEdit ? '' : 'readonly' ?>>
                        <small class="form-text text-muted">
                            <?php if ($isAdminEdit) : ?>
                                Admin dapat mengubah Setting Key sesuai kebutuhan.
                            <?php else : ?>
                                Setting Key tidak dapat diubah oleh TPQ.
                            <?php endif; ?>
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="editSettingValue">Setting Value <span class="text-danger">*</span></label>
                        <!-- Input text untuk non-boolean -->
                        <input type="text" class="form-control" id="editSettingValue" name="SettingValue" required>
                        <!-- Radio button untuk boolean -->
                        <div id="editSettingValueBoolean" style="display: none;">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="SettingValueBoolean" id="editSettingValueTrue" value="true" required>
                                <label class="form-check-label" for="editSettingValueTrue">
                                    True
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="SettingValueBoolean" id="editSettingValueFalse" value="false" required>
                                <label class="form-check-label" for="editSettingValueFalse">
                                    False
                                </label>
                            </div>
                        </div>
                        <small class="form-text text-muted">Nilai pengaturan yang dapat diubah</small>
                        <small class="form-text text-danger" id="editSettingValueError" style="display: none;"></small>
                    </div>
                    <div class="form-group">
                        <label for="editSettingType">Setting Type</label>
                        <select class="form-control" id="editSettingType" disabled>
                            <option value="number">number</option>
                            <option value="text">text</option>
                            <option value="boolean">boolean</option>
                            <option value="json">json</option>
                        </select>
                        <small class="form-text text-muted">Setting Type tidak dapat diubah</small>
                    </div>
                    <div class="form-group">
                        <label for="editDescription">Description</label>
                        <textarea class="form-control" id="editDescription" name="Description" rows="3"></textarea>
                        <small class="form-text text-muted">Keterangan pengaturan yang dapat diubah</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Duplikasi Konfigurasi -->
<div class="modal fade" id="modalDuplicateKonfigurasi" tabindex="-1" role="dialog" aria-labelledby="modalDuplicateKonfigurasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="modalDuplicateKonfigurasiLabel">
                    <i class="fas fa-copy"></i> Duplikasi Konfigurasi
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formDuplicateKonfigurasi">
                <input type="hidden" id="duplicateId" name="source_id">
                <input type="hidden" id="duplicateSettingTypeHidden" name="SettingType">
                <?php if (!$isAdmin) : ?>
                    <input type="hidden" id="duplicateIdTpqHidden" name="IdTpq" value="<?= $idTpq ?>">
                <?php endif; ?>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Anda akan menduplikasi konfigurasi dari template default ke ID TPQ yang dipilih.
                    </div>
                    <div class="form-group">
                        <label for="duplicateIdTpq">ID TPQ Tujuan <span class="text-danger">*</span></label>
                        <?php if ($isAdmin) : ?>
                            <select class="form-control" id="duplicateIdTpq" name="IdTpq" required>
                                <option value="">-- Pilih ID TPQ Tujuan --</option>
                                <option value="0">0 (Admin)</option>
                                <?php if (!empty($listTpq)) : ?>
                                    <?php foreach ($listTpq as $tpq) : ?>
                                        <option value="<?= $tpq['IdTpq'] ?>"><?= $tpq['IdTpq'] ?> - <?= $tpq['NamaTpq'] ?? $tpq['IdTpq'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="form-text text-muted">
                                Pilih ID TPQ tujuan untuk menerapkan konfigurasi ini. Gunakan '0' untuk admin. Tidak dapat menduplikasi ke 'default'.
                            </small>
                        <?php else : ?>
                            <input type="text" class="form-control" id="duplicateIdTpq" value="<?= $idTpq ?>" readonly>
                            <small class="form-text text-muted">
                                <i class="fas fa-lock"></i> Konfigurasi akan diterapkan ke TPQ Anda: <strong><?= $idTpq ?></strong> (tidak dapat diubah)
                            </small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="duplicateSettingKey">Setting Key</label>
                        <input type="text" class="form-control" id="duplicateSettingKey" name="SettingKey" <?= $isAdmin ? '' : 'readonly' ?>>
                        <small class="form-text text-muted">
                            <?php if ($isAdmin) : ?>
                                Admin dapat mengubah Setting Key sebelum duplikasi.
                            <?php else : ?>
                                Setting Key tidak dapat diubah oleh TPQ.
                            <?php endif; ?>
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="duplicateSettingValue">Setting Value <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="duplicateSettingValue" name="SettingValue" required>
                        <small class="form-text text-muted">Anda dapat mengubah nilai sebelum menduplikasi</small>
                    </div>
                    <div class="form-group">
                        <label for="duplicateSettingType">Setting Type</label>
                        <select class="form-control" id="duplicateSettingType" disabled>
                            <option value="number">number</option>
                            <option value="text">text</option>
                            <option value="boolean">boolean</option>
                            <option value="json">json</option>
                        </select>
                        <small class="form-text text-muted">Setting Type tidak dapat diubah</small>
                    </div>
                    <div class="form-group">
                        <label for="duplicateDescription">Description</label>
                        <textarea class="form-control" id="duplicateDescription" name="Description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-copy"></i> Duplikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Get current user's IdTpq from session
        const currentIdTpq = '<?= $idTpq ?? "" ?>';
        const isAdmin = currentIdTpq === '' || currentIdTpq === '0' || currentIdTpq === 0;

        // Auto-select IdTpq when opening modal Tambah Konfigurasi
        $('#modalAddKonfigurasi').on('show.bs.modal', function() {
            if (!isAdmin && currentIdTpq) {
                // Set select to current user's IdTpq
                $('#addIdTpq').val(currentIdTpq);

                // Optionally disable the select for non-admin users
                // $('#addIdTpq').prop('disabled', true);
            }
        });

        // Reset form when modal is closed
        $('#modalAddKonfigurasi').on('hidden.bs.modal', function() {
            $('#formAddKonfigurasi')[0].reset();
            // Re-enable select if it was disabled
            // $('#addIdTpq').prop('disabled', false);
        });

        // Auto-select IdTpq when opening modal Duplikasi (only for admin)
        $('#modalDuplicateKonfigurasi').on('show.bs.modal', function() {
            // For non-admin, IdTpq is already set and locked via hidden field
            // For admin, they can select freely from dropdown
            if (isAdmin) {
                // Admin can select any IdTpq
                $('#duplicateIdTpq').val('').trigger('change');
            } else {
                // For non-admin, set to their IdTpq (readonly field + hidden field)
                $('#duplicateIdTpqHidden').val(currentIdTpq);
                $('#duplicateIdTpq').val(currentIdTpq);
            }
        });

        // Form Tambah Konfigurasi
        $('#formAddKonfigurasi').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '<?= base_url('backend/munaqosah/save-konfigurasi') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $('#formAddKonfigurasi button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Gagal menyimpan data',
                            html: response.errors ? '<ul>' + Object.values(response.errors).map(err => '<li>' + err + '</li>').join('') + '</ul>' : null
                        });
                        $('#formAddKonfigurasi button[type="submit"]').prop('disabled', false).html('Simpan');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan: ' + error
                    });
                    $('#formAddKonfigurasi button[type="submit"]').prop('disabled', false).html('Simpan');
                }
            });
        });

        // Reset form saat modal ditutup
        $('#modalAddKonfigurasi').on('hidden.bs.modal', function() {
            $('#formAddKonfigurasi')[0].reset();
        });

        // Reset form saat modal edit ditutup
        $('#modalEditKonfigurasi').on('hidden.bs.modal', function() {
            $('#formEditKonfigurasi')[0].reset();
            // Reset visibility
            $('#editSettingValue').show().prop('required', true)
                .attr('type', 'text')
                .removeAttr('min')
                .removeAttr('step')
                .removeAttr('data-setting-type')
                .removeClass('is-invalid');
            $('#editSettingValueBoolean').hide();
            // Reset radio buttons
            $('#editSettingValueTrue').prop('checked', false).prop('required', false);
            $('#editSettingValueFalse').prop('checked', false).prop('required', false);
            // Reset error message
            $('#editSettingValueError').hide();
        });

        // Button Edit Click
        $(document).on('click', '.edit-konfigurasi-btn', function() {
            var id = $(this).data('id');
            var idTpq = $(this).data('idtpq');
            var settingKey = $(this).data('settingkey');
            var settingValue = $(this).data('settingvalue');
            var settingType = $(this).data('settingtype');
            var description = $(this).data('description');

            // Set form values
            if (isAdmin) {
                $('#editSettingKey').prop('readonly', false);
            } else {
                $('#editSettingKey').prop('readonly', true);
            }

            $('#editId').val(id);
            $('#editIdTpq').val(idTpq);
            $('#editSettingKey').val(settingKey);
            $('#editSettingType').val(settingType);
            $('#editSettingTypeHidden').val(settingType); // Hidden field untuk submit
            $('#editDescription').val(description);

            // Reset error message
            $('#editSettingValueError').hide();
            $('#editSettingValue').removeClass('is-invalid');

            // Handle boolean type - show radio buttons, hide text input
            if (settingType === 'boolean') {
                $('#editSettingValue').hide().prop('required', false);
                $('#editSettingValueBoolean').show();
                // Enable required on radio buttons
                $('#editSettingValueTrue').prop('required', true);
                $('#editSettingValueFalse').prop('required', true);

                // Set radio button value
                // Handle various boolean representations: true, false, "true", "false", "1", "0"
                var boolValue = String(settingValue).toLowerCase();
                if (boolValue === 'true' || boolValue === '1' || boolValue === 'yes') {
                    $('#editSettingValueTrue').prop('checked', true);
                    $('#editSettingValueFalse').prop('checked', false);
                } else {
                    $('#editSettingValueTrue').prop('checked', false);
                    $('#editSettingValueFalse').prop('checked', true);
                }
            } else if (settingType === 'number') {
                // Number type - show number input with min 0, hide radio buttons
                $('#editSettingValue').show().prop('required', true)
                    .attr('type', 'number')
                    .attr('min', '0')
                    .attr('step', '1')
                    .attr('data-setting-type', 'number') // Tambahkan atribut untuk tracking
                    .val(settingValue);
                $('#editSettingValueBoolean').hide();
                // Disable required on radio buttons and uncheck them
                $('#editSettingValueTrue').prop('required', false).prop('checked', false);
                $('#editSettingValueFalse').prop('required', false).prop('checked', false);
            } else {
                // Other types (text, json) - show text input, hide radio buttons
                $('#editSettingValue').show().prop('required', true)
                    .attr('type', 'text')
                    .removeAttr('min')
                    .removeAttr('step')
                    .removeAttr('data-setting-type')
                    .val(settingValue);
                $('#editSettingValueBoolean').hide();
                // Disable required on radio buttons and uncheck them
                $('#editSettingValueTrue').prop('required', false).prop('checked', false);
                $('#editSettingValueFalse').prop('required', false).prop('checked', false);
            }

            $('#modalEditKonfigurasi').modal('show');
        });

        // Fungsi helper untuk cek apakah setting type adalah number
        function isNumberType() {
            return $('#editSettingTypeHidden').val() === 'number' || $('#editSettingValue').attr('data-setting-type') === 'number';
        }

        // Validasi input number - hanya menerima angka 0-9
        $(document).on('keydown keypress', '#editSettingValue', function(e) {
            // Hanya validasi jika setting type adalah number
            if (!isNumberType()) {
                return true;
            }

            // Allow: backspace, delete, tab, escape, enter, arrow keys, home, end
            if ([8, 9, 27, 13, 46, 35, 36, 37, 38, 39, 40].indexOf(e.keyCode) !== -1 ||
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true)) {
                return true;
            }

            // Hanya izinkan angka 0-9 (keyboard utama: 48-57, numpad: 96-105)
            var charCode = e.which || e.keyCode;
            if (charCode >= 48 && charCode <= 57) {
                return true; // Angka 0-9 dari keyboard utama
            }
            if (charCode >= 96 && charCode <= 105) {
                return true; // Angka 0-9 dari numpad
            }

            // Blokir semua karakter lain
            e.preventDefault();
            e.stopPropagation();
            return false;
        });

        // Handler untuk paste - filter hanya angka
        $(document).on('paste', '#editSettingValue', function(e) {
            // Hanya validasi jika setting type adalah number
            if (!isNumberType()) {
                return true;
            }

            e.preventDefault();
            e.stopPropagation();

            var paste = (e.originalEvent || e).clipboardData.getData('text/plain');
            // Hapus semua karakter yang bukan angka
            var numericValue = paste.replace(/[^0-9]/g, '');

            if (numericValue !== paste) {
                $('#editSettingValueError').text('Hanya angka 0-9 yang diperbolehkan. Karakter lain telah dihapus.').show();
                $(this).addClass('is-invalid');
                var $this = $(this);
                setTimeout(function() {
                    $('#editSettingValueError').hide();
                    $this.removeClass('is-invalid');
                }, 2000);
            }

            // Set nilai yang sudah difilter
            var currentValue = $(this).val();
            var selectionStart = this.selectionStart || 0;
            var selectionEnd = this.selectionEnd || selectionStart;
            var newValue = currentValue.substring(0, selectionStart) + numericValue + currentValue.substring(selectionEnd);
            $(this).val(newValue);

            // Set cursor position
            var newCursorPos = selectionStart + numericValue.length;
            this.setSelectionRange(newCursorPos, newCursorPos);
            return false;
        });

        // Validasi saat input berubah - hapus karakter non-numeric
        $(document).on('input', '#editSettingValue', function() {
            // Hanya validasi jika setting type adalah number
            if (!isNumberType()) {
                return true;
            }

            var $this = $(this);
            var originalValue = $this.val();

            // Hapus semua karakter yang bukan angka
            var numericValue = originalValue.replace(/[^0-9]/g, '');

            // Jika ada perubahan, update nilai
            if (originalValue !== numericValue) {
                var cursorPos = this.selectionStart || 0;
                $this.val(numericValue);

                // Tampilkan peringatan jika ada karakter yang dihapus
                if (originalValue.length > numericValue.length) {
                    $('#editSettingValueError').text('Hanya angka 0-9 yang diperbolehkan. Karakter lain telah dihapus.').show();
                    $this.addClass('is-invalid');
                    var $errorField = $('#editSettingValueError');
                    var $inputField = $this;
                    setTimeout(function() {
                        $errorField.hide();
                        $inputField.removeClass('is-invalid');
                    }, 2000);
                }

                // Restore cursor position
                var newCursorPos = Math.max(0, cursorPos - (originalValue.length - numericValue.length));
                this.setSelectionRange(newCursorPos, newCursorPos);
            }

            // Validasi nilai >= 0 (sudah otomatis karena hanya angka)
            if (numericValue !== '' && parseFloat(numericValue) < 0) {
                $('#editSettingValueError').text('Hanya angka positif >= 0 yang diperbolehkan').show();
                $this.addClass('is-invalid');
            } else if (numericValue !== '') {
                $('#editSettingValueError').hide();
                $this.removeClass('is-invalid');
            }
        });

        // Validasi saat blur
        $(document).on('blur', '#editSettingValue', function() {
            // Hanya validasi jika setting type adalah number
            if (!isNumberType()) {
                return true;
            }

            var value = $(this).val();
            // Hapus karakter non-numeric terlebih dahulu
            var numericValue = value.replace(/[^0-9]/g, '');
            if (value !== numericValue) {
                $(this).val(numericValue);
            }

            if (numericValue !== '' && (isNaN(numericValue) || parseFloat(numericValue) < 0)) {
                $('#editSettingValueError').text('Hanya angka positif >= 0 yang diperbolehkan').show();
                $(this).addClass('is-invalid');
            } else {
                $('#editSettingValueError').hide();
                $(this).removeClass('is-invalid');
            }
        });

        // Form Edit Konfigurasi
        $('#formEditKonfigurasi').on('submit', function(e) {
            e.preventDefault();

            var id = $('#editId').val();
            var settingType = $('#editSettingTypeHidden').val();

            // Prepare form data
            var formData = $(this).serializeArray();

            // If boolean type, get value from radio button instead of text input
            if (settingType === 'boolean') {
                // Remove the text input value from form data
                formData = formData.filter(function(item) {
                    return item.name !== 'SettingValue';
                });

                // Add the radio button value
                var boolValue = $('input[name="SettingValueBoolean"]:checked').val();
                if (boolValue) {
                    formData.push({
                        name: 'SettingValue',
                        value: boolValue
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Silakan pilih nilai boolean (True atau False)'
                    });
                    return;
                }
            } else if (settingType === 'number') {
                // Validate number value >= 0
                var numberValue = parseFloat($('#editSettingValue').val());
                if (isNaN(numberValue) || numberValue < 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Nilai harus berupa angka positif >= 0'
                    });
                    $('#editSettingValue').focus();
                    return;
                }
            }

            // Convert array to object for jQuery ajax
            var dataObject = {};
            $.each(formData, function(i, field) {
                dataObject[field.name] = field.value;
            });

            $.ajax({
                url: '<?= base_url('backend/munaqosah/update-konfigurasi/') ?>' + id,
                type: 'POST',
                data: dataObject,
                dataType: 'json',
                beforeSend: function() {
                    $('#formEditKonfigurasi button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengupdate...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Gagal mengupdate data',
                            html: response.errors ? '<ul>' + Object.values(response.errors).map(err => '<li>' + err + '</li>').join('') + '</ul>' : null
                        });
                        $('#formEditKonfigurasi button[type="submit"]').prop('disabled', false).html('Update');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan: ' + error
                    });
                    $('#formEditKonfigurasi button[type="submit"]').prop('disabled', false).html('Update');
                }
            });
        });

        // Button Duplicate Click
        $(document).on('click', '.duplicate-konfigurasi-btn', function() {
            var id = $(this).data('id');
            var idTpq = $(this).data('idtpq');
            var settingKey = $(this).data('settingkey');
            var settingValue = $(this).data('settingvalue');
            var settingType = $(this).data('settingtype');
            var description = $(this).data('description');

            // Set form values
            if (isAdmin) {
                $('#duplicateSettingKey').prop('readonly', false);
            } else {
                $('#duplicateSettingKey').prop('readonly', true);
            }

            $('#duplicateId').val(id);

            // For admin, clear selection; for non-admin, it's already locked
            if (isAdmin) {
                $('#duplicateIdTpq').val('').trigger('change');
            } else {
                // For non-admin, set to their IdTpq (readonly field + hidden field)
                $('#duplicateIdTpq').val(currentIdTpq);
                $('#duplicateIdTpqHidden').val(currentIdTpq);
            }

            $('#duplicateSettingKey').val(settingKey);
            $('#duplicateSettingValue').val(settingValue);
            $('#duplicateSettingType').val(settingType);
            $('#duplicateSettingTypeHidden').val(settingType); // Hidden field untuk submit
            $('#duplicateDescription').val(description);

            $('#modalDuplicateKonfigurasi').modal('show');
        });

        // Form Duplicate Konfigurasi
        $('#formDuplicateKonfigurasi').on('submit', function(e) {
            e.preventDefault();

            // Validate target IdTpq
            var targetIdTpq = $('#duplicateIdTpq').val();
            if (!targetIdTpq || targetIdTpq === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'ID TPQ Tujuan harus dipilih'
                });
                $('#duplicateIdTpq').focus();
                return;
            }

            // Prevent duplicating to 'default' (meskipun option default tidak ada di select duplikasi)
            if (targetIdTpq === 'default') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Tidak dapat menduplikasi ke "default". Gunakan ID TPQ lain atau "0" untuk admin.'
                });
                return;
            }

            $.ajax({
                url: '<?= base_url('backend/munaqosah/duplicate-konfigurasi') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $('#formDuplicateKonfigurasi button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menduplikasi...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Gagal menduplikasi data',
                            html: response.errors ? '<ul>' + Object.values(response.errors).map(err => '<li>' + err + '</li>').join('') + '</ul>' : null
                        });
                        $('#formDuplicateKonfigurasi button[type="submit"]').prop('disabled', false).html('<i class="fas fa-copy"></i> Duplikasi');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan: ' + error
                    });
                    $('#formDuplicateKonfigurasi button[type="submit"]').prop('disabled', false).html('<i class="fas fa-copy"></i> Duplikasi');
                }
            });
        });

        // Reset form saat modal ditutup
        $('#modalDuplicateKonfigurasi').on('hidden.bs.modal', function() {
            $('#formDuplicateKonfigurasi')[0].reset();
        });

        // Button Delete Click
        $(document).on('click', '.delete-konfigurasi-btn', function() {
            var id = $(this).data('id');
            var idTpq = $(this).data('idtpq');
            var settingKey = $(this).data('settingkey');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: 'Apakah Anda yakin ingin menghapus konfigurasi ini?<br><br>' +
                    '<strong>ID TPQ:</strong> ' + idTpq + '<br>' +
                    '<strong>Setting Key:</strong> ' + settingKey,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('backend/munaqosah/delete-konfigurasi/') ?>' + id,
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Gagal menghapus data'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan: ' + error
                            });
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>