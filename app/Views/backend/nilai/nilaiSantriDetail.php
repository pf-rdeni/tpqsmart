<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <?php echo session()->getFlashdata('pesan'); ?>
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
                    $tableHeadersFooter =
                        '<tr>';
                    if ($pageEdit) {
                        $tableHeadersFooter .= '<th>Aksi</th>';
                    }
                    $tableHeadersFooter .=
                        '
                        <th>Nilai</th>
                        <th>Id - Nama Materi</th>
                        <th>Kategori</th>
                         </tr>';

                    echo $tableHeadersFooter
                    ?>

                </thead>
                <tbody>
                    <?php
                    foreach ($nilai as $DataNilai) : ?>

                        <tr>
                            <?php if ($pageEdit) {
                                $isNilaiPositive = $DataNilai->Nilai > 0;
                                $isGuruPendamping4 = $guruPendamping == 4; // Assuming $guruPendamping is the value of $IdJabatan Guru Kelas bukan Wali Kelas

                                $btnClass = $isNilaiPositive ? ($isGuruPendamping4 ? 'btn-success' : 'btn-warning') : 'btn-primary';
                                $faClass = $isNilaiPositive ? ($isGuruPendamping4 ? 'fa-eye' : 'fa-edit') : 'fa-plus';
                                $name = $isNilaiPositive ? ($isGuruPendamping4 ? 'View' : 'Edit') : 'Add';
                                if ($name == 'View') {
                                    // button disabled
                                    $disabled = 'disabled';
                                } else {
                                    $disabled = '';
                                }
                            ?>
                                <td>
                                    <button id="EditNilai-<?= $DataNilai->Id ?>" class="btn <?= $btnClass ?> btn-sm" onclick="showModalEditNilai('<?= $DataNilai->Id ?>')" <?= $disabled ?>>
                                        <i class="fas <?= $faClass ?>"></i><span style=" margin-left: 5px;"></span><?= $name ?>
                                    </button>
                                </td>
                            <?php } ?>
                            <td>
                                <input type="text" name="Nilai-<?= $DataNilai->Id ?>" id="Nilai-<?= $DataNilai->Id ?>" class="form-control" value="<?php echo $DataNilai->Nilai; ?>" readonly
                                    style="border: <?= $DataNilai->Nilai == 0 ? '2px solid red' : '2px solid green' ?>;" />
                            </td>
                            <td><?php echo $DataNilai->IdMateri . ' - ' . $DataNilai->NamaMateri; ?></td>
                            <td><?php echo $DataNilai->Kategori; ?></td>
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
                    <form action="<?= base_url('backend/nilai/update/' . $pageEdit) ?>" method="POST">
                        <input type="hidden" name="Id" value=<?= $DataNilai->Id ?>>
                        <div class="form-group">
                            <label for="FormProfilTpq">Kategori</label>
                            <span class="form-control" id="FormProfilTpq"><?= $DataNilai->Kategori ?></span>
                        </div>

                        <div class="form-group">
                            <label for="FormProfilTpq">Id-Nama Materi</label>
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

    // Daftar materi dari PHP
    var daftarMateri = [
        <?php
        // Kumpulkan daftar nama materi yang unik
        $uniqueMateri = [];
        foreach ($nilai as $DataNilai) {
            $materiKey = $DataNilai->IdMateri . ' - ' . $DataNilai->NamaMateri;
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

    // Inisialisasi DataTable dengan konfigurasi tambahan
    initializeDataTableUmum("#TabelNilaiPerSemester", true, true, [], {
        orderCellsTop: true,
        order: []
    });

    // Dapatkan referensi DataTable setelah inisialisasi
    var table = $('#TabelNilaiPerSemester').DataTable();

    // Tentukan index kolom "Id - Nama Materi"
    // Jika ada kolom Aksi: index 2, jika tidak: index 1
    var namaMateriColumnIndex = <?= $pageEdit ? 2 : 1 ?>;

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
                    // Terapkan filter ke tabel
                    const filterPattern = createFilterPattern(selectedValues);
                    table.column(namaMateriColumnIndex).search(filterPattern, true, false).draw();
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
        // Tunggu sampai DataTable selesai diinisialisasi
        setTimeout(function() {
            // Cari label search box DataTable
            var filterLabel = $('.dataTables_filter label');
            var searchBox = $('.dataTables_filter input');

            if (filterLabel.length && searchBox.length) {
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
                        // Buat pattern filter untuk DataTable
                        const filterPattern = createFilterPattern(selectedValues);
                        // Terapkan filter ke tabel
                        table.column(namaMateriColumnIndex).search(filterPattern, true, false).draw();
                    });

                    // Memuat filter yang tersimpan setelah Select2 siap
                    setTimeout(function() {
                        loadFilter();
                        // Set width lagi setelah load filter
                        setSelect2Width();
                    }, 100);
                }, 50);
            }
        }, 300);
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
</script>
<?= $this->endSection(); ?>