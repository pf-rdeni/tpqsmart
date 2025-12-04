<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pengaturan Group Kategori Rapor</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i>
                        <strong>Info:</strong> Fitur ini digunakan untuk menggabungkan beberapa materi dengan kategori yang sama menjadi satu materi baru. Nilai akan dihitung rata-rata dari semua materi yang digabungkan.
                    </div>

                    <!-- Section Setting -->
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cog"></i> Setting Group Kategori
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if (!$hasSetting) : ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Perhatian:</strong> Setting untuk TPQ ini belum ada di database.
                                    Silakan hubungi Admin untuk membuat setting terlebih dahulu di halaman <a href="<?= base_url('backend/tools/index') ?>" target="_blank">Tools Setting</a>.
                                </div>
                            <?php endif; ?>

                            <form id="formSetting">
                                <?= csrf_field() ?>
                                <input type="hidden" name="IdTpq" value="<?= htmlspecialchars($idTpq) ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="settingGroupKategoriNilai">
                                                <strong>Group Kategori Nilai</strong>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control" id="settingGroupKategoriNilai" name="GroupKategoriNilai" required <?= !$hasSetting ? 'disabled' : '' ?>>
                                                <option value="false" <?= !$groupKategoriNilaiEnabled ? 'selected' : '' ?>>Tidak Aktif</option>
                                                <option value="true" <?= $groupKategoriNilaiEnabled ? 'selected' : '' ?>>Aktif</option>
                                            </select>
                                            <small class="form-text text-muted">
                                                Aktifkan fitur pengelompokan kategori nilai di rapor. Jika aktif, materi dengan kategori yang sama akan digabung menjadi satu.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="settingGroupKategoriNilaiKelas">
                                                <strong>Kelas yang Menggunakan Grouping</strong>
                                            </label>
                                            <input type="text" class="form-control" id="settingGroupKategoriNilaiKelas"
                                                name="GroupKategoriNilaiKelas"
                                                value="<?= htmlspecialchars($groupKategoriNilaiKelas) ?>"
                                                placeholder="TPQ1, TPQ2, MDA1, MDA2 (kosongkan untuk semua kelas)"
                                                <?= !$hasSetting ? 'disabled' : '' ?>>
                                            <small class="form-text text-muted">
                                                Daftar kelas yang akan menggunakan pengelompokan kategori. Pisahkan dengan koma. Kosongkan untuk menerapkan ke semua kelas.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary" <?= !$hasSetting ? 'disabled' : '' ?>>
                                            <i class="fas fa-save"></i> Simpan Setting
                                        </button>
                                        <span class="ml-2 text-muted">
                                            <small>Setting ini akan diterapkan untuk ID TPQ: <strong><?= htmlspecialchars($idTpq) ?> - <?= htmlspecialchars($namaTpq) ?></strong></small>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php if (session()->getFlashdata('message')) : ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('message') ?>
                        </div>
                    <?php endif; ?>

                    <!-- Card Tambah Konfigurasi -->
                    <div class="card card-success card-outline mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus-circle"></i> Tambah Konfigurasi Group Kategori
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5><i class="fas fa-info-circle text-info"></i> Fungsi Tombol Tambah Baru</h5>
                                    <p class="text-muted">
                                        Tombol <strong>"Tambah Baru"</strong> digunakan untuk menambahkan konfigurasi baru yang akan mengelompokkan beberapa materi dengan kategori yang sama menjadi satu materi baru dengan nama yang berbeda.
                                    </p>
                                    <ul class="mb-0">
                                        <li><strong>Kategori Asal:</strong> Pilih kategori yang akan digabungkan (contoh: "Surah Pendek", "Do'a")</li>
                                        <li><strong>Nama Materi Baru:</strong> Masukkan nama baru untuk materi yang sudah digabungkan (contoh: "Tahfidz Surah", "Tahfidz Do'a")</li>
                                        <li><strong>Status:</strong> Pilih "Aktif" untuk mengaktifkan konfigurasi ini</li>
                                        <li><strong>Urutan:</strong> Tentukan urutan tampil di rapor (angka lebih kecil akan tampil lebih dulu)</li>
                                    </ul>
                                </div>
                                <div class="col-md-4 text-right">
                                    <?php
                                    // Cek apakah user adalah admin (IdTpq = 0)
                                    $isAdminUser = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));
                                    // Cek apakah user adalah operator
                                    $isOperator = in_groups('Operator');
                                    // Admin dan Operator bisa menambah
                                    $canAdd = $isAdminUser || $isOperator;
                                    ?>
                                    <?php if ($canAdd) : ?>
                                        <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modalAddGroup">
                                            <i class="fas fa-plus"></i> Tambah Baru
                                        </button>
                                    <?php else : ?>
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle"></i> Hubungi admin untuk menambah konfigurasi baru
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped" id="groupKategoriTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama TPQ</th>
                                <th>Kategori Asal</th>
                                <th>Nama Materi Baru</th>
                                <th>Status</th>
                                <th>Urutan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php if (!empty($configs)) : ?>
                                <?php foreach ($configs as $config) : ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td>
                                            <?php
                                            $idTpqConfig = $config['IdTpq'];
                                            $namaTpqConfig = $tpqMapping[$idTpqConfig] ?? $idTpqConfig;
                                            ?>
                                            <strong><?= htmlspecialchars($namaTpqConfig) ?></strong>
                                            <br>
                                            <small class="text-muted">ID: <?= htmlspecialchars($idTpqConfig) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($config['KategoriAsal']) ?></td>
                                        <td><?= htmlspecialchars($config['NamaMateriBaru']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $config['Status'] === 'Aktif' ? 'success' : 'secondary' ?>">
                                                <?= $config['Status'] ?>
                                            </span>
                                        </td>
                                        <td><?= $config['Urutan'] ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php
                                                // Cek apakah user adalah admin (IdTpq = 0)
                                                $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));
                                                // Cek apakah user adalah operator
                                                $isOperator = in_groups('Operator');

                                                // Untuk row dengan IdTpq = 'default', hanya admin yang bisa edit/delete
                                                // Operator bisa edit/delete untuk TPQ mereka sendiri
                                                $canEditDelete = ($config['IdTpq'] !== 'default') || $isAdmin;
                                                if ($isOperator && !$isAdmin) {
                                                    // Operator hanya bisa edit/delete untuk TPQ mereka sendiri
                                                    $canEditDelete = ($config['IdTpq'] === $idTpq);
                                                }
                                                ?>

                                                <?php if ($canEditDelete) : ?>
                                                    <button type="button" class="btn btn-warning btn-sm edit-group-btn"
                                                        data-id="<?= $config['id'] ?>"
                                                        data-idtpq="<?= $config['IdTpq'] ?>"
                                                        data-kategori-asal="<?= htmlspecialchars($config['KategoriAsal']) ?>"
                                                        data-nama-materi-baru="<?= htmlspecialchars($config['NamaMateriBaru']) ?>"
                                                        data-status="<?= $config['Status'] ?>"
                                                        data-urutan="<?= $config['Urutan'] ?>"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($canEditDelete) : ?>
                                                    <button type="button" class="btn btn-danger btn-sm delete-group-btn"
                                                        data-id="<?= $config['id'] ?>"
                                                        data-idtpq="<?= $config['IdTpq'] ?>"
                                                        data-kategori-asal="<?= htmlspecialchars($config['KategoriAsal']) ?>"
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
                                    <td colspan="7" class="text-center">Tidak ada data group kategori</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Group Kategori -->
