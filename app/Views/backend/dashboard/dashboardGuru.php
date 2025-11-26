<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<?php
function render_progress_bar($persentase, $height = 25)
{
    $color_class = '';
    if ($persentase <= 40) {
        $color_class = 'bg-danger';
    } elseif ($persentase <= 80) {
        $color_class = 'bg-warning';
    } else {
        $color_class = 'bg-success';
    }

    $html = '<div class="progress mobile-progress" style="height: ' . $height . 'px;">';
    $html .= '<div class="progress-bar ' . $color_class . '" ';
    $html .= 'style="width: ' . $persentase . '%; display: flex; align-items: center; justify-content: center; font-size: 15px;">';
    $html .= $persentase . '%';
    $html .= '</div>';
    $html .= '</div>';

    return $html;
}
?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-gradient-info">
                        <h3 class="card-title">
                            <i class="fas fa-chalkboard-teacher"></i> 
                            <span class="d-none d-sm-inline">Dashboard Ujian Semester - </span>
                            <?= esc($PeranLogin ?? 'Guru') ?>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-info btn-sm" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Welcome Message -->
                        <div class="row mb-3 mb-md-4">
                            <div class="col-12">
                                <div class="alert alert-info alert-dismissible">
                                    <h5 class="mb-2 mb-md-3"><i class="icon fas fa-info-circle"></i> Bismillahirrahmanirrahim</h5>
                                    <p class="mb-0 small-text-mobile">Assalamu'alaikum, <strong><?= esc(($SapaanLogin ?? 'Ustadz') . ' ' . ($NamaLogin ?? 'Pengguna')) ?></strong>...! 
                                    Selamat datang di dashboard ujian semester, Anda login sebagai <strong><?= esc($PeranLogin ?? 'Guru') ?></strong>
                                    <?php if (($PeranLogin ?? '') === 'Wali Kelas' && !empty($WaliKelasNamaKelas ?? '')): ?>
                                        - <strong><?= esc($WaliKelasNamaKelas) ?></strong>
                                        <span class="d-none d-sm-inline">(Tahun Ajaran <?= esc($TahunAjaran ?? '') ?>)</span>
                                    <?php endif; ?>.
                                    <span class="d-none d-md-inline">Semoga Allah senantiasa memberkahi langkah kita dalam menuntut ilmu dan mendidik generasi penerus.</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Access Cards -->
                        <div class="row">
                            <div class="col-lg-3 col-6 mb-3 mb-lg-0">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3 class="mobile-h3"><?= $TotalSantri ?? 0 ?></h3>
                                        <p class="mb-1">Total Santri</p>
                                        <small class="d-block"><?= $JumlahKelasDiajar ?? 0 ?> Kelas</small>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <a href="<?= base_url('backend/absensi/index') ?>" class="small-box-footer">
                                        <span class="d-none d-sm-inline">Absensi </span><i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6 mb-3 mb-lg-0">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3 class="mobile-h3">
                                            <span class="d-none d-sm-inline">Rp </span>
                                            <span class="d-inline d-sm-none">Rp</span>
                                            <?= number_format($TotalTabungan ?? 0, 0, ',', '.') ?>
                                        </h3>
                                        <p class="mb-1">Total Tabungan</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-piggy-bank"></i>
                                    </div>
                                    <a href="<?= base_url('backend/tabungan/showPerkelas') ?>" class="small-box-footer">
                                        <span class="d-none d-sm-inline">Lihat Detail </span><i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6 mb-3 mb-lg-0">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3 class="mobile-h3"><?= date('M Y') ?></h3>
                                        <p class="mb-1">Iuran Bulanan</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <a href="<?= base_url('backend/iuranBulanan/showPerKelas') ?>" class="small-box-footer">
                                        <span class="d-none d-sm-inline">Lihat Detail </span><i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6 mb-3 mb-lg-0">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3 class="mobile-h3">Prestasi</h3>
                                        <p class="mb-1">Catatan Prestasi</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                    <a href="<?= base_url('backend/prestasi/showPerKelas') ?>" class="small-box-footer">
                                        <span class="d-none d-sm-inline">Lihat Detail </span><i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Semester Cards -->
                        <?php
                        // Tentukan semester saat ini berdasarkan bulan
                        // Semester Ganjil: Juli-Desember (bulan 7-12)
                        // Semester Genap: Januari-Juni (bulan 1-6)
                        $currentMonth = date('n');
                        $isSemesterGanjil = ($currentMonth >= 7 && $currentMonth <= 12);
                        $isSemesterGenap = ($currentMonth >= 1 && $currentMonth <= 6);
                        ?>
                        <div class="row mt-3 mt-md-4">
                            <!-- Semester Ganjil -->
                            <div class="col-12 mb-3">
                                <div class="card card-secondary card-outline <?= !$isSemesterGanjil ? 'collapsed-card' : '' ?>">
                                    <div class="card-header">
                                        <h3 class="card-title mobile-card-title">
                                            <i class="fas fa-book-reader"></i> 
                                            <span class="d-none d-sm-inline">Semester Ganjil </span>
                                            <span class="d-inline d-sm-none">Sem. Ganjil </span>
                                            <span class="d-none d-md-inline">TA <?= esc($TahunAjaran ?? '') ?></span>
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-secondary btn-sm" data-card-widget="collapse">
                                                <i class="fas <?= !$isSemesterGanjil ? 'fa-plus' : 'fa-minus' ?>"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-box bg-gradient-secondary">
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Santri</span>
                                                <span class="info-box-number mobile-info-number">
                                                    <?= $TotalSantri ?? 0 ?> Santri 
                                                    <span class="d-none d-sm-inline">dari <?= $JumlahKelasDiajar ?? 0 ?> Kelas</span>
                                                    <span class="d-inline d-sm-none">(<?= $JumlahKelasDiajar ?? 0 ?> Kls)</span>
                                                </span>
                                                <?= render_progress_bar($StatusInputNilaiSemesterGanjil->persentasiSudah ?? 0) ?>
                                                <span class="progress-description mobile-progress-desc">
                                                    Input nilai (<?= $StatusInputNilaiSemesterGanjil->countSudah ?? 0 ?>/<?= $StatusInputNilaiSemesterGanjil->countTotal ?? 0 ?>)
                                                </span>
                                                
                                                <!-- Progress per Kelas -->
                                                <div class="row mt-2 mt-md-3">
                                                    <?php foreach ($StatusInputNilaiPerKelasGanjil ?? [] as $item): ?>
                                                        <div class="col-12 col-md-6 mb-2">
                                                            <span class="info-box-text mobile-kelas-text">
                                                                <?= esc($item['NamaKelas']) ?> 
                                                                <small class="float-right"><?= $JumlahSantriPerKelas[$item['IdKelas']] ?? 0 ?> Santri</small>
                                                            </span>
                                                            <?= render_progress_bar($item['StatusInputNilai']->persentasiSudah ?? 0, 20) ?>
                                                            <small class="mobile-small-text">Input nilai (<?= $item['StatusInputNilai']->countSudah ?? 0 ?>/<?= $item['StatusInputNilai']->countTotal ?? 0 ?>)</small>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="row mt-2 mt-md-3">
                                                    <?php if ($isSemesterGanjil): ?>
                                                        <div class="col-6 col-md-3 mb-2">
                                                            <a href="<?= base_url('backend/nilai/showSantriPerKelas/Ganjil') ?>" class="btn btn-block btn-primary btn-sm mobile-btn">
                                                                <i class="fas fa-edit"></i> 
                                                                <span class="d-none d-sm-inline">Input Nilai</span>
                                                                <span class="d-inline d-sm-none">Input</span>
                                                            </a>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="col-6 col-md-3 mb-2">
                                                            <button class="btn btn-block btn-secondary btn-sm mobile-btn" disabled>
                                                                <i class="fas fa-edit"></i> 
                                                                <span class="d-none d-sm-inline">Input Nilai</span>
                                                                <span class="d-inline d-sm-none">Input</span>
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="col-6 col-md-3 mb-2">
                                                        <a href="<?= base_url('backend/nilai/showDetailNilaiSantriPerKelas/Ganjil') ?>" class="btn btn-block btn-success btn-sm mobile-btn">
                                                            <i class="fas fa-eye"></i> 
                                                            <span class="d-none d-sm-inline">Detail</span>
                                                        </a>
                                                    </div>
                                                    <div class="col-6 col-md-3 mb-2">
                                                        <a href="<?= base_url('backend/nilai/showSumaryPersemester/Ganjil') ?>" class="btn btn-block btn-info btn-sm mobile-btn">
                                                            <i class="fas fa-chart-bar"></i> 
                                                            <span class="d-none d-sm-inline">Ranking</span>
                                                        </a>
                                                    </div>
                                                    <div class="col-6 col-md-3 mb-2">
                                                        <a href="<?= base_url('backend/rapor/index/Ganjil') ?>" class="btn btn-block btn-warning btn-sm mobile-btn">
                                                            <i class="fas fa-file-alt"></i> 
                                                            <span class="d-none d-sm-inline">Raport</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Semester Genap -->
                            <div class="col-12">
                                <div class="card card-info card-outline <?= !$isSemesterGenap ? 'collapsed-card' : '' ?>">
                                    <div class="card-header">
                                        <h3 class="card-title mobile-card-title">
                                            <i class="fas fa-book-reader"></i> 
                                            <span class="d-none d-sm-inline">Semester Genap </span>
                                            <span class="d-inline d-sm-none">Sem. Genap </span>
                                            <span class="d-none d-md-inline">TA <?= esc($TahunAjaran ?? '') ?></span>
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-info btn-sm" data-card-widget="collapse">
                                                <i class="fas <?= !$isSemesterGenap ? 'fa-plus' : 'fa-minus' ?>"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-box bg-gradient-info">
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Santri</span>
                                                <span class="info-box-number mobile-info-number">
                                                    <?= $TotalSantri ?? 0 ?> Santri 
                                                    <span class="d-none d-sm-inline">dari <?= $JumlahKelasDiajar ?? 0 ?> Kelas</span>
                                                    <span class="d-inline d-sm-none">(<?= $JumlahKelasDiajar ?? 0 ?> Kls)</span>
                                                </span>
                                                <?= render_progress_bar($StatusInputNilaiSemesterGenap->persentasiSudah ?? 0) ?>
                                                <span class="progress-description mobile-progress-desc">
                                                    Input nilai (<?= $StatusInputNilaiSemesterGenap->countSudah ?? 0 ?>/<?= $StatusInputNilaiSemesterGenap->countTotal ?? 0 ?>)
                                                </span>
                                                
                                                <!-- Progress per Kelas -->
                                                <div class="row mt-2 mt-md-3">
                                                    <?php foreach ($StatusInputNilaiPerKelasGenap ?? [] as $item): ?>
                                                        <div class="col-12 col-md-6 mb-2">
                                                            <span class="info-box-text mobile-kelas-text">
                                                                <?= esc($item['NamaKelas']) ?> 
                                                                <small class="float-right"><?= $JumlahSantriPerKelas[$item['IdKelas']] ?? 0 ?> Santri</small>
                                                            </span>
                                                            <?= render_progress_bar($item['StatusInputNilai']->persentasiSudah ?? 0, 20) ?>
                                                            <small class="mobile-small-text">Input nilai (<?= $item['StatusInputNilai']->countSudah ?? 0 ?>/<?= $item['StatusInputNilai']->countTotal ?? 0 ?>)</small>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="row mt-2 mt-md-3">
                                                    <?php if ($isSemesterGenap): ?>
                                                        <div class="col-6 col-md-3 mb-2">
                                                            <a href="<?= base_url('backend/nilai/showSantriPerKelas/Genap') ?>" class="btn btn-block btn-primary btn-sm mobile-btn">
                                                                <i class="fas fa-edit"></i> 
                                                                <span class="d-none d-sm-inline">Input Nilai</span>
                                                                <span class="d-inline d-sm-none">Input</span>
                                                            </a>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="col-6 col-md-3 mb-2">
                                                            <button class="btn btn-block btn-secondary btn-sm mobile-btn" disabled>
                                                                <i class="fas fa-edit"></i> 
                                                                <span class="d-none d-sm-inline">Input Nilai</span>
                                                                <span class="d-inline d-sm-none">Input</span>
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="col-6 col-md-3 mb-2">
                                                        <a href="<?= base_url('backend/nilai/showDetailNilaiSantriPerKelas/Genap') ?>" class="btn btn-block btn-success btn-sm mobile-btn">
                                                            <i class="fas fa-eye"></i> 
                                                            <span class="d-none d-sm-inline">Detail</span>
                                                        </a>
                                                    </div>
                                                    <div class="col-6 col-md-3 mb-2">
                                                        <a href="<?= base_url('backend/nilai/showSumaryPersemester/Genap') ?>" class="btn btn-block btn-info btn-sm mobile-btn">
                                                            <i class="fas fa-chart-bar"></i> 
                                                            <span class="d-none d-sm-inline">Ranking</span>
                                                        </a>
                                                    </div>
                                                    <div class="col-6 col-md-3 mb-2">
                                                        <a href="<?= base_url('backend/rapor/index/Genap') ?>" class="btn btn-block btn-warning btn-sm mobile-btn">
                                                            <i class="fas fa-file-alt"></i> 
                                                            <span class="d-none d-sm-inline">Raport</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>

