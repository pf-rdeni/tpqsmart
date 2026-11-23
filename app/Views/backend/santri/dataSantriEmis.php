<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <!-- Card Utama -->
    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h3 class="card-title font-weight-bold my-1">
                    <i class="fas fa-sync-alt text-primary mr-1"></i> Transfer Data Santri ke EMIS 4.0 Kemenag
                </h3>
                <div class="card-tools d-flex align-items-center flex-wrap gap-2">
                    <span id="extStatusBadge" class="badge badge-secondary p-2 mr-2">
                        <i class="fas fa-circle-notch fa-spin mr-1"></i> Cek Ekstensi EMIS...
                    </span>
                    <button type="button" class="btn btn-outline-info btn-sm mr-2" data-toggle="modal" data-target="#modalPanduanExtension">
                        <i class="fas fa-puzzle-piece mr-1"></i> Info Ekstensi Chrome
                    </button>
                    <a href="https://emis.kemenag.go.id" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm">
                        <i class="fas fa-external-link-alt mr-1"></i> Buka Portal EMIS 4.0
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Filter & Summary Bar -->
            <div class="row mb-3 align-items-end">
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="font-weight-bold text-muted small mb-1"><i class="fas fa-filter mr-1"></i> Filter Kelas:</label>
                    <select id="filterKelasEmis" class="form-control select2bs4">
                        <option value="">-- Semua Kelas --</option>
                        <?php if (!empty($dataKelas)) : ?>
                            <?php foreach ($dataKelas as $k) : ?>
                                <option value="<?= htmlspecialchars($k['NamaKelas']); ?>"><?= htmlspecialchars($k['NamaKelas']); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="font-weight-bold text-muted small mb-1"><i class="fas fa-tasks mr-1"></i> Status di EMIS:</label>
                    <select id="filterStatusEmis" class="form-control">
                        <option value="">-- Semua Status --</option>
                        <option value="belum">Belum Disalin</option>
                        <option value="selesai">Sudah Disalin (Selesai)</option>
                    </select>
                </div>
                <div class="col-md-6 col-sm-12 mb-2 text-md-right">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-light border btn-sm" disabled>
                            Total: <strong id="statTotalSantri"><?= count($dataSantri); ?></strong>
                        </button>
                        <button type="button" class="btn btn-success btn-sm" disabled>
                            <i class="fas fa-check-circle mr-1"></i> Selesai: <strong id="statSelesaiSantri">0</strong>
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" disabled>
                            <i class="fas fa-clock mr-1"></i> Belum: <strong id="statBelumSantri"><?= count($dataSantri); ?></strong>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="resetSemuaStatusEmis()" title="Reset tanda selesai">
                            <i class="fas fa-undo"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Santri -->
            <div class="table-responsive">
                <table id="tblSantriEmis" class="table table-bordered table-hover table-striped">
                    <thead class="bg-light">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th width="15%">Aksi EMIS</th>
                            <th width="12%">Status EMIS</th>
                            <th width="15%">NIK</th>
                            <th width="20%">Nama Santri</th>
                            <th width="10%">Kelas</th>
                            <th width="13%">TTL</th>
                            <th width="10%">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($dataSantri as $index => $santri) : ?>
                            <tr id="row-santri-<?= $santri['IdSantri']; ?>" data-id="<?= $santri['IdSantri']; ?>" data-kelas="<?= htmlspecialchars($santri['NamaKelas'] ?? ''); ?>">
                                <td class="text-center align-middle font-weight-bold"><?= $no++; ?></td>
                                <td class="align-middle text-center">
                                    <div class="btn-group-vertical btn-group-sm w-100">
                                        <button type="button" onclick="kirimKeEmisExtension('<?= $santri['IdSantri']; ?>')" class="btn btn-primary btn-sm mb-1 btn-send-emis" title="Kirim otomatis via ekstensi Chrome">
                                            <i class="fas fa-bolt mr-1"></i> <strong>Isi ke EMIS</strong>
                                        </button>
                                        <button type="button" onclick="bukaPanelClipboard('<?= $santri['IdSantri']; ?>')" class="btn btn-info btn-sm" title="Buka panel copy-paste terurut form EMIS">
                                            <i class="fas fa-clipboard-list mr-1"></i> Panel Salin
                                        </button>
                                    </div>
                                </td>
                                <td class="align-middle text-center" id="col-status-<?= $santri['IdSantri']; ?>">
                                    <span class="badge badge-warning p-2 status-badge cursor-pointer" onclick="toggleStatusSantriEmis('<?= $santri['IdSantri']; ?>')" title="Klik untuk ubah status">
                                        <i class="fas fa-hourglass-half mr-1"></i> Belum
                                    </span>
                                </td>
                                <td class="align-middle font-monospace"><?= htmlspecialchars($santri['NikSantri'] ?: '-'); ?></td>
                                <td class="align-middle font-weight-bold text-dark">
                                    <?= htmlspecialchars(ucwords(strtolower($santri['NamaSantri']))); ?>
                                    <div class="small text-muted">
                                        <i class="fas fa-venus-mars mr-1"></i> <?= htmlspecialchars($santri['JenisKelamin'] ?? '-'); ?>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-secondary"><?= htmlspecialchars($santri['NamaKelas'] ?? '-'); ?></span>
                                </td>
                                <td class="align-middle small">
                                    <?= htmlspecialchars($santri['TempatLahirSantri'] ?? '-'); ?><br>
                                    <span class="text-muted"><?= htmlspecialchars($santri['TanggalLahirSantri'] ?? '-'); ?></span>
                                </td>
                                <td class="align-middle text-center">
                                    <button type="button" onclick="showUpdateEmisSantri('<?= $santri['IdSantri']; ?>')" class="btn btn-outline-secondary btn-sm" title="Lihat detail lengkap santri">
                                        <i class="fas fa-eye"></i>
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

