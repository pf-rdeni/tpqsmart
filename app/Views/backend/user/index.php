<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah">
                        <i class="fas fa-plus"></i> Tambah User
                    </button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="tblUser" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Nama</th>
                                <th>UserNama</th>
                                <th>Password</th>
                                <th>TPQ</th>
                                <th>KelurahanDesa</th>
                                <th>Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userData as $user): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" id="status<?= $user['id']; ?>" <?= $user['active'] == 1 ? 'checked' : ''; ?>
                                            onchange="updateStatus(<?= $user['id']; ?>, this.checked)">
                                    </td>
                                    <td><?= $user['nama']; ?></td>
                                    <td><?= $user['username']; ?></td>
                                    <td>
                                        <div class="input-group">
                                            <input type="password" class="form-control" value="<?= $user['password_hash']; ?>" readonly id="password-<?= $user['id']; ?>">
                                            <div class="input-group-append">
                                                <span class="input-group-text" onclick="togglePasswordList(<?= $user['id']; ?>)">
                                                    <i class="fas fa-eye" id="eye-password-<?= $user['id']; ?>"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= $user['namaTpq']; ?></td>
                                    <td><?= $user['kelurahanDesa']; ?></td>
                                    <td><?= $user['kategori']; ?></td>
                                    <td>
                                        <button class="btn btn-warning sm-small" onclick="editUser(<?= $user['id']; ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-danger sm-small" onclick="confirmDelete(<?= $user['id']; ?>)"><i class="fas fa-trash"></i> Hapus</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Tambah -->
