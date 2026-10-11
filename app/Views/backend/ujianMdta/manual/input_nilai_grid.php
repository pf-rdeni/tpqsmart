<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<style>
    .score-input {
        width: 100px;
        font-weight: 700;
        font-size: 1.1rem;
        text-align: center;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    .score-input:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }
    .score-pass {
        color: #155724;
        background-color: #d4edda !important;
        border-color: #c3e6cb !important;
    }
    .score-fail {
        color: #721c24;
        background-color: #f8d7da !important;
        border-color: #f5c6cb !important;
    }
    .sticky-action-bar {
        position: sticky;
        top: 60px;
        z-index: 1020;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border-radius: 10px;
        padding: 12px 20px;
        margin-bottom: 20px;
    }
</style>

<div class="container-fluid">
    <!-- Header Page -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-table text-success me-2"></i> Input Nilai Ujian Manual & Fleksibel
                </h4>
                <small class="text-muted">
                    <?= esc($jadwal['NamaUjian']) ?> &bull; Kelas: <strong><?= esc($jadwal['NamaKelas'] ?? '-') ?></strong> &bull; Materi: <strong><?= esc($paket['NamaMateri'] ?? '-') ?></strong>
                </small>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('backend/ujian-mdta/jadwal') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Jadwal
                </a>
                <a href="<?= base_url("backend/ujian-mdta/jadwal/verifikasi-manual/{$jadwal['id']}") ?>" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-check-double me-1"></i> Mode Koreksi Butir LJK
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Banner -->
    <div class="alert alert-info border-start border-4 border-info shadow-sm p-3 mb-3 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h6 class="fw-bold mb-1"><i class="fas fa-lightbulb text-warning me-2"></i>Panduan Input Nilai Cepat:</h6>
            <div class="small text-muted">
                Guru/Wali Kelas dapat langsung mengetik nilai angka (0 - 100) untuk setiap santri. Perubahan warna input menunjukkan status ketercapaian KKM (<strong><?= $jadwal['NilaiMinimum'] ?></strong>). Klik <strong>"Simpan Nilai Semua Santri"</strong> untuk menyimpan seluruh nilai ke sistem secara permanen.
            </div>
        </div>
        <button type="button" class="btn btn-success btn-sm fw-bold px-3 py-2 shadow-xs" onclick="simpanSemuaNilaiAjax()">
            <i class="fas fa-save me-1"></i> SIMPAN SEMUA NILAI
        </button>
    </div>

    <!-- Sticky Summary & Quick Tools Bar -->
    <div class="sticky-action-bar border d-flex justify-content-between align-items-center flex-wrap gap-3">
        <!-- Live Statistics -->
        <div class="d-flex gap-3 align-items-center flex-wrap">
            <div>
                <small class="text-muted d-block text-uppercase" style="font-size: 10px;">Total Santri</small>
                <span class="fw-bold fs-6 text-dark" id="statTotalSantri"><?= count($santriList) ?></span>
            </div>
            <div class="border-start ps-3">
                <small class="text-muted d-block text-uppercase" style="font-size: 10px;">Sudah Dinilai</small>
                <span class="fw-bold fs-6 text-success" id="statSudahIsi">0</span>
            </div>
            <div class="border-start ps-3">
                <small class="text-muted d-block text-uppercase" style="font-size: 10px;">Belum Dinilai</small>
                <span class="fw-bold fs-6 text-danger" id="statBelumIsi"><?= count($santriList) ?></span>
            </div>
            <div class="border-start ps-3">
                <small class="text-muted d-block text-uppercase" style="font-size: 10px;">Rata-rata Kelas</small>
                <span class="fw-bold fs-6 text-primary" id="statRataRata">0.0</span>
            </div>
            <div class="border-start ps-3">
                <small class="text-muted d-block text-uppercase" style="font-size: 10px;">Tuntas KKM (&ge; <?= $jadwal['NilaiMinimum'] ?>)</small>
                <span class="fw-bold fs-6 text-success" id="statTuntas">0%</span>
            </div>
        </div>

        <!-- Quick Actions Buttons -->
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-warning btn-sm" onclick="isiKkmKeSemua(<?= $jadwal['NilaiMinimum'] ?>)">
                <i class="fas fa-magic me-1"></i> Isi KKM ke yang Kosong
            </button>
            <button type="button" class="btn btn-success btn-sm fw-bold px-3" onclick="simpanSemuaNilaiAjax()">
                <i class="fas fa-save me-1"></i> Simpan Nilai
            </button>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="fas fa-users text-success me-2"></i>Daftar Santri Kelas <?= esc($jadwal['NamaKelas'] ?? '-') ?>
            </h5>
            <span class="badge bg-secondary text-white">Nilai KKM: <?= $jadwal['NilaiMinimum'] ?></span>
        </div>
        <div class="card-body p-0">
            <form id="formBulkNilaiManual">
                <?= csrf_field() ?>
                <input type="hidden" name="idJadwal" value="<?= $jadwal['id'] ?>">
                
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0" id="tableSantriNilai">
                        <thead class="table-success align-middle">
                            <tr>
                                <th width="40" class="text-center">No</th>
                                <th width="140">NISN / ID</th>
                                <th>Nama Santri</th>
                                <th width="140" class="text-center">Nilai Ujian (0-100)</th>
                                <th width="130" class="text-center">Status KKM</th>
                                <th>Catatan Guru / Remedial</th>
                                <th width="120" class="text-center">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($santriList)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-users-slash fs-2 mb-2"></i><br>
                                        Tidak ada santri aktif di kelas ini.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($santriList as $idx => $s): ?>
                                    <?php 
                                    $idSantri = $s['IdSantri'];
                                    $valNilai = isset($nilaiKomponenMap[$idSantri]) 
                                        ? $nilaiKomponenMap[$idSantri] 
                                        : ($s['NilaiAkhir'] ?? '');
                                    $catatan  = $catatanMap[$idSantri] ?? ($s['CatatanVerifikasi'] ?? '');
                                    ?>
                                    <tr class="santri-row" id="row_<?= $idSantri ?>">
                                        <td class="text-center fw-bold text-muted"><?= $idx + 1 ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?= esc($s['NISN'] ?: "ID: {$idSantri}") ?></span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark"><?= esc($s['NamaSantri']) ?></span>
                                            <small class="d-block text-muted"><?= esc($s['NamaTpq'] ?? '') ?></small>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" 
                                                   name="nilai[<?= $idSantri ?>]" 
                                                   id="input_nilai_<?= $idSantri ?>"
                                                   class="form-control form-control-sm score-input mx-auto" 
                                                   min="0" 
                                                   max="100" 
                                                   step="0.5" 
                                                   placeholder="-" 
                                                   value="<?= $valNilai !== '' ? esc($valNilai) : '' ?>"
                                                   oninput="evaluasiSkorSantri(this, <?= $jadwal['NilaiMinimum'] ?>)"
                                                   data-kkm="<?= $jadwal['NilaiMinimum'] ?>">
                                        </td>
                                        <td class="text-center" id="badge_status_<?= $idSantri ?>">
                                            <span class="badge bg-secondary">Belum Diisi</span>
                                        </td>
                                        <td>
                                            <input type="text" 
                                                   name="catatan[<?= $idSantri ?>]" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="Opsional (misal: Remedial bab 2)"
                                                   value="<?= esc($catatan) ?>">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" title="Set KKM" onclick="setNilaiSantri('<?= $idSantri ?>', <?= $jadwal['NilaiMinimum'] ?>)">
                                                KKM
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-sm" title="Set 100" onclick="setNilaiSantri('<?= $idSantri ?>', 100)">
                                                100
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
        <div class="card-footer bg-light p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">
                <i class="fas fa-info-circle me-1"></i> Data nilai yang disimpan otomatis masuk ke kalkulasi nilai raport MDTA.
            </span>
            <button type="button" class="btn btn-success fw-bold px-4 shadow-sm" onclick="simpanSemuaNilaiAjax()">
                <i class="fas fa-save me-1"></i> Simpan Semua Nilai
            </button>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    // Jalankan kalkulasi awal saat halaman dimuat
    document.querySelectorAll('.score-input').forEach(function(input) {
        evaluasiSkorSantri(input, parseFloat(input.dataset.kkm || 0), false);
    });
    updateLiveSummaryStats();
});