<!-- ============================================================== -->
<!-- MODAL: CLIPBOARD PANEL KHUSUS FORMULIR EMIS 4.0 (Phase 4)      -->
<!-- ============================================================== -->
<div class="modal fade" id="modalClipboardEmis" tabindex="-1" role="dialog" aria-labelledby="modalClipboardEmisTitle" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content shadow-lg">
            <!-- Header Modal -->
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title font-weight-bold mb-0" id="modalClipboardEmisTitle">
                        <i class="fas fa-clipboard-check mr-2"></i> Formulir Salin Data Santri ke EMIS 4.0
                    </h5>
                    <small class="text-white-50">Urutan field di bawah ini disesuaikan persis dengan formulir pendaftaran santri baru di EMIS.</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Toolbar Cepat -->
            <div class="bg-light p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="h6 mb-0 font-weight-bold mr-3" id="clipSantriNamaBadge">-</span>
                    <span class="badge badge-info p-2 mr-2" id="clipSantriKelasBadge">-</span>
                    <span class="badge badge-light border p-2" id="clipSantriNikBadge">-</span>
                </div>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="navigasiSantriPrev()">
                        <i class="fas fa-chevron-left mr-1"></i> Santri Sebelumnya
                    </button>
                    <span class="badge badge-secondary p-2" id="clipNavIndex">1 / <?= count($dataSantri); ?></span>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="navigasiSantriNext()">
                        Santri Berikutnya <i class="fas fa-chevron-right ml-1"></i>
                    </button>
                    <button type="button" class="btn btn-success btn-sm ml-2" onclick="salinSemuaFieldText()">
                        <i class="fas fa-copy mr-1"></i> <strong>Salin Semua Teks</strong>
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" id="btnTandaiSelesaiModal" onclick="toggleStatusModalSantri()">
                        <i class="fas fa-check mr-1"></i> Tandai Sudah Disalin
                    </button>
                    <a href="https://emis.kemenag.go.id" target="_blank" rel="noopener noreferrer" class="btn btn-dark btn-sm">
                        <i class="fas fa-external-link-alt mr-1"></i> Tab EMIS
                    </a>
                </div>
            </div>

            <!-- Body Modal: Form Mirror EMIS 4.0 2-Step Layout -->
            <div class="modal-body p-4 bg-white">
                <!-- Nav Tabs 2 Step EMIS -->
                <ul class="nav nav-pills nav-justified mb-4 p-1 bg-light border rounded" id="pills-step-emis" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" id="tab-step1-link" data-toggle="pill" href="#tab-step1-content" role="tab">
                            <i class="fas fa-search mr-1"></i> <span class="badge badge-primary mr-1">Step 1</span> Validasi Awal (Nama & TTL / NIK)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-step2-link" data-toggle="pill" href="#tab-step2-content" role="tab">
                            <i class="fas fa-file-alt mr-1"></i> <span class="badge badge-success mr-1">Step 2</span> Formulir Utama Santri Baru
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="pills-step-emisContent">
                    <!-- ============================================== -->
                    <!-- TAB 1: STEP 1 (Pencarian / Validasi Awal EMIS) -->
                    <!-- ============================================== -->
                    <div class="tab-pane fade show active" id="tab-step1-content" role="tabpanel">
                        <div class="alert alert-warning py-2 px-3 small mb-3 border-warning">
                            <i class="fas fa-info-circle mr-1"></i> <strong>Tahap 1 di EMIS:</strong> Buka formulir pendaftaran santri baru di EMIS. Pilih opsi <strong>"Nama, Tempat, dan tanggal lahir"</strong> (atau opsi NIK jika menggunakan NIK), lalu salin field di bawah. Setelah itu tekan tombol <strong class="badge badge-dark">SELANJUTNYA</strong> di EMIS.
                        </div>

                        <!-- METODE UTAMA: NAMA, TEMPAT & TANGGAL LAHIR (Sesuai Lampiran EMIS) -->
                        <div class="card card-outline card-success mb-3 shadow-sm">
                            <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center">
                                <h6 class="card-title font-weight-bold text-success mb-0">
                                    <i class="fas fa-check-circle mr-1"></i> PILIHAN 1: Berdasarkan Nama, Tempat, dan Tanggal Lahir
                                </h6>
                                <span class="badge badge-success">Sesuai Formulir EMIS</span>
                            </div>
                            <div class="card-body p-3">
                                <div class="alert alert-light border small text-muted mb-3 py-2">
                                    <i class="fas fa-info-circle text-info mr-1"></i> <em>Pastikan Nama, Tempat dan Tanggal Lahir harus sesuai dengan kartu keluarga</em>
                                </div>
                                
                                <!-- Nama Lengkap Step 1 -->
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold small text-muted">NAMA LENGKAP <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="step1NamaSantri" class="form-control bg-light font-weight-bold" readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('step1NamaSantri', this)">
                                                <i class="fas fa-copy mr-1"></i> Salin
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted">Hanya dapat diisi huruf dan simbol '-' ','</small>
                                </div>

                                <!-- Tempat Lahir Step 1 -->
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold small text-muted">TEMPAT LAHIR <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="step1TempatLahir" class="form-control bg-light font-weight-bold" readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('step1TempatLahir', this)">
                                                <i class="fas fa-copy mr-1"></i> Salin
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted">Tempat Lahir hanya bisa diisi simbol/angka apabila didampingi huruf/karakter</small>
                                </div>

                                <!-- Tanggal Lahir Step 1 -->
                                <div class="form-group mb-2">
                                    <label class="font-weight-bold small text-muted">TANGGAL LAHIR <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" id="step1TanggalLahir" class="form-control bg-light font-weight-bold" readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('step1TanggalLahir', this)">
                                                <i class="fas fa-copy mr-1"></i> Salin
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted">Format: YYYY-MM-DD (Tanggal lahir wajib diisi tidak boleh kosong)</small>
                                </div>
                            </div>
                        </div>

                        <!-- METODE ALTERNATIF: NIK / NISN -->
                        <div class="card card-outline card-secondary mb-3">
                            <div class="card-header py-2 bg-light">
                                <h6 class="card-title font-weight-bold text-secondary mb-0">
                                    <i class="fas fa-id-card mr-1"></i> PILIHAN 2: Berdasarkan NIK atau NISN
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="font-weight-bold small text-muted">NIK SANTRI (16 DIGIT)</label>
                                        <div class="input-group">
                                            <input type="text" id="step1Nik" class="form-control bg-light font-weight-bold" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-secondary btn-copy" type="button" onclick="copyFieldVal('step1Nik', this)">
                                                    <i class="fas fa-copy mr-1"></i> Salin NIK
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="font-weight-bold small text-muted">NISN SANTRI</label>
                                        <div class="input-group">
                                            <input type="text" id="step1Nisn" class="form-control bg-light font-weight-bold" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-secondary btn-copy" type="button" onclick="copyFieldVal('step1Nisn', this)">
                                                    <i class="fas fa-copy mr-1"></i> Salin NISN
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Navigasi Menuju Step 2 -->
                        <div class="text-center mt-4 p-3 bg-light border rounded">
                            <button type="button" class="btn btn-success btn-lg px-4" onclick="$('#tab-step2-link').tab('show');">
                                <strong>Lanjut ke Step 2 (Formulir Utama)</strong> <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                            <div class="small text-muted mt-2">
                                Klik tombol di atas setelah Anda menekan tombol <strong>SELANJUTNYA</strong> di EMIS dan formulir utama terbuka.
                            </div>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- TAB 2: STEP 2 (Formulir Utama Santri Baru)     -->
                    <!-- ============================================== -->
                    <div class="tab-pane fade" id="tab-step2-content" role="tabpanel">
                        <div class="alert alert-info py-2 px-3 small mb-3">
                            <i class="fas fa-info-circle mr-1"></i> <strong>Tahap 2 di EMIS:</strong> Formulir utama santri baru telah terbuka di EMIS. Salin seluruh field di bawah ke form EMIS, lalu klik tombol <strong>SIMPAN</strong> di EMIS.
                        </div>

                        <!-- SEKSI 1: DATA PENDAFTARAN -->
                        <div class="card card-outline card-info mb-4">
                            <div class="card-header py-2 bg-light">
                                <h6 class="card-title font-weight-bold text-primary mb-0">
                                    <i class="fas fa-calendar-alt mr-1"></i> 1. DATA PENDAFTARAN & KELAS
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <!-- Tanggal Masuk -->
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold small text-muted">TANGGAL MASUK <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" id="fieldTanggalMasuk" class="form-control bg-light font-weight-bold" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('fieldTanggalMasuk', this)">
                                                    <i class="fas fa-copy mr-1"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">Format: YYYY-MM-DD (sesuai datepicker EMIS)</small>
                                    </div>
                                    <!-- Tingkat Kelas -->
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold small text-muted">TINGKAT KELAS <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" id="fieldTingkatKelas" class="form-control bg-light font-weight-bold" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('fieldTingkatKelas', this)">
                                                    <i class="fas fa-copy mr-1"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">Pilih opsi dropdown di EMIS sesuai nilai ini</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEKSI 2: KETERANGAN CALON SANTRI -->
                        <div class="card card-outline card-info mb-4">
                            <div class="card-header py-2 bg-light">
                                <h6 class="card-title font-weight-bold text-primary mb-0">
                                    <i class="fas fa-user-graduate mr-1"></i> 2. KETERANGAN CALON SANTRI
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <!-- Nama Lengkap -->
                                    <div class="col-md-12 mb-3">
                                        <label class="font-weight-bold small text-muted">NAMA LENGKAP SANTRI <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" id="fieldNamaSantri" class="form-control bg-light font-weight-bold" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('fieldNamaSantri', this)">
                                                    <i class="fas fa-copy mr-1"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kewarganegaraan -->
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold small text-muted">KEWARGANEGARAAN <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" id="fieldKewarganegaraan" value="WNI" class="form-control bg-light font-weight-bold" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('fieldKewarganegaraan', this)">
                                                    <i class="fas fa-copy mr-1"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">Default EMIS: WNI</small>
                                    </div>

                                    <!-- Agama -->
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold small text-muted">AGAMA <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" id="fieldAgama" value="Islam" class="form-control bg-light font-weight-bold" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('fieldAgama', this)">
                                                    <i class="fas fa-copy mr-1"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">Default EMIS: Islam</small>
                                    </div>

                                    <!-- NIK -->
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="font-weight-bold small text-muted mb-1">NIK (16 DIGIT) <span class="text-danger">*</span></label>
                                            <span id="badgeBelumPunyaNik" class="badge badge-secondary d-none">Centang: [✓] Belum Punya NIK</span>
                                        </div>
                                        <div class="input-group">
                                            <input type="text" id="fieldNik" class="form-control bg-light font-weight-bold" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('fieldNik', this)">
                                                    <i class="fas fa-copy mr-1"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- NISN -->
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="font-weight-bold small text-muted mb-1">NISN <span class="text-danger">*</span></label>
                                            <span id="badgeBelumPunyaNisn" class="badge badge-warning d-none">Centang: [✓] Belum Punya NISN</span>
                                        </div>
                                        <div class="input-group">
                                            <input type="text" id="fieldNisn" class="form-control bg-light font-weight-bold" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('fieldNisn', this)">
                                                    <i class="fas fa-copy mr-1"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Jenis Kelamin -->
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold small text-muted">JENIS KELAMIN <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" id="fieldJenisKelamin" class="form-control bg-light font-weight-bold" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('fieldJenisKelamin', this)">
                                                    <i class="fas fa-copy mr-1"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">Pilih radio button di EMIS: <strong>Laki-laki</strong> atau <strong>Perempuan</strong></small>
                                    </div>

                                    <!-- Tempat Lahir -->
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold small text-muted">TEMPAT LAHIR <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" id="fieldTempatLahir" class="form-control bg-light font-weight-bold" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('fieldTempatLahir', this)">
                                                    <i class="fas fa-copy mr-1"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tanggal Lahir -->
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold small text-muted">TANGGAL LAHIR <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" id="fieldTanggalLahir" class="form-control bg-light font-weight-bold" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('fieldTanggalLahir', this)">
                                                    <i class="fas fa-copy mr-1"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">Format: YYYY-MM-DD</small>
                                    </div>

                                    <!-- No Handphone -->
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="font-weight-bold small text-muted mb-1">NO HANDPHONE <span class="text-danger">*</span></label>
                                            <span id="badgeTidakPunyaHp" class="badge badge-warning d-none">Centang: [✓] Tidak memiliki HP</span>
                                        </div>
                                        <div class="input-group">
                                            <input type="text" id="fieldNoHp" class="form-control bg-light font-weight-bold" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('fieldNoHp', this)">
                                                    <i class="fas fa-copy mr-1"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEKSI 3: KETERANGAN ORANG TUA SANTRI -->
                        <div class="card card-outline card-info mb-3">
                            <div class="card-header py-2 bg-light">
                                <h6 class="card-title font-weight-bold text-primary mb-0">
                                    <i class="fas fa-users mr-1"></i> 3. KETERANGAN ORANG TUA & WALI SANTRI
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <!-- AYAH KANDUNG -->
                                    <div class="col-md-6 mb-3 border-right">
                                        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                                            <i class="fas fa-male text-primary mr-1"></i> Data Ayah Kandung
                                        </h6>
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold small text-muted">Nama Lengkap Ayah <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" id="fieldNamaAyah" class="form-control bg-light font-weight-bold" readonly>
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('fieldNamaAyah', this)">
                                                        <i class="fas fa-copy mr-1"></i> Salin
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label class="font-weight-bold small text-muted">Status Ayah <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" id="fieldStatusAyah" class="form-control bg-light font-weight-bold" readonly>
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('fieldStatusAyah', this)">
                                                        <i class="fas fa-copy mr-1"></i> Salin
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">Pilihan dropdown di EMIS: <strong>Masih Hidup</strong> / Meninggal</small>
                                        </div>
                                    </div>

                                    <!-- IBU KANDUNG -->
                                    <div class="col-md-6 mb-3">
                                        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                                            <i class="fas fa-female text-danger mr-1"></i> Data Ibu Kandung
                                        </h6>
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold small text-muted">Nama Lengkap Ibu <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" id="fieldNamaIbu" class="form-control bg-light font-weight-bold" readonly>
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('fieldNamaIbu', this)">
                                                        <i class="fas fa-copy mr-1"></i> Salin
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label class="font-weight-bold small text-muted">Status Ibu <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" id="fieldStatusIbu" class="form-control bg-light font-weight-bold" readonly>
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('fieldStatusIbu', this)">
                                                        <i class="fas fa-copy mr-1"></i> Salin
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">Pilihan dropdown di EMIS: <strong>Masih Hidup</strong> / Meninggal</small>
                                        </div>
                                    </div>

                                    <!-- WALI -->
                                    <div class="col-md-12 mt-2 pt-3 border-top">
                                        <h6 class="font-weight-bold text-dark mb-2">
                                            <i class="fas fa-user-shield text-info mr-1"></i> Data Wali
                                        </h6>
                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold small text-muted">Status Wali <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" id="fieldStatusWali" class="form-control bg-light font-weight-bold" readonly>
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary btn-copy" type="button" onclick="copyFieldVal('fieldStatusWali', this)">
                                                        <i class="fas fa-copy mr-1"></i> Salin
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">Pilihan dropdown di EMIS: <strong>Sama dengan ayah kandung</strong> / Sama dengan ibu kandung / Lainnya</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
                            <button type="button" class="btn btn-outline-secondary" onclick="$('#tab-step1-link').tab('show');">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Step 1
                            </button>
                            <button type="button" class="btn btn-warning" onclick="toggleStatusModalSantri()">
                                <i class="fas fa-check mr-1"></i> Tandai Sudah Selesai di EMIS
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Hidden textarea untuk salin semua teks -->
                <textarea id="hiddenAllFieldsText" class="d-none"></textarea>
            </div>

            <!-- Footer Modal -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="navigasiSantriNext()">
                    Lanjut Santri Berikutnya <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: PANDUAN EKSTENSI CHROME (Phase 3 Helper)                 -->
