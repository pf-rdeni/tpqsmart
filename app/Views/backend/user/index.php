<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Informasi Proses Flow -->
            <div class="card card-info card-outline collapsed-card mb-3">
                <div class="card-header bg-info">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Informasi Proses Data User
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <h5><i class="fas fa-list-ol"></i> Cara Menggunakan Halaman Data User:</h5>
                            <ol class="mb-3">
                                <li class="mb-2">
                                    <strong>Memahami Tampilan Halaman</strong>
                                    <ul class="mt-1">
                                        <li>Halaman ini menampilkan <strong>daftar semua user/akun</strong> yang terdaftar di sistem</li>
                                        <li>Tabel menampilkan informasi: Status (aktif/tidak aktif), Nama, Username, Password, TPQ, Kelurahan/Desa, Kategori, dan Aksi</li>
                                        <li>Kolom <strong>Password</strong> menampilkan password terenkripsi (klik ikon mata untuk melihat)</li>
                                        <li>Gunakan fitur <strong>search</strong> pada tabel untuk mencari user tertentu</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    <strong>Menambah User Baru</strong>
                                    <ul class="mt-1">
                                        <li>Klik tombol <strong>"Tambah User"</strong> di bagian atas halaman</li>
                                        <li>Pilih <strong>Group</strong> dari dropdown (Admin, Operator, Guru, dll)</li>
                                        <li>Untuk <strong>Admin</strong>: bisa input nama manual atau pilih dari daftar guru</li>
                                        <li>Untuk <strong>non-Admin</strong>: wajib pilih nama dari daftar guru</li>
                                        <li>Jika memilih guru, <strong>Username</strong> akan otomatis terisi</li>
                                        <li>Masukkan <strong>Password</strong> atau centang <strong>"Gunakan Default Password: TpqSmart123"</strong></li>
                                        <li>Masukkan <strong>Konfirmasi Password</strong> (harus sama dengan password)</li>
                                        <li>Klik <strong>"Simpan"</strong> untuk membuat akun baru</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    <strong>Mengubah Password User</strong>
                                    <ul class="mt-1">
                                        <li>Klik tombol <strong>"Edit"</strong> pada baris user yang ingin diubah passwordnya</li>
                                        <li>Nama dan Username <strong>tidak bisa diubah</strong> (sudah terkunci)</li>
                                        <li>Masukkan <strong>Password Baru</strong> atau centang <strong>"Gunakan Default Password"</strong></li>
                                        <li>Masukkan <strong>Konfirmasi Password Baru</strong> (harus sama dengan password baru)</li>
                                        <li>Jika tidak ingin mengubah password, <strong>biarkan kosong</strong></li>
                                        <li>Klik <strong>"Update"</strong> untuk menyimpan perubahan</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    <strong>Mengaktifkan/Menonaktifkan User</strong>
                                    <ul class="mt-1">
                                        <li>Gunakan <strong>checkbox</strong> pada kolom "Status" untuk mengaktifkan/nonaktifkan user</li>
                                        <li>Jika checkbox <strong>dicentang</strong>, user bisa login ke sistem</li>
                                        <li>Jika checkbox <strong>tidak dicentang</strong>, user tidak bisa login (dinonaktifkan)</li>
                                        <li>Sistem akan meminta <strong>konfirmasi</strong> sebelum mengubah status</li>
                                        <li>Perubahan status akan langsung terupdate</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    <strong>Menghapus User</strong>
                                    <ul class="mt-1">
                                        <li>Klik tombol <strong>"Hapus"</strong> (ikon tempat sampah) pada baris user yang ingin dihapus</li>
                                        <li>Sistem akan meminta <strong>konfirmasi</strong> sebelum menghapus</li>
                                        <li>Setelah dihapus, user tidak bisa login lagi dan data akan dihapus permanen</li>
                                        <li>Pastikan user tidak sedang digunakan untuk proses penting sebelum menghapus</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    <strong>Tips dan Saran</strong>
                                    <ul class="mt-1">
                                        <li>Gunakan <strong>Default Password</strong> untuk memudahkan, user bisa mengubahnya nanti</li>
                                        <li>Jika guru sudah memiliki akun, sistem akan <strong>memberi peringatan</strong> saat mencoba membuat akun baru</li>
                                        <li>Username biasanya dibuat <strong>otomatis</strong> dari nama guru (huruf pertama nama depan + nama belakang)</li>
                                        <li>Jika username sudah ada, sistem akan <strong>menambahkan angka</strong> secara otomatis</li>
                                        <li>Gunakan fitur <strong>search</strong> pada tabel untuk mencari user dengan cepat</li>
                                    </ul>
                                </li>
                            </ol>

                            <div class="alert alert-warning mb-0">
                                <h5><i class="icon fas fa-exclamation-triangle"></i> Catatan Penting:</h5>
                                <ul class="mb-0">
                                    <li>Password default adalah <strong>TpqSmart123</strong> (disarankan untuk diubah setelah login pertama)</li>
                                    <li>User yang <strong>dinonaktifkan</strong> tidak bisa login, tapi data tetap tersimpan</li>
                                    <li>User yang <strong>dihapus</strong> akan hilang permanen dan tidak bisa dikembalikan</li>
                                    <li>Pastikan <strong>password dan konfirmasi password</strong> sama sebelum menyimpan</li>
                                    <li>Halaman ini hanya bisa diakses oleh <strong>Admin</strong> dan <strong>Operator</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modern Stat Cards User Overview -->
            <div class="row mb-3">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info shadow-sm mb-2">
                        <div class="inner">
                            <h3><?= number_format($stats['totalUsers'] ?? 0); ?></h3>
                            <p>Total Akun Terdaftar</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning shadow-sm mb-2">
                        <div class="inner text-dark">
                            <h3><?= number_format($stats['totalMultiRoles'] ?? 0); ?></h3>
                            <p>Pengguna Multi-Peran</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success shadow-sm mb-2">
                        <div class="inner">
                            <h3><?= number_format($stats['totalGuruStaf'] ?? 0); ?></h3>
                            <p>Guru & Tenaga Kependidikan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary shadow-sm mb-2">
                        <div class="inner">
                            <h3><?= number_format($stats['totalSantri'] ?? 0); ?></h3>
                            <p>Akun Santri Aktif</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah">
                            <i class="fas fa-user-plus mr-1"></i> Tambah User
                        </button>
                        <button type="button" class="btn btn-success ml-2" data-toggle="modal" data-target="#modal-generate-kelas">
                            <i class="fas fa-users-cog mr-1"></i> Generate User Per Kelas
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="userTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bold" id="guru-tab" data-toggle="tab" href="#guru" role="tab" aria-controls="guru" aria-selected="true">
                                <i class="fas fa-user-tie text-primary mr-1"></i> Guru & Tenaga Kependidikan / Staf
                            </a>
                        </li>
                        <?php foreach ($dataKelas as $index => $kelas): ?>
                            <li class="nav-item">
                                <a class="nav-link" id="kelas-<?= $kelas['IdKelas']; ?>-tab" data-toggle="tab" href="#kelas-<?= $kelas['IdKelas']; ?>" role="tab" aria-controls="kelas-<?= $kelas['IdKelas']; ?>" aria-selected="false">
                                    <i class="fas fa-users mr-1"></i> <?= esc($kelas['NamaKelas']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" id="userTabContent">
                        <!-- Tab Guru & Staf -->
                        <div class="tab-pane fade show active" id="guru" role="tabpanel" aria-labelledby="guru-tab">
                            <div class="mt-3">
                                <!-- Filter Toolbar -->
                                <div class="row align-items-center mb-3 bg-light p-2 rounded border mx-0">
                                    <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                                        <label class="small text-muted mb-1 font-weight-bold"><i class="fas fa-filter text-primary mr-1"></i> Filter Berdasarkan Peran:</label>
                                        <select id="filterRole" class="form-control form-control-sm">
                                            <option value="">-- Semua Peran --</option>
                                            <option value="Multi-Peran">⭐ Khusus Multi-Peran (> 1 Peran)</option>
                                            <option value="Guru">👨‍🏫 Guru Pengajar</option>
                                            <option value="Sekretaris Lembaga">🏫 Sekretaris Lembaga</option>
                                            <option value="Sekretaris FKPQ">🏛️ Sekretaris FKPQ</option>
                                            <option value="Operator">💻 Operator Lembaga</option>
                                            <option value="Bendahara">💰 Bendahara</option>
                                            <?php if ($isAdmin): ?>
                                                <option value="Admin">🛡️ Administrator</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <?php if ($isAdmin && !empty($listTpq)): ?>
                                    <div class="col-md-4 col-sm-6 mb-2 mb-md-0">
                                        <label class="small text-muted mb-1 font-weight-bold"><i class="fas fa-mosque text-success mr-1"></i> Filter Lembaga TPQ:</label>
                                        <select id="filterTpq" class="form-control form-control-sm select2">
                                            <option value="">-- Semua Lembaga TPQ --</option>
                                            <?php foreach ($listTpq as $tpq): ?>
                                                <option value="<?= esc($tpq['NamaTpq']); ?>"><?= esc($tpq['NamaTpq']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-md-<?= ($isAdmin && !empty($listTpq)) ? '4' : '8'; ?> text-md-right mt-2 mt-md-0">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnResetFilter">
                                            <i class="fas fa-sync-alt mr-1"></i> Reset Filter
                                        </button>
                                    </div>
                                </div>

                                <table id="tblUserGuru" class="table table-bordered table-striped table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">Status</th>
                                            <th>Pengguna / Guru</th>
                                            <th>Username</th>
                                            <th>Lembaga TPQ</th>
                                            <th>Hak Akses & Penugasan Peran</th>
                                            <th style="width: 140px;">Password</th>
                                            <th style="width: 160px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($userDataGuru as $user): ?>
                                            <tr>
                                                <td class="text-center align-middle">
                                                    <input type="checkbox" id="status<?= $user['id']; ?>" <?= $user['active'] == 1 ? 'checked' : ''; ?>
                                                        onchange="updateStatus(<?= $user['id']; ?>, this.checked)">
                                                </td>
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center">
                                                        <div class="mr-2">
                                                            <span class="img-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                                <i class="fas fa-user-circle fa-2x text-light"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <span class="font-weight-bold text-dark"><?= esc($user['nama']); ?></span>
                                                            <?php if (!empty($user['nik'])): ?>
                                                                <br><small class="text-muted"><i class="fas fa-id-card mr-1"></i>NIK: <?= esc($user['nik']); ?></small>
                                                            <?php endif; ?>
                                                            <?php if (!empty($user['email'])): ?>
                                                                <br><small class="text-muted"><i class="fas fa-envelope mr-1"></i><?= esc($user['email']); ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge badge-light border text-dark font-weight-normal px-2 py-1">
                                                        <i class="fas fa-user mr-1 text-secondary"></i><?= esc($user['username']); ?>
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    <?php if (!empty($user['namaTpq'])): ?>
                                                        <strong><?= esc($user['namaTpq']); ?></strong>
                                                        <?php if (!empty($user['kelurahanDesa'])): ?>
                                                            <br><small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i><?= esc($user['kelurahanDesa']); ?></small>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="badge badge-light border text-muted"><i class="fas fa-shield-alt mr-1"></i>Tingkat Pusat / FKPQ</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="align-middle">
                                                    <?php
                                                    $rolesList = !empty($user['group_names']) ? explode(', ', $user['group_names']) : [];
                                                    foreach ($rolesList as $rName):
                                                        $badgeClass = 'badge-secondary';
                                                        $icon = 'fas fa-user';
                                                        $roleLabel = $rName;
                                                        switch ($rName) {
                                                            case 'Admin':
                                                                $badgeClass = 'badge-danger';
                                                                $icon = 'fas fa-shield-alt';
                                                                $roleLabel = 'Admin';
                                                                break;
                                                            case 'Sekretaris FKPQ':
                                                                $badgeClass = 'badge-danger';
                                                                $icon = 'fas fa-landmark';
                                                                $roleLabel = 'Sekretaris FKPQ';
                                                                break;
                                                            case 'Operator':
                                                                $badgeClass = 'badge-success';
                                                                $icon = 'fas fa-laptop-code';
                                                                $roleLabel = 'Operator';
                                                                break;
                                                            case 'Sekretaris':
                                                            case 'Sekretaris Lembaga':
                                                                $badgeClass = 'badge-warning text-dark';
                                                                $icon = 'fas fa-envelope-open-text';
                                                                $roleLabel = 'Sekretaris Lembaga';
                                                                break;
                                                            case 'Guru':
                                                                $badgeClass = 'badge-primary';
                                                                $icon = 'fas fa-chalkboard-teacher';
                                                                $roleLabel = 'Guru';
                                                                break;
                                                            case 'Bendahara':
                                                                $badgeClass = 'badge-info';
                                                                $icon = 'fas fa-money-bill-wave';
                                                                $roleLabel = 'Bendahara';
                                                                break;
                                                            case 'Juri':
                                                            case 'JuriSertifikasi':
                                                            case 'JuriLomba':
                                                                $badgeClass = 'badge-dark';
                                                                $icon = 'fas fa-gavel';
                                                                break;
                                                            case 'Panitia':
                                                            case 'PanitiaSertifikasi':
                                                            case 'PanitiaLomba':
                                                                $badgeClass = 'badge-secondary';
                                                                $icon = 'fas fa-tasks';
                                                                break;
                                                        }
                                                    ?>
                                                        <span class="badge <?= $badgeClass; ?> px-2 py-1 mb-1 mr-1">
                                                            <i class="<?= $icon; ?> mr-1"></i><?= esc($roleLabel); ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                    <?php if (($user['total_groups'] ?? 0) > 1): ?>
                                                        <span class="badge badge-pill badge-warning border border-dark text-dark font-weight-bold px-2 py-1 mb-1 mr-1" title="Pengguna memiliki <?= $user['total_groups']; ?> peran aktif">
                                                            <i class="fas fa-layer-group mr-1"></i>Multi-Peran (<?= $user['total_groups']; ?>)
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="input-group input-group-sm">
                                                        <input type="password" class="form-control" value="<?= esc($user['password_hash']); ?>" readonly id="password-<?= $user['id']; ?>">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" onclick="togglePasswordList(<?= $user['id']; ?>)" style="cursor: pointer;">
                                                                <i class="fas fa-eye" id="eye-password-<?= $user['id']; ?>"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle" style="white-space: nowrap;">
                                                    <button class="btn btn-primary btn-xs mb-1" onclick="manageUserGroups(<?= $user['id']; ?>)" title="Atur Hak Akses & Penugasan Peran">
                                                        <i class="fas fa-shield-alt mr-1"></i> Hak Akses
                                                    </button>
                                                    <button class="btn btn-warning btn-xs mb-1" onclick="editUser(<?= $user['id']; ?>)" title="Edit User, Peran & Password">
                                                        <i class="fas fa-edit mr-1"></i> Edit User
                                                    </button>
                                                    <button class="btn btn-danger btn-xs mb-1" onclick="confirmDelete(<?= $user['id']; ?>)" title="Hapus Akun Pengguna">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab Santri per Kelas -->
                        <?php foreach ($dataKelas as $index => $kelas): ?>
                            <?php
                            $kelasData = $userDataSantriPerKelas[$kelas['IdKelas']] ?? null;
                            $santriUsers = $kelasData['users'] ?? [];
                            ?>
                            <div class="tab-pane fade" id="kelas-<?= $kelas['IdKelas']; ?>" role="tabpanel" aria-labelledby="kelas-<?= $kelas['IdKelas']; ?>-tab">
                                <div class="mt-3">
                                    <table id="tblUserKelas-<?= $kelas['IdKelas']; ?>" class="table table-bordered table-striped">
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
                                            <?php foreach ($santriUsers as $user): ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" id="status<?= $user['id']; ?>" <?= $user['active'] == 1 ? 'checked' : ''; ?>
                                                            onchange="updateStatus(<?= $user['id']; ?>, this.checked)">
                                                    </td>
                                                    <td><?= esc($user['nama']); ?></td>
                                                    <td><?= esc($user['username']); ?></td>
                                                    <td>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control" value="<?= esc($user['password_hash']); ?>" readonly id="password-<?= $user['id']; ?>">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text" onclick="togglePasswordList(<?= $user['id']; ?>)">
                                                                    <i class="fas fa-eye" id="eye-password-<?= $user['id']; ?>"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?= esc($user['namaTpq']); ?></td>
                                                    <td><?= esc($user['kelurahanDesa']); ?></td>
                                                    <td><?= esc($user['kategori']); ?></td>
                                                    <td>
                                                        <button class="btn btn-warning btn-sm" onclick="editUser(<?= $user['id']; ?>)">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $user['id']; ?>)">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($santriUsers)): ?>
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">Tidak ada data user santri untuk kelas ini</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
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
                            <select class="form-control" id="IdAuthGroup" name="IdAuthGroup" required onchange="handleGroupChange()">
                                <option value="">Pilih Group</option>
                                <?php foreach ($dataAuthGroups as $group): ?>
                                    <option value="<?= $group['id']; ?>">
                                        <?= $group['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Form untuk Guru -->
                    <div id="form-guru">
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
                                            <option value="<?= $guru['IdGuru']; ?>"><?= esc($guru['Nama']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Bukan Admin, wajib pilih guru -->
                            <div class="form-group row">
                                <label for="IdNikGuru" class="col-sm-3 col-form-label">Nama Guru</label>
                                <div class="col-sm-9">
                                    <select class="form-control select2" id="IdNikGuru" name="IdNikGuru" style="width: 100%;">
                                        <option value="">Pilih Nama</option>
                                        <?php foreach ($dataGuru as $guru): ?>
                                            <option value="<?= $guru['IdGuru']; ?>"><?= esc($guru['Nama']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Multi-Role Selection untuk Guru & Staf -->
                        <div class="form-group row" id="section-role-assignment">
                            <label class="col-sm-3 col-form-label">
                                Hak Akses & Peran
                                <small class="d-block text-muted font-weight-normal">Bisa pilih lebih dari satu peran sekaligus</small>
                            </label>
                            <div class="col-sm-9">
                                <div class="card card-outline card-primary shadow-none border bg-light mb-0">
                                    <div class="card-body p-2">
                                        <div class="text-xs font-weight-bold text-uppercase text-primary mb-1">
                                            <i class="fas fa-school mr-1"></i> Peran Lembaga TPQ
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <div class="custom-control custom-checkbox border rounded p-2 bg-white h-100">
                                                    <input type="checkbox" class="custom-control-input create-role-cb" id="create_grp_3" name="groups[]" value="3" checked>
                                                    <label class="custom-control-label font-weight-bold d-block cursor-pointer" for="create_grp_3">
                                                        <span class="badge badge-primary mr-1"><i class="fas fa-chalkboard-teacher"></i></span> Guru Pengajar
                                                        <small class="d-block text-muted font-weight-normal mt-1">Mengajar & input nilai raport</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <div class="custom-control custom-checkbox border rounded p-2 bg-white h-100">
                                                    <input type="checkbox" class="custom-control-input create-role-cb" id="create_grp_12" name="groups[]" value="12">
                                                    <label class="custom-control-label font-weight-bold d-block cursor-pointer" for="create_grp_12">
                                                        <span class="badge badge-warning text-dark mr-1"><i class="fas fa-envelope-open-text"></i></span> Sekretaris Lembaga
                                                        <small class="d-block text-muted font-weight-normal mt-1">Persuratan & administrasi lembaga</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <div class="custom-control custom-checkbox border rounded p-2 bg-white h-100">
                                                    <input type="checkbox" class="custom-control-input create-role-cb" id="create_grp_13" name="groups[]" value="13">
                                                    <label class="custom-control-label font-weight-bold d-block cursor-pointer" for="create_grp_13">
                                                        <span class="badge badge-info mr-1"><i class="fas fa-money-bill-wave"></i></span> Bendahara Lembaga
                                                        <small class="d-block text-muted font-weight-normal mt-1">Keuangan lembaga TPQ</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if ($isAdmin): ?>
                                        <div class="text-xs font-weight-bold text-uppercase text-danger mt-2 mb-1">
                                            <i class="fas fa-landmark mr-1"></i> Peran FKPQ & Sistem (Admin Only)
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <div class="custom-control custom-checkbox border rounded p-2 bg-white h-100">
                                                    <input type="checkbox" class="custom-control-input create-role-cb" id="create_grp_14" name="groups[]" value="14">
                                                    <label class="custom-control-label font-weight-bold d-block cursor-pointer" for="create_grp_14">
                                                        <span class="badge badge-danger mr-1"><i class="fas fa-landmark"></i></span> Sekretaris FKPQ
                                                        <small class="d-block text-muted font-weight-normal mt-1">Persuratan FKPQ kabupaten</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <div class="custom-control custom-checkbox border rounded p-2 bg-white h-100">
                                                    <input type="checkbox" class="custom-control-input create-role-cb" id="create_grp_2" name="groups[]" value="2">
                                                    <label class="custom-control-label font-weight-bold d-block cursor-pointer" for="create_grp_2">
                                                        <span class="badge badge-success mr-1"><i class="fas fa-laptop-code"></i></span> Operator Lembaga
                                                        <small class="d-block text-muted font-weight-normal mt-1">Kelola data lembaga</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <div class="custom-control custom-checkbox border rounded p-2 bg-white h-100">
                                                    <input type="checkbox" class="custom-control-input create-role-cb" id="create_grp_1" name="groups[]" value="1">
                                                    <label class="custom-control-label font-weight-bold d-block cursor-pointer" for="create_grp_1">
                                                        <span class="badge badge-danger mr-1"><i class="fas fa-shield-alt"></i></span> Administrator
                                                        <small class="d-block text-muted font-weight-normal mt-1">Akses penuh sistem</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form untuk Santri -->
                    <div id="form-santri" style="display: none;">
                        <div class="form-group row">
                            <label for="IdKelasSantri" class="col-sm-3 col-form-label">Kelas</label>
                            <div class="col-sm-9">
                                <select class="form-control select2" id="IdKelasSantri" name="IdKelasSantri" style="width: 100%;">
                                    <option value="">Pilih Kelas</option>
                                    <?php foreach ($dataKelasForDropdown as $kelas): ?>
                                        <option value="<?= esc($kelas['IdKelas']); ?>">
                                            <?= esc($kelas['NamaKelas']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="IdNikSantri" class="col-sm-3 col-form-label">Nama Santri</label>
                            <div class="col-sm-9">
                                <select class="form-control select2" id="IdNikSantri" name="IdNikSantri" style="width: 100%;" disabled>
                                    <option value="">Pilih Kelas terlebih dahulu</option>
                                </select>
                            </div>
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
                            <div class="input-group mb-2">
                                <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePassword('confirm-password')">
                                        <i class="fas fa-eye" id="eye-confirm-password"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="custom-control custom-checkbox" id="checkbox-default-password-guru">
                                <input type="checkbox" class="custom-control-input" id="use-default-password" name="use-default-password">
                                <label class="custom-control-label text-primary" for="use-default-password"><small>Gunakan Default Password: TpqSmart123</small></label>
                            </div>
                            <div class="custom-control custom-checkbox" id="checkbox-default-password-santri" style="display: none;">
                                <input type="checkbox" class="custom-control-input" id="use-default-password-santri" name="use-default-password-santri">
                                <label class="custom-control-label text-primary" for="use-default-password-santri"><small id="label-default-password-santri">Gunakan Default Password: SmartSantriTpq<?php
                                                                                                                                                                                                        $idTpq = session()->get('IdTpq') ?? 0;
                                                                                                                                                                                                        $idTpqStr = (string)$idTpq;
                                                                                                                                                                                                        $idTpqLast3 = strlen($idTpqStr) > 3 ? substr($idTpqStr, -3) : str_pad($idTpqStr, 3, '0', STR_PAD_LEFT);
                                                                                                                                                                                                        echo $idTpqLast3;
                                                                                                                                                                                                        ?></small></label>
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

<!-- Modal Generate User Per Kelas -->
<div class="modal fade" id="modal-generate-kelas" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h4 class="modal-title">Generate User Per Kelas</h4>
            </div>
            <form id="formGenerateKelas" onsubmit="event.preventDefault(); generateUserPerKelas();">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Fitur ini akan membuat user account untuk semua santri di kelas yang dipilih yang belum memiliki user account. Password default akan digunakan untuk semua user yang dibuat.
                    </div>
                    <div class="form-group row">
                        <label for="IdKelasGenerate" class="col-sm-3 col-form-label">Pilih Kelas</label>
                        <div class="col-sm-9">
                            <select class="form-control select2" id="IdKelasGenerate" name="IdKelasGenerate" style="width: 100%;" required>
                                <option value="">Pilih Kelas</option>
                                <?php foreach ($dataKelasForDropdown as $kelas): ?>
                                    <option value="<?= esc($kelas['IdKelas']); ?>">
                                        <?= esc($kelas['NamaKelas']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Hanya kelas yang memiliki santri tanpa user account yang ditampilkan</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Password Default</label>
                        <div class="col-sm-9">
                            <div class="alert alert-warning mb-0">
                                <strong>SmartSantriTpq<?php
                                                        $idTpq = session()->get('IdTpq') ?? 0;
                                                        $idTpqStr = (string)$idTpq;
                                                        $idTpqLast3 = strlen($idTpqStr) > 3 ? substr($idTpqStr, -3) : str_pad($idTpqStr, 3, '0', STR_PAD_LEFT);
                                                        echo $idTpqLast3;
                                                        ?></strong>
                                <br><small>Password ini akan digunakan untuk semua user yang dibuat</small>
                            </div>
                        </div>
                    </div>
                    <div id="info-santri-kelas" style="display: none;">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Santri yang akan dibuat user:</label>
                            <div class="col-sm-9">
                                <div id="list-santri-kelas" class="border p-2" style="max-height: 200px; overflow-y: auto;">
                                    <!-- Daftar santri akan dimuat di sini -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-users"></i> Generate User
                    </button>
                </div>
            </form>
        </div>
    </div>
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
                    <!-- Pengaturan Peran / Hak Akses di Edit User -->
                    <div class="form-group row" id="edit_roles_section">
                        <label class="col-sm-3 col-form-label">
                            Hak Akses & Peran
                            <small class="d-block text-muted font-weight-normal">Centang peran yang aktif</small>
                        </label>
                        <div class="col-sm-9">
                            <div id="edit_groups_container" class="card card-outline card-secondary shadow-none border bg-light mb-0 p-2">
                                <div class="text-center py-2 text-muted small">
                                    <i class="fas fa-spinner fa-spin mr-1"></i> Memuat hak akses...
                                </div>
                            </div>
                            <small class="form-text text-muted">Pengguna dapat memiliki lebih dari satu peran.</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="edit_password" class="col-sm-3 col-form-label">Password Baru</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="password" class="form-control" id="edit_password" name="password" placeholder="Kosongkan jika tidak diubah">
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePassword('edit_password')">
                                        <i class="fas fa-eye" id="eye-edit_password"></i>
                                    </span>
                                </div>
                            </div>
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti password pengguna.</small>
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

<!-- Modal Hak Akses & Penugasan Peran -->
<div class="modal fade" id="modal-hak-akses" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-navy text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-shield-alt mr-2 text-warning"></i> Pengaturan Hak Akses & Penugasan Peran
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formHakAkses" onsubmit="event.preventDefault(); saveUserGroups();">
                <input type="hidden" id="hak_akses_user_id" name="user_id">
                <div class="modal-body p-4">
                    <!-- Ringkasan User Info -->
                    <div class="card card-outline card-secondary mb-3 shadow-none bg-light border">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <div class="text-muted small">Nama Pengguna:</div>
                                    <div class="h6 font-weight-bold text-dark mb-1" id="hak_akses_nama">-</div>
                                    <div class="text-muted small">Username:</div>
                                    <span class="badge badge-light border text-dark font-weight-normal px-2 py-1" id="hak_akses_username">-</span>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small">Asal Lembaga TPQ:</div>
                                    <div class="h6 font-weight-bold text-primary mb-1" id="hak_akses_tpq">-</div>
                                    <div class="text-muted small">NIK:</div>
                                    <span class="badge badge-light border text-dark font-weight-normal px-2 py-1" id="hak_akses_nik">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 px-3 small mb-3 border-0 shadow-none">
                        <i class="fas fa-info-circle mr-1"></i> Anda dapat memberikan lebih dari satu peran kepada pengguna ini (contoh: <strong>Guru</strong> sekaligus <strong>Sekretaris Lembaga</strong>). Pengguna nantinya dapat berpindah peran secara instan melalui bilah navigasi atas.
                    </div>

                    <!-- Container Pilihan Grup / Peran -->
                    <div id="hak_akses_groups_container">
                        <div class="text-center py-4">
                            <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                            <div class="text-muted mt-2">Memuat data hak akses...</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between bg-light">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btnSaveHakAkses">
                        <i class="fas fa-save mr-1"></i> Simpan Hak Akses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    // inisialisasi datatable untuk tab Guru
    let tableGuru = null;
    let tablesKelas = {};

    // Initialize DataTable for Guru tab
    if ($('#tblUserGuru tbody tr').length > 0) {
        tableGuru = initializeDataTableUmum("#tblUserGuru", true);

        // Filter event listeners untuk Tab Guru
        $('#filterRole').on('change', function() {
            const val = $(this).val();
            if (tableGuru) {
                // Kolom 4 adalah Hak Akses & Penugasan Peran
                tableGuru.column(4).search(val).draw();
            }
        });

        $('#filterTpq').on('change', function() {
            const val = $(this).val();
            if (tableGuru) {
                // Kolom 3 adalah Lembaga TPQ
                tableGuru.column(3).search(val).draw();
            }
        });

        $('#btnResetFilter').on('click', function() {
            $('#filterRole').val('');
            if ($('#filterTpq').length) {
                $('#filterTpq').val('').trigger('change');
            }
            if (tableGuru) {
                tableGuru.search('').columns().search('').draw();
            }
        });
    }

    // Initialize DataTable for each kelas tab
    <?php foreach ($dataKelas as $kelas): ?>
        const tableId<?= $kelas['IdKelas']; ?> = "#tblUserKelas-<?= $kelas['IdKelas']; ?>";
        if ($(tableId<?= $kelas['IdKelas']; ?> + " tbody tr").length > 0) {
            tablesKelas[<?= $kelas['IdKelas']; ?>] = initializeDataTableUmum(tableId<?= $kelas['IdKelas']; ?>, true);
        }
    <?php endforeach; ?>

    // Reinitialize DataTable when tab is shown
    $('#userTabs a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).attr("href");
        if (target === "#guru" && tableGuru) {
            tableGuru.columns.adjust().draw();
        } else {
            const kelasId = target.replace("#kelas-", "");
            if (tablesKelas[kelasId]) {
                tablesKelas[kelasId].columns.adjust().draw();
            }
        }
    });

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

        // Initialize Select2 for Kelas Santri
        function initSelect2KelasSantri() {
            if ($('#IdKelasSantri').hasClass('select2-hidden-accessible')) {
                $('#IdKelasSantri').select2('destroy');
            }
            $('#IdKelasSantri').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Kelas',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal-tambah')
            });
        }

        // Initialize Select2 for Santri
        function initSelect2Santri() {
            // Destroy dulu jika sudah ada
            if ($('#IdNikSantri').hasClass('select2-hidden-accessible')) {
                $('#IdNikSantri').select2('destroy');
            }
            $('#IdNikSantri').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Nama Santri',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal-tambah')
            });
        }

        // Inisialisasi Select2 saat pertama kali
        initSelect2Guru();
        initSelect2KelasSantri();
        initSelect2Santri();

        // Re-initialize Select2 saat modal dibuka untuk styling yang konsisten
        $('#modal-tambah').on('shown.bs.modal', function() {
            // Destroy dulu jika sudah ada
            if ($('#IdNikGuru').hasClass('select2-hidden-accessible')) {
                $('#IdNikGuru').select2('destroy');
            }
            if ($('#IdKelasSantri').hasClass('select2-hidden-accessible')) {
                $('#IdKelasSantri').select2('destroy');
            }
            if ($('#IdNikSantri').hasClass('select2-hidden-accessible')) {
                $('#IdNikSantri').select2('destroy');
            }
            initSelect2Guru();
            initSelect2KelasSantri();
            initSelect2Santri();
        });

        // Reset form dan Select2 saat modal ditutup
        $('#modal-tambah').on('hidden.bs.modal', function() {
            $('#formTambahUser').trigger('reset');
            $('#IdAuthGroup').val('').trigger('change');
            $('#IdNikGuru').val('').trigger('change');
            $('#IdKelasSantri').val('').trigger('change');
            $('#IdNikSantri').val('').trigger('change');
            $('#IdNikGuru').select2('destroy');
            $('#IdKelasSantri').select2('destroy');
            $('#IdNikSantri').select2('destroy');
            // Reset form visibility
            $('#form-guru').show();
            $('#form-santri').hide();
            $('#checkbox-default-password-guru').show();
            $('#checkbox-default-password-santri').hide();
            // Reset dropdown santri
            $('#IdKelasSantri').val('').trigger('change');
            $('#IdNikSantri').prop('disabled', true);
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

                // Fungsi untuk generate username dari nama
                // Bersihkan nama dari gelar di depan, tanda koma/titik, dan gelar di belakang

                // Daftar gelar di depan (dengan atau tanpa titik)
                const gelarDepan = ['dr', 'dr.', 'dr ', 'Dr', 'Dr.', 'Dr ', 'prof', 'prof.', 'Prof', 'Prof.', 'ust', 'ust.', 'Ust', 'Ust.'];

                // Daftar gelar di belakang (dengan atau tanpa titik)
                const gelarBelakang = ['s.pd', 's.pd.i', 's.ag', 's.ag.i', 'm.pd', 'm.pd.i', 'm.ag', 'm.ag.i', 's.kom', 'm.kom', 's.si', 'm.si'];

                let namaBersih = namaGuru.trim();

                // Hapus gelar di depan
                for (const gelar of gelarDepan) {
                    const regex = new RegExp('^' + gelar.replace(/\./g, '\\.') + '\\s+', 'i');
                    namaBersih = namaBersih.replace(regex, '').trim();
                }

                // Split nama menjadi bagian-bagian
                let namaParts = namaBersih.split(/\s+/);

                // Filter out gelar di belakang dan tanda koma/titik
                namaParts = namaParts.map(part => {
                    // Hapus tanda koma dan titik
                    part = part.replace(/[.,]/g, '').trim();
                    return part;
                }).filter(part => {
                    // Filter out gelar di belakang
                    if (part.length === 0) return false;
                    const partLower = part.toLowerCase();
                    return !gelarBelakang.some(gelar => partLower === gelar || partLower.includes(gelar));
                });

                // Jika masih ada bagian nama yang valid
                if (namaParts.length > 0) {
                    // Hapus karakter khusus dari setiap bagian (tanda hubung, underscore, dll)
                    namaParts = namaParts.map(part => part.replace(/[.,\-_]/g, '').trim()).filter(part => part.length > 0);

                    if (namaParts.length > 1) {
                        // Ambil huruf pertama nama depan + nama belakang (tanpa gelar)
                        const firstPart = namaParts[0].charAt(0).toLowerCase();
                        const lastPart = namaParts[namaParts.length - 1].toLowerCase();
                        username = firstPart + lastPart;
                    } else if (namaParts.length === 1) {
                        // Jika hanya satu kata, gunakan kata tersebut
                        username = namaParts[0].toLowerCase();
                    }
                } else {
                    // Fallback: gunakan nama asli jika semua dianggap gelar
                    const originalParts = namaGuru.trim().split(/\s+/);
                    // Hapus gelar di depan
                    let fallbackParts = originalParts;
                    if (originalParts.length > 0) {
                        const firstPartLower = originalParts[0].toLowerCase().replace(/[.,]/g, '');
                        if (gelarDepan.some(gelar => firstPartLower === gelar.replace(/[.\s]/g, ''))) {
                            fallbackParts = originalParts.slice(1);
                        }
                    }

                    if (fallbackParts.length > 1) {
                        const firstPart = fallbackParts[0].charAt(0).toLowerCase();
                        const secondPart = fallbackParts[1].toLowerCase().replace(/[.,\-_]/g, '');
                        username = firstPart + secondPart;
                    } else if (fallbackParts.length === 1) {
                        username = fallbackParts[0].toLowerCase().replace(/[.,\-_]/g, '');
                    } else {
                        // Jika semua dianggap gelar, gunakan nama asli dengan pembersihan minimal
                        username = namaGuru.trim().toLowerCase().replace(/[^a-z]/g, '').substring(0, 8);
                    }
                }

                // Bersihkan username dari karakter khusus yang tersisa (hanya huruf dan angka)
                username = username.replace(/[^a-z0-9]/g, '');

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

    // Handle group change
    function handleGroupChange() {
        const groupId = $('#IdAuthGroup').val();
        if (!groupId) {
            $('#form-guru').hide();
            $('#form-santri').hide();
            $('#checkbox-default-password-guru').hide();
            $('#checkbox-default-password-santri').hide();
            return;
        }

        const groupName = $('#IdAuthGroup option:selected').text().trim();

        if (groupName === 'Santri') {
            // Sembunyikan form guru
            $('#form-guru').hide();
            // Tampilkan form santri
            $('#form-santri').removeAttr('style').show();
            $('#checkbox-default-password-guru').hide();
            $('#checkbox-default-password-santri').show();

            // Reset form guru
            $('#IdNikGuru').val('').trigger('change');
            $('#fullname_manual').val('');

            // Reset username dan password
            $('#username').val('');
            $('#password').val('');
            $('#confirm-password').val('');
            $('#use-default-password').prop('checked', false);

            // Reset kelas dan santri
            $('#IdKelasSantri').val('').trigger('change');
            $('#IdNikSantri').val('').trigger('change');
            $('#IdNikSantri').prop('disabled', true);

            // Re-initialize Select2 untuk kelas dan santri
            setTimeout(function() {
                if ($('#IdKelasSantri').hasClass('select2-hidden-accessible')) {
                    $('#IdKelasSantri').select2('destroy');
                }
                if ($('#IdNikSantri').hasClass('select2-hidden-accessible')) {
                    $('#IdNikSantri').select2('destroy');
                }
                $('#IdKelasSantri').select2({
                    theme: 'bootstrap4',
                    placeholder: 'Pilih Kelas',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modal-tambah')
                });
                $('#IdNikSantri').select2({
                    theme: 'bootstrap4',
                    placeholder: 'Pilih Kelas terlebih dahulu',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modal-tambah')
                });
            }, 100);
        } else {
            // Tampilkan form guru
            $('#form-guru').show();
            // Sembunyikan form santri
            $('#form-santri').hide();
            $('#checkbox-default-password-guru').show();
            $('#checkbox-default-password-santri').hide();

            // Auto-centang peran sesuai pilihan dropdown awal
            if (groupName === 'Guru') {
                $('#create_grp_3').prop('checked', true);
            } else if (groupName === 'Sekretaris' || groupName === 'Sekretaris Lembaga') {
                $('#create_grp_12').prop('checked', true);
            } else if (groupName === 'Bendahara') {
                $('#create_grp_13').prop('checked', true);
            } else if (groupName === 'Sekretaris FKPQ') {
                $('#create_grp_14').prop('checked', true);
            } else if (groupName === 'Operator') {
                $('#create_grp_2').prop('checked', true);
            } else if (groupName === 'Admin') {
                $('#create_grp_1').prop('checked', true);
            }

            // Reset form santri
            $('#IdKelasSantri').val('').trigger('change');
            $('#IdNikSantri').val('').trigger('change');
            $('#IdNikSantri').prop('disabled', true);

            // Reset username dan password
            $('#username').val('');
            $('#password').val('');
            $('#confirm-password').val('');
            $('#use-default-password-santri').prop('checked', false);

            // Trigger guru change if value exists
            if ($('#IdNikGuru').val()) {
                handleGuruChange();
            }
        }
    }

    // Handle santri change
    function handleSantriChange() {
        const IdNik = $('#IdNikSantri').val();
        if (!IdNik || IdNik === '') {
            $('#username').val('');
            $('#password').val('');
            $('#confirm-password').val('');
            $('#use-default-password-santri').prop('checked', false);
            return;
        }

        const selectedOption = $('#IdNikSantri').find(':selected');
        const IdSantri = selectedOption.data('idsantri');
        const namaSantri = selectedOption.text().split(' (')[0]; // Ambil nama sebelum tanda kurung

        // Check idNikSantri sudah ada atau belum di user show swal popup
        fetch(`<?= site_url('backend/user/checkUserIdNikSantri/'); ?>${IdNik}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: `Santri dengan NIK ${IdNik} atas nama ${namaSantri} sudah memiliki akun!`,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    $('#username').val('');
                    $('#password').val('');
                    $('#confirm-password').val('');
                    $('#IdNikSantri').val('').trigger('change');
                    return;
                }

                // Username santri adalah IdSantri
                if (IdSantri) {
                    $('#username').val(IdSantri);

                    // Set password default untuk santri: SmartSantriTpq{IdTpq} (3 digit terakhir)
                    const idTpqFull = '<?= session()->get("IdTpq") ?? 0; ?>';
                    const idTpq = String(idTpqFull).slice(-3).padStart(3, '0'); // Ambil 3 digit terakhir
                    const defaultPasswordSantri = 'SmartSantriTpq' + idTpq;

                    // Jika checkbox default password santri dicentang, set password
                    if ($('#use-default-password-santri').is(':checked')) {
                        $('#password').val(defaultPasswordSantri);
                        $('#confirm-password').val(defaultPasswordSantri);
                        $('#password, #confirm-password').prop('readonly', true);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    // Handle kelas change untuk filter santri
    function handleKelasChange() {
        const IdKelas = $('#IdKelasSantri').val();
        const dataSantriGrouped = <?= json_encode($dataSantriGrouped); ?>;

        // Reset dropdown santri
        $('#IdNikSantri').val('').trigger('change');
        $('#username').val('');
        $('#password').val('');
        $('#confirm-password').val('');
        $('#use-default-password-santri').prop('checked', false);

        if (!IdKelas || IdKelas === '') {
            // Disable dropdown santri jika kelas tidak dipilih
            $('#IdNikSantri').prop('disabled', true);
            $('#IdNikSantri').empty().append('<option value="">Pilih Kelas terlebih dahulu</option>');

            // Re-initialize Select2
            if ($('#IdNikSantri').hasClass('select2-hidden-accessible')) {
                $('#IdNikSantri').select2('destroy');
            }
            $('#IdNikSantri').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Kelas terlebih dahulu',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal-tambah')
            });
            return;
        }

        // Enable dropdown santri
        $('#IdNikSantri').prop('disabled', false);

        // Filter santri berdasarkan kelas yang dipilih
        const kelasData = dataSantriGrouped[IdKelas];
        if (kelasData && kelasData.santri) {
            // Clear dan isi dropdown santri
            $('#IdNikSantri').empty().append('<option value="">Pilih Nama Santri</option>');

            kelasData.santri.forEach(function(santri) {
                $('#IdNikSantri').append(
                    $('<option></option>')
                    .attr('value', santri.NikSantri)
                    .attr('data-idsantri', santri.IdSantri)
                    .attr('data-kelas', santri.NamaKelas)
                    .text(santri.NamaSantri)
                );
            });

            // Re-initialize Select2
            if ($('#IdNikSantri').hasClass('select2-hidden-accessible')) {
                $('#IdNikSantri').select2('destroy');
            }
            $('#IdNikSantri').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Nama Santri',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal-tambah')
            });
        } else {
            // Jika tidak ada santri di kelas tersebut
            $('#IdNikSantri').empty().append('<option value="">Tidak ada santri di kelas ini</option>');

            // Re-initialize Select2
            if ($('#IdNikSantri').hasClass('select2-hidden-accessible')) {
                $('#IdNikSantri').select2('destroy');
            }
            $('#IdNikSantri').select2({
                theme: 'bootstrap4',
                placeholder: 'Tidak ada santri di kelas ini',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal-tambah')
            });
        }
    }

    // rekomendasi username dari selected nama guru - event change bekerja dengan Select2
    $(document).ready(function() {
        // Event handler untuk change (Select2 juga memicu event change standar)
        $(document).on('change', '#IdNikGuru', handleGuruChange);
        $(document).on('change', '#IdKelasSantri', handleKelasChange);
        $(document).on('change', '#IdNikSantri', handleSantriChange);
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

        const IdAuthGroup = formData.get('IdAuthGroup');
        // Untuk santri, ambil langsung dari select karena mungkin disabled
        const IdNikSantri = $('#IdNikSantri').val() || formData.get('IdNikSantri');
        const IdKelasSantri = $('#IdKelasSantri').val() || formData.get('IdKelasSantri');
        const groupName = $('#IdAuthGroup option:selected').text().trim();

        // Validasi berdasarkan group
        if (!IdAuthGroup) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Group harus dipilih!',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Validasi untuk Santri
        if (groupName === 'Santri') {
            // Validasi khusus untuk santri
            if (!IdKelasSantri || IdKelasSantri === '') {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Kelas harus dipilih!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (!IdNikSantri || IdNikSantri === '' || IdNikSantri === null) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Nama Santri harus dipilih!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (!username || username === '') {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Username harus diisi!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (!password || password === '') {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Password harus diisi!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
        } else {
            // Validasi untuk Guru
            const isAdmin = document.getElementById('fullname_manual') !== null;

            if (!isAdmin && (IdNikGuru === '' || username === '' || password === '')) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Semua data harus diisi!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (isAdmin && (username === '' || password === '')) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Username dan Password harus diisi!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Validasi peran minimal 1 untuk Guru / Staf
            const checkedRoles = $('.create-role-cb:checked');
            if (checkedRoles.length === 0) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Pilih minimal satu peran untuk akun Guru / Staf ini!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
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

        // Pastikan data santri terkirim jika group adalah Santri
        if (groupName === 'Santri') {
            formData.set('IdNikSantri', IdNikSantri);
            formData.set('IdKelasSantri', IdKelasSantri);
        }

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

    // Handle checkbox default password untuk santri
    $('#use-default-password-santri').change(function() {
        const idTpqFull = '<?= session()->get("IdTpq") ?? 0; ?>';
        const idTpq = String(idTpqFull).slice(-3).padStart(3, '0'); // Ambil 3 digit terakhir
        const defaultPasswordSantri = 'SmartSantriTpq' + idTpq;

        if ($(this).is(':checked')) {
            $('#password').val(defaultPasswordSantri);
            $('#confirm-password').val(defaultPasswordSantri);
            $('#password, #confirm-password').prop('readonly', true);
            $('#confirm-password').removeClass('is-invalid');
            $('#error-confirm-password').text('');
        } else {
            $('#password, #confirm-password').val('').prop('readonly', false);
        }
    });

    // Ketika santri dipilih dan checkbox sudah dicentang, set password otomatis
    $(document).on('change', '#IdNikSantri', function() {
        if ($('#use-default-password-santri').is(':checked')) {
            const idTpqFull = '<?= session()->get("IdTpq") ?? 0; ?>';
            const idTpq = String(idTpqFull).slice(-3).padStart(3, '0'); // Ambil 3 digit terakhir
            const defaultPasswordSantri = 'SmartSantriTpq' + idTpq;
            $('#password').val(defaultPasswordSantri);
            $('#confirm-password').val(defaultPasswordSantri);
            $('#password, #confirm-password').prop('readonly', true);
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
        $('#edit_password').val('');
        $('#edit_confirm-password').val('');
        $('#edit_use-default-password').prop('checked', false);
        $('#edit-error-confirm-password').text('');
        $('#edit_groups_container').html('<div class="text-center py-2 text-muted small"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat hak akses...</div>');

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
                    $('#edit_fullname').val(data.user.fullname || data.user.username);
                    $('#edit_username').val(data.user.username);

                    // Ambil peran / hak akses user
                    $.ajax({
                        url: '<?= base_url('backend/user/getUserGroups'); ?>/' + id,
                        type: 'GET',
                        dataType: 'json',
                        success: function(res) {
                            Swal.close();
                            if (res.success) {
                                const assigned = res.assigned_group_ids.map(Number);
                                const groups = res.available_groups;
                                const isAdmin = res.isAdmin;

                                // Cek apakah user santri murni
                                if (assigned.includes(4) && assigned.length === 1) {
                                    $('#edit_roles_section').hide();
                                } else {
                                    const tpqGroupIds = [3, 12, 13];
                                    const tpqGroups = groups.filter(g => tpqGroupIds.includes(parseInt(g.id)));
                                    const systemGroups = groups.filter(g => !tpqGroupIds.includes(parseInt(g.id)) && parseInt(g.id) !== 4);

                                    let html = `
                                        <div class="text-xs font-weight-bold text-uppercase text-primary mb-1">
                                            <i class="fas fa-school mr-1"></i> Peran Lembaga TPQ
                                        </div>
                                        <div class="row">
                                    `;

                                    tpqGroups.forEach(g => {
                                        const isChecked = assigned.includes(parseInt(g.id)) ? 'checked' : '';
                                        let badgeClass = 'badge-primary';
                                        let icon = 'fas fa-chalkboard-teacher';
                                        if (g.name === 'Sekretaris' || g.name === 'Sekretaris Lembaga') {
                                            badgeClass = 'badge-warning text-dark';
                                            icon = 'fas fa-envelope-open-text';
                                        } else if (g.name === 'Bendahara') {
                                            badgeClass = 'badge-info';
                                            icon = 'fas fa-money-bill-wave';
                                        }

                                        html += `
                                            <div class="col-md-6 mb-2">
                                                <div class="custom-control custom-checkbox border rounded p-2 bg-white h-100">
                                                    <input type="checkbox" class="custom-control-input edit-role-cb" id="edit_grp_${g.id}" name="groups[]" value="${g.id}" ${isChecked}>
                                                    <label class="custom-control-label font-weight-bold d-block" for="edit_grp_${g.id}" style="cursor: pointer;">
                                                        <span class="badge ${badgeClass} mr-1"><i class="${icon}"></i></span> ${g.name}
                                                    </label>
                                                </div>
                                            </div>
                                        `;
                                    });

                                    html += `</div>`;

                                    if (isAdmin && systemGroups.length > 0) {
                                        html += `
                                            <div class="text-xs font-weight-bold text-uppercase text-danger mt-2 mb-1">
                                                <i class="fas fa-landmark mr-1"></i> Peran FKPQ & Sistem (Admin Only)
                                            </div>
                                            <div class="row">
                                        `;

                                        systemGroups.forEach(g => {
                                            const isChecked = assigned.includes(parseInt(g.id)) ? 'checked' : '';
                                            let badgeClass = 'badge-secondary';
                                            let icon = 'fas fa-user-cog';
                                            if (g.name === 'Admin') {
                                                badgeClass = 'badge-danger';
                                                icon = 'fas fa-shield-alt';
                                            } else if (g.name === 'Sekretaris FKPQ') {
                                                badgeClass = 'badge-danger';
                                                icon = 'fas fa-landmark';
                                            } else if (g.name === 'Operator') {
                                                badgeClass = 'badge-success';
                                                icon = 'fas fa-laptop-code';
                                            }

                                            html += `
                                                <div class="col-md-6 mb-2">
                                                    <div class="custom-control custom-checkbox border rounded p-2 bg-white h-100">
                                                        <input type="checkbox" class="custom-control-input edit-role-cb" id="edit_grp_${g.id}" name="groups[]" value="${g.id}" ${isChecked}>
                                                        <label class="custom-control-label font-weight-bold d-block" for="edit_grp_${g.id}" style="cursor: pointer;">
                                                            <span class="badge ${badgeClass} mr-1"><i class="${icon}"></i></span> ${g.name}
                                                        </label>
                                                    </div>
                                                </div>
                                            `;
                                        });

                                        html += `</div>`;
                                    }

                                    $('#edit_groups_container').html(html);
                                    $('#edit_roles_section').show();
                                }
                            } else {
                                $('#edit_roles_section').hide();
                            }
                            $('#modal-edit').modal('show');
                        },
                        error: function() {
                            Swal.close();
                            $('#edit_roles_section').hide();
                            $('#modal-edit').modal('show');
                        }
                    });
                } else {
                    Swal.close();
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
        const password = formData.get('password');
        const confirmPassword = formData.get('confirm-password');

        if (password && password !== confirmPassword) {
            $('#edit-error-confirm-password').text('Password dan konfirmasi password tidak cocok!');
            return;
        } else {
            $('#edit-error-confirm-password').text('');
        }

        if ($('#edit_roles_section').is(':visible')) {
            const checkedRoles = $('.edit-role-cb:checked');
            if (checkedRoles.length === 0) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Pilih minimal satu peran untuk pengguna ini!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }
        }

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
                        timer: 1500
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

    // Initialize Select2 for Generate Kelas Modal
    $('#modal-generate-kelas').on('shown.bs.modal', function() {
        if (!$('#IdKelasGenerate').hasClass('select2-hidden-accessible')) {
            $('#IdKelasGenerate').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Kelas',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal-generate-kelas')
            });
        }
    });

    // Handle kelas change untuk menampilkan daftar santri
    $('#IdKelasGenerate').on('change', function() {
        const idKelas = $(this).val();
        const listSantriDiv = $('#list-santri-kelas');
        const infoSantriDiv = $('#info-santri-kelas');

        if (idKelas) {
            // Ambil data santri dari dataSantriGrouped yang sudah ada di PHP
            const dataSantriGrouped = <?= json_encode($dataSantriGrouped) ?>;
            const santriKelas = dataSantriGrouped[idKelas] ? dataSantriGrouped[idKelas].santri : [];

            if (santriKelas.length > 0) {
                let html = '<ul class="list-unstyled mb-0">';
                santriKelas.forEach(function(santri) {
                    html += '<li><i class="fas fa-user"></i> ID: ' + santri.IdSantri + ' - ' + santri.NamaSantri + '</li>';
                });
                html += '</ul>';
                listSantriDiv.html(html);
                infoSantriDiv.show();
            } else {
                listSantriDiv.html('<p class="text-muted mb-0">Tidak ada santri di kelas ini yang belum memiliki user account</p>');
                infoSantriDiv.show();
            }
        } else {
            listSantriDiv.html('');
            infoSantriDiv.hide();
        }
    });

    // Reset form saat modal ditutup
    $('#modal-generate-kelas').on('hidden.bs.modal', function() {
        $('#formGenerateKelas').trigger('reset');
        $('#IdKelasGenerate').val('').trigger('change');
        $('#list-santri-kelas').html('');
        $('#info-santri-kelas').hide();
    });

    // Fungsi untuk generate user per kelas
    function generateUserPerKelas() {
        const idKelas = $('#IdKelasGenerate').val();

        if (!idKelas) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Pilih kelas terlebih dahulu',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'User account akan dibuat untuk semua santri di kelas ini yang belum memiliki user account. Password default akan digunakan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Generate!',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch('<?= base_url('backend/user/generateUserPerKelas') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                IdKelas: idKelas
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) {
                                throw new Error(data.message || 'Gagal generate user');
                            }
                            return data;
                        })
                        .catch(error => {
                            Swal.showValidationMessage('Error: ' + error.message);
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            })
            .then((result) => {
                if (result.isConfirmed && result.value) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: result.value.message || 'User berhasil di-generate',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#modal-generate-kelas').modal('hide');
                        // Reload halaman untuk menampilkan user yang baru dibuat
                        location.reload();
                    });
                }
            });
    }

    // Fungsi Modal Pengaturan Hak Akses & Penugasan Peran
    function manageUserGroups(userId) {
        $('#hak_akses_user_id').val(userId);
        $('#hak_akses_groups_container').html(`
            <div class="text-center py-4">
                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                <div class="text-muted mt-2">Memuat data hak akses...</div>
            </div>
        `);
        $('#modal-hak-akses').modal('show');

        $.ajax({
            url: '<?= base_url('backend/user/getUserGroups'); ?>/' + userId,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    const u = res.user;
                    $('#hak_akses_nama').text(u.nama || u.username);
                    $('#hak_akses_username').text(u.username);
                    $('#hak_akses_tpq').text(u.NamaTpq || 'Tingkat Pusat / FKPQ');
                    $('#hak_akses_nik').text(u.nik || '-');

                    const assigned = res.assigned_group_ids.map(Number);
                    const groups = res.available_groups;
                    const isAdmin = res.isAdmin;

                    let html = '';

                    // Pisahkan grup TPQ vs FKPQ/Pusat
                    const tpqGroupIds = [3, 12, 13]; // Guru, Sekretaris, Bendahara
                    const tpqGroups = groups.filter(g => tpqGroupIds.includes(parseInt(g.id)));
                    const systemGroups = groups.filter(g => !tpqGroupIds.includes(parseInt(g.id)));

                    // Box Peran Lembaga TPQ
                    html += `
                        <div class="card card-outline card-primary mb-3 shadow-none border">
                            <div class="card-header py-2 bg-light">
                                <h6 class="card-title font-weight-bold text-primary mb-0">
                                    <i class="fas fa-school mr-1"></i> Peran Tingkat Lembaga TPQ
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                    `;

                    tpqGroups.forEach(g => {
                        const isChecked = assigned.includes(parseInt(g.id)) ? 'checked' : '';
                        let badgeClass = 'badge-primary';
                        let icon = 'fas fa-chalkboard-teacher';
                        let desc = g.description || '';
                        if (g.name === 'Sekretaris' || g.name === 'Sekretaris Lembaga') {
                            badgeClass = 'badge-warning text-dark';
                            icon = 'fas fa-envelope-open-text';
                            desc = 'Hak akses persuratan & administrasi lembaga TPQ';
                        } else if (g.name === 'Bendahara') {
                            badgeClass = 'badge-info';
                            icon = 'fas fa-money-bill-wave';
                            desc = 'Hak akses modul keuangan lembaga TPQ';
                        } else if (g.name === 'Guru') {
                            desc = 'Mengajar kelas santri & input nilai raport';
                        }

                        html += `
                            <div class="col-md-6 mb-2">
                                <div class="custom-control custom-checkbox border rounded p-2 h-100 bg-white">
                                    <input type="checkbox" class="custom-control-input hak-akses-cb" id="grp_${g.id}" name="groups[]" value="${g.id}" ${isChecked}>
                                    <label class="custom-control-label font-weight-bold d-block" for="grp_${g.id}" style="cursor: pointer;">
                                        <span class="badge ${badgeClass} mr-1"><i class="${icon}"></i></span> ${g.name}
                                        <small class="d-block text-muted font-weight-normal mt-1">${desc}</small>
                                    </label>
                                </div>
                            </div>
                        `;
                    });

                    html += `
                                </div>
                            </div>
                        </div>
                    `;

                    // Jika user login adalah Admin dan ada system/pusat groups
                    if (isAdmin && systemGroups.length > 0) {
                        html += `
                            <div class="card card-outline card-danger mb-3 shadow-none border">
                                <div class="card-header py-2 bg-light">
                                    <h6 class="card-title font-weight-bold text-danger mb-0">
                                        <i class="fas fa-landmark mr-1"></i> Peran Khusus FKPQ Kabupaten & Pusat Sistem
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row">
                        `;

                        systemGroups.forEach(g => {
                            const isChecked = assigned.includes(parseInt(g.id)) ? 'checked' : '';
                            let badgeClass = 'badge-secondary';
                            let icon = 'fas fa-user-cog';
                            let desc = g.description || '';

                            if (g.name === 'Admin') {
                                badgeClass = 'badge-danger';
                                icon = 'fas fa-shield-alt';
                                desc = 'Akses penuh seluruh konfigurasi sistem';
                            } else if (g.name === 'Sekretaris FKPQ') {
                                badgeClass = 'badge-danger';
                                icon = 'fas fa-landmark';
                                desc = 'Hak akses persuratan FKPQ tingkat kabupaten';
                            } else if (g.name === 'Operator') {
                                badgeClass = 'badge-success';
                                icon = 'fas fa-laptop-code';
                                desc = 'Operator pengelola akun & data lembaga TPQ';
                            }

                            html += `
                                <div class="col-md-6 mb-2">
                                    <div class="custom-control custom-checkbox border rounded p-2 h-100 bg-white">
                                        <input type="checkbox" class="custom-control-input hak-akses-cb" id="grp_${g.id}" name="groups[]" value="${g.id}" ${isChecked}>
                                        <label class="custom-control-label font-weight-bold d-block" for="grp_${g.id}" style="cursor: pointer;">
                                            <span class="badge ${badgeClass} mr-1"><i class="${icon}"></i></span> ${g.name}
                                            <small class="d-block text-muted font-weight-normal mt-1">${desc}</small>
                                        </label>
                                    </div>
                                </div>
                            `;
                        });

                        html += `
                                    </div>
                                </div>
                            </div>
                        `;
                    }

                    $('#hak_akses_groups_container').html(html);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Tidak dapat memuat hak akses'
                    });
                    $('#modal-hak-akses').modal('hide');
                }
            },
            error: function(xhr) {
                const err = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err
                });
                $('#modal-hak-akses').modal('hide');
            }
        });
    }

    function saveUserGroups() {
        const userId = $('#hak_akses_user_id').val();
        const checkedGroups = [];
        $('.hak-akses-cb:checked').each(function() {
            checkedGroups.push($(this).val());
        });

        if (checkedGroups.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'User harus memiliki minimal satu peran yang dipilih!'
            });
            return;
        }

        $('#btnSaveHakAkses').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

        $.ajax({
            url: '<?= base_url('backend/user/updateUserGroups'); ?>',
            type: 'POST',
            data: {
                user_id: userId,
                groups: checkedGroups
            },
            dataType: 'json',
            success: function(res) {
                $('#btnSaveHakAkses').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Hak Akses');
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#modal-hak-akses').modal('hide');
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Gagal memperbarui hak akses'
                    });
                }
            },
            error: function(xhr) {
                $('#btnSaveHakAkses').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Hak Akses');
                const err = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal memproses permintaan';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err
                });
            }
        });
    }
</script>

<?= $this->endSection(); ?>