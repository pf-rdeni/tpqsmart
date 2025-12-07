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
                            <strong>Atur Absensi:</strong> Klik checkbox <span class="badge badge-info">Absensi</span> pada baris santri untuk mengisi data absensi (Izin, Alfa, Sakit).
                            Anda dapat menggunakan tombol <span class="badge badge-info"><i class="fas fa-sync-alt"></i> Generate</span> untuk mengambil data dari tabel absensi otomatis.
                        </li>
                        <li class="mb-2">
                            <strong>Atur Catatan:</strong> Klik checkbox <span class="badge badge-success">Catatan</span> pada baris santri untuk mengatur catatan rapor.
                            Sistem akan menampilkan catatan default berdasarkan nilai rata-rata santri. Jika tersedia beberapa opsi catatan (Spesifik Kelas, Spesifik TPQ, Umum),
                            Anda dapat memilih dari dropdown. Anda juga dapat menambahkan catatan khusus dari wali kelas.
                        </li>
                        <li class="mb-2">
                            <strong>Cetak Rapor:</strong> Klik tombol <span class="badge badge-warning"><i class="fas fa-print"></i> Cetak Rapor</span> untuk mencetak rapor individual,
                            atau <span class="badge badge-primary"><i class="fas fa-print"></i> Cetak Semua Rapor Kelas</span> untuk mencetak semua rapor dalam satu kelas sekaligus.
                        </li>
                        <li class="mb-2">
                            <strong>Tanda Tangan:</strong>
                            <ul class="mt-2">
                                <li><span class="badge badge-info"><i class="fas fa-signature"></i> Ttd Walas</span> - Hanya Wali Kelas yang dapat menandatangani</li>
                                <li><span class="badge badge-success"><i class="fas fa-signature"></i> Ttd Kepsek</span> - Hanya Kepala TPQ/Kepala Sekolah yang dapat menandatangani</li>
                            </ul>
                            Tombol akan otomatis nonaktif setelah ditandatangani atau jika Anda tidak memiliki akses.
                        </li>
                    </ol>

                    <div class="alert alert-info mb-0">
                        <h5 class="alert-heading"><i class="fas fa-lightbulb"></i> Tips:</h5>
                        <ul class="mb-0">
                            <li>Checkbox <strong>Absensi</strong> dan <strong>Catatan</strong> berfungsi sebagai toggle untuk menampilkan/menyembunyikan informasi di rapor PDF.</li>
                            <li>Data absensi dan catatan akan tersimpan secara otomatis setelah Anda klik tombol <strong>Simpan</strong> di modal.</li>
                            <li>Catatan default dipilih secara otomatis berdasarkan prioritas: <strong>Spesifik Kelas</strong> → <strong>Spesifik TPQ</strong> → <strong>Umum</strong>.</li>
                            <li>Jika ada beberapa opsi catatan untuk nilai yang sama, dropdown akan muncul untuk memilih sumber catatan yang diinginkan.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= $page_title ?> - Semester <?= $semester ?></h3>
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
                                                        if (isset($perm['IdKelas']) && $perm['IdKelas'] == $kelas->IdKelas && 
                                                            isset($perm['NamaJabatan']) && $perm['NamaJabatan'] === 'Wali Kelas') {
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
                                                    <div class="card <?= $allSignedWalas ? 'border-danger' : 'border-info' ?> shadow-sm h-100" style="background: linear-gradient(135deg, <?= $allSignedWalas ? '#fff5f5' : '#e8f4f8' ?> 0%, #ffffff 100%);">
                                                        <div class="card-body text-center">
                                                            <h5 class="card-title">
                                                                <i class="fas fa-signature <?= $allSignedWalas ? 'text-danger' : 'text-info' ?>"></i>
                                                                <?= $allSignedWalas ? 'Batalkan TTD' : 'Tanda Tangan QR' ?>
                                                                <br><small>Wali Kelas</small>
                                                            </h5>
                                                            <div class="mt-3">
                                                                <div class="row text-center">
                                                                    <div class="col-6">
                                                                        <div class="text-success">
                                                                            <i class="fas fa-check-circle fa-2x"></i>
                                                                            <p class="mb-0 mt-2"><strong><?= $ttdWalas ?></strong></p>
                                                                            <small>Sudah TTD</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="text-warning">
                                                                            <i class="fas fa-clock fa-2x"></i>
                                                                            <p class="mb-0 mt-2"><strong><?= $belumTtdWalas ?></strong></p>
                                                                            <small>Belum TTD</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-3">
                                                                    <p class="mb-1"><strong>Total: <?= $totalSantri ?> Rapor</strong></p>
                                                                    <?php if ($allSignedWalas): ?>
                                                                        <button type="button" class="btn btn-danger btn-sm btn-block btn-cancel-ttd-walas" 
                                                                                data-kelas="<?= $kelas->IdKelas ?>" 
                                                                                data-nama-kelas="<?= esc($kelas->NamaKelas) ?>"
                                                                                data-semester="<?= $semester ?>">
                                                                            <i class="fas fa-times-circle"></i> Batalkan TTD
                                                                        </button>
                                                                    <?php else: ?>
                                                                        <button type="button" class="btn btn-info btn-sm btn-block btn-ttd-bulk-walas" 
                                                                                data-kelas="<?= $kelas->IdKelas ?>" 
                                                                                data-nama-kelas="<?= esc($kelas->NamaKelas) ?>"
                                                                                data-semester="<?= $semester ?>">
                                                                            <i class="fas fa-signature"></i> Tanda Tangan Semua
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($isKepalaSekolahBtn): ?>
                                                <div class="col-md-4 mb-3">
                                                    <div class="card <?= $allSignedKepsek ? 'border-danger' : 'border-success' ?> shadow-sm h-100" style="background: linear-gradient(135deg, <?= $allSignedKepsek ? '#fff5f5' : '#e8f8f0' ?> 0%, #ffffff 100%);">
                                                        <div class="card-body text-center">
                                                            <h5 class="card-title">
                                                                <i class="fas fa-signature <?= $allSignedKepsek ? 'text-danger' : 'text-success' ?>"></i>
                                                                <?= $allSignedKepsek ? 'Batalkan TTD' : 'Tanda Tangan QR' ?>
                                                                <br><small>Kepala Sekolah</small>
                                                            </h5>
                                                            <div class="mt-3">
                                                                <div class="row text-center">
                                                                    <div class="col-6">
                                                                        <div class="text-success">
                                                                            <i class="fas fa-check-circle fa-2x"></i>
                                                                            <p class="mb-0 mt-2"><strong><?= $ttdKepsek ?></strong></p>
                                                                            <small>Sudah TTD</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="text-warning">
                                                                            <i class="fas fa-clock fa-2x"></i>
                                                                            <p class="mb-0 mt-2"><strong><?= $belumTtdKepsek ?></strong></p>
                                                                            <small>Belum TTD</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-3">
                                                                    <p class="mb-1"><strong>Total: <?= $totalSantri ?> Rapor</strong></p>
                                                                    <?php if ($allSignedKepsek): ?>
                                                                        <button type="button" class="btn btn-danger btn-sm btn-block btn-cancel-ttd-kepsek" 
                                                                                data-kelas="<?= $kelas->IdKelas ?>" 
                                                                                data-nama-kelas="<?= esc($kelas->NamaKelas) ?>"
                                                                                data-semester="<?= $semester ?>">
                                                                            <i class="fas fa-times-circle"></i> Batalkan TTD
                                                                        </button>
                                                                    <?php else: ?>
                                                                        <button type="button" class="btn btn-success btn-sm btn-block btn-ttd-bulk-kepsek" 
                                                                                data-kelas="<?= $kelas->IdKelas ?>" 
                                                                                data-nama-kelas="<?= esc($kelas->NamaKelas) ?>"
                                                                                data-semester="<?= $semester ?>">
                                                                            <i class="fas fa-signature"></i> Tanda Tangan Semua
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="col-md-4 mb-3">
                                                <div class="card border-primary shadow-sm h-100" style="background: linear-gradient(135deg, #e8f0ff 0%, #ffffff 100%);">
                                                    <div class="card-body text-center">
                                                        <h5 class="card-title">
                                                            <i class="fas fa-print text-primary"></i>
                                                            Cetak Semua Rapor
                                                            <br><small>Kelas <?= esc($kelas->NamaKelas) ?></small>
                                                        </h5>
                                                        <div class="mt-3">
                                                            <div class="text-center">
                                                                <i class="fas fa-file-pdf fa-3x text-primary mb-3"></i>
                                                                <p class="mb-1"><strong>Jumlah Rapor: <?= $totalSantri ?></strong></p>
                                                                <small class="text-muted">Semester <?= $semester ?></small>
                                                            </div>
                                                            <div class="mt-3">
                                                                <button type="button" class="btn btn-primary btn-sm btn-block btn-print-all" 
                                                                        data-kelas="<?= $kelas->IdKelas ?>" 
                                                                        data-semester="<?= $semester ?>">
                                                                    <i class="fas fa-print"></i> Cetak Semua Rapor
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4 mb-3">
                                                <div class="card border-secondary shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
                                                    <div class="card-body text-center">
                                                        <h5 class="card-title">
                                                            <i class="fas fa-tasks text-secondary"></i>
                                                            Aksi Catatan & Absensi
                                                            <br><small>Kelas <?= esc($kelas->NamaKelas) ?></small>
                                                        </h5>
                                                        <div class="mt-3">
                                                            <table class="table table-sm table-bordered mb-0">
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
                                                                            <i class="fas fa-sticky-note text-warning"></i> Catatan
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <strong class="text-success"><?= $jumlahCatatan ?></strong>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <strong class="text-warning"><?= $belumCatatan ?></strong>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-left">
                                                                            <i class="fas fa-calendar-check text-info"></i> Absensi
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <strong class="text-success"><?= $jumlahAbsensi ?></strong>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <strong class="text-warning"><?= $belumAbsensi ?></strong>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
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
                                                if (isset($perm['IdKelas']) && $perm['IdKelas'] == $kelas->IdKelas && 
                                                    isset($perm['NamaJabatan']) && $perm['NamaJabatan'] === 'Wali Kelas') {
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
                                                <th>NIS</th>
                                                <th>Total Nilai</th>
                                                <th>Nilai Rata-Rata</th>
                                                <th>Rangking</th>
                                                <th>Kelas</th>
                                                <th>Tahun Ajaran</th>
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
                                                            <div class="mb-2">
                                                                <button type="button" class="btn btn-warning btn-sm btn-print-pdf" data-id="<?= $nilaiDetail->IdSantri ?>" data-semester="<?= $semester ?>">
                                                                    <i class="fas fa-print"></i> Cetak Rapor
                                                                </button>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input checkbox-absensi"
                                                                    type="checkbox"
                                                                    data-id="<?= $nilaiDetail->IdSantri ?>"
                                                                    data-semester="<?= $semester ?>"
                                                                    data-kelas="<?= $nilaiDetail->IdKelas ?>"
                                                                    id="absensi-<?= $nilaiDetail->IdSantri ?>-<?= $semester ?>"
                                                                    style="cursor: pointer;">
                                                                <label class="form-check-label" for="absensi-<?= $nilaiDetail->IdSantri ?>-<?= $semester ?>" style="cursor: pointer;">
                                                                    Absensi
                                                                </label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input checkbox-catatan"
                                                                    type="checkbox"
                                                                    data-id="<?= $nilaiDetail->IdSantri ?>"
                                                                    data-semester="<?= $semester ?>"
                                                                    data-kelas="<?= $nilaiDetail->IdKelas ?>"
                                                                    id="catatan-<?= $nilaiDetail->IdSantri ?>-<?= $semester ?>"
                                                                    style="cursor: pointer;">
                                                                <label class="form-check-label" for="catatan-<?= $nilaiDetail->IdSantri ?>-<?= $semester ?>" style="cursor: pointer;">
                                                                    Catatan
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td><?= $nilaiDetail->NamaSantri ?></td>
                                                        <td><?= $nilaiDetail->IdSantri ?></td>
                                                        <td><?= $nilaiDetail->TotalNilai ?></td>
                                                        <td><?= $nilaiDetail->NilaiRataRata ?></td>
                                                        <td><?= $nilaiDetail->Rangking ?></td>
                                                        <td><?= $nilaiDetail->NamaKelas ?></td>
                                                        <td><?= $nilaiDetail->IdTahunAjaran ?></td>
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
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable untuk setiap kelas
        <?php foreach ($dataKelas as $kelasId => $IdKelas): ?>
            initializeDataTableUmum("#tableSantri-<?= $kelasId ?>", true, true);
        <?php endforeach; ?>

        // Pastikan event handler terpasang setelah DataTable selesai
        setTimeout(function() {
            console.log('Setting up checkbox event handlers');
        }, 500);

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
            const printWindow = window.open(`<?= base_url('backend/rapor/printPdf') ?>/${IdSantri}/${semester}`, '_blank');
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

        $(document).on('click', '.btn-print-all', function() {
            const kelasId = $(this).data('kelas');
            const semester = $(this).data('semester');

            // Tampilkan loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang menggabungkan rapor, mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Panggil endpoint untuk menggabungkan PDF
            const printWindow = window.open(`<?= base_url('backend/rapor/printPdfBulk') ?>/${kelasId}/${semester}`, '_blank');

            // Tutup loading setelah 2 detik (memberikan waktu untuk membuka PDF)
            setTimeout(() => {
                Swal.close();
                if (printWindow) {
                    printWindow.focus();
                }
            }, 2000);
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

        // Handle save absensi
        $('#btnSaveAbsensi').on('click', function() {
            // Hitung ulang jumlah tidak masuk sebelum menyimpan
            hitungJumlahTidakMasuk();

            const formData = $('#formAbsensi').serialize();

            $.ajax({
                url: '<?= base_url("backend/rapor/saveAbsensi") ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#modalAbsensi').modal('hide');
                            // Update checkbox status
                            const IdSantri = $('#absensiIdSantri').val();
                            const semester = $('#absensiSemester').val();
                            const isChecked = $('#showAbsensi').is(':checked');
                            $(`#absensi-${IdSantri}-${semester}`).prop('checked', isChecked);
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
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#modalCatatan').modal('hide');
                            // Update checkbox status
                            const IdSantri = $('#catatanIdSantri').val();
                            const semester = $('#catatanSemester').val();
                            const isChecked = $('#showCatatan').is(':checked');
                            $(`#catatan-${IdSantri}-${semester}`).prop('checked', isChecked);
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
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="ShowAbsensi" id="showAbsensi">
                            <label class="form-check-label" for="showAbsensi">
                                Tampilkan Absensi di Rapor
                            </label>
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
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="ShowCatatan" id="showCatatan">
                            <label class="form-check-label" for="showCatatan">
                                Tampilkan Catatan di Rapor
                            </label>
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