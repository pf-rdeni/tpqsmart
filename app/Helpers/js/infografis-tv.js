/**
 * TV Digital / Digital Signage Script
 * Handles real-time clock, slideshow engine, Chart.js rendering,
 * MuslimSalat API integration, and AJAX data refreshing.
 */

$(document).ready(function() {
    
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
    $(document).keydown(function(e) {
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
        $.getJSON(`${baseUrl}/tv/api/data/${hashKey}`, function(response) {
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
                    $.each(appData.activeBlocks, function(i, block) {
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
            $.each(appData.alumniList, function(i, row) {
                totalAlumniHome += (row.Total || 0);
            });
        }
        $('#homeTotalAlumni').text(totalAlumniHome + ' Santri');

        let totalMunaqosahLulusHome = 0;
        if (appData.munaqosahGraduationStats) {
            $.each(appData.munaqosahGraduationStats, function(i, row) {
                totalMunaqosahLulusHome += (row.Lulus || 0);
            });
        }
        $('#homeMunaqosahLulus').text(totalMunaqosahLulusHome + ' Santri');
        
        // Today attendance calculations
        const totalTodayAbsen = stats.absensiSantriToday.Hadir + stats.absensiSantriToday.Izin + stats.absensiSantriToday.Sakit + stats.absensiSantriToday.Alfa;
        if (totalTodayAbsen > 0) {
            const pct = Math.round((stats.absensiSantriToday.Hadir / totalTodayAbsen) * 100);
            $('#homeKehadiranPersen').text(pct + '%');
        } else {
            $('#homeKehadiranPersen').text('0%');
        }

        // Tampilkan ringkasan kehadiran pekan ini di subtext
        if (appData.ringkasanKehadiranMingguIni) {
            const rkm = appData.ringkasanKehadiranMingguIni;
            $('#homeKehadiranRatio').html(`Pkn Ini - H: <strong>${rkm.Hadir}</strong> | I: <strong>${rkm.Izin}</strong> | S: <strong>${rkm.Sakit}</strong> | A: <strong>${rkm.Alfa}</strong>`);
        } else {
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
            $.each(appData.santriPerKelas, function(i, row) {
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
            $.each(appData.statistikPerTpq, function(i, row) {
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
            let trHtml = '';
            $.each(appData.statistikKehadiranKelas, function(i, row) {
                trHtml += `
                    <tr>
                        <td class="font-weight-bold">${row.NamaKelas}</td>
                        <td class="text-center text-success">${row.Hadir}</td>
                        <td class="text-center text-warning">${row.Izin}</td>
                        <td class="text-center text-info">${row.Sakit}</td>
                        <td class="text-center text-danger">${row.Alfa}</td>
                    </tr>
                `;
            });
            $('#kehadiranKelasTableBody').html(trHtml);
        }

        // Munaqosah Graduation Trend Table
        if (appData.munaqosahGraduationStats) {
            let trHtml = '';
            $.each(appData.munaqosahGraduationStats, function(i, row) {
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
            $.each(appData.alumniList, function(i, row) {
                let taLabel = row.TahunAjaran;
                if (taLabel && taLabel.length === 8) {
                    taLabel = taLabel.substring(0, 4) + '/' + taLabel.substring(4);
                }
                let countL = 0;
                let countP = 0;
                if (row.Santri) {
                    $.each(row.Santri, function(j, s) {
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
        $.each(activeSlides, function(i, key) {
            dotsHtml += `<span class="tv-dot" data-index="${i}"></span>`;
        });
        $('#tvSlideDots').html(dotsHtml);
        
        // Dot clicks to manually navigate
        $('.tv-dot').click(function() {
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
            $.each(appData.santriPerKelas, function(i, r) {
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
            $.each(appData.statistikKehadiranKelas, function(i, r) {
                labels.push(r.NamaKelas);
                dataSakit.push(r.Sakit);
                dataIzin.push(r.Izin);
                dataAlfa.push(r.Alfa);
            });
            createAbsensiPerbandinganChart('kehadiranKelasPerbandinganChart', labels, dataSakit, dataIzin, dataAlfa);
        }

        // 6. TREN KELULUSAN MUNAQOSAH: Line chart of graduation percentage over years
        if (key === 'trend_kelulusan' && appData.munaqosahGraduationStats) {
            const labels = [];
            const dataPct = [];
            $.each(appData.munaqosahGraduationStats, function(i, r) {
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
            $.each(reversedList, function(i, r) {
                let taLabel = r.TahunAjaran;
                if (taLabel && taLabel.length === 8) {
                    taLabel = taLabel.substring(0, 4) + '/' + taLabel.substring(4);
                }
                labels.push(taLabel);
                dataTotal.push(r.Total);
                
                let countL = 0;
                let countP = 0;
                if (r.Santri) {
                    $.each(r.Santri, function(j, s) {
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
                            callback: function(value) {
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
                            callback: function(value) {
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

    function createAbsensiPerbandinganChart(canvasId, labels, dataSakit, dataIzin, dataAlfa) {
        if (charts[canvasId]) charts[canvasId].destroy();
        
        const colors = getChartThemeColors();
        const ctx = document.getElementById(canvasId).getContext('2d');
        charts[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Sakit',
                        data: dataSakit,
                        backgroundColor: '#17a2b8' // var(--color-info)
                    },
                    {
                        label: 'Izin',
                        data: dataIzin,
                        backgroundColor: '#ffc107' // var(--color-warning)
                    },
                    {
                        label: 'Alfa',
                        data: dataAlfa,
                        backgroundColor: '#dc3545' // red
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
                            callback: function(value) {
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
                            callback: function(value) { return value + '%'; }
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
        $.getJSON(`${baseUrl}/tv/api/absensi-santri/${hashKey}`, function(response) {
            if (response.status === 'success') {
                const dates = Object.keys(response.data.bulanan);
                const values = Object.values(response.data.bulanan).map(d => d.Hadir);
                
                const ctx = document.getElementById(canvasId).getContext('2d');
                charts[canvasId] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: dates.map(d => d.substring(8, 10) + '/' + d.substring(5, 7)),
                        datasets: [{
                            label: 'Santri Hadir',
                            data: values,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0,123,255,0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            xAxes: [{ gridLines: { display: false }, ticks: { fontColor: colors.textColor } }],
                            yAxes: [{ gridLines: { color: colors.gridColor }, ticks: { fontColor: colors.textColor } }]
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });
    }

    function fetchAbsensiCharts() {
        if (appData.lembaga.isFkpq) return; // Skip for FKPQ

        const colors = getChartThemeColors();

        // 1. Santri Attendance Harian & Mingguan Charts
        $.getJSON(`${baseUrl}/tv/api/absensi-santri/${hashKey}`, function(response) {
            if (response.status === 'success') {
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
                    response.data.kehadiranPerKelas.datasets.forEach(function(dataset, index) {
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
                            dataset.data.forEach(function(val, dayIdx) {
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
                    response.data.kehadiranPerKelas.datasets.forEach(function(dataset) {
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
        $.getJSON(`${baseUrl}/tv/api/absensi-guru/${hashKey}`, function(response) {
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
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220,53,69,0.1)',
                            fill: true,
                            tension: 0.3
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
            }
        });
    }

    // ==========================================
    // 7. PHOTO GALLERY CAROUSEL (INNER LOOP)
    // ==========================================
    function fetchGaleri() {
        $.getJSON(`${baseUrl}/tv/api/galeri/${hashKey}`, function(response) {
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
        $.getJSON(`${baseUrl}/tv/api/agenda/${hashKey}`, function(response) {
            if (response.status === 'success') {
                // Populate slide agenda
                let trHtml = '';
                $.each(response.data, function(i, a) {
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
                $.each(response.data.slice(0, 3), function(i, a) {
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

        const defaultCity = "Bintan";
        $('#sholat-lokasi').text(defaultCity);

        // Fetch dari MuslimSalat client-side API
        // Gunakan JSONP untuk menghindari CORS
        const url = `https://muslimsalat.com/${defaultCity}.json?key=free&jsoncallback=?`;
        
        $.getJSON(url, function(data) {
            if (data && data.items && data.items.length > 0) {
                const item = data.items[0];
                prayerTimes = {
                    "fajr": item.fajr,
                    "shurooq": item.shurooq,
                    "dhuhr": item.dhuhr,
                    "asr": item.asr,
                    "maghrib": item.maghrib,
                    "isha": item.isha
                };

                // Inject UI times
                $('#sholat-subuh').text(item.fajr);
                $('#sholat-syuruq').text(item.shurooq);
                $('#sholat-dzuhur').text(item.dhuhr);
                $('#sholat-ashar').text(item.asr);
                $('#sholat-maghrib').text(item.maghrib);
                $('#sholat-isya').text(item.isha);

                // Start countdown loop
                startPrayerCountdown(prayerOrder, prayerNames);
            }
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

    // Helper: Parse '5:24 am' or '12:05 pm' into Date object
    function parseTime(timeStr) {
        const parts = timeStr.match(/(\d+)(?::(\d\d))?\s*(p?)/i);
        if (!parts) return null;
        
        let hours = parseInt(parts[1], 10);
        const minutes = parseInt(parts[2], 10) || 0;
        const isPm = !!parts[3];
        
        if (isPm && hours < 12) {
            hours += 12;
        } else if (!isPm && hours === 12) {
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
