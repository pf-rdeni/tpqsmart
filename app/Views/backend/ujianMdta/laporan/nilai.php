<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold"><i class="fas fa-file-invoice text-success me-2"></i> Laporan Hasil Ujian MDTA</h4>
                <small class="text-muted"><?= esc($jadwal['NamaUjian']) ?></small>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= base_url('backend/ujian-mdta/jadwal') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <a href="<?= base_url("backend/ujian-mdta/laporan/export/{$jadwal['id']}") ?>" class="btn btn-outline-danger btn-sm" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </a>
                <?php if (!empty($santriGagal) && $jadwal['BolehRemedial']): ?>
                    <a href="<?= base_url("backend/ujian-mdta/jadwal/remedial/{$jadwal['id']}") ?>" class="btn btn-warning btn-sm text-dark fw-bold">
                        <i class="fas fa-redo me-1"></i> Buat Jadwal Remedial (<?= count($santriGagal) ?> Santri)
                    </a>
                <?php endif; ?>
                <!-- Toggle Pengaturan Reset -->
                <button type="button" class="btn btn-outline-info btn-sm" id="btnToggleReset">
                    <i class="fas fa-cog me-1"></i> Pengaturan Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Panel Pengaturan Reset (tersembunyi defaultnya) -->
    <div id="panelReset" style="display:none;" class="mb-3">
        <div class="card card-outline card-warning shadow-sm">
            <div class="card-header bg-warning">
                <h5 class="card-title mb-0 text-dark fw-bold">
                    <i class="fas fa-exclamation-triangle me-2"></i> Pengaturan Reset Ujian
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Perhatian:</strong> Reset akan menghapus semua data sesi, jawaban, dan nilai santri untuk ujian ini.
                    Santri yang direset dapat mengulang ujian dari awal. Tindakan ini <strong>tidak dapat dibatalkan</strong>.
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div>
                        <span class="text-muted small">Mode reset aktif — gunakan tombol <i class="fas fa-undo text-warning"></i> di tiap baris untuk reset per santri, atau:</span>
                    </div>
                    <form id="formResetSemua" method="post" action="<?= base_url("backend/ujian-mdta/laporan/reset-semua/{$jadwal['id']}") ?>">
                        <?= csrf_field() ?>
                        <button type="button" class="btn btn-danger btn-sm fw-bold" onclick="confirmResetSemua()">
                            <i class="fas fa-trash-alt me-1"></i> Reset Semua Santri
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>    <!-- Ringkasan Statistik Laporan (Native AdminLTE 3 Small Boxes) -->
    <?php
    $totalSantriKelas = $totalSantriKelas ?? count($sesiList);
    $totalSudahUjian  = 0;
    $lulusCount       = 0;
    $gagalCount       = 0;
    $sedangCount      = 0;
    $pauseCount       = 0;
    $belumCount       = 0;
    $totalNilai       = 0;

    foreach ($sesiList as $s) {
        $st = strtolower(trim($s['StatusSesi'] ?? 'belum'));
        if ($s['NilaiAkhir'] !== null) {
            $totalSudahUjian++;
            $totalNilai += (float)$s['NilaiAkhir'];
            if ($s['NilaiAkhir'] >= $jadwal['NilaiMinimum']) {
                $lulusCount++;
            } else {
                $gagalCount++;
            }
        } else if ($st === 'sedang') {
            $sedangCount++;
        } else if ($st === 'pause') {
            $pauseCount++;
        } else {
            $belumCount++;
        }
    }
    $rataRata = $totalSudahUjian > 0 ? round($totalNilai / $totalSudahUjian, 2) : 0;
    ?>

    <div class="row g-2 mb-4">
        <!-- Card 1: Terdaftar -->
        <div class="col-lg col-md-4 col-6">
            <div class="small-box bg-indigo shadow-sm mb-0 rounded-3">
                <div class="inner">
                    <h3><?= count($sesiList) ?></h3>
                    <p class="mb-0 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Terdaftar di Kelas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Lulus -->
        <div class="col-lg col-md-4 col-6">
            <div class="small-box bg-success shadow-sm mb-0 rounded-3">
                <div class="inner">
                    <h3><?= $lulusCount ?></h3>
                    <p class="mb-0 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Lulus (≥ <?= $jadwal['NilaiMinimum'] ?>)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Belum Lulus -->
        <div class="col-lg col-md-4 col-6">
            <div class="small-box bg-danger shadow-sm mb-0 rounded-3">
                <div class="inner">
                    <h3><?= $gagalCount ?></h3>
                    <p class="mb-0 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Belum Lulus (&lt; <?= $jadwal['NilaiMinimum'] ?>)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>

        <!-- Card 4: Sedang / Belum Ujian -->
        <div class="col-lg col-md-4 col-6">
            <div class="small-box bg-warning shadow-sm mb-0 rounded-3">
                <div class="inner text-dark">
                    <h3 class="text-dark"><?= $sedangCount + $pauseCount + $belumCount ?></h3>
                    <p class="mb-0 text-uppercase fw-bold text-dark" style="font-size: 0.75rem; letter-spacing: 0.5px;">Sedang / Belum Ujian</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
        </div>

        <!-- Card 5: Rata-Rata Nilai -->
        <div class="col-lg col-md-4 col-12">
            <div class="small-box bg-info shadow-sm mb-0 rounded-3">
                <div class="inner">
                    <h3><?= $rataRata ?></h3>
                    <p class="mb-0 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Rata-Rata Nilai</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Hasil Ujian Santri -->
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-success fw-bold">
                <i class="fas fa-list-ol me-2"></i>Daftar Nilai Peserta Ujian
            </h5>
            <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-1 small">
                Total: <?= count($sesiList) ?> Santri
            </span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($sesiList)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice fa-3x text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">Belum ada peserta terdaftar</h5>
                </div>
            <?php else: ?>
