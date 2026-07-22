/**
 * TV Digital / Digital Signage Script
 * Handles real-time clock, slideshow engine, Chart.js rendering,
 * MuslimSalat API integration, and AJAX data refreshing.
 */

$(document).ready(function () {

    // Core parameters
    const hashKey = $('#tvContainer').data('hash');
    const baseUrl = window.location.origin;

    let activeSlides = []; // List of active block keys
    let currentSlideIndex = 0;
    let slideshowTimer = null;
    let refreshTimer = null;
    let refreshSecondsLeft = 300; // Default 5 mins in seconds

    // Slide-specific data placeholders
    let appData = {};
    let charts = {}; // ChartJS references
    let prayerTimes = {};
    let nextPrayerTimer = null;

    // Galeri inner slideshow params
    let galeriData = [];
    let currentGaleriIndex = 0;
    let galeriTimer = null;

    // Start clock
    initClock();

    // Start prayer times
    initPrayerTimes();

    // Load initial layout data & start slideshow loop
    loadData();

    // Keyboard navigation (Arrow keys left/right to navigate manual)
    $(document).keydown(function (e) {
        if (e.keyCode === 37) { // Left arrow
            navigateSlide(-1);
        } else if (e.keyCode === 39) { // Right arrow
            navigateSlide(1);
        }
    });

    // ==========================================
    // 1. SYSTEM CLOCK
    // ==========================================
    function initClock() {
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        setInterval(() => {
            const now = new Date();
            const timeStr = now.toTimeString().split(' ')[0];
            const dateStr = days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();

            $('#tvTime').text(timeStr);
            $('#tvDate').text(dateStr);
        }, 1000);
    }

    // ==========================================
    // 2. DATA LOADER & DYNAMIC REFRESH
    // ==========================================
    function loadData() {
        // Destroy monthly charts to force recreate with fresh data on slide enter
        if (charts['homeAbsensiChart']) {
            charts['homeAbsensiChart'].destroy();
            delete charts['homeAbsensiChart'];
        }
        if (charts['homeAbsensiBarChart']) {
            charts['homeAbsensiBarChart'].destroy();
            delete charts['homeAbsensiBarChart'];
        }

        $.getJSON(`${baseUrl}/tv/api/data/${hashKey}`, function (response) {
            if (response.status === 'success') {
                appData = response.data;

                // Update header & info
                $('#tvLembagaName').text(appData.lembaga.nama);
                if (appData.lembaga.logo) {
                    $('#tvLogo').attr('src', appData.lembaga.logo);
                }

                // Apply theme class dynamically to container
                $('#tvContainer').removeClass('theme-dark theme-colorful theme-light').addClass('theme-' + (appData.theme || 'dark'));

                // Build active slides list from response configs
                activeSlides = [];
                if (appData.activeBlocks && appData.activeBlocks.length > 0) {
                    $.each(appData.activeBlocks, function (i, block) {
                        let key = block.BlockKey;
                        // Map home to home_fkpq for FKPQ
                        if (key === 'home' && appData.lembaga.isFkpq) {
                            key = 'home_fkpq';
                        }
                        activeSlides.push(key);
                    });
                }

                // Fallback jika tidak ada block aktif sama sekali
                if (activeSlides.length === 0) {
                    if (appData.lembaga.isFkpq) {
                        activeSlides.push('home_fkpq');
                    } else {
                        activeSlides.push('home');
                    }
                }

                // Render dot indicators
                buildDots();

                // Populate Summary statistics cards
                populateSummaryStats();

                // Populate tables
                populateTables();

                // Fetch block specific data
                fetchGaleri();
                fetchAgenda();
                fetchAbsensiCharts();
                fetchUlangTahun();

                // Start or adjust timers
                refreshSecondsLeft = appData.refreshInterval * 60;
                startRefreshTimer();

                // Show first slide
                showSlide(currentSlideIndex);
            }
        });
    }

    // Refresh countdown loop
    function startRefreshTimer() {
        if (refreshTimer) clearInterval(refreshTimer);

        refreshTimer = setInterval(() => {
            refreshSecondsLeft--;
            if (refreshSecondsLeft <= 0) {
                loadData(); // Dynamic reload statistics
            } else {
                const mins = Math.floor(refreshSecondsLeft / 60);
                const secs = refreshSecondsLeft % 60;
                const formattedTime = (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
                $('#tvRefreshTimer').html(`<i class="fas fa-sync fa-spin text-info mr-1"></i> Data: ${formattedTime}`);
            }
        }, 1000);
    }

    // ==========================================
    // 3. STATS & TABLES INJECTOR
    // ==========================================
    function populateSummaryStats() {
        const stats = appData.ringkasan;

        // Standard Home
        $('#homeTotalSantri').text(stats.totalSantri);
        $('#homeTotalGuru').text(stats.totalGuru);
        $('#homeTotalKelas').text(stats.totalKelas);
        $('#homeSantriLp').text(`L: ${stats.santriLaki} | P: ${stats.santriPerempuan}`);
        $('#homeGuruLp').text(`L: ${stats.guruLaki} | P: ${stats.guruPerempuan}`);

        // Hitung total alumni & lulus munaqosah untuk home summary card
        let totalAlumniHome = 0;
        if (appData.alumniList) {
            $.each(appData.alumniList, function (i, row) {
                totalAlumniHome += (row.Total || 0);
            });
        }
        $('#homeTotalAlumni').text(totalAlumniHome + ' Santri');

        let totalMunaqosahLulusHome = 0;
        if (appData.munaqosahGraduationStats) {
            $.each(appData.munaqosahGraduationStats, function (i, row) {
                totalMunaqosahLulusHome += (row.Lulus || 0);
            });
        }
        $('#homeMunaqosahLulus').text(totalMunaqosahLulusHome + ' Santri');

        // Attendance calculations for Home Card
        const totalTodayAbsen = (stats.absensiSantriToday ? (stats.absensiSantriToday.Hadir + stats.absensiSantriToday.Izin + stats.absensiSantriToday.Sakit + stats.absensiSantriToday.Alfa) : 0);
        
        let totalPekanIni = 0;
        let hadirPekanIni = 0;
        if (appData.ringkasanKehadiranMingguIni) {
            const rkm = appData.ringkasanKehadiranMingguIni;
            totalPekanIni = (rkm.Hadir || 0) + (rkm.Izin || 0) + (rkm.Sakit || 0) + (rkm.Alfa || 0);
            hadirPekanIni = rkm.Hadir || 0;
        }

        if (totalTodayAbsen > 0) {
            const totalSantriAktif = stats.totalSantri || totalTodayAbsen;
            const pct = Math.round((stats.absensiSantriToday.Hadir / totalSantriAktif) * 100);
            $('#homeKehadiranLabel').text('Kehadiran Hari Ini');
            $('#homeKehadiranPersen').text(pct + '%');
            const h = stats.absensiSantriToday;
            $('#homeKehadiranRatio').html(`Hari Ini - H: <strong>${h.Hadir}/${totalSantriAktif}</strong> | I: <strong>${h.Izin}</strong> | S: <strong>${h.Sakit}</strong> | A: <strong>${h.Alfa}</strong>`);
        } else if (totalPekanIni > 0) {
            const pct = Math.round((hadirPekanIni / totalPekanIni) * 100);
            $('#homeKehadiranLabel').text('Kehadiran Pekan Ini');
            $('#homeKehadiranPersen').text(pct + '%');
            const rkm = appData.ringkasanKehadiranMingguIni;
            $('#homeKehadiranRatio').html(`Pkn Ini - H: <strong>${rkm.Hadir}</strong> | I: <strong>${rkm.Izin}</strong> | S: <strong>${rkm.Sakit}</strong> | A: <strong>${rkm.Alfa}</strong>`);
        } else {
            $('#homeKehadiranLabel').text('Kehadiran Hari Ini');
            $('#homeKehadiranPersen').text('0%');
            $('#homeKehadiranRatio').text('Belum ada data absensi');
        }

        // FKPQ Home
        $('#fkpqTotalTpq').text(appData.statistikPerTpq ? appData.statistikPerTpq.length : 0);
        $('#fkpqTotalSantri').text(stats.totalSantri);
        $('#fkpqTotalGuru').text(stats.totalGuru);
        $('#fkpqSantriLp').text(`L: ${stats.santriLaki} | P: ${stats.santriPerempuan}`);
        $('#fkpqGuruLp').text(`L: ${stats.guruLaki} | P: ${stats.guruPerempuan}`);

        // Input counters today
        $('#fkpqKehadiranToday').text(totalTodayAbsen > 0 ? 'Aktif' : 'Nihil');

        // Guru detailed slide stats
        $('#guruTotalStat').text(stats.totalGuru);
        $('#guruLakiStat').text(stats.guruLaki);
        $('#guruPerempuanStat').text(stats.guruPerempuan);
    }

    function populateTables() {
        // Santri Kelas Table
        if (appData.santriPerKelas) {
            let trHtml = '';
            $.each(appData.santriPerKelas, function (i, row) {
                trHtml += `
                    <tr>
                        <td class="font-weight-bold">${row.NamaKelas}</td>
                        <td class="text-center text-primary">${row.LakiLaki}</td>
                        <td class="text-center text-pink">${row.Perempuan}</td>
                        <td class="text-center font-weight-bold">${row.Total}</td>
                    </tr>
                `;
            });
            $('#santriKelasTableBody').html(trHtml);
        }

        // FKPQ Tpq breakdown rank table
        if (appData.statistikPerTpq) {
            let trHtml = '';
            $.each(appData.statistikPerTpq, function (i, row) {
                const rank = i + 1;
                const rasio = row.Guru > 0 ? Math.round(row.Santri / row.Guru) : row.Santri;
                trHtml += `
                    <tr>
                        <td class="font-weight-bold">${rank}</td>
                        <td class="font-weight-bold">${row.NamaTpq}</td>
                        <td class="text-center text-success">${row.Santri}</td>
                        <td class="text-center text-info">${row.Guru}</td>
                        <td class="text-center text-muted">1 : ${rasio}</td>
                    </tr>
                `;
            });
            $('#fkpqTpqTableBody').html(trHtml);
        }

        // Kehadiran Kelas Table (Pekan Ini)
        if (appData.statistikKehadiranKelas) {
            // Update title range tanggal pekan ini (Senin - Ahad)
            const today = new Date();
            const dayOfWeek = today.getDay(); // 0 = Minggu, 1 = Senin, dst
            const mondayOffset = (dayOfWeek == 0) ? -6 : (1 - dayOfWeek);

            const monday = new Date(today);
            monday.setDate(today.getDate() + mondayOffset);

            const sunday = new Date(monday);
            sunday.setDate(monday.getDate() + 6);

            const formatDate = (date) => {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                return date.getDate() + ' ' + months[date.getMonth()];
            };
            const currentYear = monday.getFullYear();

            $('#statistikAbsensiPekanIni').text(`(${formatDate(monday)} - ${formatDate(sunday)} ${currentYear})`);

            let trHtml = '';
            let totalHadir = 0;
            let totalIzin = 0;
            let totalSakit = 0;
            let totalAlfa = 0;
            let grandTotal = 0;

            $.each(appData.statistikKehadiranKelas, function (i, row) {
                const rowHadir = parseInt(row.Hadir) || 0;
                const rowIzin = parseInt(row.Izin) || 0;
                const rowSakit = parseInt(row.Sakit) || 0;
                const rowAlfa = parseInt(row.Alfa) || 0;
                const rowTotal = rowHadir + rowIzin + rowSakit + rowAlfa;

                totalHadir += rowHadir;
                totalIzin += rowIzin;
                totalSakit += rowSakit;
                totalAlfa += rowAlfa;
                grandTotal += rowTotal;

                trHtml += `
                    <tr>
                        <td class="font-weight-bold">${row.NamaKelas}</td>
                        <td class="text-center text-success">${rowHadir}</td>
                        <td class="text-center text-warning">${rowIzin}</td>
                        <td class="text-center text-info">${rowSakit}</td>
                        <td class="text-center text-danger">${rowAlfa}</td>
                        <td class="text-center text-primary font-weight-bold">${rowTotal}</td>
                    </tr>
                `;
            });

            // Append Total row
            trHtml += `
                <tr class="font-weight-bold" style="background: rgba(255, 255, 255, 0.05); border-top: 2px solid rgba(255, 255, 255, 0.1);">
                    <td>TOTAL</td>
                    <td class="text-center text-success">${totalHadir}</td>
                    <td class="text-center text-warning">${totalIzin}</td>
                    <td class="text-center text-info">${totalSakit}</td>
                    <td class="text-center text-danger">${totalAlfa}</td>
                    <td class="text-center text-primary">${grandTotal}</td>
                </tr>
            `;
            $('#kehadiranKelasTableBody').html(trHtml);
        }

        // Munaqosah Graduation Trend Table
        if (appData.munaqosahGraduationStats) {
            let trHtml = '';
            $.each(appData.munaqosahGraduationStats, function (i, row) {
                let taLabel = row.TahunAjaran;
                if (taLabel && taLabel.length === 8) {
                    taLabel = taLabel.substring(0, 4) + '/' + taLabel.substring(4);
                }
                trHtml += `
                    <tr>
                        <td class="font-weight-bold">${taLabel}</td>
                        <td class="text-center font-weight-bold">${row.Peserta}</td>
                        <td class="text-center text-success font-weight-bold">${row.Lulus}</td>
                        <td class="text-center text-danger font-weight-bold">${row.TidakLulus}</td>
                        <td class="text-center text-primary font-weight-bold">${row.Persentase}%</td>
                    </tr>
                `;
            });
            $('#graduationTrendTableBody').html(trHtml);
        }

        // Alumni Trend Table
        if (appData.alumniList) {
            let trHtml = '';
            $.each(appData.alumniList, function (i, row) {
                let taLabel = row.TahunAjaran;
                if (taLabel && taLabel.length === 8) {
                    taLabel = taLabel.substring(0, 4) + '/' + taLabel.substring(4);
                }
                let countL = 0;
                let countP = 0;
                if (row.Santri) {
                    $.each(row.Santri, function (j, s) {
                        if (s.JenisKelamin.toLowerCase().includes('laki')) countL++; else countP++;
                    });
                }
                trHtml += `
                    <tr>
                        <td class="font-weight-bold">${taLabel}</td>
                        <td class="text-center text-primary font-weight-bold">${countL}</td>
                        <td class="text-center text-pink font-weight-bold">${countP}</td>
                        <td class="text-center text-success font-weight-bold">${row.Total}</td>
                    </tr>
                `;
            });
            $('#alumniTrendTableBody').html(trHtml);
        }
    }

    // ==========================================
    // 4. SLIDESHOW ENGINE
    // ==========================================
    function buildDots() {
        let dotsHtml = '';
        $.each(activeSlides, function (i, key) {
            dotsHtml += `<span class="tv-dot" data-index="${i}"></span>`;
        });
        $('#tvSlideDots').html(dotsHtml);

        // Dot clicks to manually navigate
        $('.tv-dot').click(function () {
            showSlide($(this).data('index'));
        });
    }

    function showSlide(index) {
        if (activeSlides.length === 0) return;

        // Clear previous slideshow timer
        if (slideshowTimer) clearTimeout(slideshowTimer);

        // Hide all cards
        $('.tv-slide-card').addClass('d-none');

        // Adjust index boundary
        currentSlideIndex = (index + activeSlides.length) % activeSlides.length;
        const currentKey = activeSlides[currentSlideIndex];

        // Display target slide
        $(`#card-${currentKey}`).removeClass('d-none');

        // Update dots UI
        $('.tv-dot').removeClass('active-dot');
        $(`.tv-dot[data-index="${currentSlideIndex}"]`).addClass('active-dot');

        // Update Footer Label
        const prettyName = currentKey.replace('_', ' ').toUpperCase();
        $('#tvCurrentSlideName').text(prettyName);

        // Re-render block specific charts/gallery loops
        triggerSlideEnter(currentKey);

        // Queue next slide auto duration
        const duration = (appData.slideshowInterval || 15) * 1000;
        slideshowTimer = setTimeout(() => {
            navigateSlide(1);
        }, duration);
    }

    function navigateSlide(direction) {
        showSlide(currentSlideIndex + direction);
    }

    function triggerSlideEnter(key) {
        if (key === 'galeri') {
            startGaleriLoop();
        } else {
            stopGaleriLoop();
        }

        // Initialize or update charts dynamically when that slide is shown
        renderCharts(key);
    }

    // ==========================================
    // 5. CHART.JS RENDERING & DATA VISUALIZATION
    // ==========================================
    function renderCharts(key) {
        // Destroy existing instance if recreate is needed

        // 1. HOME: Last 30 days trend line chart
        if ((key === 'home' || key === 'home_fkpq') && $('#homeAbsensiChart').length) {
            fetchMonthlyChartData('homeAbsensiChart', 'home');
        }

        // 2. SANTRI: Bar chart distribution & Gender pie chart
        if (key === 'keadaan_santri' && appData.santriPerKelas) {
            const labels = [];
            const dataLaki = [];
            const dataPerempuan = [];
            $.each(appData.santriPerKelas, function (i, r) {
                labels.push(r.NamaKelas);
                dataLaki.push(r.LakiLaki);
                dataPerempuan.push(r.Perempuan);
            });
            createBarChart('santriDistribusiChart', labels, dataLaki, dataPerempuan);

            const stats = appData.ringkasan;
            createPieChart('santriGenderRasioChart', ['Santri (L)', 'Santriwati (P)'], [stats.santriLaki, stats.santriPerempuan]);
        }

        // 3. GURU: Gender pie chart
        if (key === 'keadaan_guru') {
            const stats = appData.ringkasan;
            createPieChart('guruGenderChart', ['Ustadz (L)', 'Ustadzah (P)'], [stats.guruLaki, stats.guruPerempuan]);
        }

        // 4. FKPQ HOME: Rasio Pie Chart
        if (key === 'home_fkpq') {
            const stats = appData.ringkasan;
            createPieChart('fkpqRasioChart', ['Total Santri', 'Total Guru'], [stats.totalSantri, stats.totalGuru]);
        }

        // 5. DETAIL ABSENSI: Bar chart per kelas (comparing Sakit, Izin, Alfa)
        if (key === 'statistik_absensi' && appData.statistikKehadiranKelas) {
            const labels = [];
            const dataSakit = [];
            const dataIzin = [];
            const dataAlfa = [];
            const dataTotal = [];
            $.each(appData.statistikKehadiranKelas, function (i, r) {
                labels.push(r.NamaKelas);
                dataSakit.push(r.Sakit);
                dataIzin.push(r.Izin);
                dataAlfa.push(r.Alfa);

                const total = (parseInt(r.Sakit) || 0) + (parseInt(r.Izin) || 0) + (parseInt(r.Alfa) || 0);
                dataTotal.push(total);
            });
            createAbsensiPerbandinganChart('kehadiranKelasPerbandinganChart', labels, dataSakit, dataIzin, dataAlfa, dataTotal);
        }

        // 6. TREN KELULUSAN MUNAQOSAH: Line chart of graduation percentage over years
        if (key === 'trend_kelulusan' && appData.munaqosahGraduationStats) {
            const labels = [];
            const dataPct = [];
            $.each(appData.munaqosahGraduationStats, function (i, r) {
                let taLabel = r.TahunAjaran;
                if (taLabel && taLabel.length === 8) {
                    taLabel = taLabel.substring(0, 4) + '/' + taLabel.substring(4);
                }
                labels.push(taLabel);
                dataPct.push(r.Persentase);
            });
            createGraduationTrendChart('munaqosahGraduationTrendChart', labels, dataPct);
        }

        // 7. ALUMNI TREND: Bar chart of total alumni per year
        if (key === 'daftar_alumni' && appData.alumniList) {
            const labels = [];
            const dataLaki = [];
            const dataPerempuan = [];
            const dataTotal = [];

            // Reverse list to show chronological order (oldest to newest)
            const reversedList = [...appData.alumniList].reverse();
            $.each(reversedList, function (i, r) {
                let taLabel = r.TahunAjaran;
                if (taLabel && taLabel.length === 8) {
                    taLabel = taLabel.substring(0, 4) + '/' + taLabel.substring(4);
                }
                labels.push(taLabel);
                dataTotal.push(r.Total);

                let countL = 0;
                let countP = 0;
                if (r.Santri) {
                    $.each(r.Santri, function (j, s) {
                        if (s.JenisKelamin.toLowerCase().includes('laki')) countL++; else countP++;
                    });
                }
                dataLaki.push(countL);
                dataPerempuan.push(countP);
            });

            createAlumniTrendChart('alumniTrendChart', labels, dataLaki, dataPerempuan, dataTotal);
        }
    }

    // Dynamic Chart generators helper functions
    function getChartThemeColors() {
        const isLight = $('#tvContainer').hasClass('theme-light');
        return {
            textColor: isLight ? '#475569' : '#a0a0c0',
            gridColor: isLight ? 'rgba(0,0,0,0.06)' : 'rgba(255,255,255,0.05)',
            legendColor: isLight ? '#1e293b' : '#ffffff'
        };
    }

    function createBarChart(canvasId, labels, dataL, dataP) {
        if (charts[canvasId]) charts[canvasId].destroy();

        const colors = getChartThemeColors();
        const ctx = document.getElementById(canvasId).getContext('2d');
        charts[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Laki-Laki',
                        data: dataL,
                        backgroundColor: '#007bff'
                    },
                    {
                        label: 'Perempuan',
                        data: dataP,
                        backgroundColor: '#e83e8c'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                        stacked: true,
                        gridLines: { color: colors.gridColor },
                        ticks: { fontColor: colors.textColor }
                    }],
                    yAxes: [{
                        stacked: true,
                        gridLines: { color: colors.gridColor },
                        ticks: {
                            beginAtZero: true,
                            min: 0,
                            fontColor: colors.textColor,
                            precision: 0,
                            callback: function (value) {
                                if (value % 1 === 0) {
                                    return value;
                                }
                            }
                        }
                    }]
                },
                plugins: {
                    legend: { labels: { color: colors.legendColor } }
                }
            }
        });
    }

    function createAlumniTrendChart(canvasId, labels, dataL, dataP, dataTotal) {
        if (charts[canvasId]) charts[canvasId].destroy();

        const colors = getChartThemeColors();
        const ctx = document.getElementById(canvasId).getContext('2d');
        charts[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Laki-Laki',
                        data: dataL,
                        backgroundColor: '#3b82f6',
                        borderWidth: 0,
                        borderRadius: 6
                    },
                    {
                        label: 'Perempuan',
                        data: dataP,
                        backgroundColor: '#ec4899',
                        borderWidth: 0,
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                        gridLines: { color: colors.gridColor },
                        ticks: { fontColor: colors.textColor }
                    }],
                    yAxes: [{
                        gridLines: { color: colors.gridColor },
                        ticks: {
                            beginAtZero: true,
                            min: 0,
                            fontColor: colors.textColor,
                            precision: 0,
                            callback: function (value) {
                                if (value % 1 === 0) {
                                    return value;
                                }
                            }
                        }
                    }]
                },
                plugins: {
                    legend: { labels: { color: colors.legendColor } }
                }
            }
        });
    }

    function createPieChart(canvasId, labels, data) {
        if (charts[canvasId]) charts[canvasId].destroy();

        const colors = getChartThemeColors();
        const ctx = document.getElementById(canvasId).getContext('2d');
        charts[canvasId] = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#3b82f6', '#ec4899'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom',
                    labels: {
                        fontColor: colors.textColor,
                        fontSize: 14
                    }
                }
            }
        });
    }

    function createAbsensiPerbandinganChart(canvasId, labels, dataSakit, dataIzin, dataAlfa, dataTotal) {
        if (charts[canvasId]) charts[canvasId].destroy();

        const colors = getChartThemeColors();
        const ctx = document.getElementById(canvasId).getContext('2d');

        // Calculate max bar value for left Y-axis suggestedMax offset
        const maxBarValue = Math.max(
            dataSakit.length > 0 ? Math.max(...dataSakit.map(v => parseInt(v) || 0)) : 0,
            dataIzin.length > 0 ? Math.max(...dataIzin.map(v => parseInt(v) || 0)) : 0,
            dataAlfa.length > 0 ? Math.max(...dataAlfa.map(v => parseInt(v) || 0)) : 0
        );
        const suggestedBarsMax = maxBarValue > 0 ? (maxBarValue + 2) : 5;

        const isLight = $('#tvContainer').hasClass('theme-light');
        const totalLineColor = isLight ? '#4f46e5' : '#c084fc'; // Indigo for light, Violet for dark

        charts[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Sakit',
                        data: dataSakit,
                        backgroundColor: '#17a2b8', // var(--color-info)
                        borderColor: '#17a2b8',
                        borderWidth: 1,
                        yAxisID: 'y-axis-bars',
                        order: 2
                    },
                    {
                        type: 'bar',
                        label: 'Izin',
                        data: dataIzin,
                        backgroundColor: '#ffc107', // var(--color-warning)
                        borderColor: '#ffc107',
                        borderWidth: 1,
                        yAxisID: 'y-axis-bars',
                        order: 2
                    },
                    {
                        type: 'bar',
                        label: 'Alfa',
                        data: dataAlfa,
                        backgroundColor: '#dc3545', // red
                        borderColor: '#dc3545',
                        borderWidth: 1,
                        yAxisID: 'y-axis-bars',
                        order: 2
                    },
                    {
                        type: 'line',
                        label: 'Total Tidak Hadir',
                        data: dataTotal,
                        borderColor: totalLineColor,
                        backgroundColor: 'transparent',
                        borderWidth: 3,
                        fill: false,
                        lineTension: 0.25,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: totalLineColor,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1.5,
                        yAxisID: 'y-axis-line',
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        fontColor: colors.textColor,
                        boxWidth: 10,
                        fontSize: 10
                    }
                },
                scales: {
                    xAxes: [{
                        gridLines: { color: colors.gridColor },
                        ticks: { fontColor: colors.textColor }
                    }],
                    yAxes: [
                        {
                            id: 'y-axis-bars',
                            position: 'left',
                            gridLines: { color: colors.gridColor },
                            ticks: {
                                fontColor: colors.textColor,
                                beginAtZero: true,
                                stepSize: 1,
                                suggestedMax: suggestedBarsMax
                            }
                        },
                        {
                            id: 'y-axis-line',
                            position: 'right',
                            display: true, // Show right Y-axis for Total scale
                            gridLines: { display: false },
                            ticks: {
                                fontColor: colors.textColor,
                                beginAtZero: true,
                                precision: 0
                            }
                        }
                    ]
                }
            }
        });
    }

    function createGraduationTrendChart(canvasId, labels, dataPct) {
        if (charts[canvasId]) charts[canvasId].destroy();

        const colors = getChartThemeColors();
        const ctx = document.getElementById(canvasId).getContext('2d');
        charts[canvasId] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Persentase Kelulusan (%)',
                    data: dataPct,
                    borderColor: '#28a745', // green
                    backgroundColor: 'rgba(40,167,69,0.1)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 3,
                    pointRadius: 6,
                    pointBackgroundColor: '#28a745'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                        gridLines: { color: colors.gridColor },
                        ticks: { fontColor: colors.textColor }
                    }],
                    yAxes: [{
                        gridLines: { color: colors.gridColor },
                        ticks: {
                            fontColor: colors.textColor,
                            min: 0,
                            max: 100,
                            callback: function (value) { return value + '%'; }
                        }
                    }]
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // ==========================================
    // 6. EXTERNAL GRAPH DATA AJAX FETCHERS
    // ==========================================
    function fetchMonthlyChartData(canvasId, type) {
        if (charts[canvasId]) return; // Do not refetch if already loaded

        const colors = getChartThemeColors();
        $.getJSON(`${baseUrl}/tv/api/absensi-santri/${hashKey}`, function (response) {
            if (response.status === 'success') {
                const dates = Object.keys(response.data.bulanan);
                const hadirValues = Object.values(response.data.bulanan).map(d => d.Hadir || 0);
                const tidakHadirValues = Object.values(response.data.bulanan).map(d => (d.Izin || 0) + (d.Sakit || 0) + (d.Alfa || 0));
                const formattedDates = dates.map(d => d.substring(8, 10) + '/' + d.substring(5, 7));

                // 1. Line Chart: Kehadiran vs Ketidakhadiran
                const ctxLine = document.getElementById(canvasId).getContext('2d');
                charts[canvasId] = new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: formattedDates,
                        datasets: [
                            {
                                label: 'Hadir',
                                data: hadirValues,
                                borderColor: '#28a745',
                                backgroundColor: 'rgba(40,167,69,0.08)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 3,
                                pointBackgroundColor: '#28a745'
                            },
                            {
                                label: 'Tidak Hadir',
                                data: tidakHadirValues,
                                borderColor: '#dc3545',
                                backgroundColor: 'rgba(220,53,69,0.08)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 3,
                                pointBackgroundColor: '#dc3545'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                fontColor: colors.textColor,
                                boxWidth: 12,
                                fontSize: 11
                            }
                        },
                        scales: {
                            xAxes: [{
                                gridLines: { display: false },
                                ticks: { display: false } // Hide bottom ticks to prevent duplication with bar chart
                            }],
                            yAxes: [{
                                gridLines: { color: colors.gridColor },
                                ticks: { fontColor: colors.textColor, fontSize: 10, min: 0 }
                            }]
                        }
                    }
                });

                // 2. Bar Chart: Breakdown Ketidakhadiran (Izin, Sakit, Alfa)
                const barCanvasId = 'homeAbsensiBarChart';
                if ($('#' + barCanvasId).length) {
                    const izinValues = Object.values(response.data.bulanan).map(d => d.Izin || 0);
                    const sakitValues = Object.values(response.data.bulanan).map(d => d.Sakit || 0);
                    const alfaValues = Object.values(response.data.bulanan).map(d => d.Alfa || 0);

                    const ctxBar = document.getElementById(barCanvasId).getContext('2d');
                    charts[barCanvasId] = new Chart(ctxBar, {
                        type: 'bar',
                        data: {
                            labels: formattedDates,
                            datasets: [
                                {
                                    label: 'Izin',
                                    data: izinValues,
                                    backgroundColor: '#ffc107',
                                    borderColor: '#ffc107',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Sakit',
                                    data: sakitValues,
                                    backgroundColor: '#007bff',
                                    borderColor: '#007bff',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Alfa',
                                    data: alfaValues,
                                    backgroundColor: '#dc3545',
                                    borderColor: '#dc3545',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    fontColor: colors.textColor,
                                    boxWidth: 12,
                                    fontSize: 11
                                }
                            },
                            scales: {
                                xAxes: [{
                                    stacked: true,
                                    gridLines: { display: false },
                                    ticks: { fontColor: colors.textColor, fontSize: 10 }
                                }],
                                yAxes: [{
                                    stacked: true,
                                    gridLines: { color: colors.gridColor },
                                    ticks: { fontColor: colors.textColor, fontSize: 10, min: 0 }
                                }]
                            }
                        }
                    });
                }
            }
        });
    }

    function fetchAbsensiCharts() {
        if (appData.lembaga.isFkpq) return; // Skip for FKPQ

        const colors = getChartThemeColors();

        // 1. Santri Attendance Harian & Mingguan Charts
        $.getJSON(`${baseUrl}/tv/api/absensi-santri/${hashKey}`, function (response) {
            if (response.status === 'success') {
                // Update range 2 minggu title (Senin minggu lalu s/d Ahad minggu ini)
                const today = new Date();
                const dayOfWeek = today.getDay();
                const mondayOffset = (dayOfWeek == 0) ? -6 : (1 - dayOfWeek);
                const currentWeekMonday = new Date(today);
                currentWeekMonday.setDate(today.getDate() + mondayOffset);
                const previousWeekMonday = new Date(currentWeekMonday);
                previousWeekMonday.setDate(currentWeekMonday.getDate() - 7);
                const currentWeekSunday = new Date(currentWeekMonday);
                currentWeekSunday.setDate(currentWeekMonday.getDate() + 6);

                const formatDate = (date) => {
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                    return date.getDate() + ' ' + months[date.getMonth()];
                };
                const currentYear = currentWeekSunday.getFullYear();
                $('#kehadiranKelasDuaMinggu').text(`${formatDate(previousWeekMonday)} - ${formatDate(currentWeekSunday)} ${currentYear}`);

                const classColors = [
                    '#28a745', // Hijau
                    '#007bff', // Biru
                    '#ffc107', // Kuning
                    '#dc3545', // Merah
                    '#6f42c1', // Ungu
                    '#20c997', // Teal
                    '#fd7e14', // Orange
                    '#e83e8c', // Pink
                    '#17a2b8', // Cyan
                    '#6c757d' // Gray
                ];

                let combinedDatasets = [];
                let totalAttendanceData = Array(14).fill(0);
                let hasData = false;

                if (response.data.kehadiranPerKelas && response.data.kehadiranPerKelas.datasets) {
                    response.data.kehadiranPerKelas.datasets.forEach(function (dataset, index) {
                        const colorIndex = index % classColors.length;
                        const color = classColors[colorIndex];

                        // Bar dataset (14 Hari) - Solid 100% color
                        combinedDatasets.push({
                            type: 'bar',
                            label: dataset.label, // legend name (e.g. "Kelas A")
                            data: dataset.data,
                            backgroundColor: color, // Solid color
                            borderColor: color,
                            borderWidth: 1,
                            yAxisID: 'y-axis-bars', // Bind to left axis
                            order: 2 // Render behind the line
                        });

                        // Sum daily attendance for total line
                        if (dataset.data && dataset.data.length > 0) {
                            hasData = true;
                            dataset.data.forEach(function (val, dayIdx) {
                                if (dayIdx < 14) {
                                    totalAttendanceData[dayIdx] += (val || 0);
                                }
                            });
                        }
                    });
                }

                // Add single Line dataset for Total Attendance
                if (hasData) {
                    const isLight = $('#tvContainer').hasClass('theme-light');
                    const totalLineColor = isLight ? '#4f46e5' : '#c084fc'; // Indigo for light, Violet for dark

                    combinedDatasets.push({
                        type: 'line',
                        label: 'Total Kehadiran',
                        data: totalAttendanceData,
                        borderColor: totalLineColor,
                        backgroundColor: 'transparent',
                        borderWidth: 3.5,
                        fill: false,
                        lineTension: 0.25,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: totalLineColor,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        yAxisID: 'y-axis-line', // Bind to hidden right axis
                        order: 1 // Render on top of the bars
                    });
                }

                // Calculate max bar value for left Y-axis suggestedMax offset
                let maxBarValue = 0;
                if (response.data.kehadiranPerKelas && response.data.kehadiranPerKelas.datasets) {
                    response.data.kehadiranPerKelas.datasets.forEach(function (dataset) {
                        if (dataset.data) {
                            const datasetMax = Math.max(...dataset.data.map(v => v || 0));
                            if (datasetMax > maxBarValue) {
                                maxBarValue = datasetMax;
                            }
                        }
                    });
                }
                const suggestedBarsMax = maxBarValue > 0 ? (maxBarValue + 2) : 5;

                const ctx = document.getElementById('absensiSantriCombinedChart').getContext('2d');
                if (charts['absensiSantriCombinedChart']) charts['absensiSantriCombinedChart'].destroy();
                charts['absensiSantriCombinedChart'] = new Chart(ctx, {
                    type: 'bar', // Baseline type for mixed chart
                    data: {
                        labels: response.data.kehadiranPerKelas ? response.data.kehadiranPerKelas.labels : [],
                        datasets: combinedDatasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                fontColor: colors.textColor,
                                boxWidth: 10,
                                fontSize: 10
                            }
                        },
                        scales: {
                            xAxes: [{
                                gridLines: { color: colors.gridColor },
                                ticks: {
                                    fontColor: colors.textColor,
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }],
                            yAxes: [
                                {
                                    id: 'y-axis-bars',
                                    position: 'left',
                                    gridLines: { color: colors.gridColor },
                                    ticks: {
                                        fontColor: colors.textColor,
                                        beginAtZero: true,
                                        stepSize: 1,
                                        suggestedMax: suggestedBarsMax
                                    }
                                },
                                {
                                    id: 'y-axis-line',
                                    position: 'right',
                                    display: true, // Show right axis ticks/lines
                                    gridLines: { display: false },
                                    ticks: {
                                        fontColor: colors.textColor,
                                        beginAtZero: true,
                                        precision: 0
                                    }
                                }
                            ]
                        }
                    }
                });
            }
        });

        // 2. Guru Attendance Harian & Bulanan Charts
        $.getJSON(`${baseUrl}/tv/api/absensi-guru/${hashKey}`, function (response) {
            if (response.status === 'success') {
                const days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                const thisWeekData = Object.values(response.data.mingguIni).map(d => d.Hadir).slice(0, 6);

                const ctx1 = document.getElementById('absensiGuruHarianChart').getContext('2d');
                if (charts['absensiGuruHarianChart']) charts['absensiGuruHarianChart'].destroy();
                charts['absensiGuruHarianChart'] = new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: days,
                        datasets: [{
                            label: 'Guru Hadir',
                            data: thisWeekData,
                            backgroundColor: '#28a745'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            xAxes: [{ ticks: { fontColor: colors.textColor } }],
                            yAxes: [{ ticks: { fontColor: colors.textColor } }]
                        }
                    }
                });

                // Hitung bulan dan tahun untuk 30 hari terakhir guru
                const today = new Date();
                const start = new Date();
                start.setDate(today.getDate() - 30);

                const formatMonthYear = (date1, date2) => {
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    const m1 = months[date1.getMonth()];
                    const y1 = date1.getFullYear();
                    const m2 = months[date2.getMonth()];
                    const y2 = date2.getFullYear();

                    if (y1 === y2) {
                        if (m1 === m2) {
                            return `${m1} ${y1}`;
                        } else {
                            return `${m1} - ${m2} ${y1}`;
                        }
                    } else {
                        return `${m1} ${y1} - ${m2} ${y2}`;
                    }
                };

                const monthYearText = formatMonthYear(start, today);
                $('#trendGuruBulanTahun').text(monthYearText);

                // Bulanan (Trend area chart)
                const dates = Object.keys(response.data.bulanan);
                const values = Object.values(response.data.bulanan).map(d => d.Hadir);
                const ctx2 = document.getElementById('absensiGuruBulananChart').getContext('2d');
                if (charts['absensiGuruBulananChart']) charts['absensiGuruBulananChart'].destroy();
                charts['absensiGuruBulananChart'] = new Chart(ctx2, {
                    type: 'line',
                    data: {
                        labels: dates.map(d => d.substring(8, 10)),
                        datasets: [{
                            label: 'Total Kehadiran',
                            data: values,
                            borderColor: '#28a745', // Green color
                            backgroundColor: 'rgba(40,167,69,0.1)', // Green transparency
                            fill: true,
                            tension: 0.3,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#28a745',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            xAxes: [{
                                gridLines: { color: colors.gridColor },
                                ticks: { fontColor: colors.textColor }
                            }],
                            yAxes: [{
                                gridLines: { color: colors.gridColor },
                                ticks: { fontColor: colors.textColor, beginAtZero: true, precision: 0 }
                            }]
                        }
                    }
                });
            }
        });
    }

    // ==========================================
    // 7. PHOTO GALLERY CAROUSEL (INNER LOOP)
    // ==========================================
    function fetchGaleri() {
        $.getJSON(`${baseUrl}/tv/api/galeri/${hashKey}`, function (response) {
            if (response.status === 'success') {
                galeriData = response.data;
            }
        });
    }

    function startGaleriLoop() {
        if (galeriTimer) clearInterval(galeriTimer);
        if (galeriData.length === 0) return;

        showGaleriPhoto(0);

        // Auto slide inner photos every 5 seconds
        galeriTimer = setInterval(() => {
            currentGaleriIndex = (currentGaleriIndex + 1) % galeriData.length;
            showGaleriPhoto(currentGaleriIndex);
        }, 5000);
    }

    function stopGaleriLoop() {
        if (galeriTimer) clearInterval(galeriTimer);
    }

    function showGaleriPhoto(index) {
        if (galeriData.length === 0) return;
        const photo = galeriData[index];

        $('#galeriActiveImage').attr('src', photo.FotoUrl);
        $('#galeriActiveTitle').text(photo.Judul);
        $('#galeriActiveDate').text(photo.TanggalFormatted);
        $('#galeriActiveDesc').text(photo.Keterangan || '-');
    }

    // ==========================================
    // 8. AGENDA SLIDE
    // ==========================================
    function fetchAgenda() {
        $.getJSON(`${baseUrl}/tv/api/agenda/${hashKey}`, function (response) {
            if (response.status === 'success') {
                // Populate slide agenda
                let trHtml = '';
                $.each(response.data, function (i, a) {
                    trHtml += `
                        <tr>
                            <td class="font-weight-bold text-success">${a.NamaKegiatan}</td>
                            <td><i class="far fa-calendar-alt"></i> ${a.TanggalFormatted}</td>
                            <td><i class="far fa-clock"></i> ${a.JamFormatted || '-'}</td>
                            <td><i class="fas fa-map-marker-alt text-warning"></i> ${a.Tempat || '-'}</td>
                            <td class="text-secondary text-sm">${a.Keterangan || '-'}</td>
                        </tr>
                    `;
                });
                $('#agendaTableBody').html(trHtml || '<tr><td colspan="5" class="text-center text-muted">Tidak ada agenda mendatang</td></tr>');

                // Populate Home mini list
                let homeHtml = '';
                $.each(response.data.slice(0, 3), function (i, a) {
                    const dateObj = new Date(a.TanggalMulai);
                    const day = dateObj.getDate();
                    const monthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    const month = monthsShort[dateObj.getMonth()];

                    homeHtml += `
                        <div class="home-agenda-item">
                            <div class="home-agenda-date-box">
                                <span class="had-day">${day}</span>
                                <span class="had-month">${month}</span>
                            </div>
                            <div class="home-agenda-details">
                                <h4>${a.NamaKegiatan}</h4>
                                <div style="margin-top: 5px; display: flex; flex-direction: column; gap: 3px; font-size: 13px; color: var(--text-secondary);">
                                    <span><i class="far fa-clock text-info"></i> ${a.JamFormatted || '-'}</span>
                                    <span><i class="fas fa-map-marker-alt text-warning"></i> ${a.Tempat || '-'}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                $('#homeAgendaContainer').html(homeHtml || '<div class="text-center text-muted py-4">Tidak ada agenda mendatang</div>');
            }
        });
    }

    // ==========================================
    // 9. ULANG TAHUN SLIDE
    // ==========================================

    // Helper: 2 huruf inisial nama
    function getBirthdayInitials(name) {
        if (!name) return '?';
        var parts = name.trim().split(/\s+/);
        if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }

    // Helper: warna avatar konsisten berdasarkan nama
    function getBirthdayAvatarColor(name) {
        var palette = ['#3b82f6','#10b981','#ef4444','#f59e0b','#06b6d4','#8b5cf6','#ec4899','#f97316'];
        var h = 0;
        for (var i = 0; i < name.length; i++) { h = name.charCodeAt(i) + ((h << 5) - h); }
        return palette[Math.abs(h) % palette.length];
    }

    // Helper: build avatar HTML (bisa dipakai 2 ukuran)
    function buildAvatarHtml(row, size) {
        var initials = getBirthdayInitials(row.Nama || '');
        var bgColor  = getBirthdayAvatarColor(row.Nama || '');
        var sz = size + 'px';
        var fs = Math.round(size * 0.33) + 'px';
        if (row.PhotoUrl) {
            return '<img src="' + row.PhotoUrl + '" alt="Foto" '
                + 'onerror="this.style.display=\'none\';this.nextSibling.style.display=\'flex\';" '
                + 'style="width:' + sz + ';height:' + sz + ';min-width:' + sz + ';border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.2);">'
                + '<div style="display:none;width:' + sz + ';height:' + sz + ';min-width:' + sz + ';border-radius:50%;'
                + 'background:' + bgColor + ';color:#fff;font-weight:700;font-size:' + fs + ';'
                + 'align-items:center;justify-content:center;border:2px solid rgba(255,255,255,0.2);">'
                + initials + '</div>';
        }
        return '<div style="width:' + sz + ';height:' + sz + ';min-width:' + sz + ';border-radius:50%;'
            + 'background:' + bgColor + ';color:#fff;font-weight:700;font-size:' + fs + ';'
            + 'display:flex;align-items:center;justify-content:center;'
            + 'border:2px solid rgba(255,255,255,0.2);">' + initials + '</div>';
    }

    // Render baris GURU — full width, ukuran normal
    function renderBirthdayRow(row, isSantri) {
        var nama     = $('<div>').text(row.Nama || '-').html();
        var namaKelas = isSantri ? $('<div>').text(row.NamaKelas || '-').html() : '';
        var tglUt    = $('<div>').text(row.TanggalUlangTahun || '-').html();
        var sisaHari = parseInt(row.SisaHari, 10);

        var avatarHtml = buildAvatarHtml(row, 50);

        var badgeHtml = sisaHari === 0
            ? '<span style="display:inline-flex;align-items:center;gap:4px;background:#dc3545;color:#fff;'
              + 'padding:4px 10px;border-radius:20px;font-size:12px;font-weight:700;animation:pulse-red 1.5s infinite;">'
              + '<i class="fas fa-birthday-cake"></i> HARI INI!</span>'
            : '<span style="display:inline-flex;align-items:center;background:rgba(255,255,255,0.12);'
              + 'color:#ccc;padding:4px 10px;border-radius:20px;font-size:12px;border:1px solid rgba(255,255,255,0.2);">'
              + sisaHari + ' Hari Lagi</span>';

        var tpqInfo = appData.lembaga.isFkpq ? ' &nbsp;|&nbsp; <i class="fas fa-mosque" style="color:#fd7e14;margin-right:3px;"></i>' + $('<div>').text(row.NamaTpq || '').html() : '';

        var subInfo = isSantri
            ? '<div style="color:rgba(255,255,255,0.5);font-size:11px;margin-top:2px;">'
              + '<i class="fas fa-graduation-cap" style="color:#3b82f6;margin-right:3px;"></i>' + namaKelas
              + tpqInfo
              + ' &nbsp;|&nbsp; <i class="fas fa-calendar-alt" style="color:#10b981;margin-right:3px;"></i>' + tglUt + '</div>'
            : '<div style="color:rgba(255,255,255,0.5);font-size:11px;margin-top:2px;">'
              + '<i class="fas fa-calendar-alt" style="color:#10b981;margin-right:3px;"></i>' + tglUt
              + tpqInfo + '</div>';

        return '<div style="display:flex;align-items:center;gap:12px;padding:10px 12px;margin-bottom:8px;'
            + 'border-radius:10px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">'
            + '<div style="display:flex;align-items:center;">' + avatarHtml + '</div>'
            + '<div style="flex:1;min-width:0;">'
            + '<div style="font-weight:700;color:#fff;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + nama + '</div>'
            + subInfo
            + '</div>'
            + '<div style="flex-shrink:0;">' + badgeHtml + '</div>'
            + '</div>';
    }

    // Render item SANTRI — compact, untuk grid 2 kolom
    function renderBirthdayRowCompact(row) {
        var nama     = $('<div>').text(row.Nama || '-').html();
        var namaKelas = $('<div>').text(row.NamaKelas || '-').html();
        var tglUt    = $('<div>').text(row.TanggalUlangTahun || '-').html();
        var sisaHari = parseInt(row.SisaHari, 10);

        var avatarHtml = buildAvatarHtml(row, 40);

        var badgeHtml = sisaHari === 0
            ? '<span style="display:inline-flex;align-items:center;gap:3px;background:#dc3545;color:#fff;'
              + 'padding:3px 8px;border-radius:16px;font-size:10px;font-weight:700;animation:pulse-red 1.5s infinite;">'
              + '<i class="fas fa-birthday-cake"></i> HARI INI!</span>'
            : '<span style="display:inline-flex;align-items:center;background:rgba(255,255,255,0.12);'
              + 'color:#bbb;padding:3px 8px;border-radius:16px;font-size:10px;border:1px solid rgba(255,255,255,0.18);">'
              + sisaHari + ' Hari</span>';

        return '<div style="display:flex;align-items:center;gap:8px;padding:8px 10px;'
            + 'border-radius:10px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">'
            + '<div style="display:flex;align-items:center;">' + avatarHtml + '</div>'
            + '<div style="flex:1;min-width:0;">'
            + '<div style="font-weight:700;color:#fff;font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + nama + '</div>'
            + '<div style="color:rgba(255,255,255,0.5);font-size:10px;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">'
            + '<i class="fas fa-graduation-cap" style="color:#3b82f6;margin-right:2px;"></i>' + namaKelas
            + '</div>'
            + (appData.lembaga.isFkpq ? '<div style="color:rgba(255,255,255,0.4);font-size:10px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><i class="fas fa-mosque" style="color:#fd7e14;margin-right:2px;"></i>' + $('<div>').text(row.NamaTpq || '').html() + '</div>' : '')
            + '<div style="color:rgba(255,255,255,0.45);font-size:10px;">'
            + '<i class="fas fa-calendar-alt" style="color:#10b981;margin-right:2px;"></i>' + tglUt
            + '</div>'
            + '</div>'
            + '<div style="flex-shrink:0;">' + badgeHtml + '</div>'
            + '</div>';
    }

    function fetchUlangTahun() {

        // Inject animasi pulse-red sekali saja
        if (!document.getElementById('pulseRedStyle')) {
            var s = document.createElement('style');
            s.id = 'pulseRedStyle';
            s.textContent = '@keyframes pulse-red{'
                + '0%{transform:scale(0.95);box-shadow:0 0 0 0 rgba(220,53,69,.7)}'
                + '70%{transform:scale(1);box-shadow:0 0 0 10px rgba(220,53,69,0)}'
                + '100%{transform:scale(0.95);box-shadow:0 0 0 0 rgba(220,53,69,0)}}';
            document.head.appendChild(s);
        }

        $.getJSON(baseUrl + '/tv/api/ulang-tahun/' + hashKey, function (response) {
            if (response.status !== 'success') return;

            // --- HOME WIDGET (Today/Nearest Guru & Santri - showing all with the same closest date) ---
            var homeBdayEl = document.getElementById('homeBirthdayTodayList');
            if (homeBdayEl) {
                var homeHtml = '';
                var closestGurus = [];
                var closestSantris = [];

                if (response.data.guru && response.data.guru.length > 0) {
                    var minGuruSisa = response.data.guru[0].SisaHari;
                    closestGurus = response.data.guru.filter(function(g) {
                        return g.SisaHari === minGuruSisa;
                    });
                }

                if (response.data.santri && response.data.santri.length > 0) {
                    var minSantriSisa = response.data.santri[0].SisaHari;
                    closestSantris = response.data.santri.filter(function(s) {
                        return s.SisaHari === minSantriSisa;
                    });
                }

                $.each(closestGurus, function(i, guru) {
                    var avatar = buildAvatarHtml(guru, 32);
                    var badge = guru.SisaHari === 0 
                        ? '<span class="badge badge-danger" style="font-size:10px;animation:pulse-red 1.5s infinite;padding:3px 6px;"><i class="fas fa-gift"></i> HARI INI!</span>'
                        : '<span class="badge badge-secondary" style="font-size:10px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);padding:3px 6px;">' + guru.SisaHari + ' Hari Lagi</span>';
                    
                    var labelRole = (guru.JenisKelamin && guru.JenisKelamin.toLowerCase() === 'laki-laki') ? 'Ustadz' : 'Ustadzah';
                    homeHtml += '<div style="display:flex;align-items:center;gap:10px;padding:6px 10px;background:rgba(255,255,255,0.03);border-radius:8px;border:1px solid rgba(255,255,255,0.05);">'
                        + '<div style="display:flex;align-items:center;flex-shrink:0;">' + avatar + '</div>'
                        + '<div style="flex:1;min-width:0;">'
                        + '<div style="font-weight:700;font-size:12px;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + $('<div>').text(guru.Nama).html() + '</div>'
                        + '<div style="font-size:10px;color:rgba(255,255,255,0.5);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><i class="fas fa-user-tie text-success" style="margin-right:3px;"></i>' + labelRole + (appData.lembaga.isFkpq ? ' &nbsp;|&nbsp; ' + (guru.NamaTpq || '') : '') + ' &nbsp;|&nbsp; ' + guru.TanggalUlangTahun + '</div>'
                        + '</div>'
                        + '<div style="flex-shrink:0;">' + badge + '</div>'
                        + '</div>';
                });

                $.each(closestSantris, function(i, santri) {
                    var avatar = buildAvatarHtml(santri, 32);
                    var badge = santri.SisaHari === 0 
                        ? '<span class="badge badge-danger" style="font-size:10px;animation:pulse-red 1.5s infinite;padding:3px 6px;"><i class="fas fa-gift"></i> HARI INI!</span>'
                        : '<span class="badge badge-secondary" style="font-size:10px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);padding:3px 6px;">' + santri.SisaHari + ' Hari Lagi</span>';
                    
                    homeHtml += '<div style="display:flex;align-items:center;gap:10px;padding:6px 10px;background:rgba(255,255,255,0.03);border-radius:8px;border:1px solid rgba(255,255,255,0.05);">'
                        + '<div style="display:flex;align-items:center;flex-shrink:0;">' + avatar + '</div>'
                        + '<div style="flex:1;min-width:0;">'
                        + '<div style="font-weight:700;font-size:12px;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + $('<div>').text(santri.Nama).html() + '</div>'
                        + '<div style="font-size:10px;color:rgba(255,255,255,0.5);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><i class="fas fa-user-graduate text-primary" style="margin-right:3px;"></i>' + (santri.NamaKelas || '-') + (appData.lembaga.isFkpq ? ' &nbsp;|&nbsp; ' + (santri.NamaTpq || '') : '') + ' &nbsp;|&nbsp; ' + santri.TanggalUlangTahun + '</div>'
                        + '</div>'
                        + '<div style="flex-shrink:0;">' + badge + '</div>'
                        + '</div>';
                });

                if (closestGurus.length === 0 && closestSantris.length === 0) {
                    homeHtml = '<div style="font-size:11px;color:rgba(255,255,255,0.4);font-style:italic;text-align:center;padding:10px 0;">Tidak ada data ulang tahun terdekat</div>';
                }

                homeBdayEl.innerHTML = homeHtml;
            }

            // --- GURU (1 kolom, full width) ---
            var guruEl = document.getElementById('birthdayGuruList');
            if (guruEl) {
                if (response.data.guru && response.data.guru.length > 0) {
                    var guruHtml = '';
                    $.each(response.data.guru, function (i, row) {
                        guruHtml += renderBirthdayRow(row, false);
                    });
                    guruEl.innerHTML = guruHtml;
                } else {
                    guruEl.innerHTML = '<div style="text-align:center;padding:40px 0;color:rgba(255,255,255,0.4);">'
                        + '<i class="fas fa-user-slash" style="font-size:36px;display:block;margin-bottom:10px;"></i>'
                        + 'Tidak ada ustadz/ah berulang tahun dekat ini</div>';
                }
            }

            // --- SANTRI (2 kolom, compact) ---
            var santriEl = document.getElementById('birthdaySantriList');
            if (santriEl) {
                if (response.data.santri && response.data.santri.length > 0) {
                    var itemsHtml = '';
                    $.each(response.data.santri, function (i, row) {
                        itemsHtml += renderBirthdayRowCompact(row);
                    });
                    santriEl.innerHTML = '<div class="birthday-santri-grid">'
                        + itemsHtml + '</div>';
                } else {
                    santriEl.innerHTML = '<div style="text-align:center;padding:40px 0;color:rgba(255,255,255,0.4);">'
                        + '<i class="fas fa-user-slash" style="font-size:36px;display:block;margin-bottom:10px;"></i>'
                        + 'Tidak ada santri berulang tahun dekat ini</div>';
                }
            }
        });
    }


    // ==========================================
    // 9. PRAYER TIMES COUNTDOWN & API CLIENT
    // ==========================================
    function initPrayerTimes() {
        // Order subuh, syuruq, dzuhur, ashar, maghrib, isya
        const prayerOrder = ["fajr", "shurooq", "dhuhr", "asr", "maghrib", "isha"];
        const prayerNames = {
            "fajr": "Subuh",
            "shurooq": "Syuruq",
            "dhuhr": "Dzuhur",
            "asr": "Ashar",
            "maghrib": "Maghrib",
            "isha": "Isya"
        };

        const LOCATION_SETTING_KEY = "prayerLocationSetting";
        let defaultCity = "Bintan";
        let mode = "city";
        let gpsLat = null;
        let gpsLng = null;

        try {
            const saved = localStorage.getItem(LOCATION_SETTING_KEY);
            if (saved) {
                const parsed = JSON.parse(saved);
                if (parsed.mode === "city" && parsed.city) {
                    defaultCity = parsed.city;
                } else if (parsed.mode === "gps" && parsed.lat && parsed.lng) {
                    mode = "gps";
                    gpsLat = parsed.lat;
                    gpsLng = parsed.lng;
                } else if (parsed.city) {
                    defaultCity = parsed.city;
                }
            }
        } catch (e) {}

        if (mode === "gps" && gpsLat && gpsLng) {
            $('#sholat-lokasi').text(`GPS (${parseFloat(gpsLat).toFixed(2)}, ${parseFloat(gpsLng).toFixed(2)})`);
        } else {
            $('#sholat-lokasi').text(defaultCity);
        }

        // Fetch dari Aladhan client-side API (dengan fallback backend)
        const today = new Date();
        const dd = String(today.getDate()).padStart(2, '0');
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const yyyy = today.getFullYear();
        const dateStr = `${dd}-${mm}-${yyyy}`;
        
        let url = "";
        if (mode === "gps" && gpsLat && gpsLng) {
            url = `https://api.aladhan.com/v1/timings/${dateStr}?latitude=${gpsLat}&longitude=${gpsLng}&method=20`;
        } else {
            url = `https://api.aladhan.com/v1/timingsByCity/${dateStr}?city=${encodeURIComponent(defaultCity)}&country=Indonesia&method=20`;
        }

        function applyPrayerData(times) {
            prayerTimes = {
                "fajr": times.fajr || times.Fajr,
                "shurooq": times.shurooq || times.Sunrise,
                "dhuhr": times.dhuhr || times.Dhuhr,
                "asr": times.asr || times.Asr,
                "maghrib": times.maghrib || times.Maghrib,
                "isha": times.isha || times.Isha
            };

            // Inject UI times
            $('#sholat-subuh').text(prayerTimes.fajr);
            $('#sholat-syuruq').text(prayerTimes.shurooq);
            $('#sholat-dzuhur').text(prayerTimes.dhuhr);
            $('#sholat-ashar').text(prayerTimes.asr);
            $('#sholat-maghrib').text(prayerTimes.maghrib);
            $('#sholat-isya').text(prayerTimes.isha);

            // Start countdown loop
            startPrayerCountdown(prayerOrder, prayerNames);
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data && data.code === 200 && data.data && data.data.timings) {
                    applyPrayerData(data.data.timings);
                } else {
                    throw new Error("Aladhan API response invalid");
                }
            })
            .catch(err => {
                console.warn("Gagal fetch Aladhan API client-side, mencoba fallback ke backend API...", err);
                let backendUrl = "";
                if (mode === "gps" && gpsLat && gpsLng) {
                    backendUrl = `${window.location.origin}/backend/jadwal-sholat/${gpsLat}/${gpsLng}?format=json`;
                } else {
                    backendUrl = `${window.location.origin}/backend/jadwal-sholat/${encodeURIComponent(defaultCity)}?format=json`;
                }
                fetch(backendUrl)
                    .then(res => res.json())
                    .then(bData => {
                        if (bData && bData.success && bData.prayer_times) {
                            applyPrayerData(bData.prayer_times);
                        }
                    })
                    .catch(bErr => {
                        console.error("Gagal mengambil data jadwal sholat dari backend:", bErr);
                    });
            });
    }

    function startPrayerCountdown(order, names) {
        if (nextPrayerTimer) clearInterval(nextPrayerTimer);

        nextPrayerTimer = setInterval(() => {
            const now = new Date();
            let nextIndex = -1;
            let targetTime = null;
            let isTomorrow = false;

            // Cari sholat terdekat berikutnya hari ini
            for (let i = 0; i < order.length; i++) {
                const pKey = order[i];
                const pTimeStr = prayerTimes[pKey];
                if (!pTimeStr) continue;

                const pTime = parseTime(pTimeStr);
                if (pTime > now) {
                    nextIndex = i;
                    targetTime = pTime;
                    break;
                }
            }

            // Jika semua waktu sholat hari ini sudah lewat, maka target berikutnya adalah Subuh besok
            if (nextIndex === -1) {
                const firstKey = order[0];
                const pTimeStr = prayerTimes[firstKey];
                if (pTimeStr) {
                    nextIndex = 0;
                    targetTime = parseTime(pTimeStr);
                    targetTime.setDate(targetTime.getDate() + 1); // Besok
                    isTomorrow = true;
                }
            }

            if (nextIndex !== -1 && targetTime) {
                const nextKey = order[nextIndex];
                const nextName = names[nextKey];
                const diffMs = targetTime - now;

                // Format diff to hh:mm:ss
                const diffSecs = Math.floor(diffMs / 1000);
                const hours = Math.floor(diffSecs / 3600);
                const mins = Math.floor((diffSecs % 3600) / 60);
                const secs = diffSecs % 60;

                const timeStr = (hours < 10 ? '0' : '') + hours + ':' + (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;

                // Update Slide widgets
                $('#prayerTimerClock').text(timeStr);
                $('#prayerTimerNextName').text(`${nextName} ${isTomorrow ? 'Besok' : 'Hari Ini'} jam ${prayerTimes[nextKey]}`);

                // Update Header widget badge
                $('#headerPrayerBadge').removeClass('d-none');
                $('#headerPrayerName').text(nextName);
                $('#headerPrayerTime').text(prayerTimes[nextKey]);
                $('#headerPrayerCountdown').text(`(${hours}j ${mins}m)`);

                // Highlight active prayer card box
                $('.prayer-box').removeClass('active-prayer');
                $(`.prayer-box[data-prayer="${nextKey}"]`).addClass('active-prayer');
            }
        }, 1000);
    }

    // Helper: Parse '5:24 am', '12:05 pm', or '12:09' into Date object
    function parseTime(timeStr) {
        if (!timeStr) return null;
        const trimmed = timeStr.trim().toLowerCase();
        const hasAm = trimmed.includes("am");
        const hasPm = trimmed.includes("pm");
        const timeOnly = trimmed.replace(/\s*(am|pm)\s*/gi, "");
        const parts = timeOnly.split(":");
        if (parts.length < 2) return null;

        let hours = parseInt(parts[0], 10);
        const minutes = parseInt(parts[1], 10) || 0;
        if (isNaN(hours) || isNaN(minutes)) return null;

        if (hasPm && hours < 12) {
            hours += 12;
        } else if (hasAm && hours === 12) {
            hours = 0;
        }

        const d = new Date();
        d.setHours(hours);
        d.setMinutes(minutes);
        d.setSeconds(0);
        d.setMilliseconds(0);
        return d;
    }

});
