<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
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
                                <?php foreach ($listKelas as $kelas) : ?>
                                    <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                                        <a class="nav-link border-white text-center <?= $kelas->IdKelas === $listKelas[0]->IdKelas ? 'active' : '' ?>"
                                            id="tab-<?= $kelas->IdKelas ?>"
                                            data-toggle="tab"
                                            href="#kelas-<?= $kelas->IdKelas ?>"
                                            role="tab"
                                            aria-controls="kelas-<?= $kelas->IdKelas ?>"
                                            aria-selected="<?= $kelas->IdKelas === $listKelas[0]->IdKelas ? 'true' : 'false' ?>">
                                            <?= $kelas->NamaKelas ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <br>
                        <div class="card-body">
                            <div class="tab-content" id="kelasTabContent">
                                <?php foreach ($listKelas as $kelas) : ?>
                                    <div class="tab-pane fade <?= $kelas->IdKelas === $listKelas[0]->IdKelas ? 'show active' : '' ?>"
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
                                                    $MainDataNilai = $nilai->getResult();
                                                    $no = 1;
                                                    foreach ($MainDataNilai as $DataNilai) :
                                                        if ($DataNilai->IdKelas == $kelas->IdKelas) :
                                                    ?>
                                                            <tr>
                                                                <td><?= $no++ ?></td>
                                                                <td>
                                                                    <div class="btn-group" role="group">
                                                                        <button type="button" class="btn btn-warning btn-sm btn-print-pdf" data-id="<?= $DataNilai->IdSantri ?>" data-semester="<?= $semester ?>">
                                                                            <i class="fas fa-print"></i> Cetak Rapor
                                                                        </button>
                                                                        <button type="button" class="btn btn-info btn-sm btn-ttd-walas" data-id="<?= $DataNilai->IdSantri ?>" data-semester="<?= $semester ?>">
                                                                            <i class="fas fa-signature"></i> Ttd Walas
                                                                        </button>
                                                                        <button type="button" class="btn btn-success btn-sm btn-ttd-kepsek" data-id="<?= $DataNilai->IdSantri ?>" data-semester="<?= $semester ?>">
                                                                            <i class="fas fa-signature"></i> Ttd Kepsek
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                                <td><?= $DataNilai->NamaSantri ?></td>
                                                                <td><?= $DataNilai->IdSantri ?></td>
                                                                <td><?= $DataNilai->TotalNilai ?></td>
                                                                <td><?= $DataNilai->NilaiRataRata ?></td>
                                                                <td><?= $DataNilai->Rangking ?></td>
                                                                <td><?= $DataNilai->NamaKelas ?></td>
                                                                <td><?= $DataNilai->IdTahunAjaran ?></td>
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
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable untuk setiap kelas
        <?php foreach ($listKelas as $kelasId => $IdKelas): ?>
            initializeDataTableUmum("#tableSantri-<?= $kelasId ?>", true, true);
        <?php endforeach; ?>

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
            const IdSantri = $(this).data('id');
            const semester = $(this).data('semester');

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
                    // Redirect to signature page for wali kelas
                    window.location.href = `<?= base_url('backend/rapor/ttdWalas') ?>/${IdSantri}/${semester}`;
                } else {
                    Swal.fire({
                        title: 'Tanda Tangan Wali Kelas',
                        text: 'Tanda tangan wali kelas gagal disimpan',
                        icon: 'error'
                    });
                }
            });
        });

        // Handle Ttd Kepsek button click
        $(document).on('click', '.btn-ttd-kepsek', function() {
            const IdSantri = $(this).data('id');
            const semester = $(this).data('semester');

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
                    // Redirect to signature page for kepala sekolah
                    window.location.href = `<?= base_url('backend/rapor/ttdKepsek') ?>/${IdSantri}/${semester}`;
                } else {
                    Swal.fire({
                        title: 'Tanda Tangan Kepala Sekolah',
                        text: 'Tanda tangan kepala sekolah gagal disimpan',
                        icon: 'error'
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>