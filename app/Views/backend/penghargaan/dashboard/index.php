<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle mr-1"></i> <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <?php
    $isSuperAdmin = function_exists('in_groups') && in_groups('Admin');
    $currentRoleLabel = $isSuperAdmin
        ? 'Admin Pusat'
        : (!empty($roleCtx['isOperatorOrAdmin'])
            ? 'Operator / Kepala TPQ'
            : (!empty($roleCtx['isWaliKelas']) ? 'Guru (Wali Kelas)' : 'Guru (Read-Only)'));
    ?>

    <!-- Pusat Panduan, Alur & Matriks Peran (Default Collapsed, Tab Interaktif) -->
    <div class="card card-outline card-info collapsed-card shadow-sm mb-3">
        <div class="card-header bg-white py-2 d-flex align-items-center justify-content-between" data-card-widget="collapse" style="cursor: pointer;">
            <h3 class="card-title font-weight-bold text-sm mb-0">
                <i class="fas fa-book-open text-info mr-2"></i>Pusat Panduan, Alur Kerja &amp; Matriks Peran
                <span class="badge badge-info ml-2">Peran Anda: <?= esc($currentRoleLabel) ?></span>
            </h3>
            <div class="card-tools ml-auto">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Buka/Tutup Panduan">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-2 text-sm" style="display: none;">
            <ul class="nav nav-tabs mb-3" id="guideTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="tab-peran-link" data-toggle="tab" href="#tab-peran" role="tab">
                        <i class="fas fa-users-cog mr-1"></i> 1. Peran &amp; Hak Akses
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="tab-alur-link" data-toggle="tab" href="#tab-alur" role="tab">
                        <i class="fas fa-route mr-1"></i> 2. Alur Penggunaan (Step-by-Step)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="tab-aturan-link" data-toggle="tab" href="#tab-aturan" role="tab">
                        <i class="fas fa-shield-alt mr-1"></i> 3. Aturan Penting Sistem
                    </a>
                </li>
            </ul>

            <div class="tab-content px-2 pb-2" id="guideTabsContent">
                <!-- TAB 1: MATRIKS PERAN -->
                <div class="tab-pane fade show active" id="tab-peran" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-2">
                            <div class="card h-100 <?= $isSuperAdmin ? 'border-primary shadow-sm' : 'border shadow-none' ?> mb-0">
                                <div class="card-header py-2 <?= $isSuperAdmin ? 'bg-primary text-white' : 'bg-light' ?>">
                                    <strong><i class="fas fa-crown mr-1"></i> Admin (IdTpq = 0)</strong>
                                    <?php if ($isSuperAdmin): ?><span class="badge badge-light text-primary float-right">Anda</span><?php endif; ?>
                                </div>
                                <div class="card-body p-2 text-xs">
                                    <ul class="pl-3 mb-0">
                                        <li>Membuat &amp; mengedit template starter/umum.</li>
                                        <li>Mengatur izin duplikasi (<code>AllowDuplicate</code>) untuk seluruh TPQ.</li>
                                        <li>Mengelola master jenis penghargaan sistem.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 mb-2">
                            <?php $isOpCard = !$isSuperAdmin && !empty($roleCtx['isOperatorOrAdmin']); ?>
                            <div class="card h-100 <?= $isOpCard ? 'border-primary shadow-sm' : 'border shadow-none' ?> mb-0">
                                <div class="card-header py-2 <?= $isOpCard ? 'bg-primary text-white' : 'bg-light' ?>">
                                    <strong><i class="fas fa-user-tie mr-1"></i> Operator / Kepala TPQ</strong>
                                    <?php if ($isOpCard): ?><span class="badge badge-light text-primary float-right">Anda</span><?php endif; ?>
                                </div>
                                <div class="card-body p-2 text-xs">
                                    <ul class="pl-3 mb-0">
                                        <li>Atur Setting Global, Jenjang, Kriteria, TTD &amp; Template TPQ sendiri.</li>
                                        <li>Generate semua kelas &amp; Juara Lembaga (per jenjang).</li>
                                        <li><strong>Release Final</strong>, cabut, cetak massal PDF/ZIP &amp; Laporan Audit.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 mb-2">
                            <?php $isGuruCard = !empty($roleCtx['isGuruOnly']); ?>
                            <div class="card h-100 <?= $isGuruCard ? 'border-primary shadow-sm' : 'border shadow-none' ?> mb-0">
                                <div class="card-header py-2 <?= $isGuruCard ? 'bg-primary text-white' : 'bg-light' ?>">
                                    <strong><i class="fas fa-chalkboard-teacher mr-1"></i> Guru &amp; Wali Kelas</strong>
                                    <?php if ($isGuruCard): ?><span class="badge badge-light text-primary float-right">Anda</span><?php endif; ?>
                                </div>
                                <div class="card-body p-2 text-xs">
                                    <ul class="pl-3 mb-0">
                                        <li><strong>Read-only</strong> pada pengaturan, template &amp; release.</li>
                                        <li>Melihat &amp; cetak PDF hanya untuk kelas yang diampu.</li>
                                        <li>Khusus <strong>Wali Kelas</strong>: boleh Generate penghargaan kelas walinya sendiri.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3 mb-2">
                            <div class="card h-100 border shadow-none mb-0">
                                <div class="card-header py-2 bg-light">
                                    <strong><i class="fas fa-user-graduate mr-1"></i> Santri (Portal)</strong>
                                </div>
                                <div class="card-body p-2 text-xs">
                                    <ul class="pl-3 mb-0">
                                        <li>Login ke menu <em>Penghargaan Saya</em>.</li>
                                        <li>Hanya melihat sertifikat miliknya yang berstatus <strong>Final</strong>.</li>
                                        <li>Dapat melakukan <strong>Preview</strong> &amp; <strong>Unduh PDF</strong> kapan saja.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: ALUR PENGGUNAAN -->
                <div class="tab-pane fade" id="tab-alur" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <h6 class="font-weight-bold text-primary mb-2">
                                <i class="fas fa-tools mr-1"></i> Fase Persiapan (Admin / Operator)
                            </h6>
                            <ol class="pl-3 mb-0 text-xs">
                                <li class="mb-1">
                                    <a href="<?= base_url('backend/penghargaan/setting') ?>"><strong>Setting Global</strong></a> — Atur bobot nilai/hadir (default 70:30), threshold hadir, format nomor seri, dan wilayah penetapan.
                                </li>
                                <li class="mb-1">
                                    <a href="<?= base_url('backend/penghargaan/jenjang') ?>"><strong>Jenjang</strong></a> &amp; <a href="<?= base_url('backend/penghargaan/kriteria') ?>"><strong>Kriteria</strong></a> — Kelompokkan kelas per jenjang (untuk Juara Lembaga) &amp; atur batas minimal per jenis.
                                </li>
                                <li class="mb-1">
                                    <a href="<?= base_url('backend/penghargaan/ttd-config') ?>"><strong>Setting TTD</strong></a> — Pilih 1–3 penandatangan (Wali Kelas, Kepala TPQ, Yayasan/Pengurus) dengan mode QR Code, Kosong, atau Upload PNG.
                                </li>
                                <li>
                                    <a href="<?= base_url('backend/penghargaan/template') ?>"><strong>Template Desain</strong></a> — Duplikat template Admin atau upload background sendiri, atur posisi field di Editor, lalu klik <strong>Pilih</strong>.
                                </li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-success mb-2">
                                <i class="fas fa-award mr-1"></i> Fase Penerbitan (Operator / Wali Kelas)
                            </h6>
                            <ol class="pl-3 mb-0 text-xs" start="5">
                                <li class="mb-1">
                                    <a href="<?= base_url('backend/penghargaan/generate') ?>"><strong>Generate Penghargaan</strong></a> — Pilih TA, Semester, Momen, Jenis, &amp; Scope &rarr; Hitung Kandidat &rarr; Pilih <strong>Tanggal Sertifikat</strong> &rarr; Simpan Draft.
                                </li>
                                <li class="mb-1">
                                    <a href="<?= base_url('backend/penghargaan/sertifikat') ?>"><strong>Review &amp; Cetak PDF</strong></a> — Buka Detail batch untuk mengecek/mengubah tanggal atau template, lalu cetak uji coba (PDF/ZIP).
                                </li>
                                <li class="mb-1">
                                    <strong>Release Final</strong> — Operator/Kepala TPQ klik <strong>Release</strong> agar watermark DRAFT hilang, QR validasi aktif, dan sertifikat tampil di <strong>Portal Santri</strong>.
                                </li>
                                <li>
                                    <a href="<?= base_url('backend/penghargaan/laporan') ?>"><strong>Laporan &amp; Audit</strong></a> — Lihat statistik penerima terbanyak, audit trail penerbitan, serta export Excel (CSV) atau PDF.
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: ATURAN PENTING -->
                <div class="tab-pane fade" id="tab-aturan" role="tabpanel">
                    <div class="row text-xs">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <div class="p-2 border rounded bg-light h-100">
                                <strong class="text-primary d-block mb-1"><i class="fas fa-palette mr-1"></i> Aturan Template</strong>
                                Setiap kelompok (<em>Semua Jenis</em> maupun <em>Khusus Jenis</em>) hanya punya <strong>1 template berstatus DIPILIH</strong>. Saat cetak, template khusus jenis otomatis diprioritaskan di atas template umum. Operator hanya melihat template miliknya + template Admin yang diizinkan duplikasi.
                            </div>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <div class="p-2 border rounded bg-light h-100">
                                <strong class="text-success d-block mb-1"><i class="fas fa-calendar-alt mr-1"></i> Tanggal &amp; Wilayah</strong>
                                Tanggal sertifikat ditetapkan manual saat generate/detail (bukan tanggal print) sehingga konsisten saat diunduh ulang oleh santri. Wilayah otomatis mengambil Kelurahan/Desa (Title Case) atau custom di <em>Setting Global</em>.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2 border rounded bg-light h-100">
                                <strong class="text-info d-block mb-1"><i class="fas fa-database mr-1"></i> Validitas Data &amp; QR</strong>
                                Seluruh perhitungan nilai &amp; absensi hanya menghitung <strong>santri aktif (<code>Status = 1</code>)</strong> pada <code>tbl_kelas_santri</code> sesuai Tahun Ajaran. QR Code di sertifikat terverifikasi publik lewat <code>/signature/verifikasi/{token}</code>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Periode -->
    <div class="card card-outline card-primary shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="get" action="<?= base_url('backend/penghargaan/dashboard') ?>" class="form-inline flex-wrap">
                <label class="mr-2 font-weight-bold text-sm"><i class="fas fa-filter mr-1"></i> Filter:</label>
                <select name="tahun_ajaran" class="form-control form-control-sm mr-2 mb-1">
                    <?php if (!empty($tahunAjaranList)): ?>
                        <?php foreach ($tahunAjaranList as $ta): ?>
                            <option value="<?= esc($ta['IdTahunAjaran']) ?>" <?= $idTahunAjaran == $ta['IdTahunAjaran'] ? 'selected' : '' ?>>
                                TA <?= esc($ta['IdTahunAjaran']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="<?= esc($idTahunAjaran) ?>">TA <?= esc($idTahunAjaran) ?></option>
                    <?php endif; ?>
                </select>

                <select name="semester" class="form-control form-control-sm mr-2 mb-1">
                    <option value="">Semua Semester</option>
                    <option value="Ganjil" <?= $semester === 'Ganjil' ? 'selected' : '' ?>>Semester Ganjil</option>
                    <option value="Genap" <?= $semester === 'Genap' ? 'selected' : '' ?>>Semester Genap</option>
                    <option value="Tahunan" <?= $semester === 'Tahunan' ? 'selected' : '' ?>>Kenaikan / Tahunan</option>
                </select>

                <select name="jenis" class="form-control form-control-sm mr-2 mb-1">
                    <option value="">Semua Jenis Penghargaan</option>
                    <?php foreach ($listJenis as $j): ?>
                        <option value="<?= $j['Id'] ?>" <?= (string) $idJenis === (string) $j['Id'] ? 'selected' : '' ?>>
                            [<?= esc($j['Kode']) ?>] <?= esc($j['NamaPenghargaan']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn btn-primary btn-sm mb-1 mr-1">
                    <i class="fas fa-search mr-1"></i> Terapkan
                </button>
                <a href="<?= base_url('backend/penghargaan/dashboard') ?>" class="btn btn-default btn-sm mb-1">Reset</a>
            </form>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <a href="<?= base_url('backend/penghargaan/jenis') ?>" class="text-dark">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-tags"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Jenis Aktif</span>
                        <span class="info-box-number"><?= esc($stats['totalJenis']) ?></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <a href="<?= base_url('backend/penghargaan/sertifikat') ?>" class="text-dark">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-certificate"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Batch Sertifikat</span>
                        <span class="info-box-number"><?= esc($stats['totalSertifikat']) ?> <small class="text-muted">(<?= esc($stats['totalFinal']) ?> Final)</small></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-user-graduate text-white"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Penerima</span>
                    <span class="info-box-number"><?= esc($stats['totalPenerima']) ?></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <a href="<?= base_url('backend/penghargaan/template') ?>" class="text-dark">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-palette"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Template Aktif</span>
                        <span class="info-box-number"><?= esc($stats['totalTemplate']) ?></span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Tabel Sertifikat Terbaru -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-award text-warning mr-2"></i>Penerbitan Sertifikat Terbaru
                    </h3>
                    <?php if (!empty($roleCtx['isOperatorOrAdmin']) || !empty($roleCtx['isWaliKelas'])): ?>
                        <a href="<?= base_url('backend/penghargaan/generate') ?>" class="btn btn-success btn-sm ml-auto">
                            <i class="fas fa-magic mr-1"></i> Generate Penghargaan
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 text-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Jenis Penghargaan</th>
                                    <th>Periode</th>
                                    <th class="text-center">Penerima</th>
                                    <th class="text-center">Status</th>
                                    <th>Tgl Generate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($sertifikatTerbaru)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            Belum ada sertifikat yang di-generate pada periode ini.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($sertifikatTerbaru as $row): ?>
                                        <tr>
                                            <td>
                                                <span class="badge badge-light border mr-1"><?= esc($row['Kode']) ?></span>
                                                <strong><?= esc($row['NamaPenghargaan']) ?></strong>
                                            </td>
                                            <td><?= esc($row['Semester']) ?> / <?= esc($row['IdTahunAjaran']) ?></td>
                                            <td class="text-center"><span class="badge badge-info"><?= esc($row['TotalPenerima']) ?> santri</span></td>
                                            <td class="text-center">
                                                <?php if ($row['Status'] === 'final'): ?>
                                                    <span class="badge badge-success">Final</span>
                                                <?php elseif ($row['Status'] === 'draft'): ?>
                                                    <span class="badge badge-warning">Draft</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Dibatalkan</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc(date('d/m/Y H:i', strtotime($row['TanggalGenerate']))) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Konfigurasi Lembaga -->
        <div class="col-lg-4">
            <div class="card shadow-sm card-outline card-info">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-cogs text-info mr-2"></i>Konfigurasi Aktif Lembaga
                    </h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush text-sm">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Bobot Nilai vs Kehadiran</span>
                            <strong><?= esc($setting['BobotNilaiDefault']) ?>% : <?= esc($setting['BobotHadirDefault']) ?>%</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Threshold Hadir Sempurna</span>
                            <strong>&ge; <?= esc($setting['ThresholdHadirSempurna']) ?>%</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Default Pemenang</span>
                            <strong><?= $setting['JumlahPemenangDefault'] == 0 ? 'Semua memenuhi' : 'Top ' . esc($setting['JumlahPemenangDefault']) ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Juara Lembaga</span>
                            <strong><?= !empty($setting['IsGroupByJenjang']) ? 'Per Jenjang' : 'Semua Kelas' ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Format Nomor Seri</span>
                            <code><?= esc($setting['FormatNomorSeri']) ?></code>
                        </li>
                    </ul>
                </div>
                <?php if (!empty($roleCtx['isOperatorOrAdmin'])): ?>
                    <div class="card-footer bg-white text-right">
                        <a href="<?= base_url('backend/penghargaan/setting') ?>" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-sliders-h mr-1"></i> Ubah Pengaturan
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