<div class="modal fade" id="modal-tambah" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title">Tambah User</h4>
            </div>
            <form id="formTambahUser" onsubmit="event.preventDefault(); simpanUser();">
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="IdAuthGroup" class="col-sm-3 col-form-label">Group</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="IdAuthGroup" name="IdAuthGroup" required <?= count($dataAuthGroups) === 1 ? 'readonly' : '' ?>>
                                <?php if (count($dataAuthGroups) > 1): ?>
                                    <option value="">Pilih Group</option>
                                <?php endif; ?>
                                <?php foreach ($dataAuthGroups as $group): ?>
                                    <option value="<?= $group['id']; ?>"
                                        <?= (count($dataAuthGroups) === 1) ? 'selected' : '' ?>>
                                        <?= $group['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php if ($isAdmin): ?>
                        <!-- Admin bisa input nama manual atau pilih dari guru -->
                        <div class="form-group row">
                            <label for="fullname_manual" class="col-sm-3 col-form-label">Nama Lengkap</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="fullname_manual" name="fullname_manual" placeholder="Masukkan nama lengkap (opsional)">
                                <small class="form-text text-muted">Atau pilih dari daftar guru di bawah</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="IdNikGuru" class="col-sm-3 col-form-label">Nama Guru (Opsional)</label>
                            <div class="col-sm-9">
                                <select class="form-control select2" id="IdNikGuru" name="IdNikGuru" style="width: 100%;">
                                    <option value="">Pilih Nama Guru (opsional)</option>
                                    <?php foreach ($dataGuru as $guru): ?>
                                        <option value="<?= $guru['IdGuru']; ?>"><?= $guru['Nama']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Bukan Admin, wajib pilih guru -->
                        <div class="form-group row">
                            <label for="IdNikGuru" class="col-sm-3 col-form-label">Nama Guru</label>
                            <div class="col-sm-9">
                                <select class="form-control select2" id="IdNikGuru" name="IdNikGuru" required style="width: 100%;">
                                    <option value="">Pilih Nama</option>
                                    <?php foreach ($dataGuru as $guru): ?>
                                        <option value="<?= $guru['IdGuru']; ?>"><?= $guru['Nama']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="form-group row">
                        <label for="username" class="col-sm-3 col-form-label">Username</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-sm-3 col-form-label">Password</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="eye-password"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="confirm-password" class="col-sm-3 col-form-label">Konfirmasi Password</label>
                        <div class="col-sm-9">
                            <div class="input-group mb-2">
                                <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePassword('confirm-password')">
                                        <i class="fas fa-eye" id="eye-confirm-password"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="use-default-password" name="use-default-password">
                                <label class="custom-control-label text-primary" for="use-default-password"><small>Gunakan Default Password: TpqSmart123</small></label>
                            </div>
                            <span class="text-danger" id="error-confirm-password"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" onclick="closeModalAddUser()">Close</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modal-edit" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h4 class="modal-title">Edit User</h4>
            </div>
            <form id="formEditUser" onsubmit="event.preventDefault(); updateUser();">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="edit_IdAuthGroup" class="col-sm-3 col-form-label">Nama</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="edit_fullname" name="fullname" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_username" class="col-sm-3 col-form-label">Username</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="edit_username" name="username" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_password" class="col-sm-3 col-form-label">Password Baru</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="password" class="form-control" id="edit_password" name="password">
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePassword('edit_password')">
                                        <i class="fas fa-eye" id="eye-edit_password"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  row">
                        <label for="confirm-password" class="col-sm-3 col-form-label">Konfirmasi Password Baru</label>
                        <div class="col-sm-9">
                            <div class="input-group mb-2">
                                <input type="password" class="form-control" id="edit_confirm-password" name="confirm-password">
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePassword('edit_confirm-password')">
                                        <i class="fas fa-eye" id="eye-edit_confirm-password"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="edit_use-default-password" name="edit_use-default-password">
                                <label class="custom-control-label text-primary" for="edit_use-default-password"><small>Gunakan Default Password: TpqSmart123</small></label>
                            </div>
                            <span class="text-danger" id="edit-error-confirm-password"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" onclick="closeModalEditUser()">Close</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    // inisialisasi datatable
    initializeDataTableUmum("#tblUser", true);

    // Inisialisasi Select2 untuk dropdown Nama Guru
    $(document).ready(function() {
        // Fungsi untuk inisialisasi Select2
        function initSelect2Guru() {
            $('#IdNikGuru').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Nama Guru',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal-tambah')
            });
        }

        // Inisialisasi Select2 saat pertama kali
        initSelect2Guru();

        // Re-initialize Select2 saat modal dibuka untuk styling yang konsisten
        $('#modal-tambah').on('shown.bs.modal', function() {
            $('#IdNikGuru').select2('destroy');
            initSelect2Guru();
        });

        // Reset form dan Select2 saat modal ditutup
        $('#modal-tambah').on('hidden.bs.modal', function() {
            $('#formTambahUser').trigger('reset');
            $('#IdNikGuru').val('').trigger('change');
            $('#IdNikGuru').select2('destroy');
            // Reset error messages
            $('#error-confirm-password').text('');
            $('#confirm-password').removeClass('is-invalid');
        });
    });

    // fungsi delet menngunakan swal
    function confirmDelete(id) {
        const row = event.target.closest('tr');
        const nama = row.querySelector('td:nth-child(2)').innerText;
        const username = row.querySelector('td:nth-child(3)').innerText;
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            html: `Username : <strong>${username} - ${nama}</strong> akan dihapus permanen!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                fetch('<?= site_url('backend/user/delete/'); ?>' + id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Gagal!',
                            text: error.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
            }
        });
    }

    // buat fungsi tambah akunt menggunakan MyAuth yang sudah ada
    function tambahUserAkun() {
        // panggil modal
        $('#modal-tambah').modal('show');
    }

    // Fungsi untuk handle perubahan nama guru
    function handleGuruChange() {
        const IdNik = $('#IdNikGuru').val();

        // Jika kosong (Admin tidak pilih guru), skip
        if (!IdNik || IdNik === '') {
            return;
        }

        const namaGuru = $('#IdNikGuru').find(':selected').text();
        const namaParts = namaGuru.split(' ');
        let username = '';

        //check idNikGuru sudah ada atau belum di user show swal popup 
        fetch(`<?= site_url('backend/user/checkUserIdNikGuru/'); ?>${IdNik}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: `Guru dengan Nik ${IdNik} atas nama ${namaGuru} sudah memiliki akun!`,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    // Clear input username dan reset select
                    $('#username').val('');
                    $('#IdNikGuru').val('').trigger('change');
                    return;
                }

                // Pindahkan logika pembuatan username ke sini
                if (namaParts.length > 1) {
                    username = namaParts[0].charAt(0).toLowerCase() + namaParts[1].toLowerCase();
                } else {
                    username = namaParts[0].toLowerCase();
                }

                // Check ketersediaan username
                return fetch(`<?= site_url('backend/user/checkUsername/'); ?>${username}`);
            })
            .then(response => {
                if (response && response.json) {
                    return response.json();
                }
            })
            .then(data => {
                if (data && data.exists) {
                    const randomNum = Math.floor(Math.random() * 90 + 10);
                    username = username + randomNum;
                    return fetch(`<?= site_url('backend/user/checkUsername/'); ?>${username}`);
                }
                return Promise.resolve({
                    exists: false
                });
            })
            .then(response => {
                if (response.exists) {
                    const randomNum = Math.floor(Math.random() * 90 + 10);
                    username = username.replace(/\d+$/, randomNum.toString());
                }
                $('#username').val(username);
            });
    }

    // rekomendasi username dari selected nama guru - event change bekerja dengan Select2
    $(document).ready(function() {
        // Event handler untuk change (Select2 juga memicu event change standar)
        $(document).on('change', '#IdNikGuru', handleGuruChange);
    });

    // fungsi untuk menyimpan data user
    function simpanUser() {
        // ambil form data
        const form = document.getElementById('formTambahUser');
        const formData = new FormData(form);
        const IdNikGuru = formData.get('IdNikGuru');
        const fullnameManual = formData.get('fullname_manual');
        const username = formData.get('username');
        const password = formData.get('password');
        const confirmPassword = formData.get('confirm-password');

        // Cek apakah Admin (ada field fullname_manual)
        const isAdmin = document.getElementById('fullname_manual') !== null;

        // validasi data
        // Jika Admin, IdNikGuru bisa kosong (bisa pakai fullname_manual atau username)
        // Jika bukan Admin, IdNikGuru wajib diisi
        if (!isAdmin && (IdNikGuru === '' || username === '' || password === '')) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Semua data harus diisi!',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Jika Admin, minimal username dan password harus diisi
        if (isAdmin && (username === '' || password === '')) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Username dan Password harus diisi!',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        // validasi konfirmasi password
        if (password !== confirmPassword) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Password dan konfirmasi password tidak cocok!',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        // tampilkan loading
        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // kirim data ke server menggunakan FormData
        fetch('<?= site_url('backend/user/create'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Gagal!',
                    text: error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
    }

    //fungsi check confirm password
    $('#confirm-password').on('input', function() {
        const password = $('#password').val();
        const confirmPassword = $(this).val();
        const errorSpan = $('#error-confirm-password');

        if (password !== confirmPassword) {
            $(this).addClass('is-invalid');
            errorSpan.text('Password tidak cocok!');
        } else {
            $(this).removeClass('is-invalid');
            errorSpan.text('');
        }
    });

    function togglePassword(inputId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById('eye-' + inputId);

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    function closeModalAddUser() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang telah diisi akan hilang!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, tutup!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#formTambahUser').trigger('reset');
                // Reset Select2
                $('#IdNikGuru').val('').trigger('change');
                $('#modal-tambah').modal('hide');
            }
        });
    }

    $('#use-default-password').change(function() {
        if ($(this).is(':checked')) {
            $('#password').val('TpqSmart123');
            $('#confirm-password').val('TpqSmart123');
            $('#password, #confirm-password').prop('readonly', true);
            $('#confirm-password').removeClass('is-invalid');
            $('#error-confirm-password').text('');
        } else {
            $('#password, #confirm-password').val('').prop('readonly', false);
        }
    });

    function togglePasswordList(userId) {
        const passwordInput = document.getElementById('password-' + userId);
        const eyeIcon = document.getElementById('eye-password-' + userId);

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    function editUser(id) {
        // Reset form
        $('#formEditUser')[0].reset();

        // Tampilkan loading
        Swal.fire({
            title: 'Memuat Data...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Ambil data user
        fetch(`<?= site_url('backend/user/get/'); ?>${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#edit_id').val(data.user.id);
                    $('#edit_fullname').val(data.user.fullname);
                    $('#edit_IdNikGuru').val(data.user.id_nik_guru);
                    $('#edit_username').val(data.user.username);
                    $('#edit_active').prop('checked', data.user.active == 1);

                    $('#modal-edit').modal('show');
                    Swal.close();
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Gagal!',
                    text: error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
    }

    function updateUser() {
        const form = document.getElementById('formEditUser');
        const formData = new FormData(form);

        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('<?= site_url('backend/user/update'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Gagal!',
                    text: error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
    }

    function closeModalEditUser() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Perubahan yang belum disimpan akan hilang!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, tutup!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#formEditUser').trigger('reset');
                $('#modal-edit').modal('hide');
            }
        });
    }

    // Fungsi check confirm password untuk form edit
    $('#edit_confirm-password').on('input', function() {
        const password = $('#edit_password').val();
        const confirmPassword = $(this).val();
        const errorSpan = $('#edit-error-confirm-password');

        if (password !== confirmPassword) {
            $(this).addClass('is-invalid');
            errorSpan.text('Password tidak cocok!');
        } else {
            $(this).removeClass('is-invalid');
            errorSpan.text('');
        }
    });

    $('#edit_use-default-password').change(function() {
        if ($(this).is(':checked')) {
            $('#edit_password').val('TpqSmart123');
            $('#edit_confirm-password').val('TpqSmart123');
            $('#edit_password, #edit_confirm-password').prop('readonly', true);
            $('#edit_confirm-password').removeClass('is-invalid');
            $('#edit-error-confirm-password').text('');
        } else {
            $('#edit_password, #edit_confirm-password').val('').prop('readonly', false);
        }
    });

    function updateStatus(id, status) {
        const checkbox = document.getElementById('status' + id);
        const originalStatus = !status;

        // Ambil data dari row yang dipilih
        const row = checkbox.closest('tr');
        const nama = row.querySelector('td:nth-child(2)').innerText;
        const username = row.querySelector('td:nth-child(3)').innerText;

        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: `Anda akan ${status ? 'mengaktifkan' : 'menonaktifkan'} user:<br>
                  <strong>${nama}</strong> (${username})`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, update!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memperbarui Status...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('<?= site_url('backend/user/updateStatus'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id: id,
                            active: status ? 1 : 0
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Gagal!',
                            text: error.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        checkbox.checked = originalStatus;
                    });
            } else {
                checkbox.checked = originalStatus;
            }
        });
    }
</script>

<?= $this->endSection(); ?>