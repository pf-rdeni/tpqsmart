<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<?php
// Siapkan data JSON per kelas untuk Live Preview di header
$previewDataByKelas = [];
if (!empty($santriPerKelas)) {
    foreach ($santriPerKelas as $idKelasKey => $listSantriKelas) {
        $previewDataByKelas[$idKelasKey] = [];
        foreach ($listSantriKelas as $s) {
            $photoUrl = '';
            if (!empty($s['PhotoProfil'])) {
                $photoFile = FCPATH . 'uploads/santri/' . $s['PhotoProfil'];
                if (file_exists($photoFile)) {
                    $photoUrl = base_url('uploads/santri/' . $s['PhotoProfil']);
                }
            }
            $previewDataByKelas[$idKelasKey][] = [
                'idSantri' => $s['IdSantri'] ?? '-',
                'namaSantri' => $s['NamaSantri'] ?? '-',
                'alamat' => $s['AlamatSantri'] ?? '-',
                'photoUrl' => $photoUrl,
            ];
        }
    }
}
?>
<div class="col-12">
    <div class="card card-info collapsed-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Panduan Cetak Name Plate Raport Santri
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <ul class="mb-0">
                <li>Pilih salah satu dari <strong>5 Varian Desain</strong>, atur <strong>Ukuran Label (cm)</strong> (default <code>10 &times; 4.8 cm</code>), dan pilih <strong>Ukuran Kertas</strong> (A4, F4/Folio, Letter, atau Custom).</li>
                <li><strong>Live Preview</strong> di sebelah kanan menampilkan desain dan data santri asli dari kelas tab yang aktif. Gunakan tombol <strong>&lsaquo; / &rsaquo;</strong> atau geser (*swipe*) untuk melihat santri lainnya.</li>
                <li>Jika santri belum memiliki foto profil, akan ditampilkan kotak kosong bertuliskan <strong>Foto 3x4</strong> untuk tempel foto fisik.</li>
            </ul>
        </div>
    </div>

    <!-- Card Pengaturan & Live Preview (Header Terpadu) -->
    <div class="card card-outline card-primary">
        <div class="card-header py-2 d-flex align-items-center justify-content-between">
            <h3 class="card-title mb-0">
                <i class="fas fa-sliders-h"></i> Pengaturan &amp; Live Preview Name Plate
            </h3>
            <div class="card-tools ml-auto">
                <button type="button" id="btnResetNamePlateSetting" class="btn btn-primary btn-xs">
                    <i class="fas fa-undo"></i> Set ke Default (10 &times; 4.8 cm | A4)
                </button>
            </div>
        </div>
        <div class="card-body py-3">
            <div class="row align-items-center">
                <!-- Kolom Kiri: Pengaturan -->
                <div class="col-lg-7">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 mb-2">
                            <label for="variantSelect" class="mb-1 text-xs font-weight-bold">Varian Desain Template</label>
                            <select id="variantSelect" class="form-control form-control-sm np-setting-input">
                                <option value="1" selected>Varian 1 — Formal Minimalis Beraksen Garis</option>
                                <option value="2">Varian 2 — Classic Double-Bordered Resmi</option>
                                <option value="3">Varian 3 — Modern Soft Split</option>
                                <option value="4">Varian 4 — Executive Navy &amp; Gold</option>
                                <option value="5">Varian 5 — Monochrome Clean Typographic</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <label for="paperSizeSelect" class="mb-1 text-xs font-weight-bold">Ukuran Kertas</label>
                            <select id="paperSizeSelect" class="form-control form-control-sm np-setting-input">
                                <option value="a4" selected>A4 (21 &times; 29.7)</option>
                                <option value="folio">F4 / Folio (21.5 &times; 33)</option>
                                <option value="letter">Letter (21.6 &times; 27.9)</option>
                                <option value="custom">Custom (cm)</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <label for="paperOrientationSelect" class="mb-1 text-xs font-weight-bold">Orientasi</label>
                            <select id="paperOrientationSelect" class="form-control form-control-sm np-setting-input">
                                <option value="portrait" selected>Portrait</option>
                                <option value="landscape">Landscape</option>
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-6 mb-2 custom-paper-group" style="display: none;">
                            <label for="paperWidthInput" class="mb-1 text-xs font-weight-bold">Lebar Kertas (cm)</label>
                            <input type="number" id="paperWidthInput" class="form-control form-control-sm np-setting-input" value="21.0" min="10" max="50" step="0.1">
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2 custom-paper-group" style="display: none;">
                            <label for="paperHeightInput" class="mb-1 text-xs font-weight-bold">Tinggi Kertas (cm)</label>
                            <input type="number" id="paperHeightInput" class="form-control form-control-sm np-setting-input" value="29.7" min="10" max="50" step="0.1">
                        </div>

                        <div class="col-md-3 col-sm-6 mb-2">
                            <label for="labelWidthInput" class="mb-1 text-xs font-weight-bold">Lebar Label (cm)</label>
                            <input type="number" id="labelWidthInput" class="form-control form-control-sm np-setting-input np-label-dim" value="10.0" min="5" max="25" step="0.1">
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <label for="labelHeightInput" class="mb-1 text-xs font-weight-bold">Tinggi Label (cm)</label>
                            <input type="number" id="labelHeightInput" class="form-control form-control-sm np-setting-input np-label-dim" value="4.8" min="3" max="15" step="0.1">
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <label for="fotoWidthInput" class="mb-1 text-xs font-weight-bold">Lebar Foto (cm)</label>
                            <input type="number" id="fotoWidthInput" class="form-control form-control-sm np-setting-input" value="3.0" min="1.2" max="8" step="0.1">
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <label for="fotoHeightInput" class="mb-1 text-xs font-weight-bold">Tinggi Foto (cm)</label>
                            <input type="number" id="fotoHeightInput" class="form-control form-control-sm np-setting-input" value="4.0" min="1.6" max="10" step="0.1">
                        </div>
                        <div class="col-md-12 mb-1">
                            <div class="alert alert-light border mb-0 py-1 px-2 d-flex flex-wrap justify-content-between align-items-center text-xs">
                                <span id="layoutEstimateInfo">Estimasi: <strong>2 kolom &times; 5 baris = 10 label</strong> / lembar</span>
                                <span class="text-success font-weight-bold" id="fotoRecommendInfo">Rekomendasi Foto (3:4): 3.0 &times; 4.0 cm</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Live Preview -->
                <div class="col-lg-5 mt-2 mt-lg-0">
                    <div class="border rounded bg-light p-2">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-xs font-weight-bold text-secondary">
                                <i class="fas fa-eye"></i> Live Preview (<span id="previewDimText">10.0 &times; 4.8 cm</span>)
                            </span>
                            <div class="btn-group btn-group-xs">
                                <button type="button" id="btnPreviewPrev" class="btn btn-outline-secondary btn-xs" title="Santri Sebelumnya">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <span id="previewCounter" class="btn btn-default btn-xs disabled" style="min-width: 56px; font-size: 11px;">0 / 0</span>
                                <button type="button" id="btnPreviewNext" class="btn btn-outline-secondary btn-xs" title="Santri Berikutnya">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        <div id="livePreviewViewport" class="d-flex justify-content-center align-items-center" style="min-height: 175px; overflow: hidden; cursor: grab; user-select: none;">
                            <div id="livePreviewCard"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Santri — Cetak Name Plate</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($dataKelasObject)) : ?>
                <div class="card card-primary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs flex-wrap justify-content-start justify-content-md-between" id="kelasTabNamePlate" role="tablist">
                            <?php foreach ($dataKelasObject as $index => $kelas) : ?>
                                <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                                    <a class="nav-link border-white text-center <?= $index === 0 ? 'active' : '' ?>"
                                        id="tab-np-<?= $kelas->IdKelas ?>"
                                        data-toggle="tab"
                                        data-kelas-id="<?= esc($kelas->IdKelas) ?>"
                                        href="#kelas-np-<?= $kelas->IdKelas ?>"
                                        role="tab"
                                        aria-controls="kelas-np-<?= $kelas->IdKelas ?>"
                                        aria-selected="<?= $index === 0 ? 'true' : 'false' ?>">
                                        <?= esc($kelas->NamaKelas) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="kelasTabNamePlateContent">
                            <?php foreach ($dataKelasObject as $index => $kelas) : ?>
                                <?php $kelasSantri = $santriPerKelas[$kelas->IdKelas] ?? []; ?>
                                <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>"
                                    id="kelas-np-<?= $kelas->IdKelas ?>"
                                    role="tabpanel"
                                    aria-labelledby="tab-np-<?= $kelas->IdKelas ?>">
                                    <div class="mb-3 d-flex flex-wrap">
                                        <button type="button"
                                            class="btn btn-warning btn-sm mr-2 mb-2 btn-print-all-nameplate"
                                            data-tpq="<?= esc($currentIdTpq) ?>"
                                            data-kelas="<?= esc($kelas->IdKelas) ?>">
                                            <i class="fas fa-print"></i> Cetak Semua Name Plate (Kelas <?= esc($kelas->NamaKelas) ?>)
                                        </button>
                                        <button type="button"
                                            class="btn btn-success btn-sm mb-2 btn-print-selected-nameplate"
                                            data-kelas="<?= esc($kelas->IdKelas) ?>">
                                            <i class="fas fa-check-square"></i> Cetak Terpilih
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="tableNamePlate-<?= $kelas->IdKelas ?>">
                                            <thead>
                                                <tr>
                                                    <th style="width: 35px;" class="text-center">
                                                        <input type="checkbox" class="check-all-np" data-kelas="<?= esc($kelas->IdKelas) ?>" title="Pilih Semua">
                                                    </th>
                                                    <th>No</th>
                                                    <th>Aksi</th>
                                                    <th>Foto</th>
                                                    <th>ID Santri</th>
                                                    <th>Nama Santri</th>
                                                    <th>Alamat</th>
                                                    <th>Nama Orang Tua</th>
                                                    <th>No. HP</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($kelasSantri)) : ?>
                                                    <?php $no = 1; ?>
                                                    <?php foreach ($kelasSantri as $santri) : ?>
                                                        <?php
                                                        $hasPhoto = false;
                                                        $photoUrl = '';
                                                        if (!empty($santri['PhotoProfil'])) {
                                                            $photoFile = FCPATH . 'uploads/santri/' . $santri['PhotoProfil'];
                                                            if (file_exists($photoFile)) {
                                                                $hasPhoto = true;
                                                                $photoUrl = base_url('uploads/santri/' . $santri['PhotoProfil']);
                                                            }
                                                        }
                                                        $ortu = implode(' | ', array_filter([
                                                            trim((string) ($santri['NamaAyah'] ?? '')),
                                                            trim((string) ($santri['NamaIbu'] ?? '')),
                                                        ], static fn($v) => $v !== '' && $v !== '-'));
                                                        $hp = implode(' | ', array_unique(array_filter([
                                                            trim((string) ($santri['NoHpSantri'] ?? '')),
                                                            trim((string) ($santri['NoHpAyah'] ?? '')),
                                                            trim((string) ($santri['NoHpIbu'] ?? '')),
                                                        ], static fn($v) => $v !== '' && $v !== '-')));
                                                        ?>
                                                        <tr>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="check-item-np check-item-np-<?= esc($kelas->IdKelas) ?>" value="<?= esc($santri['IdSantri']) ?>">
                                                            </td>
                                                            <td><?= $no++ ?></td>
                                                            <td>
                                                                <a href="<?= base_url('backend/santri/generatePDFNamePlate/' . $santri['IdSantri']) ?>"
                                                                    class="btn btn-primary btn-xs btn-print-single-np"
                                                                    data-id="<?= esc($santri['IdSantri']) ?>"
                                                                    target="_blank">
                                                                    <i class="fas fa-print"></i> Cetak
                                                                </a>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php if ($hasPhoto) : ?>
                                                                    <img src="<?= esc($photoUrl) ?>" alt="Foto" style="width: 36px; height: 48px; object-fit: cover; border: 1px solid #ccc;">
                                                                <?php else : ?>
                                                                    <span class="badge badge-secondary">Foto 3x4 Kosong</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= esc($santri['IdSantri']) ?></td>
                                                            <td><?= esc($santri['NamaSantri']) ?></td>
                                                            <td><?= esc($santri['AlamatSantri'] ?? '-') ?></td>
                                                            <td><?= esc($ortu ?: '-') ?></td>
                                                            <td><?= esc($hp ?: '-') ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle"></i> Tidak ada data santri yang ditemukan.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<form id="formSelectedNamePlate" action="<?= base_url('backend/santri/generatePDFSelectedNamePlate') ?>" method="POST" target="_blank" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="santri_ids" id="selectedSantriIds">
    <input type="hidden" name="paper" id="selectedPaperSize" value="a4">
    <input type="hidden" name="orientation" id="selectedOrientation" value="portrait">
    <input type="hidden" name="paper_width" id="selectedPaperWidth" value="21.0">
    <input type="hidden" name="paper_height" id="selectedPaperHeight" value="29.7">
    <input type="hidden" name="label_width" id="selectedLabelWidth" value="10.0">
    <input type="hidden" name="label_height" id="selectedLabelHeight" value="4.8">
    <input type="hidden" name="foto_width" id="selectedFotoWidth" value="3.0">
    <input type="hidden" name="foto_height" id="selectedFotoHeight" value="4.0">
    <input type="hidden" name="variant" id="selectedVariant" value="1">
