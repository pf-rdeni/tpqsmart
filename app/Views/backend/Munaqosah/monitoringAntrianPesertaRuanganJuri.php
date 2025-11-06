<?= $this->extend('backend/template/templateNoSidebarAndNavbar') ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-2 mb-md-0">Monitoring Antrian Peserta Ruangan</h3>
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-group mb-0">
                                <label class="mb-0 small">Auto Refresh</label>
                                <select id="refreshInterval" class="form-control form-control-sm">
                                    <option value="10" <?= $refresh_interval == 10 ? 'selected' : '' ?>>10 detik</option>
                                    <option value="30" <?= $refresh_interval == 30 ? 'selected' : '' ?>>30 detik</option>
                                    <option value="60" <?= $refresh_interval == 60 ? 'selected' : '' ?>>1 menit</option>
                                    <option value="300" <?= $refresh_interval == 300 ? 'selected' : '' ?>>5 menit</option>
                                </select>
                            </div>
                            <div class="mt-3">
                                <span id="refreshStatus" class="badge badge-info">
                                    <i class="fas fa-sync-alt fa-spin"></i> Auto Refresh Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Informasi Ruangan -->
                        <div class="alert alert-info mb-3">
                            <strong>Ruangan:</strong> <?= $room_id ?>
                            <?php if (!empty($juri_data->NamaMateriGrup)): ?>
                                | <strong>Grup Materi:</strong> <?= $juri_data->NamaMateriGrup ?>
                            <?php endif; ?>
                            <?php if (!empty($juri_data->TypeUjian)): ?>
                                | <strong>Type Ujian:</strong> <?= ucfirst($juri_data->TypeUjian) ?>
                            <?php endif; ?>
                            <?php if (!empty($juri_data->IdTpq) && $juri_data->IdTpq != 0 && !empty($juri_data->NamaTpq)): ?>
                                | <strong>TPQ:</strong> <?= $juri_data->NamaTpq ?>
                            <?php endif; ?>
                            | <strong>Waktu Update:</strong> <span id="lastUpdate">-</span>
                        </div>

                        <!-- Daftar Peserta di Ruangan -->
                        <?php if (!empty($peserta)): ?>
                            <div class="col-12 mb-3">
                                <?php foreach ($peserta as $index => $pesertaItem): ?>
                                    <?php if (!empty($pesertaItem['materi'])): ?>
                                        <?php
                                        // Karena sudah di-filter berdasarkan IdGrupMateriUjian juri, 
                                        // hanya akan ada satu grup materi per peserta
                                        $grupMateri = reset($pesertaItem['materi']); // Ambil grup materi pertama (dan hanya satu)
                                        ?>
                                            <div class="card mb-4" style="border: 2px solid #17a2b8;">
                                                <!-- Header No Peserta -->
                                                <div class="bg-warning text-dark p-2 text-center">
                                                    <strong class="h4 mb-0">No Peserta</strong>
                                                </div>
                                                <!-- No Peserta Number -->
                                                <div class="bg-success text-white p-1 text-center">
                                                    <h1 class="mb-0 font-weight-bold" style="font-size: 8rem;"><?= esc($pesertaItem['NoPeserta']) ?></h1>
                                                </div>
                                                <!-- Materi Kategori -->
                                                <div class="card-body p-4">
                                                    <?php if (!empty($grupMateri['kategori'])): ?>
                                                        <?php foreach ($grupMateri['kategori'] as $kategori => $materiList): ?>
                                                            <?php if (!empty($materiList)): ?>
                                                                <!-- Kategori Header -->
                                                                <div class="bg-warning text-dark p-1 mb-1 text-center">
                                                                    <strong class="h5 mb-0"><?= esc($kategori) ?></strong>
                                                                </div>
                                                                <!-- Materi Detail -->
                                                                <?php foreach ($materiList as $materi): ?>
                                                                    <div class="bg-light p-1 mb-2 text-center">
                                                                        <span class="h1 mb-0 font-weight-normal" style="font-size: 3rem;"><?= esc($materi['NamaMateri']) ?></span>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <div class="alert alert-warning mb-0 p-4">
                                                            <strong class="h4"><i class="fas fa-exclamation-triangle"></i> Belum ada materi</strong>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Belum ada peserta yang masuk ke ruangan ini.
                                Peserta akan muncul di sini setelah panitia mengaktifkan peserta untuk masuk ke ruangan.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    /* Full width untuk content saat tanpa sidebar dan navbar */
    .content-wrapper,
    .content-wrapper::before {
        margin-left: 0 !important;
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    /* Full height untuk body */
    body {
        overflow-x: hidden;
    }

    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
    }

    /* Ensure cards use full width - 1 card per row (col-12 = 12/12) */
    .row {
        margin-left: 0;
        margin-right: 0;
    }

    .row>[class*='col-'] {
        padding-left: 10px;
        padding-right: 10px;
    }