<div class="modal fade" id="modalAddGroup" tabindex="-1" role="dialog" aria-labelledby="modalAddGroupLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddGroupLabel">Tambah Group Kategori Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddGroup">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addIdTpq">ID TPQ <span class="text-danger">*</span></label>
                        <?php
                        // Check if user is admin
                        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));
                        // Check if user is operator
                        $isOperator = in_groups('Operator');
                        ?>
                        <?php if ($isAdmin) : ?>
                            <select class="form-control" id="addIdTpq" name="IdTpq" required>
                                <option value="">-- Pilih ID TPQ --</option>
                                <option value="default">default (Template Default)</option>
                                <option value="0">0 (Admin)</option>
                                <?php if (!empty($listTpq)) : ?>
                                    <?php foreach ($listTpq as $tpq) : ?>
                                        <option value="<?= $tpq['IdTpq'] ?>"><?= $tpq['IdTpq'] ?> - <?= $tpq['NamaTpq'] ?? $tpq['IdTpq'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="form-text text-muted">Pilih ID TPQ dari dropdown atau gunakan 'default' untuk template, '0' untuk admin</small>
                        <?php else : ?>
                            <input type="text" class="form-control" id="addIdTpq" name="IdTpq" value="<?= htmlspecialchars($idTpq) ?>" readonly>
                            <small class="form-text text-muted">ID TPQ Anda: <?= $idTpq ?> (otomatis terisi)</small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="addKategoriAsal">Kategori Asal <span class="text-danger">*</span></label>
                        <select class="form-control" id="addKategoriAsal" name="KategoriAsal" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php if (!empty($kategoriOptions)) : ?>
                                <?php foreach ($kategoriOptions as $kategori) : ?>
                                    <option value="<?= htmlspecialchars($kategori) ?>"><?= htmlspecialchars($kategori) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted">Kategori yang akan digabungkan (contoh: Surah Pendek, Do'a)</small>
                    </div>
                    <div class="form-group">
                        <label for="addNamaMateriBaru">Nama Materi Baru <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addNamaMateriBaru" name="NamaMateriBaru" required placeholder="Tahfidz Surah, Tahfidz Do'a, dll">
                        <small class="form-text text-muted">Nama materi yang akan ditampilkan setelah digabungkan</small>
                    </div>
                    <div class="form-group">
                        <label for="addStatus">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="addStatus" name="Status" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="addUrutan">Urutan</label>
                        <input type="number" class="form-control" id="addUrutan" name="Urutan" value="0" min="0">
                        <small class="form-text text-muted">Urutan tampil di rapor (0 = pertama)</small>
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

<!-- Modal Edit Group Kategori -->
<div class="modal fade" id="modalEditGroup" tabindex="-1" role="dialog" aria-labelledby="modalEditGroupLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditGroupLabel">Edit Group Kategori</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditGroup">
                <?= csrf_field() ?>
                <input type="hidden" id="editId" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editIdTpq">ID TPQ</label>
                        <input type="text" class="form-control" id="editIdTpq" name="IdTpq" readonly>
                        <small class="form-text text-muted">ID TPQ tidak dapat diubah</small>
                    </div>
                    <div class="form-group">
                        <label for="editKategoriAsal">Kategori Asal <span class="text-danger">*</span></label>
                        <select class="form-control" id="editKategoriAsal" name="KategoriAsal" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php if (!empty($kategoriOptions)) : ?>
                                <?php foreach ($kategoriOptions as $kategori) : ?>
                                    <option value="<?= htmlspecialchars($kategori) ?>"><?= htmlspecialchars($kategori) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editNamaMateriBaru">Nama Materi Baru <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editNamaMateriBaru" name="NamaMateriBaru" required>
                    </div>
                    <div class="form-group">
                        <label for="editStatus">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="editStatus" name="Status" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editUrutan">Urutan</label>
                        <input type="number" class="form-control" id="editUrutan" name="Urutan" min="0">
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    (function() {
        // Pastikan jQuery sudah ter-load
        function initGroupKategoriScript() {
            if (typeof jQuery === 'undefined') {
                console.error('jQuery is not loaded! Retrying...');
                setTimeout(initGroupKategoriScript, 100);
                return;
            }

            // Gunakan jQuery dengan aman
            jQuery(document).ready(function($) {
                // Handle Setting Form Submit
                $('#formSetting').on('submit', function(e) {
                    e.preventDefault();

                    // Get CSRF token
                    var csrfName = '<?= csrf_token() ?>';
                    var csrfHash = '<?= csrf_hash() ?>';
                    var formData = $(this).serializeArray();

                    // Add CSRF token to form data
                    formData.push({
                        name: csrfName,
                        value: csrfHash
                    });

                    // Disable submit button
                    var submitBtn = $(this).find('button[type="submit"]');
                    var originalBtnText = submitBtn.html();
                    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

                    $.ajax({
                        url: '<?= base_url('backend/raporGroupKategori/saveSetting') ?>',
                        type: 'POST',
                        data: $.param(formData),
                        dataType: 'json',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            submitBtn.prop('disabled', false).html(originalBtnText);

                            if (response.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Reload untuk update setting yang ditampilkan
                                    location.reload();
                                });
                            } else {
                                var errorMessage = response.message || 'Terjadi kesalahan';
                                if (response.errors) {
                                    var errorList = Object.values(response.errors).join('<br>');
                                    errorMessage += '<br><br>' + errorList;
                                }
                                Swal.fire({
                                    title: 'Error!',
                                    html: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            submitBtn.prop('disabled', false).html(originalBtnText);
                            console.error('AJAX Error:', xhr);
                            var errorMessage = 'Terjadi kesalahan saat memproses request: ' + error;
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseText) {
                                try {
                                    var response = JSON.parse(xhr.responseText);
                                    if (response.message) {
                                        errorMessage = response.message;
                                    }
                                } catch (e) {
                                    errorMessage = xhr.responseText.substring(0, 200);
                                }
                            }
                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });
                // Handle Add Form Submit
                $('#formAddGroup').on('submit', function(e) {
                    e.preventDefault();

                    // Get CSRF token
                    var csrfName = '<?= csrf_token() ?>';
                    var csrfHash = '<?= csrf_hash() ?>';
                    var formData = $(this).serializeArray();

                    // Add CSRF token to form data
                    formData.push({
                        name: csrfName,
                        value: csrfHash
                    });

                    $.ajax({
                        url: '<?= base_url('backend/raporGroupKategori/save') ?>',
                        type: 'POST',
                        data: $.param(formData),
                        dataType: 'json',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                var errorMessage = response.message || 'Terjadi kesalahan';
                                if (response.errors) {
                                    var errorList = Object.values(response.errors).join('<br>');
                                    errorMessage += '<br><br>' + errorList;
                                }
                                Swal.fire({
                                    title: 'Error!',
                                    html: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', xhr);
                            var errorMessage = 'Terjadi kesalahan saat memproses request: ' + error;
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseText) {
                                try {
                                    var response = JSON.parse(xhr.responseText);
                                    if (response.message) {
                                        errorMessage = response.message;
                                    }
                                } catch (e) {
                                    errorMessage = xhr.responseText.substring(0, 200);
                                }
                            }
                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });

                // Handle Edit Button Click
                $(document).on('click', '.edit-group-btn', function() {
                    const id = $(this).data('id');
                    const idTpq = $(this).data('idtpq');
                    const kategoriAsal = $(this).data('kategori-asal');
                    const namaMateriBaru = $(this).data('nama-materi-baru');
                    const status = $(this).data('status');
                    const urutan = $(this).data('urutan');

                    $('#editId').val(id);
                    $('#editIdTpq').val(idTpq);
                    $('#editKategoriAsal').val(kategoriAsal);
                    $('#editNamaMateriBaru').val(namaMateriBaru);
                    $('#editStatus').val(status);
                    $('#editUrutan').val(urutan);

                    $('#modalEditGroup').modal('show');
                });

                // Handle Edit Form Submit
                $('#formEditGroup').on('submit', function(e) {
                    e.preventDefault();
                    const id = $('#editId').val();

                    // Get CSRF token
                    var csrfName = '<?= csrf_token() ?>';
                    var csrfHash = '<?= csrf_hash() ?>';
                    var formData = $(this).serializeArray();

                    // Add CSRF token to form data
                    formData.push({
                        name: csrfName,
                        value: csrfHash
                    });

                    $.ajax({
                        url: '<?= base_url('backend/raporGroupKategori/update') ?>/' + id,
                        type: 'POST',
                        data: $.param(formData),
                        dataType: 'json',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                var errorMessage = response.message || 'Terjadi kesalahan';
                                if (response.errors) {
                                    var errorList = Object.values(response.errors).join('<br>');
                                    errorMessage += '<br><br>' + errorList;
                                }
                                Swal.fire({
                                    title: 'Error!',
                                    html: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', xhr);
                            var errorMessage = 'Terjadi kesalahan saat memproses request: ' + error;
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseText) {
                                try {
                                    var response = JSON.parse(xhr.responseText);
                                    if (response.message) {
                                        errorMessage = response.message;
                                    }
                                } catch (e) {
                                    errorMessage = xhr.responseText.substring(0, 200);
                                }
                            }
                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });

                // Handle Delete Button Click
                $(document).on('click', '.delete-group-btn', function() {
                    const id = $(this).data('id');
                    const idTpq = $(this).data('idtpq');
                    const kategoriAsal = $(this).data('kategori-asal');

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: 'Data group kategori "' + kategoriAsal + '" untuk ID TPQ "' + idTpq + '" akan dihapus!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Get CSRF token
                            var csrfName = '<?= csrf_token() ?>';
                            var csrfHash = '<?= csrf_hash() ?>';

                            $.ajax({
                                url: '<?= base_url('backend/raporGroupKategori/delete') ?>/' + id,
                                type: 'POST',
                                data: {
                                    [csrfName]: csrfHash
                                },
                                dataType: 'json',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire({
                                            title: 'Berhasil!',
                                            text: response.message,
                                            icon: 'success',
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: response.message,
                                            icon: 'error',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Terjadi kesalahan saat memproses request: ' + error,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            });
                        }
                    });
                });
            });
        }

        // Jalankan init saat DOM ready atau langsung jika jQuery sudah ada
        if (typeof jQuery !== 'undefined') {
            initGroupKategoriScript();
        } else {
            // Tunggu jQuery dimuat
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initGroupKategoriScript);
            } else {
                setTimeout(initGroupKategoriScript, 100);
            }
        }
    })();
</script>
<?= $this->endSection(); ?>