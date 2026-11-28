<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<?php
$isPortrait = ($template['Orientasi'] ?? 'landscape') === 'portrait';
$canvasW    = $isPortrait ? 794 : 1123;
$canvasH    = $isPortrait ? 1123 : 794;
$sampleData = [
    'LOGO_LEMBAGA'      => '[LOGO]',
    'NAMA_LEMBAGA'      => 'TPQ SMART AL-FALAH',
    'JUDUL_SERTIFIKAT'  => 'SERTIFIKAT PENGHARGAAN',
    'NOMOR_SERTIFIKAT'  => 'No: TPQ/SNRT/2026/001',
    'NAMA_SANTRI'       => 'Ahmad Fauzi Ramadhan',
    'KELAS'             => 'Kelas 3A',
    'JENIS_PENGHARGAAN' => 'Juara Umum Kelas',
    'PERINGKAT'         => 'Peringkat 1',
    'PERIODE'           => 'Semester Ganjil · TA 2025/2026',
    'NILAI_RATA_RATA'   => 'Nilai Rata-rata: 94.50',
    'PERSEN_KEHADIRAN'  => 'Kehadiran: 100%',
    'TANGGAL_CETAK'     => 'Diberikan pada: ' . date('d F Y'),
    'TTD_1_JABATAN'     => 'Wali Kelas',
    'TTD_1_QR'          => '[QR #1]',
    'TTD_1_NAMA'        => 'Ustadz Ridwan',
    'TTD_2_JABATAN'     => 'Kepala TPQ',
    'TTD_2_QR'          => '[QR #2]',
    'TTD_2_NAMA'        => 'H. Abdullah, S.Pd.I',
    'TTD_3_JABATAN'     => 'Ketua Yayasan',
    'TTD_3_QR'          => '[QR #3]',
    'TTD_3_NAMA'        => 'KH. Ahmad Yani',
];
?>

