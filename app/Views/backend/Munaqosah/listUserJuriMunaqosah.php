<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>

<?php
$roomIdMin = isset($roomIdMin) ? (int)$roomIdMin : 1;
$roomIdMax = isset($roomIdMax) ? (int)$roomIdMax : 10;

if ($roomIdMax < $roomIdMin) {
    $roomIdMax = $roomIdMin;
}

$roomOptions = $roomOptions ?? [];
if (empty($roomOptions)) {
    for ($number = $roomIdMin; $number <= $roomIdMax; $number++) {
        $roomOptions[] = sprintf('ROOM-%02d', $number);
    }
}

$roomIdMinLabel = sprintf('ROOM-%02d', $roomIdMin);
$roomIdMaxLabel = sprintf('ROOM-%02d', $roomIdMax);
?>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users"></i> Data Juri Pra-munaqosah/Munaqosah
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalJuri">
                        <i class="fas fa-plus"></i> Tambah Juri
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tableJuri" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">ID Juri</th>
                                <th width="18%">Username</th>
                                <th width="12%">Grup Materi</th>
                                <th width="10%">Room ID</th>
                                <th width="10%">Type Ujian</th>
                                <th width="12%">TPQ</th>
                                <th width="8%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($juri)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($juri as $j): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $j['IdJuri'] ?></td>
                                        <td><?= $j['UsernameJuri'] ?></td>
                                        <td><?= $j['NamaMateriGrup'] ?></td>
                                        <td>
                                            <?php if (!empty($j['RoomId'])): ?>
                                                <span class="badge badge-info"><?= $j['RoomId'] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $j['TypeUjian'] ?></td>
                                        <td><?= $j['NamaTpq'] ?? '-' ?></td>
                                        <td>
                                            <?php
                                            $statusBadgeClass = $j['Status'] === 'Aktif' ? 'badge-success' : 'badge-danger';
                                            ?>
                                            <span class="badge <?= $statusBadgeClass ?>"><?= $j['Status'] ?></span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info btn-edit-password" data-id="<?= $j['id'] ?>" title="Ubah Password">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning btn-edit-room" data-id="<?= $j['id'] ?>" data-room="<?= $j['RoomId'] ?>" title="Ubah Room">
                                                <i class="fas fa-door-open"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $j['id'] ?>" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data juri</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Tambah/Edit Juri -->