</style>
<script>
    $(function() {
        let refreshInterval = <?= $refresh_interval ?>;
        let refreshTimer = null;
        let checkStatusTimer = null;
        let currentStatusHash = null; // Simpan hash status saat ini
        let isChecking = false; // Flag untuk mencegah multiple simultaneous checks

        // Format waktu update
        function updateLastUpdateTime() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            $('#lastUpdate').text(timeStr);
        }

        // Update waktu saat ini
        updateLastUpdateTime();

        // Fungsi untuk check status antrian via AJAX
        function checkStatusAntrian() {
            // Prevent multiple simultaneous checks
            if (isChecking) {
                return;
            }

            isChecking = true;

            $.ajax({
                url: '<?= base_url('backend/munaqosah/check-status-antrian-juri') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    isChecking = false;

                    if (response.success) {
                        // Jika ini pertama kali (currentStatusHash masih null), simpan hash
                        if (currentStatusHash === null) {
                            currentStatusHash = response.hash;
                            $('#refreshStatus').html(`<i class="fas fa-check-circle"></i> Monitoring aktif (${response.count} peserta)`);
                            return;
                        }

                        // Bandingkan hash
                        if (response.hash !== currentStatusHash) {
                            // Status berubah, lakukan refresh
                            console.log('Status antrian berubah, melakukan refresh...');
                            $('#refreshStatus').html(`<i class="fas fa-sync-alt fa-spin"></i> Status berubah, refresh...`);
                            
                            // Reload halaman
                            const params = new URLSearchParams(window.location.search);
                            params.set('interval', refreshInterval);
                            window.location.reload();
                        } else {
                            // Status tidak berubah, update countdown
                            $('#refreshStatus').html(`<i class="fas fa-check-circle"></i> Monitoring aktif (${response.count} peserta) - Tidak ada perubahan`);
                        }
                    } else {
                        isChecking = false;
                        console.error('Error checking status:', response.message);
                        $('#refreshStatus').html(`<i class="fas fa-exclamation-triangle"></i> Error: ${response.message}`);
                    }
                },
                error: function(xhr, status, error) {
                    isChecking = false;
                    console.error('AJAX error:', error);
                    $('#refreshStatus').html(`<i class="fas fa-exclamation-triangle"></i> Error koneksi`);
                }
            });
        }

        // Fungsi untuk refresh data (manual atau force refresh)
        function refreshData() {
            const params = new URLSearchParams(window.location.search);
            params.set('interval', refreshInterval);
            window.location.reload();
        }

        // Event handler untuk perubahan interval
        $('#refreshInterval').on('change', function() {
            refreshInterval = parseInt($(this).val());
            const params = new URLSearchParams(window.location.search);
            params.set('interval', refreshInterval);

            // Reset timers
            if (checkStatusTimer) {
                clearInterval(checkStatusTimer);
            }
            if (refreshTimer) {
                clearInterval(refreshTimer);
            }

            // Update URL dan reload
            window.location.href = window.location.pathname + '?' + params.toString();
        });

        // Set timer untuk check status antrian
        function startStatusCheck() {
            if (checkStatusTimer) {
                clearInterval(checkStatusTimer);
            }
            
            // Check status setiap interval
            checkStatusTimer = setInterval(function() {
                checkStatusAntrian();
            }, refreshInterval * 1000);

            // Check status pertama kali setelah 1 detik (untuk mendapatkan initial hash)
            setTimeout(function() {
                checkStatusAntrian();
            }, 1000);
        }

        // Set auto refresh timer (fallback jika check status gagal)
        function startAutoRefresh() {
            if (refreshTimer) {
                clearInterval(refreshTimer);
            }
            // Backup timer: jika check status tidak berhasil selama 2x interval, force refresh
            refreshTimer = setInterval(function() {
                console.log('Backup timer: force refresh jika check status tidak berhasil');
                // Force refresh hanya jika belum ada hash yang disimpan
                if (currentStatusHash === null) {
                    refreshData();
                }
            }, refreshInterval * 2000); // 2x interval sebagai safety net
        }

        // Mulai monitoring
        startStatusCheck();
        startAutoRefresh();

        // Update status refresh setiap detik
        let countdown = refreshInterval;
        const countdownInterval = setInterval(function() {
            countdown--;
            if (countdown <= 0) {
                countdown = refreshInterval;
            }

            // Format countdown: tampilkan menit jika >= 60 detik
            let countdownText = '';
            if (countdown >= 60) {
                const menit = Math.floor(countdown / 60);
                const detik = countdown % 60;
                if (detik > 0) {
                    countdownText = `${menit} menit ${detik} detik`;
                } else {
                    countdownText = `${menit} menit`;
                }
            } else {
                countdownText = `${countdown} detik`;
            }

            // Update countdown hanya jika tidak ada perubahan status
            if (currentStatusHash !== null && !isChecking) {
                $('#refreshStatus').html(`<i class="fas fa-clock"></i> Check status dalam ${countdownText}`);
            }
        }, 1000);

        // Cleanup saat halaman ditutup
        $(window).on('beforeunload', function() {
            if (refreshTimer) {
                clearInterval(refreshTimer);
            }
            if (checkStatusTimer) {
                clearInterval(checkStatusTimer);
            }
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
        });
    });
</script>
<?= $this->endSection() ?>