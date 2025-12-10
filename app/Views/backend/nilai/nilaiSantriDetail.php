<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <?php echo session()->getFlashdata('pesan'); ?>

    <!-- Flash Notification Container -->
    <div id="flashNotificationContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;"></div>

    <!-- Card Informasi Alur Proses -->
    <div class="card card-info collapsed-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Panduan Alur Proses Input/Edit Nilai Santri
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
                            <strong>Lihat Status Nilai:</strong> Tabel menampilkan semua materi pelajaran dengan status nilai:
                            <ul class="mt-2">
                                <li>Border <span style="color: red;"><strong>merah</strong></span> - Nilai belum diisi (0)</li>
                                <li>Border <span style="color: green;"><strong>hijau</strong></span> - Nilai sudah diisi</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Filter Materi:</strong> Gunakan dropdown filter di samping search box untuk memfilter materi tertentu.
                            Anda dapat memilih beberapa materi sekaligus. Filter akan tersimpan otomatis di browser Anda.
                        </li>
                        <li class="mb-2">
                            <strong>Input/Edit Nilai:</strong> Klik tombol pada kolom "Aksi" untuk setiap materi:
                            <ul class="mt-2">
                                <li><span class="badge badge-primary"><i class="fas fa-plus"></i> Add</span> - Untuk menambah nilai baru (nilai masih 0)</li>
                                <li><span class="badge badge-warning"><i class="fas fa-edit"></i> Edit</span> - Untuk mengubah nilai yang sudah ada (Wali Kelas)</li>
                                <li><span class="badge badge-success"><i class="fas fa-eye"></i> View</span> - Hanya melihat nilai (Guru Kelas, nilai sudah diisi)</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Isi Nilai:</strong> Di dalam modal, isi nilai sesuai dengan format yang tersedia:
                            <ul class="mt-2">
                                <li><strong>Sistem Angka:</strong> Input nilai numerik (minimal dan maksimal sesuai setting)</li>
                                <li><strong>Sistem Huruf:</strong> Pilih nilai menggunakan radio button (A, B, C, D, dll sesuai setting)</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Simpan Nilai:</strong> Klik tombol <span class="badge badge-primary"><i class="fas fa-save"></i> Simpan</span>
                            untuk menyimpan nilai. Sistem akan otomatis memperbarui tampilan tabel setelah penyimpanan berhasil.
                        </li>
                    </ol>

                    <div class="alert alert-info mb-0">
                        <h5 class="alert-heading"><i class="fas fa-lightbulb"></i> Tips:</h5>
                        <ul class="mb-0">
                            <li>Filter materi akan <strong>tersimpan otomatis</strong> di browser, sehingga saat kembali ke halaman ini, filter terakhir akan tetap aktif.</li>
                            <li>Gunakan search box DataTable untuk mencari materi berdasarkan nama atau ID.</li>
                            <li>Data dapat diurutkan dengan mengklik header kolom tabel.</li>
                            <li>Jika Anda mengubah nilai di modal lalu menutup modal tanpa menyimpan, sistem akan memperingatkan Anda tentang perubahan yang tidak tersimpan.</li>
                            <li>Format nilai (angka atau huruf) tergantung pada <strong>setting kelas</strong> yang telah dikonfigurasi.</li>
                            <li>Tombol <strong>View</strong> hanya muncul untuk Guru Kelas pada nilai yang sudah diisi, dan tombol ini <strong>disabled</strong> (tidak dapat diklik).</li>
                            <li>Wali Kelas dapat mengedit nilai yang sudah diisi, sedangkan Guru Kelas hanya dapat melihat.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <?php
        // Extracting the first result from $dataNilai (assuming it has at least one result)
        if (!empty($nilai)) {
            $firstResult = $nilai[0];
            $IdSantri = htmlspecialchars($firstResult->IdSantri, ENT_QUOTES, 'UTF-8');
            $NamaSantri = htmlspecialchars($firstResult->NamaSantri, ENT_QUOTES, 'UTF-8');
            $Semester = htmlspecialchars($firstResult->Semester, ENT_QUOTES, 'UTF-8');
            $NamaKelas = htmlspecialchars($firstResult->NamaKelas, ENT_QUOTES, 'UTF-8');
            // Format the Tahun with "/"
            $Tahun = $firstResult->IdTahunAjaran;
            if (strlen($Tahun) == 8) {
                $Tahun = substr($Tahun, 0, 4) . '/' . substr($Tahun, 4, 4);
            } else {
                $Tahun = 'Invalid Year Format';
            }
        } else {
            // Default values or handle the case when $dataNilai is empty
            $NamaSantri = "";
            $Tahun = "";
            $Semester = "";
            $IdSantri = "";
            $NamaKelas = "";
        }
        ?>

        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    Data Nilai Santri <strong><?= $IdSantri . ' - ' . $NamaSantri ?></strong> Kelas <?= $NamaKelas ?> Tahun <?= $Tahun ?> Semester <?= $Semester ?>
                </h3>
                <a href="javascript:history.back()" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div> <!-- /.card-header -->
        <div class="card-body">
            <style>
                /* Style untuk filter Select2 di samping search box DataTable */
                .dataTables_wrapper .dataTables_filter {
                    float: right;
                    text-align: right;
                }

                .dataTables_wrapper .dataTables_filter label {
                    font-weight: normal;
                    white-space: nowrap;
                    text-align: left;
                    display: flex;
                    align-items: center;
                    gap: 0.5em;
                }

                .dataTables_wrapper .dataTables_filter #filterNamaMateri {
                    display: inline-block;
                    min-width: 300px;
                    margin-left: 10px;
                }

                /* Responsive untuk mobile view */
                @media (max-width: 768px) {
                    .dataTables_wrapper .dataTables_filter {
                        float: none;
                        text-align: left;
                        margin-bottom: 10px;
                    }

                    .dataTables_wrapper .dataTables_filter label {
                        flex-direction: column;
                        align-items: stretch;
                        width: 100%;
                        gap: 10px;
                    }

                    .dataTables_wrapper .dataTables_filter input[type="search"] {
                        width: 100% !important;
                        margin-bottom: 0;
                    }

                    .dataTables_wrapper .dataTables_filter #filterNamaMateri {
                        width: 100% !important;
                        min-width: 100% !important;
                        margin-left: 0 !important;
                        margin-top: 0;
                    }

                    /* Pastikan Select2 container juga full width di mobile */
                    .dataTables_wrapper .dataTables_filter .select2-container {
                        width: 100% !important;
                    }

                    /* Perbesar kolom input nilai di mobile */
                    #TabelNilaiPerSemester td:nth-child(2) {
                        min-width: 50px !important;
                        width: 50px !important;
                    }

                    .nilai-input-inline {
                        font-size: 18px !important;
                        padding: 12px 8px !important;
                        min-height: 48px !important;
                        text-align: center !important;
                    }

                    .nilai-input-inline.nilai-input-numeric {
                        font-size: 25px !important;
                        font-weight: 700 !important;
                    }

                    /* Styling button Edit, Add, dan View di mobile */
                    .btn-action-nilai {
                        display: flex !important;
                        flex-direction: column !important;
                        align-items: center !important;
                        justify-content: center !important;
                        width: 100% !important;
                        min-width: 100% !important;
                        padding: 10px 10px !important;
                        font-size: 14px !important;
                        gap: 2px !important;
                    }

                    .btn-action-nilai i {
                        font-size: 18px !important;
                        margin: 0 !important;
                        display: block !important;
                    }

                    .btn-action-nilai .btn-text {
                        margin: 0 !important;
                        display: block !important;
                        font-size: 15px !important;
                        line-height: 1 !important;
                    }
                }

                /* Responsive untuk tablet view */
                @media (max-width: 992px) and (min-width: 769px) {
                    .dataTables_wrapper .dataTables_filter #filterNamaMateri {
                        min-width: 250px;
                    }
                }
            </style>
            <table id="TabelNilaiPerSemester" class="table table-bordered table-striped">
                <thead>
                    <?php
                    $tableHeadersFooter = '<tr>';
                    $tableHeadersFooter .= '<th>Aksi</th>';
                    $tableHeadersFooter .= '
                        <th>Nilai</th>
                        <th>Materi</th>
                    </tr>';

                    echo $tableHeadersFooter
                    ?>

                </thead>
                <tbody>
                    <?php
                    foreach ($nilai as $DataNilai) : ?>
                        <tr>
                            <td>
                                <?php
                                $isNilaiPositive = $DataNilai->Nilai > 0;

                                // LOGIKA PERMISSION:
                                // 1. Wali Kelas untuk kelas ini: selalu bisa edit (tidak peduli nilai sudah ada atau belum)
                                // 2. Guru Pendamping untuk kelas ini: 
                                //    - Jika nilai kosong (0): bisa edit (tombol Add)
                                //    - Jika nilai sudah ada: hanya view (tombol View disabled)
                                // 3. Admin dan Operator: tidak bisa edit sama sekali (hanya view atau tidak akses)
                                // Catatan: Jika guru adalah Wali Kelas di kelas lain tapi pendamping di kelas ini,
                                //          maka di kelas ini dia hanya bisa edit jika nilai kosong

                                if ($canEditAll) {
                                    // Wali Kelas: bisa edit semua
                                    $btnClass = $isNilaiPositive ? 'btn-warning' : 'btn-primary';
                                    $faClass = $isNilaiPositive ? 'fa-edit' : 'fa-plus';
                                    $name = $isNilaiPositive ? 'Edit' : 'Add';
                                    $disabled = '';
                                } elseif ($isGuruPendamping) {
                                    // Guru Pendamping: hanya bisa edit jika nilai kosong
                                    if ($isNilaiPositive) {
                                        // Nilai sudah ada: hanya view (disabled)
                                        $btnClass = 'btn-success';
                                        $faClass = 'fa-eye';
                                        $name = 'View';
                                        $disabled = 'disabled';
                                    } else {
                                        // Nilai kosong: bisa edit
                                        $btnClass = 'btn-primary';
                                        $faClass = 'fa-plus';
                                        $name = 'Add';
                                        $disabled = '';
                                    }
                                } else {
                                    // Admin dan Operator: tidak bisa edit sama sekali (hanya view)
                                    $btnClass = 'btn-success';
                                    $faClass = 'fa-eye';
                                    $name = 'View';
                                    $disabled = 'disabled';
                                }
                                ?>
                                <button id="EditNilai-<?= $DataNilai->Id ?>" class="btn <?= $btnClass ?> btn-sm btn-action-nilai" onclick="showModalEditNilai('<?= $DataNilai->Id ?>')" <?= $disabled ?>>
                                    <i class="fas <?= $faClass ?>"></i>
                                    <span class="btn-text"><?= $name ?></span>
                                </button>
                            </td>
                            <td data-sort="<?= $DataNilai->Nilai ?>">
                                <?php
                                // Cek apakah kelas ini menggunakan alphabet
                                $alphabetSettings = getAlphabetKelasSettings($settingNilai, $DataNilai->IdKelas);
                                $isAlphabetKelas = $alphabetSettings['isAlphabetKelas'];
                                $SettingAlphabeticNilaiTransformed = $alphabetSettings['transformedNilai'];

                                // Tentukan apakah user bisa edit (sama seperti logic tombol)
                                $canEditNilai = $canEditAll || ($isGuruPendamping && $DataNilai->Nilai == 0);
                                ?>
                                <?php if ($isAlphabetKelas && $canEditNilai): ?>
                                    <!-- Dropdown untuk nilai alphabet -->
                                    <select name="Nilai-<?= $DataNilai->Id ?>" id="Nilai-<?= $DataNilai->Id ?>"
                                        class="form-control nilai-input-inline"
                                        data-id="<?= $DataNilai->Id ?>"
                                        data-materi="<?= htmlspecialchars($DataNilai->IdMateri . ' - ' . $DataNilai->NamaMateri, ENT_QUOTES, 'UTF-8') ?>"
                                        data-nilai-lama="<?= $DataNilai->Nilai ?>"
                                        data-is-alphabet="true"
                                        style="border: <?= $DataNilai->Nilai == 0 ? '2px solid red' : '2px solid green' ?>; cursor: pointer;">
                                        <option value="0" <?= $DataNilai->Nilai == 0 ? 'selected' : '' ?>>-- Pilih Nilai --</option>
                                        <?php foreach ($SettingAlphabeticNilaiTransformed as $nilaiItem): ?>
                                            <option value="<?= $nilaiItem['Value'] ?>" <?= $DataNilai->Nilai == $nilaiItem['Value'] ? 'selected' : '' ?>>
                                                <?= $nilaiItem['Label'] ?> (<?= $nilaiItem['Value'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <!-- Input number untuk nilai numerik -->
                                    <input type="text"
                                        name="Nilai-<?= $DataNilai->Id ?>"
                                        id="Nilai-<?= $DataNilai->Id ?>"
                                        class="form-control nilai-input-inline nilai-input-numeric <?= $canEditNilai ? '' : 'readonly-disabled' ?>"
                                        value="<?php echo $DataNilai->Nilai; ?>"
                                        <?= $canEditNilai ? '' : 'readonly' ?>
                                        data-id="<?= $DataNilai->Id ?>"
                                        data-materi="<?= htmlspecialchars($DataNilai->IdMateri . ' - ' . $DataNilai->NamaMateri, ENT_QUOTES, 'UTF-8') ?>"
                                        data-nilai-lama="<?= $DataNilai->Nilai ?>"
                                        data-nilai-min="<?= $settingNilai->NilaiMin ?? 0 ?>"
                                        data-nilai-max="<?= $settingNilai->NilaiMax ?? 100 ?>"
                                        data-is-alphabet="false"
                                        maxlength="2"
                                        pattern="[0-9]{1,2}"
                                        inputmode="numeric"
                                        placeholder="Ketik nilai"
                                        style="border: <?= $DataNilai->Nilai == 0 ? '2px solid red' : '2px solid green' ?>; <?= $canEditNilai ? '' : 'cursor: not-allowed; background-color: #e9ecef;' ?>" />
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?php echo htmlspecialchars($DataNilai->NamaMateri, ENT_QUOTES, 'UTF-8'); ?></div>
                                <div style="font-size: 0.875rem; color: #6c757d; margin-top: 4px;">
                                    <small><?php echo htmlspecialchars($DataNilai->Kategori . ' - ' . $DataNilai->IdMateri, ENT_QUOTES, 'UTF-8'); ?></small>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <?= $tableHeadersFooter ?>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>

<!-- Modal Edit Data-->
<?php
foreach ($nilai as $DataNilai) : ?>
    <div class="modal fade" id="EditNilai<?= $DataNilai->Id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content ">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Update Nilai </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('backend/nilai/update') ?>" method="POST">
                        <input type="hidden" name="Id" value=<?= $DataNilai->Id ?>>
                        <div class="form-group">
                            <label for="FormProfilTpq">Kategori</label>
                            <span class="form-control" id="FormProfilTpq"><?= $DataNilai->Kategori ?></span>
                        </div>

                        <div class="form-group">
                            <label for="FormProfilTpq">Materi</label>
                            <span class="form-control" id="FormProfilTpq"><?= $DataNilai->IdMateri . ' - ' . $DataNilai->NamaMateri ?></span>
                        </div>
                        <?= $isAlphabetKelas = false; ?>
                        <?php
                        $alphabetSettings = getAlphabetKelasSettings($settingNilai, $DataNilai->IdKelas);
                        $isAlphabetKelas = $alphabetSettings['isAlphabetKelas'];
                        $SettingAlphabeticNilaiTransformed = $alphabetSettings['transformedNilai'];

                        if ($isAlphabetKelas) {
                        ?>
                            <div class="form-group">
                                <label for="FormProfilTpq">Nilai</label>
                                <div>
                                    <?php foreach ($SettingAlphabeticNilaiTransformed as $nilaiItem) : ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input custom-radio" type="radio" name="NilaiRadio" id="nilai<?= $nilaiItem['Label'] ?>-<?= $DataNilai->Id ?>" value="<?= $nilaiItem['Value'] ?>" <?= $DataNilai->Nilai == $nilaiItem['Value'] ? 'checked' : '' ?> required>
                                            <label class="form-check-label" for="nilai<?= $nilaiItem['Label'] ?>-<?= $DataNilai->Id ?>"><?= $nilaiItem['Label'] ?> (<?= $nilaiItem['Value'] ?>)</label>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            </div>

                            <style>
                                .custom-radio {
                                    width: 24px;
                                    height: 24px;
                                    margin-top: 0.3rem;
                                }

                                .form-check {
                                    margin-bottom: 1rem;
                                }

                                .form-check-label {
                                    font-size: 1.1rem;
                                    margin-left: 0.5rem;
                                    padding-top: 0.2rem;
                                }
                            </style>
                        <?php } else { ?>
                            <div class="form-group">
                                <label for="FormProfilTpq">Nilai</label>
                                <input type="number" name="Nilai" class="form-control" id="NilaiEditModal-<?= $DataNilai->Id ?>" required
                                    placeholder="<?= $DataNilai->Nilai > 0 ? '' : 'Ketik Nilai' ?>" value="<?= $DataNilai->Nilai > 0 ? $DataNilai->Nilai : '' ?>"
                                    min="<?= $settingNilai->NilaiMin ?? 0 ?>" max="<?= $settingNilai->NilaiMax ?? 100 ?>"
                                    oninvalid="this.setCustomValidity('Nilai harus antara <?= $settingNilai->NilaiMin ?? 0 ?> dan <?= $settingNilai->NilaiMax ?? 100 ?>')"
                                    oninput="this.setCustomValidity('')"
                                    autofocus>
                            </div>
                        <?php } ?>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="tutupModal-<?= $DataNilai->Id ?>">
                                <i class="fas fa-times"></i> Keluar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
<?php endforeach ?>
<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    // Key untuk localStorage
    const filterStorageKey = 'nilaiSantriDetail_filterNamaMateri';

    // Flag untuk mencegah double save saat load filter
    var isLoadingFilter = false;

    // Daftar materi dari PHP (hanya NamaMateri karena format di tabel sudah berubah)
    var daftarMateri = [
        <?php
        // Kumpulkan daftar nama materi yang unik (hanya NamaMateri, tanpa IdMateri)
        $uniqueMateri = [];
        foreach ($nilai as $DataNilai) {
            $materiKey = $DataNilai->NamaMateri; // Hanya NamaMateri, sesuai format di tabel
            if (!in_array($materiKey, $uniqueMateri)) {
                $uniqueMateri[] = $materiKey;
            }
        }
        // Urutkan daftar materi
        sort($uniqueMateri);
        // Tampilkan sebagai array JavaScript
        $materiArray = [];
        foreach ($uniqueMateri as $materi) {
            $materiArray[] = json_encode($materi, JSON_UNESCAPED_UNICODE);
        }
        echo implode(',', $materiArray);
        ?>
    ];

    // Inisialisasi DataTable dengan scrollX simple dan simpan instance
    var table = initializeDataTableScrollX("#TabelNilaiPerSemester", [], {
        orderCellsTop: true,
        order: [],
        columnDefs: [{
                targets: 0, // Kolom Aksi
                orderable: false,
                searchable: false
            },
            {
                targets: 1, // Kolom Nilai
                type: 'num', // Sorting numerik
                render: function(data, type, row) {
                    // Untuk sorting, gunakan data-sort attribute dari td
                    if (type === 'sort' || type === 'type') {
                        // Ambil dari data-sort attribute pada td
                        const td = $(row).find('td:eq(1)');
                        const sortValue = td.attr('data-sort');
                        if (sortValue !== undefined && sortValue !== null) {
                            return parseFloat(sortValue) || 0;
                        }
                        // Fallback: ambil dari input/select
                        const input = $(data).find('input, select');
                        if (input.length) {
                            const value = input.val() || input.text();
                            return parseFloat(value) || 0;
                        }
                        return parseFloat(data) || 0;
                    }
                    return data;
                }
            },
            {
                targets: 2, // Kolom Materi
                render: function(data, type, row) {
                    // Untuk search/filter, extract NamaMateri dari HTML
                    if (type === 'filter' || type === 'search') {
                        const $temp = $('<div>').html(data);
                        const namaMateri = $temp.find('div:first').text().trim();
                        return namaMateri || $temp.text().trim();
                    }
                    return data;
                }
            }
        ]
    });

    // Tentukan index kolom "Materi"
    // Kolom Aksi selalu ada, jadi index kolom nama materi adalah 2
    var namaMateriColumnIndex = 2;

    // Fungsi untuk membuat regex pattern dari array nilai
    function createFilterPattern(selectedValues) {
        if (!selectedValues || selectedValues.length === 0) {
            return ''; // Tidak ada filter, tampilkan semua
        }
        // Escape special characters untuk regex dan gabungkan dengan OR
        const escapedValues = selectedValues.map(function(value) {
            return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        });
        return escapedValues.join('|');
    }

    // Fungsi untuk menyimpan filter ke localStorage
    function saveFilter(selectedValues) {
        if (selectedValues && selectedValues.length > 0) {
            localStorage.setItem(filterStorageKey, JSON.stringify(selectedValues));
        } else {
            localStorage.removeItem(filterStorageKey);
        }
    }

    // Variable untuk menyimpan custom filter function
    var customFilterFunction = null;

    // Fungsi untuk menerapkan custom filter
    function applyCustomFilter(selectedValues) {
        if (!table) {
            console.warn('Table not initialized');
            return;
        }

        // Hapus custom filter function yang lama jika ada
        if (customFilterFunction) {
            // Cari dan hapus customFilterFunction dari array
            const searchFunctions = $.fn.dataTable.ext.search;
            for (let i = searchFunctions.length - 1; i >= 0; i--) {
                if (searchFunctions[i] === customFilterFunction) {
                    searchFunctions.splice(i, 1);
                    break;
                }
            }
            customFilterFunction = null;
        }

        // Jika tidak ada filter yang dipilih, clear search dan tampilkan semua
        if (!selectedValues || selectedValues.length === 0) {
            table.column(namaMateriColumnIndex).search('').draw();
            return;
        }

        // Buat custom filter function baru
        customFilterFunction = function(settings, data, dataIndex) {
            // Hanya terapkan filter untuk tabel ini
            if (!settings || !settings.nTable || settings.nTable.id !== 'TabelNilaiPerSemester') {
                return true;
            }

            try {
                // Dapatkan row node langsung dari DataTable
                const row = table.row(dataIndex).node();
                if (!row) {
                    return true;
                }

                // Ambil cell dari kolom Materi (index 2)
                const $cell = $(row).find('td').eq(namaMateriColumnIndex);
                if (!$cell.length) {
                    return true;
                }

                // Ambil teks dari div pertama (NamaMateri)
                const $firstDiv = $cell.find('div:first');
                const cellText = $firstDiv.length ? $firstDiv.text().trim() : $cell.text().trim();

                // Cek apakah cellText match dengan salah satu selectedValues
                const isMatch = selectedValues.some(function(value) {
                    return cellText === value;
                });

                return isMatch;
            } catch (e) {
                console.error('Error in custom filter:', e, 'dataIndex:', dataIndex);
                return true;
            }
        };

        // Tambahkan custom filter function
        $.fn.dataTable.ext.search.push(customFilterFunction);

        // Redraw tabel
        table.draw();
    }

    // Fungsi untuk memuat filter dari localStorage
    function loadFilter() {
        const savedFilter = localStorage.getItem(filterStorageKey);
        if (savedFilter) {
            try {
                const selectedValues = JSON.parse(savedFilter);
                if (Array.isArray(selectedValues) && selectedValues.length > 0) {
                    isLoadingFilter = true;
                    // Set nilai select untuk Select2
                    $('#filterNamaMateri').val(selectedValues).trigger('change.select2');
                    // Terapkan custom filter
                    applyCustomFilter(selectedValues);
                    // Redraw tabel
                    if (table) {
                        table.draw();
                    }
                    isLoadingFilter = false;
                }
            } catch (e) {
                console.error('Error loading filter from localStorage:', e);
                localStorage.removeItem(filterStorageKey);
                isLoadingFilter = false;
            }
        }
    }

    // Ganti search box DataTable dengan Select2 di samping search box default
    $(document).ready(function() {
        // Tunggu sampai DataTable selesai diinisialisasi dan siap
        // Pastikan tabel sudah terinisialisasi dengan baik
        function initFilterSelect2() {
            // Cek apakah DataTable sudah terinisialisasi
            if (!$.fn.DataTable.isDataTable('#TabelNilaiPerSemester')) {
                setTimeout(initFilterSelect2, 100);
                return;
            }

            // Pastikan variabel table sudah ada
            if (!table) {
                table = $('#TabelNilaiPerSemester').DataTable();
            }

            // Cari label search box DataTable
            var filterLabel = $('.dataTables_filter label');
            var searchBox = $('.dataTables_filter input');

            if (filterLabel.length && searchBox.length) {
                // Cek apakah filter sudah ada, jika sudah jangan buat lagi
                if ($('#filterNamaMateri').length > 0) {
                    return;
                }

                // Buat select element untuk Select2
                var selectElement = $('<select id="filterNamaMateri" class="form-control form-control-sm" multiple></select>');

                // Tambahkan opsi dari daftar materi
                daftarMateri.forEach(function(materi) {
                    selectElement.append('<option value="' + materi + '">' + materi + '</option>');
                });

                // Sisipkan select di dalam label setelah search box
                searchBox.after(selectElement);

                // Tunggu sebentar untuk memastikan elemen sudah di DOM
                setTimeout(function() {
                    // Fungsi untuk mengatur width Select2 berdasarkan ukuran layar
                    function setSelect2Width() {
                        var width = '300px';
                        if (window.innerWidth <= 768) {
                            width = '100%';
                        } else if (window.innerWidth <= 992) {
                            width = '250px';
                        }
                        $('#filterNamaMateri').next('.select2-container').css('width', width);
                    }

                    // Inisialisasi Select2 untuk filter nama materi (multiple select)
                    $('#filterNamaMateri').select2({
                        placeholder: 'Filter Materi (bisa pilih beberapa)...',
                        allowClear: true,
                        width: 'resolve', // Gunakan resolve agar responsive
                        closeOnSelect: false,
                        minimumResultsForSearch: 0, // Paksa search box selalu muncul
                        language: {
                            noResults: function() {
                                return "Tidak ada hasil yang ditemukan";
                            },
                            searching: function() {
                                return "Mencari...";
                            }
                        }
                    });

                    // Set width awal
                    setSelect2Width();

                    // Event listener untuk resize window
                    $(window).on('resize', function() {
                        setSelect2Width();
                    });

                    // Event listener untuk filter nama materi saat berubah
                    $('#filterNamaMateri').on('change', function() {
                        const selectedValues = $(this).val() || [];
                        // Simpan filter ke localStorage (kecuali saat loading)
                        if (!isLoadingFilter) {
                            saveFilter(selectedValues);
                        }
                        // Terapkan custom filter
                        applyCustomFilter(selectedValues);
                        // Redraw tabel untuk menerapkan filter
                        if (table) {
                            table.draw();
                        }
                    });

                    // Memuat filter yang tersimpan setelah Select2 siap
                    setTimeout(function() {
                        loadFilter();
                        // Set width lagi setelah load filter
                        setSelect2Width();
                    }, 100);
                }, 50);
            } else {
                // Jika belum ada, coba lagi setelah delay
                setTimeout(initFilterSelect2, 100);
            }
        }

        // Mulai inisialisasi filter setelah delay awal
        setTimeout(initFilterSelect2, 300);
    });

    // Fungsi untuk menampilkan modal edit nilai dan menangani pengiriman form
    function showModalEditNilai(id) {
        // Ambil nilai lama
        nilaiLama = $('#NilaiEditModal-' + id).val();

        // Tampilkan modal
        $('#EditNilai' + id).modal({
            backdrop: 'static',
            keyboard: false
        });

        // Set fokus ke input nilai setelah modal dibuka
        $('#EditNilai' + id).on('shown.bs.modal', function() {
            // Tunggu sebentar untuk memastikan modal benar-benar terbuka
            setTimeout(function() {
                const input = $('#NilaiEditModal-' + id);
                if (input.length) {
                    // Fokus ke input
                    input.focus();

                    // Jika ada nilai, pindahkan kursor ke akhir
                    const value = input.val();
                    if (value) {
                        // Gunakan cara alternatif untuk memindahkan kursor
                        input.val(''); // Kosongkan dulu
                        input.val(value); // Isi kembali
                        input.focus(); // Fokus lagi
                    }
                }
            }, 200);
        });

        // Hapus event handler submit yang lama dan tambahkan yang baru
        $('#EditNilai' + id + ' form').off('submit').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);

            // Disable tombol submit untuk mencegah multiple submission
            const submitButton = form.find('button[type="submit"]');
            submitButton.prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#EditNilai' + form.find('input[name="Id"]').val()).modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data nilai berhasil diperbarui',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        const newValue = response.newValue;
                        const idNilai = form.find('input[name="Id"]').val();
                        $('#Nilai-' + idNilai).val(newValue);
                        // Ubah border warna menjadi hijau
                        $('#Nilai-' + idNilai).css({
                            'border': '2px solid green'
                        });
                        // Ubah warna button dan icon dan teks tombol berdasarkan nilai baru
                        if (newValue > 0) {
                            $('#EditNilai-' + id).html('<i class="fas fa-edit"></i><span style="margin-left: 5px;"></span>Edit');
                            $('#EditNilai-' + id).removeClass('btn-primary').addClass('btn-warning');
                        }

                        isChanged = false;
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menyimpan data',
                    });
                },
                complete: function() {
                    // Enable kembali tombol submit
                    submitButton.prop('disabled', false);
                }
            });
        });

        // Ubah handler untuk tombol Keluar
        $('#tutupModal-' + id).off('click').on('click', function() {
            nilaiBaru = $('#NilaiEditModal-' + id).val();
            if (isChanged) {
                Swal.fire({
                    title: 'Perhatian',
                    html: 'Nilai yang sudah berubah <span style="color: red; font-weight: bold;">' + nilaiLama + '</span> menjadi <span style="color: green; font-weight: bold;">' + nilaiBaru + '</span> tidak akan disimpan. Apakah Anda yakin ingin keluar?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya,Keluar',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        isChanged = false;
                        $('#NilaiEditModal-' + id).val(nilaiLama);
                        $('#EditNilai' + id).modal('hide');
                    }
                });
            } else {
                $('#EditNilai' + id).modal('hide');
            }
        });

        // Reset status perubahan saat modal dibuka
        isChanged = false;

        // Tambahkan event listener untuk perubahan nilai
        $('#NilaiEditModal-' + id).off('change').on('change', function() {
            isChanged = true;
        });
    }

    // ========== INLINE EDITING DENGAN AUTO-SAVE ==========

    // Object untuk menyimpan timeout per input
    const saveTimeouts = {};

    // Object untuk menyimpan nilai original per input
    const originalValues = {};

    // Flag untuk mencegah save ganda
    const savingInProgress = {};

    // Object untuk menyimpan notifikasi error per kolom (id -> notificationId)
    const errorNotifications = {};

    // Fungsi untuk menampilkan flash notification
    function showFlashNotification(message, type = 'success', autoClose = true, duration = 3000, columnId = null) {
        const container = $('#flashNotificationContainer');
        const notificationId = 'flash-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);

        // Tentukan warna dan icon berdasarkan type
        let bgColor, textColor, icon, progressColor;
        if (type === 'success') {
            bgColor = '#28a745';
            textColor = '#fff';
            icon = '<i class="fas fa-check-circle"></i>';
            progressColor = '#fff';
        } else if (type === 'error') {
            bgColor = '#dc3545';
            textColor = '#fff';
            icon = '<i class="fas fa-exclamation-circle"></i>';
            progressColor = '#fff';
        } else {
            bgColor = '#17a2b8';
            textColor = '#fff';
            icon = '<i class="fas fa-info-circle"></i>';
            progressColor = '#fff';
        }

        // Buat elemen notification
        const notification = $(`
            <div id="${notificationId}" class="alert alert-dismissible fade show shadow-lg" 
                 style="background-color: ${bgColor}; color: ${textColor}; border: none; margin-bottom: 10px; border-radius: 8px; padding: 15px; position: relative; overflow: hidden;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="font-size: 20px;">${icon}</div>
                    <div style="flex: 1; font-size: 14px; line-height: 1.4;">${message}</div>
                    ${!autoClose ? '<button type="button" class="close" style="color: ' + textColor + '; opacity: 0.8; font-size: 20px; line-height: 1; padding: 0; margin-left: 10px;" onclick="closeFlashNotification(\'' + notificationId + '\')"><span>&times;</span></button>' : ''}
                </div>
                ${autoClose ? '<div class="notification-progress" style="position: absolute; bottom: 0; left: 0; height: 3px; background-color: ' + progressColor + '; width: 100%; animation: progressBar ' + duration + 'ms linear;"></div>' : ''}
            </div>
        `);

        // Tambahkan ke container
        container.append(notification);

        // Jika ini adalah notifikasi error untuk kolom tertentu, simpan referensinya
        if (type === 'error' && columnId !== null) {
            // Tutup notifikasi error lama untuk kolom ini jika ada
            if (errorNotifications[columnId]) {
                closeFlashNotification(errorNotifications[columnId]);
            }
            // Simpan referensi notifikasi error baru
            errorNotifications[columnId] = notificationId;
        }

        // Auto close jika diperlukan
        if (autoClose) {
            setTimeout(function() {
                closeFlashNotification(notificationId);
            }, duration);
        }

        // Animasi masuk
        setTimeout(function() {
            $('#' + notificationId).addClass('show');
        }, 10);

        // Return notificationId untuk referensi
        return notificationId;
    }

    // Fungsi untuk menutup notification
    function closeFlashNotification(notificationId) {
        const notification = $('#' + notificationId);
        if (notification.length === 0) {
            return; // Notification sudah tidak ada
        }
        notification.removeClass('show');
        setTimeout(function() {
            notification.remove();
            // Hapus dari errorNotifications jika ada
            for (const columnId in errorNotifications) {
                if (errorNotifications[columnId] === notificationId) {
                    delete errorNotifications[columnId];
                    break;
                }
            }
        }, 300);
    }

    // Tambahkan CSS untuk animasi progress bar
    if (!$('#inlineEditStyles').length) {
        $('head').append(`
            <style id="inlineEditStyles">
                @keyframes progressBar {
                    from { width: 100%; }
                    to { width: 0%; }
                }
                .nilai-input-inline:focus {
                    border-color: #007bff !important;
                    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
                }
                .nilai-input-inline.saving {
                    border-color: #ffc107 !important;
                    background-color: #fff3cd !important;
                }
                .nilai-input-inline.saved {
                    border-color: #28a745 !important;
                    background-color: #d4edda !important;
                }
                .nilai-input-inline.error {
                    border-color: #dc3545 !important;
                    background-color: #f8d7da !important;
                }
                .readonly-disabled {
                    cursor: not-allowed !important;
                    background-color: #e9ecef !important;
                }
                
                /* Responsive untuk input nilai di mobile */
                @media (max-width: 768px) {
                    #TabelNilaiPerSemester td:nth-child(2) {
                        min-width: 120px !important;
                        width: 120px !important;
                    }
                    
                    .nilai-input-inline {
                        font-size: 18px !important;
                        padding: 12px 8px !important;
                        min-height: 48px !important;
                        text-align: center !important;
                    }
                    
                    .nilai-input-inline.nilai-input-numeric {
                        font-size: 20px !important;
                        font-weight: 600 !important;
                    }
                    
                    /* Styling button Edit, Add, dan View di mobile */
                    .btn-action-nilai {
                        display: flex !important;
                        flex-direction: column !important;
                        align-items: center !important;
                        justify-content: center !important;
                        width: 100% !important;
                        min-width: 100% !important;
                        padding: 10px 10px !important;
                        font-size: 20px !important;
                        gap: 2px !important;
                    }
                    
                    .btn-action-nilai i {
                        font-size: 18px !important;
                        margin: 0 !important;
                        display: block !important;
                    }
                    
                    .btn-action-nilai .btn-text {
                        margin: 0 !important;
                        display: block !important;
                        font-size: 15px !important;
                        line-height: 1.2 !important;
                    }
                }
            </style>
        `);
    }

    // Fungsi untuk validasi nilai
    function validateNilai(value, isAlphabet, nilaiMin, nilaiMax, alphabetOptions) {
        if (value === '' || value === null || value === undefined) {
            return {
                valid: false,
                message: 'Nilai tidak boleh kosong'
            };
        }

        if (isAlphabet) {
            // Validasi untuk alphabet: nilai harus ada di dalam opsi yang tersedia
            const numericValue = parseFloat(value);
            if (isNaN(numericValue)) {
                return {
                    valid: false,
                    message: 'Nilai harus berupa angka'
                };
            }
            const validValues = alphabetOptions.map(opt => parseFloat(opt.value));
            if (!validValues.includes(numericValue) && numericValue !== 0) {
                return {
                    valid: false,
                    message: 'Nilai tidak valid untuk sistem alphabet'
                };
            }
        } else {
            // Validasi untuk numerik
            const numericValue = parseFloat(value);
            if (isNaN(numericValue)) {
                return {
                    valid: false,
                    message: 'Nilai harus berupa angka'
                };
            }
            if (numericValue < nilaiMin || numericValue > nilaiMax) {
                return {
                    valid: false,
                    message: `Nilai harus antara ${nilaiMin} dan ${nilaiMax}`
                };
            }
        }

        return {
            valid: true
        };
    }

    // Fungsi untuk menyimpan nilai
    function saveNilai(inputElement) {
        const $input = $(inputElement);
        const id = $input.data('id');
        const nilaiBaru = $input.val();
        const nilaiLama = $input.data('nilai-lama');
        const materi = $input.data('materi');
        const isAlphabet = $input.data('is-alphabet') === true || $input.data('is-alphabet') === 'true';
        const nilaiMin = parseFloat($input.data('nilai-min')) || 0;
        const nilaiMax = parseFloat($input.data('nilai-max')) || 100;

        // Jika nilai tidak berubah, tidak perlu save
        // Konversi ke string untuk perbandingan yang konsisten
        if (String(nilaiBaru) === String(nilaiLama)) {
            return;
        }

        // Validasi: Mencegah perubahan dari nilai > 0 menjadi 0
        const nilaiLamaFloat = parseFloat(nilaiLama) || 0;
        const nilaiBaruFloat = parseFloat(nilaiBaru) || 0;

        if (nilaiLamaFloat > 0 && nilaiBaruFloat === 0) {
            $input.addClass('error');
            showFlashNotification(
                `<strong>Validasi Gagal</strong><br>${materi}<br>Nilai tidak dapat diubah dari ${nilaiLama} menjadi 0. Nilai yang sudah diisi tidak dapat dihapus.`,
                'error',
                false, // Manual close untuk error
                0, // Duration tidak digunakan untuk error
                id // Column ID untuk tracking
            );
            // Kembalikan nilai lama setelah 2 detik
            setTimeout(function() {
                $input.val(nilaiLama);
                $input.data('nilai-lama', nilaiLama);
                $input.removeClass('error');
            }, 2000);
            return;
        }

        // Validasi nilai
        let alphabetOptions = [];
        if (isAlphabet && $input.is('select')) {
            alphabetOptions = Array.from($input.find('option')).map(opt => ({
                value: opt.value
            }));
        }

        const validation = validateNilai(nilaiBaru, isAlphabet, nilaiMin, nilaiMax, alphabetOptions);
        if (!validation.valid) {
            $input.addClass('error');
            showFlashNotification(
                `<strong>Validasi Gagal</strong><br>${materi}<br>${validation.message}`,
                'error',
                false, // Manual close untuk error
                0, // Duration tidak digunakan untuk error
                id // Column ID untuk tracking
            );
            // Kembalikan nilai lama setelah 2 detik
            setTimeout(function() {
                $input.val(nilaiLama);
                $input.data('nilai-lama', nilaiLama);
                $input.removeClass('error');
            }, 2000);
            return;
        }

        // Cegah save ganda
        if (savingInProgress[id]) {
            return;
        }
        savingInProgress[id] = true;

        // Tampilkan indikator saving
        $input.addClass('saving').removeClass('saved error');

        // Siapkan data untuk dikirim
        const formData = {
            Id: id,
            Nilai: nilaiBaru
        };

        // Jika menggunakan radio button (alphabet), kirim juga NilaiRadio
        if (isAlphabet && $input.is('select')) {
            formData.NilaiRadio = nilaiBaru;
        }

        // Kirim request AJAX
        $.ajax({
            url: '<?= base_url('backend/nilai/update') ?>',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    // Tutup notifikasi error untuk kolom ini jika ada
                    if (errorNotifications[id]) {
                        closeFlashNotification(errorNotifications[id]);
                    }

                    // Update nilai lama
                    $input.data('nilai-lama', nilaiBaru);

                    // Update data-sort attribute pada td untuk sorting
                    const $td = $input.closest('td');
                    $td.attr('data-sort', nilaiBaru);

                    // Update border berdasarkan nilai
                    if (parseFloat(nilaiBaru) == 0) {
                        $input.css('border', '2px solid red');
                    } else {
                        $input.css('border', '2px solid green');
                    }

                    // Tampilkan indikator saved
                    $input.removeClass('saving').addClass('saved');
                    setTimeout(function() {
                        $input.removeClass('saved');
                    }, 2000);

                    // Tampilkan notifikasi success
                    const nilaiDisplay = isAlphabet && $input.is('select') ?
                        $input.find('option:selected').text() :
                        nilaiBaru;
                    showFlashNotification(
                        `<strong>Berhasil Disimpan</strong><br>${materi}<br>Nilai: ${nilaiDisplay}`,
                        'success',
                        true,
                        5000
                    );

                    // Update tombol edit jika perlu
                    const btnEdit = $('#EditNilai-' + id);
                    if (parseFloat(nilaiBaru) > 0) {
                        btnEdit.html('<i class="fas fa-edit"></i><span style="margin-left: 5px;"></span>Edit');
                        btnEdit.removeClass('btn-primary').addClass('btn-warning');
                    } else {
                        btnEdit.html('<i class="fas fa-plus"></i><span style="margin-left: 5px;"></span>Add');
                        btnEdit.removeClass('btn-warning').addClass('btn-primary');
                    }

                    // Update DataTable sorting jika diperlukan
                    const table = $('#TabelNilaiPerSemester').DataTable();
                    table.draw(false); // Redraw tanpa reset paging
                } else {
                    // Error dari server
                    $input.addClass('error').removeClass('saving saved');
                    showFlashNotification(
                        `<strong>Gagal Menyimpan</strong><br>${materi}<br>${response.message || 'Terjadi kesalahan saat menyimpan data'}`,
                        'error',
                        false, // Manual close untuk error
                        0, // Duration tidak digunakan untuk error
                        id // Column ID untuk tracking
                    );
                    // Kembalikan nilai lama
                    $input.val(nilaiLama);
                }
            },
            error: function(xhr) {
                // Error dari AJAX
                $input.addClass('error').removeClass('saving saved');
                let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showFlashNotification(
                    `<strong>Gagal Menyimpan</strong><br>${materi}<br>${errorMessage}`,
                    'error',
                    false, // Manual close untuk error
                    0, // Duration tidak digunakan untuk error
                    id // Column ID untuk tracking
                );
                // Kembalikan nilai lama
                $input.val(nilaiLama);
            },
            complete: function() {
                // Reset flag
                savingInProgress[id] = false;
            }
        });
    }

    // Event handler untuk mencegah input non-numerik dan membatasi 2 digit
    $(document).on('keydown', '.nilai-input-numeric', function(e) {
        const $input = $(this);
        const key = e.key;
        const currentValue = $input.val();

        // Izinkan: Backspace, Delete, Tab, Escape, Enter, Arrow keys, Home, End
        if ([8, 9, 27, 13, 46, 35, 36, 37, 38, 39, 40].indexOf(e.keyCode) !== -1 ||
            // Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            (e.keyCode === 65 && e.ctrlKey === true) ||
            (e.keyCode === 67 && e.ctrlKey === true) ||
            (e.keyCode === 86 && e.ctrlKey === true) ||
            (e.keyCode === 88 && e.ctrlKey === true)) {
            return;
        }

        // Izinkan hanya angka 0-9
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
            return false;
        }

        // Cegah input jika sudah 2 digit (kecuali untuk select all atau delete)
        if (currentValue.length >= 2 &&
            !(e.keyCode === 65 && e.ctrlKey) && // Ctrl+A
            !(e.keyCode === 8) && // Backspace
            !(e.keyCode === 46) && // Delete
            !(e.keyCode >= 35 && e.keyCode <= 40)) { // Arrow keys, Home, End
            e.preventDefault();
            return false;
        }
    });

    // Event handler untuk paste - hanya izinkan angka dan maksimal 2 digit
    $(document).on('paste', '.nilai-input-numeric', function(e) {
        e.preventDefault();
        const $input = $(this);
        const pastedText = (e.originalEvent || e).clipboardData.getData('text/plain');

        // Hapus semua karakter non-numerik
        const numericOnly = pastedText.replace(/[^0-9]/g, '');

        // Ambil hanya 2 digit pertama
        const limitedValue = numericOnly.substring(0, 2);

        $input.val(limitedValue);

        // Trigger input event untuk update
        $input.trigger('input');
    });

    // Event handler untuk input - hapus karakter non-numerik dan batasi 2 digit
    $(document).on('input', '.nilai-input-numeric', function() {
        const $input = $(this);
        let value = $input.val();

        // Hapus semua karakter non-numerik
        value = value.replace(/[^0-9]/g, '');

        // Batasi hanya 2 digit
        if (value.length > 2) {
            value = value.substring(0, 2);
        }

        // Update nilai jika ada perubahan
        if ($input.val() !== value) {
            $input.val(value);
        }
    });

    // Event handler untuk input/select nilai (auto-save dengan delay 5 detik)
    $(document).on('input', '.nilai-input-inline', function() {
        const $input = $(this);
        const id = $input.data('id');
        const nilaiBaru = $input.val();
        const nilaiLama = $input.data('nilai-lama');

        // Hapus timeout sebelumnya jika ada
        if (saveTimeouts[id]) {
            clearTimeout(saveTimeouts[id]);
        }

        // Hapus class saved/error saat user mengetik
        $input.removeClass('saved error');

        // Konversi ke number untuk perbandingan
        const nilaiBaruNum = parseFloat(nilaiBaru) || 0;
        const nilaiLamaNum = parseFloat(nilaiLama) || 0;

        // Jika nilai lama adalah 0 dan nilai baru juga 0, jangan set timeout
        // (User hanya fokus ke input tapi tidak mengubah nilai)
        if (nilaiLamaNum === 0 && nilaiBaruNum === 0) {
            return;
        }

        // Hanya set timeout jika nilai berbeda dari nilai lama
        // Konversi ke string untuk perbandingan yang konsisten
        if (String(nilaiBaru) !== String(nilaiLama)) {
            // Set timeout untuk auto-save setelah 5 detik tidak ada input
            saveTimeouts[id] = setTimeout(function() {
                // Cek lagi apakah nilai masih berbeda sebelum save
                const currentValue = $input.val();
                const originalValue = $input.data('nilai-lama');
                const currentValueNum = parseFloat(currentValue) || 0;
                const originalValueNum = parseFloat(originalValue) || 0;

                // Jangan save jika nilai tetap 0
                if (originalValueNum === 0 && currentValueNum === 0) {
                    return;
                }

                if (String(currentValue) !== String(originalValue)) {
                    saveNilai($input[0]);
                }
            }, 5000);
        }
    });

    // Event handler untuk focus (otomatis hapus nilai 0 saat user klik/tap)
    $(document).on('focus', '.nilai-input-inline', function() {
        const $input = $(this);
        const nilaiSaatIni = $input.val();
        const nilaiSaatIniNum = parseFloat(nilaiSaatIni) || 0;

        // Jika nilai saat ini adalah 0, hapus nilai (set menjadi empty string)
        // Ini memudahkan user untuk langsung mengetik tanpa harus menghapus 0 terlebih dahulu
        if (nilaiSaatIniNum === 0) {
            $input.val('');
        }
    });

    // Event handler untuk blur (auto-save saat kursor pindah atau klik di luar)
    $(document).on('blur', '.nilai-input-inline', function() {
        const $input = $(this);
        const id = $input.data('id');
        let nilaiBaru = $input.val();
        const nilaiLama = $input.data('nilai-lama');

        // Jika nilai kosong setelah blur, set kembali menjadi 0
        if (nilaiBaru === '' || nilaiBaru === null || nilaiBaru === undefined) {
            nilaiBaru = '0';
            $input.val('0');
        }

        // Hapus timeout jika ada
        if (saveTimeouts[id]) {
            clearTimeout(saveTimeouts[id]);
            delete saveTimeouts[id];
        }

        // Konversi ke number untuk perbandingan
        const nilaiBaruNum = parseFloat(nilaiBaru) || 0;
        const nilaiLamaNum = parseFloat(nilaiLama) || 0;

        // Jika nilai lama adalah 0 dan nilai baru juga 0, jangan trigger save
        // (User hanya fokus ke input tapi tidak mengubah nilai)
        if (nilaiLamaNum === 0 && nilaiBaruNum === 0) {
            return;
        }

        // Hanya save jika nilai berbeda dari nilai lama
        // Konversi ke string untuk perbandingan yang konsisten
        if (String(nilaiBaru) !== String(nilaiLama)) {
            saveNilai($input[0]);
        }
    });

    // Event handler untuk change pada select (dropdown alphabet)
    $(document).on('change', '.nilai-input-inline', function() {
        const $input = $(this);
        const id = $input.data('id');
        const nilaiBaru = $input.val();
        const nilaiLama = $input.data('nilai-lama');

        // Hapus timeout jika ada
        if (saveTimeouts[id]) {
            clearTimeout(saveTimeouts[id]);
            delete saveTimeouts[id];
        }

        // Konversi ke number untuk perbandingan
        const nilaiBaruNum = parseFloat(nilaiBaru) || 0;
        const nilaiLamaNum = parseFloat(nilaiLama) || 0;

        // Jika nilai lama adalah 0 dan nilai baru juga 0, jangan trigger save
        // (User hanya memilih opsi yang sama atau tidak mengubah)
        if (nilaiLamaNum === 0 && nilaiBaruNum === 0) {
            return;
        }

        // Hanya save jika nilai berbeda dari nilai lama
        // Konversi ke string untuk perbandingan yang konsisten
        if (String(nilaiBaru) !== String(nilaiLama)) {
            // Langsung save untuk select (dropdown)
            saveNilai($input[0]);
        }
    });

    // Event handler untuk klik di luar area input (mencegah save saat masih mengetik)
    $(document).on('click', function(e) {
        // Jika klik di luar input nilai, trigger blur pada input yang sedang aktif
        if (!$(e.target).closest('.nilai-input-inline').length) {
            $('.nilai-input-inline:focus').blur();
        }
    });
</script>
<?= $this->endSection(); ?>