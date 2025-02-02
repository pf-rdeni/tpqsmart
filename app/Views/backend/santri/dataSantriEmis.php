<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar Santri</h3>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="tblSantriEmis" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Aksi EMIS</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <!-- <th>Status EMIS </th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($dataSantri as $santri) : ?>
                        <tr>
                            <td class="d-flex justify-content-between gap-1">
                                <a href="javascript:void(0)" onclick="showInputEmisSantri('<?= $santri['IdSantri']; ?>')" class="btn btn-info btn-sm flex-fill">
                                    <i class="fas fa-file"></i><span class="d-none d-md-inline">&nbsp;Input</span>
                                </a>
                                <a href="javascript:void(0)" onclick="showUpdateEmisSantri('<?= $santri['IdSantri']; ?>')" class="btn btn-warning btn-sm flex-fill">
                                    <i class="fas fa-eye"></i><span class="d-none d-md-inline">&nbsp;Update</span>
                                </a>
                            </td>
                            <td><?= $santri['NikSantri']; ?></td>
                            <td><?= ucwords(strtolower($santri['NamaSantri'])); ?></td>
                            <td><?= $santri['NamaKelas']; ?></td>
                            <!-- <td></td> -->
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
        </div>
    </div>
    <!-- /.card -->
</div>

<!-- Input Data Santri Emis -->
<div class="modal fade" id="inputDataEmisModal" tabindex="-1" role="dialog" aria-labelledby="detailSantriModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailSantriModalLabel">Input Data Santri Emis</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="detailSantriTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="data-santri-tab" data-toggle="tab" href="#data-santri" role="tab">
                            <i class="fas fa-user"></i> Data Santri
                        </a>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="detailSantriTabContent">
                    <!-- Tab Data Santri -->
                    <div class="tab-pane fade show active" id="data-santri" role="tabpanel">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-borderless">
                                    <tr>
                                        <td>Tanggal Daftar</td>
                                        <td>:</td>
                                        <td id="modalTanggalDaftar" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Kelas</td>
                                        <td>:</td>
                                        <td id="modalKelas" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>NIK Santri</td>
                                        <td>:</td>
                                        <td id="modalNikSantri" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>NISN</td>
                                        <td>:</td>
                                        <td id="modalNIS" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Nama Santri</td>
                                        <td>:</td>
                                        <td id="modalNamaSantri" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Tempat Lahir</td>
                                        <td>:</td>
                                        <td id="modalTempatLahir" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Lahir</td>
                                        <td>:</td>
                                        <td id="modalTanggalLahir" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Jenis Kelamin</td>
                                        <td>:</td>
                                        <td id="modalJenisKelamin" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Nama Ayah</td>
                                        <td>:</td>
                                        <td id="modalNamaAyah" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>:</td>
                                        <td id="modalStatusAyah" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Nama Ibu</td>
                                        <td>:</td>
                                        <td id="modalNamaIbu" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>:</td>
                                        <td id="modalStatusIbu" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Wali</td>
                                        <td>:</td>
                                        <td id="modalStatusWali" style="font-weight: bold;"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Update Data Santri Emis -->
