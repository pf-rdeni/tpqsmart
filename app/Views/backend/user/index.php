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
                                        <input type="checkbox" <?= $user['active'] == 1 ? 'checked' : ''; ?>>
                                    </td>
                                    <td><?= $user['Nama']; ?></td>
                                    <td><?= $user['username']; ?></td>
                                    <td><?= $user['NamaTpq']; ?></td>
                                    <td><?= $user['KelurahanDesa']; ?></td>
                                    <td><?= $user['kategori']; ?></td>
                                    <td>
                                        <button class="btn btn-warning sm-small" onclick="window.location.href='<?= site_url('user/edit/' . $user['id']); ?>'"><i class="fas fa-edit"></i> Edit</button>
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
                            <select class="form-control" id="IdAuthGroup" name="IdAuthGroup" required>
                                <option value="">Pilih Group</option>
                                <?php foreach ($dataAuthGroups as $group): ?>
                                    <option value="<?= $group['id']; ?>"><?= $group['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="IdNikGuru" class="col-sm-3 col-form-label">Nama Guru</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="IdNikGuru" name="IdNikGuru" required>
                                <option value="">Pilih Nama</option>
                                <?php foreach ($dataGuru as $guru): ?>
                                    <option value="<?= $guru['IdGuru']; ?>"><?= $guru['Nama']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
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
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePassword('confirm-password')">
                                        <i class="fas fa-eye" id="eye-confirm-password"></i>
                                    </span>
                                </div>
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

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    // inisialisasi datatable
    initializeDataTableUmum("#tblUser", true);

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

    // rekomendasi username dari selected nama guru
    $('#IdNikGuru').change(function() {
        const namaGuru = $(this).find(':selected').text();
        const namaParts = namaGuru.split(' ');
        let username = '';

        // get const dari value IdNikGuru
        const IdNik = $(this).val();
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
                    // Clear input username
                    $('#username').val('');
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
    });

    // fungsi untuk menyimpan data user
    function simpanUser() {
        // ambil form data
        const form = document.getElementById('formTambahUser');
        const formData = new FormData(form);
        const IdNikGuru = formData.get('IdNikGuru');
        const username = formData.get('username');
        const password = formData.get('password');
        const confirmPassword = formData.get('confirm-password');
        const nama = $('#IdNikGuru option:selected').text();

        // validasi data
        if (IdNikGuru === '' || username === '' || password === '') {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Semua data harus diisi!',
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
                $('#modal-tambah').modal('hide');
            }
        });
    }
</script>

<?= $this->endSection(); ?>