<!-- ============================================================== -->
<div class="modal fade" id="modalPanduanExtension" tabindex="-1" role="dialog" aria-labelledby="modalPanduanExtensionTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold" id="modalPanduanExtensionTitle">
                    <i class="fas fa-puzzle-piece mr-2"></i> Ekstensi Chrome: TPQ Smart ke EMIS Bridge
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-light border mb-4">
                    <h6 class="font-weight-bold text-dark"><i class="fas fa-magic text-warning mr-1"></i> Apa itu Ekstensi Ini?</h6>
                    <p class="mb-0 text-muted">
                        Ekstensi browser ini berfungsi sebagai jembatan semi-otomatis. Saat Anda menekan tombol <strong>⚡ Isi ke EMIS</strong> di TPQ Smart, ekstensi akan secara otomatis mengisikan seluruh data santri ke formulir EMIS 4.0 yang sedang Anda buka, sehingga Anda tidak perlu mengetik atau copy-paste satu per satu.
                    </p>
                </div>

                <div class="mb-4 text-center">
                    <a href="<?= base_url('extension-emis.zip'); ?>" download class="btn btn-primary">
                        <i class="fas fa-file-archive mr-1"></i> <strong>Unduh File ZIP Ekstensi (extension-emis.zip)</strong>
                    </a>
                    <div class="small text-muted mt-1">Ekstrak file ZIP ini ke komputer Anda, lalu pilih foldernya saat melakukan <em>Load unpacked</em> di browser.</div>
                </div>

                <h6 class="font-weight-bold mb-3"><i class="fas fa-list-ol text-primary mr-1"></i> Langkah Pemasangan Ekstensi di Chrome / Edge:</h6>
                <ol class="pl-3 mb-4">
                    <li class="mb-2">Buka Google Chrome atau Microsoft Edge di komputer Anda.</li>
                    <li class="mb-2">Ketik alamat <code>chrome://extensions</code> (atau <code>edge://extensions</code>) di browser bar lalu tekan <strong>Enter</strong>.</li>
                    <li class="mb-2">Nyalakan tombol <strong>"Developer mode" (Mode Pengembang)</strong> di pojok kanan atas.</li>
                    <li class="mb-2">Klik tombol <strong>"Load unpacked" (Muat yang belum dibongkar)</strong> di pojok kiri atas.</li>
                    <li class="mb-2">Pilih folder: <code>C:\laragon\www\tpqsmart\public\extension-emis</code></li>
                    <li class="mb-2">Selesai! Ekstensi <strong>TPQ Smart - EMIS 4.0 Bridge</strong> sekarang sudah aktif.</li>
                </ol>

                <div class="card bg-light border p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Status Deteksi Saat Ini:</strong><br>
                            <span id="guideExtStatus" class="badge badge-secondary p-2 mt-1">Mengecek koneksi...</span>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="cekKoneksiExtension(true)">
                            <i class="fas fa-sync-alt mr-1"></i> Tes Ulang Koneksi
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: UPDATE / DETAIL LENGKAP SANTRI (Original Preserved)      -->
<!-- ============================================================== -->
<div class="modal fade" id="updateDataEmisModal" tabindex="-1" role="dialog" aria-labelledby="detailSantriModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailSantriModalLabel">Detail Lengkap Data Santri</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="detailSantriTabUpdate" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="data-santri-tabUpdate" data-toggle="tab" href="#data-santriUpdate" role="tab">
                            <i class="fas fa-user"></i> Data Santri
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="data-ortu-tabUpdate" data-toggle="tab" href="#data-ortuUpdate" role="tab">
                            <i class="fas fa-users"></i> Data Orang Tua
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="data-alamat-tabUpdate" data-toggle="tab" href="#data-alamatUpdate" role="tab">
                            <i class="fas fa-map-marker-alt"></i> Data Alamat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="lampiran-tabUpdate" data-toggle="tab" href="#lampiranUpdate" role="tab">
                            <i class="fas fa-file"></i> Lampiran
                        </a>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="detailSantriTabContentUpdate">
                    <!-- Tab Data Santri -->
                    <div class="tab-pane fade show active" id="data-santriUpdate" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4 text-center mb-3">
                                <img id="modalPhotoProfilUpdate" src="" alt="Foto Profil" class="img-fluid rounded shadow-sm" style="max-width: 200px;">
                            </div>
                            <div class="col-md-8">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="35%">ID Santri</td>
                                        <td width="5%">:</td>
                                        <td id="modalIdSantriUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Nama TPQ</td>
                                        <td>:</td>
                                        <td id="modalNamaTpqUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Kelas</td>
                                        <td>:</td>
                                        <td id="modalKelasUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>NIK Santri</td>
                                        <td>:</td>
                                        <td id="modalNikSantriUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>No Kartu Keluarga</td>
                                        <td>:</td>
                                        <td id="modalIdKartuKeluargaUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>NISN</td>
                                        <td>:</td>
                                        <td id="modalNISUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Nama Santri</td>
                                        <td>:</td>
                                        <td id="modalNamaSantriUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Jenis Kelamin</td>
                                        <td>:</td>
                                        <td id="modalJenisKelaminUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>TTL</td>
                                        <td>:</td>
                                        <td id="modalTempatTanggalLahirUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>No HP & Email</td>
                                        <td>:</td>
                                        <td id="modalNoHpUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Saudara & Anak Ke</td>
                                        <td>:</td>
                                        <td id="modalJumlahSaudaraAnakKeUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Cita-cita</td>
                                        <td>:</td>
                                        <td id="modalCitaCitaUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Hobi</td>
                                        <td>:</td>
                                        <td id="modalHobiUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Kebutuhan Khusus</td>
                                        <td>:</td>
                                        <td id="modalKebutuhanKhususUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Kebutuhan Disabilitas</td>
                                        <td>:</td>
                                        <td id="modalKebutuhanDisabilitasUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Biaya Sekolah</td>
                                        <td>:</td>
                                        <td id="modalYangBiayaSekolahUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Kepala Keluarga</td>
                                        <td>:</td>
                                        <td id="modalNamaKepalaKeluargaUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Status Verifikasi</td>
                                        <td>:</td>
                                        <td id="modalStatusUpdate" class="font-weight-bold"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Data Orang Tua -->
                    <div class="tab-pane fade" id="data-ortuUpdate" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-primary">Data Ayah</h6>
                                <table class="table table-borderless table-sm">
                                    <tr><td width="40%">Nama Ayah</td><td width="5%">:</td><td id="modalNamaAyahUpdate" class="font-weight-bold"></td></tr>
                                    <tr><td>Status Ayah</td><td>:</td><td id="modalStatusAyahUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ayah-hidup"><td>NIK Ayah</td><td>:</td><td id="modalNikAyahUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ayah-hidup"><td>TTL Ayah</td><td>:</td><td id="modalTempatTanggalLahirAyahUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ayah-hidup"><td>Pendidikan</td><td>:</td><td id="modalPendidikanTerakhirAyahUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ayah-hidup"><td>Pekerjaan</td><td>:</td><td id="modalPekerjaanAyahUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ayah-hidup"><td>Penghasilan</td><td>:</td><td id="modalPenghasilanAyahUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ayah-hidup"><td>No HP Ayah</td><td>:</td><td id="modalNoHpAyahUpdate" class="font-weight-bold"></td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-danger">Data Ibu</h6>
                                <table class="table table-borderless table-sm">
                                    <tr><td width="40%">Nama Ibu</td><td width="5%">:</td><td id="modalNamaIbuUpdate" class="font-weight-bold"></td></tr>
                                    <tr><td>Status Ibu</td><td>:</td><td id="modalStatusIbuUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ibu-hidup"><td>NIK Ibu</td><td>:</td><td id="modalNikIbuUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ibu-hidup"><td>TTL Ibu</td><td>:</td><td id="modalTempatTanggalLahirIbuUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ibu-hidup"><td>Pendidikan</td><td>:</td><td id="modalPendidikanTerakhirIbuUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ibu-hidup"><td>Pekerjaan</td><td>:</td><td id="modalPekerjaanIbuUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ibu-hidup"><td>Penghasilan</td><td>:</td><td id="modalPenghasilanIbuUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ibu-hidup"><td>No HP Ibu</td><td>:</td><td id="modalNoHpIbuUpdate" class="font-weight-bold"></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Data Alamat -->
                    <div class="tab-pane fade" id="data-alamatUpdate" role="tabpanel">
                        <table class="table table-borderless table-sm">
                            <tr><td width="25%">Alamat</td><td width="5%">:</td><td id="modalAlamatUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>RT / RW</td><td>:</td><td><span id="modalRTUpdate" class="font-weight-bold"></span> / <span id="modalRWUpdate" class="font-weight-bold"></span></td></tr>
                            <tr><td>Kelurahan / Desa</td><td>:</td><td id="modalKelurahanDesaUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Kecamatan</td><td>:</td><td id="modalKecamatanUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Kabupaten / Kota</td><td>:</td><td id="modalKabupatenKotaUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Provinsi</td><td>:</td><td id="modalProvinsiUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Jarak & Waktu</td><td>:</td><td><span id="modalJarakTempuhUpdate" class="font-weight-bold"></span> (<span id="modalWaktuTempuhUpdate"></span>)</td></tr>
                            <tr><td>Transportasi</td><td>:</td><td id="modalTransportasiUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Titik Koordinat</td><td>:</td><td id="modalTitikKoordinatUpdate"></td></tr>
                        </table>
                    </div>

                    <!-- Tab Lampiran -->
                    <div class="tab-pane fade" id="lampiranUpdate" role="tabpanel">
                        <table class="table table-bordered table-sm">
                            <tr><td width="40%">KK Santri</td><td id="modalLampiranKkSantriUpdate"></td></tr>
                            <tr><td>KK Ayah</td><td id="modalLampiranKkAyahUpdate"></td></tr>
                            <tr><td>KK Ibu</td><td id="modalLampiranKkIbuUpdate"></td></tr>
                            <tr><td>Kartu Indonesia Pintar (KIP)</td><td id="modalLampiranKIPUpdate"></td></tr>
                            <tr><td>Program Keluarga Harapan (PKH)</td><td id="modalLampiranPKHUpdate"></td></tr>
                            <tr><td>Kartu Keluarga Sejahtera (KKS)</td><td id="modalLampiranKKSUpdate"></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    // State aplikasi
    const dataSantriMaster = <?= json_encode($dataSantri); ?>;
    let filteredSantriList = [...dataSantriMaster];
    let currentSantriIndex = 0;
    let dataTableSantri = null;
    let isExtensionConnected = false;

    // Inisialisasi Halaman
    $(document).ready(function() {
        // Init DataTable
        dataTableSantri = $('#tblSantriEmis').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "pageLength": 25,
            "language": {
                "search": "Cari Santri:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Tidak ada data santri ditemukan",
                "info": "Menampilkan _START_ s/d _END_ dari _TOTAL_ santri",
                "infoEmpty": "Tidak ada data",
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "›",
                    "previous": "‹"
                }
            }
        });

        // Filter Kelas Event
        $('#filterKelasEmis').on('change', function() {
            const val = $(this).val();
            dataTableSantri.column(5).search(val ? '^' + val + '$' : '', true, false).draw();
            hitungUlangStatistik();
        });

        // Filter Status Event
        $('#filterStatusEmis').on('change', function() {
            const val = $(this).val();
            if (val === 'selesai') {
                dataTableSantri.column(2).search('Selesai').draw();
            } else if (val === 'belum') {
                dataTableSantri.column(2).search('Belum').draw();
            } else {
                dataTableSantri.column(2).search('').draw();
            }
            hitungUlangStatistik();
        });

        // Load Status dari localStorage
        loadSemuaStatusDariStorage();
        hitungUlangStatistik();

        // Mulai ping ke Ekstensi EMIS
        cekKoneksiExtension(false);
    });

    // ==============================================================
    // FITUR STATUS LOKAL (localStorage)
    // ==============================================================
    const STORAGE_KEY_EMIS = 'tpqsmart_emis_status_map';

    function getEmisStatusMap() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY_EMIS)) || {};
        } catch (e) {
            return {};
        }
    }

    function saveEmisStatusMap(map) {
        localStorage.setItem(STORAGE_KEY_EMIS, JSON.stringify(map));
    }

    function toggleStatusSantriEmis(idSantri) {
        const map = getEmisStatusMap();
        const isSelesai = map[idSantri] === true;
        map[idSantri] = !isSelesai;
        saveEmisStatusMap(map);
        updateStatusBadgeUI(idSantri, map[idSantri]);
        hitungUlangStatistik();
    }

    function setStatusSantriEmis(idSantri, isSelesai) {
        const map = getEmisStatusMap();
        map[idSantri] = isSelesai;
        saveEmisStatusMap(map);
        updateStatusBadgeUI(idSantri, isSelesai);
        hitungUlangStatistik();
    }

    function updateStatusBadgeUI(idSantri, isSelesai) {
        const cell = document.getElementById('col-status-' + idSantri);
        if (!cell) return;
        if (isSelesai) {
            cell.innerHTML = `
                <span class="badge badge-success p-2 status-badge cursor-pointer" onclick="toggleStatusSantriEmis('${idSantri}')" title="Klik untuk ubah">
                    <i class="fas fa-check-circle mr-1"></i> Selesai
                </span>
            `;
        } else {
            cell.innerHTML = `
                <span class="badge badge-warning p-2 status-badge cursor-pointer" onclick="toggleStatusSantriEmis('${idSantri}')" title="Klik untuk ubah">
                    <i class="fas fa-hourglass-half mr-1"></i> Belum
                </span>
            `;
        }
    }

    function loadSemuaStatusDariStorage() {
        const map = getEmisStatusMap();
        dataSantriMaster.forEach(s => {
            if (map[s.IdSantri] === true) {
                updateStatusBadgeUI(s.IdSantri, true);
            }
        });
    }

    function hitungUlangStatistik() {
        const map = getEmisStatusMap();
        let selesai = 0;
        dataSantriMaster.forEach(s => {
            if (map[s.IdSantri] === true) selesai++;
        });
        const total = dataSantriMaster.length;
        const belum = total - selesai;

        $('#statTotalSantri').text(total);
        $('#statSelesaiSantri').text(selesai);
        $('#statBelumSantri').text(belum);
    }

    function resetSemuaStatusEmis() {
        if (confirm('Apakah Anda yakin ingin mereset semua tanda status selesai?')) {
            localStorage.removeItem(STORAGE_KEY_EMIS);
            dataSantriMaster.forEach(s => updateStatusBadgeUI(s.IdSantri, false));
            hitungUlangStatistik();
        }
    }

    // ==============================================================
    // CLIPBOARD PANEL EMIS 4.0 (Phase 4)
    // ==============================================================
    function bukaPanelClipboard(idSantri) {
        const index = dataSantriMaster.findIndex(s => s.IdSantri === idSantri);
        if (index === -1) return;

        currentSantriIndex = index;
        tampilkanSantriDiModal(dataSantriMaster[currentSantriIndex]);
        $('#tab-step1-link').tab('show'); // Selalu mulai dari Step 1
        $('#modalClipboardEmis').modal('show');
    }

    function navigasiSantriNext() {
        if (currentSantriIndex < dataSantriMaster.length - 1) {
            currentSantriIndex++;
            tampilkanSantriDiModal(dataSantriMaster[currentSantriIndex]);
            $('#tab-step1-link').tab('show');
        } else {
            alert('Ini adalah santri terakhir dalam daftar.');
        }
    }

    function navigasiSantriPrev() {
        if (currentSantriIndex > 0) {
            currentSantriIndex--;
            tampilkanSantriDiModal(dataSantriMaster[currentSantriIndex]);
            $('#tab-step1-link').tab('show');
        } else {
            alert('Ini adalah santri pertama dalam daftar.');
        }
    }

    function tampilkanSantriDiModal(santri) {
        if (!santri) return;

        // Ambil data terformat dari API agar transformasinya presisi
        $.ajax({
            url: '<?= base_url('backend/santri/getSantriEmisJson'); ?>/' + santri.IdSantri,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.data) {
                    renderSantriDataToModal(res.data);
                } else {
                    renderSantriDataFallback(santri);
                }
            },
            error: function() {
                renderSantriDataFallback(santri);
            }
        });
    }

    function renderSantriDataToModal(d) {
        $('#clipSantriNamaBadge').text(d.nama_santri);
        $('#clipSantriKelasBadge').text(d.tingkat_kelas || d.nama_kelas_asal);
        $('#clipSantriNikBadge').text('NIK: ' + (d.nik || 'Belum ada'));
        $('#clipNavIndex').text((currentSantriIndex + 1) + ' / ' + dataSantriMaster.length);

        // DATA STEP 1 (Pencarian / Validasi Awal)
        $('#step1NamaSantri').val(d.nama_santri_clean || d.nama_santri);
        $('#step1TempatLahir').val(d.tempat_lahir_clean || d.tempat_lahir);
        $('#step1TanggalLahir').val(d.tanggal_lahir);
        $('#step1Nik').val(d.nik);
        $('#step1Nisn').val(d.nisn);

        // DATA STEP 2 - Seksi 1: Data Pendaftaran
        $('#fieldTanggalMasuk').val(d.tanggal_masuk);
        $('#fieldTingkatKelas').val(d.tingkat_kelas);

        // DATA STEP 2 - Seksi 2: Calon Santri
        $('#fieldNamaSantri').val(d.nama_santri);
        $('#fieldKewarganegaraan').val(d.kewarganegaraan || 'WNI');
        $('#fieldAgama').val(d.agama || 'Islam');
        $('#fieldNik').val(d.nik);
        $('#fieldNisn').val(d.nisn);
        $('#fieldJenisKelamin').val(d.jenis_kelamin);
        $('#fieldTempatLahir').val(d.tempat_lahir);
        $('#fieldTanggalLahir').val(d.tanggal_lahir);
        $('#fieldNoHp').val(d.no_hp);

        // Checkbox status indicators
        if (d.belum_punya_nik) {
            $('#badgeBelumPunyaNik').removeClass('d-none');
        } else {
            $('#badgeBelumPunyaNik').addClass('d-none');
        }

        if (d.belum_punya_nisn) {
            $('#badgeBelumPunyaNisn').removeClass('d-none');
        } else {
            $('#badgeBelumPunyaNisn').addClass('d-none');
        }

        if (d.tidak_punya_hp) {
            $('#badgeTidakPunyaHp').removeClass('d-none');
        } else {
            $('#badgeTidakPunyaHp').addClass('d-none');
        }

        // DATA STEP 2 - Seksi 3: Orang Tua & Wali
        $('#fieldNamaAyah').val(d.nama_ayah);
        $('#fieldStatusAyah').val(d.status_ayah);
        $('#fieldNamaIbu').val(d.nama_ibu);
        $('#fieldStatusIbu').val(d.status_ibu);
        $('#fieldStatusWali').val(d.status_wali);

        // Update tombol status selesai modal
        const map = getEmisStatusMap();
        const isSelesai = map[d.id_santri] === true;
        updateModalSelesaiButton(isSelesai);

        // Reset semua button copy ke teks semula
        $('.btn-copy').html('<i class="fas fa-copy mr-1"></i> Salin').removeClass('btn-success').addClass('btn-primary');
    }

    function renderSantriDataFallback(santri) {
        // Fallback jika offline / request gagal
        $('#clipSantriNamaBadge').text(santri.NamaSantri);
        $('#clipSantriKelasBadge').text(santri.NamaKelas);
        $('#clipSantriNikBadge').text('NIK: ' + (santri.NikSantri || '-'));
        
        // Step 1
        $('#step1NamaSantri').val(santri.NamaSantri);
        $('#step1TempatLahir').val(santri.TempatLahirSantri);
        $('#step1TanggalLahir').val(santri.TanggalLahirSantri);
        $('#step1Nik').val(santri.NikSantri);
        $('#step1Nisn').val(santri.NIS || santri.NISN || '');

        // Step 2
        $('#fieldNamaSantri').val(santri.NamaSantri);
        $('#fieldNik').val(santri.NikSantri);
        $('#fieldTingkatKelas').val(santri.NamaKelas);
        $('#fieldTanggalMasuk').val(santri.created_at ? santri.created_at.substring(0, 10) : '');
        $('#fieldJenisKelamin').val(santri.JenisKelamin);
        $('#fieldTempatLahir').val(santri.TempatLahirSantri);
        $('#fieldTanggalLahir').val(santri.TanggalLahirSantri);
        $('#fieldNamaAyah').val(santri.NamaAyah);
        $('#fieldStatusAyah').val(santri.StatusAyah || 'Masih Hidup');
        $('#fieldNamaIbu').val(santri.NamaIbu);
        $('#fieldStatusIbu').val(santri.StatusIbu || 'Masih Hidup');
        $('#fieldStatusWali').val(santri.StatusWali || 'Sama dengan ayah kandung');
    }

    function toggleStatusModalSantri() {
        const santri = dataSantriMaster[currentSantriIndex];
        if (!santri) return;
        toggleStatusSantriEmis(santri.IdSantri);
        const map = getEmisStatusMap();
        updateModalSelesaiButton(map[santri.IdSantri] === true);
    }

    function updateModalSelesaiButton(isSelesai) {
        const btn = $('#btnTandaiSelesaiModal');
        if (isSelesai) {
            btn.html('<i class="fas fa-check-circle mr-1"></i> Status: Sudah Selesai').removeClass('btn-warning').addClass('btn-success');
        } else {
            btn.html('<i class="fas fa-check mr-1"></i> Tandai Sudah Disalin').removeClass('btn-success').addClass('btn-warning');
        }
    }

    // Copy Single Value ke Clipboard
    function copyFieldVal(elementId, btnElement) {
        const el = document.getElementById(elementId);
        if (!el) return;
        const text = el.value || '';

        navigator.clipboard.writeText(text).then(() => {
            const $btn = $(btnElement);
            const originalHtml = $btn.html();
            $btn.html('<i class="fas fa-check mr-1"></i> Tersalin!').removeClass('btn-primary').addClass('btn-success');
            setTimeout(() => {
                $btn.html(originalHtml).removeClass('btn-success').addClass('btn-primary');
            }, 1500);
        }).catch(err => {
            // Fallback manual copy
            el.select();
            document.execCommand('copy');
            alert('Tersalin ke clipboard: ' + text);
        });
    }

    // Salin Semua Field Sekaligus
    function salinSemuaFieldText() {
        const nama = $('#fieldNamaSantri').val();
        const nik = $('#fieldNik').val();
        const nisn = $('#fieldNisn').val();
        const kelas = $('#fieldTingkatKelas').val();
        const jk = $('#fieldJenisKelamin').val();
        const ttl = $('#fieldTempatLahir').val() + ', ' + $('#fieldTanggalLahir').val();
        const hp = $('#fieldNoHp').val();
        const ayah = $('#fieldNamaAyah').val() + ' (' + $('#fieldStatusAyah').val() + ')';
        const ibu = $('#fieldNamaIbu').val() + ' (' + $('#fieldStatusIbu').val() + ')';
        const wali = $('#fieldStatusWali').val();

        const formatted = `DATA SANTRI UNTUK EMIS 4.0:
Nama Lengkap : ${nama}
Tingkat Kelas: ${kelas}
NIK          : ${nik}
NISN         : ${nisn}
Jenis Kelamin: ${jk}
TTL          : ${ttl}
Agama        : Islam
Kewarganegaraan: WNI
No Handphone : ${hp}
Ayah Kandung : ${ayah}
Ibu Kandung  : ${ibu}
Status Wali  : ${wali}`;

        navigator.clipboard.writeText(formatted).then(() => {
            alert('Semua data santri berhasil disalin ke clipboard!');
        });
    }

    // ==============================================================
    // KOMUNIKASI CHROME EXTENSION (Phase 3 & Protocol)
    // ==============================================================
    window.addEventListener('message', function(event) {
        // Abaikan jika bukan dari domain sendiri / extension
        if (!event.data || !event.data.type) return;

        if (event.data.type === 'EMIS_EXTENSION_PONG') {
            isExtensionConnected = true;
            $('#extStatusBadge')
                .removeClass('badge-secondary badge-danger')
                .addClass('badge-success')
                .html('<i class="fas fa-check-circle mr-1"></i> Ekstensi EMIS Aktif');
            $('#guideExtStatus')
                .removeClass('badge-secondary')
                .addClass('badge-success')
                .text('Terhubung! Ekstensi siap digunakan.');
        }

        if (event.data.type === 'EMIS_EXTENSION_NEED_REFRESH') {
            if (confirm('Ekstensi EMIS baru saja diperbarui di browser.\n\nKlik OK untuk me-refresh halaman TPQ Smart ini sekarang agar koneksi tersambung kembali.')) {
                window.location.reload();
            }
        }

        if (event.data.type === 'EMIS_EXTENSION_FILLED_SUCCESS') {
            const santriId = event.data.id_santri;
            if (santriId) {
                setStatusSantriEmis(santriId, true);
                alert('Data santri berhasil diisikan ke tab EMIS! Silakan verifikasi dan klik tombol Simpan di EMIS.');
            }
        }
    });

    function cekKoneksiExtension(showAlert = false) {
        window.postMessage({ type: 'TPQ_TO_EMIS_EXTENSION_PING' }, '*');
        setTimeout(() => {
            if (!isExtensionConnected) {
                $('#extStatusBadge')
                    .removeClass('badge-secondary badge-success')
                    .addClass('badge-secondary')
                    .html('<i class="fas fa-exclamation-circle mr-1"></i> Ekstensi Belum Aktif');
                $('#guideExtStatus')
                    .removeClass('badge-success')
                    .addClass('badge-warning')
                    .text('Belum terdeteksi. Pastikan folder ekstensi sudah dimuat di chrome://extensions');
                if (showAlert) {
                    alert('Ekstensi belum terdeteksi. Silakan ikuti panduan pemasangan di jendela ini.');
                }
            }
        }, 1200);
    }

    function kirimKeEmisExtension(idSantri) {
        // Ambil data santri via API endpoint
        $.ajax({
            url: '<?= base_url('backend/santri/getSantriEmisJson'); ?>/' + idSantri,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.data) {
                    if (isExtensionConnected) {
                        // Kirim data ke background / content script extension
                        window.postMessage({
                            type: 'TPQ_SEND_TO_EMIS',
                            data: res.data
                        }, '*');
                    } else {
                        // Jika extension belum terpasang, buka modal panduan atau modal clipboard
                        if (confirm('Ekstensi Chrome belum terdeteksi.\n\nApakah Anda ingin membuka Panel Salin Manual sekarang? (Atau klik Batal untuk melihat info pemasangan ekstensi)')) {
                            bukaPanelClipboard(idSantri);
                        } else {
                            $('#modalPanduanExtension').modal('show');
                        }
                    }
                }
            },
            error: function() {
                alert('Gagal mengambil data santri dari server.');
            }
        });
    }

    // ==============================================================
    // DETAIL LENGKAP SANTRI (Original View Detail Function)
    // ==============================================================
    function showUpdateEmisSantri(IdSantri) {
        const santri = dataSantriMaster.find(s => s.IdSantri === IdSantri);
        if (!santri) return;

        const uploadPath = '<?= (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/' : base_url('uploads/santri/') ?>';
        document.getElementById('modalPhotoProfilUpdate').src = santri.PhotoProfil ? uploadPath + santri.PhotoProfil : '<?= base_url('images/no-photo.jpg') ?>';
        document.getElementById('modalIdSantriUpdate').textContent = santri.IdSantri;
        document.getElementById('modalNamaTpqUpdate').textContent = santri.NamaTpq || '-';
        document.getElementById('modalKelasUpdate').textContent = santri.NamaKelas || '-';
        document.getElementById('modalNISUpdate').textContent = santri.NIS || santri.NISN || '-';
        document.getElementById('modalNikSantriUpdate').textContent = santri.NikSantri || '-';
        document.getElementById('modalIdKartuKeluargaUpdate').textContent = santri.IdKartuKeluarga || '-';
        document.getElementById('modalNamaSantriUpdate').textContent = santri.NamaSantri || '-';
        document.getElementById('modalJenisKelaminUpdate').textContent = santri.JenisKelamin || '-';
        document.getElementById('modalTempatTanggalLahirUpdate').textContent = (santri.TempatLahirSantri || '-') + ', ' + (santri.TanggalLahirSantri || '-');
        document.getElementById('modalNoHpUpdate').textContent = (santri.NoHpSantri || '-') + ' | ' + (santri.EmailSantri || '-');
        document.getElementById('modalJumlahSaudaraAnakKeUpdate').textContent = 'Jumlah Saudara: ' + (santri.JumlahSaudara || '-') + ' Anak Ke-' + (santri.AnakKe || '-');
        document.getElementById('modalCitaCitaUpdate').textContent = santri.CitaCita || santri.CitaCitaLainya || '-';
        document.getElementById('modalHobiUpdate').textContent = santri.Hobi || santri.HobiLainya || '-';
        document.getElementById('modalKebutuhanKhususUpdate').textContent = santri.KebutuhanKhusus || santri.KebutuhanKhususLainya || '-';
        document.getElementById('modalKebutuhanDisabilitasUpdate').textContent = santri.KebutuhanDisabilitas || santri.KebutuhanDisabilitasLainya || '-';
        document.getElementById('modalYangBiayaSekolahUpdate').textContent = santri.YangBiayaSekolah || '-';
        document.getElementById('modalNamaKepalaKeluargaUpdate').textContent = santri.NamaKepalaKeluarga || '-';

        const statusElement = document.getElementById('modalStatusUpdate');
        let badgeClass = 'bg-success';
        if (santri.Status === 'Belum Diverifikasi') {
            badgeClass = 'bg-warning';
        } else if (santri.Status === 'Perlu Perbaikan') {
            badgeClass = 'bg-danger';
        }
        statusElement.innerHTML = `<span class="badge ${badgeClass}">${santri.Status || '-'}</span>`;

        // Data Ayah
        document.getElementById('modalNamaAyahUpdate').textContent = santri.NamaAyah || '-';
        document.getElementById('modalStatusAyahUpdate').textContent = santri.StatusAyah || '-';
        if (santri.StatusAyah === 'Masih Hidup') {
            document.getElementById('modalNikAyahUpdate').textContent = santri.NikAyah || '-';
            document.getElementById('modalTempatTanggalLahirAyahUpdate').textContent = (santri.TempatLahirAyah || '-') + ', ' + (santri.TanggalLahirAyah || '-');
            document.getElementById('modalPendidikanTerakhirAyahUpdate').textContent = santri.PendidikanAyah || '-';
            document.getElementById('modalPekerjaanAyahUpdate').textContent = santri.PekerjaanUtamaAyah || '-';
            document.getElementById('modalPenghasilanAyahUpdate').textContent = santri.PenghasilanUtamaAyah || '-';
            document.getElementById('modalNoHpAyahUpdate').textContent = santri.NoHpAyah || '-';
            document.querySelectorAll('.data-ayah-hidup').forEach(el => el.style.display = 'table-row');
        } else {
            document.querySelectorAll('.data-ayah-hidup').forEach(el => el.style.display = 'none');
        }

        // Data Ibu
        document.getElementById('modalNamaIbuUpdate').textContent = santri.NamaIbu || '-';
        document.getElementById('modalStatusIbuUpdate').textContent = santri.StatusIbu || '-';
        if (santri.StatusIbu === 'Masih Hidup') {
            document.getElementById('modalNikIbuUpdate').textContent = santri.NikIbu || '-';
            document.getElementById('modalTempatTanggalLahirIbuUpdate').textContent = (santri.TempatLahirIbu || '-') + ', ' + (santri.TanggalLahirIbu || '-');
            document.getElementById('modalPendidikanTerakhirIbuUpdate').textContent = santri.PendidikanIbu || '-';
            document.getElementById('modalPekerjaanIbuUpdate').textContent = santri.PekerjaanUtamaIbu || '-';
            document.getElementById('modalPenghasilanIbuUpdate').textContent = santri.PenghasilanUtamaIbu || '-';
            document.getElementById('modalNoHpIbuUpdate').textContent = santri.NoHpIbu || '-';
            document.querySelectorAll('.data-ibu-hidup').forEach(el => el.style.display = 'table-row');
        } else {
            document.querySelectorAll('.data-ibu-hidup').forEach(el => el.style.display = 'none');
        }

        // Alamat
        document.getElementById('modalAlamatUpdate').textContent = santri.AlamatSantri || '-';
        document.getElementById('modalRTUpdate').textContent = santri.RtSantri || '-';
        document.getElementById('modalRWUpdate').textContent = santri.RwSantri || '-';
        document.getElementById('modalKelurahanDesaUpdate').textContent = santri.KelurahanDesaSantri || '-';
        document.getElementById('modalKecamatanUpdate').textContent = santri.KecamatanSantri || '-';
        document.getElementById('modalKabupatenKotaUpdate').textContent = santri.KabupatenKotaSantri || '-';
        document.getElementById('modalProvinsiUpdate').textContent = santri.ProvinsiSantri || '-';
        document.getElementById('modalJarakTempuhUpdate').textContent = santri.JarakTempuhSantri || '-';
        document.getElementById('modalTransportasiUpdate').textContent = santri.TransportasiSantri || '-';
        document.getElementById('modalWaktuTempuhUpdate').textContent = santri.WaktuTempuhSantri || '-';

        if (santri.TitikKoordinatSantri) {
            const coords = santri.TitikKoordinatSantri.split(',');
            const mapsLink = `https://www.google.com/maps?q=${coords[0]},${coords[1]}`;
            document.getElementById('modalTitikKoordinatUpdate').innerHTML = `
                <a href="${mapsLink}" target="_blank" class="btn btn-sm btn-info">
                    <i class="fas fa-map-marker-alt"></i> Lihat di Google Maps
                </a>
                <span class="ml-2">${santri.TitikKoordinatSantri}</span>
            `;
        } else {
            document.getElementById('modalTitikKoordinatUpdate').textContent = '-';
        }

        function createFileLink(fileName, path) {
            if (!fileName || fileName.trim() === '') {
                return '<span class="text-muted">Tidak ada file</span>';
            }
            const fullPath = path + fileName;
            return `<a href="${fullPath}" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-file-download mr-1"></i> Lihat File</a>`;
        }

        document.getElementById('modalLampiranKkSantriUpdate').innerHTML = createFileLink(santri?.FileKkSantri, uploadPath);
        document.getElementById('modalLampiranKkAyahUpdate').innerHTML = createFileLink(santri?.FileKkAyah, uploadPath);
        document.getElementById('modalLampiranKkIbuUpdate').innerHTML = createFileLink(santri?.FileKkIbu, uploadPath);
        document.getElementById('modalLampiranKIPUpdate').innerHTML = createFileLink(santri?.FileKIP, uploadPath);
        document.getElementById('modalLampiranPKHUpdate').innerHTML = createFileLink(santri?.FilePKH, uploadPath);
        document.getElementById('modalLampiranKKSUpdate').innerHTML = createFileLink(santri?.FileKKS, uploadPath);

        $('#updateDataEmisModal').modal('show');
    }
</script>
<style>
    .cursor-pointer { cursor: pointer; }
    .status-badge:hover { opacity: 0.85; }
    .font-monospace { font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
</style>
<?= $this->endSection(); ?>
