<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<div class="col-12">
    <!-- Card Informasi Alur Proses -->
    <div class="card card-info collapsed-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Panduan Alur Proses Rapor Santri
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h5 class="mb-3"><i class="fas fa-list-ol text-primary"></i> Alur Proses:</h5>
                    <ol class="mb-4">
                        <li class="mb-2">
                            <strong>Pilih Kelas:</strong> Gunakan tab di atas untuk memilih kelas yang ingin dikelola.
                        </li>
                        <li class="mb-2">
                            <strong>Lihat Statistik:</strong> Di bagian atas setiap kelas, terdapat card informasi yang menampilkan:
                            <ul class="mt-2">
                                <li><strong>Card Tanda Tangan:</strong> Menampilkan jumlah rapor yang sudah dan belum ditandatangani oleh Wali Kelas atau Kepala Sekolah</li>
                                <li><strong>Card Cetak:</strong> Menampilkan jumlah rapor yang akan dicetak</li>
                                <li><strong>Card Catatan & Absensi:</strong> Menampilkan tabel statistik jumlah santri yang sudah dan belum mengisi catatan/absensi</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Atur Absensi:</strong> Klik toggle switch <span class="badge badge-info">Absensi</span> pada kolom Aksi untuk mengisi data absensi (Izin, Alfa, Sakit).
                            Toggle switch akan berubah warna menjadi hijau saat aktif. Anda dapat menggunakan tombol <span class="badge badge-info"><i class="fas fa-sync-alt"></i> Generate</span> di modal untuk mengambil data dari tabel absensi otomatis.
                        </li>
                        <li class="mb-2">
                            <strong>Atur Catatan:</strong> Klik toggle switch <span class="badge badge-success">Catatan</span> pada kolom Aksi untuk mengatur catatan rapor.
                            Sistem akan menampilkan catatan default berdasarkan nilai rata-rata santri. Jika tersedia beberapa opsi catatan (Spesifik Kelas, Spesifik TPQ, Umum),
                            Anda dapat memilih dari dropdown. Anda juga dapat menambahkan catatan khusus dari wali kelas.
                        </li>
                        <li class="mb-2">
                            <strong>Cetak Rapor:</strong>
                            <ul class="mt-2">
                                <li>Klik tombol <span class="badge badge-warning"><i class="fas fa-print"></i> Cetak Rapor</span> pada kolom Aksi untuk mencetak rapor individual</li>
                                <li>Klik tombol <span class="badge badge-primary"><i class="fas fa-print"></i> Cetak Semua Rapor</span> di card untuk mencetak semua rapor dalam satu kelas sekaligus</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Tanda Tangan QR:</strong>
                            <ul class="mt-2">
                                <li><strong>Wali Kelas:</strong> Klik tombol <span class="badge badge-info"><i class="fas fa-signature"></i> Tanda Tangan QR Semua Wali Kelas</span> di card untuk menandatangani semua rapor sekaligus.
                                    Tombol akan berubah menjadi <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Batalkan TTD</span> jika semua rapor sudah ditandatangani.</li>
                                <li><strong>Kepala Sekolah:</strong> Klik tombol <span class="badge badge-success"><i class="fas fa-signature"></i> Tanda Tangan QR Semua Kepala Sekolah</span> di card untuk menandatangani semua rapor sekaligus.
                                    Tombol akan berubah menjadi <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Batalkan TTD</span> jika semua rapor sudah ditandatangani.</li>
                            </ul>
                            <small class="text-muted">Catatan: Tanda tangan dilakukan secara bulk untuk semua santri dalam kelas. Jika semua sudah ditandatangani, Anda dapat membatalkan dengan klik tombol "Batalkan TTD".</small>
                        </li>
                    </ol>

                    <div class="alert alert-info mb-0">
                        <h5 class="alert-heading"><i class="fas fa-lightbulb"></i> Tips:</h5>
                        <ul class="mb-0">
                            <li>Toggle switch <strong>Absensi</strong> dan <strong>Catatan</strong> berfungsi untuk menampilkan/menyembunyikan informasi di rapor PDF. Warna hijau = aktif, merah = tidak aktif.</li>
                            <li>Data absensi dan catatan akan tersimpan secara otomatis setelah Anda klik tombol <strong>Simpan</strong> di modal. Popup berhasil akan otomatis tertutup setelah 1.5 detik.</li>
                            <li>Statistik di card akan otomatis terupdate setelah Anda menyimpan absensi atau catatan, tanpa perlu reload halaman.</li>
                            <li>Catatan default dipilih secara otomatis berdasarkan prioritas: <strong>Spesifik Kelas</strong> → <strong>Spesifik TPQ</strong> → <strong>Umum</strong>.</li>
                            <li>Jika ada beberapa opsi catatan untuk nilai yang sama, dropdown akan muncul untuk memilih sumber catatan yang diinginkan.</li>
                            <li>Card statistik menampilkan informasi real-time tentang status pengisian absensi, catatan, dan tanda tangan untuk setiap kelas.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= $page_title ?> - Semester <?= $semester ?> - Tahun Ajaran <?= convertTahunAjaran(session()->get('IdTahunAjaran')) ?></h3>
        </div>
        <div class="card-body">
            <!-- Tab Navigation -->
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs flex-wrap justify-content-start justify-content-md-between" id="kelasTab" role="tablist">
                        <?php foreach ($dataKelas as $kelas) : ?>
                            <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                                <a class="nav-link border-white text-center <?= $kelas->IdKelas === $dataKelas[0]->IdKelas ? 'active' : '' ?>"
                                    id="tab-<?= $kelas->IdKelas ?>"
                                    data-toggle="tab"
                                    href="#kelas-<?= $kelas->IdKelas ?>"
                                    role="tab"
                                    aria-controls="kelas-<?= $kelas->IdKelas ?>"
                                    aria-selected="<?= $kelas->IdKelas === $dataKelas[0]->IdKelas ? 'true' : 'false' ?>">
                                    <?= $kelas->NamaKelas ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <br>
                <div class="card-body">
                    <div class="tab-content" id="kelasTabContent">
                        <?php foreach ($dataKelas as $kelas) : ?>
                            <div class="tab-pane fade <?= $kelas->IdKelas === $dataKelas[0]->IdKelas ? 'show active' : '' ?>"
                                id="kelas-<?= $kelas->IdKelas ?>"
                                role="tabpanel"
                                aria-labelledby="tab-<?= $kelas->IdKelas ?>">
                                <div class="table-responsive">
                                    <div class="mb-3">
                                        <div class="row">
                                            <?php
                                            // Cek apakah user adalah Kepala Sekolah atau Wali Kelas untuk tombol tanda tangan QR
                                            $idTpqBtn = session()->get('IdTpq');
                                            $idGuruBtn = session()->get('IdGuru');
                                            $isKepalaSekolahBtn = false;
                                            $isWaliKelasBtn = false;

                                            if (!empty($idGuruBtn) && !empty($idTpqBtn)) {
                                                $helpFunctionModelBtn = new \App\Models\HelpFunctionModel();

                                                // Cek apakah user adalah Kepala Sekolah
                                                $jabatanDataBtn = $helpFunctionModelBtn->getStrukturLembagaJabatan($idGuruBtn, $idTpqBtn);
                                                if (!empty($jabatanDataBtn)) {
                                                    foreach ($jabatanDataBtn as $jabatan) {
                                                        if (isset($jabatan['NamaJabatan']) && $jabatan['NamaJabatan'] === 'Kepala TPQ') {
                                                            $isKepalaSekolahBtn = true;
                                                            break;
                                                        }
                                                    }
                                                }

                                                // Cek apakah user adalah Wali Kelas untuk kelas ini
                                                if (!$isKepalaSekolahBtn && !empty($guruKelasPermissions)) {
                                                    foreach ($guruKelasPermissions as $perm) {
                                                        if (
                                                            isset($perm['IdKelas']) && $perm['IdKelas'] == $kelas->IdKelas &&
                                                            isset($perm['NamaJabatan']) && $perm['NamaJabatan'] === 'Wali Kelas'
                                                        ) {
                                                            $isWaliKelasBtn = true;
                                                            break;
                                                        }
                                                    }
                                                }
                                            }

                                            // Cek status tanda tangan bulk untuk kelas ini
                                            $bulkStatus = $bulkSignatureStatus[$kelas->IdKelas] ?? null;
                                            $allSignedWalas = $bulkStatus && $bulkStatus['all_signed_walas'];
                                            $allSignedKepsek = $bulkStatus && $bulkStatus['all_signed_kepsek'];
                                            $totalSantri = $bulkStatus['total'] ?? 0;
                                            $ttdWalas = $bulkStatus['ttd_walas'] ?? 0;
                                            $ttdKepsek = $bulkStatus['ttd_kepsek'] ?? 0;
                                            $belumTtdWalas = $totalSantri - $ttdWalas;
                                            $belumTtdKepsek = $totalSantri - $ttdKepsek;

                                            // Statistik catatan dan absensi
                                            $jumlahCatatan = $bulkStatus['catatan'] ?? 0;
                                            $jumlahAbsensi = $bulkStatus['absensi'] ?? 0;
                                            $belumCatatan = $totalSantri - $jumlahCatatan;
                                            $belumAbsensi = $totalSantri - $jumlahAbsensi;
                                            ?>

                                            <?php if ($isWaliKelasBtn): ?>
                                                <div class="col-md-4 mb-3">
                                                    <div class="info-box <?= $allSignedWalas ? 'bg-gradient-success' : 'bg-gradient-info' ?> shadow-sm">
                                                        <span class="info-box-icon">
                                                            <i class="fas fa-qrcode"></i>
                                                        </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">
                                                                <?= $allSignedWalas ? 'Batalkan TTD' : 'Tanda Tangan QR' ?>
                                                                <br><small>Wali Kelas - Kelas <?= esc($kelas->NamaKelas) ?></small>
                                                            </span>
                                                            <div class="row mt-2">
                                                                <div class="col-6">
                                                                    <div class="text-center">
                                                                        <span class="info-box-number" style="font-size: 1.5rem;"><?= $ttdWalas ?></span>
                                                                        <small>Sudah TTD</small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="text-center">
                                                                        <span class="info-box-number" style="font-size: 1.5rem;"><?= $belumTtdWalas ?></span>
                                                                        <small>Belum TTD</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mt-2">
                                                                <small>Total: <?= $totalSantri ?> Rapor</small>
                                                            </div>
                                                            <div class="mt-2">
                                                                <?php if ($allSignedWalas): ?>
                                                                    <button type="button" class="btn btn-danger btn-sm btn-block btn-cancel-ttd-walas"
                                                                        data-kelas="<?= $kelas->IdKelas ?>"
                                                                        data-nama-kelas="<?= esc($kelas->NamaKelas) ?>"
                                                                        data-semester="<?= $semester ?>">
                                                                        <i class="fas fa-times-circle"></i> Batalkan TTD
                                                                    </button>
                                                                    <div class="mt-2">
                                                                        <small>Info: Untuk membatalkan tanda tangan semua rapor, silahkan klik tombol <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Batalkan TTD</span> di card</small>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <button type="button" class="btn btn-warning btn-sm btn-block btn-ttd-bulk-walas"
                                                                        data-kelas="<?= $kelas->IdKelas ?>"
                                                                        data-nama-kelas="<?= esc($kelas->NamaKelas) ?>"
                                                                        data-semester="<?= $semester ?>">
                                                                        <i class="fas fa-signature"></i> Tanda Tangan Semua
                                                                    </button>
                                                                    <div class="mt-2">
                                                                        <small>Info: Untuk menandatangani semua rapor, silahkan klik tombol <span class="badge badge-warning"><i class="fas fa-signature"></i> Tanda Tangan Semua</span> di card</small>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($isKepalaSekolahBtn): ?>
                                                <div class="col-md-4 mb-3">
                                                    <div class="info-box <?= $allSignedKepsek ? 'bg-gradient-success' : 'bg-gradient-info' ?> shadow-sm">
                                                        <span class="info-box-icon">
                                                            <i class="fas fa-qrcode"></i>
                                                        </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">
                                                                <?= $allSignedKepsek ? 'Batalkan TTD' : 'Tanda Tangan QR' ?>
                                                                <br><small>Kepala Sekolah - Kelas <?= esc($kelas->NamaKelas) ?></small>
                                                            </span>
                                                            <div class="row mt-2">
                                                                <div class="col-6">
                                                                    <div class="text-center">
                                                                        <span class="info-box-number" style="font-size: 1.5rem;"><?= $ttdKepsek ?></span>
                                                                        <small>Sudah TTD</small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="text-center">
                                                                        <span class="info-box-number" style="font-size: 1.5rem;"><?= $belumTtdKepsek ?></span>
                                                                        <small>Belum TTD</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mt-2">
                                                                <small>Total: <?= $totalSantri ?> Rapor</small>
                                                            </div>
                                                            <div class="mt-2">
                                                                <?php if ($allSignedKepsek): ?>
                                                                    <button type="button" class="btn btn-danger btn-sm btn-block btn-cancel-ttd-kepsek"
                                                                        data-kelas="<?= $kelas->IdKelas ?>"
                                                                        data-nama-kelas="<?= esc($kelas->NamaKelas) ?>"
                                                                        data-semester="<?= $semester ?>">
                                                                        <i class="fas fa-times-circle"></i> Batalkan TTD
                                                                    </button>
                                                                    <div class="mt-2">
                                                                        <small>Info: Untuk membatalkan tanda tangan semua rapor, silahkan klik tombol <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Batalkan TTD</span> di card</small>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <button type="button" class="btn btn-warning btn-sm btn-block btn-ttd-bulk-kepsek"
                                                                        data-kelas="<?= $kelas->IdKelas ?>"
                                                                        data-nama-kelas="<?= esc($kelas->NamaKelas) ?>"
                                                                        data-semester="<?= $semester ?>">
                                                                        <i class="fas fa-signature"></i> Tanda Tangan Semua
                                                                    </button>
                                                                    <div class="mt-2">
                                                                        <small>Info: Untuk menandatangani semua rapor, silahkan klik tombol <span class="badge badge-warning"><i class="fas fa-signature"></i> Tanda Tangan Semua</span> di card</small>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="col-md-4 mb-3">
                                                <div class="info-box bg-gradient-primary shadow-sm">
                                                    <span class="info-box-icon">
                                                        <i class="fas fa-print"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">
                                                            Cetak Semua Rapor
                                                            <br><small>Kelas <?= esc($kelas->NamaKelas) ?></small>
                                                        </span>
                                                        <div class="mt-2">
                                                            <div class="form-group text-left">
                                                                <label for="tanggalCetak-<?= $kelas->IdKelas ?>" class="small mb-1"><strong>Tanggal Diserahkan:</strong></label>
                                                                <input type="date"
                                                                    class="form-control form-control-sm tanggal-cetak"
                                                                    id="tanggalCetak-<?= $kelas->IdKelas ?>"
                                                                    data-kelas="<?= $kelas->IdKelas ?>"
                                                                    data-semester="<?= $semester ?>"
                                                                    data-tahun-ajaran="<?= session()->get('IdTahunAjaran') ?>"
                                                                    value="<?= date('Y-m-d') ?>">
                                                            </div>
                                                            <div class="form-check text-left mb-2">
                                                                <input class="form-check-input checkbox-tanggal-cetak"
                                                                    type="checkbox"
                                                                    id="confirmTanggal-<?= $kelas->IdKelas ?>"
                                                                    data-kelas="<?= $kelas->IdKelas ?>"
                                                                    data-semester="<?= $semester ?>"
                                                                    data-tahun-ajaran="<?= session()->get('IdTahunAjaran') ?>">
                                                                <label class="form-check-label small" for="confirmTanggal-<?= $kelas->IdKelas ?>">
                                                                    Ya, tanggal tersebut sudah benar
                                                                </label>
                                                            </div>
                                                            <div class="form-group text-left mb-2">
                                                                <label for="peringatanRapor-<?= $kelas->IdKelas ?>" class="small mb-1"><strong>Peringkat yang akan ditampilkan di rapor:</strong></label>
                                                                <input type="text"
                                                                    class="form-control form-control-sm peringatan-rapor"
                                                                    id="peringatanRapor-<?= $kelas->IdKelas ?>"
                                                                    data-kelas="<?= $kelas->IdKelas ?>"
                                                                    data-semester="<?= $semester ?>"
                                                                    data-tahun-ajaran="<?= session()->get('IdTahunAjaran') ?>"
                                                                    data-max="<?= $totalSantri ?>"
                                                                    placeholder="Masukan jumlah peringkat besar yang akan ditampilkan">
                                                            </div>
                                                            <button type="button"
                                                                class="btn btn-warning btn-sm btn-block btn-print-all"
                                                                data-kelas="<?= $kelas->IdKelas ?>"
                                                                data-semester="<?= $semester ?>">
                                                                <i class="fas fa-print"></i> Cetak Semua Rapor
                                                            </button>
                                                        </div>
                                                        <div class="mt-2">
                                                            <small>Info: Untuk mencetak semua rapor, silahkan klik tombol <span class="badge badge-warning"><i class="fas fa-print"></i> Cetak Semua Rapor</span> di card. atau klik tombol <span class="badge badge-warning"><i class="fas fa-print"></i> Cetak Rapor</span> pada kolom Aksi untuk mencetak rapor individual.</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="info-box bg-gradient-secondary shadow-sm">
                                                    <span class="info-box-icon">
                                                        <i class="fas fa-tasks"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">
                                                            Catatan & Absensi
                                                            <br><small>Kelas <?= esc($kelas->NamaKelas) ?></small>
                                                        </span>
                                                        <div class="mt-2">
                                                            <table class="table table-sm table-bordered mb-0" data-no-datatable="true">
                                                                <thead class="thead-light">
                                                                    <tr>
                                                                        <th class="text-left">Aksi</th>
                                                                        <th class="text-center text-success">Sudah</th>
                                                                        <th class="text-center text-warning">Belum</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="text-left">
                                                                            <i class="fas fa-calendar-check text-info"></i> Absensi
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <strong class="text-success stat-absensi-sudah" data-kelas="<?= $kelas->IdKelas ?>"><?= $jumlahAbsensi ?></strong>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <strong class="text-warning stat-absensi-belum" data-kelas="<?= $kelas->IdKelas ?>"><?= $belumAbsensi ?></strong>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-left">
                                                                            <i class="fas fa-sticky-note text-warning"></i> Catatan
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <strong class="text-success stat-catatan-sudah" data-kelas="<?= $kelas->IdKelas ?>"><?= $jumlahCatatan ?></strong>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <strong class="text-warning stat-catatan-belum" data-kelas="<?= $kelas->IdKelas ?>"><?= $belumCatatan ?></strong>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <div class="mt-2">
                                                                <small>Info: Untuk melakukan catatan dan absensi, silahkan klik tombol <span class="badge badge-info">Catatan</span> atau <span class="badge badge-info">Absensi</span> pada kolom Aksi.</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        // Tampilkan tombol setting mapping hanya untuk Wali Kelas dan jika setting aktif
                                        $isWaliKelas = false;
                                        $mappingEnabled = false;

                                        if (!empty($guruKelasPermissions)) {
                                            foreach ($guruKelasPermissions as $perm) {
                                                if (
                                                    isset($perm['IdKelas']) && $perm['IdKelas'] == $kelas->IdKelas &&
                                                    isset($perm['NamaJabatan']) && $perm['NamaJabatan'] === 'Wali Kelas'
                                                ) {
                                                    $isWaliKelas = true;
                                                    break;
                                                }
                                            }
                                        }

                                        if ($isWaliKelas) {
                                            $toolsModel = new \App\Models\ToolsModel();
                                            $idTpq = session()->get('IdTpq');
                                            $mappingEnabled = $toolsModel->getSetting($idTpq, 'MappingWaliKelas');
                                        }
                                        ?>
                                        <?php if ($isWaliKelas && $mappingEnabled): ?>
                                            <a href="<?= base_url('backend/rapor/settingMappingWaliKelas/' . $kelas->IdKelas) ?>"
                                                class="btn btn-info btn-sm">
                                                <i class="fas fa-users-cog"></i> Setting Mapping Wali Kelas
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <table class="table table-bordered table-striped" id="tableSantri-<?= $kelas->IdKelas ?>">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Aksi</th>
                                                <th>Nama Santri</th>
                                                <th>Total Nilai</th>
                                                <th>Nilai Rata-Rata</th>
                                                <th>Rangking</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            foreach ($nilai as $nilaiDetail) :
                                                if ($nilaiDetail->IdKelas == $kelas->IdKelas) :
                                            ?>
                                                    <tr>
                                                        <td><?= $no++ ?></td>
                                                        <td>
                                                            <div class="d-flex flex-column align-items-start action-buttons-container">
                                                                <button type="button" class="btn btn-warning btn-sm btn-print-pdf"
                                                                    data-id="<?= $nilaiDetail->IdSantri ?>"
                                                                    data-semester="<?= $semester ?>"
                                                                    data-kelas="<?= $kelas->IdKelas ?>">
                                                                    <i class="fas fa-print"></i> Cetak Rapor
                                                                </button>
                                                                <label class="toggle-switch toggle-switch-absensi">
                                                                    <input type="checkbox"
                                                                        class="toggle-switch-input checkbox-absensi"
                                                                        data-id="<?= $nilaiDetail->IdSantri ?>"
                                                                        data-semester="<?= $semester ?>"
                                                                        data-kelas="<?= $nilaiDetail->IdKelas ?>"
                                                                        id="absensi-<?= $nilaiDetail->IdSantri ?>-<?= $semester ?>">
                                                                    <span class="toggle-switch-slider">
                                                                        <span class="toggle-switch-label-on">Absensi</span>
                                                                        <span class="toggle-switch-label-off">Absensi</span>
                                                                    </span>
                                                                </label>
                                                                <label class="toggle-switch toggle-switch-catatan">
                                                                    <input type="checkbox"
                                                                        class="toggle-switch-input checkbox-catatan"
                                                                        data-id="<?= $nilaiDetail->IdSantri ?>"
                                                                        data-semester="<?= $semester ?>"
                                                                        data-kelas="<?= $nilaiDetail->IdKelas ?>"
                                                                        id="catatan-<?= $nilaiDetail->IdSantri ?>-<?= $semester ?>">
                                                                    <span class="toggle-switch-slider">
                                                                        <span class="toggle-switch-label-on">Catatan</span>
                                                                        <span class="toggle-switch-label-off">Catatan</span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div><?= $nilaiDetail->NamaSantri ?></div>
                                                            <small class="text-muted">NIS: <?= $nilaiDetail->IdSantri ?></small>
                                                        </td>
                                                        <td><?= $nilaiDetail->TotalNilai ?></td>
                                                        <td><?= $nilaiDetail->NilaiRataRata ?></td>
                                                        <td><?= $nilaiDetail->Rangking ?></td>
                                                    </tr>
                                            <?php
                                                endif;
                                            endforeach;
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    /* Toggle Switch Container */
    .toggle-switch-container {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
    }

    /* Container untuk button dan toggle switch dengan spacing yang sama */
    .action-buttons-container>* {
        margin-bottom: 8px;
    }

    .action-buttons-container>*:last-child {
        margin-bottom: 0;
    }

    /* Toggle Switch */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 120px;
        height: 38px;
        cursor: pointer;
    }

    .toggle-switch-input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-switch-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #dc3545;
        transition: 0.3s;
        border-radius: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 12px;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .toggle-switch-slider:before {
        position: absolute;
        content: "";
        height: 30px;
        width: 30px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        z-index: 2;
    }

    .toggle-switch-input:checked+.toggle-switch-slider {
        background-color: #28a745;
    }

    .toggle-switch-input:checked+.toggle-switch-slider:before {
        transform: translateX(82px);
    }

    .toggle-switch-label-on,
    .toggle-switch-label-off {
        font-size: 0.8rem;
        font-weight: bold;
        color: white;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        z-index: 1;
        transition: opacity 0.3s;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        white-space: nowrap;
        pointer-events: none;
    }

    /* Pastikan label tidak tertutup oleh bulatan slider saat OFF */
    .toggle-switch-label-off {
        opacity: 1;
        padding-left: 20px;
        /* Memberikan ruang untuk bulatan di kiri */
    }

    /* Pastikan label tidak tertutup oleh bulatan slider saat ON */
    .toggle-switch-label-on {
        opacity: 0;
        padding-right: 20px;
        /* Memberikan ruang untuk bulatan di kanan */
    }

    .toggle-switch-input:checked+.toggle-switch-slider .toggle-switch-label-on {
        opacity: 1;
        padding-right: 20px;
        padding-left: 0;
    }

    .toggle-switch-input:checked+.toggle-switch-slider .toggle-switch-label-off {
        opacity: 0;
    }

    /* Button Cetak Rapor - Ukuran sama dengan toggle switch */
    .btn-print-pdf {
        width: 120px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        font-size: 0.8rem;
        border-radius: 38px;
    }
