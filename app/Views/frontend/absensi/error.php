<?= $this->extend('frontend/template/publicTemplate'); ?>

<?= $this->section('content'); ?>
    <style>
        .error-container {
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-card {
            max-width: 600px;
            width: 100%;
        }
        .error-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .error-message {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 20px;
        }
        .activity-info {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .activity-info h5 {
            margin-bottom: 10px;
            color: #495057;
        }
        .activity-info p {
            margin-bottom: 5px;
            color: #6c757d;
        }
    </style>

    <div class="error-container">
        <div class="error-card text-center">
            <?php if ($errorType === 'invalid_token'): ?>
                <div class="error-icon text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="error-title text-danger">Link Tidak Valid</h2>
                <p class="error-message">
                    Maaf, link presensi yang Anda akses tidak valid atau sudah tidak berlaku.
                    Silakan hubungi admin untuk mendapatkan link yang benar.
                </p>

            <?php elseif ($errorType === 'inactive'): ?>
                <div class="error-icon text-warning">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <h2 class="error-title text-warning">Kegiatan Belum Aktif</h2>
                <p class="error-message">
                    Kegiatan presensi ini belum diaktifkan oleh admin.
                    Silakan hubungi admin untuk informasi lebih lanjut.
                </p>
                
                <?php if (!empty($kegiatan)): ?>
                <div class="activity-info text-left">
                    <h5><i class="fas fa-info-circle"></i> Informasi Kegiatan</h5>
                    <p><strong>Nama:</strong> <?= esc($kegiatan['NamaKegiatan']) ?></p>
                    <p><strong>Tanggal:</strong> <?= date('d F Y', strtotime($kegiatan['Tanggal'])) ?></p>
                    <p><strong>Waktu:</strong> <?= date('H:i', strtotime($kegiatan['JamMulai'])) ?> - <?= date('H:i', strtotime($kegiatan['JamSelesai'])) ?> WIB</p>
                    <?php if (!empty($kegiatan['Tempat'])): ?>
                    <p><strong>Tempat:</strong> <?= esc($kegiatan['Tempat']) ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            <?php elseif ($errorType === 'no_occurrence'): ?>
                <div class="error-icon text-info">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h2 class="error-title text-info">Tidak Ada Jadwal Hari Ini</h2>
                <p class="error-message">
                    Kegiatan ini berjadwal rutin, namun tidak ada sesi untuk hari ini.
                </p>
                
                <?php if (!empty($kegiatan)): ?>
                <div class="activity-info text-left">
                    <h5><i class="fas fa-info-circle"></i> Informasi Jadwal</h5>
                    <p><strong>Nama:</strong> <?= esc($kegiatan['NamaKegiatan']) ?></p>
                    <p><strong>Jenis:</strong> 
                        <?php 
                        $jenisLabel = [
                            'harian' => 'Harian',
                            'mingguan' => 'Mingguan',
                            'bulanan' => 'Bulanan'
                        ];
                        echo $jenisLabel[$kegiatan['JenisJadwal']] ?? $kegiatan['JenisJadwal'];
                        ?>
                    </p>
                    
                    <?php if ($kegiatan['JenisJadwal'] === 'mingguan'): ?>
                        <p><strong>Jadwal:</strong> Setiap hari 
                        <?php 
                            $daysMap = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                            $daysIndices = explode(',', $kegiatan['HariDalamMinggu'] ?? '');
                            $daysNames = [];
                            foreach ($daysIndices as $idx) {
                                $idx = (int)$idx;
                                if (isset($daysMap[$idx])) {
                                    $daysNames[] = $daysMap[$idx];
                                }
                            }
                            echo implode(', ', $daysNames);
                        ?>
                        </p>
                    <?php elseif ($kegiatan['JenisJadwal'] === 'bulanan'): ?>
                        <p><strong>Jadwal:</strong> 
                        <?php
                            if (($kegiatan['OpsiPola'] ?? 'Tanggal') == 'Tanggal') {
                                echo 'Setiap Tanggal ' . ($kegiatan['TanggalDalamBulan'] ?? '-');
                            } else {
                                $pos = ['', 'Ke-1', 'Ke-2', 'Ke-3', 'Ke-4', 'Terakhir'][$kegiatan['PosisiMinggu'] ?? 1] ?? '';
                                $dIdx = $kegiatan['HariDalamMinggu'] ?? 1;
                                $dName = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][(int)$dIdx] ?? '';
                                echo "Setiap Hari $dName Minggu $pos setiap Bulannya";
                                
                                // Show specific date info for current month to avoid week confusion
                                $posMapEng = ['', 'first', 'second', 'third', 'fourth', 'last'];
                                $posEng = $posMapEng[$kegiatan['PosisiMinggu'] ?? 1] ?? 'first';
                                $dayMapEng = ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                $dayEng = $dayMapEng[(int)$dIdx] ?? 'Monday';
                                
                                $thisMonthTarget = strtotime("$posEng $dayEng of " . date('F Y'));
                                $thisMonthDate = date('d F Y', $thisMonthTarget);
                                
                                echo "<br><span class='text-muted small'><i class='fas fa-info-circle mr-1'></i>Untuk bulan ini, jadwal jatuh pada tanggal $thisMonthDate</span>";
                            }
                        ?>
                        </p>
                    <?php elseif ($kegiatan['JenisJadwal'] === 'harian'): ?>
                        <p><strong>Jadwal:</strong> Setiap hari</p>
                    <?php endif; ?>
                    
                    <p><strong>Waktu:</strong> <?= date('H:i', strtotime($kegiatan['JamMulai'])) ?> - <?= date('H:i', strtotime($kegiatan['JamSelesai'])) ?> WIB</p>
                    <?php if (!empty($kegiatan['Tempat'])): ?>
                    <p><strong>Tempat:</strong> <?= esc($kegiatan['Tempat']) ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if (isset($nextOccurrence) && $nextOccurrence): ?>
                <div class="card shadow-sm mt-4 border-info">
                    <div class="card-header bg-info text-white text-center">
                        <h5 class="mb-0 font-weight-bold"><i class="fas fa-calendar-alt mr-2"></i>Sesi Berikutnya</h5>
                    </div>
                    <div class="card-body text-center py-4">
                        <h3 class="font-weight-bold text-dark mb-2"><?= date('d F Y', strtotime($nextOccurrence)) ?></h3>
                        <h4 class="text-secondary mb-4">Pukul <?= date('H:i', strtotime($kegiatan['JamMulai'])) ?> WIB</h4>
                        
                        <?php if (isset($activityStart) && $activityStart): ?>
                            <div class="countdown-container p-3 bg-light rounded border border-light">
                                <p class="mb-2 text-muted font-weight-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.9rem;">Akan dimulai dalam</p>
                                <div id="countdown-next" class="text-info font-weight-bold" style="font-size: 2rem;">
                                    ---
                                </div>
                            </div>
                    </div>
                </div>

                        <script>
                            // Server time synchronization (reuse if available or define simple version)
                            // Assuming similar logic to before_start but specific for this block if needed
                            // For simplicity, we'll use a self-contained script here or reuse the logic if we extract it.
                            // Let's copy the robust logic to ensure it works independently.

                            (function() {
                                const targetTime = <?= $activityStart * 1000 ?>;
                                const serverTime = <?= time() * 1000 ?>;
                                const clientTime = new Date().getTime();
                                const timeOffset = serverTime - clientTime;

                                function updateCountdown() {
                                    const now = new Date().getTime() + timeOffset;
                                    const distance = targetTime - now;

                                    if (distance < 0) {
                                        document.getElementById('countdown-next').innerHTML = "Waktu Tiba!";
                                        setTimeout(() => location.reload(), 2000);
                                        return;
                                    }

                                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                    let text = '';
                                    if (days > 0) text += days + ' Hari, ';
                                    if (days > 0 || hours > 0) text += hours + ' Jam ';
                                    text += minutes + ' Menit ' + seconds + ' Detik';

                                    document.getElementById('countdown-next').textContent = text;
                                }

                                setInterval(updateCountdown, 1000);
                                updateCountdown();
                            })();
                        </script>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            <?php elseif ($errorType === 'before_start'): ?>
                <div class="error-icon text-primary">
                    <i class="fas fa-hourglass-start"></i>
                </div>
                <h2 class="error-title text-primary">Kegiatan Belum Dimulai</h2>
                <p class="error-message">
                    Kegiatan presensi ini belum dimulai. Silakan tunggu hingga waktu yang dijadwalkan.
                </p>
                
                <?php if (!empty($kegiatan)): ?>
                <div class="activity-info text-left">
                    <h5><i class="fas fa-calendar-alt"></i> Jadwal Kegiatan</h5>
                    <p><strong>Nama:</strong> <?= esc($kegiatan['NamaKegiatan']) ?></p>
                    <p><strong>Tanggal:</strong> <?= date('d F Y', strtotime($kegiatan['Tanggal'])) ?></p>
                    <p><strong>Waktu:</strong> <?= date('H:i', strtotime($kegiatan['JamMulai'])) ?> - <?= date('H:i', strtotime($kegiatan['JamSelesai'])) ?> WIB</p>
                    <?php if (!empty($kegiatan['Tempat'])): ?>
                    <p><strong>Tempat:</strong> <?= esc($kegiatan['Tempat']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Live Current Time -->
                <div class="alert alert-light mt-3 mb-3" style="font-size: 1rem; border: 1px solid #dee2e6;">
                    <div class="text-center">
                        <i class="fas fa-calendar-day mr-1"></i> <strong>Waktu Saat Ini:</strong><br>
                        <span id="currentDateTime" style="font-size: 1.1rem; font-weight: 500;"></span>
                    </div>
                </div>

                <!-- Countdown Timer -->
                <div class="alert alert-primary mt-4" style="font-size: 1.2rem;">
                    <div class="mb-2"><i class="fas fa-clock"></i> <strong>Dimulai dalam:</strong></div>
                    <div id="countdown" style="font-size: 1.8rem; font-weight: bold;">
                        <!-- Countdown will be inserted here -->
                    </div>
                </div>

                <script>
                    // Server time synchronization
                    const serverTime = <?= time() ?> * 1000; // Server time in milliseconds
                    const clientTime = new Date().getTime();
                    const timeOffset = serverTime - clientTime; // Offset between server and client
                    
                    // Countdown timer
                    const targetTime = <?= $activityStart ?> * 1000; // Convert to milliseconds
                    const activityToken = '<?= $kegiatan['Token'] ?? '' ?>';
                    
                    function getCurrentTime() {
                        // Get current time adjusted with server offset
                        return new Date().getTime() + timeOffset;
                    }
                    
                    function updateCurrentTime() {
                        const now = new Date(getCurrentTime());
                        
                        // Indonesian month names
                        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                          'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        
                        const day = String(now.getDate()).padStart(2, '0');
                        const month = monthNames[now.getMonth()];
                        const year = now.getFullYear();
                        const hours = String(now.getHours()).padStart(2, '0');
                        const minutes = String(now.getMinutes()).padStart(2, '0');
                        const seconds = String(now.getSeconds()).padStart(2, '0');
                        
                        const dateTimeStr = `${day} ${month} ${year}, ${hours}:${minutes}:${seconds} WIB`;
                        document.getElementById('currentDateTime').textContent = dateTimeStr;
                    }
                    
                    function updateCountdown() {
                        const now = getCurrentTime();
                        const distance = targetTime - now;

                        if (distance < 0) {
                            // Countdown finished, reload page
                            location.reload();
                            return;
                        }

                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        // Build countdown string based on what's relevant
                        let countdownText = '';
                        
                        if (days > 0) {
                            countdownText += String(days).padStart(2, '0') + ' Hari, ';
                        }
                        
                        if (days > 0 || hours > 0) {
                            countdownText += String(hours).padStart(2, '0') + ' Jam ';
                        }
                        
                        countdownText += String(minutes).padStart(2, '0') + ' Menit ';
                        countdownText += String(seconds).padStart(2, '0') + ' Detik';

                        document.getElementById('countdown').textContent = countdownText;
                    }

                    // Periodic validation check - verify activity schedule hasn't changed
                    function checkActivityStatus() {
                        // Reload page every 30 seconds to check for any schedule changes
                        // This ensures if admin changes the time, users will see the update
                        location.reload();
                    }

                    // Update countdown immediately and then every second
                    updateCurrentTime();
                    updateCountdown();
                    setInterval(function() {
                        updateCurrentTime();
                        updateCountdown();
                    }, 1000);
                    
                    // Check for schedule changes every 30 seconds
                    setInterval(checkActivityStatus, 30000);
                </script>
                <?php endif; ?>

            <?php elseif ($errorType === 'after_end'): ?>
                <div class="error-icon text-secondary">
                    <i class="fas fa-clock"></i>
                </div>
                <h2 class="error-title text-secondary">Kegiatan Sudah Berakhir</h2>
                <p class="error-message">
                    Waktu presensi untuk kegiatan ini sudah berakhir.
                    Silakan hubungi admin jika Anda memerlukan bantuan.
                </p>
                
                <?php if (!empty($kegiatan)): ?>
                <div class="activity-info text-left">
                    <h5><i class="fas fa-calendar-alt"></i> Jadwal Kegiatan</h5>
                    <p><strong>Nama:</strong> <?= esc($kegiatan['NamaKegiatan']) ?></p>
                    <p><strong>Tanggal:</strong> <?= date('d F Y', strtotime($kegiatan['Tanggal'])) ?></p>
                    <p><strong>Waktu:</strong> <?= date('H:i', strtotime($kegiatan['JamMulai'])) ?> - <?= date('H:i', strtotime($kegiatan['JamSelesai'])) ?> WIB</p>
                    <?php if (!empty($kegiatan['Tempat'])): ?>
                    <p><strong>Tempat:</strong> <?= esc($kegiatan['Tempat']) ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if (isset($nextOccurrence) && $nextOccurrence): ?>
                <div class="alert alert-info mt-3">
                    <h5 class="alert-heading h6 font-weight-bold"><i class="fas fa-calendar-check mr-1"></i> Jadwal Berikutnya</h5>
                    <p class="mb-0">
                        Jadwal absensi saat ini sudah selesai.<br>
                        Jadwal berikutnya: <strong><?= date('d F Y', strtotime($nextOccurrence)) ?></strong> pukul <strong><?= date('H:i', strtotime($kegiatan['JamMulai'])) ?> WIB</strong>.
                    </p>
                </div>
                <?php endif; ?>

            <?php elseif ($errorType === 'outside_schedule'): ?>
                <div class="error-icon text-info">
                    <i class="fas fa-clock"></i>
                </div>
                <h2 class="error-title text-info">Di Luar Jadwal Kegiatan</h2>
                <p class="error-message">
                    Anda mengakses halaman presensi di luar waktu yang dijadwalkan.
                    Presensi hanya dapat dilakukan pada tanggal dan waktu yang telah ditentukan.
                </p>
                
                <?php if (!empty($kegiatan)): ?>
                <div class="activity-info text-left">
                    <h5><i class="fas fa-calendar-alt"></i> Jadwal Kegiatan</h5>
                    <p><strong>Nama:</strong> <?= esc($kegiatan['NamaKegiatan']) ?></p>
                    <p><strong>Tanggal:</strong> <?= date('d F Y', strtotime($kegiatan['Tanggal'])) ?></p>
                    <p><strong>Waktu:</strong> <?= date('H:i', strtotime($kegiatan['JamMulai'])) ?> - <?= date('H:i', strtotime($kegiatan['JamSelesai'])) ?> WIB</p>
                    <?php if (!empty($kegiatan['Tempat'])): ?>
                    <p><strong>Tempat:</strong> <?= esc($kegiatan['Tempat']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> Silakan akses kembali pada waktu yang telah dijadwalkan.
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="error-icon text-secondary">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h2 class="error-title text-secondary">Terjadi Kesalahan</h2>
                <p class="error-message">
                    Maaf, terjadi kesalahan yang tidak terduga.
                    Silakan coba lagi atau hubungi admin.
                </p>
            <?php endif; ?>
        </div>
    </div>

<?= $this->endSection(); ?>
