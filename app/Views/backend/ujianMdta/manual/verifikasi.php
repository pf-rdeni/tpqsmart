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
                        <div id="ljkImageWrapper" style="position: relative; display: inline-block; max-width: 100%; max-height: 100%; transform-origin: center center; margin: auto;">
                            <img id="ljkPreviewImage" src="" alt="LJK Preview" style="display: block; max-width: 100%; max-height: 100%; object-fit: contain;">
                            <svg id="omrTargetOverlaySvg" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; display: none; z-index: 4;"></svg>
                        </div>

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

<!-- OpenCV.js Engine -->
<script>
var cvReady = false;
var Module = {
    onRuntimeInitialized: function() {
        cvReady = true;
        console.log('✅ OpenCV.js engine loaded and ready');
    }
};
</script>
<script async src="https://docs.opencv.org/4.9.0/opencv.js" type="text/javascript"></script>

<script>
let currentJadwalId     = <?= (int)$jadwal['id'] ?>;
let currentSantriId     = null;
let currentSesiId       = null;
let currentDistribusi   = [];
let currentJawabanMap   = {};
let currentJumlahPilihan = <?= (int)($jadwal['JumlahPilihan'] ?? 4) ?>;
let currentZoom         = 1;
let currentRotate       = 0;

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

            currentSesiId           = res.idSesi;
            currentDistribusi       = res.distribusi;
            currentJawabanMap       = res.jawabanMap || {};
            currentJumlahPilihan    = res.jumlahPilihan || currentJumlahPilihan;
            currentWaktuVerifikasi  = res.waktuVerifikasi || null;
            currentDiverifikasiOleh = res.diverifikasiOleh || null;

            // Render Header Santri
            $('#selectedSantriTitle').text(res.santriInfo.NamaSantri);
            $('#selectedSantriSub').text('NIS: ' + (res.santriInfo.NISN || res.santriInfo.IdSantri) + ' | Kelas: ' + (res.santriInfo.NamaKelas || '-'));

            // Render Image LJK jika ada di DB, KECUALI jika pengguna baru saja mengunggah foto baru secara langsung
            if (!hasTempUpload) {
                if (res.fotoUrl) {
                    $('#ljkPreviewImage').attr('src', res.fotoUrl).show();
                    $('#ljkImageWrapper').show();
                    $('#imagePlaceholder').hide();
                    $('#imageControls').show();
                    $('#btnAutoDetectOmr').show();
                } else {
                    $('#ljkPreviewImage').attr('src', '').hide();
                    $('#ljkImageWrapper').hide();
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
            $('#ljkImageWrapper').show();
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
    $('#ljkImageWrapper').css('transform', styleVal);
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
        let firstBtn = $('.item-santri-btn:first');
        if (firstBtn.length > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Santri Belum Dipilih',
                text: 'Silakan pilih santri terlebih dahulu di daftar sebelah kiri.',
                confirmButtonText: 'Pilih Santri Pertama'
            }).then(() => { firstBtn.click(); });
        } else {
            Swal.fire('Peringatan', 'Form kisi-kisi santri belum dimuat. Silakan pilih santri.', 'warning');
        }
        return;
    }

    if (typeof cv === 'undefined' || !cvReady) {
        Swal.fire({
            title: 'Memuat Engine OpenCV...',
            text: 'Harap tunggu beberapa detik hingga engine Computer Vision siap.',
            timer: 2500,
            showConfirmButton: false
        });
        return;
    }

    Swal.fire({
        title: 'Deteksi Bulatan OMR (OpenCV)...',
        html: '<i class="fas fa-circle-notch fa-spin text-success me-2 fs-4"></i>Mencari geometri bulatan LJK (silangan X & hitam penuh)...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    setTimeout(() => {
        try {
            processOMRWithDirectCircles(img);
        } catch (e) {
            Swal.close();
            console.error('[OpenCV Error]', e);
            Swal.fire('Error OMR', 'Gagal memproses deteksi lingkaran: ' + e.message, 'error');
        }
    }, 120);
}

