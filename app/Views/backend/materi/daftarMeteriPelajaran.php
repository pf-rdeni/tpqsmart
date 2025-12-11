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
                                <tr>
                                    <td><?= !empty($row['IdTpq']) ? "TPQ " . $row['NamaTpq'] : "FKPQ"; ?></td>
                                    <td><?= $row['IdMateri']; ?></td>
                                    <td><?= $row['NamaMateri']; ?></td>
                                    <td><?= $row['Kategori']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                            data-target="#modal-edit<?= $row['Id']; ?>"
                                            <?= empty($row['IdTpq']) ? 'disabled' : '' ?>>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="<?= !empty($row['IdTpq']) ? "confirmDelete('" . $row['Id'] . "')" : 'void(0)' ?>"
                                            <?= empty($row['IdTpq']) ? 'disabled' : '' ?>>
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
                console.log('Select2 surah initialized');
            }, 100);
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

    // Initialize DataTable for #tblMateri
    initializeDataTableUmum("#tblMateri", true, true);

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