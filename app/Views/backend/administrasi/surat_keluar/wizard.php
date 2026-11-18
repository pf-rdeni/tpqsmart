<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <?= $this->include('backend/administrasi/_partials/page_header'); ?>

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

                    <div class="form-group">
                        <label>Pilih Master Template Surat <span class="text-danger">*</span></label>
                        <select name="IdTemplate" id="selectTemplate" class="form-control form-control-lg font-weight-bold" required>
                            <option value="">-- Pilih Template Surat --</option>
                            <?php foreach ($templates as $t): ?>
                                <option value="<?= $t['Id'] ?>" data-kategori="<?= $t['IdKategori'] ?>" <?= (isset($surat['IdTemplate']) && $surat['IdTemplate'] == $t['Id']) ? 'selected' : '' ?>>
                                    <?= esc($t['NamaTemplate']) ?> (<?= esc($t['NamaKategori']) ?>) - Kertas <?= esc($t['UkuranKertas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="alert alert-info py-3" id="templateInfoBox" style="display: none;">
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
                            <div class="col-md-6 form-group" id="filterKelasWrapper" style="display: none;">
                                <label>Filter Per Kelas Santri:</label>
                                <select id="filterKelasSelect" class="form-control">
                                    <option value="">-- Semua Kelas --</option>
                                    <?php foreach ($kelasList as $k): ?>
                                        <option value="<?= $k['IdKelas'] ?>"><?= esc($k['NamaKelas']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 ml-auto text-right align-self-end">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="btnSelectAllRecipients">
                                    <i class="fas fa-check-double mr-1"></i> Pilih Semua
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnUnselectAllRecipients">
                                    Batal Pilih
                                </button>
                            </div>
                        </div>

                        <div class="border rounded p-2" style="max-height: 280px; overflow-y: auto;" id="recipientCheckboxList">
                            <div class="text-center py-4 text-muted">Memuat data penerima...</div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            Terpilih: <strong id="countSelectedRecipients">0</strong> penerima. File PDF dapat digenerate sekaligus atau per individual dalam file ZIP.
                        </small>
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

    // Pre-populate penerima jika ada (mode edit / duplikat)
    let preloadedRecipients = <?= !empty($penerima) ? json_encode($penerima) : '[]' ?>;
    if (preloadedRecipients && preloadedRecipients.length > 1) {
        $('.tipe-penerima-tab').removeClass('active');
        $('.tipe-penerima-tab[data-type="master"]').addClass('active');
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
        $('.step-panel').removeClass('active');
        $('#step' + stepNum).addClass('active');

        $('.wizard-step').removeClass('active');
        $('.wizard-step[data-step="' + stepNum + '"]').addClass('active');

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

    // Template selection change
    $('#selectTemplate').on('change', function() {
        let id = $(this).val();
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

    // Recipient mode tabs
    $('.tipe-penerima-tab').on('click', function() {
        $('.tipe-penerima-tab').removeClass('active');
        $(this).addClass('active');
        let type = $(this).data('type');

        if (type === 'single') {
            $('#wrapperSingleRecipient').show();
            $('#wrapperMultiRecipient').hide();
            selectedRecipients = [];
        } else {
            $('#wrapperSingleRecipient').hide();
            $('#wrapperMultiRecipient').show();

            if (type === 'wali_santri' || type === 'santri') {
                $('#filterKelasWrapper').show();
            } else {
                $('#filterKelasWrapper').hide();
            }

            loadRecipientList(type);
        }
    });

    $('#filterKelasSelect').on('change', function() {
        loadRecipientList('wali_santri', $(this).val());
    });

    function loadRecipientList(type, idKelas = '') {
        $('#recipientCheckboxList').html('<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat daftar...</div>');

        $.getJSON('<?= base_url('backend/administrasi/surat-keluar/ajax-recipients') ?>', { type: type, id_kelas: idKelas }, function(res) {
            if (res.success && res.data.length > 0) {
                let html = '<div class="row">';
                res.data.forEach(function(item) {
                    html += `
                        <div class="col-md-6 mb-2">
                            <div class="border rounded p-2 recipient-box" style="cursor: pointer;">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input chk-recipient" id="rec_${item.id}" 
                                           value="${item.id}" 
                                           data-nama="${item.nama}" 
                                           data-jabatan="${item.info || ''}" 
                                           data-santri="${item.santri || ''}" 
                                           data-wali="${item.wali || ''}" 
                                           data-kelas="${item.kelas || ''}">
                                    <label class="custom-control-label font-weight-bold" for="rec_${item.id}">
                                        ${item.nama}
                                    </label>
                                </div>
                                <small class="text-muted d-block ml-4">${item.info || ''} ${item.kontak ? ' | ' + item.kontak : ''}</small>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                $('#recipientCheckboxList').html(html);
                bindRecipientCheckboxEvents();
            } else {
                $('#recipientCheckboxList').html('<div class="text-center py-4 text-muted">Tidak ada data penerima yang ditemukan.</div>');
            }
        });
    }

    function bindRecipientCheckboxEvents() {
        $('.chk-recipient').on('change', function() {
            rebuildSelectedRecipients();
        });
    }

    function rebuildSelectedRecipients() {
        selectedRecipients = [];
        $('.chk-recipient:checked').each(function() {
            selectedRecipients.push({
                id_ref: $(this).val(),
                nama: $(this).data('nama'),
                jabatan: $(this).data('jabatan'),
                santri: $(this).data('santri'),
                wali: $(this).data('wali'),
                kelas: $(this).data('kelas'),
                tipe: 'Massal'
            });
            $(this).closest('.recipient-box').addClass('recipient-item-selected');
        });
        $('.chk-recipient:not(:checked)').each(function() {
            $(this).closest('.recipient-box').removeClass('recipient-item-selected');
        });
        $('#countSelectedRecipients').text(selectedRecipients.length);
    }

    $('#btnSelectAllRecipients').on('click', function() {
        $('.chk-recipient').prop('checked', true);
        rebuildSelectedRecipients();
    });

    $('#btnUnselectAllRecipients').on('click', function() {
        $('.chk-recipient').prop('checked', false);
        rebuildSelectedRecipients();
    });

    // Auto Number Generation
    function triggerAutoNumber() {
        let tplId = $('#selectTemplate').val();
        let katId = $('#selectTemplate option:selected').data('kategori');
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
