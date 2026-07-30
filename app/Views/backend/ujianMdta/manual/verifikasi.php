<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<style>
    .verifikasi-card {
        min-height: calc(100vh - 160px);
    }
    .santri-item-btn {
        text-align: left;
        border-radius: 6px;
        margin-bottom: 4px;
        transition: all 0.2s ease;
        font-size: 13px;
    }
    .santri-item-btn.active {
        background-color: #1b5e20 !important;
        color: #fff !important;
        border-color: #1b5e20 !important;
    }
    .santri-item-btn.active small {
        color: #e8f5e9 !important;
    }
    .image-viewer-container {
        position: relative;
        width: 100%;
        height: 520px;
        background-color: #1e1e1e;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px dashed #444;
    }
    .image-viewer-container img {
        max-width: 100%;
        max-height: 100%;
        transition: transform 0.25s ease;
        cursor: grab;
    }
    .image-controls {
        position: absolute;
        bottom: 12px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.75);
        padding: 6px 14px;
        border-radius: 20px;
        display: flex;
        gap: 10px;
        z-index: 10;
    }
    .image-controls button {
        background: transparent;
        border: none;
        color: #fff;
        font-size: 15px;
        cursor: pointer;
        padding: 2px 6px;
        border-radius: 4px;
    }
    .image-controls button:hover {
        color: #28a745;
    }
    .btn-overlay-on {
        background-color: #28a745 !important;
        color: #ffffff !important;
    }
    .btn-overlay-off {
        background-color: transparent !important;
        color: #cccccc !important;
    }
    .soal-grid-row {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 10px 14px;
        margin-bottom: 8px;
        border-left: 4px solid #ced4da;
        transition: all 0.2s ease;
    }
    .soal-grid-row.answered {
        border-left-color: #0d6efd;
        background: #f0f7ff;
    }
    .soal-grid-row.correct-row {
        border-left-color: #198754 !important;
        background: #e8f5e9 !important;
    }
    .soal-grid-row.wrong-row {
        border-left-color: #dc3545 !important;
        background: #ffebee !important;
    }
    .btn-pil-option {
        min-width: 42px;
        height: 38px;
        font-weight: 700;
        border-radius: 20px;
        margin-right: 4px;
        font-size: 13px;
        transition: all 0.15s ease;
    }
    .btn-pil-option.active {
        background-color: #0d6efd !important;
        color: #fff !important;
        border-color: #0d6efd !important;
        box-shadow: 0 2px 6px rgba(13, 110, 253, 0.4);
    }
    .btn-pil-option.btn-correct-active {
        background-color: #198754 !important;
        color: #fff !important;
        border-color: #198754 !important;
        box-shadow: 0 2px 6px rgba(25, 135, 84, 0.4);
    }
    .btn-pil-option.btn-wrong-active {
        background-color: #dc3545 !important;
        color: #fff !important;
        border-color: #dc3545 !important;
        box-shadow: 0 2px 6px rgba(220, 53, 69, 0.4);
    }
    .btn-pil-option.btn-key-highlight {
        border: 2px solid #198754 !important;
        color: #198754 !important;
        background-color: #ffffff !important;
        font-weight: 800;
    }
</style>

<!-- jsQR Library for QR Code Auto-Scan -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>

<div class="container-fluid">
    <!-- Header Page -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-8">
            <h4 class="mb-0 fw-bold">
                <i class="fas fa-camera text-success me-2"></i> Verifikasi Jawaban Manual MDTA
            </h4>
            <small class="text-muted">
                Jadwal: <strong><?= esc($jadwal['NamaUjian']) ?></strong> — Paket: <?= esc($paket['NamaPaket'] ?? '-') ?> (Kelas <?= esc($jadwal['NamaKelas'] ?? '') ?>)
            </small>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= base_url('backend/ujian-mdta/jadwal') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Jadwal
            </a>
            <a href="<?= base_url('backend/ujian-mdta/jadwal/cetak-manual/' . $jadwal['id']) ?>" target="_blank" class="btn btn-outline-success btn-sm ms-1">
                <i class="fas fa-print me-1"></i> Cetak Ujian
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Panel 1: Roster Santri (Kiri - 3 Kolom) -->
        <div class="col-lg-3 col-md-4 mb-3">
            <div class="card card-outline card-success shadow-sm verifikasi-card">
                <div class="card-header p-2">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="searchSantriInput" placeholder="Cari santri..." onkeyup="filterSantriRoster()">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-2" style="max-height: 650px; overflow-y: auto;">
                    <div id="santriRosterList">
                        <?php foreach ($santriList as $s): ?>
                            <?php
                            $tipe = $s['TipePengerjaan'] ?? 'online';
                            $statusSesi = strtolower($s['StatusSesi'] ?? 'belum');
                            $hasFoto = !empty($s['FotoJawaban']);

                            $badgeClass = 'bg-secondary';
                            $statusLabel = 'Belum Ada Sesi';

                            if ($statusSesi === 'selesai') {
                                if ($tipe === 'manual') {
                                    $badgeClass = 'bg-success';
                                    $statusLabel = 'Verifikasi Manual';
                                } else {
                                    $badgeClass = 'bg-info text-dark';
                                    $statusLabel = 'CBT Online';
                                }
                            } else if (in_array($statusSesi, ['sedang', 'pause'])) {
                                $badgeClass = 'bg-warning text-dark';
                                $statusLabel = 'Sedang Ujian CBT';
                            }
                            ?>

                            <button type="button" class="btn btn-light btn-block santri-item-btn p-2 border item-santri-btn"
                                    id="btn-santri-<?= $s['IdSantri'] ?>"
                                    data-santri-id="<?= $s['IdSantri'] ?>"
                                    data-nama="<?= esc($s['NamaSantri']) ?>"
                                    onclick="loadSantriVerification(<?= $jadwal['id'] ?>, <?= $s['IdSantri'] ?>, null, true)">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="d-block text-truncate" style="max-width: 170px;">
                                            <?= esc($s['NamaSantri']) ?>
                                        </strong>
                                        <small class="text-muted">NIS: <?= esc($s['NISN'] ?: $s['IdSantri']) ?></small>
                                    </div>
                                    <span class="badge <?= $badgeClass ?> font-size-11 status-badge" style="font-size: 10px;">
                                        <?= $statusLabel ?>
                                    </span>
                                </div>
                                <div class="nilai-container mt-1 d-flex justify-content-between align-items-center">
                                    <?php if (!is_null($s['NilaiAkhir'])): ?>
                                        <small class="text-success fw-bold nilai-text"><i class="fas fa-check-circle me-1"></i> Nilai: <?= number_format((float)$s['NilaiAkhir'], 1) ?></small>
                                        <?php if ($hasFoto): ?>
                                            <small class="text-primary foto-icon"><i class="fas fa-image" title="Ada Foto LJK"></i></small>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel 2: Viewer Foto LJK (Tengah - 4 Kolom) -->
        <div class="col-lg-4 col-md-8 mb-3">
            <div class="card card-outline card-primary shadow-sm verifikasi-card">
                <div class="card-header p-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-image me-1"></i> Foto / Scan LJK Fisik</h6>
                    <div>
                        <button type="button" class="btn btn-sm btn-success me-1 font-weight-bold" id="btnAutoDetectOmr" style="display: none;" onclick="runAutoDetectOMR()" title="Auto-Detect bulatan OMR & QR Code">
                            <i class="fas fa-magic me-1"></i> Auto-Detect (OMR)
                        </button>
                        <label class="btn btn-sm btn-outline-primary mb-0" for="uploadFotoInput">
                            <i class="fas fa-upload me-1"></i> Upload Foto
                        </label>
                        <input type="file" id="uploadFotoInput" accept="image/*" style="display: none;" onchange="handleFotoUploadChange(this)">
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="image-viewer-container" id="imageViewerBox" style="position: relative; overflow: hidden;">
                        <div class="text-center text-muted p-4" id="imagePlaceholder">
                            <i class="fas fa-camera fa-3x mb-2"></i>
                            <p class="mb-0 small">Pilih santri di sebelah kiri, lalu foto / upload gambar lembar jawaban fisik untuk verifikasi visual.</p>
                        </div>
                        <img id="ljkPreviewImage" src="" alt="LJK Preview" style="display: none;">
                        <svg id="omrTargetOverlaySvg" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; display: none; z-index: 4; transform-origin: center center;"></svg>

                        <div class="image-controls" id="imageControls" style="display: none;">
                            <button type="button" onclick="zoomImage(0.15)" title="Zoom In"><i class="fas fa-search-plus"></i></button>
                            <button type="button" onclick="zoomImage(-0.15)" title="Zoom Out"><i class="fas fa-search-minus"></i></button>
                            <button type="button" onclick="rotateImage(-90)" title="Rotate Kiri"><i class="fas fa-undo"></i></button>
                            <button type="button" onclick="rotateImage(90)" title="Rotate Kanan"><i class="fas fa-redo"></i></button>
                            <button type="button" onclick="resetImageViewer()" title="Reset"><i class="fas fa-sync-alt"></i></button>
                            <button type="button" id="btnToggleOverlay" onclick="toggleOmrTargetOverlay()" title="Tampilkan/Sembunyikan Target Visual OMR" class="btn-overlay-off"><i class="fas fa-crosshairs"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel 3: Form Kisi-Kisi Jawaban Cepat (Kanan - 5 Kolom) -->
        <div class="col-lg-5 col-md-12 mb-3">
            <div class="card card-outline card-success shadow-sm verifikasi-card">
                <div class="card-header p-2 d-flex justify-content-between align-items-center bg-light">
                    <div>
                        <h6 class="mb-0 fw-bold" id="selectedSantriTitle">PILIH SANTRI UNTUK VERIFIKASI</h6>
                        <small class="text-muted" id="selectedSantriSub">-</small>
                    </div>
                    <div id="scorePreviewBadge" style="display: none;">
                        <span class="badge bg-success fs-6" id="scoreText">Nilai: 0.0</span>
                    </div>
                </div>

                <!-- Alert Banner Mismatch Santri -->
                <div id="mismatchAlertBanner" class="alert alert-danger m-2 p-2" style="display: none; font-size: 12px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>BEDA SANTRI!</strong> Foto di layar milik <strong id="mismatchDetectedName" class="text-underline"></strong>, tetapi form kanan adalah <strong id="mismatchCurrentName"></strong>.
                        </div>
                        <button type="button" class="btn btn-sm btn-light text-danger fw-bold ms-2" id="btnSwitchDetectedSantri" onclick="switchToDetectedSantri()">
                            ⚡ Switch Naskah
                        </button>
                    </div>
                </div>

                <div class="card-body p-3" style="max-height: 580px; overflow-y: auto;" id="formJawabanContainer">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-tasks fa-3x mb-3 text-secondary"></i>
                        <p>Silakan pilih santri dari daftar di sebelah kiri untuk mengisi atau merevisi jawaban secara manual.</p>
                    </div>
                </div>

                <div class="card-footer p-2 bg-light d-flex justify-content-between align-items-center" id="formJawabanFooter" style="display: none !important;">
                    <div>
                        <small class="text-muted d-block" id="answeredCountText">Dijawab: 0 / 0</small>
                    </div>
                    <button type="button" class="btn btn-success font-weight-bold" id="btnSimpanJawaban" onclick="simpanJawabanManualAction()">
                        <i class="fas fa-check-circle me-1"></i> Simpan Jawaban & Hitung Nilai
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 & Image Zoom Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- OpenCV.js Computer Vision Engine -->
<script>
var cvReady = false;
var Module = {
    onRuntimeInitialized: function() {
        cvReady = true;
        console.log('✅ OpenCV.js siap digunakan');
    }
};
</script>
<script async src="https://docs.opencv.org/4.9.0/opencv.js" type="text/javascript"></script>

