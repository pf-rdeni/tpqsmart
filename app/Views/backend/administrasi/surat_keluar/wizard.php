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
    </style>

    <!-- Wizard Step Indicator -->
    <div class="wizard-steps d-none d-md-flex">
        <div class="wizard-step active" data-step="1">
            <span class="step-dot">1</span>
            <span class="step-title">1. Template</span>
        </div>
        <div class="wizard-step" data-step="2">
            <span class="step-dot">2</span>
            <span class="step-title">2. Kop & Layout</span>
        </div>
        <div class="wizard-step" data-step="3">
            <span class="step-dot">3</span>
            <span class="step-title">3. Penerima</span>
        </div>
        <div class="wizard-step" data-step="4">
            <span class="step-dot">4</span>
            <span class="step-title">4. Perihal & Isi</span>
        </div>
        <div class="wizard-step" data-step="5">
            <span class="step-dot">5</span>
            <span class="step-title">5. Nomor Surat</span>
        </div>
        <div class="wizard-step" data-step="6">
            <span class="step-dot">6</span>
            <span class="step-title">6. Tanda Tangan</span>
        </div>
        <div class="wizard-step" data-step="7">
            <span class="step-dot">7</span>
            <span class="step-title">7. Preview</span>
        </div>
        <div class="wizard-step" data-step="8">
            <span class="step-dot">8</span>
            <span class="step-title">8. Finalisasi</span>
        </div>
    </div>

    <!-- Mobile Step Indicator Header -->
    <div class="card d-block d-md-none mb-3 bg-primary text-white">
        <div class="card-body py-2 text-center">
            <strong id="mobileStepText">Langkah 1 dari 8: Pilih Template</strong>
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
        <!-- STEP 1: PILIH TEMPLATE -->
        <!-- ============================================================== -->
        <div class="step-panel active" id="step1">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-file-invoice mr-2"></i>Langkah 1: Pilih Template Surat</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Pilih salah satu template surat sebagai dasar format dan layout. Pengaturan kop, margin, dan nomor surat akan otomatis dimuat.</p>

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
                        <div class="row" id="templateCardGrid" style="max-height: 420px; overflow-y: auto;">
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

                    <div class="alert alert-info py-3 mt-3" id="templateInfoBox" style="display: none;">
                        <div class="font-weight-bold mb-1"><i class="fas fa-info-circle mr-1"></i> Detail Template Terpilih:</div>
                        <div id="templateDesc" class="text-muted small"></div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-end align-items-center flex-wrap">
                    <button type="button" class="btn btn-primary px-4 btn-next <?= !empty($isEdit) ? 'mr-2' : '' ?> mb-1" data-next="2">
                        Lanjut ke Kop & Layout <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                    <?php if (!empty($isEdit)): ?>
                        <button type="button" class="btn btn-success px-4 btn-submit-wizard font-weight-bold mb-1" title="Simpan Perubahan Surat">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- STEP 2: KOP & LAYOUT -->
        <!-- ============================================================== -->
        <div class="step-panel" id="step2">
            <div class="card card-info card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-stamp mr-2"></i>Langkah 2: Konfigurasi Kop Surat & Layout Kertas</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Sumber Kop -->
                        <div class="col-md-6 form-group">
                            <label>Sumber Kop Surat</label>
                            <?php $currentSumberKop = $templateSurat['SumberKop'] ?? ($surat['SumberKop'] ?? 'TPQ'); ?>
                            <select name="SumberKop" id="optSumberKop" class="form-control">
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
                            <label>Pilih Gambar Kop Kepanitiaan</label>
                            <select name="IdKopCustom" id="optIdKopCustom" class="form-control">
                                <option value="">-- Pilih Kop Kepanitiaan --</option>
                                <?php foreach ($kopCustom as $kc): ?>
                                    <option value="<?= $kc['Id'] ?>" <?= ($currentIdKopCustom == $kc['Id']) ? 'selected' : '' ?>><?= esc($kc['NamaKop']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Ukuran Kertas</label>
                            <?php $currentKertas = $templateSurat['UkuranKertas'] ?? 'A4'; ?>
                            <select name="UkuranKertas" id="optUkuranKertas" class="form-control">
                                <option value="A4" <?= ($currentKertas === 'A4') ? 'selected' : '' ?>>A4 (210 x 297 mm)</option>
                                <option value="F4" <?= ($currentKertas === 'F4') ? 'selected' : '' ?>>F4 / Folio (215 x 330 mm)</option>
                                <option value="A5" <?= ($currentKertas === 'A5') ? 'selected' : '' ?>>A5 (148 x 210 mm)</option>
                                <option value="Legal" <?= ($currentKertas === 'Legal') ? 'selected' : '' ?>>Legal</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Orientasi</label>
                            <?php $currentOrientasi = $templateSurat['Orientasi'] ?? 'Portrait'; ?>
                            <select name="Orientasi" id="optOrientasi" class="form-control">
                                <option value="Portrait" <?= ($currentOrientasi === 'Portrait') ? 'selected' : '' ?>>Portrait (Tegak)</option>
                                <option value="Landscape" <?= ($currentOrientasi === 'Landscape') ? 'selected' : '' ?>>Landscape (Mendatar)</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Jenis & Ukuran Font</label>
                            <?php 
                                $currentFontFamily = $templateSurat['FontFamily'] ?? 'Times New Roman'; 
                                $currentFontSize   = $templateSurat['FontSize'] ?? '12';
                            ?>
                            <div class="input-group">
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
                <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap">
                    <button type="button" class="btn btn-secondary btn-prev mb-1" data-prev="1"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
                    <div class="d-flex align-items-center flex-wrap mb-1">
                        <button type="button" class="btn btn-info px-4 btn-next <?= !empty($isEdit) ? 'mr-2' : '' ?>" data-next="3">Lanjut ke Penerima <i class="fas fa-arrow-right ml-1"></i></button>
                        <?php if (!empty($isEdit)): ?>
                            <button type="button" class="btn btn-success px-4 btn-submit-wizard font-weight-bold" title="Simpan Perubahan Surat"><i class="fas fa-save mr-1"></i> Simpan</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- STEP 3: PILIH TUJUAN PENERIMA -->
        <!-- ============================================================== -->
        <div class="step-panel" id="step3">
            <div class="card card-success card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-users mr-2"></i>Langkah 3: Pilih Tujuan / Penerima Surat</h3>
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
                <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap">
                    <button type="button" class="btn btn-secondary btn-prev mb-1" data-prev="2"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
                    <div class="d-flex align-items-center flex-wrap mb-1">
                        <button type="button" class="btn btn-success px-4 btn-next <?= !empty($isEdit) ? 'mr-2' : '' ?>" data-next="4">Lanjut ke Perihal & Isi <i class="fas fa-arrow-right ml-1"></i></button>
                        <?php if (!empty($isEdit)): ?>
                            <button type="button" class="btn btn-success px-4 btn-submit-wizard font-weight-bold" title="Simpan Perubahan Surat"><i class="fas fa-save mr-1"></i> Simpan</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- STEP 4: PERIHAL & KONTEN ISI SURAT -->
        <!-- ============================================================== -->
        <div class="step-panel" id="step4">
            <div class="card card-warning card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Langkah 4: Perihal, Tanggal, & Konten Isi Surat</h3>
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
                <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap">
                    <button type="button" class="btn btn-secondary btn-prev mb-1" data-prev="3"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
                    <div class="d-flex align-items-center flex-wrap mb-1">
                        <button type="button" class="btn btn-warning px-4 btn-next <?= !empty($isEdit) ? 'mr-2' : '' ?>" data-next="5">Lanjut ke Nomor Surat <i class="fas fa-arrow-right ml-1"></i></button>
                        <?php if (!empty($isEdit)): ?>
                            <button type="button" class="btn btn-success px-4 btn-submit-wizard font-weight-bold" title="Simpan Perubahan Surat"><i class="fas fa-save mr-1"></i> Simpan</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- STEP 5: NOMOR SURAT -->
        <!-- ============================================================== -->
        <div class="step-panel" id="step5">
            <div class="card card-secondary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-sort-numeric-down mr-2"></i>Langkah 5: Penomoran Surat Otomatis</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Format Penomoran yang Digunakan</label>
                        <div class="input-group">
                            <input type="text" name="FormatNomor" id="inputFormatNomor" class="form-control" value="<?= esc($templateSurat['FormatNomor'] ?? '[NO:3]/[KODE-TPQ]/[KODE-KAT]/[BLN-ROMAWI]/[TAHUN]') ?>">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" id="btnHitungNomor">
                                    <i class="fas fa-sync-alt mr-1"></i> Hitung Nomor Otomatis
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nomor Surat Final (Hasil Generate / Bisa Diedit Manual) <span class="text-danger">*</span></label>
                        <input type="text" name="NomorSurat" id="inputNomorSurat" class="form-control form-control-lg font-weight-bold text-primary" value="<?= esc($surat['NomorSurat'] ?? '') ?>" required>
                        <input type="hidden" name="UrutanNomor" id="inputUrutanNomor" value="<?= esc($surat['UrutanNomor'] ?? '1') ?>">
                        <small class="text-muted">Nomor surat ini akan dicetak pada lembar surat dan disimpan dalam arsip penomoran.</small>
                    </div>

                    <div class="alert alert-light border mt-3">
                        <i class="fas fa-shield-alt text-success mr-1"></i> Sistem otomatis mencegah nomor ganda dengan mengunci counter saat Anda menekan tombol Simpan/Finalisasi.
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap">
                    <button type="button" class="btn btn-secondary btn-prev mb-1" data-prev="4"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
                    <div class="d-flex align-items-center flex-wrap mb-1">
                        <button type="button" class="btn btn-secondary px-4 btn-next <?= !empty($isEdit) ? 'mr-2' : '' ?>" data-next="6">Lanjut ke Tanda Tangan <i class="fas fa-arrow-right ml-1"></i></button>
                        <?php if (!empty($isEdit)): ?>
                            <button type="button" class="btn btn-success px-4 btn-submit-wizard font-weight-bold" title="Simpan Perubahan Surat"><i class="fas fa-save mr-1"></i> Simpan</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- STEP 6: PENANDA TANGAN (TTD & QR CODE) -->
        <!-- ============================================================== -->
        <div class="step-panel" id="step6">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-file-signature mr-2"></i>Langkah 6: Pengaturan Tanda Tangan & QR Code</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6 form-group">
                            <label>Tempat Tanda Tangan</label>
                            <input type="text" name="TempatTtd" id="inputTempatTtd" class="form-control" value="<?= esc($surat['TempatTtd'] ?? 'Bintan') ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tanggal Tanda Tangan</label>
                            <input type="date" name="TanggalTtd" id="inputTanggalTtd" class="form-control" value="<?= esc($surat['TanggalTtd'] ?? date('Y-m-d')) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <!-- TTD 1: Mengetahui (Kepala Lembaga) -->
                        <div class="col-md-6">
                            <div class="card border bg-light">
                                <div class="card-header bg-white font-weight-bold py-2">
                                    <i class="fas fa-user-check text-primary mr-1"></i> Penanda Tangan 1 (Mengetahui)
                                </div>
                                <div class="card-body py-3">
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold">Pilih Guru / Kepala:</label>
                                        <select name="IdPenandaTangan1" id="selectTtd1" class="form-control form-control-sm">
                                            <option value="">-- Pilih Guru / Kepala --</option>
                                            <?php foreach ($guruList as $g): ?>
                                                <option value="<?= $g['IdGuru'] ?>" data-jabatan="<?= esc($g['Jabatan'] ?? '') ?>" <?= (isset($surat['IdPenandaTangan1']) && (string)$surat['IdPenandaTangan1'] === (string)$g['IdGuru']) ? 'selected' : '' ?>>
                                                    <?= esc($g['Nama']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold">Jabatan:</label>
                                        <input type="text" name="Jabatan1" id="inputJabatan1" class="form-control form-control-sm" value="<?= esc($surat['Jabatan1'] ?? 'Kepala TPQ') ?>">
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold d-block">Mode Tanda Tangan:</label>
                                        <?php $m1 = $surat['ModeTtd1'] ?? 'Manual'; ?>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="modeTtd1_qr" name="ModeTtd1" value="QR" class="custom-control-input" <?= ($m1 === 'QR') ? 'checked' : '' ?>>
                                            <label class="custom-control-label small" for="modeTtd1_qr"><i class="fas fa-qrcode"></i> QR Code Digital</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="modeTtd1_manual" name="ModeTtd1" value="Manual" class="custom-control-input" <?= ($m1 !== 'QR') ? 'checked' : '' ?>>
                                            <label class="custom-control-label small" for="modeTtd1_manual"><i class="fas fa-pen"></i> Blank Manual (Fisik)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TTD 2: Yang Membuat / Sekretaris -->
                        <div class="col-md-6">
                            <div class="card border bg-light">
                                <div class="card-header bg-white font-weight-bold py-2">
                                    <i class="fas fa-user-edit text-success mr-1"></i> Penanda Tangan 2 (Pembuat / Sekretaris)
                                </div>
                                <div class="card-body py-3">
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold">Pilih Guru / Pembuat:</label>
                                        <select name="IdPenandaTangan2" id="selectTtd2" class="form-control form-control-sm">
                                            <option value="">-- Pilih Guru / Sekretaris (Opsional) --</option>
                                            <?php foreach ($guruList as $g): ?>
                                                <option value="<?= $g['IdGuru'] ?>" data-jabatan="<?= esc($g['Jabatan'] ?? '') ?>" <?= (isset($surat['IdPenandaTangan2']) && (string)$surat['IdPenandaTangan2'] === (string)$g['IdGuru']) ? 'selected' : '' ?>>
                                                    <?= esc($g['Nama']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold">Jabatan:</label>
                                        <input type="text" name="Jabatan2" id="inputJabatan2" class="form-control form-control-sm" value="<?= esc($surat['Jabatan2'] ?? 'Sekretaris') ?>">
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold d-block">Mode Tanda Tangan:</label>
                                        <?php $m2 = $surat['ModeTtd2'] ?? 'Manual'; ?>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="modeTtd2_qr" name="ModeTtd2" value="QR" class="custom-control-input" <?= ($m2 === 'QR') ? 'checked' : '' ?>>
                                            <label class="custom-control-label small" for="modeTtd2_qr"><i class="fas fa-qrcode"></i> QR Code Digital</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="modeTtd2_manual" name="ModeTtd2" value="Manual" class="custom-control-input" <?= ($m2 !== 'QR') ? 'checked' : '' ?>>
                                            <label class="custom-control-label small" for="modeTtd2_manual"><i class="fas fa-pen"></i> Blank Manual (Fisik)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap">
                    <button type="button" class="btn btn-secondary btn-prev mb-1" data-prev="5"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
                    <div class="d-flex align-items-center flex-wrap mb-1">
                        <button type="button" class="btn btn-primary px-4 btn-next <?= !empty($isEdit) ? 'mr-2' : '' ?>" data-next="7">Lanjut ke Preview <i class="fas fa-arrow-right ml-1"></i></button>
                        <?php if (!empty($isEdit)): ?>
                            <button type="button" class="btn btn-success px-4 btn-submit-wizard font-weight-bold" title="Simpan Perubahan Surat"><i class="fas fa-save mr-1"></i> Simpan</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- STEP 7: LIVE PREVIEW HALAMAN SURAT -->
        <!-- ============================================================== -->
        <div class="step-panel" id="step7">
            <div class="card card-dark card-outline shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-eye mr-2"></i>Langkah 7: Pratinjau Tampilan Surat Lengkap</h3>
                    <button type="button" class="btn btn-outline-light btn-sm" id="btnRefreshPreview">
                        <i class="fas fa-sync-alt mr-1"></i> Refresh Pratinjau
                    </button>
                </div>
                <div class="card-body p-3 bg-secondary" style="min-height: 500px; display: flex; justify-content: center;">
                    <div id="previewWrapper" class="bg-white shadow rounded" style="max-width: 850px; width: 100%; min-height: 800px; overflow: hidden;">
                        <iframe id="previewIframe" style="width: 100%; height: 900px; border: none; display: block; background: #fff;" srcdoc=""></iframe>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap">
                    <button type="button" class="btn btn-secondary btn-prev mb-1" data-prev="6"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
                    <div class="d-flex align-items-center flex-wrap mb-1">
                        <button type="button" class="btn btn-success px-4 btn-next <?= !empty($isEdit) ? 'mr-2' : '' ?>" data-next="8">Lanjut ke Finalisasi <i class="fas fa-arrow-right ml-1"></i></button>
                        <?php if (!empty($isEdit)): ?>
                            <button type="button" class="btn btn-success px-4 btn-submit-wizard font-weight-bold" title="Simpan Perubahan Surat"><i class="fas fa-save mr-1"></i> Simpan</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- STEP 8: FINALISASI & OUTPUT PDF -->
        <!-- ============================================================== -->
        <div class="step-panel" id="step8">
            <div class="card card-success card-outline shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-check-circle mr-2"></i>Langkah 8: Finalisasi Surat & Unduh PDF</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-success py-3">
                        <h5 class="font-weight-bold"><i class="fas fa-check-double mr-2"></i>Surat Siap Difinalisasi</h5>
                        <p class="mb-0">Periksa kembali data di bawah ini. Setelah difinalisasi, Anda dapat langsung mencetak PDF atau mengunduh ZIP untuk penerima massal.</p>
                    </div>

                    <table class="table table-bordered mb-4">
                        <tr>
                            <th style="width: 200px;" class="bg-light">Nomor Surat</th>
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

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Catatan Efisiensi:</strong> File PDF di-generate secara <em>on-demand (streaming)</em> dan tidak disimpan di server untuk menghemat kapasitas storage. Anda dapat mencetak ulang kapan saja dari menu <strong>Arsip Surat Keluar</strong>.
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap">
                    <button type="button" class="btn btn-secondary btn-prev mb-1" data-prev="7"><i class="fas fa-arrow-left mr-1"></i> Kembali</button>
                    <button type="button" class="btn btn-success <?= !empty($isEdit) ? '' : 'btn-lg' ?> px-4 font-weight-bold btn-submit-wizard mb-1" id="btnSubmitFinal" title="Simpan Perubahan Surat">
                        <?= !empty($isEdit) ? '<i class="fas fa-save mr-1"></i> Simpan' : '<i class="fas fa-save mr-2"></i> Finalisasi & Simpan Surat' ?>
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

        $('#mobileStepText').text('Langkah ' + stepNum + ' dari 8: ' + $('.wizard-step[data-step="' + stepNum + '"] .step-title').text());
        currentStep = stepNum;

        if (stepNum === 7) {
            loadPreviewHtml();
        } else if (stepNum === 8) {
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

        let ttd1 = $('#selectTtd1 option:selected').text();
        let ttd2 = $('#selectTtd2 option:selected').text();
        $('#summaryTtd').text(ttd1 !== '-- Pilih Guru / Kepala --' ? ttd1 : '-');
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

<?= $this->endSection(); ?>
