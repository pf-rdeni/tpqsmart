<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <style>
        .wizard-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 25px;
            background: #fff;
            padding: 15px 10px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
        }
        .wizard-step {
            flex: 1;
            text-align: center;
            position: relative;
            cursor: pointer;
        }
        .wizard-step .step-dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 5px;
            transition: all 0.2s ease;
        }
        .wizard-step.active .step-dot {
            background: #007bff;
            color: #fff;
            box-shadow: 0 0 0 4px rgba(0,123,255,0.2);
        }
        .wizard-step.completed .step-dot {
            background: #28a745;
            color: #fff;
        }
        .wizard-step .step-title {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: #495057;
            text-transform: uppercase;
        }
        .wizard-step.active .step-title {
            color: #007bff;
        }
        .wizard-step.completed .step-title {
            color: #28a745;
        }
        .step-panel {
            display: none;
        }
        .step-panel.active {
            display: block;
        }
        .recipient-item-selected {
            background: #e8f4fd;
            border-color: #b8daff !important;
        }
        .bg-light-primary {
            background-color: #f0f7ff !important;
        }
        .template-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        .template-card:hover {
            border-color: #007bff !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.12) !important;
        }
        .template-card.active-selected {
            border-color: #007bff !important;
            background-color: #f0f7ff !important;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25) !important;
        }
        /* ===== Step 6: Tanda Tangan Dinamis & Kaidah Naskah Dinas ===== */
        .preset-card {
            border-radius: 10px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            background: #ffffff;
            position: relative;
            border: 1.5px solid #e2e8f0;
            overflow: hidden;
        }
        .preset-card:hover {
            border-color: #3b82f6 !important;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.15) !important;
        }
        .preset-card.selected {
            border-color: #2563eb !important;
            border-width: 2px !important;
            background: linear-gradient(180deg, #f0f7ff 0%, #e6f0fa 100%) !important;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.18) !important;
        }
        .preset-card .selected-check {
            display: none;
            position: absolute;
            top: 6px;
            right: 6px;
            background: #2563eb;
            color: #fff;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 11px;
            line-height: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.25);
            z-index: 2;
        }
        .preset-card.selected .selected-check {
            display: block;
        }
        .preset-badge {
            display: inline-block;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 10px;
            margin-bottom: 4px;
        }
        .preset-mini-diagram {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 42px;
            margin: 6px 0 8px;
            gap: 4px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 4px;
        }
        .preset-card.selected .preset-mini-diagram {
            background: rgba(255, 255, 255, 0.75);
            border-color: #93c5fd;
        }
        .preset-mini-diagram .mini-row {
            display: flex;
            justify-content: center;
            gap: 5px;
            width: 100%;
        }
        .preset-mini-diagram .mini-box {
            display: inline-block;
            height: 11px;
            border-radius: 3px;
            border: 1px solid #94a3b8;
            background: #cbd5e1;
            transition: all 0.2s;
        }
        .preset-mini-diagram .mini-box.box-ketua {
            background: #2563eb;
            border-color: #1d4ed8;
        }
        .preset-mini-diagram .mini-box.box-sekretaris {
            background: #10b981;
            border-color: #059669;
        }
        .preset-mini-diagram .mini-box.box-bendahara {
            background: #f59e0b;
            border-color: #d97706;
        }
        .preset-mini-diagram .mini-box.box-mengetahui {
            background: #06b6d4;
            border-color: #0891b2;
        }
        .preset-mini-diagram .mini-box.box-fkpq {
            background: #7c3aed;
            border-color: #6d28d9;
        }
        .signer-card {
            border-radius: 10px;
            transition: all 0.25s ease-in-out;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }
        .signer-card:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08) !important;
            border-color: #cbd5e1;
        }
        .signer-card.border-accent-kanan { border-top: 3.5px solid #2563eb; }
        .signer-card.border-accent-kiri  { border-top: 3.5px solid #10b981; }
        .signer-card.border-accent-tengah { border-top: 3.5px solid #f59e0b; }
        .signer-card.border-accent-mengetahui { border-top: 3.5px solid #06b6d4; }

        .signer-card-header {
            background: #f8fafc;
            padding: 9px 14px;
        }
        .btn-xs { padding: 0.2rem 0.55rem; font-size: 0.75rem; line-height: 1.35; border-radius: 4px; }
        .btn-sumber {
            font-size: 0.74rem;
            padding: 0.3rem 0.55rem;
            font-weight: 600;
            border-radius: 4px;
            transition: all 0.15s ease-in-out;
        }
        .btn-sumber.active[data-sumber="guru"] { background: #2563eb !important; color: #fff !important; border-color: #2563eb !important; }
        .btn-sumber.active[data-sumber="master_tujuan"] { background: #7c3aed !important; color: #fff !important; border-color: #7c3aed !important; }
        .btn-sumber.active[data-sumber="fkpq"] { background: #0d9488 !important; color: #fff !important; border-color: #0d9488 !important; }
        .btn-sumber.active[data-sumber="manual"] { background: #475569 !important; color: #fff !important; border-color: #475569 !important; }

        /* Styling Select2 Pejabat di Step 6 */
        .signer-select2-wrap .select2-container {
            width: 100% !important;
        }
        .signer-select2-wrap .select2-container--bootstrap4 .select2-selection--single {
            height: calc(2.1rem + 2px) !important;
            padding: 0.35rem 0.65rem !important;
            font-size: 0.85rem !important;
            border: 1px solid #ced4da;
            border-radius: 5px;
            background-color: #fff;
            transition: all 0.2s ease-in-out;
        }
        .signer-select2-wrap .select2-container--bootstrap4 .select2-selection--single:hover {
            border-color: #3b82f6 !important;
        }
        .signer-select2-wrap .select2-container--bootstrap4.select2-container--focus .select2-selection--single,
        .signer-select2-wrap .select2-container--bootstrap4.select2-container--open .select2-selection--single {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.2) !important;
        }
        .signer-select2-wrap .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            line-height: 1.4 !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            padding-left: 0 !important;
        }
        .signer-select2-wrap .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
            color: #64748b !important;
            font-weight: 400 !important;
        }
        .signer-select2-wrap .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
            height: calc(2.1rem + 2px) !important;
            right: 0.5rem !important;
        }
        /* Sembunyikan tombol [x] clear agar dropdown bersih dan rapi */
        .signer-select2-wrap .select2-selection__clear {
            display: none !important;
        }

        /* Mode TTD Pill Cards */
        .mode-ttd-card {
            border: 1.5px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            cursor: pointer;
            transition: all 0.2s;
            background: #f8fafc;
        }
        .mode-ttd-card:hover {
            border-color: #3b82f6;
            background: #f0f7ff;
        }
        .mode-ttd-card.active {
            border-color: #2563eb;
            background: #eff6ff;
            box-shadow: 0 0 0 1px #2563eb;
        }

        /* Wizard Footer Actions */
        .wizard-footer {
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        .wizard-footer .btn {
            font-size: 0.88rem;
            letter-spacing: 0.01em;
            transition: all 0.2s ease-in-out;
        }
        .wizard-footer .btn:hover {
            transform: translateY(-1px);
        }
        .wizard-footer .btn-prev {
            border-color: #cbd5e1;
            color: #475569;
            background-color: #ffffff;
        }
        .wizard-footer .btn-prev:hover {
            background-color: #f8fafc;
            color: #0f172a;
            border-color: #94a3b8;
        }

        /* Sticky Real-Time Document Preview Box */
        .preview-sticky-wrap {
            position: sticky;
            top: 15px;
            z-index: 5;
        }
        #ttdPreviewBox {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            padding: 20px 16px;
            min-height: 240px;
            font-family: inherit;
            font-size: 0.85rem;
            line-height: 1.4;
            color: #0f172a;
            position: relative;
            transition: background 0.3s;
        }
        #ttdPreviewBox.preview-flash {
            animation: highlightSwap 0.75s ease-out;
        }
        @keyframes highlightSwap {
            0% { background-color: #dbeafe; }
            100% { background-color: #ffffff; }
        }
        .preview-header-ribbon {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: inherit;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #64748b;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 7px;
            margin-bottom: 12px;
        }
        .preview-row {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            gap: 8px;
        }
        .preview-col {
            text-align: center;
            padding: 4px 6px;
            flex: 1;
            min-width: 0;
            font-family: inherit;
        }
        .preview-col .preview-space-manual {
            height: 42px;
            border-bottom: 1px dashed #64748b;
            margin: 6px auto;
            width: 78%;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            font-size: 0.65rem;
            color: #94a3b8;
            font-style: normal;
            font-family: inherit;
            padding-bottom: 2px;
        }
        .preview-col .preview-space-qr {
            height: 44px;
            width: 44px;
            margin: 6px auto;
            border: 1.5px solid #2563eb;
            background: #eff6ff;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.15);
        }
        .preview-col .preview-space-qr i {
            font-size: 26px;
        }
        .preview-col .preview-nama {
            font-weight: bold;
            font-size: 0.85rem;
            margin-top: 5px;
            text-decoration: underline;
            color: #0f172a;
            font-family: inherit;
            text-transform: none;
        }
        .preview-col .preview-jabatan {
            font-weight: bold;
            font-size: 0.82rem;
            color: #0f172a;
            line-height: 1.3;
            text-transform: none;
            font-family: inherit;
        }
        .preview-col .preview-instansi {
            font-size: 0.78rem;
            color: #475569;
            margin-bottom: 2px;
            font-style: normal;
            font-family: inherit;
            text-transform: none;
        }
        .preview-col .preview-nip {
            font-size: 0.76rem;
            color: #475569;
            margin-top: 2px;
            font-family: inherit;
            text-transform: none;
        }
        .preview-mengetahui-label {
            text-align: center;
            font-size: 0.85rem;
            font-weight: bold;
            margin-top: 14pt;
            margin-bottom: 2pt;
            color: #1e293b;
            font-family: inherit;
            text-transform: none;
        }
        .preview-tanggal {
            text-align: right;
            font-size: 0.82rem;
            margin-bottom: 8px;
            color: #0f172a;
            font-family: inherit;
            text-transform: none;
        }
    </style>

    <!-- Wizard Step Indicator -->
    <div class="wizard-steps d-none d-md-flex">
        <div class="wizard-step active" data-step="1">
            <span class="step-dot">1</span>
            <span class="step-title">1. Dasar & Nomor</span>
        </div>
        <div class="wizard-step" data-step="2">
            <span class="step-dot">2</span>
            <span class="step-title">2. Perihal & Isi</span>
        </div>
        <div class="wizard-step" data-step="3">
            <span class="step-dot">3</span>
            <span class="step-title">3. Tanda Tangan</span>
        </div>
        <div class="wizard-step" data-step="4">
            <span class="step-dot">4</span>
            <span class="step-title">4. Penerima</span>
        </div>
        <div class="wizard-step" data-step="5">
            <span class="step-dot">5</span>
            <span class="step-title">5. Pratinjau & Final</span>
        </div>
    </div>

    <!-- Mobile Step Indicator Header -->
    <div class="card d-block d-md-none mb-3 bg-primary text-white">
        <div class="card-body py-2 text-center">
            <strong id="mobileStepText">Langkah 1 dari 5: Dasar Format & Penomoran Surat</strong>
        </div>
    </div>

    <?php if (!empty($isEdit)): ?>
        <div class="alert alert-warning shadow-sm mb-3">
            <i class="fas fa-edit mr-2"></i><strong>Mode Edit Surat:</strong> Anda sedang menyunting surat <strong><?= esc($surat['NomorSurat'] ?? '') ?></strong>. Perubahan akan langsung diperbarui saat disimpan.
        </div>
    <?php elseif (!empty($isDuplicate)): ?>
        <div class="alert alert-info shadow-sm mb-3">
            <i class="fas fa-copy mr-2"></i><strong>Mode Duplikasi Surat:</strong> Isi surat disalin dari surat sebelumnya dan akan diterbitkan sebagai surat baru.
        </div>
    <?php endif; ?>

    <form id="wizardForm">
        <?= csrf_field() ?>
        <?php if (!empty($isEdit) && !empty($surat['Id'])): ?>
            <input type="hidden" name="IdSurat" id="wizardIdSurat" value="<?= esc($surat['Id']) ?>">
        <?php endif; ?>
        <input type="hidden" name="IdTpq" id="wizardIdTpq" value="<?= esc($isAdmin ? ($surat['IdTpq'] ?? 0) : (int)$idTpq) ?>">
        <input type="hidden" name="Status" id="wizardStatus" value="Final">

        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- STEP 1: DASAR FORMAT & PENOMORAN SURAT -->
        <!-- ============================================================== -->
        <div class="step-panel active" id="step1">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-file-invoice mr-2"></i>Langkah 1: Dasar Format & Penomoran Surat</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Pilih template surat sebagai dasar format dokumen. Nomor surat akan otomatis terhitung dan pengaturan tata letak disesuaikan secara otomatis.</p>

                    <!-- Filter Kategori & Search Template -->
                    <div class="row mb-3 bg-light rounded p-2 border mx-0">
                        <div class="col-md-5 form-group mb-md-0">
                            <label class="font-weight-bold small text-muted"><i class="fas fa-filter mr-1 text-primary"></i> Filter Kategori Surat:</label>
                            <select id="filterKategoriTemplate" class="form-control form-control-sm">
                                <option value="">-- Semua Kategori Surat --</option>
                                <?php if (!empty($kategori)): ?>
                                    <?php foreach ($kategori as $kat): ?>
                                        <option value="<?= $kat['Id'] ?>"><?= esc($kat['NamaKategori']) ?> (<?= esc($kat['Kode']) ?>)</option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-7 form-group mb-0">
                            <label class="font-weight-bold small text-muted"><i class="fas fa-search mr-1 text-primary"></i> Cari Template (Nama / Kertas / Deskripsi):</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                </div>
                                <input type="text" id="inputSearchTemplate" class="form-control" placeholder="Ketik kata kunci pencarian template...">
                                <div class="input-group-append" id="btnClearSearchTemplate" style="display: none;">
                                    <button class="btn btn-outline-secondary" type="button" title="Bersihkan Pencarian">&times;</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Input IdTemplate untuk Form Submission -->
                    <input type="hidden" name="IdTemplate" id="selectTemplate" value="<?= esc($surat['IdTemplate'] ?? '') ?>" required>

                    <!-- Pilihan Visual Kartu Template -->
                    <div class="mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                            <label class="font-weight-bold text-dark mb-0"><i class="fas fa-th-large mr-1 text-primary"></i> Pilih Template Surat: <span class="text-danger">*</span></label>
                            <span id="templateFilterCount" class="small text-muted"></span>
                        </div>
                        <div class="row" id="templateCardGrid" style="max-height: 380px; overflow-y: auto;">
                            <?php foreach ($templates as $t): 
                                $isSelected = (isset($surat['IdTemplate']) && $surat['IdTemplate'] == $t['Id']);
                                $descStr = $t['Deskripsi'] ?? '';
                                if (strlen($descStr) > 75) {
                                    $descStr = substr($descStr, 0, 75) . '...';
                                }
                            ?>
                                <div class="col-md-6 col-lg-4 mb-3 template-card-col" 
                                     data-id="<?= $t['Id'] ?>" 
                                     data-kategori="<?= $t['IdKategori'] ?>" 
                                     data-nama="<?= esc(strtolower($t['NamaTemplate'])) ?>"
                                     data-katnama="<?= esc(strtolower($t['NamaKategori'])) ?>"
                                     data-kertas="<?= esc(strtolower($t['UkuranKertas'])) ?>"
                                     data-deskripsi="<?= esc(strtolower($t['Deskripsi'] ?? '')) ?>">
                                    <div class="card h-100 template-card <?= $isSelected ? 'active-selected' : '' ?>">
                                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                                            <div>
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <span class="badge badge-info px-2 py-1"><?= esc($t['NamaKategori']) ?></span>
                                                    <span class="badge badge-secondary"><?= esc($t['UkuranKertas']) ?></span>
                                                </div>
                                                <h6 class="font-weight-bold text-dark mb-1 template-card-title"><?= esc($t['NamaTemplate']) ?></h6>
                                                <p class="small text-muted mb-2" style="font-size: 85%;">
                                                    <?= !empty($descStr) ? esc($descStr) : 'Template dokumen surat resmi.' ?>
                                                </p>
                                            </div>
                                            <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                                                <small class="text-muted"><i class="fas fa-file-alt mr-1"></i> <?= esc($t['Orientasi']) ?></small>
                                                <span class="btn btn-xs <?= $isSelected ? 'btn-primary font-weight-bold' : 'btn-outline-primary' ?> btn-select-card">
                                                    <i class="fas <?= $isSelected ? 'fa-check' : 'fa-check-circle' ?> mr-1"></i> <?= $isSelected ? 'Terpilih' : 'Pilih' ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div id="templateNoMatchAlert" class="alert alert-warning text-center py-3 mt-2" style="display: none;">
                            <i class="fas fa-search-minus mr-1"></i> Tidak ada template yang cocok dengan filter atau kata kunci pencarian.
                        </div>
                    </div>

                    <div class="alert alert-info py-2 mt-2" id="templateInfoBox" style="display: none;">
                        <div class="font-weight-bold small"><i class="fas fa-info-circle mr-1"></i> Detail Template Terpilih:</div>
                        <div id="templateDesc" class="text-muted small"></div>
                    </div>

                    <!-- Panel Penomoran Surat Otomatis -->
                    <div class="card card-outline card-primary bg-light mt-3 mb-3 shadow-sm border">
                        <div class="card-header py-2">
                            <h6 class="card-title font-weight-bold text-primary mb-0"><i class="fas fa-barcode mr-1"></i> Penomoran Surat Otomatis</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-5 form-group mb-md-0">
                                    <label class="small font-weight-bold">Format Penomoran</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="FormatNomor" id="inputFormatNomor" class="form-control" value="<?= esc($templateSurat['FormatNomor'] ?? '[NO:3]/[KODE-TPQ]/[KODE-KAT]/[BLN-ROMAWI]/[TAHUN]') ?>">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" id="btnHitungNomor" title="Hitung Nomor Berdasarkan Format & Tanggal">
                                                <i class="fas fa-sync-alt mr-1"></i> Hitung
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7 form-group mb-0">
                                    <label class="small font-weight-bold">Nomor Surat Final <span class="text-danger">*</span></label>
                                    <input type="text" name="NomorSurat" id="inputNomorSurat" class="form-control form-control-sm font-weight-bold text-primary" value="<?= esc($surat['NomorSurat'] ?? '') ?>" placeholder="Nomor otomatis terisi saat template dipilih" required>
                                    <input type="hidden" name="UrutanNomor" id="inputUrutanNomor" value="<?= esc($surat['UrutanNomor'] ?? '1') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel Accordion / Collapsible: Penyesuaian Kop Surat & Layout Kertas -->
                    <div class="card card-outline card-secondary collapsed-card mt-2 mb-0 shadow-sm border" id="cardKopLayoutAccordion">
                        <div class="card-header py-2" data-card-widget="collapse" style="cursor: pointer;">
                            <h6 class="card-title font-weight-bold text-secondary mb-0">
                                <i class="fas fa-sliders-h mr-1"></i> Penyesuaian Kop Surat & Tata Letak Kertas (Opsional)
                            </h6>
                            <div class="card-tools">
                                <span class="badge badge-light border text-muted mr-2" id="badgeKopLayoutSummary">Default Template</span>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="card-body p-3" style="display: none;">
                            <div class="row">
                                <!-- Sumber Kop -->
                                <div class="col-md-6 form-group">
                                    <label class="small font-weight-bold">Sumber Kop Surat</label>
                                    <?php $currentSumberKop = $templateSurat['SumberKop'] ?? ($surat['SumberKop'] ?? 'TPQ'); ?>
                                    <select name="SumberKop" id="optSumberKop" class="form-control form-control-sm">
                                        <option value="TPQ" <?= ($currentSumberKop === 'TPQ') ? 'selected' : '' ?>>Kop Profil TPQ</option>
                                        <option value="MDA" <?= ($currentSumberKop === 'MDA') ? 'selected' : '' ?>>Kop Profil MDA / MDTA</option>
                                        <option value="FKPQ" <?= ($currentSumberKop === 'FKPQ') ? 'selected' : '' ?>>Kop FKPQ</option>
                                        <option value="Kepanitiaan" <?= ($currentSumberKop === 'Kepanitiaan') ? 'selected' : '' ?>>Kop Kepanitiaan Khusus</option>
                                        <option value="TanpaKop" <?= ($currentSumberKop === 'TanpaKop') ? 'selected' : '' ?>>Tanpa Kop (Kertas Kop Cetak / Polos)</option>
                                    </select>
                                </div>

                                <!-- Pilihan Kop Custom jika Kepanitiaan -->
                                <?php $currentIdKopCustom = $templateSurat['IdKopCustom'] ?? ($surat['IdKopCustom'] ?? ''); ?>
                                <div class="col-md-6 form-group" id="wrapperKopCustomWizard" style="<?= ($currentSumberKop === 'Kepanitiaan') ? '' : 'display: none;' ?>">
                                    <label class="small font-weight-bold">Pilih Gambar Kop Kepanitiaan</label>
                                    <select name="IdKopCustom" id="optIdKopCustom" class="form-control form-control-sm">
                                        <option value="">-- Pilih Kop Kepanitiaan --</option>
                                        <?php foreach ($kopCustom as $kc): ?>
                                            <option value="<?= $kc['Id'] ?>" <?= ($currentIdKopCustom == $kc['Id']) ? 'selected' : '' ?>><?= esc($kc['NamaKop']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="small font-weight-bold">Ukuran Kertas</label>
                                    <?php $currentKertas = $templateSurat['UkuranKertas'] ?? 'A4'; ?>
                                    <select name="UkuranKertas" id="optUkuranKertas" class="form-control form-control-sm">
                                        <option value="A4" <?= ($currentKertas === 'A4') ? 'selected' : '' ?>>A4 (210 x 297 mm)</option>
                                        <option value="F4" <?= ($currentKertas === 'F4') ? 'selected' : '' ?>>F4 / Folio (215 x 330 mm)</option>
                                        <option value="A5" <?= ($currentKertas === 'A5') ? 'selected' : '' ?>>A5 (148 x 210 mm)</option>
                                        <option value="Legal" <?= ($currentKertas === 'Legal') ? 'selected' : '' ?>>Legal</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="small font-weight-bold">Orientasi</label>
                                    <?php $currentOrientasi = $templateSurat['Orientasi'] ?? 'Portrait'; ?>
                                    <select name="Orientasi" id="optOrientasi" class="form-control form-control-sm">
                                        <option value="Portrait" <?= ($currentOrientasi === 'Portrait') ? 'selected' : '' ?>>Portrait (Tegak)</option>
                                        <option value="Landscape" <?= ($currentOrientasi === 'Landscape') ? 'selected' : '' ?>>Landscape (Mendatar)</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="small font-weight-bold">Jenis & Ukuran Font</label>
                                    <?php 
                                        $currentFontFamily = $templateSurat['FontFamily'] ?? 'Times New Roman'; 
                                        $currentFontSize   = $templateSurat['FontSize'] ?? '12';
                                    ?>
                                    <div class="input-group input-group-sm">
                                        <select name="FontFamily" id="optFontFamily" class="form-control">
                                            <option value="Times New Roman" <?= ($currentFontFamily === 'Times New Roman') ? 'selected' : '' ?>>Times New Roman</option>
                                            <option value="Arial" <?= ($currentFontFamily === 'Arial') ? 'selected' : '' ?>>Arial</option>
                                            <option value="Calibri" <?= ($currentFontFamily === 'Calibri') ? 'selected' : '' ?>>Calibri</option>
                                        </select>
                                        <select name="FontSize" id="optFontSize" class="form-control" style="max-width: 80px;">
                                            <option value="10" <?= ($currentFontSize == '10') ? 'selected' : '' ?>>10 pt</option>
                                            <option value="11" <?= ($currentFontSize == '11') ? 'selected' : '' ?>>11 pt</option>
                                            <option value="12" <?= ($currentFontSize == '12') ? 'selected' : '' ?>>12 pt</option>
                                            <option value="14" <?= ($currentFontSize == '14') ? 'selected' : '' ?>>14 pt</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <label class="d-block text-muted small text-uppercase font-weight-bold mb-2">Batas Tepi / Margin (mm):</label>
                            <div class="row">
                                <div class="col-3 form-group">
                                    <label class="small">Atas</label>
                                    <input type="number" name="MarginAtas" id="optMarginAtas" class="form-control form-control-sm" value="<?= esc($templateSurat['MarginAtas'] ?? 20) ?>">
                                </div>
                                <div class="col-3 form-group">
                                    <label class="small">Bawah</label>
                                    <input type="number" name="MarginBawah" id="optMarginBawah" class="form-control form-control-sm" value="<?= esc($templateSurat['MarginBawah'] ?? 20) ?>">
                                </div>
                                <div class="col-3 form-group">
                                    <label class="small">Kiri</label>
                                    <input type="number" name="MarginKiri" id="optMarginKiri" class="form-control form-control-sm" value="<?= esc($templateSurat['MarginKiri'] ?? 25) ?>">
                                </div>
                                <div class="col-3 form-group">
                                    <label class="small">Kanan</label>
                                    <input type="number" name="MarginKanan" id="optMarginKanan" class="form-control form-control-sm" value="<?= esc($templateSurat['MarginKanan'] ?? 15) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer wizard-footer d-flex justify-content-between align-items-center flex-wrap shadow-sm">
                    <div class="d-none d-md-flex align-items-center mb-1">
                        <span class="badge badge-light border px-3 py-2 text-secondary font-weight-normal shadow-sm" style="font-size: 0.8rem; border-radius: 6px;">
                            <i class="fas fa-file-alt text-primary mr-1"></i> Langkah 1 dari 5 &bull; Dasar Format & Penomoran
                        </span>
                    </div>
                    <div class="d-flex align-items-center flex-wrap mb-1 ml-auto" style="gap: 10px;">
                        <?php if (!empty($isEdit)): ?>
                            <button type="button" class="btn btn-outline-success px-4 py-2 btn-submit-wizard font-weight-bold shadow-sm" title="Simpan Perubahan Surat" style="border-radius: 6px;">
                                <i class="fas fa-save mr-1"></i> Simpan
                            </button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-primary px-4 py-2 btn-next font-weight-bold shadow-sm" data-next="2" style="border-radius: 6px; min-width: 180px;">
                            Lanjut ke Perihal & Isi <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- STEP 2: PERIHAL & KONTEN ISI SURAT -->
        <!-- ============================================================== -->
        <div class="step-panel" id="step2">
            <div class="card card-warning card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Langkah 2: Perihal, Tanggal, & Konten Isi Surat</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7 form-group">
                            <label>Perihal / Hal Surat <span class="text-danger">*</span></label>
                            <input type="text" name="Hal" id="inputHal" class="form-control font-weight-bold" value="<?= esc($surat['Hal'] ?? '') ?>" placeholder="Contoh: Undangan Rapat Evaluasi Semester" required>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" name="Tanggal" id="inputTanggal" class="form-control" value="<?= esc($surat['Tanggal'] ?? date('Y-m-d')) ?>" required>
                        </div>
                        <div class="col-md-2 form-group">
                            <label>Lampiran</label>
                            <input type="text" name="Lampiran" id="inputLampiran" class="form-control" value="<?= esc($surat['Lampiran'] ?? '') ?>" placeholder="1 Berkas / -">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Salam Pembuka</label>
                        <input type="text" name="SalamPembuka" id="inputSalamPembuka" class="form-control" value="<?= esc($surat['SalamPembuka'] ?? "Assalamu'alaikum Warahmatullahi Wabarakatuh") ?>">
                    </div>

                    <div class="form-group">
                        <label>Mukadimah / Pembuka</label>
                        <textarea name="MukadimahIsi" id="inputMukadimahIsi" class="form-control" rows="2"><?= esc($surat['MukadimahIsi'] ?? 'Dengan hormat, sehubungan dengan...') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Isi Pokok Surat <span class="text-danger">*</span></label>
                        <textarea name="IsiSurat" id="inputIsiSurat" class="form-control" rows="7" placeholder="Ketik isi surat di sini..."><?= esc($surat['IsiSurat'] ?? '') ?></textarea>
                        <small class="text-muted d-block mt-1">
                            Placeholder otomatis: <code>{{NAMA_TUJUAN}}</code>, <code>{{NOMOR_SURAT}}</code>, <code>{{TANGGAL_SURAT}}</code>, <code>{{NAMA_SANTRI}}</code>, <code>{{NAMA_GURU}}</code>, <code>{{NAMA_KELAS}}</code>
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Kalimat Penutup</label>
                        <textarea name="Penutup" id="inputPenutup" class="form-control" rows="2"><?= esc($surat['Penutup'] ?? 'Demikian surat ini kami sampaikan, atas perhatian dan kerja samanya kami ucapkan terima kasih.') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Salam Penutup</label>
                        <input type="text" name="SalamPenutup" id="inputSalamPenutup" class="form-control" value="<?= esc($surat['SalamPenutup'] ?? "Wassalamu'alaikum Warahmatullahi Wabarakatuh") ?>">
                    </div>
                </div>
                <div class="card-footer wizard-footer d-flex justify-content-between align-items-center flex-wrap shadow-sm">
                    <button type="button" class="btn btn-outline-secondary btn-prev px-4 py-2 font-weight-bold shadow-sm mb-1" data-prev="1" style="border-radius: 6px; min-width: 130px;">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </button>
                    <div class="d-none d-md-flex align-items-center mb-1">
                        <span class="badge badge-light border px-3 py-2 text-secondary font-weight-normal shadow-sm" style="font-size: 0.8rem; border-radius: 6px;">
                            <i class="fas fa-align-left text-warning mr-1"></i> Langkah 2 dari 5 &bull; Perihal & Redaksi Isi
                        </span>
                    </div>
                    <div class="d-flex align-items-center flex-wrap mb-1" style="gap: 10px;">
                        <?php if (!empty($isEdit)): ?>
                            <button type="button" class="btn btn-outline-success px-4 py-2 btn-submit-wizard font-weight-bold shadow-sm" title="Simpan Perubahan Surat" style="border-radius: 6px;">
                                <i class="fas fa-save mr-1"></i> Simpan
                            </button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-warning px-4 py-2 btn-next font-weight-bold shadow-sm" data-next="3" style="border-radius: 6px; min-width: 180px;">
                            Lanjut ke Tanda Tangan <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- STEP 3: PENANDA TANGAN DINAMIS (TTD & QR CODE) -->
        <!-- ============================================================== -->
        <?php
        // Parse konfigurasi penanda tangan yang sudah ada (untuk mode edit)
        $ttdConfig      = [];
        $ttdLayoutPreset = 'preset_2';
        if (!empty($surat['PenandaTanganConfig'])) {
            $decoded = json_decode($surat['PenandaTanganConfig'], true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($decoded)) {
                $ttdConfig       = $decoded;
                $ttdLayoutPreset = $decoded['layout_preset'] ?? 'preset_2';
            }
        }
        // Fallback ke data lama jika config kosong (backward compat)
        if (empty($ttdConfig['signers'])) {
            $ttdConfig = [
                'layout_preset'   => $ttdLayoutPreset,
                'label_mengetahui'=> 'Mengetahui,',
                'signers'         => [
                    [
                        'signer_id'     => 'signer_1',
                        'posisi'        => 'kanan',
                        'sumber'        => 'guru',
                        'ref_id'        => $surat['IdPenandaTangan1'] ?? '',
                        'nama'          => '',
                        'jabatan'       => $surat['Jabatan1'] ?? 'Kepala TPQ',
                        'instansi'      => '',
                        'nip'           => '',
                        'show_nip'      => false,
                        'show_instansi' => false,
                        'mode_ttd'      => $surat['ModeTtd1'] ?? 'Manual',
                        'token_10'      => null,
                    ],
                    [
                        'signer_id'     => 'signer_2',
                        'posisi'        => 'kiri',
                        'sumber'        => 'guru',
                        'ref_id'        => $surat['IdPenandaTangan2'] ?? '',
                        'nama'          => '',
                        'jabatan'       => $surat['Jabatan2'] ?? 'Sekretaris',
                        'instansi'      => '',
                        'nip'           => '',
                        'show_nip'      => false,
                        'show_instansi' => false,
                        'mode_ttd'      => $surat['ModeTtd2'] ?? 'Manual',
                        'token_10'      => null,
                    ],
                ],
            ];
        }
        // Data FKPQ untuk dropdown sumber FKPQ
        $db_step6 = \Config\Database::connect();
        $fkpqData = $db_step6->table('tbl_fkpq')->get()->getRowArray();
        ?>
        <div class="step-panel" id="step3">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-file-signature mr-2"></i>Langkah 3: Penanda Tangan & Tata Letak Naskah Dinas
                    </h3>
                </div>
                <div class="card-body">

                    <!-- Hidden: simpan JSON config lengkap -->
                    <input type="hidden" name="PenandaTanganConfig" id="inputPenandaTanganConfig" value="<?= esc(json_encode($ttdConfig)) ?>">
                    <!-- Backward compat: field lama tetap diisi lewat JS -->
                    <input type="hidden" name="IdPenandaTangan1" id="hiddenIdPenandaTangan1" value="<?= esc($surat['IdPenandaTangan1'] ?? '') ?>">
                    <input type="hidden" name="Jabatan1"         id="hiddenJabatan1"         value="<?= esc($surat['Jabatan1'] ?? '') ?>">
                    <input type="hidden" name="ModeTtd1"         id="hiddenModeTtd1"         value="<?= esc($surat['ModeTtd1'] ?? 'Manual') ?>">
                    <input type="hidden" name="IdPenandaTangan2" id="hiddenIdPenandaTangan2" value="<?= esc($surat['IdPenandaTangan2'] ?? '') ?>">
                    <input type="hidden" name="Jabatan2"         id="hiddenJabatan2"         value="<?= esc($surat['Jabatan2'] ?? '') ?>">
                    <input type="hidden" name="ModeTtd2"         id="hiddenModeTtd2"         value="<?= esc($surat['ModeTtd2'] ?? 'Manual') ?>">

                    <!-- ======== BAGIAN 1: Parameter Waktu & Tempat ======== -->
                    <div class="row mb-3 bg-light p-3 rounded border">
                        <div class="col-12 mb-2 d-flex justify-content-between align-items-center flex-wrap">
                            <span class="small font-weight-bold text-dark">
                                <i class="fas fa-calendar-check text-primary mr-1"></i> Tempat & Tanggal Pengesahan Naskah Dinas
                            </span>
                            <span class="badge badge-light border text-muted small">Dicetak otomatis di atas kolom penanggung jawab</span>
                        </div>
                        <div class="col-md-6 form-group mb-0">
                            <label class="small font-weight-bold text-secondary mb-1">Tempat Tanda Tangan:</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-map-marker-alt text-danger"></i></span></div>
                                <input type="text" name="TempatTtd" id="inputTempatTtd" class="form-control font-weight-bold" value="<?= esc($surat['TempatTtd'] ?? 'Bintan') ?>" placeholder="Contoh: Bintan / Tanjung Uban">
                            </div>
                        </div>
                        <div class="col-md-6 form-group mb-0 mt-2 mt-md-0">
                            <label class="small font-weight-bold text-secondary mb-1">Tanggal Tanda Tangan:</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-calendar-alt text-primary"></i></span></div>
                                <input type="date" name="TanggalTtd" id="inputTanggalTtd" class="form-control font-weight-bold" value="<?= esc($surat['TanggalTtd'] ?? date('Y-m-d')) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- ======== BAGIAN 2: Pilih Preset Tata Letak ======== -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="font-weight-bold mb-0 text-dark">
                                <i class="fas fa-th-large text-primary mr-1"></i> Pola Tata Letak Tanda Tangan (Kaidah Naskah Dinas):
                            </label>
                            <span class="badge badge-info px-2 py-1"><i class="fas fa-magic mr-1"></i> 5 Preset Baku</span>
                        </div>
                        <div class="row" id="layoutPresetRow">
                            <!-- Preset 1: Tunggal -->
                            <div class="col-12 col-sm-6 col-md-4 col-lg mb-2">
                                <div class="preset-card card h-100 text-center p-2 <?= $ttdLayoutPreset === 'preset_1' ? 'selected' : '' ?>"
                                     data-preset="preset_1" style="cursor:pointer;" title="Pola 1 Penanda Tangan Tunggal">
                                    <span class="selected-check"><i class="fas fa-check"></i></span>
                                    <span class="preset-badge badge-primary text-white">1 TTD</span>
                                    <div class="preset-mini-diagram">
                                        <div class="mini-row" style="justify-content: flex-end; padding-right: 12px;">
                                            <span class="mini-box box-ketua" style="width: 32px;" title="Ketua / Kepala"></span>
                                        </div>
                                    </div>
                                    <div class="font-weight-bold text-dark" style="font-size: 0.84rem;">Tunggal (1 TTD)</div>
                                    <div class="text-muted" style="font-size: 0.7rem; line-height: 1.2;">Kepala TPQ / Mandat</div>
                                </div>
                            </div>

                            <!-- Preset 2: Bipartit Sejajar -->
                            <div class="col-12 col-sm-6 col-md-4 col-lg mb-2">
                                <div class="preset-card card h-100 text-center p-2 <?= $ttdLayoutPreset === 'preset_2' ? 'selected' : '' ?>"
                                     data-preset="preset_2" style="cursor:pointer;" title="Pola 2 Penanda Tangan Sejajar">
                                    <span class="selected-check"><i class="fas fa-check"></i></span>
                                    <span class="preset-badge badge-success text-white">2 TTD</span>
                                    <div class="preset-mini-diagram">
                                        <div class="mini-row" style="justify-content: space-between; padding: 0 4px;">
                                            <span class="mini-box box-sekretaris" style="width: 24px;" title="Sekretaris (Kiri)"></span>
                                            <span class="mini-box box-ketua" style="width: 24px;" title="Ketua (Kanan)"></span>
                                        </div>
                                    </div>
                                    <div class="font-weight-bold text-dark" style="font-size: 0.84rem;">Bipartit Sejajar</div>
                                    <div class="text-muted" style="font-size: 0.7rem; line-height: 1.2;">Sekretaris & Ketua</div>
                                </div>
                            </div>

                            <!-- Preset 3: Piramida Tripartit -->
                            <div class="col-12 col-sm-6 col-md-4 col-lg mb-2">
                                <div class="preset-card card h-100 text-center p-2 <?= $ttdLayoutPreset === 'preset_3' ? 'selected' : '' ?>"
                                     data-preset="preset_3" style="cursor:pointer;" title="Pola 3 Penanda Tangan Piramida">
                                    <span class="selected-check"><i class="fas fa-check"></i></span>
                                    <span class="preset-badge badge-info text-white">3 TTD ▲</span>
                                    <div class="preset-mini-diagram">
                                        <div class="mini-row" style="justify-content: space-between; padding: 0 4px;">
                                            <span class="mini-box box-sekretaris" style="width: 22px;" title="Sekretaris"></span>
                                            <span class="mini-box box-ketua" style="width: 22px;" title="Ketua Panitia"></span>
                                        </div>
                                        <div class="mini-row">
                                            <span class="mini-box box-mengetahui" style="width: 28px;" title="Mengetahui"></span>
                                        </div>
                                    </div>
                                    <div class="font-weight-bold text-dark" style="font-size: 0.84rem;">Piramida ▲</div>
                                    <div class="text-muted" style="font-size: 0.7rem; line-height: 1.2;">2 Pelaksana + 1 Mengetahui</div>
                                </div>
                            </div>

                            <!-- Preset 4: Tiga Sejajar -->
                            <div class="col-12 col-sm-6 col-md-4 col-lg mb-2">
                                <div class="preset-card card h-100 text-center p-2 <?= $ttdLayoutPreset === 'preset_4' ? 'selected' : '' ?>"
                                     data-preset="preset_4" style="cursor:pointer;" title="Pola 3 Penanda Tangan Sejajar Horisontal">
                                    <span class="selected-check"><i class="fas fa-check"></i></span>
                                    <span class="preset-badge badge-warning text-dark">3 TTD ═</span>
                                    <div class="preset-mini-diagram">
                                        <div class="mini-row" style="justify-content: space-between;">
                                            <span class="mini-box box-sekretaris" style="width: 18px;" title="Sekretaris"></span>
                                            <span class="mini-box box-bendahara" style="width: 18px;" title="Bendahara"></span>
                                            <span class="mini-box box-ketua" style="width: 18px;" title="Ketua"></span>
                                        </div>
                                    </div>
                                    <div class="font-weight-bold text-dark" style="font-size: 0.84rem;">Tiga Sejajar ═</div>
                                    <div class="text-muted" style="font-size: 0.7rem; line-height: 1.2;">Sekretaris, Bendahara, Ketua</div>
                                </div>
                            </div>

                            <!-- Preset 5: Kuartet Berjenjang -->
                            <div class="col-12 col-sm-6 col-md-4 col-lg mb-2">
                                <div class="preset-card card h-100 text-center p-2 <?= $ttdLayoutPreset === 'preset_5' ? 'selected' : '' ?>"
                                     data-preset="preset_5" style="cursor:pointer;" title="Pola 4 Penanda Tangan Kuartet Berjenjang">
                                    <span class="selected-check"><i class="fas fa-check"></i></span>
                                    <span class="preset-badge badge-danger text-white">4 TTD ⊞</span>
                                    <div class="preset-mini-diagram">
                                        <div class="mini-row" style="justify-content: space-between; padding: 0 4px;">
                                            <span class="mini-box box-sekretaris" style="width: 22px;" title="Sekretaris Panitia"></span>
                                            <span class="mini-box box-ketua" style="width: 22px;" title="Ketua Panitia"></span>
                                        </div>
                                        <div class="mini-row" style="justify-content: space-between; padding: 0 4px;">
                                            <span class="mini-box box-fkpq" style="width: 22px;" title="FKPQ / Pembina"></span>
                                            <span class="mini-box box-mengetahui" style="width: 22px;" title="Camat / Kades"></span>
                                        </div>
                                    </div>
                                    <div class="font-weight-bold text-dark" style="font-size: 0.84rem;">Kuartet Berjenjang</div>
                                    <div class="text-muted" style="font-size: 0.7rem; line-height: 1.2;">2 Pelaksana + 2 Mengetahui</div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="selectedLayoutPreset" value="<?= esc($ttdLayoutPreset) ?>">
                    </div>

                    <!-- ======== BAGIAN 3: Daftar Kartu Penanda Tangan + Preview Sticky ======== -->
                    <div class="row">
                        <!-- Kolom Kiri: Kartu Penanda Tangan Dinamis -->
                        <div class="col-lg-7 col-xl-8">
                            <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-2 px-3 rounded border shadow-sm flex-wrap">
                                <div class="d-flex align-items-center mb-2 mb-sm-0">
                                    <span class="font-weight-bold text-dark mr-2">
                                        <i class="fas fa-users text-primary mr-1"></i> Pejabat Penanda Tangan
                                    </span>
                                    <span class="badge badge-primary px-2 py-1 font-weight-bold" id="signerCountBadge">2 Pejabat</span>
                                </div>
                                <div class="d-flex align-items-center flex-wrap">
                                    <button type="button" id="btnSwapBaris1" class="btn btn-sm btn-outline-primary mr-1 mb-1 mb-sm-0 shadow-sm" title="Tukar Posisi Kiri ⇄ Kanan pada Baris Pertama">
                                        <i class="fas fa-exchange-alt mr-1"></i> Tukar Baris 1
                                    </button>
                                    <button type="button" id="btnSwapBaris2" class="btn btn-sm btn-outline-info mr-1 mb-1 mb-sm-0 shadow-sm d-none" title="Tukar Posisi Kiri ⇄ Kanan pada Baris Mengetahui">
                                        <i class="fas fa-exchange-alt mr-1"></i> Tukar Baris 2
                                    </button>
                                    <button type="button" id="btnTambahSigner" class="btn btn-sm btn-success mb-1 mb-sm-0 shadow-sm">
                                        <i class="fas fa-user-plus mr-1"></i> Tambah Pejabat
                                    </button>
                                </div>
                            </div>

                            <!-- Kontainer Kartu Penanda Tangan (Dirender Dinamis via JS) -->
                            <div id="signerCardsContainer"></div>
                        </div>

                        <!-- Kolom Kanan: Preview Real-time Mengambang (Sticky) -->
                        <div class="col-lg-5 col-xl-4 mt-3 mt-lg-0">
                            <div class="preview-sticky-wrap">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="font-weight-bold mb-0 text-dark">
                                        <i class="fas fa-eye text-info mr-1"></i> Pratinjau Lembar Surat
                                    </label>
                                    <span class="badge badge-success px-2 py-1" style="font-size: 0.68rem;"><i class="fas fa-sync-alt mr-1"></i> Live Real-Time</span>
                                </div>
                                <div id="ttdPreviewBox">
                                    <div class="preview-header-ribbon">
                                        <span><i class="fas fa-file-alt mr-1"></i> SIMULASI CETAK TANDA TANGAN</span>
                                        <span>KERTAS RESMI</span>
                                    </div>
                                    <div class="text-center text-muted small py-4">
                                        <i class="fas fa-sync fa-spin mr-1"></i> Memuat simulasi tanda tangan...
                                    </div>
                                </div>
                                <div class="alert alert-light border mt-2 py-2 px-3 small text-muted shadow-sm" style="font-size: 0.72rem; line-height: 1.4;">
                                    <i class="fas fa-info-circle text-primary mr-1"></i>
                                    <strong>Petunjuk Kaidah Naskah:</strong> Penanggung jawab utama berada di sebelah kanan baris 1. Gunakan tombol <em>Tukar Baris 1</em> jika ingin menukar posisi Kiri ⇄ Kanan dengan satu klik tanpa perlu ketik ulang.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Template Kartu Signer (Tersembunyi, Diklon oleh JS Engine) -->
                    <template id="tmplSignerCard">
                        <div class="signer-card card shadow-sm mb-3" data-signer-index="">
                            <div class="signer-card-header card-header py-2 d-flex justify-content-between align-items-center border-bottom bg-light">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-primary mr-2 px-2 py-1 signer-posisi-badge" style="font-size: 0.78rem;">
                                        <i class="fas fa-user-check mr-1 signer-icon"></i> <span class="label-posisi-text">Penanda Tangan</span>
                                    </span>
                                    <span class="text-muted small font-italic signer-subtitle">Penanggung Jawab</span>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-move-up" title="Geser Urutan ke Atas">
                                        <i class="fas fa-arrow-up"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-move-down" title="Geser Urutan ke Bawah">
                                        <i class="fas fa-arrow-down"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-danger btn-hapus-signer ml-1" title="Hapus Penanda Tangan Ini">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <!-- Row 1: Posisi & Sumber Data -->
                                <div class="row">
                                    <div class="col-md-6 form-group mb-2">
                                        <label class="small font-weight-bold text-dark"><i class="fas fa-arrows-alt text-muted mr-1"></i> Posisi Kolom Surat:</label>
                                        <select class="form-control form-control-sm signer-posisi font-weight-bold">
                                            <option value="kanan">Kanan (Penanggung Jawab / Ketua)</option>
                                            <option value="kiri">Kiri (Sekretaris / Pelaksana)</option>
                                            <option value="tengah">Tengah (Bendahara / Penyusun)</option>
                                            <option value="mengetahui_tengah">Bawah Tengah (Mengetahui 1 Orang)</option>
                                            <option value="mengetahui_kiri">Bawah Kiri (Mengetahui Kiri)</option>
                                            <option value="mengetahui_kanan">Bawah Kanan (Mengetahui Kanan)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group mb-2">
                                        <label class="small font-weight-bold text-dark"><i class="fas fa-database text-muted mr-1"></i> Sumber Data Pejabat:</label>
                                        <div class="btn-group btn-group-sm w-100 signer-sumber-group" role="group">
                                            <button type="button" class="btn btn-sumber btn-outline-secondary active" data-sumber="guru" title="Ambil dari daftar guru TPQ">
                                                <i class="fas fa-chalkboard-teacher mr-1"></i> Guru TPQ
                                            </button>
                                            <button type="button" class="btn btn-sumber btn-outline-secondary" data-sumber="master_tujuan" title="Ambil dari Kontak Master (Camat, Lurah, dll)">
                                                <i class="fas fa-landmark mr-1"></i> Kontak Master
                                            </button>
                                            <button type="button" class="btn btn-sumber btn-outline-secondary" data-sumber="fkpq" title="Ambil dari Pengurus FKPQ">
                                                <i class="fas fa-mosque mr-1"></i> FKPQ
                                            </button>
                                            <button type="button" class="btn btn-sumber btn-outline-secondary" data-sumber="manual" title="Input nama bebas ad-hoc">
                                                <i class="fas fa-keyboard mr-1"></i> Bebas
                                            </button>
                                        </div>
                                        <input type="hidden" class="signer-sumber" value="guru">
                                    </div>
                                </div>

                                <!-- Baris Teks Pengantar Mengetahui (Muncul jika posisi Mengetahui) -->
                                <div class="form-group mb-2 wrap-label-mengetahui d-none bg-light p-2 rounded border border-info">
                                    <label class="small font-weight-bold text-info mb-1"><i class="fas fa-tag mr-1"></i> Teks Pengantar Mengetahui / Pengesahan:</label>
                                    <input type="text" class="form-control form-control-sm signer-label-mengetahui font-weight-bold" value="Mengetahui," placeholder="Contoh: Mengetahui, / Mengesahkan, / Menyetujui,">
                                </div>

                                <!-- Row 2: Dropdown Pilih Pejabat (Select2 Terintegrasi Pencarian) -->
                                <div class="form-group mb-2 wrap-pilih-pejabat">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="small font-weight-bold text-dark mb-0">
                                            <i class="fas fa-user-check text-primary mr-1"></i> Pilih / Cari Nama Pejabat:
                                        </label>
                                        <span class="badge badge-light border text-muted signer-sumber-badge" style="font-size: 0.68rem;">
                                            Sumber: Guru TPQ
                                        </span>
                                    </div>
                                    <div class="signer-select2-wrap">
                                        <select class="form-control form-control-sm signer-ref-select font-weight-bold" style="width: 100%;">
                                            <option value="">-- Cari atau Pilih Pejabat --</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Row 3: Nama Lengkap & Jabatan -->
                                <div class="row">
                                    <div class="col-md-6 form-group mb-2">
                                        <label class="small font-weight-bold text-dark">Nama Lengkap & Gelar: <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm signer-nama font-weight-bold" placeholder="Nama lengkap dengan gelar akademik/keagamaan">
                                    </div>
                                    <div class="col-md-6 form-group mb-2">
                                        <label class="small font-weight-bold text-dark">Jabatan pada Surat: <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm signer-jabatan font-weight-bold" placeholder="Contoh: Ketua Panitia / Kepala TPQ">
                                    </div>
                                </div>

                                <!-- Row 4: Instansi & NIP beserta Switch Tampilkan -->
                                <div class="row">
                                    <div class="col-md-6 form-group mb-2">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <label class="small font-weight-bold text-dark mb-0">Instansi / Lembaga:</label>
                                            <div class="custom-control custom-switch" style="font-size:0.75rem">
                                                <input type="checkbox" class="custom-control-input signer-show-instansi" id="toggleInstansi_IDX">
                                                <label class="custom-control-label small font-weight-bold text-secondary" for="toggleInstansi_IDX">Tampilkan di Surat</label>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control form-control-sm signer-instansi" placeholder="Contoh: TPQ Al-Ikhlas / Kantor Camat">
                                    </div>
                                    <div class="col-md-6 form-group mb-2">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <label class="small font-weight-bold text-dark mb-0">NIP / NIK / ID Guru:</label>
                                            <div class="custom-control custom-switch" style="font-size:0.75rem">
                                                <input type="checkbox" class="custom-control-input signer-show-nip" id="toggleNip_IDX">
                                                <label class="custom-control-label small font-weight-bold text-secondary" for="toggleNip_IDX">Tampilkan di Surat</label>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control form-control-sm signer-nip font-monospace" placeholder="Nomor identitas resmi (opsional)">
                                    </div>
                                </div>

                                <!-- Row 5: Metode Pengesahan & Checkbox Simpan Master -->
                                <div class="row pt-2 align-items-center border-top mt-2">
                                    <div class="col-md-7 form-group mb-1">
                                        <label class="small font-weight-bold d-block mb-1 text-dark">Metode Pengesahan / TTD:</label>
                                        <div class="d-flex" style="gap: 8px;">
                                            <label class="mode-ttd-card mode-card-qr mb-0 flex-fill">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input signer-mode-qr" name="modeTtd_IDX" id="modeQr_IDX" value="QR">
                                                    <span class="custom-control-label small font-weight-bold text-primary">
                                                        <i class="fas fa-qrcode mr-1"></i> QR Code Digital
                                                    </span>
                                                </div>
                                                <div class="text-muted" style="font-size: 0.68rem; margin-left: 24px;">Verifikasi online (10 karakter unik)</div>
                                            </label>
                                            <label class="mode-ttd-card mode-card-manual mb-0 flex-fill active">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input signer-mode-manual" name="modeTtd_IDX" id="modeManual_IDX" value="Manual" checked>
                                                    <span class="custom-control-label small font-weight-bold text-secondary">
                                                        <i class="fas fa-pen-nib mr-1"></i> TTD Basah Fisik
                                                    </span>
                                                </div>
                                                <div class="text-muted" style="font-size: 0.68rem; margin-left: 24px;">Ruang kosong tanda tangan & stempel</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-5 form-group mb-1 wrap-save-master d-none">
                                        <div class="alert alert-info py-2 px-3 mb-0 border border-info rounded">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input signer-save-master" id="saveMaster_IDX">
                                                <label class="custom-control-label small text-dark font-weight-bold" for="saveMaster_IDX">
                                                    <i class="fas fa-save text-primary mr-1"></i> Simpan ke Kontak Master
                                                </label>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.68rem; margin-left: 24px;">Agar otomatis tersimpan dan dapat dipilih lagi di surat berikutnya</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Data JSON untuk JS: Guru, Master, FKPQ -->
                    <script>
                    var STEP6_DATA = {
                        guruList: <?= json_encode(array_map(function($g){
                            return [
                                'id'       => $g['IdGuru'],
                                'nama'     => function_exists('formatNamaGelar') ? formatNamaGelar($g['Nama']) : $g['Nama'],
                                'jabatan'  => $g['Jabatan'] ?? 'Guru TPQ',
                                'instansi' => $g['TempatTugas'] ?? '',
                                'nip'      => $g['IdGuru'] ?? '',
                            ];
                        }, $guruList)) ?>,
                        masterList: <?= json_encode(array_map(function($m){
                            return [
                                'id'       => $m['Id'],
                                'nama'     => function_exists('formatNamaGelar') ? formatNamaGelar($m['NamaTujuan']) : $m['NamaTujuan'],
                                'jabatan'  => $m['Jabatan'] ?? '',
                                'instansi' => $m['Instansi'] ?? '',
                                'nip'      => $m['Nip'] ?? '',
                            ];
                        }, $tujuanMaster)) ?>,
                        fkpqList: <?php
                            $fkpqOptions = [];
                            if (!empty($fkpqData)) {
                                if (!empty($fkpqData['KetuaFkpq'])) {
                                    $fkpqOptions[] = [
                                        'id'       => 'fkpq_ketua',
                                        'nama'     => function_exists('formatNamaGelar') ? formatNamaGelar($fkpqData['KetuaFkpq']) : $fkpqData['KetuaFkpq'],
                                        'jabatan'  => 'Ketua FKPQ',
                                        'instansi' => $fkpqData['NamaFkpq'] ?? 'FKPQ Kabupaten Bintan',
                                        'nip'      => '',
                                    ];
                                }
                            }
                            echo json_encode($fkpqOptions);
                        ?>,
                        config: <?= json_encode($ttdConfig) ?>
                    };
                    </script>

                </div><!-- /.card-body -->
                <div class="card-footer wizard-footer d-flex justify-content-between align-items-center flex-wrap shadow-sm">
                    <button type="button" class="btn btn-outline-secondary btn-prev px-4 py-2 font-weight-bold shadow-sm mb-1" data-prev="2" style="border-radius: 6px; min-width: 130px;">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </button>
                    <div class="d-none d-md-flex align-items-center mb-1">
                        <span class="badge badge-light border px-3 py-2 text-secondary font-weight-normal shadow-sm" style="font-size: 0.8rem; border-radius: 6px;">
                            <i class="fas fa-file-signature text-primary mr-1"></i> Langkah 3 dari 5 &bull; Penanda Tangan & Tata Letak
                        </span>
                    </div>
                    <div class="d-flex align-items-center flex-wrap mb-1" style="gap: 10px;">
                        <?php if (!empty($isEdit)): ?>
                            <button type="button" class="btn btn-outline-success px-4 py-2 btn-submit-wizard font-weight-bold shadow-sm" title="Simpan Perubahan Surat" style="border-radius: 6px;">
                                <i class="fas fa-save mr-1"></i> Simpan
                            </button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-primary px-4 py-2 btn-next font-weight-bold shadow-sm" data-next="4" style="border-radius: 6px; min-width: 180px;">
                            Lanjut ke Penerima Surat <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- STEP 4: PILIH TUJUAN PENERIMA -->
        <!-- ============================================================== -->
        <div class="step-panel" id="step4">
            <div class="card card-success card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-users mr-2"></i>Langkah 4: Pilih Tujuan / Penerima Surat</h3>
                </div>
                <div class="card-body">
                    <!-- Tab Pilihan Tipe Penerima -->
                    <ul class="nav nav-pills mb-3" id="penerimaTabs">
                        <li class="nav-item">
                            <a class="nav-link active tipe-penerima-tab" href="javascript:void(0)" data-type="single">
                                <i class="fas fa-user mr-1"></i> Penerima Tunggal
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link tipe-penerima-tab" href="javascript:void(0)" data-type="master">
                                <i class="fas fa-address-book mr-1"></i> Database Tujuan (Pejabat/Lembaga)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link tipe-penerima-tab" href="javascript:void(0)" data-type="guru">
                                <i class="fas fa-chalkboard-teacher mr-1"></i> Massal Guru / Wali Kelas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link tipe-penerima-tab" href="javascript:void(0)" data-type="wali_santri">
                                <i class="fas fa-user-graduate mr-1"></i> Massal Wali Santri
                            </a>
                        </li>
                    </ul>

                    <!-- Mode A: Single Manual -->
                    <div id="wrapperSingleRecipient">
                        <div class="row">
                            <div class="col-md-12 form-group mb-3">
                                <div class="card bg-light border p-2 mb-0">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 10px;">
                                        <span class="small font-weight-bold text-dark">
                                            <i class="fas fa-database text-primary mr-1"></i> Ambil dari Database Tujuan:
                                        </span>
                                        <div style="flex: 1; min-width: 260px;">
                                            <select id="selectQuickTujuanMaster" class="form-control form-control-sm">
                                                <option value="">-- Pilih Tujuan dari Database untuk Mengisi Otomatis --</option>
                                                <?php if (!empty($tujuanMaster)): ?>
                                                    <?php foreach ($tujuanMaster as $tm): 
                                                        $namaFmt = function_exists('formatNamaGelar') ? formatNamaGelar($tm['NamaTujuan']) : $tm['NamaTujuan'];
                                                    ?>
                                                        <option value="<?= $tm['Id'] ?>"
                                                                data-nama="<?= esc($namaFmt) ?>"
                                                                data-jabatan="<?= esc($tm['Jabatan'] ?? '') ?>"
                                                                data-instansi="<?= esc($tm['Instansi'] ?? '') ?>"
                                                                data-alamat="<?= esc($tm['Alamat'] ?? '') ?>"
                                                                data-kota="<?= esc($tm['Kota'] ?? '') ?>">
                                                            <?= esc($namaFmt) ?><?= !empty($tm['Jabatan']) ? ' - ' . esc($tm['Jabatan']) : '' ?><?= !empty($tm['Instansi']) ? ' (' . esc($tm['Instansi']) . ')' : '' ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Nama Penerima / Pejabat <span class="text-danger">*</span></label>
                                <input type="text" name="TujuanNama" id="inputTujuanNama" class="form-control" value="<?= esc($surat['TujuanNama'] ?? '') ?>" placeholder="Contoh: Sudarno, S.Pd">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Jabatan</label>
                                <input type="text" name="TujuanJabatan" id="inputTujuanJabatan" class="form-control" value="<?= esc($surat['TujuanJabatan'] ?? '') ?>" placeholder="Contoh: Kepala Bagian KESRA">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Instansi / Lembaga</label>
                                <input type="text" name="TujuanInstansi" id="inputTujuanInstansi" class="form-control" value="<?= esc($surat['TujuanInstansi'] ?? '') ?>" placeholder="Contoh: Pemkab Bintan">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Kota / Kabupaten</label>
                                <input type="text" name="TujuanKota" id="inputTujuanKota" class="form-control" value="<?= esc($surat['TujuanKota'] ?? 'Tempat') ?>" placeholder="Tempat / Bintan">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Alamat Lengkap (Opsional)</label>
                            <input type="text" name="TujuanAlamat" id="inputTujuanAlamat" class="form-control" value="<?= esc($surat['TujuanAlamat'] ?? '') ?>" placeholder="Jalan, No...">
                        </div>
                    </div>

                    <!-- Mode B & C & D: Multi Select List -->
                    <div id="wrapperMultiRecipient" style="display: none;">
                        <div class="row mb-2">
                            <div class="col-md-4 form-group" id="filterKelasWrapper" style="display: none;">
                                <label class="font-weight-bold small text-muted">Filter Kelas Santri:</label>
                                <select id="filterKelasSelect" class="form-control form-control-sm">
                                    <option value="">-- Semua Kelas --</option>
                                    <?php foreach ($kelasList as $k): ?>
                                        <option value="<?= $k['IdKelas'] ?>"><?= esc($k['NamaKelas']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 form-group" id="filterKategoriMasterWrapper" style="display: none;">
                                <label class="font-weight-bold small text-muted">Filter Kategori Tujuan:</label>
                                <select id="filterKategoriMasterSelect" class="form-control form-control-sm">
                                    <option value="">-- Semua Kategori Tujuan --</option>
                                    <option value="Pemerintah">Pemerintah</option>
                                    <option value="Lembaga">Lembaga</option>
                                    <option value="Perorangan">Perorangan</option>
                                    <option value="Donatur">Donatur</option>
                                </select>
                            </div>
                            <div class="col-md-5 form-group" id="searchRecipientWrapper">
                                <label class="font-weight-bold small text-muted">Cari Penerima (Nama / Jabatan / Instansi):</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                    </div>
                                    <input type="text" id="inputSearchRecipient" class="form-control" placeholder="Ketik kata kunci pencarian...">
                                    <div class="input-group-append" id="btnClearSearchRecipient" style="display: none;">
                                        <button class="btn btn-outline-secondary" type="button" title="Bersihkan Pencarian">&times;</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mb-2 flex-wrap">
                                <div id="searchRecipientInfo" class="small text-muted py-1">
                                    <!-- Status / Info Hasil Pencarian -->
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnSelectAllRecipients">
                                        <i class="fas fa-check-double mr-1"></i> Pilih Semua (Tampil)
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm ml-1" id="btnUnselectAllRecipients">
                                        Batal Pilih (Tampil)
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded p-2" style="max-height: 280px; overflow-y: auto;" id="recipientCheckboxList">
                            <div class="text-center py-4 text-muted">Memuat data penerima...</div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
                            <small class="text-muted">
                                Terpilih: <strong id="countSelectedRecipients">0</strong> penerima. File PDF dapat digenerate sekaligus atau per individual dalam file ZIP.
                            </small>
                        </div>
                        <!-- Box Daftar Penerima Terpilih (Chips/Badges) -->
                        <div class="mt-2 p-2 bg-light rounded border" id="boxSelectedRecipientsBadges" style="max-height: 115px; overflow-y: auto;">
                            <div class="small font-weight-bold text-muted mb-1"><i class="fas fa-check-circle text-success mr-1"></i> Penerima Terpilih (Klik &times; untuk membatalkan):</div>
                            <div id="selectedRecipientsBadges" class="d-flex flex-wrap" style="gap: 5px;">
                                <span class="text-muted small">Belum ada penerima yang dipilih.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer wizard-footer d-flex justify-content-between align-items-center flex-wrap shadow-sm">
                    <button type="button" class="btn btn-outline-secondary btn-prev px-4 py-2 font-weight-bold shadow-sm mb-1" data-prev="3" style="border-radius: 6px; min-width: 130px;">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </button>
                    <div class="d-none d-md-flex align-items-center mb-1">
                        <span class="badge badge-light border px-3 py-2 text-secondary font-weight-normal shadow-sm" style="font-size: 0.8rem; border-radius: 6px;">
                            <i class="fas fa-users text-success mr-1"></i> Langkah 4 dari 5 &bull; Data Penerima Surat
                        </span>
                    </div>
                    <div class="d-flex align-items-center flex-wrap mb-1" style="gap: 10px;">
                        <?php if (!empty($isEdit)): ?>
                            <button type="button" class="btn btn-outline-success px-4 py-2 btn-submit-wizard font-weight-bold shadow-sm" title="Simpan Perubahan Surat" style="border-radius: 6px;">
                                <i class="fas fa-save mr-1"></i> Simpan
                            </button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-success px-4 py-2 btn-next font-weight-bold shadow-sm" data-next="5" style="border-radius: 6px; min-width: 180px;">
                            Lanjut ke Pratinjau & Finalisasi <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- STEP 5: PRATINJAU & FINALISASI (STACKED VERTICAL) -->
        <!-- ============================================================== -->
        <div class="step-panel" id="step5">
            <div class="card card-dark card-outline shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-eye mr-2"></i>Langkah 5: Pratinjau Tampilan Surat & Finalisasi</h3>
                    <button type="button" class="btn btn-outline-light btn-sm" id="btnRefreshPreview">
                        <i class="fas fa-sync-alt mr-1"></i> Refresh Pratinjau
                    </button>
                </div>
                <div class="card-body p-3">
                    <!-- 1. Bagian Atas: Live Preview Surat (Full Width Center) -->
                    <div class="p-3 bg-secondary rounded mb-3" style="min-height: 500px; display: flex; justify-content: center;">
                        <div id="previewWrapper" class="bg-white shadow rounded" style="max-width: 850px; width: 100%; min-height: 800px; overflow: hidden;">
                            <iframe id="previewIframe" style="width: 100%; height: 850px; border: none; display: block; background: #fff;" srcdoc=""></iframe>
                        </div>
                    </div>

                    <!-- 2. Bagian Bawah: Ringkasan Final Surat (Collapsible Accordion) -->
                    <div class="card card-outline card-info collapsed-card mb-0 shadow-none border" id="cardSummaryAccordion">
                        <div class="card-header py-2" style="cursor: pointer;" data-card-widget="collapse">
                            <h3 class="card-title text-info font-weight-bold" style="font-size: 0.95rem;">
                                <i class="fas fa-info-circle mr-1"></i> Informasi & Ringkasan Data Surat
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-3" style="display: none;">
                            <table class="table table-sm table-bordered mb-3">
                                <tr>
                                    <th style="width: 220px;" class="bg-light">Nomor Surat</th>
                                    <td><strong id="summaryNomor" class="text-primary">-</strong></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Perihal / Hal</th>
                                    <td id="summaryHal">-</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Penerima</th>
                                    <td id="summaryPenerima">-</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Tanggal Surat</th>
                                    <td id="summaryTanggal">-</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Penanda Tangan</th>
                                    <td id="summaryTtd">-</td>
                                </tr>
                            </table>

                            <div class="alert alert-light border mb-0 text-muted small">
                                <i class="fas fa-info-circle text-info mr-1"></i>
                                <strong>Catatan Efisiensi:</strong> File PDF di-generate secara <em>on-demand (streaming)</em> dan tidak disimpan di server untuk menghemat kapasitas storage. Anda dapat mencetak ulang kapan saja dari menu <strong>Arsip Surat Keluar</strong>.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer wizard-footer d-flex justify-content-between align-items-center flex-wrap shadow-sm">
                    <button type="button" class="btn btn-outline-secondary btn-prev px-4 py-2 font-weight-bold shadow-sm mb-1" data-prev="4" style="border-radius: 6px; min-width: 130px;">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </button>
                    <div class="d-none d-md-flex align-items-center mb-1">
                        <span class="badge badge-light border px-3 py-2 text-secondary font-weight-normal shadow-sm" style="font-size: 0.8rem; border-radius: 6px;">
                            <i class="fas fa-check-double text-primary mr-1"></i> Langkah 5 dari 5 &bull; Pratinjau & Finalisasi
                        </span>
                    </div>
                    <button type="button" class="btn btn-success <?= !empty($isEdit) ? '' : 'btn-lg' ?> px-4 py-2 font-weight-bold btn-submit-wizard shadow-sm mb-1" id="btnSubmitFinal" title="Simpan Perubahan Surat" style="border-radius: 6px; min-width: 180px;">
                        <?= !empty($isEdit) ? '<i class="fas fa-save mr-1"></i> Simpan Perubahan' : '<i class="fas fa-paper-plane mr-2"></i> Finalisasi & Simpan Surat' ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Aksi Pasca Finalisasi -->
<div class="modal fade" id="modalPostFinal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold"><?= !empty($isEdit) ? '<i class="fas fa-check-circle mr-2"></i>Surat Berhasil Diperbarui!' : '<i class="fas fa-check-circle mr-2"></i>Surat Berhasil Difinalisasi!' ?></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" title="Tutup & Tetap di Halaman Edit">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="mb-4">Pilih format output dokumen yang ingin Anda unduh / cetak sekarang:</p>
                <div class="d-flex flex-column gap-2">
                    <a href="#" id="modalBtnPdf" target="_blank" class="btn btn-danger btn-lg mb-2 font-weight-bold shadow-sm">
                        <i class="fas fa-file-pdf mr-2"></i> Buka / Cetak PDF (Gabungan)
                    </a>
                    <a href="#" id="modalBtnZip" class="btn btn-primary btn-lg mb-3 font-weight-bold shadow-sm">
                        <i class="fas fa-file-archive mr-2"></i> Unduh File ZIP (PDF Individual per Penerima)
                    </a>
                    <div class="row pt-2 border-top">
                        <div class="col-6 pr-1">
                            <a href="<?= base_url('backend/administrasi/surat-keluar') ?>" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-folder-open mr-1"></i> Ke Arsip Surat
                            </a>
                        </div>
                        <div class="col-6 pl-1">
                            <button type="button" class="btn btn-outline-secondary btn-block font-weight-bold" data-dismiss="modal">
                                <i class="fas fa-times mr-1"></i> Tetap di Halaman
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    let selectedRecipients = [];
    let isEditMode = <?= !empty($isEdit) ? 'true' : 'false' ?>;
    let isDuplicateMode = <?= !empty($isDuplicate) ? 'true' : 'false' ?>;
    let btnSubmitDefaultHtml = isEditMode 
        ? '<i class="fas fa-save mr-1"></i> Simpan' 
        : '<i class="fas fa-save mr-2"></i> Finalisasi & Simpan Surat';

    // Helper escapeHtml
    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    // Helper cek apakah suatu item penerima sudah ada di selectedRecipients
    function isRecipientSelected(item) {
        if (!selectedRecipients || selectedRecipients.length === 0) return false;
        let itemNamaNorm = (item.nama || '').trim().toLowerCase();
        let itemIdStr = String(item.id || item.id_ref || '');

        return selectedRecipients.some(function(r) {
            if (r.id_ref && String(r.id_ref) !== '2147483647' && String(r.id_ref) === itemIdStr) {
                return true;
            }
            let rNamaNorm = (r.nama || '').trim().toLowerCase();
            if (rNamaNorm && itemNamaNorm && rNamaNorm === itemNamaNorm) {
                return true;
            }
            return false;
        });
    }

    // Render badge / chips penerima terpilih
    function renderSelectedBadges() {
        let container = $('#selectedRecipientsBadges');
        if (!container.length) return;

        if (!selectedRecipients || selectedRecipients.length === 0) {
            container.html('<span class="text-muted small">Belum ada penerima massal yang dipilih.</span>');
            return;
        }

        let html = '';
        selectedRecipients.forEach(function(r, idx) {
            let label = escapeHtml(r.nama);
            if (r.jabatan) {
                label += ' <small class="text-white-50">(' + escapeHtml(r.jabatan) + ')</small>';
            }
            html += `
                <span class="badge badge-primary py-1 px-2 font-weight-normal d-inline-flex align-items-center" style="font-size: 85%;">
                    <span>${label}</span>
                    <a href="javascript:void(0)" class="text-white ml-2 btn-remove-recipient" data-index="${idx}" title="Hapus Penerima" style="font-weight: bold; font-size: 110%; line-height: 1;">&times;</a>
                </span>
            `;
        });
        container.html(html);
    }

    // Pre-populate penerima jika ada (mode edit / duplikat)
    let preloadedRecipients = <?= !empty($penerima) ? json_encode($penerima) : '[]' ?>;
    if (preloadedRecipients && preloadedRecipients.length > 0) {
        if (preloadedRecipients.length > 1 || (preloadedRecipients.length === 1 && preloadedRecipients[0].TipePenerima !== 'Manual')) {
            let initialType = 'guru';
            if (preloadedRecipients[0].TipePenerima) {
                let tLower = preloadedRecipients[0].TipePenerima.toLowerCase();
                if (tLower.includes('wali') || tLower.includes('santri')) {
                    initialType = 'wali_santri';
                } else if (tLower.includes('master')) {
                    initialType = 'master';
                } else if (tLower.includes('guru')) {
                    initialType = 'guru';
                }
            }

            $('.tipe-penerima-tab').removeClass('active');
            $('.tipe-penerima-tab[data-type="' + initialType + '"]').addClass('active');
            $('#wrapperSingleRecipient').hide();
            $('#wrapperMultiRecipient').show();

            selectedRecipients = preloadedRecipients.map(function(p) {
                return {
                    id_ref: p.IdReferensi,
                    nama: p.NamaPenerima,
                    jabatan: p.JabatanPenerima || '',
                    santri: '',
                    wali: '',
                    kelas: '',
                    tipe: p.TipePenerima || 'Massal'
                };
            });
            $('#countSelectedRecipients').text(selectedRecipients.length);
            renderSelectedBadges();

            // Panggil loadRecipientList agar daftar termuat dan nama penerima otomatis tercentang!
            loadRecipientList(initialType);
        }
    }

    // Tampilkan info template jika sudah terpilih saat page load
    if ($('#selectTemplate').val()) {
        $('#templateDesc').html('Template terpilih untuk dokumen surat.');
        $('#templateInfoBox').show();
        if (isDuplicateMode && !$('#inputNomorSurat').val()) {
            triggerAutoNumber();
        }
    }

    // Initialize TinyMCE for Wizard Isi Surat
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#inputIsiSurat',
            height: 300,
            menubar: false,
            plugins: 'table lists link code searchreplace',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | insertPlaceholder | code',
            content_style: 'body { font-family: "Times New Roman", Times, serif; font-size: 12pt; line-height: 1.5; }',
            setup: function(editor) {
                editor.ui.registry.addMenuButton('insertPlaceholder', {
                    text: '{ Variabel }',
                    fetch: function(callback) {
                        var items = [
                            { type: 'menuitem', text: '{{NAMA_TUJUAN}} - Nama Penerima', onAction: function() { editor.insertContent('{{NAMA_TUJUAN}}'); } },
                            { type: 'menuitem', text: '{{JABATAN_TUJUAN}} - Jabatan Penerima', onAction: function() { editor.insertContent('{{JABATAN_TUJUAN}}'); } },
                            { type: 'menuitem', text: '{{NOMOR_SURAT}} - Nomor Surat', onAction: function() { editor.insertContent('{{NOMOR_SURAT}}'); } },
                            { type: 'menuitem', text: '{{TANGGAL_SURAT}} - Tanggal Surat', onAction: function() { editor.insertContent('{{TANGGAL_SURAT}}'); } },
                            { type: 'menuitem', text: '{{NAMA_SANTRI}} - Nama Santri', onAction: function() { editor.insertContent('{{NAMA_SANTRI}}'); } },
                            { type: 'menuitem', text: '{{NAMA_WALI}} - Nama Orang Tua/Wali', onAction: function() { editor.insertContent('{{NAMA_WALI}}'); } },
                            { type: 'menuitem', text: '{{NAMA_KELAS}} - Nama Kelas', onAction: function() { editor.insertContent('{{NAMA_KELAS}}'); } },
                            { type: 'menuitem', text: '{{NAMA_GURU}} - Nama Guru', onAction: function() { editor.insertContent('{{NAMA_GURU}}'); } }
                        ];
                        callback(items);
                    }
                });
            }
        });
    }

    // Switch step navigation
    function showStep(stepNum) {
        stepNum = parseInt(stepNum);
        $('.step-panel').removeClass('active');
        $('#step' + stepNum).addClass('active');

        // Reset semua status active dan completed agar step sesudahnya kembali abu-abu
        $('.wizard-step').removeClass('active completed');
        $('.wizard-step[data-step="' + stepNum + '"]').addClass('active');

        // Tandai hanya step yang berada sebelum step saat ini sebagai completed
        for (let i = 1; i < stepNum; i++) {
            $('.wizard-step[data-step="' + i + '"]').addClass('completed');
        }

        $('#mobileStepText').text('Langkah ' + stepNum + ' dari 5: ' + $('.wizard-step[data-step="' + stepNum + '"] .step-title').text());
        currentStep = stepNum;

        if (stepNum === 3) {
            if (window.Step6Engine && typeof window.Step6Engine.refresh === 'function') {
                window.Step6Engine.refresh();
            }
        } else if (stepNum === 5) {
            loadPreviewHtml();
            updateSummary();
        }
    }

    $('.btn-next').on('click', function() {
        let next = $(this).data('next');
        if (currentStep === 1 && next === 2) {
            if (!$('#selectTemplate').val()) {
                Swal.fire('Pilih Template', 'Silakan pilih salah satu template surat terlebih dahulu.', 'warning');
                return;
            }
            if (!$('#inputNomorSurat').val() || !$('#inputNomorSurat').val().trim()) {
                Swal.fire('Nomor Surat Kosong', 'Silakan isi atau hitung nomor surat terlebih dahulu.', 'warning');
                return;
            }
        } else if (currentStep === 2 && next === 3) {
            if (!$('#inputHal').val() || !$('#inputHal').val().trim()) {
                Swal.fire('Perihal Kosong', 'Silakan isi perihal surat terlebih dahulu.', 'warning');
                return;
            }
        } else if (currentStep === 4 && next === 5) {
            let curTab = $('.tipe-penerima-tab.active').data('type');
            if (curTab === 'single') {
                if (!$('#inputTujuanNama').val() || !$('#inputTujuanNama').val().trim()) {
                    Swal.fire('Penerima Belum Diisi', 'Silakan isi nama penerima surat terlebih dahulu.', 'warning');
                    return;
                }
            } else {
                if (selectedRecipients.length === 0) {
                    Swal.fire('Penerima Belum Dipilih', 'Silakan pilih minimal 1 penerima surat dari daftar.', 'warning');
                    return;
                }
            }
        }
        showStep(next);
    });

    $('.btn-prev').on('click', function() {
        let prev = $(this).data('prev');
        showStep(prev);
    });

    $('.wizard-step').on('click', function() {
        let step = $(this).data('step');
        showStep(step);
    });

    // Filter & Search Master Template (Step 1)
    function filterTemplates() {
        let selKat = $('#filterKategoriTemplate').val();
        let q = ($('#inputSearchTemplate').val() || '').trim().toLowerCase();
        let cardItems = $('#templateCardGrid .template-card-col');
        let totalCount = cardItems.length;

        if (q !== '') {
            $('#btnClearSearchTemplate').show();
        } else {
            $('#btnClearSearchTemplate').hide();
        }

        let matched = 0;

        // Filter Grid Kartu Template
        cardItems.each(function() {
            let katId = $(this).data('kategori');
            let nama = ($(this).data('nama') || '').toString();
            let katNama = ($(this).data('katnama') || '').toString();
            let kertas = ($(this).data('kertas') || '').toString();
            let deskripsi = ($(this).data('deskripsi') || '').toString();

            let matchKat = !selKat || String(katId) === String(selKat);
            let matchText = !q || (nama.includes(q) || katNama.includes(q) || kertas.includes(q) || deskripsi.includes(q));

            if (matchKat && matchText) {
                $(this).show();
                matched++;
            } else {
                $(this).hide();
            }
        });

        if (matched === 0) {
            $('#templateNoMatchAlert').show();
            $('#templateFilterCount').html('<span class="text-danger font-weight-bold">0</span> dari ' + totalCount + ' template');
        } else {
            $('#templateNoMatchAlert').hide();
            if (selKat || q) {
                $('#templateFilterCount').html('Ditemukan <span class="text-primary font-weight-bold">' + matched + '</span> dari ' + totalCount + ' template');
            } else {
                $('#templateFilterCount').html('<span class="text-muted">Total: <strong>' + totalCount + '</strong> template</span>');
            }
        }
    }

    $('#filterKategoriTemplate').on('change', filterTemplates);
    $('#inputSearchTemplate').on('input keyup', filterTemplates);
    $('#btnClearSearchTemplate button').on('click', function() {
        $('#inputSearchTemplate').val('');
        filterTemplates();
        $('#inputSearchTemplate').focus();
    });

    // Sinkronisasi status visual kartu template
    function syncTemplateCardVisual(selectedId) {
        $('.template-card-col').each(function() {
            let cardId = $(this).data('id');
            let card = $(this).find('.template-card');
            let btn = $(this).find('.btn-select-card');

            if (selectedId && String(cardId) === String(selectedId)) {
                card.addClass('active-selected');
                btn.removeClass('btn-outline-primary').addClass('btn-primary font-weight-bold')
                   .html('<i class="fas fa-check mr-1"></i> Terpilih');
            } else {
                card.removeClass('active-selected');
                btn.removeClass('btn-primary font-weight-bold').addClass('btn-outline-primary')
                   .html('<i class="fas fa-check-circle mr-1"></i> Pilih');
            }
        });
    }

    // Klik langsung pada kartu template
    $(document).on('click', '.template-card-col', function() {
        let tplId = $(this).data('id');
        let katId = $(this).data('kategori');
        if (tplId) {
            $('#selectTemplate').val(tplId).data('kategori', katId).attr('data-kategori', katId).trigger('change');
        }
    });

    // Inisialisasi awal filter & kartu template
    filterTemplates();
    let initTplId = $('#selectTemplate').val();
    if (initTplId) {
        let initKat = $('.template-card-col[data-id="' + initTplId + '"]').data('kategori');
        if (initKat) {
            $('#selectTemplate').data('kategori', initKat).attr('data-kategori', initKat);
        }
    }
    syncTemplateCardVisual(initTplId);

    // Template selection change
    $('#selectTemplate').on('change', function() {
        let id = $(this).val();
        syncTemplateCardVisual(id);

        if (!id) {
            $('#templateInfoBox').hide();
            return;
        }

        $.getJSON('<?= base_url('backend/administrasi/surat-keluar/ajax-template') ?>/' + id, function(res) {
            if (res.success) {
                let t = res.template;
                $('#optSumberKop').val(t.SumberKop);
                if (t.SumberKop === 'Kepanitiaan') {
                    $('#wrapperKopCustomWizard').show();
                    $('#optIdKopCustom').val(t.IdKopCustom);
                } else {
                    $('#wrapperKopCustomWizard').hide();
                }

                $('#optUkuranKertas').val(t.UkuranKertas);
                $('#optOrientasi').val(t.Orientasi);
                $('#optFontFamily').val(t.FontFamily);
                $('#optFontSize').val(t.FontSize);
                $('#optMarginAtas').val(t.MarginAtas);
                $('#optMarginBawah').val(t.MarginBawah);
                $('#optMarginKiri').val(t.MarginKiri);
                $('#optMarginKanan').val(t.MarginKanan);

                if (t.SalamPembuka) $('#inputSalamPembuka').val(t.SalamPembuka);
                if (t.MukadimahIsi) $('#inputMukadimahIsi').val(t.MukadimahIsi);
                if (t.IsiSurat) {
                    if (typeof tinymce !== 'undefined' && tinymce.get('inputIsiSurat')) {
                        tinymce.get('inputIsiSurat').setContent(t.IsiSurat);
                    } else {
                        $('#inputIsiSurat').val(t.IsiSurat);
                    }
                }
                if (t.Penutup) $('#inputPenutup').val(t.Penutup);
                if (t.SalamPenutup) $('#inputSalamPenutup').val(t.SalamPenutup);
                if (t.FormatNomor) $('#inputFormatNomor').val(t.FormatNomor);

                if (t.IdTpq !== undefined && t.IdTpq !== null) {
                    $('#wizardIdTpq').val(t.IdTpq);
                }

                $('#templateDesc').html('Kertas: ' + t.UkuranKertas + ' (' + t.Orientasi + ') | Kop: ' + t.SumberKop + (t.Deskripsi ? '<br>' + t.Deskripsi : ''));
                $('#templateInfoBox').slideDown();

                // Generate auto number
                triggerAutoNumber();
            }
        });
    });

    $('#optSumberKop').on('change', function() {
        if ($(this).val() === 'Kepanitiaan') {
            $('#wrapperKopCustomWizard').slideDown();
        } else {
            $('#wrapperKopCustomWizard').slideUp();
        }
    });

    // Standardisasi Format Nama & Gelar (JavaScript)
    function formatNamaGelarJs(raw) {
        if (!raw || typeof raw !== 'string') return '';
        raw = raw.trim();
        if (!raw) return '';

        const gelarMap = {
            'dr': 'Dr.', 'dr.': 'Dr.', 'dokter': 'dr.', 'drg': 'drg.', 'drg.': 'drg.',
            'drs': 'Drs.', 'drs.': 'Drs.', 'dra': 'Dra.', 'dra.': 'Dra.',
            'prof': 'Prof.', 'prof.': 'Prof.', 'ir': 'Ir.', 'ir.': 'Ir.',
            'h': 'H.', 'h.': 'H.', 'hj': 'Hj.', 'hj.': 'Hj.',
            'kh': 'K.H.', 'kh.': 'K.H.', 'k.h.': 'K.H.',
            'ust': 'Ust.', 'ust.': 'Ust.', 'ustadz': 'Ustadz', 'kyai': 'Kyai', 'tgk': 'Tgk.', 'tgk.': 'Tgk.',
            's.pd': 'S.Pd', 'spd': 'S.Pd', 's.pd.i': 'S.Pd.I', 'spdi': 'S.Pd.I',
            's.kom': 'S.Kom', 'skom': 'S.Kom', 's.e': 'S.E', 'se': 'S.E',
            's.ag': 'S.Ag', 'sag': 'S.Ag', 's.h': 'S.H', 'sh': 'S.H',
            's.t': 'S.T', 'st': 'S.T', 's.sos': 'S.Sos', 'ssos': 'S.Sos',
            's.si': 'S.Si', 'ssi': 'S.Si', 's.ked': 'S.Ked', 'sked': 'S.Ked',
            's.kep': 'S.Kep', 'skep': 'S.Kep', 's.farm': 'S.Farm', 'sfarm': 'S.Farm',
            's.psi': 'S.Psi', 'spsi': 'S.Psi', 's.sn': 'S.Sn', 's.pt': 'S.Pt', 's.p': 'S.P',
            's.ip': 'S.I.P', 's.i.p': 'S.I.P', 'sip': 'S.I.P', 's.sos.i': 'S.Sos.I', 's.hum': 'S.Hum',
            's.pd.sd': 'S.Pd.SD', 's.th.i': 'S.Th.I', 's.sy': 'S.Sy', 's.e.i': 'S.E.I',
            's.ak': 'S.Ak', 's.ab': 'S.A.B', 's.ap': 'S.A.P', 's.tr.kom': 'S.Tr.Kom', 's.tr.t': 'S.Tr.T',
            'm.pd': 'M.Pd', 'mpd': 'M.Pd', 'm.pd.i': 'M.Pd.I', 'mpdi': 'M.Pd.I',
            'm.kom': 'M.Kom', 'mkom': 'M.Kom', 'm.m': 'M.M', 'mm': 'M.M',
            'm.ag': 'M.Ag', 'mag': 'M.Ag', 'm.si': 'M.Si', 'msi': 'M.Si',
            'm.h': 'M.H', 'mh': 'M.H', 'm.t': 'M.T', 'mt': 'M.T',
            'm.sc': 'M.Sc', 'msc': 'M.Sc', 'm.a': 'M.A', 'ma': 'M.A',
            'm.b.a': 'M.B.A', 'mba': 'M.B.A', 'b.a': 'B.A', 'lc': 'Lc.', 'lc.': 'Lc.',
            'ph.d': 'Ph.D', 'phd': 'Ph.D', 'll.m': 'LL.M', 'ak': 'Ak.', 'ak.': 'Ak.',
            'cpa': 'CPA', 'ca': 'CA'
        };

        const titleCase = (s) => s.charAt(0).toUpperCase() + s.slice(1).toLowerCase();

        let extraBracket = '';
        let mBracket = raw.match(/\s*(\([^)]+\))\s*$/);
        if (mBracket) {
            raw = raw.substring(0, raw.length - mBracket[0].length).trim();
            let inner = mBracket[1].replace(/^[(\s]+|[)\s]+$/g, '');
            let innerFormatted = inner.split(/\s+/).map(w => titleCase(w)).join(' ');
            extraBracket = ' (' + innerFormatted + ')';
        }

        let parts = raw.split(',').map(p => p.trim());
        let namaUtama = parts.shift() || '';

        let words = namaUtama.split(/\s+/);
        let formattedWords = [];
        let gelarBelakangAuto = [];

        words.forEach((w, idx) => {
            let wClean = w.toLowerCase().trim();
            let wNoDot = wClean.replace(/\.+$/, '');

            if (idx > 0 && (gelarMap[wClean] || gelarMap[wNoDot])) {
                let canonical = gelarMap[wClean] || gelarMap[wNoDot];
                if (!['h', 'hj', 'dr', 'drs', 'dra', 'prof', 'ir', 'kh', 'ust', 'ustadz', 'kyai', 'tgk'].includes(wNoDot)) {
                    gelarBelakangAuto.push(canonical);
                    return;
                }
            }

            if (gelarMap[wClean]) {
                formattedWords.push(gelarMap[wClean]);
            } else if (gelarMap[wNoDot] && ['h', 'hj', 'dr', 'drs', 'dra', 'prof', 'ir', 'kh', 'ust', 'ustadz', 'kyai', 'tgk'].includes(wNoDot)) {
                formattedWords.push(gelarMap[wNoDot]);
            } else {
                if (['bin', 'binti', 'ibn', 'van', 'de'].includes(wClean) && idx > 0) {
                    formattedWords.push(wClean);
                } else {
                    formattedWords.push(titleCase(w));
                }
            }
        });

        let formattedNama = formattedWords.join(' ');
        let allGelar = gelarBelakangAuto.concat(parts);
        let formattedGelar = [];

        allGelar.forEach(g => {
            let gTrim = g.trim();
            if (!gTrim) return;
            let gLower = gTrim.toLowerCase();
            let gNoDot = gLower.replace(/\.+$/, '');

            if (gelarMap[gLower]) {
                formattedGelar.push(gelarMap[gLower]);
            } else if (gelarMap[gNoDot]) {
                formattedGelar.push(gelarMap[gNoDot]);
            } else {
                if (gTrim.length <= 4 && !gTrim.includes('.')) {
                    formattedGelar.push(gTrim.toUpperCase());
                } else {
                    formattedGelar.push(titleCase(gTrim));
                }
            }
        });

        if (formattedGelar.length > 0) {
            formattedNama += ', ' + formattedGelar.join(', ');
        }

        return formattedNama + extraBracket;
    }

    // Auto-standardize nama saat blur dari input Nama Penerima Tunggal
    $('#inputTujuanNama').on('blur', function() {
        let val = $(this).val();
        if (val) {
            $(this).val(formatNamaGelarJs(val));
        }
    });

    // Quick autofill Penerima Tunggal dari Database Tujuan
    $('#selectQuickTujuanMaster').on('change', function() {
        let opt = $(this).find('option:selected');
        if (opt.val()) {
            $('#inputTujuanNama').val(formatNamaGelarJs(opt.data('nama') || ''));
            $('#inputTujuanJabatan').val(opt.data('jabatan') || '');
            $('#inputTujuanInstansi').val(opt.data('instansi') || '');
            $('#inputTujuanKota').val(opt.data('kota') || 'Bintan');
            $('#inputTujuanAlamat').val(opt.data('alamat') || '');
        }
    });

    // Recipient mode tabs
    $('.tipe-penerima-tab').on('click', function() {
        $('.tipe-penerima-tab').removeClass('active');
        $(this).addClass('active');
        let type = $(this).data('type');

        // Reset pencarian saat beralih tab
        $('#inputSearchRecipient').val('');
        $('#btnClearSearchRecipient').hide();
        $('#searchRecipientInfo').html('');

        if (type === 'single') {
            $('#wrapperSingleRecipient').show();
            $('#wrapperMultiRecipient').hide();
        } else {
            $('#wrapperSingleRecipient').hide();
            $('#wrapperMultiRecipient').show();

            if (type === 'wali_santri' || type === 'santri') {
                $('#filterKelasWrapper').show();
                $('#filterKategoriMasterWrapper').hide();
                loadRecipientList(type, $('#filterKelasSelect').val());
            } else if (type === 'master' || type === 'tujuan_master') {
                $('#filterKelasWrapper').hide();
                $('#filterKategoriMasterWrapper').show();
                loadRecipientList('master', '', $('#filterKategoriMasterSelect').val());
            } else {
                $('#filterKelasWrapper').hide();
                $('#filterKategoriMasterWrapper').hide();
                loadRecipientList(type);
            }
        }
    });

    $('#filterKelasSelect').on('change', function() {
        loadRecipientList('wali_santri', $(this).val());
    });

    $('#filterKategoriMasterSelect').on('change', function() {
        loadRecipientList('master', '', $(this).val());
    });

    function loadRecipientList(type, idKelas = '', kategoriTujuan = '') {
        $('#recipientCheckboxList').html('<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat daftar penerima...</div>');

        $.getJSON('<?= base_url('backend/administrasi/surat-keluar/ajax-recipients') ?>', { 
            type: type, 
            id_kelas: idKelas,
            kategori_tujuan: kategoriTujuan 
        }, function(res) {
            if (res.success && res.data.length > 0) {
                let html = '<div class="row">';
                res.data.forEach(function(item) {
                    let checked = isRecipientSelected(item);
                    let checkedAttr = checked ? 'checked' : '';
                    let selectedClass = checked ? 'recipient-item-selected' : '';

                    html += `
                        <div class="col-md-6 mb-2 recipient-card-col">
                            <div class="border rounded p-2 recipient-box ${selectedClass}" style="cursor: pointer;">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input chk-recipient" id="rec_${item.id}" 
                                           value="${item.id}" 
                                           data-type="${type}"
                                           data-nama="${escapeHtml(item.nama)}" 
                                           data-jabatan="${escapeHtml(item.jabatan || item.info || '')}" 
                                           data-instansi="${escapeHtml(item.instansi || '')}" 
                                           data-alamat="${escapeHtml(item.alamat || '')}" 
                                           data-kota="${escapeHtml(item.kota || '')}" 
                                           data-santri="${escapeHtml(item.santri || '')}" 
                                           data-wali="${escapeHtml(item.wali || '')}" 
                                           data-kelas="${escapeHtml(item.kelas || '')}"
                                           ${checkedAttr}>
                                    <label class="custom-control-label font-weight-bold" for="rec_${item.id}">
                                        ${escapeHtml(item.nama)}
                                    </label>
                                </div>
                                <small class="text-muted d-block ml-4">${escapeHtml(item.info || '')} ${item.kontak ? ' | ' + escapeHtml(item.kontak) : ''}</small>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                $('#recipientCheckboxList').html(html);
                bindRecipientCheckboxEvents();
                filterRecipientsByKeyword();
            } else {
                $('#recipientCheckboxList').html('<div class="text-center py-4 text-muted">Tidak ada data penerima yang ditemukan.</div>');
                $('#searchRecipientInfo').html('');
                $('#btnClearSearchRecipient').hide();
            }
        });
    }

    // Filter instan penerima berdasarkan kata kunci
    function filterRecipientsByKeyword() {
        let q = ($('#inputSearchRecipient').val() || '').trim().toLowerCase();
        let items = $('#recipientCheckboxList .recipient-card-col');
        let total = items.length;

        if (!total) {
            $('#searchRecipientInfo').html('');
            $('#btnClearSearchRecipient').hide();
            return;
        }

        if (q === '') {
            items.show();
            $('#btnClearSearchRecipient').hide();
            $('#searchRecipientInfo').html('<span class="text-muted"><i class="fas fa-users mr-1"></i> Total: <strong>' + total + '</strong> penerima</span>');
            $('#recipientNoMatchMessage').remove();
            return;
        }

        $('#btnClearSearchRecipient').show();
        let matched = 0;

        items.each(function() {
            let chk = $(this).find('.chk-recipient');
            let dataNama = (chk.data('nama') || '').toString().toLowerCase();
            let dataJabatan = (chk.data('jabatan') || '').toString().toLowerCase();
            let dataInstansi = (chk.data('instansi') || '').toString().toLowerCase();
            let dataSantri = (chk.data('santri') || '').toString().toLowerCase();
            let dataWali = (chk.data('wali') || '').toString().toLowerCase();
            let dataKelas = (chk.data('kelas') || '').toString().toLowerCase();
            let boxText = ($(this).text() || '').toLowerCase();

            if (dataNama.includes(q) || dataJabatan.includes(q) || dataInstansi.includes(q) || dataSantri.includes(q) || dataWali.includes(q) || dataKelas.includes(q) || boxText.includes(q)) {
                $(this).show();
                matched++;
            } else {
                $(this).hide();
            }
        });

        $('#recipientNoMatchMessage').remove();
        if (matched === 0) {
            $('#recipientCheckboxList .row').append('<div class="col-12 text-center py-4 text-muted" id="recipientNoMatchMessage"><i class="fas fa-search-minus mr-1"></i> Tidak ada penerima yang cocok dengan kata kunci "<strong>' + escapeHtml(q) + '</strong>"</div>');
            $('#searchRecipientInfo').html('<span class="text-danger font-weight-bold">0</span> dari ' + total + ' cocok');
        } else {
            $('#searchRecipientInfo').html('Ditemukan <span class="text-primary font-weight-bold">' + matched + '</span> dari ' + total + ' penerima');
        }
    }

    $('#inputSearchRecipient').on('input keyup', function() {
        filterRecipientsByKeyword();
    });

    $('#btnClearSearchRecipient button').on('click', function() {
        $('#inputSearchRecipient').val('');
        filterRecipientsByKeyword();
        $('#inputSearchRecipient').focus();
    });

    function bindRecipientCheckboxEvents() {
        $('.chk-recipient').off('change').on('change', function() {
            let isChecked = $(this).is(':checked');
            let curType = $(this).data('type') || $('.tipe-penerima-tab.active').data('type');
            let tipeRec = 'Massal';
            if (curType === 'master' || curType === 'tujuan_master') {
                tipeRec = 'Master';
            } else if (curType === 'guru') {
                tipeRec = 'Guru';
            } else if (curType === 'wali_santri' || curType === 'santri') {
                tipeRec = 'Wali Santri';
            }

            let itemData = {
                id_ref: $(this).val(),
                nama: $(this).data('nama'),
                jabatan: $(this).data('jabatan'),
                instansi: $(this).data('instansi'),
                alamat: $(this).data('alamat'),
                kota: $(this).data('kota'),
                santri: $(this).data('santri'),
                wali: $(this).data('wali'),
                kelas: $(this).data('kelas'),
                tipe: tipeRec
            };

            if (isChecked) {
                if (!isRecipientSelected(itemData)) {
                    selectedRecipients.push(itemData);
                }
                $(this).closest('.recipient-box').addClass('recipient-item-selected');
            } else {
                let itemNamaNorm = (itemData.nama || '').trim().toLowerCase();
                let itemIdStr = String(itemData.id_ref || '');
                selectedRecipients = selectedRecipients.filter(function(r) {
                    if (r.id_ref && String(r.id_ref) !== '2147483647' && String(r.id_ref) === itemIdStr) {
                        return false;
                    }
                    let rNamaNorm = (r.nama || '').trim().toLowerCase();
                    if (rNamaNorm && itemNamaNorm && rNamaNorm === itemNamaNorm) {
                        return false;
                    }
                    return true;
                });
                $(this).closest('.recipient-box').removeClass('recipient-item-selected');
            }

            $('#countSelectedRecipients').text(selectedRecipients.length);
            renderSelectedBadges();
        });
    }

    // Klik pada area recipient-box untuk men-toggle checkbox
    $(document).off('click', '.recipient-box').on('click', '.recipient-box', function(e) {
        if ($(e.target).is('input[type="checkbox"]') || $(e.target).is('label') || $(e.target).closest('label').length) {
            return;
        }
        let chk = $(this).find('.chk-recipient');
        chk.prop('checked', !chk.prop('checked')).trigger('change');
    });

    // Tombol hapus dari badge penerima terpilih
    $(document).off('click', '.btn-remove-recipient').on('click', '.btn-remove-recipient', function(e) {
        e.preventDefault();
        e.stopPropagation();
        let idx = parseInt($(this).data('index'));
        if (!isNaN(idx) && idx >= 0 && idx < selectedRecipients.length) {
            let removed = selectedRecipients[idx];
            selectedRecipients.splice(idx, 1);

            let remNamaNorm = (removed.nama || '').trim().toLowerCase();
            let remIdStr = String(removed.id_ref || '');
            $('.chk-recipient').each(function() {
                let thisNamaNorm = ($(this).data('nama') || '').trim().toLowerCase();
                let thisIdStr = String($(this).val() || '');
                if ((remIdStr !== '2147483647' && thisIdStr === remIdStr) || (thisNamaNorm && remNamaNorm && thisNamaNorm === remNamaNorm)) {
                    $(this).prop('checked', false);
                    $(this).closest('.recipient-box').removeClass('recipient-item-selected');
                }
            });

            $('#countSelectedRecipients').text(selectedRecipients.length);
            renderSelectedBadges();
        }
    });

    $('#btnSelectAllRecipients').on('click', function() {
        let curType = $('.tipe-penerima-tab.active').data('type');
        let tipeRec = 'Massal';
        if (curType === 'master' || curType === 'tujuan_master') {
            tipeRec = 'Master';
        } else if (curType === 'guru') {
            tipeRec = 'Guru';
        } else if (curType === 'wali_santri' || curType === 'santri') {
            tipeRec = 'Wali Santri';
        }

        // Pilih hanya item yang sedang tampil (sesuai pencarian / filter)
        $('#recipientCheckboxList .recipient-card-col:visible .chk-recipient').each(function() {
            if (!$(this).is(':checked')) {
                $(this).prop('checked', true);
                let itemData = {
                    id_ref: $(this).val(),
                    nama: $(this).data('nama'),
                    jabatan: $(this).data('jabatan'),
                    instansi: $(this).data('instansi'),
                    alamat: $(this).data('alamat'),
                    kota: $(this).data('kota'),
                    santri: $(this).data('santri'),
                    wali: $(this).data('wali'),
                    kelas: $(this).data('kelas'),
                    tipe: tipeRec
                };
                if (!isRecipientSelected(itemData)) {
                    selectedRecipients.push(itemData);
                }
                $(this).closest('.recipient-box').addClass('recipient-item-selected');
            }
        });
        $('#countSelectedRecipients').text(selectedRecipients.length);
        renderSelectedBadges();
    });

    $('#btnUnselectAllRecipients').on('click', function() {
        // Batalkan hanya item yang sedang tampil (sesuai pencarian / filter)
        $('#recipientCheckboxList .recipient-card-col:visible .chk-recipient').each(function() {
            let itemNamaNorm = ($(this).data('nama') || '').trim().toLowerCase();
            let itemIdStr = String($(this).val() || '');
            selectedRecipients = selectedRecipients.filter(function(r) {
                if (r.id_ref && String(r.id_ref) !== '2147483647' && String(r.id_ref) === itemIdStr) {
                    return false;
                }
                let rNamaNorm = (r.nama || '').trim().toLowerCase();
                if (rNamaNorm && itemNamaNorm && rNamaNorm === itemNamaNorm) {
                    return false;
                }
                return true;
            });
            $(this).prop('checked', false);
            $(this).closest('.recipient-box').removeClass('recipient-item-selected');
        });
        $('#countSelectedRecipients').text(selectedRecipients.length);
        renderSelectedBadges();
    });

    // Auto Number Generation
    function triggerAutoNumber() {
        let tplId = $('#selectTemplate').val();
        let katId = $('#selectTemplate').data('kategori') || $('#selectTemplate').attr('data-kategori');
        let format = $('#inputFormatNomor').val();

        if (!tplId) return;

        $.post('<?= base_url('backend/administrasi/surat-keluar/ajax-generate-nomor') ?>', {
            id_tpq: $('#wizardIdTpq').val(),
            id_kategori: katId,
            format: format
        }, function(res) {
            if (res.success) {
                $('#inputNomorSurat').val(res.nomorSurat);
                $('#inputUrutanNomor').val(res.urutan);
            }
        }, 'json');
    }

    $('#btnHitungNomor').on('click', triggerAutoNumber);

    // Live Preview HTML (Step 7)
    function loadPreviewHtml() {
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }
        let iframe = document.getElementById('previewIframe');
        if (iframe) {
            iframe.srcdoc = '<div style="text-align:center; padding:100px 0; font-family:sans-serif; color:#777;"><i class="fas fa-spinner fa-spin fa-2x" style="margin-bottom:15px; display:block;"></i><div>Merender pratinjau surat...</div></div>';
        }

        let formData = $('#wizardForm').serializeArray();
        if (selectedRecipients.length > 0) {
            formData.push({ name: 'TujuanNama', value: selectedRecipients[0].nama });
            formData.push({ name: 'TujuanJabatan', value: selectedRecipients[0].jabatan });
        }

        $.post('<?= base_url('backend/administrasi/surat-keluar/ajax-preview-html') ?>', formData, function(res) {
            if (res.success && iframe) {
                iframe.srcdoc = res.html;
            }
        }, 'json');
    }

    $('#btnRefreshPreview').on('click', loadPreviewHtml);

    // Update Summary in Step 8
    function updateSummary() {
        $('#summaryNomor').text($('#inputNomorSurat').val() || '-');
        $('#summaryHal').text($('#inputHal').val() || '-');
        $('#summaryTanggal').text($('#inputTanggal').val() || '-');

        if (selectedRecipients.length > 0) {
            $('#summaryPenerima').text(selectedRecipients.length + ' Penerima Terpilih (Contoh: ' + selectedRecipients[0].nama + ')');
        } else {
            $('#summaryPenerima').text($('#inputTujuanNama').val() || '-');
        }

        // Update summary TTD dari konfigurasi dinamis
        var cfg = window.Step6Engine ? window.Step6Engine.getConfig() : null;
        if (cfg && cfg.signers && cfg.signers.length > 0) {
            var signerNames = cfg.signers.map(function(s){ return s.nama || s.jabatan || '?'; });
            $('#summaryTtd').text(signerNames.join(' & ') || '-');
        } else {
            var ttd1 = $('#selectTtd1 option:selected').text();
            $('#summaryTtd').text(ttd1 !== '-- Pilih Guru / Kepala --' ? ttd1 : '-');
        }
    }

    // Submit Final & Simpan Perubahan di Semua Step
    $(document).on('click', '.btn-submit-wizard, #btnSubmitFinal', function(e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }
        let btn = $(this);
        let origHtml = btn.html();

        $('.btn-submit-wizard, #btnSubmitFinal').prop('disabled', true);
        btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> ' + (isEditMode ? 'Menyimpan...' : 'Memfinalisasi...'));

        let formData = $('#wizardForm').serializeArray();
        if (selectedRecipients.length > 0) {
            formData.push({ name: 'PenerimaList', value: JSON.stringify(selectedRecipients) });
        }

        $.ajax({
            url: '<?= base_url('backend/administrasi/surat-keluar/simpan') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#modalBtnPdf').attr('href', res.printUrl);
                    $('#modalBtnZip').attr('href', res.zipUrl);

                    // Show/hide ZIP button based on recipients
                    if (selectedRecipients.length > 1) {
                        $('#modalBtnZip').show();
                    } else {
                        $('#modalBtnZip').hide();
                    }

                    // Simpan ID Surat di form agar jika disimpan ulang tetap mengupdate surat yang sama
                    if (res.idSurat) {
                        if (!$('#wizardIdSurat').length) {
                            $('#wizardForm').prepend('<input type="hidden" name="IdSurat" id="wizardIdSurat" value="' + res.idSurat + '">');
                        } else {
                            $('#wizardIdSurat').val(res.idSurat);
                        }
                    }

                    $('#modalPostFinal').modal('show');
                } else {
                    let errMsg = res.message;
                    if (res.errors) {
                        errMsg += '<br>' + Object.values(res.errors).join('<br>');
                    }
                    Swal.fire('Gagal', errMsg, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan sistem saat ' + (isEditMode ? 'menyimpan' : 'memfinalisasi') + ' surat', 'error');
            },
            complete: function() {
                $('.btn-submit-wizard, #btnSubmitFinal').prop('disabled', false);
                btn.html(origHtml);
            }
        });
    });
});
</script>

<script>
/* ============================================================
   STEP 6 ENGINE — Tanda Tangan Dinamis & Kaidah Naskah Dinas
   - Render kartu signer dari STEP6_DATA.config
   - Preset picker + Live preview kertas surat resmi
   - Swap Baris 1 (Kiri ⇄ Kanan) & Baris 2 (Mengetahui ⇄)
   - Geser urutan (Up/Down) & Proteksi Hapus minimal 1 signer
   - Multi-source (Guru, Master Kontak, FKPQ, Bebas)
   - Auto-save master & sinkronisasi real-time
============================================================ */
document.addEventListener('DOMContentLoaded', function() {
    (function($) {
        if (!$) {
            console.error('Step6Engine: jQuery tidak tersedia saat DOMContentLoaded.');
            return;
        }
        if (typeof STEP6_DATA === 'undefined') {
            console.error('Step6Engine: STEP6_DATA tidak terdefinisi.');
            return;
        }

        var signers = JSON.parse(JSON.stringify(STEP6_DATA.config.signers || []));
        var labelMengetahui = STEP6_DATA.config.label_mengetahui || 'Mengetahui,';

        /* ---- Preset → default signers baku ---- */
        var presetDefaults = {
            preset_1: [
                {signer_id:'signer_1',posisi:'kanan',sumber:'guru',ref_id:'',nama:'',jabatan:'Kepala TPQ',instansi:'',nip:'',show_nip:false,show_instansi:false,mode_ttd:'Manual',token_10:null,save_master:false}
            ],
            preset_2: [
                {signer_id:'signer_1',posisi:'kanan',sumber:'guru',ref_id:'',nama:'',jabatan:'Ketua',instansi:'',nip:'',show_nip:false,show_instansi:false,mode_ttd:'Manual',token_10:null,save_master:false},
                {signer_id:'signer_2',posisi:'kiri',sumber:'guru',ref_id:'',nama:'',jabatan:'Sekretaris',instansi:'',nip:'',show_nip:false,show_instansi:false,mode_ttd:'Manual',token_10:null,save_master:false}
            ],
            preset_3: [
                {signer_id:'signer_1',posisi:'kanan',sumber:'guru',ref_id:'',nama:'',jabatan:'Ketua Panitia',instansi:'',nip:'',show_nip:false,show_instansi:false,mode_ttd:'Manual',token_10:null,save_master:false},
                {signer_id:'signer_2',posisi:'kiri',sumber:'guru',ref_id:'',nama:'',jabatan:'Sekretaris Panitia',instansi:'',nip:'',show_nip:false,show_instansi:false,mode_ttd:'Manual',token_10:null,save_master:false},
                {signer_id:'signer_3',posisi:'mengetahui_tengah',sumber:'master_tujuan',ref_id:'',nama:'',jabatan:'Camat / Kepala Desa / FKPQ',instansi:'',nip:'',show_nip:false,show_instansi:true,mode_ttd:'Manual',token_10:null,save_master:false}
            ],
            preset_4: [
                {signer_id:'signer_1',posisi:'kanan',sumber:'guru',ref_id:'',nama:'',jabatan:'Ketua',instansi:'',nip:'',show_nip:false,show_instansi:false,mode_ttd:'Manual',token_10:null,save_master:false},
                {signer_id:'signer_2',posisi:'tengah',sumber:'guru',ref_id:'',nama:'',jabatan:'Bendahara',instansi:'',nip:'',show_nip:false,show_instansi:false,mode_ttd:'Manual',token_10:null,save_master:false},
                {signer_id:'signer_3',posisi:'kiri',sumber:'guru',ref_id:'',nama:'',jabatan:'Sekretaris',instansi:'',nip:'',show_nip:false,show_instansi:false,mode_ttd:'Manual',token_10:null,save_master:false}
            ],
            preset_5: [
                {signer_id:'signer_1',posisi:'kanan',sumber:'guru',ref_id:'',nama:'',jabatan:'Ketua Panitia',instansi:'',nip:'',show_nip:false,show_instansi:false,mode_ttd:'Manual',token_10:null,save_master:false},
                {signer_id:'signer_2',posisi:'kiri',sumber:'guru',ref_id:'',nama:'',jabatan:'Sekretaris Panitia',instansi:'',nip:'',show_nip:false,show_instansi:false,mode_ttd:'Manual',token_10:null,save_master:false},
                {signer_id:'signer_3',posisi:'mengetahui_kanan',sumber:'master_tujuan',ref_id:'',nama:'',jabatan:'Camat / Kepala Desa',instansi:'',nip:'',show_nip:false,show_instansi:true,mode_ttd:'Manual',token_10:null,save_master:false},
                {signer_id:'signer_4',posisi:'mengetahui_kiri',sumber:'fkpq',ref_id:'fkpq_ketua',nama:'',jabatan:'Ketua FKPQ',instansi:'',nip:'',show_nip:false,show_instansi:true,mode_ttd:'Manual',token_10:null,save_master:false}
            ]
        };

        var posisiMeta = {
            'kanan':            { label: 'Kanan (Penanggung Jawab / Ketua)', badge: 'badge-primary', icon: 'fa-user-check', sub: 'Penanggung Jawab / Ketua', accent: 'border-accent-kanan' },
            'kiri':             { label: 'Kiri (Sekretaris / Pelaksana)',     badge: 'badge-success', icon: 'fa-user-edit',  sub: 'Pelaksana / Sekretaris', accent: 'border-accent-kiri' },
            'tengah':           { label: 'Tengah (Bendahara / Penyusun)',    badge: 'badge-warning text-dark', icon: 'fa-coins', sub: 'Bendahara / Penyusun', accent: 'border-accent-tengah' },
            'mengetahui_tengah':{ label: 'Bawah Tengah (Mengetahui 1 Orang)',badge: 'badge-info',    icon: 'fa-stamp',      sub: 'Mengetahui / Pembina', accent: 'border-accent-mengetahui' },
            'mengetahui_kiri':  { label: 'Bawah Kiri (Mengetahui Kiri)',     badge: 'badge-info',    icon: 'fa-stamp',      sub: 'Mengetahui Kiri', accent: 'border-accent-mengetahui' },
            'mengetahui_kanan': { label: 'Bawah Kanan (Mengetahui Kanan)',    badge: 'badge-info',    icon: 'fa-stamp',      sub: 'Mengetahui Kanan', accent: 'border-accent-mengetahui' }
        };

        var sumberLabelMap = {
            'guru': 'Sumber: Guru TPQ',
            'master_tujuan': 'Sumber: Kontak Master',
            'fkpq': 'Sumber: Pengurus FKPQ',
            'manual': 'Input Manual'
        };

        function escH(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

        /* ---- Build dropdown options untuk Select2 ---- */
        function buildOptions(sumber, selectedId) {
            var list = [];
            if (sumber === 'guru') list = STEP6_DATA.guruList || [];
            else if (sumber === 'master_tujuan') list = STEP6_DATA.masterList || [];
            else if (sumber === 'fkpq') list = STEP6_DATA.fkpqList || [];

            var html = '<option value="">-- Cari atau Pilih Pejabat --</option>';
            $.each(list, function(_, item) {
                var sel = (String(item.id) === String(selectedId)) ? ' selected' : '';
                var subText = item.jabatan ? ' — ' + item.jabatan : '';
                var instText = item.instansi ? ' [' + item.instansi + ']' : '';
                var nipText = item.nip ? ' (NIP: ' + item.nip + ')' : '';
                html += '<option value="'+escH(item.id)+'" data-nama="'+escH(item.nama)+'" data-jabatan="'+escH(item.jabatan)+'" data-instansi="'+escH(item.instansi||'')+'" data-nip="'+escH(item.nip||'')+'"'+sel+'>'+escH(item.nama) + subText + instText + nipText + '</option>';
            });
            return html;
        }

        /* ---- Custom Matcher Select2 (Cari Nama, Jabatan, Instansi, NIP) ---- */
        function matchSelect2Pejabat(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }
            if (typeof data.text === 'undefined') {
                return null;
            }
            var term = params.term.toLowerCase();
            var text = data.text.toLowerCase();

            if (text.indexOf(term) > -1) {
                return data;
            }

            if (data.element) {
                var $el = $(data.element);
                var nama = ($el.data('nama') || '').toString().toLowerCase();
                var jabatan = ($el.data('jabatan') || '').toString().toLowerCase();
                var instansi = ($el.data('instansi') || '').toString().toLowerCase();
                var nip = ($el.data('nip') || '').toString().toLowerCase();
                if (nama.indexOf(term) > -1 || jabatan.indexOf(term) > -1 || instansi.indexOf(term) > -1 || nip.indexOf(term) > -1) {
                    return data;
                }
            }

            return null;
        }

        /* ---- Inisialisasi Select2 pada Kolom Pejabat Step 6 ---- */
        function initSelect2Signers($scope) {
            if (!$.fn || !$.fn.select2) return;
            var $target = $scope ? $scope.find('.signer-ref-select') : $('#signerCardsContainer .signer-ref-select');
            $target.each(function() {
                var $sel = $(this);
                if ($sel.hasClass('select2-hidden-accessible')) {
                    $sel.select2('destroy');
                }
                $sel.select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: '-- Cari atau Pilih Pejabat --',
                    allowClear: false,
                    matcher: matchSelect2Pejabat,
                    language: {
                        noResults: function() {
                            return 'Pejabat tidak ditemukan. Coba ganti sumber (Guru/Kontak/FKPQ) atau ketik manual.';
                        }
                    }
                });
            });
        }

        /* ---- Render semua kartu signer ---- */
        function renderCards() {
            var tmplEl = document.getElementById('tmplSignerCard');
            var tmplHtml = tmplEl ? tmplEl.innerHTML : $('#tmplSignerCard').html();
            var $container = $('#signerCardsContainer');

            // Hancurkan instance select2 lama sebelum kontainer dikosongkan
            $container.find('.signer-ref-select').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });
            $container.empty();

            if (!signers || signers.length === 0) {
                var preset = $('#selectedLayoutPreset').val() || 'preset_2';
                signers = JSON.parse(JSON.stringify(presetDefaults[preset] || presetDefaults.preset_2));
            }

            // Update badge counter di toolbar
            $('#signerCountBadge').text(signers.length + ' Pejabat');

            // Kontrol tombol swap
            var baris1Signers = signers.filter(function(s){ return s.posisi === 'kiri' || s.posisi === 'kanan' || s.posisi === 'tengah'; });
            var hasDuaBaris1 = baris1Signers.length >= 2;
            $('#btnSwapBaris1').toggleClass('d-none', !hasDuaBaris1);

            var baris2Signers = signers.filter(function(s){ return s.posisi && s.posisi.indexOf('mengetahui') >= 0; });
            var hasDuaBaris2 = baris2Signers.length >= 2;
            $('#btnSwapBaris2').toggleClass('d-none', !hasDuaBaris2);

            $.each(signers, function(idx, signer) {
                var uid = idx + 1;
                var html = tmplHtml
                    .replace(/IDX/g, uid)
                    .replace(/name="modeTtd_IDX"/g, 'name="modeTtd_'+uid+'"');
                var $card = $('<div>').append(html).children().first();
                $card.attr('data-signer-index', idx);

                var meta = posisiMeta[signer.posisi] || { label: signer.posisi, badge: 'badge-primary', icon: 'fa-user', sub: 'Pejabat', accent: 'border-accent-kanan' };

                // Accent border sesuai posisi
                $card.removeClass('border-accent-kanan border-accent-kiri border-accent-tengah border-accent-mengetahui').addClass(meta.accent);

                // Badge Posisi & Icon
                var $badge = $card.find('.signer-posisi-badge');
                $badge.removeClass('badge-primary badge-success badge-warning badge-info text-dark').addClass(meta.badge);
                $badge.find('.signer-icon').attr('class', 'fas ' + meta.icon + ' mr-1 signer-icon');
                $badge.find('.label-posisi-text').text('Pejabat #' + (idx + 1) + ' — ' + (signer.posisi === 'kanan' ? 'Kanan' : (signer.posisi === 'kiri' ? 'Kiri' : (signer.posisi === 'tengah' ? 'Tengah' : 'Mengetahui'))));
                $card.find('.signer-subtitle').text(meta.sub);

                // Posisi Select
                $card.find('.signer-posisi').val(signer.posisi);

                // Label Mengetahui
                if (signer.posisi && signer.posisi.indexOf('mengetahui') >= 0) {
                    $card.find('.wrap-label-mengetahui').removeClass('d-none');
                    $card.find('.signer-label-mengetahui').val(signer.label_mengetahui || labelMengetahui);
                } else {
                    $card.find('.wrap-label-mengetahui').addClass('d-none');
                }

                // Sumber Group Buttons
                $card.find('.btn-sumber').removeClass('active');
                $card.find('.btn-sumber[data-sumber="'+signer.sumber+'"]').addClass('active');
                $card.find('.signer-sumber').val(signer.sumber);

                // Dropdown Ref Pejabat
                if (signer.sumber !== 'manual') {
                    $card.find('.wrap-pilih-pejabat').removeClass('d-none');
                    $card.find('.wrap-save-master').addClass('d-none');
                    $card.find('.signer-sumber-badge').text(sumberLabelMap[signer.sumber] || 'Sumber: Database');
                    $card.find('.signer-ref-select').html(buildOptions(signer.sumber, signer.ref_id));

                    // Jika nama kosong tapi ada ref_id, resolve nama otomatis
                    if (!signer.nama && signer.ref_id) {
                        var found = null;
                        if (signer.sumber === 'guru') found = STEP6_DATA.guruList.find(function(g){ return String(g.id) === String(signer.ref_id); });
                        else if (signer.sumber === 'master_tujuan') found = STEP6_DATA.masterList.find(function(m){ return String(m.id) === String(signer.ref_id); });
                        else if (signer.sumber === 'fkpq') found = STEP6_DATA.fkpqList.find(function(f){ return String(f.id) === String(signer.ref_id); });
                        if (found) {
                            signer.nama = found.nama;
                            if (!signer.jabatan) signer.jabatan = found.jabatan;
                            if (!signer.instansi) signer.instansi = found.instansi;
                            if (!signer.nip) signer.nip = found.nip;
                        }
                    }
                } else {
                    $card.find('.wrap-pilih-pejabat').addClass('d-none');
                    $card.find('.wrap-save-master').removeClass('d-none');
                }

                // Text fields
                $card.find('.signer-nama').val(signer.nama || '');
                $card.find('.signer-jabatan').val(signer.jabatan || '');
                $card.find('.signer-instansi').val(signer.instansi || '');
                $card.find('.signer-nip').val(signer.nip || '');

                // Switches
                $card.find('.signer-show-nip').prop('checked', !!signer.show_nip);
                $card.find('.signer-show-instansi').prop('checked', !!signer.show_instansi);
                $card.find('.signer-save-master').prop('checked', !!signer.save_master);

                // Mode TTD (QR vs Manual) & visual pill card
                if (signer.mode_ttd === 'QR') {
                    $card.find('.signer-mode-qr').prop('checked', true);
                    $card.find('.mode-card-qr').addClass('active');
                    $card.find('.mode-card-manual').removeClass('active');
                } else {
                    $card.find('.signer-mode-manual').prop('checked', true);
                    $card.find('.mode-card-manual').addClass('active');
                    $card.find('.mode-card-qr').removeClass('active');
                }

                // Disable reorder buttons at boundaries
                if (idx === 0) $card.find('.btn-move-up').prop('disabled', true).addClass('opacity-50');
                if (idx === signers.length - 1) $card.find('.btn-move-down').prop('disabled', true).addClass('opacity-50');

                // Proteksi hapus: disable jika tinggal 1 signer
                if (signers.length <= 1) {
                    $card.find('.btn-hapus-signer').prop('disabled', true).addClass('opacity-50').attr('title', 'Minimal satu penanda tangan wajib ada');
                }

                $container.append($card);
            });

            // Inisialisasi Select2 pada semua select pejabat yang baru dibuat
            initSelect2Signers($container);

            updatePreview();
            syncBackwardCompat();
        }

        /* ---- Collect state dari DOM ke array signers ---- */
        function collectSigners() {
            var newSigners = [];
            $('#signerCardsContainer .signer-card').each(function() {
                var idx = parseInt($(this).attr('data-signer-index'));
                var old = signers[idx] || {};
                newSigners.push({
                    signer_id       : old.signer_id || ('signer_' + (newSigners.length + 1)),
                    posisi          : $(this).find('.signer-posisi').val(),
                    sumber          : $(this).find('.signer-sumber').val(),
                    ref_id          : $(this).find('.signer-ref-select').val() || '',
                    nama            : $(this).find('.signer-nama').val(),
                    jabatan         : $(this).find('.signer-jabatan').val(),
                    instansi        : $(this).find('.signer-instansi').val(),
                    nip             : $(this).find('.signer-nip').val(),
                    show_nip        : $(this).find('.signer-show-nip').is(':checked'),
                    show_instansi   : $(this).find('.signer-show-instansi').is(':checked'),
                    mode_ttd        : $(this).find('[name^="modeTtd_"]:checked').val() || 'Manual',
                    label_mengetahui: $(this).find('.signer-label-mengetahui').val() || labelMengetahui,
                    save_master     : $(this).find('.signer-save-master').is(':checked'),
                    token_10        : old.token_10 || null,
                });
            });
            return newSigners;
        }

        /* ---- Serialize ke hidden JSON ---- */
        function serializeConfig() {
            signers = collectSigners();
            var preset = $('#selectedLayoutPreset').val() || 'preset_2';
            var config = {
                layout_preset   : preset,
                label_mengetahui: labelMengetahui,
                signers         : signers
            };
            $('#inputPenandaTanganConfig').val(JSON.stringify(config));
            syncBackwardCompat();
            updatePreview();
        }

        /* ---- Backward compat: isi field lama tbl_surat ---- */
        function syncBackwardCompat() {
            var s1 = signers[0] || {};
            var s2 = signers[1] || {};
            var right = signers.find(function(s){ return s.posisi === 'kanan'; }) || s1;
            var left  = signers.find(function(s){ return s.posisi === 'kiri'; })  || s2;
            $('#hiddenIdPenandaTangan1').val(right.ref_id || '');
            $('#hiddenJabatan1').val(right.jabatan || '');
            $('#hiddenModeTtd1').val(right.mode_ttd || 'Manual');
            $('#hiddenIdPenandaTangan2').val(left.ref_id || '');
            $('#hiddenJabatan2').val(left.jabatan || '');
            $('#hiddenModeTtd2').val(left.mode_ttd || 'Manual');
        }

        /* ---- Live Document Preview Box (Simulasi Cetak) ---- */
        function updatePreview() {
            var tempatTtd  = $('#inputTempatTtd').val() || 'Tempat';
            var tanggalTtd = $('#inputTanggalTtd').val() || '';
            if (tanggalTtd) {
                var d = new Date(tanggalTtd);
                var bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                tanggalTtd = tempatTtd + ', ' + d.getDate() + ' ' + (bulan[d.getMonth()]||'') + ' ' + d.getFullYear();
            } else {
                tanggalTtd = tempatTtd + ', [Tanggal Surat]';
            }

            var current = collectSigners();
            var baris1 = current.filter(function(s){ return s.posisi === 'kiri' || s.posisi === 'kanan' || s.posisi === 'tengah'; });
            var baris2 = current.filter(function(s){ return s.posisi && s.posisi.indexOf('mengetahui') >= 0; });

            // Urutkan baris 1: kiri, tengah, kanan
            var order1 = ['kiri','tengah','kanan'];
            baris1.sort(function(a,b){ return order1.indexOf(a.posisi) - order1.indexOf(b.posisi); });

            // Urutkan baris 2: mengetahui_kiri, mengetahui_tengah, mengetahui_kanan
            var order2 = ['mengetahui_kiri','mengetahui_tengah','mengetahui_kanan'];
            baris2.sort(function(a,b){ return order2.indexOf(a.posisi) - order2.indexOf(b.posisi); });

            var html = '<div class="preview-header-ribbon">'
                + '<span><i class="fas fa-file-alt mr-1"></i> SIMULASI CETAK TANDA TANGAN</span>'
                + '<span>KERTAS RESMI</span>'
                + '</div>';

            html += '<div class="preview-tanggal"><strong>' + escH(tanggalTtd) + '</strong></div>';

            function colHtml(s) {
                var nm = s.nama ? '( ' + escH(s.nama) + ' )' : '( ____________________ )';
                var spaceHtml = (s.mode_ttd === 'QR')
                    ? '<div class="preview-space-qr" title="QR Code Digital 10-Karakter Verifikasi Online"><i class="fas fa-qrcode"></i></div>'
                    : '<div class="preview-space-manual" title="Tanda Tangan & Cap Basah Fisik"><span>Tanda Tangan & Cap</span></div>';

                return '<div class="preview-col">'
                    + '<div class="preview-jabatan">' + escH(s.jabatan || 'Penanda Tangan') + '</div>'
                    + (s.show_instansi && s.instansi ? '<div class="preview-instansi">' + escH(s.instansi) + '</div>' : '')
                    + spaceHtml
                    + '<div class="preview-nama">' + nm + '</div>'
                    + (s.show_nip && s.nip ? '<div class="preview-nip">NIP/ID: ' + escH(s.nip) + '</div>' : '')
                    + '</div>';
            }

            // Render Baris 1
            if (baris1.length) {
                html += '<div class="preview-row">';
                if (baris1.length === 1 && baris1[0].posisi === 'kanan') {
                    html += '<div class="preview-col" style="visibility:hidden;"></div>';
                    html += colHtml(baris1[0]);
                } else {
                    baris1.forEach(function(s){ html += colHtml(s); });
                }
                html += '</div>';
            }

            // Render Baris 2 (Mengetahui)
            if (baris2.length) {
                var lbl = baris2[0].label_mengetahui || labelMengetahui;
                html += '<div class="preview-mengetahui-label">' + escH(lbl) + '</div>';
                var colStyle = baris2.length === 1 ? 'justify-content:center;' : 'justify-content:space-between;';
                html += '<div class="preview-row" style="' + colStyle + '">';
                baris2.forEach(function(s){ html += colHtml(s); });
                html += '</div>';
            }

            if (!baris1.length && !baris2.length) {
                html += '<div class="text-center text-muted small py-4"><i class="fas fa-plus-circle mr-1"></i> Belum ada penanda tangan</div>';
            }

            $('#ttdPreviewBox').html(html);
        }

        /* ---- Animasi Flash Preview saat Posisi Ditukar ---- */
        function flashPreview() {
            var $box = $('#ttdPreviewBox');
            $box.removeClass('preview-flash');
            setTimeout(function() { $box.addClass('preview-flash'); }, 10);
        }

        /* ---- Tombol Swap Baris 1 (Kiri ⇄ Kanan) ---- */
        $('#btnSwapBaris1').on('click', function() {
            signers = collectSigners();
            var kiriIdx  = signers.findIndex(function(s){ return s.posisi === 'kiri'; });
            var kananIdx = signers.findIndex(function(s){ return s.posisi === 'kanan'; });
            if (kiriIdx >= 0 && kananIdx >= 0) {
                signers[kiriIdx].posisi  = 'kanan';
                signers[kananIdx].posisi = 'kiri';
            } else if (kiriIdx >= 0) {
                signers[kiriIdx].posisi  = 'kanan';
            } else if (kananIdx >= 0) {
                signers[kananIdx].posisi = 'kiri';
            }
            renderCards();
            serializeConfig();
            flashPreview();
        });

        /* ---- Tombol Swap Baris 2 (Mengetahui Kiri ⇄ Kanan) ---- */
        $('#btnSwapBaris2').on('click', function() {
            signers = collectSigners();
            var mk = signers.findIndex(function(s){ return s.posisi === 'mengetahui_kiri'; });
            var mn = signers.findIndex(function(s){ return s.posisi === 'mengetahui_kanan'; });
            if (mk >= 0 && mn >= 0) {
                signers[mk].posisi = 'mengetahui_kanan';
                signers[mn].posisi = 'mengetahui_kiri';
            }
            renderCards();
            serializeConfig();
            flashPreview();
        });

        /* ---- Geser Urutan (Move Up / Down) ---- */
        $(document).on('click', '.btn-move-up', function() {
            var idx = parseInt($(this).closest('.signer-card').attr('data-signer-index'));
            if (idx > 0) {
                signers = collectSigners();
                var temp = signers[idx - 1];
                signers[idx - 1] = signers[idx];
                signers[idx] = temp;
                renderCards();
                serializeConfig();
            }
        });

        $(document).on('click', '.btn-move-down', function() {
            var idx = parseInt($(this).closest('.signer-card').attr('data-signer-index'));
            if (idx < signers.length - 1) {
                signers = collectSigners();
                var temp = signers[idx + 1];
                signers[idx + 1] = signers[idx];
                signers[idx] = temp;
                renderCards();
                serializeConfig();
            }
        });

        /* ---- Klik Preset Card ---- */
        $(document).on('click', '.preset-card', function() {
            var preset = $(this).data('preset');
            $('#selectedLayoutPreset').val(preset);
            $('.preset-card').removeClass('selected');
            $(this).addClass('selected');

            var defaults = JSON.parse(JSON.stringify(presetDefaults[preset] || presetDefaults.preset_2));
            var current = collectSigners();

            if (current.length > 0) {
                defaults.forEach(function(def, i) {
                    if (current[i]) {
                        def.nama          = current[i].nama          || def.nama;
                        def.jabatan       = current[i].jabatan       || def.jabatan;
                        def.instansi      = current[i].instansi      || def.instansi;
                        def.nip           = current[i].nip           || def.nip;
                        def.ref_id        = current[i].ref_id        || def.ref_id;
                        def.sumber        = current[i].sumber        || def.sumber;
                        def.mode_ttd      = current[i].mode_ttd      || def.mode_ttd;
                        def.show_nip      = typeof current[i].show_nip !== 'undefined' ? current[i].show_nip : def.show_nip;
                        def.show_instansi = typeof current[i].show_instansi !== 'undefined' ? current[i].show_instansi : def.show_instansi;
                    }
                });
            }
            signers = defaults;

            renderCards();
            serializeConfig();
            flashPreview();
        });

        /* ---- Tombol Tambah Signer Baru ---- */
        $('#btnTambahSigner').on('click', function() {
            signers = collectSigners();
            signers.push({
                signer_id       : 'signer_' + (signers.length + 1),
                posisi          : 'kiri',
                sumber          : 'guru',
                ref_id          : '',
                nama            : '',
                jabatan         : '',
                instansi        : '',
                nip             : '',
                show_nip        : false,
                show_instansi   : false,
                mode_ttd        : 'Manual',
                save_master     : false,
                token_10        : null
            });
            renderCards();
            serializeConfig();
            flashPreview();
        });

        /* ---- Event: Ganti Sumber Data (Guru / Master / FKPQ / Manual) ---- */
        $(document).on('click', '#signerCardsContainer .btn-sumber', function() {
            var $card  = $(this).closest('.signer-card');
            var sumber = $(this).data('sumber');
            $card.find('.btn-sumber').removeClass('active');
            $(this).addClass('active');
            $card.find('.signer-sumber').val(sumber);

            var idx = parseInt($card.attr('data-signer-index'));
            $card.find('.signer-sumber-badge').text(sumberLabelMap[sumber] || 'Sumber: Database');

            if (sumber === 'manual') {
                $card.find('.wrap-pilih-pejabat').addClass('d-none');
                $card.find('.wrap-save-master').removeClass('d-none');
            } else {
                $card.find('.wrap-pilih-pejabat').removeClass('d-none');
                $card.find('.wrap-save-master').addClass('d-none');
                var $select = $card.find('.signer-ref-select');
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                $select.html(buildOptions(sumber, signers[idx] ? signers[idx].ref_id : ''));
                initSelect2Signers($card);
            }
            serializeConfig();
        });

        /* ---- Event: Pilih Pejabat dari Dropdown (Select2) ---- */
        $(document).on('change', '#signerCardsContainer .signer-ref-select', function() {
            var $opt  = $(this).find('option:selected');
            var $card = $(this).closest('.signer-card');
            if ($opt.val()) {
                $card.find('.signer-nama').val($opt.data('nama') || '');
                if ($opt.data('jabatan')) {
                    $card.find('.signer-jabatan').val($opt.data('jabatan'));
                }
                if ($opt.data('instansi')) {
                    $card.find('.signer-instansi').val($opt.data('instansi'));
                    $card.find('.signer-show-instansi').prop('checked', true);
                }
                if ($opt.data('nip')) {
                    $card.find('.signer-nip').val($opt.data('nip'));
                    $card.find('.signer-show-nip').prop('checked', true);
                }
            } else {
                $card.find('.signer-nama').val('');
                $card.find('.signer-jabatan').val('');
                $card.find('.signer-instansi').val('');
                $card.find('.signer-nip').val('');
            }
            serializeConfig();
        });

        /* ---- Event: Ganti Posisi Pejabat ---- */
        $(document).on('change', '#signerCardsContainer .signer-posisi', function() {
            var $card  = $(this).closest('.signer-card');
            var posisi = $(this).val();
            var idx    = parseInt($card.attr('data-signer-index'));
            var meta   = posisiMeta[posisi] || { label: posisi, badge: 'badge-primary', icon: 'fa-user', sub: 'Pejabat', accent: 'border-accent-kanan' };

            $card.removeClass('border-accent-kanan border-accent-kiri border-accent-tengah border-accent-mengetahui').addClass(meta.accent);

            var $badge = $card.find('.signer-posisi-badge');
            $badge.removeClass('badge-primary badge-success badge-warning badge-info text-dark').addClass(meta.badge);
            $badge.find('.signer-icon').attr('class', 'fas ' + meta.icon + ' mr-1 signer-icon');
            $badge.find('.label-posisi-text').text('Pejabat #' + (idx + 1) + ' — ' + (posisi === 'kanan' ? 'Kanan' : (posisi === 'kiri' ? 'Kiri' : (posisi === 'tengah' ? 'Tengah' : 'Mengetahui'))));
            $card.find('.signer-subtitle').text(meta.sub);

            if (posisi.indexOf('mengetahui') >= 0) {
                $card.find('.wrap-label-mengetahui').removeClass('d-none');
            } else {
                $card.find('.wrap-label-mengetahui').addClass('d-none');
            }
            serializeConfig();
        });

        /* ---- Event: Mode TTD Click (Pill Cards) ---- */
        $(document).on('click', '#signerCardsContainer .mode-ttd-card', function() {
            var $card = $(this).closest('.signer-card');
            $card.find('.mode-ttd-card').removeClass('active');
            $(this).addClass('active');
            $(this).find('input[type="radio"]').prop('checked', true);
            serializeConfig();
        });

        /* ---- Event: Hapus Signer ---- */
        $(document).on('click', '#signerCardsContainer .btn-hapus-signer', function() {
            var idx = parseInt($(this).closest('.signer-card').attr('data-signer-index'));
            signers = collectSigners();
            if (signers.length <= 1) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Bisa Dihapus',
                        text: 'Surat resmi wajib memiliki minimal satu orang penanda tangan.',
                        confirmButtonColor: '#3085d6',
                    });
                } else {
                    alert('Surat resmi wajib memiliki minimal satu orang penanda tangan.');
                }
                return;
            }
            signers.splice(idx, 1);
            renderCards();
            serializeConfig();
            flashPreview();
        });

        /* ---- Event: Input Text & Switches ---- */
        $(document).on('input change', '#signerCardsContainer .signer-nama, #signerCardsContainer .signer-jabatan, #signerCardsContainer .signer-instansi, #signerCardsContainer .signer-nip, #signerCardsContainer .signer-show-nip, #signerCardsContainer .signer-show-instansi, #signerCardsContainer .signer-save-master, #signerCardsContainer .signer-label-mengetahui', function() {
            serializeConfig();
        });

        /* ---- Event: Tempat & Tanggal TTD Berubah ---- */
        $('#inputTempatTtd, #inputTanggalTtd').on('input change', function() {
            updatePreview();
        });

        /* ---- Submit Trigger Hook ---- */
        $(document).on('click', '.btn-submit-wizard, #btnSubmitFinal, .btn-next', function() {
            serializeConfig();
        });

        /* ---- Ekspor Public API ---- */
        window.Step6Engine = {
            getConfig: function() {
                return {
                    signers: collectSigners(),
                    layout_preset: $('#selectedLayoutPreset').val()
                };
            },
            refresh: function() {
                renderCards();
            }
        };

        /* ---- Init On Ready ---- */
        var initialPreset = $('#selectedLayoutPreset').val() || 'preset_2';
        $('.preset-card[data-preset="'+initialPreset+'"]').addClass('selected');
        renderCards();

    })(window.jQuery || window.$);
});
</script>

<?= $this->endSection(); ?>