<script>
let currentJadwalId = <?= (int)$jadwal['id'] ?>;
let currentSantriId = null;
let currentSesiId   = null;
let currentDistribusi = [];
let currentJawabanMap = {};
let currentZoom     = 1;
let currentRotate   = 0;

function filterSantriRoster() {
    let q = $('#searchSantriInput').val().toLowerCase();
    $('.item-santri-btn').each(function() {
        let text = $(this).text().toLowerCase();
        if (text.indexOf(q) !== -1) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

function loadSantriVerification(idJadwal, idSantri, callback, isManualClick = false) {
    currentSantriId = idSantri;
    $('.item-santri-btn').removeClass('active');
    $('#btn-santri-' + idSantri).addClass('active');

    let hasTempUpload = !isManualClick && $('#ljkPreviewImage').is(':visible') && $('#ljkPreviewImage').attr('src') !== '';

    Swal.fire({
        title: 'Memuat data santri...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    $.ajax({
        url: '<?= base_url('backend/ujian-mdta/jadwal/get-form-jawaban-santri') ?>/' + idJadwal + '/' + idSantri,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            Swal.close();
            if (!res.success) {
                Swal.fire('Error', res.message || 'Gagal memuat data.', 'error');
                return;
            }

            currentSesiId          = res.idSesi;
            currentDistribusi      = res.distribusi;
            currentJawabanMap      = res.jawabanMap || {};
            currentWaktuVerifikasi = res.waktuVerifikasi || null;
            currentDiverifikasiOleh= res.diverifikasiOleh || null;

            // Render Header Santri
            $('#selectedSantriTitle').text(res.santriInfo.NamaSantri);
            $('#selectedSantriSub').text('NIS: ' + (res.santriInfo.NISN || res.santriInfo.IdSantri) + ' | Kelas: ' + (res.santriInfo.NamaKelas || '-'));

            // Render Image LJK jika ada di DB, KECUALI jika pengguna baru saja mengunggah foto baru secara langsung
            if (!hasTempUpload) {
                if (res.fotoUrl) {
                    $('#ljkPreviewImage').attr('src', res.fotoUrl).show();
                    $('#imagePlaceholder').hide();
                    $('#imageControls').show();
                    $('#btnAutoDetectOmr').show();
                } else {
                    $('#ljkPreviewImage').attr('src', '').hide();
                    $('#imagePlaceholder').show();
                    $('#imageControls').hide();
                    $('#btnAutoDetectOmr').hide();
                }
            } else {
                $('#imagePlaceholder').hide();
                $('#imageControls').show();
                $('#btnAutoDetectOmr').show();
            }

            resetImageViewer();
            renderFormJawabanGrid(res.distribusi, res.jawabanMap, res.jumlahPilihan);
            updateLiveScore();

            if (typeof callback === 'function') {
                callback();
            }
        },
        error: function() {
            Swal.close();
            Swal.fire('Error', 'Terjadi kesalahan koneksi server.', 'error');
        }
    });
}

function renderFormJawabanGrid(distribusi, jawabanMap, maxPilihan) {
    let html = '';
    let totalSoal = distribusi.length;
    let totalBenar = 0;
    let totalSalah = 0;
    let totalTerjawab = 0;

    distribusi.forEach(function(soal, idx) {
        let idSoal = soal.IdSoal;
        let infoJawaban = jawabanMap[idSoal] || {};
        let selectedPilihanId = infoJawaban.idPilihan;
        let isAnswered = selectedPilihanId !== null && selectedPilihanId !== undefined;
        let isBenarVal = infoJawaban.isBenar; // 1, 0, or null

        let rowStatusClass = '';
        let badgeHtml = '';

        if (isAnswered) {
            totalTerjawab++;
            if (parseInt(isBenarVal) === 1) {
                rowStatusClass = 'correct-row';
                badgeHtml = '<span class="badge bg-success ms-2"><i class="fas fa-check me-1"></i> Benar</span>';
                totalBenar++;
            } else if (parseInt(isBenarVal) === 0) {
                rowStatusClass = 'wrong-row';
                badgeHtml = '<span class="badge bg-danger ms-2"><i class="fas fa-times me-1"></i> Salah</span>';
                totalSalah++;
            } else {
                rowStatusClass = 'answered';
            }
        }

        html += `
            <div class="soal-grid-row ${rowStatusClass}" id="soal-row-${idSoal}">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div>
                        <strong class="text-dark font-size-13">Soal #${soal.UrutanSoal}</strong>
                        ${badgeHtml}
                    </div>
                    <small class="text-muted text-truncate" style="max-width: 240px;">${soal.UraianSoal.replace(/<[^>]*>?/gm, '').substring(0, 45)}...</small>
                </div>
                <div class="d-flex flex-wrap gap-1 align-items-center mt-2">
        `;

        let hurufPositional = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        soal.pilihan.forEach(function(pil, pIdx) {
            if (maxPilihan > 0 && pIdx >= maxPilihan) return;

            let labelHuruf = hurufPositional[pIdx] || String.fromCharCode(65 + pIdx);
            let isChecked  = (parseInt(selectedPilihanId) === parseInt(pil.id));
            let isPilBenar = (parseInt(pil.IsBenar) === 1);
            let teksClean  = pil.TeksPilihan ? pil.TeksPilihan.replace(/<[^>]*>?/gm, '') : '';

            let btnClass = 'btn-outline-secondary';
            if (isChecked) {
                if (parseInt(isBenarVal) === 1) {
                    btnClass = 'btn-correct-active';
                } else if (parseInt(isBenarVal) === 0) {
                    btnClass = 'btn-wrong-active';
                } else {
                    btnClass = 'active';
                }
            } else if (isAnswered && parseInt(isBenarVal) === 0 && isPilBenar) {
                // Jawaban santri salah, tapi pilihan ini adalah KUNCI BENAR
                btnClass = 'btn-key-highlight';
            }

            html += `
                <button type="button" class="btn ${btnClass} btn-pil-option"
                        id="btn-pil-${idSoal}-${pil.id}"
                        onclick="selectOptionJawaban(${idSoal}, ${pil.id})"
                        title="Pilihan ${labelHuruf}: ${teksClean} ${isPilBenar ? '(Kunci Jawaban)' : ''}">
                    ${labelHuruf} ${isAnswered && parseInt(isBenarVal) === 0 && isPilBenar ? '<i class="fas fa-key text-success ms-1" style="font-size:10px;"></i>' : ''}
                </button>
            `;
        });

        html += `
                    <button type="button" class="btn btn-link text-danger btn-sm p-0 ms-2" onclick="clearOptionJawaban(${idSoal})" title="Hapus Jawaban">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
            </div>
        `;
    });

    $('#formJawabanContainer').html(html);
    $('#formJawabanFooter').removeAttr('style');

    if (totalTerjawab > 0) {
        $('#scorePreviewBadge').show();
        let skorPct = totalSoal > 0 ? ((totalBenar / totalSoal) * 100).toFixed(1) : 0;
        $('#scoreText').text(`Benar: ${totalBenar} | Salah: ${totalSalah} | Nilai: ${skorPct}`);
    }
}

function selectOptionJawaban(idSoal, idPilihan) {
    if (!currentJawabanMap[idSoal]) {
        currentJawabanMap[idSoal] = {};
    }
    currentJawabanMap[idSoal].idPilihan = idPilihan;

    $('#soal-row-' + idSoal).find('.btn-pil-option').removeClass('active btn-correct-active btn-wrong-active btn-key-highlight');
    $('#btn-pil-' + idSoal + '-' + idPilihan).addClass('active');
    $('#soal-row-' + idSoal).removeClass('correct-row wrong-row').addClass('answered');

    updateLiveScore();
}

function clearOptionJawaban(idSoal) {
    if (currentJawabanMap[idSoal]) {
        currentJawabanMap[idSoal].idPilihan = null;
        currentJawabanMap[idSoal].isBenar = null;
    }
    $('#soal-row-' + idSoal).find('.btn-pil-option').removeClass('active btn-correct-active btn-wrong-active btn-key-highlight');
    $('#soal-row-' + idSoal).removeClass('answered correct-row wrong-row');

    updateLiveScore();
}

function updateLiveScore() {
    let answered = 0;
    let total = currentDistribusi.length;

    currentDistribusi.forEach(function(soal) {
        let idSoal = soal.IdSoal;
        if (currentJawabanMap[idSoal] && currentJawabanMap[idSoal].idPilihan) {
            answered++;
        }
    });

    $('#answeredCountText').text('Dijawab: ' + answered + ' / ' + total);
}

function dataURLtoBlob(dataurl) {
    let arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
    while(n--){
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new Blob([u8arr], {type:mime});
}

function handleFotoUploadChange(input) {
    if (input.files && input.files[0]) {
        let file = input.files[0];
        let reader = new FileReader();

        reader.onload = function(e) {
            let img = document.getElementById('ljkPreviewImage');

            // Gunakan RESOLUSI ASLI 100% MURNI tanpa konversi saat upload/preview
            img.onload = function() {
                setTimeout(autoScanQRAndProcess, 100);
            };

            img.src = e.target.result;
            $('#ljkPreviewImage').show();
            $('#imagePlaceholder').hide();
            $('#imageControls').show();
            $('#btnAutoDetectOmr').show();
            resetImageViewer();

            if (img.complete) {
                setTimeout(autoScanQRAndProcess, 150);
            }
        };
        reader.readAsDataURL(file);
    }
}

function autoScanQRAndProcess() {
    let img = document.getElementById('ljkPreviewImage');
    if (!img || !img.src || img.style.display === 'none') return;

    let canvas = document.getElementById('omrProcessCanvas');
    if (!canvas) {
        canvas = document.createElement('canvas');
        canvas.id = 'omrProcessCanvas';
        canvas.style.display = 'none';
        document.body.appendChild(canvas);
    }

    let ctx = canvas.getContext('2d');
    canvas.width  = img.naturalWidth || img.width || 800;
    canvas.height = img.naturalHeight || img.height || 1000;
    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

    let imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    let detectedSantriId = null;

    // Pass 1: Coba scan QR Code langsung dari raw image
    if (typeof jsQR !== 'undefined') {
        let code = jsQR(imageData.data, imageData.width, imageData.height);
        if (code && code.data) {
            let matches = code.data.match(/SANTRI:(\d+)/i);
            if (matches && matches[1]) {
                detectedSantriId = parseInt(matches[1]);
            }
        }

        // Pass 2: Jika gagal di Pass 1, tingkatkan kontras/binarisasi canvas lalu scan ulang
        if (!detectedSantriId) {
            let d = imageData.data;
            for (let i = 0; i < d.length; i += 4) {
                let v = (d[i] + d[i+1] + d[i+2]) / 3;
                let bw = (v < 128) ? 0 : 255;
                d[i] = d[i+1] = d[i+2] = bw;
            }
            ctx.putImageData(imageData, 0, 0);
            let code2 = jsQR(imageData.data, imageData.width, imageData.height);
            if (code2 && code2.data) {
                let matches2 = code2.data.match(/SANTRI:(\d+)/i);
                if (matches2 && matches2[1]) {
                    detectedSantriId = parseInt(matches2[1]);
                }
            }
        }
    }

    // Jika QR Code terdeteksi dan memiliki ID Santri
    if (detectedSantriId) {
        lastDetectedSantriId = detectedSantriId;
        $('#mismatchAlertBanner').hide();

        if (detectedSantriId !== currentSantriId) {
            // Otomatis langsung beralih ke santri terdeteksi dari QR Code tanpa pop-up konfirmasi
            loadSantriVerification(currentJadwalId, detectedSantriId, function() {
                runAutoDetectOMR();
            });
            return;
        } else {
            runAutoDetectOMR();
            return;
        }
    } else if (!currentSantriId) {
        // Fallback jika QR Code terpotong: Otomatis pilih santri pertama di daftar roster
        let firstBtn = $('.item-santri-btn:first');
        if (firstBtn.length > 0) {
            let firstSantriId = firstBtn.attr('id').replace('btn-santri-', '');
            loadSantriVerification(currentJadwalId, firstSantriId, function() {
                runAutoDetectOMR();
            });
        }
    } else {
        $('#mismatchAlertBanner').hide();
    }

    // Jika QR Code tidak terdeteksi dari foto
    if (!currentSantriId) {
        Swal.fire({
            icon: 'info',
            title: 'Foto LJK Berhasil Diunggah',
            text: 'Silakan pilih nama santri pada daftar di sebelah kiri untuk memverifikasi foto ini.',
            timer: 2500,
            showConfirmButton: false
        });
    } else {
        runAutoDetectOMR();
    }
}

let lastDetectedSantriId = null;
function switchToDetectedSantri() {
    if (lastDetectedSantriId) {
        $('#mismatchAlertBanner').hide();
        loadSantriVerification(currentJadwalId, lastDetectedSantriId, function() {
            runAutoDetectOMR();
        });
    }
}

function zoomImage(step) {
    currentZoom = Math.max(0.5, Math.min(3, currentZoom + step));
    applyImageTransform();
}

function rotateImage(deg) {
    currentRotate = (currentRotate + deg) % 360;
    applyImageTransform();
}

function resetImageViewer() {
    currentZoom = 1;
    currentRotate = 0;
    applyImageTransform();
}

function applyImageTransform() {
    let styleVal = 'scale(' + currentZoom + ') rotate(' + currentRotate + 'deg)';
    $('#ljkPreviewImage, #omrTargetOverlaySvg').css('transform', styleVal);
}

function toggleOmrTargetOverlay() {
    let svg = $('#omrTargetOverlaySvg');
    if (svg.is(':visible')) {
        svg.hide();
        $('#btnToggleOverlay').removeClass('btn-overlay-on').addClass('btn-overlay-off');
    } else {
        svg.show();
        $('#btnToggleOverlay').removeClass('btn-overlay-off').addClass('btn-overlay-on');
    }
}

function runAutoDetectOMR() {
    let img = document.getElementById('ljkPreviewImage');
    if (!img || !img.src || img.style.display === 'none') {
        Swal.fire('Peringatan', 'Unggah foto LJK terlebih dahulu.', 'warning');
        return;
    }

    if (!currentDistribusi || currentDistribusi.length === 0) {
        Swal.close();
        let firstBtn = $('.item-santri-btn:first');
        if (firstBtn.length > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Santri Belum Dipilih',
                text: 'Silakan pilih santri terlebih dahulu di daftar sebelah kiri untuk memuat kisi-kisi jawaban.',
                confirmButtonText: 'Pilih Santri Pertama'
            }).then(() => {
                firstBtn.click();
            });
        } else {
            Swal.fire('Peringatan', 'Form kisi-kisi santri belum dimuat. Silakan pilih santri di daftar sebelah kiri.', 'warning');
        }
        return;
    }

    Swal.fire({
        title: 'Memproses Deteksi OMR via OpenCV.js...',
        text: 'Sistem Computer Vision sedang meratakan foto miring & menghitung sel bulatan LJK...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    setTimeout(() => {
        try {
            if (typeof cv !== 'undefined' && cvReady) {
                processOMRWithOpenCV(img);
            } else {
                processOMRFallback(img);
            }
        } catch (e) {
            console.error('OpenCV OMR Error, using fallback:', e);
            processOMRFallback(img);
        }
    }, 150);
}

function processOMRWithOpenCV(imgElement) {
    let src = null, gray = null, blurred = null, thresh = null;
    try {
        src = cv.imread(imgElement);
        let W = src.cols;
        let H = src.rows;

        gray = new cv.Mat();
        cv.cvtColor(src, gray, cv.COLOR_RGBA2GRAY, 0);

        blurred = new cv.Mat();
        cv.GaussianBlur(gray, blurred, new cv.Size(3, 3), 0);

        thresh = new cv.Mat();
        // THRESH_BINARY_INV: piksel hitam (tanda/garis) → 255, putih → 0
        cv.adaptiveThreshold(blurred, thresh, 255, cv.ADAPTIVE_THRESH_GAUSSIAN_C, cv.THRESH_BINARY_INV, 25, 12);

        // Ambil data pixel thresh sebagai array
        let data = thresh.data;

        // Helper: hitung jumlah piksel putih (hitam asli) di area (x1,y1,x2,y2)
        function countDark(x1, y1, x2, y2) {
            let cnt = 0;
            let ax1 = Math.max(0, Math.floor(x1)), ay1 = Math.max(0, Math.floor(y1));
            let ax2 = Math.min(W, Math.floor(x2)), ay2 = Math.min(H, Math.floor(y2));
            for (let y = ay1; y < ay2; y += 2) {
                for (let x = ax1; x < ax2; x += 2) {
                    if (data[y * W + x] > 128) cnt++;
                }
            }
            return cnt / Math.max(1, ((ax2 - ax1) / 2) * ((ay2 - ay1) / 2));
        }

        // Helper: column projection (kerapatan piksel per kolom X) pada area Y tertentu
        function colProjection(x1, x2, y1, y2) {
            let ax1 = Math.max(0, Math.floor(x1)), ax2 = Math.min(W, Math.floor(x2));
            let ay1 = Math.max(0, Math.floor(y1)), ay2 = Math.min(H, Math.floor(y2));
            let proj = new Array(ax2 - ax1).fill(0);
            let rowH = Math.max(1, ay2 - ay1);
            for (let y = ay1; y < ay2; y++) {
                for (let x = ax1; x < ax2; x++) {
                    if (data[y * W + x] > 128) proj[x - ax1]++;
                }
            }
            return proj.map(v => v / rowH);
        }

        // Helper: row projection (kerapatan piksel per baris Y) pada area X tertentu
        function rowProjection(y1, y2, x1, x2) {
            let ax1 = Math.max(0, Math.floor(x1)), ax2 = Math.min(W, Math.floor(x2));
            let ay1 = Math.max(0, Math.floor(y1)), ay2 = Math.min(H, Math.floor(y2));
            let proj = new Array(ay2 - ay1).fill(0);
            let colW = Math.max(1, ax2 - ax1);
            for (let y = ay1; y < ay2; y++) {
                for (let x = ax1; x < ax2; x++) {
                    if (data[y * W + x] > 128) proj[y - ay1]++;
                }
            }
            return proj.map(v => v / colW);
        }

        // -------------------------------------------------------
        // STEP 1: Temukan batas kertas (area putih)
        // -------------------------------------------------------
        let paperTop = 0, paperBot = H, paperLeft = 0, paperRight = W;

        // Row projection tengah gambar untuk cari batas atas/bawah kertas
        let midX1 = W * 0.2, midX2 = W * 0.8;
        for (let y = 0; y < H; y += 4) {
            let dark = countDark(midX1, y, midX2, y + 4);
            if (dark < 0.6) { paperTop = y; break; }
        }
        for (let y = H - 1; y > 0; y -= 4) {
            let dark = countDark(midX1, y - 4, midX2, y);
            if (dark < 0.6) { paperBot = y; break; }
        }
        let pH = paperBot - paperTop;

        // -------------------------------------------------------
        // STEP 2: Temukan area tabel LJK (bawah kertas ~50-90% dari tinggi kertas)
        // Tabel LJK ada di bawah header identitas santri
        // -------------------------------------------------------
        let tableTop    = paperTop + pH * 0.46;
        let tableBot    = paperTop + pH * 0.92;
        let tableHeight = tableBot - tableTop;

        // -------------------------------------------------------
        // STEP 3: Row Projection → temukan Y center setiap baris soal
        // Baris soal dipisahkan garis horisontal (kerapatan tinggi)
        // Cari celah (lembah) di antara puncak-puncak row projection
        // -------------------------------------------------------
        let totalSoal = currentDistribusi.length;
        let halfCount = Math.ceil(totalSoal / 2);

        // Ambil row projection di area tabel (gunakan kolom tengah)
        let rp = rowProjection(tableTop, tableBot, W * 0.05, W * 0.48);

        // Temukan garis horizontal tebal (puncak tinggi): threshold > 0.15
        let borderYs = [];
        let inBorder = false;
        let borderStart = 0;
        for (let i = 0; i < rp.length; i++) {
            if (!inBorder && rp[i] > 0.15) {
                inBorder = true; borderStart = i;
            } else if (inBorder && rp[i] <= 0.15) {
                inBorder = false;
                borderYs.push(tableTop + (borderStart + i) / 2);
            }
        }
        // Jika tidak cukup border, fallback ke estimasi rata
        while (borderYs.length < halfCount + 2) {
            let step = tableHeight / (halfCount + 1);
            borderYs = [];
            for (let i = 0; i <= halfCount + 1; i++) {
                borderYs.push(tableTop + i * step);
            }
        }

        // Row center Y = tengah antara dua border
        let leftRowYs = [];
        for (let i = 0; i < borderYs.length - 1 && leftRowYs.length < halfCount; i++) {
            // Skip header row (row pertama = header "PILIHAN JAWABAN SANTRI")
            if (i === 0 && (borderYs[1] - borderYs[0]) < tableHeight * 0.12) continue;
            leftRowYs.push((borderYs[i] + borderYs[i + 1]) / 2);
        }

        // Untuk kolom kanan, gunakan row projection di sisi kanan
        let rp2 = rowProjection(tableTop, tableBot, W * 0.52, W * 0.95);
        let borderYs2 = [];
        let inBorder2 = false, bStart2 = 0;
        for (let i = 0; i < rp2.length; i++) {
            if (!inBorder2 && rp2[i] > 0.15) {
                inBorder2 = true; bStart2 = i;
            } else if (inBorder2 && rp2[i] <= 0.15) {
                inBorder2 = false;
                borderYs2.push(tableTop + (bStart2 + i) / 2);
            }
        }
        let rightCount = totalSoal - halfCount;
        while (borderYs2.length < rightCount + 2) {
            let step2 = tableHeight / (rightCount + 1);
            borderYs2 = [];
            for (let i = 0; i <= rightCount + 1; i++) {
                borderYs2.push(tableTop + i * step2);
            }
        }
        let rightRowYs = [];
        for (let i = 0; i < borderYs2.length - 1 && rightRowYs.length < rightCount; i++) {
            if (i === 0 && (borderYs2[1] - borderYs2[0]) < tableHeight * 0.12) continue;
            rightRowYs.push((borderYs2[i] + borderYs2[i + 1]) / 2);
        }

        console.log('Left row Y centers:', leftRowYs.map(y => (y/H*100).toFixed(1)+'%'));
        console.log('Right row Y centers:', rightRowYs.map(y => (y/H*100).toFixed(1)+'%'));

        // -------------------------------------------------------
        // STEP 4: Column Projection → temukan X center per opsi
        // Untuk setiap baris soal, scan column projection di area jawaban
        // -------------------------------------------------------
        function findOptionCenters(rowY, halfHeight, xStart, xEnd, numOptions) {
            let cp = colProjection(xStart, xEnd, rowY - halfHeight, rowY + halfHeight);
            let rangeW = Math.floor(xEnd - xStart);

            // Bagi rentang menjadi numOptions segmen, cari puncak di tiap segmen
            let segW = Math.floor(rangeW / numOptions);
            let centers = [];
            for (let seg = 0; seg < numOptions; seg++) {
                let segStart = seg * segW;
                let segEnd = Math.min((seg + 1) * segW, cp.length);
                let maxVal = 0, maxIdx = segStart + Math.floor(segW / 2);
                for (let i = segStart; i < segEnd; i++) {
                    if (cp[i] > maxVal) { maxVal = cp[i]; maxIdx = i; }
                }
                centers.push({ x: xStart + maxIdx, density: maxVal });
            }
            return centers;
        }

        // -------------------------------------------------------
        // STEP 5: Deteksi jawaban per soal
        // -------------------------------------------------------
        let detectedResults = [];
        let hurufPos = ['A', 'B', 'C', 'D', 'E', 'F'];

        currentDistribusi.forEach((soal, idx) => {
            let numPilihan = Math.min(soal.pilihan.length, 4);
            if (numPilihan <= 0) return;

            let isRightCol = idx >= halfCount;
            let rowIdx     = isRightCol ? (idx - halfCount) : idx;
            let activeYs   = isRightCol ? rightRowYs : leftRowYs;

            let rowY = activeYs.length > rowIdx
                ? activeYs[rowIdx]
                : (isRightCol
                    ? tableTop + tableHeight * (0.15 + rowIdx * 0.5)
                    : tableTop + tableHeight * (0.15 + rowIdx * 0.35));

            // Setengah tinggi baris = estimasi dari jarak antar baris atau 3%
            let rowHalf = activeYs.length >= 2
                ? (activeYs[1] - activeYs[0]) * 0.4
                : tableHeight * 0.12;
            rowHalf = Math.max(rowHalf, H * 0.012);

            // X area jawaban (setelah NO column)
            // Left: 18%-47%  Right: 66%-95%
            let xStart = isRightCol ? W * 0.66 : W * 0.18;
            let xEnd   = isRightCol ? W * 0.96 : W * 0.48;
            let xRange = xEnd - xStart;
            let colW   = xRange / numPilihan;

            // Scan kerapatan per opsi (ukuran sample inner 60% dari lebar & tinggi)
            let cRatios  = [];
            let soalBoxes = [];
            for (let pIdx = 0; pIdx < numPilihan; pIdx++) {
                let optX1 = xStart + pIdx * colW;
                let optX2 = optX1 + colW;
                // inner 30%-70% dari lebar sel, inner 20%-80% dari tinggi
                let scanX1 = optX1 + colW * 0.20;
                let scanX2 = optX2 - colW * 0.20;
                let scanY1 = rowY - rowHalf * 0.75;
                let scanY2 = rowY + rowHalf * 0.75;

                let ratio = countDark(scanX1, scanY1, scanX2, scanY2);
                cRatios.push({ pIdx, ratio });
                soalBoxes.push({ x1: scanX1, y1: scanY1, x2: scanX2, y2: scanY2 });

                console.log(`Soal${idx+1} Opt${hurufPos[pIdx]}: ratio=${ratio.toFixed(3)} @ X[${(scanX1/W*100).toFixed(0)}%-${(scanX2/W*100).toFixed(0)}%] Y[${(scanY1/H*100).toFixed(0)}%-${(scanY2/H*100).toFixed(0)}%]`);
            }

            cRatios.sort((a, b) => b.ratio - a.ratio);
            let top    = cRatios[0];
            let second = cRatios[1] || { ratio: 0 };
            let gap    = top.ratio - second.ratio;
            // Deteksi jika ada pilihan yang jelas lebih gelap dari yang lain
            let isDetected = top.ratio > 0.04 && (gap > 0.015 || top.ratio > second.ratio * 1.3);

            detectedResults.push({
                idSoal: soal.IdSoal,
                pilihanIdx: isDetected ? top.pIdx : null,
                selectedOptionId: isDetected ? (soal.pilihan[top.pIdx]?.id ?? null) : null,
                ratio: top.ratio,
                boxes: soalBoxes
            });
        });

        // -------------------------------------------------------
        // STEP 6: Terapkan jawaban & render overlay SVG
        // -------------------------------------------------------
        let autoSelectedCount = 0;
        detectedResults.forEach(res => {
            if (res.selectedOptionId !== null) {
                selectOptionJawaban(res.idSoal, res.selectedOptionId);
                autoSelectedCount++;
            }
        });

        let svgContent = '';
        detectedResults.forEach(res => {
            (res.boxes || []).forEach((box, pIdx) => {
                let pctX1 = (box.x1 / W) * 100;
                let pctY1 = (box.y1 / H) * 100;
                let pctW  = ((box.x2 - box.x1) / W) * 100;
                let pctH  = ((box.y2 - box.y1) / H) * 100;
                let label = hurufPos[pIdx] || 'A';
                if (res.pilihanIdx === pIdx) {
                    svgContent += `<rect x="${pctX1.toFixed(2)}%" y="${pctY1.toFixed(2)}%" width="${pctW.toFixed(2)}%" height="${pctH.toFixed(2)}%" fill="rgba(40,167,69,0.45)" stroke="#28a745" stroke-width="2.5" rx="4"/>`;
                    svgContent += `<text x="${(pctX1+pctW/2).toFixed(2)}%" y="${(pctY1+pctH/2).toFixed(2)}%" fill="#fff" font-size="11" font-weight="bold" text-anchor="middle" dominant-baseline="central">✓${label}</text>`;
                } else {
                    svgContent += `<rect x="${pctX1.toFixed(2)}%" y="${pctY1.toFixed(2)}%" width="${pctW.toFixed(2)}%" height="${pctH.toFixed(2)}%" fill="rgba(220,53,69,0.08)" stroke="#dc3545" stroke-width="1" stroke-dasharray="4,3" rx="4"/>`;
                    svgContent += `<text x="${(pctX1+pctW/2).toFixed(2)}%" y="${(pctY1+pctH/2).toFixed(2)}%" fill="#dc3545" font-size="9" text-anchor="middle" dominant-baseline="central">${label}</text>`;
                }
            });
        });

        $('#omrTargetOverlaySvg').html(svgContent).show();
        $('#btnToggleOverlay').removeClass('btn-overlay-off').addClass('btn-overlay-on');

        Swal.close();
        Swal.fire({
            icon: autoSelectedCount > 0 ? 'success' : 'warning',
            title: 'Analisis OpenCV.js Selesai!',
            html: `Terisi otomatis: <strong>${autoSelectedCount} dari ${totalSoal} jawaban</strong>.<br><small>Cek Console (F12) untuk detail kerapatan piksel.</small>`,
            timer: 2500,
            showConfirmButton: false
        });

    } catch (err) {
        console.error('OpenCV processing error:', err);
        throw err;
    } finally {
        if (src) src.delete();
        if (gray) gray.delete();
        if (blurred) blurred.delete();
        if (thresh) thresh.delete();
    }
}
        let W = src.cols;

function processOMRFallback(imgElement) {
    let img = imgElement;
    let canvas = document.getElementById('omrProcessCanvas');
    if (!canvas) {
        canvas = document.createElement('canvas');
        canvas.id = 'omrProcessCanvas';
        canvas.style.display = 'none';
        document.body.appendChild(canvas);
    }

    try {
        let ctx = canvas.getContext('2d');
        canvas.width = img.naturalWidth || img.width || 800;
        canvas.height = img.naturalHeight || img.height || 1000;
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

        let imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

        let totalSoal = currentDistribusi.length;
        let pixels    = imageData.data;
        let width     = imageData.width;
        let height    = imageData.height;

            // -----------------------------------------------------------------
            // STEP A: DETEKSI BATAS KERTAS PUTIH (Mengabaikan Padding Hitam Canvas)
            // -----------------------------------------------------------------
            let paperTop = 0, paperBot = height, paperLeft = 0, paperRight = width;

            for (let y = 0; y < height; y += 4) {
                let lightCount = 0;
                for (let x = Math.floor(width * 0.2); x < width * 0.8; x += 4) {
                    let offset = (y * width + x) * 4;
                    let b = (pixels[offset] + pixels[offset + 1] + pixels[offset + 2]) / 3;
                    if (b > 150) lightCount++;
                }
                if (lightCount > (width * 0.6 / 4) * 0.35) {
                    paperTop = y;
                    break;
                }
            }

            for (let y = height - 1; y >= 0; y -= 4) {
                let lightCount = 0;
                for (let x = Math.floor(width * 0.2); x < width * 0.8; x += 4) {
                    let offset = (y * width + x) * 4;
                    let b = (pixels[offset] + pixels[offset + 1] + pixels[offset + 2]) / 3;
                    if (b > 150) lightCount++;
                }
                if (lightCount > (width * 0.6 / 4) * 0.35) {
                    paperBot = y;
                    break;
                }
            }

            let midPaperY = Math.floor(paperTop + (paperBot - paperTop) * 0.5);

            for (let x = 0; x < width; x += 4) {
                let lightCount = 0;
                for (let y = Math.max(0, midPaperY - 60); y < Math.min(height, midPaperY + 60); y += 4) {
                    let offset = (y * width + x) * 4;
                    let b = (pixels[offset] + pixels[offset + 1] + pixels[offset + 2]) / 3;
                    if (b > 150) lightCount++;
                }
                if (lightCount > 4) {
                    paperLeft = x;
                    break;
                }
            }

            for (let x = width - 1; x >= 0; x -= 4) {
                let lightCount = 0;
                for (let y = Math.max(0, midPaperY - 60); y < Math.min(height, midPaperY + 60); y += 4) {
                    let offset = (y * width + x) * 4;
                    let b = (pixels[offset] + pixels[offset + 1] + pixels[offset + 2]) / 3;
                    if (b > 150) lightCount++;
                }
                if (lightCount > 4) {
                    paperRight = x;
                    break;
                }
            }

            let rawPW = paperRight - paperLeft;
            let rawPH = paperBot - paperTop;

            // -----------------------------------------------------------------
            // STEP A2: CARI 4 KOTAK HITAM (⬛) DI DALAM AREA KERTAS PUTIH
            // -----------------------------------------------------------------
            function findInnerCornerCentroid(minX, maxX, minY, maxY) {
                let sumX = 0, sumY = 0, count = 0;
                for (let y = Math.floor(minY); y < maxY; y += 2) {
                    for (let x = Math.floor(minX); x < maxX; x += 2) {
                        if (x >= 0 && x < width && y >= 0 && y < height) {
                            let offset = (y * width + x) * 4;
                            let b = (pixels[offset] + pixels[offset + 1] + pixels[offset + 2]) / 3;
                            if (b < 90) {
                                sumX += x;
                                sumY += y;
                                count++;
                            }
                        }
                    }
                }
                return count >= 6 ? { x: sumX / count, y: sumY / count } : null;
            }

            let topLeftMark     = findInnerCornerCentroid(paperLeft, paperLeft + (rawPW * 0.20), paperTop, paperTop + (rawPH * 0.20));
            let topRightMark    = findInnerCornerCentroid(paperRight - (rawPW * 0.20), paperRight, paperTop, paperTop + (rawPH * 0.20));
            let bottomLeftMark  = findInnerCornerCentroid(paperLeft, paperLeft + (rawPW * 0.20), paperBot - (rawPH * 0.20), paperBot);
            let bottomRightMark = findInnerCornerCentroid(paperRight - (rawPW * 0.20), paperRight, paperBot - (rawPH * 0.20), paperBot);

            if (topLeftMark && topRightMark && bottomLeftMark && bottomRightMark) {
                paperLeft  = (topLeftMark.x + bottomLeftMark.x) / 2;
                paperRight = (topRightMark.x + bottomRightMark.x) / 2;
                paperTop   = (topLeftMark.y + topRightMark.y) / 2;
                paperBot   = (bottomLeftMark.y + bottomRightMark.y) / 2;
            }

            let pW = paperRight - paperLeft;
            let pH = paperBot - paperTop;

            // -----------------------------------------------------------------
            // STEP A3: HITUNG AMBANG BATAS DINAMIS ADAPTIF (Adaptive Dynamic Threshold)
            // -----------------------------------------------------------------
            let paperBrightnessSum = 0, paperSampleCount = 0;
            for (let y = Math.floor(paperTop + pH * 0.15); y < paperBot - pH * 0.15; y += 6) {
                for (let x = Math.floor(paperLeft + pW * 0.15); x < paperRight - pW * 0.15; x += 6) {
                    let offset = (Math.floor(y) * width + Math.floor(x)) * 4;
                    let b = (pixels[offset] + pixels[offset + 1] + pixels[offset + 2]) / 3;
                    if (b > 120) {
                        paperBrightnessSum += b;
                        paperSampleCount++;
                    }
                }
            }

            let avgPaperB = paperSampleCount > 0 ? (paperBrightnessSum / paperSampleCount) : 210;
            let dynamicThreshold = Math.min(165, Math.max(95, avgPaperB * 0.72));

            function sampleDarkRatio(x1, y1, x2, y2) {
                let dark = 0, sampled = 0;
                for (let y = Math.floor(y1); y < y2; y += 2) {
                    for (let x = Math.floor(x1); x < x2; x += 2) {
                        if (x >= 0 && x < width && y >= 0 && y < height) {
                            let offset = (Math.floor(y) * width + Math.floor(x)) * 4;
                            let brightness = (pixels[offset] + pixels[offset + 1] + pixels[offset + 2]) / 3;
                            sampled++;
                            if (brightness < dynamicThreshold) dark++;
                        }
                    }
                }
                return sampled > 0 ? (dark / sampled) : 0;
            }

            // -----------------------------------------------------------------
            // STEP B: DETEKSI 4 KOTAK HITAM BLOK TABEL LJK (Solid Square ⬛)
            // -----------------------------------------------------------------
            function findSolidSquareCentroid(minX, maxX, minY, maxY) {
                let sumX = 0, sumY = 0, count = 0;
                for (let y = Math.floor(minY); y < maxY; y += 2) {
                    for (let x = Math.floor(minX); x < maxX; x += 2) {
                        if (x >= 2 && x < width - 2 && y >= 2 && y < height - 2) {
                            let isSolid = true;
                            for (let dy = -2; dy <= 2; dy += 2) {
                                for (let dx = -2; dx <= 2; dx += 2) {
                                    let offset = ((y + dy) * width + (x + dx)) * 4;
                                    let b = (pixels[offset] + pixels[offset + 1] + pixels[offset + 2]) / 3;
                                    if (b > 80) { isSolid = false; break; }
                                }
                                if (!isSolid) break;
                            }
                            if (isSolid) {
                                sumX += x;
                                sumY += y;
                                count++;
                            }
                        }
                    }
                }
                return count >= 6 ? { x: sumX / count, y: sumY / count } : null;
            }

            // Cari 4 titik sudut tepat di lokasi Kotak Hitam Blok Tabel LJK (⬛)
            // Y: 37% - 44% (Tepat di bawah QR Code/Kartu Identitas), Y: 52% - 62% (Bawah tabel)
            let tableTL = findSolidSquareCentroid(paperLeft, paperLeft + (pW * 0.20), paperTop + (pH * 0.37), paperTop + (pH * 0.44));
            let tableTR = findSolidSquareCentroid(paperRight - (pW * 0.20), paperRight, paperTop + (pH * 0.37), paperTop + (pH * 0.44));
            let tableBL = findSolidSquareCentroid(paperLeft, paperLeft + (pW * 0.20), paperTop + (pH * 0.52), paperTop + (pH * 0.62));
            let tableBR = findSolidSquareCentroid(paperRight - (pW * 0.20), paperRight, paperTop + (pH * 0.52), paperTop + (pH * 0.62));

            let hasTableCorners = (tableTL && tableTR && tableBL && tableBR);

            // Rumus Proyeksi Perspektif Bilinear 2D (Mengoreksi Foto Miring/Trapesium 100%)
            function getBilinearPoint(u, v) {
                if (hasTableCorners) {
                    let topX = (1 - v) * tableTL.x + v * tableTR.x;
                    let topY = (1 - v) * tableTL.y + v * tableTR.y;
                    let botX = (1 - v) * tableBL.x + v * tableBR.x;
                    let botY = (1 - v) * tableBL.y + v * tableBR.y;
                    return {
                        x: (1 - u) * topX + u * botX,
                        y: (1 - u) * topY + u * botY
                    };
                } else {
                    let tableAnchorY = paperTop + (pH * 0.37);
                    let gridTopC  = tableAnchorY + (pH * 0.045);
                    let rowHC     = (pH * 0.055);
                    return {
                        x: paperLeft + (pW * v),
                        y: gridTopC + (u * rowHC * 2)
                    };
                }
            }

            // -----------------------------------------------------------------
            // STEP C: DETEKSI DUAL TIMING TRACK ROW MARKS (⬛ Garis Penanda Kiri & Kanan)
            // -----------------------------------------------------------------
            function scanTimingTrackColumn(minX, maxX, minY, maxY) {
                let centers = [];
                let currentCluster = null;
                for (let y = Math.floor(minY); y < maxY; y += 2) {
                    let darkCountInRow = 0;
                    for (let x = Math.floor(minX); x < maxX; x += 2) {
                        let offset = (y * width + x) * 4;
                        let b = (pixels[offset] + pixels[offset + 1] + pixels[offset + 2]) / 3;
                        if (b < 70) darkCountInRow++;
                    }

                    if (darkCountInRow >= 3) {
                        if (!currentCluster) {
                            currentCluster = { minY: y, maxY: y };
                        } else {
                            currentCluster.maxY = y;
                        }
                    } else {
                        if (currentCluster) {
                            if ((currentCluster.maxY - currentCluster.minY) >= 4) {
                                centers.push((currentCluster.minY + currentCluster.maxY) / 2);
                            }
                            currentCluster = null;
                        }
                    }
                }
                if (currentCluster && (currentCluster.maxY - currentCluster.minY) >= 4) {
                    centers.push((currentCluster.minY + currentCluster.maxY) / 2);
                }
                return centers;
            }

            let leftTrackYCenters  = scanTimingTrackColumn(paperLeft + (pW * 0.11), paperLeft + (pW * 0.20), paperTop + (pH * 0.38), paperTop + (pH * 0.65));
            let rightTrackYCenters = scanTimingTrackColumn(paperLeft + (pW * 0.52), paperLeft + (pW * 0.62), paperTop + (pH * 0.38), paperTop + (pH * 0.65));

            // -----------------------------------------------------------------
            // STRATEGI 1: FORMAT C (LJK Terpisah - 2 Kolom Dual Timing Track Precision)
            // -----------------------------------------------------------------
            let formatCResults = [];
            let halfCount = Math.ceil(totalSoal / 2);

            currentDistribusi.forEach((soal, idx) => {
                let numPilihan = Math.min(soal.pilihan.length, 4);
                if (numPilihan <= 0) return;

                let isRightCol = idx >= halfCount;
                let rowIdx     = isRightCol ? (idx - halfCount) : idx;

                let activeTrack = isRightCol ? (rightTrackYCenters.length > 0 ? rightTrackYCenters : leftTrackYCenters) : leftTrackYCenters;
                let qY1, qY2;
                if (activeTrack.length > rowIdx) {
                    let centerY = activeTrack[rowIdx];
                    qY1 = centerY - (pH * 0.024);
                    qY2 = centerY + (pH * 0.024);
                } else {
                    let u1 = (rowIdx + 0.12) / (halfCount || 1);
                    let u2 = (rowIdx + 0.88) / (halfCount || 1);
                    let pTL = getBilinearPoint(u1, isRightCol ? 0.58 : 0.16);
                    let pBR = getBilinearPoint(u2, isRightCol ? 0.86 : 0.44);
                    qY1 = pTL.y;
                    qY2 = pBR.y;
                }

                let startXPercent = isRightCol ? 0.58 : 0.20;
                let totalXWidth   = 0.28;
                let colW          = totalXWidth / numPilihan;

                let cRatios = [];
                let optionBoxes = [];

                for (let pIdx = 0; pIdx < numPilihan; pIdx++) {
                    let optX1 = paperLeft + (pW * (startXPercent + (pIdx * colW)));
                    let optX2 = optX1 + (pW * colW);

                    let ratio = sampleDarkRatio(optX1, qY1, optX2, qY2);
                    cRatios.push({ pIdx: pIdx, ratio: ratio });
                    optionBoxes.push({ x1: optX1, y1: qY1, x2: optX2, y2: qY2 });
                }

                cRatios.sort((a, b) => b.ratio - a.ratio);
                let top = cRatios[0];
                let second = cRatios[1] || { ratio: 0 };
                let gap = top.ratio - second.ratio;

                let isDetected = (top.ratio > 0.008 && (gap > 0.002 || top.ratio > (second.ratio * 1.10)));

                formatCResults.push({
                    idx: idx,
                    soal: soal,
                    bestPIdx: isDetected ? top.pIdx : -1,
                    topPIdx: top.pIdx,
                    gap: gap,
                    topRatio: top.ratio,
                    optionBoxes: optionBoxes
                });
            });

            let avgGapC = formatCResults.reduce((acc, r) => acc + r.gap, 0) / (totalSoal || 1);

            // -----------------------------------------------------------------
            // STRATEGI 2: FORMAT A (Jawaban Langsung di Soal Y: paperTop -> paperBot)
            // -----------------------------------------------------------------
            let formatAResults = [];
            let topY_A    = paperTop + (pH * 0.28);
            let botY_A    = paperBot - (pH * 0.08);
            let qHeight_A = (botY_A - topY_A) / (totalSoal || 1);

            currentDistribusi.forEach((soal, idx) => {
                let numPilihan = Math.min(soal.pilihan.length, 4);
                if (numPilihan <= 0) return;

                let qTop = topY_A + (idx * qHeight_A);
                let optH = (qHeight_A * 0.70) / numPilihan;
                let optTopBase = qTop + (qHeight_A * 0.22);
                let aRatios = [];
                let optionBoxesA = [];
                for (let pIdx = 0; pIdx < numPilihan; pIdx++) {
                    let optY1 = optTopBase + (pIdx * optH);
                    let optY2 = optY1 + optH;
                    let bubbleX1 = paperLeft + (pW * 0.03);
                    let bubbleX2 = paperLeft + (pW * 0.18);

                    let ratio = sampleDarkRatio(bubbleX1, optY1, bubbleX2, optY2);
                    aRatios.push({ pIdx: pIdx, ratio: ratio });
                    optionBoxesA.push({ x1: bubbleX1, y1: optY1, x2: bubbleX2, y2: optY2 });
                }

                aRatios.sort((a, b) => b.ratio - a.ratio);
                let top = aRatios[0];
                let second = aRatios[1] || { ratio: 0 };
                let gap = top.ratio - second.ratio;

                let isDetected = (top.ratio > 0.008 && (gap > 0.002 || top.ratio > (second.ratio * 1.10)));

                formatAResults.push({
                    idx: idx,
                    soal: soal,
                    bestPIdx: isDetected ? top.pIdx : -1,
                    topPIdx: top.pIdx,
                    gap: gap,
                    topRatio: top.ratio,
                    optionBoxes: optionBoxesA
                });
            });

            let avgGapA = formatAResults.reduce((acc, r) => acc + r.gap, 0) / (totalSoal || 1);

            // -----------------------------------------------------------------
            // STRATEGI 3: FORMAT B (LJK Side-by-Side Sisi Kanan)
            // -----------------------------------------------------------------
            let formatBResults = [];
            let topY_B    = paperTop + (pH * 0.28);
            let botY_B    = paperBot - (pH * 0.08);
            let qHeight_B = (botY_B - topY_B) / (totalSoal || 1);

            currentDistribusi.forEach((soal, idx) => {
                let numPilihan = Math.min(soal.pilihan.length, 4);
                if (numPilihan <= 0) return;

                let qTop       = topY_B + (idx * qHeight_B);
                let rightWidth = pW * 0.48;
                let rightMargin= paperLeft + (pW * 0.48);
                let colW       = rightWidth / numPilihan;
                let bRatios    = [];
                let optionBoxesB = [];

                for (let pIdx = 0; pIdx < numPilihan; pIdx++) {
                    let cellX = rightMargin + (pIdx * colW) + (colW * 0.10);
                    let cellY = qTop + (qHeight_B * 0.10);
                    let cellW = colW * 0.8;
                    let cellH = qHeight_B * 0.8;

                    let ratio = sampleDarkRatio(cellX, cellY, cellX + cellW, cellY + cellH);
                    bRatios.push({ pIdx: pIdx, ratio: ratio });
                    optionBoxesB.push({ x1: cellX, y1: cellY, x2: cellX + cellW, y2: cellY + cellH });
                }

                bRatios.sort((a, b) => b.ratio - a.ratio);
                let top = bRatios[0];
                let second = bRatios[1] || { ratio: 0 };
                let gap = top.ratio - second.ratio;

                let isDetected = (top.ratio > 0.008 && (gap > 0.002 || top.ratio > (second.ratio * 1.10)));

                formatBResults.push({
                    idx: idx,
                    soal: soal,
                    bestPIdx: isDetected ? top.pIdx : -1,
                    topPIdx: top.pIdx,
                    gap: gap,
                    topRatio: top.ratio,
                    optionBoxes: optionBoxesB
                });
            });

            let avgGapB = formatBResults.reduce((acc, r) => acc + r.gap, 0) / (totalSoal || 1);

            // -----------------------------------------------------------------
            // PILIH STRATEGI TERBAIK BERDASARKAN JUMLAH JAWABAN TERDETEKSI PROPOSIONAL
            // -----------------------------------------------------------------
            let countC = formatCResults.filter(r => r.bestPIdx >= 0).length;
            let countA = formatAResults.filter(r => r.bestPIdx >= 0).length;
            let countB = formatBResults.filter(r => r.bestPIdx >= 0).length;

            let winningStrategy = formatCResults;
            if (countA > countC && countA >= countB) {
                winningStrategy = formatAResults;
            } else if (countB > countC && countB > countA) {
                winningStrategy = formatBResults;
            } else if (countC >= countA && countC >= countB && countC > 0) {
                winningStrategy = formatCResults;
            } else if (avgGapC >= avgGapA && avgGapC >= avgGapB) {
                winningStrategy = formatCResults;
            }

            // Ultimate Fallback: jika batas ketat gagal, ambil opsi dengan rasio kehitaman tertinggi
            let activeCount = winningStrategy.filter(r => r.bestPIdx >= 0).length;
            if (activeCount === 0) {
                winningStrategy = formatCResults;
                winningStrategy.forEach(r => {
                    if (r.topRatio > 0.003) {
                        r.bestPIdx = r.topPIdx;
                    }
                });
            }

            let detectedCount = 0;
            winningStrategy.forEach((res) => {
                if (res.bestPIdx >= 0 && res.soal.pilihan[res.bestPIdx]) {
                    let detectedPilId = res.soal.pilihan[res.bestPIdx].id;
                    selectOptionJawaban(res.soal.IdSoal, detectedPilId);
                    detectedCount++;
                }
            });

            // -----------------------------------------------------------------
            // RENDER SVG TARGET GUIDE VISUAL OMR (SOLUSI 2: VISUAL OVERLAY)
            // -----------------------------------------------------------------
            let svgContent = '';
            let hurufPositional = ['A', 'B', 'C', 'D', 'E', 'F'];

            winningStrategy.forEach((res) => {
                let numPilihan = Math.min(res.soal.pilihan.length, 4);
                for (let pIdx = 0; pIdx < numPilihan; pIdx++) {
                    let box = res.optionBoxes ? res.optionBoxes[pIdx] : null;
                    if (!box) continue;

                    let pctX1 = (box.x1 / width) * 100;
                    let pctY1 = (box.y1 / height) * 100;
                    let pctW  = ((box.x2 - box.x1) / width) * 100;
                    let pctH  = ((box.y2 - box.y1) / height) * 100;
                    let label = hurufPositional[pIdx] || 'A';

                    if (res.bestPIdx === pIdx) {
                        svgContent += `<rect x="${pctX1.toFixed(2)}%" y="${pctY1.toFixed(2)}%" width="${pctW.toFixed(2)}%" height="${pctH.toFixed(2)}%" fill="rgba(40, 167, 69, 0.40)" stroke="#28a745" stroke-width="2" rx="4" />`;
                        svgContent += `<text x="${(pctX1 + pctW/2).toFixed(2)}%" y="${(pctY1 + pctH/2).toFixed(2)}%" fill="#ffffff" font-size="12" font-weight="bold" text-anchor="middle" dominant-baseline="central">✓ ${label}</text>`;
                    } else {
                        svgContent += `<rect x="${pctX1.toFixed(2)}%" y="${pctY1.toFixed(2)}%" width="${pctW.toFixed(2)}%" height="${pctH.toFixed(2)}%" fill="rgba(220, 53, 69, 0.10)" stroke="#dc3545" stroke-width="1.5" stroke-dasharray="3,3" rx="4" />`;
                        svgContent += `<text x="${(pctX1 + pctW/2).toFixed(2)}%" y="${(pctY1 + pctH/2).toFixed(2)}%" fill="#dc3545" font-size="10" text-anchor="middle" dominant-baseline="central">${label}</text>`;
                    }
                }
            });

            $('#omrTargetOverlaySvg').html(svgContent).show();
            $('#btnToggleOverlay').removeClass('btn-overlay-off').addClass('btn-overlay-on');

            let qrCodeDetected = (typeof lastDetectedSantriId !== 'undefined' && lastDetectedSantriId !== null);

            Swal.close();
            Swal.fire({
                icon: 'success',
                title: 'Auto-Detect (OMR) Selesai!',
                html: `Sistem berhasil menganalisis foto LJK.<br><strong>${detectedCount} dari ${totalSoal} jawaban</strong> terisi secara otomatis.${qrCodeDetected ? '<br><small class="text-success"><i class="fas fa-qrcode"></i> QR Code Identitas Terverifikasi</small>' : ''}`,
                confirmButtonText: 'Tinjau & Simpan'
            });

        } catch (err) {
            Swal.close();
            console.error(err);
            Swal.fire('Error', 'Gagal memproses deteksi foto OMR: ' + err.message, 'error');
        }
}

function compressImageElement(imgElement, maxDim = 2000, quality = 0.85) {
    let canvas = document.createElement('canvas');
    let w = imgElement.naturalWidth || imgElement.width || 1200;
    let h = imgElement.naturalHeight || imgElement.height || 1600;

    if (w > maxDim || h > maxDim) {
        if (w > h) {
            h = Math.round((h * maxDim) / w);
            w = maxDim;
        } else {
            w = Math.round((w * maxDim) / h);
            h = maxDim;
        }
    }

    canvas.width  = w;
    canvas.height = h;
    let ctx = canvas.getContext('2d');
    ctx.drawImage(imgElement, 0, 0, w, h);

    let dataUrl = canvas.toDataURL('image/jpeg', quality);
    return dataURLtoBlob(dataUrl);
}

function simpanJawabanManualAction() {
    if (!currentSantriId || !currentSesiId) {
        Swal.fire('Peringatan', 'Silakan pilih santri terlebih dahulu.', 'warning');
        return;
    }

    let formData = new FormData();
    formData.append('idJadwal', currentJadwalId);
    formData.append('idSantri', currentSantriId);
    formData.append('idSesi', currentSesiId);

    // Kompresi foto HANYA dilakukan saat menekan tombol Simpan Jawaban
    let imgElement = document.getElementById('ljkPreviewImage');
    let fileInput  = document.getElementById('uploadFotoInput');

    if (imgElement && imgElement.src && imgElement.style.display !== 'none' && imgElement.src !== '' && !imgElement.src.startsWith('http')) {
        let compressedBlob = compressImageElement(imgElement, 2000, 0.85);
        formData.append('foto_ljk', compressedBlob, 'LJK_SAVE_' + (currentSantriId || '0') + '_' + Date.now() + '.jpg');
    } else if (fileInput && fileInput.files && fileInput.files[0]) {
        formData.append('foto_ljk', fileInput.files[0]);
    }

    for (let idSoal in currentJawabanMap) {
        if (currentJawabanMap[idSoal].idPilihan) {
            formData.append('jawaban[' + idSoal + ']', currentJawabanMap[idSoal].idPilihan);
        }
    }

    Swal.fire({
        title: 'Menyimpan Jawaban...',
        text: 'Sistem sedang mengevaluasi jawaban dan menghitung nilai.',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    $.ajax({
        url: '<?= base_url('backend/ujian-mdta/jadwal/simpan-jawaban-manual') ?>',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(res) {
            Swal.close();
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disimpan & Diverifikasi!',
                    html: `Jawaban manual berhasil dihitung.<br><strong class="fs-5 text-success">Nilai Akhir: ${res.nilaiAkhir}</strong>`,
                    timer: 1800,
                    showConfirmButton: false
                });

                // Update badge status & nilai secara langsung pada roster sebelah kiri
                let btn = $('#btn-santri-' + currentSantriId);
                if (btn.length) {
                    btn.find('.status-badge')
                       .removeClass('bg-secondary bg-warning text-dark bg-info')
                       .addClass('bg-success text-white')
                       .text('Verifikasi Manual');

                    let nilaiFormatted = parseFloat(res.nilaiAkhir || 0).toFixed(1);
                    let hasFoto = (res.fotoUrl && res.fotoUrl !== '') || ($('#ljkPreviewImage').is(':visible') && $('#ljkPreviewImage').attr('src') !== '');
                    
                    let nilaiHtml = `
                        <small class="text-success fw-bold nilai-text"><i class="fas fa-check-circle me-1"></i> Nilai: ${nilaiFormatted}</small>
                        ${hasFoto ? '<small class="text-primary foto-icon"><i class="fas fa-image" title="Ada Foto LJK"></i></small>' : ''}
                    `;

                    let nilaiBox = btn.find('.nilai-container');
                    if (nilaiBox.length) {
                        nilaiBox.html(nilaiHtml);
                    } else {
                        btn.append(`<div class="nilai-container mt-1 d-flex justify-content-between align-items-center">${nilaiHtml}</div>`);
                    }
                }

                // Re-load data santri agar warna Benar (Hijau) & Salah (Merah) serta Kunci Jawaban langsung tampil
                loadSantriVerification(currentJadwalId, currentSantriId);
            } else {
                Swal.fire('Gagal', res.message || 'Gagal menyimpan jawaban.', 'error');
            }
        },
        error: function() {
            Swal.close();
            Swal.fire('Error', 'Terjadi kesalahan koneksi saat menyimpan.', 'error');
        }
    });
}
</script>

<?= $this->endSection(); ?>