</style>
<script>
    // Simpan referensi original di level global
    var originalDataTableFn = null;

    $(document).ready(function() {
        // Simpan referensi original sebelum override
        if ($.fn.DataTable && !originalDataTableFn) {
            originalDataTableFn = $.fn.DataTable;
        }

        // Override $.fn.DataTable dengan mempertahankan semua method dan property
        if (originalDataTableFn) {
            // Simpan referensi original untuk digunakan di dalam function
            var originalFn = originalDataTableFn;

            // Buat wrapper function yang akan menjadi $.fn.DataTable baru
            var newDataTableFn = function(options) {
                // Cek jika tabel memiliki atribut data-no-datatable="true"
                if (this.length && this.attr('data-no-datatable') === 'true') {
                    console.log('Blocking DataTable initialization - data-no-datatable="true"');
                    // Return jQuery object, tapi pastikan isDataTable mengembalikan false
                    var $table = this;
                    // Hapus data-dt-id jika ada (yang digunakan DataTable untuk tracking)
                    $table.removeAttr('data-dt-id');
                    // Return jQuery object biasa (bukan DataTable instance)
                    return $table;
                }
                // Panggil fungsi original untuk tabel yang boleh diinisialisasi
                try {
                    var result = originalFn.apply(this, arguments);
                    // Pastikan result adalah DataTable instance yang valid
                    if (result && typeof result.columns === 'object' && typeof result.columns.adjust === 'function') {
                        return result;
                    }
                    // Jika result tidak valid, return original result
                    return result;
                } catch (e) {
                    console.error('Error initializing DataTable:', e);
                    return this; // Return jQuery object sebagai fallback
                }
            };

            // Copy semua property dan method dari original ke function baru (termasuk isDataTable)
            // Gunakan Object.assign untuk memastikan semua property ter-copy
            if (typeof Object.assign === 'function') {
                Object.assign(newDataTableFn, originalDataTableFn);
            } else {
                // Fallback untuk browser lama
                for (var prop in originalDataTableFn) {
                    if (originalDataTableFn.hasOwnProperty(prop)) {
                        newDataTableFn[prop] = originalDataTableFn[prop];
                    }
                }
            }

            // Pastikan isDataTable tersedia secara eksplisit (sangat penting!)
            if (originalDataTableFn.isDataTable) {
                newDataTableFn.isDataTable = originalDataTableFn.isDataTable;
            }

            // Copy property penting lainnya yang mungkin tidak enumerable
            if (originalDataTableFn.settings) {
                newDataTableFn.settings = originalDataTableFn.settings;
            }
            if (originalDataTableFn.version) {
                newDataTableFn.version = originalDataTableFn.version;
            }

            // Set sebagai $.fn.DataTable
            $.fn.DataTable = newDataTableFn;

            // Double check: Pastikan isDataTable masih tersedia setelah assignment
            if (!$.fn.DataTable.isDataTable && originalDataTableFn.isDataTable) {
                $.fn.DataTable.isDataTable = originalDataTableFn.isDataTable;
            }
        }

        // Pastikan tabel dengan data-no-datatable tidak terinisialisasi
        $('table[data-no-datatable="true"]').each(function() {
            if ($.fn.DataTable.isDataTable && $.fn.DataTable.isDataTable(this)) {
                $(this).DataTable().destroy();
            }
        });

        // Inisialisasi DataTable untuk setiap kelas (hanya untuk tabel santri) - langsung tanpa initializeDataTableUmum
        <?php foreach ($dataKelas as $kelas): ?>
            $("#tableSantri-<?= $kelas->IdKelas ?>").DataTable({
                "lengthChange": true,
                "responsive": true,
                "autoWidth": false,
                "paging": true,
                "buttons": [],
                "pageLength": 20,
                "lengthMenu": [
                    [10, 20, 30, 50, 100, -1],
                    [10, 20, 30, 50, 100, "Semua"]
                ],
                "language": {
                    "search": "Pencarian:",
                    "paginate": {
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    },
                    "lengthMenu": "Tampilkan _MENU_ entri"
                }
            });
        <?php endforeach; ?>

        // Fungsi untuk memastikan tabel dengan data-no-datatable tidak terinisialisasi
        function preventDataTableOnNoDatatableTables() {
            $('table[data-no-datatable="true"]').each(function() {
                if ($.fn.DataTable.isDataTable && $.fn.DataTable.isDataTable(this)) {
                    try {
                        $(this).DataTable().destroy();
                        // Hapus wrapper DataTable jika ada
                        const $wrapper = $(this).closest('.dataTables_wrapper');
                        if ($wrapper.length) {
                            $wrapper.find('.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate').remove();
                        }
                    } catch (e) {
                        console.log('Error destroying DataTable:', e);
                    }
                }
            });
        }

        // Check setiap 500ms untuk memastikan tabel tidak terinisialisasi
        setInterval(function() {
            preventDataTableOnNoDatatableTables();
        }, 500);

        // Simpan tab aktif ke localStorage saat tab diklik
        $('#kelasTab a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            const targetTab = $(e.target).attr('href'); // e.g., #kelas-123
            const storageKey = 'rapor_active_tab_<?= $semester ?>_<?= session()->get("IdTahunAjaran") ?>';
            localStorage.setItem(storageKey, targetTab);

            // Pastikan tabel dengan data-no-datatable tidak terinisialisasi saat tab diaktifkan
            setTimeout(function() {
                preventDataTableOnNoDatatableTables();
            }, 100);
        });

        // Pulihkan tab aktif dari localStorage saat halaman dimuat
        const storageKey = 'rapor_active_tab_<?= $semester ?>_<?= session()->get("IdTahunAjaran") ?>';
        const savedTab = localStorage.getItem(storageKey);
        if (savedTab) {
            // Cek apakah tab yang disimpan masih ada di halaman
            const tabExists = $(savedTab).length > 0;
            if (tabExists) {
                // Aktifkan tab yang disimpan
                const tabLink = $('#kelasTab a[href="' + savedTab + '"]');
                if (tabLink.length > 0) {
                    tabLink.tab('show');
                }
            }
        }

        // Tampilkan pesan dari session jika ada
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                title: 'Berhasil!',
                text: '<?= session()->getFlashdata('success') ?>',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                title: 'Error!',
                text: '<?= session()->getFlashdata('error') ?>',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('warning')): ?>
            Swal.fire({
                title: 'Peringatan!',
                text: '<?= session()->getFlashdata('warning') ?>',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('info')): ?>
            Swal.fire({
                title: 'Informasi!',
                text: '<?= session()->getFlashdata('info') ?>',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        // Handle print PDF button click
        $(document).on('click', '.btn-print-pdf', function() {
            const IdSantri = $(this).data('id');
            const semester = $(this).data('semester');
            const IdKelas = $(this).data('kelas');

            // Ambil tanggal dari card "Cetak Rapor" yang sesuai dengan kelas
            const tanggal = $(`#tanggalCetak-${IdKelas}`).val();
            const checkbox = $(`#confirmTanggal-${IdKelas}`);

            // Validasi checkbox harus tercentang
            if (!checkbox.is(':checked')) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Silakan centang konfirmasi tanggal di card "Cetak Rapor" terlebih dahulu',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Validasi tanggal harus diisi
            if (!tanggal) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Silakan isi tanggal diserahkan di card "Cetak Rapor" terlebih dahulu',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Ambil nilai peringkat dari input (jika ada)
            const peringkat = $(`#peringatanRapor-${IdKelas}`).val() || '';

            // Panggil endpoint dengan parameter tanggal dan peringkat
            let url = `<?= base_url('backend/rapor/printPdf') ?>/${IdSantri}/${semester}?tanggal=${tanggal}`;
            if (peringkat && peringkat.trim() !== '') {
                url += `&peringkat=${peringkat}`;
            }
            const printWindow = window.open(url, '_blank');
            if (printWindow) {
                printWindow.onload = function() {
                    printWindow.print();
                };
            }
        });

        // Handle print all button click
        // Handle tanda tangan bulk wali kelas
        $(document).on('click', '.btn-ttd-bulk-walas', function() {
            const IdKelas = $(this).data('kelas');
            const namaKelas = $(this).data('nama-kelas');
            const semester = $(this).data('semester');
            const btn = $(this);

            // Hitung jumlah santri di kelas ini dari tabel
            const tableId = '#tableSantri-' + IdKelas;
            const jumlahSantri = $(tableId + ' tbody tr').length;

            Swal.fire({
                title: 'Konfirmasi Tanda Tangan Wali Kelas',
                html: '<div class="text-left">' +
                    '<p><strong>Kelas:</strong> ' + namaKelas + '</p>' +
                    '<p><strong>Semester:</strong> ' + semester + '</p>' +
                    '<p><strong>Jumlah Rapor:</strong> ' + jumlahSantri + ' rapor</p>' +
                    '<p class="mt-3">Apakah Anda yakin ingin menandatangani semua rapor santri dalam kelas ini sebagai Wali Kelas?</p>' +
                    '</div>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Tandatangani',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch('<?= base_url('backend/rapor/ttdBulkWalas') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                IdKelas: IdKelas,
                                Semester: semester
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat memproses tanda tangan',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            });
        });

        // Handle tanda tangan bulk kepala sekolah
        $(document).on('click', '.btn-ttd-bulk-kepsek', function() {
            const IdKelas = $(this).data('kelas');
            const namaKelas = $(this).data('nama-kelas');
            const semester = $(this).data('semester');
            const btn = $(this);

            // Hitung jumlah santri di kelas ini dari tabel
            const tableId = '#tableSantri-' + IdKelas;
            const jumlahSantri = $(tableId + ' tbody tr').length;

            Swal.fire({
                title: 'Konfirmasi Tanda Tangan Kepala Sekolah',
                html: '<div class="text-left">' +
                    '<p><strong>Kelas:</strong> ' + namaKelas + '</p>' +
                    '<p><strong>Semester:</strong> ' + semester + '</p>' +
                    '<p><strong>Jumlah Rapor:</strong> ' + jumlahSantri + ' rapor</p>' +
                    '<p class="mt-3">Apakah Anda yakin ingin menandatangani semua rapor santri dalam kelas ini sebagai Kepala Sekolah?</p>' +
                    '</div>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Tandatangani',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch('<?= base_url('backend/rapor/ttdBulkKepsek') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                IdKelas: IdKelas,
                                Semester: semester
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat memproses tanda tangan',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            });
        });

        // Handle cancel tanda tangan bulk wali kelas
        $(document).on('click', '.btn-cancel-ttd-walas', function() {
            const IdKelas = $(this).data('kelas');
            const namaKelas = $(this).data('nama-kelas');
            const semester = $(this).data('semester');
            const btn = $(this);

            // Hitung jumlah santri di kelas ini dari tabel
            const tableId = '#tableSantri-' + IdKelas;
            const jumlahSantri = $(tableId + ' tbody tr').length;

            Swal.fire({
                title: 'Konfirmasi Batalkan Tanda Tangan Wali Kelas',
                html: '<div class="text-left">' +
                    '<p><strong>Kelas:</strong> ' + namaKelas + '</p>' +
                    '<p><strong>Semester:</strong> ' + semester + '</p>' +
                    '<p><strong>Jumlah Rapor:</strong> ' + jumlahSantri + ' rapor</p>' +
                    '<p class="mt-3 text-danger"><strong>Peringatan:</strong> Tanda tangan yang sudah dibuat akan dihapus dari database dan tidak dapat dikembalikan.</p>' +
                    '<p class="mt-2">Apakah Anda yakin ingin membatalkan semua tanda tangan wali kelas untuk kelas ini?</p>' +
                    '</div>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch('<?= base_url('backend/rapor/cancelBulkWalas') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                IdKelas: IdKelas,
                                Semester: semester
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat memproses pembatalan tanda tangan',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            });
        });

        // Handle cancel tanda tangan bulk kepala sekolah
        $(document).on('click', '.btn-cancel-ttd-kepsek', function() {
            const IdKelas = $(this).data('kelas');
            const namaKelas = $(this).data('nama-kelas');
            const semester = $(this).data('semester');
            const btn = $(this);

            // Hitung jumlah santri di kelas ini dari tabel
            const tableId = '#tableSantri-' + IdKelas;
            const jumlahSantri = $(tableId + ' tbody tr').length;

            Swal.fire({
                title: 'Konfirmasi Batalkan Tanda Tangan Kepala Sekolah',
                html: '<div class="text-left">' +
                    '<p><strong>Kelas:</strong> ' + namaKelas + '</p>' +
                    '<p><strong>Semester:</strong> ' + semester + '</p>' +
                    '<p><strong>Jumlah Rapor:</strong> ' + jumlahSantri + ' rapor</p>' +
                    '<p class="mt-3 text-danger"><strong>Peringatan:</strong> Tanda tangan yang sudah dibuat akan dihapus dari database dan tidak dapat dikembalikan.</p>' +
                    '<p class="mt-2">Apakah Anda yakin ingin membatalkan semua tanda tangan kepala sekolah untuk kelas ini?</p>' +
                    '</div>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Tidak',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch('<?= base_url('backend/rapor/cancelBulkKepsek') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                IdKelas: IdKelas,
                                Semester: semester
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat memproses pembatalan tanda tangan',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            });
        });

        // Handle checkbox konfirmasi tanggal cetak
        $(document).on('change', '.checkbox-tanggal-cetak', function() {
            const IdKelas = $(this).data('kelas');
            const semester = $(this).data('semester');
            const tahunAjaran = $(this).data('tahun-ajaran');
            const isChecked = $(this).is(':checked');
            const storageKey = `confirmTanggalCetak_${IdKelas}_${semester}_${tahunAjaran}`;

            // Simpan status checkbox ke localStorage
            localStorage.setItem(storageKey, isChecked);
        });

        // Load tanggal dan checkbox dari localStorage saat halaman dimuat
        $(document).ready(function() {
            $('.tanggal-cetak').each(function() {
                const IdKelas = $(this).data('kelas');
                const semester = $(this).data('semester');
                const tahunAjaran = $(this).data('tahun-ajaran');
                const storageKeyTanggal = `tanggalCetakRapor_${IdKelas}_${semester}_${tahunAjaran}`;
                const storageKeyCheckbox = `confirmTanggalCetak_${IdKelas}_${semester}_${tahunAjaran}`;

                // Ambil tanggal dari localStorage jika ada
                const savedTanggal = localStorage.getItem(storageKeyTanggal);
                if (savedTanggal) {
                    $(this).val(savedTanggal);
                }

                // Ambil status checkbox dari localStorage jika ada
                const savedCheckbox = localStorage.getItem(storageKeyCheckbox);
                if (savedCheckbox === 'true') {
                    $(`#confirmTanggal-${IdKelas}`).prop('checked', true);
                }
            });

            // Load nilai peringkat dari localStorage saat halaman dimuat
            $('.peringatan-rapor').each(function() {
                const IdKelas = $(this).data('kelas');
                const semester = $(this).data('semester');
                const tahunAjaran = $(this).data('tahun-ajaran');
                const storageKeyPeringkat = `peringkatRapor_${IdKelas}_${semester}_${tahunAjaran}`;

                // Ambil nilai peringkat dari localStorage jika ada
                const savedPeringkat = localStorage.getItem(storageKeyPeringkat);
                if (savedPeringkat) {
                    $(this).val(savedPeringkat);
                }
            });

        });

        // Handle perubahan tanggal cetak
        $(document).on('change', '.tanggal-cetak', function() {
            const IdKelas = $(this).data('kelas');
            const semester = $(this).data('semester');
            const tahunAjaran = $(this).data('tahun-ajaran');
            const tanggal = $(this).val();
            const checkbox = $(`#confirmTanggal-${IdKelas}`);
            const storageKeyCheckbox = `confirmTanggalCetak_${IdKelas}_${semester}_${tahunAjaran}`;

            // Simpan tanggal ke localStorage
            if (tanggal) {
                const storageKey = `tanggalCetakRapor_${IdKelas}_${semester}_${tahunAjaran}`;
                localStorage.setItem(storageKey, tanggal);
            }

            // Reset checkbox jika tanggal berubah
            if (checkbox.is(':checked')) {
                checkbox.prop('checked', false);
                localStorage.setItem(storageKeyCheckbox, 'false');
            }
        });

        // Handle input peringatan rapor - hanya menerima angka
        $(document).on('keypress', '.peringatan-rapor', function(e) {
            // Hanya izinkan angka (0-9)
            const charCode = (e.which) ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                e.preventDefault();
                return false;
            }
        });

        // Handle input peringatan rapor - validasi maksimal dan hanya angka
        $(document).on('input paste', '.peringatan-rapor', function() {
            const $input = $(this);
            const IdKelas = $input.data('kelas');
            const semester = $input.data('semester');
            const tahunAjaran = $input.data('tahun-ajaran');
            const maxValue = parseInt($input.data('max')) || 0;
            let value = $input.val();

            // Hapus karakter non-angka
            value = value.replace(/[^0-9]/g, '');

            // Jika ada nilai dan melebihi maksimal, batasi ke maksimal
            if (value && parseInt(value) > maxValue) {
                value = maxValue.toString();
                $input.addClass('is-invalid');
            } else {
                $input.removeClass('is-invalid');
            }

            // Update nilai input
            $input.val(value);

            // Simpan nilai peringkat ke localStorage
            const storageKey = `peringkatRapor_${IdKelas}_${semester}_${tahunAjaran}`;
            if (value && value.trim() !== '') {
                localStorage.setItem(storageKey, value);
            } else {
                localStorage.removeItem(storageKey);
            }
        });

        // Handle blur untuk validasi akhir
        $(document).on('blur', '.peringatan-rapor', function() {
            const $input = $(this);
            const IdKelas = $input.data('kelas');
            const semester = $input.data('semester');
            const tahunAjaran = $input.data('tahun-ajaran');
            const maxValue = parseInt($input.data('max')) || 0;
            let value = $input.val();

            // Hapus karakter non-angka
            value = value.replace(/[^0-9]/g, '');

            // Jika ada nilai dan melebihi maksimal, batasi ke maksimal
            if (value && parseInt(value) > maxValue) {
                value = maxValue.toString();
                Swal.fire({
                    title: 'Peringatan!',
                    text: `Angka peringkat tidak boleh melebihi ${maxValue} (jumlah santri kelas)`,
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    timer: 3000
                });
            }

            // Update nilai input
            $input.val(value);
            $input.removeClass('is-invalid');

            // Simpan nilai peringkat ke localStorage
            const storageKey = `peringkatRapor_${IdKelas}_${semester}_${tahunAjaran}`;
            if (value && value.trim() !== '') {
                localStorage.setItem(storageKey, value);
            } else {
                localStorage.removeItem(storageKey);
            }
        });

        $(document).on('click', '.btn-print-all', function() {
            const kelasId = $(this).data('kelas');
            const semester = $(this).data('semester');
            const tanggal = $(`#tanggalCetak-${kelasId}`).val();
            const checkbox = $(`#confirmTanggal-${kelasId}`);
            const peringkat = $(`#peringatanRapor-${kelasId}`).val();

            // Validasi checkbox harus tercentang
            if (!checkbox.is(':checked')) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Silakan centang konfirmasi bahwa tanggal sudah diisi',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Validasi tanggal harus diisi
            if (!tanggal) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Silakan isi tanggal diserahkan terlebih dahulu',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const estimatedTime = 10; // Estimasi waktu default

            // Tampilkan progress modal
            let progress = 0;
            let progressInterval;
            let downloadStarted = false;

            Swal.fire({
                title: 'Memproses Rapor',
                html: `
                    <div class="text-center mb-3">
                        <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                        <p class="mb-2">Sedang membuat file ZIP rapor...</p>
                        <p class="text-muted small" id="progress-status">Memulai proses...</p>
                    </div>
                    <div class="progress" style="height: 30px;">
                        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                             role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <span id="progress-text" class="font-weight-bold" style="line-height: 30px;">0%</span>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted" id="progress-detail">Mempersiapkan...</small>
                    </div>
                `,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    // Simulasi progress (akan diupdate oleh actual download)
                    progressInterval = setInterval(() => {
                        if (!downloadStarted && progress < 90) {
                            progress += Math.random() * 5; // Increment random 0-5%
                            if (progress > 90) progress = 90; // Max 90% sampai download benar-benar selesai

                            updateProgress(progress, 'Membuat PDF rapor...');
                        }
                    }, 500);
                },
                willClose: () => {
                    if (progressInterval) {
                        clearInterval(progressInterval);
                    }
                }
            });

            // Function untuk update progress
            function updateProgress(percent, status, detail = '') {
                progress = Math.min(100, Math.max(0, percent));
                $('#progress-bar').css('width', progress + '%').attr('aria-valuenow', progress);
                $('#progress-text').text(Math.round(progress) + '%');
                if (status) $('#progress-status').text(status);
                if (detail) $('#progress-detail').text(detail);
            }

            // Download file menggunakan fetch untuk track progress
            let url = `<?= base_url('backend/rapor/printPdfBulk') ?>/${kelasId}/${semester}?tanggal=${tanggal}`;
            if (peringkat && peringkat.trim() !== '') {
                url += `&peringkat=${peringkat}`;
            }

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal membuat file ZIP');
                    }
                    downloadStarted = true;
                    updateProgress(95, 'File ZIP siap, sedang mengunduh...', 'Hampir selesai...');

                    // Ambil nama file dari Content-Disposition header
                    const contentDisposition = response.headers.get('Content-Disposition');
                    let filename = 'Rapor_Kelas.zip';
                    if (contentDisposition) {
                        const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                        if (filenameMatch && filenameMatch[1]) {
                            filename = filenameMatch[1].replace(/['"]/g, '');
                        }
                    }

                    return response.blob().then(blob => ({
                        blob,
                        filename
                    }));
                })
                .then(({
                    blob,
                    filename
                }) => {
                    // Buat URL untuk download
                    const blobUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = blobUrl;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(blobUrl);

                    // Update progress ke 100%
                    updateProgress(100, 'Download selesai!', 'File ZIP berhasil diunduh');

                    // Tutup progress dan tampilkan success message
                    setTimeout(() => {
                        if (progressInterval) {
                            clearInterval(progressInterval);
                        }
                        Swal.close();
                        Swal.fire({
                            title: 'Berhasil!',
                            html: `
                                <div class="text-center">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <p>File ZIP rapor berhasil dibuat dan diunduh</p>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }, 500);
                })
                .catch(error => {
                    if (progressInterval) {
                        clearInterval(progressInterval);
                    }
                    Swal.close();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal membuat file ZIP: ' + error.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        });


        // Load data absensi dan catatan saat halaman dimuat (menggunakan data dari PHP)
        function loadCatatanAbsensiData() {
            const raportSettingsMap = <?= json_encode($raportSettingsMap ?? []) ?>;

            $('.checkbox-absensi, .checkbox-catatan').each(function() {
                const checkbox = $(this);
                const IdSantri = checkbox.data('id');
                const semester = checkbox.data('semester');
                const key = IdSantri + '_' + semester;

                // Ambil data dari map yang sudah di-pass dari PHP
                const setting = raportSettingsMap[key];

                if (setting) {
                    // Set checkbox absensi
                    if (checkbox.hasClass('checkbox-absensi')) {
                        checkbox.prop('checked', setting.ShowAbsensi == 1);
                    }

                    // Set checkbox catatan
                    if (checkbox.hasClass('checkbox-catatan')) {
                        checkbox.prop('checked', setting.ShowCatatan == 1);
                    }
                }
            });
        }

        // Panggil saat halaman dimuat
        loadCatatanAbsensiData();

        // Fungsi untuk menghitung jumlah tidak masuk otomatis
        function hitungJumlahTidakMasuk() {
            const jumlahIzin = parseInt($('#jumlahIzin').val()) || 0;
            const jumlahAlfa = parseInt($('#jumlahAlfa').val()) || 0;
            const jumlahSakit = parseInt($('#jumlahSakit').val()) || 0;
            const total = jumlahIzin + jumlahAlfa + jumlahSakit;
            $('#jumlahTidakMasuk').val(total);
            $('#jumlahTidakMasukHidden').val(total); // Update hidden input untuk form submit
        }

        // Event listener untuk menghitung otomatis saat input berubah
        $(document).on('input change', '#jumlahIzin, #jumlahAlfa, #jumlahSakit', function() {
            hitungJumlahTidakMasuk();
        });

        // Handle checkbox absensi change (lebih reliable untuk checkbox)
        $(document).on('change', '.checkbox-absensi', function(e) {
            e.preventDefault();
            e.stopPropagation();

            console.log('Checkbox absensi changed');

            const checkbox = $(this);
            const IdSantri = checkbox.data('id');
            const IdKelas = checkbox.data('kelas');
            const semester = checkbox.data('semester');
            const IdTahunAjaran = '<?= session()->get("IdTahunAjaran") ?>';

            console.log('Data:', {
                IdSantri,
                IdKelas,
                semester,
                IdTahunAjaran
            });

            // Prevent default checkbox behavior
            checkbox.prop('checked', !checkbox.prop('checked'));

            // Set form values first
            $('#absensiIdSantri').val(IdSantri);
            $('#absensiIdKelas').val(IdKelas);
            $('#absensiSemester').val(semester);

            // Load data existing
            $.ajax({
                url: '<?= base_url("backend/rapor/getCatatanAbsensi") ?>',
                type: 'POST',
                data: {
                    IdSantri: IdSantri,
                    IdTahunAjaran: IdTahunAjaran,
                    Semester: semester
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        const data = response.data;
                        const absensiData = data.AbsensiData || {};

                        $('#showAbsensi').prop('checked', data.ShowAbsensi == 1);
                        $('#jumlahIzin').val(absensiData.jumlahIzin || 0);
                        $('#jumlahAlfa').val(absensiData.jumlahAlfa || 0);
                        $('#jumlahSakit').val(absensiData.jumlahSakit || 0);

                        // Hitung jumlah tidak masuk otomatis
                        hitungJumlahTidakMasuk();
                    } else {
                        // Initialize dengan nilai default jika data tidak ada
                        $('#showAbsensi').prop('checked', false);
                        $('#jumlahIzin').val(0);
                        $('#jumlahAlfa').val(0);
                        $('#jumlahSakit').val(0);

                        // Hitung jumlah tidak masuk otomatis
                        hitungJumlahTidakMasuk();
                    }

                    // Tampilkan modal
                    console.log('Showing modal absensi');
                    hitungJumlahTidakMasuk(); // Hitung ulang sebelum tampil
                    $('#modalAbsensi').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error loading absensi data:', error, xhr.responseText);
                    // Initialize dengan nilai default jika error
                    $('#showAbsensi').prop('checked', false);
                    $('#jumlahIzin').val(0);
                    $('#jumlahAlfa').val(0);
                    $('#jumlahSakit').val(0);

                    // Hitung jumlah tidak masuk otomatis
                    hitungJumlahTidakMasuk();

                    // Tampilkan modal meskipun ada error
                    console.log('Showing modal absensi (error case)');
                    hitungJumlahTidakMasuk(); // Hitung ulang sebelum tampil
                    $('#modalAbsensi').modal('show');
                }
            });
        });

        // Handle checkbox catatan change (lebih reliable untuk checkbox)
        $(document).on('change', '.checkbox-catatan', function(e) {
            e.preventDefault();
            e.stopPropagation();

            console.log('Checkbox catatan changed');

            const checkbox = $(this);
            const IdSantri = checkbox.data('id');
            const IdKelas = checkbox.data('kelas');
            const semester = checkbox.data('semester');
            const IdTahunAjaran = '<?= session()->get("IdTahunAjaran") ?>';

            console.log('Data:', {
                IdSantri,
                IdKelas,
                semester,
                IdTahunAjaran
            });

            // Prevent default checkbox behavior
            checkbox.prop('checked', !checkbox.prop('checked'));

            // Set form values first
            $('#catatanIdSantri').val(IdSantri);
            $('#catatanIdKelas').val(IdKelas);
            $('#catatanSemester').val(semester);

            // Ambil catatan default berdasarkan nilai
            $.ajax({
                url: '<?= base_url("backend/rapor/getCatatanDefaultByNilai") ?>',
                type: 'POST',
                data: {
                    IdSantri: IdSantri,
                    Semester: semester
                },
                dataType: 'json',
                success: function(response) {
                    let catatanDefault = '';
                    let selectedCatatanId = null;

                    // Populate dropdown opsi catatan
                    const selectCatatan = $('#selectCatatanSource');
                    selectCatatan.empty();
                    selectCatatan.append('<option value="">-- Pilih Sumber Catatan --</option>');

                    if (response.success && response.allOpsiCatatan && response.allOpsiCatatan.length > 0) {
                        // Jika ada lebih dari 1 opsi, tampilkan dropdown
                        if (response.allOpsiCatatan.length > 1) {
                            response.allOpsiCatatan.forEach(function(opsi) {
                                const option = $('<option></option>')
                                    .attr('value', opsi.id)
                                    .attr('data-catatan', opsi.Catatan)
                                    .text(opsi.label + ' (Nilai: ' + opsi.NilaiHuruf + ')');

                                // Set selected jika ini adalah yang default
                                if (opsi.id == response.selectedCatatanId) {
                                    option.prop('selected', true);
                                    catatanDefault = opsi.Catatan;
                                    selectedCatatanId = opsi.id;
                                }

                                selectCatatan.append(option);
                            });
                            selectCatatan.show();
                        } else {
                            // Hanya 1 opsi, langsung set
                            const opsi = response.allOpsiCatatan[0];
                            catatanDefault = opsi.Catatan;
                            selectedCatatanId = opsi.id;
                            selectCatatan.hide();
                        }
                    } else if (response.success && response.catatan) {
                        // Fallback ke catatan default jika tidak ada opsi
                        catatanDefault = response.catatan;
                        selectCatatan.hide();
                    }

                    // Event handler untuk perubahan pilihan catatan
                    selectCatatan.off('change').on('change', function() {
                        const selectedOption = $(this).find('option:selected');
                        if (selectedOption.val()) {
                            $('#catatanDefault').val(selectedOption.data('catatan') || '');
                        }
                    });

                    // Load data existing
                    $.ajax({
                        url: '<?= base_url("backend/rapor/getCatatanAbsensi") ?>',
                        type: 'POST',
                        data: {
                            IdSantri: IdSantri,
                            IdTahunAjaran: IdTahunAjaran,
                            Semester: semester
                        },
                        dataType: 'json',
                        success: function(response2) {
                            if (response2.success && response2.data) {
                                const data = response2.data;
                                const catatanData = data.CatatanData || {};

                                $('#showCatatan').prop('checked', data.ShowCatatan == 1);

                                // Jika ada catatan yang sudah disimpan, gunakan itu
                                if (catatanData.catatanDefault) {
                                    $('#catatanDefault').val(catatanData.catatanDefault);
                                    // Set selected option jika ada
                                    if (catatanData.selectedCatatanId) {
                                        $('#selectCatatanSource').val(catatanData.selectedCatatanId);
                                    }
                                } else {
                                    $('#catatanDefault').val(catatanDefault);
                                    if (selectedCatatanId) {
                                        $('#selectCatatanSource').val(selectedCatatanId);
                                    }
                                }

                                $('#catatanKhusus').val(catatanData.catatanKhusus || '');
                            } else {
                                // Initialize dengan nilai default jika data tidak ada
                                $('#showCatatan').prop('checked', false);
                                $('#catatanDefault').val(catatanDefault);
                                if (selectedCatatanId) {
                                    $('#selectCatatanSource').val(selectedCatatanId);
                                }
                                $('#catatanKhusus').val('');
                            }

                            // Tampilkan modal
                            console.log('Showing modal catatan');
                            $('#modalCatatan').modal('show');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading catatan data:', error, xhr.responseText);
                            // Initialize dengan nilai default jika error
                            $('#showCatatan').prop('checked', false);
                            $('#catatanDefault').val(catatanDefault);
                            if (selectedCatatanId) {
                                $('#selectCatatanSource').val(selectedCatatanId);
                            }
                            $('#catatanKhusus').val('');

                            // Tampilkan modal meskipun ada error
                            console.log('Showing modal catatan (error case 1)');
                            $('#modalCatatan').modal('show');
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading default catatan:', error, xhr.responseText);
                    // Hide dropdown jika error
                    $('#selectCatatanSource').hide();

                    // Load data existing meskipun error
                    $.ajax({
                        url: '<?= base_url("backend/rapor/getCatatanAbsensi") ?>',
                        type: 'POST',
                        data: {
                            IdSantri: IdSantri,
                            IdTahunAjaran: IdTahunAjaran,
                            Semester: semester
                        },
                        dataType: 'json',
                        success: function(response2) {
                            if (response2.success && response2.data) {
                                const data = response2.data;
                                const catatanData = data.CatatanData || {};

                                $('#showCatatan').prop('checked', data.ShowCatatan == 1);
                                $('#catatanDefault').val(catatanData.catatanDefault || '');
                                $('#catatanKhusus').val(catatanData.catatanKhusus || '');

                                // Set selected option jika ada
                                if (catatanData.selectedCatatanId) {
                                    $('#selectCatatanSource').val(catatanData.selectedCatatanId);
                                }
                            } else {
                                $('#showCatatan').prop('checked', false);
                                $('#catatanDefault').val('');
                                $('#catatanKhusus').val('');
                            }

                            $('#modalCatatan').modal('show');
                        },
                        error: function() {
                            // Initialize dengan nilai default
                            $('#showCatatan').prop('checked', false);
                            $('#catatanDefault').val('');
                            $('#catatanKhusus').val('');

                            console.log('Showing modal catatan (error case 2)');
                            $('#modalCatatan').modal('show');
                        }
                    });
                }
            });
        });

        // Handle generate absensi dari tabel
        $('#btnGenerateAbsensi').on('click', function() {
            const IdSantri = $('#absensiIdSantri').val();
            const IdTahunAjaran = '<?= session()->get("IdTahunAjaran") ?>';
            const semester = $('#absensiSemester').val();

            if (!IdSantri || !semester) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Data santri atau semester tidak ditemukan',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Tampilkan loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang mengambil data dari tabel absensi',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url("backend/rapor/getAbsensiFromTable") ?>',
                type: 'POST',
                data: {
                    IdSantri: IdSantri,
                    IdTahunAjaran: IdTahunAjaran,
                    Semester: semester
                },
                dataType: 'json',
                success: function(response) {
                    Swal.close();

                    if (response.success && response.data) {
                        const data = response.data;

                        // Isi form dengan data dari tabel
                        $('#jumlahIzin').val(data.jumlahIzin || 0);
                        $('#jumlahAlfa').val(data.jumlahAlfa || 0);
                        $('#jumlahSakit').val(data.jumlahSakit || 0);

                        // Hitung jumlah tidak masuk otomatis
                        hitungJumlahTidakMasuk();

                        Swal.fire({
                            title: 'Berhasil!',
                            html: `Data absensi berhasil diambil dari tabel.<br>
                                   <strong>Jumlah Hadir:</strong> ${data.jumlahHadir || 0}<br>
                                   <strong>Jumlah Izin:</strong> ${data.jumlahIzin}<br>
                                   <strong>Jumlah Alfa:</strong> ${data.jumlahAlfa}<br>
                                   <strong>Jumlah Sakit:</strong> ${data.jumlahSakit}<br>
                                   <strong>Total Record:</strong> ${data.totalRecords}`,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Informasi',
                            text: response.message || 'Tidak ada data absensi dari tabel untuk periode ini',
                            icon: 'info',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    console.error('Error:', error, xhr.responseText);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengambil data: ' + error,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Fungsi untuk update statistik di card
        function updateStatistikCard(IdKelas) {
            const tableId = '#tableSantri-' + IdKelas;
            const totalSantri = $(tableId + ' tbody tr').length;

            // Hitung jumlah checkbox yang tercentang
            let jumlahCatatan = 0;
            let jumlahAbsensi = 0;

            $(tableId + ' tbody tr').each(function() {
                const row = $(this);
                const checkboxCatatan = row.find('.checkbox-catatan');
                const checkboxAbsensi = row.find('.checkbox-absensi');

                if (checkboxCatatan.is(':checked')) {
                    jumlahCatatan++;
                }
                if (checkboxAbsensi.is(':checked')) {
                    jumlahAbsensi++;
                }
            });

            const belumCatatan = totalSantri - jumlahCatatan;
            const belumAbsensi = totalSantri - jumlahAbsensi;

            // Update statistik di card
            $(`.stat-catatan-sudah[data-kelas="${IdKelas}"]`).text(jumlahCatatan);
            $(`.stat-catatan-belum[data-kelas="${IdKelas}"]`).text(belumCatatan);
            $(`.stat-absensi-sudah[data-kelas="${IdKelas}"]`).text(jumlahAbsensi);
            $(`.stat-absensi-belum[data-kelas="${IdKelas}"]`).text(belumAbsensi);
        }

        // Handle save absensi
        $('#btnSaveAbsensi').on('click', function() {
            // Hitung ulang jumlah tidak masuk sebelum menyimpan
            hitungJumlahTidakMasuk();

            const formData = $('#formAbsensi').serialize();
            const IdKelas = $('#absensiIdKelas').val();

            $.ajax({
                url: '<?= base_url("backend/rapor/saveAbsensi") ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const IdSantri = $('#absensiIdSantri').val();
                        const semester = $('#absensiSemester').val();
                        const isChecked = $('#showAbsensi').is(':checked');

                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            timerProgressBar: true,
                            showConfirmButton: false
                        }).then(() => {
                            $('#modalAbsensi').modal('hide');
                            // Update checkbox status
                            $(`#absensi-${IdSantri}-${semester}`).prop('checked', isChecked);

                            // Update statistik di card
                            updateStatistikCard(IdKelas);
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menyimpan data',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Handle save catatan
        $('#btnSaveCatatan').on('click', function() {
            // Ambil catatan dari dropdown jika dipilih
            const selectedCatatanId = $('#selectCatatanSource').val();
            const selectedOption = $('#selectCatatanSource option:selected');
            let catatanDefault = $('#catatanDefault').val();

            // Jika ada pilihan dari dropdown, gunakan catatan dari dropdown
            if (selectedCatatanId && selectedOption.length > 0) {
                const catatanFromDropdown = selectedOption.data('catatan');
                if (catatanFromDropdown) {
                    catatanDefault = catatanFromDropdown;
                    $('#catatanDefault').val(catatanDefault);
                }
            }

            const formData = $('#formCatatan').serialize();
            const IdKelas = $('#catatanIdKelas').val();

            // Tambahkan CatatanSource ke formData
            const formDataObj = {};
            $.each(formData.split('&'), function(i, pair) {
                const parts = pair.split('=');
                formDataObj[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1] || '');
            });
            formDataObj['CatatanSource'] = selectedCatatanId || '';

            $.ajax({
                url: '<?= base_url("backend/rapor/saveCatatan") ?>',
                type: 'POST',
                data: formDataObj,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const IdSantri = $('#catatanIdSantri').val();
                        const semester = $('#catatanSemester').val();
                        const isChecked = $('#showCatatan').is(':checked');

                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            timerProgressBar: true,
                            showConfirmButton: false
                        }).then(() => {
                            $('#modalCatatan').modal('hide');
                            // Update checkbox status
                            $(`#catatan-${IdSantri}-${semester}`).prop('checked', isChecked);

                            // Update statistik di card
                            updateStatistikCard(IdKelas);
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menyimpan data',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });
</script>

<!-- Modal Absensi -->
<div class="modal fade" id="modalAbsensi" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Absensi</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formAbsensi">
                    <input type="hidden" name="IdSantri" id="absensiIdSantri">
                    <input type="hidden" name="IdKelas" id="absensiIdKelas">
                    <input type="hidden" name="Semester" id="absensiSemester">

                    <div class="form-group">
                        <label class="mb-2"><strong>Tampilkan Absensi di Rapor</strong></label>
                        <div class="d-flex align-items-center">
                            <div class="toggle-switch-container">
                                <label class="toggle-switch">
                                    <input type="checkbox"
                                        class="toggle-switch-input"
                                        name="ShowAbsensi"
                                        id="showAbsensi">
                                    <span class="toggle-switch-slider">
                                        <span class="toggle-switch-label-on">YA</span>
                                        <span class="toggle-switch-label-off">TIDAK</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Jumlah Izin</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="JumlahIzin" id="jumlahIzin" min="0" value="0">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-info btn-sm" id="btnGenerateAbsensi" title="Generate dari Tabel Absensi">
                                    <i class="fas fa-sync-alt"></i> Generate
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Klik tombol Generate untuk mengambil data dari tabel absensi.</small>
                    </div>

                    <div class="form-group">
                        <label>Jumlah Alfa</label>
                        <input type="number" class="form-control" name="JumlahAlfa" id="jumlahAlfa" min="0" value="0">
                    </div>

                    <div class="form-group">
                        <label>Jumlah Sakit</label>
                        <input type="number" class="form-control" name="JumlahSakit" id="jumlahSakit" min="0" value="0">
                    </div>

                    <div class="form-group">
                        <label>Jumlah Tidak Masuk <small class="text-muted">(Otomatis: Izin + Alfa + Sakit)</small></label>
                        <input type="number" class="form-control" id="jumlahTidakMasuk" min="0" value="0" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                        <input type="hidden" name="JumlahTidakMasuk" id="jumlahTidakMasukHidden">
                        <small class="form-text text-muted">Jumlah ini dihitung otomatis dari jumlah izin, alfa, dan sakit.</small>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSaveAbsensi">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Catatan -->
<div class="modal fade" id="modalCatatan" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Catatan</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formCatatan">
                    <input type="hidden" name="IdSantri" id="catatanIdSantri">
                    <input type="hidden" name="IdKelas" id="catatanIdKelas">
                    <input type="hidden" name="Semester" id="catatanSemester">

                    <div class="form-group">
                        <label class="mb-2"><strong>Tampilkan Catatan di Rapor</strong></label>
                        <div class="d-flex align-items-center">
                            <div class="toggle-switch-container">
                                <label class="toggle-switch">
                                    <input type="checkbox"
                                        class="toggle-switch-input"
                                        name="ShowCatatan"
                                        id="showCatatan">
                                    <span class="toggle-switch-slider">
                                        <span class="toggle-switch-label-on">YA</span>
                                        <span class="toggle-switch-label-off">TIDAK</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Pilih Sumber Catatan</label>
                        <select class="form-control" id="selectCatatanSource" name="CatatanSource" style="display: none;">
                            <option value="">-- Pilih Sumber Catatan --</option>
                        </select>
                        <small class="form-text text-muted">Pilih catatan dari kriteria yang tersedia. Secara default menggunakan yang paling spesifik. Dropdown akan muncul jika tersedia lebih dari satu opsi catatan.</small>
                    </div>

                    <div class="form-group">
                        <label>Catatan Default (Berdasarkan Nilai Rata-Rata)</label>
                        <textarea class="form-control" name="CatatanDefault" id="catatanDefault" rows="5" readonly></textarea>
                        <small class="form-text text-muted">Catatan ini diambil dari kriteria catatan rapor berdasarkan nilai rata-rata santri.</small>
                    </div>

                    <div class="form-group">
                        <label>Catatan Khusus (Opsional)</label>
                        <textarea class="form-control" name="CatatanKhusus" id="catatanKhusus" rows="5" placeholder="Tambahkan catatan khusus dari wali kelas jika diperlukan..."></textarea>
                        <small class="form-text text-muted">Catatan khusus ini akan digabungkan dengan catatan default.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSaveCatatan">Simpan</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>