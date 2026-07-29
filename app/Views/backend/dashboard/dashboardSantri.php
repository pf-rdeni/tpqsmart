<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-gradient-success">
                        <h3 class="card-title">
                            <i class="fas fa-user-graduate"></i>
                            <span class="d-none d-sm-inline">Dashboard Santri - </span>
                            <?= esc($NamaLogin ?? 'Santri') ?>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Welcome Message -->
                        <div class="row mb-3 mb-md-4">
                            <div class="col-12">
                                <div class="alert alert-success alert-dismissible">
                                    <div class="d-flex align-items-start">
                                        <?php
                                        // Ambil foto profil santri
                                        $photoUrl = base_url('images/no-photo.jpg');
                                        if (!empty($profilSantri['PhotoProfil'])) {
                                            $photoPath = FCPATH . 'uploads/santri/' . $profilSantri['PhotoProfil'];
                                            if (file_exists($photoPath)) {
                                                $photoUrl = base_url('uploads/santri/' . $profilSantri['PhotoProfil']);
                                            }
                                        }
                                        ?>
                                        <div class="mr-3" style="flex-shrink: 0;">
                                            <img src="<?= $photoUrl ?>" 
                                                alt="Foto Profil"
                                                style="width: 90px; height: 120px; object-fit: cover; border: 2px solid #dee2e6; border-radius: 8px;"
                                                onerror="this.src='<?= base_url('images/no-photo.jpg') ?>'">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-2 mb-md-3"><i class="icon fas fa-info-circle"></i> Bismillahirrahmanirrahim</h5>
                                            <p class="mb-0 small-text-mobile">
                                                Assalamu'alaikum, <strong><?= esc($profilSantri['NamaSantri'] ?? 'Santri') ?></strong>...!
                                                Selamat datang di aplikasi TPQ Smart.
                                                <?php if (!empty($NamaKelas)): ?>
                                                    Anda saat ini berada di kelas <strong><?= esc($NamaKelas) ?></strong>
                                                    <span class="d-none d-sm-inline">(Tahun Ajaran <?= esc($TahunAjaran ?? '') ?>)</span>.
                                                <?php endif; ?>
                                                <span class="d-none d-md-inline">Semoga Allah senantiasa memberkahi langkah kita dalam menuntut ilmu.</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ===== UJIAN MDTA AKTIF ===== -->
                        <?php if (!empty($jadwalUjian)): ?>
                        <div class="row mt-2 mb-3">
                            <div class="col-12">
                                <div class="card card-outline card-danger shadow-sm">
                                    <div class="card-header" style="background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%); border-radius: 4px 4px 0 0;">
                                        <h3 class="card-title text-white font-weight-bold">
                                            <i class="fas fa-file-alt mr-2"></i>
                                            Ujian Aktif — Segera Kerjakan!
                                        </h3>
                                        <div class="card-tools">
                                            <a href="<?= base_url('backend/ujian-mdta/santri') ?>" class="btn btn-sm btn-light text-danger font-weight-bold">
                                                <i class="fas fa-list mr-1"></i> Lihat Semua
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th class="border-0">Nama Ujian</th>
                                                        <th class="border-0 d-none d-md-table-cell">Mata Pelajaran</th>
                                                        <th class="border-0 d-none d-sm-table-cell text-center">Durasi</th>
                                                        <th class="border-0 d-none d-sm-table-cell text-center">Berakhir</th>
                                                        <th class="border-0 text-center">Status</th>
                                                        <th class="border-0 text-center">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($jadwalUjian as $j): ?>
                                                    <tr>
                                                        <td class="align-middle">
                                                            <strong><?= esc($j['NamaUjian']) ?></strong>
                                                            <?php if (($j['TipeJadwal'] ?? 'utama') === 'remedial'): ?>
                                                                <span class="badge badge-warning ml-1">Remedial</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="align-middle d-none d-md-table-cell">
                                                            <small class="text-muted"><?= esc($j['NamaMateri'] ?? '-') ?></small>
                                                        </td>
                                                        <td class="align-middle d-none d-sm-table-cell text-center">
                                                            <span class="badge badge-secondary"><?= (int)($j['DurasiMenit'] ?? 0) ?> mnt</span>
                                                        </td>
                                                        <td class="align-middle d-none d-sm-table-cell text-center">
                                                            <small class="text-danger font-weight-bold">
                                                                <?= date('d M, H:i', strtotime($j['TanggalSelesai'])) ?>
                                                            </small>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <?php
                                                            $statusSesi = $j['status_sesi'] ?? 'belum';
                                                            if ($statusSesi === 'selesai'):
                                                            ?>
                                                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Selesai</span>
                                                            <?php elseif ($statusSesi === 'berlangsung'): ?>
                                                                <span class="badge badge-warning"><i class="fas fa-clock mr-1"></i>Berlangsung</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-danger"><i class="fas fa-circle mr-1"></i>Belum Dikerjakan</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <?php if ($statusSesi === 'berlangsung' && !empty($j['token_sesi'])): ?>
                                                                <a href="<?= base_url('backend/ujian-mdta/santri/ujian/' . $j['token_sesi']) ?>"
                                                                   class="btn btn-warning btn-sm font-weight-bold">
                                                                    <i class="fas fa-play mr-1"></i> Lanjutkan
                                                                </a>
                                                            <?php elseif ($statusSesi === 'selesai'): ?>
                                                                <a href="<?= base_url('backend/ujian-mdta/santri') ?>"
                                                                   class="btn btn-outline-success btn-sm">
                                                                    <i class="fas fa-eye mr-1"></i> Lihat Hasil
                                                                </a>
                                                            <?php else: ?>
                                                                <form method="post" action="<?= base_url('backend/ujian-mdta/santri/mulai/' . $j['id']) ?>" style="display:inline;">
                                                                    <?= csrf_field() ?>
                                                                    <button type="button" class="btn btn-danger btn-sm font-weight-bold btn-mulai-ujian-dash"
                                                                            data-nama="<?= esc($j['NamaUjian']) ?>">
                                                                        <i class="fas fa-play-circle mr-1"></i> Mulai Ujian
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <!-- ===== END UJIAN MDTA AKTIF ===== -->

                        <?= prayer_schedule_widget() ?>

                        <!-- Card Status Serah Terima Rapor -->
                        <?php
                        $serahTerimaRapor = $serahTerimaRapor ?? null;
                        if ($serahTerimaRapor):
                            // Tentukan semester saat ini
                            $currentMonth = (int)date('m');
                            $semesterSaatIni = ($currentMonth >= 7) ? 'Ganjil' : 'Genap';
                            $statusSaatIni = $serahTerimaRapor[$semesterSaatIni]['status'] ?? null;
                            $hasKey = $statusSaatIni['HasKey'] ?? null;
                            $statusText = $statusSaatIni['Status'] ?? 'Belum Diserahkan';
                            
                            // Tentukan warna badge
                            $badgeClass = 'badge-warning';
                            if ($statusText === 'Sudah Diserahkan') {
                                $badgeClass = 'badge-info';
                            } elseif ($statusText === 'Sudah Dikembalikan') {
                                $badgeClass = 'badge-success';
                            }
                        ?>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card card-info card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-file-alt"></i> Status Serah Terima Rapor
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5>Semester <?= $semesterSaatIni ?></h5>
                                                <p><strong>Status:</strong> <span class="badge <?= $badgeClass ?>"><?= esc($statusText) ?></span></p>
                                                <?php if ($statusSaatIni): ?>
                                                    <?php if (!empty($statusSaatIni['TanggalTransaksi'])): ?>
                                                        <p><strong>Tanggal:</strong> <?= date('d F Y, H:i', strtotime($statusSaatIni['TanggalTransaksi'])) ?></p>
                                                    <?php endif; ?>
                                                    <?php if ($hasKey): ?>
                                                        <p>
                                                            <strong>Link Status:</strong><br>
                                                            <small class="text-muted"><?= base_url('cek-status-rapor/' . $hasKey) ?></small>
                                                            <button class="btn btn-sm btn-primary ml-2" onclick="copyToClipboard('<?= base_url('cek-status-rapor/' . $hasKey) ?>')">
                                                                <i class="fas fa-copy"></i> Copy
                                                            </button>
                                                        </p>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-6">
                                                <h5>Semester <?= ($semesterSaatIni === 'Ganjil') ? 'Genap' : 'Ganjil' ?></h5>
                                                <?php
                                                $semesterLain = ($semesterSaatIni === 'Ganjil') ? 'Genap' : 'Ganjil';
                                                $statusLain = $serahTerimaRapor[$semesterLain]['status'] ?? null;
                                                $statusTextLain = $statusLain['Status'] ?? 'Belum Diserahkan';
                                                
                                                $badgeClassLain = 'badge-warning';
                                                if ($statusTextLain === 'Sudah Diserahkan') {
                                                    $badgeClassLain = 'badge-info';
                                                } elseif ($statusTextLain === 'Sudah Dikembalikan') {
                                                    $badgeClassLain = 'badge-success';
                                                }
                                                ?>
                                                <p><strong>Status:</strong> <span class="badge <?= $badgeClassLain ?>"><?= esc($statusTextLain) ?></span></p>
                                                <?php if ($statusLain && !empty($statusLain['TanggalTransaksi'])): ?>
                                                    <p><strong>Tanggal:</strong> <?= date('d F Y, H:i', strtotime($statusLain['TanggalTransaksi'])) ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        </div>

                        <!-- Grafik Statistik Absensi -->
                        <?php
                        // Pastikan variabel hideGenap tersedia
                        $hideGenap = $hideGenap ?? false;
                        ?>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-chart-bar"></i> Statistik Absensi
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Bar Chart: Perbandingan Absensi Ganjil vs Genap -->
                                            <div class="col-md-8">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title">Perbandingan Absensi Semester Ganjil dan Genap</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <canvas id="absensiBarChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Pie Chart: Persentase Absensi Semester Ganjil -->
                                            <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title">Absensi Semester Ganjil</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <canvas id="absensiPieChartGanjil" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if (!$hideGenap): ?>
                                        <div class="row mt-3">
                                            <!-- Pie Chart: Persentase Absensi Semester Genap -->
                                            <div class="col-md-4 offset-md-4">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title">Absensi Semester Genap</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <canvas id="absensiPieChartGenap" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab untuk Nilai per Semester -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card card-primary card-outline card-tabs">
                                    <div class="card-header p-0 pt-1 border-bottom-0">
                                        <?php
                                        $hideGanjil = $hideGanjil ?? false;
                                        $hideGenap = $hideGenap ?? false;
                                        
                                        // Tentukan tab mana yang aktif pertama kali
                                        $ganjilActive = !$hideGanjil;
                                        $genapActive = !$hideGenap && $hideGanjil;
                                        
                                        // Tentukan pesan informasi berdasarkan kondisi
                                        $infoMessage = '';
                                        $isTahunAjaranSaatIni = $isTahunAjaranSaatIni ?? false;
                                        $semesterSaatIni = $semesterSaatIni ?? 'Ganjil';
                                        
                                        if ($isTahunAjaranSaatIni) {
                                            if ($hideGanjil && $hideGenap) {
                                                $infoMessage = 'Nilai semester Ganjil dan Genap untuk tahun ajaran <strong>' . esc($TahunAjaran ?? '') . '</strong> (kelas ' . esc($NamaKelas ?? '') . ') belum dapat ditampilkan karena semester <strong>' . $semesterSaatIni . '</strong> saat ini masih berlangsung. Nilai akan ditampilkan setelah semester selesai dan nilai telah diinput oleh guru.';
                                            } elseif ($hideGenap) {
                                                $infoMessage = 'Nilai semester Genap untuk tahun ajaran <strong>' . esc($TahunAjaran ?? '') . '</strong> (kelas ' . esc($NamaKelas ?? '') . ') belum dapat ditampilkan karena semester <strong>Genap</strong> saat ini masih berlangsung. Nilai akan ditampilkan setelah semester selesai dan nilai telah diinput oleh guru.';
                                            }
                                        }
                                        ?>
                                        
                                        <?php if (!empty($infoMessage)): ?>
                                            <div class="alert alert-warning alert-dismissible fade show m-3 mb-0" role="alert">
                                                <h5 class="alert-heading">
                                                    <i class="fas fa-exclamation-triangle"></i> Informasi Nilai
                                                </h5>
                                                <hr>
                                                <p class="mb-0"><?= $infoMessage ?></p>
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <ul class="nav nav-tabs" id="nilai-tabs" role="tablist">
                                            <?php if (!$hideGanjil): ?>
                                            <li class="nav-item">
                                                    <a class="nav-link <?= $ganjilActive ? 'active' : '' ?>" id="ganjil-tab" data-toggle="pill" href="#ganjil" role="tab" aria-controls="ganjil" aria-selected="<?= $ganjilActive ? 'true' : 'false' ?>">
                                                    <i class="fas fa-book"></i> Semester Ganjil
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            <?php if (!$hideGenap): ?>
                                            <li class="nav-item">
                                                    <a class="nav-link <?= $genapActive ? 'active' : '' ?>" id="genap-tab" data-toggle="pill" href="#genap" role="tab" aria-controls="genap" aria-selected="<?= $genapActive ? 'true' : 'false' ?>">
                                                    <i class="fas fa-book-open"></i> Semester Genap
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content" id="nilai-tab-content">
                                            <!-- Tab Ganjil -->
                                            <?php if (!$hideGanjil): ?>
                                            <div class="tab-pane fade <?= $ganjilActive ? 'show active' : '' ?>" id="ganjil" role="tabpanel" aria-labelledby="ganjil-tab">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h5>Nilai Semester Ganjil</h5>
                                                        <p>Rata-rata: <strong><?= number_format($rataRataGanjil ?? 0, 2) ?></strong></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h5>Absensi Semester Ganjil</h5>
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
                                                <?php if (!empty($nilaiGanjil)): ?>
                                                    <div class="table-responsive mt-3">
                                                        <table class="table table-bordered table-striped table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Materi Pelajaran</th>
                                                                    <th>Kategori</th>
                                                                    <th>Nilai</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $no = 1; foreach ($nilaiGanjil as $nilai): ?>
                                                                    <tr>
                                                                        <td><?= $no++ ?></td>
                                                                        <td><?= esc($nilai->NamaMateri ?? $nilai['NamaMateri'] ?? '-') ?></td>
                                                                        <td><?= esc($nilai->Kategori ?? $nilai['Kategori'] ?? '-') ?></td>
                                                                        <td><strong><?= esc($nilai->Nilai ?? $nilai['Nilai'] ?? '-') ?></strong></td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="alert alert-info mt-3">
                                                        <i class="fas fa-info-circle"></i> Belum ada data nilai untuk Semester Ganjil
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <?php endif; ?>

                                            <!-- Tab Genap -->
                                            <?php if (!$hideGenap): ?>
                                            <div class="tab-pane fade <?= $genapActive ? 'show active' : '' ?>" id="genap" role="tabpanel" aria-labelledby="genap-tab">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h5>Nilai Semester Genap</h5>
                                                        <p>Rata-rata: <strong><?= number_format($rataRataGenap ?? 0, 2) ?></strong></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h5>Absensi Semester Genap</h5>
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
                                                <?php if (!empty($nilaiGenap)): ?>
                                                    <div class="table-responsive mt-3">
                                                        <table class="table table-bordered table-striped table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th>No</th>
                                                                    <th>Materi Pelajaran</th>
                                                                    <th>Kategori</th>
                                                                    <th>Nilai</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $no = 1; foreach ($nilaiGenap as $nilai): ?>
                                                                    <tr>
                                                                        <td><?= $no++ ?></td>
                                                                        <td><?= esc($nilai->NamaMateri ?? $nilai['NamaMateri'] ?? '-') ?></td>
                                                                        <td><?= esc($nilai->Kategori ?? $nilai['Kategori'] ?? '-') ?></td>
                                                                        <td><strong><?= esc($nilai->Nilai ?? $nilai['Nilai'] ?? '-') ?></strong></td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="alert alert-info mt-3">
                                                        <i class="fas fa-info-circle"></i> Belum ada data nilai untuk Semester Genap
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabungan Terbaru -->
                        <?php if (!empty($tabungan['transaksi']) && count($tabungan['transaksi']) > 0): ?>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="card card-primary card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-piggy-bank"></i> Transaksi Tabungan Terbaru
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Jenis</th>
                                                            <th>Nominal</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($tabungan['transaksi'] as $trans): ?>
                                                            <tr>
                                                                <td><?= date('d/m/Y', strtotime($trans['TanggalTransaksi'])) ?></td>
                                                                <td>
                                                                    <?php if ($trans['JenisTransaksi'] === 'Setoran'): ?>
                                                                        <span class="badge badge-success"><?= esc($trans['JenisTransaksi']) ?></span>
                                                                    <?php else: ?>
                                                                        <span class="badge badge-danger"><?= esc($trans['JenisTransaksi']) ?></span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>Rp <?= number_format($trans['Nominal'], 0, ',', '.') ?></td>
                                                                <td><?= esc($trans['Keterangan'] ?? '-') ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Prestasi Terbaru -->
                        <?php if (!empty($prestasi['terbaru']) && count($prestasi['terbaru']) > 0): ?>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="card card-primary card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-trophy"></i> Prestasi Terbaru
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Tanggal</th>
                                                            <th>Jenis Prestasi</th>
                                                            <th>Materi</th>
                                                            <th>Tingkatan</th>
                                                            <th>Status</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $no = 1; foreach ($prestasi['terbaru'] as $prest): ?>
                                                            <tr>
                                                                <td><?= $no++ ?></td>
                                                                <td><?= !empty($prest['Tanggal']) ? date('d/m/Y', strtotime($prest['Tanggal'])) : '-' ?></td>
                                                                <td><strong><?= esc($prest['JenisPrestasi'] ?? '-') ?></strong></td>
                                                                <td><?= esc($prest['NamaMateri'] ?? '-') ?></td>
                                                                <td><?= esc($prest['Tingkatan'] ?? '-') ?></td>
                                                                <td>
                                                                    <?php if (!empty($prest['Status'])): ?>
                                                                        <span class="badge badge-info"><?= esc($prest['Status']) ?></span>
                                                                    <?php else: ?>
                                                                        <span class="badge badge-secondary">-</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td><?= esc($prest['Keterangan'] ?? '-') ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
