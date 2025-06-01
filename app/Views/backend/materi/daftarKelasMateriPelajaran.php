<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content') ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    Daftar Kelas Materi Pelajaran
                </h3>

                <div class="card-tools d-flex flex-column flex-md-row gap-3">
                    <a href="<?php echo base_url('backend/materiPelajaran/showMateriPelajaran') ?>" class="btn btn-info btn-sm w-100 flex-fill">
                        <i class="fas fa-list"></i> Daftar Materi
                    </a>

                    <a href="#" class="btn btn-primary btn-sm w-100 flex-fill" data-toggle="modal" data-target="#modalTambahData">
                        <i class="fas fa-gear"></i> Atur Materi
                    </a>
                    <a href="#" class="btn btn-warning btn-sm w-100 flex-fill" onclick="updateDataMateriPenilaian()">
                        <i class="fas fa-sync"></i> Perbarui Materi
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="card card-primary card-tabs">
                <!-- Tab Navigation -->
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs flex-wrap justify-content-start justify-content-md-between" id="kelasTab" role="tablist">
                        <?php foreach ($dataMateriPerKelas as $kelasId => $kelas): ?>
                            <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                                <a class="nav-link border-white text-center <?= $kelasId === array_key_first($dataMateriPerKelas) ? 'active' : '' ?>"
                                    id="tab-<?= $kelasId ?>"
                                    data-toggle="tab"
                                    href="#kelas-<?= $kelasId ?>"
                                    role="tab"
                                    aria-controls="kelas-<?= $kelasId ?>"
                                    aria-selected="<?= $kelasId === array_key_first($dataMateriPerKelas) ? 'true' : 'false' ?>">
                                    <?= $kelas['nama_kelas'] ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <br>
                <div class="card-body">
                    <div class="tab-content" id="kelasTabContent">
                        <?php foreach ($dataMateriPerKelas as $kelasId => $kelas): ?>
                            <div class="tab-pane fade <?= $kelasId === array_key_first($dataMateriPerKelas) ? 'show active' : '' ?>"
                                id="kelas-<?= $kelasId ?>"
                                role="tabpanel"
                                aria-labelledby="tab-<?= $kelasId ?>">
                                <table class="table table-bordered table-striped" id="tblKelas-<?= $kelasId ?>">
                                    <thead>
                                        <tr>
                                            <th>Id Materi</th>
                                            <th>Kategori</th>
                                            <th>Materi</th>
                                            <th>S.Ganjil</th>
                                            <th>S.Genap</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($kelas['materi']  as $materi): ?>
                                            <tr>
                                                <td><?= $materi['IdMateri'] ?></td>
                                                <td><?= $materi['Kategori'] ?></td>
                                                <td><?= $materi['NamaMateri'] ?></td>
                                                <td>
                                                    <input type="checkbox"
                                                        <?= $materi['SemesterGanjil'] ? 'checked' : '' ?>
                                                        onchange="confirmCheckboxChange(this, '<?= $materi['Id'] ?>', 'SemesterGanjil','<?= addslashes($materi['NamaMateri']) ?>')">
                                                </td>
                                                <td>
                                                    <input type="checkbox"
                                                        <?= $materi['SemesterGenap'] ? 'checked' : '' ?>
                                                        onchange="confirmCheckboxChange(this, '<?= $materi['Id'] ?>', 'SemesterGenap', '<?= addslashes($materi['NamaMateri']) ?>')">
                                                </td>
                                                <td>
                                                    <a class="btn btn-danger btn-sm" onclick="confirmDelete('<?= $materi['Id'] ?>', '<?= addslashes($materi['NamaMateri']) ?>')">
                                                        <i class="fas fa-trash fa-sm"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Id Materi</th>
                                            <th>Kategori</th>
                                            <th>Materi</th>
                                            <th>S.Ganjil</th>
                                            <th>S.Genap</th>
                                            <th>Aksi</th>
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
                        <label for="IdKelas">Tingkat Kelas</label>
                        <select class="form-control" id="IdKelas" name="IdKelas" required>
                            <option value="">Pilih Tingkat Kelas</option>
                            <?php foreach ($dataKelas as $tingkat): ?>
                                <option value="<?= $tingkat['IdKelas'] ?>"><?= $tingkat['NamaKelas'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="IdTpq">Nama TPQ</label>
                        <select class="form-control" id="IdTpq" name="IdTpq" required <?= count($dataTpq) === 1 ? 'readonly' : '' ?>>
                            <?php if (count($dataTpq) > 1): ?>
                                <option value="">Pilih Nama TPQ</option>
                            <?php endif; ?>
                            <?php foreach ($dataTpq as $tpq): ?>
                                <option value="<?= $tpq['IdTpq'] ?>"
                                    <?= (count($dataTpq) === 1 || $tpq['IdTpq'] == $defaultTpq) ? 'selected' : '' ?>>
                                    <?= $tpq['NamaTpq'] ?>
                                </option>
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
                                            <th>Id Materi</th>
                                            <th>Nama Materi</th>
                                            <th>Semester</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dataMateriPelajaran as $item): ?>
                                            <?php if ($item['Kategori'] === $materi['Kategori']): ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="Materi[<?= $item['Id'] ?>][IdMateri]" value="<?= $item['IdMateri'] ?>">
                                                        <?= $item['IdMateri'] ?>
                                                    </td>
                                                    <td><?= $item['NamaMateri'] ?></td>

                                                    <td>
                                                        <input type="checkbox" id="SemesterGanjil<?= $item['Id'] ?>" name="Materi[<?= $item['Id'] ?>][SemesterGanjil]" value="1" onchange="this.value = this.checked ? 1 : 0;"> GANJIL
                                                        <input type="checkbox" id="SemesterGenap<?= $item['Id'] ?>" name="Materi[<?= $item['Id'] ?>][SemesterGenap]" value="1" onchange="this.value = this.checked ? 1 : 0;"> GENAP
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
                <button type="button" class="btn btn-primary" id="simpanData" onclick="simpanData()">Simpan Data</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Data -->
