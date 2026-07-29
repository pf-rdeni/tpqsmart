<?php
/**
 * Shared CBT Exam Engine View Component (cbt_sheet.php)
 * Used by both Preview Mode (preview.php) & Santri Real Exam (lembar_ujian.php)
 *
 * Parameters passed to this view:
 * - bool   $isPreview      (true for preview mode, false for real exam)
 * - string $pageTitle      (Title shown in title banner)
 * - string $namaUjian      (Name of exam)
 * - string $namaSantri     (Student name)
 * - string $namaKelas      (Class name)
 * - string $namaPaket      (Question package name)
 * - array  $soalList       (List of questions with choices)
 * - array  $jawabanMap     (Map of saved answers: idSoal => idPilihan)
 * - int    $sisaWaktuDetik (Remaining seconds for timer)
 * - string $token          (Token for real exam AJAX, empty if preview)
 * - string $exitUrl        (URL for Exit button)
 * - string $exitLabel      (Label for Exit button, e.g., 'Exit Preview' or 'Keluar Ujian')
 */
$isPreview      = isset($isPreview) ? (bool)$isPreview : false;
$pageTitle      = $pageTitle ?? 'CBT Ujian MDTA';
$namaUjian      = $namaUjian ?? 'Ujian MDTA';
$namaSantri     = $namaSantri ?? 'Santri';
$namaKelas      = $namaKelas ?? '-';
$namaPaket      = $namaPaket ?? '-';
$soalList       = !empty($soalList) ? $soalList : (!empty($distribusi) ? $distribusi : []);
$jawabanMap     = $jawabanMap ?? [];
$raguMap        = $raguMap ?? [];
$sisaWaktuDetik = $sisaWaktuDetik ?? 3600;
$token          = $token ?? '';
$exitUrl        = $exitUrl ?? base_url('backend/ujian-mdta/santri');
$exitLabel      = $exitLabel ?? ($isPreview ? 'Exit Preview' : 'Keluar Ujian');

// Pre-process grouping & ModeSoal filtering (campuran, pg, esai)
$modeSoal = strtolower($jadwal['ModeSoal'] ?? 'campuran');
$pgList   = [];
$esaiList = [];

foreach ($soalList as $ds) {
    $jenis = strtolower($ds['JenisSoal'] ?? 'pilihan_ganda');
    if ($jenis === 'esai') {
        if ($modeSoal !== 'pg') {
            $esaiList[] = $ds;
        }
    } else {
        if ($modeSoal !== 'esai') {
            $pgList[] = $ds;
        }
    }
}

// Gabungkan kembali: Seluruh PG di depan, diikuti seluruh Esai
$soalListSorted = array_merge($pgList, $esaiList);

$pgCounter       = 0;
$esaiCounter     = 0;
$soalListGrouped = [];

foreach ($soalListSorted as $idx => $ds) {
    $jenis = $ds['JenisSoal'] ?? 'pilihan_ganda';
    if ($jenis === 'esai') {
        $esaiCounter++;
        $ds['groupType']    = 'esai';
        $ds['groupNum']     = $esaiCounter;
        $ds['displayTitle'] = "Soal Esai #" . $esaiCounter;
    } else {
        $pgCounter++;
        $ds['groupType']    = 'pg';
        $ds['groupNum']     = $pgCounter;
        $ds['displayTitle'] = "Soal PG #" . $pgCounter;
    }
    $soalListGrouped[$idx] = $ds;
}
?>

