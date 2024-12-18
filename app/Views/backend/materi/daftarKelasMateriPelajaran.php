<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content') ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    Daftar Kelas Materi Pelajaran
                    <?php
                    $kelas = $materi_per_kelas[1];
                    if (!empty($kelas['nama_tpq']))
                        echo "TPQ " . $kelas['nama_tpq']
                    ?>
                </h3>

                <div class="card-tools">
                    <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahData">
                        <i class="fas fa-plus"></i> Tambah Data
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="card card-primary card-tabs">
                <!-- Tab Navigation -->
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs flex-wrap justify-content-start justify-content-md-between" id="kelasTab" role="tablist">
                        <?php foreach ($materi_per_kelas as $kelasId => $kelas): ?>
                            <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                                <a class="nav-link border-white text-center <?= $kelasId === array_key_first($materi_per_kelas) ? 'active' : '' ?>"
                                    id="tab-<?= $kelasId ?>"
                                    data-toggle="tab"
                                    href="#kelas-<?= $kelasId ?>"
                                    role="tab"
                                    aria-controls="kelas-<?= $kelasId ?>"
                                    aria-selected="<?= $kelasId === array_key_first($materi_per_kelas) ? 'true' : 'false' ?>">
                                    <?= $kelas['nama_kelas'] ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <br>
                <div class="card-body">
                    <div class="tab-content" id="kelasTabContent">
                        <?php foreach ($materi_per_kelas as $kelasId => $kelas): ?>
                            <div class="tab-pane fade <?= $kelasId === array_key_first($materi_per_kelas) ? 'show active' : '' ?>"
                                id="kelas-<?= $kelasId ?>"
                                role="tabpanel"
                                aria-labelledby="tab-<?= $kelasId ?>">
                                <table class="table table-bordered table-striped" id="tblKelas-<?= $kelasId ?>">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>IdMateri</th>
                                            <th>Kategori</th>
                                            <th>Materi</th>
                                            <th>S.Ganjil</th>
                                            <th>S.Genap</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $grouped_materi = [];

                                        // Mengelompokkan materi berdasarkan IdMateri
                                        foreach ($kelas['materi'] as $item) {
                                            $id = $item['IdMateri'];
                                            if (!isset($grouped_materi[$id])) {
                                                $grouped_materi[$id] = [
                                                    'IdMateri' => $item['IdMateri'],
                                                    'Kategori' => $item['Kategori'],
                                                    'NamaMateri' => $item['NamaMateri'],
                                                    'Semester1' => false,
                                                    'Semester2' => false
                                                ];
                                            }
                                            if ($item['Semester'] == 1) {
                                                $grouped_materi[$id]['Semester1'] = true;
                                            }
                                            if ($item['Semester'] == 2) {
                                                $grouped_materi[$id]['Semester2'] = true;
                                            }
                                        }

                                        foreach ($grouped_materi as $materi): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= $materi['IdMateri'] ?></td>
                                                <td><?= $materi['Kategori'] ?></td>
                                                <td><?= $materi['NamaMateri'] ?></td>
                                                <td>
                                                    <input type="checkbox"
                                                        <?= $materi['Semester1'] ? 'checked' : '' ?>>
                                                </td>
                                                <td>
                                                    <input type="checkbox"
                                                        <?= $materi['Semester2'] ? 'checked' : '' ?>>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>IdMateri</th>
                                            <th>Kategori</th>
                                            <th>Materi</th>
                                            <th>S.Ganjil</th>
                                            <th>S.Genap</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Buat Target Materi -->
