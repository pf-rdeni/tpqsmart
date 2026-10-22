<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<?php
$tahunList = !empty($tahunAjaranList) ? $tahunAjaranList : [$current_tahun_ajaran];
$defaultTahun = in_array($current_tahun_ajaran, $tahunList, true) ? $current_tahun_ajaran : $tahunList[0];
$tpqName = !empty($activeTpq['NamaTpq']) ? $activeTpq['NamaTpq'] : 'TPQ Smart';
$tpqLogo = !empty($activeTpq['LogoLembaga']) ? base_url('uploads/tpq/' . $activeTpq['LogoLembaga']) : base_url('assets/images/logo.png');
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

<style>
/* ==================== ROOT VARIABLES & THEME ==================== */
:root {
    --prs-primary: #0284c7;
    --prs-primary-dark: #0369a1;
    --prs-primary-light: #e0f2fe;
    --prs-success: #10b981;
    --prs-warning: #f59e0b;
    --prs-danger: #ef4444;
    --prs-dark-bg: #090e17;
    --prs-dark-card: #111827;
    --prs-dark-border: rgba(255, 255, 255, 0.1);
    --prs-light-bg: #f8fafc;
    --prs-light-card: #ffffff;
    --prs-font-serif: 'Playfair Display', Georgia, serif;
    --prs-font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
}

/* Base presentation styles */
.prs-wrapper {
    font-family: var(--prs-font-sans);
    color: #1e293b;
}

/* Glassmorphism control panel */
.prs-control-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
}

.prs-badge-soft {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Aspect 16:9 Presentation Canvas Container */
.prs-viewport-wrapper {
    position: relative;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    background: #000;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
}

.prs-aspect-ratio {
    position: relative;
    width: 100%;
    padding-top: 56.25%; /* 16:9 */
}

.prs-canvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    background: #fff;
    display: flex;
    flex-direction: column;
    user-select: none;
}

