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

    $html = '<div class="progress" style="height: ' . $height . 'px;">';
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
                            <i class="fas fa-chalkboard-teacher"></i> Dashboard Ujian Semester - <?= esc($PeranLogin ?? 'Guru') ?>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-info btn-sm" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Welcome Message -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info alert-dismissible">
                                    <h5><i class="icon fas fa-info-circle"></i> Bismillahirrahmanirrahim</h5>
                                    <p class="mb-0">Assalamu'alaikum, <strong><?= esc(($SapaanLogin ?? 'Ustadz') . ' ' . ($NamaLogin ?? 'Pengguna')) ?></strong>...! 
                                    Selamat datang di dashboard ujian semester, Anda login sebagai <strong><?= esc($PeranLogin ?? 'Guru') ?></strong>
                                    <?php if (($PeranLogin ?? '') === 'Wali Kelas' && !empty($WaliKelasNamaKelas ?? '')): ?>
                                        - <strong><?= esc($WaliKelasNamaKelas) ?></strong>
                                        <span>(Tahun Ajaran <?= esc($TahunAjaran ?? '') ?>)</span>
                                    <?php endif; ?>.
                                    Semoga Allah senantiasa memberkahi langkah kita dalam menuntut ilmu dan mendidik generasi penerus.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Access Cards -->
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3><?= $TotalSantri ?? 0 ?></h3>
                                        <p>Total Santri</p>
                                        <small><?= $JumlahKelasDiajar ?? 0 ?> Kelas</small>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <a href="<?= base_url('backend/absensi/index') ?>" class="small-box-footer">
                                        Absensi <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>Rp <?= number_format($TotalTabungan ?? 0, 0, ',', '.') ?></h3>
                                        <p>Total Tabungan</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-piggy-bank"></i>
                                    </div>
                                    <a href="<?= base_url('backend/tabungan/showPerkelas') ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3><?= date('M Y') ?></h3>
                                        <p>Iuran Bulanan</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <a href="<?= base_url('backend/iuranBulanan/showPerKelas') ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                        <h3>Prestasi</h3>
                                        <p>Catatan Prestasi</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                    <a href="<?= base_url('backend/prestasi/showPerKelas') ?>" class="small-box-footer">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Semester Cards -->
                        <div class="row mt-4">
                            <!-- Semester Ganjil -->
                            <div class="col-md-6">
                                <div class="card card-warning card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-book-reader"></i> Semester Ganjil TA <?= esc($TahunAjaran ?? '') ?>
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-warning btn-sm" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-box bg-gradient-warning">
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Santri</span>
                                                <span class="info-box-number"><?= $TotalSantri ?? 0 ?> Santri dari <?= $JumlahKelasDiajar ?? 0 ?> Kelas</span>
                                                <?= render_progress_bar($StatusInputNilaiSemesterGanjil->persentasiSudah ?? 0) ?>
                                                <span class="progress-description">
                                                    Input nilai (<?= $StatusInputNilaiSemesterGanjil->countSudah ?? 0 ?>/<?= $StatusInputNilaiSemesterGanjil->countTotal ?? 0 ?>)
                                                </span>
                                                
                                                <!-- Progress per Kelas -->
                                                <div class="row mt-3">
                                                    <?php foreach ($StatusInputNilaiPerKelasGanjil ?? [] as $item): ?>
                                                        <div class="col-md-6 mb-2">
                                                            <span class="info-box-text"><?= esc($item['NamaKelas']) ?> 
                                                                <small class="float-right"><?= $JumlahSantriPerKelas[$item['IdKelas']] ?? 0 ?> Santri</small>
                                                            </span>
                                                            <?= render_progress_bar($item['StatusInputNilai']->persentasiSudah ?? 0, 20) ?>
                                                            <small>Input nilai (<?= $item['StatusInputNilai']->countSudah ?? 0 ?>/<?= $item['StatusInputNilai']->countTotal ?? 0 ?>)</small>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="row mt-3">
                                                    <?php
                                                    $currentMonth = date('n');
                                                    if ($currentMonth >= 7 && $currentMonth <= 12):
                                                    ?>
                                                        <div class="col-md-3 col-6 mb-2">
                                                            <a href="<?= base_url('backend/nilai/showSantriPerKelas/Ganjil') ?>" class="btn btn-block btn-primary btn-sm">
                                                                <i class="fas fa-edit"></i> Input Nilai
                                                            </a>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="col-md-3 col-6 mb-2">
                                                            <button class="btn btn-block btn-secondary btn-sm" disabled>
                                                                <i class="fas fa-edit"></i> Input Nilai
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="col-md-3 col-6 mb-2">
                                                        <a href="<?= base_url('backend/nilai/showDetailNilaiSantriPerKelas/Ganjil') ?>" class="btn btn-block btn-success btn-sm">
                                                            <i class="fas fa-eye"></i> Detail
                                                        </a>
                                                    </div>
                                                    <div class="col-md-3 col-6 mb-2">
                                                        <a href="<?= base_url('backend/nilai/showSumaryPersemester/Ganjil') ?>" class="btn btn-block btn-info btn-sm">
                                                            <i class="fas fa-chart-bar"></i> Ranking
                                                        </a>
                                                    </div>
                                                    <div class="col-md-3 col-6 mb-2">
                                                        <a href="<?= base_url('backend/rapor/index/Ganjil') ?>" class="btn btn-block btn-warning btn-sm">
                                                            <i class="fas fa-file-alt"></i> Raport
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Semester Genap -->
                            <div class="col-md-6">
                                <div class="card card-info card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-book-reader"></i> Semester Genap TA <?= esc($TahunAjaran ?? '') ?>
                                        </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-info btn-sm" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-box bg-gradient-info">
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Santri</span>
                                                <span class="info-box-number"><?= $TotalSantri ?? 0 ?> Santri dari <?= $JumlahKelasDiajar ?? 0 ?> Kelas</span>
                                                <?= render_progress_bar($StatusInputNilaiSemesterGenap->persentasiSudah ?? 0) ?>
                                                <span class="progress-description">
                                                    Input nilai (<?= $StatusInputNilaiSemesterGenap->countSudah ?? 0 ?>/<?= $StatusInputNilaiSemesterGenap->countTotal ?? 0 ?>)
                                                </span>
                                                
                                                <!-- Progress per Kelas -->
                                                <div class="row mt-3">
                                                    <?php foreach ($StatusInputNilaiPerKelasGenap ?? [] as $item): ?>
                                                        <div class="col-md-6 mb-2">
                                                            <span class="info-box-text"><?= esc($item['NamaKelas']) ?> 
                                                                <small class="float-right"><?= $JumlahSantriPerKelas[$item['IdKelas']] ?? 0 ?> Santri</small>
                                                            </span>
                                                            <?= render_progress_bar($item['StatusInputNilai']->persentasiSudah ?? 0, 20) ?>
                                                            <small>Input nilai (<?= $item['StatusInputNilai']->countSudah ?? 0 ?>/<?= $item['StatusInputNilai']->countTotal ?? 0 ?>)</small>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="row mt-3">
                                                    <?php
                                                    $currentMonth = date('n');
                                                    if ($currentMonth >= 1 && $currentMonth <= 6):
                                                    ?>
                                                        <div class="col-md-3 col-6 mb-2">
                                                            <a href="<?= base_url('backend/nilai/showSantriPerKelas/Genap') ?>" class="btn btn-block btn-primary btn-sm">
                                                                <i class="fas fa-edit"></i> Input Nilai
                                                            </a>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="col-md-3 col-6 mb-2">
                                                            <button class="btn btn-block btn-secondary btn-sm" disabled>
                                                                <i class="fas fa-edit"></i> Input Nilai
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="col-md-3 col-6 mb-2">
                                                        <a href="<?= base_url('backend/nilai/showDetailNilaiSantriPerKelas/Genap') ?>" class="btn btn-block btn-success btn-sm">
                                                            <i class="fas fa-eye"></i> Detail
                                                        </a>
                                                    </div>
                                                    <div class="col-md-3 col-6 mb-2">
                                                        <a href="<?= base_url('backend/nilai/showSumaryPersemester/Genap') ?>" class="btn btn-block btn-info btn-sm">
                                                            <i class="fas fa-chart-bar"></i> Ranking
                                                        </a>
                                                    </div>
                                                    <div class="col-md-3 col-6 mb-2">
                                                        <a href="<?= base_url('backend/rapor/index/Genap') ?>" class="btn btn-block btn-warning btn-sm">
                                                            <i class="fas fa-file-alt"></i> Raport
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

