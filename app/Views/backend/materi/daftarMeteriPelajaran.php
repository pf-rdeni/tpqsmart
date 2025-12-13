<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Informasi Proses Flow -->
            <div class="card card-info card-outline collapsed-card mb-3">
                <div class="card-header bg-info">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Informasi Proses Data Materi Pelajaran
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
                            <h5><i class="fas fa-list-ol"></i> Cara Menggunakan Halaman Data Materi Pelajaran:</h5>
                            <ol class="mb-3">
                                <li class="mb-2">
                                    <strong>Memahami Tampilan Halaman</strong>
                                    <ul class="mt-1">
                                        <li>Halaman ini menampilkan <strong>daftar semua materi pelajaran</strong> yang tersedia di sistem</li>
                                        <li>Tabel menampilkan informasi: Materi (TPQ/FKPQ), ID Materi, Nama Materi, Kategori, dan Aksi</li>
                                        <li>Materi yang berasal dari <strong>FKPQ</strong> (pusat) tidak bisa diubah atau dihapus</li>
                                        <li>Materi yang berasal dari <strong>TPQ</strong> Anda bisa diubah atau dihapus</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    <strong>Menambah Materi Pelajaran Baru</strong>
                                    <ul class="mt-1">
                                        <li>Klik tombol <strong>"Tambah Materi"</strong> di bagian atas halaman</li>
                                        <li>Pilih <strong>Kategori</strong> dari dropdown (misalnya: Al-Quran, Aqidah, dll)</li>
                                        <li>ID Materi akan <strong>otomatis terisi</strong> setelah memilih kategori</li>
                                        <li>Masukkan <strong>Nama Materi</strong> yang ingin ditambahkan</li>
                                        <li>Klik <strong>"Simpan"</strong> untuk menyimpan materi baru</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    <strong>Mengubah Materi Pelajaran</strong>
                                    <ul class="mt-1">
                                        <li>Klik tombol <strong>"Edit"</strong> (ikon pensil) pada baris materi yang ingin diubah</li>
                                        <li>Hanya materi dari <strong>TPQ Anda</strong> yang bisa diubah</li>
                                        <li>Kategori dan ID Materi <strong>tidak bisa diubah</strong> (sudah terkunci)</li>
                                        <li>Anda hanya bisa mengubah <strong>Nama Materi</strong></li>
                                        <li>Klik <strong>"Update"</strong> untuk menyimpan perubahan</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    <strong>Menghapus Materi Pelajaran</strong>
                                    <ul class="mt-1">
                                        <li>Klik tombol <strong>"Hapus"</strong> (ikon tempat sampah) pada baris materi yang ingin dihapus</li>
                                        <li>Hanya materi dari <strong>TPQ Anda</strong> yang bisa dihapus</li>
                                        <li>Sistem akan meminta <strong>konfirmasi</strong> sebelum menghapus</li>
                                        <li>Pastikan materi tidak sedang digunakan di kelas sebelum menghapus</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    <strong>Mengatur Materi ke Kelas</strong>
                                    <ul class="mt-1">
                                        <li>Klik tombol <strong>"Daftar Materi Kelas"</strong> untuk mengatur materi ke kelas tertentu</li>
                                        <li>Di halaman tersebut, Anda bisa menentukan materi mana yang digunakan di setiap kelas</li>
                                        <li>Anda juga bisa mengatur urutan materi dan semester (Ganjil/Genap)</li>
                                    </ul>
                                </li>
                            </ol>

                            <div class="alert alert-warning mb-0">
                                <h5><i class="icon fas fa-exclamation-triangle"></i> Catatan Penting:</h5>
                                <ul class="mb-0">
                                    <li>Materi dari <strong>FKPQ</strong> adalah materi standar yang tidak bisa diubah atau dihapus</li>
                                    <li>Materi dari <strong>TPQ</strong> adalah materi khusus TPQ Anda yang bisa dikelola</li>
                                    <li>Pastikan <strong>ID Materi</strong> unik untuk setiap kategori</li>
                                    <li>Sebelum menghapus materi, pastikan materi tersebut <strong>tidak sedang digunakan</strong> di kelas manapun</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah">
                        <i class="fas fa-plus"></i> Tambah Materi
                    </button>
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-tambah-step">
                        <i class="fas fa-plus-circle"></i> Tambah Materi (Step Form)
                    </button>
                    <button type="button" class="btn btn-info" onclick="window.location.href='<?php echo base_url('backend/kelasMateriPelajaran/showMateriKelas') ?>';">
                        <i class="fas fa-list"></i> Daftar Materi Kelas
                    </button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-3" id="filterSection">
                        <div class="col-md-6">
                            <label for="filterTpq">Filter TPQ:</label>
                            <select class="form-control form-control-sm select2" id="filterTpq" multiple style="width: 100%;">
                                <?php
                                // Ambil daftar TPQ unik dari data materi
                                $uniqueTpq = [];
                                foreach ($materiPelajaran as $row) {
                                    $tpqKey = !empty($row['IdTpq']) ? $row['IdTpq'] : 'FKPQ';
                                    $tpqLabel = !empty($row['IdTpq']) ? "TPQ " . $row['NamaTpq'] : "FKPQ";
                                    if (!isset($uniqueTpq[$tpqKey])) {
                                        $uniqueTpq[$tpqKey] = $tpqLabel;
                                    }
                                }
                                // Sort by label
                                asort($uniqueTpq);
                                foreach ($uniqueTpq as $key => $label) {
                                    echo '<option value="' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="filterKategori">Filter Kategori:</label>
                            <select class="form-control form-control-sm select2" id="filterKategori" multiple style="width: 100%;">
                                <?php
                                // Ambil daftar kategori unik dari data materi
                                $uniqueKategori = [];
                                foreach ($materiPelajaran as $row) {
                                    $kategoriKey = $row['Kategori'] ?? '';
                                    if (!empty($kategoriKey) && !isset($uniqueKategori[$kategoriKey])) {
                                        $uniqueKategori[$kategoriKey] = $kategoriKey;
                                    }
                                }
                                // Sort by kategori
                                asort($uniqueKategori);
                                foreach ($uniqueKategori as $kategori) {
                                    echo '<option value="' . htmlspecialchars($kategori, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($kategori, ENT_QUOTES, 'UTF-8') . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-12 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-danger" id="btnResetFilter">
                                <i class="fas fa-times"></i> Reset Filter
                            </button>
                        </div>
                    </div>
                    <table id="tblMateri" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Materi</th>
                                <th>ID Materi</th>
                                <th>Nama Materi</th>
                                <th>Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($materiPelajaran as $row): ?>
                                <tr data-tpq="<?= !empty($row['IdTpq']) ? htmlspecialchars($row['IdTpq'], ENT_QUOTES, 'UTF-8') : 'FKPQ' ?>"
                                    data-kategori="<?= htmlspecialchars($row['Kategori'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <td><?= !empty($row['IdTpq']) ? "TPQ " . $row['NamaTpq'] : "FKPQ"; ?></td>
                                    <td><?= $row['IdMateri']; ?></td>
                                    <td><?= $row['NamaMateri']; ?></td>
                                    <td><?= $row['Kategori']; ?></td>
                                    <td>
                                        <?php
                                        // Cek apakah user adalah admin atau operator
                                        $isAdmin = in_groups('Admin');
                                        $isOperator = in_groups('Operator');

                                        // Ambil IdTpq dari session untuk operator
                                        $userTpq = session()->get('IdTpq') ?? null;

                                        // Logika Edit:
                                        // Admin: bisa edit semua materi (IdTpq = 0/null atau yang punya IdTpq)
                                        // Operator: hanya bisa edit materi yang IdTpq sama dengan IdTpq mereka sendiri
                                        $canEdit = false;
                                        if ($isAdmin) {
                                            // Admin bisa edit semua materi (termasuk IdTpq = 0 atau null)
                                            $canEdit = true;
                                        } elseif ($isOperator && !empty($row['IdTpq']) && $row['IdTpq'] == $userTpq) {
                                            // Operator hanya bisa edit materi TPQ mereka sendiri
                                            $canEdit = true;
                                        }

                                        $isAlquranKategori = ($row['IdKategori'] == 'KM002' || $row['IdKategori'] == 'KM004');
                                        $editModal = $isAlquranKategori ? 'modal-edit-step' : 'modal-edit' . $row['Id'];
                                        $editDataAttr = $isAlquranKategori ? 'data-id-materi="' . $row['IdMateri'] . '"' : '';
                                        ?>
                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                            data-target="#<?= $editModal; ?>" <?= $editDataAttr; ?>
                                            <?= !$canEdit ? 'disabled' : '' ?>>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php
                                        // Logika Delete:
                                        // Admin: bisa hapus semua materi (IdTpq = 0/null atau yang punya IdTpq)
                                        // Operator: tidak bisa hapus (hanya bisa edit)
                                        $canDelete = false;
                                        if ($isAdmin) {
                                            // Admin bisa hapus semua materi (termasuk IdTpq = 0 atau null)
                                            $canDelete = true;
                                        }
                                        // Operator tidak bisa hapus materi
                                        ?>
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="<?= $canDelete ? "confirmDelete('" . $row['Id'] . "')" : 'void(0)' ?>"
                                            <?= !$canDelete ? 'disabled' : '' ?>>
                                            <i class="fas fa-trash"></i>
                                        </button>
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
<div class="modal fade" id="modal-tambah">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title">Tambah Materi Pelajaran</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('backend/materiPelajaran/store') ?>" method="post" id="formTambahMateri">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kategori</label>
                        <select class="form-control" name="Kategori" id="kategori" required onchange="getLastIdMateri()">
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($kategoriPelajaran as $kat): ?>
                                <option value="<?= $kat['Kategori'] ?>"><?= $kat['Kategori'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ID Materi</label>
                        <input type="text" class="form-control" name="IdMateri" id="idMateri" readonly required>
                    </div>
                    <div class="form-group">
                        <label>Nama Materi</label>
                        <input type="text" class="form-control" name="NamaMateri" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" onclick="showLoadingOnSubmit(event)">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<?php foreach ($materiPelajaran as $row): ?>
    <div class="modal fade" id="modal-edit<?= $row['Id']; ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h4 class="modal-title">Edit Materi Pelajaran</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?= base_url('backend/materiPelajaran/update/' . $row['Id']) ?>" method="post" id="formEditMateri<?= $row['Id']; ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Kategori</label>
                            <select class="form-control" name="Kategori" readonly disabled>
                                <?php foreach ($kategoriPelajaran as $kat): ?>
                                    <option value="<?= $kat['Kategori'] ?>" <?= ($row['Kategori'] == $kat['Kategori']) ? 'selected' : ''; ?>>
                                        <?= $kat['Kategori'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <!-- Tambahkan hidden input untuk menyimpan nilai -->
                            <input type="hidden" name="Kategori" value="<?= $row['Kategori'] ?>">
                        </div>
                        <div class="form-group">
                            <label>ID Materi</label>
                            <input type="text" class="form-control" name="IdMateri" value="<?= $row['IdMateri']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Nama Materi</label>
                            <input type="text" class="form-control" name="NamaMateri" value="<?= $row['NamaMateri']; ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" onclick="showLoadingOnUpdate(event, <?= $row['Id']; ?>)">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Modal Edit Step Form -->
<div class="modal fade" id="modal-edit-step" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h4 class="modal-title">Edit Materi Pelajaran (Step Form)</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditMateriStep">
                <input type="hidden" id="editIdMateri" name="IdMateri">
                <div class="modal-body">
                    <!-- Progress Steps -->
                    <div class="steps-progress mb-4">
                        <div class="step-indicator">
                            <div class="step active" data-step="1">
                                <div class="step-number">1</div>
                                <div class="step-label">Pilih Kategori</div>
                            </div>
                            <div class="step-line"></div>
                            <div class="step" data-step="2">
                                <div class="step-number">2</div>
                                <div class="step-label">Form Input</div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Pilih Kategori Materi -->
                    <div class="step-content" id="edit-step-1">
                        <!-- Info Materi (jika belum ada data alquran) -->
                        <div id="editInfoMateri" style="display: none;" class="alert alert-info mb-3">
                            <h6><i class="fas fa-info-circle"></i> Informasi Materi</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Nama Materi:</strong><br>
                                    <span id="editInfoNamaMateri"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Kategori Saat Ini:</strong><br>
                                    <span id="editInfoKategori"></span>
                                </div>
                            </div>
                            <hr>
                            <small class="text-muted">Materi ini belum memiliki data di tabel materi alquran. Silakan pilih kategori baru di bawah untuk menambahkan data alquran.</small>
                        </div>

                        <div class="form-group">
                            <label>Pilih Kategori Materi <span class="text-danger">*</span></label>
                            <select class="form-control" id="editSelectKategoriMateri" required>
                                <option value="">-- Pilih Kategori Materi --</option>
                            </select>
                            <small class="form-text text-muted" id="editHintKategori">Pilih kategori materi untuk materi alquran</small>
                        </div>
                    </div>

                    <!-- Step 2: Form Input (untuk KM002 dan KM004) -->
                    <div class="step-content" id="edit-step-2" style="display: none;">
                        <!-- Info Materi Referensi (ditampilkan di step 2) -->
                        <div id="editInfoMateriStep2" style="display: none;" class="alert alert-info mb-3">
                            <h6><i class="fas fa-info-circle"></i> Informasi Materi Sebelumnya</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Nama Materi:</strong><br>
                                    <span id="editInfoNamaMateriStep2"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Kategori Sebelumnya:</strong><br>
                                    <span id="editInfoKategoriStep2"></span>
                                </div>
                            </div>
                            <small class="text-muted"><i class="fas fa-lightbulb"></i> Gunakan informasi ini sebagai referensi saat mengisi form di bawah</small>
                        </div>

                        <div id="editFormAlquran" style="display: none;">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Pilih Ayat Al-Quran <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <select class="form-control select2" id="editSelectIdSurah" required style="width: 100%;">
                                        <option value="">-- Pilih Ayat Al-Quran --</option>
                                    </select>
                                    <small class="form-text text-muted">Cari berdasarkan nomor surah, nama surah, atau juz (contoh: "101", "AdDuha", atau "Juz 30")</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Nama Surah</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="editNamaSurah" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Kategori</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="editIdKategori" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">ID Materi</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="editIdMateriDisplay" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Awal Ayat <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="editAyatAwal" min="1" step="1" required
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <small class="form-text text-muted">Masukkan nomor ayat awal (hanya angka)</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Akhir Ayat (Opsional)</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="editAyatAkhir" min="1" step="1"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <small class="form-text text-muted" id="editHintAyatAkhir">Kosongkan jika hanya satu ayat</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">TPQ</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="editSelectIdTpq" required>
                                        <option value="">-- Pilih TPQ --</option>
                                    </select>
                                    <small class="form-text text-muted" id="editHintTpq">Pilih TPQ atau Default untuk FKPQ</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <div>
                        <button type="button" class="btn btn-secondary" id="editBtnPrevStep" style="display: none;" onclick="editPrevStep()">Sebelumnya</button>
                        <button type="button" class="btn btn-primary" id="editBtnNextStep" onclick="editNextStep()">Selanjutnya</button>
                        <button type="submit" class="btn btn-success" id="editBtnSubmitStep" style="display: none;">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Step Form -->
<div class="modal fade" id="modal-tambah-step" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h4 class="modal-title">Tambah Materi Pelajaran (Step Form)</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formTambahMateriStep">
                <div class="modal-body">
                    <!-- Progress Steps -->
                    <div class="steps-progress mb-4">
                        <div class="step-indicator">
                            <div class="step active" data-step="1">
                                <div class="step-number">1</div>
                                <div class="step-label">Pilih Kategori</div>
                            </div>
                            <div class="step-line"></div>
                            <div class="step" data-step="2">
                                <div class="step-number">2</div>
                                <div class="step-label">Form Input</div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Pilih Kategori Materi -->
                    <div class="step-content" id="step-1">
                        <div class="form-group">
                            <label>Pilih Kategori Materi <span class="text-danger">*</span></label>
                            <select class="form-control" id="selectKategoriMateri" required>
                                <option value="">-- Pilih Kategori Materi --</option>
                            </select>
                            <small class="form-text text-muted">Pilih kategori materi dari daftar yang tersedia</small>
                        </div>
                    </div>

                    <!-- Step 2: Form Input (untuk KM002 dan KM004) -->
                    <div class="step-content" id="step-2" style="display: none;">
                        <div id="formAlquran" style="display: none;">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Pilih Ayat Al-Quran <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <select class="form-control select2" id="selectIdSurah" required style="width: 100%;">
                                        <option value="">-- Pilih Ayat Al-Quran --</option>
                                    </select>
                                    <small class="form-text text-muted">Cari berdasarkan nomor surah, nama surah, atau juz (contoh: "101", "AdDuha", atau "Juz 30")</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Nama Surah</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="NamaSurah" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Kategori</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="IdKategori" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">ID Materi</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="IdMateri" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Awal Ayat <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="AyatAwal" min="1" step="1" required
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <small class="form-text text-muted">Masukkan nomor ayat awal (hanya angka)</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Akhir Ayat (Opsional)</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="AyatAkhir" min="1" step="1"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <small class="form-text text-muted" id="hintAyatAkhir">Kosongkan jika hanya satu ayat</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">TPQ</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="selectIdTpq" required>
                                        <option value="">-- Pilih TPQ --</option>
                                    </select>
                                    <small class="form-text text-muted" id="hintTpq">Pilih TPQ atau Default untuk FKPQ</small>
                                </div>
                            </div>
                        </div>
                        <div id="formOther" style="display: none;">
                            <p class="text-muted">Form untuk kategori lain akan dikembangkan kemudian.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <div>
                        <button type="button" class="btn btn-secondary" id="btnPrevStep" style="display: none;" onclick="prevStep()">Sebelumnya</button>
                        <button type="button" class="btn btn-primary" id="btnNextStep" onclick="nextStep()">Selanjutnya</button>
                        <button type="submit" class="btn btn-success" id="btnSubmitStep" style="display: none;">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<style>
    .steps-progress {
        margin-bottom: 30px;
    }

    .step-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e0e0e0;
        color: #999;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-bottom: 8px;
        transition: all 0.3s;
    }

    .step.active .step-number {
        background-color: #28a745;
        color: white;
    }

    .step.completed .step-number {
        background-color: #17a2b8;
        color: white;
    }

    .step-label {
        font-size: 12px;
        color: #666;
        text-align: center;
    }

    .step.active .step-label {
        color: #28a745;
        font-weight: bold;
    }

    .step-line {
        width: 100px;
        height: 2px;
        background-color: #e0e0e0;
        margin: 0 10px;
        margin-top: -20px;
    }

    .step.active~.step-line,
    .step.completed~.step-line {
        background-color: #17a2b8;
    }

    .step-content {
        min-height: 300px;
    }
</style>
<script>
    let currentStep = 1;
    let selectedKategori = null;
    let kategoriData = [];
    let surahData = [];
    let tpqData = [];

    // Load data saat modal dibuka
    $('#modal-tambah-step').on('show.bs.modal', function() {
        currentStep = 1;
        selectedKategori = null;
        resetStepForm();
        loadKategoriMateri();
        loadTpqList();

        // Destroy Select2 jika sudah ada
        if ($('#selectIdSurah').hasClass('select2-hidden-accessible')) {
            $('#selectIdSurah').select2('destroy');
        }
    });

    // Pastikan Select2 di-initialize setelah modal fully shown
    $('#modal-tambah-step').on('shown.bs.modal', function() {
        // Prevent click event yang menyebabkan dropdown close
        $(document).off('click.select2').on('click.select2', '.select2-container', function(e) {
            e.stopPropagation();
        });
    });

    // Cleanup saat modal ditutup
    $('#modal-tambah-step').on('hidden.bs.modal', function() {
        // Destroy Select2 saat modal ditutup
        if ($('#selectIdSurah').hasClass('select2-hidden-accessible')) {
            $('#selectIdSurah').select2('destroy');
        }
    });

    function resetStepForm() {
        currentStep = 1;
        $('#selectKategoriMateri').val('');
        $('#selectIdSurah').val('');
        $('#NamaSurah').val('');
        $('#IdKategori').val('');
        $('#IdMateri').val('');
        $('#AyatAwal').val('');
        $('#AyatAkhir').val('');
        $('#selectIdTpq').val('');
        $('#selectIdTpq').prop('disabled', false);
        $('#hintTpq').text('Pilih TPQ atau Default untuk FKPQ');
        updateStepDisplay();
    }

    function updateStepDisplay() {
        // Update step indicator
        $('.step').removeClass('active completed');
        for (let i = 1; i <= 2; i++) {
            if (i < currentStep) {
                $(`.step[data-step="${i}"]`).addClass('completed');
            } else if (i === currentStep) {
                $(`.step[data-step="${i}"]`).addClass('active');
            }
        }

        // Show/hide step content
        $('.step-content').hide();
        $(`#step-${currentStep}`).show();

        // Show/hide buttons
        if (currentStep === 1) {
            $('#btnPrevStep').hide();
            $('#btnNextStep').show();
            $('#btnSubmitStep').hide();
        } else if (currentStep === 2) {
            $('#btnPrevStep').show();
            $('#btnNextStep').hide();
            $('#btnSubmitStep').show();
        }
    }

    function loadKategoriMateri() {
        fetch('<?= base_url('backend/materiPelajaran/getKategoriMateri') ?>', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    kategoriData = data.data;
                    const select = $('#selectKategoriMateri');
                    select.html('<option value="">-- Pilih Kategori Materi --</option>');
                    data.data.forEach(kategori => {
                        select.append(`<option value="${kategori.IdKategoriMateri}">${kategori.NamaKategoriMateri}</option>`);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading kategori:', error);
                Swal.fire('Error', 'Gagal memuat data kategori', 'error');
            });
    }


    function populateSurahSelect(data) {
        if (data.success) {
            surahData = data.data || [];
            const select = $('#selectIdSurah');

            select.empty();
            select.append('<option value="">-- Pilih Ayat Al-Quran --</option>');

            if (surahData.length === 0) {
                select.append('<option value="" disabled>Tidak ada surah ditemukan</option>');
            } else {
                surahData.forEach((surah, index) => {
                    // IdSurah harus menggunakan id (primary key) dari tbl_alquran
                    const idSurah = surah.IdSurah || surah.id || surah.Id || index + 1;
                    // NoSurah untuk display
                    const noSurah = surah.NoSurah || surah.No_Surah || surah.no_surah || idSurah;
                    const namaSurah = surah.NamaSurah || surah.Nama || surah.Surah || surah.nama_surah || surah.name || `Surah ${noSurah}`;
                    const idKategori = surah.IdKategori || surah.id_kategori || '';
                    const juz = surah.Juz || surah.juz || '';
                    const jumlahAyat = surah.JumlahAyat || surah.jumlah_ayat || surah.AyatAkhir || '';

                    if (idSurah && namaSurah) {
                        // Format: "101 - AdDuha (Juz 30)" - menggunakan NoSurah untuk display
                        let displayText = `${noSurah} - ${namaSurah}`;
                        if (juz) {
                            displayText += ` (Juz ${juz})`;
                        }

                        // Value menggunakan id (primary key), bukan NoSurah
                        select.append(`<option value="${idSurah}" data-nama="${namaSurah}" data-kategori="${idKategori}" data-juz="${juz}" data-jumlah-ayat="${jumlahAyat}" data-no-surah="${noSurah}">${displayText}</option>`);
                    }
                });
            }

            // Reinitialize Select2
            setTimeout(function() {
                if (select.hasClass('select2-hidden-accessible')) {
                    select.select2('destroy');
                }
                select.select2({
                    theme: 'bootstrap4',
                    placeholder: '-- Pilih Ayat Al-Quran --',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modal-tambah-step'),
                    language: {
                        noResults: function() {
                            return "Tidak ada hasil";
                        },
                        searching: function() {
                            return "Mencari...";
                        }
                    },
                    matcher: function(params, data) {
                        // Custom matcher untuk search berdasarkan nomor surah, nama surah, atau juz
                        if ($.trim(params.term) === '') {
                            return data;
                        }

                        const term = params.term.toLowerCase().trim();
                        const text = data.text.toLowerCase();
                        const idSurah = data.id || '';
                        const optionElement = $(data.element);
                        const juz = optionElement.data('juz') || '';

                        // Cek apakah term cocok dengan nomor surah
                        if (idSurah.toString().includes(term)) {
                            return data;
                        }

                        // Cek apakah term cocok dengan nama surah
                        if (text.includes(term)) {
                            return data;
                        }

                        // Cek apakah term cocok dengan juz (misal: "30", "juz 30", "juz30")
                        if (juz && (
                                juz.toString() === term ||
                                term === 'juz ' + juz ||
                                term === 'juz' + juz ||
                                text.includes('juz ' + term) ||
                                text.includes('juz' + term)
                            )) {
                            return data;
                        }

                        return null;
                    }
                });

                // Prevent event bubbling yang menyebabkan dropdown close
                select.on('select2:open', function() {
                    $(this).data('select2').$dropdown.off('mousedown').on('mousedown', function(e) {
                        e.stopPropagation();
                    });
                });

                console.log('Select2 surah initialized');
            }, 200);
        }
    }

    function loadSurahAlquran() {
        console.log('Loading surah alquran...');

        fetch('<?= base_url('backend/materiPelajaran/getSurahAlquran') ?>', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                console.log('Success:', data.success);
                console.log('Data count:', data.count || data.data?.length || 0);
                console.log('Message:', data.message);

                if (data.success) {
                    surahData = data.data || [];
                    const select = $('#selectIdSurah');

                    // Clear existing options
                    select.empty();
                    select.append('<option value="">-- Pilih Ayat Al-Quran --</option>');

                    if (surahData.length === 0) {
                        console.warn('Tidak ada data surah ditemukan');
                        select.append(`<option value="" disabled>${data.message || 'Tidak ada data surah ditemukan'}</option>`);

                        // Show warning to user
                        if (data.message) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Data Tidak Ditemukan',
                                text: data.message,
                                timer: 3000,
                                showConfirmButton: false
                            });
                        }
                    } else {
                        populateSurahSelect(data);
                    }
                } else {
                    console.error('Failed to load surah:', data.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Data',
                        text: data.message || 'Gagal memuat data surah alquran'
                    });
                }
            })
            .catch(error => {
                console.error('Error loading surah:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data surah: ' + error.message
                });
            });
    }

    function loadTpqList() {
        fetch('<?= base_url('backend/materiPelajaran/getAllTpq') ?>', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    tpqData = data.data;
                    const select = $('#selectIdTpq');
                    const isOperator = data.isOperator || false;
                    const isAdmin = data.isAdmin || false;
                    const sessionIdTpq = data.sessionIdTpq || null;

                    select.html('');

                    data.data.forEach(tpq => {
                        let displayText = tpq.NamaTpq;
                        if (tpq.KelurahanDesa && tpq.KelurahanDesa.trim() !== '') {
                            displayText += ' - ' + tpq.KelurahanDesa;
                        }
                        select.append(`<option value="${tpq.IdTpq}">${displayText}</option>`);
                    });

                    // Jika Operator, auto-select TPQ mereka dan disable select
                    if (isOperator && sessionIdTpq && sessionIdTpq != 0) {
                        select.val(sessionIdTpq);
                        select.prop('disabled', true);
                        $('#hintTpq').text('TPQ Anda (tidak dapat diubah)');
                    } else if (isAdmin) {
                        // Jika Admin, set default ke NULL (kosong)
                        select.val('');
                        select.prop('disabled', false);
                        $('#hintTpq').text('Pilih TPQ atau Default untuk FKPQ');
                    } else {
                        select.prop('disabled', false);
                        $('#hintTpq').text('Pilih TPQ atau Default untuk FKPQ');
                    }
                }
            })
            .catch(error => {
                console.error('Error loading TPQ:', error);
            });
    }

    // Handle kategori selection
    $('#selectKategoriMateri').on('change', function() {
        const idKategori = $(this).val();
        selectedKategori = kategoriData.find(k => k.IdKategoriMateri === idKategori);

        // Clear surah selection when kategori changes
        if ($('#selectIdSurah').hasClass('select2-hidden-accessible')) {
            $('#selectIdSurah').val(null).trigger('change');
        } else {
            $('#selectIdSurah').val('');
        }
        $('#NamaSurah').val('');
        $('#IdKategori').val('');
        $('#IdMateri').val('');
    });

    // Handle surah selection dengan Select2
    $(document).on('change', '#selectIdSurah', function() {
        const idSurah = $(this).val();

        if (idSurah) {
            // Cari data surah dari surahData atau dari option yang dipilih
            const selectedOption = $(this).find('option:selected');
            let namaSurah = selectedOption.data('nama') || selectedOption.text();
            const jumlahAyat = selectedOption.data('jumlah-ayat') || '';

            // Jika nama surah masih kosong, cari dari surahData
            if (!namaSurah && surahData.length > 0) {
                const surah = surahData.find(s => (s.IdSurah || s.id) == idSurah);
                if (surah) {
                    namaSurah = surah.NamaSurah || surah.Nama || surah.Surah || '';
                    jumlahAyat = surah.JumlahAyat || surah.jumlah_ayat || surah.AyatAkhir || '';
                }
            }

            // Gunakan IdKategori dari kategori yang dipilih di step 1 (WAJIB dari step 1)
            const idKategori = selectedKategori?.IdKategoriMateri || $('#selectKategoriMateri').val();

            if (!idKategori) {
                Swal.fire('Peringatan', 'Silakan pilih kategori materi terlebih dahulu', 'warning');
                $(this).val(null).trigger('change');
                return;
            }

            // Auto-fill field
            $('#NamaSurah').val(namaSurah);
            $('#IdKategori').val(idKategori);

            // Set max value untuk input ayat berdasarkan jumlah ayat surah
            if (jumlahAyat && parseInt(jumlahAyat) > 0) {
                $('#AyatAwal').attr('max', jumlahAyat);
                $('#AyatAkhir').attr('max', jumlahAyat);
                $('#hintAyatAkhir').text(`Kosongkan jika hanya satu ayat (Maksimal: ${jumlahAyat} ayat)`);
            } else {
                $('#AyatAwal').removeAttr('max');
                $('#AyatAkhir').removeAttr('max');
                $('#hintAyatAkhir').text('Kosongkan jika hanya satu ayat');
            }

            // Hapus atribut min untuk AyatAkhir agar user bebas mengetik tanpa proteksi real-time
            $('#AyatAkhir').removeAttr('min');

            // Clear input ayat saat surah berubah
            $('#AyatAwal').val('');
            $('#AyatAkhir').val('');

            // Generate IdMateri otomatis
            generateIdMateri(idKategori);
        } else {
            // Clear fields jika tidak ada yang dipilih
            $('#NamaSurah').val('');
            $('#IdKategori').val('');
            $('#IdMateri').val('');
            $('#AyatAwal').val('');
            $('#AyatAkhir').val('');
            $('#AyatAwal').removeAttr('max');
            $('#AyatAkhir').removeAttr('max');
            $('#hintAyatAkhir').text('Kosongkan jika hanya satu ayat');
        }
    });

    // Validasi input AyatAwal - hanya validasi maksimal saat blur, tidak validasi relasi dengan ayat akhir
    $(document).on('blur', '#AyatAwal', function() {
        const ayatAwal = parseInt($(this).val()) || 0;
        const maxAyat = parseInt($(this).attr('max')) || 0;

        // Validasi maksimal hanya saat blur (kehilangan fokus)
        if (maxAyat > 0 && ayatAwal > maxAyat) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: `Ayat awal tidak boleh lebih dari ${maxAyat} (jumlah ayat surah)`,
                timer: 3000,
                showConfirmButton: false
            });
            $(this).val(maxAyat);
            return;
        }
    });

    // Validasi input AyatAkhir - hanya validasi maksimal saat blur, TIDAK validasi relasi dengan ayat awal saat mengetik
    // Ini memungkinkan user mengetik angka berapa pun tanpa gangguan, validasi hanya saat submit
    $(document).on('blur', '#AyatAkhir', function() {
        const ayatAkhir = parseInt($(this).val()) || 0;
        const maxAyat = parseInt($(this).attr('max')) || 0;

        // Validasi maksimal hanya saat blur (kehilangan fokus)
        // TIDAK validasi relasi dengan ayat awal di sini, biarkan user bebas mengetik
        if (maxAyat > 0 && ayatAkhir > maxAyat) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: `Ayat akhir tidak boleh lebih dari ${maxAyat} (jumlah ayat surah)`,
                timer: 3000,
                showConfirmButton: false
            });
            $(this).val(maxAyat);
            return;
        }
    });

    function generateIdMateri(idKategori) {
        fetch('<?= base_url('backend/materiPelajaran/getLastIdMateriByKategori') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify({
                    IdKategori: idKategori
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#IdMateri').val(data.nextId);
                }
            })
            .catch(error => {
                console.error('Error generating IdMateri:', error);
            });
    }

    function nextStep() {
        if (currentStep === 1) {
            const idKategori = $('#selectKategoriMateri').val();
            if (!idKategori) {
                Swal.fire('Peringatan', 'Silakan pilih kategori materi terlebih dahulu', 'warning');
                return;
            }

            selectedKategori = kategoriData.find(k => k.IdKategoriMateri === idKategori);

            // Show appropriate form based on kategori
            if (idKategori === 'KM002' || idKategori === 'KM004') {
                $('#formAlquran').show();
                $('#formOther').hide();

                // Load all surah data
                loadSurahAlquran();
            } else {
                $('#formAlquran').hide();
                $('#formOther').show();
            }

            currentStep = 2;
            updateStepDisplay();
        }
    }

    function prevStep() {
        if (currentStep === 2) {
            currentStep = 1;
            updateStepDisplay();
        }
    }

    // Handle form submission
    $('#formTambahMateriStep').on('submit', function(e) {
        e.preventDefault();

        // Remove required attribute dari semua field yang hidden atau di step yang tidak aktif
        // untuk menghindari error "An invalid form control is not focusable"
        $('#formTambahMateriStep').find('input[required], select[required]').each(function() {
            const $field = $(this);
            const $stepContent = $field.closest('.step-content');
            const $formAlquran = $field.closest('#formAlquran');

            // Cek jika field berada di step yang tidak aktif
            if ($stepContent.length > 0 && ($stepContent.css('display') === 'none' || !$stepContent.is(':visible'))) {
                $field.removeAttr('required');
            }
            // Cek jika field berada di form alquran yang hidden
            else if ($formAlquran.length > 0 && ($formAlquran.css('display') === 'none' || !$formAlquran.is(':visible'))) {
                $field.removeAttr('required');
            }
            // Cek jika field sendiri hidden
            else if (!$field.is(':visible')) {
                $field.removeAttr('required');
            }
        });

        const idKategori = $('#selectKategoriMateri').val();

        if (idKategori === 'KM002' || idKategori === 'KM004') {
            // Validate alquran form
            if (!validateAlquranForm()) {
                return;
            }

            // Ambil IdTpq, jika disabled (Operator) tetap ambil nilainya
            let idTpq = $('#selectIdTpq').val();
            if (!$('#selectIdTpq').is(':disabled') && !idTpq) {
                idTpq = '0'; // Default untuk Admin jika tidak dipilih
            }

            const formData = {
                IdKategori: $('#IdKategori').val(),
                IdSurah: $('#selectIdSurah').val(),
                AyatAwal: parseInt($('#AyatAwal').val()),
                AyatAkhir: $('#AyatAkhir').val() ? parseInt($('#AyatAkhir').val()) : null,
                IdTpq: idTpq || '0'
            };

            // Validasi ayat range
            if (formData.AyatAkhir && formData.AyatAkhir < formData.AyatAwal) {
                Swal.fire('Error', 'Ayat akhir harus lebih besar atau sama dengan ayat awal', 'error');
                return;
            }

            // Validasi maksimal ayat berdasarkan jumlah ayat surah
            const selectedOption = $('#selectIdSurah').find('option:selected');
            const jumlahAyat = parseInt(selectedOption.data('jumlah-ayat')) || 0;

            if (jumlahAyat > 0) {
                if (formData.AyatAwal > jumlahAyat) {
                    Swal.fire('Error', `Ayat awal tidak boleh lebih dari ${jumlahAyat} (jumlah ayat surah)`, 'error');
                    return;
                }
                if (formData.AyatAkhir && formData.AyatAkhir > jumlahAyat) {
                    Swal.fire('Error', `Ayat akhir tidak boleh lebih dari ${jumlahAyat} (jumlah ayat surah)`, 'error');
                    return;
                }
            }

            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('<?= base_url('backend/materiPelajaran/storeMateriAlquran') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message || 'Data berhasil disimpan',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $('#modal-tambah-step').modal('hide');
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan saat menyimpan data');
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
        } else {
            Swal.fire('Info', 'Form untuk kategori ini akan dikembangkan kemudian', 'info');
        }
    });

    function validateAlquranForm() {
        if (!$('#selectIdSurah').val()) {
            Swal.fire('Peringatan', 'Silakan pilih ayat Al-Quran', 'warning');
            return false;
        }
        if (!$('#AyatAwal').val()) {
            Swal.fire('Peringatan', 'Silakan masukkan ayat awal', 'warning');
            return false;
        }
        return true;
    }

    // ============ EDIT MODAL STEP FORM ============
    let editCurrentStep = 1;
    let editSelectedKategori = null;
    let editKategoriData = [];
    let editSurahData = [];
    let editTpqData = [];
    let editMateriData = null;

    // Load data saat modal edit dibuka
    $('#modal-edit-step').on('show.bs.modal', function(e) {
        const button = $(e.relatedTarget);
        const idMateri = button.data('id-materi');

        if (!idMateri) {
            Swal.fire('Error', 'IdMateri tidak ditemukan', 'error');
            $(this).modal('hide');
            return;
        }

        editCurrentStep = 1;
        editSelectedKategori = null;
        editMateriData = null;
        resetEditStepForm();
        loadEditMateriData(idMateri);
        loadKategoriMateriForEdit();
        loadTpqListForEdit();
    });

    // Pastikan Select2 di-initialize setelah modal fully shown
    $('#modal-edit-step').on('shown.bs.modal', function() {
        // Prevent click event yang menyebabkan dropdown close
        $(document).off('click.select2-edit').on('click.select2-edit', '.select2-container', function(e) {
            e.stopPropagation();
        });
    });

    // Cleanup saat modal ditutup
    $('#modal-edit-step').on('hidden.bs.modal', function() {
        if ($('#editSelectIdSurah').hasClass('select2-hidden-accessible')) {
            $('#editSelectIdSurah').select2('destroy');
        }
    });

    function resetEditStepForm() {
        editCurrentStep = 1;
        $('#editInfoMateri').hide();
        $('#editInfoMateriStep2').hide();
        $('#editSelectKategoriMateri').val('');
        $('#editSelectKategoriMateri').prop('disabled', false);
        $('#editSelectIdSurah').val('');
        $('#editNamaSurah').val('');
        $('#editIdKategori').val('');
        $('#editIdMateri').val('');
        $('#editIdMateriDisplay').val('');
        $('#editAyatAwal').val('');
        $('#editAyatAkhir').val('');
        $('#editSelectIdTpq').val('');
        $('#editSelectIdTpq').prop('disabled', false);
        $('#editHintTpq').text('Pilih TPQ atau Default untuk FKPQ');
        $('#editHintKategori').text('Pilih kategori materi untuk materi alquran');
        updateEditStepDisplay();
    }

    function updateEditStepDisplay() {
        // Update step indicator
        $('.step').removeClass('active completed');
        for (let i = 1; i <= 2; i++) {
            if (i < editCurrentStep) {
                $(`.step[data-step="${i}"]`).addClass('completed');
            } else if (i === editCurrentStep) {
                $(`.step[data-step="${i}"]`).addClass('active');
            }
        }

        // Show/hide step content
        $('.step-content').hide();
        $(`#edit-step-${editCurrentStep}`).show();

        // Show/hide buttons
        if (editCurrentStep === 1) {
            $('#editBtnPrevStep').hide();
            $('#editBtnNextStep').show();
            $('#editBtnSubmitStep').hide();
        } else if (editCurrentStep === 2) {
            $('#editBtnPrevStep').show();
            $('#editBtnNextStep').hide();
            $('#editBtnSubmitStep').show();
        }
    }

    function loadEditMateriData(idMateri) {
        fetch('<?= base_url('backend/materiPelajaran/getMateriAlquranForEdit') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify({
                    IdMateri: idMateri
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    editMateriData = data.data;
                    populateEditForm(data.data);
                } else {
                    Swal.fire('Error', data.message || 'Gagal memuat data materi', 'error');
                    $('#modal-edit-step').modal('hide');
                }
            })
            .catch(error => {
                console.error('Error loading materi:', error);
                Swal.fire('Error', 'Gagal memuat data materi', 'error');
                $('#modal-edit-step').modal('hide');
            });
    }

    function populateEditForm(data) {
        const materi = data.materi;
        const materiAlquran = data.materiAlquran;
        const surah = data.surah;
        const hasAlquranData = data.hasAlquranData !== false; // Default true jika tidak ada property

        // Simpan info materi untuk ditampilkan di step 2 (selalu set)
        const infoNamaMateri = materi.NamaMateri || '-';
        const infoKategori = materi.Kategori || materi.IdKategori || '-';

        // Set info untuk step 2 (selalu ditampilkan sebagai referensi)
        $('#editInfoNamaMateriStep2').text(infoNamaMateri);
        $('#editInfoKategoriStep2').text(infoKategori);

        // Set IdMateri
        $('#editIdMateri').val(materi.IdMateri);
        $('#editIdMateriDisplay').val(materi.IdMateri);

        // Jika belum ada data alquran, tampilkan info dan enable kategori selection
        if (!hasAlquranData || !materiAlquran) {
            // Tampilkan info materi di step 1
            $('#editInfoMateri').show();
            $('#editInfoNamaMateri').text(infoNamaMateri);
            $('#editInfoKategori').text(infoKategori);

            // Tampilkan info referensi di step 2 juga (dengan pesan yang sama)
            $('#editInfoMateriStep2').show();
            // Info sudah di-set di awal function

            // Enable kategori selection
            $('#editSelectKategoriMateri').prop('disabled', false);
            $('#editHintKategori').text('Pilih kategori materi untuk menambahkan data alquran (KM002 atau KM004)');

            // Set kategori saat ini sebagai default (jika ada)
            if (materi.IdKategori) {
                $('#editSelectKategoriMateri').val(materi.IdKategori);

                // Cek apakah kategori adalah KM002 atau KM004, atau nama kategori mengandung "AYAT PILIHAN" atau "SURAT PENDEK"
                const kategoriNama = (materi.Kategori || '').toUpperCase();
                const isAyatPilihan = kategoriNama.includes('AYAT PILIHAN');
                const isSuratPendek = kategoriNama.includes('SURAT PENDEK');
                const isAlquranKategori = materi.IdKategori === 'KM002' || materi.IdKategori === 'KM004' || isAyatPilihan || isSuratPendek;

                if (isAlquranKategori) {
                    // Fungsi untuk auto-select dan ke step 2
                    const autoSelectAndGoToStep2 = (selectedIdKategori) => {
                        // Set kategori di dropdown
                        $('#editSelectKategoriMateri').val(selectedIdKategori);

                        // Set TPQ dari materi
                        const idTpq = materi.IdTpq || '0';
                        $('#editSelectIdTpq').val(idTpq || '0');

                        // Load surah dan langsung ke step 2
                        loadSurahAlquranForEdit().then(() => {
                            // Set kategori
                            $('#editIdKategori').val(selectedIdKategori);

                            // Clear form alquran (karena belum ada data)
                            $('#editSelectIdSurah').val('');
                            $('#editNamaSurah').val('');
                            $('#editAyatAwal').val('');
                            $('#editAyatAkhir').val('');

                            // Parse nama materi untuk extract ayat
                            const namaMateri = materi.NamaMateri || '';
                            let ayatAwal = '';
                            let ayatAkhir = '';

                            // Cek pola "284-286" (range ayat) - prioritas pertama
                            const rangePattern = /(\d+)-(\d+)/;
                            const rangeMatch = namaMateri.match(rangePattern);
                            if (rangeMatch) {
                                ayatAwal = rangeMatch[1];
                                ayatAkhir = rangeMatch[2];
                            } else {
                                // Cek pola angka di akhir nama materi (setelah spasi atau di akhir string)
                                // Contoh: "AL-BAQARAH 255" atau "AL-BAQARAH255"
                                const singlePattern = /[\s]?(\d+)$/;
                                const singleMatch = namaMateri.match(singlePattern);
                                if (singleMatch) {
                                    ayatAwal = singleMatch[1];
                                    ayatAkhir = '';
                                } else {
                                    // Default: set awal ayat = 1
                                    ayatAwal = '1';
                                    ayatAkhir = '';
                                }
                            }

                            // Set ayat jika sudah ada surah yang dipilih
                            if (ayatAwal) {
                                $('#editAyatAwal').val(ayatAwal);
                            }
                            if (ayatAkhir) {
                                $('#editAyatAkhir').val(ayatAkhir);
                            }

                            // Langsung ke step 2
                            editCurrentStep = 2;
                            updateEditStepDisplay();
                            $('#editFormAlquran').show();
                        });
                    };

                    // Tunggu editKategoriData ter-load dulu (jika belum)
                    if (editKategoriData && editKategoriData.length > 0) {
                        // Cari IdKategori yang sesuai
                        let selectedIdKategori = materi.IdKategori;

                        // Jika IdKategori bukan KM002 atau KM004, cari dari nama kategori
                        if (selectedIdKategori !== 'KM002' && selectedIdKategori !== 'KM004') {
                            // Cari kategori yang sesuai berdasarkan nama
                            const foundKategori = editKategoriData.find(k => {
                                const namaKategori = (k.NamaKategoriMateri || '').toUpperCase();
                                return (isAyatPilihan && namaKategori.includes('AYAT PILIHAN')) ||
                                    (isSuratPendek && namaKategori.includes('SURAT PENDEK'));
                            });

                            if (foundKategori) {
                                selectedIdKategori = foundKategori.IdKategoriMateri;
                            }
                        }

                        // Auto-select kategori dan langsung ke step 2
                        editSelectedKategori = editKategoriData.find(k => k.IdKategoriMateri === selectedIdKategori);
                        autoSelectAndGoToStep2(selectedIdKategori);
                    } else {
                        // Jika editKategoriData belum ter-load, tunggu sebentar
                        setTimeout(() => {
                            if (editKategoriData && editKategoriData.length > 0) {
                                // Cari IdKategori yang sesuai
                                let selectedIdKategori = materi.IdKategori;

                                // Jika IdKategori bukan KM002 atau KM004, cari dari nama kategori
                                if (selectedIdKategori !== 'KM002' && selectedIdKategori !== 'KM004') {
                                    // Cari kategori yang sesuai berdasarkan nama
                                    const foundKategori = editKategoriData.find(k => {
                                        const namaKategori = (k.NamaKategoriMateri || '').toUpperCase();
                                        return (isAyatPilihan && namaKategori.includes('AYAT PILIHAN')) ||
                                            (isSuratPendek && namaKategori.includes('SURAT PENDEK'));
                                    });

                                    if (foundKategori) {
                                        selectedIdKategori = foundKategori.IdKategoriMateri;
                                    }
                                }

                                editSelectedKategori = editKategoriData.find(k => k.IdKategoriMateri === selectedIdKategori);
                                autoSelectAndGoToStep2(selectedIdKategori);
                            } else {
                                // Jika masih belum ter-load, tetap di step 1
                                editSelectedKategori = {
                                    IdKategoriMateri: materi.IdKategori
                                };
                                const idTpq = materi.IdTpq || '0';
                                $('#editSelectIdTpq').val(idTpq || '0');
                                editCurrentStep = 1;
                                updateEditStepDisplay();
                            }
                        }, 500);
                    }
                } else {
                    // Kategori bukan KM002 atau KM004, tetap di step 1
                    editSelectedKategori = {
                        IdKategoriMateri: materi.IdKategori
                    };

                    // Set TPQ dari materi
                    const idTpq = materi.IdTpq || '0';
                    $('#editSelectIdTpq').val(idTpq || '0');

                    // Clear form alquran
                    $('#editSelectIdSurah').val('');
                    $('#editNamaSurah').val('');
                    $('#editIdKategori').val('');
                    $('#editAyatAwal').val('');
                    $('#editAyatAkhir').val('');

                    // Tetap di step 1, user harus pilih kategori
                    editCurrentStep = 1;
                    updateEditStepDisplay();
                }
            } else {
                // Tidak ada kategori, tetap di step 1
                // Set TPQ dari materi
                const idTpq = materi.IdTpq || '0';
                $('#editSelectIdTpq').val(idTpq || '0');

                // Clear form alquran
                $('#editSelectIdSurah').val('');
                $('#editNamaSurah').val('');
                $('#editIdKategori').val('');
                $('#editAyatAwal').val('');
                $('#editAyatAkhir').val('');

                // Tetap di step 1, user harus pilih kategori
                editCurrentStep = 1;
                updateEditStepDisplay();
            }
        } else {
            // Jika sudah ada data alquran, gunakan logika lama
            $('#editInfoMateri').hide();

            // Set kategori (disabled jika sudah ada data)
            $('#editSelectKategoriMateri').val(materiAlquran.IdKategoriMateri || materi.IdKategori);
            $('#editSelectKategoriMateri').prop('disabled', true);
            $('#editHintKategori').text('Kategori tidak dapat diubah');
            editSelectedKategori = {
                IdKategoriMateri: materiAlquran.IdKategoriMateri || materi.IdKategori
            };

            // Set TPQ
            const idTpq = materiAlquran.IdTpq || materi.IdTpq || '0';
            $('#editSelectIdTpq').val(idTpq || '0');

            // Load surah dan set values
            loadSurahAlquranForEdit().then(() => {
                if (surah && surah.IdSurah) {
                    // Verifikasi bahwa surah ada di dropdown
                    const surahOption = $('#editSelectIdSurah').find(`option[value="${surah.IdSurah}"]`);
                    if (surahOption.length > 0) {
                        $('#editSelectIdSurah').val(surah.IdSurah).trigger('change');
                        $('#editNamaSurah').val(surah.Surah || surah.NamaSurah);
                        $('#editIdKategori').val(materiAlquran.IdKategoriMateri || materi.IdKategori);

                        // Set max ayat
                        if (surah.JumlahAyat) {
                            $('#editAyatAwal').attr('max', surah.JumlahAyat);
                            $('#editAyatAkhir').attr('max', surah.JumlahAyat);
                            $('#editHintAyatAkhir').text(`Kosongkan jika hanya satu ayat (Maksimal: ${surah.JumlahAyat} ayat)`);
                        }
                    } else {
                        console.warn('Surah dengan IdSurah ' + surah.IdSurah + ' tidak ditemukan di dropdown');
                        // Coba cari berdasarkan NoSurah sebagai fallback
                        const surahByNo = editSurahData.find(s => (s.NoSurah || s.noSurah) == (surah.NoSurah || materiAlquran.IdSurah));
                        if (surahByNo && surahByNo.IdSurah) {
                            $('#editSelectIdSurah').val(surahByNo.IdSurah).trigger('change');
                            $('#editNamaSurah').val(surahByNo.NamaSurah || surahByNo.Surah);
                        } else {
                            // Jika masih tidak ditemukan, set nama surah dari data yang ada
                            $('#editNamaSurah').val(surah.Surah || surah.NamaSurah || materiAlquran.NamaSurah);
                        }
                    }
                } else {
                    // Jika surah tidak ditemukan, set nama surah dari materiAlquran
                    if (materiAlquran.NamaSurah) {
                        $('#editNamaSurah').val(materiAlquran.NamaSurah);
                    }
                }

                // Set ayat
                $('#editAyatAwal').val(materiAlquran.AyatMulai);
                $('#editAyatAkhir').val(materiAlquran.AyatAkhir || '');

                // Auto go to step 2
                editCurrentStep = 2;
                updateEditStepDisplay();
                $('#editFormAlquran').show();

                // Tampilkan info referensi di step 2 (selalu tampilkan)
                $('#editInfoMateriStep2').show();
            });
        }

        // Pastikan info referensi selalu di-set untuk step 2
        // (sudah di-set di awal function)
    }

    function loadKategoriMateriForEdit() {
        fetch('<?= base_url('backend/materiPelajaran/getKategoriMateri') ?>', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    editKategoriData = data.data;
                    const select = $('#editSelectKategoriMateri');
                    select.html('<option value="">-- Pilih Kategori Materi --</option>');
                    data.data.forEach(kategori => {
                        select.append(`<option value="${kategori.IdKategoriMateri}">${kategori.NamaKategoriMateri}</option>`);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading kategori:', error);
            });
    }

    function loadSurahAlquranForEdit() {
        return fetch('<?= base_url('backend/materiPelajaran/getSurahAlquran') ?>', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    editSurahData = data.data || [];
                    const select = $('#editSelectIdSurah');

                    select.empty();
                    select.append('<option value="">-- Pilih Ayat Al-Quran --</option>');

                    if (editSurahData.length === 0) {
                        select.append('<option value="" disabled>Tidak ada surah ditemukan</option>');
                    } else {
                        editSurahData.forEach((surah, index) => {
                            const idSurah = surah.IdSurah || surah.id || index + 1;
                            const noSurah = surah.NoSurah || idSurah;
                            const namaSurah = surah.NamaSurah || surah.Surah || `Surah ${noSurah}`;
                            const idKategori = surah.IdKategori || '';
                            const juz = surah.Juz || surah.juz || '';
                            const jumlahAyat = surah.JumlahAyat || '';

                            if (idSurah && namaSurah) {
                                let displayText = `${noSurah} - ${namaSurah}`;
                                if (juz) {
                                    displayText += ` (Juz ${juz})`;
                                }

                                select.append(`<option value="${idSurah}" data-nama="${namaSurah}" data-kategori="${idKategori}" data-juz="${juz}" data-jumlah-ayat="${jumlahAyat}" data-no-surah="${noSurah}">${displayText}</option>`);
                            }
                        });
                    }

                    // Initialize Select2
                    setTimeout(function() {
                        if (select.hasClass('select2-hidden-accessible')) {
                            select.select2('destroy');
                        }
                        select.select2({
                            theme: 'bootstrap4',
                            placeholder: '-- Pilih Ayat Al-Quran --',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#modal-edit-step'),
                            language: {
                                noResults: function() {
                                    return "Tidak ada hasil";
                                },
                                searching: function() {
                                    return "Mencari...";
                                }
                            },
                            matcher: function(params, data) {
                                if ($.trim(params.term) === '') {
                                    return data;
                                }
                                const term = params.term.toLowerCase().trim();
                                const text = data.text.toLowerCase();
                                const idSurah = data.id || '';
                                const optionElement = $(data.element);
                                const juz = optionElement.data('juz') || '';

                                if (idSurah.toString().includes(term) ||
                                    text.includes(term) ||
                                    (juz && juz.toString().includes(term)) ||
                                    (juz && ('juz ' + juz).includes(term)) ||
                                    (juz && ('juz' + juz).includes(term))) {
                                    return data;
                                }
                                return null;
                            }
                        });

                        // Prevent event bubbling yang menyebabkan dropdown close
                        select.on('select2:open', function() {
                            $(this).data('select2').$dropdown.off('mousedown').on('mousedown', function(e) {
                                e.stopPropagation();
                            });
                        });
                    }, 200);
                }
            });
    }

    function loadTpqListForEdit() {
        fetch('<?= base_url('backend/materiPelajaran/getAllTpq') ?>', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    editTpqData = data.data;
                    const select = $('#editSelectIdTpq');
                    const isOperator = data.isOperator || false;
                    const isAdmin = data.isAdmin || false;
                    const sessionIdTpq = data.sessionIdTpq || null;

                    select.html('');

                    data.data.forEach(tpq => {
                        let displayText = tpq.NamaTpq;
                        if (tpq.KelurahanDesa && tpq.KelurahanDesa.trim() !== '') {
                            displayText += ' - ' + tpq.KelurahanDesa;
                        }
                        select.append(`<option value="${tpq.IdTpq}">${displayText}</option>`);
                    });

                    // Set TPQ berdasarkan data yang sudah ada
                    if (editMateriData) {
                        // Handle jika materiAlquran null (belum ada data alquran)
                        const idTpq = (editMateriData.materiAlquran && editMateriData.materiAlquran.IdTpq) ?
                            editMateriData.materiAlquran.IdTpq :
                            (editMateriData.materi && editMateriData.materi.IdTpq) ?
                            editMateriData.materi.IdTpq :
                            '0';
                        select.val(idTpq || '0');
                    }

                    if (isOperator && sessionIdTpq && sessionIdTpq != 0) {
                        select.prop('disabled', true);
                        $('#editHintTpq').text('TPQ Anda (tidak dapat diubah)');
                    } else if (isAdmin) {
                        select.prop('disabled', false);
                        $('#editHintTpq').text('Pilih TPQ atau Default untuk FKPQ');
                    } else {
                        select.prop('disabled', false);
                        $('#editHintTpq').text('Pilih TPQ atau Default untuk FKPQ');
                    }
                }
            })
            .catch(error => {
                console.error('Error loading TPQ:', error);
            });
    }

    // Handle surah selection untuk edit
    $(document).on('change', '#editSelectIdSurah', function() {
        const idSurah = $(this).val();
        if (idSurah) {
            const selectedOption = $(this).find('option:selected');
            let namaSurah = selectedOption.data('nama') || selectedOption.text();
            const jumlahAyat = selectedOption.data('jumlah-ayat') || '';

            if (!namaSurah && editSurahData.length > 0) {
                const surah = editSurahData.find(s => (s.IdSurah || s.id) == idSurah);
                if (surah) {
                    namaSurah = surah.NamaSurah || surah.Surah || '';
                }
            }

            const idKategori = editSelectedKategori?.IdKategoriMateri || $('#editSelectKategoriMateri').val();

            $('#editNamaSurah').val(namaSurah);
            $('#editIdKategori').val(idKategori);

            if (jumlahAyat && parseInt(jumlahAyat) > 0) {
                $('#editAyatAwal').attr('max', jumlahAyat);
                $('#editAyatAkhir').attr('max', jumlahAyat);
                $('#editHintAyatAkhir').text(`Kosongkan jika hanya satu ayat (Maksimal: ${jumlahAyat} ayat)`);
            } else {
                $('#editAyatAwal').removeAttr('max');
                $('#editAyatAkhir').removeAttr('max');
                $('#editHintAyatAkhir').text('Kosongkan jika hanya satu ayat');
            }

            $('#editAyatAkhir').removeAttr('min');
        }
    });

    function editNextStep() {
        if (editCurrentStep === 1) {
            const idKategori = $('#editSelectKategoriMateri').val();
            if (!idKategori) {
                Swal.fire('Peringatan', 'Silakan pilih kategori materi terlebih dahulu', 'warning');
                return;
            }

            editSelectedKategori = editKategoriData.find(k => k.IdKategoriMateri === idKategori);

            // Show appropriate form based on kategori
            if (idKategori === 'KM002' || idKategori === 'KM004') {
                $('#editFormAlquran').show();

                // Tampilkan info referensi di step 2
                $('#editInfoMateriStep2').show();

                // Load surah data
                loadSurahAlquranForEdit().then(() => {
                    // Set kategori
                    $('#editIdKategori').val(idKategori);

                    // Jika belum ada data alquran, parse nama materi untuk extract ayat
                    if (!editMateriData || !editMateriData.hasAlquranData) {
                        $('#editSelectIdSurah').val('');
                        $('#editNamaSurah').val('');

                        // Parse nama materi untuk extract ayat
                        const namaMateri = editMateriData?.materi?.NamaMateri || '';
                        let ayatAwal = '';
                        let ayatAkhir = '';

                        // Cek pola "284-286" (range ayat) - prioritas pertama
                        const rangePattern = /(\d+)-(\d+)/;
                        const rangeMatch = namaMateri.match(rangePattern);
                        if (rangeMatch) {
                            ayatAwal = rangeMatch[1];
                            ayatAkhir = rangeMatch[2];
                        } else {
                            // Cek pola angka di akhir nama materi (setelah spasi atau di akhir string)
                            // Contoh: "AL-BAQARAH 255" atau "AL-BAQARAH255"
                            const singlePattern = /[\s]?(\d+)$/;
                            const singleMatch = namaMateri.match(singlePattern);
                            if (singleMatch) {
                                ayatAwal = singleMatch[1];
                                ayatAkhir = '';
                            } else {
                                // Default: set awal ayat = 1
                                ayatAwal = '1';
                                ayatAkhir = '';
                            }
                        }

                        // Set ayat
                        if (ayatAwal) {
                            $('#editAyatAwal').val(ayatAwal);
                        }
                        if (ayatAkhir) {
                            $('#editAyatAkhir').val(ayatAkhir);
                        }
                    }
                });
            } else {
                $('#editFormAlquran').hide();
                Swal.fire('Info', 'Form untuk kategori ini akan dikembangkan kemudian', 'info');
                return;
            }

            editCurrentStep = 2;
            updateEditStepDisplay();
        }
    }

    function editPrevStep() {
        if (editCurrentStep === 2) {
            editCurrentStep = 1;
            updateEditStepDisplay();
        }
    }

    // Handle form submission untuk edit
    $('#formEditMateriStep').on('submit', function(e) {
        e.preventDefault();

        // Remove required attribute dari semua field yang hidden atau di step yang tidak aktif
        // untuk menghindari error "An invalid form control is not focusable"
        $('#formEditMateriStep').find('input[required], select[required]').each(function() {
            var $field = $(this);
            var $stepContent = $field.closest('.step-content');
            var $formAlquran = $field.closest('#editFormAlquran');
            var shouldRemove = false;

            // Cek jika field berada di step yang tidak aktif
            if ($stepContent.length > 0) {
                var stepDisplay = $stepContent.css('display');
                if (stepDisplay === 'none' || !$stepContent.is(':visible')) {
                    shouldRemove = true;
                }
            }

            // Cek jika field berada di form alquran yang hidden
            if (!shouldRemove && $formAlquran.length > 0) {
                var formDisplay = $formAlquran.css('display');
                if (formDisplay === 'none' || !$formAlquran.is(':visible')) {
                    shouldRemove = true;
                }
            }

            // Cek jika field sendiri hidden
            if (!shouldRemove && !$field.is(':visible')) {
                shouldRemove = true;
            }

            if (shouldRemove) {
                $field.removeAttr('required');
            }
        });

        // Ambil kategori dari dropdown atau dari field yang sudah di-set di step 2
        let idKategori = $('#editSelectKategoriMateri').val();
        if (!idKategori) {
            // Jika tidak ada di dropdown, ambil dari field yang sudah di-set di step 2
            idKategori = $('#editIdKategori').val();
        }

        // Validasi kategori hanya jika benar-benar kosong
        if (!idKategori) {
            Swal.fire('Peringatan', 'Silakan pilih kategori materi terlebih dahulu', 'warning');
            return;
        }

        if (idKategori === 'KM002' || idKategori === 'KM004') {
            if (!validateEditAlquranForm()) {
                return;
            }

            let idTpq = $('#editSelectIdTpq').val();
            if (!$('#editSelectIdTpq').is(':disabled') && !idTpq) {
                idTpq = '0';
            }

            const formData = {
                IdMateri: $('#editIdMateri').val(),
                IdKategori: $('#editIdKategori').val(),
                IdSurah: $('#editSelectIdSurah').val(),
                AyatAwal: parseInt($('#editAyatAwal').val()),
                AyatAkhir: $('#editAyatAkhir').val() ? parseInt($('#editAyatAkhir').val()) : null,
                IdTpq: idTpq || '0'
            };

            // Validasi ayat range
            if (formData.AyatAkhir && formData.AyatAkhir < formData.AyatAwal) {
                Swal.fire('Error', 'Ayat akhir harus lebih besar atau sama dengan ayat awal', 'error');
                return;
            }

            // Validasi maksimal ayat
            const selectedOption = $('#editSelectIdSurah').find('option:selected');
            const jumlahAyat = parseInt(selectedOption.data('jumlah-ayat')) || 0;

            if (jumlahAyat > 0) {
                if (formData.AyatAwal > jumlahAyat) {
                    Swal.fire('Error', `Ayat awal tidak boleh lebih dari ${jumlahAyat} (jumlah ayat surah)`, 'error');
                    return;
                }
                if (formData.AyatAkhir && formData.AyatAkhir > jumlahAyat) {
                    Swal.fire('Error', `Ayat akhir tidak boleh lebih dari ${jumlahAyat} (jumlah ayat surah)`, 'error');
                    return;
                }
            }

            Swal.fire({
                title: 'Memperbarui...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('<?= base_url('backend/materiPelajaran/updateMateriAlquran') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message || 'Data berhasil diperbarui',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $('#modal-edit-step').modal('hide');
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan saat memperbarui data');
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

    function validateEditAlquranForm() {
        if (!$('#editSelectIdSurah').val()) {
            Swal.fire('Peringatan', 'Silakan pilih ayat Al-Quran', 'warning');
            return false;
        }
        if (!$('#editAyatAwal').val()) {
            Swal.fire('Peringatan', 'Silakan masukkan ayat awal', 'warning');
            return false;
        }
        return true;
    }

    // Initialize DataTable for #tblMateri
    initializeDataTableUmum("#tblMateri", true, true);

    // Get DataTable instance setelah inisialisasi
    var table = null;
    $(document).ready(function() {
        // Tunggu sebentar untuk memastikan DataTable sudah terinisialisasi
        setTimeout(function() {
            if ($.fn.DataTable.isDataTable("#tblMateri")) {
                table = $("#tblMateri").DataTable();
            } else {
                console.error('DataTable belum terinisialisasi untuk #tblMateri');
            }
        }, 100);
    });

    // Key untuk localStorage
    const filterStorageKey = 'materiPelajaran_filterTpq';
    const filterKategoriStorageKey = 'materiPelajaran_filterKategori';

    // Flag untuk mencegah save saat loading
    var isLoadingFilter = false;

    // Initialize Select2 untuk filter TPQ dan Kategori
    $(document).ready(function() {
        // Initialize Select2 untuk filter TPQ (multiple select)
        $('#filterTpq').select2({
            placeholder: 'Pilih TPQ (bisa pilih beberapa)...',
            allowClear: true,
            width: '100%',
            closeOnSelect: false
        });

        // Initialize Select2 untuk filter Kategori (multiple select)
        $('#filterKategori').select2({
            placeholder: 'Pilih Kategori (bisa pilih beberapa)...',
            allowClear: true,
            width: '100%',
            closeOnSelect: false
        });
    });

    // Custom filter function untuk TPQ
    var customFilterTpqFunction = null;
    var customFilterKategoriFunction = null;

    // Fungsi untuk menerapkan filter TPQ (multiple select)
    function applyFilterTpq(selectedValues) {
        if (!table) {
            console.warn('Table not initialized');
            return;
        }

        // Hapus custom filter TPQ function yang lama jika ada
        if (customFilterTpqFunction) {
            const searchFunctions = $.fn.dataTable.ext.search;
            for (let i = searchFunctions.length - 1; i >= 0; i--) {
                if (searchFunctions[i] === customFilterTpqFunction) {
                    searchFunctions.splice(i, 1);
                    break;
                }
            }
            customFilterTpqFunction = null;
        }

        // Jika tidak ada filter, hapus filter dan redraw
        if (!selectedValues || selectedValues.length === 0) {
            // Hanya redraw jika filter kategori juga tidak ada
            const selectedKategori = $('#filterKategori').val() || [];
            if (!selectedKategori || selectedKategori.length === 0) {
                table.draw();
            } else {
                table.draw();
            }
            return;
        }

        // Pastikan selectedValues adalah array
        if (!Array.isArray(selectedValues)) {
            selectedValues = [selectedValues];
        }

        // Buat custom filter TPQ function baru
        customFilterTpqFunction = function(settings, data, dataIndex) {
            // Hanya terapkan filter untuk tabel ini
            if (!settings || !settings.nTable || settings.nTable.id !== 'tblMateri') {
                return true;
            }

            try {
                // Dapatkan row node langsung dari DataTable
                const row = table.row(dataIndex).node();
                if (!row) {
                    return true;
                }

                // Ambil data-tpq dari row
                const rowTpq = $(row).attr('data-tpq') || '';

                // Cek apakah rowTpq ada dalam array selectedValues
                return selectedValues.indexOf(rowTpq) !== -1;
            } catch (e) {
                console.error('Error in custom filter TPQ:', e, 'dataIndex:', dataIndex);
                return true;
            }
        };

        // Tambahkan custom filter TPQ function
        $.fn.dataTable.ext.search.push(customFilterTpqFunction);

        // Redraw tabel
        table.draw();
    }

    // Fungsi untuk menerapkan filter Kategori (multiple select)
    function applyFilterKategori(selectedValues) {
        // Pastikan table sudah terinisialisasi
        if (!table && $.fn.DataTable.isDataTable("#tblMateri")) {
            table = $("#tblMateri").DataTable();
        }

        if (!table) {
            console.warn('Table not initialized');
            return;
        }

        // Hapus custom filter Kategori function yang lama jika ada
        if (customFilterKategoriFunction) {
            const searchFunctions = $.fn.dataTable.ext.search;
            for (let i = searchFunctions.length - 1; i >= 0; i--) {
                if (searchFunctions[i] === customFilterKategoriFunction) {
                    searchFunctions.splice(i, 1);
                    break;
                }
            }
            customFilterKategoriFunction = null;
        }

        // Jika tidak ada filter, hapus filter dan redraw
        if (!selectedValues || selectedValues.length === 0) {
            // Hanya redraw jika filter TPQ juga tidak ada
            const selectedTpq = $('#filterTpq').val() || [];
            if (!selectedTpq || selectedTpq.length === 0) {
                table.draw();
            } else {
                table.draw();
            }
            return;
        }

        // Pastikan selectedValues adalah array
        if (!Array.isArray(selectedValues)) {
            selectedValues = [selectedValues];
        }

        // Buat custom filter Kategori function baru
        customFilterKategoriFunction = function(settings, data, dataIndex) {
            // Hanya terapkan filter untuk tabel ini
            if (!settings || !settings.nTable || settings.nTable.id !== 'tblMateri') {
                return true;
            }

            try {
                // Dapatkan row node langsung dari DataTable
                const row = table.row(dataIndex).node();
                if (!row) {
                    return true;
                }

                // Ambil data-kategori dari row (gunakan attr untuk mendapatkan nilai asli)
                // jQuery .data() akan mengkonversi ke camelCase dan mungkin mengubah nilai
                // Gunakan .attr() untuk mendapatkan nilai string asli
                let rowKategori = $(row).attr('data-kategori') || '';

                // Jika data-kategori kosong, ambil dari cell text (kolom Kategori adalah index 3)
                if (!rowKategori) {
                    const $cell = $(row).find('td').eq(3);
                    if ($cell.length) {
                        rowKategori = $cell.text().trim();
                    }
                }

                // Trim untuk menghilangkan whitespace
                const normalizedRowKategori = String(rowKategori).trim();

                // Cek apakah rowKategori ada dalam array selectedValues
                // Normalisasi setiap nilai dalam selectedValues
                const normalizedSelectedValues = selectedValues.map(function(val) {
                    return String(val).trim();
                });

                return normalizedSelectedValues.indexOf(normalizedRowKategori) !== -1;
            } catch (e) {
                console.error('Error in custom filter Kategori:', e, 'dataIndex:', dataIndex);
                return true;
            }
        };

        // Tambahkan custom filter Kategori function
        $.fn.dataTable.ext.search.push(customFilterKategoriFunction);

        // Redraw tabel
        table.draw();
    }

    // Fungsi untuk menyimpan filter ke localStorage
    function saveFilterTpq(values) {
        try {
            if (values && values.length > 0) {
                localStorage.setItem(filterStorageKey, JSON.stringify(values));
            } else {
                localStorage.removeItem(filterStorageKey);
            }
        } catch (e) {
            console.error('Error saving filter TPQ:', e);
        }
    }

    function saveFilterKategori(values) {
        try {
            if (values && values.length > 0) {
                localStorage.setItem(filterKategoriStorageKey, JSON.stringify(values));
            } else {
                localStorage.removeItem(filterKategoriStorageKey);
            }
        } catch (e) {
            console.error('Error saving filter Kategori:', e);
        }
    }

    // Fungsi untuk memuat filter dari localStorage
    function loadFilters() {
        try {
            isLoadingFilter = true;

            // Load filter TPQ
            const savedFilterTpq = localStorage.getItem(filterStorageKey);
            if (savedFilterTpq) {
                try {
                    const filterTpqArray = JSON.parse(savedFilterTpq);
                    if (Array.isArray(filterTpqArray) && filterTpqArray.length > 0) {
                        $('#filterTpq').val(filterTpqArray).trigger('change');
                        applyFilterTpq(filterTpqArray);
                    }
                } catch (e) {
                    // Fallback untuk format lama (string tunggal)
                    const oldValue = savedFilterTpq;
                    if (oldValue) {
                        $('#filterTpq').val([oldValue]).trigger('change');
                        applyFilterTpq([oldValue]);
                    }
                }
            }

            // Load filter Kategori
            const savedFilterKategori = localStorage.getItem(filterKategoriStorageKey);
            if (savedFilterKategori) {
                try {
                    const filterKategoriArray = JSON.parse(savedFilterKategori);
                    if (Array.isArray(filterKategoriArray) && filterKategoriArray.length > 0) {
                        $('#filterKategori').val(filterKategoriArray).trigger('change');
                        applyFilterKategori(filterKategoriArray);
                    }
                } catch (e) {
                    // Fallback untuk format lama (string tunggal)
                    const oldValue = savedFilterKategori;
                    if (oldValue) {
                        $('#filterKategori').val([oldValue]).trigger('change');
                        applyFilterKategori([oldValue]);
                    }
                }
            }
        } catch (e) {
            console.error('Error loading filters:', e);
        } finally {
            isLoadingFilter = false;
        }
    }

    // Event listener untuk filter TPQ
    $('#filterTpq').on('change', function() {
        const selectedValues = $(this).val() || [];
        // Simpan filter ke localStorage (kecuali saat loading)
        if (!isLoadingFilter) {
            saveFilterTpq(selectedValues);
        }
        // Terapkan filter
        applyFilterTpq(selectedValues);
    });

    // Event listener untuk filter Kategori
    $('#filterKategori').on('change', function() {
        const selectedValues = $(this).val() || [];
        // Simpan filter ke localStorage (kecuali saat loading)
        if (!isLoadingFilter) {
            saveFilterKategori(selectedValues);
        }
        // Terapkan filter
        applyFilterKategori(selectedValues);
    });

    // Load filter saat halaman dimuat
    $(document).ready(function() {
        // Tunggu sebentar untuk memastikan DataTable sudah terinisialisasi
        setTimeout(function() {
            // Pastikan table sudah terinisialisasi
            if (!table && $.fn.DataTable.isDataTable("#tblMateri")) {
                table = $("#tblMateri").DataTable();
            }

            loadFilters();
            // Update DataTable setelah filter dimuat
            if (table) {
                table.draw();
            }
        }, 300);
    });

    // Fungsi untuk reset filter
    function resetFilters() {
        // Pastikan table sudah terinisialisasi
        if (!table && $.fn.DataTable.isDataTable("#tblMateri")) {
            table = $("#tblMateri").DataTable();
        }

        $('#filterTpq').val('').trigger('change');
        $('#filterKategori').val('').trigger('change');
        localStorage.removeItem(filterStorageKey);
        localStorage.removeItem(filterKategoriStorageKey);
        if (table) {
            table.draw();
        }
    }

    // Event listener untuk button reset filter
    $('#btnResetFilter').on('click', function() {
        resetFilters();
    });

    // Create Delete fungsi
    function confirmDelete(id) {
        // Dapatkan informasi materi dari baris tabel
        const row = event.target.closest('tr');
        const idMateri = row.querySelector('td:nth-child(2)').textContent;
        const namaMateri = row.querySelector('td:nth-child(3)').textContent;

        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: `Data materi ID: <strong>${idMateri}</strong> Nama: <strong>${namaMateri}</strong> akan dihapus permanen!`,
            icon: 'question',
            iconColor: '#d33',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                fetch('<?= base_url('backend/materiPelajaran/delete/') ?>' + id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
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


    function getLastIdMateri() {
        const kategori = document.getElementById('kategori').value;
        if (kategori) {
            fetch('<?= base_url('backend/materiPelajaran/getLastIdMateri') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?= csrf_header() ?>': '<?= csrf_hash() ?>' // CSRF protection
                    },
                    body: JSON.stringify({
                        kategori: kategori
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('idMateri').value = data.nextId;
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengambil ID Materi',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        } else {
            document.getElementById('idMateri').value = '';
        }
    }

    function showLoadingOnSubmit(event) {
        event.preventDefault();

        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Ambil form
        const form = document.getElementById('formTambahMateri');

        // Kirim form menggunakan fetch
        fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message || 'Data berhasil disimpan',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan saat menyimpan data');
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

    function showLoadingOnUpdate(event, id) {
        event.preventDefault();

        Swal.fire({
            title: 'Memperbarui...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Ambil form
        const form = document.getElementById('formEditMateri' + id);

        // Kirim form menggunakan fetch
        fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message || 'Data berhasil diperbarui',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan saat memperbarui data');
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
</script>
<?= $this->endSection(); ?>