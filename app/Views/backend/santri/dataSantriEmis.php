<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <!-- Card Utama -->
    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h3 class="card-title font-weight-bold my-1">
                    <i class="fas fa-sync-alt text-primary mr-1"></i> Transfer Data Santri ke EMIS 4.0 Kemenag
                    <?php
                        $taLabel = !empty($IdTahunAjaran) && strlen($IdTahunAjaran) === 8
                            ? substr($IdTahunAjaran, 0, 4) . '/' . substr($IdTahunAjaran, 4, 4)
                            : ($IdTahunAjaran ?? '-');
                        $semLabel = $Semester ?? '-';
                    ?>
                    <span class="badge badge-info ml-2" title="Periode aktif pencatatan status EMIS">
                        <i class="fas fa-calendar-alt mr-1"></i> TA <?= htmlspecialchars($taLabel); ?> · Semester <?= htmlspecialchars($semLabel); ?>
                    </span>
                </h3>
                <div class="card-tools d-flex align-items-center flex-wrap gap-2">
                    <span id="extStatusBadge" class="badge badge-secondary p-2 mr-2">
                        <i class="fas fa-circle-notch fa-spin mr-1"></i> Cek Ekstensi EMIS...
                    </span>
                    <button type="button" class="btn btn-outline-info btn-sm mr-2" data-toggle="modal" data-target="#modalPanduanExtension">
                        <i class="fas fa-puzzle-piece mr-1"></i> Info Ekstensi Chrome
                    </button>
                    <a href="https://emis.kemenag.go.id/search-data-ppdb/baru" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm">
                        <i class="fas fa-external-link-alt mr-1"></i> Form PPDB EMIS 4.0
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Filter & Summary Bar -->
            <div class="row mb-3 align-items-end">
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="font-weight-bold text-muted small mb-1"><i class="fas fa-filter mr-1"></i> Filter Kelas:</label>
                    <select id="filterKelasEmis" class="form-control select2bs4">
                        <option value="">-- Semua Kelas --</option>
                        <?php if (!empty($dataKelas)) : ?>
                            <?php foreach ($dataKelas as $k) : ?>
                                <?php 
                                    $val = $k['NamaKelas'];
                                    $raw = $k['NamaKelasRaw'] ?? $val;
                                    $label = $val;
                                    if ($raw !== $val) {
                                        $label = $val . ' (' . $raw . ')';
                                    }
                                ?>
                                <option value="<?= htmlspecialchars($val); ?>" data-raw="<?= htmlspecialchars($raw); ?>">
                                    <?= htmlspecialchars($label); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="font-weight-bold text-muted small mb-1"><i class="fas fa-tasks mr-1"></i> Status di EMIS:</label>
                    <select id="filterStatusEmis" class="form-control">
                        <option value="">-- Semua Status --</option>
                        <option value="belum">Belum Disalin</option>
                        <option value="selesai">Sudah Disalin (Selesai)</option>
                        <option value="konflik">Konflik Lembaga Lain</option>
                    </select>
                </div>
                <div class="col-md-6 col-sm-12 mb-2 text-md-right">
                    <?php
                        $totalSantri = count($dataSantri);
                        $totalSelesai = 0;
                        $totalKonflik = 0;
                        foreach ($dataSantri as $s) {
                            $st = $s['StatusEmis'] ?? 'belum';
                            if ($st === 'selesai') {
                                $totalSelesai++;
                            } elseif ($st === 'konflik') {
                                $totalKonflik++;
                            }
                        }
                        $totalBelum = $totalSantri - $totalSelesai - $totalKonflik;
                    ?>
                    <div class="btn-group flex-wrap" role="group">
                        <button type="button" class="btn btn-light border btn-sm" disabled>
                            Total: <strong id="statTotalSantri"><?= $totalSantri; ?></strong>
                        </button>
                        <button type="button" class="btn btn-success btn-sm" disabled>
                            <i class="fas fa-check-circle mr-1"></i> Selesai: <strong id="statSelesaiSantri"><?= $totalSelesai; ?></strong>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" disabled>
                            <i class="fas fa-exclamation-triangle mr-1"></i> Konflik: <strong id="statKonflikSantri"><?= $totalKonflik; ?></strong>
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" disabled>
                            <i class="fas fa-clock mr-1"></i> Belum: <strong id="statBelumSantri"><?= $totalBelum; ?></strong>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Toolbar Aksi Masal & Panel Progress Antrean -->
            <div class="d-flex flex-wrap align-items-center justify-content-between bg-light border rounded p-2 mb-3" style="gap: 8px;">
                <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                    <button type="button" id="btnInputMasalEmis" class="btn btn-success btn-sm font-weight-bold" onclick="mulaiInputMasalEmis()" disabled>
                        <i class="fas fa-rocket mr-1"></i> Input Masal ke EMIS (<span id="countCheckedSantri">0</span>)
                    </button>
                    <button type="button" id="btnClearCheckedSantri" class="btn btn-outline-secondary btn-sm" onclick="clearCheckedSantriEmis()" style="display: none;">
                        <i class="fas fa-times mr-1"></i> Batal Pilih
                    </button>
                    <span class="small text-muted d-none d-md-inline">Centang santri pada tabel untuk memproses input data ke EMIS secara otomatis berurutan.</span>
                </div>
            </div>

            <div id="batchProgressPanel" class="alert alert-info border border-info shadow-sm mb-3" style="display: none;">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2" style="gap: 8px;">
                    <div>
                        <strong id="batchProgressTitle"><i class="fas fa-spinner fa-spin mr-1"></i> Mempersiapkan antrean masal...</strong>
                        <div class="small mt-1" id="batchProgressStats">Selesai: 0 | Konflik: 0 | Tunda: 0</div>
                    </div>
                    <div class="d-flex" style="gap: 6px;">
                        <button type="button" id="btnPauseBatchEmis" class="btn btn-warning btn-sm font-weight-bold" onclick="togglePauseBatchEmis()">
                            <i class="fas fa-pause mr-1"></i> Jeda
                        </button>
                        <button type="button" id="btnStopBatchEmis" class="btn btn-danger btn-sm font-weight-bold" onclick="hentikanBatchEmis()">
                            <i class="fas fa-stop mr-1"></i> Hentikan Antrean
                        </button>
                    </div>
                </div>
                <div class="progress" style="height: 10px;">
                    <div id="batchProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;"></div>
                </div>
            </div>

            <!-- Tabel Data Santri -->
            <div class="table-responsive">
                <table id="tblSantriEmis" class="table table-bordered table-hover table-striped">
                    <thead class="bg-light">
                        <tr class="text-center">
                            <th width="4%" class="text-center">
                                <input type="checkbox" id="checkAllSantriEmis" title="Pilih semua santri yang tampil" style="width: 16px; height: 16px; cursor: pointer;">
                            </th>
                            <th width="5%">No</th>
                            <th width="14%">Aksi EMIS</th>
                            <th width="12%">Status EMIS</th>
                            <th width="14%">NIK</th>
                            <th width="19%">Nama Santri</th>
                            <th width="10%">Kelas</th>
                            <th width="13%">TTL</th>
                            <th width="9%">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($dataSantri as $index => $santri) : ?>
                            <tr id="row-santri-<?= $santri['IdSantri']; ?>" data-id="<?= $santri['IdSantri']; ?>" data-kelas="<?= htmlspecialchars($santri['NamaKelas'] ?? ''); ?>" data-kelas-raw="<?= htmlspecialchars($santri['NamaKelasRaw'] ?? ($santri['NamaKelas'] ?? '')); ?>" data-idkelas="<?= $santri['IdKelas'] ?? ''; ?>">
                                <td class="text-center align-middle">
                                    <input type="checkbox" class="cb-santri-emis" value="<?= $santri['IdSantri']; ?>" data-nama="<?= htmlspecialchars($santri['NamaSantri']); ?>" style="width: 16px; height: 16px; cursor: pointer;">
                                </td>
                                <td class="text-center align-middle font-weight-bold"><?= $no++; ?></td>
                                <td class="align-middle text-center">
                                    <div class="d-flex flex-column" style="gap: 5px;">
                                        <button type="button" onclick="kirimKeEmisExtension('<?= $santri['IdSantri']; ?>')" class="btn btn-success btn-sm btn-send-emis" title="Isi formulir pendaftaran baru ke EMIS via ekstensi Chrome">
                                            <i class="fas fa-bolt mr-1"></i> <strong>Isi ke EMIS</strong>
                                        </button>
                                        <button type="button" onclick="kirimUpdateAlamatKeEmis('<?= $santri['IdSantri']; ?>')" class="btn btn-primary btn-sm btn-update-alamat-emis" title="Update data domisili dan alamat santri & orang tua ke EMIS">
                                            <i class="fas fa-map-marker-alt mr-1"></i> <strong>Update Alamat</strong>
                                        </button>
                                    </div>
                                </td>
                                <td class="align-middle text-center" id="col-status-<?= $santri['IdSantri']; ?>">
                                    <?php 
                                        $statusEmis = $santri['StatusEmis'] ?? 'belum';
                                        $tglUpdate = !empty($santri['TglUpdateEmis']) ? date('d M Y, H:i', strtotime($santri['TglUpdateEmis'])) : '';
                                        $isAlamatSelesai = ($santri['StatusAlamatEmis'] ?? 'belum') === 'selesai';
                                        $tglUpdateAlamat = !empty($santri['TglUpdateAlamatEmis']) ? date('d M Y, H:i', strtotime($santri['TglUpdateAlamatEmis'])) : '';
                                        $catatanEmis = trim($santri['CatatanEmis'] ?? '');

                                        $badgeDaftarClass = 'badge-warning';
                                        $badgeDaftarIcon = 'fa-hourglass-half';
                                        $badgeDaftarText = 'Belum';
                                        if ($statusEmis === 'selesai') {
                                            $badgeDaftarClass = 'badge-success';
                                            $badgeDaftarIcon = 'fa-check-circle';
                                            $badgeDaftarText = 'Selesai';
                                        } elseif ($statusEmis === 'konflik') {
                                            $badgeDaftarClass = 'badge-danger';
                                            $badgeDaftarIcon = 'fa-exclamation-triangle';
                                            $badgeDaftarText = 'Konflik';
                                        }
                                    ?>
                                    <div class="mb-1" id="box-status-daftar-<?= $santri['IdSantri']; ?>">
                                        <span class="badge <?= $badgeDaftarClass; ?> p-1 status-badge cursor-pointer" onclick="openModalUbahStatusEmis('<?= $santri['IdSantri']; ?>')" title="Klik untuk ubah status pendaftaran / catatan">
                                            <i class="fas <?= $badgeDaftarIcon; ?> mr-1"></i> Daftar: <?= $badgeDaftarText; ?>
                                        </span>
                                        <?php if (!empty($tglUpdate)): ?>
                                            <small class="text-muted d-block status-time" style="font-size: 10px;">
                                                <i class="far fa-clock mr-1"></i><?= $tglUpdate; ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div id="box-status-alamat-<?= $santri['IdSantri']; ?>">
                                        <span class="badge <?= $isAlamatSelesai ? 'badge-info' : 'badge-secondary'; ?> p-1 status-badge cursor-pointer" onclick="toggleStatusAlamatSantriEmis('<?= $santri['IdSantri']; ?>')" title="Klik untuk ubah status alamat">
                                            <i class="fas <?= $isAlamatSelesai ? 'fa-map-marked-alt' : 'fa-map-marker-alt'; ?> mr-1"></i> Alamat: <?= $isAlamatSelesai ? 'Selesai' : 'Belum'; ?>
                                        </span>
                                        <?php if (!empty($tglUpdateAlamat)): ?>
                                            <small class="text-muted d-block status-time-alamat" style="font-size: 10px;">
                                                <i class="far fa-clock mr-1"></i><?= $tglUpdateAlamat; ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div id="box-catatan-<?= $santri['IdSantri']; ?>" class="mt-1">
                                        <?php if (!empty($catatanEmis)): ?>
                                            <div class="p-1 px-2 rounded bg-light border border-danger text-danger text-left small cursor-pointer" onclick="openModalUbahStatusEmis('<?= $santri['IdSantri']; ?>')" style="font-size: 11px; max-width: 170px; margin: 0 auto; line-height: 1.2;" title="Klik untuk edit status / catatan">
                                                <i class="fas fa-exclamation-circle text-danger mr-1"></i><span class="catatan-text"><?= htmlspecialchars($catatanEmis); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="align-middle font-monospace"><?= htmlspecialchars($santri['NikSantri'] ?: '-'); ?></td>
                                <td class="align-middle font-weight-bold text-dark">
                                    <?= htmlspecialchars(ucwords(strtolower($santri['NamaSantri']))); ?>
                                    <div class="small text-muted">
                                        <i class="fas fa-venus-mars mr-1"></i> <?= htmlspecialchars($santri['JenisKelamin'] ?? '-'); ?>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-secondary"><?= htmlspecialchars($santri['NamaKelas'] ?? '-'); ?></span>
                                    <?php if (!empty($santri['NamaKelasRaw']) && $santri['NamaKelasRaw'] !== $santri['NamaKelas']) : ?>
                                        <small class="text-muted d-block" style="font-size: 10px;"><?= htmlspecialchars($santri['NamaKelasRaw']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle small">
                                    <?= htmlspecialchars($santri['TempatLahirSantri'] ?? '-'); ?><br>
                                    <span class="text-muted"><?= htmlspecialchars($santri['TanggalLahirSantri'] ?? '-'); ?></span>
                                </td>
                                <td class="align-middle text-center">
                                    <button type="button" onclick="showUpdateEmisSantri('<?= $santri['IdSantri']; ?>')" class="btn btn-outline-secondary btn-sm" title="Lihat detail lengkap santri">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- ============================================================== -->
<!-- MODAL: PANDUAN EKSTENSI CHROME (Phase 3 Helper)                 -->
<!-- ============================================================== -->
<div class="modal fade" id="modalPanduanExtension" tabindex="-1" role="dialog" aria-labelledby="modalPanduanExtensionTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold" id="modalPanduanExtensionTitle">
                    <i class="fas fa-puzzle-piece mr-2"></i> Ekstensi Chrome: TPQ Smart ke EMIS Bridge
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-light border mb-4">
                    <h6 class="font-weight-bold text-dark"><i class="fas fa-magic text-warning mr-1"></i> Apa itu Ekstensi Ini?</h6>
                    <p class="mb-0 text-muted">
                        Ekstensi browser ini berfungsi sebagai jembatan semi-otomatis. Saat Anda menekan tombol <strong>⚡ Isi ke EMIS</strong> di TPQ Smart, ekstensi akan secara otomatis mengisikan seluruh data santri ke formulir EMIS 4.0 yang sedang Anda buka, sehingga Anda tidak perlu mengetik atau copy-paste satu per satu.
                    </p>
                </div>

                <div class="mb-4 text-center">
                    <a href="<?= site_url('backend/santri/downloadExtensionEmis'); ?>" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-file-archive mr-1"></i> <strong>Unduh File ZIP Ekstensi (extension-emis.zip)</strong>
                    </a>
                    <div class="small text-muted mt-2">
                        <i class="fas fa-info-circle text-info mr-1"></i> Ekstrak file ZIP ini di komputer Anda, lalu muat folder hasil ekstraknya di browser Chrome / Edge.
                    </div>
                </div>

                <h6 class="font-weight-bold mb-3"><i class="fas fa-list-ol text-primary mr-1"></i> Langkah Pemasangan Ekstensi di Chrome / Edge:</h6>
                <ol class="pl-3 mb-4">
                    <li class="mb-2">Unduh file ZIP ekstensi dengan menekan tombol biru <strong>Unduh File ZIP Ekstensi</strong> di atas.</li>
                    <li class="mb-2">Buka folder hasil unduhan di komputer Anda (misalnya di folder <code>Downloads</code>), lalu <strong>Ekstrak (Extract All / Extract Here)</strong> file <code>extension-emis.zip</code> tersebut.</li>
                    <li class="mb-2">Buka browser <strong>Google Chrome</strong> atau <strong>Microsoft Edge</strong> di komputer Anda.</li>
                    <li class="mb-2">Ketik alamat <code>chrome://extensions</code> (atau <code>edge://extensions</code>) pada bar alamat browser lalu tekan <strong>Enter</strong>.</li>
                    <li class="mb-2">Nyalakan tombol saklar <strong>"Developer mode" (Mode Pengembang)</strong> di pojok kanan atas layar.</li>
                    <li class="mb-2">Klik tombol <strong>"Load unpacked" (Muat yang belum dibongkar)</strong> di pojok kiri atas.</li>
                    <li class="mb-2">Pilih folder <code>extension-emis</code> hasil ekstrak yang ada di komputer Anda.</li>
                    <li class="mb-2">Selesai! Ekstensi <strong>TPQ Smart - EMIS 4.0 Bridge</strong> sekarang sudah aktif dan siap digunakan.</li>
                </ol>

                <div class="card bg-light border p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Status Deteksi Saat Ini:</strong><br>
                            <span id="guideExtStatus" class="badge badge-secondary p-2 mt-1">Mengecek koneksi...</span>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="cekKoneksiExtension(true)">
                            <i class="fas fa-sync-alt mr-1"></i> Tes Ulang Koneksi
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: UBAH STATUS PENDAFTARAN & CATATAN EMIS                  -->
<!-- ============================================================== -->
<div class="modal fade" id="modalUbahStatusEmis" tabindex="-1" role="dialog" aria-labelledby="modalUbahStatusEmisLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="modalUbahStatusEmisLabel">
                    <i class="fas fa-user-edit mr-2"></i> Ubah Status Pendaftaran EMIS
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="modalStatusIdSantri">

                <!-- Info Santri Singkat -->
                <div class="bg-light p-3 rounded border mb-3">
                    <div class="font-weight-bold text-primary" style="font-size: 15px;" id="modalStatusNamaSantri">-</div>
                    <div class="d-flex flex-wrap justify-content-between small text-muted mt-1">
                        <div>NIK: <strong id="modalStatusNikSantri" class="text-dark font-monospace">-</strong></div>
                        <div>Kelas: <strong id="modalStatusKelasSantri" class="text-dark">-</strong></div>
                    </div>
                </div>

                <!-- Pilihan Status Pendaftaran -->
                <label class="font-weight-bold mb-2 text-dark">
                    <i class="fas fa-tasks text-muted mr-1"></i> Pilih Status Pendaftaran di EMIS:
                </label>
                <div class="list-group mb-3">
                    <label class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer mb-2 rounded border" style="padding: 10px 14px;">
                        <input class="form-check-input mr-3 ml-0" type="radio" name="radioStatusEmis" value="belum" id="radioStatusBelum">
                        <div class="w-100 ml-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold text-dark"><i class="fas fa-clock text-warning mr-1"></i> Belum Disalin</span>
                                <span class="badge badge-warning">Belum</span>
                            </div>
                            <small class="text-muted d-block mt-1">Santri masih dalam antrean / belum diproses ke EMIS 4.0.</small>
                        </div>
                    </label>

                    <label class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer mb-2 rounded border" style="padding: 10px 14px;">
                        <input class="form-check-input mr-3 ml-0" type="radio" name="radioStatusEmis" value="selesai" id="radioStatusSelesai">
                        <div class="w-100 ml-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold text-success"><i class="fas fa-check-circle text-success mr-1"></i> Sudah Berhasil (Selesai)</span>
                                <span class="badge badge-success">Selesai</span>
                            </div>
                            <small class="text-muted d-block mt-1">Data santri telah berhasil disalin / didaftarkan ke EMIS 4.0.</small>
                        </div>
                    </label>

                    <label class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer rounded border border-danger" style="padding: 10px 14px; background-color: #fff8f8;">
                        <input class="form-check-input mr-3 ml-0" type="radio" name="radioStatusEmis" value="konflik" id="radioStatusKonflik">
                        <div class="w-100 ml-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold text-danger"><i class="fas fa-exclamation-triangle text-danger mr-1"></i> Konflik Lembaga Lain (Gagal)</span>
                                <span class="badge badge-danger">Konflik</span>
                            </div>
                            <small class="text-muted d-block mt-1">Santri tidak bisa didaftarkan karena sudah terdaftar di lembaga lain atau data bentrok.</small>
                        </div>
                    </label>
                </div>

                <!-- Input Catatan / Keterangan -->
                <div class="form-group mb-2">
                    <label class="font-weight-bold text-dark mb-1" for="modalStatusCatatan">
                        <i class="fas fa-sticky-note text-muted mr-1"></i> Catatan / Keterangan Masalah:
                    </label>
                    <textarea class="form-control" id="modalStatusCatatan" rows="3" placeholder="Tuliskan keterangan detail (misal: Sudah aktif di TPQ Al-Huda / NIK ganda di sekolah lain / menunggu mutasi)..."></textarea>
                </div>

                <!-- Quick tags template -->
                <div class="small mb-1 text-muted"><i class="fas fa-lightbulb text-warning mr-1"></i> Templat Keterangan Cepat (klik untuk mengisi):</div>
                <div class="d-flex flex-wrap" style="gap: 5px;">
                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="isiTemplateCatatan('Aktif di lembaga lain')">
                        + Aktif di lembaga lain
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="isiTemplateCatatan('NIK bentrok / sudah terdaftar')">
                        + NIK bentrok
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="isiTemplateCatatan('Menunggu mutasi EMIS')">
                        + Menunggu mutasi
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="isiTemplateCatatan('Data tidak valid di Dukcapil')">
                        + Dukcapil tidak valid
                    </button>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary font-weight-bold" id="btnSimpanStatusEmis">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: UPDATE / DETAIL LENGKAP SANTRI (Original Preserved)      -->
<!-- ============================================================== -->
<div class="modal fade" id="updateDataEmisModal" tabindex="-1" role="dialog" aria-labelledby="detailSantriModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailSantriModalLabel">Detail Lengkap Data Santri</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="detailSantriTabUpdate" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="data-santri-tabUpdate" data-toggle="tab" href="#data-santriUpdate" role="tab">
                            <i class="fas fa-user"></i> Data Santri
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="data-ortu-tabUpdate" data-toggle="tab" href="#data-ortuUpdate" role="tab">
                            <i class="fas fa-users"></i> Data Orang Tua
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="data-alamat-tabUpdate" data-toggle="tab" href="#data-alamatUpdate" role="tab">
                            <i class="fas fa-map-marker-alt"></i> Data Alamat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="lampiran-tabUpdate" data-toggle="tab" href="#lampiranUpdate" role="tab">
                            <i class="fas fa-file"></i> Lampiran
                        </a>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="detailSantriTabContentUpdate">
                    <!-- Tab Data Santri -->
                    <div class="tab-pane fade show active" id="data-santriUpdate" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4 text-center mb-3">
                                <img id="modalPhotoProfilUpdate" src="" alt="Foto Profil" class="img-fluid rounded shadow-sm" style="max-width: 200px;">
                            </div>
                            <div class="col-md-8">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="35%">ID Santri</td>
                                        <td width="5%">:</td>
                                        <td id="modalIdSantriUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Nama TPQ</td>
                                        <td>:</td>
                                        <td id="modalNamaTpqUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Kelas</td>
                                        <td>:</td>
                                        <td id="modalKelasUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>NIK Santri</td>
                                        <td>:</td>
                                        <td id="modalNikSantriUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>No Kartu Keluarga</td>
                                        <td>:</td>
                                        <td id="modalIdKartuKeluargaUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>NISN</td>
                                        <td>:</td>
                                        <td id="modalNISUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Nama Santri</td>
                                        <td>:</td>
                                        <td id="modalNamaSantriUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Jenis Kelamin</td>
                                        <td>:</td>
                                        <td id="modalJenisKelaminUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>TTL</td>
                                        <td>:</td>
                                        <td id="modalTempatTanggalLahirUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>No HP & Email</td>
                                        <td>:</td>
                                        <td id="modalNoHpUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Saudara & Anak Ke</td>
                                        <td>:</td>
                                        <td id="modalJumlahSaudaraAnakKeUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Cita-cita</td>
                                        <td>:</td>
                                        <td id="modalCitaCitaUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Hobi</td>
                                        <td>:</td>
                                        <td id="modalHobiUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Kebutuhan Khusus</td>
                                        <td>:</td>
                                        <td id="modalKebutuhanKhususUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Kebutuhan Disabilitas</td>
                                        <td>:</td>
                                        <td id="modalKebutuhanDisabilitasUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Biaya Sekolah</td>
                                        <td>:</td>
                                        <td id="modalYangBiayaSekolahUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Kepala Keluarga</td>
                                        <td>:</td>
                                        <td id="modalNamaKepalaKeluargaUpdate" class="font-weight-bold"></td>
                                    </tr>
                                    <tr>
                                        <td>Status Verifikasi</td>
                                        <td>:</td>
                                        <td id="modalStatusUpdate" class="font-weight-bold"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Data Orang Tua -->
                    <div class="tab-pane fade" id="data-ortuUpdate" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-primary">Data Ayah</h6>
                                <table class="table table-borderless table-sm">
                                    <tr><td width="40%">Nama Ayah</td><td width="5%">:</td><td id="modalNamaAyahUpdate" class="font-weight-bold"></td></tr>
                                    <tr><td>Status Ayah</td><td>:</td><td id="modalStatusAyahUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ayah-hidup"><td>NIK Ayah</td><td>:</td><td id="modalNikAyahUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ayah-hidup"><td>TTL Ayah</td><td>:</td><td id="modalTempatTanggalLahirAyahUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ayah-hidup"><td>Pendidikan</td><td>:</td><td id="modalPendidikanTerakhirAyahUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ayah-hidup"><td>Pekerjaan</td><td>:</td><td id="modalPekerjaanAyahUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ayah-hidup"><td>Penghasilan</td><td>:</td><td id="modalPenghasilanAyahUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ayah-hidup"><td>No HP Ayah</td><td>:</td><td id="modalNoHpAyahUpdate" class="font-weight-bold"></td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-danger">Data Ibu</h6>
                                <table class="table table-borderless table-sm">
                                    <tr><td width="40%">Nama Ibu</td><td width="5%">:</td><td id="modalNamaIbuUpdate" class="font-weight-bold"></td></tr>
                                    <tr><td>Status Ibu</td><td>:</td><td id="modalStatusIbuUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ibu-hidup"><td>NIK Ibu</td><td>:</td><td id="modalNikIbuUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ibu-hidup"><td>TTL Ibu</td><td>:</td><td id="modalTempatTanggalLahirIbuUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ibu-hidup"><td>Pendidikan</td><td>:</td><td id="modalPendidikanTerakhirIbuUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ibu-hidup"><td>Pekerjaan</td><td>:</td><td id="modalPekerjaanIbuUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ibu-hidup"><td>Penghasilan</td><td>:</td><td id="modalPenghasilanIbuUpdate" class="font-weight-bold"></td></tr>
                                    <tr class="data-ibu-hidup"><td>No HP Ibu</td><td>:</td><td id="modalNoHpIbuUpdate" class="font-weight-bold"></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Data Alamat -->
                    <div class="tab-pane fade" id="data-alamatUpdate" role="tabpanel">
                        <h6 class="font-weight-bold text-primary mb-2"><i class="fas fa-home mr-1"></i> Domisili & Alamat Santri</h6>
                        <table class="table table-borderless table-sm mb-3">
                            <tr><td width="30%">Status Tempat Tinggal</td><td width="5%">:</td><td id="modalStatusTempatTinggalUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Status Mukim</td><td>:</td><td id="modalStatusMukimUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Alamat Santri</td><td>:</td><td id="modalAlamatUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>RT / RW</td><td>:</td><td><span id="modalRTUpdate" class="font-weight-bold"></span> / <span id="modalRWUpdate" class="font-weight-bold"></span></td></tr>
                            <tr><td>Kelurahan / Desa</td><td>:</td><td id="modalKelurahanDesaUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Kecamatan</td><td>:</td><td id="modalKecamatanUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Kabupaten / Kota</td><td>:</td><td id="modalKabupatenKotaUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Provinsi</td><td>:</td><td id="modalProvinsiUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Kode Pos</td><td>:</td><td id="modalKodePosUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Jarak & Waktu</td><td>:</td><td><span id="modalJarakTempuhUpdate" class="font-weight-bold"></span> (<span id="modalWaktuTempuhUpdate"></span>)</td></tr>
                            <tr><td>Transportasi</td><td>:</td><td id="modalTransportasiUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Titik Koordinat</td><td>:</td><td id="modalTitikKoordinatUpdate"></td></tr>
                        </table>

                        <hr class="my-2">
                        <h6 class="font-weight-bold text-success mb-2"><i class="fas fa-users mr-1"></i> Alamat Orang Tua / Ayah (Sumber EMIS)</h6>
                        <table class="table table-borderless table-sm mb-0">
                            <tr><td width="30%">Status Kepemilikan Rumah</td><td width="5%">:</td><td id="modalStatusRumahAyahUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Alamat Orang Tua</td><td>:</td><td id="modalAlamatAyahUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>RT / RW</td><td>:</td><td><span id="modalRTAyahUpdate" class="font-weight-bold"></span> / <span id="modalRWAyahUpdate" class="font-weight-bold"></span></td></tr>
                            <tr><td>Kelurahan / Desa</td><td>:</td><td id="modalKelurahanDesaAyahUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Kecamatan</td><td>:</td><td id="modalKecamatanAyahUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Kabupaten / Kota</td><td>:</td><td id="modalKabupatenKotaAyahUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Provinsi</td><td>:</td><td id="modalProvinsiAyahUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Kode Pos</td><td>:</td><td id="modalKodePosAyahUpdate" class="font-weight-bold"></td></tr>
                        </table>
                    </div>

                    <!-- Tab Lampiran -->
                    <div class="tab-pane fade" id="lampiranUpdate" role="tabpanel">
                        <table class="table table-bordered table-sm">
                            <tr><td width="40%">KK Santri</td><td id="modalLampiranKkSantriUpdate"></td></tr>
                            <tr><td>KK Ayah</td><td id="modalLampiranKkAyahUpdate"></td></tr>
                            <tr><td>KK Ibu</td><td id="modalLampiranKkIbuUpdate"></td></tr>
                            <tr><td>Kartu Indonesia Pintar (KIP)</td><td id="modalLampiranKIPUpdate"></td></tr>
                            <tr><td>Program Keluarga Harapan (PKH)</td><td id="modalLampiranPKHUpdate"></td></tr>
                            <tr><td>Kartu Keluarga Sejahtera (KKS)</td><td id="modalLampiranKKSUpdate"></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    // State aplikasi
    const dataSantriMaster = <?= json_encode($dataSantri); ?>;
    let dataTableSantri = null;
    let isExtensionConnected = false;

    // Inisialisasi Halaman
    $(document).ready(function() {
        // 1. Init DataTable
        dataTableSantri = $('#tblSantriEmis').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "pageLength": 25,
            "columnDefs": [
                { "orderable": false, "targets": [0, 2, 8] }
            ],
            "language": {
                "search": "Cari Santri:",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Tidak ada data santri ditemukan",
                "info": "Menampilkan _START_ s/d _END_ dari _TOTAL_ santri",
                "infoEmpty": "Tidak ada data",
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "›",
                    "previous": "‹"
                }
            }
        });

        initChecklistBatchHandlers();

        // 3. Filter Event Listener dengan penyimpanan ke localStorage
        $('#filterKelasEmis').on('change', function() {
            simpanFilterKeStorage();
            terapkanFilterKelas($(this).val());
            hitungUlangStatistik();
        });

        $('#filterStatusEmis').on('change', function() {
            simpanFilterKeStorage();
            terapkanFilterStatus($(this).val());
            hitungUlangStatistik();
        });

        // 4. Pulihkan selection filter terakhir dari localStorage
        muatFilterDariStorage();

        // 5. Update statistik dan cek koneksi ekstensi
        hitungUlangStatistik();
        cekKoneksiExtension(false);

        // 6. Handler tombol simpan perubahan status dari modal
        $('#btnSimpanStatusEmis').on('click', function() {
            simpanPerubahanStatusEmis();
        });
    });

    // ==============================================================
    // FITUR FILTER SELECTION STORAGE (localStorage)
    // ==============================================================
    const STORAGE_KEY_FILTERS = 'tpqsmart_emis_filter_selection';

    function simpanFilterKeStorage() {
        const filterState = {
            kelas: $('#filterKelasEmis').val() || '',
            status: $('#filterStatusEmis').val() || ''
        };
        localStorage.setItem(STORAGE_KEY_FILTERS, JSON.stringify(filterState));
    }

    function terapkanFilterKelas(val) {
        if (!dataTableSantri) return;
        if (!val) {
            dataTableSantri.column(6).search('').draw();
            return;
        }
        const selectedOpt = $('#filterKelasEmis').find('option:selected');
        const rawVal = selectedOpt.data('raw');
        let searchPattern = '';
        if (rawVal && rawVal !== val) {
            searchPattern = '(?:' + $.fn.dataTable.util.escapeRegex(val) + '|' + $.fn.dataTable.util.escapeRegex(rawVal) + ')';
        } else {
            searchPattern = $.fn.dataTable.util.escapeRegex(val);
        }
        dataTableSantri.column(6).search(searchPattern, true, false).draw();
    }

    function terapkanFilterStatus(val) {
        if (!dataTableSantri) return;
        if (val === 'selesai') {
            dataTableSantri.column(3).search('Daftar:\\s*Selesai', true, false).draw();
        } else if (val === 'belum') {
            dataTableSantri.column(3).search('Daftar:\\s*Belum', true, false).draw();
        } else if (val === 'konflik') {
            dataTableSantri.column(3).search('Daftar:\\s*Konflik', true, false).draw();
        } else {
            dataTableSantri.column(3).search('').draw();
        }
    }

    function muatFilterDariStorage() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY_FILTERS);
            if (!raw) return;
            const filterState = JSON.parse(raw);
            if (!filterState) return;

            let needsDraw = false;

            // Pulihkan Filter Kelas
            if (filterState.kelas !== undefined && filterState.kelas !== null && filterState.kelas !== '') {
                let matchOpt = $('#filterKelasEmis').find('option').filter(function() {
                    return $(this).val() === filterState.kelas || $(this).data('raw') === filterState.kelas;
                });

                if (matchOpt.length > 0) {
                    const matchedVal = matchOpt.first().val();
                    const rawVal = matchOpt.first().data('raw');
                    $('#filterKelasEmis').val(matchedVal);
                    if ($.fn.select2 && $('#filterKelasEmis').data('select2')) {
                        $('#filterKelasEmis').trigger('change.select2');
                    }
                    let searchPattern = '';
                    if (rawVal && rawVal !== matchedVal) {
                        searchPattern = '(?:' + $.fn.dataTable.util.escapeRegex(matchedVal) + '|' + $.fn.dataTable.util.escapeRegex(rawVal) + ')';
                    } else {
                        searchPattern = $.fn.dataTable.util.escapeRegex(matchedVal);
                    }
                    dataTableSantri.column(6).search(searchPattern, true, false);
                    needsDraw = true;
                }
            }

            // Pulihkan Filter Status
            if (filterState.status !== undefined && filterState.status !== null && filterState.status !== '') {
                $('#filterStatusEmis').val(filterState.status);
                if (filterState.status === 'selesai') {
                    dataTableSantri.column(3).search('Daftar:\\s*Selesai', true, false);
                } else if (filterState.status === 'belum') {
                    dataTableSantri.column(3).search('Daftar:\\s*Belum', true, false);
                } else if (filterState.status === 'konflik') {
                    dataTableSantri.column(3).search('Daftar:\\s*Konflik', true, false);
                }
                needsDraw = true;
            }

            if (needsDraw) {
                dataTableSantri.draw();
            }
        } catch (e) {
            console.warn('Gagal memulihkan filter dari localStorage:', e);
        }
    }

    // ==============================================================
    // HELPER & FITUR MODAL STATUS DATABASE (tbl_santri_emis)
    // ==============================================================

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function openModalUbahStatusEmis(idSantri) {
        const sObj = dataSantriMaster.find(s => String(s.IdSantri) === String(idSantri));
        if (!sObj) {
            console.error('Santri tidak ditemukan:', idSantri);
            return;
        }

        $('#modalStatusIdSantri').val(idSantri);
        $('#modalStatusNamaSantri').text(sObj.NamaSantri || '-');
        $('#modalStatusNikSantri').text(sObj.NikSantri || '-');
        $('#modalStatusKelasSantri').text(sObj.NamaKelas || '-');

        const currentStatus = (sObj.StatusEmis || 'belum').toLowerCase();
        if (currentStatus === 'selesai') {
            $('#radioStatusSelesai').prop('checked', true);
        } else if (currentStatus === 'konflik') {
            $('#radioStatusKonflik').prop('checked', true);
        } else {
            $('#radioStatusBelum').prop('checked', true);
        }

        $('#modalStatusCatatan').val(sObj.CatatanEmis || '');
        $('#modalUbahStatusEmis').modal('show');
    }

    function isiTemplateCatatan(teks) {
        const $catatan = $('#modalStatusCatatan');
        const currentVal = $catatan.val().trim();
        if (currentVal) {
            if (!currentVal.includes(teks)) {
                $catatan.val(currentVal + ', ' + teks);
            }
        } else {
            $catatan.val(teks);
        }
        // Jika template terkait kendala/bentrok, arahkan radio status ke Konflik otomatis
        if (teks.includes('lembaga') || teks.includes('bentrok') || teks.includes('Dukcapil') || teks.includes('mutasi')) {
            $('#radioStatusKonflik').prop('checked', true);
        }
    }

    function simpanPerubahanStatusEmis() {
        const idSantri = $('#modalStatusIdSantri').val();
        const status = $('input[name="radioStatusEmis"]:checked').val() || 'belum';
        const catatan = $('#modalStatusCatatan').val().trim();

        if (!idSantri) return;

        const $btn = $('#btnSimpanStatusEmis');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

        $.ajax({
            url: '<?= base_url('backend/santri/updateStatusEmisAjax'); ?>',
            method: 'POST',
            data: {
                IdSantri: idSantri,
                Status: status,
                Catatan: catatan
            },
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Perubahan');
                if (res.success) {
                    $('#modalUbahStatusEmis').modal('hide');
                    updateStatusBadgeUI(idSantri, res.status, res.tgl_update_label || '', res.catatan);
                    hitungUlangStatistik();

                    const sObj = dataSantriMaster.find(s => String(s.IdSantri) === String(idSantri));
                    const namaSantri = sObj && sObj.NamaSantri ? sObj.NamaSantri : 'Santri';

                    if (typeof toastr !== 'undefined') {
                        if (res.status === 'selesai') {
                            toastr.success(`Status ${namaSantri} berhasil disimpan: Selesai`, 'Status EMIS');
                        } else if (res.status === 'konflik') {
                            toastr.warning(`Status ${namaSantri} disimpan: Konflik Lembaga Lain`, 'Status EMIS');
                        } else {
                            toastr.info(`Status ${namaSantri} dikembalikan ke Belum`, 'Status EMIS');
                        }
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(res.message || 'Gagal menyimpan status.', 'Gagal');
                    }
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Perubahan');
                if (typeof toastr !== 'undefined') {
                    toastr.error('Terjadi kesalahan koneksi saat menyimpan status.', 'Error');
                }
            }
        });
    }

    function toggleStatusSantriEmis(idSantri) {
        // Alihkan klik badge status untuk membuka modal perubahan status & catatan
        openModalUbahStatusEmis(idSantri);
    }

    function setStatusSantriEmis(idSantri, statusInput, callback = null) {
        const newStatus = (typeof statusInput === 'string') 
            ? statusInput 
            : (statusInput ? 'selesai' : 'belum');

        $.ajax({
            url: '<?= base_url('backend/santri/updateStatusEmisAjax'); ?>',
            method: 'POST',
            data: {
                IdSantri: idSantri,
                Status: newStatus
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    updateStatusBadgeUI(idSantri, res.status, res.tgl_update_label || '', res.catatan);
                    hitungUlangStatistik();
                    if (typeof callback === 'function') {
                        callback(res);
                    }
                }
            }
        });
    }

    function updateStatusBadgeUI(idSantri, statusInput, tglUpdateLabel = '', catatan = null, refreshDataTable = true) {
        const box = document.getElementById('box-status-daftar-' + idSantri);
        if (!box) return;

        // Normalisasi status
        let normalizedStatus = 'belum';
        if (typeof statusInput === 'boolean') {
            normalizedStatus = statusInput ? 'selesai' : 'belum';
        } else if (typeof statusInput === 'string') {
            normalizedStatus = statusInput.toLowerCase();
        }

        // Update dataSantriMaster agar konsisten
        const sObj = dataSantriMaster.find(s => String(s.IdSantri) === String(idSantri));
        if (sObj) {
            sObj.StatusEmis = normalizedStatus;
            if (normalizedStatus === 'selesai' || normalizedStatus === 'konflik') {
                if (tglUpdateLabel) sObj.TglUpdateEmis = tglUpdateLabel;
            } else {
                sObj.TglUpdateEmis = null;
            }
            if (catatan !== null && catatan !== undefined) {
                sObj.CatatanEmis = catatan;
            }
        }

        let badgeClass = 'badge-warning';
        let badgeIcon = 'fa-hourglass-half';
        let badgeText = 'Belum';

        if (normalizedStatus === 'selesai') {
            badgeClass = 'badge-success';
            badgeIcon = 'fa-check-circle';
            badgeText = 'Selesai';
        } else if (normalizedStatus === 'konflik') {
            badgeClass = 'badge-danger';
            badgeIcon = 'fa-exclamation-triangle';
            badgeText = 'Konflik';
        }

        const timeHtml = ((normalizedStatus === 'selesai' || normalizedStatus === 'konflik') && tglUpdateLabel) 
            ? `<small class="text-muted d-block status-time" style="font-size: 10px;"><i class="far fa-clock mr-1"></i>${tglUpdateLabel}</small>` 
            : '';

        box.innerHTML = `
            <span class="badge ${badgeClass} p-1 status-badge cursor-pointer" onclick="openModalUbahStatusEmis('${idSantri}')" title="Klik untuk ubah status pendaftaran / catatan">
                <i class="fas ${badgeIcon} mr-1"></i> Daftar: ${badgeText}
            </span>
            ${timeHtml}
        `;

        // Update tampilan box catatan
        const boxCatatan = document.getElementById('box-catatan-' + idSantri);
        if (boxCatatan) {
            const currentCatatan = (catatan !== null && catatan !== undefined) 
                ? catatan 
                : (sObj ? (sObj.CatatanEmis || '') : '');
            
            if (currentCatatan && currentCatatan.trim() !== '') {
                boxCatatan.innerHTML = `
                    <div class="p-1 px-2 rounded bg-light border border-danger text-danger text-left small cursor-pointer" onclick="openModalUbahStatusEmis('${idSantri}')" style="font-size: 11px; max-width: 170px; margin: 0 auto; line-height: 1.2;" title="Klik untuk edit status / catatan">
                        <i class="fas fa-exclamation-circle text-danger mr-1"></i><span class="catatan-text">${escapeHtml(currentCatatan.trim())}</span>
                    </div>
                `;
            } else {
                boxCatatan.innerHTML = '';
            }
        }

        const cell = document.getElementById('col-status-' + idSantri);
        if (dataTableSantri && cell && refreshDataTable) {
            dataTableSantri.cell(cell).invalidate().draw(false);
        }
    }

    function toggleStatusAlamatSantriEmis(idSantri) {
        const sObj = dataSantriMaster.find(s => String(s.IdSantri) === String(idSantri));
        const currentIsSelesai = sObj ? (sObj.StatusAlamatEmis === 'selesai') : false;
        const newStatus = currentIsSelesai ? 'belum' : 'selesai';

        $.ajax({
            url: '<?= base_url('backend/santri/updateStatusAlamatEmisAjax'); ?>',
            method: 'POST',
            data: {
                IdSantri: idSantri,
                Status: newStatus
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    updateStatusAlamatBadgeUI(idSantri, res.status === 'selesai', res.tgl_update_label || '');
                    let namaSantri = sObj && sObj.NamaSantri ? sObj.NamaSantri : '';
                    if (typeof toastr !== 'undefined') {
                        if (res.status === 'selesai') {
                            toastr.success(namaSantri ? `Status alamat ${namaSantri} ditandai Selesai di database.` : 'Status alamat ditandai Selesai di database.', 'Status Alamat');
                        } else {
                            toastr.info(namaSantri ? `Status alamat ${namaSantri} diubah ke Belum di database.` : 'Status alamat diubah ke Belum di database.', 'Status Alamat');
                        }
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(res.message || 'Gagal mengubah status alamat di database', 'Gagal');
                    }
                }
            },
            error: function() {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Terjadi kesalahan koneksi saat memperbarui status alamat.', 'Gagal');
                }
            }
        });
    }

    function setStatusAlamatSantriEmis(idSantri, isSelesai, callback = null) {
        const newStatus = isSelesai ? 'selesai' : 'belum';
        $.ajax({
            url: '<?= base_url('backend/santri/updateStatusAlamatEmisAjax'); ?>',
            method: 'POST',
            data: {
                IdSantri: idSantri,
                Status: newStatus
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    updateStatusAlamatBadgeUI(idSantri, res.status === 'selesai', res.tgl_update_label || '');
                    if (typeof callback === 'function') {
                        callback(res);
                    }
                }
            }
        });
    }

    function updateStatusAlamatBadgeUI(idSantri, isSelesai, tglUpdateLabel = '', refreshDataTable = true) {
        const box = document.getElementById('box-status-alamat-' + idSantri);
        if (!box) return;

        // Update dataSantriMaster agar konsisten
        const sObj = dataSantriMaster.find(s => String(s.IdSantri) === String(idSantri));
        if (sObj) {
            sObj.StatusAlamatEmis = isSelesai ? 'selesai' : 'belum';
            if (isSelesai && tglUpdateLabel) {
                sObj.TglUpdateAlamatEmis = tglUpdateLabel;
            } else if (!isSelesai) {
                sObj.TglUpdateAlamatEmis = null;
            }
        }

        const timeHtml = (isSelesai && tglUpdateLabel) ? `<small class="text-muted d-block status-time-alamat" style="font-size: 10px;"><i class="far fa-clock mr-1"></i>${tglUpdateLabel}</small>` : '';
        box.innerHTML = `
            <span class="badge ${isSelesai ? 'badge-info' : 'badge-secondary'} p-1 status-badge cursor-pointer" onclick="toggleStatusAlamatSantriEmis('${idSantri}')" title="Klik untuk ubah status alamat">
                <i class="fas ${isSelesai ? 'fa-map-marked-alt' : 'fa-map-marker-alt'} mr-1"></i> Alamat: ${isSelesai ? 'Selesai' : 'Belum'}
            </span>
            ${timeHtml}
        `;

        const cell = document.getElementById('col-status-' + idSantri);
        if (dataTableSantri && cell && refreshDataTable) {
            dataTableSantri.cell(cell).invalidate().draw(false);
        }
    }

    function hitungUlangStatistik() {
        let selesai = 0;
        let konflik = 0;
        dataSantriMaster.forEach(s => {
            const st = (s.StatusEmis || 'belum').toLowerCase();
            if (st === 'selesai') selesai++;
            else if (st === 'konflik') konflik++;
        });
        const total = dataSantriMaster.length;
        const belum = total - selesai - konflik;

        $('#statTotalSantri').text(total);
        $('#statSelesaiSantri').text(selesai);
        $('#statKonflikSantri').text(konflik);
        $('#statBelumSantri').text(belum);
    }

    // ==============================================================
    // KOMUNIKASI CHROME EXTENSION (Phase 3 & Protocol)
    // ==============================================================
    window.addEventListener('message', function(event) {
        // Abaikan jika bukan dari domain sendiri / extension
        if (!event.data || !event.data.type) return;

        if (event.data.type === 'EMIS_EXTENSION_PONG') {
            isExtensionConnected = true;
            const extVer = event.data.version ? escapeHtml(String(event.data.version)) : '';
            const verBadgeHtml = extVer ? ` <span class="badge badge-light text-success ml-1">v${extVer}</span>` : '';
            $('#extStatusBadge')
                .removeClass('badge-secondary badge-danger')
                .addClass('badge-success')
                .html(`<i class="fas fa-check-circle mr-1"></i> Ekstensi EMIS Aktif${verBadgeHtml}`);
            $('#guideExtStatus')
                .removeClass('badge-secondary')
                .addClass('badge-success')
                .text(extVer ? `Terhubung (v${extVer})! Ekstensi siap digunakan.` : 'Terhubung! Ekstensi siap digunakan.');
        }

        if (event.data.type === 'EMIS_EXTENSION_NEED_REFRESH') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Ekstensi EMIS Diperbarui',
                    text: 'Ekstensi EMIS baru saja dimuat ulang di Chrome. Refresh halaman ini sekarang agar tersambung kembali?',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-sync-alt mr-1"></i> Refresh Halaman',
                    cancelButtonText: 'Nanti'
                }).then((res) => {
                    if (res.isConfirmed) {
                        window.location.reload();
                    }
                });
            } else if (confirm('Ekstensi EMIS baru saja diperbarui di browser.\n\nKlik OK untuk me-refresh halaman TPQ Smart ini sekarang agar koneksi tersambung kembali.')) {
                window.location.reload();
            }
        }

        if (event.data.type === 'EMIS_EXTENSION_FILLED_SUCCESS') {
            const santriId = event.data.id_santri;
            const isReturnToTpq = Boolean(event.data.returnToTpq);
            if (santriId) {
                setStatusSantriEmis(santriId, true, function(res) {
                    const isCurrentBatch = batchState.active && String(santriId) === String(batchState.currentId);
                    if (isCurrentBatch) {
                        if (isReturnToTpq) {
                            onBatchItemCompleted(santriId, 'selesai');
                        }
                        return;
                    }

                    let namaSantri = '';
                    const santriObj = dataSantriMaster.find(s => String(s.IdSantri) === String(santriId));
                    if (santriObj && santriObj.NamaSantri) {
                        namaSantri = santriObj.NamaSantri;
                    }

                    const recordTime = res.tgl_update_label || 'Baru saja';

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Status Pendaftaran EMIS Selesai!',
                            html: namaSantri 
                                ? `Data pendaftaran santri <strong>${namaSantri}</strong> berhasil disimpan dengan status <strong>Selesai</strong> di database.<br><small class="text-muted"><i class="far fa-clock mr-1"></i>Tercatat pada: ${recordTime}</small>`
                                : `Data pendaftaran santri berhasil disimpan dengan status <strong>Selesai</strong> di database.<br><small class="text-muted"><i class="far fa-clock mr-1"></i>Tercatat pada: ${recordTime}</small>`,
                            confirmButtonColor: '#198754',
                            confirmButtonText: '<i class="fas fa-check mr-1"></i> OK'
                        });
                    } else if (typeof toastr !== 'undefined') {
                        toastr.success('Data pendaftaran santri berhasil ditandai Selesai di database!', 'Status EMIS');
                    }
                });
            }
        }

        if (event.data.type === 'EMIS_EXTENSION_UPDATE_ALAMAT_SUCCESS') {
            const santriId = event.data.id_santri;
            if (santriId) {
                setStatusAlamatSantriEmis(santriId, true, function(res) {
                    if (batchState.active && String(santriId) === String(batchState.currentId)) {
                        onBatchItemCompleted(santriId, 'selesai');
                        return;
                    }

                    let namaSantri = '';
                    const santriObj = dataSantriMaster.find(s => String(s.IdSantri) === String(santriId));
                    if (santriObj && santriObj.NamaSantri) {
                        namaSantri = santriObj.NamaSantri;
                    }

                    const recordTime = res.tgl_update_label || 'Baru saja';

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Update Alamat EMIS Selesai!',
                            html: namaSantri 
                                ? `Pembaruan alamat santri <strong>${namaSantri}</strong> berhasil disimpan dengan status <strong>Selesai</strong> di database.<br><small class="text-muted"><i class="far fa-clock mr-1"></i>Tercatat pada: ${recordTime}</small>`
                                : `Pembaruan alamat santri berhasil disimpan dengan status <strong>Selesai</strong> di database.<br><small class="text-muted"><i class="far fa-clock mr-1"></i>Tercatat pada: ${recordTime}</small>`,
                            confirmButtonColor: '#0d6efd',
                            confirmButtonText: '<i class="fas fa-check mr-1"></i> OK'
                        });
                    } else if (typeof toastr !== 'undefined') {
                        toastr.success('Data alamat santri berhasil ditandai Selesai di database!', 'Status Alamat EMIS');
                    }
                });
            }
        }

        if (event.data.type === 'EMIS_EXTENSION_BATCH_ITEM_FAILED') {
            const santriId = event.data.id_santri;
            const failStatus = (event.data.status === 'konflik') ? 'konflik' : 'belum';
            const failCatatan = event.data.catatan || (failStatus === 'konflik' ? 'Konflik di EMIS' : 'Tunda: Gagal proses di EMIS');
            if (santriId) {
                simpanStatusGagalDariEmis(santriId, failStatus, failCatatan, function() {
                    if (batchState.active && String(santriId) === String(batchState.currentId)) {
                        onBatchItemCompleted(santriId, failStatus === 'konflik' ? 'konflik' : 'tunda', failCatatan);
                    } else if (failStatus === 'konflik') {
                        const santriObj = dataSantriMaster.find(s => String(s.IdSantri) === String(santriId));
                        const namaSantri = santriObj && santriObj.NamaSantri ? santriObj.NamaSantri : 'Santri';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Konflik Lembaga Lain di EMIS',
                                html: `Santri <strong>${escapeHtml(namaSantri)}</strong> otomatis ditandai <span class="badge badge-danger">Konflik</span>.<br><small class="text-muted mt-1 d-block">${escapeHtml(failCatatan)}</small>`,
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'OK'
                            });
                        } else if (typeof toastr !== 'undefined') {
                            toastr.warning(`${namaSantri} ditandai Konflik: ${failCatatan}`, 'Konflik EMIS');
                        }
                    }
                });
            }
        }
    });

    function cekKoneksiExtension(showAlert = false) {
        window.postMessage({ type: 'TPQ_TO_EMIS_EXTENSION_PING' }, '*');
        setTimeout(() => {
            if (!isExtensionConnected) {
                $('#extStatusBadge')
                    .removeClass('badge-secondary badge-success')
                    .addClass('badge-secondary')
                    .html('<i class="fas fa-exclamation-circle mr-1"></i> Ekstensi Belum Aktif');
                $('#guideExtStatus')
                    .removeClass('badge-success')
                    .addClass('badge-warning')
                    .text('Belum terdeteksi. Pastikan folder ekstensi sudah dimuat di chrome://extensions');
                if (showAlert) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Ekstensi Belum Terdeteksi',
                            text: 'Silakan ikuti panduan pemasangan ekstensi pada jendela panduan.',
                            confirmButtonColor: '#0d6efd',
                            confirmButtonText: 'Mengerti'
                        });
                    } else {
                        alert('Ekstensi belum terdeteksi. Silakan ikuti panduan pemasangan di jendela ini.');
                    }
                }
            }
        }, 1200);
    }

    // ==============================================================
    // KONTROL ANTREAN & CHECKLIST INPUT MASAL EMIS
    // ==============================================================
    const selectedSantriSet = new Set();
    let batchState = {
        active: false,
        paused: false,
        queue: [],
        index: 0,
        currentId: null,
        selesai: 0,
        konflik: 0,
        tunda: 0,
        watchdogTimer: null
    };

    function initChecklistBatchHandlers() {
        // Handler checkbox individual (delegasi agar tetap jalan di semua halaman DataTable)
        $('#tblSantriEmis tbody').on('change', '.cb-santri-emis', function() {
            const id = String($(this).val());
            if ($(this).is(':checked')) {
                selectedSantriSet.add(id);
            } else {
                selectedSantriSet.delete(id);
            }
            updateBatchToolbarUI();
        });

        // Handler Select All (hanya untuk baris yang tampil/lolos filter saat ini)
        $('#checkAllSantriEmis').on('change', function() {
            const isChecked = $(this).is(':checked');
            if (dataTableSantri) {
                const visibleNodes = dataTableSantri.rows({ search: 'applied' }).nodes();
                $(visibleNodes).find('.cb-santri-emis').each(function() {
                    const id = String($(this).val());
                    $(this).prop('checked', isChecked);
                    if (isChecked) {
                        selectedSantriSet.add(id);
                    } else {
                        selectedSantriSet.delete(id);
                    }
                });
            }
            updateBatchToolbarUI();
        });

        // Sinkronkan status checkbox setiap DataTable berpindah halaman / filter
        if (dataTableSantri) {
            dataTableSantri.on('draw', function() {
                syncCheckboxesOnDraw();
            });
        }
    }

    function syncCheckboxesOnDraw() {
        if (!dataTableSantri) return;
        let visibleCount = 0;
        let visibleCheckedCount = 0;
        const visibleNodes = dataTableSantri.rows({ search: 'applied' }).nodes();
        $(visibleNodes).find('.cb-santri-emis').each(function() {
            visibleCount++;
            const id = String($(this).val());
            const checked = selectedSantriSet.has(id);
            $(this).prop('checked', checked);
            if (checked) visibleCheckedCount++;
        });
        $('#checkAllSantriEmis').prop('checked', visibleCount > 0 && visibleCount === visibleCheckedCount);
    }

    function updateBatchToolbarUI() {
        const count = selectedSantriSet.size;
        $('#countCheckedSantri').text(count);
        $('#btnInputMasalEmis').prop('disabled', count === 0 || batchState.active);
        if (count > 0 && !batchState.active) {
            $('#btnClearCheckedSantri').show();
        } else {
            $('#btnClearCheckedSantri').hide();
        }
        syncCheckboxesOnDraw();
    }

    function clearCheckedSantriEmis() {
        selectedSantriSet.clear();
        $('#checkAllSantriEmis').prop('checked', false);
        $('.cb-santri-emis').prop('checked', false);
        updateBatchToolbarUI();
    }

    function mulaiInputMasalEmis() {
        if (selectedSantriSet.size === 0) return;
        if (!isExtensionConnected) {
            $('#modalPanduanExtension').modal('show');
            return;
        }

        const selectedIds = Array.from(selectedSantriSet);
        const alreadySelesai = selectedIds.filter(id => {
            const s = dataSantriMaster.find(item => String(item.IdSantri) === String(id));
            return s && (s.StatusEmis || '').toLowerCase() === 'selesai';
        });

        const startQueue = (idsToProcess) => {
            if (!idsToProcess || idsToProcess.length === 0) {
                if (typeof toastr !== 'undefined') toastr.info('Tidak ada santri yang perlu diproses.', 'Input Masal');
                return;
            }
            batchState = {
                active: true,
                paused: false,
                queue: idsToProcess,
                index: 0,
                currentId: null,
                selesai: 0,
                konflik: 0,
                tunda: 0,
                watchdogTimer: null
            };
            $('#batchProgressPanel').show();
            $('#btnPauseBatchEmis').html('<i class="fas fa-pause mr-1"></i> Jeda').removeClass('btn-success').addClass('btn-warning');
            updateBatchToolbarUI();
            prosesBatchItemBerikutnya();
        };

        if (alreadySelesai.length > 0 && typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'question',
                title: 'Sebagian Santri Sudah Selesai',
                html: `Dari <strong>${selectedIds.length}</strong> santri yang dipilih, terdapat <strong>${alreadySelesai.length}</strong> santri yang statusnya sudah <strong>Selesai</strong>.<br>Apakah ingin melewati santri yang sudah selesai?`,
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Lewati yang Selesai',
                denyButtonText: 'Proses Semua',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#198754',
                denyButtonColor: '#0d6efd'
            }).then((res) => {
                if (res.isConfirmed) {
                    const filtered = selectedIds.filter(id => !alreadySelesai.includes(id));
                    startQueue(filtered);
                } else if (res.isDenied) {
                    startQueue(selectedIds);
                }
            });
        } else {
            startQueue(selectedIds);
        }
    }

    function updateBatchProgressUI(namaSantri = '') {
        const total = batchState.queue.length;
        const done = batchState.selesai + batchState.konflik + batchState.tunda;
        const pct = total > 0 ? Math.round((done / total) * 100) : 0;
        const currentNo = Math.min(batchState.index + 1, total);

        if (batchState.paused) {
            $('#batchProgressTitle').html(`<i class="fas fa-pause-circle text-warning mr-1"></i> Dijeda pada Santri ${currentNo}/${total} — ${escapeHtml(namaSantri)}`);
        } else {
            $('#batchProgressTitle').html(`<i class="fas fa-spinner fa-spin mr-1"></i> Memproses Santri ${currentNo}/${total}: <strong>${escapeHtml(namaSantri)}</strong>`);
        }
        $('#batchProgressStats').html(`Selesai: <strong class="text-success">${batchState.selesai}</strong> | Konflik: <strong class="text-danger">${batchState.konflik}</strong> | Tunda: <strong class="text-warning">${batchState.tunda}</strong>`);
        $('#batchProgressBar').css('width', pct + '%');
    }

    function prosesBatchItemBerikutnya() {
        if (!batchState.active || batchState.paused) return;
        if (batchState.watchdogTimer) {
            clearTimeout(batchState.watchdogTimer);
            batchState.watchdogTimer = null;
        }

        if (batchState.index >= batchState.queue.length) {
            selesaikanBatchEmis();
            return;
        }

        const idSantri = batchState.queue[batchState.index];
        batchState.currentId = idSantri;
        const santriObj = dataSantriMaster.find(s => String(s.IdSantri) === String(idSantri));
        const namaSantri = santriObj ? santriObj.NamaSantri : idSantri;

        updateBatchProgressUI(namaSantri);

        // Watchdog 120 detik jika tidak ada balasan dari tab EMIS
        batchState.watchdogTimer = setTimeout(() => {
            if (!batchState.active || String(batchState.currentId) !== String(idSantri)) return;
            simpanStatusGagalDariEmis(idSantri, 'belum', 'Tunda: Timeout proses EMIS (>120 detik)', function() {
                onBatchItemCompleted(idSantri, 'tunda', 'Timeout (>120 detik)');
            });
        }, 120000);

        kirimKeEmisExtension(idSantri, true);
    }

    function onBatchItemCompleted(idSantri, outcome, noteText = '') {
        if (!batchState.active || String(idSantri) !== String(batchState.currentId)) {
            return;
        }
        batchState.currentId = null; // Kunci agar satu santri tidak pernah dihitung ganda
        if (batchState.watchdogTimer) {
            clearTimeout(batchState.watchdogTimer);
            batchState.watchdogTimer = null;
        }

        if (outcome === 'selesai') batchState.selesai++;
        else if (outcome === 'konflik') batchState.konflik++;
        else batchState.tunda++;

        selectedSantriSet.delete(String(idSantri));
        updateBatchToolbarUI();

        const santriObj = dataSantriMaster.find(s => String(s.IdSantri) === String(idSantri));
        const namaSantri = santriObj ? santriObj.NamaSantri : idSantri;
        if (typeof toastr !== 'undefined') {
            if (outcome === 'selesai') toastr.success(`(${batchState.index + 1}/${batchState.queue.length}) ${namaSantri}: Selesai`, 'Input Masal');
            else if (outcome === 'konflik') toastr.warning(`(${batchState.index + 1}/${batchState.queue.length}) ${namaSantri}: Konflik`, 'Input Masal');
            else toastr.info(`(${batchState.index + 1}/${batchState.queue.length}) ${namaSantri}: Tunda (${noteText})`, 'Input Masal');
        }

        batchState.index++;
        updateBatchProgressUI(namaSantri);

        setTimeout(() => {
            prosesBatchItemBerikutnya();
        }, 1500);
    }

    function simpanStatusGagalDariEmis(idSantri, status, catatan, callback = null) {
        $.ajax({
            url: '<?= base_url('backend/santri/updateStatusEmisAjax'); ?>',
            method: 'POST',
            data: {
                IdSantri: idSantri,
                Status: status,
                Catatan: catatan
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    updateStatusBadgeUI(idSantri, res.status, res.tgl_update_label || '', res.catatan);
                    hitungUlangStatistik();
                }
                if (typeof callback === 'function') callback(res);
            },
            error: function() {
                if (typeof callback === 'function') callback({ success: false });
            }
        });
    }

    function togglePauseBatchEmis() {
        if (!batchState.active) return;
        batchState.paused = !batchState.paused;
        const santriObj = dataSantriMaster.find(s => String(s.IdSantri) === String(batchState.currentId));
        const namaSantri = santriObj ? santriObj.NamaSantri : '';
        if (batchState.paused) {
            if (batchState.watchdogTimer) {
                clearTimeout(batchState.watchdogTimer);
                batchState.watchdogTimer = null;
            }
            $('#btnPauseBatchEmis').html('<i class="fas fa-play mr-1"></i> Lanjutkan').removeClass('btn-warning').addClass('btn-success');
            updateBatchProgressUI(namaSantri);
        } else {
            $('#btnPauseBatchEmis').html('<i class="fas fa-pause mr-1"></i> Jeda').removeClass('btn-success').addClass('btn-warning');
            prosesBatchItemBerikutnya();
        }
    }

    function hentikanBatchEmis() {
        if (!batchState.active) return;
        if (batchState.watchdogTimer) {
            clearTimeout(batchState.watchdogTimer);
            batchState.watchdogTimer = null;
        }
        batchState.active = false;
        batchState.paused = false;
        batchState.currentId = null;
        $('#batchProgressPanel').hide();
        updateBatchToolbarUI();
        if (typeof toastr !== 'undefined') {
            toastr.warning(`Antrean dihentikan. Selesai: ${batchState.selesai}, Konflik: ${batchState.konflik}, Tunda: ${batchState.tunda}`, 'Input Masal');
        }
    }

    function selesaikanBatchEmis() {
        if (batchState.watchdogTimer) {
            clearTimeout(batchState.watchdogTimer);
            batchState.watchdogTimer = null;
        }
        const summary = { ...batchState };
        batchState.active = false;
        batchState.paused = false;
        batchState.currentId = null;
        $('#batchProgressBar').css('width', '100%');
        setTimeout(() => { $('#batchProgressPanel').hide(); }, 1500);
        updateBatchToolbarUI();

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Input Masal EMIS Selesai!',
                html: `Total diproses: <strong>${summary.queue.length}</strong> santri<br>
                       <span class="text-success font-weight-bold">Selesai: ${summary.selesai}</span> | 
                       <span class="text-danger font-weight-bold">Konflik: ${summary.konflik}</span> | 
                       <span class="text-warning font-weight-bold">Tunda: ${summary.tunda}</span>`,
                confirmButtonColor: '#198754',
                confirmButtonText: 'OK'
            });
        }
    }

    function kirimKeEmisExtension(idSantri, isBatch = false) {
        // Ambil data santri via API endpoint
        $.ajax({
            url: '<?= base_url('backend/santri/getSantriEmisJson'); ?>/' + idSantri,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.data) {
                    if (isExtensionConnected) {
                        if (!isBatch && typeof toastr !== 'undefined') {
                            toastr.info('Membuka halaman PPDB EMIS 4.0 dan mentransfer data santri...', 'Transfer EMIS');
                        }
                        // Kirim data ke background / content script extension
                        window.postMessage({
                            type: 'TPQ_SEND_TO_EMIS',
                            data: res.data,
                            isBatch: Boolean(isBatch)
                        }, '*');
                    } else {
                        // Jika extension belum terpasang, buka panduan pemasangan ekstensi
                        $('#modalPanduanExtension').modal('show');
                    }
                } else if (isBatch) {
                    onBatchItemCompleted(idSantri, 'tunda', 'Data santri tidak ditemukan');
                }
            },
            error: function() {
                if (isBatch) {
                    onBatchItemCompleted(idSantri, 'tunda', 'Gagal mengambil data dari server');
                    return;
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengambil Data',
                        text: 'Gagal mengambil data santri dari server TPQ Smart.',
                        confirmButtonColor: '#d33'
                    });
                } else {
                    alert('Gagal mengambil data santri dari server.');
                }
            }
        });
    }

    function kirimUpdateAlamatKeEmis(idSantri) {
        $.ajax({
            url: '<?= base_url('backend/santri/getSantriEmisJson'); ?>/' + idSantri,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.data) {
                    if (isExtensionConnected) {
                        const namaSantri = res.data.nama_santri || 'santri';
                        if (typeof toastr !== 'undefined') {
                            toastr.info(`Membuka halaman Kesiswaan EMIS untuk update alamat ${namaSantri}...`, 'Update Alamat EMIS');
                        }
                        // Kirim data ke background / content script extension dengan action update alamat
                        window.postMessage({
                            type: 'TPQ_SEND_UPDATE_ALAMAT_TO_EMIS',
                            data: res.data
                        }, '*');
                    } else {
                        $('#modalPanduanExtension').modal('show');
                    }
                }
            },
            error: function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengambil Data',
                        text: 'Gagal mengambil data santri dari server TPQ Smart.',
                        confirmButtonColor: '#d33'
                    });
                } else {
                    alert('Gagal mengambil data santri dari server.');
                }
            }
        });
    }

    // ==============================================================
    // DETAIL LENGKAP SANTRI (Original View Detail Function)
    // ==============================================================
    function showUpdateEmisSantri(IdSantri) {
        const santri = dataSantriMaster.find(s => s.IdSantri === IdSantri);
        if (!santri) return;

        const uploadPath = '<?= (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/' : base_url('uploads/santri/') ?>';
        document.getElementById('modalPhotoProfilUpdate').src = santri.PhotoProfil ? uploadPath + santri.PhotoProfil : '<?= base_url('images/no-photo.jpg') ?>';
        document.getElementById('modalIdSantriUpdate').textContent = santri.IdSantri;
        document.getElementById('modalNamaTpqUpdate').textContent = santri.NamaTpq || '-';
        document.getElementById('modalKelasUpdate').textContent = santri.NamaKelas || '-';
        document.getElementById('modalNISUpdate').textContent = santri.NIS || santri.NISN || '-';
        document.getElementById('modalNikSantriUpdate').textContent = santri.NikSantri || '-';
        document.getElementById('modalIdKartuKeluargaUpdate').textContent = santri.IdKartuKeluarga || '-';
        document.getElementById('modalNamaSantriUpdate').textContent = santri.NamaSantri || '-';
        document.getElementById('modalJenisKelaminUpdate').textContent = santri.JenisKelamin || '-';
        document.getElementById('modalTempatTanggalLahirUpdate').textContent = (santri.TempatLahirSantri || '-') + ', ' + (santri.TanggalLahirSantri || '-');
        document.getElementById('modalNoHpUpdate').textContent = (santri.NoHpSantri || '-') + ' | ' + (santri.EmailSantri || '-');
        document.getElementById('modalJumlahSaudaraAnakKeUpdate').textContent = 'Jumlah Saudara: ' + (santri.JumlahSaudara || '-') + ' Anak Ke-' + (santri.AnakKe || '-');
        document.getElementById('modalCitaCitaUpdate').textContent = santri.CitaCita || santri.CitaCitaLainya || '-';
        document.getElementById('modalHobiUpdate').textContent = santri.Hobi || santri.HobiLainya || '-';
        document.getElementById('modalKebutuhanKhususUpdate').textContent = santri.KebutuhanKhusus || santri.KebutuhanKhususLainya || '-';
        document.getElementById('modalKebutuhanDisabilitasUpdate').textContent = santri.KebutuhanDisabilitas || santri.KebutuhanDisabilitasLainya || '-';
        document.getElementById('modalYangBiayaSekolahUpdate').textContent = santri.YangBiayaSekolah || '-';
        document.getElementById('modalNamaKepalaKeluargaUpdate').textContent = santri.NamaKepalaKeluarga || '-';

        const statusElement = document.getElementById('modalStatusUpdate');
        let badgeClass = 'bg-success';
        if (santri.Status === 'Belum Diverifikasi') {
            badgeClass = 'bg-warning';
        } else if (santri.Status === 'Perlu Perbaikan') {
            badgeClass = 'bg-danger';
        }
        statusElement.innerHTML = `<span class="badge ${badgeClass}">${santri.Status || '-'}</span>`;

        // Data Ayah
        document.getElementById('modalNamaAyahUpdate').textContent = santri.NamaAyah || '-';
        document.getElementById('modalStatusAyahUpdate').textContent = santri.StatusAyah || '-';
        if (santri.StatusAyah === 'Masih Hidup') {
            document.getElementById('modalNikAyahUpdate').textContent = santri.NikAyah || '-';
            document.getElementById('modalTempatTanggalLahirAyahUpdate').textContent = (santri.TempatLahirAyah || '-') + ', ' + (santri.TanggalLahirAyah || '-');
            document.getElementById('modalPendidikanTerakhirAyahUpdate').textContent = santri.PendidikanAyah || '-';
            document.getElementById('modalPekerjaanAyahUpdate').textContent = santri.PekerjaanUtamaAyah || '-';
            document.getElementById('modalPenghasilanAyahUpdate').textContent = santri.PenghasilanUtamaAyah || '-';
            document.getElementById('modalNoHpAyahUpdate').textContent = santri.NoHpAyah || '-';
            document.querySelectorAll('.data-ayah-hidup').forEach(el => el.style.display = 'table-row');
        } else {
            document.querySelectorAll('.data-ayah-hidup').forEach(el => el.style.display = 'none');
        }

        // Data Ibu
        document.getElementById('modalNamaIbuUpdate').textContent = santri.NamaIbu || '-';
        document.getElementById('modalStatusIbuUpdate').textContent = santri.StatusIbu || '-';
        if (santri.StatusIbu === 'Masih Hidup') {
            document.getElementById('modalNikIbuUpdate').textContent = santri.NikIbu || '-';
            document.getElementById('modalTempatTanggalLahirIbuUpdate').textContent = (santri.TempatLahirIbu || '-') + ', ' + (santri.TanggalLahirIbu || '-');
            document.getElementById('modalPendidikanTerakhirIbuUpdate').textContent = santri.PendidikanIbu || '-';
            document.getElementById('modalPekerjaanIbuUpdate').textContent = santri.PekerjaanUtamaIbu || '-';
            document.getElementById('modalPenghasilanIbuUpdate').textContent = santri.PenghasilanUtamaIbu || '-';
            document.getElementById('modalNoHpIbuUpdate').textContent = santri.NoHpIbu || '-';
            document.querySelectorAll('.data-ibu-hidup').forEach(el => el.style.display = 'table-row');
        } else {
            document.querySelectorAll('.data-ibu-hidup').forEach(el => el.style.display = 'none');
        }

        // Alamat & Domisili Santri
        document.getElementById('modalStatusTempatTinggalUpdate').textContent = santri.StatusTempatTinggalSantri || '-';
        document.getElementById('modalStatusMukimUpdate').textContent = santri.StatusMukim || '-';
        document.getElementById('modalAlamatUpdate').textContent = santri.AlamatSantri || '-';
        document.getElementById('modalRTUpdate').textContent = santri.RtSantri || '-';
        document.getElementById('modalRWUpdate').textContent = santri.RwSantri || '-';
        document.getElementById('modalKelurahanDesaUpdate').textContent = santri.KelurahanDesaSantri || '-';
        document.getElementById('modalKecamatanUpdate').textContent = santri.KecamatanSantri || '-';
        document.getElementById('modalKabupatenKotaUpdate').textContent = santri.KabupatenKotaSantri || '-';
        document.getElementById('modalProvinsiUpdate').textContent = santri.ProvinsiSantri || '-';
        document.getElementById('modalKodePosUpdate').textContent = santri.KodePosSantri || '-';
        document.getElementById('modalJarakTempuhUpdate').textContent = santri.JarakTempuhSantri || '-';
        document.getElementById('modalTransportasiUpdate').textContent = santri.TransportasiSantri || '-';
        document.getElementById('modalWaktuTempuhUpdate').textContent = santri.WaktuTempuhSantri || '-';

        // Alamat Orang Tua / Ayah (beserta fallback ke alamat santri seperti pengiriman EMIS)
        document.getElementById('modalStatusRumahAyahUpdate').textContent = santri.StatusKepemilikanRumahAyah || '-';
        document.getElementById('modalAlamatAyahUpdate').textContent = santri.AlamatAyah || santri.AlamatSantri || '-';
        document.getElementById('modalRTAyahUpdate').textContent = santri.RtAyah || santri.RtSantri || '-';
        document.getElementById('modalRWAyahUpdate').textContent = santri.RwAyah || santri.RwSantri || '-';
        document.getElementById('modalKelurahanDesaAyahUpdate').textContent = santri.KelurahanDesaAyah || santri.KelurahanDesaSantri || '-';
        document.getElementById('modalKecamatanAyahUpdate').textContent = santri.KecamatanAyah || santri.KecamatanSantri || '-';
        document.getElementById('modalKabupatenKotaAyahUpdate').textContent = santri.KabupatenKotaAyah || santri.KabupatenKotaSantri || '-';
        document.getElementById('modalProvinsiAyahUpdate').textContent = santri.ProvinsiAyah || santri.ProvinsiSantri || '-';
        document.getElementById('modalKodePosAyahUpdate').textContent = santri.KodePosAyah || santri.KodePosSantri || '-';

        if (santri.TitikKoordinatSantri) {
            const coords = santri.TitikKoordinatSantri.split(',');
            const mapsLink = `https://www.google.com/maps?q=${coords[0]},${coords[1]}`;
            document.getElementById('modalTitikKoordinatUpdate').innerHTML = `
                <a href="${mapsLink}" target="_blank" class="btn btn-sm btn-info">
                    <i class="fas fa-map-marker-alt"></i> Lihat di Google Maps
                </a>
                <span class="ml-2">${santri.TitikKoordinatSantri}</span>
            `;
        } else {
            document.getElementById('modalTitikKoordinatUpdate').textContent = '-';
        }

        function createFileLink(fileName, path) {
            if (!fileName || fileName.trim() === '') {
                return '<span class="text-muted">Tidak ada file</span>';
            }
            const fullPath = path + fileName;
            return `<a href="${fullPath}" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-file-download mr-1"></i> Lihat File</a>`;
        }

        document.getElementById('modalLampiranKkSantriUpdate').innerHTML = createFileLink(santri?.FileKkSantri, uploadPath);
        document.getElementById('modalLampiranKkAyahUpdate').innerHTML = createFileLink(santri?.FileKkAyah, uploadPath);
        document.getElementById('modalLampiranKkIbuUpdate').innerHTML = createFileLink(santri?.FileKkIbu, uploadPath);
        document.getElementById('modalLampiranKIPUpdate').innerHTML = createFileLink(santri?.FileKIP, uploadPath);
        document.getElementById('modalLampiranPKHUpdate').innerHTML = createFileLink(santri?.FilePKH, uploadPath);
        document.getElementById('modalLampiranKKSUpdate').innerHTML = createFileLink(santri?.FileKKS, uploadPath);

        $('#updateDataEmisModal').modal('show');
    }
</script>
<style>
    .cursor-pointer { cursor: pointer; }
    .status-badge:hover { opacity: 0.85; }
    .font-monospace { font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
</style>
<?= $this->endSection(); ?>
