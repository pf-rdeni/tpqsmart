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
                        <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                            <a class="nav-link border-white text-center active" id="custom-tabs-one-all-tab" data-toggle="pill" href="#custom-tabs-one-all" role="tab" aria-controls="custom-tabs-one-all" aria-selected="true">Semua</a>
                        </li>
                        <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tk-tab" data-toggle="pill" href="#custom-tabs-one-tk" role="tab" aria-controls="custom-tabs-one-tk" aria-selected="false">TK</a>
                        </li>
                        <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tka-tab" data-toggle="pill" href="#custom-tabs-one-tka" role="tab" aria-controls="custom-tabs-one-tka" aria-selected="false">TKA</a>
                        </li>
                        <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tkb-tab" data-toggle="pill" href="#custom-tabs-one-tkb" role="tab" aria-controls="custom-tabs-one-tkb" aria-selected="false">TKB</a>
                        </li>
                        <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tpq1-tab" data-toggle="pill" href="#custom-tabs-one-tpq1" role="tab" aria-controls="custom-tabs-one-tpq1" aria-selected="false">TPQ1</a>
                        </li>
                        <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tpq2-tab" data-toggle="pill" href="#custom-tabs-one-tpq2" role="tab" aria-controls="custom-tabs-one-tpq2" aria-selected="false">TPQ2</a>
                        </li>
                        <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tpq3-tab" data-toggle="pill" href="#custom-tabs-one-tpq3" role="tab" aria-controls="custom-tabs-one-tpq3" aria-selected="false">TPQ3</a>
                        </li>
                        <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tpq4-tab" data-toggle="pill" href="#custom-tabs-one-tpq4" role="tab" aria-controls="custom-tabs-one-tpq4" aria-selected="false">TPQ4</a>
                        </li>
                        <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tpq5-tab" data-toggle="pill" href="#custom-tabs-one-tpq5" role="tab" aria-controls="custom-tabs-one-tpq5" aria-selected="false">TPQ5</a>
                        </li>
                        <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                            <a class="nav-link border-white text-center" id="custom-tabs-one-tpq6-tab" data-toggle="pill" href="#custom-tabs-one-tpq6" role="tab" aria-controls="custom-tabs-one-tpq6" aria-selected="false">TPQ6</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-one-all" role="tabpanel" aria-labelledby="custom-tabs-one-all-tab">
                            <?= renderTpqTable($dataSantriAll, 0) ?>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-tk" role="tabpanel" aria-labelledby="custom-tabs-one-tk-tab">
                            <?= renderTpqTable($dataSantriTK, 1) ?>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-tka" role="tabpanel" aria-labelledby="custom-tabs-one-tka-tab">
                            <?= renderTpqTable($dataSantriTKA, 2) ?>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-tkb" role="tabpanel" aria-labelledby="custom-tabs-one-tkb-tab">
                            <?= renderTpqTable($dataSantriTKB, 3) ?>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-tpq1" role="tabpanel" aria-labelledby="custom-tabs-one-tpq1-tab">
                            <?= renderTpqTable($dataSantriTPQ1, 4) ?>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-tpq2" role="tabpanel" aria-labelledby="custom-tabs-one-tpq2-tab">
                            <?= renderTpqTable($dataSantriTPQ2, 5) ?>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-tpq3" role="tabpanel" aria-labelledby="custom-tabs-one-tpq3-tab">
                            <?= renderTpqTable($dataSantriTPQ3, 6) ?>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-tpq4" role="tabpanel" aria-labelledby="custom-tabs-one-tpq4-tab">
                            <?= renderTpqTable($dataSantriTPQ4, 7) ?>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-tpq5" role="tabpanel" aria-labelledby="custom-tabs-one-tpq5-tab">
                            <?= renderTpqTable($dataSantriTPQ5, 8) ?>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-tpq6" role="tabpanel" aria-labelledby="custom-tabs-one-tpq6-tab">
                            <?= renderTpqTable($dataSantriTPQ6, 9) ?>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan Modal Detail Santri -->
