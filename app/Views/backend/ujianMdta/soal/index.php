<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<style>
    /* Styling khusus mengadopsi UI Paket Soal dari referensi */
    .stat-card-custom {
        background-color: #faf8f0;
        border: 1px solid #eae6d9;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
    }
    .stat-card-custom-white {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
    }
    .stat-card-title {
        font-size: 0.75rem;
        font-weight: 700;
        color: #888;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
        text-transform: uppercase;
    }
    .stat-card-value {
        font-size: 1.6rem;
        font-weight: 800;
        color: #222;
    }

    .info-alert-custom {
        background-color: #e0f2fe;
        border: 1px solid #bae6fd;
        color: #0369a1;
        border-radius: 8px;
        padding: 12px 18px;
    }

    /* Option item & score badge */
    .option-item-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 10px 0;
    }
    .option-badge-circle {
        width: 32px;
        height: 32px;
        min-width: 32px;
        min-height: 32px;
        max-width: 32px;
        max-height: 32px;
        border-radius: 50%;
        border: 1px solid #ccc;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        margin-right: 12px;
        color: #555;
        background: #fff;
        flex-shrink: 0;
        align-self: flex-start;
        margin-top: 2px;
    }
    .option-badge-circle.selected-correct {
        background-color: #0ea5e9;
        color: #fff;
        border-color: #0ea5e9;
    }
    .score-badge-zero,
    .score-badge-one {
        display: inline-block !important;
        white-space: nowrap !important;
        word-break: keep-all !important;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 4px 12px;
        border-radius: 12px;
        line-height: 1.2;
        flex-shrink: 0;
    }
    .score-badge-zero {
        background-color: #f97316;
        color: #ffffff;
    }
    .score-badge-one {
        background-color: #22c55e;
        color: #ffffff;
    }

    /* Number circle buttons in right column */
    .nomor-soal-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
        padding: 15px;
    }
    .btn-circle-num {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 1.5px solid #facc15;
        background-color: #fef08a;
        color: #854d0e;
        font-weight: 700;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-circle-num:hover {
        transform: scale(1.08);
        border-color: #eab308;
    }
    .btn-circle-num.active-num {
        background-color: #000000 !important;
        color: #ffffff !important;
        border-color: #000000 !important;
    }

    .badge-pilihan-ganda {
        background-color: #38bdf8;
        color: #fff;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 5px 10px;
        border-radius: 6px;
    }
    .btn-action-light {
        background-color: #ffffff;
        border: 1px solid #d1d5db;
        color: #374151;
        font-size: 0.85rem;
        font-weight: 500;
        padding: 5px 12px;
        border-radius: 6px;
    }
    .btn-action-light:hover {
        background-color: #f3f4f6;
        color: #111827;
    }

    /* CSS Support Rendering Image Resizing dari CKEditor 5 */
    figure.image, .uraian-text-content figure.image {
        margin: 10px auto;
        display: table;
        max-width: 100%;
    }
    figure.image-style-side {
        float: right;
        margin-left: 15px;
        max-width: 50%;
    }
    figure.image img, .uraian-text-content img {
        height: auto !important;
        max-width: 100%;
    }
    figure.image[style*="width"] {
        max-width: 100% !important;
    }

    /* Complete Dark Mode Adaptations for Daftar Soal (index.php) */
    body.dark-mode h4,
    body.dark-mode h4.text-dark,
    body.dark-mode h5.fw-bold {
        color: #f8fafc !important;
    }
    body.dark-mode small.text-muted {
        color: #94a3b8 !important;
    }
    body.dark-mode .stat-card-custom,
    body.dark-mode .stat-card-custom-white {
        background-color: #1e293b !important;
        border-color: #334155 !important;
    }
    body.dark-mode .stat-card-title {
        color: #94a3b8 !important;
    }
    body.dark-mode .stat-card-value {
        color: #f8fafc !important;
    }
    body.dark-mode .info-alert-custom {
        background-color: #0f172a !important;
        border-color: #0284c7 !important;
        color: #38bdf8 !important;
    }
    body.dark-mode .card-soal-display {
        background-color: #1e293b !important;
        border-color: #334155 !important;
    }
    body.dark-mode .uraian-text-content,
    body.dark-mode .uraian-text-content p,
    body.dark-mode .uraian-text-content span,
    body.dark-mode .option-item-row .text-dark {
        color: #f1f5f9 !important;
    }
    body.dark-mode .option-item-row {
        border-color: #334155 !important;
    }
    body.dark-mode .option-badge-circle {
        background-color: #0f172a;
        border-color: #475569;
        color: #f1f5f9;
    }
    body.dark-mode .option-badge-circle.selected-correct {
        background-color: #0284c7 !important;
        color: #ffffff !important;
        border-color: #38bdf8 !important;
    }
    body.dark-mode .card-header.bg-light {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #38bdf8 !important;
    }
    body.dark-mode .btn-circle-num {
        background-color: #78350f;
        color: #fef08a;
        border-color: #eab308;
    }
    body.dark-mode .btn-circle-num.active-num {
        background-color: #38bdf8 !important;
        color: #0f172a !important;
        border-color: #38bdf8 !important;
    }
    body.dark-mode .btn-action-light {
        background-color: #1e293b;
        border-color: #475569;
        color: #f1f5f9;
    }
    body.dark-mode .btn-action-light:hover {
        background-color: #334155;
        color: #38bdf8;
    }
    body.dark-mode .dropdown-menu {
        background-color: #1e293b;
        border-color: #334155;
    }
    body.dark-mode .dropdown-item {
        color: #f1f5f9;
    }
    body.dark-mode .dropdown-item:hover {
        background-color: #334155;
        color: #38bdf8;
    }
</style>

<div class="container-fluid">

    <!-- Top Header Bar -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-4">
            <h4 class="fw-bold mb-0 text-uppercase d-flex align-items-center" style="letter-spacing: 0.5px;">
                DAFTAR SOAL
                <?php if (!empty($isGlobalReadOnly)): ?>
                    <span class="badge bg-primary fs-6 ms-2"><i class="fas fa-globe me-1"></i>GLOBAL / PUSAT</span>
                <?php endif; ?>
            </h4>
        </div>
        <div class="col-md-8 text-end">
            <div class="d-flex gap-2 justify-content-end flex-wrap">
                <?php if (!empty($isGlobalReadOnly)): ?>
                    <a href="<?= base_url("backend/ujian-mdta/paket/duplikasi/{$paket['id']}") ?>" class="btn btn-warning btn-sm px-3 fw-bold shadow-sm">
                        <i class="fas fa-copy me-1"></i> Duplikasi Ke TPQ Saya
                    </a>
                <?php else: ?>
                    <a href="<?= base_url("backend/ujian-mdta/paket/{$paket['id']}/soal/create") ?>" class="btn btn-primary btn-sm px-3 fw-semibold">
                        <i class="fas fa-plus me-1"></i> Tambah Soal
                    </a>
                <?php endif; ?>
                <a href="<?= base_url("backend/ujian-mdta/paket/preview/{$paket['id']}") ?>" target="_blank" class="btn btn-action-light btn-sm">
                    <i class="fas fa-eye me-1"></i> Preview
                </a>
                <?php if (empty($isGlobalReadOnly)): ?>
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-action-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-file-import me-1"></i> Import
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-item small" href="#"><i class="fas fa-file-excel me-2 text-success"></i>Import dari Excel</a></li>
                            <li><a class="dropdown-item small" href="#"><i class="fas fa-file-word me-2 text-primary"></i>Import dari Word</a></li>
                        </ul>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm px-3" id="btnKosongkanSoal">
                        <i class="fas fa-trash-alt me-1"></i> Kosongkan
                    </button>
                <?php endif; ?>
                <a href="<?= base_url('backend/ujian-mdta/paket') ?>" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>



    <!-- Alert Success / Error Handled via SweetAlert2 -->

    <!-- Title & 3 AdminLTE Summary Stat Cards -->
    <div class="mb-4">
        <h4 class="fw-bold mb-1 text-dark"><?= esc($paket['NamaPaket']) ?></h4>
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge bg-light text-dark border"><i class="fas fa-book me-1 text-primary"></i><?= esc($paket['NamaMateri'] ?? 'Materi') ?></span>
            <span class="badge bg-light text-dark border"><i class="fas fa-layer-group me-1 text-success"></i>Kelas: <?= esc($paket['NamaKelas'] ?? '-') ?></span>
        </div>

        <?php
        $totalPG = 0;
        $totalEsai = 0;
        if (!empty($soalList)) {
            foreach ($soalList as $s) {
                if (($s['JenisSoal'] ?? 'pilihan_ganda') === 'esai') {
                    $totalEsai++;
                } else {
                    $totalPG++;
                }
            }
        }
        ?>

        <div class="row g-3">
            <!-- Card 1: Total Soal -->
            <div class="col-md-4 col-sm-6">
                <div class="small-box bg-info shadow-sm rounded-3">
                    <div class="inner p-3">
                        <h3 class="fw-bold text-white mb-1"><?= count($soalList) ?></h3>
                        <p class="text-white-50 mb-0 fw-semibold">Total Butir Soal</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-list-ol"></i>
                    </div>
                </div>
            </div>

            <!-- Card 2: Pilihan Ganda & Esai -->
            <div class="col-md-4 col-sm-6">
                <div class="small-box bg-success shadow-sm rounded-3">
                    <div class="inner p-3">
                        <h3 class="fw-bold text-white mb-1">
                            <?= $totalPG ?> <span class="fs-6 fw-normal text-white-50 ms-1">(Esai: <?= $totalEsai ?>)</span>
                        </h3>
                        <p class="text-white-50 mb-0 fw-semibold">Pilihan Ganda & Esai</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-square"></i>
                    </div>
                </div>
            </div>

            <!-- Card 3: Skala Nilai Maksimum -->
            <div class="col-md-4 col-sm-6">
                <div class="small-box bg-warning shadow-sm rounded-3">
                    <div class="inner p-3">
                        <h3 class="fw-bold text-dark mb-1"><?= (int)($paket['SkalaNilai'] ?? 100) ?></h3>
                        <p class="text-dark mb-0 fw-semibold opacity-75">Skala Nilai Maksimum</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Alert Box -->
    <div class="info-alert-custom mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle fa-lg me-2 text-primary"></i>
            <div>
                <strong class="text-primary">Informasi</strong><br>
                <span class="small text-secondary">Untuk menambahkan soal pada paket soal ini, klik pada tombol Tambah Soal atau Import Soal di kanan atas. Klik pada pilihan tombol soal di sebelah kanan untuk menampilkan soal yang sesuai.</span>
            </div>
        </div>
    </div>

    <!-- Main Content Area: 2 Columns Grid -->
    <div class="row g-4 mb-5">

        <!-- Kolom Kiri: Display & Kontrol Soal Aktif (7 Columns) -->
        <div class="col-lg-7">
            <?php if (empty($soalList)): ?>
                <div class="card border p-5 text-center shadow-xs">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted fw-bold">Belum ada soal dalam paket ini</h5>
                    <p class="text-muted small">Klik tombol <strong>"Tambah Soal"</strong> untuk memasukkan soal pertama.</p>
                    <div>
                        <a href="<?= base_url("backend/ujian-mdta/paket/{$paket['id']}/soal/create") ?>" class="btn btn-primary px-4">
                            <i class="fas fa-plus me-1"></i> Tambah Soal
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($soalList as $index => $soal): ?>
                    <div class="card card-soal-display shadow-xs mb-3 <?= $index > 0 ? 'd-none' : '' ?>" id="card_soal_<?= $index ?>">
                        <div class="card-body p-4">

                            <!-- Question Header Row -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h5 class="fw-bold mb-0 me-2"><?= $index + 1 ?>.</h5>
                                    <?php if (($soal['JenisSoal'] ?? 'pilihan_ganda') === 'esai'): ?>
                                        <span class="badge text-white px-3 py-2 fw-bold" style="background-color: #8b5cf6; border-radius: 6px;">Esai / Uraian</span>
                                    <?php else: ?>
                                        <span class="badge-pilihan-ganda">Pilihan Ganda</span>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-outline-warning btn-sm text-dark fw-semibold" style="background-color: #fed7aa; border-color:#f97316;" onclick="Swal.fire('Fitur Soal Bonus', 'Semua santri yang mengerjakan soal ini akan mendapatkan nilai penuh.', 'info')">Set Soal Bonus</button>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="btn-group btn-group-sm me-1">
                                        <button type="button" class="btn btn-action-light btn-sm px-2 fw-bold" onclick="adjustIndexFontSize(-2)" title="Perkecil Teks & Gambar">A-</button>
                                        <button type="button" class="btn btn-action-light btn-sm px-2 fw-bold" onclick="adjustIndexFontSize(2)" title="Perbesar Teks & Gambar">A+</button>
                                    </div>
                                    <button type="button" class="btn btn-action-light btn-sm btn-next-soal" data-next="<?= $index + 1 ?>">
                                        Next
                                    </button>
                                </div>
                            </div>

                            <!-- Uraian Pertanyaan -->
                            <div class="uraian-text-content my-3 fs-6 lh-base text-dark">
                                <?= $soal['UraianSoal'] ?>
                            </div>

                            <!-- Opsi Jawaban / Info Esai -->
                            <div class="my-4 border-top border-bottom py-2">
                                <?php if (($soal['JenisSoal'] ?? 'pilihan_ganda') === 'esai'): ?>
                                    <div class="alert alert-purple p-3 rounded-3 my-2" style="background-color: #f3e8ff; border: 1px solid #d8b4fe; color: #6b21a8;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-pen-nib fa-lg me-3 text-purple" style="color: #8b5cf6;"></i>
                                            <div>
                                                <strong>Soal Esai / Uraian Bebas</strong><br>
                                                <span class="small">Santri akan mengisikan jawaban uraian tertulis pada lembar ujian. Penilaian dilakukan oleh guru/operator.</span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (!empty($soal['Pembahasan'])): ?>
                                        <div class="mt-3 p-3 bg-light rounded border">
                                            <strong class="text-secondary small d-block mb-1"><i class="fas fa-lightbulb text-warning me-1"></i>Kunci Jawaban / Uraian Pembahasan:</strong>
                                            <div class="small text-dark"><?= $soal['Pembahasan'] ?></div>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if (!empty($soal['pilihan'])): ?>
                                        <?php foreach ($soal['pilihan'] as $p): ?>
                                            <div class="option-item-row border-bottom py-2">
                                                <div class="d-flex align-items-start flex-grow-1 me-2">
                                                    <span class="option-badge-circle <?= $p['IsBenar'] == 1 ? 'selected-correct' : '' ?>">
                                                        <?= esc($p['HurufPilihan']) ?>
                                                    </span>
                                                    <div class="text-dark small flex-grow-1 align-self-start pt-1">
                                                        <?= $p['TeksPilihan'] ?>
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0 ms-2 mt-1">
                                                    <?php if ($p['IsBenar'] == 1): ?>
                                                        <span class="score-badge-one">skor: 1</span>
                                                    <?php else: ?>
                                                        <span class="score-badge-zero">skor: 0</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Footer Bar: Edit / Duplikasi / Hapus & Indicators -->
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2">
                                <div class="d-flex gap-2">
                                    <?php if (empty($isGlobalReadOnly)): ?>
                                        <a href="<?= base_url("backend/ujian-mdta/soal/edit/{$soal['id']}") ?>" class="btn btn-action-light btn-sm">
                                            Edit
                                        </a>
                                        <button type="button" class="btn btn-warning btn-sm text-dark px-3 btn-duplikasi-single-soal" data-id="<?= $soal['id'] ?>" data-no="<?= $index + 1 ?>" title="Duplikasi soal ini">
                                            <i class="fas fa-copy me-1"></i> Duplikasi
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm px-3 btn-delete-single-soal" data-id="<?= $soal['id'] ?>" data-no="<?= $index + 1 ?>">
                                            Hapus
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border px-3 py-2"><i class="fas fa-lock me-1"></i> Soal Read-Only</span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-3 small text-muted">
                                    <span class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i>Acak Soal</span>
                                    <span class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i>Acak Jawaban</span>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Kolom Kanan: Grid Panel NOMOR SOAL (5 Columns) -->
        <div class="col-lg-5">
            <div class="card shadow-xs border rounded-3">
                <div class="card-header bg-light py-2 text-center border-bottom">
                    <h6 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 0.5px;">NOMOR SOAL</h6>
                </div>
                <div class="card-body p-3">
                    <!-- Hint Alert -->
                    <div class="alert alert-info py-2 px-3 small border-0 mb-3" style="background-color: #e0f2fe; color: #0369a1; border-radius: 6px;">
                        Petunjuk. Warna background pada nomor soal menggambarkan sebaran perbedaan skor masing-masing soal. Tanda tersebut dapat membantu Anda mengidentifikasi butir soal mana yang skornya masih belum sesuai.
                    </div>

                    <!-- Grid Circular Buttons -->
                    <?php if (!empty($soalList)): ?>
                        <div class="nomor-soal-grid">
                            <?php foreach ($soalList as $idx => $s): ?>
                                <div class="btn-circle-num <?= $idx == 0 ? 'active-num' : '' ?>"
                                     id="num_btn_<?= $idx ?>"
                                     data-index="<?= $idx ?>"
                                     onclick="setActiveSoal(<?= $idx ?>)">
                                    <?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted small">
                            Belum ada nomor soal.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Form Hapus Hidden -->
<form id="formHapusSoalIndex" method="post" style="display:none;">
    <?= csrf_field() ?>
</form>

<script>
let currentSoalIndex = 0;
const totalSoalCount = <?= count($soalList) ?>;
let indexFontSize = 16;

function adjustIndexFontSize(delta) {
    indexFontSize += delta;
    if (indexFontSize < 12) indexFontSize = 12;
    if (indexFontSize > 36) indexFontSize = 36;

    // Skalakan ukuran teks soal dan pilihan jawaban
    document.querySelectorAll('.uraian-text-content, .option-item-row, .option-item-row div').forEach(el => {
        el.style.fontSize = `${indexFontSize}px`;
    });

    // Skalakan lingkaran huruf pilihan
    const circleSize = Math.max(30, indexFontSize + 14);
    document.querySelectorAll('.option-badge-circle').forEach(el => {
        el.style.width = `${circleSize}px`;
        el.style.height = `${circleSize}px`;
        el.style.minWidth = `${circleSize}px`;
        el.style.minHeight = `${circleSize}px`;
        el.style.fontSize = `${Math.round(indexFontSize * 0.85)}px`;
    });

    // Skalakan Gambar di Uraian Soal dan Pilihan Jawaban
    const scaleRatio = indexFontSize / 16;
    document.querySelectorAll('.uraian-text-content img, .option-item-row img, figure.image img').forEach(img => {
        if (!img.dataset.origPercent) {
            const parentFig = img.closest('figure');
            const figWidth  = parentFig ? parentFig.style.width : '';
            const imgWidth  = img.style.width || figWidth || '100%';
            img.dataset.origPercent = parseFloat(imgWidth) || 100;
        }
        const basePct = parseFloat(img.dataset.origPercent);
        const newPct  = Math.min(100, Math.max(15, Math.round(basePct * scaleRatio)));

        const figure = img.closest('figure');
        if (figure) {
            figure.style.width = `${newPct}%`;
        }
        img.style.width = `${newPct}%`;
        img.style.height = 'auto';
        img.style.maxWidth = '100%';
    });
}

function setActiveSoal(index) {
    if (index >= totalSoalCount) {
        index = 0; // wrap around back to first
    }
    // Sembunyikan semua card soal
    document.querySelectorAll('.card-soal-display').forEach(c => c.classList.add('d-none'));
    // Hilangkan status active dari semua tombol nomor
    document.querySelectorAll('.btn-circle-num').forEach(b => b.classList.remove('active-num'));

    // Tampilkan card soal terpilih
    const activeCard = document.getElementById(`card_soal_${index}`);
    const activeBtn  = document.getElementById(`num_btn_${index}`);

    if (activeCard) activeCard.classList.remove('d-none');
    if (activeBtn) activeBtn.classList.add('active-num');

    currentSoalIndex = index;
}

document.addEventListener('DOMContentLoaded', function () {
    // Tombol Next di dalam card soal
    document.querySelectorAll('.btn-next-soal').forEach(btn => {
        btn.addEventListener('click', function () {
            const nextIdx = (currentSoalIndex + 1) % totalSoalCount;
            setActiveSoal(nextIdx);
        });
    });

    // Duplikasi single soal
    document.querySelectorAll('.btn-duplikasi-single-soal').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const no = this.dataset.no;
            Swal.fire({
                title: `Duplikasi Soal No. ${no}?`,
                text: 'Soal beserta pilihan jawabannya akan digandakan ke nomor baru.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-copy me-1"></i> Ya, Duplikasi',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('formHapusSoalIndex');
                    form.action = `<?= base_url('backend/ujian-mdta/soal/duplikasi/') ?>/${id}`;
                    form.method = 'POST';
                    form.submit();
                }
            });
        });
    });

    // Hapus single soal
    document.querySelectorAll('.btn-delete-single-soal').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const no = this.dataset.no;
            Swal.fire({
                title: `Hapus Soal No. ${no}?`,
                text: 'Soal ini akan dihapus dari paket soal.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('formHapusSoalIndex');
                    form.action = `<?= base_url('backend/ujian-mdta/soal/delete/') ?>/${id}`;
                    form.method = 'POST';
                    form.submit();
                }
            });
        });
    });

    // Kosongkan seluruh paket soal
    const btnKosongkan = document.getElementById('btnKosongkanSoal');
    if (btnKosongkan) {
        btnKosongkan.addEventListener('click', function () {
            Swal.fire({
                title: 'Kosongkan Semua Soal?',
                text: 'Seluruh soal dalam paket ini akan dihapus secara permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> Ya, Kosongkan Paket',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('formHapusSoalIndex');
                    form.action = `<?= base_url("backend/ujian-mdta/paket/{$paket['id']}/soal/kosongkan") ?>`;
                    form.method = 'POST';
                    form.submit();
                }
            });
        });
    }

    // SweetAlert2 Flash Notifications
    <?php if (session()->getFlashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= esc(session()->getFlashdata('success'), 'js') ?>',
            timer: 2500,
            showConfirmButton: false
        });
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '<?= esc(session()->getFlashdata('error'), 'js') ?>',
            timer: 4000,
            showConfirmButton: true
        });
    <?php endif; ?>
});
</script>

<?= $this->endSection(); ?>
