<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-check"></i> Detail Absensi - <?= esc($santri['NamaSantri'] ?? 'Santri') ?>
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Statistik Absensi -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Semester Ganjil</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Hadir:</td>
                                            <td><strong><?= $absensiGanjil['hadir'] ?? 0 ?></strong> (<?= $absensiGanjil['persenHadir'] ?? 0 ?>%)</td>
                                        </tr>
                                        <tr>
                                            <td>Izin:</td>
                                            <td><strong><?= $absensiGanjil['izin'] ?? 0 ?></strong> (<?= $absensiGanjil['persenIzin'] ?? 0 ?>%)</td>
                                        </tr>
                                        <tr>
                                            <td>Sakit:</td>
                                            <td><strong><?= $absensiGanjil['sakit'] ?? 0 ?></strong> (<?= $absensiGanjil['persenSakit'] ?? 0 ?>%)</td>
                                        </tr>
                                        <tr>
                                            <td>Alfa:</td>
                                            <td><strong><?= $absensiGanjil['alfa'] ?? 0 ?></strong> (<?= $absensiGanjil['persenAlfa'] ?? 0 ?>%)</td>
                                        </tr>
                                        <tr>
                                            <td>Total:</td>
                                            <td><strong><?= $absensiGanjil['total'] ?? 0 ?></strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Semester Genap</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Hadir:</td>
                                            <td><strong><?= $absensiGenap['hadir'] ?? 0 ?></strong> (<?= $absensiGenap['persenHadir'] ?? 0 ?>%)</td>
                                        </tr>
                                        <tr>
                                            <td>Izin:</td>
                                            <td><strong><?= $absensiGenap['izin'] ?? 0 ?></strong> (<?= $absensiGenap['persenIzin'] ?? 0 ?>%)</td>
                                        </tr>
                                        <tr>
                                            <td>Sakit:</td>
                                            <td><strong><?= $absensiGenap['sakit'] ?? 0 ?></strong> (<?= $absensiGenap['persenSakit'] ?? 0 ?>%)</td>
                                        </tr>
                                        <tr>
                                            <td>Alfa:</td>
                                            <td><strong><?= $absensiGenap['alfa'] ?? 0 ?></strong> (<?= $absensiGenap['persenAlfa'] ?? 0 ?>%)</td>
                                        </tr>
                                        <tr>
                                            <td>Total:</td>
                                            <td><strong><?= $absensiGenap['total'] ?? 0 ?></strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grafik Absensi -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-chart-bar"></i> Grafik Absensi Per Semester
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="absensiChart" style="min-height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($absensiPerBulan)): ?>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-chart-line"></i> Grafik Absensi Per Bulan (2 Bulan Terakhir)
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="absensiBulanChart" style="min-height: 300px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Tabel History Absensi -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-history"></i> History Absensi
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($historyAbsensi)): ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> Belum ada data absensi.
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table id="historyAbsensiTable" class="table table-bordered table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Tanggal</th>
                                                        <th>Kelas</th>
                                                        <th>Semester</th>
                                                        <th>Tahun Ajaran</th>
                                                        <th>Guru</th>
                                                        <th>Kehadiran</th>
                                                        <th>Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $no = 1; foreach ($historyAbsensi as $absensi): ?>
                                                        <tr>
                                                            <td><?= $no++ ?></td>
                                                            <td>
                                                                <?php
                                                                if (!empty($absensi['Tanggal'])) {
                                                                    $tanggal = date('d-m-Y', strtotime($absensi['Tanggal']));
                                                                    echo $tanggal;
                                                                } else {
                                                                    echo '-';
                                                                }
                                                                ?>
                                                            </td>
                                                            <td><?= !empty($absensi['NamaKelas']) ? esc($absensi['NamaKelas']) : '-' ?></td>
                                                            <td>
                                                                <?php
                                                                $semester = $absensi['Semester'] ?? '-';
                                                                if ($semester === 'Ganjil') {
                                                                    echo '<span class="badge badge-primary">' . esc($semester) . '</span>';
                                                                } elseif ($semester === 'Genap') {
                                                                    echo '<span class="badge badge-info">' . esc($semester) . '</span>';
                                                                } else {
                                                                    echo esc($semester);
                                                                }
                                                                ?>
                                                            </td>
                                                            <td><?= !empty($absensi['TahunAjaran']) ? esc($absensi['TahunAjaran']) : '-' ?></td>
                                                            <td><?= !empty($absensi['NamaGuruFormatted']) ? esc($absensi['NamaGuruFormatted']) : '-' ?></td>
                                                            <td>
                                                                <?php
                                                                $kehadiran = $absensi['Kehadiran'] ?? '-';
                                                                $badgeClass = '';
                                                                switch ($kehadiran) {
                                                                    case 'Hadir':
                                                                        $badgeClass = 'badge-success';
                                                                        break;
                                                                    case 'Izin':
                                                                        $badgeClass = 'badge-info';
                                                                        break;
                                                                    case 'Sakit':
                                                                        $badgeClass = 'badge-warning';
                                                                        break;
                                                                    case 'Alfa':
                                                                        $badgeClass = 'badge-danger';
                                                                        break;
                                                                    default:
                                                                        $badgeClass = 'badge-secondary';
                                                                }
                                                                ?>
                                                                <span class="badge <?= $badgeClass ?>"><?= esc($kehadiran) ?></span>
                                                            </td>
                                                            <td><?= !empty($absensi['Keterangan']) ? esc($absensi['Keterangan']) : '-' ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Grafik Absensi Per Semester
    var ctx = document.getElementById('absensiChart');
    if (ctx) {
        var absensiChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alfa'],
                datasets: [{
                    label: 'Semester Ganjil',
                    data: [
                        <?= $absensiGanjil['hadir'] ?? 0 ?>,
                        <?= $absensiGanjil['izin'] ?? 0 ?>,
                        <?= $absensiGanjil['sakit'] ?? 0 ?>,
                        <?= $absensiGanjil['alfa'] ?? 0 ?>
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }, {
                    label: 'Semester Genap',
                    data: [
                        <?= $absensiGenap['hadir'] ?? 0 ?>,
                        <?= $absensiGenap['izin'] ?? 0 ?>,
                        <?= $absensiGenap['sakit'] ?? 0 ?>,
                        <?= $absensiGenap['alfa'] ?? 0 ?>
                    ],
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    <?php if (!empty($absensiPerBulan)): ?>
    // Grafik Absensi Per Bulan
    var ctxBulan = document.getElementById('absensiBulanChart');
    if (ctxBulan) {
        var bulanLabels = <?= json_encode(array_keys($absensiPerBulan)) ?>;
        var absensiBulanChart = new Chart(ctxBulan, {
            type: 'line',
            data: {
                labels: bulanLabels,
                datasets: [{
                    label: 'Hadir',
                    data: <?= json_encode(array_column($absensiPerBulan, 'Hadir')) ?>,
                    borderColor: 'rgba(40, 167, 69, 1)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.1
                }, {
                    label: 'Izin',
                    data: <?= json_encode(array_column($absensiPerBulan, 'Izin')) ?>,
                    borderColor: 'rgba(23, 162, 184, 1)',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    tension: 0.1
                }, {
                    label: 'Sakit',
                    data: <?= json_encode(array_column($absensiPerBulan, 'Sakit')) ?>,
                    borderColor: 'rgba(255, 193, 7, 1)',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.1
                }, {
                    label: 'Alfa',
                    data: <?= json_encode(array_column($absensiPerBulan, 'Alfa')) ?>,
                    borderColor: 'rgba(220, 53, 69, 1)',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    <?php endif; ?>

    <?php if (!empty($historyAbsensi)): ?>
    // Inisialisasi DataTables untuk History Absensi
    $(document).ready(function() {
        $('#historyAbsensiTable').DataTable({
            'responsive': true,
            'lengthChange': true,
            'autoWidth': false,
            'pageLength': 25,
            'order': [[1, 'desc']], // Urutkan berdasarkan tanggal (kolom ke-2) descending
            'language': {
                'url': '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            'columnDefs': [
                {
                    'targets': [0], // Kolom No
                    'orderable': false,
                    'width': '4%'
                },
                {
                    'targets': [1], // Kolom Tanggal
                    'width': '10%'
                },
                {
                    'targets': [2], // Kolom Kelas
                    'width': '10%'
                },
                {
                    'targets': [3], // Kolom Semester
                    'width': '8%'
                },
                {
                    'targets': [4], // Kolom Tahun Ajaran
                    'width': '10%'
                },
                {
                    'targets': [5], // Kolom Guru
                    'width': '15%'
                },
                {
                    'targets': [6], // Kolom Kehadiran
                    'width': '10%'
                },
                {
                    'targets': [7], // Kolom Keterangan
                    'width': '33%'
                }
            ]
        });
    });
    <?php endif; ?>
</script>
<?= $this->endSection(); ?>