<style>
    .table-responsive-custom {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }
    div.dataTables_wrapper {
        padding: 1rem 1.25rem !important;
    }
    div.dataTables_wrapper .dataTables_length,
    div.dataTables_wrapper .dataTables_filter {
        margin-bottom: 0.75rem !important;
    }
    div.dataTables_wrapper .dataTables_info,
    div.dataTables_wrapper .dataTables_paginate {
        margin-top: 0.75rem !important;
    }
    @media (max-width: 767.98px) {
        .table th, .table td {
            white-space: nowrap;
            vertical-align: middle;
        }
    }
</style>

                <div class="table-responsive table-responsive-custom">
                    <table class="table table-hover table-striped align-middle mb-0" id="tabelLaporanNilai">
                        <thead class="table-success align-middle">
                            <tr>
                                <th width="40" class="text-center">No</th>
                                <th>ID Santri</th>
                                <th>Nama Santri</th>
                                <?php if (!empty($isUserAdmin)): ?>
                                    <th>Lembaga / TPQ</th>
                                <?php endif; ?>
                                <th class="text-center">Tipe Ujian</th>
                                <th>Waktu Selesai</th>
                                <th class="text-center">Nilai Akhir</th>
                                <th class="text-center">Status Kelulusan</th>
                                <th class="text-center reset-col" style="display:none;">Reset</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sesiList as $idx => $s): ?>
                                <tr>
                                    <td class="text-center"><?= $idx + 1 ?></td>
                                    <td><code><?= esc($s['IdSantri']) ?></code></td>
                                    <td class="fw-bold"><?= esc($s['NamaSantri'] ?? 'Santri #' . $s['IdSantri']) ?></td>
                                    <?php if (!empty($isUserAdmin)): ?>
                                        <td><span class="badge bg-light text-dark border"><i class="fas fa-building text-primary me-1"></i><?= esc($s['NamaTpq'] ?? 'TPQ #' . ($s['IdTpq'] ?? '-')) ?></span></td>
                                    <?php endif; ?>
                                    <td class="text-center">
                                        <?php
                                        $attempt = (int)($s['AttemptKe'] ?? 1);
                                        if ($attempt === 1) {
                                            echo '<span class="badge bg-info px-3 py-1"><i class="fas fa-file-alt me-1"></i>Ujian Utama</span>';
                                        } else {
                                            $remKe = $attempt - 1;
                                            echo '<span class="badge bg-warning text-dark px-3 py-1" title="Percobaan Ujian ke-'.$attempt.'"><i class="fas fa-redo me-1"></i>Remedial #'.$remKe.'</span>';
                                        }
                                        ?>
                                    </td>
                                    <td><small><?= !empty($s['WaktuSelesai']) ? date('d/m/Y H:i', strtotime($s['WaktuSelesai'])) : '-' ?></small></td>
                                    <td class="text-center fw-bold fs-6">
                                        <?php if ($s['NilaiAkhir'] !== null): ?>
                                            <span class="<?= $s['NilaiAkhir'] >= $jadwal['NilaiMinimum'] ? 'text-success' : 'text-danger' ?>">
                                                <?= number_format($s['NilaiAkhir'], 2) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $stSesi = strtolower(trim($s['StatusSesi'] ?? 'belum'));
                                        if ($s['NilaiAkhir'] !== null) {
                                            if ($s['NilaiAkhir'] >= $jadwal['NilaiMinimum']) {
                                                echo '<span class="badge bg-success px-3 py-1"><i class="fas fa-check-circle me-1"></i>LULUS</span>';
                                            } else {
                                                echo '<span class="badge bg-danger px-3 py-1"><i class="fas fa-times-circle me-1"></i>BELUM LULUS</span>';
                                            }
                                        } else if ($stSesi === 'sedang') {
                                            echo '<span class="badge bg-warning text-dark px-3 py-1"><i class="fas fa-spinner fa-spin me-1"></i>Sedang Mengerjakan</span>';
                                        } else if ($stSesi === 'pause') {
                                            echo '<span class="badge bg-info text-white px-3 py-1"><i class="fas fa-pause me-1"></i>Di-Pause</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary px-3 py-1"><i class="fas fa-user-clock me-1"></i>Belum Memulai</span>';
                                        }
                                        ?>
                                    </td>
                                    <!-- Kolom reset (muncul saat panel reset aktif) -->
                                    <td class="text-center reset-col" style="display:none;">
                                        <button type="button" class="btn btn-warning btn-sm"
                                                title="Reset ujian santri ini"
                                                onclick="confirmResetSantri('<?= esc(addslashes($s['NamaSantri'] ?? 'santri ini')) ?>', '<?= base_url("backend/ujian-mdta/laporan/reset-santri/{$jadwal['id']}/" . urlencode($s['IdSantri'])) ?>')">
                                            <i class="fas fa-undo me-1"></i> Reset
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btnToggleReset');
    const panel = document.getElementById('panelReset');
    const resetCols = document.querySelectorAll('.reset-col');

    let resetAktif = false;

    btn.addEventListener('click', function () {
        resetAktif = !resetAktif;

        if (resetAktif) {
            panel.style.display = 'block';
            resetCols.forEach(el => el.style.display = '');
            btn.classList.remove('btn-outline-info');
            btn.classList.add('btn-info', 'text-white');
            btn.innerHTML = '<i class="fas fa-times me-1"></i> Tutup Pengaturan';
        } else {
            panel.style.display = 'none';
            resetCols.forEach(el => el.style.display = 'none');
            btn.classList.add('btn-outline-info');
            btn.classList.remove('btn-info', 'text-white');
            btn.innerHTML = '<i class="fas fa-cog me-1"></i> Pengaturan Reset';
        }
    });
});