<?= $this->section('styles'); ?>
<style>
    /* Mobile-friendly styles untuk Dashboard Guru */
    
    /* Responsive font sizes untuk mobile */
    @media (max-width: 576px) {
        .small-text-mobile {
            font-size: 0.875rem;
            line-height: 1.4;
        }
        
        .mobile-h3 {
            font-size: 1.5rem !important;
        }
        
        .mobile-card-title {
            font-size: 0.95rem !important;
        }
        
        .mobile-info-number {
            font-size: 1.1rem !important;
        }
        
        .mobile-progress-desc {
            font-size: 0.8rem !important;
        }
        
        .mobile-kelas-text {
            font-size: 0.85rem !important;
        }
        
        .mobile-small-text {
            font-size: 0.75rem !important;
        }
        
        .mobile-btn {
            font-size: 0.75rem !important;
            padding: 0.25rem 0.5rem !important;
        }
        
        /* Small box adjustments untuk mobile */
        .small-box .inner h3 {
            font-size: 1.8rem !important;
        }
        
        .small-box .inner p {
            font-size: 0.9rem !important;
            margin-bottom: 0.25rem !important;
        }
        
        .small-box .inner small {
            font-size: 0.75rem !important;
        }
        
        /* Card header adjustments */
        .card-header .card-title {
            font-size: 0.95rem !important;
        }
        
        /* Info box adjustments */
        .info-box-content .info-box-text {
            font-size: 0.85rem !important;
        }
        
        .info-box-content .info-box-number {
            font-size: 1.1rem !important;
        }
        
        /* Progress bar adjustments */
        .mobile-progress {
            height: 20px !important;
        }
        
        .mobile-progress .progress-bar {
            font-size: 0.7rem !important;
            min-height: 20px;
        }
        
        /* Progress bar untuk height kecil (progress per kelas) */
        .mobile-progress[style*="height: 20px"] .progress-bar {
            font-size: 0.65rem !important;
        }
        
        /* Button spacing untuk mobile */
        .row .col-6.mb-2 {
            margin-bottom: 0.5rem !important;
        }
        
        /* Card body padding untuk mobile */
        .card-body {
            padding: 0.75rem !important;
        }
        
        /* Alert adjustments */
        .alert h5 {
            font-size: 1rem !important;
        }
        
        /* Icon size untuk mobile */
        .small-box .icon {
            font-size: 3rem !important;
        }
    }
    
    /* Tablet adjustments */
    @media (min-width: 577px) and (max-width: 768px) {
        .mobile-h3 {
            font-size: 1.8rem !important;
        }
        
        .mobile-card-title {
            font-size: 1rem !important;
        }
        
        .mobile-btn {
            font-size: 0.8rem !important;
        }
    }
    
    /* Pastikan button touch-friendly di mobile */
    @media (max-width: 768px) {
        .mobile-btn {
            min-height: 38px;
            padding: 0.375rem 0.75rem;
        }
        
        /* Pastikan card tidak terlalu lebar di mobile */
        .card {
            margin-bottom: 1rem;
        }
        
        /* Spacing untuk progress per kelas */
        .row .col-12.col-md-6.mb-2 {
            margin-bottom: 0.75rem;
        }
    }
    
        /* Optimasi untuk layar sangat kecil */
    @media (max-width: 375px) {
        .small-box .inner h3 {
            font-size: 1.5rem !important;
        }
        
        .mobile-h3 {
            font-size: 1.3rem !important;
        }
        
        .mobile-btn {
            font-size: 0.7rem !important;
            padding: 0.2rem 0.4rem !important;
        }
        
        .card-header {
            padding: 0.5rem 0.75rem !important;
        }
    }
    
    /* Perbaikan spacing umum untuk mobile */
    @media (max-width: 768px) {
        /* Margin bottom untuk row */
        .row.mt-3, .row.mt-4 {
            margin-top: 1rem !important;
        }
        
        /* Padding untuk card body */
        .card-body {
            padding: 1rem !important;
        }
        
        /* Info box padding */
        .info-box {
            margin-bottom: 0.5rem;
        }
        
        /* Small box footer */
        .small-box-footer {
            padding: 0.5rem;
            font-size: 0.85rem;
        }
        
        /* Card spacing */
        .card {
            margin-bottom: 1rem;
        }
    }
</style>
<?= $this->endSection(); ?>