function copyToClipboard(text) {
    // Create a temporary input element
    const tempInput = document.createElement('input');
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
    
    // Show feedback
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Link berhasil disalin ke clipboard',
        timer: 2000,
        showConfirmButton: false
    });
}

$(document).ready(function() {
    // Data absensi dari PHP
    var absensiGanjil = {
        hadir: <?= $absensiGanjil['hadir'] ?? 0 ?>,
        izin: <?= $absensiGanjil['izin'] ?? 0 ?>,
        sakit: <?= $absensiGanjil['sakit'] ?? 0 ?>,
        alfa: <?= $absensiGanjil['alfa'] ?? 0 ?>,
        total: <?= $absensiGanjil['total'] ?? 0 ?>
    };
    
    var absensiGenap = {
        hadir: <?= $absensiGenap['hadir'] ?? 0 ?>,
        izin: <?= $absensiGenap['izin'] ?? 0 ?>,
        sakit: <?= $absensiGenap['sakit'] ?? 0 ?>,
        alfa: <?= $absensiGenap['alfa'] ?? 0 ?>,
        total: <?= $absensiGenap['total'] ?? 0 ?>
    };
    
    // Bar Chart: Perbandingan Absensi Ganjil vs Genap
    var barChartCanvas = document.getElementById('absensiBarChart');
    if (barChartCanvas) {
        var barCtx = barChartCanvas.getContext('2d');
        var absensiBarChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alfa'],
                datasets: [
                    {
                        label: 'Semester Ganjil',
                        data: [
                            absensiGanjil.hadir,
                            absensiGanjil.izin,
                            absensiGanjil.sakit,
                            absensiGanjil.alfa
                        ],
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Semester Genap',
                        data: [
                            absensiGenap.hadir,
                            absensiGenap.izin,
                            absensiGenap.sakit,
                            absensiGenap.alfa
                        ],
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Kehadiran'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Status Kehadiran'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y;
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Pie Chart: Absensi Semester Ganjil
    var pieChartGanjilCanvas = document.getElementById('absensiPieChartGanjil');
    if (pieChartGanjilCanvas) {
        var pieGanjilCtx = pieChartGanjilCanvas.getContext('2d');
        var absensiPieChartGanjil = new Chart(pieGanjilCtx, {
            type: 'pie',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alfa'],
                datasets: [{
                    data: [
                        absensiGanjil.hadir,
                        absensiGanjil.izin,
                        absensiGanjil.sakit,
                        absensiGanjil.alfa
                    ],
                    backgroundColor: [
                        '#28a745', // Hadir - Hijau
                        '#17a2b8', // Izin - Biru
                        '#ffc107', // Sakit - Kuning
                        '#dc3545'  // Alfa - Merah
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed || 0;
                                var total = absensiGanjil.total || 1;
                                var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Pie Chart: Absensi Semester Genap
    <?php if (!$hideGenap): ?>
    var pieChartGenapCanvas = document.getElementById('absensiPieChartGenap');
    if (pieChartGenapCanvas) {
        var pieGenapCtx = pieChartGenapCanvas.getContext('2d');
        var absensiPieChartGenap = new Chart(pieGenapCtx, {
            type: 'pie',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alfa'],
                datasets: [{
                    data: [
                        absensiGenap.hadir,
                        absensiGenap.izin,
                        absensiGenap.sakit,
                        absensiGenap.alfa
                    ],
                    backgroundColor: [
                        '#28a745', // Hadir - Hijau
                        '#17a2b8', // Izin - Biru
                        '#ffc107', // Sakit - Kuning
                        '#dc3545'  // Alfa - Merah
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed || 0;
                                var total = absensiGenap.total || 1;
                                var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>

    document.querySelectorAll('.btn-mulai-ujian-dash').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const namaUjian = this.dataset.nama || 'Ujian';

            Swal.fire({
                title: '📝 Mulai Ujian Online?',
                html: `Apakah Anda yakin ingin memulai ujian <strong>${namaUjian}</strong>?<br><br><span class="text-warning small"><i class="fas fa-clock me-1"></i> Timer pengerjaan akan langsung berjalan begitu Anda mengklik Ya.</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-play me-1"></i> Ya, Mulai Sekarang!',
                cancelButtonText: 'Batal',
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
});
</script>
<?= prayer_schedule_js(base_url('backend/jadwal-sholat')) ?>
<?= prayer_schedule_settings_js(base_url('backend/jadwal-sholat')) ?>
<?= $this->endSection(); ?>