<div class="modal fade" id="modalJuri" tabindex="-1" role="dialog" aria-labelledby="modalJuriLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalJuriLabel">Tambah Juri Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formJuri">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="IdGrupMateriUjian">Grup Materi Ujian <span class="text-danger">*</span></label>
                                <select class="form-control" id="IdGrupMateriUjian" name="IdGrupMateriUjian" required>
                                    <option value="">Pilih Grup Materi Ujian</option>
                                    <?php foreach ($grupMateriUjian as $grup): ?>
                                        <option value="<?= $grup['IdGrupMateriUjian'] ?>"><?= $grup['NamaMateriGrup'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="IdTpq">TPQ (Opsional)</label>
                                <select class="form-control" id="IdTpq" name="IdTpq">
                                    <?php if (count($tpqDropdown) == 1): ?>
                                        <!-- Jika hanya ada satu TPQ, set sebagai default -->
                                        <option value="<?= $tpqDropdown[0]['IdTpq'] ?>" selected><?= $tpqDropdown[0]['NamaTpq'] ?></option>
                                    <?php else: ?>
                                        <!-- Jika ada banyak TPQ, tampilkan pilihan -->
                                        <option value="">Pilih TPQ</option>
                                        <?php foreach ($tpqDropdown as $tpq): ?>
                                            <option value="<?= $tpq['IdTpq'] ?>"><?= $tpq['NamaTpq'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="UsernameJuri">Username Juri <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="UsernameJuri" name="UsernameJuri" required readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="btnGenerateUsername">
                                            <i class="fas fa-sync"></i> Generate
                                        </button>
                                    </div>
                                </div>
                                <div class="invalid-feedback"></div>
                                <small class="form-text text-muted">Username akan digenerate otomatis berdasarkan grup materi dan TPQ</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Status">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="Status" name="Status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="Aktif" selected>Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="RoomId">Room ID (Opsional)</label>
                                <select class="form-control" id="RoomId" name="RoomId">
                                    <option value="">Tanpa Room</option>
                                    <?php foreach ($roomOptions as $roomOption): ?>
                                        <option value="<?= $roomOption ?>"><?= $roomOption ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Pilih room untuk menempatkan juri (<?= $roomIdMinLabel ?> - <?= $roomIdMaxLabel ?>).
                                    <strong>Biarkan Tanpa Room jika tidak menggunakan sistem room.</strong>
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-sm alert-warning mt-4 mb-0">
                                <small>
                                    <i class="fas fa-lightbulb"></i> <strong>Tips:</strong> Pilihan Room ID tersedia dari <?= $roomIdMinLabel ?> hingga <?= $roomIdMaxLabel ?>.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="PasswordJuri">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="PasswordJuri" name="PasswordJuri" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="useDefaultPassword" name="useDefaultPassword">
                                    <label class="form-check-label" for="useDefaultPassword">
                                        Gunakan password default: <strong>JuriTpqSmart</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ConfirmPasswordJuri">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="ConfirmPasswordJuri" name="ConfirmPasswordJuri" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="icon fas fa-info"></i> Informasi Akun:</h6>
                                <ul class="mb-0">
                                    <li><strong>Email:</strong> <span id="emailPreview">username@smartpq.simpedis.com</span></li>
                                    <li><strong>Password:</strong> JuriTpqSmart</li>
                                    <li><strong>Group:</strong> Juri (ID: 5)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveJuri">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Password -->
<div class="modal fade" id="modalEditPassword" tabindex="-1" role="dialog" aria-labelledby="modalEditPasswordLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPasswordLabel">Ubah Password Juri</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditPassword">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editPasswordJuri">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="editPasswordJuri" name="editPasswordJuri" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="toggleEditPasswordBtn">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="useDefaultPasswordEdit" name="useDefaultPasswordEdit">
                            <label class="form-check-label" for="useDefaultPasswordEdit">
                                Gunakan password default: <strong>JuriTpqSmart</strong>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editConfirmPasswordJuri">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="editConfirmPasswordJuri" name="editConfirmPasswordJuri" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnUpdatePassword">
                        <i class="fas fa-save"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Room -->
<div class="modal fade" id="modalEditRoom" tabindex="-1" role="dialog" aria-labelledby="modalEditRoomLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditRoomLabel">Ubah Room Juri</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditRoom">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editRoomId">Room ID</label>
                        <select class="form-control" id="editRoomId" name="RoomId">
                            <option value="">Tanpa Room</option>
                            <?php foreach ($roomOptions as $roomOption): ?>
                                <option value="<?= $roomOption ?>"><?= $roomOption ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Pilih room untuk juri ini (<?= $roomIdMinLabel ?> - <?= $roomIdMaxLabel ?>) atau pilih Tanpa Room jika tidak menggunakan sistem room.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnUpdateRoom">
                        <i class="fas fa-save"></i> Update Room
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
        let tableJuri;
        let currentJuriId = null;
        // DataTabe
        // tableJuri = $('#tableJuri').DataTable({
        //     "responsive": true,
        //     "lengthChange": false,
        //     "autoWidth": false
        //     // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        // });

        // Generate Username
        function generateUsername() {
            let idGrupMateriUjian = $('#IdGrupMateriUjian').val();
            let idTpq = $('#IdTpq').val();

            if (!idGrupMateriUjian) {
                showAlert('Pilih grup materi ujian terlebih dahulu', 'warning');
                return;
            }

            // Show loading on button
            let $btn = $('#btnGenerateUsername');
            let originalText = $btn.html();
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generate...');

            $.post('<?= base_url('backend/munaqosah/generate-username-juri') ?>', {
                    IdGrupMateriUjian: idGrupMateriUjian,
                    IdTpq: idTpq
                })
                .done(function(response) {
                    if (response.success) {
                        $('#UsernameJuri').val(response.username);
                        $('#emailPreview').text(response.username + '@smartpq.simpedis.com');
                    } else {
                        showAlert(response.message, 'error');
                    }
                })
                .fail(function() {
                    showAlert('Gagal generate username', 'error');
                })
                .always(function() {
                    // Restore button
                    $btn.prop('disabled', false).html(originalText);
                });
        }

        // Show Alert using SweetAlert2
        function showAlert(message, type) {
            let icon = 'info';
            let title = 'Informasi';

            switch (type) {
                case 'success':
                    icon = 'success';
                    title = 'Berhasil';
                    break;
                case 'error':
                    icon = 'error';
                    title = 'Error';
                    break;
                case 'warning':
                    icon = 'warning';
                    title = 'Peringatan';
                    break;
                default:
                    icon = 'info';
                    title = 'Informasi';
            }

            Swal.fire({
                icon: icon,
                title: title,
                text: message,
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                timer: type === 'success' ? 3000 : null,
                timerProgressBar: type === 'success' ? true : false
            });
        }

        // Reset Form
        function resetForm() {
            $('#formJuri')[0].reset();
            $('#formJuri .invalid-feedback').text('');
            $('#formJuri .form-control').removeClass('is-invalid');
            $('#modalJuriLabel').text('Tambah Juri Baru');
            $('#btnSaveJuri').html('<i class="fas fa-save"></i> Simpan');
            currentJuriId = null;
            $('#emailPreview').text('username@smartpq.simpedis.com');
            // Set default status to Aktif
            $('#Status').val('Aktif');
            // Reset RoomId selection
            $('#RoomId').val('');
            // Set default TypeUjian
            $('#TypeUjian').val('munaqosah');
            // Set default password
            $('#useDefaultPassword').prop('checked', true);
            $('#PasswordJuri').val('JuriTpqSmart').prop('readonly', true);
            $('#ConfirmPasswordJuri').val('JuriTpqSmart').prop('readonly', true);

            // Reset TPQ dropdown jika hanya ada satu TPQ
            <?php if (count($tpqDropdown) == 1): ?>
                $('#IdTpq').val('<?= $tpqDropdown[0]['IdTpq'] ?>');
            <?php endif; ?>
            // Set TypeUjian berdasar TPQ yang terpilih
            const hasTpq = $('#IdTpq').val() && $('#IdTpq').val() !== '' && $('#IdTpq').val() !== '0';
            $('#TypeUjian').val(hasTpq ? 'pra-munaqosah' : 'munaqosah');
        }


        // Event Handlers
        $('#btnGenerateUsername').click(function() {
            generateUsername();
        });

        $('#IdGrupMateriUjian, #IdTpq').change(function() {
            // Atur TypeUjian otomatis berdasarkan pilihan TPQ
            const hasTpq = $('#IdTpq').val() && $('#IdTpq').val() !== '' && $('#IdTpq').val() !== '0';
            $('#TypeUjian').val(hasTpq ? 'pra-munaqosah' : 'munaqosah');
            if ($('#IdGrupMateriUjian').val()) {
                generateUsername();
            }
        });

        // Toggle Password Visibility
        $(document).on('click', '#togglePasswordBtn', function() {
            const passwordField = $('#PasswordJuri');
            const icon = $(this).find('i');

            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Toggle Edit Password Visibility
        $(document).on('click', '#toggleEditPasswordBtn', function() {
            const passwordField = $('#editPasswordJuri');
            const icon = $(this).find('i');

            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Use Default Password Checkbox
        $(document).on('change', '#useDefaultPassword', function() {
            const passwordField = $('#PasswordJuri');
            const confirmField = $('#ConfirmPasswordJuri');

            if ($(this).is(':checked')) {
                passwordField.val('JuriTpqSmart').prop('readonly', true);
                confirmField.val('JuriTpqSmart').prop('readonly', true);
            } else {
                passwordField.val('').prop('readonly', false);
                confirmField.val('').prop('readonly', false);
            }
        });

        // Use Default Password Edit Checkbox
        $(document).on('change', '#useDefaultPasswordEdit', function() {
            const passwordField = $('#editPasswordJuri');
            const confirmField = $('#editConfirmPasswordJuri');

            if ($(this).is(':checked')) {
                passwordField.val('JuriTpqSmart').prop('readonly', true);
                confirmField.val('JuriTpqSmart').prop('readonly', false);
            } else {
                passwordField.val('').prop('readonly', false);
                confirmField.val('').prop('readonly', false);
            }
        });

        // Form Submit
        $('#formJuri').submit(function(e) {
            e.preventDefault();

            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            let formData = new FormData(this);
            let url = currentJuriId ?
                '<?= base_url('backend/munaqosah/updateJuri') ?>/' + currentJuriId :
                '<?= base_url('backend/munaqosah/saveJuri') ?>';

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#modalJuri').modal('hide');
                        // Reload halaman untuk menampilkan data terbaru
                        location.reload();
                    } else {
                        // Show validation errors
                        if (response.errors) {
                            let errorMessages = [];
                            Object.keys(response.errors).forEach(function(key) {
                                let field = $('[name="' + key + '"]');
                                field.addClass('is-invalid');
                                field.siblings('.invalid-feedback').text(response.errors[key]);
                                errorMessages.push(response.errors[key]);
                            });

                            // Show detailed error with SweetAlert2
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                html: '<ul class="text-left"><li>' + errorMessages.join('</li><li>') + '</li></ul>',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d33'
                            });
                        } else {
                            showAlert(response.message, 'error');
                        }
                    }
                },
                error: function() {
                    Swal.close();
                    showAlert('Terjadi kesalahan saat menyimpan data', 'error');
                }
            });
        });

        // Edit Password Button
        $(document).on('click', '.btn-edit-password', function() {
            currentJuriId = $(this).data('id');
            $('#modalEditPassword').modal('show');
            // Reset form
            $('#formEditPassword')[0].reset();
            $('#useDefaultPasswordEdit').prop('checked', false);
        });

        // Edit Room Button
        $(document).on('click', '.btn-edit-room', function() {
            currentJuriId = $(this).data('id');
            const roomId = $(this).data('room') || '';
            $('#editRoomId').val(roomId);
            $('#modalEditRoom').modal('show');
        });

        // Form Edit Room Submit
        $('#formEditRoom').submit(function(e) {
            e.preventDefault();

            const roomId = $('#editRoomId').val();

            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url('backend/munaqosah/updateRoomJuri') ?>/' + currentJuriId,
                type: 'POST',
                data: {
                    RoomId: roomId
                },
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#modalEditRoom').modal('hide');
                        // Reload halaman untuk menampilkan data terbaru
                        location.reload();
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function() {
                    Swal.close();
                    showAlert('Terjadi kesalahan saat mengupdate room', 'error');
                }
            });
        });

        // Delete Button
        $(document).on('click', '.btn-delete', function() {
            currentJuriId = $(this).data('id');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data juri ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with delete
                    $.ajax({
                        url: '<?= base_url('backend/munaqosah/deleteJuri') ?>/' + currentJuriId,
                        type: 'POST',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                // Reload halaman untuk menampilkan data terbaru
                                location.reload();
                            } else {
                                showAlert(response.message, 'error');
                            }
                        },
                        error: function() {
                            showAlert('Terjadi kesalahan saat menghapus data', 'error');
                        }
                    });
                }
            });
        });

        // Form Edit Password Submit
        $('#formEditPassword').submit(function(e) {
            e.preventDefault();

            // Validate password match
            const password = $('#editPasswordJuri').val();
            const confirmPassword = $('#editConfirmPasswordJuri').val();

            if (password !== confirmPassword) {
                showAlert('Password dan konfirmasi password tidak sama', 'error');
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url('backend/munaqosah/updatePasswordJuri') ?>/' + currentJuriId,
                type: 'POST',
                data: {
                    password: password
                },
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#modalEditPassword').modal('hide');
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function() {
                    Swal.close();
                    showAlert('Terjadi kesalahan saat mengupdate password', 'error');
                }
            });
        });

        // Modal Events
        $('#modalJuri').on('hidden.bs.modal', function() {
            resetForm();
        });

        $('#modalEditRoom').on('hidden.bs.modal', function() {
            $('#formEditRoom')[0].reset();
        });

        // Auto generate username when modal is shown and TPQ is pre-selected
        $('#modalJuri').on('shown.bs.modal', function() {
            <?php if (count($tpqDropdown) == 1): ?>
                // Auto generate username if TPQ is pre-selected
                if ($('#IdGrupMateriUjian').val()) {
                    generateUsername();
                }
            <?php endif; ?>
        });

    });
</script>
<?= $this->endSection() ?>