<div class="modal fade" id="updateDataEmisModal" tabindex="-1" role="dialog" aria-labelledby="detailSantriModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailSantriModalLabel">Update Data Santri Emis</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="detailSantriTabUpdate" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="data-santri-tabUpdate" data-toggle="tab" href="#data-santriUpdate" role="tab">
                            <i class="fas fa-user"></i> Data Santri
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="data-ortu-tabUpdate" data-toggle="tab" href="#data-ortuUpdate" role="tab">
                            <i class="fas fa-users"></i> Data Orang Tua
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="data-alamat-tabUpdate" data-toggle="tab" href="#data-alamatUpdate" role="tab">
                            <i class="fas fa-map-marker-alt"></i> Data Alamat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="lampiran-tabUpdate" data-toggle="tab" href="#lampiranUpdate" role="tab">
                            <i class="fas fa-file"></i> Lampiran
                        </a>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="detailSantriTabContentUpdate">
                    <!-- Tab Data Santri -->
                    <div class="tab-pane fade show active" id="data-santriUpdate" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4 text-center mb-3">
                                <img id="modalPhotoProfilUpdate" src="" alt="Foto Profil" class="img-fluid rounded" style="max-width: 200px;">
                            </div>
                            <div class="col-md-8">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%">ID Santri</td>
                                        <td width="5%">:</td>
                                        <td id="modalIdSantriUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Nama TPQ</td>
                                        <td>:</td>
                                        <td id="modalNamaTpqUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Kelas</td>
                                        <td>:</td>
                                        <td id="modalKelasUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>NIS</td>
                                        <td>:</td>
                                        <td id="modalNISUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>NIK Santri</td>
                                        <td>:</td>
                                        <td id="modalNikSantriUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>ID Kartu Keluarga</td>
                                        <td>:</td>
                                        <td id="modalIdKartuKeluargaUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Nama Santri</td>
                                        <td>:</td>
                                        <td id="modalNamaSantriUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Jenis Kelamin</td>
                                        <td>:</td>
                                        <td id="modalJenisKelaminUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Tempat, Tanggal Lahir</td>
                                        <td>:</td>
                                        <td id="modalTempatTanggalLahirUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>No HP | Email</td>
                                        <td>:</td>
                                        <td id="modalNoHpUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah Saudara & Anak Ke</td>
                                        <td>:</td>
                                        <td id="modalJumlahSaudaraAnakKeUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Cita-cita</td>
                                        <td>:</td>
                                        <td id="modalCitaCitaUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Hobi</td>
                                        <td>:</td>
                                        <td id="modalHobiUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Kebutuhan Khusus</td>
                                        <td>:</td>
                                        <td id="modalKebutuhanKhususUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Kebutuhan Disabilitas</td>
                                        <td>:</td>
                                        <td id="modalKebutuhanDisabilitasUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Yang Biaya Sekolah</td>
                                        <td>:</td>
                                        <td id="modalYangBiayaSekolahUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Nama Kepala Keluarga</td>
                                        <td>:</td>
                                        <td id="modalNamaKepalaKeluargaUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>:</td>
                                        <td id="modalStatusUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Data Orang Tua -->
                    <div class="tab-pane fade" id="data-ortuUpdate" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold mb-3">Data Ayah</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%">Nama Ayah</td>
                                        <td width="5%">:</td>
                                        <td id="modalNamaAyahUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>:</td>
                                        <td id="modalStatusAyahUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <!-- Data tambahan yang hanya muncul jika Status "Masih Hidup" -->
                                    <tr class="data-ayah-hidup" style="display: none;">
                                        <td>NIK</td>
                                        <td>:</td>
                                        <td id="modalNikAyahUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr class="data-ayah-hidup" style="display: none;">
                                        <td>Tempat, Tanggal Lahir</td>
                                        <td>:</td>
                                        <td id="modalTempatTanggalLahirAyahUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr class="data-ayah-hidup" style="display: none;">
                                        <td>Pendidikan Terakhir</td>
                                        <td>:</td>
                                        <td id="modalPendidikanTerakhirAyahUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr class="data-ayah-hidup" style="display: none;">
                                        <td>Pekerjaan</td>
                                        <td>:</td>
                                        <td id="modalPekerjaanAyahUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr class="data-ayah-hidup" style="display: none;">
                                        <td>Penghasilan</td>
                                        <td>:</td>
                                        <td id="modalPenghasilanAyahUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr class="data-ayah-hidup" style="display: none;">
                                        <td>No. HP</td>
                                        <td>:</td>
                                        <td id="modalNoHpAyahUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold mb-3">Data Ibu</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%">Nama Ibu</td>
                                        <td width="5%">:</td>
                                        <td id="modalNamaIbuUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>:</td>
                                        <td id="modalStatusIbuUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <!-- Data tambahan yang hanya muncul jika Status "Masih Hidup" -->
                                    <tr class="data-ibu-hidup" style="display: none;">
                                        <td>NIK</td>
                                        <td>:</td>
                                        <td id="modalNikIbuUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr class="data-ibu-hidup" style="display: none;">
                                        <td>Tempat, Tanggal Lahir</td>
                                        <td>:</td>
                                        <td id="modalTempatTanggalLahirIbuUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr class="data-ibu-hidup" style="display: none;">
                                        <td>Pendidikan Terakhir</td>
                                        <td>:</td>
                                        <td id="modalPendidikanTerakhirIbuUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr class="data-ibu-hidup" style="display: none;">
                                        <td>Pekerjaan</td>
                                        <td>:</td>
                                        <td id="modalPekerjaanIbuUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr class="data-ibu-hidup" style="display: none;">
                                        <td>Penghasilan</td>
                                        <td>:</td>
                                        <td id="modalPenghasilanIbuUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                    <tr class="data-ibu-hidup" style="display: none;">
                                        <td>No. HP</td>
                                        <td>:</td>
                                        <td id="modalNoHpIbuUpdate" style="font-weight: bold;"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Data Alamat -->
                    <div class="tab-pane fade" id="data-alamatUpdate" role="tabpanel">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%">Alamat</td>
                                <td width="5%">:</td>
                                <td id="modalAlamatUpdate" style="font-weight: bold;"></td>
                            </tr>
                            <tr>
                                <td>RT/RW</td>
                                <td>:</td>
                                <td><span id="modalRTUpdate" style="font-weight: bold;"></span>/<span id="modalRWUpdate" style="font-weight: bold;"></span></td>
                            </tr>
                            <tr>
                                <td>Kelurahan/Desa</td>
                                <td>:</td>
                                <td id="modalKelurahanDesaUpdate" style="font-weight: bold;"></td>
                            </tr>
                            <tr>
                                <td>Kecamatan</td>
                                <td>:</td>
                                <td id="modalKecamatanUpdate" style="font-weight: bold;"></td>
                            </tr>
                            <tr>
                                <td>Kabupaten/Kota</td>
                                <td>:</td>
                                <td id="modalKabupatenKotaUpdate" style="font-weight: bold;"></td>
                            </tr>
                            <tr>
                                <td>Provinsi</td>
                                <td>:</td>
                                <td id="modalProvinsiUpdate" style="font-weight: bold;"></td>
                            </tr>
                            <tr>
                                <td>Jarak Tempuh ke TPQ</td>
                                <td>:</td>
                                <td id="modalJarakTempuhUpdate" style="font-weight: bold;"></td>
                            </tr>
                            <tr>
                                <td>Transportasi ke TPQ</td>
                                <td>:</td>
                                <td id="modalTransportasiUpdate" style="font-weight: bold;"></td>
                            </tr>
                            <tr>
                                <td>Waktu Tempuh ke TPQ</td>
                                <td>:</td>
                                <td id="modalWaktuTempuhUpdate" style="font-weight: bold;"></td>
                            </tr>
                            <tr>
                                <td>Titik Koordinat</td>
                                <td>:</td>
                                <td id="modalTitikKoordinatUpdate" style="font-weight: bold;"></td>
                            </tr>
                        </table>
                    </div>
                    <!-- tab untuk lampiran -->
                    <div class="tab-pane fade" id="lampiranUpdate" role="tabpanel">
                        <h1>Lampiran</h1>
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%">Kartu Keluarga Santri</td>
                                <td width="5%">:</td>
                                <td id="modalLampiranKkSantriUpdate" style="font-weight: bold;"></td>
                            </tr>
                            <tr>
                                <td width="30%">Kartu Keluarga Ayah</td>
                                <td width="5%">:</td>
                                <td id="modalLampiranKkAyahUpdate" style="font-weight: bold;"></td>
                            </tr>
                            <tr>
                                <td width="30%">Kartu Keluarga Ibu</td>
                                <td width="5%">:</td>
                                <td id="modalLampiranKkIbuUpdate" style="font-weight: bold;"></td>
                            </tr>
                            <tr>
                                <td width="30%">Lampiran KIP</td>
                                <td width="5%">:</td>
                                <td id="modalLampiranKIPUpdate" style="font-weight: bold;"></td>
                            </tr>
                            <tr>
                                <td width="30%">Lampiran PKH</td>
                                <td width="5%">:</td>
                                <td id="modalLampiranPKHUpdate" style="font-weight: bold;"></td>
                            </tr>
                            <tr>
                                <td width="30%">Lampiran KKS</td>
                                <td width="5%">:</td>
                                <td id="modalLampiranKKSUpdate" style="font-weight: bold;"></td>
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

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    /*=== Modal View detail santri===*/
    function showInputEmisSantri(IdSantri) {
        const dataSantri = <?= json_encode($dataSantri) ?>;
        const santri = dataSantri.find(s => s.IdSantri === IdSantri);

        if (santri) {
            // Set nilai untuk tab Data Santri
            document.getElementById('modalKelas').textContent = santri.NamaKelas;
            document.getElementById('modalNIS').textContent = santri.NIS ? santri.NIS : "Belum Punya NISN";
            document.getElementById('modalNikSantri').textContent = santri.NikSantri;
            document.getElementById('modalNamaSantri').textContent = santri.NamaSantri;
            document.getElementById('modalJenisKelamin').textContent = santri.JenisKelamin;
            document.getElementById('modalTempatLahir').textContent = santri.TempatLahirSantri;
            document.getElementById('modalTanggalLahir').textContent = santri.TanggalLahirSantri;

            // Set nilai untuk tab Data Orang Tua
            // Data Ayah
            document.getElementById('modalNamaAyah').textContent = santri.NamaAyah || '-';
            document.getElementById('modalStatusAyah').textContent = santri.StatusAyah || '-';
            // Data Ibu
            document.getElementById('modalNamaIbu').textContent = santri.NamaIbu || '-';
            document.getElementById('modalStatusIbu').textContent = santri.StatusIbu || '-';

            // Data Status Wali
            document.getElementById('modalStatusWali').textContent = santri.StatusWali || '-';

            // Data Tanggal Daftar
            document.getElementById('modalTanggalDaftar').textContent = santri.created_at ? formatDate(santri.created_at) : '-';

            function formatDate(dateString) {
                const date = new Date(dateString);
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0'); // Menambahkan 0 di depan jika bulan kurang dari 10
                const day = String(date.getDate()).padStart(2, '0'); // Menambahkan 0 di depan jika tanggal kurang dari 10
                return `${year}-${month}-${day}`; // Format: YYYY-MM-DD
            }

            // Tampilkan modal
            $('#inputDataEmisModal').modal('show');
        }
    }
    /*=== Modal View detail santri===*/
    function showUpdateEmisSantri(IdSantri) {
        const dataSantri = <?= json_encode($dataSantri) ?>;
        const santri = dataSantri.find(s => s.IdSantri === IdSantri);

        if (santri) {
            // Set nilai untuk tab Data Santri
            const uploadPath = '<?= (ENVIRONMENT === 'production') ? 'https://tpqsmart.simpedis.com/uploads/santri/' : base_url('uploads/santri/') ?>';
            document.getElementById('modalPhotoProfilUpdate').src = santri.PhotoProfil ? uploadPath + santri.PhotoProfil : '<?= base_url('images/no-photo.jpg') ?>';
            document.getElementById('modalIdSantriUpdate').textContent = santri.IdSantri;
            document.getElementById('modalNamaTpqUpdate').textContent = santri.NamaTpq;
            document.getElementById('modalKelasUpdate').textContent = santri.NamaKelas;
            document.getElementById('modalNISUpdate').textContent = santri.NIS;
            document.getElementById('modalNikSantriUpdate').textContent = santri.NikSantri;
            document.getElementById('modalIdKartuKeluargaUpdate').textContent = santri.IdKartuKeluarga;
            document.getElementById('modalNamaSantriUpdate').textContent = santri.NamaSantri;
            document.getElementById('modalJenisKelaminUpdate').textContent = santri.JenisKelamin;
            document.getElementById('modalTempatTanggalLahirUpdate').textContent = santri.TempatLahirSantri + ', ' + santri.TanggalLahirSantri;
            document.getElementById('modalNoHpUpdate').textContent = santri.NoHpSantri || '-' + ' | ' + santri.EmailSantri || '-';
            document.getElementById('modalJumlahSaudaraAnakKeUpdate').textContent = 'Jumlah Saudara: ' + santri.JumlahSaudara + ' Anak Ke-' + santri.AnakKe;
            document.getElementById('modalCitaCitaUpdate').textContent = santri.CitaCita || santri.CitaCitaLainya;
            document.getElementById('modalHobiUpdate').textContent = santri.Hobi || santri.HobiLainya;
            document.getElementById('modalKebutuhanKhususUpdate').textContent = santri.KebutuhanKhusus || santri.KebutuhanKhususLainya;
            document.getElementById('modalKebutuhanDisabilitasUpdate').textContent = santri.KebutuhanDisabilitas || santri.KebutuhanDisabilitasLainya;
            document.getElementById('modalYangBiayaSekolahUpdate').textContent = santri.YangBiayaSekolah;
            document.getElementById('modalNamaKepalaKeluargaUpdate').textContent = santri.NamaKepalaKeluarga;

            // Set Status dengan badge
            const statusElement = document.getElementById('modalStatusUpdate');
            let badgeClass = 'bg-success';
            if (santri.Status === 'Belum Diverifikasi') {
                badgeClass = 'bg-warning';
            } else if (santri.Status === 'Perlu Perbaikan') {
                badgeClass = 'bg-danger';
            }
            statusElement.innerHTML = `<span class="badge ${badgeClass}">${santri.Status}</span>`;

            // Set nilai untuk tab Data Orang Tua
            // Data Ayah
            document.getElementById('modalNamaAyahUpdate').textContent = santri.NamaAyah || '-';
            document.getElementById('modalStatusAyahUpdate').textContent = santri.StatusAyah || '-';
            //jika statusAyah masih hidup ambil data lainya
            if (santri.StatusAyah === 'Masih Hidup') {
                document.getElementById('modalNikAyahUpdate').textContent = santri.NikAyah || '-';
                document.getElementById('modalTempatTanggalLahirAyahUpdate').textContent = santri.TempatLahirAyah + ', ' + santri.TanggalLahirAyah || '-';
                document.getElementById('modalPendidikanTerakhirAyahUpdate').textContent = santri.PendidikanAyah || '-';
                document.getElementById('modalPekerjaanAyahUpdate').textContent = santri.PekerjaanUtamaAyah || '-';
                document.getElementById('modalPenghasilanAyahUpdate').textContent = santri.PenghasilanUtamaAyah || '-';
                document.getElementById('modalNoHpAyahUpdate').textContent = santri.NoHpAyah || '-';
            }
            // Data Ibu
            document.getElementById('modalNamaIbuUpdate').textContent = santri.NamaIbu || '-';
            document.getElementById('modalStatusIbuUpdate').textContent = santri.StatusIbu || '-';
            //jika statusIbu masih hidup ambil data lainya
            if (santri.StatusIbu === 'Masih Hidup') {
                document.getElementById('modalNikIbuUpdate').textContent = santri.NikIbu || '-';
                document.getElementById('modalTempatTanggalLahirIbuUpdate').textContent = santri.TempatLahirIbu + ', ' + santri.TanggalLahirIbu || '-';
                document.getElementById('modalPendidikanTerakhirIbuUpdate').textContent = santri.PendidikanIbu || '-';
                document.getElementById('modalPekerjaanIbuUpdate').textContent = santri.PekerjaanUtamaIbu || '-';
                document.getElementById('modalPenghasilanIbuUpdate').textContent = santri.PenghasilanUtamaIbu || '-';
                document.getElementById('modalNoHpIbuUpdate').textContent = santri.NoHpIbu || '-';
            }

            // Set nilai untuk tab Data Alamat
            document.getElementById('modalAlamatUpdate').textContent = santri.AlamatSantri || '-';
            document.getElementById('modalRTUpdate').textContent = santri.RtSantri || '-';
            document.getElementById('modalRWUpdate').textContent = santri.RwSantri || '-';
            document.getElementById('modalKelurahanDesaUpdate').textContent = santri.KelurahanDesaSantri || '-';
            document.getElementById('modalKecamatanUpdate').textContent = santri.KecamatanSantri || '-';
            document.getElementById('modalKabupatenKotaUpdate').textContent = santri.KabupatenKotaSantri || '-';
            document.getElementById('modalProvinsiUpdate').textContent = santri.ProvinsiSantri || '-';

            // JarakTempuhSantri
            document.getElementById('modalJarakTempuhUpdate').textContent = santri.JarakTempuhSantri || '-';
            document.getElementById('modalTransportasiUpdate').textContent = santri.TransportasiSantri || '-';
            document.getElementById('modalWaktuTempuhUpdate').textContent = santri.WaktuTempuhSantri || '-';
            if (santri.TitikKoordinatSantri) {
                const coords = santri.TitikKoordinatSantri.split(',');
                const mapsLink = `https://www.google.com/maps?q=${coords[0]},${coords[1]}`;
                document.getElementById('modalTitikKoordinatUpdate').innerHTML = `
                    <a href="${mapsLink}" target="_blank" class="btn btn-sm btn-info">
                        <i class="fas fa-map-marker-alt"></i> Lihat di Google Maps
                    </a>
                    <span class="ml-2">${santri.TitikKoordinatSantri}</span>
                `;
            } else {
                document.getElementById('modalTitikKoordinatUpdate').textContent = '-';
            }

            // Fungsi helper untuk membuat link lampiran
            function createFileLink(fileName, path) {
                // Periksa jika fileName null, undefined, atau string kosong
                if (!fileName || fileName.trim() === '') {
                    return '<span class="text-muted">Tidak ada file</span>';
                }

                try {
                    const fullPath = path + fileName;
                    return `<a href="${fullPath}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-file-download"></i> Lihat File
                            </a>`;
                } catch (error) {
                    console.error('Error creating file link:', error);
                    return '<span class="text-danger">Error: File tidak dapat diakses</span>';
                }
            }

            // Gunakan operator optional chaining untuk menghindari error jika properti tidak ada
            document.getElementById('modalLampiranKkSantriUpdate').innerHTML = createFileLink(santri?.FileKkSantri, uploadPath);
            document.getElementById('modalLampiranKkAyahUpdate').innerHTML = createFileLink(santri?.FileKkAyah, uploadPath);
            document.getElementById('modalLampiranKkIbuUpdate').innerHTML = createFileLink(santri?.FileKkIbu, uploadPath);
            document.getElementById('modalLampiranKIPUpdate').innerHTML = createFileLink(santri?.FileKIP, uploadPath);
            document.getElementById('modalLampiranPKHUpdate').innerHTML = createFileLink(santri?.FilePKH, uploadPath);
            document.getElementById('modalLampiranKKSUpdate').innerHTML = createFileLink(santri?.FileKKS, uploadPath);

            // Untuk Ayah
            if (santri.StatusAyah === 'Masih Hidup') {
                document.querySelectorAll('.data-ayah-hidup').forEach(el => {
                    el.style.display = 'table-row';
                });
            } else {
                document.querySelectorAll('.data-ayah-hidup').forEach(el => {
                    el.style.display = 'none';
                });
            }

            // Untuk Ibu
            if (santri.StatusIbu === 'Masih Hidup') {
                document.querySelectorAll('.data-ibu-hidup').forEach(el => {
                    el.style.display = 'table-row';
                });
            } else {
                document.querySelectorAll('.data-ibu-hidup').forEach(el => {
                    el.style.display = 'none';
                });
            }

            // Tampilkan modal
            $('#updateDataEmisModal').modal('show');
        }
    }
</script>
<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    initializeDataTableUmum("#tblSantriEmis", true, true, ["excel", "pdf", "colvis"]);
    //initializeDataTableWithFilter("#tblSantriEmis", true, ["excel", "pdf", "print", "colvis"]);
</script>
<?= $this->endSection(); ?>