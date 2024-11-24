<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar Santri Per Kelas <b>TPQ <?= $namaTpq['NamaTpq'] . ' - ' . $namaTpq['Alamat'] ?></b></h3>
                <div class="d-flex">
                    <a href="<?= base_url('backend/santri/createEmisStep') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i><span class="d-none d-md-inline">&nbsp;Daftar Santri Baru</span>
                    </a>
                    <a href="<?= base_url('backend/santri/showSantriBaru') ?>" class="btn btn-info ml-2">
                        <i class="fas fa-list"></i><span class="d-none d-md-inline">&nbsp;Data Santri Baru</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <br>
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs flex-wrap justify-content-start justify-content-md-between" id="custom-tabs-one-tab" role="tablist">
                        <li class="nav-item flex-fill">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tk-tab" data-toggle="pill" href="#custom-tabs-one-tk" role="tab" aria-controls="custom-tabs-one-tk" aria-selected="false">TK</a>
                        </li>
                        <li class="nav-item flex-fill">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tka-tab" data-toggle="pill" href="#custom-tabs-one-tka" role="tab" aria-controls="custom-tabs-one-tka" aria-selected="false">TKA</a>
                        </li>
                        <li class="nav-item flex-fill">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tkb-tab" data-toggle="pill" href="#custom-tabs-one-tkb" role="tab" aria-controls="custom-tabs-one-tkb" aria-selected="false">TKB</a>
                        </li>
                        <li class="nav-item flex-fill">
                            <a class="nav-link border-white text-center active" id="custom-tabs-one-tpq1-tab" data-toggle="pill" href="#custom-tabs-one-tpq1" role="tab" aria-controls="custom-tabs-one-tpq1" aria-selected="true">TPQ1</a>
                        </li>
                        <li class="nav-item flex-fill">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tpq2-tab" data-toggle="pill" href="#custom-tabs-one-tpq2" role="tab" aria-controls="custom-tabs-one-tpq2" aria-selected="false">TPQ2</a>
                        </li>
                        <li class="nav-item flex-fill">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tpq3-tab" data-toggle="pill" href="#custom-tabs-one-tpq3" role="tab" aria-controls="custom-tabs-one-tpq3" aria-selected="false">TPQ3</a>
                        </li>
                        <li class="nav-item flex-fill">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tpq4-tab" data-toggle="pill" href="#custom-tabs-one-tpq4" role="tab" aria-controls="custom-tabs-one-tpq4" aria-selected="false">TPQ4</a>
                        </li>
                        <li class="nav-item flex-fill">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tpq5-tab" data-toggle="pill" href="#custom-tabs-one-tpq5" role="tab" aria-controls="custom-tabs-one-tpq5" aria-selected="false">TPQ5</a>
                        </li>
                        <li class="nav-item flex-fill">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tpq6-tab" data-toggle="pill" href="#custom-tabs-one-tpq6" role="tab" aria-controls="custom-tabs-one-tpq6" aria-selected="false">TPQ6</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        <div class="tab-pane fade show" id="custom-tabs-one-tk" role="tabpanel" aria-labelledby="custom-tabs-one-tk-tab">
                            <?= renderTpqTable($dataSantriTK, 1) ?>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-tka" role="tabpanel" aria-labelledby="custom-tabs-one-tka-tab">
                            <?= renderTpqTable($dataSantriTKA, 2) ?>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-tkb" role="tabpanel" aria-labelledby="custom-tabs-one-tkb-tab">
                            <?= renderTpqTable($dataSantriTKB, 3) ?>
                        </div>
                        <div class="tab-pane fade show active" id="custom-tabs-one-tpq1" role="tabpanel" aria-labelledby="custom-tabs-one-tpq1-tab">
                            <?= renderTpqTable($dataSantriTPQ1, 3) ?>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-tpq2" role="tabpanel" aria-labelledby="custom-tabs-one-tpq2-tab">
                            <?= renderTpqTable($dataSantriTPQ2, 4) ?>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-tpq3" role="tabpanel" aria-labelledby="custom-tabs-one-tpq3-tab">
                            <?= renderTpqTable($dataSantriTPQ3, 5) ?>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-tpq4" role="tabpanel" aria-labelledby="custom-tabs-one-tpq4-tab">
                            <?= renderTpqTable($dataSantriTPQ4, 6) ?>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-tpq5" role="tabpanel" aria-labelledby="custom-tabs-one-tpq5-tab">
                            <?= renderTpqTable($dataSantriTPQ5, 7) ?>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-tpq6" role="tabpanel" aria-labelledby="custom-tabs-one-tpq6-tab">
                            <?= renderTpqTable($dataSantriTPQ6, 8) ?>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
