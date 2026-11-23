<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <!-- Card Utama -->
    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h3 class="card-title font-weight-bold my-1">
                    <i class="fas fa-sync-alt text-primary mr-1"></i> Transfer Data Santri ke EMIS 4.0 Kemenag
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
                                <option value="<?= htmlspecialchars($k['NamaKelas']); ?>"><?= htmlspecialchars($k['NamaKelas']); ?></option>
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
                    </select>
                </div>
                <div class="col-md-6 col-sm-12 mb-2 text-md-right">
                    <?php
                        $totalSantri = count($dataSantri);
                        $totalSelesai = 0;
                        foreach ($dataSantri as $s) {
                            if (($s['StatusEmis'] ?? 'belum') === 'selesai') {
                                $totalSelesai++;
                            }
                        }
                        $totalBelum = $totalSantri - $totalSelesai;
                    ?>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-light border btn-sm" disabled>
                            Total: <strong id="statTotalSantri"><?= $totalSantri; ?></strong>
                        </button>
                        <button type="button" class="btn btn-success btn-sm" disabled>
                            <i class="fas fa-check-circle mr-1"></i> Selesai: <strong id="statSelesaiSantri"><?= $totalSelesai; ?></strong>
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" disabled>
                            <i class="fas fa-clock mr-1"></i> Belum: <strong id="statBelumSantri"><?= $totalBelum; ?></strong>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="resetSemuaStatusEmis()" title="Reset tanda selesai di database">
                            <i class="fas fa-undo"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Santri -->
            <div class="table-responsive">
                <table id="tblSantriEmis" class="table table-bordered table-hover table-striped">
                    <thead class="bg-light">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th width="15%">Aksi EMIS</th>
                            <th width="12%">Status EMIS</th>
                            <th width="15%">NIK</th>
                            <th width="20%">Nama Santri</th>
                            <th width="10%">Kelas</th>
                            <th width="13%">TTL</th>
                            <th width="10%">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($dataSantri as $index => $santri) : ?>
                            <tr id="row-santri-<?= $santri['IdSantri']; ?>" data-id="<?= $santri['IdSantri']; ?>" data-kelas="<?= htmlspecialchars($santri['NamaKelas'] ?? ''); ?>">
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
                                        $isSelesai = ($santri['StatusEmis'] ?? 'belum') === 'selesai';
                                        $tglUpdate = !empty($santri['TglUpdateEmis']) ? date('d M Y, H:i', strtotime($santri['TglUpdateEmis'])) : '';
                                        $isAlamatSelesai = ($santri['StatusAlamatEmis'] ?? 'belum') === 'selesai';
                                        $tglUpdateAlamat = !empty($santri['TglUpdateAlamatEmis']) ? date('d M Y, H:i', strtotime($santri['TglUpdateAlamatEmis'])) : '';
                                    ?>
                                    <div class="mb-1" id="box-status-daftar-<?= $santri['IdSantri']; ?>">
                                        <span class="badge <?= $isSelesai ? 'badge-success' : 'badge-warning'; ?> p-1 status-badge cursor-pointer" onclick="toggleStatusSantriEmis('<?= $santri['IdSantri']; ?>')" title="Klik untuk ubah status pendaftaran">
                                            <i class="fas <?= $isSelesai ? 'fa-check-circle' : 'fa-hourglass-half'; ?> mr-1"></i> Daftar: <?= $isSelesai ? 'Selesai' : 'Belum'; ?>
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
                        <table class="table table-borderless table-sm">
                            <tr><td width="25%">Alamat</td><td width="5%">:</td><td id="modalAlamatUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>RT / RW</td><td>:</td><td><span id="modalRTUpdate" class="font-weight-bold"></span> / <span id="modalRWUpdate" class="font-weight-bold"></span></td></tr>
                            <tr><td>Kelurahan / Desa</td><td>:</td><td id="modalKelurahanDesaUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Kecamatan</td><td>:</td><td id="modalKecamatanUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Kabupaten / Kota</td><td>:</td><td id="modalKabupatenKotaUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Provinsi</td><td>:</td><td id="modalProvinsiUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Jarak & Waktu</td><td>:</td><td><span id="modalJarakTempuhUpdate" class="font-weight-bold"></span> (<span id="modalWaktuTempuhUpdate"></span>)</td></tr>
                            <tr><td>Transportasi</td><td>:</td><td id="modalTransportasiUpdate" class="font-weight-bold"></td></tr>
                            <tr><td>Titik Koordinat</td><td>:</td><td id="modalTitikKoordinatUpdate"></td></tr>
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
        const regexVal = val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '';
        dataTableSantri.column(5).search(regexVal, true, false).draw();
    }

    function terapkanFilterStatus(val) {
        if (!dataTableSantri) return;
        if (val === 'selesai') {
            dataTableSantri.column(2).search('Selesai').draw();
        } else if (val === 'belum') {
            dataTableSantri.column(2).search('Belum').draw();
        } else {
            dataTableSantri.column(2).search('').draw();
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
                $('#filterKelasEmis').val(filterState.kelas);
                if ($.fn.select2 && $('#filterKelasEmis').data('select2')) {
                    $('#filterKelasEmis').trigger('change.select2');
                }
                const regexVal = '^' + $.fn.dataTable.util.escapeRegex(filterState.kelas) + '$';
                dataTableSantri.column(5).search(regexVal, true, false);
                needsDraw = true;
            }

            // Pulihkan Filter Status
            if (filterState.status !== undefined && filterState.status !== null && filterState.status !== '') {
                $('#filterStatusEmis').val(filterState.status);
                if (filterState.status === 'selesai') {
                    dataTableSantri.column(2).search('Selesai');
                } else if (filterState.status === 'belum') {
                    dataTableSantri.column(2).search('Belum');
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
    // FITUR STATUS DATABASE (tbl_santri_emis)
    // ==============================================================

    function toggleStatusSantriEmis(idSantri) {
        const sObj = dataSantriMaster.find(s => String(s.IdSantri) === String(idSantri));
        const currentIsSelesai = sObj ? (sObj.StatusEmis === 'selesai') : false;
        const newStatus = currentIsSelesai ? 'belum' : 'selesai';

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
                    updateStatusBadgeUI(idSantri, res.status === 'selesai', res.tgl_update_label || '');
                    hitungUlangStatistik();

                    let namaSantri = sObj && sObj.NamaSantri ? sObj.NamaSantri : '';
                    if (typeof toastr !== 'undefined') {
                        if (res.status === 'selesai') {
                            toastr.success(namaSantri ? `Status ${namaSantri} ditandai Selesai di database.` : 'Status santri ditandai Selesai di database.', 'Status EMIS');
                        } else {
                            toastr.info(namaSantri ? `Status ${namaSantri} dikembalikan ke Belum di database.` : 'Status santri dikembalikan ke Belum di database.', 'Status EMIS');
                        }
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(res.message || 'Gagal mengubah status di database', 'Gagal');
                    }
                }
            },
            error: function() {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Terjadi kesalahan koneksi ke server saat memperbarui status.', 'Gagal');
                }
            }
        });
    }

    function setStatusSantriEmis(idSantri, isSelesai, callback = null) {
        const newStatus = isSelesai ? 'selesai' : 'belum';
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
                    updateStatusBadgeUI(idSantri, res.status === 'selesai', res.tgl_update_label || '');
                    hitungUlangStatistik();
                    if (typeof callback === 'function') {
                        callback(res);
                    }
                }
            }
        });
    }

    function updateStatusBadgeUI(idSantri, isSelesai, tglUpdateLabel = '', refreshDataTable = true) {
        const box = document.getElementById('box-status-daftar-' + idSantri);
        if (!box) return;

        // Update dataSantriMaster agar konsisten
        const sObj = dataSantriMaster.find(s => String(s.IdSantri) === String(idSantri));
        if (sObj) {
            sObj.StatusEmis = isSelesai ? 'selesai' : 'belum';
            if (isSelesai && tglUpdateLabel) {
                sObj.TglUpdateEmis = tglUpdateLabel;
            } else if (!isSelesai) {
                sObj.TglUpdateEmis = null;
            }
        }

        const timeHtml = (isSelesai && tglUpdateLabel) ? `<small class="text-muted d-block status-time" style="font-size: 10px;"><i class="far fa-clock mr-1"></i>${tglUpdateLabel}</small>` : '';
        box.innerHTML = `
            <span class="badge ${isSelesai ? 'badge-success' : 'badge-warning'} p-1 status-badge cursor-pointer" onclick="toggleStatusSantriEmis('${idSantri}')" title="Klik untuk ubah status pendaftaran">
                <i class="fas ${isSelesai ? 'fa-check-circle' : 'fa-hourglass-half'} mr-1"></i> Daftar: ${isSelesai ? 'Selesai' : 'Belum'}
            </span>
            ${timeHtml}
        `;

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
        dataSantriMaster.forEach(s => {
            if (s.StatusEmis === 'selesai') selesai++;
        });
        const total = dataSantriMaster.length;
        const belum = total - selesai;

        $('#statTotalSantri').text(total);
        $('#statSelesaiSantri').text(selesai);
        $('#statBelumSantri').text(belum);
    }

    function resetSemuaStatusEmis() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Reset Semua Status EMIS?',
                text: 'Semua tanda status santri yang sudah selesai akan dikembalikan menjadi "Belum" di database.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-undo mr-1"></i> Ya, Reset Semua',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('backend/santri/resetStatusEmisAjax'); ?>',
                        method: 'POST',
                        dataType: 'json',
                        success: function(res) {
                            if (res.success) {
                                dataSantriMaster.forEach(s => {
                                    s.StatusEmis = 'belum';
                                    s.TglUpdateEmis = null;
                                    updateStatusBadgeUI(s.IdSantri, false, '', false);
                                });
                                if (dataTableSantri) {
                                    dataTableSantri.rows().invalidate().draw(false);
                                }
                                hitungUlangStatistik();
                                if (typeof toastr !== 'undefined') {
                                    toastr.success(res.message || 'Semua status santri berhasil direset di database.', 'Reset Selesai');
                                }
                            }
                        },
                        error: function() {
                            if (typeof toastr !== 'undefined') {
                                toastr.error('Gagal mereset status santri di database.', 'Gagal');
                            }
                        }
                    });
                }
            });
        }
    }



    // ==============================================================
    // KOMUNIKASI CHROME EXTENSION (Phase 3 & Protocol)
    // ==============================================================
    window.addEventListener('message', function(event) {
        // Abaikan jika bukan dari domain sendiri / extension
        if (!event.data || !event.data.type) return;

        if (event.data.type === 'EMIS_EXTENSION_PONG') {
            isExtensionConnected = true;
            $('#extStatusBadge')
                .removeClass('badge-secondary badge-danger')
                .addClass('badge-success')
                .html('<i class="fas fa-check-circle mr-1"></i> Ekstensi EMIS Aktif');
            $('#guideExtStatus')
                .removeClass('badge-secondary')
                .addClass('badge-success')
                .text('Terhubung! Ekstensi siap digunakan.');
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
            if (santriId) {
                setStatusSantriEmis(santriId, true, function(res) {
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

    function kirimKeEmisExtension(idSantri) {
        // Ambil data santri via API endpoint
        $.ajax({
            url: '<?= base_url('backend/santri/getSantriEmisJson'); ?>/' + idSantri,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.data) {
                    if (isExtensionConnected) {
                        if (typeof toastr !== 'undefined') {
                            toastr.info('Membuka halaman PPDB EMIS 4.0 dan mentransfer data santri...', 'Transfer EMIS');
                        }
                        // Kirim data ke background / content script extension
                        window.postMessage({
                            type: 'TPQ_SEND_TO_EMIS',
                            data: res.data
                        }, '*');
                    } else {
                        // Jika extension belum terpasang, buka panduan pemasangan ekstensi
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

        // Alamat
        document.getElementById('modalAlamatUpdate').textContent = santri.AlamatSantri || '-';
        document.getElementById('modalRTUpdate').textContent = santri.RtSantri || '-';
        document.getElementById('modalRWUpdate').textContent = santri.RwSantri || '-';
        document.getElementById('modalKelurahanDesaUpdate').textContent = santri.KelurahanDesaSantri || '-';
        document.getElementById('modalKecamatanUpdate').textContent = santri.KecamatanSantri || '-';
        document.getElementById('modalKabupatenKotaUpdate').textContent = santri.KabupatenKotaSantri || '-';
        document.getElementById('modalProvinsiUpdate').textContent = santri.ProvinsiSantri || '-';
        document.getElementById('modalJarakTempuhUpdate').textContent = santri.JarakTempuhSantri || '-';
        document.getElementById('modalTransportasiUpdate').textContent = santri.TransportasiSantri || '-';
        document.getElementById('modalWaktuTempuhUpdate').textContent = santri.WaktuTempuhSantri || '-';

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