function evaluasiSkorSantri(inputEl, kkm, triggerUpdate = true) {
    const val = inputEl.value.trim();
    const idSantri = inputEl.id.replace('input_nilai_', '');
    const badgeEl = document.getElementById('badge_status_' + idSantri);

    inputEl.classList.remove('score-pass', 'score-fail');

    if (val === '') {
        if (badgeEl) badgeEl.innerHTML = '<span class="badge bg-secondary">Belum Diisi</span>';
    } else {
        const num = parseFloat(val);
        if (num >= kkm) {
            inputEl.classList.add('score-pass');
            if (badgeEl) badgeEl.innerHTML = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Tuntas KKM</span>';
        } else {
            inputEl.classList.add('score-fail');
            if (badgeEl) badgeEl.innerHTML = '<span class="badge bg-danger"><i class="fas fa-exclamation-circle me-1"></i>Remedial</span>';
        }
    }

    if (triggerUpdate) {
        updateLiveSummaryStats();
    }
}

function updateLiveSummaryStats() {
    let inputs = document.querySelectorAll('.score-input');
    let total = inputs.length;
    let terisi = 0;
    let totalNilai = 0;
    let tuntas = 0;
    let kkm = parseFloat('<?= $jadwal['NilaiMinimum'] ?? 70 ?>');

    inputs.forEach(function(input) {
        let val = input.value.trim();
        if (val !== '') {
            let num = parseFloat(val);
            terisi++;
            totalNilai += num;
            if (num >= kkm) {
                tuntas++;
            }
        }
    });

    let belumIsi = total - terisi;
    let avg = terisi > 0 ? (totalNilai / terisi).toFixed(1) : '0.0';
    let pctTuntas = terisi > 0 ? Math.round((tuntas / terisi) * 100) : 0;

    $('#statTotalSantri').text(total);
    $('#statSudahIsi').text(terisi);
    $('#statBelumIsi').text(belumIsi);
    $('#statRataRata').text(avg);
    $('#statTuntas').text(pctTuntas + '%');
}