/* Individual Slide Theme Classes */
.slide-theme-dark {
    background: radial-gradient(circle at 85% 15%, #1e293b 0%, #0f172a 50%, #020617 100%);
    color: #f8fafc;
}
.slide-theme-light {
    background: radial-gradient(circle at 100% 0%, #f1f5f9 0%, #ffffff 70%, #f8fafc 100%);
    color: #0f172a;
}

/* Slide Typography */
.slide-eyebrow {
    font-size: 0.85rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.slide-eyebrow::before {
    content: '';
    display: inline-block;
    width: 24px;
    height: 3px;
    background: currentColor;
    border-radius: 99px;
}

.slide-title-serif {
    font-family: var(--prs-font-serif);
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: -0.02em;
}

.slide-content-area {
    flex: 1;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 2.2% 4.5%;
}

/* Custom Scrollbar for Matriks Table */
.prs-matriks-scroll::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.prs-matriks-scroll::-webkit-scrollbar-track {
    background: #f8fafc;
}
.prs-matriks-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.prs-matriks-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Thumbnail Carousel */
.prs-thumb-strip {
    display: flex;
    gap: 12px;
    overflow-x: auto;
    padding: 10px 4px;
    scrollbar-width: thin;
}
.prs-thumb-strip::-webkit-scrollbar {
    height: 6px;
}
.prs-thumb-strip::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.prs-thumb-card {
    flex: 0 0 160px;
    aspect-ratio: 16/9;
    border-radius: 8px;
    border: 2px solid #e2e8f0;
    cursor: pointer;
    background: #f8fafc;
    position: relative;
    overflow: hidden;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    font-weight: 700;
    text-align: center;
    padding: 6px;
    color: #475569;
}
.prs-thumb-card:hover {
    border-color: var(--prs-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.15);
}
.prs-thumb-card.active {
    border-color: var(--prs-primary);
    background: #0284c7;
    color: #ffffff;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.25);
}
.prs-thumb-num {
    position: absolute;
    top: 4px;
    left: 4px;
    width: 20px;
    height: 20px;
    border-radius: 99px;
    background: rgba(0, 0, 0, 0.2);
    color: #fff;
    font-size: 0.65rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Fullscreen Overlay Mode */
.prs-fullscreen-active {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 999999 !important;
    border-radius: 0 !important;
    background: #000 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
.prs-fullscreen-active .prs-aspect-ratio {
    max-height: 100vh;
    max-width: 177.78vh; /* 16:9 ratio lock */
    width: 100%;
    margin: auto;
}

/* Laser Pointer Effect */
#laserPointer {
    position: absolute;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: rgba(239, 68, 68, 0.95);
    box-shadow: 0 0 16px 6px rgba(239, 68, 68, 0.75);
    pointer-events: none;
    z-index: 99999;
    transform: translate(-50%, -50%);
    display: none;
    transition: width 0.1s, height 0.1s;
}

/* Card Styling inside slides */
.prs-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: transform 0.2s, box-shadow 0.2s;
}
.slide-theme-dark .prs-card {
    background: rgba(30, 41, 59, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #f8fafc;
}

/* Progress bar */
.prs-progress-track {
    height: 4px;
    background: rgba(0, 0, 0, 0.08);
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 30;
}
.prs-progress-fill {
    height: 100%;
    background: var(--prs-primary);
    width: 8.33%;
    transition: width 0.3s ease;
}

/* Keyframes & Animations */
@keyframes fadeInSlide {
    from { opacity: 0; transform: scale(0.985); }
    to { opacity: 1; transform: scale(1); }
}
.prs-slide-animate {
    animation: fadeInSlide 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Custom Scroll inside slide 11 if many students */
.prs-student-scroll {
    max-height: 40vh;
    overflow-y: auto;
    scrollbar-width: thin;
    padding-right: 4px;
}
.prs-student-scroll::-webkit-scrollbar {
    width: 4px;
}
.prs-student-scroll::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.15);
    border-radius: 4px;
}

/* Floating controls in slideshow */
.prs-hud {
    position: absolute;
    bottom: 16px;
    right: 20px;
    z-index: 50;
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(8px);
    padding: 6px 14px;
    border-radius: 9999px;
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #fff;
    opacity: 0.3;
    transition: opacity 0.25s ease;
}
.prs-viewport-wrapper:hover .prs-hud,
.prs-fullscreen-active:hover .prs-hud {
    opacity: 1;
}

.prs-hud-btn {
    background: transparent;
    border: none;
    color: #f8fafc;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.15s;
}
.prs-hud-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: #38bdf8;
    transform: scale(1.1);
}
</style>

<section class="content prs-wrapper">
    <div class="container-fluid py-3">

        <!-- ==================== HEADER & FILTER BAR ==================== -->
        <div class="card prs-control-card mb-3">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-lg-5 col-md-12 mb-2 mb-lg-0">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary text-white p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; font-size: 1.3rem;">
                                <i class="fas fa-tv"></i>
                            </div>
                            <div class="ml-2">
                                <h4 class="mb-0 font-weight-bold" style="font-family: var(--prs-font-sans); letter-spacing: -0.01em;">Presentasi Statistik Hasil Munaqosah</h4>
                                <div class="text-muted small">Slideshow interaktif 12 slide berbasis web untuk evaluasi santri & wali santri</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7 col-md-12 text-lg-right">
                        <div class="btn-group mb-1">
                            <button id="btnLaunchPresentation" class="btn btn-primary px-3 shadow-sm font-weight-bold" style="border-radius: 8px 0 0 8px;">
                                <i class="fas fa-play-circle mr-1"></i> Mulai Presentasi Fullscreen
                            </button>
                            <button id="btnExportPdf" class="btn btn-danger px-3 shadow-sm font-weight-bold">
                                <i class="fas fa-file-pdf mr-1"></i> Export PDF (A4 16:9)
                            </button>
                            <button id="btnOpenConfigModal" class="btn btn-outline-secondary px-3" style="border-radius: 0 8px 8px 0;">
                                <i class="fas fa-sliders-h mr-1"></i> Pengaturan Slide
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="my-2">

                <!-- Filter Controls (Sejajar & Rapi) -->
                <div class="row pt-1">
                    <div class="col-lg-3 col-md-6 mb-2">
                        <label class="small mb-1 font-weight-bold text-muted">Tahun Ajaran</label>
                        <select id="filterTahunAjaran" class="form-control form-control-sm select2" style="width: 100%;">
                            <?php foreach ($tahunList as $t): ?>
                                <?php $label = preg_match('/^\d{8}$/', (string)$t) ? 'T.A ' . substr($t, 2, 2) . '/' . substr($t, 6, 2) : $t; ?>
                                <option value="<?= esc($t) ?>" <?= $t === $defaultTahun ? 'selected' : '' ?>>
                                    <?= esc($label) ?> (<?= esc($t) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-2">
                        <label class="small mb-1 font-weight-bold text-muted">Lembaga TPQ</label>
                        <select id="filterTpq" class="form-control form-control-sm" <?= $isAdmin ? '' : 'disabled' ?>>
                            <?php if ($isAdmin): ?><option value="0">Semua TPQ</option><?php endif; ?>
                            <?php if (!empty($tpqDropdown)): foreach ($tpqDropdown as $tpq): ?>
                                <option value="<?= esc($tpq['IdTpq']) ?>" <?= (!empty($activeTpq['IdTpq']) && $activeTpq['IdTpq'] == $tpq['IdTpq']) ? 'selected' : '' ?>>
                                    <?= esc($tpq['NamaTpq']) ?>
                                </option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4 mb-2">
                        <label class="small mb-1 font-weight-bold text-muted">Type Ujian</label>
                        <select id="filterTypeUjian" class="form-control form-control-sm">
                            <option value="pra-munaqosah" selected>Pra-Munaqosah</option>
                            <option value="munaqosah">Munaqosah</option>
                            <option value="pra-munaqosah,munaqosah">Semua (Pra &amp; Munaqosah)</option>
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-8 mb-2">
                        <label class="small mb-1 font-weight-bold text-muted">Batas Nilai (KKM, Aman)</label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="filterThreshold" class="form-control" value="65, 70, 75" placeholder="contoh: 65, 70, 75">
                            <div class="input-group-append">
                                <button id="btnReloadData" class="btn btn-primary font-weight-bold px-3" type="button" title="Terapkan filter dan muat data ulang">
                                    <i class="fas fa-sync-alt mr-1"></i> Muat Data
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-baris: Catatan Petunjuk & Pilihan Cepat Preset (Rata & Bersih) -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-between flex-wrap py-1 px-2 rounded bg-light border" style="font-size: 0.74rem; gap: 6px;">
                            <div class="text-muted">
                                <i class="fas fa-info-circle text-primary mr-1"></i>
                                <strong>Catatan:</strong> Pisahkan koma (contoh: <code>65, 70, 75</code>). Nilai di bawah angka pertama (&lt;65) adalah Zona Belum Tuntas / KKM.
                            </div>
                            <div class="d-flex align-items-center flex-wrap" style="gap: 4px;">
                                <span class="font-weight-bold text-secondary mr-1">Pilihan Cepat:</span>
                                <a href="#" class="badge badge-white border shadow-xs prs-preset-val text-dark px-2 py-1" data-val="65, 70, 75" title="KKM 65, Cukup 65-69, Baik 70-74, Sangat Baik ≥75"><strong>65, 70, 75</strong> (Default 3 Level)</a>
                                <a href="#" class="badge badge-white border shadow-xs prs-preset-val text-dark px-2 py-1" data-val="65, 70" title="KKM 65, Aman 70">65, 70 (2 Level)</a>
                                <a href="#" class="badge badge-white border shadow-xs prs-preset-val text-dark px-2 py-1" data-val="70, 75" title="KKM 70, Aman 75">70, 75</a>
                                <a href="#" class="badge badge-white border shadow-xs prs-preset-val text-dark px-2 py-1" data-val="60, 70, 80" title="KKM 60, Aman 70, Unggul 80">60, 70, 80</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== MAIN PRESENTATION CANVAS ==================== -->
        <div class="prs-viewport-wrapper mb-3" id="prsPresentationWrapper">
            <div id="laserPointer"></div>

            <div class="prs-aspect-ratio">
                <div class="prs-canvas prs-slide-animate" id="prsCanvas">
                    <!-- Dynamic Slide Content rendered via JavaScript -->
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        <div class="text-center">
                            <div class="spinner-border text-primary mb-2" role="status"></div>
                            <div>Menyiapkan data presentasi...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="prs-progress-track">
                <div class="prs-progress-fill" id="prsProgressFill"></div>
            </div>

            <!-- HUD Floating Controls -->
            <div class="prs-hud" id="prsHud">
                <button class="prs-hud-btn" id="hudPrev" title="Slide Sebelumnya (Panah Kiri)"><i class="fas fa-chevron-left"></i></button>
                <span class="small font-weight-bold px-2" id="hudSlideCounter">1 / 13</span>
                <button class="prs-hud-btn" id="hudNext" title="Slide Berikutnya (Panah Kanan / Space)"><i class="fas fa-chevron-right"></i></button>
                <div style="width: 1px; height: 16px; background: rgba(255,255,255,0.2); margin: 0 4px;"></div>
                <button class="prs-hud-btn" id="hudLaser" title="Toggle Laser Pointer (P)"><i class="fas fa-bullseye"></i></button>
                <button class="prs-hud-btn" id="hudFullscreen" title="Toggle Fullscreen (F)"><i class="fas fa-expand"></i></button>
                <button class="prs-hud-btn" id="hudExitFs" style="display:none;" title="Keluar Fullscreen (Esc)"><i class="fas fa-compress"></i></button>
            </div>
        </div>

        <!-- ==================== THUMBNAIL CAROUSEL ==================== -->
        <div class="card prs-control-card">
            <div class="card-body p-2">
                <div class="d-flex justify-content-between align-items-center px-2 mb-1">
                    <span class="small font-weight-bold text-muted"><i class="fas fa-th-large mr-1"></i> Daftar Slide (Klik untuk Langsung Berpindah)</span>
                    <span class="small text-muted"><kbd class="bg-light text-dark border">←</kbd> <kbd class="bg-light text-dark border">→</kbd> Navigasi keyboard | <kbd class="bg-light text-dark border">F</kbd> Layar Penuh</span>
                </div>
                <div class="prs-thumb-strip" id="prsThumbStrip">
                    <!-- 12 Slide Thumbnails generated dynamically -->
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ==================== MODAL KONFIGURASI PRESENTASI ==================== -->
<div class="modal fade" id="modalConfig" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold text-dark">
                    <i class="fas fa-sliders-h text-primary mr-1"></i> Pengaturan Konten & Tampilan Presentasi
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4">
                <ul class="nav nav-pills mb-3" id="configTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" id="tab-global-link" data-toggle="pill" href="#tab-global" role="tab">Identitas & Cover</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-pengelompokan-link" data-toggle="pill" href="#tab-pengelompokan" role="tab">Kriteria & Privasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-langkah-link" data-toggle="pill" href="#tab-langkah" role="tab">Strategi & Tim Ustadz</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-kendala-link" data-toggle="pill" href="#tab-kendala" role="tab">Analisis Kendala</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-matriks-link" data-toggle="pill" href="#tab-matriks" role="tab"><i class="fas fa-table mr-1"></i> Matriks Evaluasi</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Tab 1: Global & Cover -->
                    <div class="tab-pane fade show active" id="tab-global" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold">Nama Lembaga (Override)</label>
                                <input type="text" id="cfg_namaTpq" class="form-control" value="<?= esc($tpqName) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold">Sub-Judul Cover</label>
                                <input type="text" id="cfg_subtitleCover" class="form-control" value="Evaluasi Pra-Munaqosah Santri Kelas 6">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="small font-weight-bold">Judul Utama Cover</label>
                                <input type="text" id="cfg_judulCover" class="form-control" value="Kesiapan Menuju Munaqosah">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="small font-weight-bold">Tagline Utama</label>
                                <input type="text" id="cfg_taglineCover" class="form-control" value="Satu Tekad: 100% Lulus Munaqosah, Berangkat Wisuda Bersama">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold">Footer Kiri Cover</label>
                                <input type="text" id="cfg_footerKiri" class="form-control" value="Pertemuan Orang Tua & Santri">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold">Footer Kanan Cover</label>
                                <input type="text" id="cfg_footerKanan" class="form-control" value="Fokus Peningkatan & Perbaikan Materi">
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Kriteria & Privasi (Slide 8, 11) -->
                    <div class="tab-pane fade" id="tab-pengelompokan" role="tabpanel">
                        <div class="alert alert-info py-2 px-3 small">
                            <i class="fas fa-info-circle mr-1"></i> Atur kriteria pengelompokan santri di <strong>Slide 11 (Peta Kebutuhan Santri)</strong> berdasarkan jumlah materi yang di bawah KKM (&lt; threshold KKM).
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="small font-weight-bold text-danger">Klinik Khusus (Merah)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">&ge;</span></div>
                                    <input type="number" id="cfg_klinikThreshold" class="form-control" value="4" min="1" max="10">
                                    <div class="input-group-append"><span class="input-group-text">materi</span></div>
                                </div>
                                <span class="text-muted small">Santri dengan nilai di bawah KKM &ge; batas ini</span>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="small font-weight-bold text-warning">Penguatan Inti (Oranye)</label>
                                <div class="input-group">
                                    <input type="text" id="cfg_penguatanRange" class="form-control" value="2 - 3" readonly>
                                    <div class="input-group-append"><span class="input-group-text">materi</span></div>
                                </div>
                                <span class="text-muted small">Otomatis di antara Polesan &amp; Klinik</span>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="small font-weight-bold text-primary">Polesan Ringan (Biru)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">&le;</span></div>
                                    <input type="number" id="cfg_polesanThreshold" class="form-control" value="1" min="1" max="3">
                                    <div class="input-group-append"><span class="input-group-text">materi</span></div>
                                </div>
                                <span class="text-muted small">Hanya 1 materi yang butuh perbaikan</span>
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="form-group mb-2">
                            <label class="small font-weight-bold d-block text-dark mb-2">
                                <i class="fas fa-id-card text-primary mr-1"></i> Format Tampilan Identitas Santri di Slide 11
                            </label>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="custom-control custom-radio border rounded p-2 bg-light h-100">
                                        <input type="radio" id="disp_full" name="cfg_santriDisplayMode" class="custom-control-input" value="full" checked>
                                        <label class="custom-control-label small font-weight-bold ml-1" for="disp_full">
                                            Nama Lengkap Saja
                                            <span class="d-block text-muted font-weight-normal" style="font-size: 0.72rem;">Contoh: <strong>Muhammad Fatih</strong></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="custom-control custom-radio border rounded p-2 bg-light h-100 border-danger">
                                        <input type="radio" id="disp_notest_only" name="cfg_santriDisplayMode" class="custom-control-input" value="notest_only">
                                        <label class="custom-control-label small font-weight-bold text-danger ml-1" for="disp_notest_only">
                                            <i class="fas fa-id-badge mr-1"></i> Hanya No. Test (Anonim)
                                            <span class="d-block text-muted font-weight-normal" style="font-size: 0.72rem;">Contoh: <strong>No. Test: 2026-001</strong> (Nama disembunyikan penuh)</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="custom-control custom-radio border rounded p-2 bg-light h-100">
                                        <input type="radio" id="disp_name_notest" name="cfg_santriDisplayMode" class="custom-control-input" value="name_notest">
                                        <label class="custom-control-label small font-weight-bold ml-1" for="disp_name_notest">
                                            Nama Lengkap + No. Test
                                            <span class="d-block text-muted font-weight-normal" style="font-size: 0.72rem;">Contoh: <strong>Muhammad Fatih [#2026-001]</strong></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="custom-control custom-radio border rounded p-2 bg-light h-100">
                                        <input type="radio" id="disp_initial" name="cfg_santriDisplayMode" class="custom-control-input" value="initial">
                                        <label class="custom-control-label small font-weight-bold ml-1" for="disp_initial">
                                            Inisial / Sensor Nama
                                            <span class="d-block text-muted font-weight-normal" style="font-size: 0.72rem;">Contoh: <strong>M******* F****</strong></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Langkah Aksi & Tim Guru (Slide 9) -->
                    <div class="tab-pane fade" id="tab-langkah" role="tabpanel">
                        <div class="alert alert-secondary py-2 px-3 small">
                            <i class="fas fa-users-cog mr-1"></i> Tentukan 4 langkah aksi di <strong>Slide 9</strong> beserta ustadz/penanggung jawab.
                        </div>

                        <!-- Langkah 1 -->
                        <div class="border rounded p-2 mb-2 bg-light">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="small font-weight-bold">01 • Konteks</label>
                                    <input type="text" id="cfg_step1_ctx" class="form-control form-control-sm" value="TPQ">
                                </div>
                                <div class="col-md-5">
                                    <label class="small font-weight-bold">Judul Aksi</label>
                                    <input type="text" id="cfg_step1_title" class="form-control form-control-sm" value="Bimbingan Tambahan Maghrib">
                                </div>
                                <div class="col-md-4">
                                    <label class="small font-weight-bold">Penanggung Jawab</label>
                                    <input type="text" id="cfg_step1_tag" class="form-control form-control-sm" list="guruSuggestions" value="Ustadz Pembina Kelas 6">
                                </div>
                                <div class="col-12 mt-1">
                                    <label class="small font-weight-bold text-muted">Deskripsi</label>
                                    <input type="text" id="cfg_step1_desc" class="form-control form-control-sm" value="Fokus drill materi tajwid &amp; kelancaran ayat-ayat panjang">
                                </div>
                            </div>
                        </div>

                        <!-- Langkah 2 -->
                        <div class="border rounded p-2 mb-2 bg-light">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="small font-weight-bold">02 • Konteks</label>
                                    <input type="text" id="cfg_step2_ctx" class="form-control form-control-sm" value="TPQ">
                                </div>
                                <div class="col-md-5">
                                    <label class="small font-weight-bold">Judul Aksi</label>
                                    <input type="text" id="cfg_step2_title" class="form-control form-control-sm" value="Klinik Menulis &amp; Imla' Khusus">
                                </div>
                                <div class="col-md-4">
                                    <label class="small font-weight-bold">Penanggung Jawab</label>
                                    <input type="text" id="cfg_step2_tag" class="form-control form-control-sm" list="guruSuggestions" value="Tim Pengajar Khat &amp; Imla'">
                                </div>
                                <div class="col-12 mt-1">
                                    <label class="small font-weight-bold text-muted">Deskripsi</label>
                                    <input type="text" id="cfg_step2_desc" class="form-control form-control-sm" value="Pendampingan privat penulisan surah pendek dan kaidah sambung huruf">
                                </div>
                            </div>
                        </div>

                        <!-- Langkah 3 -->
                        <div class="border rounded p-2 mb-2 bg-light">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="small font-weight-bold">03 • Konteks</label>
                                    <input type="text" id="cfg_step3_ctx" class="form-control form-control-sm" value="RUMAH">
                                </div>
                                <div class="col-md-5">
                                    <label class="small font-weight-bold">Judul Aksi</label>
                                    <input type="text" id="cfg_step3_title" class="form-control form-control-sm" value="Setoran Rutin Ba'da Subuh">
                                </div>
                                <div class="col-md-4">
                                    <label class="small font-weight-bold">Penanggung Jawab</label>
                                    <input type="text" id="cfg_step3_tag" class="form-control form-control-sm" value="Ayah / Bunda di Rumah">
                                </div>
                                <div class="col-12 mt-1">
                                    <label class="small font-weight-bold text-muted">Deskripsi</label>
                                    <input type="text" id="cfg_step3_desc" class="form-control form-control-sm" value="Murojaah 15 menit setiap pagi untuk menguatkan retensi hafalan">
                                </div>
                            </div>
                        </div>

                        <!-- Langkah 4 -->
                        <div class="border rounded p-2 mb-2 bg-light">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="small font-weight-bold">04 • Konteks</label>
                                    <input type="text" id="cfg_step4_ctx" class="form-control form-control-sm" value="WALI SANTRI">
                                </div>
                                <div class="col-md-5">
                                    <label class="small font-weight-bold">Judul Aksi</label>
                                    <input type="text" id="cfg_step4_title" class="form-control form-control-sm" value="Buku Kendali &amp; Komunikasi Guru">
                                </div>
                                <div class="col-md-4">
                                    <label class="small font-weight-bold">Penanggung Jawab</label>
                                    <input type="text" id="cfg_step4_tag" class="form-control form-control-sm" value="Wali Kelas &amp; Orang Tua">
                                </div>
                                <div class="col-12 mt-1">
                                    <label class="small font-weight-bold text-muted">Deskripsi</label>
                                    <input type="text" id="cfg_step4_desc" class="form-control form-control-sm" value="Pemeriksaan paraf buku pantauan harian dan simulasi ujian berkala">
                                </div>
                            </div>
                        </div>

                        <datalist id="guruSuggestions">
                            <?php if (!empty($guruList)): foreach ($guruList as $g): ?>
                                <option value="<?= esc($g['Nama']) ?>">
                            <?php endforeach; endif; ?>
                        </datalist>
                    </div>

                    <!-- Tab 4: Analisis Kendala (Slide 7) -->
                    <div class="tab-pane fade" id="tab-kendala" role="tabpanel">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="small font-weight-bold">Kendala 1 (Judul &amp; Penjelasan)</label>
                                <input type="text" id="cfg_k1_title" class="form-control form-control-sm mb-1" value="Kerap Terburu-buru">
                                <textarea id="cfg_k1_desc" class="form-control form-control-sm" rows="2">Santri sering membaca terlalu cepat sehingga makharijul huruf dan ketukan mad kurang konsisten.</textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="small font-weight-bold">Kendala 2 (Judul &amp; Penjelasan)</label>
                                <input type="text" id="cfg_k2_title" class="form-control form-control-sm mb-1" value="Hafalan Sambung Ayat">
                                <textarea id="cfg_k2_desc" class="form-control form-control-sm" rows="2">Ragu ketika penguji memotong bacaan di pertengahan ayat atau memanggil ayat acak.</textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="small font-weight-bold">Kendala 3 (Judul &amp; Penjelasan)</label>
                                <input type="text" id="cfg_k3_title" class="form-control form-control-sm mb-1" value="Kaidah Sambung Huruf Imla'">
                                <textarea id="cfg_k3_desc" class="form-control form-control-sm" rows="2">Penulisan huruf hijaiyah posisi tengah dan akhir kalimat masih sering tertukar.</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 5: Matriks Evaluasi & Pemetaan (Slide 12) -->
                    <div class="tab-pane fade" id="tab-matriks" role="tabpanel">
                        <div class="alert alert-info py-2 px-3 small">
                            <i class="fas fa-table mr-1"></i> Pengaturan untuk <strong>Slide 12 (Matriks Evaluasi &amp; Pemetaan Materi)</strong>.
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold">Judul Slide Matriks</label>
                                <input type="text" id="cfg_matriks_title" class="form-control form-control-sm" value="Matriks Evaluasi &amp; Pemetaan Kekurangan Materi">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold">Sub-Judul / Keterangan</label>
                                <input type="text" id="cfg_matriks_subtitle" class="form-control form-control-sm" value="Daftar Rekapitulasi Capaian Materi per Santri">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold">Filter Tampilan Santri</label>
                                <select id="cfg_matriks_filter" class="form-control form-control-sm">
                                    <option value="kurang_only">Hanya yang Kurang (Nilai &lt; KKM)</option>
                                    <option value="semua">Tampilkan Seluruh Santri (Lengkap)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small font-weight-bold">Tampilkan Angka Nilai</label>
                                <div class="custom-control custom-switch mt-1">
                                    <input type="checkbox" class="custom-control-input" id="cfg_matriks_show_score" checked>
                                    <label class="custom-control-label small" for="cfg_matriks_show_score">Tampilkan skor numerik di samping bulatan merah / hijau</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-danger btn-sm" id="btnResetDefaultConfig">
                    <i class="fas fa-undo mr-1"></i> Reset ke Preset Default
                </button>
                <div>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary btn-sm px-3" id="btnSaveConfig">
                        <i class="fas fa-save mr-1"></i> Simpan &amp; Terapkan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODAL RINCIAN SANTRI PER MATERI (DRILL-DOWN DARI SLIDE 4) ==================== -->
<div class="modal fade" id="modalDetailSantriMateri" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header bg-light">
                <div>
                    <h5 class="modal-title font-weight-bold text-dark mb-0" id="mdsmTitle">
                        <i class="fas fa-book-reader text-primary mr-1"></i> Rincian Capaian Santri
                    </h5>
                    <div class="small text-muted" id="mdsmSubtitle">Daftar santri dan perolehan nilai materi</div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap: 8px;">
                    <!-- Filter Pills -->
                    <div class="btn-group btn-group-sm" role="group" id="mdsmFilterGroup">
                        <button type="button" class="btn btn-primary font-weight-bold btn-filter-materi-status" data-filter="all">Semua (<span id="mdsmCountAll">0</span>)</button>
                        <button type="button" class="btn btn-outline-success font-weight-bold btn-filter-materi-status" data-filter="aman">Aman (<span id="mdsmCountAman">0</span>)</button>
                        <button type="button" class="btn btn-outline-warning font-weight-bold btn-filter-materi-status" data-filter="cukup">Cukup (<span id="mdsmCountCukup">0</span>)</button>
                        <button type="button" class="btn btn-outline-danger font-weight-bold btn-filter-materi-status" data-filter="kurang">Perbaikan (<span id="mdsmCountKurang">0</span>)</button>
                    </div>
                    <!-- Search Input -->
                    <div style="min-width: 200px;">
                        <input type="search" id="mdsmSearchInput" class="form-control form-control-sm" placeholder="Cari santri...">
                    </div>
                </div>

                <div class="table-responsive rounded border" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-striped table-hover mb-0" id="mdsmTable">
                        <thead class="thead-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th style="width: 45px;" class="text-center">No</th>
                                <th>Nama Santri</th>
                                <th class="text-center" style="width: 100px;">Nilai</th>
                                <th class="text-center" style="width: 130px;">Kategori</th>
                            </tr>
                        </thead>
                        <tbody id="mdsmTableBody">
                            <!-- Diisi secara dinamis -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer justify-content-between bg-light py-2">
                <div class="small text-muted" id="mdsmFooterInfo">
                    Total santri ditampilkan
                </div>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- html2canvas and jsPDF for high-res PDF generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<!-- Chart.js for Slide 4 -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
(function(window, $) {
    'use strict';

    function runPresentasiInit() {
        if (!window.jQuery) {
            setTimeout(runPresentasiInit, 50);
            return;
        }
        const $ = window.jQuery;

    // State object
    const TPQ_APP = {
        currentSlide: 1,
        totalSlides: 13,
        dataset: null,
        chartInstance: null,
        isLaserActive: false,
        isFullscreen: false,
        config: {},
        activeTpqLogo: "<?= esc($tpqLogo) ?>",
        activeTpqName: "<?= esc($tpqName) ?>"
    };

    // Default configuration preset
    const DEFAULT_CONFIG = {
        namaTpq: "<?= esc($tpqName) ?>",
        subtitleCover: "Evaluasi Pra-Munaqosah Santri Kelas 6",
        judulCover: "Kesiapan Menuju Munaqosah",
        taglineCover: "Satu Tekad: 100% Lulus Munaqosah, Berangkat Wisuda Bersama",
        footerKiri: "Pertemuan Orang Tua & Santri",
        footerKanan: "Fokus Peningkatan & Perbaikan Materi",
        klinikThreshold: 4,
        polesanThreshold: 1,
        santriDisplayMode: 'full',
        maskSantriName: false,
        // Slide 7 Kendala
        k1_title: "Kerap Terburu-buru",
        k1_desc: "Santri sering membaca terlalu cepat sehingga makharijul huruf dan ketukan mad kurang konsisten.",
        k2_title: "Hafalan Sambung Ayat",
        k2_desc: "Ragu ketika penguji memotong bacaan di pertengahan ayat atau memanggil ayat acak.",
        k3_title: "Kaidah Sambung Huruf Imla'",
        k3_desc: "Penulisan huruf hijaiyah posisi tengah dan akhir kalimat masih sering tertukar.",
        // Slide 9 Langkah Aksi
        step1: { ctx: "TPQ", title: "Bimbingan Tambahan Maghrib", tag: "Ustadz Pembina Kelas 6", desc: "Fokus drill materi tajwid & kelancaran ayat-ayat panjang" },
        step2: { ctx: "TPQ", title: "Klinik Menulis & Imla' Khusus", tag: "Tim Pengajar Khat & Imla'", desc: "Pendampingan privat penulisan surah pendek dan kaidah sambung huruf" },
        step3: { ctx: "RUMAH", title: "Setoran Rutin Ba'da Subuh", tag: "Ayah / Bunda di Rumah", desc: "Murojaah 15 menit setiap pagi untuk menguatkan retensi hafalan" },
        step4: { ctx: "WALI SANTRI", title: "Buku Kendali & Komunikasi Guru", tag: "Wali Kelas & Orang Tua", desc: "Pemeriksaan paraf buku pantauan harian dan simulasi ujian berkala" },
        // Slide 12 Matriks Evaluasi
        matriks_title: "Matriks Evaluasi & Pemetaan Kekurangan Materi",
        matriks_subtitle: "Daftar Rekapitulasi Capaian Materi per Santri",
        matriks_filter: "kurang_only",
        matriks_show_score: true
    };

    // Storage Key per TPQ
    function getStorageKey() {
        const idTpq = $('#filterTpq').val() || '0';
        return 'prs_config_tpq_' + idTpq;
    }

    // Load config from localStorage
    function loadConfig() {
        try {
            const raw = localStorage.getItem(getStorageKey());
            if (raw) {
                TPQ_APP.config = Object.assign({}, DEFAULT_CONFIG, JSON.parse(raw));
            } else {
                TPQ_APP.config = Object.assign({}, DEFAULT_CONFIG);
            }
        } catch (e) {
            TPQ_APP.config = Object.assign({}, DEFAULT_CONFIG);
        }
        syncConfigInputs();
    }

    // Save config to localStorage
    function saveConfig() {
        TPQ_APP.config.namaTpq = $('#cfg_namaTpq').val() || DEFAULT_CONFIG.namaTpq;
        TPQ_APP.config.subtitleCover = $('#cfg_subtitleCover').val();
        TPQ_APP.config.judulCover = $('#cfg_judulCover').val();
        TPQ_APP.config.taglineCover = $('#cfg_taglineCover').val();
        TPQ_APP.config.footerKiri = $('#cfg_footerKiri').val();
        TPQ_APP.config.footerKanan = $('#cfg_footerKanan').val();
        TPQ_APP.config.klinikThreshold = parseInt($('#cfg_klinikThreshold').val(), 10) || 4;
        TPQ_APP.config.polesanThreshold = parseInt($('#cfg_polesanThreshold').val(), 10) || 1;
        TPQ_APP.config.santriDisplayMode = $('input[name="cfg_santriDisplayMode"]:checked').val() || 'full';
        TPQ_APP.config.maskSantriName = (TPQ_APP.config.santriDisplayMode === 'initial');

        TPQ_APP.config.k1_title = $('#cfg_k1_title').val();
        TPQ_APP.config.k1_desc = $('#cfg_k1_desc').val();
        TPQ_APP.config.k2_title = $('#cfg_k2_title').val();
        TPQ_APP.config.k2_desc = $('#cfg_k2_desc').val();
        TPQ_APP.config.k3_title = $('#cfg_k3_title').val();
        TPQ_APP.config.k3_desc = $('#cfg_k3_desc').val();

        TPQ_APP.config.step1 = { ctx: $('#cfg_step1_ctx').val(), title: $('#cfg_step1_title').val(), tag: $('#cfg_step1_tag').val(), desc: $('#cfg_step1_desc').val() };
        TPQ_APP.config.step2 = { ctx: $('#cfg_step2_ctx').val(), title: $('#cfg_step2_title').val(), tag: $('#cfg_step2_tag').val(), desc: $('#cfg_step2_desc').val() };
        TPQ_APP.config.step3 = { ctx: $('#cfg_step3_ctx').val(), title: $('#cfg_step3_title').val(), tag: $('#cfg_step3_tag').val(), desc: $('#cfg_step3_desc').val() };
        TPQ_APP.config.step4 = { ctx: $('#cfg_step4_ctx').val(), title: $('#cfg_step4_title').val(), tag: $('#cfg_step4_tag').val(), desc: $('#cfg_step4_desc').val() };

        // Slide 12 Matriks Config
        TPQ_APP.config.matriks_title = $('#cfg_matriks_title').val();
        TPQ_APP.config.matriks_subtitle = $('#cfg_matriks_subtitle').val();
        TPQ_APP.config.matriks_filter = $('#cfg_matriks_filter').val();
        TPQ_APP.config.matriks_show_score = $('#cfg_matriks_show_score').is(':checked');

        try {
            localStorage.setItem(getStorageKey(), JSON.stringify(TPQ_APP.config));
        } catch (e) {}

        toastr.success('Pengaturan presentasi berhasil disimpan!');
        $('#modalConfig').modal('hide');
        renderSlide(TPQ_APP.currentSlide);
    }

    function syncConfigInputs() {
        $('#cfg_namaTpq').val(TPQ_APP.config.namaTpq);
        $('#cfg_subtitleCover').val(TPQ_APP.config.subtitleCover);
        $('#cfg_judulCover').val(TPQ_APP.config.judulCover);
        $('#cfg_taglineCover').val(TPQ_APP.config.taglineCover);
        $('#cfg_footerKiri').val(TPQ_APP.config.footerKiri);
        $('#cfg_footerKanan').val(TPQ_APP.config.footerKanan);
        $('#cfg_klinikThreshold').val(TPQ_APP.config.klinikThreshold);
        $('#cfg_polesanThreshold').val(TPQ_APP.config.polesanThreshold);

        const curMode = TPQ_APP.config.santriDisplayMode || (TPQ_APP.config.maskSantriName ? 'initial' : 'full');
        $(`input[name="cfg_santriDisplayMode"][value="${curMode}"]`).prop('checked', true);

        $('#cfg_k1_title').val(TPQ_APP.config.k1_title);
        $('#cfg_k1_desc').val(TPQ_APP.config.k1_desc);
        $('#cfg_k2_title').val(TPQ_APP.config.k2_title);
        $('#cfg_k2_desc').val(TPQ_APP.config.k2_desc);
        $('#cfg_k3_title').val(TPQ_APP.config.k3_title);
        $('#cfg_k3_desc').val(TPQ_APP.config.k3_desc);

        if (TPQ_APP.config.step1) {
            $('#cfg_step1_ctx').val(TPQ_APP.config.step1.ctx);
            $('#cfg_step1_title').val(TPQ_APP.config.step1.title);
            $('#cfg_step1_tag').val(TPQ_APP.config.step1.tag);
            $('#cfg_step1_desc').val(TPQ_APP.config.step1.desc);
        }
        if (TPQ_APP.config.step2) {
            $('#cfg_step2_ctx').val(TPQ_APP.config.step2.ctx);
            $('#cfg_step2_title').val(TPQ_APP.config.step2.title);
            $('#cfg_step2_tag').val(TPQ_APP.config.step2.tag);
            $('#cfg_step2_desc').val(TPQ_APP.config.step2.desc);
        }
        if (TPQ_APP.config.step3) {
            $('#cfg_step3_ctx').val(TPQ_APP.config.step3.ctx);
            $('#cfg_step3_title').val(TPQ_APP.config.step3.title);
            $('#cfg_step3_tag').val(TPQ_APP.config.step3.tag);
            $('#cfg_step3_desc').val(TPQ_APP.config.step3.desc);
        }
        if (TPQ_APP.config.step4) {
            $('#cfg_step4_ctx').val(TPQ_APP.config.step4.ctx);
            $('#cfg_step4_title').val(TPQ_APP.config.step4.title);
            $('#cfg_step4_tag').val(TPQ_APP.config.step4.tag);
            $('#cfg_step4_desc').val(TPQ_APP.config.step4.desc);
        }

        // Slide 12 Matriks Config
        $('#cfg_matriks_title').val(TPQ_APP.config.matriks_title || DEFAULT_CONFIG.matriks_title);
        $('#cfg_matriks_subtitle').val(TPQ_APP.config.matriks_subtitle || DEFAULT_CONFIG.matriks_subtitle);
        $('#cfg_matriks_filter').val(TPQ_APP.config.matriks_filter || 'kurang_only');
        $('#cfg_matriks_show_score').prop('checked', TPQ_APP.config.matriks_show_score !== false);
    }

    // Parse thresholds
    function getThresholds() {
        const val = $('#filterThreshold').val() || '65, 70, 75';
        const parts = val.split(',').map(s => parseFloat(s.trim())).filter(n => !isNaN(n));
        if (parts.length >= 3) {
            return { list: parts, kkm: parts[0], cukup: parts[0], aman: parts[1], target: parts[2] };
        } else if (parts.length === 2) {
            return { list: parts, kkm: parts[0], cukup: parts[0], aman: parts[1], target: parts[1] };
        } else if (parts.length === 1) {
            return { list: [parts[0], parts[0] + 5], kkm: parts[0], cukup: parts[0], aman: parts[0] + 5, target: parts[0] + 5 };
        }
        return { list: [65, 70, 75], kkm: 65, cukup: 65, aman: 70, target: 75 };
    }

    // Format identitas santri (Full Nama, No Test Saja, Nama + No Test, Inisial)
    function formatSantriIdentity(s) {
        const mode = TPQ_APP.config.santriDisplayMode || 'full';
        const name = s.name || '-';
        const np = s.np || '-';

        if (mode === 'notest_only') {
            return `<span class="badge badge-light border border-danger text-danger font-weight-bold px-2 py-1" style="font-size:0.8rem; letter-spacing:0.5px;"><i class="fas fa-id-badge mr-1"></i>No. Test: ${np}</span>`;
        } else if (mode === 'name_notest') {
            return `<span class="text-dark font-weight-bold">${name}</span> <span class="badge badge-light border border-info text-info ml-1 px-1 font-weight-bold" style="font-size:0.7rem;"><i class="fas fa-hashtag"></i>${np}</span>`;
        } else if (mode === 'initial') {
            const masked = name.split(' ').map(word => {
                if (word.length <= 2) return word;
                return word[0] + '*'.repeat(Math.min(word.length - 1, 5));
            }).join(' ');
            return `<span class="text-dark font-weight-bold"><i class="fas fa-user-secret text-muted mr-1"></i>${masked}</span>`;
        } else {
            // 'full'
            return `<span class="text-dark font-weight-bold">${name}</span>`;
        }
    }

    // Fetch Data from DB
    function fetchStatistikData() {
        const tahun = $('#filterTahunAjaran').val();
        const idTpq = $('#filterTpq').val() || '0';
        const typeUjian = $('#filterTypeUjian').val() || 'pra-munaqosah';

        $('#prsCanvas').html(`
            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-2" role="status"></div>
                    <div class="font-weight-bold">Memuat Data Statistik dari Database...</div>
                    <div class="small">Tahun Ajaran: ${tahun} • Type: ${typeUjian}</div>
                </div>
            </div>
        `);

        $.ajax({
            url: '<?= base_url('backend/munaqosah/statistik-hasil-data') ?>',
            type: 'GET',
            data: {
                IdTahunAjaran: tahun,
                IdTpq: idTpq,
                TypeUjian: typeUjian
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            dataType: 'json',
            success: function(resp) {
                if (resp && resp.success && resp.data && resp.data.rows && resp.data.rows.length > 0) {
                    TPQ_APP.dataset = resp.data;
                    renderThumbnails();
                    renderSlide(TPQ_APP.currentSlide);
                } else {
                    const msg = (resp && resp.message) ? resp.message : `Belum ada data peserta untuk filter yang dipilih (T.A: ${tahun}, Type: ${typeUjian}).`;
                    $('#prsCanvas').html(`
                        <div class="d-flex align-items-center justify-content-center h-100 p-4 text-center">
                            <div class="alert alert-warning shadow-sm" style="max-width: 540px;">
                                <h5><i class="fas fa-exclamation-triangle mr-1"></i> Data Belum Ditemukan</h5>
                                <div>${msg}</div>
                                <div class="mt-2 small text-muted">Silakan periksa pilihan <strong>Type Ujian</strong> (Pra-Munaqosah atau Munaqosah) atau pilih TPQ yang sesuai.</div>
                            </div>
                        </div>
                    `);
                }
            },
            error: function(err) {
                $('#prsCanvas').html(`
                    <div class="d-flex align-items-center justify-content-center h-100 p-4 text-center">
                        <div class="alert alert-danger shadow-sm">
                            <i class="fas fa-times-circle mr-1"></i> Gagal memuat data dari server.
                        </div>
                    </div>
                `);
            }
        });
    }

    // ==================== SLIDE DEFINITIONS & THUMBNAILS ====================
    const SLIDE_TITLES = [
        "Cover / Pembuka",
        "Tujuan Evaluasi",
        "Standar Kelulusan",
        "Grafik Sebaran Nilai",
        "Modal Kelulusan (Kuat)",
        "Fokus Perbaikan (Lemah)",
        "Analisis Kendala",
        "Strategi Pendampingan",
        "Strategi Perbaikan",
        "Peran Wali Santri",
        "Peta Kebutuhan Santri",
        "Matriks Evaluasi Materi",
        "Penutup & Komitmen"
    ];

    function renderThumbnails() {
        let html = '';
        for (let i = 1; i <= TPQ_APP.totalSlides; i++) {
            const activeClass = i === TPQ_APP.currentSlide ? 'active' : '';
            html += `
                <div class="prs-thumb-card ${activeClass}" data-slide="${i}">
                    <span class="prs-thumb-num">${i}</span>
                    <span class="px-2">${SLIDE_TITLES[i - 1]}</span>
                </div>
            `;
        }
        $('#prsThumbStrip').html(html);
    }

    // Update Progress Bar & Counter
    function updateSlideNav() {
        $('#hudSlideCounter').text(`${TPQ_APP.currentSlide} / ${TPQ_APP.totalSlides}`);
        const pct = (TPQ_APP.currentSlide / TPQ_APP.totalSlides) * 100;
        $('#prsProgressFill').css('width', pct + '%');

        $('#prsThumbStrip .prs-thumb-card').removeClass('active');
        $(`#prsThumbStrip .prs-thumb-card[data-slide="${TPQ_APP.currentSlide}"]`).addClass('active');
    }

    // ==================== SLIDE RENDERERS ====================
    function renderSlide(slideNumber) {
        TPQ_APP.currentSlide = slideNumber;
        updateSlideNav();

        const canvas = $('#prsCanvas');
        canvas.removeClass('slide-theme-dark slide-theme-light prs-slide-animate');
        void canvas[0].offsetWidth; // force reflow for animation
        canvas.addClass('prs-slide-animate');

        // Destroy previous chart if any
        if (TPQ_APP.chartInstance) {
            TPQ_APP.chartInstance.destroy();
            TPQ_APP.chartInstance = null;
        }

        switch (slideNumber) {
            case 1: renderSlide1(canvas); break;
            case 2: renderSlide2(canvas); break;
            case 3: renderSlide3(canvas); break;
            case 4: renderSlide4(canvas); break;
            case 5: renderSlide5(canvas); break;
            case 6: renderSlide6(canvas); break;
            case 7: renderSlide7(canvas); break;
            case 8: renderSlide8(canvas); break;
            case 9: renderSlide9(canvas); break;
            case 10: renderSlide10(canvas); break;
            case 11: renderSlide11(canvas); break;
            case 12: renderSlide12(canvas); break;
            case 13: renderSlide13(canvas); break;
            default: renderSlide1(canvas); break;
        }
    }

    // SLIDE 1: Cover / Pembuka (Dark Theme)
    function renderSlide1(canvas) {
        canvas.addClass('slide-theme-dark');
        const cfg = TPQ_APP.config;
        const ta = $('#filterTahunAjaran').val();

        canvas.html(`
            <div class="slide-content-area text-center position-relative h-100 d-flex flex-column justify-content-between p-4 p-lg-5">
                <!-- Header / Logo -->
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="d-flex align-items-center gap-3 text-left">
                        <img src="${TPQ_APP.activeTpqLogo}" alt="Logo" style="height: 52px; object-fit: contain;" onerror="this.src='https://placehold.co/100x100/0284c7/ffffff?text=TPQ'">
                        <div class="ml-2">
                            <h5 class="mb-0 font-weight-bold text-white tracking-wide" style="font-family: var(--prs-font-serif);">${cfg.namaTpq}</h5>
                            <span class="text-info small font-weight-bold">Tahun Ajaran ${ta}</span>
                        </div>
                    </div>
                    <span class="prs-badge-soft bg-primary text-white px-3 py-1">
                        <i class="fas fa-certificate mr-1"></i> Munaqosah Smart
                    </span>
                </div>

                <!-- Center Hero / Title -->
                <div class="my-auto py-3">
                    <div class="slide-eyebrow text-info justify-content-center mb-2">
                        ${cfg.subtitleCover}
                    </div>
                    <h1 class="slide-title-serif font-weight-bold text-white mb-3" style="font-size: clamp(2rem, 4vw, 3.2rem);">
                        ${cfg.judulCover}
                    </h1>
                    <p class="lead text-slate-300 mx-auto font-weight-light" style="max-width: 820px; color: #94a3b8; font-size: clamp(1rem, 1.8vw, 1.3rem);">
                        "${cfg.taglineCover}"
                    </p>
                </div>

                <!-- Footer -->
                <div class="d-flex justify-content-between align-items-center w-100 pt-3 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
                    <div class="text-left text-muted small">
                        <i class="fas fa-users text-info mr-1"></i> ${cfg.footerKiri}
                    </div>
                    <div class="text-right text-muted small">
                        <i class="fas fa-bullseye text-warning mr-1"></i> ${cfg.footerKanan}
                    </div>
                </div>
            </div>
        `);
    }

    // SLIDE 2: Tujuan Evaluasi (Light Theme)
    function renderSlide2(canvas) {
        canvas.addClass('slide-theme-light');
        canvas.html(`
            <div class="slide-content-area h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="slide-eyebrow text-primary">Tujuan Evaluasi</div>
                    <h2 class="slide-title-serif text-dark font-weight-bold mb-1">Mengapa Ada Pra-Munaqosah?</h2>
                    <p class="text-muted small">Langkah strategis membimbing santri mencapai potensi terbaik sebelum ujian sesungguhnya</p>
                </div>

                <div class="row my-auto g-3">
                    <div class="col-4">
                        <div class="prs-card p-3 p-lg-4 h-100 text-center" style="border-top: 4px solid #ef4444;">
                            <div class="rounded-circle bg-danger text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; font-size: 1.4rem;">
                                <i class="fas fa-search"></i>
                            </div>
                            <h5 class="font-weight-bold mb-2">Cek Titik Lemah</h5>
                            <p class="text-muted small mb-0">Mendeteksi sedini mungkin aspek materi yang belum tuntas sebelum hari pelaksanaan munaqosah resmi.</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="prs-card p-3 p-lg-4 h-100 text-center" style="border-top: 4px solid #f59e0b;">
                            <div class="rounded-circle bg-warning text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; font-size: 1.4rem;">
                                <i class="fas fa-brain"></i>
                            </div>
                            <h5 class="font-weight-bold mb-2">Simulasi Mental</h5>
                            <p class="text-muted small mb-0">Membiasakan santri berhadapan langsung dengan tim juri penguji agar santai dan percaya diri tanpa demam panggung.</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="prs-card p-3 p-lg-4 h-100 text-center" style="border-top: 4px solid #10b981;">
                            <div class="rounded-circle bg-success text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; font-size: 1.4rem;">
                                <i class="fas fa-award"></i>
                            </div>
                            <h5 class="font-weight-bold mb-2">Garansi Wisuda</h5>
                            <p class="text-muted small mb-0">Memastikan seluruh ikhtiar santri dan dukungan wali santri bermuara pada kelulusan bersama 100%.</p>
                        </div>
                    </div>
                </div>

                <div class="text-muted small text-right border-top pt-2">
                    <i class="fas fa-info-circle text-primary mr-1"></i> Standar Evaluasi Terintegrasi Lembaga
                </div>
            </div>
        `);
    }

    // SLIDE 3: Standar Nilai Kelulusan (Light Theme)
    function renderSlide3(canvas) {
        canvas.addClass('slide-theme-light');
        const th = getThresholds();
        const isMultiLevel = th.list.length >= 3;
        const kkmVal = th.kkm;
        const amanVal = th.aman;
        const targetVal = isMultiLevel ? th.target : th.aman;

        let visualBarHtml = '';
        if (isMultiLevel) {
            visualBarHtml = `
                <div class="mb-2 d-flex justify-content-between font-weight-bold small flex-wrap">
                    <span class="text-danger">&lt; ${kkmVal} (Zona Rawan / Belum Tuntas)</span>
                    <span class="text-warning">${kkmVal} - ${(amanVal - 0.1).toFixed(1)} (Polesan / Cukup)</span>
                    <span class="text-primary">${amanVal} - ${(targetVal - 0.1).toFixed(1)} (Zona Aman)</span>
                    <span class="text-success">&ge; ${targetVal} (Zona Target Proteksi)</span>
                </div>
                <div class="progress" style="height: 38px; border-radius: 12px; overflow: hidden; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                    <div class="progress-bar bg-danger font-weight-bold" style="width: 25%;">
                        &lt; ${kkmVal} (Klinik)
                    </div>
                    <div class="progress-bar bg-warning text-dark font-weight-bold" style="width: 25%;">
                        ${kkmVal} - ${amanVal} (Poles)
                    </div>
                    <div class="progress-bar bg-primary font-weight-bold" style="width: 25%;">
                        ${amanVal} - ${targetVal} (Aman)
                    </div>
                    <div class="progress-bar bg-success font-weight-bold" style="width: 25%;">
                        &ge; ${targetVal} (Target TPQ)
                    </div>
                </div>
            `;
        } else {
            visualBarHtml = `
                <div class="mb-2 d-flex justify-content-between font-weight-bold small">
                    <span class="text-danger">&lt; ${kkmVal} (Zona Berbahaya / Belum Tuntas)</span>
                    <span class="text-warning">${kkmVal} - ${(amanVal - 0.1).toFixed(1)} (Zona Rawan / Cukup)</span>
                    <span class="text-success">&ge; ${amanVal} (Zona Aman / Tuntas Baik)</span>
                </div>
                <div class="progress" style="height: 38px; border-radius: 12px; overflow: hidden; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                    <div class="progress-bar bg-danger font-weight-bold" style="width: 33.3%;">
                        &lt; ${kkmVal} (Perlu Bimbingan Intensif)
                    </div>
                    <div class="progress-bar bg-warning text-dark font-weight-bold" style="width: 33.3%;">
                        ${kkmVal} - ${amanVal} (Polesan Ringan)
                    </div>
                    <div class="progress-bar bg-success font-weight-bold" style="width: 33.4%;">
                        &ge; ${amanVal} (Aman Kelulusan)
                    </div>
                </div>
            `;
        }

        canvas.html(`
            <div class="slide-content-area h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="slide-eyebrow text-primary">STANDAR NILAI KELULUSAN</div>
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h2 class="slide-title-serif text-dark font-weight-bold mb-1">Mengapa TPQ Menyoroti Nilai &lt; ${amanVal}?</h2>
                        <span class="prs-badge-soft bg-danger text-white"><i class="fas fa-shield-alt mr-1"></i> Proteksi Dini TPQ</span>
                    </div>
                    <p class="text-muted small">Menjamin santri tidak hanya sekadar lulus, melainkan memiliki pondasi bacaan dan hafalan yang kokoh</p>
                </div>

                <!-- Visual Bar Segments -->
                <div class="my-auto py-2">
                    ${visualBarHtml}
                </div>

                <!-- 3 Bottom Columns -->
                <div class="row g-3">
                    <div class="col-4">
                        <div class="prs-card p-3 text-center border-danger">
                            <span class="badge badge-danger mb-1 font-weight-bold">Batas KKM: ${kkmVal}</span>
                            <h6 class="font-weight-bold mt-1 text-danger">Zona Rawan Gagal</h6>
                            <p class="text-muted small mb-0">Santri dengan nilai &lt; ${kkmVal} belum memenuhi batas KKM dan wajib mengikuti klinik perbaikan intensif.</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="prs-card p-3 text-center border-warning">
                            <span class="badge badge-warning mb-1 font-weight-bold">Zona Tengah: ${kkmVal} - ${amanVal}</span>
                            <h6 class="font-weight-bold mt-1 text-warning">Zona Cukup / Rawan</h6>
                            <p class="text-muted small mb-0">Santri sudah mencapai batas minimal, namun rentan turun bila menghadapi demam panggung di meja juri.</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="prs-card p-3 text-center border-success">
                            <span class="badge badge-success mb-1 font-weight-bold">Target Aman: &ge; ${isMultiLevel ? targetVal : amanVal}</span>
                            <h6 class="font-weight-bold mt-1 text-success">Target Proteksi TPQ</h6>
                            <p class="text-muted small mb-0">Santri siap 100% dan terbukti menguasai kaidah tajwid, makharijul huruf, dan hafalan matang.</p>
                        </div>
                    </div>
                </div>

                <div class="text-muted small text-right border-top pt-2">
                    <i class="fas fa-check-circle text-success mr-1"></i> Kriteria Kelulusan Bersama
                </div>
            </div>
        `);
    }

    // SLIDE 4: Grafik Sebaran Nilai per Materi (Sepenuhnya dari DB dengan Ringkasan Aman & Perbaikan)
    function renderSlide4(canvas) {
        canvas.addClass('slide-theme-light');
        const th = getThresholds();
        const data = TPQ_APP.dataset;

        if (!data || !data.rows || !data.categories) {
            canvas.html('<div class="slide-content-area text-center py-5">Belum ada data tersedia</div>');
            return;
        }

        const totalSantri = data.rows.length;

        // Process category distribution
        const catStats = data.categories.map(cat => {
            let aman = 0, cukup = 0, kurang = 0;
            data.rows.forEach(r => {
                const score = r.avg ? r.avg[cat.id] : null;
                if (score !== undefined && score !== null) {
                    if (score >= th.aman) aman++;
                    else if (score >= th.kkm) cukup++;
                    else kurang++;
                }
            });
            return {
                id: cat.id,
                name: cat.name,
                aman: aman,
                cukup: cukup,
                kurang: kurang,
                total: aman + cukup + kurang
            };
        });

        // Cari materi dengan jumlah perbaikan terbanyak
        let worstCat = null;
        catStats.forEach(c => {
            if (!worstCat || c.kurang > worstCat.kurang) {
                worstCat = c;
            }
        });

        function getCategoryIcon(name) {
            const s = String(name || '').toLowerCase();
            if (s.includes('doa') || s.includes('adab')) return '🤲';
            if (s.includes('surah') || s.includes('surat') || s.includes('juz')) return '📖';
            if (s.includes('sholat') || s.includes('shalat')) return '🧎';
            if (s.includes('ayat')) return '☪️';
            if (s.includes('tulis') || s.includes('khat') || s.includes('imla')) return '✏️';
            if (s.includes('baca') || s.includes('tajwid') || s.includes('tilawah')) return '📕';
            if (s.includes('hafalan') || s.includes('tahfidz')) return '🧠';
            if (s.includes('fiqih') || s.includes('dinul')) return '🕌';
            return '📘';
        }

        const catCount = catStats.length;
        const itemMb = catCount >= 8 ? 'mb-1' : (catCount >= 6 ? 'mb-2' : (catCount >= 5 ? 'mb-2' : 'mb-3'));
        const itemPy = catCount >= 6 ? 'py-1 px-2' : 'py-1 px-2';
        const barHeight = catCount >= 8 ? '12px' : (catCount >= 6 ? '14px' : (catCount >= 5 ? '16px' : '18px'));
        const titleSize = catCount >= 8 ? '0.84rem' : (catCount >= 6 ? '0.88rem' : '0.94rem');
        const sumSize = catCount >= 8 ? '0.78rem' : (catCount >= 6 ? '0.82rem' : '0.86rem');
        const iconSize = catCount >= 8 ? '0.95rem' : '1.05rem';

        const barRowsHtml = catStats.map(c => {
            const icon = getCategoryIcon(c.name);
            const total = c.aman + c.cukup + c.kurang;
            const pctAman = total > 0 ? Math.round((c.aman / total) * 100) : 0;
            const pctCukup = total > 0 ? Math.round((c.cukup / total) * 100) : 0;
            const pctKurang = total > 0 ? Math.max(0, 100 - pctAman - pctCukup) : 0;

            // Logika label ringkasan persis seperti halaman statistik
            let p1Text = '';
            let p2Text = '';
            if (c.kurang === 0) {
                p1Text = `${c.aman} Santri Aman (100%)`;
                p2Text = `0 Perbaikan`;
            } else if (pctAman >= 45 || c.aman >= c.kurang) {
                p1Text = `${c.aman} Santri Aman (${pctAman}%)`;
                p2Text = `${c.kurang} Perbaikan`;
            } else {
                p1Text = `${c.aman} Santri Aman`;
                p2Text = `${c.kurang} Perbaikan (${pctKurang}%)`;
            }

            return `
                <div class="${itemMb} prs-stat-bar-item ${itemPy} rounded" style="transition: background 0.15s ease;" onmouseenter="this.style.background='#f8fafc'" onmouseleave="this.style.background='transparent'">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="font-weight-bold text-dark d-flex align-items-center text-truncate mr-2" style="font-size: ${titleSize}; cursor: pointer;" onclick="openMateriSantriModal('${c.id}', 'all')" title="Klik untuk melihat rincian santri">
                            <span class="mr-2" style="font-size: ${iconSize};">${icon}</span>
                            <span class="text-truncate" style="max-width: 250px;">${c.name}</span>
                        </div>
                        <div class="text-right flex-shrink-0" style="font-size: ${sumSize}; cursor: pointer;" onclick="openMateriSantriModal('${c.id}', 'all')" title="Klik untuk melihat rincian santri">
                            <span class="font-weight-bold" style="color: #16a34a;">${p1Text}</span>
                            <span class="mx-1" style="color: #94a3b8; font-weight: bold;">•</span>
                            <span class="font-weight-bold" style="color: ${c.kurang > 0 ? '#dc2626' : '#94a3b8'};">${p2Text}</span>
                        </div>
                    </div>
                    <div class="progress" style="height: ${barHeight}; border-radius: 99px; background: #e2e8f0; overflow: hidden; box-shadow: inset 0 1px 2px rgba(0,0,0,0.06); cursor: pointer;" title="Klik untuk melihat rincian santri">
                        ${pctAman > 0 ? `<div class="progress-bar" role="progressbar" onclick="event.stopPropagation(); openMateriSantriModal('${c.id}', 'aman');" style="width: ${pctAman}%; background: #16a34a;" title="Aman (&ge;${th.aman}): ${c.aman} Santri (${pctAman}%) • Klik untuk rincian"></div>` : ''}
                        ${pctCukup > 0 ? `<div class="progress-bar" role="progressbar" onclick="event.stopPropagation(); openMateriSantriModal('${c.id}', 'cukup');" style="width: ${pctCukup}%; background: #f59e0b;" title="Cukup (${th.kkm} - ${th.aman}): ${c.cukup} Santri (${pctCukup}%) • Klik untuk rincian"></div>` : ''}
                        ${pctKurang > 0 ? `<div class="progress-bar" role="progressbar" onclick="event.stopPropagation(); openMateriSantriModal('${c.id}', 'kurang');" style="width: ${pctKurang}%; background: #ef4444;" title="Kurang (<${th.kkm}): ${c.kurang} Santri (${pctKurang}%) • Klik untuk rincian"></div>` : ''}
                    </div>
                </div>
            `;
        }).join('');

        canvas.html(`
            <div class="slide-content-area h-100 d-flex flex-column justify-content-between p-3 p-lg-4" style="overflow: visible;">
                <div>
                    <div class="slide-eyebrow text-primary">GRAFIK SEBARAN NILAI</div>
                    <div class="d-flex justify-content-between align-items-baseline mb-2 flex-wrap">
                        <h2 class="slide-title-serif text-dark font-weight-bold mb-1">Sebaran Capaian ${totalSantri} Santri per Materi</h2>
                        <div class="d-flex gap-2">
                            <span class="badge badge-success px-2 py-1"><i class="fas fa-circle mr-1" style="font-size:0.5rem; vertical-align:middle;"></i> Aman (&ge;${th.aman})</span>
                            <span class="badge badge-warning px-2 py-1 ml-1"><i class="fas fa-circle mr-1" style="font-size:0.5rem; vertical-align:middle;"></i> Cukup (${th.kkm}-${th.aman})</span>
                            <span class="badge badge-danger px-2 py-1 ml-1"><i class="fas fa-circle mr-1" style="font-size:0.5rem; vertical-align:middle;"></i> Kurang (&lt;${th.kkm})</span>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center my-auto w-100 mx-0">
                    <!-- Kolom Kiri: Baris Sebaran per Materi dengan Ringkasan Aman & Perbaikan (Semua Tampil Penuh Tanpa Scroll) -->
                    <div class="col-8 px-2">
                        <div class="w-100" style="overflow: visible;">
                            ${barRowsHtml}
                        </div>
                    </div>

                    <!-- Kolom Kanan: Rangkuman Prioritas & Keterangan -->
                    <div class="col-4 px-2">
                        <div class="prs-card p-2 p-xl-3 mb-2" style="border-left: 4px solid #16a34a;">
                            <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 0.72rem;">TOTAL SANTRI DIUJI</div>
                            <div class="h3 font-weight-bold text-dark mb-0">${totalSantri} Santri</div>
                            <div class="small text-muted mt-1">${catStats.length} Materi Ujian Dinilai</div>
                        </div>

                        ${worstCat && worstCat.kurang > 0 ? `
                        <div class="prs-card p-2 p-xl-3 mb-2" style="border-left: 4px solid #ef4444; background: #fff5f5;">
                            <div class="small text-danger font-weight-bold text-uppercase" style="font-size: 0.72rem;"><i class="fas fa-exclamation-triangle mr-1"></i>Fokus Pembinaan Utama</div>
                            <div class="font-weight-bold text-dark mt-1">${worstCat.name}</div>
                            <div class="small text-danger font-weight-bold mt-1">${worstCat.kurang} Santri Butuh Perbaikan</div>
                            <div class="small text-muted mt-1" style="font-size: 0.78rem;">Prioritas pendampingan sebelum munaqosah.</div>
                        </div>
                        ` : `
                        <div class="prs-card p-2 p-xl-3 mb-2" style="border-left: 4px solid #16a34a; background: #f0fdf4;">
                            <div class="small text-success font-weight-bold text-uppercase" style="font-size: 0.72rem;"><i class="fas fa-check-circle mr-1"></i>Status Capaian</div>
                            <div class="font-weight-bold text-dark mt-1">Pencapaian Sangat Baik</div>
                            <div class="small text-muted mt-1" style="font-size: 0.78rem;">Seluruh santri memenuhi ambang kelulusan.</div>
                        </div>
                        `}

                        <div class="prs-card p-2 p-xl-3 bg-light border mb-2">
                            <div class="small font-weight-bold text-dark mb-1" style="font-size: 0.76rem;"><i class="fas fa-layer-group text-primary mr-1"></i> Standar Kategori:</div>
                            <div class="d-flex justify-content-between align-items-center mb-1 small" style="font-size: 0.78rem;">
                                <span><i class="fas fa-circle mr-1" style="color: #16a34a; font-size: 0.5rem;"></i>Aman</span>
                                <span class="font-weight-bold text-success">&ge; ${th.aman}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1 small" style="font-size: 0.78rem;">
                                <span><i class="fas fa-circle mr-1" style="color: #f59e0b; font-size: 0.5rem;"></i>Cukup</span>
                                <span class="font-weight-bold text-warning">${th.kkm} - ${th.aman}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small" style="font-size: 0.78rem;">
                                <span><i class="fas fa-circle mr-1" style="color: #ef4444; font-size: 0.5rem;"></i>Perbaikan</span>
                                <span class="font-weight-bold text-danger">&lt; ${th.kkm}</span>
                            </div>
                        </div>

                        <div class="small text-muted text-center" style="font-size: 0.75rem;">
                            <i class="fas fa-hand-pointer text-primary mr-1"></i> Klik pada batang untuk rincian santri
                        </div>
                    </div>
                </div>

                <div class="text-muted small text-right border-top pt-2">
                    <i class="fas fa-chart-bar text-primary mr-1"></i> Visualisasi Hasil Monitoring Terpadu
                </div>
            </div>
        `);
    }

    // State & Handler Modal Rincian Santri per Materi (Slide 4 Drill-down)
    let currentMateriDetail = {
        catId: null,
        catName: '',
        filter: 'all',
        searchTerm: '',
        items: []
    };

    window.openMateriSantriModal = function(catId, filterType) {
        const data = TPQ_APP.dataset;
        const th = getThresholds();
        if (!data || !data.rows || !data.categories) return;

        const cat = data.categories.find(c => String(c.id) === String(catId));
        if (!cat) return;

        const items = [];
        data.rows.forEach(r => {
            const score = r.avg ? r.avg[cat.id] : null;
            if (score !== undefined && score !== null) {
                const sVal = Number(score) || 0;
                let status = 'kurang';
                if (sVal >= th.aman) status = 'aman';
                else if (sVal >= th.kkm) status = 'cukup';

                items.push({
                    np: r.np,
                    nm: r.nm,
                    scores: r.avg || {},
                    score: sVal,
                    status: status
                });
            }
        });

        // Urutkan nilai dari terendah ke tertinggi (prioritas santri butuh bimbingan)
        items.sort((a, b) => a.score - b.score);

        currentMateriDetail = {
            catId: cat.id,
            catName: cat.name,
            filter: filterType || 'all',
            searchTerm: '',
            items: items
        };

        const countAman = items.filter(i => i.status === 'aman').length;
        const countCukup = items.filter(i => i.status === 'cukup').length;
        const countKurang = items.filter(i => i.status === 'kurang').length;

        $('#mdsmTitle').html(`<i class="fas fa-book-reader text-primary mr-2"></i> ${cat.name} • Rincian Santri`);
        $('#mdsmSubtitle').text(`${items.length} Santri Dinilai • Standar Aman: ≥${th.aman}, KKM: ≥${th.kkm}`);
        $('#mdsmCountAll').text(items.length);
        $('#mdsmCountAman').text(countAman);
        $('#mdsmCountCukup').text(countCukup);
        $('#mdsmCountKurang').text(countKurang);
        $('#mdsmSearchInput').val('');

        updateMateriDetailFilterUI(currentMateriDetail.filter);
        renderMateriDetailTable();
        $('#modalDetailSantriMateri').modal('show');
    };

    function updateMateriDetailFilterUI(activeFilter) {
        $('.btn-filter-materi-status').each(function() {
            const f = $(this).data('filter');
            $(this).removeClass('btn-primary btn-success btn-warning btn-danger active').addClass('btn-outline-secondary');
            if (f === activeFilter) {
                $(this).removeClass('btn-outline-secondary').addClass('active');
                if (f === 'all') $(this).addClass('btn-primary');
                else if (f === 'aman') $(this).addClass('btn-success');
                else if (f === 'cukup') $(this).addClass('btn-warning text-dark');
                else if (f === 'kurang') $(this).addClass('btn-danger');
            }
        });
    }

    function renderMateriDetailTable() {
        const { items, filter, searchTerm } = currentMateriDetail;
        const term = (searchTerm || '').toLowerCase();

        const filtered = items.filter(i => {
            if (filter !== 'all' && i.status !== filter) return false;
            if (term && !i.nm.toLowerCase().includes(term) && !String(i.np || '').toLowerCase().includes(term)) return false;
            return true;
        });

        if (!filtered.length) {
            $('#mdsmTableBody').html(`
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        <i class="fas fa-info-circle mr-1"></i> Tidak ada data santri yang cocok dengan filter / pencarian.
                    </td>
                </tr>
            `);
            $('#mdsmFooterInfo').text('Menampilkan 0 santri');
            return;
        }

        const rowsHtml = filtered.map((s, idx) => {
            let badge = '';
            if (s.status === 'aman') {
                badge = `<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>Aman</span>`;
            } else if (s.status === 'cukup') {
                badge = `<span class="badge badge-warning px-2 py-1"><i class="fas fa-exclamation-circle mr-1"></i>Cukup</span>`;
            } else {
                badge = `<span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i>Perbaikan</span>`;
            }

            const valColor = (s.status === 'kurang') ? '#dc2626' : (s.status === 'aman' ? '#16a34a' : '#d97706');

            return `
                <tr>
                    <td class="text-center text-muted align-middle">${idx + 1}</td>
                    <td class="align-middle">${formatSantriIdentity(s)}</td>
                    <td class="text-center align-middle font-weight-bold" style="color: ${valColor}; font-size: 0.95rem;">
                        ${s.score.toFixed(2)}
                    </td>
                    <td class="text-center align-middle">${badge}</td>
                </tr>
            `;
        }).join('');

        $('#mdsmTableBody').html(rowsHtml);
        $('#mdsmFooterInfo').text(`Menampilkan ${filtered.length} dari total ${items.length} santri`);
    }

    // Helper: Hitung rata-rata & tuntas per materi
    function getMateriStats() {
        const th = getThresholds();
        const data = TPQ_APP.dataset;
        if (!data || !data.rows || !data.categories) return [];

        return data.categories.map(cat => {
            let totalNilai = 0, count = 0, tuntas = 0, kurang = 0;
            data.rows.forEach(r => {
                const score = r.avg ? r.avg[cat.id] : null;
                if (score !== undefined && score !== null) {
                    totalNilai += score;
                    count++;
                    if (score >= th.kkm) tuntas++;
                    else kurang++;
                }
            });
            const avg = count > 0 ? (totalNilai / count) : 0;
            const pct = count > 0 ? ((tuntas / count) * 100) : 0;
            return {
                id: cat.id,
                name: cat.name,
                avg: avg.toFixed(1),
                tuntasCount: tuntas,
                kurangCount: kurang,
                totalCount: count,
                pct: pct.toFixed(0)
            };
        });
    }

    // SLIDE 5: Modal Kelulusan (Materi Terkuat dari DB)
    function renderSlide5(canvas) {
        canvas.addClass('slide-theme-light');
        const stats = getMateriStats().sort((a, b) => b.pct - a.pct || b.avg - a.avg);
        const topMateri = stats.slice(0, 3);

        let cardsHtml = '';
        topMateri.forEach((m, idx) => {
            cardsHtml += `
                <div class="col-4">
                    <div class="prs-card p-3 p-lg-4 text-center h-100" style="border-top: 4px solid #10b981;">
                        <span class="badge badge-success px-2 py-1 mb-2">Peringkat ${idx + 1}</span>
                        <h4 class="font-weight-bold mb-1">${m.name}</h4>
                        <div class="display-4 font-weight-bold text-success my-2" style="font-size: 2.4rem;">${m.pct}%</div>
                        <div class="small font-weight-bold text-dark mb-1">${m.tuntasCount} dari ${m.totalCount} Santri Tuntas KKM</div>
                        <p class="text-muted small mb-0">Rata-rata nilai mencapai <strong>${m.avg}</strong>. Modal yang sangat kuat menuju kelulusan sempurna.</p>
                    </div>
                </div>
            `;
        });

        canvas.html(`
            <div class="slide-content-area h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="slide-eyebrow text-success">MODAL KELULUSAN</div>
                    <h2 class="slide-title-serif text-dark font-weight-bold mb-1">${topMateri.length} Materi yang Sudah Dikuasai Baik</h2>
                    <p class="text-muted small">Capaian membanggakan santri yang siap dipertahankan hingga hari kelulusan</p>
                </div>

                <div class="row my-auto g-3">
                    ${cardsHtml}
                </div>

                <div class="text-muted small text-right border-top pt-2">
                    <i class="fas fa-trophy text-success mr-1"></i> Keunggulan Akademik Santri TPQ
                </div>
            </div>
        `);
    }

    // SLIDE 6: Fokus Perbaikan (Materi Terlemah dari DB)
    function renderSlide6(canvas) {
        canvas.addClass('slide-theme-light');
        const stats = getMateriStats().sort((a, b) => b.kurangCount - a.kurangCount || a.avg - b.avg);
        const bottomMateri = stats.filter(m => m.kurangCount > 0).slice(0, 3);

        let cardsHtml = '';
        bottomMateri.forEach((m, idx) => {
            cardsHtml += `
                <div class="col-4">
                    <div class="prs-card p-3 p-lg-4 text-center h-100" style="border-top: 4px solid #ef4444; background: #fff5f5;">
                        <span class="badge badge-danger px-2 py-1 mb-2">Fokus Prioritas ${idx + 1}</span>
                        <h4 class="font-weight-bold mb-1 text-danger">${m.name}</h4>
                        <div class="display-4 font-weight-bold text-danger my-2" style="font-size: 2.4rem;">${m.kurangCount}</div>
                        <div class="small font-weight-bold text-dark mb-1">Santri Belum Tuntas KKM</div>
                        <p class="text-muted small mb-0">Rata-rata materi: <strong>${m.avg}</strong>. Wajib mendapatkan perhatian drill bersama ustadz dan orang tua.</p>
                    </div>
                </div>
            `;
        });

        if (bottomMateri.length === 0) {
            cardsHtml = '<div class="col-12 text-center text-success py-5"><h4><i class="fas fa-check-circle mr-1"></i> Masya Allah! Seluruh materi telah melampaui batas KKM.</h4></div>';
        }

        canvas.html(`
            <div class="slide-content-area h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="slide-eyebrow text-danger">FOKUS PERBAIKAN</div>
                    <h2 class="slide-title-serif text-dark font-weight-bold mb-1">${bottomMateri.length} Materi yang Wajib Diperbaiki Bersama</h2>
                    <p class="text-muted small">Titik fokus bimbingan intensif agar seluruh santri tuntas sebelum ujian munaqosah resmi</p>
                </div>

                <div class="row my-auto g-3">
                    ${cardsHtml}
                </div>

                <div class="text-muted small text-right border-top pt-2">
                    <i class="fas fa-exclamation-triangle text-danger mr-1"></i> Rencana Aksi Perbaikan Terjadwal
                </div>
            </div>
        `);
    }

    // SLIDE 7: Analisis Kendala (Manual Operator Preset)
    function renderSlide7(canvas) {
        canvas.addClass('slide-theme-light');
        const cfg = TPQ_APP.config;

        canvas.html(`
            <div class="slide-content-area h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="slide-eyebrow text-warning">ANALISIS KENDALA</div>
                    <h2 class="slide-title-serif text-dark font-weight-bold mb-1">Apa Saja yang Perlu Dilatih Ulang?</h2>
                    <p class="text-muted small">Temuan observasi langsung para ustadz penguji saat simulasi munaqosah</p>
                </div>

                <div class="row my-auto g-3">
                    <div class="col-4">
                        <div class="prs-card p-3 p-lg-4 text-center h-100" style="border-top: 4px solid #f97316;">
                            <div class="rounded-circle text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; font-size: 1.3rem; background: #f97316;">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <h5 class="font-weight-bold mb-2">${cfg.k1_title}</h5>
                            <p class="text-muted small mb-0">${cfg.k1_desc}</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="prs-card p-3 p-lg-4 text-center h-100" style="border-top: 4px solid #f59e0b;">
                            <div class="rounded-circle text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; font-size: 1.3rem; background: #f59e0b;">
                                <i class="fas fa-link"></i>
                            </div>
                            <h5 class="font-weight-bold mb-2">${cfg.k2_title}</h5>
                            <p class="text-muted small mb-0">${cfg.k2_desc}</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="prs-card p-3 p-lg-4 text-center h-100" style="border-top: 4px solid #0284c7;">
                            <div class="rounded-circle text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; font-size: 1.3rem; background: #0284c7;">
                                <i class="fas fa-pen-fancy"></i>
                            </div>
                            <h5 class="font-weight-bold mb-2">${cfg.k3_title}</h5>
                            <p class="text-muted small mb-0">${cfg.k3_desc}</p>
                        </div>
                    </div>
                </div>

                <div class="text-muted small text-right border-top pt-2">
                    <i class="fas fa-clipboard-check text-warning mr-1"></i> Observasi Langsung Tim Penguji
                </div>
            </div>
        `);
    }

    // SLIDE 8: Strategi Pendampingan (Pengelompokan Santri dari DB)
    function renderSlide8(canvas) {
        canvas.addClass('slide-theme-light');
        const th = getThresholds();
        const data = TPQ_APP.dataset;

        let siapCount = 0, polesCount = 0, klinikCount = 0;

        if (data && data.rows) {
            data.rows.forEach(r => {
                let belowKkm = 0, inZoneRawan = 0;
                Object.values(r.avg || {}).forEach(score => {
                    if (score < th.kkm) belowKkm++;
                    else if (score < th.aman) inZoneRawan++;
                });

                if (belowKkm >= 1) {
                    klinikCount++;
                } else if (inZoneRawan >= 1) {
                    polesCount++;
                } else {
                    siapCount++;
                }
            });
        }

        canvas.html(`
            <div class="slide-content-area h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="slide-eyebrow text-primary">STRATEGI PENDAMPINGAN</div>
                    <h2 class="slide-title-serif text-dark font-weight-bold mb-1">Rencana Bimbingan Menurut Kebutuhan</h2>
                    <p class="text-muted small">Pendekatan terarah agar waktu bimbingan efektif dan tepat sasaran bagi setiap santri</p>
                </div>

                <div class="row my-auto g-3">
                    <div class="col-4">
                        <div class="prs-card p-3 p-lg-4 text-center h-100" style="border-top: 4px solid #10b981;">
                            <span class="badge badge-success px-3 py-1 mb-2 font-weight-bold">${siapCount} SANTRI</span>
                            <h4 class="font-weight-bold mb-2 text-success">Kelompok Siap</h4>
                            <p class="text-muted small mb-0">Seluruh materi sudah &ge; ${th.aman}. Pendampingan difokuskan pada penguatan mental &amp; ketenangan saat tampil.</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="prs-card p-3 p-lg-4 text-center h-100" style="border-top: 4px solid #f59e0b;">
                            <span class="badge badge-warning px-3 py-1 mb-2 font-weight-bold">${polesCount} SANTRI</span>
                            <h4 class="font-weight-bold mb-2 text-warning">Kelompok Poles</h4>
                            <p class="text-muted small mb-0">Materi di zona cukup (${th.kkm}-${th.aman}). Bimbingan berupa pemantapan tajwid &amp; murojaah surah pilihan.</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="prs-card p-3 p-lg-4 text-center h-100" style="border-top: 4px solid #ef4444;">
                            <span class="badge badge-danger px-3 py-1 mb-2 font-weight-bold">${klinikCount} SANTRI</span>
                            <h4 class="font-weight-bold mb-2 text-danger">Klinik Khusus</h4>
                            <p class="text-muted small mb-0">Terdapat nilai di bawah KKM (&lt; ${th.kkm}). Pendampingan privat tatap muka bersama asatidz pembina.</p>
                        </div>
                    </div>
                </div>

                <div class="text-muted small text-right border-top pt-2">
                    <i class="fas fa-user-friends text-primary mr-1"></i> Pembagian Kelompok Bimbingan Belajar
                </div>
            </div>
        `);
    }

    // SLIDE 9: Strategi Perbaikan (4 Langkah Aksi)
    function renderSlide9(canvas) {
        canvas.addClass('slide-theme-light');
        const cfg = TPQ_APP.config;

        canvas.html(`
            <div class="slide-content-area h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="slide-eyebrow text-primary">STRATEGI PERBAIKAN</div>
                    <h2 class="slide-title-serif text-dark font-weight-bold mb-1">4 Langkah Nyata Menuju Lulus 100%</h2>
                    <p class="text-muted small">Kolaborasi sinergis antara TPQ, santri di rumah, dan bimbingan orang tua</p>
                </div>

                <div class="row my-auto g-2">
                    <div class="col-3">
                        <div class="prs-card p-3 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #0284c7;">
                            <div>
                                <span class="badge badge-primary px-2 py-1 mb-2">01 • ${cfg.step1.ctx}</span>
                                <h6 class="font-weight-bold mb-1">${cfg.step1.title}</h6>
                                <p class="text-muted small mb-2">${cfg.step1.desc}</p>
                            </div>
                            <span class="badge badge-light border text-primary small text-truncate" title="${cfg.step1.tag}">
                                <i class="fas fa-user-tie mr-1"></i> ${cfg.step1.tag}
                            </span>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="prs-card p-3 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #10b981;">
                            <div>
                                <span class="badge badge-success px-2 py-1 mb-2">02 • ${cfg.step2.ctx}</span>
                                <h6 class="font-weight-bold mb-1">${cfg.step2.title}</h6>
                                <p class="text-muted small mb-2">${cfg.step2.desc}</p>
                            </div>
                            <span class="badge badge-light border text-success small text-truncate" title="${cfg.step2.tag}">
                                <i class="fas fa-user-tie mr-1"></i> ${cfg.step2.tag}
                            </span>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="prs-card p-3 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #f59e0b;">
                            <div>
                                <span class="badge badge-warning px-2 py-1 mb-2 text-dark">03 • ${cfg.step3.ctx}</span>
                                <h6 class="font-weight-bold mb-1">${cfg.step3.title}</h6>
                                <p class="text-muted small mb-2">${cfg.step3.desc}</p>
                            </div>
                            <span class="badge badge-light border text-warning small text-truncate" title="${cfg.step3.tag}">
                                <i class="fas fa-home mr-1"></i> ${cfg.step3.tag}
                            </span>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="prs-card p-3 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid #8b5cf6;">
                            <div>
                                <span class="badge badge-purple px-2 py-1 mb-2 text-white" style="background:#8b5cf6;">04 • ${cfg.step4.ctx}</span>
                                <h6 class="font-weight-bold mb-1">${cfg.step4.title}</h6>
                                <p class="text-muted small mb-2">${cfg.step4.desc}</p>
                            </div>
                            <span class="badge badge-light border text-purple small text-truncate" title="${cfg.step4.tag}">
                                <i class="fas fa-clipboard-check mr-1"></i> ${cfg.step4.tag}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="text-muted small text-right border-top pt-2">
                    <i class="fas fa-calendar-check text-primary mr-1"></i> Jadwal &amp; Penanggung Jawab Terpadu
                </div>
            </div>
        `);
    }

    // SLIDE 10: Peran Wali Santri
    function renderSlide10(canvas) {
        canvas.addClass('slide-theme-light');
        canvas.html(`
            <div class="slide-content-area h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="slide-eyebrow text-primary">PERAN PENTING WALI SANTRI</div>
                    <h2 class="slide-title-serif text-dark font-weight-bold mb-1">Sinergi Ayah Bunda Mengawal Kelulusan</h2>
                    <p class="text-muted small">Dukungan terbaik di rumah adalah kunci terkuat ketenangan dan kelancaran ujian ananda</p>
                </div>

                <div class="row my-auto g-3">
                    <div class="col-4">
                        <div class="prs-card p-3 p-lg-4 text-center h-100" style="border-top: 4px solid #0284c7;">
                            <div class="rounded-circle text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; font-size: 1.3rem; background: #0284c7;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h5 class="font-weight-bold mb-2">Kawal Jam Tambahan</h5>
                            <p class="text-muted small mb-0">Memastikan ananda hadir tepat waktu dan disiplin mengikuti jadwal klinik khusus di TPQ.</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="prs-card p-3 p-lg-4 text-center h-100" style="border-top: 4px solid #f59e0b;">
                            <div class="rounded-circle text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; font-size: 1.3rem; background: #f59e0b;">
                                <i class="fas fa-pencil-alt"></i>
                            </div>
                            <h5 class="font-weight-bold mb-2">Cek Menulis &amp; Hafalan</h5>
                            <p class="text-muted small mb-0">Mendampingi ananda latihan menulis surah pendek serta mendengarkan setoran hafalan sebelum tidur.</p>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="prs-card p-3 p-lg-4 text-center h-100" style="border-top: 4px solid #10b981;">
                            <div class="rounded-circle text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; font-size: 1.3rem; background: #10b981;">
                                <i class="fas fa-heart"></i>
                            </div>
                            <h5 class="font-weight-bold mb-2">Semangat &amp; Doa Terbaik</h5>
                            <p class="text-muted small mb-0">Menghadirkan suasana tenang dan penuh kasih sayang. Hindari membebani agar ananda percaya diri.</p>
                        </div>
                    </div>
                </div>

                <div class="text-muted small text-right border-top pt-2">
                    <i class="fas fa-praying-hands text-success mr-1"></i> Keberkahan Ridho Orang Tua
                </div>
            </div>
        `);
    }

    // SLIDE 11: Peta Kebutuhan Santri (Daftar Santri Program Penguatan dari DB)
    function renderSlide11(canvas) {
        canvas.addClass('slide-theme-light');
        const th = getThresholds();
        const cfg = TPQ_APP.config;
        const data = TPQ_APP.dataset;

        const catMap = {};
        if (data && data.categories) {
            data.categories.forEach(c => { catMap[c.id] = c.name; });
        }

        const klinikList = [];
        const penguatanList = [];
        const polesanList = [];

        if (data && data.rows) {
            data.rows.forEach(r => {
                const weakSubjects = [];
                Object.entries(r.avg || {}).forEach(([catId, score]) => {
                    if (score < th.kkm) {
                        weakSubjects.push(catMap[catId] || 'Materi');
                    }
                });

                if (weakSubjects.length >= cfg.klinikThreshold) {
                    klinikList.push({ name: r.nm, np: r.np, subjects: weakSubjects });
                } else if (weakSubjects.length > cfg.polesanThreshold) {
                    penguatanList.push({ name: r.nm, np: r.np, subjects: weakSubjects });
                } else if (weakSubjects.length >= 1) {
                    polesanList.push({ name: r.nm, np: r.np, subjects: weakSubjects });
                }
            });
        }

        const totalPerluPenguatan = klinikList.length + penguatanList.length + polesanList.length;

        function renderSantriCards(list, badgeColor) {
            if (!list || list.length === 0) {
                return '<div class="text-center text-muted small py-3"><i class="fas fa-check-circle text-success mr-1"></i> Tidak ada santri di kelompok ini</div>';
            }
            return list.map(s => `
                <div class="prs-card p-2 mb-2 bg-white shadow-none border">
                    <div class="small mb-1">${formatSantriIdentity(s)}</div>
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        ${s.subjects.map(subj => `<span class="badge ${badgeColor} mr-1" style="font-size:0.65rem;">${subj}</span>`).join('')}
                    </div>
                </div>
            `).join('');
        }

        const curMode = cfg.santriDisplayMode || 'full';

        canvas.html(`
            <div class="slide-content-area h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="slide-eyebrow text-primary">PETA KEBUTUHAN SANTRI</div>
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                        <h2 class="slide-title-serif text-dark font-weight-bold mb-0">Daftar Santri untuk Program Penguatan</h2>
                        <div class="d-flex align-items-center flex-wrap mt-1">
                            <!-- Quick Switcher Format Identitas Langsung di Slide 11 -->
                            <div class="btn-group btn-group-sm mr-2 shadow-sm" role="group" aria-label="Format Identitas">
                                <button type="button" class="btn btn-sm ${curMode === 'full' ? 'btn-primary font-weight-bold' : 'btn-light border text-secondary'} btn-switch-ident" data-mode="full" title="Tampilkan Full Nama Saja">
                                    <i class="fas fa-user mr-1"></i> Full Nama
                                </button>
                                <button type="button" class="btn btn-sm ${curMode === 'notest_only' ? 'btn-danger font-weight-bold' : 'btn-light border text-secondary'} btn-switch-ident" data-mode="notest_only" title="Hanya Tampilkan Nomor Test">
                                    <i class="fas fa-id-badge mr-1"></i> Hanya No. Test
                                </button>
                                <button type="button" class="btn btn-sm ${curMode === 'name_notest' ? 'btn-info text-white font-weight-bold' : 'btn-light border text-secondary'} btn-switch-ident" data-mode="name_notest" title="Tampilkan Full Nama dan No. Test">
                                    <i class="fas fa-address-card mr-1"></i> Nama + No. Test
                                </button>
                                <button type="button" class="btn btn-sm ${curMode === 'initial' ? 'btn-dark font-weight-bold' : 'btn-light border text-secondary'} btn-switch-ident" data-mode="initial" title="Sensor Nama Santri (Inisial)">
                                    <i class="fas fa-user-secret mr-1"></i> Inisial
                                </button>
                            </div>
                            <span class="prs-badge-soft bg-danger text-white">
                                Total: ${totalPerluPenguatan} Santri (Nilai &lt; ${th.kkm})
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row my-auto g-2">
                    <!-- Kolom 1: Klinik Khusus -->
                    <div class="col-4">
                        <div class="prs-card p-3 h-100 d-flex flex-column" style="background:#fff5f5; border-top: 4px solid #ef4444;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="font-weight-bold text-danger mb-0">Klinik Khusus</h6>
                                <span class="badge badge-danger">${klinikList.length} Santri</span>
                            </div>
                            <span class="text-muted small mb-2">&ge; ${cfg.klinikThreshold} Materi di Bawah KKM</span>
                            <div class="prs-student-scroll flex-grow-1">
                                ${renderSantriCards(klinikList, 'badge-danger')}
                            </div>
                            <div class="badge badge-light border text-danger text-center mt-2 small">
                                <i class="fas fa-user-shield mr-1"></i> Privat Ustadz Ba'da Maghrib
                            </div>
                        </div>
                    </div>

                    <!-- Kolom 2: Penguatan Inti -->
                    <div class="col-4">
                        <div class="prs-card p-3 h-100 d-flex flex-column" style="background:#fffbeb; border-top: 4px solid #f59e0b;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="font-weight-bold text-warning mb-0">Penguatan Inti</h6>
                                <span class="badge badge-warning text-dark">${penguatanList.length} Santri</span>
                            </div>
                            <span class="text-muted small mb-2">Fokus 2 - 3 Materi Rawan</span>
                            <div class="prs-student-scroll flex-grow-1">
                                ${renderSantriCards(penguatanList, 'badge-warning text-dark')}
                            </div>
                            <div class="badge badge-light border text-warning text-center mt-2 small">
                                <i class="fas fa-chalkboard-teacher mr-1"></i> Drill Kelompok Terarah
                            </div>
                        </div>
                    </div>

                    <!-- Kolom 3: Polesan Ringan -->
                    <div class="col-4">
                        <div class="prs-card p-3 h-100 d-flex flex-column" style="background:#f0f9ff; border-top: 4px solid #0284c7;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="font-weight-bold text-primary mb-0">Polesan Ringan</h6>
                                <span class="badge badge-primary">${polesanList.length} Santri</span>
                            </div>
                            <span class="text-muted small mb-2">Hanya 1 Materi Belum Tuntas</span>
                            <div class="prs-student-scroll flex-grow-1">
                                ${renderSantriCards(polesanList, 'badge-info')}
                            </div>
                            <div class="badge badge-light border text-primary text-center mt-2 small">
                                <i class="fas fa-check-double mr-1"></i> Pemantapan Mandiri &amp; Wali
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-muted small text-right border-top pt-2">
                    <i class="fas fa-shield-alt text-primary mr-1"></i> Transparansi Menuju Sukses Kelulusan 100%
                </div>
            </div>
        `);
    }

    // SLIDE 12: Matriks Evaluasi & Pemetaan Kekurangan Materi Santri (Tampilan Bersih & Presentatif)
    function renderSlide12(canvas) {
        canvas.addClass('slide-theme-light');
        const th = getThresholds();
        const cfg = TPQ_APP.config;
        const data = TPQ_APP.dataset;

        if (!data || !data.rows || data.rows.length === 0) {
            canvas.html(`
                <div class="slide-content-area h-100 d-flex align-items-center justify-content-center text-muted">
                    <div class="text-center">
                        <i class="fas fa-inbox fa-3x mb-3 text-secondary"></i>
                        <h5>Belum Ada Data Peserta</h5>
                        <p class="small">Silakan pilih Tahun Ajaran &amp; Lembaga TPQ yang sesuai.</p>
                    </div>
                </div>
            `);
            return;
        }

        const showScore = cfg.matriks_show_score !== false;
        const filterMode = cfg.matriks_filter || 'kurang_only';
        const cats = data.categories || [];
        const currentTh = th.kkm;

        // Process rows
        const items = data.rows.map(r => {
            const scores = {};
            let totalKurang = 0;
            cats.forEach(c => {
                const v = (r.avg && r.avg[c.id] !== undefined) ? Number(r.avg[c.id]) : 0;
                scores[c.id] = v;
                if (v < currentTh) {
                    totalKurang++;
                }
            });
            return {
                np: r.np,
                nm: r.nm || '-',
                name: r.nm || '-',
                scores: scores,
                totalKurang: totalKurang
            };
        });

        // Filter: default hanya santri yang memiliki nilai kurang (< KKM)
        let filtered = items;
        if (filterMode === 'kurang_only') {
            filtered = items.filter(s => s.totalKurang > 0);
        }

        // Urutkan santri dengan kekurangan materi terbanyak di paling atas
        filtered.sort((a, b) => {
            if (b.totalKurang !== a.totalKurang) return b.totalKurang - a.totalKurang;
            return a.nm.localeCompare(b.nm);
        });

        const totalKurangSantri = items.filter(s => s.totalKurang > 0).length;
        const title = cfg.matriks_title || 'Matriks Evaluasi & Pemetaan Kekurangan Materi';
        const subtitle = cfg.matriks_subtitle || 'Daftar Rekapitulasi Capaian Materi per Santri';

        // Table Header (Tanpa No Test, T.A, Type, dan TPQ)
        let thCols = `
            <th style="width: 45px;" class="text-center bg-light">No</th>
            <th class="bg-light" style="min-width: 180px;">Nama Santri</th>
        `;
        cats.forEach(c => {
            thCols += `<th class="text-center bg-light font-weight-bold" style="min-width: 95px;">${c.name.toUpperCase()}</th>`;
        });
        thCols += `<th class="text-center font-weight-bold bg-light" style="width: 110px;">Total Kurang</th>`;

        // Table Body
        let bodyHtml = '';
        if (!filtered.length) {
            bodyHtml = `
                <tr>
                    <td colspan="${2 + cats.length + 1}" class="text-center text-muted py-5">
                        <i class="fas fa-check-circle text-success fa-2x mb-2 d-block"></i>
                        <strong class="text-success">Alhamdulillah, Seluruh Santri Tuntas di Atas Standar KKM (&ge; ${currentTh}).</strong>
                    </td>
                </tr>
            `;
        } else {
            bodyHtml = filtered.map((s, idx) => {
                const catTds = cats.map(c => {
                    const v = s.scores[c.id];
                    if (v < currentTh) {
                        const scoreDisplay = (v === 0) ? '0' : v.toFixed(2);
                        const content = showScore ?
                            `<span class="badge badge-danger font-weight-bold px-2 py-1" style="font-size:0.75rem; border-radius:99px;"><i class="fas fa-circle mr-1" style="font-size:0.46rem; vertical-align:middle;"></i>${scoreDisplay}</span>` :
                            `<i class="fas fa-circle text-danger" style="font-size:0.9rem;" title="Nilai: ${scoreDisplay}"></i>`;
                        return `<td class="text-center table-danger" style="background:#ffebee;">${content}</td>`;
                    } else {
                        const content = showScore ?
                            `<span class="badge badge-success font-weight-bold px-2 py-1" style="font-size:0.75rem; border-radius:99px;"><i class="fas fa-circle mr-1" style="font-size:0.46rem; vertical-align:middle;"></i>${v.toFixed(2)}</span>` :
                            `<i class="fas fa-circle text-success" style="font-size:0.9rem;" title="Nilai: ${v.toFixed(2)}"></i>`;
                        return `<td class="text-center" style="background:#f0fdf4;">${content}</td>`;
                    }
                }).join('');

                const badgeTotal = s.totalKurang > 0 ?
                    `<span class="badge badge-danger px-2 py-1 font-weight-bold" style="font-size:0.78rem;">${s.totalKurang} Materi</span>` :
                    `<span class="badge badge-success px-2 py-1" style="font-size:0.78rem;">0 (Tuntas)</span>`;

                return `
                    <tr style="height: 38px;">
                        <td class="text-center text-muted align-middle">${idx + 1}</td>
                        <td class="align-middle">${formatSantriIdentity(s)}</td>
                        ${catTds}
                        <td class="text-center align-middle">${badgeTotal}</td>
                    </tr>
                `;
            }).join('');
        }

        canvas.html(`
            <div class="slide-content-area h-100 d-flex flex-column justify-content-between p-3 p-lg-4">
                <!-- Header Slide (Bersih, Rapi & Elegan) -->
                <div>
                    <div class="slide-eyebrow text-primary">MATRIKS EVALUASI &amp; PEMETAAN</div>
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                        <div>
                            <h2 class="slide-title-serif text-dark font-weight-bold mb-0" style="font-size: 1.4rem;">
                                ${title}
                            </h2>
                            <div class="small text-muted mt-1">
                                <i class="fas fa-clipboard-check text-primary mr-1"></i>${subtitle}
                            </div>
                        </div>

                        <!-- Lencana Ringkas Standar & Total -->
                        <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
                            <span class="prs-badge-soft bg-danger text-white">
                                <i class="fas fa-user-times mr-1"></i>${totalKurangSantri} Santri Perlu Penguatan (Nilai &lt; ${currentTh})
                            </span>
                            <span class="prs-badge-soft bg-light border text-dark font-weight-bold">
                                Total: ${items.length} Peserta
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Table Container with High-Res Scroll -->
                <div class="flex-grow-1 table-responsive prs-matriks-scroll border rounded bg-white shadow-sm my-2" style="overflow-y: auto; max-height: 440px;">
                    <table class="table table-bordered table-sm table-hover mb-0 text-nowrap" id="tblMatriksEvaluasi" style="font-size: 0.82rem;">
                        <thead class="thead-light" style="position: sticky; top: 0; z-index: 5; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                            <tr>${thCols}</tr>
                        </thead>
                        <tbody>${bodyHtml}</tbody>
                    </table>
                </div>

                <!-- Footer Slide -->
                <div class="d-flex justify-content-between align-items-center border-top pt-2 text-muted small">
                    <div>
                        <span class="badge badge-light border text-danger mr-2">
                            <i class="fas fa-circle text-danger mr-1" style="font-size:0.55rem; vertical-align:middle;"></i> Nilai &lt; ${currentTh} (Belum Tuntas)
                        </span>
                        <span class="badge badge-light border text-success">
                            <i class="fas fa-circle text-success mr-1" style="font-size:0.55rem; vertical-align:middle;"></i> Nilai &ge; ${currentTh} (Tuntas)
                        </span>
                    </div>
                    <div>
                        <i class="fas fa-shield-alt text-primary mr-1"></i> Menuju Sukses Kelulusan 100%
                    </div>
                </div>
            </div>
        `);
    }

    // SLIDE 13: Penutup / Closing (Dark Theme)
    function renderSlide13(canvas) {
        canvas.addClass('slide-theme-dark');
        const cfg = TPQ_APP.config;

        canvas.html(`
            <div class="slide-content-area text-center position-relative h-100 d-flex flex-column justify-content-between p-4 p-lg-5">
                <!-- Top Eyebrow -->
                <div class="pt-2">
                    <div class="slide-eyebrow text-info justify-content-center mb-1">
                        KOMITMEN BERSAMA KELULUSAN 100%
                    </div>
                </div>

                <!-- Main Message -->
                <div class="my-auto py-3">
                    <h1 class="slide-title-serif font-weight-bold text-white mb-3" style="font-size: clamp(1.8rem, 3.6vw, 2.9rem); line-height: 1.3;">
                        Bersama Mengantarkan Seluruh Santri Melenggang ke Panggung Wisuda
                    </h1>
                    <p class="lead text-slate-300 mx-auto font-weight-light" style="max-width: 820px; color: #94a3b8; font-size: clamp(0.95rem, 1.6vw, 1.25rem);">
                        Dukungan penuh orang tua dan bimbingan terpadu para asatidz adalah kunci kesuksesan ananda di Munaqosah.
                    </p>

                    <!-- 2 Target Badges -->
                    <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                        <div class="prs-card px-4 py-2 border-0 bg-dark text-white d-flex align-items-center gap-2" style="background: rgba(30,41,59,0.8);">
                            <span class="mr-2 text-warning"><i class="fas fa-calendar-alt"></i></span>
                            <span class="font-weight-bold small">Program Penguatan: Dimulai Pekan Depan</span>
                        </div>
                        <div class="prs-card px-4 py-2 border-0 bg-dark text-white d-flex align-items-center gap-2 ml-3" style="background: rgba(30,41,59,0.8);">
                            <span class="mr-2 text-success"><i class="fas fa-user-graduate"></i></span>
                            <span class="font-weight-bold small">Target Bersama: 100% Lulus Munaqosah</span>
                        </div>
                    </div>
                </div>

                <!-- Footer line -->
                <div class="w-100 text-center pb-2">
                    <div style="width: 80px; height: 3px; background: #0284c7; border-radius: 99px; margin: 0 auto 10px auto;"></div>
                    <div class="text-muted small">${cfg.namaTpq} • Menyiapkan Generasi Qur'ani yang Tangguh</div>
                </div>
            </div>
        `);
    }

    // ==================== NAVIGATION & FULLSCREEN CONTROLS ====================
    function nextSlide() {
        if (TPQ_APP.currentSlide < TPQ_APP.totalSlides) {
            renderSlide(TPQ_APP.currentSlide + 1);
        }
    }

    function prevSlide() {
        if (TPQ_APP.currentSlide > 1) {
            renderSlide(TPQ_APP.currentSlide - 1);
        }
    }

    function toggleFullscreen() {
        const wrap = document.getElementById('prsPresentationWrapper');
        if (!document.fullscreenElement) {
            if (wrap.requestFullscreen) {
                wrap.requestFullscreen();
            } else if (wrap.webkitRequestFullscreen) {
                wrap.webkitRequestFullscreen();
            } else if (wrap.msRequestFullscreen) {
                wrap.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }

    // Monitor fullscreen change events
    document.addEventListener('fullscreenchange', handleFsChange);
    document.addEventListener('webkitfullscreenchange', handleFsChange);

    function handleFsChange() {
        const wrap = $('#prsPresentationWrapper');
        if (document.fullscreenElement) {
            wrap.addClass('prs-fullscreen-active');
            $('#hudFullscreen').hide();
            $('#hudExitFs').show();
            TPQ_APP.isFullscreen = true;
        } else {
            wrap.removeClass('prs-fullscreen-active');
            $('#hudFullscreen').show();
            $('#hudExitFs').hide();
            TPQ_APP.isFullscreen = false;
        }
    }

    // Laser pointer toggle & mouse follower
    function toggleLaser() {
        TPQ_APP.isLaserActive = !TPQ_APP.isLaserActive;
        const pointer = $('#laserPointer');
        if (TPQ_APP.isLaserActive) {
            pointer.show();
            $('#hudLaser').addClass('text-danger');
            toastr.info('Mode Laser Pointer Aktif (P untuk mematikan)');
        } else {
            pointer.hide();
            $('#hudLaser').removeClass('text-danger');
        }
    }

    $('#prsPresentationWrapper').on('mousemove', function(e) {
        if (!TPQ_APP.isLaserActive) return;
        const rect = this.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        $('#laserPointer').css({ top: y + 'px', left: x + 'px' });
    });

    // Keyboard Shortcuts
    $(document).on('keydown', function(e) {
        // Jangan tangkap shortcut jika sedang fokus di input / modal
        if ($(e.target).is('input, textarea, select')) return;

        if (e.key === 'ArrowRight' || e.key === ' ' || e.key === 'PageDown') {
            e.preventDefault();
            nextSlide();
        } else if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
            e.preventDefault();
            prevSlide();
        } else if (e.key === 'f' || e.key === 'F') {
            e.preventDefault();
            toggleFullscreen();
        } else if (e.key === 'p' || e.key === 'P') {
            e.preventDefault();
            toggleLaser();
        } else if (e.key === 'g' || e.key === 'G') {
            const target = prompt(`Lompat ke slide nomor (1 - ${TPQ_APP.totalSlides}):`, TPQ_APP.currentSlide);
            const num = parseInt(target, 10);
            if (!isNaN(num) && num >= 1 && num <= TPQ_APP.totalSlides) {
                renderSlide(num);
            }
        }
    });

    // Event listeners
    $('#btnLaunchPresentation').on('click', function() {
        toggleFullscreen();
    });

    $('#hudNext').on('click', nextSlide);
    $('#hudPrev').on('click', prevSlide);
    $('#hudFullscreen').on('click', toggleFullscreen);
    $('#hudExitFs').on('click', toggleFullscreen);
    $('#hudLaser').on('click', toggleLaser);

    $('#prsThumbStrip').on('click', '.prs-thumb-card', function() {
        const slide = parseInt($(this).data('slide'), 10);
        if (slide) renderSlide(slide);
    });

    $('#btnOpenConfigModal').on('click', function() {
        $('#modalConfig').modal('show');
    });

    $('#btnSaveConfig').on('click', saveConfig);

    $('#btnResetDefaultConfig').on('click', function() {
        if (confirm('Apakah Anda yakin ingin mengembalikan pengaturan ke preset bawaan?')) {
            TPQ_APP.config = Object.assign({}, DEFAULT_CONFIG);
            syncConfigInputs();
            toastr.info('Pengaturan dikembalikan ke default. Klik Simpan untuk menerapkan.');
        }
    });

    $('#btnReloadData').on('click', function() {
        fetchStatistikData();
    });

    $(document).on('click', '.prs-preset-val', function(e) {
        e.preventDefault();
        const v = $(this).data('val');
        $('#filterThreshold').val(v);
        toastr.info('Batas nilai disetel ke: ' + v);
        fetchStatistikData();
    });

    // Quick Switcher Identitas Santri di Slide 11
    $(document).on('click', '.btn-switch-ident', function(e) {
        e.preventDefault();
        const mode = $(this).data('mode');
        TPQ_APP.config.santriDisplayMode = mode;
        TPQ_APP.config.maskSantriName = (mode === 'initial');
        try {
            localStorage.setItem(getStorageKey(), JSON.stringify(TPQ_APP.config));
        } catch (err) {}
        syncConfigInputs();
        toastr.info('Tampilan identitas diubah ke: ' + $(this).text().trim());
        renderSlide(11);
    });

    // Modal Rincian Santri Slide 4 Filter & Search
    $(document).on('click', '.btn-filter-materi-status', function(e) {
        e.preventDefault();
        const f = $(this).data('filter');
        currentMateriDetail.filter = f;
        updateMateriDetailFilterUI(f);
        renderMateriDetailTable();
    });

    $(document).on('input', '#mdsmSearchInput', function() {
        currentMateriDetail.searchTerm = $(this).val();
        renderMateriDetailTable();
    });

    $('#filterTpq, #filterTahunAjaran, #filterTypeUjian').on('change', function() {
        if (this.id === 'filterTpq') {
            loadConfig();
        }
        fetchStatistikData();
    });

    // ==================== EXPORT PDF (LANDSCAPE A4 16:9) ====================
    $('#btnExportPdf').on('click', async function() {
        if (!window.jspdf || !window.html2canvas) {
            toastr.error('Library PDF belum selesai dimuat.');
            return;
        }

        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Membuat PDF...');

        toastr.info(`Memproses ${TPQ_APP.totalSlides} slide ke format PDF Landscape... Mohon tunggu.`);

        try {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({
                orientation: 'landscape',
                unit: 'mm',
                format: 'a4'
            });

            const current = TPQ_APP.currentSlide;
            const canvasEl = document.getElementById('prsCanvas');

            for (let i = 1; i <= TPQ_APP.totalSlides; i++) {
                renderSlide(i);
                // Beri waktu sejenak agar Chart / CSS render sempurna
                await new Promise(resolve => setTimeout(resolve, 300));

                const capturedCanvas = await html2canvas(canvasEl, {
                    scale: 2, // High resolution
                    useCORS: true,
                    logging: false
                });

                const imgData = capturedCanvas.toDataURL('image/jpeg', 0.95);
                if (i > 1) {
                    pdf.addPage();
                }
                pdf.addImage(imgData, 'JPEG', 0, 0, 297, 210); // A4 Landscape
            }

            // Kembalikan ke slide sebelumnya
            renderSlide(current);

            const fileName = `Presentasi_Hasil_Munaqosah_${TPQ_APP.config.namaTpq.replace(/[^a-zA-Z0-9]/g, '_')}.pdf`;
            pdf.save(fileName);
            toastr.success('PDF berhasil diunduh!');
        } catch (err) {
            console.error('PDF error:', err);
            toastr.error('Gagal mengekspor PDF: ' + err.message);
        } finally {
            btn.prop('disabled', false).html(originalHtml);
        }
    });

        // Init
        if (typeof $.fn.select2 === 'function') {
            $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
        }
        loadConfig();
        fetchStatistikData();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', runPresentasiInit);
    } else {
        runPresentasiInit();
    }
})(window, window.jQuery);
</script>
<?= $this->endSection(); ?>