</div>
<?php

// fungsi untuk menampilkan data table per kelas tpq
function renderTpqTable($dataTpq, $tpqLevel)
{
    ob_start();
?>
    <table id="tableSantriBaruPerKelasTpq<?= $tpqLevel ?>" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Profil</th>
                <th>Nama</th>
                <th>Detail</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dataTpq as $santri) : ?>
                <tr>
                    <td>
                        <?php
                        $uploadPath = (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/' : base_url('uploads/santri/');
                        ?>
                        <img src="<?= $santri['PhotoProfil'] ? $uploadPath . $santri['PhotoProfil'] : base_url('images/no-photo.jpg'); ?>"
                            alt="PhotoProfil"
                            class="img-fluid popup-image"
                            width="30"
                            height="40"
                            onmouseover="showPopup(this)"
                            onmouseout="hidePopup(this)"
                            onclick="showPopup(this)"
                            style="cursor: pointer;">
                        <div class="image-popup" style="display: none; position: absolute; z-index: 1000;">
                            <img src="<?= $santri['PhotoProfil'] ? $uploadPath . $santri['PhotoProfil'] : base_url('images/no-photo.jpg'); ?>"
                                alt="PhotoProfil"
                                width="200"
                                height="250">
                        </div>
                    </td>
                    <td><?= $santri['NamaSantri']; ?></td>
                    <td>
                        <a href="javascript:void(0)" onclick="viewDetail(<?= $santri['IdSantri'] ?>)" class="btn btn-success btn-sm"><i class="fas fa-info-circle"></i></a>
                        <a href="javascript:void(0)" onclick="printPdf(<?= $santri['IdSantri'] ?>)" class="btn btn-primary btn-sm"><i class="fas fa-print"></i></a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Profil</th>
                <th>Nama</th>
                <th>Detail</th>
            </tr>
        </tfoot>
    </table>
<?php
    return ob_get_clean();
}
?>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    // fungsi untuk menampilkan popup image
    function showPopup(img) {
        const popup = img.nextElementSibling;
        popup.style.display = 'block';
    }
    // fungsi untuk menutup popup image
    function hidePopup(img) {
        const popup = img.nextElementSibling;
        popup.style.display = 'none';
    }

    // fungsi untuk inisialisasi data table
    function initializeDataTable(selector, paging = true, buttons = [], options = {}) {
        $(selector).DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "paging": paging,
            "buttons": buttons,
            ...options
        }).buttons().container().appendTo(`${selector}_wrapper .col-md-6:eq(0)`);
    }

    // Inisialisasi untuk semua tabel TPQ (1-6)
    for (let i = 1; i <= 9; i++) {
        initializeDataTable(`#tableSantriBaruPerKelasTpq${i}`, false, [], {
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    }

    /*=== fungsi untuk mencetak pdf ===*/
    function printPdf(idSantri) {
        Swal.fire({
            title: 'Cetak PDF',
            text: "Apakah anda yakin ingin mencetak data santri ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Cetak!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(`<?= base_url('backend/santri/generatePDFSantriBaru/') ?>${idSantri}`, '_blank');
            }
        });
    }

    /*=== fungsi untuk melihat detail santri ===*/
    function viewDetail(idSantri) {
        Swal.fire({
            title: 'Detail Santri',
            text: "Informasi saat ini belum tersedia.",
            icon: 'info',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    }
</script>
<?= $this->endSection(); ?>