function setNilaiSantri(idSantri, nilai) {
    let inputEl = document.getElementById('input_nilai_' + idSantri);
    if (inputEl) {
        inputEl.value = nilai;
        evaluasiSkorSantri(inputEl, parseFloat(inputEl.dataset.kkm || 0));
    }
}

function isiKkmKeSemua(kkm) {
    Swal.fire({
        title: 'Isi Nilai KKM Otomatis?',
        html: `Apakah Anda ingin mengisi nilai KKM (<strong>${kkm}</strong>) untuk semua santri yang nilainya masih kosong?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-magic me-1"></i> Ya, Isi Otomatis',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#28a745'
    }).then((result) => {
        if (result.isConfirmed) {
            document.querySelectorAll('.score-input').forEach(function(input) {
                if (input.value.trim() === '') {
                    input.value = kkm;
                    evaluasiSkorSantri(input, kkm, false);
                }
            });
            updateLiveSummaryStats();
            Swal.fire('Selesai', 'Nilai KKM berhasil diisikan ke santri yang kosong.', 'success');
        }
    });
}

function simpanSemuaNilaiAjax() {
    let formData = $('#formBulkNilaiManual').serialize();

    Swal.fire({
        title: 'Menyimpan Nilai...',
        html: 'Sedang memproses penyimpanan nilai ke database, mohon tunggu...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    $.ajax({
        url: '<?= base_url("backend/ujian-mdta/jadwal/simpan-nilai-manual-bulk") ?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                Swal.fire({
                    title: '🎉 Berhasil Disimpan!',
                    html: res.message || 'Semua nilai berhasil disimpan dengan sukses.',
                    icon: 'success',
                    confirmButtonColor: '#28a745'
                });
            } else {
                Swal.fire('Gagal Menyimpan', res.message || 'Terjadi kesalahan saat menyimpan data.', 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error', 'Terjadi kesalahan pada server saat menyimpan nilai.', 'error');
        }
    });
}
</script>
<?= $this->endSection(); ?>
