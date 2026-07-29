<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="mb-0 fw-bold"><i class="fas fa-edit text-success me-2"></i> Ruang Ujian Online MDTA</h4>
            <small class="text-muted">Daftar ujian dan riwayat hasil pengerjaan untuk kelas Anda</small>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($warning)): ?>
        <div class="alert alert-warning border-start border-4 border-warning">
            <i class="fas fa-exclamation-triangle me-2"></i><?= esc($warning) ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- LIST CARD UJIAN -->
    <?php if (empty($jadwalList)): ?>
        <div class="card card-outline card-secondary shadow-sm text-center py-5">
            <div class="card-body">
                <i class="fas fa-calendar-check fa-4x text-muted mb-3 d-block"></i>
                <h5 class="text-muted fw-bold">Tidak ada ujian aktif saat ini</h5>
                <p class="text-muted small mb-0">Belum ada jadwal ujian online yang dibuka untuk kelas Anda. Silakan hubungi ustadz/ustadzah Anda.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($jadwalList as $j): ?>
                <div class="col-md-6">
                    <div class="card card-outline <?= $j['TipeJadwal'] == 'utama' ? 'card-success' : 'card-warning' ?> shadow-sm h-100 mb-0">
                        <!-- Card Header -->
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                            <div>
                                <span class="badge <?= $j['TipeJadwal'] == 'utama' ? 'bg-primary' : 'bg-warning text-dark' ?> me-1">
                                    <?= $j['TipeJadwal'] == 'utama' ? 'Ujian Utama' : 'Remedial ke-' . ($j['AttemptKe'] - 1) ?>
                                </span>
                                <span class="badge bg-secondary"><?= esc($j['NamaKelas'] ?? '-') ?></span>
                            </div>
                            <small class="text-muted"><i class="fas fa-clock me-1"></i><?= $j['DurasiMenit'] ?> Menit</small>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="fw-bold text-success mb-1"><?= esc($j['NamaUjian']) ?></h5>
                                <p class="text-muted small mb-3">Materi: <strong><?= esc($j['NamaMateri'] ?? '-') ?></strong> | Paket: <strong><?= esc($j['NamaPaket'] ?? '-') ?></strong></p>

                                <div class="bg-light p-2 rounded small mb-3">
                                    <div><i class="fas fa-list-ol me-2 text-primary"></i>Jumlah Soal: <strong><?= $j['JumlahSoal'] ?> Soal</strong></div>
                                    <div><i class="fas fa-trophy me-2 text-warning"></i>Nilai Kelulusan: <strong><?= $j['NilaiMinimum'] ?></strong></div>
                                    <div><i class="fas fa-calendar-alt me-2 text-info"></i>Batas Selesai: <strong><?= date('d/m/Y H:i', strtotime($j['TanggalSelesai'])) ?></strong></div>
                                </div>

                                <!-- EMBEDDED RIWAYAT PENGERJAAN DI DALAM CARD UJIAN -->
                                <?php if (!empty($j['semuaSesi'])): ?>
                                    <div class="border rounded p-2 mb-3 bg-white">
                                        <div class="fw-bold small text-secondary mb-2 border-bottom pb-1">
                                            <i class="fas fa-history me-1 text-info"></i> Riwayat Hasil Pengerjaan:
                                        </div>
                                        <?php foreach ($j['semuaSesi'] as $s): ?>
                                            <?php if (in_array($s['StatusSesi'], ['selesai', 'timeout'])): ?>
                                                <?php $isLulusSesi = ($s['NilaiAkhir'] !== null && $s['NilaiAkhir'] >= $j['NilaiMinimum']); ?>
                                                <div class="d-flex justify-content-between align-items-center mb-1 p-2 rounded <?= $isLulusSesi ? 'bg-success-subtle border border-success-subtle' : 'bg-danger-subtle border border-danger-subtle' ?>">
                                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                                        <span class="badge <?= $s['AttemptKe'] == 1 ? 'bg-primary' : 'bg-warning text-dark' ?>">
                                                            <?= $s['AttemptKe'] == 1 ? 'Ujian Utama' : 'Remedial ' . ($s['AttemptKe'] - 1) ?>
                                                        </span>
                                                        <span class="fw-bold small <?= $isLulusSesi ? 'text-success' : 'text-danger' ?>">
                                                            Nilai: <?= number_format($s['NilaiAkhir'], 2) ?>
                                                        </span>
                                                        <span class="badge <?= $isLulusSesi ? 'bg-success' : 'bg-danger' ?>">
                                                            <?= $isLulusSesi ? 'LULUS' : 'BELUM LULUS' ?>
                                                        </span>
                                                    </div>
                                                    <a href="<?= base_url("backend/ujian-mdta/santri/hasil/{$s['TokenSesi']}") ?>" class="btn btn-outline-info btn-xs py-0 px-2 fw-semibold">
                                                        <i class="fas fa-eye me-1"></i> Hasil
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- CARD FOOTER / TOMBOL UTAMA -->
                            <?php
                            $statusSesi = strtolower(trim($j['status_sesi'] ?? 'belum'));
                            $isPaused   = ($statusSesi === 'pause');
                            ?>
                            <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top">
                                <div>
                                    <?php if ($statusSesi == 'selesai' || $statusSesi == 'timeout'): ?>
                                        <?php
                                        $remedialKe = (int)($j['attempt_ke'] ?? 1);
                                        ?>
                                        <?php if ($j['can_remedial']): ?>
                                            <small class="fw-bold" style="color: #6f42c1;"><i class="fas fa-redo me-1"></i> Sesi Remedial (Ke-<?= $remedialKe ?>) Aktif</small>
                                        <?php elseif (!empty($j['remedial_not_started'])): ?>
                                            <small class="text-info fw-bold"><i class="fas fa-clock me-1"></i> Remedial Dimulai <?= date('d/m/Y H:i', strtotime($j['TanggalMulaiRemedial'])) ?></small>
                                        <?php elseif (!empty($j['remedial_expired'])): ?>
                                            <small class="text-danger fw-semibold"><i class="fas fa-calendar-times me-1"></i> Waktu Remedial Berakhir</small>
                                        <?php elseif (!empty($j['remedial_pending_active'])): ?>
                                            <small class="text-muted fw-semibold"><i class="fas fa-hourglass-half me-1 text-warning"></i> Remedial Menunggu Pengawas</small>
                                        <?php else: ?>
                                            <span class="badge <?= $j['nilai_akhir'] >= $j['NilaiMinimum'] ? 'bg-success' : 'bg-secondary' ?> fs-6">
                                                <i class="fas fa-check-circle me-1"></i>Selesai (Nilai: <?= number_format($j['nilai_akhir'], 2) ?>)
                                            </span>
                                        <?php endif; ?>
                                    <?php elseif ($isPaused): ?>
                                        <span class="badge bg-warning text-dark fs-6"><i class="fas fa-pause-circle me-1"></i>Di-Pause oleh Pengawas</span>
                                        <small class="d-block text-muted mt-1" style="font-size:11px;"><i class="fas fa-info-circle me-1"></i>Tombol Lanjutkan akan aktif otomatis</small>
                                    <?php elseif ($statusSesi == 'sedang'): ?>
                                        <span class="badge bg-warning text-dark fs-6"><i class="fas fa-spinner fa-spin me-1"></i>Sedang Mengerjakan</span>
                                        <span class="badge bg-dark text-warning fs-6 ms-1 live-card-timer" id="timer_card_<?= $j['id'] ?>" data-seconds="<?= (int)($j['sisa_waktu_detik'] ?? 0) ?>">
                                            <i class="fas fa-clock me-1 text-warning"></i><span class="timer-text">00 : 00 : 00</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary fs-6">Belum Dikerjakan</span>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex gap-1 flex-wrap">
                                    <?php if ($statusSesi == 'selesai' || $statusSesi == 'timeout'): ?>
                                        <?php if ($j['can_remedial']): ?>
                                            <form method="post" action="<?= base_url("backend/ujian-mdta/santri/mulai/{$j['id']}") ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn text-white btn-sm fw-bold btn-mulai-ujian" style="background-color: #6f42c1; border-color: #6f42c1;">
                                                    <i class="fas fa-redo me-1"></i> Mulai Remedial (Ke-<?= $remedialKe ?>)
                                                </button>
                                            </form>
                                        <?php elseif (!empty($j['remedial_not_started'])): ?>
                                            <button type="button" class="btn btn-outline-info btn-sm fw-semibold" disabled title="Ujian remedial belum dimulai">
                                                <i class="fas fa-clock me-1"></i> Remedial Belum Dimulai
                                            </button>
                                        <?php elseif (!empty($j['remedial_expired'])): ?>
                                            <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" disabled>
                                                <i class="fas fa-calendar-times me-1"></i> Selesai
                                            </button>
                                        <?php elseif (!empty($j['remedial_pending_active'])): ?>
                                            <button type="button" class="btn btn-outline-warning btn-sm fw-semibold" disabled title="Menunggu pengawas mengaktifkan sesi remedial">
                                                <i class="fas fa-hourglass-half me-1"></i> Menunggu Akses Remedial
                                            </button>
                                        <?php endif; ?>
                                    <?php elseif ($isPaused): ?>
                                        <button type="button" class="btn btn-secondary btn-sm fw-bold" disabled title="Menunggu pengawas melanjutkan ujian">
                                            <i class="fas fa-pause me-1"></i> Di-Pause Pengawas
                                        </button>
                                    <?php elseif ($statusSesi == 'sedang'): ?>
                                        <a href="<?= base_url("backend/ujian-mdta/santri/ujian/{$j['token_sesi']}") ?>" class="btn btn-warning btn-sm fw-bold text-dark">
                                            <i class="fas fa-play me-1"></i> Lanjutkan Ujian
                                        </a>
                                    <?php else: ?>
                                        <form method="post" action="<?= base_url("backend/ujian-mdta/santri/mulai/{$j['id']}") ?>">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-success btn-sm fw-bold px-3 btn-mulai-ujian">
                                                <i class="fas fa-pen me-1"></i> Mulai Ujian
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-mulai-ujian').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const isRemedial = this.innerText.includes('Remedial');

            Swal.fire({
                title: isRemedial ? '🔄 Mulai Ujian Remedial?' : '📝 Mulai Ujian Sekarang?',
                html: 'Apakah Anda yakin ingin memulai pengerjaan ujian sekarang?<br><br><span class="text-warning small"><i class="fas fa-clock me-1"></i> Timer pengerjaan akan langsung berjalan begitu Anda mengklik Ya.</span>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-play me-1"></i> Ya, Mulai Sekarang!',
                cancelButtonText: 'Nanti Dulu',
                confirmButtonColor: '#10b981'
            }).then((res) => {
                if (res.isConfirmed) {
                    Swal.fire({
                        title: 'Menyiapkan Lembar Ujian...',
                        text: 'Mendistribusikan soal acak Anda',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    form.submit();
                }
            });
        });
    });

    // Client-side 1s countdown timer untuk card yang sedang dikerjakan
    function formatCardTime(totalSeconds) {
        if (totalSeconds <= 0) return '00 : 00 : 00';
        const h = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
        const m = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
        const s = String(totalSeconds % 60).padStart(2, '0');
        return `${h} : ${m} : ${s}`;
    }

    function updateCardTimers() {
        document.querySelectorAll('.live-card-timer').forEach(el => {
            let sec = parseInt(el.getAttribute('data-seconds') || 0);
            const textEl = el.querySelector('.timer-text');
            if (textEl) {
                textEl.textContent = formatCardTime(sec);
            }
            if (sec > 0) {
                el.setAttribute('data-seconds', sec - 1);
            }
        });
    }

    setInterval(updateCardTimers, 1000);
    updateCardTimers();

    // AJAX Live Check untuk status Pause di halaman Ruang Ujian Santri (Tanpa Reload Halaman Terus-Menerus)
    <?php
    $hasPausedExam = false;
    if (!empty($jadwalList)) {
        foreach ($jadwalList as $checkJadwal) {
            $statusSesi = strtolower(trim($checkJadwal['status_sesi'] ?? 'belum'));
            if ($statusSesi === 'pause') {
                $hasPausedExam = true;
                break;
            }
        }
    }
    ?>
    let initialPausedState = <?= $hasPausedExam ? 'true' : 'false' ?>;

    function pollJadwalStatusSantri() {
        fetch(`<?= site_url("backend/ujian-mdta/santri/cekJadwalStatus") ?>`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            if (!res || !res.success) return;

            // Sync waktu sisa live untuk card yang sedang dikerjakan
            if (res.activeTimers) {
                for (let jId in res.activeTimers) {
                    const timerEl = document.getElementById(`timer_card_${jId}`);
                    if (timerEl) {
                        timerEl.setAttribute('data-seconds', res.activeTimers[jId]);
                    }
                }
            }

            // Jika status berubah dari Pause -> Aktif (atau sebaliknya), HANYA SEKALI reload halaman agar tombol aktif kembali
            if (initialPausedState !== res.hasPaused) {
                initialPausedState = res.hasPaused;
                window.location.reload();
            }
        })
        .catch(e => {
            // Silently ignore network catch
        });
    }

    // Polling setiap 4 detik secara senyap tanpa reload halaman terus-menerus
    setInterval(pollJadwalStatusSantri, 4000);
});
</script>

<?= $this->endSection(); ?>
