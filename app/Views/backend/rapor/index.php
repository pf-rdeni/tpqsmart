<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<div class="col-12">
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
                                        <button type="button" class="btn btn-primary btn-sm btn-print-all" data-kelas="<?= $kelas->IdKelas ?>" data-semester="<?= $semester ?>">
                                            <i class="fas fa-print"></i> Cetak Semua Rapor Kelas <?= $kelas->NamaKelas ?>
                                        </button>
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
                                                <th>Aksi Catatan & Absensi</th>
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
                                                            <div class="btn-group" role="group">
                                                                <button type="button" class="btn btn-warning btn-sm btn-print-pdf" data-id="<?= $nilaiDetail->IdSantri ?>" data-semester="<?= $semester ?>">
                                                                    <i class="fas fa-print"></i> Cetak Rapor
                                                                </button>
                                                                <button type="button" class="btn btn-info btn-sm btn-ttd-walas" data-id="<?= $nilaiDetail->IdSantri ?>" data-semester="<?= $semester ?>" data-kelas="<?= $nilaiDetail->IdKelas ?>">
                                                                    <i class="fas fa-signature"></i> Ttd Walas
                                                                </button>
                                                                <button type="button" class="btn btn-success btn-sm btn-ttd-kepsek" data-id="<?= $nilaiDetail->IdSantri ?>" data-semester="<?= $semester ?>" data-kelas="<?= $nilaiDetail->IdKelas ?>">
                                                                    <i class="fas fa-signature"></i> Ttd Kepsek
                                                                </button>
                                                            </div>
                                                        </td>
                                                        <td><?= $nilaiDetail->NamaSantri ?></td>
                                                        <td><?= $nilaiDetail->IdSantri ?></td>
                                                        <td><?= $nilaiDetail->TotalNilai ?></td>
                                                        <td><?= $nilaiDetail->NilaiRataRata ?></td>
                                                        <td><?= $nilaiDetail->Rangking ?></td>
                                                        <td><?= $nilaiDetail->NamaKelas ?></td>
                                                        <td><?= $nilaiDetail->IdTahunAjaran ?></td>
                                                        <td>
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

        // Fungsi untuk mengecek role user dan mengaktifkan button signature
        function checkSignaturePermissions() {
            const guruKelasPermissions = <?= json_encode($guruKelasPermissions) ?>;
            const signatureStatus = <?= json_encode($signatureStatus) ?>;
            const currentGuruId = '<?= $currentGuruId ?>';

            // Debug logging
            console.log('Guru Kelas Permissions:', guruKelasPermissions);
            console.log('Current Guru ID:', currentGuruId);

            // Cek apakah data permission tersedia
            if (!guruKelasPermissions || guruKelasPermissions.length === 0) {
                console.error('Data permission guru kelas tidak tersedia');
                // Disable semua button signature
                $('.btn-ttd-walas, .btn-ttd-kepsek').prop('disabled', true)
                    .removeClass('btn-info btn-primary btn-success')
                    .addClass('btn-secondary')
                    .attr('title', 'Anda tidak memiliki akses untuk kelas ini');
                return;
            }

            // Buat mapping permission berdasarkan IdKelas dan cek Kepala TPQ
            const permissionMap = {};
            let isKepalaTpq = false;
            let kepalaTpqPermission = null;

            guruKelasPermissions.forEach(permission => {
                console.log('Processing permission:', permission);
                if (permission.IdKelas !== null && permission.IdKelas !== undefined) {
                    // Permission untuk kelas tertentu
                    permissionMap[permission.IdKelas] = permission;
                } else if (permission.NamaJabatan === 'Kepala TPQ') {
                    // Kepala TPQ - bisa akses semua kelas
                    isKepalaTpq = true;
                    kepalaTpqPermission = permission;
                    console.log('Found Kepala TPQ permission:', permission);
                }
            });

            console.log('Is Kepala TPQ:', isKepalaTpq);
            console.log('Permission Map:', permissionMap);

            // Cek permission untuk setiap button berdasarkan kelas
            $('.btn-ttd-walas').each(function() {
                const IdKelas = $(this).data('kelas');
                const IdSantri = $(this).data('id');
                const permission = permissionMap[IdKelas];

                // Cek apakah guru sudah menandatangani untuk santri ini
                const signatureKey = IdSantri + '_' + currentGuruId;
                const hasSigned = signatureStatus && signatureStatus[signatureKey] && signatureStatus[signatureKey].length > 0;

                if (permission && permission.NamaJabatan === 'Wali Kelas') {
                    if (hasSigned) {
                        // Guru sudah menandatangani, disable button - warna grey
                        $(this).prop('disabled', true)
                            .removeClass('btn-info btn-primary')
                            .addClass('btn-secondary')
                            .attr('title', 'Sudah ditandatangani')
                            .attr('data-toggle', 'tooltip')
                            .attr('data-placement', 'top');
                    } else {
                        // Guru belum menandatangani, enable button - warna biru menyala (primary)
                        $(this).prop('disabled', false)
                            .removeClass('btn-info btn-secondary')
                            .addClass('btn-primary')
                            .attr('title', 'Tandatangani sebagai Wali Kelas')
                            .removeAttr('data-toggle data-placement');
                    }
                } else {
                    // Bukan Wali Kelas (Guru Kelas atau lainnya)
                    if (hasSigned) {
                        // Sudah di TTD button grey
                        $(this).prop('disabled', true)
                            .removeClass('btn-info btn-primary')
                            .addClass('btn-secondary')
                            .attr('title', 'Sudah ditandatangani')
                            .attr('data-toggle', 'tooltip')
                            .attr('data-placement', 'top');
                    } else {
                        // Belum di TTD - warna biru tapi disable
                        $(this).prop('disabled', true)
                            .removeClass('btn-info btn-secondary')
                            .addClass('btn-primary')
                            .attr('title', 'Hanya Wali Kelas yang dapat menandatangani')
                            .attr('data-toggle', 'tooltip')
                            .attr('data-placement', 'top');
                    }
                }
            });

            $('.btn-ttd-kepsek').each(function() {
                const IdKelas = $(this).data('kelas');
                const IdSantri = $(this).data('id');
                const permission = permissionMap[IdKelas];

                console.log('Checking TTD Kepsek for:', {
                    IdKelas,
                    IdSantri,
                    permission,
                    isKepalaTpq
                });

                // Cek apakah guru sudah menandatangani untuk santri ini
                const signatureKey = IdSantri + '_' + currentGuruId;
                const hasSigned = signatureStatus && signatureStatus[signatureKey] && signatureStatus[signatureKey].length > 0;

                // Cek apakah guru adalah Kepala TPQ (prioritas utama)
                let canSign = false;
                let signTitle = '';

                if (isKepalaTpq) {
                    // Kepala TPQ dapat menandatangani semua kelas
                    canSign = true;
                    signTitle = 'Tandatangani sebagai Kepala TPQ';
                    console.log('Kepala TPQ can sign for all classes');
                } else if (permission && (permission.NamaJabatan === 'Kepala TPQ' || permission.NamaJabatan === 'Kepala Sekolah')) {
                    // Permission untuk kelas tertentu
                    canSign = true;
                    signTitle = 'Tandatangani sebagai ' + permission.NamaJabatan;
                    console.log('Has specific permission for this class:', permission.NamaJabatan);
                }

                if (canSign) {
                    if (hasSigned) {
                        // Guru sudah menandatangani, disable button
                        $(this).prop('disabled', true)
                            .removeClass('btn-secondary')
                            .addClass('btn-success')
                            .attr('title', 'Sudah ditandatangani')
                            .attr('data-toggle', 'tooltip')
                            .attr('data-placement', 'top');
                        console.log('Already signed, disabling button');
                    } else {
                        // Guru belum menandatangani, enable button
                        $(this).prop('disabled', false)
                            .removeClass('btn-secondary')
                            .addClass('btn-success')
                            .attr('title', signTitle)
                            .removeAttr('data-toggle data-placement');
                        console.log('Can sign, enabling button');
                    }
                } else {
                    $(this).prop('disabled', true)
                        .removeClass('btn-success')
                        .addClass('btn-secondary')
                        .attr('title', 'Hanya Kepala TPQ yang dapat menandatangani')
                        .attr('data-toggle', 'tooltip')
                        .attr('data-placement', 'top');
                    console.log('Cannot sign, no permission');
                }
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        }

        // Fungsi helper untuk menangani signature request
        function handleSignatureRequest(IdSantri, IdKelas, semester, signatureType) {
            const url = `<?= base_url('backend/rapor/ttd') ?>${signatureType}/${IdSantri}/${IdKelas}/${semester}`;

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Refresh halaman untuk update status signature
                            location.reload();
                        });
                    } else if (response.status === 'warning') {
                        Swal.fire({
                            title: 'Peringatan!',
                            text: response.message,
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    } else if (response.status === 'info') {
                        Swal.fire({
                            title: 'Informasi!',
                            text: response.message,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Ganti',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Kirim request untuk mengganti signature
                                $.ajax({
                                    url: url,
                                    type: 'POST',
                                    data: {
                                        replace: true,
                                        existing_id: response.existing_id
                                    },
                                    dataType: 'json',
                                    success: function(replaceResponse) {
                                        if (replaceResponse.status === 'success') {
                                            Swal.fire({
                                                title: 'Berhasil!',
                                                text: 'Tanda tangan berhasil diganti.',
                                                icon: 'success',
                                                confirmButtonText: 'OK'
                                            }).then(() => {
                                                // Refresh halaman untuk update status signature
                                                location.reload();
                                            });
                                        } else {
                                            Swal.fire({
                                                title: 'Error!',
                                                text: replaceResponse.message,
                                                icon: 'error',
                                                confirmButtonText: 'OK'
                                            });
                                        }
                                    },
                                    error: function() {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: 'Terjadi kesalahan saat mengganti tanda tangan.',
                                            icon: 'error',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                });
                            }
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
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat memproses request: ' + error,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        // Panggil fungsi pengecekan permission saat halaman dimuat
        checkSignaturePermissions();
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

        // Handle Ttd Walas button click
        $(document).on('click', '.btn-ttd-walas', function() {
            // Cek apakah button disabled
            if ($(this).prop('disabled')) {
                return false;
            }

            const IdSantri = $(this).data('id');
            const semester = $(this).data('semester');
            const IdKelas = $(this).data('kelas');
            Swal.fire({
                title: 'Tanda Tangan Wali Kelas',
                text: 'Apakah Anda yakin ingin menandatangani rapor ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Tandatangani',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    handleSignatureRequest(IdSantri, IdKelas, semester, 'Walas');
                }
            });
        });

        // Handle Ttd Kepsek button click
        $(document).on('click', '.btn-ttd-kepsek', function() {
            // Cek apakah button disabled
            if ($(this).prop('disabled')) {
                return false;
            }

            const IdSantri = $(this).data('id');
            const semester = $(this).data('semester');
            const IdKelas = $(this).data('kelas');
            Swal.fire({
                title: 'Tanda Tangan Kepala Sekolah',
                text: 'Apakah Anda yakin ingin menandatangani rapor ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Tandatangani',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    handleSignatureRequest(IdSantri, IdKelas, semester, 'Kepsek');
                }
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
            const formData = $('#formCatatan').serialize();

            $.ajax({
                url: '<?= base_url("backend/rapor/saveCatatan") ?>',
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
                        <select class="form-control" id="selectCatatanSource" name="CatatanSource">
                            <option value="">-- Pilih Sumber Catatan --</option>
                        </select>
                        <small class="form-text text-muted">Pilih catatan dari kriteria yang tersedia. Secara default menggunakan yang paling spesifik.</small>
                    </div>

                    <div class="form-group">
                        <label>Pilih Sumber Catatan</label>
                        <select class="form-control" id="selectCatatanSource" name="CatatanSource" style="display: none;">
                            <option value="">-- Pilih Sumber Catatan --</option>
                        </select>
                        <small class="form-text text-muted">Pilih catatan dari kriteria yang tersedia. Secara default menggunakan yang paling spesifik.</small>
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