<div class="modal fade" id="modalUpdateData" tabindex="-1" role="dialog" aria-labelledby="modalUpdateDataLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modalUpdateDataLabel">Rangkuman Perubahan Materi</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle"></i> Fungsi ini akan memeriksa perubahan materi yang telah dilakukan pada tabel nilai semua kelas dan semua santri untuk tahun ajaran dan semester saat ini. Jika ada perubahan, materi yang tidak valid akan ditampilkan di bawah.
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0">Materi yang Akan Dihapus</h6>
                            </div>
                            <div class="card-body">
                                <div id="materiToDeleteList" class="table-responsive">
                                    <table class="table table-sm" id="tblMateriToDelete">
                                        <thead>
                                            <tr>
                                                <th>Kelas</th>
                                                <th>Nama Materi</th>
                                                <th>Semester</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data akan diisi melalui JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="noMateriToDelete" class="alert alert-info" style="display: none;">
                                    Tidak ada materi yang perlu dihapus
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">Materi yang Akan Ditambahkan</h6>
                            </div>
                            <div class="card-body">
                                <div id="materiToAddList" class="table-responsive">
                                    <table class="table table-sm" id="tblMateriToAdd">
                                        <thead>
                                            <tr>
                                                <th>Kelas</th>
                                                <th>Nama Materi</th>
                                                <th>Semester</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data akan diisi melalui JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="noMateriToAdd" class="alert alert-info" style="display: none;">
                                    Tidak ada materi baru yang perlu ditambahkan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="btnHapusMateri" onclick="prosesHapusMateri()">
                    <i class="fas fa-trash"></i> Hapus Materi
                </button>
                <button type="button" class="btn btn-success" id="btnTambahMateri" onclick="prosesTambahMateri()">
                    <i class="fas fa-plus"></i> Tambah Materi
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Initial datatabel untuk lihat list materi per kelas
    <?php foreach ($dataMateriPerKelas as $kelasId => $kelas): ?>
        initializeDataTableUmum("#tblKelas-<?= $kelasId ?>", true, true);
    <?php endforeach; ?>

    // Initial datatabel untuk lihat list materi yang akan dihapus
    initializeDataTableUmum("#tblMateriToDelete", true, true);

    // Initial datatabel untuk lihat list materi yang akan ditambahkan
    initializeDataTableUmum("#tblMateriToAdd", true, true);

    // Menutup modal
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

    // Simpan modal tambah materi
    function simpanData() {
        // Validasi input
        var tingkatKelas = $('#IdKelas').val();
        var idTpq = $('#IdTpq').val();
        var isChecked = $('#formTambahData input[type="checkbox"][name^="IdMateri"]:checked').length > 0;
        isChecked = true;

        if (!tingkatKelas) {
            Swal.fire('Peringatan!', 'Tingkat Kelas harus dipilih.', 'warning');
            return; // Hentikan eksekusi jika select tidak dipilih
        }

        if (!idTpq) {
            Swal.fire('Peringatan!', 'Nama TPQ harus dipilih.', 'warning');
            return; // Hentikan eksekusi jika IdTpq tidak dipilih
        }

        if (!isChecked) {
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
                // Logika untuk menyimpan data
                $.ajax({
                    url: '<?= base_url('backend/KelasMateriPelajaran/add') ?>',
                    type: 'POST',
                    data: $('#formTambahData').serialize(),
                    success: function(response) {
                        Swal.fire({
                            title: response.status === 'success' ? 'Sukses!' : 'Gagal!',
                            text: response.message,
                            icon: response.status === 'success' ? 'success' : 'error',
                            timer: 2000,
                            showConfirmButton: true
                        }).then(() => {
                            if (response.status === 'success') {
                                $('#modalTambahData').modal('hide');
                                location.reload();
                            }
                        });
                    },
                    error: function(response) {
                        console.error('Error response:', response); // Tambahkan log untuk melihat respon error
                        Swal.fire(
                            'Gagal!',
                            response.responseJSON ? response.responseJSON.message : 'Terjadi kesalahan',
                            'error'
                        );
                    }
                });
                // Menampilkan loading spinner
                Swal.fire({
                    title: 'Menyimpan Data',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    icon: 'info',
                    html: '<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>', // Menambahkan spinner

                    onBeforeOpen: () => {
                        Swal.showLoading();
                    }

                });
            }
        });
    }

    // Clear all modal cheklist tambah materi
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

    // Fungsi untuk konfirmasi perubahan checkbox
    function confirmCheckboxChange(checkbox, id, semester, namaMateri) {
        const isChecked = checkbox.checked ? 1 : 0;
        const action = isChecked ? "Active" : "Tidak Active";
        const namaSemester = semester === 'SemesterGanjil' ? 'Semester Ganjil' : "Semester Genap";

        Swal.fire({
            title: 'Konfirmasi Perubahan',
            text: `Apakah Anda yakin materi ${namaMateri} ini ingin diset ${action} untuk ${namaSemester}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, simpan!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim data ke kontroler
                $.ajax({
                    url: '<?= base_url('backend/KelasMateriPelajaran/update') ?>', // Ganti dengan URL yang sesuai
                    type: 'POST',
                    data: {
                        Id: id,
                        SemesterStatus: isChecked,
                        NamaSemester: semester,
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Sukses!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(response) {
                        Swal.fire(
                            'Gagal!',
                            response.responseJSON ? response.responseJSON.message : 'Terjadi kesalahan',
                            'error'
                        );
                        // Kembalikan checkbox ke status sebelumnya jika gagal
                        checkbox.checked = !isChecked;
                    }
                });
                // Menampilkan loading spinner
                Swal.fire({
                    title: 'Menyimpan Data',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    icon: 'info',
                    html: '<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>', // Menambahkan spinner

                    onBeforeOpen: () => {
                        Swal.showLoading();
                    }

                });
            } else {
                // Kembalikan checkbox ke status sebelumnya jika tidak dikonfirmasi
                checkbox.checked = !isChecked;
            }
        });
    }

    // FUngsi Delet Materi kelas
    function confirmDelete(id, namaMateri) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: `Apakah Anda yakin ingin menghapus materi ${namaMateri}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Menampilkan loading spinner
                Swal.fire({
                    title: 'Menghapus Data',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    icon: 'info',
                    html: '<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>',
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Jika dikonfirmasi, lakukan penghapusan
                $.ajax({
                    url: '<?= base_url('backend/KelasMateriPelajaran/delete/') ?>' + id,
                    type: 'POST',
                    success: function(response) {
                        Swal.fire({
                            title: response.status === 'success' ? 'Sukses!' : 'Gagal!',
                            text: response.message,
                            icon: response.status === 'success' ? 'success' : 'error',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            if (response.status === 'success') {
                                location.reload(); // Refresh halaman setelah konfirmasi
                            }
                        });
                    },
                    error: function() {
                        Swal.fire(
                            'Gagal!',
                            response.responseJSON ? response.responseJSON.message : 'Terjadi kesalahan', // Menggunakan pesan dari respon
                            'error'
                        );
                    }
                });
            }
        });
    }

    // fungsi untuk perbarui data materi kelas pada tabel nilai semua kelas dan semua santri untuk tahun ajaran dan semester saat ini
    function updateDataMateriPenilaian() {
        // Menampilkan loading spinner
        Swal.fire({
            title: 'Memeriksa Perubahan',
            text: 'Mohon tunggu...',
            allowOutsideClick: false,
            icon: 'info',
            html: '<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>',
            onBeforeOpen: () => {
                Swal.showLoading();
            }
        });

        // Mengirim permintaan AJAX untuk memeriksa perubahan
        $.ajax({
            url: '<?= base_url('backend/KelasMateriPelajaran/updateMateriPelajaranPadaTabelNilai') ?>',
            type: 'POST',
            success: function(response) {
                if (response.status === 'success') {
                    // Tampilkan modal dengan data perubahan
                    displayChanges(response.data);
                    $('#modalUpdateData').modal('show');
                    // jika sudah berhasil di load, hapus loading
                    Swal.close();
                } else {
                    Swal.fire('Error!', 'Gagal memeriksa perubahan', 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Terjadi kesalahan saat memeriksa perubahan', 'error');
            }
        });
    }

    function displayChanges(data) {
        // Tampilkan materi yang akan dihapus
        const materiToDelete = data.materiToDelete;
        const materiToDeleteList = $('#materiToDeleteList tbody');

        // Destroy DataTable jika sudah diinisialisasi
        if ($.fn.DataTable.isDataTable('#tblMateriToDelete')) {
            $('#tblMateriToDelete').DataTable().destroy();
        }

        // Bersihkan tabel
        materiToDeleteList.empty();

        if (materiToDelete.length > 0) {
            $('#noMateriToDelete').hide();
            materiToDeleteList.show();

            // Kelompokkan data berdasarkan kelas dan materi
            const groupedByKelasMateri = {};
            materiToDelete.forEach(materi => {
                const key = `${materi.NamaKelas}_${materi.NamaMateri}`;
                if (!groupedByKelasMateri[key]) {
                    groupedByKelasMateri[key] = {
                        kelas: materi.NamaKelas,
                        materi: materi.NamaMateri,
                        semester: []
                    };
                }
                if (!groupedByKelasMateri[key].semester.includes(materi.Semester)) {
                    groupedByKelasMateri[key].semester.push(materi.Semester);
                }
            });

            // Tampilkan data yang sudah dikelompokkan
            Object.values(groupedByKelasMateri).forEach(item => {
                materiToDeleteList.append(`
                    <tr>
                        <td>${item.kelas}</td>
                        <td>${item.materi}</td>
                        <td>${item.semester.join(', ')}</td>
                    </tr>
                `);
            });

            // Reinitialize DataTable
            $('#tblMateriToDelete').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": true
            });

            $('#btnHapusMateri').show();
        } else {
            $('#noMateriToDelete').show();
            materiToDeleteList.hide();
            $('#btnHapusMateri').hide();
        }

        // Tampilkan materi yang akan ditambahkan
        const materiToAdd = data.materiToAdd;
        const materiToAddList = $('#materiToAddList tbody');

        // Destroy DataTable jika sudah diinisialisasi
        if ($.fn.DataTable.isDataTable('#tblMateriToAdd')) {
            $('#tblMateriToAdd').DataTable().destroy();
        }

        // Bersihkan tabel
        materiToAddList.empty();

        if (materiToAdd.length > 0) {
            $('#noMateriToAdd').hide();
            materiToAddList.show();

            // Kelompokkan data berdasarkan kelas dan materi
            const groupedByKelasMateri = {};
            materiToAdd.forEach(materi => {
                const key = `${materi.NamaKelas}_${materi.NamaMateri}`;
                if (!groupedByKelasMateri[key]) {
                    groupedByKelasMateri[key] = {
                        kelas: materi.NamaKelas,
                        materi: materi.NamaMateri,
                        semester: []
                    };
                }
                if (!groupedByKelasMateri[key].semester.includes(materi.Semester)) {
                    groupedByKelasMateri[key].semester.push(materi.Semester);
                }
            });

            // Tampilkan data yang sudah dikelompokkan
            Object.values(groupedByKelasMateri).forEach(item => {
                materiToAddList.append(`
                    <tr>
                        <td>${item.kelas}</td>
                        <td>${item.materi}</td>
                        <td>${item.semester.join(', ')}</td>
                    </tr>
                `);
            });

            // Reinitialize DataTable
            $('#tblMateriToAdd').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": true
            });

            $('#btnTambahMateri').show();
        } else {
            $('#noMateriToAdd').show();
            materiToAddList.hide();
            $('#btnTambahMateri').hide();
        }
    }

    function prosesHapusMateri() {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus materi yang tidak valid?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Menghapus Materi',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    icon: 'info',
                    html: '<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>',
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Proses penghapusan
                $.ajax({
                    url: '<?= base_url('backend/KelasMateriPelajaran/prosesHapusMateri') ?>',
                    type: 'POST',
                    success: function(response) {
                        Swal.fire({
                            title: response.status === 'success' ? 'Sukses!' : 'Gagal!',
                            text: response.message,
                            icon: response.status === 'success' ? 'success' : 'error',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            if (response.status === 'success') {
                                location.reload();
                            }
                        });
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus materi', 'error');
                    }
                });
            }
        });
    }

    function prosesTambahMateri() {
        Swal.fire({
            title: 'Konfirmasi Tambah',
            text: 'Apakah Anda yakin ingin menambahkan materi baru?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, tambahkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Menambahkan Materi',
                    text: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    icon: 'info',
                    html: '<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>',
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Proses penambahan
                $.ajax({
                    url: '<?= base_url('backend/KelasMateriPelajaran/prosesTambahMateri') ?>',
                    type: 'POST',
                    success: function(response) {
                        Swal.fire({
                            title: response.status === 'success' ? 'Sukses!' : 'Gagal!',
                            text: response.message,
                            icon: response.status === 'success' ? 'success' : 'error',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            if (response.status === 'success') {
                                location.reload();
                            }
                        });
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menambahkan materi', 'error');
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>