<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header p-2">
                    <h3 class="card-title">Data Nilai</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($kelasData)): ?>
                        <div class="alert alert-info">
                            <p class="mb-0">Belum ada data nilai untuk santri ini.</p>
                        </div>
                    <?php else: ?>
                        <!-- Tab Kelas (Level Pertama) -->
                        <ul class="nav nav-tabs" id="kelasTabs" role="tablist">
                            <?php
                            $firstTab = true;
                            $tabIndex = 0;
                            foreach ($kelasData as $key => $kelas):
                                $tabId = 'kelas_' . $key;
                            ?>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link <?= $firstTab ? 'active' : '' ?>"
                                        id="<?= $tabId ?>-tab"
                                        data-toggle="tab"
                                        href="#<?= $tabId ?>"
                                        role="tab"
                                        aria-controls="<?= $tabId ?>"
                                        aria-selected="<?= $firstTab ? 'true' : 'false' ?>">
                                        <?= esc($kelas['NamaKelas']) ?>
                                    </a>
                                </li>
                            <?php
                                $firstTab = false;
                                $tabIndex++;
                            endforeach;
                            ?>
                        </ul>

                        <!-- Tab Content untuk setiap kelas -->
                        <div class="tab-content" id="kelasTabContent">
                            <?php
                            $firstTab = true;
                            foreach ($kelasData as $key => $kelas):
                                $tabId = 'kelas_' . $key;
                                $ganjilId = 'ganjil_' . $key;
                                $genapId = 'genap_' . $key;
                            ?>
                                <div class="tab-pane fade <?= $firstTab ? 'show active' : '' ?>"
                                    id="<?= $tabId ?>"
                                    role="tabpanel"
                                    aria-labelledby="<?= $tabId ?>-tab">

                                    <div class="card mt-3">
                                        <div class="card-header p-2">
                                            <h4 class="card-title mb-0">
                                                <?= esc($kelas['NamaKelas']) ?> - Tahun Ajaran <?= esc($kelas['TahunAjaranDisplay']) ?>
                                                <?php if (!empty($kelas['waliKelas'])): ?>
                                                    <small class="text-muted">(Wali Kelas: <?= esc($kelas['waliKelas']) ?>)</small>
                                                <?php endif; ?>
                                            </h4>
                                        </div>
                                        <div class="card-body">
                                            <!-- Tab Semester (Level Kedua) -->
                                            <?php
                                            $hideGanjil = $kelas['hideGanjil'] ?? false;
                                            $hideGenap = $kelas['hideGenap'] ?? false;

                                            // Tentukan tab mana yang aktif pertama kali
                                            // Jika Ganjil tidak di-hide, aktifkan Ganjil (kecuali jika Genap juga tidak di-hide, maka Ganjil tetap aktif sebagai default)
                                            // Jika Ganjil di-hide dan Genap tidak di-hide, aktifkan Genap
                                            $ganjilActive = !$hideGanjil;
                                            $genapActive = !$hideGenap && $hideGanjil;

                                            // Tentukan pesan informasi berdasarkan kondisi
                                            $infoMessage = '';
                                            $currentTahunAjaran = $IdTahunAjaran ?? session()->get('IdTahunAjaran');
                                            $isTahunAjaranSaatIni = ($kelas['IdTahunAjaran'] == $currentTahunAjaran);

                                            if ($isTahunAjaranSaatIni) {
                                                $currentMonth = (int)date('m');
                                                $semesterSaatIni = ($currentMonth >= 7) ? 'Ganjil' : 'Genap';

                                                if ($hideGanjil && $hideGenap) {
                                                    $infoMessage = 'Nilai semester Ganjil dan Genap untuk tahun ajaran <strong>' . esc($kelas['TahunAjaranDisplay']) . '</strong> (kelas ' . esc($kelas['NamaKelas']) . ') belum dapat ditampilkan karena semester <strong>' . $semesterSaatIni . '</strong> saat ini masih berlangsung. Nilai akan ditampilkan setelah semester selesai dan nilai telah diinput oleh guru.';
                                                } elseif ($hideGenap) {
                                                    $infoMessage = 'Nilai semester Genap untuk tahun ajaran <strong>' . esc($kelas['TahunAjaranDisplay']) . '</strong> (kelas ' . esc($kelas['NamaKelas']) . ') belum dapat ditampilkan karena semester <strong>Genap</strong> saat ini masih berlangsung. Nilai akan ditampilkan setelah semester selesai dan nilai telah diinput oleh guru.';
                                                }
                                            }
                                            ?>

                                            <?php if (!empty($infoMessage)): ?>
                                                <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                                                    <h5 class="alert-heading">
                                                        <i class="fas fa-exclamation-triangle"></i> Informasi Nilai
                                                    </h5>
                                                    <hr>
                                                    <p class="mb-0"><?= $infoMessage ?></p>
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                            <?php endif; ?>

                                            <ul class="nav nav-pills mb-3" id="semesterTabs_<?= $key ?>" role="tablist">
                                                <?php if (!$hideGanjil): ?>
                                                    <li class="nav-item" role="presentation">
                                                        <a class="nav-link <?= $ganjilActive ? 'active' : '' ?>"
                                                            id="<?= $ganjilId ?>-tab"
                                                            data-toggle="tab"
                                                            href="#<?= $ganjilId ?>"
                                                            role="tab"
                                                            aria-controls="<?= $ganjilId ?>"
                                                            aria-selected="<?= $ganjilActive ? 'true' : 'false' ?>">
                                                            Semester Ganjil
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if (!$hideGenap): ?>
                                                    <li class="nav-item" role="presentation">
                                                        <a class="nav-link <?= $genapActive ? 'active' : '' ?>"
                                                            id="<?= $genapId ?>-tab"
                                                            data-toggle="tab"
                                                            href="#<?= $genapId ?>"
                                                            role="tab"
                                                            aria-controls="<?= $genapId ?>"
                                                            aria-selected="<?= $genapActive ? 'true' : 'false' ?>">
                                                            Semester Genap
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>

                                            <!-- Tab Content untuk Semester -->
                                            <div class="tab-content" id="semesterTabContent_<?= $key ?>">
                                                <!-- Tab Semester Ganjil -->
                                                <?php if (!$hideGanjil): ?>
                                                    <div class="tab-pane fade <?= $ganjilActive ? 'show active' : '' ?>"
                                                        id="<?= $ganjilId ?>"
                                                        role="tabpanel"
                                                        aria-labelledby="<?= $ganjilId ?>-tab">
                                                        <!-- Statistik Card -->
                                                        <?php
                                                        $statistik = $kelas['statistikGanjil'] ?? ['naik' => 0, 'turun' => 0, 'sama' => 0, 'baru' => 0, 'total' => 0];
                                                        ?>
                                                        <div class="row mb-3">
                                                            <div class="col-md-3">
                                                                <div class="info-box clickable-filter"
                                                                    data-filter="naik"
                                                                    data-table="table_ganjil_<?= $key ?>"
                                                                    style="cursor: pointer;">
                                                                    <span class="info-box-icon bg-success"><i class="fas fa-arrow-up"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Nilai Naik</span>
                                                                        <span class="info-box-number"><?= $statistik['naik'] ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="info-box clickable-filter"
                                                                    data-filter="turun"
                                                                    data-table="table_ganjil_<?= $key ?>"
                                                                    style="cursor: pointer;">
                                                                    <span class="info-box-icon bg-danger"><i class="fas fa-arrow-down"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Nilai Turun</span>
                                                                        <span class="info-box-number"><?= $statistik['turun'] ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="info-box clickable-filter"
                                                                    data-filter="sama"
                                                                    data-table="table_ganjil_<?= $key ?>"
                                                                    style="cursor: pointer;">
                                                                    <span class="info-box-icon bg-warning"><i class="fas fa-equals"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Nilai Sama</span>
                                                                        <span class="info-box-number"><?= $statistik['sama'] ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="info-box clickable-filter"
                                                                    data-filter="baru"
                                                                    data-table="table_ganjil_<?= $key ?>"
                                                                    style="cursor: pointer;">
                                                                    <span class="info-box-icon bg-info"><i class="fas fa-star"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Materi Baru</span>
                                                                        <span class="info-box-number"><?= $statistik['baru'] ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h3 class="card-title">Data Nilai Santri Detail - Semester Ganjil</h3>
                                                                <div class="card-tools">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-secondary btn-refresh-filter"
                                                                        data-table="table_ganjil_<?= $key ?>"
                                                                        title="Tampilkan Semua Data">
                                                                        <i class="fas fa-sync-alt"></i> Refresh
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <table id="table_ganjil_<?= $key ?>" class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Id Materi</th>
                                                                            <th>Nama Materi</th>
                                                                            <th>Kategori</th>
                                                                            <th>Nilai</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        $MainDataNilaiGanjil = is_array($kelas['nilaiGanjil'] ?? [])
                                                                            ? ($kelas['nilaiGanjil'] ?? [])
                                                                            : (is_object($kelas['nilaiGanjil'] ?? null) && method_exists($kelas['nilaiGanjil'], 'getResult')
                                                                                ? $kelas['nilaiGanjil']->getResult()
                                                                                : []);
                                                                        if (empty($MainDataNilaiGanjil)): ?>
                                                                            <tr>
                                                                                <td colspan="4" class="text-center">Belum ada data nilai untuk Semester Ganjil</td>
                                                                            </tr>
                                                                        <?php else: ?>
                                                                            <?php foreach ($MainDataNilaiGanjil as $DataNilai) : ?>
                                                                                <?php
                                                                                $nilai = is_object($DataNilai) ? $DataNilai->Nilai : ($DataNilai['Nilai'] ?? '');
                                                                                $status = is_object($DataNilai) ? ($DataNilai->statusNilai ?? 'baru') : ($DataNilai['statusNilai'] ?? 'baru');

                                                                                // Tentukan icon berdasarkan status
                                                                                $icon = '';
                                                                                $iconColor = '';
                                                                                $iconTitle = '';
                                                                                switch ($status) {
                                                                                    case 'naik':
                                                                                        $icon = 'fa-arrow-up';
                                                                                        $iconColor = 'text-success';
                                                                                        $iconTitle = 'Nilai naik dari semester sebelumnya';
                                                                                        break;
                                                                                    case 'turun':
                                                                                        $icon = 'fa-arrow-down';
                                                                                        $iconColor = 'text-danger';
                                                                                        $iconTitle = 'Nilai turun dari semester sebelumnya';
                                                                                        break;
                                                                                    case 'sama':
                                                                                        $icon = 'fa-equals';
                                                                                        $iconColor = 'text-warning';
                                                                                        $iconTitle = 'Nilai sama dengan semester sebelumnya';
                                                                                        break;
                                                                                    default:
                                                                                        $icon = 'fa-star';
                                                                                        $iconColor = 'text-info';
                                                                                        $iconTitle = 'Materi baru (belum ada di semester sebelumnya)';
                                                                                        break;
                                                                                }
                                                                                ?>
                                                                                <tr data-status="<?= esc($status) ?>">
                                                                                    <td><?php echo is_object($DataNilai) ? $DataNilai->IdMateri : ($DataNilai['IdMateri'] ?? ''); ?></td>
                                                                                    <td><?php echo is_object($DataNilai) ? $DataNilai->NamaMateri : ($DataNilai['NamaMateri'] ?? ''); ?></td>
                                                                                    <td><?php echo is_object($DataNilai) ? $DataNilai->Kategori : ($DataNilai['Kategori'] ?? ''); ?></td>
                                                                                    <td>
                                                                                        <?php echo esc($nilai); ?>
                                                                                        <?php if (!empty($icon)): ?>
                                                                                            <i class="fas <?= $icon ?> <?= $iconColor ?> ml-2"
                                                                                                data-toggle="tooltip"
                                                                                                data-placement="top"
                                                                                                title="<?= esc($iconTitle) ?>"></i>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php endforeach ?>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr>
                                                                            <th>Id Materi</th>
                                                                            <th>Nama Materi</th>
                                                                            <th>Kategori</th>
                                                                            <th>Nilai</th>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Tab Semester Genap -->
                                                <?php if (!$hideGenap): ?>
                                                    <div class="tab-pane fade <?= $genapActive ? 'show active' : '' ?>"
                                                        id="<?= $genapId ?>"
                                                        role="tabpanel"
                                                        aria-labelledby="<?= $genapId ?>-tab">
                                                        <!-- Statistik Card -->
                                                        <?php
                                                        $statistik = $kelas['statistikGenap'] ?? ['naik' => 0, 'turun' => 0, 'sama' => 0, 'baru' => 0, 'total' => 0];
                                                        ?>
                                                        <div class="row mb-3">
                                                            <div class="col-md-3">
                                                                <div class="info-box clickable-filter"
                                                                    data-filter="naik"
                                                                    data-table="table_genap_<?= $key ?>"
                                                                    style="cursor: pointer;">
                                                                    <span class="info-box-icon bg-success"><i class="fas fa-arrow-up"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Nilai Naik</span>
                                                                        <span class="info-box-number"><?= $statistik['naik'] ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="info-box clickable-filter"
                                                                    data-filter="turun"
                                                                    data-table="table_genap_<?= $key ?>"
                                                                    style="cursor: pointer;">
                                                                    <span class="info-box-icon bg-danger"><i class="fas fa-arrow-down"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Nilai Turun</span>
                                                                        <span class="info-box-number"><?= $statistik['turun'] ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="info-box clickable-filter"
                                                                    data-filter="sama"
                                                                    data-table="table_genap_<?= $key ?>"
                                                                    style="cursor: pointer;">
                                                                    <span class="info-box-icon bg-warning"><i class="fas fa-equals"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Nilai Sama</span>
                                                                        <span class="info-box-number"><?= $statistik['sama'] ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="info-box clickable-filter"
                                                                    data-filter="baru"
                                                                    data-table="table_genap_<?= $key ?>"
                                                                    style="cursor: pointer;">
                                                                    <span class="info-box-icon bg-info"><i class="fas fa-star"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Materi Baru</span>
                                                                        <span class="info-box-number"><?= $statistik['baru'] ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h3 class="card-title">Data Nilai Santri Detail - Semester Genap</h3>
                                                                <div class="card-tools">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-secondary btn-refresh-filter"
                                                                        data-table="table_genap_<?= $key ?>"
                                                                        title="Tampilkan Semua Data">
                                                                        <i class="fas fa-sync-alt"></i> Refresh
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <table id="table_genap_<?= $key ?>" class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Id Materi</th>
                                                                            <th>Nama Materi</th>
                                                                            <th>Kategori</th>
                                                                            <th>Nilai</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        $MainDataNilaiGenap = is_array($kelas['nilaiGenap'] ?? [])
                                                                            ? ($kelas['nilaiGenap'] ?? [])
                                                                            : (is_object($kelas['nilaiGenap'] ?? null) && method_exists($kelas['nilaiGenap'], 'getResult')
                                                                                ? $kelas['nilaiGenap']->getResult()
                                                                                : []);
                                                                        if (empty($MainDataNilaiGenap)): ?>
                                                                            <tr>
                                                                                <td colspan="4" class="text-center">Belum ada data nilai untuk Semester Genap</td>
                                                                            </tr>
                                                                        <?php else: ?>
                                                                            <?php foreach ($MainDataNilaiGenap as $DataNilai) : ?>
                                                                                <?php
                                                                                $nilai = is_object($DataNilai) ? $DataNilai->Nilai : ($DataNilai['Nilai'] ?? '');
                                                                                $status = is_object($DataNilai) ? ($DataNilai->statusNilai ?? 'baru') : ($DataNilai['statusNilai'] ?? 'baru');

                                                                                // Tentukan icon berdasarkan status
                                                                                $icon = '';
                                                                                $iconColor = '';
                                                                                $iconTitle = '';
                                                                                switch ($status) {
                                                                                    case 'naik':
                                                                                        $icon = 'fa-arrow-up';
                                                                                        $iconColor = 'text-success';
                                                                                        $iconTitle = 'Nilai naik dari semester sebelumnya';
                                                                                        break;
                                                                                    case 'turun':
                                                                                        $icon = 'fa-arrow-down';
                                                                                        $iconColor = 'text-danger';
                                                                                        $iconTitle = 'Nilai turun dari semester sebelumnya';
                                                                                        break;
                                                                                    case 'sama':
                                                                                        $icon = 'fa-equals';
                                                                                        $iconColor = 'text-warning';
                                                                                        $iconTitle = 'Nilai sama dengan semester sebelumnya';
                                                                                        break;
                                                                                    default:
                                                                                        $icon = 'fa-star';
                                                                                        $iconColor = 'text-info';
                                                                                        $iconTitle = 'Materi baru (belum ada di semester sebelumnya)';
                                                                                        break;
                                                                                }
                                                                                ?>
                                                                                <tr data-status="<?= esc($status) ?>">
                                                                                    <td><?php echo is_object($DataNilai) ? $DataNilai->IdMateri : ($DataNilai['IdMateri'] ?? ''); ?></td>
                                                                                    <td><?php echo is_object($DataNilai) ? $DataNilai->NamaMateri : ($DataNilai['NamaMateri'] ?? ''); ?></td>
                                                                                    <td><?php echo is_object($DataNilai) ? $DataNilai->Kategori : ($DataNilai['Kategori'] ?? ''); ?></td>
                                                                                    <td>
                                                                                        <?php echo esc($nilai); ?>
                                                                                        <?php if (!empty($icon)): ?>
                                                                                            <i class="fas <?= $icon ?> <?= $iconColor ?> ml-2"
                                                                                                data-toggle="tooltip"
                                                                                                data-placement="top"
                                                                                                title="<?= esc($iconTitle) ?>"></i>
                                                                                        <?php endif; ?>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php endforeach ?>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr>
                                                                            <th>Id Materi</th>
                                                                            <th>Nama Materi</th>
                                                                            <th>Kategori</th>
                                                                            <th>Nilai</th>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                $firstTab = false;
                            endforeach;
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<?php if (!empty($kelasData)): ?>
    <script>
        $(document).ready(function() {
            // Object untuk menyimpan instance DataTable
            var dataTables = {};

            // Function untuk menginisialisasi DataTable dengan pengecekan
            function initDataTable(selector) {
                // Cek apakah DataTable sudah ada
                if ($.fn.DataTable.isDataTable(selector)) {
                    // Destroy DataTable yang sudah ada
                    $(selector).DataTable().destroy();
                }

                // Inisialisasi DataTable baru hanya jika tabel memiliki data
                if ($(selector + ' tbody tr').length > 0) {
                    initializeDataTableUmum(selector, true, true);
                    // Simpan instance DataTable setelah inisialisasi
                    dataTables[selector] = $(selector).DataTable();
                }
            }

            // Function untuk menginisialisasi DataTable pada tab yang aktif
            function initDataTableForActiveTab() {
                // Inisialisasi DataTable untuk tab kelas aktif
                var activeKelasTab = $('#kelasTabs .nav-link.active');
                if (activeKelasTab.length > 0) {
                    var activeKelasId = activeKelasTab.attr('href'); // e.g., #kelas_123_20232024
                    var kelasKey = activeKelasId.replace('#kelas_', '');

                    // Inisialisasi DataTable untuk tab semester aktif di dalam tab kelas aktif
                    var activeSemesterTab = $(activeKelasId + ' .nav-pills .nav-link.active');
                    if (activeSemesterTab.length > 0) {
                        var activeSemesterId = activeSemesterTab.attr('href'); // e.g., #ganjil_123_20232024

                        // Tentukan selector tabel berdasarkan semester
                        var tableSelector = '';
                        if (activeSemesterId.includes('ganjil')) {
                            tableSelector = '#table_ganjil_' + kelasKey;
                        } else if (activeSemesterId.includes('genap')) {
                            tableSelector = '#table_genap_' + kelasKey;
                        }

                        if (tableSelector) {
                            initDataTable(tableSelector);
                        }
                    }
                }
            }

            // Inisialisasi DataTable untuk tab pertama yang aktif saat halaman dimuat
            setTimeout(function() {
                initDataTableForActiveTab();
                // Inisialisasi tooltip setelah DataTable di-render
                setTimeout(function() {
                    $('[data-toggle="tooltip"]').tooltip();
                }, 200);
            }, 300);

            // Event handler untuk tab kelas
            $('#kelasTabs a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var target = $(e.target).attr("href"); // e.g., #kelas_123_20232024
                var kelasKey = target.replace('#kelas_', '');

                // Tunggu sedikit untuk memastikan tab content sudah ter-render
                setTimeout(function() {
                    // Inisialisasi DataTable untuk tab semester aktif di dalam tab kelas yang baru
                    var activeSemesterTab = $(target + ' .nav-pills .nav-link.active');
                    if (activeSemesterTab.length > 0) {
                        var activeSemesterId = activeSemesterTab.attr('href');

                        var tableSelector = '';
                        if (activeSemesterId.includes('ganjil')) {
                            tableSelector = '#table_ganjil_' + kelasKey;
                        } else if (activeSemesterId.includes('genap')) {
                            tableSelector = '#table_genap_' + kelasKey;
                        }

                        if (tableSelector) {
                            initDataTable(tableSelector);
                            // Inisialisasi tooltip setelah DataTable di-render
                            setTimeout(function() {
                                $('[data-toggle="tooltip"]').tooltip();
                            }, 200);
                        }
                    }
                }, 100);
            });

            // Event handler untuk tab semester (dalam setiap tab kelas)
            <?php foreach ($kelasData as $key => $kelas): ?>
                $('#semesterTabs_<?= $key ?> a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                    var target = $(e.target).attr("href");
                    var tableSelector = '';

                    if (target.includes('ganjil')) {
                        tableSelector = '#table_ganjil_<?= $key ?>';
                    } else if (target.includes('genap')) {
                        tableSelector = '#table_genap_<?= $key ?>';
                    }

                    if (tableSelector) {
                        setTimeout(function() {
                            initDataTable(tableSelector);
                            // Inisialisasi tooltip setelah DataTable di-render
                            setTimeout(function() {
                                $('[data-toggle="tooltip"]').tooltip();
                            }, 200);
                        }, 100);
                    }
                });
            <?php endforeach; ?>

            // Inisialisasi tooltip saat halaman dimuat
            setTimeout(function() {
                $('[data-toggle="tooltip"]').tooltip();
            }, 500);

            // Object untuk menyimpan custom filter function per tabel
            var customFilters = {};

            // Fungsi untuk mendapatkan instance DataTable
            function getDataTableInstance(tableId) {
                var selector = '#' + tableId;
                if ($.fn.DataTable.isDataTable(selector)) {
                    return $(selector).DataTable();
                }
                // Coba dari dataTables object
                if (dataTables[selector]) {
                    return dataTables[selector];
                }
                return null;
            }

            // Fungsi untuk menerapkan filter berdasarkan status
            function applyStatusFilter(tableId, status) {
                var selector = '#' + tableId;
                var table = getDataTableInstance(tableId);

                if (!table) {
                    console.warn('Table not found or not initialized: ' + tableId);
                    // Coba inisialisasi jika tabel ada tapi belum terinisialisasi
                    if ($(selector).length > 0 && $(selector + ' tbody tr').length > 0) {
                        console.log('Initializing table: ' + tableId);
                        initDataTable(selector);
                        // Coba lagi setelah delay
                        setTimeout(function() {
                            applyStatusFilter(tableId, status);
                        }, 300);
                    } else {
                        // Coba lagi setelah delay
                        setTimeout(function() {
                            applyStatusFilter(tableId, status);
                        }, 500);
                    }
                    return;
                }

                // Hapus filter sebelumnya untuk tabel ini jika ada
                if (customFilters[tableId]) {
                    // Cari dan hapus filter function dari array
                    var index = $.fn.dataTable.ext.search.indexOf(customFilters[tableId]);
                    if (index !== -1) {
                        $.fn.dataTable.ext.search.splice(index, 1);
                    }
                    customFilters[tableId] = null;
                }

                // Buat custom filter function dengan closure untuk tableId
                var filterFunction = (function(tId, stat) {
                    return function(settings, data, dataIndex) {
                        // Cek apakah ini tabel yang benar dengan membandingkan ID
                        var currentTableId = $(settings.nTable).attr('id');
                        if (currentTableId !== tId) {
                            return true; // Biarkan filter lain bekerja untuk tabel lain
                        }

                        // Ambil row node menggunakan API dari settings
                        var api = new $.fn.dataTable.Api(settings);
                        var row = api.row(dataIndex).node();
                        if (!row) {
                            return false;
                        }

                        // Ambil status dari attribute data-status
                        var rowStatus = $(row).attr('data-status');
                        if (!rowStatus) {
                            // Jika tidak ada data-status, coba ambil dari data array (kolom ke-4 adalah nilai)
                            // Tapi lebih baik return false karena seharusnya semua row punya data-status
                            return false;
                        }

                        return rowStatus === stat;
                    };
                })(tableId, status);

                // Simpan filter function
                customFilters[tableId] = filterFunction;

                // Tambahkan filter ke DataTable
                $.fn.dataTable.ext.search.push(filterFunction);

                // Redraw tabel
                table.draw();

                // Update visual card yang aktif
                $('.clickable-filter[data-table="' + tableId + '"]').removeClass('filter-active');
                $('.clickable-filter[data-table="' + tableId + '"][data-filter="' + status + '"]').addClass('filter-active');
            }

            // Fungsi untuk menghapus filter (refresh)
            function removeStatusFilter(tableId) {
                var table = getDataTableInstance(tableId);

                if (!table) {
                    return;
                }

                // Hapus filter jika ada
                if (customFilters[tableId]) {
                    // Cari dan hapus filter function dari array
                    var index = $.fn.dataTable.ext.search.indexOf(customFilters[tableId]);
                    if (index !== -1) {
                        $.fn.dataTable.ext.search.splice(index, 1);
                    }
                    customFilters[tableId] = null;

                    // Redraw tabel
                    table.draw();

                    // Hapus visual aktif dari semua card
                    $('.clickable-filter[data-table="' + tableId + '"]').removeClass('filter-active');
                }
            }

            // Event handler untuk click pada card statistik
            $(document).on('click', '.clickable-filter', function(e) {
                e.preventDefault();
                var filter = $(this).data('filter');
                var tableId = $(this).data('table');

                console.log('Card clicked - Filter:', filter, 'Table:', tableId);

                if (filter && tableId) {
                    applyStatusFilter(tableId, filter);
                }
            });

            // Event handler untuk button refresh
            $(document).on('click', '.btn-refresh-filter', function(e) {
                e.preventDefault();
                var tableId = $(this).data('table');

                console.log('Refresh clicked - Table:', tableId);

                if (tableId) {
                    removeStatusFilter(tableId);
                }
            });

            // Tambahkan CSS untuk card yang aktif
            if (!$('#filterCardStyle').length) {
                $('head').append('<style id="filterCardStyle">.clickable-filter.filter-active { box-shadow: 0 0 10px rgba(0,123,255,0.5); border: 2px solid #007bff; } .clickable-filter:hover { opacity: 0.8; transform: scale(1.02); transition: all 0.2s; }</style>');
            }
        });
    </script>
<?php endif; ?>
<?= $this->endSection(); ?>