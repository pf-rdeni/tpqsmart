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
    });
</script>
<?= $this->endSection() ?>