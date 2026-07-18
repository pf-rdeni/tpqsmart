<?= $this->extend('frontend/TvDigital/tvTemplate'); ?>
<?= $this->section('content'); ?>

<!-- Container utama TV -->
<div class="tv-container theme-<?= $link['Theme'] ?? 'dark' ?>" id="tvContainer" data-hash="<?= $link['HashKey'] ?>">
    
    <!-- HEADER BAR -->
    <header class="tv-header">
        <div class="header-logo-section">
            <img src="<?= $logoUrl ?>" alt="Logo Lembaga" id="tvLogo" class="logo-image">
            <div class="header-titles">
                <h1 id="tvLembagaName"><?= esc($tpqName) ?></h1>
                <span class="header-subtitle" id="tvTahunAjaran">Tahun Ajaran: <?= convertTahunAjaran($link['IdTahunAjaran']) ?></span>
            </div>
        </div>
        
        <!-- Live Clock & Dynamic Info -->
        <div class="header-info-section">
            <!-- Next Prayer Indicator -->
            <div class="header-prayer-badge d-none" id="headerPrayerBadge">
                <i class="fas fa-mosque mr-2 text-warning"></i>
                <span id="headerPrayerName">-</span>: <strong id="headerPrayerTime">-</strong> 
                <small class="text-xs ml-1" id="headerPrayerCountdown">(-)</small>
            </div>
            
            <div class="header-time-badge">
                <div class="live-time" id="tvTime">00:00:00</div>
                <div class="live-date" id="tvDate">Memuat tanggal...</div>
            </div>
        </div>
    </header>

    <!-- CONTENT BODY (Dynamic Cards Container) -->
    <main class="tv-body">
        
        <!-- 1. BLOCK: HOME -->
        <div class="tv-slide-card d-none" id="card-home">
            <!-- Row 1: Stat Cards (4 columns) -->
            <div class="home-stats-grid">
                <div class="stat-box-tv border-blue">
                    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="stat-content">
                        <span class="stat-label">Total Santri</span>
                        <h2 class="stat-number" id="homeTotalSantri">0</h2>
                        <span class="stat-subtext" id="homeSantriLp">L: 0 | P: 0</span>
                    </div>
                </div>
                <div class="stat-box-tv border-green">
                    <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
                    <div class="stat-content">
                        <span class="stat-label">Total Ustadz/ah</span>
                        <h2 class="stat-number" id="homeTotalGuru">0</h2>
                        <span class="stat-subtext" id="homeGuruLp">L: 0 | P: 0</span>
                    </div>
                </div>
                <div class="stat-box-tv border-purple">
                    <div class="stat-icon"><i class="fas fa-school"></i></div>
                    <div class="stat-content">
                        <span class="stat-label">Total Kelas</span>
                        <h2 class="stat-number" id="homeTotalKelas">0</h2>
                        <span class="stat-subtext">Rombongan Belajar</span>
                    </div>
                </div>
                <div class="stat-box-tv border-orange">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-content">
                        <span class="stat-label">Kehadiran Hari Ini</span>
                        <h2 class="stat-number" id="homeKehadiranPersen">0%</h2>
                        <span class="stat-subtext" id="homeKehadiranRatio">Santri Hadir: 0/0</span>
                    </div>
                </div>
            </div>

            <!-- Row 2: Bottom Cards (divided into 4 proportional parts) -->
            <div class="home-widgets-grid">
                <!-- Part 1: Chart (1.2fr) -->
                <div class="glass-card d-flex flex-column overflow-hidden">
                    <h3 class="card-title-tv"><i class="fas fa-chart-line text-primary"></i> Trend Absensi Santri (30 Hari Terakhir)</h3>
                    <div class="chart-wrapper flex-grow-1 home-charts-flex">
                        <div class="home-chart-container">
                            <canvas id="homeAbsensiChart"></canvas>
                        </div>
                        <div class="home-chart-container">
                            <canvas id="homeAbsensiBarChart"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Part 2: Agenda (1fr) -->
                <div class="glass-card d-flex flex-column overflow-hidden">
                    <h3 class="card-title-tv"><i class="fas fa-calendar-alt text-success"></i> Agenda Terdekat</h3>
                    <div class="home-agenda-list flex-grow-1 overflow-y-auto" id="homeAgendaContainer">
                        <div class="loading-placeholder"><i class="fas fa-spinner fa-spin"></i> Memuat agenda...</div>
                    </div>
                </div>
                <!-- Part 3: Ulang Tahun (1fr) -->
                <div class="glass-card d-flex flex-column overflow-hidden">
                    <h3 class="card-title-tv"><i class="fas fa-birthday-cake text-danger"></i> Ulang Tahun</h3>
                    <div id="homeBirthdayTodayList" class="home-birthday-list flex-grow-1 overflow-y-auto">
                        <div class="home-birthday-loading">Memuat...</div>
                    </div>
                </div>
                <!-- Part 4: Alumni & Kelulusan (1fr) -->
                <div class="glass-card d-flex flex-column overflow-hidden">
                    <h3 class="card-title-tv"><i class="fas fa-user-graduate text-info"></i> Alumni & Kelulusan</h3>
                    <div class="home-alumni-container flex-grow-1">
                        <div class="home-alumni-item">
                            <div class="home-alumni-icon award-color"><i class="fas fa-award"></i></div>
                            <div>
                                <div class="home-alumni-label">Lulus Munaqosah</div>
                                <h3 class="home-alumni-number" id="homeMunaqosahLulus">0 Santri</h3>
                            </div>
                        </div>
                        <div class="home-alumni-item">
                            <div class="home-alumni-icon alumni-color"><i class="fas fa-graduation-cap"></i></div>
                            <div>
                                <div class="home-alumni-label">Total Alumni</div>
                                <h3 class="home-alumni-number" id="homeTotalAlumni">0 Santri</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. BLOCK: KEADAAN SANTRI -->
        <div class="tv-slide-card d-none" id="card-keadaan_santri">
            <h2 class="slide-main-title"><i class="fas fa-user-graduate text-primary"></i> Statistik Keadaan Santri</h2>
            <div class="keadaan-santri-grid">
                <!-- Tabel Keadaan Santri -->
                <div class="glass-card d-flex flex-column overflow-hidden">
                    <h3 class="card-title-tv"><i class="fas fa-list-ol"></i> Distribusi Santri per Kelas</h3>
                    <div class="table-tv-wrapper flex-grow-1 overflow-y-auto">
                        <table class="table-tv">
                            <thead>
                                <tr>
                                    <th>Nama Kelas</th>
                                    <th class="text-center">Laki-Laki</th>
                                    <th class="text-center">Perempuan</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody id="santriKelasTableBody">
                                <!-- Dinamis via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Chart Distribusi -->
                <div class="glass-card d-flex flex-column">
                    <h3 class="card-title-tv"><i class="fas fa-chart-bar"></i> Grafik Distribusi Kelas</h3>
                    <div class="chart-wrapper flex-grow-1">
                        <canvas id="santriDistribusiChart"></canvas>
                    </div>
                </div>
                <!-- Chart Rasio Gender -->
                <div class="glass-card d-flex flex-column">
                    <h3 class="card-title-tv"><i class="fas fa-chart-pie"></i> Rasio Gender Santri (L/P)</h3>
                    <div class="chart-wrapper flex-grow-1">
                        <canvas id="santriGenderRasioChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. BLOCK: KEADAAN GURU -->
        <div class="tv-slide-card d-none" id="card-keadaan_guru">
            <h2 class="slide-main-title"><i class="fas fa-user-tie text-success"></i> Keadaan Guru / Ustadz-Ustadzah</h2>
            <div class="grid-2-columns">
                <!-- Data Detail -->
                <div class="glass-card d-flex flex-column justify-content-between p-4">
                    <div>
                        <h3 class="card-title-tv"><i class="fas fa-info-circle"></i> Ringkasan Tenaga Pendidik</h3>
                        <div class="stat-row mt-4">
                            <div class="stat-row-item">
                                <span class="stat-row-label">Total Guru Aktif</span>
                                <h1 class="text-success text-xxl" id="guruTotalStat">0</h1>
                            </div>
                            <div class="stat-row-item">
                                <span class="stat-row-label">Ustadz (Laki-Laki)</span>
                                <h1 class="text-primary text-xl" id="guruLakiStat">0</h1>
                            </div>
                            <div class="stat-row-item">
                                <span class="stat-row-label">Ustadzah (Perempuan)</span>
                                <h1 class="text-pink text-xl" id="guruPerempuanStat">0</h1>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert-info-signage mt-4">
                        <i class="fas fa-quote-left text-muted"></i>
                        <p class="text-sm italic text-muted">"Sebaik-baik kalian adalah orang yang belajar Al-Qur'an dan mengajarkannya." (HR. Bukhari)</p>
                    </div>
                </div>
                <!-- Pie Chart -->
                <div class="glass-card d-flex flex-column">
                    <h3 class="card-title-tv"><i class="fas fa-chart-pie"></i> Rasio Distribusi Gender Guru</h3>
                    <div class="chart-wrapper flex-grow-1" style="max-height: 400px;">
                        <canvas id="guruGenderChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. BLOCK: ABSENSI SANTRI -->
        <div class="tv-slide-card d-none" id="card-absensi_santri">
            <h2 class="slide-main-title"><i class="fas fa-chart-line text-purple"></i> Kehadiran / Absensi Santri</h2>
            <div class="glass-card d-flex flex-column card-full-height overflow-hidden">
                <h3 class="card-title-tv"><i class="fas fa-chart-bar"></i> Kehadiran Per Kelas (Kombinasi Garis & Batang - <span id="kehadiranKelasDuaMinggu">2 Minggu</span>)</h3>
                <div class="chart-wrapper flex-grow-1">
                    <canvas id="absensiSantriCombinedChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 5. BLOCK: ABSENSI GURU -->
        <div class="tv-slide-card d-none" id="card-absensi_guru">
            <h2 class="slide-main-title"><i class="fas fa-user-check text-danger"></i> Kehadiran / Absensi Guru</h2>
            <div class="card-grid-absensi">
                <!-- Grafik Harian Minggu Ini -->
                <div class="glass-card">
                    <h3 class="card-title-tv"><i class="fas fa-chart-bar"></i> Kehadiran Ustadz/ah Harian (Minggu Berjalan)</h3>
                    <div class="chart-wrapper">
                        <canvas id="absensiGuruHarianChart"></canvas>
                    </div>
                </div>
                <!-- Trend Bulanan -->
                <div class="glass-card">
                    <h3 class="card-title-tv"><i class="fas fa-chart-area"></i> Trend Kehadiran Guru (<span id="trendGuruBulanTahun">30 Hari Terakhir</span>)</h3>
                    <div class="chart-wrapper">
                        <canvas id="absensiGuruBulananChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- BLOCK: TREN KELULUSAN MUNAQOSAH -->
        <div class="tv-slide-card d-none" id="card-trend_kelulusan">
            <h2 class="slide-main-title"><i class="fas fa-graduation-cap text-warning"></i> Tren Kelulusan Ujian Munaqosah</h2>
            <div class="grid-2-columns">
                <!-- Tabel Tren Kelulusan -->
                <div class="glass-card">
                    <h3 class="card-title-tv"><i class="fas fa-list-ol"></i> Rekapitulasi Kelulusan per Tahun Ajaran</h3>
                    <div class="table-tv-wrapper">
                        <table class="table-tv">
                            <thead>
                                <tr>
                                    <th>Tahun Ajaran</th>
                                    <th class="text-center">Peserta</th>
                                    <th class="text-center text-success">Lulus</th>
                                    <th class="text-center text-danger">Belum Lulus</th>
                                    <th class="text-center text-primary">Persentase</th>
                                </tr>
                            </thead>
                            <tbody id="graduationTrendTableBody">
                                <!-- Dinamis via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Grafik Tren Persentase Kelulusan -->
                <div class="glass-card">
                    <h3 class="card-title-tv"><i class="fas fa-chart-line text-primary"></i> Grafik Tren Persentase Kelulusan (%)</h3>
                    <div class="chart-wrapper">
                        <canvas id="munaqosahGraduationTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- BLOCK: DAFTAR ALUMNI -->
        <div class="tv-slide-card d-none" id="card-daftar_alumni">
            <h2 class="slide-main-title"><i class="fas fa-graduation-cap text-success"></i> Tren Perkembangan Alumni per Tahun Ajaran</h2>
            <div class="grid-2-columns">
                <!-- Tabel Jumlah Alumni -->
                <div class="glass-card">
                    <h3 class="card-title-tv"><i class="fas fa-list-ol"></i> Rekapitulasi Alumni per Angkatan</h3>
                    <div class="table-tv-wrapper">
                        <table class="table-tv">
                            <thead>
                                <tr>
                                    <th>Tahun Ajaran</th>
                                    <th class="text-center text-primary">Laki-Laki</th>
                                    <th class="text-center text-pink">Perempuan</th>
                                    <th class="text-center text-success">Total Alumni</th>
                                </tr>
                            </thead>
                            <tbody id="alumniTrendTableBody">
                                <!-- Dinamis via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Grafik Tren Alumni -->
                <div class="glass-card">
                    <h3 class="card-title-tv"><i class="fas fa-chart-bar text-success"></i> Grafik Perkembangan Jumlah Alumni</h3>
                    <div class="chart-wrapper">
                        <canvas id="alumniTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. BLOCK: JADWAL SHOLAT -->
        <div class="tv-slide-card d-none" id="card-jadwal_sholat">
            <h2 class="slide-main-title"><i class="fas fa-mosque text-warning"></i> Jadwal Sholat & Countdown Waktu Sholat</h2>
            <div class="glass-card p-4 card-full-height">
                <div class="prayer-tv-container">
                    
                    <!-- Timer Utama -->
                    <div class="prayer-countdown-tv">
                        <div class="prayer-countdown-label">Menjelang Waktu Sholat</div>
                        <div class="prayer-countdown-time" id="prayerTimerClock">--:--:--</div>
                        <div class="prayer-countdown-next" id="prayerTimerNextName">Memuat waktu sholat berikutnya...</div>
                    </div>
                    
                    <!-- Waktu Sholat Grid -->
                    <div class="prayer-times-grid">
                        <div class="prayer-box" data-prayer="fajr">
                            <span class="pb-name">Subuh</span>
                            <h2 class="pb-time" id="sholat-subuh">--:--</h2>
                        </div>
                        <div class="prayer-box" data-prayer="shurooq">
                            <span class="pb-name">Syuruq</span>
                            <h2 class="pb-time" id="sholat-syuruq">--:--</h2>
                        </div>
                        <div class="prayer-box" data-prayer="dhuhr">
                            <span class="pb-name">Dzuhur</span>
                            <h2 class="pb-time" id="sholat-dzuhur">--:--</h2>
                        </div>
                        <div class="prayer-box" data-prayer="asr">
                            <span class="pb-name">Ashar</span>
                            <h2 class="pb-time" id="sholat-ashar">--:--</h2>
                        </div>
                        <div class="prayer-box" data-prayer="maghrib">
                            <span class="pb-name">Maghrib</span>
                            <h2 class="pb-time" id="sholat-maghrib">--:--</h2>
                        </div>
                        <div class="prayer-box" data-prayer="isha">
                            <span class="pb-name">Isya</span>
                            <h2 class="pb-time" id="sholat-isya">--:--</h2>
                        </div>
                    </div>

                    <div class="text-center mt-4 text-muted text-sm">
                        <i class="fas fa-map-marker-alt text-warning"></i> Jadwal Sholat Wilayah: <strong id="sholat-lokasi">-</strong> (Auto GPS/Kota Bintan)
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. BLOCK: DETAIL STATISTIK ABSENSI (Per Kelas) -->
        <div class="tv-slide-card d-none" id="card-statistik_absensi">
            <h2 class="slide-main-title"><i class="fas fa-chart-bar text-primary"></i> Statistik Absensi per Kelas <span id="statistikAbsensiPekanIni">(Pekan Ini)</span></h2>
            <div class="grid-2-columns">
                <!-- Tabel Keadaan Santri -->
                <div class="glass-card d-flex flex-column overflow-hidden">
                    <h3 class="card-title-tv"><i class="fas fa-list-ol"></i> Rekapitulasi Kehadiran Kelas</h3>
                    <div class="table-tv-wrapper flex-grow-1 overflow-y-auto">
                        <table class="table-tv">
                            <thead>
                                <tr>
                                    <th>Nama Kelas</th>
                                    <th class="text-center text-success">Hadir</th>
                                    <th class="text-center text-warning">Izin</th>
                                    <th class="text-center text-info">Sakit</th>
                                    <th class="text-center text-danger">Alfa</th>
                                    <th class="text-center text-primary">Total</th>
                                </tr>
                            </thead>
                            <tbody id="kehadiranKelasTableBody">
                                <!-- Dinamis via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="glass-card">
                    <h3 class="card-title-tv"><i class="fas fa-chart-bar text-success"></i> Grafik Perbandingan Sakit, Izin, & Alfa</h3>
                    <div class="chart-wrapper">
                        <canvas id="kehadiranKelasPerbandinganChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- BLOCK: ULANG TAHUN TERDEKAT -->
        <div class="tv-slide-card d-none" id="card-ulang_tahun">
            <h2 class="slide-main-title"><i class="fas fa-birthday-cake text-danger"></i> Ulang Tahun Terdekat</h2>
            <div class="grid-2-columns grid-birthday-full">
                <!-- List Guru Ulang Tahun -->
                <div class="glass-card d-flex flex-column overflow-hidden">
                    <h3 class="card-title-tv"><i class="fas fa-user-tie text-success"></i> Guru / Ustadz-Ustadzah</h3>
                    <div class="table-tv-wrapper flex-grow-1 overflow-y-auto p-3" id="birthdayGuruList">
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-birthday-cake fa-3x mb-3"></i>
                            <p>Memuat data ulang tahun...</p>
                        </div>
                    </div>
                </div>
                <!-- List Santri Ulang Tahun -->
                <div class="glass-card d-flex flex-column overflow-hidden">
                    <h3 class="card-title-tv"><i class="fas fa-user-graduate text-primary"></i> Santri / Santriwati</h3>
                    <div class="table-tv-wrapper flex-grow-1 overflow-y-auto p-3" id="birthdaySantriList">
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-birthday-cake fa-3x mb-3"></i>
                            <p>Memuat data ulang tahun...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. BLOCK: GALERI KEGIATAN -->
        <div class="tv-slide-card d-none" id="card-galeri">
            <h2 class="slide-main-title"><i class="fas fa-images text-info"></i> Dokumentasi Kegiatan Lembaga</h2>
            <div class="galeri-tv-container">
                <div class="galeri-tv-wrapper">
                    <!-- Dinamis via JS, Slider Dalam Slide -->
                    <div class="galeri-tv-slide" id="galeriActiveSlide">
                        <div class="galeri-tv-image-wrapper">
                            <img src="" alt="Galeri" id="galeriActiveImage">
                        </div>
                        <div class="galeri-tv-caption">
                            <h3 id="galeriActiveTitle">Memuat foto...</h3>
                            <span class="galeri-date" id="galeriActiveDate">-</span>
                            <p id="galeriActiveDesc">-</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 8. BLOCK: AGENDA -->
        <div class="tv-slide-card d-none" id="card-agenda">
            <h2 class="slide-main-title"><i class="fas fa-calendar-check text-warning"></i> Agenda & Kegiatan Mendatang</h2>
            <div class="glass-card d-flex flex-column card-full-height overflow-hidden">
                <div class="agenda-tv-container flex-grow-1 d-flex flex-column overflow-hidden">
                    <div class="table-tv-wrapper flex-grow-1 overflow-y-auto">
                        <table class="table-tv agenda-table">
                            <thead>
                                <tr>
                                    <th>Nama Kegiatan</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Tempat</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="agendaTableBody">
                                <!-- Dinamis via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 9. BLOCK HOME (FKPQ VERSION) -->
        <div class="tv-slide-card d-none" id="card-home_fkpq">
            <div class="card-grid-home-fkpq">
                <!-- FKPQ Summary Stats -->
                <div class="stat-box-tv border-blue">
                    <div class="stat-icon"><i class="fas fa-mosque"></i></div>
                    <div class="stat-content">
                        <span class="stat-label">TPQ Binaan</span>
                        <h2 class="stat-number" id="fkpqTotalTpq">0</h2>
                        <span class="stat-subtext">Lembaga Terdaftar</span>
                    </div>
                </div>
                <div class="stat-box-tv border-green">
                    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="stat-content">
                        <span class="stat-label">Total Santri Binaan</span>
                        <h2 class="stat-number" id="fkpqTotalSantri">0</h2>
                        <span class="stat-subtext" id="fkpqSantriLp">L: 0 | P: 0</span>
                    </div>
                </div>
                <div class="stat-box-tv border-purple">
                    <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
                    <div class="stat-content">
                        <span class="stat-label">Total Guru</span>
                        <h2 class="stat-number" id="fkpqTotalGuru">0</h2>
                        <span class="stat-subtext" id="fkpqGuruLp">L: 0 | P: 0</span>
                    </div>
                </div>
                <div class="stat-box-tv border-orange">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-content">
                        <span class="stat-label">Kehadiran TPQ</span>
                        <h2 class="stat-number" id="fkpqKehadiranToday">0</h2>
                        <span class="stat-subtext">Input Hari Ini</span>
                    </div>
                </div>

                <!-- Main Grid Breakdown -->
                <div class="home-fkpq-breakdown glass-card">
                    <h3 class="card-title-tv"><i class="fas fa-list-ol text-warning"></i> Peringkat Distribusi Lembaga TPQ</h3>
                    <div class="table-tv-wrapper" style="max-height: 380px; overflow-y: auto;">
                        <table class="table-tv">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Lembaga TPQ</th>
                                    <th class="text-center">Total Santri</th>
                                    <th class="text-center">Total Guru</th>
                                    <th class="text-center">Rasio</th>
                                </tr>
                            </thead>
                            <tbody id="fkpqTpqTableBody">
                                <!-- Dinamis via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="glass-card">
                    <h3 class="card-title-tv"><i class="fas fa-chart-pie text-success"></i> Rasio Total Santri vs Guru</h3>
                    <div class="chart-wrapper" style="max-height: 380px;">
                        <canvas id="fkpqRasioChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- FOOTER CONTROLS & DOTS -->
    <footer class="tv-footer">
        <div class="footer-control-indicator">
            <span class="refresh-indicator" id="tvRefreshTimer"><i class="fas fa-sync fa-spin text-info mr-1"></i> Data: 05:00</span>
        </div>
        
        <!-- Navigation indicators dots -->
        <div class="tv-slide-dots" id="tvSlideDots">
            <!-- Dinamis via JS -->
        </div>
        
        <div class="footer-status-label">
            <span id="tvCurrentSlideName">HOME</span>
        </div>
    </footer>

</div>

<?= $this->endSection(); ?>