<style>
    /* Custom Purple Styling Helpers for Esai Grouping */
    .text-purple { color: #8b5cf6 !important; }
    .bg-purple-50 { background-color: #f5f3ff !important; }
    .bg-purple-subtle { background-color: #ede9fe !important; color: #6d28d9 !important; }
    .border-purple { border-color: #ddd6fe !important; }
    .alert-purple { background-color: #f5f3ff; border: 1px solid #ddd6fe; color: #5b21b6; }
    /* ========================================================
       CBT SHARED STYLING (Used by Preview & Real Exam)
       ======================================================== */
    :root {
        --cbt-blue:        #0088cc;
        --cbt-dark-blue:   #006699;
        --cbt-header-blue: #0284c7;
    }

    /* Sub-header Title Banner */
    .page-title-banner {
        background-color: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 24px;
        margin-bottom: 20px;
    }
    .page-title-banner h5 {
        margin: 0;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: 0.5px;
    }

    /* Header Info Card */
    .preview-header-info {
        background-color: #e0f2fe;
        color: #0369a1;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 10px 16px;
        border-radius: 6px 6px 0 0;
    }
    .info-pengerjaan-table td {
        padding: 6px 12px;
        font-size: 0.88rem;
    }
    .info-pengerjaan-table td.label-col {
        font-weight: 600;
        width: 150px;
        color: #475569;
    }

    /* Big Digital Countdown Timer Box */
    .digital-timer-card {
        background-color: #fffbeb;
        border: 1px solid #fef3c7;
        border-radius: 8px;
        padding: 18px;
        text-align: center;
    }
    .digital-clock-display {
        font-family: 'Courier New', Courier, monospace;
        font-size: 2.1rem;
        font-weight: 800;
        letter-spacing: 4px;
        background: #000000;
        color: #ffffff;
        padding: 6px 16px;
        border-radius: 6px;
        display: inline-block;
        box-shadow: inset 0 2px 4px rgba(255,255,255,0.2);
        transition: background 0.3s;
    }
    .digital-clock-display.danger-time {
        background: #dc2626;
    }
    .digital-clock-display.timer-success,
    body.dark-mode .digital-clock-display.timer-success,
    .dark-mode .digital-clock-display.timer-success {
        background: #16a34a !important;
        background-color: #16a34a !important;
        color: #ffffff !important;
    }
    .digital-clock-display.timer-warning,
    body.dark-mode .digital-clock-display.timer-warning,
    .dark-mode .digital-clock-display.timer-warning {
        background: #ca8a04 !important;
        background-color: #ca8a04 !important;
        color: #ffffff !important;
    }
    .digital-clock-display.timer-danger,
    body.dark-mode .digital-clock-display.timer-danger,
    .dark-mode .digital-clock-display.timer-danger {
        background: #dc2626 !important;
        background-color: #dc2626 !important;
        color: #ffffff !important;
        animation: pulse-animation 1s infinite !important;
    }
    .clock-labels {
        font-size: 0.72rem;
        color: #64748b;
        margin-top: 4px;
        letter-spacing: 6px;
        text-transform: lowercase;
    }

    /* Soal Card Header */
    .card-soal-header-blue {
        background: var(--cbt-header-blue);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 10px 16px;
        border-radius: 6px 6px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Touchscreen Friendly Zoom & Nav Controls */
    .btn-group-touch .btn {
        padding: 4px 12px;
        font-size: 0.85rem;
        font-weight: 700;
        min-width: 38px;
        min-height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        touch-action: manipulation;
        user-select: none;
    }
    .btn-touch-zoom {
        background-color: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.4);
        transition: all 0.15s ease;
    }
    .btn-touch-zoom:hover, .btn-touch-zoom:active {
        background-color: #ffffff !important;
        color: var(--cbt-header-blue) !important;
    }
    .btn-touch-nav {
        background-color: rgba(255, 255, 255, 0.3);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
    .btn-touch-nav:hover, .btn-touch-nav:active {
        background-color: #ffffff !important;
        color: var(--cbt-header-blue) !important;
    }

    /* Pilihan Jawaban Items */
    .pilihan-cbt-item {
        display: flex;
        align-items: flex-start;
        padding: 10px 14px;
        margin-bottom: 8px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background-color: #ffffff;
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
    }
    .pilihan-cbt-item:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
    }
    .pilihan-cbt-item.selected, .pilihan-cbt-item.active-choice {
        background-color: #e0f2fe;
        border-color: #0284c7;
        font-weight: 600;
    }
    .pilihan-cbt-item input[type="radio"] {
        display: none;
    }
    .circle-letter {
        width: 32px;
        height: 32px;
        min-width: 32px;
        min-height: 32px;
        border-radius: 50%;
        border: 1px solid #cbd5e1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 700;
        color: #475569;
        margin-right: 12px;
        background: #ffffff;
        flex-shrink: 0;
        align-self: flex-start;
        margin-top: 2px;
        transition: all 0.15s ease;
    }
    .pilihan-cbt-item.selected .circle-letter, .pilihan-cbt-item.active-choice .circle-letter {
        background: #0284c7;
        color: #ffffff;
        border-color: #0284c7;
    }

    /* Nomor Soal Grid */
    .nomor-soal-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        padding: 18px;
    }
    .btn-circle-num {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 1px solid #cbd5e1;
        background-color: #ffffff;
        color: #334155;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        margin: 0 auto;
    }
    .btn-circle-num:hover {
        border-color: var(--cbt-header-blue);
        color: var(--cbt-header-blue);
        transform: scale(1.05);
    }
    .btn-circle-num.dijawab {
        background-color: #16a34a !important;
        color: #ffffff !important;
        border-color: #16a34a !important;
    }
    .btn-circle-num.ragu {
        background-color: #eab308 !important;
        color: #ffffff !important;
        border-color: #ca8a04 !important;
    }
    .btn-circle-num.aktif, .btn-circle-num.active-preview-num {
        background-color: #0284c7 !important;
        color: #ffffff !important;
        border-color: #0284c7 !important;
    }
    .btn-circle-num.dijawab.aktif {
        background-color: #0284c7 !important;
        color: #ffffff !important;
        border-color: #0284c7 !important;
        box-shadow: 0 0 0 3px #16a34a !important;
    }
    .btn-circle-num.ragu.aktif {
        background-color: #eab308 !important;
        color: #ffffff !important;
        border-color: #ca8a04 !important;
        box-shadow: 0 0 0 3px #0284c7 !important;
    }

    /* Ragu-Ragu Toggle Button Styling */
    .btn-ragu-toggle {
        background-color: #fefce8;
        color: #a16207;
        border: 1px solid #fde047;
        font-weight: 600;
        padding: 5px 16px;
        border-radius: 20px;
        transition: all 0.2s ease;
    }
    .btn-ragu-toggle:hover {
        background-color: #fef08a;
        color: #854d0e;
        border-color: #eab308;
    }
    .btn-ragu-toggle.active-ragu {
        background-color: #eab308 !important;
        color: #ffffff !important;
        border-color: #ca8a04 !important;
        box-shadow: 0 2px 6px rgba(234, 179, 8, 0.4);
    }

    /* Legend Badge Helpers */
    .legend-badge-dijawab { background-color: #16a34a !important; border: 1px solid #16a34a; }
    .legend-badge-ragu   { background-color: #eab308 !important; border: 1px solid #ca8a04; }
    .legend-badge-aktif   { background-color: #0284c7 !important; border: 1px solid #0284c7; }
    .legend-badge-belum   { background-color: #ffffff !important; border: 1px solid #cbd5e1 !important; color: #334155; }

    /* Arabic & Image Support */
    figure.image, .uraian-soal-text figure.image {
        margin: 10px auto;
        display: table;
        max-width: 100%;
    }
    figure.image img, .uraian-soal-text img, .pilihan-cbt-item img {
        height: auto !important;
        max-width: 100%;
        border-radius: 6px;
    }

    /* Natural Slate Dark Mode Theme */
    body.dark-mode {
        background-color: #0f172a !important;
        color: #f1f5f9 !important;
    }
    body.dark-mode .content-wrapper {
        background-color: #0f172a !important;
    }
    body.dark-mode .main-header.navbar {
        background-color: #1e293b !important;
        border-bottom: 1px solid #334155 !important;
    }
    body.dark-mode .page-title-banner {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .page-title-banner h5 {
        color: #f8fafc !important;
    }
    body.dark-mode .alert-purple {
        background-color: #2e1065 !important;
        border-color: #5b21b6 !important;
        color: #ddd6fe !important;
    }
    body.dark-mode .text-purple { color: #c4b5fd !important; }
    body.dark-mode .bg-purple-subtle { background-color: #4c1d95 !important; color: #ddd6fe !important; }
    body.dark-mode .card {
        background-color: #1e293b !important;
        border: 1px solid #334155 !important;
        color: #f1f5f9 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3) !important;
    }
    body.dark-mode .preview-header-info {
        background-color: #0f172a !important;
        color: #60a5fa !important;
        border-bottom: 1px solid #334155 !important;
    }
    body.dark-mode .card-soal-header-blue {
        background-color: #1e3a8a !important;
        color: #ffffff !important;
    }
    body.dark-mode .info-pengerjaan-table td {
        color: #cbd5e1 !important;
    }
    body.dark-mode .info-pengerjaan-table td.label-col {
        color: #94a3b8 !important;
    }
    body.dark-mode .pilihan-cbt-item {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }
    body.dark-mode .pilihan-cbt-item:hover {
        background-color: #1e293b !important;
        border-color: #475569 !important;
    }
    body.dark-mode .pilihan-cbt-item.selected, body.dark-mode .pilihan-cbt-item.active-choice {
        background-color: #1e3a8a !important;
        border-color: #3b82f6 !important;
        color: #ffffff !important;
    }
    body.dark-mode .pilihan-cbt-item span.text-dark {
        color: #f1f5f9 !important;
    }
    body.dark-mode .circle-letter {
        background-color: #1e293b !important;
        color: #cbd5e1 !important;
        border-color: #475569 !important;
    }
    body.dark-mode .pilihan-cbt-item.selected .circle-letter, body.dark-mode .pilihan-cbt-item.active-choice .circle-letter {
        background-color: #2563eb !important;
        color: #ffffff !important;
        border-color: #2563eb !important;
    }
    body.dark-mode .btn-circle-num {
        background-color: #0f172a !important;
        color: #cbd5e1 !important;
        border-color: #334155 !important;
    }
    body.dark-mode .btn-circle-num:hover {
        border-color: #60a5fa !important;
        color: #60a5fa !important;
    }
    body.dark-mode .btn-circle-num.dijawab {
        background-color: #15803d !important;
        color: #ffffff !important;
        border-color: #15803d !important;
    }
    body.dark-mode .btn-circle-num.ragu {
        background-color: #d97706 !important;
        color: #ffffff !important;
        border-color: #b45309 !important;
    }
    body.dark-mode .btn-circle-num.aktif, body.dark-mode .btn-circle-num.active-preview-num {
        background-color: #2563eb !important;
        color: #ffffff !important;
        border-color: #3b82f6 !important;
    }
    body.dark-mode .btn-circle-num.dijawab.aktif {
        background-color: #2563eb !important;
        color: #ffffff !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px #16a34a !important;
    }
    body.dark-mode .btn-circle-num.ragu.aktif {
        background-color: #d97706 !important;
        color: #ffffff !important;
        border-color: #b45309 !important;
        box-shadow: 0 0 0 3px #2563eb !important;
    }
    body.dark-mode .btn-ragu-toggle {
        background-color: #422006;
        color: #fef08a;
        border-color: #713f12;
    }
    body.dark-mode .btn-ragu-toggle:hover {
        background-color: #713f12;
        color: #ffffff;
    }
    body.dark-mode .btn-ragu-toggle.active-ragu {
        background-color: #d97706 !important;
        color: #ffffff !important;
        border-color: #b45309 !important;
        box-shadow: 0 2px 6px rgba(217, 119, 6, 0.5);
    }
    body.dark-mode .legend-badge-dijawab { background-color: #16a34a !important; border: 1px solid #16a34a !important; }
    body.dark-mode .legend-badge-ragu   { background-color: #d97706 !important; border: 1px solid #b45309 !important; }
    body.dark-mode .legend-badge-aktif   { background-color: #2563eb !important; border: 1px solid #2563eb !important; }
    body.dark-mode .legend-badge-belum   { background-color: #0f172a !important; border: 1px solid #334155 !important; }
    body.dark-mode .digital-timer-card {
        background-color: #1e293b !important;
        border-color: #334155 !important;
    }
    body.dark-mode .digital-clock-display {
        background-color: #090d16 !important;
        color: #fbbf24 !important;
        border: 1px solid #334155 !important;
    }
    body.dark-mode .uraian-soal-text, body.dark-mode .uraian-soal-text p {
        color: #f1f5f9 !important;
    }
</style>

<!-- SUB-HEADER TITLE BANNER -->
<!-- OVERLAY PAUSE DARI PENGAWAS -->
<div id="cbtPauseOverlay" style="display: <?= (isset($sesi['StatusSesi']) && $sesi['StatusSesi'] === 'pause') ? 'flex' : 'none' ?>; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.92); z-index: 99999; justify-content: center; align-items: center; text-align: center; color: white; backdrop-filter: blur(5px);">
    <div style="max-width: 500px; padding: 2.5rem; background: #1e293b; border-radius: 1rem; border: 1px solid #334155; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <div class="mb-3">
            <i class="fas fa-pause-circle text-info" style="font-size: 4.5rem; animation: pulse-animation 1.5s infinite;"></i>
        </div>
        <h3 class="fw-bold mb-2 text-info">UJIAN DI-PAUSE</h3>
        <p class="text-light mb-3">Ujian Anda di-pause sementara oleh Pengawas Ujian. Layar terkunci dan waktu pengerjaan terhenti.</p>
        <div class="badge bg-info text-dark px-3 py-2 fs-6">
            <i class="fas fa-spinner fa-spin me-1"></i> Menunggu Pengawas melanjutkan...
        </div>
    </div>
</div>

<div class="page-title-banner shadow-xs">
    <div class="container-fluid px-4">
        <h5><i class="fas fa-laptop-code me-2 text-primary"></i> <?= esc($pageTitle) ?></h5>
    </div>
</div>

<!-- MAIN EXAM CONTENT GRID -->
<div class="container-fluid px-4 pb-5">

    <!-- ROW 1: TOP ROW (Informasi Pengerjaan & Digital Timer Card) -->
    <div class="row g-4 mb-4">
        <!-- 1. LEFT: Informasi Pengerjaan (Desktop Only) -->
        <div class="col-lg-8 d-none d-lg-block">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden h-100 mb-0">
                <div class="preview-header-info">
                    Informasi Pengerjaan
                </div>
                <div class="card-body p-0">
                    <table class="table table-borderless info-pengerjaan-table mb-0">
                        <tbody>
                            <tr>
                                <td class="label-col">Sesi Tes</td>
                                <td>: <strong><?= $isPreview ? 'PREVIEW PAKET SOAL' : esc($namaUjian) ?></strong></td>
                            </tr>
                            <tr>
                                <td class="label-col">Identitas</td>
                                <td>: <strong><?= esc($namaSantri) ?></strong></td>
                            </tr>
                            <tr>
                                <td class="label-col">Kelompok / Kelas</td>
                                <td>: <span class="badge bg-secondary"><?= esc($namaKelas) ?></span></td>
                            </tr>
                            <tr>
                                <td class="label-col">Nama Paket Soal</td>
                                <td>: <strong><?= esc($namaPaket) ?></strong></td>
                            </tr>
                            <tr>
                                <td class="label-col">Jumlah Soal</td>
                                <td>: <strong><?= count($soalList) ?> soal</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. RIGHT: Timer Digital Card (Desktop & Mobile) -->
        <div class="col-lg-4 col-12">
            <div class="card shadow-sm border-0 rounded-3 h-100 mb-0">
                <div class="digital-timer-card">
                    <div class="digital-clock-display" id="cbtClockDisplay">00 : 00 : 00</div>
                    <div class="clock-labels">hours &nbsp; minutes &nbsp; seconds</div>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <?php if ($isPreview): ?>
                            <a href="<?= esc($exitUrl) ?>" class="btn btn-danger btn-sm px-3 fw-semibold">
                                <i class="fas fa-flag me-1"></i> <?= esc($exitLabel) ?>
                            </a>
                        <?php else: ?>
                            <button type="button" class="btn btn-warning btn-sm px-3 fw-semibold text-dark" id="btnSubmitUjianTop">
                                <i class="fas fa-paper-plane me-1"></i> Selesaikan Ujian
                            </button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-primary btn-sm px-3 fw-semibold" id="btnToggleGrid">
                            Toggle Nomor Soal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: BOTTOM ROW (Soal Left & Nomor Soal Grid Right) -->
    <div class="row g-4">
        <!-- 1. LEFT COLUMN: Lembar Soal Card (expands to col-lg-12 when grid is hidden) -->
        <div class="col-lg-8" id="colSoalLeft">
            <?php if (empty($soalListGrouped)): ?>
                <div class="card shadow-sm border-0 rounded-3 text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                        <h5>Belum Ada Soal Tersedia</h5>
                        <p class="text-muted small">Silakan tambah atau upload soal terlebih dahulu.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($soalListGrouped as $idx => $ds): ?>
                    <?php
                    $idSoal    = $ds['IdSoal'] ?? $ds['id'] ?? $idx;
                    $isDijawab = isset($jawabanMap[$idSoal]) && $jawabanMap[$idSoal] !== null;
                    $isRagu    = !empty($raguMap[$idSoal]);
                    $isLast    = $idx === count($soalListGrouped) - 1;
                    $isFirst   = $idx === 0;
                    $pilihanArr= $ds['pilihan'] ?? [];
                    ?>
                    <div class="card shadow-sm border-0 rounded-3 mb-4 soal-block <?= $idx > 0 ? 'd-none' : '' ?>"
                         id="soal_block_<?= $idx ?>" data-idsoal="<?= $idSoal ?>">

                        <!-- Header Card Soal -->
                        <div class="card-soal-header-blue">
                            <span id="judulSoal_<?= $idx ?>">
                                <i class="<?= $ds['groupType'] === 'esai' ? 'fas fa-pen-fancy' : 'fas fa-list-ul' ?> me-2"></i>
                                <?= esc($ds['displayTitle']) ?>
                            </span>

                            <!-- Touchscreen Friendly Zoom & Nav Controls -->
                            <div class="btn-group btn-group-touch" role="group">
                                <button type="button" class="btn btn-touch-zoom"
                                        onclick="adjustCbtFontSize(-2)" title="Kecilkan Teks">
                                    <i class="fas fa-search-minus me-1"></i> A-
                                </button>
                                <button type="button" class="btn btn-touch-zoom"
                                        onclick="adjustCbtFontSize(2)" title="Besarkan Teks">
                                    <i class="fas fa-search-plus me-1"></i> A+
                                </button>
                                <button type="button" class="btn btn-touch-nav"
                                        onclick="showCbtSoal(<?= $idx - 1 ?>)"
                                        <?= $isFirst ? 'disabled' : '' ?> title="Soal Sebelumnya">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" class="btn btn-touch-nav"
                                        onclick="<?= $isLast ? ($isPreview ? 'navNextCbtSoal()' : 'konfirmasiSelesai()') : "showCbtSoal(" . ($idx + 1) . ")" ?>"
                                        title="<?= $isLast ? 'Selesaikan Ujian' : 'Soal Selanjutnya' ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Body Soal -->
                        <div class="card-body p-4">
                            <!-- Uraian Soal -->
                            <div class="uraian-soal-text mb-4 text-dark fs-6 leading-relaxed">
                                <?= $ds['UraianSoal'] ?>
                            </div>

                            <!-- Audio Soal Jika Ada -->
                            <?php if (!empty($ds['AudioSoal'])): ?>
                                <div class="mb-4 p-3 bg-light rounded border">
                                    <label class="form-label small fw-semibold text-muted mb-2 d-block">
                                        <i class="fas fa-volume-up me-1"></i> Audio Penjelas Soal:
                                    </label>
                                    <audio controls class="w-100">
                                        <source src="<?= base_url('uploads/ujian_mdta/audio/' . $ds['AudioSoal']) ?>" type="audio/mpeg">
                                        Browser Anda tidak mendukung elemen audio.
                                    </audio>
                                </div>
                            <?php endif; ?>

                            <!-- Tipe Soal: Esai vs Pilihan Ganda -->
                            <?php if (($ds['JenisSoal'] ?? 'pilihan_ganda') === 'esai'): ?>
                                <?php
                                $valEsai = $jawabanEsaiMap[$idSoal] ?? '';
                                ?>
                                <div class="mb-4">
                                    <div class="alert alert-purple bg-purple-50 text-purple border-purple mb-3 p-3 rounded-3">
                                        <i class="fas fa-pen-fancy me-2"></i> <strong>Soal Uraian / Esai:</strong> Silakan ketik jawaban santri pada kotak di bawah ini. Jawaban Anda tersimpan otomatis.
                                    </div>
                                    <textarea class="form-control input-jawaban-esai"
                                              rows="5"
                                              data-idsoal="<?= $idSoal ?>"
                                              data-soalindex="<?= $idx ?>"
                                              placeholder="Tuliskan jawaban uraian di sini..."><?= esc($valEsai) ?></textarea>
                                </div>
                            <?php else: ?>
                                <!-- Sub-header Pilihan Jawaban -->
                                <div class="text-muted small fw-semibold mb-3 border-bottom pb-2">
                                    Pilih Salah Satu Jawaban
                                </div>

                                <div class="pilihan-wrapper-cbt mb-4" id="pilihan_wrapper_<?= $idx ?>">
                                    <?php
                                    // Huruf posisional: A, B, C, D... berdasarkan posisi tampil (bukan HurufPilihan asli dari DB)
                                    $hurufPositional = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
                                    $maxPilihan = (int)($jadwal['JumlahPilihan'] ?? $sesi['JumlahPilihan'] ?? 4);

                                    // PROSES SELEKSI PILIHAN ACAK BERDASARKAN JUMLAH PILIHAN (misal 2 pilihan A-B):
                                    // 1 Kunci Jawaban Wajib Ada + Pengecoh Diambil Secara Acak Dari Seluruh Pilihan Lainnya (A-D)
                                    if ($maxPilihan > 0 && count($pilihanArr) > $maxPilihan) {
                                        $correctList = [];
                                        $wrongList   = [];

                                        foreach ($pilihanArr as $p) {
                                            if (!empty($p['IsBenar']) && (int)$p['IsBenar'] === 1) {
                                                $correctList[] = $p;
                                            } else {
                                                $wrongList[] = $p;
                                            }
                                        }

                                        // Acak seluruh pilihan pengecoh yang salah
                                        shuffle($wrongList);

                                        // Ambil pengecoh acak sebanyak sisa slot ($maxPilihan - jumlah kunci)
                                        $neededWrong   = max(1, $maxPilihan - count($correctList));
                                        $selectedWrong = array_slice($wrongList, 0, $neededWrong);

                                        // Gabungkan kunci jawaban + pengecoh acak
                                        $pilihanArr = array_merge($correctList, $selectedWrong);

                                        // Acak urutan posisi tampil (A, B, C...)
                                        shuffle($pilihanArr);
                                    }

                                    foreach ($pilihanArr as $pIdx => $p):
                                        if ($pIdx >= $maxPilihan) break; // Hanya tampilkan pilihan hingga jumlah opsi yang disetting (misal A-B, A-C, A-D)
                                        $idPilihan      = $p['id'] ?? null;
                                        $labelHuruf     = $hurufPositional[$pIdx] ?? chr(65 + $pIdx); // A=65, B=66, dst
                                        $isSelected     = isset($jawabanMap[$idSoal]) && $jawabanMap[$idSoal] == $idPilihan;
                                        if ($isPreview && !empty($p['IsBenar']) && $p['IsBenar'] == 1) {
                                            $isSelected = true;
                                        }
                                    ?>
                                        <div class="pilihan-cbt-item <?= $isSelected ? 'selected active-choice' : '' ?>"
                                             data-idsoal="<?= $idSoal ?>"
                                             data-idpilihan="<?= $idPilihan ?>"
                                             data-soalindex="<?= $idx ?>">
                                            <input type="radio" name="jawaban_<?= $idSoal ?>"
                                                   value="<?= $idPilihan ?>" <?= $isSelected ? 'checked' : '' ?>>
                                            <span class="circle-letter"><?= esc($labelHuruf) ?></span>
                                            <span class="text-dark small flex-grow-1 align-self-center">
                                                <?= $p['TeksPilihan'] ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Footer Navigasi & Tombol Ragu-Ragu -->
                            <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-4">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-semibold"
                                        onclick="showCbtSoal(<?= $idx - 1 ?>)" <?= $isFirst ? 'disabled' : '' ?>>
                                    <i class="fas fa-arrow-left me-1"></i> Sebelumnya
                                </button>

                                <button type="button" class="btn btn-sm btn-ragu-toggle <?= $isRagu ? 'active-ragu' : '' ?>"
                                        id="btn_ragu_<?= $idx ?>"
                                        onclick="toggleRaguCbt(<?= $idx ?>, <?= $idSoal ?>)"
                                        title="Tandai ragu-ragu jika belum yakin">
                                    <i class="fas fa-bookmark me-1"></i> Ragu-Ragu
                                </button>

                                <?php if ($isLast): ?>
                                    <?php if ($isPreview): ?>
                                        <a href="<?= esc($exitUrl) ?>" class="btn btn-danger btn-sm px-3 fw-semibold">
                                            <i class="fas fa-flag me-1"></i> <?= esc($exitLabel) ?>
                                        </a>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-warning btn-sm px-3 fw-semibold text-dark"
                                                onclick="konfirmasiSelesai()">
                                            <i class="fas fa-check me-1"></i> Selesai
                                        </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary btn-sm px-3 fw-semibold"
                                            onclick="showCbtSoal(<?= $idx + 1 ?>)">
                                        Selanjutnya <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- 2. RIGHT COLUMN: Grid Nomor Soal Card -->
        <div class="col-lg-4" id="colGridRight">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden" id="cardNomorSoalGrid">
                <div class="preview-header-info d-flex justify-content-between align-items-center" id="headerNomorSoalGrid">
                    <span><i class="fas fa-th me-2"></i>Nomor Soal</span>
                    <span class="badge bg-white text-primary fw-bold" id="badgeProgress">
                        0 / <?= count($soalListGrouped) ?>
                    </span>
                </div>
                <div class="card-body p-0" id="bodyNomorSoalGrid">

                    <!-- Group Pilihan Ganda -->
                    <?php if ($pgCounter > 0): ?>
                        <div class="px-3 pt-3 pb-1 fw-bold text-muted text-uppercase small tracking-wider border-bottom d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-list-ul me-1 text-primary"></i> Pilihan Ganda</span>
                            <span class="badge bg-primary-subtle text-primary rounded-pill"><?= $pgCounter ?> Soal</span>
                        </div>
                        <div class="nomor-soal-grid-4">
                            <?php foreach ($soalListGrouped as $idx => $ds): ?>
                                <?php if ($ds['groupType'] === 'pg'): ?>
                                    <?php
                                    $idSoal    = $ds['IdSoal'] ?? $ds['id'] ?? $idx;
                                    $isDijawab = isset($jawabanMap[$idSoal]) && $jawabanMap[$idSoal] !== null;
                                    $isRagu    = !empty($raguMap[$idSoal]);
                                    ?>
                                    <div class="btn-circle-num <?= $isDijawab ? 'dijawab' : '' ?> <?= $isRagu ? 'ragu' : '' ?> <?= $idx === 0 ? 'aktif' : '' ?>"
                                         id="num_<?= $idx ?>"
                                         onclick="showCbtSoal(<?= $idx ?>)"
                                         title="Soal PG #<?= $ds['groupNum'] ?>">
                                        <?= str_pad($ds['groupNum'], 2, '0', STR_PAD_LEFT) ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Group Uraian / Esai -->
                    <?php if ($esaiCounter > 0): ?>
                        <div class="px-3 pt-3 pb-1 fw-bold text-muted text-uppercase small tracking-wider border-bottom <?= $pgCounter > 0 ? 'border-top' : '' ?> d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-pen-fancy me-1 text-purple"></i> Uraian / Esai</span>
                            <span class="badge bg-purple-subtle text-purple rounded-pill"><?= $esaiCounter ?> Soal</span>
                        </div>
                        <div class="nomor-soal-grid-4">
                            <?php foreach ($soalListGrouped as $idx => $ds): ?>
                                <?php if ($ds['groupType'] === 'esai'): ?>
                                    <?php
                                    $idSoal    = $ds['IdSoal'] ?? $ds['id'] ?? $idx;
                                    $isDijawab = isset($jawabanMap[$idSoal]) && $jawabanMap[$idSoal] !== null;
                                    $isRagu    = !empty($raguMap[$idSoal]);
                                    ?>
                                    <div class="btn-circle-num <?= $isDijawab ? 'dijawab' : '' ?> <?= $isRagu ? 'ragu' : '' ?> <?= $idx === 0 ? 'aktif' : '' ?>"
                                         id="num_<?= $idx ?>"
                                         onclick="showCbtSoal(<?= $idx ?>)"
                                         title="Soal Esai #<?= $ds['groupNum'] ?>">
                                        <?= str_pad($ds['groupNum'], 2, '0', STR_PAD_LEFT) ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Legend -->
                    <div class="px-3 pb-3 d-flex flex-wrap gap-2 small text-muted border-top pt-2">
                        <span><span class="badge rounded-pill legend-badge-dijawab">&nbsp;&nbsp;</span> Dijawab</span>
                        <span><span class="badge rounded-pill legend-badge-ragu">&nbsp;&nbsp;</span> Ragu</span>
                        <span><span class="badge rounded-pill legend-badge-aktif">&nbsp;&nbsp;</span> Aktif</span>
                        <span><span class="badge rounded-pill legend-badge-belum">&nbsp;&nbsp;</span> Belum</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!$isPreview): ?>
<!-- Hidden Submit Form (Khusus Real Exam) -->
<form id="formSubmitUjian" method="post" action="<?= base_url("backend/ujian-mdta/santri/selesai/{$token}") ?>">
    <?= csrf_field() ?>
</form>

<!-- Modal Konfirmasi Selesai Ujian -->
<div class="modal fade" id="modalKonfirmasiSelesai" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white py-3">
                <h6 class="modal-title fw-bold mb-0">
                    <i class="fas fa-question-circle me-2"></i> Konfirmasi Selesaikan Ujian
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 text-center mb-3">
                    <div class="col-4">
                        <div class="p-2 rounded bg-success text-white">
                            <h4 class="fw-bold mb-0" id="statAnswered">0</h4>
                            <span class="small fw-semibold">Dijawab</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded bg-warning text-dark">
                            <h4 class="fw-bold mb-0" id="statRagu">0</h4>
                            <span class="small fw-semibold">Ragu-Ragu</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded bg-secondary text-white">
                            <h4 class="fw-bold mb-0" id="statBelum">0</h4>
                            <span class="small fw-semibold">Belum</span>
                        </div>
                    </div>
                </div>

                <div id="alertWarningRagu" class="alert alert-warning border-warning p-3 rounded-3 small mb-3 d-none">
                    <span id="alertWarningRaguText"></span>
                </div>

                <p class="text-secondary small mb-0 text-center">
                    Apakah Anda yakin ingin mengumpulkan lembar jawaban ini sekarang? Setelah dikumpulkan, ujian dianggap selesai dan jawaban tidak dapat diubah kembali.
                </p>
            </div>
            <div class="modal-footer bg-light p-3 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary btn-sm px-4 fw-semibold" data-dismiss="modal">
                    <i class="fas fa-arrow-left me-1"></i> Tinjau Soal
                </button>
                <button type="button" class="btn btn-success btn-sm px-4 fw-bold" onclick="submitCbtUjianForm()">
                    <i class="fas fa-paper-plane me-1"></i> Ya, Kumpulkan
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Pause Overlay -->
<div id="pauseOverlayCbt" class="<?= ($sesi['StatusSesi'] ?? '') === 'pause' ? '' : 'd-none' ?>" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(15,23,42,0.95);backdrop-filter:blur(8px);z-index:999999;display:flex;align-items:center;justify-content:center;color:#fff;text-align:center;padding:20px;">
    <div class="text-center p-4 bg-dark border border-warning rounded-4 shadow-lg" style="max-width:500px;">
        <i class="fas fa-pause-circle text-warning fa-4x mb-3"></i>
        <h3 class="fw-bold text-white mb-2">UJIAN DI-PAUSE</h3>
        <p class="text-light fs-6 mb-3">Sesi ujian Anda sedang di-pause sementara oleh Proktor / Pengawas. Waktu dan lembar soal dihentikan sementara.</p>
        <span class="badge bg-warning text-dark px-3 py-2 fw-semibold fs-6">
            <i class="fas fa-spinner fa-spin me-1"></i> Menunggu Pengawas Melanjutkan Ujian...
        </span>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentIndex = 0;
    const totalSoal  = <?= count($soalList) ?>;
    const isPreview  = <?= $isPreview ? 'true' : 'false' ?>;
    const tokenSesi  = '<?= esc($token) ?>';
    let sisaDetik         = <?= (int)$sisaWaktuDetik ?>;
    let isPausedByProctor = <?= (isset($sesi['StatusSesi']) && $sesi['StatusSesi'] === 'pause') ? 'true' : 'false' ?>;
    let isTimeOutHandled  = false;
    let timerInterval     = null;

    function formatTime(totalSeconds) {
        if (totalSeconds <= 0) return '00 : 00 : 00';
        const h = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
        const m = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
        const s = String(totalSeconds % 60).padStart(2, '0');
        return `${h} : ${m} : ${s}`;
    }

    function updateTimer() {
        if (isPausedByProctor) {
            updateClocks(formatTime(sisaDetik));
            const overlay = document.getElementById('cbtPauseOverlay');
            if (overlay) overlay.style.display = 'flex';
            return;
        }

        if (!isPreview && sisaDetik <= 0) {
            if (!isTimeOutHandled) {
                isTimeOutHandled = true;
                if (timerInterval) clearInterval(timerInterval);
                updateClocks('00 : 00 : 00');

                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: '⏰ Waktu Ujian Habis!',
                        text: 'Waktu pengerjaan ujian telah habis. Sistem akan mengumpulkan jawaban Anda secara otomatis.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ef4444'
                    }).then(() => {
                        const form = document.getElementById('formSubmitUjian');
                        if (form) form.submit();
                    });
                } else {
                    alert('⏰ Waktu pengerjaan ujian telah habis! Sistem akan mengumpulkan jawaban Anda secara otomatis.');
                    const form = document.getElementById('formSubmitUjian');
                    if (form) form.submit();
                }
            }
            return;
        }

        const timeStr = formatTime(sisaDetik);
        updateClocks(timeStr);

        // Dynamic Counter Background:
        // 100% - 50%: Hijau (timer-success)
        // 50% - 10%: Kuning (timer-warning)
        // 10% - 0%: Merah (timer-danger)
        if (typeof window.maxTotalDetikCbt === 'undefined' || sisaDetik > window.maxTotalDetikCbt) {
            window.maxTotalDetikCbt = Math.max(sisaDetik, 1);
        }
        const pctSisa = window.maxTotalDetikCbt > 0 ? (sisaDetik / window.maxTotalDetikCbt) * 100 : 0;
        const clockEl = document.getElementById('cbtClockDisplay');
        const topClock = document.getElementById('topLiveClock');

        if (clockEl) {
            clockEl.classList.remove('timer-success', 'timer-warning', 'timer-danger', 'danger-time');
            if (pctSisa < 10) {
                clockEl.classList.add('timer-danger');
            } else if (pctSisa < 50) {
                clockEl.classList.add('timer-warning');
            } else {
                clockEl.classList.add('timer-success');
            }
        }

        if (topClock) {
            if (pctSisa < 10) {
                topClock.style.color = '#dc2626';
            } else if (pctSisa < 50) {
                topClock.style.color = '#ca8a04';
            } else {
                topClock.style.color = '#16a34a';
            }
        }

        if (sisaDetik > 0) sisaDetik--;
    }

    function updateClocks(str) {
        const clockEl = document.getElementById('cbtClockDisplay');
        const topEl   = document.getElementById('topLiveClock');
        if (clockEl) clockEl.textContent = str;
        if (topEl)   topEl.textContent   = str;
    }

    timerInterval = setInterval(updateTimer, 1000);
    updateTimer();

    // ========================
    // Live Polling Kontrol Pengawas (Auto Trigger Pause, Resume, Stop, +Waktu, Reset)
    // ========================
    function pollStatusSesi() {
        if (isPreview || !tokenSesi) return;

        fetch(`<?= site_url("backend/ujian-mdta/santri/cek-status/") ?>${tokenSesi}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data || !data.success) return;

            const currentStatus   = String(data.status || '').toLowerCase().trim();
            const serverSisaDetik = parseInt(data.sisaDetik || 0);

            // 1. PAUSE status (Auto Lock Screen & Freeze Timer)
            if (currentStatus === 'pause') {
                isPausedByProctor = true;
                const overlay = document.getElementById('cbtPauseOverlay');
                if (overlay) overlay.style.display = 'flex';

                if (!window.isPauseToastShowing) {
                    window.isPauseToastShowing = true;
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'warning',
                            title: '⏸️ Ujian Di-Pause oleh Pengawas',
                            text: 'Layar terkunci dan waktu pengerjaan dihentikan sementara.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000
                        });
                    }
                }
                return;
            } 
            // 2. SEDANG status (Auto Resume & Lock Release + Waktu Check)
            else if (currentStatus === 'sedang') {
                if (isPausedByProctor) {
                    isPausedByProctor = false;
                    window.isPauseToastShowing = false;
                    const overlay = document.getElementById('cbtPauseOverlay');
                    if (overlay) overlay.style.display = 'none';

                    if (serverSisaDetik > 0) sisaDetik = serverSisaDetik;
                    updateTimer();

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: '▶️ Ujian Dilanjutkan!',
                            text: 'Pengawas telah melanjutkan ujian. Selamat mengerjakan kembali!',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000
                        });
                    }
                }

                if (serverSisaDetik > sisaDetik + 5) {
                    const extraSec = serverSisaDetik - sisaDetik;
                    const extraMin = Math.round(extraSec / 60);
                    sisaDetik = serverSisaDetik;
                    updateTimer();
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'info',
                            title: '➕ Waktu Diperpanjang!',
                            text: `Pengawas telah menambahkan waktu ujian +${extraMin} menit!`,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3500
                        });
                    }
                }
            } 
            // 3. STOP / SELESAI / TIMEOUT status set by Proctor
            else if (currentStatus === 'selesai' || currentStatus === 'timeout') {
                if (!window.isStopModalShowing) {
                    window.isStopModalShowing = true;
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'info',
                            title: '⏹️ Ujian Selesai',
                            text: 'Ujian telah dihentikan/diselesaikan oleh pengawas.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            confirmButtonText: 'Lihat Hasil'
                        }).then(() => {
                            window.location.href = `<?= site_url("backend/ujian-mdta/santri/hasil/") ?>${tokenSesi}`;
                        });
                    } else {
                        window.location.href = `<?= site_url("backend/ujian-mdta/santri/hasil/") ?>${tokenSesi}`;
                    }
                }
            }
        })
        .catch(e => {
            // Silently ignore transient network errors during background polling
        });
    }

    if (!isPreview && tokenSesi) {
        pollStatusSesi();
        setInterval(pollStatusSesi, 3000);
    }

    // ========================
    // 2. Navigasi Soal
    // ========================
    window.showCbtSoal = function(index) {
        if (index < 0 || index >= totalSoal) return;

        document.querySelectorAll('.soal-block').forEach(el => el.classList.add('d-none'));
        document.querySelectorAll('.btn-circle-num').forEach(el => el.classList.remove('aktif'));

        const targetBlock = document.getElementById(`soal_block_${index}`);
        const targetNum   = document.getElementById(`num_${index}`);

        if (targetBlock) targetBlock.classList.remove('d-none');
        if (targetNum)   targetNum.classList.add('aktif');

        currentIndex = index;
    };

    window.navPrevCbtSoal = function() {
        if (currentIndex > 0) showCbtSoal(currentIndex - 1);
    };

    window.navNextCbtSoal = function() {
        if (currentIndex < totalSoal - 1) showCbtSoal(currentIndex + 1);
    };

    // ========================
    // 3. Selection & Auto-save Jawaban
    // ========================
    document.querySelectorAll('.pilihan-cbt-item').forEach(item => {
        item.addEventListener('click', function () {
            const idSoal     = this.dataset.idsoal;
            const idPilihan  = this.dataset.idpilihan;
            const soalIndex  = parseInt(this.dataset.soalindex);

            // Highlight UI
            const wrapper = this.closest('.pilihan-wrapper-cbt');
            if (wrapper) {
                wrapper.querySelectorAll('.pilihan-cbt-item').forEach(el => el.classList.remove('selected', 'active-choice'));
            }
            this.classList.add('selected', 'active-choice');
            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;

            // Mark grid number as answered
            const numBtn = document.getElementById(`num_${soalIndex}`);
            if (numBtn) numBtn.classList.add('dijawab');

            updateProgress();

            // Perform AJAX auto-save only if NOT in preview mode
            if (!isPreview && tokenSesi) {
                const fd = new FormData();
                fd.append('idSoal', idSoal);
                fd.append('idPilihan', idPilihan);
                fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                fetch(`<?= base_url("backend/ujian-mdta/santri/jawaban/") ?>${tokenSesi}`, {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(r => { if (!r.success) console.warn('Auto-save gagal:', r.message); })
                .catch(e => console.error('Auto-save error:', e));
            }
        });
    });

    // Auto-save & Deteksi Indikator Jawaban Esai / Uraian
    document.querySelectorAll('.input-jawaban-esai').forEach(textarea => {
        let debounceTimer;
        textarea.addEventListener('input', function() {
            const idSoal    = this.dataset.idsoal;
            const soalIndex = parseInt(this.dataset.soalindex);
            const val       = this.value.trim();

            // Update status indikator nomor di grid (Hijau jika terisi, abu-abu jika kosong)
            const numBtn = document.getElementById(`num_${soalIndex}`);
            if (numBtn) {
                if (val.length > 0) {
                    numBtn.classList.add('dijawab');
                } else {
                    numBtn.classList.remove('dijawab');
                }
            }

            updateProgress();

            // Debounced AJAX Auto-Save (simpan 500ms setelah selesai mengetik)
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                if (!isPreview && tokenSesi) {
                    const fd = new FormData();
                    fd.append('idSoal', idSoal);
                    fd.append('jawabanEsai', val);
                    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                    fetch(`<?= base_url("backend/ujian-mdta/santri/jawaban/") ?>${tokenSesi}`, {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(r => { if (!r.success) console.warn('Auto-save esai gagal:', r.message); })
                    .catch(e => console.error('Auto-save esai error:', e));
                }
            }, 500);
        });
    });

    function updateProgress() {
        const answered = document.querySelectorAll('.btn-circle-num.dijawab').length;
        const badge    = document.getElementById('badgeProgress');
        if (badge) badge.textContent = `${answered} / ${totalSoal}`;
    }
    updateProgress();

    // ========================
    // 4. Toggle Ragu-Ragu & Submit Konfirmasi
    // ========================
    window.toggleRaguCbt = function(idx, idSoal) {
        const numBtn  = document.getElementById(`num_${idx}`);
        const btnRagu = document.getElementById(`btn_ragu_${idx}`);
        if (!numBtn || !btnRagu) return;

        const isRaguNow = numBtn.classList.toggle('ragu');
        btnRagu.classList.toggle('active-ragu', isRaguNow);

        if (!isPreview && tokenSesi) {
            const fd = new FormData();
            fd.append('idSoal', idSoal);
            fd.append('isRagu', isRaguNow ? 1 : 0);
            fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            fetch(`<?= base_url("backend/ujian-mdta/santri/ragu/") ?>${tokenSesi}`, {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(r => { if (!r.success) console.warn('Save ragu-ragu failed:', r.message); })
            .catch(e => console.error('Save ragu-ragu error:', e));
        }
    };

    window.submitCbtUjianForm = function() {
        const form = document.getElementById('formSubmitUjian');
        if (form) form.submit();
    };

    window.konfirmasiSelesai = function () {
        if (isPreview) return;

        const answered   = document.querySelectorAll('.btn-circle-num.dijawab').length;
        const raguCount  = document.querySelectorAll('.btn-circle-num.ragu').length;
        const unanswered = totalSoal - answered;

        // Populate modal stats if present
        const elAns   = document.getElementById('statAnswered');
        const elRagu  = document.getElementById('statRagu');
        const elBelum = document.getElementById('statBelum');
        if (elAns)   elAns.textContent   = answered;
        if (elRagu)  elRagu.textContent  = raguCount;
        if (elBelum) elBelum.textContent = unanswered;

        const alertBox = document.getElementById('alertWarningRagu');
        const alertTxt = document.getElementById('alertWarningRaguText');
        if (alertBox && alertTxt) {
            if (raguCount > 0 && unanswered > 0) {
                alertTxt.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> Perhatian: Masih ada <strong>${raguCount} soal Ragu-Ragu (Kuning)</strong> dan <strong>${unanswered} soal Belum Dijawab</strong>!`;
                alertBox.classList.remove('d-none');
            } else if (raguCount > 0) {
                alertTxt.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> Perhatian: Masih ada <strong>${raguCount} soal Ragu-Ragu (Kuning)</strong> yang perlu ditinjau kembali!`;
                alertBox.classList.remove('d-none');
            } else if (unanswered > 0) {
                alertTxt.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> Perhatian: Masih ada <strong>${unanswered} soal Belum Dijawab</strong>!`;
                alertBox.classList.remove('d-none');
            } else {
                alertBox.classList.add('d-none');
            }
        }

        if (window.jQuery && $('#modalKonfirmasiSelesai').length) {
            $('#modalKonfirmasiSelesai').modal('show');
        } else {
            let bodyHtml = `<div class="text-start small">`;
            bodyHtml += `<div>✅ <strong>Sudah Dijawab:</strong> ${answered} / ${totalSoal} soal</div>`;
            if (raguCount > 0) bodyHtml += `<div class="text-warning fw-bold">🟨 <strong>Ragu-Ragu:</strong> ${raguCount} soal</div>`;
            if (unanswered > 0) bodyHtml += `<div class="text-danger fw-bold">❌ <strong>Belum Dijawab:</strong> ${unanswered} soal</div>`;
            if (raguCount > 0 || unanswered > 0) {
                bodyHtml += `<div class="alert alert-warning mt-2 p-2 small"><i class="fas fa-exclamation-triangle me-1"></i> Disarankan meninjau kembali soal berwarna kuning/belum dijawab!</div>`;
            }
            bodyHtml += `<div class="mt-3">Yakin ingin mengumpulkan ujian sekarang?</div></div>`;

            Swal.fire({
                title: '🏁 Konfirmasi Selesai Ujian',
                html: bodyHtml,
                icon: (raguCount > 0 || unanswered > 0) ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> Ya, Kumpulkan',
                cancelButtonText: 'Tinjau Soal',
                confirmButtonColor: '#10b981'
            }).then((res) => {
                if (res.isConfirmed) {
                    submitCbtUjianForm();
                }
            });
        }
    };

    const btnSubmitTop = document.getElementById('btnSubmitUjianTop');
    if (btnSubmitTop) btnSubmitTop.addEventListener('click', konfirmasiSelesai);

    // ========================
    // 5. Toggle Grid Nomor Soal (ColGridRight hides & ColSoalLeft expands to col-lg-12)
    // ========================
    const btnToggleGrid = document.getElementById('btnToggleGrid');
    if (btnToggleGrid) {
        btnToggleGrid.addEventListener('click', function () {
            const colLeft  = document.getElementById('colSoalLeft');
            const colRight = document.getElementById('colGridRight');

            if (colRight) {
                const isHidden = colRight.classList.toggle('d-none');
                if (colLeft) {
                    if (isHidden) {
                        colLeft.classList.remove('col-lg-8');
                        colLeft.classList.add('col-lg-12');
                    } else {
                        colLeft.classList.remove('col-lg-12');
                        colLeft.classList.add('col-lg-8');
                    }
                }
            }
        });
    }

    // Live status polling and proktor actions are handled by pollStatusSesi()
});

// ========================
// Font Size Adjustment
// ========================
let cbtFontSize = 16;
function adjustCbtFontSize(delta) {
    cbtFontSize = Math.min(36, Math.max(12, cbtFontSize + delta));

    document.querySelectorAll('.uraian-soal-text, .pilihan-cbt-item span:not(.circle-letter)').forEach(el => {
        el.style.fontSize = `${cbtFontSize}px`;
    });

    const circleSize = Math.max(30, cbtFontSize + 14);
    document.querySelectorAll('.circle-letter').forEach(el => {
        el.style.width = `${circleSize}px`;
        el.style.height = `${circleSize}px`;
        el.style.minWidth = `${circleSize}px`;
        el.style.minHeight = `${circleSize}px`;
        el.style.fontSize = `${Math.round(cbtFontSize * 0.85)}px`;
    });

    const scaleRatio = cbtFontSize / 16;
    document.querySelectorAll('.uraian-soal-text img, .pilihan-cbt-item img, figure.image img').forEach(img => {
        if (!img.dataset.origPercent) {
            const parentFig = img.closest('figure');
            const figWidth  = parentFig ? parentFig.style.width : '';
            img.dataset.origPercent = parseFloat(img.style.width || figWidth || '100') || 100;
        }
        const basePct = parseFloat(img.dataset.origPercent);
        const newPct  = Math.min(100, Math.max(15, Math.round(basePct * scaleRatio)));
        const figure = img.closest('figure');
        if (figure) figure.style.width = `${newPct}%`;
        img.style.width = `${newPct}%`;
        img.style.height = 'auto';
        img.style.maxWidth = '100%';
    });
}
</script>
