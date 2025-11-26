<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">
                <i class="fas fa-chart-bar"></i> Statistik Kehadiran
            </h3>
            <div class="card-tools">
                <a href="<?= base_url('backend/absensi') ?>" class="btn btn-light btn-sm">
                    <i class="fas fa-clipboard-check"></i> <span class="d-none d-sm-inline">Absensi</span>
                </a>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <?php
            // Tampilkan flash message jika ada
            if (session()->getFlashdata('success')) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                echo '<i class="fas fa-check-circle"></i> ' . session()->getFlashdata('success');
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                echo '<span aria-hidden="true">&times;</span>';
                echo '</button>';
                echo '</div>';
            }

            // Cek apakah ada kelas
            if (empty($kelas_list_all)) {
                echo "<div class='alert alert-info'><i class='fas fa-info-circle'></i> Tidak ada kelas yang tersedia.</div>";
            } else {
            ?>
                <div class="card card-primary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs flex-wrap justify-content-start justify-content-md-between" id="statistik-tabs" role="tablist">
                            <?php
                            $firstTab = true;
                            foreach ($kelas_list_all as $index => $kelas): ?>
                                <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                                    <a class="nav-link border-white text-center <?= $firstTab ? 'active' : '' ?>"
                                        id="statistik-tab-<?= $kelas['IdKelas'] ?>"
                                        data-toggle="pill"
                                        href="#statistik-content-<?= $kelas['IdKelas'] ?>"
                                        role="tab"
                                        aria-controls="statistik-content-<?= $kelas['IdKelas'] ?>"
                                        aria-selected="<?= $firstTab ? 'true' : 'false' ?>">
                                        <?= htmlspecialchars($kelas['NamaKelas']) ?>
                                    </a>
                                </li>
                            <?php
                                $firstTab = false;
                            endforeach; ?>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="statistik-tabContent">
                            <?php
                            $firstTab = true;
                            foreach ($kelas_list_all as $kelas):
                            ?>
                                <div class="tab-pane fade <?= $firstTab ? 'show active' : '' ?>"
                                    id="statistik-content-<?= $kelas['IdKelas'] ?>"
                                    role="tabpanel"
                                    aria-labelledby="statistik-tab-<?= $kelas['IdKelas'] ?>">

                                    <!-- Periode Minggu -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="periode-minggu-<?= $kelas['IdKelas'] ?>">
                                                    <i class="fas fa-calendar-week"></i> Pilih Periode Minggu:
                                                </label>
                                                <select id="periode-minggu-<?= $kelas['IdKelas'] ?>"
                                                    class="form-control periode-minggu-select select2"
                                                    data-kelas-id="<?= $kelas['IdKelas'] ?>"
                                                    style="width: 100%;">
                                                    <?php
                                                    // Generate opsi week berdasarkan kalender pendidikan per semester
                                                    // Parse tahun ajaran dari format number (20252026)
                                                    $tahunAwal = (int)substr($IdTahunAjaran, 0, 4);
                                                    $tahunAkhir = (int)substr($IdTahunAjaran, 4, 4);

                                                    // Semester Ganjil: Juli-Desember tahun awal
                                                    // Semester Genap: Januari-Juni tahun akhir

                                                    $defaultStart = date('Y-m-d', strtotime($start_of_week));
                                                    $allWeeks = [];

                                                    // Fungsi untuk generate week dalam periode tertentu
                                                    $generateWeeksForPeriod = function ($startDate, $endDate, $semesterLabel, $defaultStart) use (&$allWeeks) {
                                                        $start = new DateTime($startDate);
                                                        $end = new DateTime($endDate);

                                                        // Cari Senin pertama dalam periode
                                                        $current = clone $start;
                                                        $dayOfWeek = (int)$current->format('w'); // 0 = Minggu, 1 = Senin
                                                        $mondayOffset = ($dayOfWeek == 0) ? -6 : (1 - $dayOfWeek);
                                                        if ($mondayOffset != 0) {
                                                            $current->modify($mondayOffset . ' days');
                                                        }

                                                        // Jika Senin pertama sebelum start date, maju ke Senin berikutnya
                                                        if ($current < $start) {
                                                            $current->modify('+7 days');
                                                        }

                                                        $weekNum = 1;
                                                        while ($current <= $end) {
                                                            $weekStart = clone $current;
                                                            $weekEnd = clone $current;
                                                            $weekEnd->modify('+6 days');

                                                            // Hanya tambahkan jika weekStart dalam periode dan weekEnd tidak melebihi periode
                                                            if ($weekStart >= $start && $weekEnd <= $end) {
                                                                $weekStartStr = $weekStart->format('Y-m-d');
                                                                $weekEndStr = $weekEnd->format('Y-m-d');
                                                                $weekValue = $weekStartStr . '|' . $weekEndStr;
                                                                $weekLabel = $weekStart->format('d M Y') . ' - ' . $weekEnd->format('d M Y');
                                                                $selected = ($weekStartStr == $defaultStart) ? 'selected' : '';

                                                                $allWeeks[] = [
                                                                    'value' => $weekValue,
                                                                    'label' => $semesterLabel . ' - Minggu ' . $weekNum . ': ' . $weekLabel,
                                                                    'selected' => $selected,
                                                                    'date' => $weekStartStr
                                                                ];
                                                            }

                                                            $current->modify('+7 days');
                                                            $weekNum++;
                                                        }
                                                    };

                                                    // Generate week untuk Semester Ganjil (Juli-Desember tahun awal)
                                                    $ganjilStart = $tahunAwal . '-07-01';
                                                    $ganjilEnd = $tahunAwal . '-12-31';
                                                    $generateWeeksForPeriod($ganjilStart, $ganjilEnd, 'Semester Ganjil', $defaultStart);

                                                    // Generate week untuk Semester Genap (Januari-Juni tahun akhir)
                                                    $genapStart = $tahunAkhir . '-01-01';
                                                    $genapEnd = $tahunAkhir . '-06-30';
                                                    $generateWeeksForPeriod($genapStart, $genapEnd, 'Semester Genap', $defaultStart);

                                                    // Sort berdasarkan tanggal
                                                    usort($allWeeks, function ($a, $b) {
                                                        return strcmp($a['date'], $b['date']);
                                                    });

                                                    // Tampilkan semua week
                                                    foreach ($allWeeks as $week) {
                                                        echo '<option value="' . htmlspecialchars($week['value']) . '" ' . $week['selected'] . '>' . htmlspecialchars($week['label']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Periode:</label>
                                                <div class="form-control" id="periode-display-<?= $kelas['IdKelas'] ?>" style="background-color: #f8f9fa;">
                                                    <?= date('d-m-Y', strtotime($start_of_week)) ?> s/d <?= date('d-m-Y', strtotime($end_of_week)) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tabel Jumlah Absensi -->
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">
                                                        <i class="fas fa-table"></i> Tabel Jumlah Absensi
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped" id="tabel-absensi-<?= $kelas['IdKelas'] ?>">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>Status</th>
                                                                    <th class="text-center">Jumlah</th>
                                                                    <th class="text-center">Persentase</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td><i class="fas fa-check-circle text-success"></i> Hadir</td>
                                                                    <td class="text-center" id="count-hadir-<?= $kelas['IdKelas'] ?>">0</td>
                                                                    <td class="text-center" id="persen-hadir-<?= $kelas['IdKelas'] ?>">0%</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><i class="fas fa-info-circle text-info"></i> Izin</td>
                                                                    <td class="text-center" id="count-izin-<?= $kelas['IdKelas'] ?>">0</td>
                                                                    <td class="text-center" id="persen-izin-<?= $kelas['IdKelas'] ?>">0%</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><i class="fas fa-heartbeat text-warning"></i> Sakit</td>
                                                                    <td class="text-center" id="count-sakit-<?= $kelas['IdKelas'] ?>">0</td>
                                                                    <td class="text-center" id="persen-sakit-<?= $kelas['IdKelas'] ?>">0%</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><i class="fas fa-times-circle text-danger"></i> Alfa</td>
                                                                    <td class="text-center" id="count-alfa-<?= $kelas['IdKelas'] ?>">0</td>
                                                                    <td class="text-center" id="persen-alfa-<?= $kelas['IdKelas'] ?>">0%</td>
                                                                </tr>
                                                                <tr class="table-primary">
                                                                    <td><strong>Total</strong></td>
                                                                    <td class="text-center" id="count-total-<?= $kelas['IdKelas'] ?>"><strong>0</strong></td>
                                                                    <td class="text-center"><strong>100%</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Grafik -->
                                    <div class="row">
                                        <!-- Grafik Pie Perbandingan Kehadiran -->
                                        <div class="col-md-6 mb-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">
                                                        <i class="fas fa-chart-pie"></i> Perbandingan Kehadiran
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="pieChart-<?= $kelas['IdKelas'] ?>" style="max-height: 300px;"></canvas>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Grafik Batang Kehadiran Per Hari -->
                                        <div class="col-md-6 mb-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">
                                                        <i class="fas fa-chart-bar"></i> Kehadiran Per Hari (Minggu)
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="barChart-<?= $kelas['IdKelas'] ?>" style="max-height: 300px;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Grafik Perbandingan Semester -->
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">
                                                        <i class="fas fa-chart-line"></i> Perbandingan Kehadiran Per Semester (Ganjil vs Genap)
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="semesterChart-<?= $kelas['IdKelas'] ?>" style="max-height: 300px;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Grafik Pie Perbandingan Semester -->
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">
                                                        <i class="fas fa-chart-pie"></i> Perbandingan Kehadiran Per Semester (Ganjil vs Genap)
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <h6 class="text-center mb-3">Semester Ganjil</h6>
                                                            <canvas id="semesterPieChartGanjil-<?= $kelas['IdKelas'] ?>" style="max-height: 300px;"></canvas>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <h6 class="text-center mb-3">Semester Genap</h6>
                                                            <canvas id="semesterPieChartGenap-<?= $kelas['IdKelas'] ?>" style="max-height: 300px;"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tabel List Santri dengan Statistik Kehadiran -->
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title mb-0">
                                                        <a data-toggle="collapse" href="#collapse-list-santri-<?= $kelas['IdKelas'] ?>" role="button" aria-expanded="false" aria-controls="collapse-list-santri-<?= $kelas['IdKelas'] ?>" style="color: inherit; text-decoration: none;">
                                                            <i class="fas fa-table"></i> List Santri dengan Statistik Kehadiran
                                                            <i class="fas fa-chevron-down float-right"></i>
                                                        </a>
                                                    </h5>
                                                </div>
                                                <div class="collapse" id="collapse-list-santri-<?= $kelas['IdKelas'] ?>">
                                                    <div class="card-body">
                                                        <!-- Filter Semester -->
                                                        <div class="row mb-3">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="filter-semester-<?= $kelas['IdKelas'] ?>">
                                                                        <i class="fas fa-filter"></i> Pilih Semester:
                                                                    </label>
                                                                    <select id="filter-semester-<?= $kelas['IdKelas'] ?>"
                                                                        class="form-control filter-semester-select select2"
                                                                        data-kelas-id="<?= $kelas['IdKelas'] ?>"
                                                                        style="width: 100%;">
                                                                        <option value="Ganjil">Semester Ganjil</option>
                                                                        <option value="Genap">Semester Genap</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Tabel -->
                                                        <div class="table-responsive">
                                                            <table id="tabel-santri-statistik-<?= $kelas['IdKelas'] ?>" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                                                <thead class="thead-light">
                                                                    <tr>
                                                                        <th>No</th>
                                                                        <th>Nama Santri</th>
                                                                        <th>Kelas</th>
                                                                        <th>Semester</th>
                                                                        <th>Tahun Ajaran</th>
                                                                        <th class="text-center">Hadir</th>
                                                                        <th class="text-center">Izin</th>
                                                                        <th class="text-center">Sakit</th>
                                                                        <th class="text-center">Alfa</th>
                                                                        <th class="text-center">Total</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td colspan="10" class="text-center">
                                                                            <i class="fas fa-spinner fa-spin"></i> Memuat data...
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                $firstTab = false;
                            endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link {
        border-radius: 0.25rem 0.25rem 0 0;
    }

    .nav-tabs .nav-link.active {
        background-color: #007bff;
        color: white;
    }

    canvas {
        max-width: 100%;
    }
</style>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    // Variabel global untuk menyimpan chart instances
    var pieCharts = {};
    var barCharts = {};
    var semesterCharts = {};
    var semesterPieChartsGanjil = {};
    var semesterPieChartsGenap = {};
    var semesterPieChartsGanjil = {};
    var semesterPieChartsGenap = {};

    // Function untuk mendapatkan tanggal dari value select (format: YYYY-MM-DD|YYYY-MM-DD)
    function getWeekDatesFromSelect(selectValue) {
        if (!selectValue) {
            // Default: minggu ini (Senin - Minggu)
            var today = new Date();
            var dayOfWeek = today.getDay(); // 0 = Minggu, 1 = Senin, dst
            var mondayOffset = (dayOfWeek == 0) ? -6 : (1 - dayOfWeek);
            var startDate = new Date(today);
            startDate.setDate(today.getDate() + mondayOffset);
            var endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + 6);

            return {
                start: startDate.toISOString().split('T')[0],
                end: endDate.toISOString().split('T')[0]
            };
        }

        // Format: YYYY-MM-DD|YYYY-MM-DD
        var parts = selectValue.split('|');
        if (parts.length !== 2) {
            console.error('[STATISTIK] Invalid select value format:', selectValue);
            return null;
        }

        return {
            start: parts[0],
            end: parts[1]
        };
    }

    // Function untuk memuat data statistik
    function loadStatistikData(idKelas, startDate, endDate) {
        console.log('[STATISTIK] Loading data for kelas:', idKelas, 'Period:', startDate, 'to', endDate);

        // Tampilkan loading - jangan ganti seluruh tbody, hanya update nilai
        var tabel = $('#tabel-absensi-' + idKelas);
        if (tabel.length === 0) {
            console.error('[STATISTIK] Tabel not found for kelas:', idKelas);
            return;
        }

        // Tampilkan loading indicator di semua cell
        $('#count-hadir-' + idKelas).html('<i class="fas fa-spinner fa-spin"></i>');
        $('#count-izin-' + idKelas).html('<i class="fas fa-spinner fa-spin"></i>');
        $('#count-sakit-' + idKelas).html('<i class="fas fa-spinner fa-spin"></i>');
        $('#count-alfa-' + idKelas).html('<i class="fas fa-spinner fa-spin"></i>');
        $('#count-total-' + idKelas).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '<?= base_url("backend/absensi/getStatistikData") ?>',
            type: 'GET',
            data: {
                IdKelas: idKelas,
                startDate: startDate,
                endDate: endDate
            },
            dataType: 'json',
            success: function(response) {
                console.log('[STATISTIK] AJAX Success - Full response:', response);

                if (response && response.success) {
                    console.log('[STATISTIK] Data received:', response);
                    updateStatistikDisplay(idKelas, response);
                } else {
                    console.error('[STATISTIK] Error response:', response);

                    // Update tabel dengan nilai 0
                    updateStatistikDisplay(idKelas, {
                        kehadiran: {
                            hadir: 0,
                            izin: 0,
                            sakit: 0,
                            alfa: 0
                        }
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: (response && response.message) ? response.message : 'Gagal memuat data statistik'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('[STATISTIK] AJAX Error Details:');
                console.error('[STATISTIK] Status:', status);
                console.error('[STATISTIK] Error:', error);
                console.error('[STATISTIK] XHR:', xhr);
                console.error('[STATISTIK] Response Text:', xhr.responseText);
                console.error('[STATISTIK] Status Code:', xhr.status);

                // Update tabel dengan nilai 0
                updateStatistikDisplay(idKelas, {
                    kehadiran: {
                        hadir: 0,
                        izin: 0,
                        sakit: 0,
                        alfa: 0
                    }
                });

                var errorMessage = 'Terjadi kesalahan saat memuat data statistik.';
                if (xhr.status === 404) {
                    errorMessage += ' Endpoint tidak ditemukan. Periksa route backend/absensi/getStatistikData';
                } else if (xhr.status === 500) {
                    errorMessage += ' Server error. Periksa log server.';
                } else if (xhr.responseText) {
                    try {
                        var errorResponse = JSON.parse(xhr.responseText);
                        errorMessage += ' ' + (errorResponse.message || '');
                    } catch (e) {
                        errorMessage += ' ' + xhr.responseText.substring(0, 100);
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    footer: 'Periksa console browser untuk detail error'
                });
            }
        });
    }

    // Function untuk update tampilan statistik
    function updateStatistikDisplay(idKelas, data) {
        console.log('[STATISTIK] updateStatistikDisplay called for kelas:', idKelas);
        console.log('[STATISTIK] Data received:', data);

        // Pastikan data.kehadiran ada
        if (!data || !data.kehadiran) {
            console.error('[STATISTIK] Invalid data structure:', data);
            // Reset tabel ke kondisi default
            var tbody = $('#tabel-absensi-' + idKelas + ' tbody');
            tbody.html(
                '<tr><td><i class="fas fa-check-circle text-success"></i> Hadir</td><td class="text-center">0</td><td class="text-center">0%</td></tr>' +
                '<tr><td><i class="fas fa-info-circle text-info"></i> Izin</td><td class="text-center">0</td><td class="text-center">0%</td></tr>' +
                '<tr><td><i class="fas fa-heartbeat text-warning"></i> Sakit</td><td class="text-center">0</td><td class="text-center">0%</td></tr>' +
                '<tr><td><i class="fas fa-times-circle text-danger"></i> Alfa</td><td class="text-center">0</td><td class="text-center">0%</td></tr>' +
                '<tr class="table-primary"><td><strong>Total</strong></td><td class="text-center"><strong>0</strong></td><td class="text-center"><strong>100%</strong></td></tr>'
            );
            return;
        }

        var kehadiran = data.kehadiran || {};
        var hadir = parseInt(kehadiran.hadir || 0);
        var izin = parseInt(kehadiran.izin || 0);
        var sakit = parseInt(kehadiran.sakit || 0);
        var alfa = parseInt(kehadiran.alfa || 0);
        var total = hadir + izin + sakit + alfa;

        console.log('[STATISTIK] Kehadiran values - Hadir:', hadir, 'Izin:', izin, 'Sakit:', sakit, 'Alfa:', alfa, 'Total:', total);

        // Update tabel - pastikan elemen ada
        var countHadir = $('#count-hadir-' + idKelas);
        var countIzin = $('#count-izin-' + idKelas);
        var countSakit = $('#count-sakit-' + idKelas);
        var countAlfa = $('#count-alfa-' + idKelas);
        var countTotal = $('#count-total-' + idKelas);

        if (countHadir.length > 0) {
            countHadir.text(hadir);
        } else {
            console.error('[STATISTIK] Element #count-hadir-' + idKelas + ' not found');
        }

        if (countIzin.length > 0) {
            countIzin.text(izin);
        } else {
            console.error('[STATISTIK] Element #count-izin-' + idKelas + ' not found');
        }

        if (countSakit.length > 0) {
            countSakit.text(sakit);
        } else {
            console.error('[STATISTIK] Element #count-sakit-' + idKelas + ' not found');
        }

        if (countAlfa.length > 0) {
            countAlfa.text(alfa);
        } else {
            console.error('[STATISTIK] Element #count-alfa-' + idKelas + ' not found');
        }

        if (countTotal.length > 0) {
            countTotal.text(total);
        } else {
            console.error('[STATISTIK] Element #count-total-' + idKelas + ' not found');
        }

        // Hitung persentase
        var persenHadir = total > 0 ? ((hadir / total) * 100).toFixed(1) : 0;
        var persenIzin = total > 0 ? ((izin / total) * 100).toFixed(1) : 0;
        var persenSakit = total > 0 ? ((sakit / total) * 100).toFixed(1) : 0;
        var persenAlfa = total > 0 ? ((alfa / total) * 100).toFixed(1) : 0;

        $('#persen-hadir-' + idKelas).text(persenHadir + '%');
        $('#persen-izin-' + idKelas).text(persenIzin + '%');
        $('#persen-sakit-' + idKelas).text(persenSakit + '%');
        $('#persen-alfa-' + idKelas).text(persenAlfa + '%');

        console.log('[STATISTIK] Tabel updated successfully');

        // Update grafik pie
        updatePieChart(idKelas, kehadiran);

        // Update grafik batang
        updateBarChart(idKelas, data.hari_labels, data.hari_data);
    }

    // Function untuk update grafik pie
    function updatePieChart(idKelas, kehadiran) {
        var canvasId = 'pieChart-' + idKelas;
        var ctx = document.getElementById(canvasId);

        if (!ctx) {
            console.error('[STATISTIK] Canvas not found:', canvasId);
            return;
        }

        // Hancurkan chart lama jika ada
        if (pieCharts[idKelas]) {
            try {
                pieCharts[idKelas].destroy();
            } catch (e) {
                console.warn('[STATISTIK] Error destroying pie chart:', e);
            }
            pieCharts[idKelas] = null;
        }

        var chartInstance = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alfa'],
                datasets: [{
                    data: [kehadiran.hadir, kehadiran.izin, kehadiran.sakit, kehadiran.alfa],
                    backgroundColor: [
                        '#28a745', // Hadir - Hijau
                        '#17a2b8', // Izin - Biru
                        '#ffc107', // Sakit - Kuning
                        '#dc3545' // Alfa - Merah
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            generateLabels: function(chart) {
                                var data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map(function(label, i) {
                                        var meta = chart.getDatasetMeta(0);
                                        var style = meta.controller.getStyle(i);
                                        var value = data.datasets[0].data[i];

                                        return {
                                            text: label,
                                            fillStyle: style.backgroundColor,
                                            strokeStyle: style.borderColor,
                                            lineWidth: style.borderWidth,
                                            hidden: isNaN(value) || meta.data[i].hidden,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed || 0;
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                animation: {
                    onComplete: function(animation) {
                        try {
                            // Gunakan chartInstance yang sudah dibuat, bukan this.chart
                            if (!chartInstance || !chartInstance.ctx) {
                                console.warn('[STATISTIK] Chart instance not available in onComplete');
                                return;
                            }

                            var chartCtx = chartInstance.ctx;
                            var chartData = chartInstance.data;
                            var meta = chartInstance.getDatasetMeta(0);
                            var total = chartData.datasets[0].data.reduce((a, b) => a + b, 0);

                            if (total === 0) return;

                            meta.data.forEach(function(element, index) {
                                var value = chartData.datasets[0].data[index];
                                if (value === 0) return;

                                try {
                                    var percentage = ((value / total) * 100).toFixed(1);
                                    var position = element.tooltipPosition();

                                    if (!position) return;

                                    chartCtx.save();
                                    chartCtx.fillStyle = '#fff';
                                    chartCtx.font = 'bold 13px Arial';
                                    chartCtx.textAlign = 'center';
                                    chartCtx.textBaseline = 'middle';
                                    chartCtx.shadowColor = 'rgba(0, 0, 0, 0.5)';
                                    chartCtx.shadowBlur = 3;
                                    chartCtx.shadowOffsetX = 1;
                                    chartCtx.shadowOffsetY = 1;
                                    chartCtx.fillText(percentage + '%', position.x, position.y);
                                    chartCtx.restore();
                                } catch (e) {
                                    console.warn('[STATISTIK] Error drawing percentage label:', e);
                                }
                            });
                        } catch (e) {
                            console.warn('[STATISTIK] Error in onComplete callback:', e);
                        }
                    }
                }
            }
        });

        pieCharts[idKelas] = chartInstance;
    }

    // Function untuk update grafik batang
    function updateBarChart(idKelas, hariLabels, hariData) {
        var canvasId = 'barChart-' + idKelas;
        var ctx = document.getElementById(canvasId);

        if (!ctx) {
            console.error('[STATISTIK] Canvas not found:', canvasId);
            return;
        }

        // Hancurkan chart lama jika ada
        if (barCharts[idKelas]) {
            barCharts[idKelas].destroy();
        }

        // Siapkan data untuk grafik
        var hadirData = [];
        var izinData = [];
        var sakitData = [];
        var alfaData = [];

        // hariData sekarang adalah array, bukan object
        hariLabels.forEach(function(label, index) {
            if (hariData[index]) {
                hadirData.push(hariData[index].hadir || 0);
                izinData.push(hariData[index].izin || 0);
                sakitData.push(hariData[index].sakit || 0);
                alfaData.push(hariData[index].alfa || 0);
            } else {
                hadirData.push(0);
                izinData.push(0);
                sakitData.push(0);
                alfaData.push(0);
            }
        });

        barCharts[idKelas] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: hariLabels,
                datasets: [{
                        label: 'Hadir',
                        data: hadirData,
                        backgroundColor: '#28a745',
                        borderColor: '#28a745',
                        borderWidth: 1
                    },
                    {
                        label: 'Izin',
                        data: izinData,
                        backgroundColor: '#17a2b8',
                        borderColor: '#17a2b8',
                        borderWidth: 1
                    },
                    {
                        label: 'Sakit',
                        data: sakitData,
                        backgroundColor: '#ffc107',
                        borderColor: '#ffc107',
                        borderWidth: 1
                    },
                    {
                        label: 'Alfa',
                        data: alfaData,
                        backgroundColor: '#dc3545',
                        borderColor: '#dc3545',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
    }

    // Function untuk memuat data statistik per semester
    function loadStatistikPerSemester(idKelas) {
        console.log('[STATISTIK] Loading semester data for kelas:', idKelas);

        $.ajax({
            url: '<?= base_url("backend/absensi/getStatistikPerSemester") ?>',
            type: 'GET',
            data: {
                IdKelas: idKelas
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    console.log('[STATISTIK] Semester data received:', response);
                    updateSemesterChart(idKelas, response);
                    updateSemesterPieCharts(idKelas, response);
                } else {
                    console.error('[STATISTIK] Error loading semester data:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('[STATISTIK] AJAX Error loading semester data:', error);
            }
        });
    }

    // Function untuk update grafik perbandingan semester
    function updateSemesterChart(idKelas, data) {
        var canvasId = 'semesterChart-' + idKelas;
        var ctx = document.getElementById(canvasId);

        if (!ctx) {
            console.error('[STATISTIK] Canvas not found:', canvasId);
            return;
        }

        // Hancurkan chart lama jika ada
        if (semesterCharts[idKelas]) {
            semesterCharts[idKelas].destroy();
        }

        var ganjil = data.ganjil || {};
        var genap = data.genap || {};

        semesterCharts[idKelas] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alfa'],
                datasets: [{
                        label: 'Semester Ganjil',
                        data: [
                            parseInt(ganjil.hadir || 0),
                            parseInt(ganjil.izin || 0),
                            parseInt(ganjil.sakit || 0),
                            parseInt(ganjil.alfa || 0)
                        ],
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Semester Genap',
                        data: [
                            parseInt(genap.hadir || 0),
                            parseInt(genap.izin || 0),
                            parseInt(genap.sakit || 0),
                            parseInt(genap.alfa || 0)
                        ],
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';
                                var value = context.parsed.y || 0;
                                return label + ': ' + value;
                            }
                        }
                    }
                }
            }
        });
    }

    // Function untuk update grafik pie perbandingan semester
    function updateSemesterPieCharts(idKelas, data) {
        var ganjil = data.ganjil || {};
        var genap = data.genap || {};

        // Pie Chart untuk Semester Ganjil
        var canvasGanjilId = 'semesterPieChartGanjil-' + idKelas;
        var ctxGanjil = document.getElementById(canvasGanjilId);

        if (ctxGanjil) {
            // Hancurkan chart lama jika ada
            if (semesterPieChartsGanjil[idKelas]) {
                try {
                    semesterPieChartsGanjil[idKelas].destroy();
                } catch (e) {
                    console.warn('[STATISTIK] Error destroying semester pie chart ganjil:', e);
                }
                semesterPieChartsGanjil[idKelas] = null;
            }

            var chartInstanceGanjil = new Chart(ctxGanjil, {
                type: 'pie',
                data: {
                    labels: ['Hadir', 'Izin', 'Sakit', 'Alfa'],
                    datasets: [{
                        data: [
                            parseInt(ganjil.hadir || 0),
                            parseInt(ganjil.izin || 0),
                            parseInt(ganjil.sakit || 0),
                            parseInt(ganjil.alfa || 0)
                        ],
                        backgroundColor: [
                            '#28a745', // Hadir - Hijau
                            '#17a2b8', // Izin - Biru
                            '#ffc107', // Sakit - Kuning
                            '#dc3545' // Alfa - Merah
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = context.parsed || 0;
                                    var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });

            semesterPieChartsGanjil[idKelas] = chartInstanceGanjil;
        }

        // Pie Chart untuk Semester Genap
        var canvasGenapId = 'semesterPieChartGenap-' + idKelas;
        var ctxGenap = document.getElementById(canvasGenapId);

        if (ctxGenap) {
            // Hancurkan chart lama jika ada
            if (semesterPieChartsGenap[idKelas]) {
                try {
                    semesterPieChartsGenap[idKelas].destroy();
                } catch (e) {
                    console.warn('[STATISTIK] Error destroying semester pie chart genap:', e);
                }
                semesterPieChartsGenap[idKelas] = null;
            }

            var chartInstanceGenap = new Chart(ctxGenap, {
                type: 'pie',
                data: {
                    labels: ['Hadir', 'Izin', 'Sakit', 'Alfa'],
                    datasets: [{
                        data: [
                            parseInt(genap.hadir || 0),
                            parseInt(genap.izin || 0),
                            parseInt(genap.sakit || 0),
                            parseInt(genap.alfa || 0)
                        ],
                        backgroundColor: [
                            '#28a745', // Hadir - Hijau
                            '#17a2b8', // Izin - Biru
                            '#ffc107', // Sakit - Kuning
                            '#dc3545' // Alfa - Merah
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = context.parsed || 0;
                                    var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });

            semesterPieChartsGenap[idKelas] = chartInstanceGenap;
        }
    }

    // Initialize
    $(document).ready(function() {
        console.log('[STATISTIK] Page loaded');

        // Initialize Select2 untuk dropdown periode minggu
        $('.periode-minggu-select').each(function() {
            $(this).select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Periode Minggu',
                allowClear: false,
                width: '100%'
            });
        });

        // Handle perubahan periode minggu
        $(document).on('change', '.periode-minggu-select', function() {
            var idKelas = $(this).data('kelas-id');
            var selectValue = $(this).val();
            var dates = getWeekDatesFromSelect(selectValue);

            if (!dates) {
                console.error('[STATISTIK] Invalid dates for kelas:', idKelas);
                return;
            }

            console.log('[STATISTIK] Week changed for kelas:', idKelas, 'Dates:', dates);

            // Update display periode
            var startFormatted = new Date(dates.start + 'T00:00:00').toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            var endFormatted = new Date(dates.end + 'T00:00:00').toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            $('#periode-display-' + idKelas).text(startFormatted + ' s/d ' + endFormatted);

            // Load data statistik
            loadStatistikData(idKelas, dates.start, dates.end);
        });

        // Load data untuk tab aktif saat pertama kali
        $('.tab-pane.active').each(function() {
            var idKelas = $(this).attr('id').replace('statistik-content-', '');
            var selectValue = $('#periode-minggu-' + idKelas).val();
            var dates = getWeekDatesFromSelect(selectValue);
            if (dates) {
                loadStatistikData(idKelas, dates.start, dates.end);
            }
            // Load data statistik per semester
            loadStatistikPerSemester(idKelas);
            // Load data list santri (default: Ganjil)
            var semester = $('#filter-semester-' + idKelas).val() || 'Ganjil';
            if (semester) {
                loadListSantriStatistik(idKelas, semester);
            }
        });

        // Handle tab change
        $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).attr('href');
            var idKelas = target.replace('#statistik-content-', '');
            var selectValue = $('#periode-minggu-' + idKelas).val();
            var dates = getWeekDatesFromSelect(selectValue);
            if (dates) {
                loadStatistikData(idKelas, dates.start, dates.end);
            }
            // Load data statistik per semester
            loadStatistikPerSemester(idKelas);
            // Load data list santri
            var semester = $('#filter-semester-' + idKelas).val();
            loadListSantriStatistik(idKelas, semester);
        });

        // Initialize Select2 untuk filter semester
        $('.filter-semester-select').each(function() {
            $(this).select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Semester',
                allowClear: false,
                width: '100%'
            });
        });

        // Handle perubahan filter semester
        $(document).on('change', '.filter-semester-select', function() {
            var idKelas = $(this).data('kelas-id');
            var semester = $(this).val();
            loadListSantriStatistik(idKelas, semester);
        });

        // Handle collapse untuk card List Santri
        $('[data-toggle="collapse"]').on('click', function() {
            var target = $(this).attr('href');
            var icon = $(this).find('.fa-chevron-down, .fa-chevron-up');

            $(target).on('shown.bs.collapse', function() {
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            });

            $(target).on('hidden.bs.collapse', function() {
                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            });
        });
    });

    // Function untuk memuat data list santri dengan statistik
    function loadListSantriStatistik(idKelas, semester) {
        console.log('[STATISTIK] Loading list santri for kelas:', idKelas, 'Semester:', semester);

        var tbody = $('#tabel-santri-statistik-' + idKelas + ' tbody');
        tbody.html('<tr><td colspan="10" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');

        $.ajax({
            url: '<?= base_url("backend/absensi/getListSantriStatistik") ?>',
            type: 'GET',
            data: {
                IdKelas: idKelas,
                semester: semester
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    console.log('[STATISTIK] List santri data received:', response.data);
                    updateTabelSantriStatistik(idKelas, response.data);
                } else {
                    console.error('[STATISTIK] Error loading list santri:', response.message);
                    tbody.html('<tr><td colspan="10" class="text-center text-danger">Gagal memuat data: ' + (response.message || 'Unknown error') + '</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('[STATISTIK] AJAX Error loading list santri:');
                console.error('[STATISTIK] Status:', status);
                console.error('[STATISTIK] Error:', error);
                console.error('[STATISTIK] XHR:', xhr);
                console.error('[STATISTIK] Response Text:', xhr.responseText);
                console.error('[STATISTIK] Status Code:', xhr.status);

                var errorMessage = 'Terjadi kesalahan saat memuat data.';
                if (xhr.status === 404) {
                    errorMessage += ' Endpoint tidak ditemukan.';
                } else if (xhr.status === 500) {
                    errorMessage += ' Server error.';
                } else if (xhr.responseText) {
                    try {
                        var errorResponse = JSON.parse(xhr.responseText);
                        errorMessage += ' ' + (errorResponse.message || '');
                    } catch (e) {
                        errorMessage += ' ' + xhr.responseText.substring(0, 100);
                    }
                }

                tbody.html('<tr><td colspan="10" class="text-center text-danger">' + errorMessage + '</td></tr>');
            }
        });
    }

    // Function untuk update tabel list santri
    function updateTabelSantriStatistik(idKelas, data) {
        var tbody = $('#tabel-santri-statistik-' + idKelas + ' tbody');

        if (!data || data.length === 0) {
            tbody.html('<tr><td colspan="10" class="text-center">Tidak ada data</td></tr>');
            return;
        }

        var html = '';
        data.forEach(function(santri, index) {
            var total = parseInt(santri.TotalAbsensi || 0);
            var hadir = parseInt(santri.Hadir || 0);
            var izin = parseInt(santri.Izin || 0);
            var sakit = parseInt(santri.Sakit || 0);
            var alfa = parseInt(santri.Alfa || 0);

            html += '<tr>';
            html += '<td>' + (index + 1) + '</td>';
            html += '<td>' + (santri.NamaSantri || '-') + '</td>';
            html += '<td>' + (santri.NamaKelas || '-') + '</td>';
            html += '<td>' + (santri.Semester || '-') + '</td>';
            html += '<td>' + (santri.TahunAjaran || '-') + '</td>';
            html += '<td class="text-center">' + hadir + ' <small class="text-muted">(' + (santri.PersenHadir || 0) + '%)</small></td>';
            html += '<td class="text-center">' + izin + ' <small class="text-muted">(' + (santri.PersenIzin || 0) + '%)</small></td>';
            html += '<td class="text-center">' + sakit + ' <small class="text-muted">(' + (santri.PersenSakit || 0) + '%)</small></td>';
            html += '<td class="text-center">' + alfa + ' <small class="text-muted">(' + (santri.PersenAlfa || 0) + '%)</small></td>';
            html += '<td class="text-center"><strong>' + total + '</strong></td>';
            html += '</tr>';
        });

        tbody.html(html);
    }
</script>
<?= $this->endSection(); ?>