<div class="container-fluid">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle mr-1"></i> <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('backend/penghargaan/template/save-fields/' . $template['Id']) ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white d-flex align-items-center justify-content-between flex-wrap">
                <h3 class="card-title font-weight-bold"><i class="fas fa-sliders-h text-primary mr-2"></i>Editor Desain: <?= esc($template['NamaTemplate']) ?></h3>
                <div class="ml-auto">
                    <a href="<?= base_url('backend/penghargaan/template/preview/' . $template['Id']) ?>" class="btn btn-info btn-sm mr-1" target="_blank">
                        <i class="fas fa-eye mr-1"></i> Preview Penuh
                    </a>
                    <a href="<?= base_url('backend/penghargaan/template') ?>" class="btn btn-secondary btn-sm mr-1">Kembali</a>
                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save mr-1"></i> Simpan Template</button>
                </div>
            </div>
            <div class="card-body py-2">
                <div class="row text-sm">
                    <div class="col-md-3 form-group mb-1">
                        <label class="mb-0">Nama Template</label>
                        <input type="text" name="NamaTemplate" class="form-control form-control-sm" value="<?= esc($template['NamaTemplate']) ?>">
                    </div>
                    <div class="col-md-3 form-group mb-1">
                        <label class="mb-0">Untuk Jenis Penghargaan</label>
                        <select name="IdJenisPenghargaan" class="form-control form-control-sm">
                            <option value="">Semua Jenis (Umum)</option>
                            <?php foreach ($listJenis as $j): ?>
                                <option value="<?= $j['Id'] ?>" <?= (string) ($template['IdJenisPenghargaan'] ?? '') === (string) $j['Id'] ? 'selected' : '' ?>>
                                    [<?= esc($j['Kode']) ?>] <?= esc($j['NamaPenghargaan']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-1">
                        <label class="mb-0">Kertas / Orientasi</label>
                        <div class="input-group input-group-sm">
                            <select name="UkuranKertas" class="form-control">
                                <option value="A4" <?= $template['UkuranKertas'] === 'A4' ? 'selected' : '' ?>>A4</option>
                                <option value="A5" <?= $template['UkuranKertas'] === 'A5' ? 'selected' : '' ?>>A5</option>
                            </select>
                            <select name="Orientasi" class="form-control">
                                <option value="landscape" <?= $template['Orientasi'] === 'landscape' ? 'selected' : '' ?>>Landscape</option>
                                <option value="portrait" <?= $template['Orientasi'] === 'portrait' ? 'selected' : '' ?>>Portrait</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1 form-group mb-1">
                        <label class="mb-0">Status</label>
                        <select name="Status" class="form-control form-control-sm">
                            <option value="draft" <?= $template['Status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="aktif" <?= $template['Status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group mb-1">
                        <label class="mb-0">Ganti Background (Opsional)</label>
                        <input type="file" name="BackgroundFile" class="form-control-file form-control-sm" accept="image/png,image/jpeg,image/svg+xml,image/webp">
                        <?php if (!empty($isAdmin)): ?>
                            <div class="custom-control custom-checkbox mt-1">
                                <input type="checkbox" class="custom-control-input" id="editAllowDuplicate" name="AllowDuplicate" value="1" <?= !empty($template['AllowDuplicate']) ? 'checked' : '' ?>>
                                <label class="custom-control-label text-xs" for="editAllowDuplicate">Izinkan Operator TPQ duplikasi template ini</label>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Kanvas Drag & Drop -->
            <div class="col-lg-8 mb-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-light py-2 text-sm">
                        <strong><i class="fas fa-mouse-pointer mr-1"></i> Kanvas Visual</strong>
                        <span class="text-muted ml-2">Geser (drag) elemen di atas background untuk mengatur posisi.</span>
                    </div>
                    <div class="card-body bg-dark p-2 text-center" style="overflow: auto;">
                        <div id="certCanvasWrap" style="display: inline-block; transform-origin: top left;">
                            <div id="certCanvas" style="position: relative; width: <?= $canvasW ?>px; height: <?= $canvasH ?>px; background: #fff url('<?= base_url($template['BackgroundFile']) ?>') center/cover no-repeat; box-shadow: 0 4px 16px rgba(0,0,0,0.4); user-select: none;">
                                <?php foreach ($fields as $f): ?>
                                    <div class="draggable-field <?= empty($f['IsAktif']) ? 'd-none' : '' ?>"
                                        id="canvasField_<?= $f['Id'] ?>"
                                        data-id="<?= $f['Id'] ?>"
                                        style="
                                            position: absolute;
                                            left: <?= (int) $f['PosisiX'] ?>px;
                                            top: <?= (int) $f['PosisiY'] ?>px;
                                            width: <?= (int) $f['Lebar'] ?>px;
                                            height: <?= (int) $f['Tinggi'] ?>px;
                                            font-size: <?= (int) $f['FontSize'] ?>px;
                                            font-family: <?= esc($f['FontFamily']) ?>, sans-serif;
                                            font-weight: <?= !empty($f['FontBold']) ? 'bold' : 'normal' ?>;
                                            color: <?= esc($f['FontColor']) ?>;
                                            text-align: <?= esc($f['TextAlign']) ?>;
                                            border: 1px dashed rgba(59,130,246,0.65);
                                            background: rgba(255,255,255,0.22);
                                            cursor: move;
                                            display: flex;
                                            align-items: center;
                                            justify-content: <?= $f['TextAlign'] === 'left' ? 'flex-start' : ($f['TextAlign'] === 'right' ? 'flex-end' : 'center') ?>;
                                            padding: 2px 4px;
                                        ">
                                        <?= esc($sampleData[$f['NamaField']] ?? $f['NamaField']) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Pengaturan Field -->
            <div class="col-lg-4 mb-3">
                <div class="card shadow-sm" style="max-height: 760px; overflow-y: auto;">
                    <div class="card-header bg-light py-2 text-sm sticky-top">
                        <strong><i class="fas fa-list-ul mr-1"></i> Daftar Field Overlay</strong>
                    </div>
                    <div class="card-body p-2">
                        <?php foreach ($fields as $f): ?>
                            <div class="border rounded p-2 mb-2 field-panel" id="panelField_<?= $f['Id'] ?>" data-id="<?= $f['Id'] ?>">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input field-active-cb" id="aktif_<?= $f['Id'] ?>" name="fields[<?= $f['Id'] ?>][IsAktif]" value="1" data-id="<?= $f['Id'] ?>" <?= !empty($f['IsAktif']) ? 'checked' : '' ?>>
                                        <label class="custom-control-label font-weight-bold text-xs" for="aktif_<?= $f['Id'] ?>"><?= esc($f['NamaField']) ?></label>
                                    </div>
                                    <button type="button" class="btn btn-xs btn-outline-secondary" data-toggle="collapse" data-target="#detail_<?= $f['Id'] ?>">
                                        <i class="fas fa-sliders-h"></i>
                                    </button>
                                </div>
                                <div class="collapse <?= !empty($f['IsAktif']) ? '' : '' ?>" id="detail_<?= $f['Id'] ?>">
                                    <div class="row text-xs mt-1">
                                        <div class="col-3">
                                            <label class="mb-0">X</label>
                                            <input type="number" class="form-control form-control-sm prop-input" data-prop="left" data-id="<?= $f['Id'] ?>" id="inputX_<?= $f['Id'] ?>" name="fields[<?= $f['Id'] ?>][PosisiX]" value="<?= (int) $f['PosisiX'] ?>">
                                        </div>
                                        <div class="col-3">
                                            <label class="mb-0">Y</label>
                                            <input type="number" class="form-control form-control-sm prop-input" data-prop="top" data-id="<?= $f['Id'] ?>" id="inputY_<?= $f['Id'] ?>" name="fields[<?= $f['Id'] ?>][PosisiY]" value="<?= (int) $f['PosisiY'] ?>">
                                        </div>
                                        <div class="col-3">
                                            <label class="mb-0">Lebar</label>
                                            <input type="number" class="form-control form-control-sm prop-input" data-prop="width" data-id="<?= $f['Id'] ?>" name="fields[<?= $f['Id'] ?>][Lebar]" value="<?= (int) $f['Lebar'] ?>">
                                        </div>
                                        <div class="col-3">
                                            <label class="mb-0">Tinggi</label>
                                            <input type="number" class="form-control form-control-sm prop-input" data-prop="height" data-id="<?= $f['Id'] ?>" name="fields[<?= $f['Id'] ?>][Tinggi]" value="<?= (int) $f['Tinggi'] ?>">
                                        </div>
                                        <div class="col-4 mt-1">
                                            <label class="mb-0">Size</label>
                                            <input type="number" class="form-control form-control-sm prop-input" data-prop="fontSize" data-id="<?= $f['Id'] ?>" name="fields[<?= $f['Id'] ?>][FontSize]" value="<?= (int) $f['FontSize'] ?>">
                                        </div>
                                        <div class="col-4 mt-1">
                                            <label class="mb-0">Warna</label>
                                            <input type="color" class="form-control form-control-sm prop-input" data-prop="color" data-id="<?= $f['Id'] ?>" name="fields[<?= $f['Id'] ?>][FontColor]" value="<?= esc($f['FontColor']) ?>">
                                        </div>
                                        <div class="col-4 mt-1">
                                            <label class="mb-0">Align</label>
                                            <select class="form-control form-control-sm prop-input" data-prop="textAlign" data-id="<?= $f['Id'] ?>" name="fields[<?= $f['Id'] ?>][TextAlign]">
                                                <option value="left" <?= $f['TextAlign'] === 'left' ? 'selected' : '' ?>>Kiri</option>
                                                <option value="center" <?= $f['TextAlign'] === 'center' ? 'selected' : '' ?>>Tengah</option>
                                                <option value="right" <?= $f['TextAlign'] === 'right' ? 'selected' : '' ?>>Kanan</option>
                                            </select>
                                        </div>
                                        <div class="col-7 mt-1">
                                            <label class="mb-0">Font</label>
                                            <select class="form-control form-control-sm prop-input" data-prop="fontFamily" data-id="<?= $f['Id'] ?>" name="fields[<?= $f['Id'] ?>][FontFamily]">
                                                <?php foreach (['Arial', 'Georgia', 'Times New Roman', 'Verdana', 'Trebuchet MS', 'Courier New'] as $fn): ?>
                                                    <option value="<?= $fn ?>" <?= $f['FontFamily'] === $fn ? 'selected' : '' ?>><?= $fn ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-5 mt-1 d-flex align-items-end">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input prop-bold" id="bold_<?= $f['Id'] ?>" data-id="<?= $f['Id'] ?>" name="fields[<?= $f['Id'] ?>][FontBold]" value="1" <?= !empty($f['FontBold']) ? 'checked' : '' ?>>
                                                <label class="custom-control-label" for="bold_<?= $f['Id'] ?>">Bold</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('certCanvas');
    const wrap = document.getElementById('certCanvasWrap');
    const baseW = <?= $canvasW ?>;

    function fitCanvas() {
        const parentW = wrap.parentElement.clientWidth - 20;
        const scale = Math.min(1, parentW / baseW);
        wrap.style.transform = 'scale(' + scale + ')';
        wrap.style.width = (baseW * scale) + 'px';
        wrap.style.height = (<?= $canvasH ?> * scale) + 'px';
    }
    fitCanvas();
    window.addEventListener('resize', fitCanvas);

    // Drag & Drop pada kanvas
    let activeEl = null, startX = 0, startY = 0, origL = 0, origT = 0;

    document.querySelectorAll('.draggable-field').forEach(function (el) {
        el.addEventListener('mousedown', function (e) {
            activeEl = el;
            const scale = parseFloat((wrap.style.transform.match(/scale\(([^)]+)\)/) || [1, 1])[1]);
            startX = e.clientX / scale;
            startY = e.clientY / scale;
            origL = parseInt(el.style.left, 10) || 0;
            origT = parseInt(el.style.top, 10) || 0;
            e.preventDefault();
        });
    });

    document.addEventListener('mousemove', function (e) {
        if (!activeEl) return;
        const scale = parseFloat((wrap.style.transform.match(/scale\(([^)]+)\)/) || [1, 1])[1]);
        const dx = (e.clientX / scale) - startX;
        const dy = (e.clientY / scale) - startY;
        const newL = Math.max(0, Math.round(origL + dx));
        const newT = Math.max(0, Math.round(origT + dy));
        activeEl.style.left = newL + 'px';
        activeEl.style.top = newT + 'px';
        const id = activeEl.dataset.id;
        document.getElementById('inputX_' + id).value = newL;
        document.getElementById('inputY_' + id).value = newT;
    });

    document.addEventListener('mouseup', function () {
        activeEl = null;
    });

    // Toggle aktif field
    document.querySelectorAll('.field-active-cb').forEach(function (cb) {
        cb.addEventListener('change', function () {
            const el = document.getElementById('canvasField_' + this.dataset.id);
            el.classList.toggle('d-none', !this.checked);
        });
    });

    // Live update properti
    document.querySelectorAll('.prop-input').forEach(function (inp) {
        inp.addEventListener('input', function () {
            const el = document.getElementById('canvasField_' + this.dataset.id);
            const prop = this.dataset.prop;
            if (['left', 'top', 'width', 'height', 'fontSize'].includes(prop)) {
                el.style[prop] = this.value + 'px';
            } else {
                el.style[prop] = this.value;
            }
        });
    });

    document.querySelectorAll('.prop-bold').forEach(function (cb) {
        cb.addEventListener('change', function () {
            const el = document.getElementById('canvasField_' + this.dataset.id);
            el.style.fontWeight = this.checked ? 'bold' : 'normal';
        });
    });
});
</script>

<?= $this->endSection(); ?>