<div class="modal fade" id="modalTambahData" tabindex="-1" role="dialog" aria-labelledby="modalTambahDataLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalTambahDataLabel">Tambah Data Materi</h5>
            </div>
            <div class="card-body">
                <form id="formTambahData">
                    <div class="form-group">
                        <label for="tingkatKelas">Tingkat Kelas</label>
                        <select class="form-control" id="tingkatKelas" name="tingkatKelas" required>
                            <option value="">Pilih Tingkat Kelas</option>
                            <?php foreach ($dataKelas as $tingkat): ?>
                                <option value="<?= $tingkat['IdKelas'] ?>"><?= $tingkat['NamaKelas'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="card card-primary card-tabs">
                        <div class="card-header p-0 pt-1">
                            <ul class="nav nav-tabs flex-wrap justify-content-start justify-content-md-between" id="kategoriTab" role="tablist">
                                <?php
                                $kategoriTampil = []; // Array untuk menyimpan kategori yang sudah ditampilkan
                                foreach ($dataMateriPelajaran as $index => $materi):
                                    if (!in_array($materi['Kategori'], $kategoriTampil)): // Cek jika kategori sudah ditampilkan
                                        $kategoriTampil[] = $materi['Kategori']; // Tambahkan kategori ke array
                                ?>
                                        <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                                            <a class="nav-link border-white text-center  <?= $index === 0 ? 'active' : '' ?>"
                                                id="tab-kategori-<?= $index ?>"
                                                data-toggle="tab"
                                                href="#kategori-<?= $index ?>"
                                                role="tab"
                                                aria-controls="kategori-<?= $index ?>"
                                                aria-selected="<?= $index === 0 ? 'true' : 'false' ?>">
                                                <?= $materi['Kategori'] ?>
                                            </a>
                                        </li>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </ul>
                        </div>
                    </div>
                    <br>
                    <div class="tab-content">
                        <?php foreach ($dataMateriPelajaran as $index => $materi): ?>
                            <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" id="kategori-<?= $index ?>" role="tabpanel" aria-labelledby="tab-kategori-<?= $index ?>">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nama Materi</th>
                                            <th>Pilih</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dataMateriPelajaran as $item): ?>
                                            <?php if ($item['Kategori'] === $materi['Kategori']): ?>
                                                <tr>
                                                    <td><?= $item['NamaMateri'] ?></td>
                                                    <td>
                                                        <input type="checkbox" id="IdMateri<?= $item['IdMateri'] ?>" name="IdMateri[<?= $item['IdMateri'] ?>]" <?= $item['NamaMateri'] == 1 ? 'checked' : '' ?>>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="tutupModal">Tutup</button>
                <button type="button" class="btn btn-warning" id="clearAllCheckbox">Cancel Semua Ceklist</button>
                <button type="button" class="btn btn-primary" id="simpanData">Simpan Data</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    <?php foreach ($materi_per_kelas as $kelasId => $kelas): ?>
        initializeDataTableUmum("#tblKelas-<?= $kelasId ?>", true, true);
    <?php endforeach; ?>

    // Tambahkan event listener untuk tab changes
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        // Dapatkan target tab yang aktif
        let targetTab = $(e.target).attr("href");

        // Cari table di dalam tab yang aktif
        let table = $(targetTab).find('table').DataTable();

        // Adjust columns untuk memastikan responsive bekerja
        table.columns.adjust().responsive.recalc();
    });

    $('#tutupModal').on('click', function() {
        var isChecked = $('#formTambahData input[type="checkbox"]:checked').length > 0;

        if (isChecked) {
            // Tambahkan konfirmasi sebelum menutup modal
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin menutup modal? Semua perubahan yang belum disimpan akan hilang.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, tutup!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Reset semua checkbox di dalam modal
                    $('#formTambahData input[type="checkbox"]').prop('checked', false);
                    $('#modalTambahData').modal('hide'); // Menutup modal
                }
            });
        } else {
            $('#modalTambahData').modal('hide'); // Menutup modal
        }
    });

    $('#simpanData').on('click', function() {
        // Validasi input
        var tingkatKelas = $('#tingkatKelas').val();
        var isChecked = $('#formTambahData input[type="checkbox"]:checked').length > 0;

        if (!tingkatKelas) {
            Swal.fire('Peringatan!', 'Tingkat Kelas harus dipilih.', 'warning');
            return; // Hentikan eksekusi jika select tidak dipilih
        }

        if (isChecked) {
            Swal.fire('Peringatan!', 'Anda harus memilih setidaknya satu materi.', 'warning');
            return; // Hentikan eksekusi jika tidak ada checkbox yang dicentang
        }

        // Menampilkan SweetAlert konfirmasi
        Swal.fire({
            title: 'Konfirmasi',
            text: "Apakah Anda yakin ingin menyimpan data?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, simpan!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Menampilkan loading spinner
                Swal.fire({
                    title: 'Menyimpan Data',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    },
                    // Tambahkan ikon animasi loading
                    icon: 'info', // Menambahkan ikon informasi
                    html: '<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>' // Menambahkan spinner
                });

                // Logika untuk menyimpan data
                $.ajax({
                    url: '<?= base_url('backend/KelasMateriPelajaran/add') ?>', // Ganti dengan URL yang sesuai
                    type: 'POST',
                    data: $('#formTambahData').serialize(),
                    success: function(response) {
                        Swal.fire({
                            title: response.status === 'success' ? 'Sukses!' : 'Gagal!',
                            text: response.message, // Menggunakan pesan dari respon
                            icon: response.status === 'success' ? 'success' : 'error',
                            timer: 2000, // Menambahkan timer selama 2 detik
                            showConfirmButton: true // Menampilkan tombol konfirmasi
                        }).then(() => {
                            if (response.status === 'success') {
                                $('#modalTambahData').modal('hide'); // Menutup modal
                                location.reload(); // Refresh halaman setelah konfirmasi
                            }
                        });
                        // Tambahkan logika tambahan jika diperlukan
                    },
                    error: function(response) {
                        Swal.fire(
                            'Gagal!',
                            response.responseJSON ? response.responseJSON.message : 'Terjadi kesalahan', // Menggunakan pesan dari respon
                            'error'
                        );
                    }
                });
            }
        });
    });

    $('#clearAllCheckbox').on('click', function() {
        // Cek apakah ada checkbox yang dicentang
        var isChecked = $('#formTambahData input[type="checkbox"]:checked').length > 0;

        if (isChecked) {
            // Tambahkan konfirmasi sebelum mereset semua checkbox
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin menghapus semua centang?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus centang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Reset semua checkbox di dalam modal
                    $('#formTambahData input[type="checkbox"]').prop('checked', false);
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>