/**
 * processOMRWithDirectCircles â€” OpenCV Direct Bubble Detection + Grid Reconstruction
 *
 * Mengapa Grid Reconstruction?
 * Coretan silang (X) atau hitam pada bulatan merusak kontur lingkaran murni, sehingga
 * OpenCV HoughCircles sering melewatinya. Namun, bulatan KOSONG di sekitarnya tetap terdeteksi 100%.
 * Algoritma ini merekonstruksi matriks kisi-kisi (Grid Matrix) lengkap dari bulatan kosong yang terdeteksi,
 * lalu mengukur kepadatan piksel (Silang X / Hitam) pada SETIAP sel kisi-kisi.
 */

function processOMRWithDirectCircles(imgElement) {
    let src = null, gray = null, blurred = null, thresh = null, circles = null, contours = null, hierarchy = null;

    try {
        src = cv.imread(imgElement);
        let W = src.cols;
        let H = src.rows;

        gray = new cv.Mat();
        cv.cvtColor(src, gray, cv.COLOR_RGBA2GRAY, 0);

        blurred = new cv.Mat();
        cv.GaussianBlur(gray, blurred, new cv.Size(5, 5), 0);

        // --- Step 1: Deteksi Lingkaran Bulatan LJK via HoughCircles ---
        circles = new cv.Mat();
        let minRadius = Math.max(5, Math.round(W * 0.007));  // ~8-10px
        let maxRadius = Math.min(35, Math.round(W * 0.025)); // ~20-25px
        let minDist   = Math.max(10, Math.round(W * 0.018)); // Jarak minimum antar bulatan

        cv.HoughCircles(
            blurred, circles, cv.HOUGH_GRADIENT,
            1.2,
            minDist,
            50,
            24,         // Threshold sensitivitas lingkaran
            minRadius,
            maxRadius
        );

        let rawCircles = [];
        if (circles.cols > 0) {
            for (let i = 0; i < circles.cols; ++i) {
                let x = circles.data32F[i * 3];
                let y = circles.data32F[i * 3 + 1];
                let r = circles.data32F[i * 3 + 2];
                rawCircles.push({ x: x, y: y, r: r });
            }
        }

        // --- Step 2: Fallback Contour Circularity Filter jika HoughCircles sedikit ---
        if (rawCircles.length < 15) {
            thresh = new cv.Mat();
            cv.adaptiveThreshold(blurred, thresh, 255, cv.ADAPTIVE_THRESH_GAUSSIAN_C, cv.THRESH_BINARY_INV, 15, 3);

            contours = new cv.MatVector();
            hierarchy = new cv.Mat();
            cv.findContours(thresh, contours, hierarchy, cv.RETR_TREE, cv.CHAIN_APPROX_SIMPLE);

            for (let i = 0; i < contours.size(); ++i) {
                let cnt = contours.get(i);
                let area = cv.contourArea(cnt);
                let perimeter = cv.arcLength(cnt, true);

                if (perimeter > 0) {
                    let circularity = (4 * Math.PI * area) / (perimeter * perimeter);
                    if (circularity >= 0.55) {
                        let circle = cv.minEnclosingCircle(cnt);
                        let r = circle.radius;
                        if (r >= minRadius && r <= maxRadius) {
                            rawCircles.push({ x: circle.center.x, y: circle.center.y, r: r });
                        }
                    }
                }
            }
        }

        // --- Step 3: FILTERING KARTU HEADER IDENTITAS ---
        // Buang seluruh lingkaran di luar area tabel jawaban (Y < 24% tinggi gambar adalah header identitas)
        let detectedCircles = rawCircles.filter(c => c.y >= H * 0.24 && c.y <= H * 0.96);

        console.log('[OpenCV] Total lingkaran terdeteksi (raw):', rawCircles.length, '| Setelah filter header:', detectedCircles.length);

        if (detectedCircles.length === 0) {
            Swal.close();
            Swal.fire({
                icon: 'warning',
                title: 'Lingkaran Tidak Terdeteksi',
                text: 'Sistem tidak menemukan bentuk bulatan LJK di tabel jawaban. Pastikan foto cukup terang dan tabel jawaban terlihat.'
            });
            return;
        }

        // --- Step 4: Helper Pengukur Kegelapan Piksel (Silang X / Hitam Penuh) ---
        function measureCircleDarkness(cx, cy, r) {
            let sampleRadius = Math.max(4, Math.round(r * 0.80));
            let x1 = Math.max(0, Math.floor(cx - sampleRadius));
            let x2 = Math.min(W - 1, Math.ceil(cx + sampleRadius));
            let y1 = Math.max(0, Math.floor(cy - sampleRadius));
            let y2 = Math.min(H - 1, Math.ceil(cy + sampleRadius));

            let darkCount = 0;
            let totalCount = 0;

            for (let y = y1; y <= y2; y += 2) {
                for (let x = x1; x <= x2; x += 2) {
                    let dx = x - cx;
                    let dy = y - cy;
                    if (dx * dx + dy * dy <= sampleRadius * sampleRadius) {
                        totalCount++;
                        let val = gray.ucharPtr(y, x)[0];
                        // Threshold gray < 130 = piksel gelap (pulpen/pensil/silang/hitam)
                        if (val < 130) darkCount++;
                    }
                }
            }
            return totalCount > 0 ? (darkCount / totalCount) : 0.0;
        }

        // --- Step 5: Rekonstruksi Matriks Kisi-kisi (Grid Matrix Reconstruction) ---
        function reconstructSubTableGrid(circlesList, expectedRows, numOptions) {
            if (circlesList.length === 0) return [];

            // A. Kelompokkan Y menjadi baris-baris
            circlesList.sort((a, b) => a.y - b.y);
            let rawRowClusters = [];
            let curRow = [circlesList[0]];
            for (let i = 1; i < circlesList.length; i++) {
                if (Math.abs(circlesList[i].y - curRow[curRow.length - 1].y) <= 18) {
                    curRow.push(circlesList[i]);
                } else {
                    rawRowClusters.push(curRow);
                    curRow = [circlesList[i]];
                }
            }
            if (curRow.length > 0) rawRowClusters.push(curRow);

            let rowYCenters = rawRowClusters.map(cluster => {
                let sumY = cluster.reduce((acc, c) => acc + c.y, 0);
                return sumY / cluster.length;
            });

            // Filter Baris Header Noise:
            // Jika baris terdeteksi > expectedRows (misal: terdeteksi lingkaran header "NO"),
            // ambil expectedRows baris paling bawah (yaitu baris Soal 1..N)
            if (rowYCenters.length > expectedRows) {
                rowYCenters = rowYCenters.slice(rowYCenters.length - expectedRows);
            } else if (rowYCenters.length > 1 && rowYCenters.length < expectedRows) {
                let deltaYs = [];
                for (let i = 1; i < rowYCenters.length; i++) {
                    deltaYs.push(rowYCenters[i] - rowYCenters[i - 1]);
                }
                deltaYs.sort((a, b) => a - b);
                let medianDeltaY = deltaYs[Math.floor(deltaYs.length / 2)];

                while (rowYCenters.length < expectedRows) {
                    let lastY = rowYCenters[rowYCenters.length - 1];
                    rowYCenters.push(lastY + medianDeltaY);
                }
                rowYCenters = rowYCenters.slice(0, expectedRows);
            }

            // B. Kelompokkan X menjadi kolom-kolom (A, B, C, D)
            circlesList.sort((a, b) => a.x - b.x);
            let colClusters = [];
            let curCol = [circlesList[0]];
            for (let i = 1; i < circlesList.length; i++) {
                if (Math.abs(circlesList[i].x - curCol[curCol.length - 1].x) <= 22) {
                    curCol.push(circlesList[i]);
                } else {
                    colClusters.push(curCol);
                    curCol = [circlesList[i]];
                }
            }
            if (curCol.length > 0) colClusters.push(curCol);

            let colXCenters = colClusters.map(cluster => {
                let sumX = cluster.reduce((acc, c) => acc + c.x, 0);
                return sumX / cluster.length;
            });

            // Filter Kolom NO / Timing Mark Noise:
            // Jika kolom terdeteksi > numOptions (misal: terdeteksi kolom NO/Timing Mark di sebelah kiri A),
            // ambil numOptions kolom paling kanan (yaitu kolom pilihan A, B, C, D)
            if (colXCenters.length > numOptions) {
                colXCenters = colXCenters.slice(colXCenters.length - numOptions);
            } else if (colXCenters.length > 1 && colXCenters.length < numOptions) {
                let deltaXs = [];
                for (let i = 1; i < colXCenters.length; i++) {
                    deltaXs.push(colXCenters[i] - colXCenters[i - 1]);
                }
                deltaXs.sort((a, b) => a - b);
                let medianDeltaX = deltaXs[Math.floor(deltaXs.length / 2)];

                while (colXCenters.length < numOptions) {
                    let lastX = colXCenters[colXCenters.length - 1];
                    colXCenters.push(lastX + medianDeltaX);
                }
                colXCenters = colXCenters.slice(0, numOptions);
            }

            // Radius median
            let radii = circlesList.map(c => c.r);
            radii.sort((a, b) => a - b);
            let avgRadius = radii.length > 0 ? radii[Math.floor(radii.length / 2)] : 12;

            // Bentuk Matriks Kisi-kisi Presisi (expectedRows x numOptions)
            let fullGrid = [];
            for (let r = 0; r < rowYCenters.length; r++) {
                let rowGrid = [];
                let y = rowYCenters[r];
                for (let c = 0; c < colXCenters.length; c++) {
                    let x = colXCenters[c];
                    rowGrid.push({
                        x: x,
                        y: y,
                        r: avgRadius,
                        darkRatio: measureCircleDarkness(x, y, avgRadius)
                    });
                }
                fullGrid.push(rowGrid);
            }

            return fullGrid;
        }


        // --- Step 6: Multi-Format Layout & Multi-Page Auto-Detection (Format A, B, C) ---
        let numOptions  = currentJumlahPilihan || 4;
        let distribusi  = currentDistribusi || [];

        let midX = W * 0.49;
        let leftCircles  = detectedCircles.filter(c => c.x < midX);
        let rightCircles = detectedCircles.filter(c => c.x >= midX);

        let detectedAnswers = [];
        let detectedCount   = 0;
        let allGridCircles  = [];

        // Helper untuk mengevaluasi & mengaplikasikan jawaban dari suatu matriks grid
        function processGridAnswers(soalList, gridMatrix) {
            soalList.forEach((soal, rowIdx) => {
                if (rowIdx >= gridMatrix.length) return;
                let rowCircles = gridMatrix[rowIdx];
                allGridCircles.push(...rowCircles);

                let maxDark = -1;
                let bestCircleIdx = -1;
                rowCircles.forEach((c, cIdx) => {
                    if (c.darkRatio > maxDark) {
                        maxDark = c.darkRatio;
                        bestCircleIdx = cIdx;
                    }
                });

                // Threshold: darkRatio >= 0.08 (kegelapan silangan X / hitam penuh)
                if (maxDark >= 0.08 && bestCircleIdx >= 0 && bestCircleIdx < numOptions) {
                    let pilihanList = soal.pilihan || [];
                    let pilObj = pilihanList[bestCircleIdx];
                    let idPil = pilObj ? (pilObj.id || pilObj.IdPilihan) : null;

                    if (idPil) {
                        selectOptionJawaban(soal.IdSoal, idPil);
                        detectedCount++;
                        detectedAnswers.push({
                            soalId: soal.IdSoal,
                            circle: rowCircles[bestCircleIdx],
                            optionIdx: bestCircleIdx,
                            darkRatio: maxDark
                        });
                    }
                }
            });
        }

        // Cek apakah tata letak LJK berupa 2 Sub-tabel (Format C) atau 1 Tabel/Flow Tunggal (Format A & B)
        let isTwoColumnSubTable = (leftCircles.length >= 6 && rightCircles.length >= 6);

        if (isTwoColumnSubTable) {
            // FORMAT C: LJK Terpisah (2 sub-tabel terpisah)
            let halfCount = Math.ceil(distribusi.length / 2);
            let leftSoal  = distribusi.slice(0, halfCount);
            let rightSoal = distribusi.slice(halfCount);

            let leftGrid  = reconstructSubTableGrid(leftCircles, leftSoal.length, numOptions);
            let rightGrid = reconstructSubTableGrid(rightCircles, rightSoal.length, numOptions);

            processGridAnswers(leftSoal, leftGrid);
            processGridAnswers(rightSoal, rightGrid);
        } else {
            // FORMAT A & B: Jawaban Langsung di Soal / Side-by-Side (1 tabel / flow)
            let gridRowsCount = Math.min(distribusi.length, 15); // Estimasi baris per halaman
            let fullGrid = reconstructSubTableGrid(detectedCircles, gridRowsCount, numOptions);

            // INTELLIGENT MULTI-PAGE OFFSET DETECTION:
            // Jika total soal ujian > jumlah baris yang muat dalam 1 lembar foto (misal: 20 soal, 2 lembar):
            // Deteksi blok soal pertama yang belum terisi di form untuk lembar foto ke-2, 3, dst.
            let soalOffset = 0;
            if (distribusi.length > fullGrid.length && fullGrid.length > 0) {
                let pageSize = fullGrid.length;
                for (let i = 0; i < distribusi.length; i += pageSize) {
                    let isBlockComplete = true;
                    for (let j = i; j < Math.min(distribusi.length, i + pageSize); j++) {
                        let idS = distribusi[j].IdSoal;
                        if (!currentJawabanMap[idS] || !currentJawabanMap[idS].idPilihan) {
                            isBlockComplete = false;
                            break;
                        }
                    }
                    if (!isBlockComplete) {
                        soalOffset = i;
                        break;
                    }
                }
            }

            let pageSoalList = distribusi.slice(soalOffset, soalOffset + fullGrid.length);
            processGridAnswers(pageSoalList, fullGrid);
        }
        renderDirectCirclesSvgOverlay(allGridCircles, detectedAnswers, W, H);

        updateLiveScore();

        Swal.close();
        Swal.fire({
            icon: 'success',
            title: 'Deteksi OMR Presisi Selesai!',
            html: `OpenCV berhasil merekonstruksi kisi-kisi LJK.<br>` +
                  `<strong class="text-success fs-5">${detectedCount} dari ${distribusi.length} jawaban</strong> terdeteksi otomatis (Silang X / Hitam Penuh).`,
            confirmButtonText: 'Tinjau & Simpan'
        });

    } finally {
        if (src) src.delete();
        if (gray) gray.delete();
        if (blurred) blurred.delete();
        if (thresh) thresh.delete();
        if (circles) circles.delete();
        if (contours) contours.delete();
        if (hierarchy) hierarchy.delete();
    }
}
function renderDirectCirclesSvgOverlay(allCircles, detectedAnswers, imgW, imgH) {
    let svgEl = document.getElementById('omrTargetOverlaySvg');
    if (!svgEl) return;

    while (svgEl.firstChild) svgEl.removeChild(svgEl.firstChild);

    let imgEl = document.getElementById('ljkPreviewImage');
    if (!imgEl) return;

    let dispW = imgEl.offsetWidth;
    let dispH = imgEl.offsetHeight;
    let scX = dispW / imgW;
    let scY = dispH / imgH;

    svgEl.setAttribute('width', dispW);
    svgEl.setAttribute('height', dispH);

    let selectedCirclesSet = new Set(detectedAnswers.map(a => a.circle));

    allCircles.forEach(c => {
        let isSelected = selectedCirclesSet.has(c);
        let cx = c.x * scX;
        let cy = c.y * scY;
        let r  = c.r * scX;

        let circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circle.setAttribute('cx', cx.toFixed(1));
        circle.setAttribute('cy', cy.toFixed(1));
        circle.setAttribute('r', Math.max(6, r).toFixed(1));
        circle.setAttribute('fill', isSelected ? 'rgba(40,167,69,0.35)' : 'rgba(255,193,7,0.35)');
        circle.setAttribute('stroke', isSelected ? '#28a745' : '#ffc107');
        circle.setAttribute('stroke-width', isSelected ? '2.5' : '1.5');
        svgEl.appendChild(circle);

        if (isSelected) {
            let markAns = detectedAnswers.find(a => a.circle === c);
            if (markAns) {
                let text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                text.setAttribute('x', cx.toFixed(1));
                text.setAttribute('y', (cy - r - 2).toFixed(1));
                text.setAttribute('text-anchor', 'middle');
                text.setAttribute('font-size', '10');
                text.setAttribute('fill', '#28a745');
                text.setAttribute('font-weight', 'bold');
                text.textContent = String.fromCharCode(65 + markAns.optionIdx);
                svgEl.appendChild(text);
            }
        }
    });

    svgEl.style.display = 'block';
    $('#btnToggleOverlay').removeClass('btn-overlay-off').addClass('btn-overlay-on');
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