</form>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    <?php if (!empty($dataKelasObject)) : ?>
        <?php foreach ($dataKelasObject as $kelas) : ?>
            initializeDataTableUmum("#tableNamePlate-<?= $kelas->IdKelas ?>", true, true);
        <?php endforeach; ?>
    <?php endif; ?>

    const previewDataByKelas = <?= json_encode($previewDataByKelas, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    let activeKelasId = $('#kelasTabNamePlate a.active').data('kelas-id') || Object.keys(previewDataByKelas)[0] || null;
    let previewIndex = 0;

    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function(m) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
        });
    }

    const npTabStorageKey = 'nameplate_active_tab_<?= $currentIdTpq ?>';
    $('#kelasTabNamePlate a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        localStorage.setItem(npTabStorageKey, $(e.target).attr('href'));
        activeKelasId = $(e.target).data('kelas-id');
        previewIndex = 0;
        renderLivePreview();
    });

    const savedNpTab = localStorage.getItem(npTabStorageKey);
    if (savedNpTab && $(savedNpTab).length > 0) {
        $('#kelasTabNamePlate a[href="' + savedNpTab + '"]').tab('show');
        activeKelasId = $('#kelasTabNamePlate a[href="' + savedNpTab + '"]').data('kelas-id');
    }

    const npSettingKey = 'nameplate_print_settings';
    const paperDimensionsCm = {
        a4: [21.0, 29.7],
        folio: [21.5, 33.0],
        letter: [21.59, 27.94]
    };

    function computeRecommendedPhoto(labelW, labelH) {
        const maxHByLabel = Math.max(1.6, labelH - 0.8);
        const maxHByWidth = Math.max(1.6, (labelW * 0.36) / 0.75);
        const recH = Math.min(5.6, maxHByLabel, maxHByWidth);
        const recW = recH * 0.75;
        return {
            w: Math.round(recW * 10) / 10,
            h: Math.round(recH * 10) / 10
        };
    }

    function getPrintSettings() {
        const labelWidth = parseFloat($('#labelWidthInput').val()) || 10.0;
        const labelHeight = parseFloat($('#labelHeightInput').val()) || 4.8;
        const rec = computeRecommendedPhoto(labelWidth, labelHeight);
        return {
            variant: parseInt($('#variantSelect').val(), 10) || 1,
            paper: $('#paperSizeSelect').val() || 'a4',
            orientation: $('#paperOrientationSelect').val() || 'portrait',
            paperWidth: parseFloat($('#paperWidthInput').val()) || 21.0,
            paperHeight: parseFloat($('#paperHeightInput').val()) || 29.7,
            labelWidth: labelWidth,
            labelHeight: labelHeight,
            fotoWidth: parseFloat($('#fotoWidthInput').val()) || rec.w,
            fotoHeight: parseFloat($('#fotoHeightInput').val()) || rec.h,
            recFoto: rec
        };
    }

    function renderLivePreview() {
        const s = getPrintSettings();
        const list = (activeKelasId && previewDataByKelas[activeKelasId]) ? previewDataByKelas[activeKelasId] : [];
        const total = list.length;

        $('#previewDimText').text(s.labelWidth.toFixed(1) + ' × ' + s.labelHeight.toFixed(1) + ' cm');

        if (total === 0) {
            $('#previewCounter').text('0 / 0');
            $('#livePreviewCard').html('<div class="text-muted text-xs py-4">Tidak ada data santri di kelas ini.</div>');
            return;
        }

        if (previewIndex >= total) previewIndex = 0;
        if (previewIndex < 0) previewIndex = total - 1;
        $('#previewCounter').text((previewIndex + 1) + ' / ' + total);

        const item = list[previewIndex];
        // Skala visual preview agar muat di header (base 1cm = 34px, diperkecil otomatis jika lebar > 10.5cm)
        const maxPxW = 350;
        const scale = Math.min(34, maxPxW / Math.max(5, s.labelWidth));
        const boxW = Math.round(s.labelWidth * scale);
        const boxH = Math.round(s.labelHeight * scale);
        const fotoH = Math.round(Math.min(s.labelHeight - 0.4, Math.max(1.6, s.fotoHeight)) * scale * (s.variant === 2 ? 0.92 : 1));
        const fotoW = Math.round(Math.min(s.labelWidth * 0.45, Math.max(1.2, s.fotoWidth)) * scale * (s.variant === 2 ? 0.92 : 1));

        const fotoInner = item.photoUrl
            ? '<img src="' + escapeHtml(item.photoUrl) + '" style="width:100%;height:100%;object-fit:cover;display:block;" alt="Foto">'
            : '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;text-align:center;font-size:10px;font-weight:700;color:#64748b;line-height:1.3;background:#f8fafc;">Foto<br>3 x 4</div>';

        const idSafe = escapeHtml(item.idSantri);
        const namaSafe = escapeHtml(item.namaSantri);
        const alamatSafe = escapeHtml(item.alamat);

        let html = '';
        const baseStyle = 'width:' + boxW + 'px;height:' + boxH + 'px;background:#fff;box-sizing:border-box;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);font-family:Arial,sans-serif;';

        if (s.variant === 1) {
            html = '<div style="' + baseStyle + 'border:1px dashed #9ca3af;border-left:5px solid #047857;display:flex;align-items:center;padding:10px;gap:12px;">' +
                '<div style="width:' + fotoW + 'px;height:' + fotoH + 'px;border:1px solid #9ca3af;flex-shrink:0;overflow:hidden;">' + fotoInner + '</div>' +
                '<div style="flex:1;min-width:0;">' +
                '<div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.7px;color:#065f46;">ID Santri : ' + idSafe + '</div>' +
                '<div style="font-size:12.5px;font-weight:800;text-transform:uppercase;color:#111827;line-height:1.25;padding-bottom:5px;border-bottom:1px solid #d1d5db;margin-top:2px;">' + namaSafe + '</div>' +
                '<div style="font-size:8px;font-weight:700;text-transform:uppercase;color:#9ca3af;margin-top:6px;">Alamat</div>' +
                '<div style="font-size:9.5px;color:#374151;line-height:1.35;">' + alamatSafe + '</div>' +
                '</div></div>';
        } else if (s.variant === 2) {
            html = '<div style="' + baseStyle + 'border:1px dashed #9ca3af;padding:5px;">' +
                '<div style="width:100%;height:100%;border:2px solid #1f2937;padding:2px;box-sizing:border-box;">' +
                '<div style="width:100%;height:100%;border:1px solid #6b7280;display:flex;align-items:center;padding:6px 8px;gap:10px;box-sizing:border-box;">' +
                '<div style="width:' + fotoW + 'px;height:' + fotoH + 'px;border:1px solid #6b7280;flex-shrink:0;overflow:hidden;">' + fotoInner + '</div>' +
                '<div style="flex:1;min-width:0;">' +
                '<div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.7px;color:#6b7280;border-bottom:1px solid #1f2937;padding-bottom:2px;margin-bottom:5px;">Identitas Pemilik Raport</div>' +
                '<div style="font-size:11.5px;font-weight:800;text-transform:uppercase;color:#111827;line-height:1.2;margin-bottom:5px;">' + namaSafe + '</div>' +
                '<div style="font-size:9px;color:#1f2937;line-height:1.35;"><strong>ID Santri :</strong> ' + idSafe + '<br><strong>Alamat :</strong> ' + alamatSafe + '</div>' +
                '</div></div></div></div>';
        } else if (s.variant === 3) {
            html = '<div style="' + baseStyle + 'border:1px dashed #9ca3af;display:flex;">' +
                '<div style="width:' + (fotoW + 20) + 'px;background:#f1f5f9;border-right:1px solid #cbd5e1;display:flex;align-items:center;justify-content:center;flex-shrink:0;">' +
                '<div style="width:' + fotoW + 'px;height:' + fotoH + 'px;border:1px solid #94a3b8;overflow:hidden;background:#fff;">' + fotoInner + '</div></div>' +
                '<div style="flex:1;min-width:0;padding:10px 12px;display:flex;flex-direction:column;justify-content:center;">' +
                '<div><span style="display:inline-block;background:#1e293b;color:#fff;font-size:8.5px;font-weight:700;padding:2px 6px;border-radius:3px;">ID: ' + idSafe + '</span></div>' +
                '<div style="font-size:12px;font-weight:800;text-transform:uppercase;color:#0f172a;line-height:1.25;margin-top:5px;">' + namaSafe + '</div>' +
                '<div style="font-size:9.5px;color:#475569;line-height:1.35;margin-top:6px;padding-top:5px;border-top:1px solid #e2e8f0;"><strong>Alamat:</strong> ' + alamatSafe + '</div>' +
                '</div></div>';
        } else if (s.variant === 4) {
            html = '<div style="' + baseStyle + 'border:1px dashed #9ca3af;display:flex;align-items:center;padding:10px;gap:12px;">' +
                '<div style="width:' + fotoW + 'px;height:' + fotoH + 'px;border:1.5px solid #1e3a5f;flex-shrink:0;overflow:hidden;">' + fotoInner + '</div>' +
                '<div style="flex:1;min-width:0;">' +
                '<div style="background:#1e3a5f;border-bottom:2px solid #d97706;color:#fff;padding:3px 7px;display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">' +
                '<span style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;">Identitas Santri</span>' +
                '<span style="font-size:8.5px;font-weight:700;color:#fcd34d;">' + idSafe + '</span></div>' +
                '<div style="font-size:12px;font-weight:800;text-transform:uppercase;color:#1e3a5f;line-height:1.25;margin-bottom:6px;">' + namaSafe + '</div>' +
                '<div style="font-size:9.5px;color:#374151;line-height:1.35;padding-top:5px;border-top:1px solid #e5e7eb;"><strong>Alamat:</strong> ' + alamatSafe + '</div>' +
                '</div></div>';
        } else {
            html = '<div style="' + baseStyle + 'border:1px dashed #9ca3af;display:flex;align-items:center;padding:10px;gap:12px;">' +
                '<div style="width:' + fotoW + 'px;height:' + fotoH + 'px;border:1px solid #111827;flex-shrink:0;overflow:hidden;">' + fotoInner + '</div>' +
                '<div style="flex:1;min-width:0;">' +
                '<div style="display:flex;justify-content:space-between;align-items:center;border-bottom:1.5px solid #111827;padding-bottom:3px;margin-bottom:6px;">' +
                '<span style="font-size:8.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#6b7280;">ID Santri</span>' +
                '<span style="font-size:9.5px;font-weight:800;color:#111827;">' + idSafe + '</span></div>' +
                '<div style="font-size:12.5px;font-weight:800;text-transform:uppercase;color:#030712;line-height:1.25;margin-bottom:6px;">' + namaSafe + '</div>' +
                '<div style="font-size:9.5px;color:#374151;line-height:1.35;"><strong>Alamat :</strong> ' + alamatSafe + '</div>' +
                '</div></div>';
        }

        $('#livePreviewCard').html(html);
    }

    function updateSettingsUI() {
        const s = getPrintSettings();
        if (s.paper === 'custom') {
            $('.custom-paper-group').show();
        } else {
            $('.custom-paper-group').hide();
        }

        const baseDims = s.paper === 'custom' ? [s.paperWidth, s.paperHeight] : (paperDimensionsCm[s.paper] || paperDimensionsCm.a4);
        const effW = s.orientation === 'landscape' ? baseDims[1] : baseDims[0];
        const effH = s.orientation === 'landscape' ? baseDims[0] : baseDims[1];

        const printW = Math.max(1, effW - 0.8);
        const printH = Math.max(1, effH - 1.0);
        const cols = Math.max(1, Math.floor((printW + 0.2) / (s.labelWidth + 0.2)));
        const rows = Math.max(1, Math.floor((printH + 0.25) / (s.labelHeight + 0.25)));

        $('#layoutEstimateInfo').html(
            'Estimasi: <strong>' + cols + ' kolom &times; ' + rows + ' baris = ' + (cols * rows) + ' label</strong> / lembar'
        );
        $('#fotoRecommendInfo').html(
            'Rekomendasi Foto (3:4): <strong>' + s.recFoto.w.toFixed(1) + ' &times; ' + s.recFoto.h.toFixed(1) + ' cm</strong>'
        );

        localStorage.setItem(npSettingKey, JSON.stringify(s));
        renderLivePreview();
    }

    function loadSavedSettings() {
        try {
            const raw = localStorage.getItem(npSettingKey);
            if (!raw) return;
            const s = JSON.parse(raw);
            if (s.variant) $('#variantSelect').val(String(s.variant));
            if (s.paper) $('#paperSizeSelect').val(s.paper);
            if (s.orientation) $('#paperOrientationSelect').val(s.orientation);
            if (s.paperWidth) $('#paperWidthInput').val(s.paperWidth);
            if (s.paperHeight) $('#paperHeightInput').val(s.paperHeight);
            if (s.labelWidth) $('#labelWidthInput').val(s.labelWidth);
            if (s.labelHeight) $('#labelHeightInput').val(s.labelHeight);
            if (s.fotoWidth) $('#fotoWidthInput').val(s.fotoWidth);
            if (s.fotoHeight) $('#fotoHeightInput').val(s.fotoHeight);
        } catch (e) {}
    }

    function buildSettingsQueryString() {
        const s = getPrintSettings();
        return 'variant=' + encodeURIComponent(s.variant) +
            '&paper=' + encodeURIComponent(s.paper) +
            '&orientation=' + encodeURIComponent(s.orientation) +
            '&paper_width=' + encodeURIComponent(s.paperWidth) +
            '&paper_height=' + encodeURIComponent(s.paperHeight) +
            '&label_width=' + encodeURIComponent(s.labelWidth) +
            '&label_height=' + encodeURIComponent(s.labelHeight) +
            '&foto_width=' + encodeURIComponent(s.fotoWidth) +
            '&foto_height=' + encodeURIComponent(s.fotoHeight);
    }

    loadSavedSettings();
    updateSettingsUI();

    $(document).on('change input', '.np-label-dim', function() {
        const lw = parseFloat($('#labelWidthInput').val()) || 10.0;
        const lh = parseFloat($('#labelHeightInput').val()) || 4.8;
        const rec = computeRecommendedPhoto(lw, lh);
        $('#fotoWidthInput').val(rec.w.toFixed(1));
        $('#fotoHeightInput').val(rec.h.toFixed(1));
    });

    $(document).on('change input', '.np-setting-input', function() {
        updateSettingsUI();
    });

    $('#btnPreviewPrev').on('click', function() {
        previewIndex--;
        renderLivePreview();
    });

    $('#btnPreviewNext').on('click', function() {
        previewIndex++;
        renderLivePreview();
    });

    // Dukungan geser (drag / swipe) pada area Live Preview
    (function() {
        const vp = document.getElementById('livePreviewViewport');
        if (!vp) return;
        let startX = null;
        vp.addEventListener('pointerdown', function(e) { startX = e.clientX; });
        vp.addEventListener('pointerup', function(e) {
            if (startX === null) return;
            const diff = e.clientX - startX;
            startX = null;
            if (Math.abs(diff) > 30) {
                previewIndex += (diff < 0 ? 1 : -1);
                renderLivePreview();
            }
        });
    })();

    $('#btnResetNamePlateSetting').on('click', function() {
        $('#variantSelect').val('1');
        $('#paperSizeSelect').val('a4');
        $('#paperOrientationSelect').val('portrait');
        $('#paperWidthInput').val('21.0');
        $('#paperHeightInput').val('29.7');
        $('#labelWidthInput').val('10.0');
        $('#labelHeightInput').val('4.8');
        $('#fotoWidthInput').val('3.0');
        $('#fotoHeightInput').val('4.0');
        updateSettingsUI();
    });

    $(document).on('change', '.check-all-np', function() {
        const kelasId = $(this).data('kelas');
        $('.check-item-np-' + kelasId).prop('checked', $(this).is(':checked'));
    });

    $(document).on('click', '.btn-print-single-np', function(e) {
        e.preventDefault();
        const idSantri = $(this).data('id');
        const url = '<?= base_url('backend/santri/generatePDFNamePlate') ?>/' + encodeURIComponent(idSantri) + '?' + buildSettingsQueryString();
        window.open(url, '_blank');
    });

    $(document).on('click', '.btn-print-all-nameplate', function() {
        const idTpq = $(this).data('tpq') || '';
        const idKelas = $(this).data('kelas') || '';
        const url = '<?= base_url('backend/santri/generatePDFAllNamePlate') ?>?filterIdTpq=' + encodeURIComponent(idTpq) + '&filterIdKelas=' + encodeURIComponent(idKelas) + '&' + buildSettingsQueryString();
        window.open(url, '_blank');
    });

    $(document).on('click', '.btn-print-selected-nameplate', function() {
        const kelasId = $(this).data('kelas');
        const ids = [];
        $('.check-item-np-' + kelasId + ':checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum ada santri dipilih',
                text: 'Centang minimal satu santri terlebih dahulu untuk dicetak.'
            });
            return;
        }

        const s = getPrintSettings();
        $('#selectedSantriIds').val(ids.join(','));
        $('#selectedPaperSize').val(s.paper);
        $('#selectedOrientation').val(s.orientation);
        $('#selectedPaperWidth').val(s.paperWidth);
        $('#selectedPaperHeight').val(s.paperHeight);
        $('#selectedLabelWidth').val(s.labelWidth);
        $('#selectedLabelHeight').val(s.labelHeight);
        $('#selectedFotoWidth').val(s.fotoWidth);
        $('#selectedFotoHeight').val(s.fotoHeight);
        $('#selectedVariant').val(s.variant);
        $('#formSelectedNamePlate').submit();
    });
</script>
<?= $this->endSection(); ?>
