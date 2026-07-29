<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<style>
    /* Styling AdminLTE Native Form Control & Custom Select Match */
    .form-control-adminlte, 
    select.form-control, 
    select.form-select {
        height: calc(2.25rem + 2px) !important;
        padding: .375rem .75rem !important;
        font-size: 1rem !important;
        font-weight: 400 !important;
        line-height: 1.5 !important;
        color: #495057 !important;
        background-color: #fff !important;
        border: 1px solid #ced4da !important;
        border-radius: .25rem !important;
        box-shadow: none !important;
        appearance: auto !important;
        -webkit-appearance: auto !important;
    }
    select.form-control:focus, 
    select.form-select:focus,
    input.form-control:focus {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-calendar-plus text-success me-2"></i>
                    <?= isset($jadwal) ? 'Edit Pengaturan Jadwal Ujian' : 'Buat Jadwal Ujian MDTA Baru' ?>
                </h4>
                <small class="text-muted">Konfigurasi tanggal, waktu, paket soal, nilai KKM, dan visibilitas hasil evaluasi</small>
            </div>
            <a href="<?= base_url('backend/ujian-mdta/jadwal') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Jadwal
            </a>
        </div>
    </div>

    <!-- Alert Errors -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i>Terdapat kesalahan pengisian:</h6>
            <ul class="mb-0 ps-3">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Info Mode Edit Aman Jika Data Sudah Berjalan -->
    <?php if (!empty($totalSesi) && $totalSesi > 0): ?>
        <div class="alert alert-info border-start border-4 border-info shadow-sm p-3 mb-4 rounded-3 d-flex align-items-center justify-content-between">
            <div>
                <h6 class="fw-bold text-dark mb-1">
                    <i class="fas fa-shield-alt me-2 text-info fs-5"></i> Mode Edit Aman (Terdeteksi <?= $totalSesi ?> Data Sesi Santri)
                </h6>
                <p class="small text-muted mb-0">
                    Anda bebas mengedit Nama Ujian, Waktu Mulai/Selesai, Durasi, KKM, dan Visibilitas Hasil <strong>tanpa mempengaruhi atau menghapus data nilai santri</strong>.<br>
                    <span class="text-danger fw-semibold"><i class="fas fa-exclamation-triangle me-1"></i>Catatan:</span> Mengubah Paket Soal atau Jumlah Soal akan memberikan konfirmasi lanjutan sebelum mengosongkan data sesi santri.
                </p>
            </div>
            <span class="badge bg-info text-white fs-6 px-3 py-2">
                <i class="fas fa-users me-1"></i> <?= $totalSesi ?> Sesi Aktif
            </span>
        </div>
    <?php endif; ?>

    <form id="formJadwalUjian" method="post" action="<?= isset($jadwal) ? base_url("backend/ujian-mdta/jadwal/update/{$jadwal['id']}") : base_url('backend/ujian-mdta/jadwal/save') ?>">
        <?= csrf_field() ?>

        <!-- Hidden Tracking Fields -->
        <input type="hidden" name="OriginalIdPaket" id="originalIdPaket" value="<?= $jadwal['IdPaket'] ?? '' ?>">
        <input type="hidden" name="OriginalJumlahSoal" id="originalJumlahSoal" value="<?= $jadwal['JumlahSoal'] ?? '' ?>">
        <input type="hidden" name="TotalSesi" id="totalSesiCount" value="<?= $totalSesi ?? 0 ?>">
        <input type="hidden" name="ConfirmResetData" id="inputConfirmResetData" value="0">

        <!-- CARD 1: INFORMASI UTAMA & PAKET SOAL -->
        <div class="card card-outline card-success shadow-sm mb-4">
            <div class="card-header bg-light py-3">
                <h5 class="card-title mb-0 text-success fw-bold">
                    <i class="fas fa-info-circle me-2"></i>1. Informasi Utama & Paket Soal
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <!-- Nama Ujian -->
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-dark">Nama Ujian MDTA <span class="text-danger">*</span></label>
                        <input type="text" name="NamaUjian" class="form-control form-control-adminlte" placeholder="Contoh: UJIAN MID SEMESTER FIQIH KELAS 4" required value="<?= old('NamaUjian', $jadwal['NamaUjian'] ?? '') ?>">
                    </div>

                    <?php if (!empty($isUserAdmin)): ?>
                        <?php
                        $targetTpqVal = $jadwal['TargetTpq'] ?? 'all';
                        $isAllTpq     = ($targetTpqVal === 'all' || empty($targetTpqVal));
                        $selectedTpqArr = !$isAllTpq ? explode(',', $targetTpqVal) : [];
                        ?>
                        <div class="col-md-12">
                            <div class="card bg-light border-primary p-3 border shadow-xs rounded-3 mb-2">
                                <label class="form-label fw-bold text-primary mb-2">
                                    <i class="fas fa-building me-1"></i> Target TPQ / Lembaga Peserta (Khusus Admin Pusat)
                                </label>
                                <div class="d-flex gap-4 align-items-center mb-2">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="targetTypeAll" name="TargetType" value="all" <?= $isAllTpq ? 'checked' : '' ?> onchange="toggleTargetTpqList()">
                                        <label for="targetTypeAll" class="custom-control-label fw-bold text-dark">
                                            Semua TPQ (Global - UAS / UTS Serentak)
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="targetTypeSelected" name="TargetType" value="selected" <?= !$isAllTpq ? 'checked' : '' ?> onchange="toggleTargetTpqList()">
                                        <label for="targetTypeSelected" class="custom-control-label fw-bold text-dark">
                                            Pilih TPQ Tertentu Saja
                                        </label>
                                    </div>
                                </div>

                                <!-- Multi Select / Checkbox list of TPQs -->
                                <div id="wrapperTargetTpqList" style="<?= $isAllTpq ? 'display:none;' : '' ?>" class="mt-2 pt-2 border-top">
                                    <small class="text-muted d-block mb-2">Centang TPQ yang berhak mengikuti ujian ini:</small>
                                    <div class="row g-2" style="max-height: 180px; overflow-y: auto;">
                                        <?php if (!empty($tpqList)): ?>
                                            <?php foreach ($tpqList as $tpq): ?>
                                                <?php $tpqId = (string)($tpq['IdTpq'] ?? $tpq['id'] ?? ''); ?>
                                                <div class="col-md-4 col-sm-6">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" name="TargetTpqList[]" id="tpq_<?= $tpqId ?>" value="<?= $tpqId ?>" <?= in_array($tpqId, $selectedTpqArr) ? 'checked' : '' ?>>
                                                        <label for="tpq_<?= $tpqId ?>" class="custom-control-label small fw-semibold text-secondary">
                                                            <?= esc($tpq['NamaTpq'] ?? $tpq['NamaLembaga'] ?? "TPQ #{$tpqId}") ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="col-12"><small class="text-muted">Tidak ada data TPQ terdaftar.</small></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                        function toggleTargetTpqList() {
                            const isSelected = document.getElementById('targetTypeSelected').checked;
                            document.getElementById('wrapperTargetTpqList').style.display = isSelected ? 'block' : 'none';
                        }
                        </script>
                    <?php endif; ?>

                    <!-- Target Kelas -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">Kelas Peserta <span class="text-danger">*</span></label>
                        <select name="IdKelas" id="selectKelas" class="form-control select2 custom-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelasList as $k): ?>
                                <option value="<?= $k['IdKelas'] ?>" <?= old('IdKelas', $jadwal['IdKelas'] ?? '') == $k['IdKelas'] ? 'selected' : '' ?>>
                                    <?= esc($k['NamaKelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Paket Soal -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">
                            Paket Soal Yang Digunakan <span class="text-danger">*</span>
                            <?php if (!empty($totalSesi) && $totalSesi > 0): ?>
                                <span class="badge bg-warning text-dark ms-1" style="font-size:10px;" title="Mengubah paket soal pada ujian berjalan memerlukan konfirmasi reset data">
                                    <i class="fas fa-exclamation-circle"></i> Berdampak Data
                                </span>
                            <?php endif; ?>
                        </label>
                        <select name="IdPaket" id="selectPaket" class="form-control select2 custom-select" required>
                            <option value="">-- Pilih Paket Soal --</option>
                            <?php foreach ($paketList as $p): ?>
                                <option value="<?= $p['id'] ?>" data-jumlah="<?= $p['JumlahSoal'] ?>" <?= old('IdPaket', $jadwal['IdPaket'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                                    <?= esc($p['NamaPaket']) ?> (<?= $p['JumlahSoal'] ?> Soal Aktif)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tahun Ajaran -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">Tahun Ajaran <span class="text-danger">*</span></label>
                        <select name="IdTahunAjaran" class="form-control select2 custom-select" required>
                            <?php foreach ($taList as $ta): ?>
                                <option value="<?= $ta ?>" <?= old('IdTahunAjaran', $jadwal['IdTahunAjaran'] ?? session()->get('IdTahunAjaran')) == $ta ? 'selected' : '' ?>>
                                    <?= convertTahunAjaran($ta) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Semester & Tipe Evaluasi -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-dark">Semester / Tipe Ujian <span class="text-danger">*</span></label>
                        <select name="Semester" id="selectSemester" class="form-control select2 custom-select" required>
                            <option value="1" <?= old('Semester', $jadwal['Semester'] ?? '1') === '1' ? 'selected' : '' ?>>Semester 1 (Ganjil)</option>
                            <option value="2" <?= old('Semester', $jadwal['Semester'] ?? '') === '2' ? 'selected' : '' ?>>Semester 2 (Genap)</option>
                            <?php if (!empty($isFullAccess)): ?>
                                <optgroup label="Tipe Evaluasi Lainnya">
                                    <option value="mingguan" <?= old('Semester', $jadwal['Semester'] ?? '') === 'mingguan' ? 'selected' : '' ?>>Ujian Harian / Mingguan</option>
                                    <option value="uts" <?= old('Semester', $jadwal['Semester'] ?? '') === 'uts' ? 'selected' : '' ?>>Ujian Tengah Semester (UTS)</option>
                                    <option value="uas" <?= old('Semester', $jadwal['Semester'] ?? '') === 'uas' ? 'selected' : '' ?>>Ujian Akhir Semester (UAS)</option>
                                </optgroup>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Status Terbit Jadwal -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-dark">Status Terbit <span class="text-danger">*</span></label>
                        <select name="Status" class="form-control custom-select" required>
                            <option value="aktif" <?= old('Status', $jadwal['Status'] ?? 'aktif') === 'aktif' ? 'selected' : '' ?>>🟢 Aktif (Terbitkan ke Santri)</option>
                            <option value="draft" <?= old('Status', $jadwal['Status'] ?? '') === 'draft' ? 'selected' : '' ?>>⚪ Draft (Simpan Draf)</option>
                            <option value="pause" <?= old('Status', $jadwal['Status'] ?? '') === 'pause' ? 'selected' : '' ?>>🟡 Pause (Jeda Sementara)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 2: PENGATURAN WAKTU & NILAI KKM -->
        <div class="card card-outline card-info shadow-sm mb-4">
            <div class="card-header bg-light py-3">
                <h5 class="card-title mb-0 text-info fw-bold">
                    <i class="fas fa-clock me-2"></i>2. Pengaturan Waktu Pelaksanaan & KKM
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Waktu Mulai Pelaksanaan <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="TanggalMulai" class="form-control form-control-adminlte" required value="<?= old('TanggalMulai', isset($jadwal['TanggalMulai']) ? date('Y-m-d\TH:i', strtotime($jadwal['TanggalMulai'])) : date('Y-m-d\TH:i')) ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Waktu Selesai Pelaksanaan <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="TanggalSelesai" class="form-control form-control-adminlte" required value="<?= old('TanggalSelesai', isset($jadwal['TanggalSelesai']) ? date('Y-m-d\TH:i', strtotime($jadwal['TanggalSelesai'])) : date('Y-m-d\TH:i', strtotime('+7 days'))) ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Durasi Pengerjaan Santri <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="DurasiMenit" class="form-control form-control-adminlte" required min="5" max="300" value="<?= old('DurasiMenit', $jadwal['DurasiMenit'] ?? 60) ?>">
                            <span class="input-group-text bg-light fw-bold text-dark">Menit</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">
                            Jumlah Soal Diambil Acak <span class="text-danger">*</span>
                            <?php if (!empty($totalSesi) && $totalSesi > 0): ?>
                                <span class="badge bg-warning text-dark ms-1" style="font-size:10px;" title="Mengubah jumlah soal pada ujian berjalan memerlukan konfirmasi reset data">
                                    <i class="fas fa-exclamation-circle"></i> Berdampak Data
                                </span>
                            <?php endif; ?>
                        </label>
                        <input type="number" name="JumlahSoal" id="inputJumlahSoal" class="form-control form-control-adminlte" required min="1" value="<?= old('JumlahSoal', $jadwal['JumlahSoal'] ?? 10) ?>">
                        <small class="text-muted" id="infoTotalSoal">Diambil acak dari total soal di paket.</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Nilai Minimum KKM <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="NilaiMinimum" class="form-control form-control-adminlte" required min="0" max="100" value="<?= old('NilaiMinimum', $jadwal['NilaiMinimum'] ?? 70.00) ?>">
                        <small class="text-muted">Nilai di bawah ini dinyatakan belum lulus.</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Jumlah Pilihan Jawaban Tampil <span class="text-danger">*</span></label>
                        <select name="JumlahPilihan" id="selectJumlahPilihan" class="form-control custom-select" required>
                            <option value="2" <?= old('JumlahPilihan', $jadwal['JumlahPilihan'] ?? 4) == 2 ? 'selected' : '' ?>>A - B (2 Pilihan)</option>
                            <option value="3" <?= old('JumlahPilihan', $jadwal['JumlahPilihan'] ?? 4) == 3 ? 'selected' : '' ?>>A - C (3 Pilihan)</option>
                            <option value="4" <?= old('JumlahPilihan', $jadwal['JumlahPilihan'] ?? 4) == 4 ? 'selected' : '' ?>>A - D (4 Pilihan - Standar)</option>
                            <option value="5" <?= old('JumlahPilihan', $jadwal['JumlahPilihan'] ?? 4) == 5 ? 'selected' : '' ?>>A - E (5 Pilihan)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Jenis Soal Tampil / Mode Ujian <span class="text-danger">*</span></label>
                        <select name="ModeSoal" id="selectModeSoal" class="form-control custom-select" required>
                            <option value="campuran" <?= old('ModeSoal', $jadwal['ModeSoal'] ?? 'campuran') === 'campuran' ? 'selected' : '' ?>>Pilihan Ganda & Esai (Campuran)</option>
                            <option value="pg" <?= old('ModeSoal', $jadwal['ModeSoal'] ?? '') === 'pg' ? 'selected' : '' ?>>Hanya Pilihan Ganda Saja</option>
                            <option value="esai" <?= old('ModeSoal', $jadwal['ModeSoal'] ?? '') === 'esai' ? 'selected' : '' ?>>Hanya Esai Saja</option>
                        </select>
                        <small class="text-muted">Pilih jenis soal yang akan ditampilkan pada lembar ujian santri.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 3: OPSI PENGACAKAN, REMEDIAL & VISIBILITAS HASIL -->
        <div class="card card-outline card-warning shadow-sm mb-4">
            <div class="card-header bg-light py-3">
                <h5 class="card-title mb-0 text-dark fw-bold">
                    <i class="fas fa-sliders-h me-2 text-warning"></i>3. Opsi Pengacakan, Remedial & Visibilitas Evaluasi Santri
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Opsi Pengacakan -->
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-random me-2 text-primary"></i>Opsi Pengacakan & Remedial</h6>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="AcakSoal" id="AcakSoal" value="1" <?= old('AcakSoal', $jadwal['AcakSoal'] ?? 1) == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold text-dark" for="AcakSoal">Acak Urutan Soal Tiap Santri</label>
                            <small class="text-muted d-block">Urutan nomor soal akan diacak unik untuk masing-masing santri.</small>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="AcakJawaban" id="AcakJawaban" value="1" <?= old('AcakJawaban', $jadwal['AcakJawaban'] ?? 1) == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold text-dark" for="AcakJawaban">Acak Urutan Pilihan Jawaban Tiap Santri</label>
                            <small class="text-muted d-block">Pilihan A, B, C, D diacak urutannya pada tiap santri.</small>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="BolehRemedial" id="BolehRemedial" value="1" <?= old('BolehRemedial', $jadwal['BolehRemedial'] ?? 1) == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold text-dark" for="BolehRemedial">Izinkan Remedial Jika Belum Lulus</label>
                            <small class="text-muted d-block">Santri yang nilainya di bawah KKM berhak mengikuti ujian remedial yang dibuka pengawas.</small>
                        </div>
                    </div>

                    <!-- Opsi Visibilitas Hasil Santri -->
                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-eye me-2 text-success"></i>Tampilan Hasil Evaluasi Santri</h6>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="TampilSoalJawaban" id="TampilSoalJawaban" value="1" <?= old('TampilSoalJawaban', $jadwal['TampilSoalJawaban'] ?? 1) == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold text-dark" for="TampilSoalJawaban">Tampilkan Rincian Soal & Jawaban Santri di Hasil Ujian</label>
                            <small class="text-muted d-block">Jika aktif, santri dapat melihat kembali daftar rincian soal dan jawaban yang telah diketik/dipilih setelah ujian selesai.</small>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="TampilKunciJawaban" id="TampilKunciJawaban" value="1" <?= old('TampilKunciJawaban', $jadwal['TampilKunciJawaban'] ?? 1) == 1 ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold text-dark" for="TampilKunciJawaban">Tampilkan Kunci Jawaban & Pembahasan di Hasil Ujian</label>
                            <small class="text-muted d-block">Jika aktif, santri dapat melihat mana kunci jawaban yang benar beserta pembahasan soal setelah ujian selesai.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="<?= base_url('backend/ujian-mdta/jadwal') ?>" class="btn btn-secondary px-4 fw-semibold">Batal</a>
            <button type="submit" id="btnSubmitFormJadwal" class="btn btn-success px-5 fw-bold shadow-sm">
                <i class="fas fa-save me-1"></i> Simpan Jadwal Ujian
            </button>
        </div>
    </form>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Inisialisasi Select2 pada dropdown
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    }

    // Dynamic Package Reloading saat Kelas Dipilih
    $('#selectKelas').on('change', function() {
        let kelasId = $(this).val();
        let paketSelect = $('#selectPaket');
        let currentPaketId = '<?= old("IdPaket", $jadwal["IdPaket"] ?? "") ?>';

        if (!kelasId) {
            paketSelect.empty().append('<option value="">-- Pilih Paket Soal --</option>').trigger('change');
            return;
        }

        paketSelect.empty().append('<option value="">Mohon tunggu, memuat paket soal...</option>').trigger('change');

        $.ajax({
            url: '<?= base_url("backend/ujian-mdta/ajax/paket-options") ?>',
            type: 'GET',
            data: { idKelas: kelasId },
            dataType: 'json',
            success: function(data) {
                paketSelect.empty();
                paketSelect.append('<option value="">-- Pilih Paket Soal --</option>');

                if (data && data.length > 0) {
                    data.forEach(function(p) {
                        let isSelected = (currentPaketId && (currentPaketId == p.id));
                        let text = p.NamaPaket + ' (' + p.JumlahSoal + ' Soal Aktif)';
                        if (p.NamaKelas) {
                            text += ' — Kelas ' + p.NamaKelas;
                        }
                        let option = new Option(text, p.id, false, isSelected);
                        $(option).attr('data-jumlah', p.JumlahSoal);
                        paketSelect.append(option);
                    });
                } else {
                    paketSelect.append('<option value="" disabled>Belum ada paket soal aktif untuk kelas ini</option>');
                }

                paketSelect.trigger('change');
            },
            error: function() {
                paketSelect.empty().append('<option value="">Gagal memuat paket soal</option>').trigger('change');
            }
        });
    });

    // Form submit validation & confirmation
    const formJadwal = document.getElementById('formJadwalUjian');
    if (formJadwal) {
        formJadwal.addEventListener('submit', function(e) {
            const totalSesi = parseInt(document.getElementById('totalSesiCount').value || 0);
            const origPaket = document.getElementById('originalIdPaket').value;
            const currPaket = document.getElementById('selectPaket').value;
            const origJumlah = parseInt(document.getElementById('originalJumlahSoal').value || 0);
            const inputJumlahEl = document.getElementById('inputJumlahSoal');
            const currJumlah = inputJumlahEl ? parseInt(inputJumlahEl.value || 0) : 0;
            const confirmInput = document.getElementById('inputConfirmResetData');

            if (totalSesi > 0 && confirmInput.value !== '1') {
                const isPaketChanged = (origPaket !== '' && origPaket !== currPaket);
                const isJumlahChanged = (origJumlah > 0 && origJumlah !== currJumlah);

                if (isPaketChanged || isJumlahChanged) {
                    e.preventDefault();

                    let changeMsg = isPaketChanged ? 'Paket Soal' : 'Jumlah Soal';
                    if (isPaketChanged && isJumlahChanged) {
                        changeMsg = 'Paket Soal dan Jumlah Soal';
                    }

                    Swal.fire({
                        title: '⚠️ KONFIRMASI RESET DATA SESI SANTRI',
                        html: `Anda mengubah <strong>${changeMsg}</strong> pada jadwal yang telah memiliki <strong>${totalSesi} data sesi santri</strong>.<br><br><span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> Perubahan Paket Soal / Jumlah Soal secara mendasar akan MERESET & MENGOSONGKAN data sesi santri agar bank soal baru dapat dimuat.</span><br><br>Apakah Anda yakin ingin melanjutkan dan mereset sesi santri?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-redo me-1"></i> Ya, Reset & Simpan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            confirmInput.value = '1';
                            formJadwal.submit();
                        }
                    });
                }
            }
        });
    }
});
</script>
<?= $this->endSection(); ?>
