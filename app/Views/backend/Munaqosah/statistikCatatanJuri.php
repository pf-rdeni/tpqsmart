<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<?php
$tahunList = !empty($tahunAjaranList) ? $tahunAjaranList : [$current_tahun_ajaran];
$defaultTahun = in_array($current_tahun_ajaran, $tahunList, true) ? $current_tahun_ajaran : $tahunList[0];
?>
<section class="content">
    <div class="container-fluid">

        <!-- FILTER -->
        <div class="card card-outline card-primary stat-card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h3 class="card-title mb-0 font-weight-bold"><i class="fas fa-clipboard-check text-primary mr-1"></i> Analisis Catatan Juri &amp; Kesalahan Generik</h3>
                <div class="btn-group btn-group-toggle mt-1 mt-md-0" data-toggle="buttons" id="modeToggleGroup">
                    <label class="btn btn-sm btn-outline-primary active" id="lblModeGabungan">
                        <input type="radio" name="analisisMode" id="optModeGabungan" value="gabungan" checked>
                        <i class="fas fa-layer-group mr-1"></i> Mode Gabungan (Kesimpulan)
                    </label>
                    <label class="btn btn-sm btn-outline-primary" id="lblModeVs">
                        <input type="radio" name="analisisMode" id="optModeVs" value="vs">
                        <i class="fas fa-columns mr-1"></i> Mode Juri 1 vs Juri 2
                    </label>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-12 mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="small mb-0 font-weight-bold" for="filterTahunAjaran">Tahun Ajaran <span class="text-muted font-weight-normal">(Multiple)</span></label>
                            <div>
                                <a href="#" id="taSemua" class="small font-weight-bold mr-1">Semua</a> |
                                <a href="#" id="taCurrent" class="small mx-1">T.A Aktif</a> |
                                <a href="#" id="taReset" class="small ml-1 text-muted">Kosongkan</a>
                            </div>
                        </div>
                        <select id="filterTahunAjaran" class="form-control form-control-sm select2" multiple="multiple" data-placeholder="Pilih Tahun Ajaran..." style="width: 100%;">
                            <?php foreach ($tahunList as $t): ?>
                                <?php
                                $label = preg_match('/^\d{8}$/', (string)$t) ? 'T.A ' . substr($t, 2, 2) . '/' . substr($t, 6, 2) : $t;
                                ?>
                                <option value="<?= esc($t) ?>" <?= $t === $defaultTahun ? 'selected' : '' ?>>
                                    <?= esc($label) ?> (<?= esc($t) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-2">
                        <label class="small mb-1 font-weight-bold" for="filterTpq">TPQ</label>
                        <select id="filterTpq" class="form-control form-control-sm" <?= $isAdmin ? '' : 'disabled' ?>>
                            <?php if ($isAdmin): ?><option value="0">Semua TPQ</option><?php endif; ?>
                            <?php if (!empty($tpqDropdown)): foreach ($tpqDropdown as $tpq): ?>
                                    <option value="<?= esc($tpq['IdTpq']) ?>"><?= esc($tpq['NamaTpq']) ?></option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="small mb-0 font-weight-bold" for="filterTypeUjian">Type Ujian <span class="text-muted font-weight-normal">(Multiple)</span></label>
                            <div>
                                <a href="#" id="typeSemua" class="small font-weight-bold mr-1">Semua</a> |
                                <a href="#" id="typeReset" class="small ml-1 text-muted">Kosongkan</a>
                            </div>
                        </div>
                        <select id="filterTypeUjian" class="form-control form-control-sm select2" multiple="multiple" data-placeholder="Pilih Type Ujian..." style="width: 100%;">
                            <option value="pra-munaqosah" selected>Pra-Munaqosah</option>
                            <option value="munaqosah" <?= $isAdmin ? 'selected' : '' ?>>Munaqosah</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-12 mb-2 d-flex align-items-end">
                        <div class="w-100">
                            <button id="btnMuat" class="btn btn-primary btn-sm btn-block font-weight-bold"><i class="fas fa-sync-alt mr-1"></i> Muat Data</button>
                            <button id="btnPdf" class="btn btn-danger btn-sm btn-block mt-1 font-weight-bold" disabled><i class="fas fa-file-pdf mr-1"></i> Export PDF</button>
                        </div>
                    </div>
                </div>

                <!-- PILIHAN KATEGORI MATERI -->
                <div class="row mt-2" id="rowMateriSelect" style="display:none;">
                    <div class="col-12">
                        <label class="small mb-1 font-weight-bold text-dark"><i class="fas fa-book-open text-primary mr-1"></i> Pilih Kategori Materi Ujian:</label>
                        <div class="chip-wrap" id="kategoriMateriChips"></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="statLoading" class="text-center py-5" style="display:none;">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="small text-muted mt-2 font-weight-bold">Menganalisis data catatan juri…</div>
        </div>
        <div id="statEmpty" class="alert alert-info" style="display:none;"></div>

        <!-- CONTENT SECTION -->
        <div id="statContent" style="display:none;">
            <!-- SUMMARY CARDS -->
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="sum-box bg-gradient-primary">
                        <div class="val" id="sumSantriTeruji">0</div>
                        <div class="lbl">Santri Teruji</div>
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="sum-box bg-gradient-danger">
                        <div class="val" id="sumTotalKesalahan">0</div>
                        <div class="lbl">Total Catatan Kesalahan</div>
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="sum-box bg-gradient-success">
                        <div class="val" id="sumKesepakatanJuri">0%</div>
                        <div class="lbl">Tingkat Kesepakatan Juri</div>
                        <i class="fas fa-handshake"></i>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="sum-box bg-gradient-warning text-dark">
                        <div class="val" id="sumTopKesalahan" style="font-size:1.15rem; font-weight:bold; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">-</div>
                        <div class="lbl text-dark font-weight-bold">Kesalahan Terbanyak (Top 1)</div>
                        <i class="fas fa-bullseye"></i>
                    </div>
                </div>
            </div>

            <!-- VIEW: MODE GABUNGAN -->
            <div id="viewGabungan">
                <div class="row">
                    <div class="col-lg-7 mb-3">
                        <div class="card chart-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h5 class="card-title font-weight-bold mb-0 text-dark">
                                    <i class="fas fa-chart-bar text-primary mr-1"></i> Frekuensi Kesalahan Generik
                                </h5>
                                <div class="d-flex align-items-center mt-1 mt-md-0">
                                    <label class="small mb-0 mr-1 text-muted">Metode:</label>
                                    <select id="selGabunganMethod" class="form-control form-control-sm" style="width:190px;">
                                        <option value="union" selected>Dicatat Salah Satu Juri</option>
                                        <option value="intersection">Disepakati Kedua Juri</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-box" style="height: 380px;">
                                    <canvas id="chartGabungan"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 mb-3">
                        <div class="card chart-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title font-weight-bold mb-0 text-dark">
                                    <i class="fas fa-list-ol text-primary mr-1"></i> Daftar Frekuensi
                                </h5>
                                <button class="btn btn-xs btn-outline-success" id="btnCsvGabungan"><i class="fas fa-file-csv mr-1"></i> CSV</button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                                    <table class="table table-sm table-striped table-hover mb-0" id="tblGabungan">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width:35px">No</th>
                                                <th>Nama Kesalahan Generik</th>
                                                <th class="text-center" style="width:65px">Santri</th>
                                                <th class="text-center" style="width:65px">% Teruji</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbGabunganBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VIEW: MODE JURI 1 VS JURI 2 -->
            <div id="viewVs" style="display:none;">
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <div class="card chart-card">
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h5 class="card-title font-weight-bold mb-0 text-dark">
                                    <i class="fas fa-balance-scale text-primary mr-1"></i> Perbandingan Frekuensi Kesalahan (Juri 1 vs Juri 2)
                                </h5>
                                <div class="badge badge-light border px-2 py-1">
                                    <span class="d-inline-block rounded-circle mr-1" style="width:10px;height:10px;background:#3b82f6;"></span> Juri 1
                                    <span class="d-inline-block rounded-circle ml-3 mr-1" style="width:10px;height:10px;background:#f97316;"></span> Juri 2
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-box" style="height: 360px;">
                                    <canvas id="chartVs"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 mb-3">
                        <div class="card chart-card">
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h5 class="card-title font-weight-bold mb-0 text-dark">
                                    <i class="fas fa-table text-primary mr-1"></i> Tabel Analisis Komparasi Juri
                                </h5>
                                <button class="btn btn-xs btn-outline-success" id="btnCsvVs"><i class="fas fa-file-csv mr-1"></i> Export CSV</button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-striped table-hover mb-0 text-nowrap" id="tblVs">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width:35px" class="text-center">No</th>
                                                <th>Kode</th>
                                                <th>Nama Kesalahan Generik</th>
                                                <th class="text-center table-primary" style="width:80px">Juri 1</th>
                                                <th class="text-center table-warning" style="width:80px">Juri 2</th>
                                                <th class="text-center table-success" style="width:100px">Keduanya</th>
                                                <th class="text-center" style="width:90px">Hanya J1</th>
                                                <th class="text-center" style="width:90px">Hanya J2</th>
                                                <th class="text-center" style="width:110px">Kesepakatan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbVsBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DETAIL PER SANTRI MATRIX / TABLE -->
            <div class="card chart-card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:10px;">
                    <div class="d-flex align-items-center flex-wrap" style="gap:10px;">
                        <h5 class="card-title font-weight-bold mb-0 text-dark">
                            <i class="fas fa-users-cog text-primary mr-1"></i> Detail Catatan per Santri
                        </h5>
                        <select id="selFilterKesalahanSantri" class="form-control form-control-sm ml-md-2" style="max-width:240px;">
                            <option value="all" selected>Semua Jenis Kesalahan</option>
                        </select>
                        <select id="selFilterStatusSantri" class="form-control form-control-sm" style="width:170px;">
                            <option value="all" selected>Semua Status Juri</option>
                            <option value="ada_kesalahan">Ada Catatan Kesalahan</option>
                            <option value="sepakat_sama">Sepakat Sama Persis</option>
                            <option value="ada_perbedaan">Ada Beda Pendapat</option>
                            <option value="bersih">Tanpa Catatan (Lancar)</option>
                        </select>
                    </div>
                    <div class="d-flex align-items-center flex-wrap" style="gap:10px;">
                        <div class="custom-control custom-switch" title="Tampilkan/sembunyikan kolom perolehan nilai santri">
                            <input type="checkbox" class="custom-control-input" id="chkShowScoreSantri">
                            <label class="custom-control-label small font-weight-bold text-muted mb-0" for="chkShowScoreSantri" style="cursor:pointer;"><i class="fas fa-calculator mr-1"></i>Tampilkan Nilai</label>
                        </div>
                        <div class="custom-control custom-switch" title="Sembunyikan nama santri jika dibagikan ke umum">
                            <input type="checkbox" class="custom-control-input" id="chkPrivacySantri">
                            <label class="custom-control-label small font-weight-bold text-muted mb-0" for="chkPrivacySantri" style="cursor:pointer;">Privasi Nama</label>
                        </div>
                        <input type="search" id="inpSearchSantri" class="form-control form-control-sm" placeholder="Cari santri / no peserta…" style="width:200px;">
                        <button class="btn btn-xs btn-outline-success" id="btnCsvSantri"><i class="fas fa-file-csv mr-1"></i> CSV</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                        <table class="table table-sm table-bordered table-hover mb-0" id="tblSantriDetail">
                            <thead class="thead-light" id="tblSantriDetailThead" style="position: sticky; top: 0; z-index: 2;">
                                <tr>
                                    <th style="width:40px" class="text-center">No</th>
                                    <th style="width:100px">No Peserta</th>
                                    <th>Nama Santri</th>
                                    <th>TPQ</th>
                                    <th style="width:90px" class="text-center">Tahun/Type</th>
                                    <th style="min-width:200px">Catatan Juri 1</th>
                                    <th style="min-width:200px">Catatan Juri 2</th>
                                    <th style="width:120px" class="text-center">Status Juri</th>
                                </tr>
                            </thead>
                            <tbody id="tbSantriDetailBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- CATATAN TEKS BEBAS (COLLAPSIBLE) -->
            <div class="card card-outline card-secondary stat-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center" data-toggle="collapse" data-target="#collapseTeksBebas" style="cursor:pointer;">
                    <h5 class="card-title font-weight-bold mb-0 text-dark">
                        <i class="fas fa-comment-dots text-secondary mr-1"></i> Catatan Teks Bebas / Khusus Juri (Opsional)
                    </h5>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool"><i class="fas fa-chevron-down"></i></button>
                    </div>
                </div>
                <div id="collapseTeksBebas" class="collapse">
                    <div class="card-body p-0">
                        <div class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span class="small text-muted font-italic">Catatan tambahan yang diketik manual oleh juri di luar checkbox generik.</span>
                            <input type="search" id="inpSearchTeks" class="form-control form-control-sm" placeholder="Cari isi catatan…" style="width:220px;">
                        </div>
                        <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                            <table class="table table-sm table-bordered table-striped mb-0" id="tblTeksBebas">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width:40px" class="text-center">No</th>
                                        <th style="width:110px">No Peserta</th>
                                        <th>Nama Santri</th>
                                        <th>TPQ</th>
                                        <th style="width:40%">Teks Catatan Juri 1</th>
                                        <th style="width:40%">Teks Catatan Juri 2</th>
                                    </tr>
                                </thead>
                                <tbody id="tbTeksBebasBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL DETAIL SANTRI DARI BARIS KESALAHAN -->
        <div class="modal fade" id="modalDetailKesalahan" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                <div class="modal-content" style="border-radius:12px;">
                    <div class="modal-header bg-primary text-white py-2 px-3">
                        <h6 class="modal-title font-weight-bold mb-0" id="mdkTitle">Daftar Santri dengan Kesalahan Terpilih</h6>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="alert alert-light border py-1 px-2 mb-2 small" id="mdkSub"></div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped mb-0" id="tblMdk">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width:40px" class="text-center">No</th>
                                        <th>No Peserta</th>
                                        <th>Nama Santri</th>
                                        <th>TPQ</th>
                                        <th class="text-center">Dicatat Oleh</th>
                                    </tr>
                                </thead>
                                <tbody id="tbMdkBody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL PENGATURAN EXPORT PDF -->
        <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 480px;">
                <div class="modal-content" style="border-radius:12px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,.2);">
                    <div class="modal-header bg-danger text-white py-2 px-3">
                        <h6 class="modal-title font-weight-bold mb-0"><i class="fas fa-file-pdf mr-1"></i> Opsi Cetak Dokumen Analisis Catatan Juri</h6>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-dark mb-1 d-block"><i class="fas fa-columns text-primary mr-1"></i> Mode Konten Yang Dicetak:</label>
                            <div class="custom-control custom-radio mb-1">
                                <input type="radio" id="optPdfModeCurrent" name="pdfModeChoice" class="custom-control-input" value="current" checked>
                                <label class="custom-control-label small" for="optPdfModeCurrent">Sesuai Mode Tampilan Aktif di Layar (<span id="txtPdfCurrentMode">Gabungan</span>)</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="optPdfModeBoth" name="pdfModeChoice" class="custom-control-input" value="both">
                                <label class="custom-control-label small" for="optPdfModeBoth">Lengkap (Mode Gabungan + Perbandingan Juri 1 vs 2)</label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-dark mb-1 d-block"><i class="fas fa-table text-primary mr-1"></i> Sertakan Tabel Detail Santri:</label>
                            <div class="custom-control custom-radio mb-1">
                                <input type="radio" id="optPdfSantriYes" name="pdfIncludeSantri" class="custom-control-input" value="yes" checked>
                                <label class="custom-control-label small" for="optPdfSantriYes">Ya, sertakan daftar catatan kesalahan per santri</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="optPdfSantriNo" name="pdfIncludeSantri" class="custom-control-input" value="no">
                                <label class="custom-control-label small" for="optPdfSantriNo">Hanya ringkasan statistik &amp; grafik</label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-dark mb-1 d-block"><i class="fas fa-comment-dots text-primary mr-1"></i> Catatan Bebas / Khusus Juri:</label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="chkPdfIncludeTeks" checked>
                                <label class="custom-control-label small font-weight-bold" for="chkPdfIncludeTeks">Sertakan Catatan Teks Bebas / Tambahan Juri</label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-dark mb-1 d-block"><i class="fas fa-user-shield text-primary mr-1"></i> Privasi Data Santri:</label>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="chkPdfPrivacy">
                                <label class="custom-control-label small font-weight-bold" for="chkPdfPrivacy">Sembunyikan Nama Santri (Hanya tampilkan No Peserta)</label>
                            </div>
                        </div>

                        <div class="form-group mb-1">
                            <label class="small font-weight-bold text-dark mb-1 d-block"><i class="fas fa-file-alt text-primary mr-1"></i> Orientasi Halaman:</label>
                            <div class="d-flex" style="gap:15px;">
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="pdfOriP" name="pdfOrientation" class="custom-control-input" value="portrait" checked>
                                    <label class="custom-control-label small" for="pdfOriP">Portrait (Tegak)</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="pdfOriL" name="pdfOrientation" class="custom-control-input" value="landscape">
                                    <label class="custom-control-label small" for="pdfOriL">Landscape (Mendatar)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2 px-3 justify-content-between">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-sm btn-danger font-weight-bold" id="btnPdfExecute"><i class="fas fa-download mr-1"></i> Generate &amp; Download PDF</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<style>
    .stat-card {
        border-radius: 12px;
        box-shadow: 0 4px 18px rgba(30, 60, 120, .08);
    }

    .select2-container--bootstrap4 .select2-selection--multiple {
        min-height: calc(1.8125rem + 2px);
        padding: 2px 4px;
        font-size: 0.85rem;
        border-radius: 4px;
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
        background: #3b6fe0;
        color: #fff;
        border: none;
        border-radius: 14px;
        padding: 2px 8px;
        font-size: 0.8rem;
        margin-top: 2px;
        margin-bottom: 2px;
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        margin-right: 4px;
    }

    .chip-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .chip {
        margin: 0;
        cursor: pointer;
    }

    .chip input {
        display: none;
    }

    .chip span {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: .84rem;
        background: #eef1f6;
        color: #4a5568;
        border: 1px solid #dde3ed;
        transition: all .15s ease;
        user-select: none;
        font-weight: 600;
    }

    .chip:hover span {
        background: #e0e8f7;
    }

    .chip input:checked+span {
        background: linear-gradient(135deg, #3b6fe0, #5b8def);
        color: #fff;
        border-color: #3b6fe0;
        box-shadow: 0 2px 6px rgba(59, 111, 224, .35);
    }

    .sum-box {
        border-radius: 12px;
        color: #fff;
        padding: 14px 16px;
        margin-bottom: 14px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, .10);
        position: relative;
        overflow: hidden;
        transition: transform .2s ease;
    }

    .sum-box:hover {
        transform: translateY(-3px);
    }

    .sum-box .val {
        font-size: 1.7rem;
        font-weight: 700;
        line-height: 1.1;
    }

    .sum-box .lbl {
        font-size: .8rem;
        opacity: .92;
    }

    .sum-box i {
        position: absolute;
        right: 12px;
        top: 10px;
        font-size: 2.2rem;
        opacity: .25;
    }

    .chart-card {
        border-radius: 12px;
        box-shadow: 0 3px 14px rgba(30, 60, 120, .07);
        border: 1px solid #eef1f6;
    }

    .chart-card .card-header {
        background: #fff;
        border-bottom: 1px solid #eef1f6;
    }

    .chip-badge-juri1 {
        display: inline-block;
        padding: 2px 7px;
        border-radius: 12px;
        font-size: 0.75rem;
        background-color: #dbeafe;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        margin: 1px;
    }

    .chip-badge-juri2 {
        display: inline-block;
        padding: 2px 7px;
        border-radius: 12px;
        font-size: 0.75rem;
        background-color: #ffedd5;
        color: #9a3412;
        border: 1px solid #fed7aa;
        margin: 1px;
    }

    .chip-badge-same {
        display: inline-block;
        padding: 2px 7px;
        border-radius: 12px;
        font-size: 0.75rem;
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
        margin: 1px;
    }
</style>
<script>
    (function() {
        'use strict';

        const CFG = {
            dataUrl: '<?= base_url('backend/munaqosah/statistik-catatan-juri-data') ?>',
            isAdmin: <?= $isAdmin ? 'true' : 'false' ?>
        };

        let DATA = null;
        let activeKategoriId = null;
        let currentMode = 'gabungan'; // 'gabungan' | 'vs'
        let chartGabunganInstance = null;
        let chartVsInstance = null;

        const $ = id => document.getElementById(id);
        const fmtTA = t => /^\d{8}$/.test(String(t)) ? 'T.A ' + String(t).slice(2, 4) + '/' + String(t).slice(6, 8) : String(t);
        const fmtType = t => t === 'pra-munaqosah' ? 'Pra-Munaqosah' : (t === 'munaqosah' ? 'Munaqosah' : t);
        const esc = s => String(s || '').replace(/[&<>"']/g, c => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        } [c]));

        // Inisialisasi event filter shortcut
        $('taSemua').onclick = e => {
            e.preventDefault();
            if (window.jQuery) {
                const vals = window.jQuery('#filterTahunAjaran option').map(function() {
                    return this.value;
                }).get();
                window.jQuery('#filterTahunAjaran').val(vals).trigger('change');
            }
        };
        $('taReset').onclick = e => {
            e.preventDefault();
            if (window.jQuery) window.jQuery('#filterTahunAjaran').val(null).trigger('change');
        };
        $('taCurrent').onclick = e => {
            e.preventDefault();
            if (window.jQuery) window.jQuery('#filterTahunAjaran').val(['<?= esc($defaultTahun) ?>']).trigger('change');
        };
        $('typeSemua').onclick = e => {
            e.preventDefault();
            if (window.jQuery) {
                const vals = window.jQuery('#filterTypeUjian option').map(function() {
                    return this.value;
                }).get();
                window.jQuery('#filterTypeUjian').val(vals).trigger('change');
            }
        };
        $('typeReset').onclick = e => {
            e.preventDefault();
            if (window.jQuery) window.jQuery('#filterTypeUjian').val(null).trigger('change');
        };

        // Mode switch
        $('optModeGabungan').addEventListener('change', () => switchMode('gabungan'));
        $('optModeVs').addEventListener('change', () => switchMode('vs'));
        $('selGabunganMethod').addEventListener('change', () => renderGabungan());

        function switchMode(mode) {
            currentMode = mode;
            if (mode === 'gabungan') {
                $('viewGabungan').style.display = '';
                $('viewVs').style.display = 'none';
                $('txtPdfCurrentMode').textContent = 'Mode Gabungan';
                renderGabungan();
            } else {
                $('viewGabungan').style.display = 'none';
                $('viewVs').style.display = '';
                $('txtPdfCurrentMode').textContent = 'Mode Juri 1 vs Juri 2';
                renderVs();
            }
        }

        $('btnMuat').onclick = loadData;
        $('btnPdf').onclick = openPdfModal;
        $('btnPdfExecute').onclick = handlePdfExport;
        $('chkPrivacySantri').addEventListener('change', () => {
            renderSantriDetail();
            renderTeksBebas();
        });
        $('chkShowScoreSantri').addEventListener('change', renderSantriDetail);
        $('selFilterKesalahanSantri').addEventListener('change', renderSantriDetail);
        $('selFilterStatusSantri').addEventListener('change', renderSantriDetail);
        $('inpSearchSantri').addEventListener('input', renderSantriDetail);
        $('inpSearchTeks').addEventListener('input', renderTeksBebas);

        $('btnCsvGabungan').onclick = () => exportCsvGabungan();
        $('btnCsvVs').onclick = () => exportCsvVs();
        $('btnCsvSantri').onclick = () => exportCsvSantri();

        function loadData() {
            let years = [];
            if (window.jQuery) {
                const val = window.jQuery('#filterTahunAjaran').val();
                years = Array.isArray(val) ? val : (val ? [val] : []);
            }
            if (!years.length) {
                $('statEmpty').style.display = '';
                $('statEmpty').textContent = 'Pilih minimal satu Tahun Ajaran.';
                $('statContent').style.display = 'none';
                return;
            }

            let types = [];
            if (window.jQuery) {
                const val = window.jQuery('#filterTypeUjian').val();
                types = Array.isArray(val) ? val : (val ? [val] : []);
            }
            if (!types.length) {
                $('statEmpty').style.display = '';
                $('statEmpty').textContent = 'Pilih minimal satu Type Ujian.';
                $('statContent').style.display = 'none';
                return;
            }

            const params = new URLSearchParams({
                IdTahunAjaran: years.join(','),
                IdTpq: $('filterTpq').value || '0',
                TypeUjian: types.join(',')
            });

            $('statLoading').style.display = '';
            $('statEmpty').style.display = 'none';
            $('statContent').style.display = 'none';
            $('btnMuat').disabled = true;
            $('btnPdf').disabled = true;

            fetch(CFG.dataUrl + '?' + params.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(json => {
                    if (!json.success) throw new Error(json.message || 'Gagal memuat data');
                    DATA = json.data;
                    if (!DATA.rows.length) {
                        $('statEmpty').style.display = '';
                        $('statEmpty').textContent = 'Belum ada data peserta & catatan juri untuk filter yang dipilih.';
                        $('rowMateriSelect').style.display = 'none';
                        return;
                    }

                    renderKategoriMateriChips();
                    $('statContent').style.display = '';
                    renderAll();
                    $('btnPdf').disabled = false;
                })
                .catch(err => {
                    $('statEmpty').style.display = '';
                    $('statEmpty').textContent = err.message;
                })
                .finally(() => {
                    $('statLoading').style.display = 'none';
                    $('btnMuat').disabled = false;
                });
        }

        function renderKategoriMateriChips() {
            const wrap = $('kategoriMateriChips');
            wrap.innerHTML = '';
            if (!DATA.categories.length) {
                $('rowMateriSelect').style.display = 'none';
                return;
            }

            if (!activeKategoriId || !DATA.categories.some(c => c.id === activeKategoriId)) {
                activeKategoriId = DATA.categories[0].id;
            }

            DATA.categories.forEach(c => {
                const count = DATA.rows.filter(r => r.cat === c.id).length;
                const label = document.createElement('label');
                label.className = 'chip';
                label.innerHTML = `<input type="radio" name="kmChoice" value="${esc(c.id)}" ${c.id === activeKategoriId ? 'checked' : ''}>` +
                    `<span>${esc(c.name)} <small class="opacity-75">(${count})</small></span>`;
                label.querySelector('input').addEventListener('change', () => {
                    activeKategoriId = c.id;
                    renderAll();
                });
                wrap.appendChild(label);
            });
            $('rowMateriSelect').style.display = '';
        }

        function getFilteredRows() {
            if (!DATA) return [];
            return DATA.rows.filter(r => r.cat === activeKategoriId);
        }

        function renderAll() {
            renderSummaryCards();
            populateKesalahanFilter();
            if (currentMode === 'gabungan') {
                renderGabungan();
            } else {
                renderVs();
            }
            renderSantriDetail();
            renderTeksBebas();
        }

        function populateKesalahanFilter() {
            const sel = $('selFilterKesalahanSantri');
            if (!sel || !DATA) return;
            const oldVal = sel.value;
            sel.innerHTML = '<option value="all">Semua Jenis Kesalahan</option>';

            const rows = getFilteredRows();
            const countMap = {};
            rows.forEach(r => {
                const union = new Set([...((r.j && r.j['1']) || []), ...((r.j && r.j['2']) || [])]);
                union.forEach(k => {
                    countMap[k] = (countMap[k] || 0) + 1;
                });
            });

            const activeErrors = [];
            Object.keys(DATA.errors).forEach(k => {
                if (DATA.errors[k].cat === activeKategoriId) {
                    activeErrors.push({
                        kode: k,
                        name: DATA.errors[k].name,
                        count: countMap[k] || 0
                    });
                }
            });

            activeErrors.sort((a, b) => b.count - a.count);
            activeErrors.forEach(err => {
                const opt = document.createElement('option');
                opt.value = err.kode;
                opt.textContent = `${err.name} (${err.count})`;
                sel.appendChild(opt);
            });

            if (oldVal && activeErrors.some(e => e.kode === oldVal)) {
                sel.value = oldVal;
            } else {
                sel.value = 'all';
            }
        }

        function renderSummaryCards() {
            const rows = getFilteredRows();
            const totalSantri = rows.length;
            $('sumSantriTeruji').textContent = totalSantri;

            let totalKesalahan = 0;
            let agreeCount = 0;
            const errFreq = {};

            rows.forEach(r => {
                const j1 = (r.j && r.j['1']) ? r.j['1'] : [];
                const j2 = (r.j && r.j['2']) ? r.j['2'] : [];

                const set1 = new Set(j1);
                const set2 = new Set(j2);
                const union = new Set([...j1, ...j2]);

                totalKesalahan += union.size;
                union.forEach(k => {
                    errFreq[k] = (errFreq[k] || 0) + 1;
                });

                // Cek kesepakatan juri per santri
                let isSame = false;
                if (set1.size === 0 && set2.size === 0) {
                    isSame = true;
                } else if (set1.size > 0 && set2.size > 0) {
                    let common = 0;
                    set1.forEach(k => {
                        if (set2.has(k)) common++;
                    });
                    if (common === set1.size && common === set2.size) {
                        isSame = true;
                    }
                }
                if (isSame) agreeCount++;
            });

            $('sumTotalKesalahan').textContent = totalKesalahan;
            const pctAgree = totalSantri > 0 ? Math.round((agreeCount / totalSantri) * 100) : 0;
            $('sumKesepakatanJuri').textContent = pctAgree + '%';

            let topName = '-';
            let maxCount = 0;
            Object.keys(errFreq).forEach(k => {
                if (errFreq[k] > maxCount) {
                    maxCount = errFreq[k];
                    topName = (DATA.errors[k] ? DATA.errors[k].name : k) + ` (${maxCount})`;
                }
            });
            $('sumTopKesalahan').textContent = topName;
            $('sumTopKesalahan').title = topName;
        }

        // ---------- MODE GABUNGAN ----------
        function renderGabungan() {
            const rows = getFilteredRows();
            const method = $('selGabunganMethod').value; // 'union' | 'intersection'
            const errStats = {};

            // Inisialisasi daftar master error untuk kategori ini
            Object.keys(DATA.errors).forEach(k => {
                if (DATA.errors[k].cat === activeKategoriId) {
                    errStats[k] = {
                        kode: k,
                        name: DATA.errors[k].name,
                        count: 0
                    };
                }
            });

            rows.forEach(r => {
                const j1 = (r.j && r.j['1']) ? r.j['1'] : [];
                const j2 = (r.j && r.j['2']) ? r.j['2'] : [];
                const s1 = new Set(j1);
                const s2 = new Set(j2);

                let targetKeys = [];
                if (method === 'intersection') {
                    targetKeys = j1.filter(k => s2.has(k));
                } else {
                    targetKeys = [...new Set([...j1, ...j2])];
                }

                targetKeys.forEach(k => {
                    if (!errStats[k]) {
                        errStats[k] = {
                            kode: k,
                            name: DATA.errors[k] ? DATA.errors[k].name : k,
                            count: 0
                        };
                    }
                    errStats[k].count++;
                });
            });

            const statArr = Object.values(errStats).sort((a, b) => b.count - a.count);
            const totalSantri = rows.length || 1;

            // Render Tabel Gabungan
            const tb = $('tbGabunganBody');
            tb.innerHTML = '';
            if (!statArr.length) {
                tb.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada catatan kesalahan generik pada kategori ini.</td></tr>';
            } else {
                statArr.forEach((item, idx) => {
                    const pct = Math.round((item.count / totalSantri) * 1000) / 10;
                    const tr = document.createElement('tr');
                    tr.style.cursor = 'pointer';
                    tr.title = 'Klik untuk melihat daftar santri dengan kesalahan ini';
                    tr.innerHTML = `<td class="text-center font-weight-bold text-muted">${idx+1}</td>` +
                        `<td><strong>${esc(item.name)}</strong> <span class="badge badge-light border ml-1">${esc(item.kode)}</span></td>` +
                        `<td class="text-center"><span class="badge badge-primary px-2 py-1">${item.count}</span></td>` +
                        `<td class="text-center font-weight-bold text-primary">${pct}%</td>`;
                    tr.onclick = () => showModalDetailKesalahan(item.kode, item.name);
                    tb.appendChild(tr);
                });
            }

            // Render Horizontal Bar Chart Gabungan
            renderChartGabungan(statArr, totalSantri);
        }

        function renderChartGabungan(statArr, totalSantri) {
            const ctx = $('chartGabungan').getContext('2d');
            if (chartGabunganInstance) {
                chartGabunganInstance.destroy();
            }

            const topItems = statArr.slice(0, 10);
            const labels = topItems.map(i => i.name.length > 25 ? i.name.substring(0, 23) + '…' : i.name);
            const dataCounts = topItems.map(i => i.count);
            const dataPcts = topItems.map(i => Math.round((i.count / totalSantri) * 1000) / 10);

            chartGabunganInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Santri',
                        data: dataCounts,
                        backgroundColor: 'rgba(59, 111, 224, 0.85)',
                        borderColor: '#3b6fe0',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const idx = context.dataIndex;
                                    return ` ${topItems[idx].name}: ${dataCounts[idx]} santri (${dataPcts[idx]}% dari total ${totalSantri} teruji)`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        // ---------- MODE JURI 1 VS JURI 2 ----------
        function renderVs() {
            const rows = getFilteredRows();
            const comp = {};

            Object.keys(DATA.errors).forEach(k => {
                if (DATA.errors[k].cat === activeKategoriId) {
                    comp[k] = {
                        kode: k,
                        name: DATA.errors[k].name,
                        j1: 0,
                        j2: 0,
                        both: 0,
                        only1: 0,
                        only2: 0
                    };
                }
            });

            rows.forEach(r => {
                const j1 = (r.j && r.j['1']) ? r.j['1'] : [];
                const j2 = (r.j && r.j['2']) ? r.j['2'] : [];
                const s1 = new Set(j1);
                const s2 = new Set(j2);

                const union = new Set([...j1, ...j2]);
                union.forEach(k => {
                    if (!comp[k]) {
                        comp[k] = {
                            kode: k,
                            name: DATA.errors[k] ? DATA.errors[k].name : k,
                            j1: 0,
                            j2: 0,
                            both: 0,
                            only1: 0,
                            only2: 0
                        };
                    }
                    const has1 = s1.has(k);
                    const has2 = s2.has(k);
                    if (has1) comp[k].j1++;
                    if (has2) comp[k].j2++;
                    if (has1 && has2) comp[k].both++;
                    else if (has1 && !has2) comp[k].only1++;
                    else if (!has1 && has2) comp[k].only2++;
                });
            });

            const compArr = Object.values(comp).sort((a, b) => (b.j1 + b.j2) - (a.j1 + a.j2));

            // Render Table VS
            const tb = $('tbVsBody');
            tb.innerHTML = '';
            if (!compArr.length) {
                tb.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-3">Tidak ada data perbandingan.</td></tr>';
            } else {
                compArr.forEach((item, idx) => {
                    const totalUnion = item.both + item.only1 + item.only2;
                    const agreePct = totalUnion > 0 ? Math.round((item.both / totalUnion) * 100) : 100;
                    let badgeClass = 'badge-success';
                    if (agreePct < 50) badgeClass = 'badge-danger';
                    else if (agreePct < 80) badgeClass = 'badge-warning';

                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td class="text-center font-weight-bold text-muted">${idx+1}</td>` +
                        `<td><span class="badge badge-light border">${esc(item.kode)}</span></td>` +
                        `<td><strong>${esc(item.name)}</strong></td>` +
                        `<td class="text-center font-weight-bold text-primary">${item.j1}</td>` +
                        `<td class="text-center font-weight-bold text-orange" style="color:#ea580c">${item.j2}</td>` +
                        `<td class="text-center font-weight-bold text-success">${item.both}</td>` +
                        `<td class="text-center text-muted">${item.only1}</td>` +
                        `<td class="text-center text-muted">${item.only2}</td>` +
                        `<td class="text-center"><span class="badge ${badgeClass} px-2">${agreePct}%</span></td>`;
                    tb.appendChild(tr);
                });
            }

            renderChartVs(compArr);
        }

        function renderChartVs(compArr) {
            const ctx = $('chartVs').getContext('2d');
            if (chartVsInstance) {
                chartVsInstance.destroy();
            }

            const topItems = compArr.slice(0, 8);
            const labels = topItems.map(i => i.name.length > 22 ? i.name.substring(0, 20) + '…' : i.name);
            const dataJ1 = topItems.map(i => i.j1);
            const dataJ2 = topItems.map(i => i.j2);

            chartVsInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Juri 1',
                            data: dataJ1,
                            backgroundColor: 'rgba(59, 130, 246, 0.85)',
                            borderColor: '#2563eb',
                            borderWidth: 1,
                            borderRadius: 4
                        },
                        {
                            label: 'Juri 2',
                            data: dataJ2,
                            backgroundColor: 'rgba(249, 115, 22, 0.85)',
                            borderColor: '#ea580c',
                            borderWidth: 1,
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        // ---------- DETAIL PER SANTRI ----------
        function renderSantriDetail() {
            const rows = getFilteredRows();
            const tb = $('tbSantriDetailBody');
            tb.innerHTML = '';

            const showScore = $('chkShowScoreSantri') && $('chkShowScoreSantri').checked;
            const kesalahanFilter = $('selFilterKesalahanSantri') ? $('selFilterKesalahanSantri').value : 'all';
            const statusFilter = $('selFilterStatusSantri').value;
            const searchVal = ($('inpSearchSantri').value || '').toLowerCase().trim();
            const hideName = $('chkPrivacySantri').checked;

            // Render Header Tabel Dinamis
            const thead = $('tblSantriDetailThead');
            if (thead) {
                const thScore = showScore ? '<th style="width:130px" class="text-center">Nilai (J1 | J2 | Rata)</th>' : '';
                thead.innerHTML = `<tr>
                    <th style="width:40px" class="text-center">No</th>
                    <th style="width:100px">No Peserta</th>
                    <th>Nama Santri</th>
                    <th>TPQ</th>
                    <th style="width:90px" class="text-center">Tahun/Type</th>
                    ${thScore}
                    <th style="min-width:200px">Catatan Juri 1</th>
                    <th style="min-width:200px">Catatan Juri 2</th>
                    <th style="width:120px" class="text-center">Status Juri</th>
                </tr>`;
            }

            let filtered = rows.filter(r => {
                if (searchVal) {
                    const matchNp = String(r.np).toLowerCase().includes(searchVal);
                    const matchNm = String(r.nm).toLowerCase().includes(searchVal);
                    if (!matchNp && !matchNm) return false;
                }

                const j1 = (r.j && r.j['1']) ? r.j['1'] : [];
                const j2 = (r.j && r.j['2']) ? r.j['2'] : [];
                const s1 = new Set(j1);
                const s2 = new Set(j2);
                const totalErrors = s1.size + s2.size;

                if (kesalahanFilter !== 'all') {
                    if (!s1.has(kesalahanFilter) && !s2.has(kesalahanFilter)) {
                        return false;
                    }
                }

                if (statusFilter === 'ada_kesalahan' && totalErrors === 0) return false;
                if (statusFilter === 'bersih' && totalErrors > 0) return false;

                if (statusFilter === 'sepakat_sama') {
                    if (s1.size !== s2.size) return false;
                    for (let k of s1) {
                        if (!s2.has(k)) return false;
                    }
                }

                if (statusFilter === 'ada_perbedaan') {
                    let diff = false;
                    if (s1.size !== s2.size) diff = true;
                    else {
                        for (let k of s1) {
                            if (!s2.has(k)) {
                                diff = true;
                                break;
                            }
                        }
                    }
                    if (!diff) return false;
                }

                return true;
            });

            const colCount = showScore ? 9 : 8;
            if (!filtered.length) {
                tb.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-muted py-3">Tidak ada data santri yang sesuai filter.</td></tr>`;
                return;
            }

            filtered.forEach((r, idx) => {
                const j1 = (r.j && r.j['1']) ? r.j['1'] : [];
                const j2 = (r.j && r.j['2']) ? r.j['2'] : [];
                const s1 = new Set(j1);
                const s2 = new Set(j2);

                let chips1Html = '';
                if (j1.length === 0) {
                    chips1Html = '<span class="text-muted small font-italic">Tidak ada catatan</span>';
                } else {
                    chips1Html = j1.map(k => {
                        const isBoth = s2.has(k);
                        const cls = isBoth ? 'chip-badge-same' : 'chip-badge-juri1';
                        const name = DATA.errors[k] ? DATA.errors[k].name : k;
                        return `<span class="${cls}" title="${esc(name)}">${esc(name)}</span>`;
                    }).join(' ');
                }

                let chips2Html = '';
                if (j2.length === 0) {
                    chips2Html = '<span class="text-muted small font-italic">Tidak ada catatan</span>';
                } else {
                    chips2Html = j2.map(k => {
                        const isBoth = s1.has(k);
                        const cls = isBoth ? 'chip-badge-same' : 'chip-badge-juri2';
                        const name = DATA.errors[k] ? DATA.errors[k].name : k;
                        return `<span class="${cls}" title="${esc(name)}">${esc(name)}</span>`;
                    }).join(' ');
                }

                // Status badge
                let statusBadge = '';
                if (s1.size === 0 && s2.size === 0) {
                    statusBadge = '<span class="badge badge-light border text-muted">Lancar (0)</span>';
                } else if (s1.size === s2.size && [...s1].every(k => s2.has(k))) {
                    statusBadge = '<span class="badge badge-success"><i class="fas fa-check-double mr-1"></i> Sepakat</span>';
                } else {
                    let common = [...s1].filter(k => s2.has(k)).length;
                    if (common > 0) {
                        statusBadge = '<span class="badge badge-warning text-dark"><i class="fas fa-exclamation-circle mr-1"></i> Parsial</span>';
                    } else {
                        statusBadge = '<span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> Berbeda</span>';
                    }
                }

                const displayName = hideName ? `<span class="text-muted font-italic">[Nama Disembunyikan]</span>` : `<strong>${esc(r.nm)}</strong>`;
                const tpqName = DATA.tpqs[r.tpq] || r.tpq;

                let scoreTd = '';
                if (showScore) {
                    const s1Val = (r.s && r.s['1'] !== null && r.s['1'] !== undefined) ? r.s['1'] : '-';
                    const s2Val = (r.s && r.s['2'] !== null && r.s['2'] !== undefined) ? r.s['2'] : '-';
                    const avgVal = (r.avg !== null && r.avg !== undefined) ? r.avg : '-';

                    scoreTd = `<td class="text-center py-1" style="white-space:nowrap; vertical-align:middle;">
                        <div class="d-flex justify-content-center align-items-center mb-1" style="gap:3px;">
                            <span class="badge badge-light border text-primary px-1" title="Nilai Juri 1">J1: ${esc(s1Val)}</span>
                            <span class="badge badge-light border text-orange px-1" style="color:#ea580c" title="Nilai Juri 2">J2: ${esc(s2Val)}</span>
                        </div>
                        <span class="badge badge-success px-2 py-0 font-weight-bold" title="Rata-rata Nilai">Rata: ${esc(avgVal)}</span>
                    </td>`;
                }

                const tr = document.createElement('tr');
                tr.innerHTML = `<td class="text-center font-weight-bold text-muted">${idx+1}</td>` +
                    `<td><code>${esc(r.np)}</code></td>` +
                    `<td>${displayName}</td>` +
                    `<td class="small">${esc(tpqName)}</td>` +
                    `<td class="text-center small">${esc(fmtTA(r.y))}<br><span class="badge badge-info">${esc(fmtType(r.type))}</span></td>` +
                    scoreTd +
                    `<td>${chips1Html}</td>` +
                    `<td>${chips2Html}</td>` +
                    `<td class="text-center">${statusBadge}</td>`;
                tb.appendChild(tr);
            });
        }

        // ---------- CATATAN TEKS BEBAS ----------
        function renderTeksBebas() {
            const rows = getFilteredRows();
            const tb = $('tbTeksBebasBody');
            tb.innerHTML = '';

            const searchVal = ($('inpSearchTeks').value || '').toLowerCase().trim();
            const hideName = $('chkPrivacySantri').checked;

            const list = [];
            rows.forEach(r => {
                const t1 = (r.t && r.t['1']) ? r.t['1'].join('; ') : '';
                const t2 = (r.t && r.t['2']) ? r.t['2'].join('; ') : '';
                if (!t1 && !t2) return;

                if (searchVal) {
                    const matchText = (t1 + ' ' + t2).toLowerCase().includes(searchVal);
                    const matchNp = String(r.np).toLowerCase().includes(searchVal);
                    const matchNm = String(r.nm).toLowerCase().includes(searchVal);
                    if (!matchText && !matchNp && !matchNm) return;
                }

                list.push({
                    np: r.np,
                    nm: r.nm,
                    tpq: DATA.tpqs[r.tpq] || r.tpq,
                    t1,
                    t2
                });
            });

            if (!list.length) {
                tb.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">Tidak ada catatan teks bebas juri pada materi ini.</td></tr>';
                return;
            }

            list.forEach((item, idx) => {
                const displayName = hideName ? `<span class="text-muted font-italic">[Nama Disembunyikan]</span>` : `<strong>${esc(item.nm)}</strong>`;
                const tr = document.createElement('tr');
                tr.innerHTML = `<td class="text-center font-weight-bold text-muted">${idx+1}</td>` +
                    `<td><code>${esc(item.np)}</code></td>` +
                    `<td>${displayName}</td>` +
                    `<td class="small">${esc(item.tpq)}</td>` +
                    `<td class="small text-primary">${item.t1 ? esc(item.t1) : '<span class="text-muted font-italic">-</span>'}</td>` +
                    `<td class="small text-orange" style="color:#ea580c">${item.t2 ? esc(item.t2) : '<span class="text-muted font-italic">-</span>'}</td>`;
                tb.appendChild(tr);
            });
        }

        // ---------- MODAL DETAIL KESALAHAN ----------
        function showModalDetailKesalahan(kode, name) {
            const rows = getFilteredRows();
            $('mdkTitle').textContent = `Daftar Santri: ${name} (${kode})`;
            const tb = $('tbMdkBody');
            tb.innerHTML = '';

            let count = 0;
            rows.forEach(r => {
                const j1 = (r.j && r.j['1']) ? r.j['1'] : [];
                const j2 = (r.j && r.j['2']) ? r.j['2'] : [];
                const has1 = j1.includes(kode);
                const has2 = j2.includes(kode);
                if (!has1 && !has2) return;

                count++;
                let badge = '';
                if (has1 && has2) {
                    badge = '<span class="badge badge-success">Kedua Juri</span>';
                } else if (has1) {
                    badge = '<span class="badge badge-primary">Hanya Juri 1</span>';
                } else {
                    badge = '<span class="badge badge-warning text-dark">Hanya Juri 2</span>';
                }

                const hideName = $('chkPrivacySantri').checked;
                const displayName = hideName ? `[Nama Disembunyikan]` : esc(r.nm);
                const tpqName = DATA.tpqs[r.tpq] || r.tpq;

                const tr = document.createElement('tr');
                tr.innerHTML = `<td class="text-center font-weight-bold">${count}</td>` +
                    `<td><code>${esc(r.np)}</code></td>` +
                    `<td><strong>${displayName}</strong></td>` +
                    `<td class="small">${esc(tpqName)}</td>` +
                    `<td class="text-center">${badge}</td>`;
                tb.appendChild(tr);
            });

            $('mdkSub').innerHTML = `<div class="d-flex justify-content-between align-items-center flex-wrap">` +
                `<span>Total <strong>${count} santri</strong> tercatat melakukan kesalahan ini pada materi terpilih.</span>` +
                `<button type="button" class="btn btn-xs btn-primary mt-1 mt-md-0" id="btnFilterTabelFromModal"><i class="fas fa-filter mr-1"></i> Terapkan ke Tabel Santri</button>` +
                `</div>`;

            setTimeout(() => {
                const btnApply = $('btnFilterTabelFromModal');
                if (btnApply) {
                    btnApply.onclick = () => {
                        if (window.jQuery) window.jQuery('#modalDetailKesalahan').modal('hide');
                        const sel = $('selFilterKesalahanSantri');
                        if (sel) {
                            sel.value = kode;
                            renderSantriDetail();
                            const tblEl = $('tblSantriDetail');
                            if (tblEl) {
                                tblEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        }
                    };
                }
            }, 100);

            if (window.jQuery) window.jQuery('#modalDetailKesalahan').modal('show');
        }

        // ---------- CSV EXPORTS ----------
        function downloadCsv(content, filename) {
            const blob = new Blob(["\uFEFF" + content], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.setAttribute('download', filename);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function exportCsvGabungan() {
            const rows = getFilteredRows();
            const totalSantri = rows.length || 1;
            const errStats = {};
            rows.forEach(r => {
                const union = new Set([...((r.j && r.j['1']) || []), ...((r.j && r.j['2']) || [])]);
                union.forEach(k => {
                    if (!errStats[k]) errStats[k] = {
                        kode: k,
                        name: DATA.errors[k] ? DATA.errors[k].name : k,
                        count: 0
                    };
                    errStats[k].count++;
                });
            });
            const statArr = Object.values(errStats).sort((a, b) => b.count - a.count);
            let csv = "No,Kode Kesalahan,Nama Kesalahan Generik,Jumlah Kasus Santri,Persentase (%)\n";
            statArr.forEach((item, idx) => {
                const pct = Math.round((item.count / totalSantri) * 1000) / 10;
                csv += `"${idx+1}","${item.kode}","${item.name}","${item.count}","${pct}%"\n`;
            });
            downloadCsv(csv, `Analisis_Catatan_Juri_Gabungan_${activeKategoriId}.csv`);
        }

        function exportCsvVs() {
            const rows = getFilteredRows();
            const comp = {};
            rows.forEach(r => {
                const j1 = (r.j && r.j['1']) || [];
                const j2 = (r.j && r.j['2']) || [];
                const s1 = new Set(j1);
                const s2 = new Set(j2);
                new Set([...j1, ...j2]).forEach(k => {
                    if (!comp[k]) comp[k] = {
                        kode: k,
                        name: DATA.errors[k] ? DATA.errors[k].name : k,
                        j1: 0,
                        j2: 0,
                        both: 0,
                        only1: 0,
                        only2: 0
                    };
                    const h1 = s1.has(k);
                    const h2 = s2.has(k);
                    if (h1) comp[k].j1++;
                    if (h2) comp[k].j2++;
                    if (h1 && h2) comp[k].both++;
                    else if (h1) comp[k].only1++;
                    else if (h2) comp[k].only2++;
                });
            });
            const compArr = Object.values(comp).sort((a, b) => (b.j1 + b.j2) - (a.j1 + a.j2));
            let csv = "No,Kode Kesalahan,Nama Kesalahan Generik,Juri 1,Juri 2,Dicatat Keduanya,Hanya Juri 1,Hanya Juri 2,Kesepakatan (%)\n";
            compArr.forEach((item, idx) => {
                const tot = item.both + item.only1 + item.only2;
                const pct = tot > 0 ? Math.round((item.both / tot) * 100) : 100;
                csv += `"${idx+1}","${item.kode}","${item.name}","${item.j1}","${item.j2}","${item.both}","${item.only1}","${item.only2}","${pct}%"\n`;
            });
            downloadCsv(csv, `Analisis_Catatan_Juri_Vs_${activeKategoriId}.csv`);
        }

        function exportCsvSantri() {
            const rows = getFilteredRows();
            const hideName = $('chkPrivacySantri').checked;
            const kesalahanFilter = $('selFilterKesalahanSantri') ? $('selFilterKesalahanSantri').value : 'all';
            const statusFilter = $('selFilterStatusSantri').value;
            const searchVal = ($('inpSearchSantri').value || '').toLowerCase().trim();

            const filtered = rows.filter(r => {
                if (searchVal) {
                    const matchNp = String(r.np).toLowerCase().includes(searchVal);
                    const matchNm = String(r.nm).toLowerCase().includes(searchVal);
                    if (!matchNp && !matchNm) return false;
                }
                const s1 = new Set((r.j && r.j['1']) || []);
                const s2 = new Set((r.j && r.j['2']) || []);
                if (kesalahanFilter !== 'all') {
                    if (!s1.has(kesalahanFilter) && !s2.has(kesalahanFilter)) return false;
                }
                const totalErrors = s1.size + s2.size;
                if (statusFilter === 'ada_kesalahan' && totalErrors === 0) return false;
                if (statusFilter === 'bersih' && totalErrors > 0) return false;
                if (statusFilter === 'sepakat_sama') {
                    if (s1.size !== s2.size || ![...s1].every(k => s2.has(k))) return false;
                }
                if (statusFilter === 'ada_perbedaan') {
                    if (s1.size === s2.size && [...s1].every(k => s2.has(k))) return false;
                }
                return true;
            });

            const showScore = $('chkShowScoreSantri') && $('chkShowScoreSantri').checked;
            let csv = showScore 
                ? "No,No Peserta,Nama Santri,TPQ,Tahun Ajaran,Type Ujian,Nilai J1,Nilai J2,Rata-rata Nilai,Catatan Juri 1,Catatan Juri 2,Status Kesepakatan\n"
                : "No,No Peserta,Nama Santri,TPQ,Tahun Ajaran,Type Ujian,Catatan Juri 1,Catatan Juri 2,Status Kesepakatan\n";

            filtered.forEach((r, idx) => {
                const j1 = ((r.j && r.j['1']) || []).map(k => DATA.errors[k] ? DATA.errors[k].name : k).join('; ');
                const j2 = ((r.j && r.j['2']) || []).map(k => DATA.errors[k] ? DATA.errors[k].name : k).join('; ');
                const s1 = new Set((r.j && r.j['1']) || []);
                const s2 = new Set((r.j && r.j['2']) || []);
                let status = 'Berbeda';
                if (s1.size === s2.size && [...s1].every(k => s2.has(k))) status = 'Sepakat Sama';
                else if ([...s1].some(k => s2.has(k))) status = 'Parsial';
                const displayName = hideName ? '[Nama Disembunyikan]' : r.nm;
                const tpqName = DATA.tpqs[r.tpq] || r.tpq;

                if (showScore) {
                    const sc1 = (r.s && r.s['1'] !== null && r.s['1'] !== undefined) ? r.s['1'] : '';
                    const sc2 = (r.s && r.s['2'] !== null && r.s['2'] !== undefined) ? r.s['2'] : '';
                    const scAvg = (r.avg !== null && r.avg !== undefined) ? r.avg : '';
                    csv += `"${idx+1}","${r.np}","${displayName}","${tpqName}","${fmtTA(r.y)}","${fmtType(r.type)}","${sc1}","${sc2}","${scAvg}","${j1}","${j2}","${status}"\n`;
                } else {
                    csv += `"${idx+1}","${r.np}","${displayName}","${tpqName}","${fmtTA(r.y)}","${fmtType(r.type)}","${j1}","${j2}","${status}"\n`;
                }
            });
            downloadCsv(csv, `Daftar_Catatan_Santri_${activeKategoriId}.csv`);
        }

        // ---------- EXPORT PDF ----------
        function openPdfModal() {
            if (!DATA) return;
            if (window.jQuery) window.jQuery('#pdfModal').modal('show');
        }

        function loadJsPdf() {
            if (window.jspdf && window.jspdf.jsPDF) {
                if (typeof window.jspdf.jsPDF.API.autoTable === 'function' || typeof window.jspdf.autoTable === 'function') {
                    return Promise.resolve(window.jspdf.jsPDF);
                }
            }
            return new Promise((resolve, reject) => {
                const s1 = document.createElement('script');
                s1.src = 'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js';
                s1.onload = () => {
                    const s2 = document.createElement('script');
                    s2.src = 'https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js';
                    s2.onload = () => resolve(window.jspdf.jsPDF);
                    s2.onerror = () => resolve(window.jspdf.jsPDF);
                    document.head.appendChild(s2);
                };
                s1.onerror = () => reject(new Error('Gagal memuat library PDF.'));
                document.head.appendChild(s1);
            });
        }

        function cleanPdfText(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/≥/g, '>=')
                .replace(/≤/g, '<=')
                .replace(/[–—]/g, '-')
                .replace(/•/g, '-')
                .replace(/…/g, '...');
        }

        function renderOffscreenChart(config, width = 1000, height = 360) {
            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');

            // Set latar putih dasar
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, width, height);

            const whiteBgPlugin = {
                id: 'pdfWhiteBackground',
                beforeDraw: (chart) => {
                    const c = chart.ctx;
                    c.save();
                    c.fillStyle = '#ffffff';
                    c.fillRect(0, 0, chart.width, chart.height);
                    c.restore();
                }
            };

            const chartConfig = {
                ...config,
                plugins: [whiteBgPlugin, ...(config.plugins || [])],
                options: {
                    ...config.options,
                    animation: false,
                    responsive: false
                }
            };

            const tempChart = new Chart(ctx, chartConfig);
            tempChart.update('none');

            // Salin ke canvas baru untuk memastikan 100% solid putih tanpa transparansi
            const finalCanvas = document.createElement('canvas');
            finalCanvas.width = width;
            finalCanvas.height = height;
            const fCtx = finalCanvas.getContext('2d');
            fCtx.fillStyle = '#ffffff';
            fCtx.fillRect(0, 0, width, height);
            fCtx.drawImage(canvas, 0, 0);

            const dataUrl = finalCanvas.toDataURL('image/png', 1.0);
            tempChart.destroy();
            return dataUrl;
        }

        function getChartGabunganImage(statArr, totalSantri) {
            const topItems = statArr.slice(0, 10);
            const labels = topItems.map(i => i.name.length > 25 ? i.name.substring(0, 23) + '…' : i.name);
            const dataCounts = topItems.map(i => i.count);

            return renderOffscreenChart({
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Santri',
                        data: dataCounts,
                        backgroundColor: 'rgba(59, 111, 224, 0.85)',
                        borderColor: '#3b6fe0',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { precision: 0, font: { size: 12, weight: 'bold' } }
                        },
                        y: {
                            ticks: { font: { size: 12, weight: 'bold' } }
                        }
                    }
                }
            }, 1000, 360);
        }

        function getChartVsImage(compArr) {
            const topItems = compArr.slice(0, 8);
            const labels = topItems.map(i => i.name.length > 22 ? i.name.substring(0, 20) + '…' : i.name);
            const dataJ1 = topItems.map(i => i.j1);
            const dataJ2 = topItems.map(i => i.j2);

            return renderOffscreenChart({
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Juri 1',
                        data: dataJ1,
                        backgroundColor: 'rgba(59, 130, 246, 0.85)',
                        borderColor: '#2563eb',
                        borderWidth: 1,
                        borderRadius: 4
                    }, {
                        label: 'Juri 2',
                        data: dataJ2,
                        backgroundColor: 'rgba(249, 115, 22, 0.85)',
                        borderColor: '#ea580c',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    plugins: {
                        legend: { position: 'top', labels: { font: { size: 13, weight: 'bold' } } }
                    },
                    scales: {
                        x: {
                            ticks: { font: { size: 12, weight: 'bold' } }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, font: { size: 12, weight: 'bold' } }
                        }
                    }
                }
            }, 1000, 360);
        }

        async function handlePdfExport() {
            if (window.jQuery) window.jQuery('#pdfModal').modal('hide');
            const btn = $('btnPdf');
            const oldHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyusun PDF…';

            try {
                const JsPDF = await loadJsPdf();
                const orientation = document.querySelector('input[name="pdfOrientation"]:checked').value;
                const modeChoice = document.querySelector('input[name="pdfModeChoice"]:checked').value;
                const incSantri = document.querySelector('input[name="pdfIncludeSantri"]:checked').value === 'yes';
                const hideName = $('chkPdfPrivacy').checked;

                const doc = new JsPDF({
                    unit: 'mm',
                    format: 'a4',
                    orientation: orientation
                });

                const W = orientation === 'landscape' ? 297 : 210;
                const H = orientation === 'landscape' ? 210 : 297;
                const M = 12;
                const CW = W - M * 2;

                const tpqSel = $('filterTpq');
                const tpqText = tpqSel.options[tpqSel.selectedIndex] ? tpqSel.options[tpqSel.selectedIndex].text : '-';
                const activeCatObj = DATA.categories.find(c => c.id === activeKategoriId);
                const activeCatName = activeCatObj ? activeCatObj.name : 'Materi Ujian';

                let y = M;

                function drawHeader() {
                    doc.setFillColor(30, 60, 120);
                    doc.rect(M, y, CW, 14, 'F');
                    doc.setTextColor(255, 255, 255);
                    doc.setFontSize(13);
                    doc.setFont(undefined, 'bold');
                    doc.text(cleanPdfText('LAPORAN ANALISIS CATATAN JURI & KESALAHAN GENERIK'), M + CW / 2, y + 6, {
                        align: 'center'
                    });
                    doc.setFontSize(8.5);
                    doc.setFont(undefined, 'normal');
                    doc.text(cleanPdfText(`Materi: ${activeCatName} | TPQ: ${tpqText}`), M + CW / 2, y + 11, {
                        align: 'center'
                    });
                    y += 18;
                }

                drawHeader();

                const rows = getFilteredRows();
                const totalSantri = rows.length || 1;

                // Summary metric boxes at the top of PDF
                const totalKesalahanTxt = $('sumTotalKesalahan').textContent || '0';
                const kesepakatanTxt = $('sumKesepakatanJuri').textContent || '0%';
                const topErrTxt = $('sumTopKesalahan').textContent || '-';

                const boxW = (CW - 9) / 4;
                const boxH = 12;
                const boxData = [
                    { lbl: 'Santri Teruji', val: String(totalSantri), fill: [235, 242, 255], border: [59, 111, 224] },
                    { lbl: 'Total Catatan Kesalahan', val: totalKesalahanTxt, fill: [255, 235, 235], border: [220, 53, 69] },
                    { lbl: 'Tingkat Kesepakatan', val: kesepakatanTxt, fill: [235, 255, 240], border: [40, 167, 69] },
                    { lbl: 'Kesalahan Terbanyak', val: topErrTxt, fill: [255, 250, 230], border: [255, 193, 7] }
                ];

                boxData.forEach((b, i) => {
                    const bx = M + i * (boxW + 3);
                    doc.setFillColor(b.fill[0], b.fill[1], b.fill[2]);
                    doc.setDrawColor(b.border[0], b.border[1], b.border[2]);
                    doc.roundedRect(bx, y, boxW, boxH, 1.5, 1.5, 'FD');

                    doc.setFontSize(6.5);
                    doc.setFont(undefined, 'normal');
                    doc.setTextColor(100, 100, 100);
                    doc.text(cleanPdfText(b.lbl), bx + boxW / 2, y + 4, { align: 'center' });

                    doc.setFontSize(8.5);
                    doc.setFont(undefined, 'bold');
                    doc.setTextColor(30, 30, 30);
                    let valStr = cleanPdfText(b.val);
                    if (valStr.length > 20) valStr = valStr.substring(0, 18) + '…';
                    doc.text(valStr, bx + boxW / 2, y + 9.5, { align: 'center' });
                });
                y += boxH + 6;

                let secIndex = 1;

                // Render Mode Gabungan Section (Chart + Table) in PDF
                if (modeChoice === 'current' ? currentMode === 'gabungan' : true) {
                    const errStats = {};
                    rows.forEach(r => {
                        const union = new Set([...((r.j && r.j['1']) || []), ...((r.j && r.j['2']) || [])]);
                        union.forEach(k => {
                            if (!errStats[k]) errStats[k] = {
                                kode: k,
                                name: DATA.errors[k] ? DATA.errors[k].name : k,
                                count: 0
                            };
                            errStats[k].count++;
                        });
                    });
                    const statArr = Object.values(errStats).sort((a, b) => b.count - a.count);

                    doc.setFont(undefined, 'bold');
                    doc.setFontSize(10.5);
                    doc.setTextColor(30, 60, 120);
                    doc.text(`${secIndex++}. Grafik & Frekuensi Kesalahan Generik (Gabungan 2 Juri)`, M, y + 2);
                    y += 5;

                    // Render chart gabungan off-screen secara sinkron
                    try {
                        const imgDataG = getChartGabunganImage(statArr, totalSantri);
                        if (imgDataG) {
                            const imgW = orientation === 'landscape' ? Math.min(CW, 220) : CW;
                            const imgH = (360 / 1000) * imgW;
                            if (y + imgH > H - 25) {
                                doc.addPage();
                                y = M;
                            }
                            doc.addImage(imgDataG, 'PNG', M + (CW - imgW) / 2, y, imgW, imgH);
                            y += imgH + 5;
                        }
                    } catch (e) {
                        console.warn('Gagal memuat chart gabungan ke PDF:', e);
                    }

                    if (y > H - 35) {
                        doc.addPage();
                        y = M;
                    }

                    const bodyGabungan = statArr.map((item, idx) => [
                        idx + 1,
                        item.kode,
                        cleanPdfText(item.name),
                        item.count,
                        Math.round((item.count / totalSantri) * 1000) / 10 + '%'
                    ]);

                    doc.autoTable({
                        startY: y,
                        margin: {
                            left: M,
                            right: M
                        },
                        head: [
                            ['No', 'Kode', 'Nama Kesalahan Generik', 'Kasus Santri', '% Teruji']
                        ],
                        body: bodyGabungan,
                        theme: 'striped',
                        headStyles: {
                            fillColor: [59, 111, 224],
                            fontSize: 8,
                            fontStyle: 'bold'
                        },
                        bodyStyles: {
                            fontSize: 8
                        },
                        columnStyles: {
                            0: {
                                halign: 'center',
                                cellWidth: 10
                            },
                            1: {
                                halign: 'center',
                                cellWidth: 20
                            },
                            3: {
                                halign: 'center',
                                cellWidth: 25
                            },
                            4: {
                                halign: 'center',
                                cellWidth: 25
                            }
                        }
                    });

                    y = doc.lastAutoTable.finalY + 8;
                }

                // Render Mode VS Section (Chart + Table) in PDF
                if (modeChoice === 'current' ? currentMode === 'vs' : true) {
                    if (y > H - 75) {
                        doc.addPage();
                        y = M;
                    }
                    const comp = {};
                    rows.forEach(r => {
                        const j1 = (r.j && r.j['1']) || [];
                        const j2 = (r.j && r.j['2']) || [];
                        const s1 = new Set(j1);
                        const s2 = new Set(j2);
                        new Set([...j1, ...j2]).forEach(k => {
                            if (!comp[k]) comp[k] = {
                                kode: k,
                                name: DATA.errors[k] ? DATA.errors[k].name : k,
                                j1: 0,
                                j2: 0,
                                both: 0,
                                only1: 0,
                                only2: 0
                            };
                            const h1 = s1.has(k);
                            const h2 = s2.has(k);
                            if (h1) comp[k].j1++;
                            if (h2) comp[k].j2++;
                            if (h1 && h2) comp[k].both++;
                            else if (h1) comp[k].only1++;
                            else if (h2) comp[k].only2++;
                        });
                    });
                    const compArr = Object.values(comp).sort((a, b) => (b.j1 + b.j2) - (a.j1 + a.j2));

                    doc.setFont(undefined, 'bold');
                    doc.setFontSize(10.5);
                    doc.setTextColor(30, 60, 120);
                    doc.text(`${secIndex++}. Grafik & Perbandingan Penilaian (Juri 1 vs Juri 2)`, M, y + 2);
                    y += 5;

                    // Render chart VS off-screen secara sinkron
                    try {
                        const imgDataVs = getChartVsImage(compArr);
                        if (imgDataVs) {
                            const imgW = orientation === 'landscape' ? Math.min(CW, 220) : CW;
                            const imgH = (360 / 1000) * imgW;
                            if (y + imgH > H - 25) {
                                doc.addPage();
                                y = M;
                            }
                            doc.addImage(imgDataVs, 'PNG', M + (CW - imgW) / 2, y, imgW, imgH);
                            y += imgH + 5;
                        }
                    } catch (e) {
                        console.warn('Gagal memuat chart VS ke PDF:', e);
                    }

                    if (y > H - 35) {
                        doc.addPage();
                        y = M;
                    }

                    const bodyVs = compArr.map((item, idx) => {
                        const tot = item.both + item.only1 + item.only2;
                        const pct = tot > 0 ? Math.round((item.both / tot) * 100) + '%' : '100%';
                        return [
                            idx + 1,
                            item.kode,
                            cleanPdfText(item.name),
                            item.j1,
                            item.j2,
                            item.both,
                            item.only1,
                            item.only2,
                            pct
                        ];
                    });

                    doc.autoTable({
                        startY: y,
                        margin: {
                            left: M,
                            right: M
                        },
                        head: [
                            ['No', 'Kode', 'Nama Kesalahan', 'J1', 'J2', 'Keduanya', 'Hanya J1', 'Hanya J2', 'Sepakat']
                        ],
                        body: bodyVs,
                        theme: 'striped',
                        headStyles: {
                            fillColor: [30, 60, 120],
                            fontSize: 7.5,
                            fontStyle: 'bold'
                        },
                        bodyStyles: {
                            fontSize: 7.5
                        },
                        columnStyles: {
                            0: {
                                halign: 'center',
                                cellWidth: 8
                            },
                            1: {
                                halign: 'center',
                                cellWidth: 16
                            },
                            3: {
                                halign: 'center',
                                cellWidth: 14
                            },
                            4: {
                                halign: 'center',
                                cellWidth: 14
                            },
                            5: {
                                halign: 'center',
                                cellWidth: 18
                            },
                            6: {
                                halign: 'center',
                                cellWidth: 16
                            },
                            7: {
                                halign: 'center',
                                cellWidth: 16
                            },
                            8: {
                                halign: 'center',
                                cellWidth: 18
                            }
                        }
                    });

                    y = doc.lastAutoTable.finalY + 8;
                }

                // Render Detail Santri if requested
                if (incSantri) {
                    if (y > H - 60) {
                        doc.addPage();
                        y = M;
                    }
                    doc.setFont(undefined, 'bold');
                    doc.setFontSize(11);
                    doc.setTextColor(30, 60, 120);
                    doc.text(`${secIndex++}. Daftar Catatan Kesalahan per Santri`, M, y + 2);
                    y += 5;

                    const showScore = $('chkShowScoreSantri') && $('chkShowScoreSantri').checked;
                    const bodySantri = rows.map((r, idx) => {
                        const j1 = ((r.j && r.j['1']) || []).map(k => DATA.errors[k] ? DATA.errors[k].name : k).join('; ');
                        const j2 = ((r.j && r.j['2']) || []).map(k => DATA.errors[k] ? DATA.errors[k].name : k).join('; ');
                        const s1 = new Set((r.j && r.j['1']) || []);
                        const s2 = new Set((r.j && r.j['2']) || []);
                        let status = 'Beda';
                        if (s1.size === s2.size && [...s1].every(k => s2.has(k))) status = 'Sepakat';
                        else if ([...s1].some(k => s2.has(k))) status = 'Parsial';

                        if (showScore) {
                            const sc1 = (r.s && r.s['1'] !== null && r.s['1'] !== undefined) ? r.s['1'] : '-';
                            const sc2 = (r.s && r.s['2'] !== null && r.s['2'] !== undefined) ? r.s['2'] : '-';
                            const scAvg = (r.avg !== null && r.avg !== undefined) ? r.avg : '-';
                            const scoreTxt = `J1:${sc1} J2:${sc2} (${scAvg})`;

                            return [
                                idx + 1,
                                cleanPdfText(r.np),
                                hideName ? '[Nama Disembunyikan]' : cleanPdfText(r.nm),
                                cleanPdfText(DATA.tpqs[r.tpq] || r.tpq),
                                cleanPdfText(scoreTxt),
                                cleanPdfText(j1 || '-'),
                                cleanPdfText(j2 || '-'),
                                status
                            ];
                        }

                        return [
                            idx + 1,
                            cleanPdfText(r.np),
                            hideName ? '[Nama Disembunyikan]' : cleanPdfText(r.nm),
                            cleanPdfText(DATA.tpqs[r.tpq] || r.tpq),
                            cleanPdfText(j1 || '-'),
                            cleanPdfText(j2 || '-'),
                            status
                        ];
                    });

                    const santriHead = showScore
                        ? [['No', 'No Peserta', 'Nama Santri', 'TPQ', 'Nilai (J1/J2/Avg)', 'Catatan Juri 1', 'Catatan Juri 2', 'Status']]
                        : [['No', 'No Peserta', 'Nama Santri', 'TPQ', 'Catatan Juri 1', 'Catatan Juri 2', 'Status']];

                    const colStyles = showScore ? {
                        0: { halign: 'center', cellWidth: 8 },
                        1: { halign: 'center', cellWidth: 20 },
                        4: { halign: 'center', cellWidth: 24 },
                        7: { halign: 'center', cellWidth: 16 }
                    } : {
                        0: { halign: 'center', cellWidth: 8 },
                        1: { halign: 'center', cellWidth: 22 },
                        6: { halign: 'center', cellWidth: 16 }
                    };

                    doc.autoTable({
                        startY: y,
                        margin: {
                            left: M,
                            right: M
                        },
                        head: santriHead,
                        body: bodySantri,
                        theme: 'striped',
                        headStyles: {
                            fillColor: [70, 80, 95],
                            fontSize: 7.5,
                            fontStyle: 'bold'
                        },
                        bodyStyles: {
                            fontSize: 7
                        },
                        columnStyles: colStyles
                    });

                    y = doc.lastAutoTable.finalY + 8;
                }

                // Render Catatan Teks Bebas Juri if requested
                const incTeks = $('chkPdfIncludeTeks') && $('chkPdfIncludeTeks').checked;
                if (incTeks) {
                    const listTeks = [];
                    rows.forEach(r => {
                        const t1 = (r.t && r.t['1']) ? r.t['1'].join('; ') : '';
                        const t2 = (r.t && r.t['2']) ? r.t['2'].join('; ') : '';
                        if (!t1 && !t2) return;

                        listTeks.push({
                            np: r.np,
                            nm: r.nm,
                            tpq: DATA.tpqs[r.tpq] || r.tpq,
                            t1: t1,
                            t2: t2
                        });
                    });

                    if (listTeks.length > 0) {
                        if (y > H - 55) {
                            doc.addPage();
                            y = M;
                        }
                        doc.setFont(undefined, 'bold');
                        doc.setFontSize(11);
                        doc.setTextColor(30, 60, 120);
                        doc.text(`${secIndex++}. Catatan Teks Bebas / Khusus Juri`, M, y + 2);
                        y += 5;

                        const bodyTeks = listTeks.map((item, idx) => [
                            idx + 1,
                            cleanPdfText(item.np),
                            hideName ? '[Nama Disembunyikan]' : cleanPdfText(item.nm),
                            cleanPdfText(item.tpq),
                            cleanPdfText(item.t1 || '-'),
                            cleanPdfText(item.t2 || '-')
                        ]);

                        doc.autoTable({
                            startY: y,
                            margin: {
                                left: M,
                                right: M
                            },
                            head: [
                                ['No', 'No Peserta', 'Nama Santri', 'TPQ', 'Teks Catatan Juri 1', 'Teks Catatan Juri 2']
                            ],
                            body: bodyTeks,
                            theme: 'striped',
                            headStyles: {
                                fillColor: [100, 110, 125],
                                fontSize: 7.5,
                                fontStyle: 'bold'
                            },
                            bodyStyles: {
                                fontSize: 7
                            },
                            columnStyles: {
                                0: {
                                    halign: 'center',
                                    cellWidth: 8
                                },
                                1: {
                                    halign: 'center',
                                    cellWidth: 20
                                },
                                2: {
                                    cellWidth: orientation === 'landscape' ? 45 : 35
                                },
                                3: {
                                    cellWidth: orientation === 'landscape' ? 40 : 30
                                }
                            }
                        });

                        y = doc.lastAutoTable.finalY + 8;
                    }
                }

                // Nomor Halaman Footer
                const pageCount = doc.internal.getNumberOfPages();
                for (let i = 1; i <= pageCount; i++) {
                    doc.setPage(i);
                    doc.setFontSize(7.5);
                    doc.setTextColor(150, 150, 150);
                    doc.text(`Halaman ${i} dari ${pageCount} | TPQ Smart System`, W / 2, H - 6, {
                        align: 'center'
                    });
                }

                doc.save(`Analisis_Catatan_Juri_${activeCatName.replace(/\s+/g, '_')}.pdf`);
            } catch (err) {
                alert('Gagal membuat file PDF: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = oldHtml;
            }
        }

        // Initialize Select2
        if (window.jQuery && window.jQuery.fn.select2) {
            window.jQuery('#filterTahunAjaran').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Tahun Ajaran...',
                allowClear: true,
                closeOnSelect: false,
                width: '100%'
            });
            window.jQuery('#filterTypeUjian').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih Type Ujian...',
                allowClear: true,
                closeOnSelect: false,
                width: '100%'
            });

            const currentTahunAjaran = <?= json_encode($current_tahun_ajaran ?? '') ?>;
            const isAdmin = <?= ($isAdmin ?? false) ? 'true' : 'false' ?>;
            const aktiveTombolKelulusan = <?= ($aktiveTombolKelulusan ?? false) ? 'true' : 'false' ?>;

            function updateTypeUjianOptionsStatistik() {
                const $ta = window.jQuery('#filterTahunAjaran');
                const $type = window.jQuery('#filterTypeUjian');
                const selectedYears = $ta.val() || [];
                const hasOldYear = selectedYears.some(y => y && currentTahunAjaran && y !== currentTahunAjaran);
                const canAccessMunaqosah = isAdmin || hasOldYear || aktiveTombolKelulusan;

                const hasMunaqosahOpt = $type.find('option[value="munaqosah"]').length > 0;
                if (canAccessMunaqosah) {
                    if (!hasMunaqosahOpt) {
                        $type.append(new Option('Munaqosah', 'munaqosah', false, false));
                        $type.trigger('change.select2');
                    }
                } else {
                    if (hasMunaqosahOpt) {
                        let curVals = $type.val() || [];
                        if (curVals.includes('munaqosah')) {
                            curVals = curVals.filter(v => v !== 'munaqosah');
                            if (curVals.length === 0) curVals = ['pra-munaqosah'];
                            $type.val(curVals);
                        }
                        $type.find('option[value="munaqosah"]').remove();
                        $type.trigger('change.select2');
                    }
                }
            }

            updateTypeUjianOptionsStatistik();
            window.jQuery('#filterTahunAjaran').on('change', updateTypeUjianOptionsStatistik);
        }

        // Auto load initial data
        loadData();
    })();
</script>
<?= $this->endSection(); ?>