<div class="modal fade" id="modalDetailSantri" tabindex="-1" role="dialog" aria-labelledby="modalDetailSantriLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="modalDetailSantriLabel">Data Santri</h5>
                    <p class="mb-0"><i class="fas fa-info-circle"></i> Informasi berikut adalah sekilas data santri yang sudah masuk</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <img id="santriPhoto" src="" alt="Foto Santri" class="img-fluid rounded" style="max-width: 200px;">
                    </div>
                    <div class="col-md-8">
                        <div class="section-title" style="background-color: #4CAF50; color: white; padding: 5px 10px; margin: 15px 0;">Data Pribadi Santri</div>
                        <table class="table table-bordered">
                            <tr>
                                <td width="30%">Nama TPQ</td>
                                <th id="namaTpq"></th>
                            </tr>
                            <tr>
                                <td>Nama Kelas</td>
                                <th id="namaKelas"></th>
                            </tr>
                            <tr>
                                <td>Nama Santri</td>
                                <th id="namaSantri"></th>
                            </tr>
                            <tr>
                                <td>ID Santri</td>
                                <th id="idSantri"></th>
                            </tr>
                            <tr>
                                <td>Tempat, Tgl Lahir</td>
                                <th id="ttlSantri"></th>
                            </tr>
                            <tr>
                                <td>Jenis Kelamin</td>
                                <th id="jenisKelamin"></th>
                            </tr>
                            <tr>
                                <td>Anak Ke</td>
                                <th id="anakKe"></th>
                            </tr>
                            <tr>
                                <td>Jumlah Saudara</td>
                                <th id="jumlahSaudara"></th>
                            </tr>
                            <tr>
                                <td>Hobi</td>
                                <th id="hobi"></th>
                            </tr>
                            <tr>
                                <td>Cita-Cita</td>
                                <th id="citaCita"></th>
                            </tr>
                            <tr>
                                <td>Nama Ayah</td>
                                <th id="namaAyah"></th>
                            </tr>
                            <tr>
                                <td>Nama Ibu</td>
                                <th id="namaIbu"></th>
                            </tr>
                        </table>
                        <div class="section-title" style="background-color: #4CAF50; color: white; padding: 5px 10px; margin: 15px 0;">Data Alamat Santri</div>
                        <table class="table table-bordered">
                            <tr>
                                <td>Alamat</td>
                                <th id="alamatSantri"></th>
                            </tr>
                            <tr>
                                <td>RW Santri</td>
                                <th id="rwSantri"></th>
                            </tr>
                            <tr>
                                <td>RT Santri</td>
                                <th id="rtSantri"></th>
                            </tr>
                            <tr>
                                <td>Kecamatan Santri</td>
                                <th id="kecamatanSantri"></th>
                            </tr>
                            <tr>
                                <td>Kelurahan/Desa Santri</td>
                                <th id="kelurahanDesaSantri"></th>
                            </tr>
                            <tr>
                                <td>Kabupaten/Kota Santri</td>
                                <th id="kabupatenKotaSantri"></th>
                            </tr>
                            <tr>
                                <td>Provinsi Santri</td>
                                <th id="provinsiSantri"></th>
                            </tr>
                            <tr>
                                <td>Kode Pos Santri</td>
                                <th id="kodePosSantri"></th>
                            </tr>
                            <tr>
                                <td>Jarak Ke Lembaga</td>
                                <th id="jarakKeLembaga"></th>
                            </tr>
                            <tr>
                                <td>Waktu Tempuh</td>
                                <th id="waktuTempuh"></th>
                            </tr>
                            <tr>
                                <td>Transportasi</td>
                                <th id="transportasi"></th>
                            </tr>
                            <tr>
                                <td>Titik Koordinat & Arah</td>
                                <th>
                                    <span id="titikKoordinatSantri"></span>
                                    <br>
                                    <span id="googleMapLink"></span>
                                </th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
                <th>Kelas</th>
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
                    <td><?= $santri['NamaKelas']; ?></td>
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
                <th>Kelas</th>
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

    /*=== Inisialisasi untuk semua tabel semua kelas TPQ (0-9) ===*/
    for (let i = 0; i <= 9; i++) {
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
        $.ajax({
            url: `<?= base_url('backend/santri/getDetailSantri/') ?>${idSantri}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success',
                        text: "Berhasil mengambil data santri",
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK',
                        timer: 1000
                    }).then(() => {
                        const data = response.data;
                        const uploadPath = '<?= (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/' : base_url('uploads/santri/') ?>';

                        // Update elemen modal dengan data santri
                        $('#santriPhoto').attr('src', data.PhotoProfil ? uploadPath + data.PhotoProfil : '<?= base_url('images/no-photo.jpg') ?>');
                        $('#namaTpq').text(data.NamaTpq);
                        $('#namaKelas').text(data.NamaKelas);
                        $('#namaSantri').text(data.NamaSantri);
                        $('#idSantri').text(data.IdSantri);
                        $('#ttlSantri').text(`${data.TempatLahirSantri}, ${data.TanggalLahirSantri}`);
                        $('#jenisKelamin').text(data.JenisKelamin);
                        $('#anakKe').text(data.AnakKe);
                        $('#jumlahSaudara').text(data.JumlahSaudara);
                        $('#hobi').text(data.Hobi);
                        $('#citaCita').text(data.CitaCita);
                        $('#namaAyah').text(data.NamaAyah);
                        $('#namaIbu').text(data.NamaIbu);
                        $('#alamatSantri').text(data.AlamatSantri);
                        $('#rwSantri').text(data.RwSantri);
                        $('#rtSantri').text(data.RtSantri);
                        $('#kecamatanSantri').text(data.KecamatanSantri);
                        $('#kelurahanDesaSantri').text(data.KelurahanDesaSantri);
                        $('#kabupatenKotaSantri').text(data.KabupatenKotaSantri);
                        $('#provinsiSantri').text(data.ProvinsiSantri);
                        $('#kodePosSantri').text(data.KodePosSantri);
                        $('#jarakKeLembaga').text(data.JarakTempuhSantri);
                        $('#waktuTempuh').text(data.WaktuTempuhSantri);
                        $('#transportasi').text(data.TransportasiSantri);
                        $('#titikKoordinatSantri').text(data.TitikKoordinatSantri);
                        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
                        const coordinates = data.TitikKoordinatSantri;
                        const mapUrl = isMobile ?
                            `geo:0,0?q=${encodeURIComponent(coordinates)}` :
                            `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(coordinates)}`;

                        $('#googleMapLink').html(`<a href="${mapUrl}" target="_blank" rel="noopener noreferrer">Lihat di Google Maps</a>`);
                        $('#modalDetailSantri').modal('show');
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || "Terjadi kesalahan saat mengambil data santri",
                        icon: 'error',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK',
                        timer: 2000
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "Gagal mengambil data santri",
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
</script>
<?= $this->endSection(); ?>