function confirmResetSemua() {
    Swal.fire({
        title: '🔄 RESET SEMUA SESI UJIAN?',
        html: 'Apakah Anda yakin ingin mereset <strong>SELURUH SESI SANTRI</strong> untuk ujian ini?<br><br><span class="text-danger small"><i class="fas fa-exclamation-triangle me-1"></i> Seluruh nilai, jawaban, dan timer santri akan dihapus dan tidak dapat dikembalikan.</span>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> Ya, Reset Semua',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444'
    }).then((res) => {
        if (res.isConfirmed) {
            document.getElementById('formResetSemua').submit();
        }
    });
}

function confirmResetSantri(namaSantri, formActionUrl) {
    Swal.fire({
        title: '🔄 Reset Ujian Santri?',
        html: `Seluruh nilai dan jawaban untuk <strong>${namaSantri}</strong> akan dihapus dan santri dapat mengulang dari awal.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-undo me-1"></i> Ya, Reset Sesi',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#f59e0b'
    }).then((res) => {
        if (res.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = formActionUrl;

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '<?= csrf_token() ?>';
            csrfInput.value = '<?= csrf_hash() ?>';
            form.appendChild(csrfInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

$(document).ready(function() {
    if (typeof $.fn.DataTable !== 'undefined') {
        if ($.fn.DataTable.isDataTable('#tabelLaporanNilai')) {
            $('#tabelLaporanNilai').DataTable().destroy();
        }
        $('#tabelLaporanNilai').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pageLength": 10,
            "language": {
                "sSearch": "Cari Santri:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ nilai",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 nilai",
                "infoFiltered": "(disaring dari _MAX_ total nilai)",
                "zeroRecords": "Tidak ada data nilai yang sesuai",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Lanjut",
                    "previous": "Sebelumnya"
                }
            }
        });
    }
});
</script>

<?= $this->endSection(); ?>
