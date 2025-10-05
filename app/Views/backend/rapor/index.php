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
                                                        <th>Kelas</th>
                                                        <th>Tahun Ajaran</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $no = 1;
                                                    foreach ($listSantri as $santri) :
                                                        if ($santri['IdKelas'] === $kelas->IdKelas) :
                                                    ?>
                                                            <tr>
                                                                <td><?= $no++ ?></td>
                                                                <td>
                                                                    <button type="button" class="btn btn-warning btn-sm btn-print-pdf" data-id="<?= $santri['IdSantri'] ?>" data-semester="<?= $semester ?>">
                                                                        <i class="fas fa-print"></i> Cetak Rapor
                                                                    </button>
                                                                </td>
                                                                <td><?= $santri['NamaSantri'] ?></td>
                                                                <td><?= $santri['IdSantri'] ?></td>
                                                                <td><?= $santri['NamaKelas'] ?></td>
                                                                <td><?= $kelas->IdTahunAjaran ?></td>
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
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>