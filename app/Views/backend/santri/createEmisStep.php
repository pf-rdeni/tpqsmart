<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<?php echo session()->getFlashdata('pesan');
// Cek environment untuk menentukan nilai $required
if (ENVIRONMENT === 'production') {
    $required = 'required';
} else {
    $required = '';
}
?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="card-title m-0">Formulir Data Santri</h3>
                        <div class="d-flex">
                            <a href="<?= base_url('backend/santri/showSantriBaru') ?>" class="btn btn-info">
                                <i class="fas fa-list"></i><span class="d-none d-md-inline">&nbsp;Data Santri Baru</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian..!</strong> Kolom isian dengan tanda <span class="text-danger font-weight-bold">*</span> merah adalah harus diisi.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="bs-stepper">
                        <div class="bs-stepper-header" role="tablist">
                            <!-- your steps here -->
                            <div class="step" data-target="#tpq-part">
                                <button type="button" class="step-trigger" role="tab" aria-controls="tpq-part" id="tpq-part-trigger">
                                    <span class="bs-stepper-circle"><i class="fas fa-mosque"></i></span>
                                    <span class="bs-stepper-label">Tpq</span>
                                </button>
                            </div>
                            <div class="line"></div>
                            <div class="step" data-target="#santri-part">
                                <button type="button" class="step-trigger" role="tab" aria-controls="santri-part" id="santri-part-trigger">
                                    <span class="bs-stepper-circle"><i class="fas fa-user"></i></span>
                                    <span class="bs-stepper-label">Santri</span>
                                </button>
                            </div>
                            <div class="line"></div>
                            <div class="step" data-target="#ortu-part">
                                <button type="button" class="step-trigger" role="tab" aria-controls="ortu-part" id="ortu-part-trigger">
                                    <span class="bs-stepper-circle"><i class="fas fa-users"></i></span>
                                    <span class="bs-stepper-label">OrTu</span>
                                </button>
                            </div>
                            <div class="line"></div>
                            <div class="step" data-target="#alamat-part">
                                <button type="button" class="step-trigger" role="tab" aria-controls="alamat-part" id="alamat-part-trigger">
                                    <span class="bs-stepper-circle"><i class="fas fa-map-marker-alt"></i></span>
                                    <span class="bs-stepper-label">Alamat</span>
                                </button>
                            </div>
                        </div>
                        <div class="bs-stepper-content">
                            <!-- your steps content here -->
                            <form action="<?= base_url('backend/santri/save') ?>" method="POST" id="santriForm" enctype="multipart/form-data">
                                <div id="tpq-part" class="content" role="tabpanel" aria-labelledby="tpq-part-trigger">
                                    <br>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> <small>Silakan pilih lokasi TPQ berdasarkan Desa/Kelurahan terlebih dahulu, kemudian pilih nama TPQ dan kelas yang dituju.</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="KelurahanDesaTpq">Lokasi TPQ<span class="text-danger font-weight-bold">*</span></label>
                                        <select class="form-control" id="KelurahanDesaTpq" name="KelurahanDesaTpq" <?= $required ?>>
                                            <option value="">Pilih Lokasi TPQ</option>
                                            <option value="TELUK SASAH">TELUK SASAH</option>
                                            <option value="BUSUNG">BUSUNG</option>
                                            <option value="KUALA SEMPANG">KUALA SEMPANG</option>
                                            <option value="TANJUNG PERMAI">TANJUNG PERMAI</option>
                                            <option value="TELUK LOBAM">TELUK LOBAM</option>
                                        </select>
                                        <span id="KelurahanDesaTpqError" class="text-danger" style="display:none;">Desa/Kelurahan diperlukan.</span>
                                    </div>

                                    <div class="form-group">
                                        <label for="IdTpq">Nama TPQ<span class="text-danger font-weight-bold">*</span></label>
                                        <select class="form-control" id="IdTpq" name="IdTpq" <?= $required ?>>
                                            <option value="">Pilih Nama TPQ sesuai Desa/Kelurahan</option>
                                            <?php foreach ($dataTpq as $tpq): ?>
                                                <option value="<?= $tpq['IdTpq'] ?>"><?= $tpq['NamaTpq'] ?> - <?= $tpq['Alamat'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span id="IdTpqError" class="text-danger" style="display:none;">Nama TPQ diperlukan.</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="IdKelas">Kelas<span class="text-danger font-weight-bold">*</span></label>
                                        <select class="form-control" id="IdKelas" name="IdKelas" <?= $required ?>>
                                            <option value="">Pilih Kelas</option>
                                            <?php foreach ($dataKelas as $kelas): ?>
                                                <option value="<?= $kelas['IdKelas'] ?>"><?= $kelas['NamaKelas'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span id="IdKelasError" class="text-danger" style="display:none;">Kelas diperlukan.</span>
                                    </div>
                                    <button type="button" class="btn btn-primary" onclick="validateAndNext('tpq-part')">Selanjutnya</button>
                                </div>
                                <!-- Bagian Profil Santri -->
                                <div id="santri-part" class="content" role="tabpanel" aria-labelledby="santri-part-trigger">
                                    <br>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> <small>Silakan mengisi data profil santri dengan benar!</small>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="bg-success p-2">
                                                <h5 class="mb-0 text-white">Data Santri</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="text-center w-100">Photo Profil<span class="text-danger font-weight-bold">*</span></label>
                                                <div class="text-center">
                                                    <img id="previewPhotoProfil" src="/images/no-photo.jpg" alt="Preview Photo"
                                                        class="img-thumbnail mx-auto d-block" style="width: 100%; max-width: 215px; height: auto; min-height: 280px; object-fit: cover; cursor: pointer;">
                                                    <div class="mt-2 d-flex justify-content-between" style="width: 215px; margin: 0 auto;">
                                                        <button type="button" class="btn btn-sm btn-primary flex-grow-1 mr-2" onclick="document.getElementById('PhotoProfil').click()">
                                                            <i class="fas fa-upload"></i> Upload Foto
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-success flex-grow-1" onclick="openCamera()">
                                                            <i class="fas fa-camera"></i> Ambil Foto
                                                        </button>
                                                    </div>
                                                </div> <small class="text-center d-block mb-2 text-muted">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                    Format photo background merah dengan rasio 2:3, file format JPG, JPEG, PNG. and max file size 2MB
                                                </small>
                                                <input class="form-control custom-file-input" type="file" id="PhotoProfil" name="PhotoProfil" accept=".jpg,.jpeg,.png,.png,image/*;capture=camera" onchange="previewPhoto(this)" <?= $required ?> style="display: none;">
                                                <span id="PhotoProfilError" class="text-danger" style="display:none;">Photo Profil diperlukan.</span>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="NikSantri">NIK Santri<span class="text-danger font-weight-bold">*</span></label>
                                                            <input type="text" class="form-control" id="NikSantri" name="NikSantri"
                                                                placeholder="Masukkan NIK 16 digit" <?= $required ?> pattern="^[1-9]\d{15}$"
                                                                title="NIK harus terdiri dari 16 digit angka dan tidak boleh diawali dengan angka 0">
                                                            <span id="NikSantriError" class="text-danger" style="display:none;">NIK diperlukan.</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="NamaSantri">Nama Santri<span class="text-danger font-weight-bold">*</span></label>
                                                            <input type="text" class="form-control name-input" id="NamaSantri" name="NamaSantri" placeholder="Masukkan nama lengkap" <?= $required ?>>
                                                            <span id="NamaSantriError" class="text-danger" style="display:none;">Nama Santri diperlukan.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="NISN">NISN</label>
                                                            <input type="text" class="form-control" id="NISN" name="NISN" placeholder="Masukkan NISN"
                                                                pattern="[0-9]{10}" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                            <small class="text-muted">NISN harus 10 digit angka</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="Agama">Agama<span class="text-danger font-weight-bold">*</span></label>
                                                            <input type="text" class="form-control" id="Agama" name="Agama" value="Islam" readonly <?= $required ?>>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="JenisKelamin">Jenis Kelamin<span class="text-danger font-weight-bold">*</span></label>
                                                            <select class="form-control" id="JenisKelamin" name="JenisKelamin" <?= $required ?>>
                                                                <option value="">Pilih Jenis Kelamin</option>
                                                                <option value="Laki-laki">Laki-laki</option>
                                                                <option value="Perempuan">Perempuan</option>
                                                            </select>
                                                            <span id="JenisKelaminError" class="text-danger" style="display:none;">Jenis Kelamin diperlukan.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="TempatLahirSantri">Tempat Lahir<span class="text-danger font-weight-bold">*</span></label>
                                                            <input type="text" class="form-control name-input" id="TempatLahirSantri" name="TempatLahirSantri" placeholder="Ketik Tempat Lahir Santri" <?= $required ?>>
                                                            <span id="TempatLahirSantriError" class="text-danger" style="display:none;">Tempat Lahir diperlukan.</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="TanggalLahirSantri">Tanggal Lahir<span class="text-danger font-weight-bold">*</span></label>
                                                            <input type="date" class="form-control" id="TanggalLahirSantri" name="TanggalLahirSantri" <?= $required ?>>
                                                            <span id="TanggalLahirSantriError" class="text-danger" style="display:none;">Tanggal Lahir diperlukan.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="JumlahSaudara">Jumlah Saudara<span class="text-danger font-weight-bold">*</span></label>
                                                            <input type="text" class="form-control" id="JumlahSaudara" name="JumlahSaudara" placeholder="Masukkan angka jumlah saudara"
                                                                pattern="[1-9]+" title="Jumlah Saudara harus berupa angka" oninput="this.value = this.value.replace(/[^1-9]/g, '')" <?= $required ?>>
                                                            <span id="JumlahSaudaraError" class="text-danger" style="display:none;">Jumlah Saudara diperlukan.</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="AnakKe">Anak Ke<span class="text-danger font-weight-bold">*</span></label>
                                                            <input type="text" class="form-control" id="AnakKe" name="AnakKe" placeholder="Masukkan angka anak ke berapa"
                                                                pattern="[1-9]+" title="Anak Ke harus berupa angka" oninput="this.value = this.value.replace(/[^1-9]/g, '')" <?= $required ?>>
                                                            <span id="AnakKeError" class="text-danger" style="display:none;">Anak Ke diperlukan.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <!-- Bagian Cita-Cita -->
                                            <div class="col-md-6">
                                                <label for="CitaCita">Cita-Cita<span class="text-danger font-weight-bold">*</span></label>
                                                <select class="form-control" id="CitaCita" name="CitaCita" <?= $required ?>>
                                                    <option value="">Pilih Cita-Cita</option>
                                                    <option value="PNS">PNS</option>
                                                    <option value="TNI/Polri">TNI/Polri</option>
                                                    <option value="Guru/Dosen">Guru/Dosen</option>
                                                    <option value="Dokter">Dokter</option>
                                                    <option value="Politikus">Politikus</option>
                                                    <option value="Wiraswasta">Wiraswasta</option>
                                                    <option value="Seniman/Artis">Seniman/Artis</option>
                                                    <option value="Ilmuwan">Ilmuwan</option>
                                                    <option value="Agamawan">Agamawan</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                                <span id="CitaCitaError" class="text-danger" style="display:none;">Cita-Cita diperlukan.</span>
                                            </div>
                                            <!-- Bagian Hobi -->
                                            <div class="col-md-6">
                                                <label for="Hobi">Hobi<span class="text-danger font-weight-bold">*</span></label>
                                                <select class="form-control" id="Hobi" name="Hobi" <?= $required ?>>
                                                    <option value="">Pilih Hobi</option>
                                                    <option value="Olahraga">Olahraga</option>
                                                    <option value="Kesenian">Kesenian</option>
                                                    <option value="Membaca">Membaca</option>
                                                    <option value="Menulis">Menulis</option>
                                                    <option value="Jalan-jalan">Jalan-jalan</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                                <span id="HobiError" class="text-danger" style="display:none;">Hobi diperlukan.</span>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="CitaCitaLainya">Cita-Cita Lainnya</label>
                                                <input type="text" class="form-control" id="CitaCitaLainya" name="CitaCitaLainya" placeholder="Ketik cita-cita lainnya" disabled>
                                                <span id="CitaCitaLainyaError" class="text-danger" style="display:none;">Cita-Cita Lainnya diperlukan.</span>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="HobiLainya">Hobi Lainnya</label>
                                                <input type="text" class="form-control" id="HobiLainya" placeholder="Ketik hobi lainnya" disabled>
                                                <span id="HobiLainyaError" class="text-danger" style="display:none;">Hobi Lainnya diperlukan.</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <label for="NoHpSantri">No Handphone</label>
                                                    <div class="d-flex align-items-center">
                                                        <input class="form-check-input mr-2" type="checkbox" id="NoHpSantriBelumPunya" name="NoHpSantriBelumPunya" checked>
                                                        <small class="form-check-label text-primary mb-0">Tidak memiliki nomor handphone</small>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control" id="NoHpSantri" name="NoHpSantri" placeholder="Masukkan nomor handphone" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <label for="EmailSantri">Alamat Email</label>
                                                    <div class="d-flex align-items-center">
                                                        <input class="form-check-input mr-2" type="checkbox" id="EmailSantriBelumPunya" name="EmailSantriBelumPunya" checked>
                                                        <small class="form-check-label text-primary mb-0">Tidak memiliki alamat email</small>
                                                    </div>
                                                </div>
                                                <input type="email" class="form-control" id="EmailSantri" name="EmailSantri" placeholder="Contoh: nama@email.com" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="KebutuhanKhusus">Kebutuhan Khusus</label>
                                                <select class="form-control" id="KebutuhanKhusus" name="KebutuhanKhusus">
                                                    <option value="Tidak Ada">Tidak Ada</option>
                                                    <option value="Lamban Belajar">Lamban Belajar</option>
                                                    <option value="Kesulitan Belajar Spesific">Kesulitan Belajar Spesific</option>
                                                    <option value="Berbakat/Memiliki Kemampuan dan Kecerdasan Luar Biasa">Berbakat/Memiliki Kemampuan dan Kecerdasan Luar Biasa</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="KebutuhanDisabilitas">Kebutuhan Disabilitas</label>
                                                <select class="form-control" id="KebutuhanDisabilitas" name="KebutuhanDisabilitas">
                                                    <option value="Tidak Ada">Tidak Ada</option>
                                                    <option value="Tuna Netra">Tuna Netra</option>
                                                    <option value="Tuna Wicara">Tuna Wicara</option>
                                                    <option value="Tuna Rungu">Tuna Rungu</option>
                                                    <option value="Tuna Laras">Tuna Laras</option>
                                                    <option value="Tuna Grahita">Tuna Grahita</option>
                                                    <option value="Tuna Daksa">Tuna Daksa</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="KebutuhanKhususLainya">Kebutuhan Khusus Lainnya</label>
                                                <input type="text" class="form-control" id="KebutuhanKhususLainya" name="KebutuhanKhususLainya" placeholder="Masukkan kebutuhan khusus lainnya" disabled>
                                                <span id="KebutuhanKhususLainyaError" class="text-danger" style="display:none;">Kebutuhan Khusus Lainnya diperlukan.</span>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="KebutuhanDisabilitasLainya">Kebutuhan Disabilitas Lainnya</label>
                                                <input type="text" class="form-control" id="KebutuhanDisabilitasLainya" placeholder="Masukkan kebutuhan disabilitas lainnya" disabled>
                                                <span id="KebutuhanDisabilitasLainyaError" class="text-danger" style="display:none;">Kebutuhan Disabilitas Lainnya diperlukan.</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="YangBiayaSekolah">Yang Membiayai Sekolah<span class="text-danger font-weight-bold">*</span></label>
                                                <select class="form-control" id="YangBiayaSekolah" name="YangBiayaSekolah" <?= $required ?>>
                                                    <option value="">Pilih Yang Membiayai</option>
                                                    <option value="Orang Tua">Orang Tua</option>
                                                    <option value="Wali/Orang Tua Asuh">Wali/Orang Tua Asuh</option>
                                                </select>
                                                <span id="YangBiayaSekolahError" class="text-danger" style="display:none;">Yang Membiayai Sekolah diperlukan.</span>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="NamaKepalaKeluarga">Nama Kepala Keluarga<span class="text-danger font-weight-bold">*</span></label>
                                                <input type="text" class="form-control name-input" id="NamaKepalaKeluarga" name="NamaKepalaKeluarga" placeholder="Masukkan nama kepala keluarga" <?= $required ?>>
                                                <span id="NamaKepalaKeluargaError" class="text-danger" style="display:none;">Nama Kepala Keluarga diperlukan.</span>
                                                <div class="form-check mt-2">
                                                    <input type="checkbox" class="form-check-input" id="NamaKepalaKeluargaSamaDenganAyah" name="NamaKepalaKeluargaSamaDenganAyah">
                                                    <label class="form-check-label small text-primary" for="NamaKepalaKeluargaSamaDenganAyah">
                                                        Checklist Jika Nama Kepala Keluarga Sama Dengan Ayah Kandung
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="IdKartuKeluarga">No Kartu Keluarga (KK)<span class="text-danger font-weight-bold">*</span></label>
                                                <input type="text" class="form-control" id="IdKartuKeluarga" name="IdKartuKeluarga" placeholder="Masukkan nomor KK"
                                                    pattern="^[1-9]\d{15}$" maxlength="16" oninput="this.value = this.value.replace(/[^1-9]/g, '')" <?= $required ?>>
                                                <small class="text-muted">Nomor KK harus 16 digit angka</small>
                                                <span id="IdKartuKeluargaError" class="text-danger" style="display:none;">No Kartu Keluarga diperlukan.</span>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="FileKkSantri">Upload KK Santri<span class="text-danger font-weight-bold">*</span></label>
                                                <div class="input-group mb-3">
                                                    <div class="custom-file">
                                                        <input type="file" class="form-control custom-file-input" id="FileKkSantri" name="FileKkSantri" accept=".pdf,.jpg,.jpeg,.png" <?= $required ?>>
                                                        <label class="custom-file-label" for="FileKkSantri">Upload KK</label>
                                                    </div>
                                                </div>
                                                <span id="FileKkSantriError" class="text-danger d-none">Upload KK Santri diperlukan.</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="NoKIP">No Kartu Indonesia Pintar (KIP)</label>
                                                <input type="text" class="form-control" id="NoKIP" name="NoKIP" placeholder="Masukkan Nomor KIP"
                                                    pattern="[0-9]{16}" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                <small class="text-muted">Nomor KIP harus 16 digit angka</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="FileKIP">Upload KIP</label>
                                                <div class="input-group mb-3">
                                                    <div class="custom-file">
                                                        <input type="file" class="form-control" id="FileKIP" name="FileKIP" accept=".pdf,.jpg,.jpeg,.png">
                                                        <label class="custom-file-label" for="FileKIP">Upload KIP</label>
                                                    </div>
                                                </div>
                                                <small id="FileKIPError" class="text-danger d-none"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-secondary" onclick="stepper.previous()">Sebelumnya</button>
                                    <button type="button" class="btn btn-primary" onclick="validateAndNext('santri-part')">Selanjutnya</button>
                                </div>
                                <!-- Bagian Profil Orang Tua atau Wali -->
                                <div id="ortu-part" class="content" role="tabpanel" aria-labelledby="ortu-part-trigger">
                                    <br>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> <small>Silakan mengisi data ayah dan ibu atau wali dengan benar!</small>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="bg-success p-2">
                                                <h5 class="mb-0 text-white">Data Ayah Kandung</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Bagian Data Profil Ayah -->
                                    <div id="DataProfilAyahDiv">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="NamaAyah">Nama Ayah Kandung<span class="text-danger font-weight-bold">*</span></label>
                                                <input type="text" class="form-control name-input" id="NamaAyah" name="NamaAyah" placeholder="Ketik nama lengkap ayah kandung" <?= $required ?>>
                                                <span id="NamaAyahError" class="text-danger" style="display:none;">Nama Ayah Kandung diperlukan.</span>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="StatusAyah">Status Ayah<span class="text-danger font-weight-bold">*</span></label>
                                                <select class="form-control" id="StatusAyah" name="StatusAyah" <?= $required ?>>
                                                    <option value="">Pilih Status</option>
                                                    <option value="Masih Hidup">Masih Hidup</option>
                                                    <option value="Sudah Meninggal">Sudah Meninggal</option>
                                                    <option value="Tidak Diketahui">Tidak Diketahui</option>
                                                </select>
                                                <span id="StatusAyahError" class="text-danger" style="display:none;">Status Ayah diperlukan.</span>
                                            </div>
                                        </div>
                                        <!-- Div Data Profil Ayah -->
                                        <div id="DataProfilAyahDetailDiv">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="NikAyah">NIK Ayah</label>
                                                    <input type="text" class="form-control number-only" id="NikAyah" name="NikAyah" placeholder="Masukkan NIK ayah">
                                                    <span id="NikAyahError" class="text-danger" style="display:none;">NIK Ayah diperlukan.</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="KewarganegaraanAyah">Kewarganegaraan<span class="text-danger font-weight-bold">*</span></label>
                                                    <select class="form-control" id="KewarganegaraanAyah" name="KewarganegaraanAyah">
                                                        <option value="WNI" selected>Warga Negara Indonesia (WNI)</option>
                                                        <option value="WNA">Warga Negara Asing (WNA)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="TempatLahirAyah">Tempat Lahir Ayah</label>
                                                        <input type="text" class="form-control" id="TempatLahirAyah" name="TempatLahirAyah" placeholder="Masukkan tempat lahir ayah">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="TanggalLahirAyah">Tanggal Lahir Ayah</label>
                                                        <input type="date" class="form-control" id="TanggalLahirAyah" name="TanggalLahirAyah">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="PendidikanAyah">Pendidikan Terakhir</label>
                                                        <select class="form-control" id="PendidikanAyah" name="PendidikanAyah">
                                                            <option value="">Pilih Pendidikan</option>
                                                            <option value="Tidak Sekolah">Tidak Sekolah</option>
                                                            <option value="SD Sederajat">SD Sederajat</option>
                                                            <option value="SMP Sederajat">SMP Sederajat</option>
                                                            <option value="SMA Sederajat">SMA Sederajat</option>
                                                            <option value="D1">D1</option>
                                                            <option value="D2">D2</option>
                                                            <option value="D3">D3</option>
                                                            <option value="D4/S1">D4/S1</option>
                                                            <option value="S2">S2</option>
                                                            <option value="S3">S3</option>
                                                            <option value="Lainnya">Lainnya</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="PekerjaanUtamaAyah">Pekerjaan Utama Ayah</label>
                                                        <select class="form-control" id="PekerjaanUtamaAyah" name="PekerjaanUtamaAyah">
                                                            <option value="">Pilih Pekerjaan</option>
                                                            <option value="Tidak Bekerja">Tidak Bekerja</option>
                                                            <option value="Pensiunan">Pensiunan</option>
                                                            <option value="PNS">PNS</option>
                                                            <option value="TNI/Polisi">TNI/Polisi</option>
                                                            <option value="Guru/Dosen">Guru/Dosen</option>
                                                            <option value="Pegawai Swasta">Pegawai Swasta</option>
                                                            <option value="Wiraswasta">Wiraswasta</option>
                                                            <option value="Pengacara/Jaksa/Hakim/Notaris">Pengacara/Jaksa/Hakim/Notaris</option>
                                                            <option value="Seniman/Pelukis/Artis/Sejenis">Seniman/Pelukis/Artis/Sejenis</option>
                                                            <option value="Dokter/Bidan/Perawat">Dokter/Bidan/Perawat</option>
                                                            <option value="Pilot/Pramugara">Pilot/Pramugara</option>
                                                            <option value="Pedagang">Pedagang</option>
                                                            <option value="Petani/Peternak">Petani/Peternak</option>
                                                            <option value="Nelayan">Nelayan</option>
                                                            <option value="Buruh (Tani/Pabrik/Bangunan)">Buruh (Tani/Pabrik/Bangunan)</option>
                                                            <option value="Sopir/Masinis/Kondektur">Sopir/Masinis/Kondektur</option>
                                                            <option value="Politikus">Politikus</option>
                                                            <option value="Lainnya">Lainnya</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="PenghasilanUtamaAyah">Penghasilan Utama</label>
                                                        <select class="form-control" id="PenghasilanUtamaAyah" name="PenghasilanUtamaAyah">
                                                            <option value="">Pilih Penghasilan</option>
                                                            <option value="Tidak Ada">Tidak Ada</option>
                                                            <option value="Kurang dari Rp. 500.000">Kurang dari Rp. 500.000</option>
                                                            <option value="Rp. 500.000 - Rp. 1.000.000">Rp. 500.000 - Rp. 1.000.000</option>
                                                            <option value="Rp. 1.000.000 - Rp. 2.000.000">Rp. 1.000.000 - Rp. 2.000.000</option>
                                                            <option value="Rp. 2.000.000 - Rp. 3.000.000">Rp. 2.000.000 - Rp. 3.000.000</option>
                                                            <option value="Rp. 3.000.000 - Rp. 4.000.000">Rp. 3.000.000 - Rp. 4.000.000</option>
                                                            <option value="Rp. 4.000.000 - Rp. 5.000.000">Rp. 4.000.000 - Rp. 5.000.000</option>
                                                            <option value="Lebih dari Rp. 5.000.000">Lebih dari Rp. 5.000.000</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="NoHpAyah">No Handphone Ayah</label>
                                                        <input type="text" class="form-control number-only" id="NoHpAyah" name="NoHpAyah" placeholder="Masukkan nomor handphone">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="KkAyahSamaDenganSantri" name="KkAyahSamaDenganSantri">
                                                        <label class="form-check-label" for="KkAyahSamaDenganSantri">Ayah satu KK dengan santri</label>
                                                    </div>
                                                    <div class="form-group" id="FileKKAyahDiv">
                                                        <label for="FileKkAyah">Upload KK Ayah</label>
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input type="file" class="form-control" id="FileKkAyah" name="FileKkAyah" onchange="validateFile('FileKkAyah')" accept=".pdf,.jpg,.jpeg,.png">
                                                                <label class="custom-file-label" for="FileKkAyah">Pilih file KK Ayah</label>
                                                            </div>
                                                            <!-- div input-group-append dihapus -->
                                                        </div>
                                                        <small id="FileKKAyahError" class="text-danger d-none"></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="bg-success p-2">
                                                <h5 class="mb-0 text-white">Data Ibu Kandung</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Bagian Data Ibu -->
                                    <div id="DataProfilIbuDiv">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="NamaIbu">Nama Ibu Kandung<span class="text-danger font-weight-bold">*</span></label>
                                                    <input type="text" class="form-control name-input" id="NamaIbu" name="NamaIbu" placeholder="Ketik nama lengkap ibu kandung" <?= $required ?>>
                                                    <span id="NamaIbuError" class="text-danger" style="display:none;">Nama Ibu Kandung diperlukan.</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="StatusIbu">Status Ibu<span class="text-danger font-weight-bold">*</span></label>
                                                    <select class="form-control" id="StatusIbu" name="StatusIbu" <?= $required ?>>
                                                        <option value="">Pilih Status</option>
                                                        <option value="Masih Hidup">Masih Hidup</option>
                                                        <option value="Sudah Meninggal">Sudah Meninggal</option>
                                                        <option value="Tidak Diketahui">Tidak Diketahui</option>
                                                    </select>
                                                    <span id="StatusIbuError" class="text-danger" style="display:none;">Status Ibu diperlukan.</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="DataProfilIbuDetailDiv">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="NikIbu">NIK Ibu</label>
                                                        <input type="text" class="form-control number-only" id="NikIbu" name="NikIbu" placeholder="Masukkan NIK ibu">
                                                        <span id="NikIbuError" class="text-danger" style="display:none;">NIK Ibu diperlukan.</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="KewarganegaraanIbu">Kewarganegaraan Ibu</label>
                                                        <select class="form-control" id="KewarganegaraanIbu" name="KewarganegaraanIbu">
                                                            <option value="WNI" selected>Warga Negara Indonesia (WNI)</option>
                                                            <option value="WNA">Warga Negara Asing (WNA)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="TempatLahirIbu">Tempat Lahir Ibu</label>
                                                        <input type="text" class="form-control" id="TempatLahirIbu" name="TempatLahirIbu" placeholder="Masukkan tempat lahir ibu">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="TanggalLahirIbu">Tanggal Lahir Ibu</label>
                                                        <input type="date" class="form-control" id="TanggalLahirIbu" name="TanggalLahirIbu">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="form-group">
                                                            <label for="PendidikanIbu">Pendidikan Terakhir Ibu</label>
                                                            <select class="form-control" id="PendidikanIbu" name="PendidikanIbu">
                                                                <option value="">Pilih Pendidikan</option>
                                                                <option value="Tidak Sekolah">Tidak Sekolah</option>
                                                                <option value="SD">SD</option>
                                                                <option value="SMP">SMP</option>
                                                                <option value="SMA">SMA</option>
                                                                <option value="D1">D1</option>
                                                                <option value="D2">D2</option>
                                                                <option value="D3">D3</option>
                                                                <option value="D4/S1">D4/S1</option>
                                                                <option value="S2">S2</option>
                                                                <option value="S3">S3</option>
                                                                <option value="Lainnya">Lainnya</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="PekerjaanUtamaIbu">Pekerjaan Utama</label>
                                                        <select class="form-control" id="PekerjaanUtamaIbu" name="PekerjaanUtamaIbu">
                                                            <option value="">Pilih Pekerjaan</option>
                                                            <option value="Tidak Bekerja">Tidak Bekerja</option>
                                                            <option value="Nelayan">Nelayan</option>
                                                            <option value="Petani">Petani</option>
                                                            <option value="Peternak">Peternak</option>
                                                            <option value="PNS/TNI/Polri">PNS/TNI/Polri</option>
                                                            <option value="Karyawan Swasta">Karyawan Swasta</option>
                                                            <option value="Pedagang Kecil">Pedagang Kecil</option>
                                                            <option value="Pedagang Besar">Pedagang Besar</option>
                                                            <option value="Wiraswasta">Wiraswasta</option>
                                                            <option value="Wirausaha">Wirausaha</option>
                                                            <option value="Buruh">Buruh</option>
                                                            <option value="Pensiunan">Pensiunan</option>
                                                            <option value="Lainnya">Lainnya</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="PenghasilanUtamaIbu">Penghasilan Utama Ibu</label>
                                                        <select class="form-control" id="PenghasilanUtamaIbu" name="PenghasilanUtamaIbu">
                                                            <option value="">Pilih Penghasilan</option>
                                                            <option value="Tidak Ada">Tidak Ada</option>
                                                            <option value="Kurang dari Rp. 500.000">Kurang dari Rp. 500.000</option>
                                                            <option value="Rp. 500.000 - Rp. 1.000.000">Rp. 500.000 - Rp. 1.000.000</option>
                                                            <option value="Rp. 1.000.000 - Rp. 2.000.000">Rp. 1.000.000 - Rp. 2.000.000</option>
                                                            <option value="Rp. 2.000.000 - Rp. 3.000.000">Rp. 2.000.000 - Rp. 3.000.000</option>
                                                            <option value="Rp. 3.000.000 - Rp. 4.000.000">Rp. 3.000.000 - Rp. 4.000.000</option>
                                                            <option value="Rp. 4.000.000 - Rp. 5.000.000">Rp. 4.000.000 - Rp. 5.000.000</option>
                                                            <option value="Lebih dari Rp. 5.000.000">Lebih dari Rp. 5.000.000</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="NoHpIbu">No Handphone Ibu</label>
                                                        <input type="text" class="form-control number-only" id="NoHpIbu" name="NoHpIbu" placeholder="Masukkan nomor handphone">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="KkIbuSamaDenganAyahAtauSantri" name="KkIbuSamaDenganAyahAtauSantri">
                                                        <label class="form-check-label" for="KkIbuSamaDenganAyahAtauSantri">
                                                            Ibu satu KK dengan Ayah atau Santri
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group" id="FileKKIbuDiv">
                                                        <label for="FileKkIbu">Upload KK Ibu</label>
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input type="file" class="form-control" id="FileKkIbu" name="FileKkIbu" onchange="validateFile('FileKkIbu')" accept=".pdf,.jpg,.jpeg,.png">
                                                                <label class="custom-file-label" for="FileKkIbu">Pilih file KK Ibu</label>
                                                            </div>
                                                            <!-- div input-group-append dihapus -->
                                                        </div>
                                                        <small id="FileKKIbuError" class="text-danger d-none"></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="bg-success p-2">
                                                <h5 class="mb-0 text-white">Data Wali Santri</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Bagian Data Wali -->
                                    <div id="DataProfilWaliDiv">
                                        <div class="form-group">
                                            <label for="StatusWali">Wali</label>
                                            <select class="form-control" id="StatusWali" name="StatusWali" <?= $required ?>>
                                                <option value="">Pilih Wali</option>
                                            </select>
                                            <span id="StatusWaliError" class="text-danger" style="display:none;">Status Wali diperlukan.</span>
                                            <script>
                                                /* ===== Region: Update opsi wali ===== 
                                                 * Fungsi ini memperbarui opsi wali berdasarkan status ayah dan ibu.
                                                 */
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    const waliSelect = document.getElementById('StatusWali');
                                                    const statusAyah = document.getElementById('StatusAyah');
                                                    const statusIbu = document.getElementById('StatusIbu');

                                                    function updateWaliOptions() {
                                                        waliSelect.innerHTML = '<option value="">Pilih Wali</option>';
                                                        if (statusAyah.value === 'Masih Hidup') {
                                                            waliSelect.innerHTML += '<option value="Ayah Kandung">Sama Dengan Ayah Kandung</option>';
                                                        }

                                                        if (statusIbu.value === 'Masih Hidup') {
                                                            waliSelect.innerHTML += '<option value="Ibu Kandung">Sama Dengan Ibu Kandung</option>';
                                                        }

                                                        waliSelect.innerHTML += '<option value="Saudara">Saudara</option>';

                                                        // Tambahkan info berdasarkan status yang dipilih
                                                        const infoDiv = document.createElement('small');
                                                        infoDiv.className = 'form-text text-primary';

                                                        if (statusAyah.value === 'Masih Hidup') {
                                                            infoDiv.innerHTML = '<i class="fas fa-info-circle"></i> Anda dapat memilih ayah kandung sebagai wali';
                                                        } else if (statusIbu.value === 'Masih Hidup') {
                                                            infoDiv.innerHTML = '<i class="fas fa-info-circle"></i> Anda dapat memilih ibu kandung sebagai wali';
                                                        } else {
                                                            infoDiv.className = 'form-text text-warning';
                                                            infoDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Karena status orang tua tidak diketahui/meninggal, silakan pilih wali lainnya';
                                                        }

                                                        // Hapus info sebelumnya jika ada
                                                        const existingInfo = waliSelect.nextElementSibling;
                                                        if (existingInfo && existingInfo.tagName === 'SMALL') {
                                                            existingInfo.remove();
                                                        }

                                                        // Tambahkan info baru
                                                        waliSelect.parentNode.insertBefore(infoDiv, waliSelect.nextSibling);
                                                    }

                                                    statusAyah.addEventListener('change', updateWaliOptions);
                                                    statusIbu.addEventListener('change', updateWaliOptions);

                                                    updateWaliOptions();
                                                });
                                            </script>
                                        </div>
                                        <div id="dataWali" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="NamaWali">Nama Wali<span class="text-danger font-weight-bold">*</span></label>
                                                        <input type="text" class="form-control name-input" id="NamaWali" name="NamaWali" placeholder="Masukkan nama wali">
                                                        <span id="NamaWaliError" class="text-danger" style="display:none;">Nama Wali diperlukan.</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="NikWali">NIK Wali<span class="text-danger font-weight-bold">*</span></label>
                                                        <input type="text" class="form-control" id="NikWali" name="NikWali" placeholder="Masukkan NIK wali">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="KewarganegaraanWali">Kewarganegaraan Wali<span class="text-danger font-weight-bold">*</span></label>
                                                        <select class="form-control" id="KewarganegaraanWali" name="KewarganegaraanWali">
                                                            <option value="">Pilih Kewarganegaraan</option>
                                                            <option value="WNI">Warga Negara Indonesia</option>
                                                            <option value="WNA">Warga Negara Asing</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="TempatLahirWali">Tempat Lahir Wali<span class="text-danger font-weight-bold">*</span></label>
                                                        <input type="text" class="form-control" id="TempatLahirWali" name="TempatLahirWali" placeholder="Masukkan tempat lahir wali">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="TanggalLahirWali">Tanggal Lahir Wali<span class="text-danger font-weight-bold">*</span></label>
                                                        <input type="date" class="form-control" id="TanggalLahirWali" name="TanggalLahirWali">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="PendidikanWali">Pendidikan</label>
                                                        <select class="form-control" id="PendidikanWali" name="PendidikanWali">
                                                            <option value="">Pilih Pendidikan</option>
                                                            <option value="Tidak Sekolah">Tidak Sekolah</option>
                                                            <option value="Putus SD">Putus SD</option>
                                                            <option value="SD Sederajat">SD Sederajat</option>
                                                            <option value="SMP Sederajat">SMP Sederajat</option>
                                                            <option value="SMA Sederajat">SMA Sederajat</option>
                                                            <option value="D1">D1</option>
                                                            <option value="D2">D2</option>
                                                            <option value="D3">D3</option>
                                                            <option value="D4/S1">D4/S1</option>
                                                            <option value="S2">S2</option>
                                                            <option value="S3">S3</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="PekerjaanUtamaWali">Pekerjaan Utama</label>
                                                        <select class="form-control" id="PekerjaanUtamaWali" name="PekerjaanUtamaWali">
                                                            <option value="">Pilih Pekerjaan</option>
                                                            <option value="Tidak Bekerja">Tidak Bekerja</option>
                                                            <option value="Nelayan">Nelayan</option>
                                                            <option value="Petani">Petani</option>
                                                            <option value="Peternak">Peternak</option>
                                                            <option value="PNS/TNI/Polri">PNS/TNI/Polri</option>
                                                            <option value="Karyawan Swasta">Karyawan Swasta</option>
                                                            <option value="Pedagang Kecil">Pedagang Kecil</option>
                                                            <option value="Pedagang Besar">Pedagang Besar</option>
                                                            <option value="Wiraswasta">Wiraswasta</option>
                                                            <option value="Wirausaha">Wirausaha</option>
                                                            <option value="Buruh">Buruh</option>
                                                            <option value="Pensiunan">Pensiunan</option>
                                                            <option value="Lainnya">Lainnya</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="PenghasilanUtamaWali">Penghasilan Utama Wali</label>
                                                        <select class="form-control" id="PenghasilanUtamaWali" name="PenghasilanUtamaWali">
                                                            <option value="">Pilih Penghasilan</option>
                                                            <option value="Tidak Ada">Tidak Ada</option>
                                                            <option value="Kurang dari Rp. 500.000">Kurang dari Rp. 500.000</option>
                                                            <option value="Rp. 500.000 - Rp. 1.000.000">Rp. 500.000 - Rp. 1.000.000</option>
                                                            <option value="Rp. 1.000.000 - Rp. 2.000.000">Rp. 1.000.000 - Rp. 2.000.000</option>
                                                            <option value="Rp. 2.000.000 - Rp. 3.000.000">Rp. 2.000.000 - Rp. 3.000.000</option>
                                                            <option value="Rp. 3.000.000 - Rp. 4.000.000">Rp. 3.000.000 - Rp. 4.000.000</option>
                                                            <option value="Rp. 4.000.000 - Rp. 5.000.000">Rp. 4.000.000 - Rp. 5.000.000</option>
                                                            <option value="Lebih dari Rp. 5.000.000">Lebih dari Rp. 5.000.000</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="NoHpWali">No Handphone Wali</label>
                                                        <input type="text" class="form-control number-only" id="NoHpWali" name="NoHpWali" placeholder="Masukkan nomor handphone">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        /* ===== Region: Menampilkan data wali ===== 
                                         * Fungsi ini menampilkan data wali yang perlu di isi berdasarkan status wali yang dipilih.
                                         */
                                        document.getElementById('StatusWali').addEventListener('change', function() {
                                            var dataWali = document.getElementById('dataWali');
                                            if (this.value === 'Saudara') {
                                                dataWali.style.display = 'block';
                                            } else {
                                                dataWali.style.display = 'none';
                                            }
                                        });
                                    </script>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="NomorKKS">Nomor KKS</label>
                                                <input type="text" class="form-control" id="NomorKKS" name="NomorKKS" placeholder="Masukkan Nomor Kartu Keluarga Sejahtera (KKS)"
                                                    pattern="[0-9]{6}" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                <small class="text-muted">Nomor KKS harus 6 digit angka</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="NomorPKH">Nomor PKH</label>
                                                <input type="text" class="form-control" id="NomorPKH" name="NomorPKH" placeholder="Masukkan Nomor Program Keluarga Harapan (PKH)"
                                                    pattern="[0-9]{14}" maxlength="14" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                <small class="text-muted">Nomor PKH harus 14 digit angka</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="FileKKS">Upload File KKS</label>
                                                <div class="input-group mb-3">
                                                    <div class="custom-file">
                                                        <input type="file" class="form-control" id="FileKKS" name="FileKKS" accept=".pdf,.jpg,.jpeg,.png">
                                                        <label class="custom-file-label" for="FileKKS">Pilih file KKS</label>
                                                    </div>
                                                </div>
                                                <small id="FileKKSError" class="text-danger d-none"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="FilePKH">Upload File PKH</label>
                                                <div class="input-group mb-3">
                                                    <div class="custom-file">
                                                        <input type="file" class="form-control" id="FilePKH" name="FilePKH" accept=".pdf,.jpg,.jpeg,.png">
                                                        <label class="custom-file-label" for="FilePKH">Pilih file PKH</label>
                                                    </div>
                                                </div>
                                                <small id="FilePKHError" class="text-danger d-none"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-secondary" onclick="stepper.previous()">Sebelumnya</button>
                                    <button type="button" class="btn btn-primary" onclick="validateAndNext('ortu-part')">Selanjutnya</button>
                                </div>
                                <!-- Bagian Alamat Orang Tua dan Santri beserta jarak tempat tinggal santri ke lembaga-->
                                <div id="alamat-part" class="content" role="tabpanel" aria-labelledby="alamat-part-trigger">
                                    <!-- bagian data alamat ayah -->
                                    <br>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> <small>Silakan mengisi data alamat orang tua atau wali dengan benar!</small>
                                    </div>
                                    <div id="DataAlamatAyahDiv">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="bg-success p-2">
                                                    <h5 class="mb-0 text-white" id="HeaderDataAlamatAyah">Data Alamat Ayah Kandung</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="TinggalDiluarNegeriAyah" name="TinggalDiluarNegeriAyah">
                                                    <label class="form-check-label" for="TinggalDiluarNegeriAyah">
                                                        Tinggal Di Luar Daerah atau Luar Negeri
                                                    </label>
                                                    <small class="form-text text-primary">
                                                        <i class="fas fa-info-circle"></i> Centang jika ayah tinggal di luar daerah atau luar negeri
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="DataAlamatAyahProvinsiDiv">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="StatusKepemilikanRumahAyah">Status Kepemilikan Rumah</label>
                                                        <select class="form-control" id="StatusKepemilikanRumahAyah" name="StatusKepemilikanRumahAyah">
                                                            <option value="">-- Pilih Status Kepemilikan Rumah --</option>
                                                            <option value="Milik Sendiri">Milik Sendiri</option>
                                                            <option value="Rumah Orang Tua">Rumah Orang Tua</option>
                                                            <option value="Rumah Saudara/Kerabat">Rumah Saudara/Kerabat</option>
                                                            <option value="Rumah Dinas">Rumah Dinas</option>
                                                            <option value="Sewa/Kontrak">Sewa/Kontrak</option>
                                                            <option value="Lainnya">Lainnya</option>
                                                        </select>
                                                        <span id="StatusKepemilikanRumahAyahError" class="text-danger" style="display:none;">Status kepemilikan rumah diperlukan.</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="ProvinsiAyah">Provinsi</label>
                                                        <input type="text" class="form-control" id="ProvinsiAyah" name="ProvinsiAyah" value="Kepulauan Riau" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="KabupatenKotaAyah">Kabupaten/Kota</label>
                                                        <input type="text" class="form-control" id="KabupatenKotaAyah" name="KabupatenKotaAyah" value="Bintan" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="KecamatanAyah">Kecamatan</label>
                                                        <input type="text" class="form-control" id="KecamatanAyah" name="KecamatanAyah" value="Seri Kuala Lobam" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="KodePosAyah">Kode Pos</label>
                                                        <input type="text" class="form-control number-only" id="KodePosAyah" name="KodePosAyah" value="29152" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="KelurahanDesaAyah">Kelurahan/Desa</label>
                                                        <select class="form-control" id="KelurahanDesaAyah" name="KelurahanDesaAyah">
                                                            <option value="">Pilih Kelurahan/Desa</option>
                                                            <option value="TELUK LOBAM">TELUK LOBAM</option>
                                                            <option value="TANJUNG PERMAI">TANJUNG PERMAI</option>
                                                            <option value="BUSUNG">BUSUNG</option>
                                                            <option value="TELUK SASAH">TELUK SASAH</option>
                                                            <option value="KUALA SEMPANG">KUALA SEMPANG</option>
                                                        </select>
                                                        <span id="KelurahanDesaAyahError" class="text-danger" style="display:none;">Kelurahan/Desa diperlukan.</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="RWAyah">RW</label>
                                                        <input type="text" class="form-control" id="RWAyah" name="RWAyah" placeholder="Masukkan RW">
                                                        <span id="RWAyahError" class="text-danger" style="display:none;">RW diperlukan.</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="RTAyah">RT</label>
                                                        <input type="text" class="form-control" id="RTAyah" name="RTAyah" placeholder="Masukkan RT">
                                                        <span id="RTAyahError" class="text-danger" style="display:none;">RT diperlukan.</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="AlamatAyah">Alamat</label>
                                                    <input type="text" class="form-control" id="AlamatAyah" name="AlamatAyah" placeholder="Masukkan Alamat">
                                                    <span id="AlamatAyahError" class="text-danger" style="display:none;">Alamat diperlukan.</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- bagian data alamat ibu -->
                                    <div id="DataAlamatIbuDiv">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="bg-success p-2">
                                                    <h5 id="HeaderDataAlamatIbu" class="mb-0 text-white">Data Alamat Ibu Kandung</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div class="form-check" id="AlamatIbuSamaDenganAyahDiv">
                                                    <input class="form-check-input" type="checkbox" id="AlamatIbuSamaDenganAyah" name="AlamatIbuSamaDenganAyah" checked>
                                                    <label class="form-check-label" for="AlamatIbuSamaDenganAyah">Sama Dengan Ayah Kandung</label>
                                                    <small class="form-text text-primary">
                                                        <i class="fas fa-info-circle"></i> Centang dirubah jika ibu dan ayah tinggal di rumah yang berbeda
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="DataAlamatIbuDetailDiv">
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="TinggalDiluarNegeriIbu" name="TinggalDiluarNegeriIbu">
                                                        <label class="form-check-label" for="TinggalDiluarNegeriIbu">Tinggal Di Luar Daerah atau Luar Negeri</label>
                                                        <small class="form-text text-primary">
                                                            <i class="fas fa-info-circle"></i> Centang jika ibu tinggal di luar daerah atau luar negeri
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- -->
                                            <div id="DataAlamatIbuProvinsiDiv">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="StatusKepemilikanRumahIbu">Status Kepemilikan Rumah</label>
                                                            <select class="form-control" id="StatusKepemilikanRumahIbu" name="StatusKepemilikanRumahIbu">
                                                                <option value="">Pilih Status Kepemilikan</option>
                                                                <option value="Milik Sendiri">Milik Sendiri</option>
                                                                <option value="Rumah Orang Tua">Rumah Orang Tua</option>
                                                                <option value="Rumah Saudara/Kerabat">Rumah Saudara/Kerabat</option>
                                                                <option value="Rumah Dinas">Rumah Dinas</option>
                                                                <option value="Sewa/Kontrak">Sewa/Kontrak</option>
                                                                <option value="Lainnya">Lainnya</option>
                                                            </select>
                                                            <span id="StatusKepemilikanRumahIbuError" class="text-danger" style="display:none;">Status Kepemilikan Rumah diperlukan.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="ProvinsiIbu">Provinsi</label>
                                                            <input type="text" class="form-control" id="ProvinsiIbu" name="ProvinsiIbu" value="Kepulauan Riau" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="KabupatenKotaIbu">Kabupaten/Kota</label>
                                                            <input type="text" class="form-control" id="KabupatenKotaIbu" name="KabupatenKotaIbu" value="Bintan" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="KecamatanIbu">Kecamatan</label>
                                                            <input type="text" class="form-control" id="KecamatanIbu" name="KecamatanIbu" value="Seri Kuala Lobam" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="KodePosIbu">Kode Pos</label>
                                                            <input type="text" class="form-control number-only" id="KodePosIbu" name="KodePosIbu" value="29152" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="KelurahanDesaIbu">Kelurahan/Desa</label>
                                                            <select class="form-control" id="KelurahanDesaIbu" name="KelurahanDesaIbu">
                                                                <option value="">Pilih Kelurahan/Desa</option>
                                                                <option value="TELUK LOBAM">TELUK LOBAM</option>
                                                                <option value="TANJUNG PERMAI">TANJUNG PERMAI</option>
                                                                <option value="BUSUNG">BUSUNG</option>
                                                                <option value="TELUK SASAH">TELUK SASAH</option>
                                                                <option value="KUALA SEMPANG">KUALA SEMPANG</option>
                                                            </select>
                                                            <span id="KelurahanDesaIbuError" class="text-danger" style="display:none;">Kelurahan/Desa diperlukan.</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="RWIbu">RW</label>
                                                            <input type="text" class="form-control" id="RWIbu" name="RWIbu" placeholder="Masukkan RW">
                                                            <span id="RWIbuError" class="text-danger" style="display:none;">RW diperlukan.</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="RTIbu">RT</label>
                                                            <input type="text" class="form-control" id="RTIbu" name="RTIbu" placeholder="Masukkan RT">
                                                            <span id="RTIbuError" class="text-danger" style="display:none;">RT diperlukan.</span>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="AlamatIbu">Alamat</label>
                                                        <input type="text" class="form-control" id="AlamatIbu" name="AlamatIbu" placeholder="Masukkan Alamat">
                                                        <span id="AlamatIbuError" class="text-danger" style="display:none;">Alamat diperlukan.</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- -->
                                        </div>
                                    </div>
                                    <!-- bagian data alamat santri -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="bg-success p-2">
                                                <h5 class="mb-0 text-white">Data Alamat Santri</h5>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="StatusMukim">Status MUKIM</label>
                                                <input type="text" class="form-control" id="StatusMukim" name="StatusMukim" value="Tidak Mukim" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="StatusTempatTinggalSantri">Status Tempat Tinggal <span class="text-danger font-weight-bold">*</span></label>
                                                <select class="form-control" id="StatusTempatTinggalSantri" name="StatusTempatTinggalSantri" <?= $required ?>>
                                                    <option value="">Pilih Status Tempat Tinggal</option>
                                                    <script>
                                                        /* ===== Region: Mengupdate Pilihan Status Tempat Tinggal Santri berdasarkan Status Orang Tua dan Wali ===== 
                                                         * Fungsi ini berfungsi untuk mengupdate pilihan status tempat tinggal santri berdasarkan status orang tua (hidup/meninggal) dan lokasi tinggal mereka, serta status wali.
                                                         *
                                                         * Alur kerja:
                                                         * 1. Mengambil elemen select status tempat tinggal santri.
                                                         * 2. Mengambil status ayah dan ibu (hidup/meninggal).
                                                         * 3. Mengambil status wali yang dipilih.
                                                         * 4. Mengupdate pilihan berdasarkan:
                                                         *    - Jika ayah masih hidup dan tidak tinggal di luar negeri, tambah opsi 'Tinggal dengan Ayah Kandung'.
                                                         *    - Jika ibu masih hidup dan tidak tinggal di luar negeri, tambah opsi 'Tinggal dengan Ibu Kandung'.
                                                         *    - Jika wali berstatus 'Saudara', tambah opsi 'Tinggal dengan Wali'.
                                                         *    - Tambah pilihan statis 'Lainnya'.
                                                         * 5. Mengupdate pilihan saat ada perubahan status orang tua/wali.
                                                         * 6. Mengupdate pilihan saat ada perubahan lokasi tinggal orang tua.
                                                         */
                                                        document.addEventListener('DOMContentLoaded', function() {
                                                            const StatusTempatTinggalSantri = document.getElementById('StatusTempatTinggalSantri');
                                                            const statusAyah = document.getElementById('StatusAyah');
                                                            const statusIbu = document.getElementById('StatusIbu');
                                                            const statusWali = document.getElementById('StatusWali');

                                                            function updateOptions() {
                                                                // Reset pilihan
                                                                StatusTempatTinggalSantri.innerHTML = '<option value="">Pilih Status Tempat Tinggal</option>';

                                                                // Tambahkan pilihan tempat tinggal berdasarkan status orang tua (hidup/meninggal) dan lokasi tinggal
                                                                // Tambahkan opsi tinggal dengan ayah jika ayah masih hidup dan tidak tinggal di luar negeri
                                                                if (statusAyah.value === 'Masih Hidup' && !document.getElementById('TinggalDiluarNegeriAyah').checked) {
                                                                    StatusTempatTinggalSantri.innerHTML += '<option value="Tinggal dengan Ayah Kandung">Tinggal dengan Ayah Kandung</option>';
                                                                }
                                                                // Tambahkan opsi tinggal dengan ibu jika ibu masih hidup dan tidak tinggal di luar negeri
                                                                if (statusIbu.value === 'Masih Hidup' && !document.getElementById('TinggalDiluarNegeriIbu').checked) {
                                                                    StatusTempatTinggalSantri.innerHTML += '<option value="Tinggal dengan Ibu Kandung">Tinggal dengan Ibu Kandung</option>';
                                                                } // jika wali sudah diisi
                                                                if (statusWali.value == 'Saudara') {
                                                                    StatusTempatTinggalSantri.innerHTML += '<option value="Tinggal dengan Wali">Tinggal dengan Wali</option>';
                                                                }
                                                                // Tambahkan pilihan statis lainnya
                                                                StatusTempatTinggalSantri.innerHTML += `
                                                                        <option value="Lainnya">Lainnya</option>
                                                                    `;
                                                            }

                                                            // Perbarui pilihan saat status orang tua berubah
                                                            statusAyah.addEventListener('change', updateOptions);
                                                            statusIbu.addEventListener('change', updateOptions);
                                                            statusWali.addEventListener('change', updateOptions);

                                                            // Initial update
                                                            updateOptions();

                                                            // Update saat checkbox tinggal di luar negeri berubah
                                                            document.getElementById('TinggalDiluarNegeriAyah').addEventListener('change', updateOptions);
                                                            document.getElementById('TinggalDiluarNegeriIbu').addEventListener('change', updateOptions);
                                                            // Update saat checkbox alamat ibu sama dengan ayah berubah
                                                            document.getElementById('AlamatIbuSamaDenganAyah').addEventListener('change', updateOptions);
                                                        });
                                                    </script>
                                                </select>
                                                <span id="StatusTempatTinggalSantriError" class="text-danger" style="display:none;">Status tempat tinggal diperlukan.</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="DataAlamatSantriProvinsiDiv">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="ProvinsiSantri">Provinsi</label>
                                                    <input type="text" class="form-control" id="ProvinsiSantri" name="ProvinsiSantri" value="Kepulauan Riau" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="KabupatenKotaSantri">Kabupaten/Kota</label>
                                                    <input type="text" class="form-control" id="KabupatenKotaSantri" name="KabupatenKotaSantri" value="Bintan" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="KecamatanSantri">Kecamatan</label>
                                                    <input type="text" class="form-control" id="KecamatanSantri" name="KecamatanSantri" value="Seri Kuala Lobam" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="KodePosSantri">Kode Pos</span></label>
                                                    <input type="text" class="form-control number-only" id="KodePosSantri" name="KodePosSantri" value="29152" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="KelurahanDesaSantri">Kelurahan/Desa</label>
                                                    <select class="form-control" id="KelurahanDesaSantri" name="KelurahanDesaSantri">
                                                        <option value="">Pilih Kelurahan/Desa</option>
                                                        <option value="TELUK LOBAM">TELUK LOBAM</option>
                                                        <option value="TANJUNG PERMAI">TANJUNG PERMAI</option>
                                                        <option value="BUSUNG">BUSUNG</option>
                                                        <option value="TELUK SASAH">TELUK SASAH</option>
                                                        <option value="KUALA SEMPANG">KUALA SEMPANG</option>
                                                    </select>
                                                    <span id="KelurahanDesaSantriError" class="text-danger" style="display:none;">Kelurahan/Desa diperlukan.</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="RWSantri">RW</label>
                                                    <input type="text" class="form-control" id="RWSantri" name="RWSantri" placeholder="Masukkan RW">
                                                    <span id="RWSantriError" class="text-danger" style="display:none;">RW diperlukan.</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="RTSantri">RT</label>
                                                    <input type="text" class="form-control" id="RTSantri" name="RTSantri" placeholder="Masukkan RT">
                                                    <span id="RTSantriError" class="text-danger" style="display:none;">RT diperlukan.</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="AlamatSantri">Alamat</label>
                                                    <input type="text" class="form-control" id="AlamatSantri" name="AlamatSantri" placeholder="Masukkan Alamat">
                                                    <span id="AlamatSantriError" class="text-danger" style="display:none;">Alamat diperlukan.</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- bagian data jarak tempat tinggal santri ke lembaga -->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="JarakTempuhSantri">Jarak Tempat Tinggal ke Lembaga<span class="text-danger font-weight-bold">*</span></label>
                                                <select class="form-control" id="JarakTempuhSantri" name="JarakTempuhSantri" <?= $required ?>>
                                                    <option value="">Pilih Jarak</option>
                                                    <option value="Kurang dari 5 km" selected>Kurang dari 5 km</option>
                                                    <option value="Antara 5 - 10 Km">Antara 5 - 10 Km</option>
                                                    <option value="Antara 11 - 20 Km">Antara 11 - 20 Km</option>
                                                    <option value="Antara 21 - 30 Km">Antara 21 - 30 Km</option>
                                                    <option value="Lebih dari 30 Km">Lebih dari 30 Km</option>
                                                </select>
                                                <span id="JarakTempuhSantriError" class="text-danger" style="display:none;">Jarak tempuh diperlukan.</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="TransportasiSantri">Transportasi ke Lembaga<span class="text-danger font-weight-bold">*</span></label>
                                                <select class="form-control" id="TransportasiSantri" name="TransportasiSantri" <?= $required ?>>
                                                    <option value="">Pilih Transportasi</option>
                                                    <option value="Jalan Kaki">Jalan Kaki</option>
                                                    <option value="Sepeda">Sepeda</option>
                                                    <option value="Sepeda Motor">Sepeda Motor</option>
                                                    <option value="Mobil Pribadi">Mobil Pribadi</option>
                                                    <option value="Antar Jemput Sekolah">Antar Jemput Sekolah</option>
                                                    <option value="Angkutan Umum">Angkutan Umum</option>
                                                    <option value="Perahu/Sampan">Perahu/Sampan</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                    <option value="Kendaraan Pribadi">Kendaraan Pribadi</option>
                                                    <option value="Ojek">Ojek</option>
                                                    <option value="Andong/Bendi/Sado/Dokar/Delman/Becak">Andong/Bendi/Sado/Dokar/Delman/Becak</option>
                                                </select>
                                                <span id="TransportasiSantriError" class="text-danger" style="display:none;">Transportasi diperlukan.</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="WaktuTempuhSantri">Waktu Tempuh<span class="text-danger font-weight-bold">*</span></label>
                                                <select class="form-control" id="WaktuTempuhSantri" name="WaktuTempuhSantri" <?= $required ?>>
                                                    <option value="">Pilih Waktu Tempuh</option>
                                                    <option value="1-10 menit" selected>1-10 menit</option>
                                                    <option value="10-19 menit">10-19 menit</option>
                                                    <option value="20-29 menit">20-29 menit</option>
                                                    <option value="30-39 menit">30-39 menit</option>
                                                    <option value="1-2 jam">1-2 jam</option>
                                                    <option value="> 2 jam">> 2 jam</option>
                                                </select>
                                                <span id="WaktuTempuhSantriError" class="text-danger" style="display:none;">Waktu tempuh diperlukan.</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="TitikKoordinatSantri">Titik Koordinat</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="TitikKoordinatSantri" name="TitikKoordinatSantri" placeholder="Titik Koordinat" readonly>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="button" id="getLocationBtn">
                                                            <i class="fas fa-map-marker-alt"></i> Dapatkan Lokasi
                                                        </button>
                                                    </div>
                                                </div>
                                                <small class="form-text text-primary">Klik tombol <strong>Dapatkan Lokasi</strong> untuk mendapatkan koordinat otomatis</small>
                                                <span id="TitikKoordinatSantriError" class="text-danger" style="display:none;">Titik koordinat diperlukan.</span>
                                                <script>
                                                    document.getElementById('getLocationBtn').addEventListener('click', function() {
                                                        // Reset tampilan field dan error message
                                                        const koordinatField = document.getElementById('TitikKoordinatSantri');
                                                        const errorSpan = document.getElementById('TitikKoordinatSantriError');

                                                        koordinatField.style.border = '1px solid #ced4da'; // Reset border ke default
                                                        koordinatField.style.backgroundColor = ''; // Reset background
                                                        errorSpan.style.display = 'none'; // Sembunyikan pesan error

                                                        if (navigator.geolocation) {
                                                            navigator.geolocation.getCurrentPosition(function(position) {
                                                                var lat = position.coords.latitude;
                                                                var lng = position.coords.longitude;
                                                                koordinatField.value = lat + ', ' + lng;
                                                                validateField(koordinatField);
                                                            }, function() {
                                                                alert('Tidak dapat mengakses lokasi. Pastikan Anda mengizinkan akses lokasi.');
                                                            });
                                                        } else {
                                                            alert('Geolocation tidak didukung oleh browser Anda.');
                                                        }
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- bagian tombol navigasi -->

                                    <button type="button" class="btn btn-secondary" onclick="stepper.previous()">Sebelumnya</button>
                                    <button type="button" class="btn btn-primary" onclick="showPreview()">Pratinjau</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    Melihat <a href="<?= base_url('backend/santri/showSantriBaru') ?>">data yang sudah masuk</a>.
                </div>
            </div>
            <!-- /.card -->
        </div>
        <!-- /.row -->
    </div>
</div>

<!-- ============================================= Start Modal Preview ============================================= -->
<!-- =============================================================================================================== -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="previewModalLabel">Pratinjau Data Santri</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> <strong>Perhatian!</strong></h5>
                    <p>Mohon periksa kembali data sebelum mengirim:</p>
                    <ul>
                        <li>Pastikan semua data sudah benar</li>
                        <li>Data yang terkirim tidak dapat diubah</li>
                        <li><small>Tekan tombol <strong>[Kirim Data]</strong> jika sudah yakin</small></li>
                        <li><small>Tekan tombol <strong>[Ubah Data]</strong> untuk memperbaiki</small></li>
                        <li><small>Tekan tombol <strong>[Cetak PDF]</strong> untuk menyimpan salinan data</small></li>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-tabs">
                            <div class="card-header p-0 pt-1">
                                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-santri-tab" data-toggle="pill" href="#dataSantri" role="tab" aria-controls="dataSantri" aria-selected="true">Data Santri</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-orangtua-tab" data-toggle="pill" href="#dataOrangTua" role="tab" aria-controls="dataOrangTua" aria-selected="false">Data Orang Tua</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-alamat-tab" data-toggle="pill" href="#dataAlamat" role="tab" aria-controls="dataAlamat" aria-selected="false">Data Alamat</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-one-tabContent">
                                    <!-- Tab Data Santri -->
                                    <div class="tab-pane fade show active" id="dataSantri" role="tabpanel" aria-labelledby="custom-tabs-santri-tab">
                                        <div class="row">
                                            <div class="col-md-3 text-center">
                                                <img id="previewFotoSantri" src="" alt="Foto Santri" class="img-thumbnail mb-2" style="max-width: 200px">
                                                <p class="font-weight-bold" id="previewNamaSantri"></p>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th width="30%">Nama TPQ</th>
                                                            <td id="previewIdTpq"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Nama Kelas</th>
                                                            <td id="previewIdKelas"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>NIK</th>
                                                            <td id="previewNikSantri"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Nama Kepala Keluarga</th>
                                                            <td id="previewNamaKepalaKeluarga"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tempat, Tanggal Lahir</th>
                                                            <td id="previewTtl"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Jenis Kelamin</th>
                                                            <td id="previewJenisKelamin"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Anak Ke / Jumlah Saudara</th>
                                                            <td><span id="previewAnakKe"></span> / <span id="previewJumlahSaudara"></span></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Cita-cita</th>
                                                            <td id="previewCitaCita"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Hobi</th>
                                                            <td id="previewHobi"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Kebutuhan Khusus</th>
                                                            <td id="previewKebutuhanKhusus"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Kebutuhan Disabilitas</th>
                                                            <td id="previewKebutuhanDisabilitas"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Yang Membiayai Sekolah</th>
                                                            <td id="previewYangMembiayaiSekolah"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Nomor HP / Email</th>
                                                            <td><span id="previewNoHpSantri"></span> / <span id="previewEmailSantri"></span></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Nomor KK / File KK</th>
                                                            <td><span id="previewNoKkSantri"></span> / <span id="previewFileKkSantri"></span></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Nomor KIP / File KIP</th>
                                                            <td><span id="previewKip"></span> / <span id="previewFileKip"></span></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab Data Orang Tua -->
                                    <div class="tab-pane fade" id="dataOrangTua" role="tabpanel" aria-labelledby="custom-tabs-orangtua-tab">
                                        <div class="row">
                                            <div class="col-md-6" id="previewDataAyahAllDiv">
                                                <h6 class="font-weight-bold">Data Ayah</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th width="30%">Nama Ayah</th>
                                                            <td id="previewNamaAyah"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Status</th>
                                                            <td id="previewStatusAyah"></td>
                                                        </tr>
                                                        <tbody id="previewDataAyahLainnya">
                                                            <tr>
                                                                <th>Tempat, Tanggal Lahir</th>
                                                                <td id="previewTtlAyah"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>NIK Ayah</th>
                                                                <td id="previewNikAyah"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Pendidikan</th>
                                                                <td id="previewPendidikanAyah"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Pekerjaan</th>
                                                                <td id="previewPekerjaanAyah"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Penghasilan</th>
                                                                <td id="previewPenghasilanAyah"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>No. HP</th>
                                                                <td id="previewNoHpAyah"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Kewarganegaraan</th>
                                                                <td id="previewKewarganegaraanAyah"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>File KK</th>
                                                                <td id="previewFileKkAyah"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-md-6" id="previewDataIbuDiv">
                                                <h6 class="font-weight-bold">Data Ibu</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th width="30%">Nama Ibu</th>
                                                            <td id="previewNamaIbu"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Status</th>
                                                            <td id="previewStatusIbu"></td>
                                                        </tr>
                                                        <tbody id="previewDataIbuLainya">
                                                            <tr>
                                                                <th>Tempat, Tanggal Lahir</th>
                                                                <td id="previewTtlIbu"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>NIK Ibu</th>
                                                                <td id="previewNikIbu"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Pendidikan</th>
                                                                <td id="previewPendidikanIbu"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Pekerjaan</th>
                                                                <td id="previewPekerjaanIbu"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Penghasilan</th>
                                                                <td id="previewPenghasilanIbu"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>No. HP</th>
                                                                <td id="previewNoHpIbu"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Kewarganegaraan</th>
                                                                <td id="previewKewarganegaraanIbu"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>File KK</th>
                                                                <td id="previewFileKkIbu"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3" id="previewDataWaliDiv">
                                                <h6 class="font-weight-bold">Data Wali</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th width="30%">Status Wali</th>
                                                            <td id="previewStatusWali"></td>
                                                        </tr>
                                                        <tbody id="previewDataWaliLainya">
                                                            <tr>
                                                                <th>Nama Wali</th>
                                                                <td id="previewNamaWali"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>NIK Wali</th>
                                                                <td id="previewNikWali"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>No. HP Wali</th>
                                                                <td id="previewNoHpWali"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Tempat, Tanggal Lahir</th>
                                                                <td id="previewTtlWali"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Pendidikan</th>
                                                                <td id="previewPendidikanWali"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Pekerjaan</th>
                                                                <td id="previewPekerjaanWali"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Penghasilan</th>
                                                                <td id="previewPenghasilanWali"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Kewarganegaraan</th>
                                                                <td id="previewKewarganegaraanWali"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab Data Alamat -->
                                    <div class="tab-pane fade" id="dataAlamat" role="tabpanel" aria-labelledby="custom-tabs-alamat-tab">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="font-weight-bold">Alamat Ayah</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th width="30%">Status Kepemilikan</th>
                                                            <td id="previewStatusKepemilikanRumahAyah"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Alamat</th>
                                                            <td id="previewAlamatAyah"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>RT/RW</th>
                                                            <td><span id="previewRTAyah"></span>/<span id="previewRWAyah"></span></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Kelurahan/Desa</th>
                                                            <td id="previewKelurahanDesaAyah"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Kecamatan</th>
                                                            <td id="previewKecamatanAyah"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Kabupaten/Kota</th>
                                                            <td id="previewKabupatenKotaAyah"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Provinsi</th>
                                                            <td id="previewProvinsiAyah"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Kode Pos</th>
                                                            <td id="previewKodePosAyah"></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="font-weight-bold">Alamat Ibu</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th width="30%">Status Kepemilikan</th>
                                                            <td id="previewStatusKepemilikanRumahIbu"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Alamat</th>
                                                            <td id="previewAlamatIbu"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>RT/RW</th>
                                                            <td><span id="previewRTIbu"></span>/<span id="previewRWIbu"></span></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Kelurahan/Desa</th>
                                                            <td id="previewKelurahanDesaIbu"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Kecamatan</th>
                                                            <td id="previewKecamatanIbu"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Kabupaten/Kota</th>
                                                            <td id="previewKabupatenKotaIbu"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Provinsi</th>
                                                            <td id="previewProvinsiIbu"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Kode Pos</th>
                                                            <td id="previewKodePosIbu"></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <h6 class="font-weight-bold">Alamat Santri</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th width="30%">Status Tempat Tinggal</th>
                                                            <td id="previewStatusTempatTinggalSantri"></td>
                                                        </tr>
                                                        <tbody id="previewAlamatSantriLainya">
                                                            <tr>
                                                                <th>Status Mukim</th>
                                                                <td id="previewStatusMukim"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Alamat</th>
                                                                <td id="previewAlamatSantri"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>RT/RW</th>
                                                                <td><span id="previewRTSantri"></span>/<span id="previewRWSantri"></span></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Kelurahan/Desa</th>
                                                                <td id="previewKelurahanDesaSantri"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Kecamatan</th>
                                                                <td id="previewKecamatanSantri"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Kabupaten/Kota</th>
                                                                <td id="previewKabupatenKotaSantri"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Provinsi</th>
                                                                <td id="previewProvinsiSantri"></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Kode Pos</th>
                                                                <td id="previewKodePosSantri"></td>
                                                            </tr>
                                                        </tbody>
                                                        <tr>
                                                            <th>Jarak ke Lembaga</th>
                                                            <td id="previewJarakTempuhSantri"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Transportasi</th>
                                                            <td id="previewTransportasiSantri"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Waktu Tempuh</th>
                                                            <td id="previewWaktuTempuhSantri"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Titik Koordinat</th>
                                                            <td id="previewTitikKoordinatSantri"></td>
                                                        </tr>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-success" onclick="printPDF()">
                    <i class="fas fa-print"></i> Cetak PDF
                </button>
                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Ubah Data</button>
                    <button type="submit" class="btn btn-primary" onclick="submitForm()">Kirim Data</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ============================================= End Modal Preview ================================================ -->

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    /* ===== Region: Menampilkan Preview Data Santri =====
     * Fungsi ini dipanggil saat tombol "Pratinjau" diklik
     * Menampilkan data santri yang telah diisi dalam modal preview
     */
    function showPreview() {
        // Validasi form terlebih dahulu
        if (!validateFormBeforePreview()) {
            return;
        }

        // Bagian Data Santri
        document.getElementById('previewIdTpq').textContent = (() => {
            const tpqSelect = document.getElementById('IdTpq');
            const selectedOption = tpqSelect.options[tpqSelect.selectedIndex];
            return selectedOption ? selectedOption.text : '';
        })();
        document.getElementById('previewIdKelas').textContent = (() => {
            const kelasSelect = document.getElementById('IdKelas');
            const selectedOption = kelasSelect.options[kelasSelect.selectedIndex];
            return selectedOption ? selectedOption.text : '';
        })();
        document.getElementById('previewNamaSantri').textContent = document.getElementById('NamaSantri').value;
        document.getElementById('previewNikSantri').textContent = document.getElementById('NikSantri').value || '-';
        document.getElementById('previewNoKkSantri').textContent = document.getElementById('IdKartuKeluarga').value || '-';
        document.getElementById('previewNamaKepalaKeluarga').textContent = document.getElementById('NamaKepalaKeluarga').value || '-';
        document.getElementById('previewJenisKelamin').textContent = document.getElementById('JenisKelamin').value;
        document.getElementById('previewAnakKe').textContent = document.getElementById('AnakKe').value || '-';
        document.getElementById('previewJumlahSaudara').textContent = document.getElementById('JumlahSaudara').value || '-';
        document.getElementById('previewCitaCita').textContent = document.getElementById('CitaCita').value || '-';
        document.getElementById('previewHobi').textContent = document.getElementById('Hobi').value || '-';
        document.getElementById('previewKebutuhanKhusus').textContent = document.getElementById('KebutuhanKhusus').value || '-';
        document.getElementById('previewKebutuhanDisabilitas').textContent = document.getElementById('KebutuhanDisabilitas').value || '-';
        document.getElementById('previewYangMembiayaiSekolah').textContent = document.getElementById('YangBiayaSekolah').value || '-';
        document.getElementById('previewKip').textContent = document.getElementById('NoKIP').value || '-';
        document.getElementById('previewFileKip').textContent = document.getElementById('FileKIP').files[0]?.name || '-';
        document.getElementById('previewNoHpSantri').textContent = document.getElementById('NoHpSantri').value || '-';
        document.getElementById('previewEmailSantri').textContent = document.getElementById('EmailSantri').value || '-';
        document.getElementById('previewFileKkSantri').textContent = document.getElementById('FileKkSantri').files[0]?.name || '-';

        // Preview foto santri
        const photoInput = document.getElementById('PhotoProfil');
        if (photoInput.files && photoInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewFotoSantri').src = e.target.result;
            }
            reader.readAsDataURL(photoInput.files[0]);
        }

        const tempat = document.getElementById('TempatLahirSantri').value;
        const tanggal = new Date(document.getElementById('TanggalLahirSantri').value)
            .toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        document.getElementById('previewTtl').textContent = `${tempat}, ${tanggal}`;

        // Bagian Data Ayah
        const statusAyah = document.getElementById('StatusAyah').value;
        document.getElementById('previewStatusAyah').textContent = statusAyah;
        document.getElementById('previewNamaAyah').textContent = document.getElementById('NamaAyah').value;
        // Dapatkan tbody data ayah lainnya
        const dataAyahLainnya = document.getElementById('previewDataAyahLainnya');

        if (statusAyah === 'Masih Hidup') {
            // Tampilkan tbody data ayah
            dataAyahLainnya.style.display = 'table-row-group';

            document.getElementById('previewNikAyah').textContent = document.getElementById('NikAyah').value || '-';
            document.getElementById('previewPendidikanAyah').textContent = document.getElementById('PendidikanAyah').value;
            document.getElementById('previewPekerjaanAyah').textContent = document.getElementById('PekerjaanUtamaAyah').value;
            document.getElementById('previewPenghasilanAyah').textContent = document.getElementById('PenghasilanUtamaAyah').value;
            document.getElementById('previewNoHpAyah').textContent = document.getElementById('NoHpAyah').value || '-';
            const tempatAyah = document.getElementById('TempatLahirAyah').value;
            const tanggalAyah = new Date(document.getElementById('TanggalLahirAyah').value)
                .toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            document.getElementById('previewTtlAyah').textContent = `${tempatAyah}, ${tanggalAyah}`;
            document.getElementById('previewKewarganegaraanAyah').textContent = document.getElementById('KewarganegaraanAyah').value;
            document.getElementById('previewFileKkAyah').textContent = document.getElementById('FileKkAyah').files[0]?.name || '-';
        } else {
            // Sembunyikan tbody data ayah
            dataAyahLainnya.style.display = 'none';
        }
        // Bagian Data Ibu
        const statusIbu = document.getElementById('StatusIbu').value;
        document.getElementById('previewStatusIbu').textContent = statusIbu;
        document.getElementById('previewNamaIbu').textContent = document.getElementById('NamaIbu').value;
        const dataIbuLainnya = document.getElementById('previewDataIbuLainya');
        if (statusIbu === 'Masih Hidup') {
            dataIbuLainnya.style.display = 'table-row-group';
            document.getElementById('previewNikIbu').textContent = document.getElementById('NikIbu').value || '-';
            document.getElementById('previewPendidikanIbu').textContent = document.getElementById('PendidikanIbu').value;
            document.getElementById('previewPekerjaanIbu').textContent = document.getElementById('PekerjaanUtamaIbu').value;
            document.getElementById('previewPenghasilanIbu').textContent = document.getElementById('PenghasilanUtamaIbu').value;
            document.getElementById('previewNoHpIbu').textContent = document.getElementById('NoHpIbu').value || '-';
            const tempatIbu = document.getElementById('TempatLahirIbu').value;
            const tanggalIbu = new Date(document.getElementById('TanggalLahirIbu').value)
                .toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            document.getElementById('previewTtlIbu').textContent = `${tempatIbu}, ${tanggalIbu}`;
            document.getElementById('previewKewarganegaraanIbu').textContent = document.getElementById('KewarganegaraanIbu').value;
            document.getElementById('previewFileKkIbu').textContent = document.getElementById('FileKkIbu').files[0]?.name || '-';
        } else {
            dataIbuLainnya.style.display = 'none';
        }

        // Bagian Data Wali 
        const statusWali = document.getElementById('StatusWali').value;
        document.getElementById('previewStatusWali').textContent = statusWali;
        //jika status wali lainya atau saudara maka tampilkan data wali
        const dataWaliLainnya = document.getElementById('previewDataWaliLainya');
        if (statusWali === 'Saudara') {
            dataWaliLainnya.style.display = 'table-row-group';
            document.getElementById('previewNamaWali').textContent = document.getElementById('NamaWali').value || '-';
            document.getElementById('previewNikWali').textContent = document.getElementById('NikWali').value || '-';
            document.getElementById('previewNoHpWali').textContent = document.getElementById('NoHpWali').value || '-';
            // Format TTL Wali
            const tempatWali = document.getElementById('TempatLahirWali').value;
            const tanggalWali = new Date(document.getElementById('TanggalLahirWali').value)
                .toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            document.getElementById('previewTtlWali').textContent = `${tempatWali}, ${tanggalWali}`;
            document.getElementById('previewKewarganegaraanWali').textContent = document.getElementById('KewarganegaraanWali').value || '-';
            document.getElementById('previewPendidikanWali').textContent = document.getElementById('PendidikanWali').value || '-';
            document.getElementById('previewPekerjaanWali').textContent = document.getElementById('PekerjaanUtamaWali').value || '-';
            document.getElementById('previewPenghasilanWali').textContent = document.getElementById('PenghasilanUtamaWali').value || '-';
            document.getElementById('previewNoHpWali').textContent = document.getElementById('NoHpWali').value || '-';
        } else {
            dataWaliLainnya.style.display = 'none';
        }
        // Bagian  Alamat Ayah
        document.getElementById('previewStatusKepemilikanRumahAyah').textContent = document.getElementById('StatusKepemilikanRumahAyah').value;
        document.getElementById('previewAlamatAyah').textContent = document.getElementById('AlamatAyah').value;
        document.getElementById('previewRTAyah').textContent = document.getElementById('RTAyah').value;
        document.getElementById('previewRWAyah').textContent = document.getElementById('RWAyah').value;
        document.getElementById('previewKelurahanDesaAyah').textContent = document.getElementById('KelurahanDesaAyah').value;
        document.getElementById('previewKecamatanAyah').textContent = document.getElementById('KecamatanAyah').value;
        document.getElementById('previewKabupatenKotaAyah').textContent = document.getElementById('KabupatenKotaAyah').value;
        document.getElementById('previewProvinsiAyah').textContent = document.getElementById('ProvinsiAyah').value;
        document.getElementById('previewKodePosAyah').textContent = document.getElementById('KodePosAyah').value;

        // Alamat Ibu
        document.getElementById('previewStatusKepemilikanRumahIbu').textContent = document.getElementById('StatusKepemilikanRumahIbu').value;
        document.getElementById('previewAlamatIbu').textContent = document.getElementById('AlamatIbu').value;
        document.getElementById('previewRTIbu').textContent = document.getElementById('RTIbu').value;
        document.getElementById('previewRWIbu').textContent = document.getElementById('RWIbu').value;
        document.getElementById('previewKelurahanDesaIbu').textContent = document.getElementById('KelurahanDesaIbu').value;
        document.getElementById('previewKecamatanIbu').textContent = document.getElementById('KecamatanIbu').value;
        document.getElementById('previewKabupatenKotaIbu').textContent = document.getElementById('KabupatenKotaIbu').value;
        document.getElementById('previewProvinsiIbu').textContent = document.getElementById('ProvinsiIbu').value;
        document.getElementById('previewKodePosIbu').textContent = document.getElementById('KodePosIbu').value;

        // Alamat Santri
        // check status tempat tinggal  
        const StatusTempatTinggalSantri = document.getElementById('StatusTempatTinggalSantri').value;
        document.getElementById('previewStatusTempatTinggalSantri').textContent = StatusTempatTinggalSantri;
        document.getElementById('previewStatusMukim').textContent = document.getElementById('StatusMukim').value;
        //jika status tempat tinggal lainnya maka tampilkan alamat santri   
        const dataAlamatSantri = document.getElementById('previewAlamatSantriLainya');
        if (StatusTempatTinggalSantri === 'Lainnya' || StatusTempatTinggalSantri === 'Tinggal dengan Wali' || StatusTempatTinggalSantri === '') {
            dataAlamatSantri.style.display = 'table-row-group';
            document.getElementById('previewAlamatSantri').textContent = document.getElementById('AlamatSantri').value;
            document.getElementById('previewRTSantri').textContent = document.getElementById('RTSantri').value;
            document.getElementById('previewRWSantri').textContent = document.getElementById('RWSantri').value;
            document.getElementById('previewKelurahanDesaSantri').textContent = document.getElementById('KelurahanDesaSantri').value;
            document.getElementById('previewKecamatanSantri').textContent = document.getElementById('KecamatanSantri').value;
            document.getElementById('previewKabupatenKotaSantri').textContent = document.getElementById('KabupatenKotaSantri').value;
            document.getElementById('previewProvinsiSantri').textContent = document.getElementById('ProvinsiSantri').value;
            document.getElementById('previewKodePosSantri').textContent = document.getElementById('KodePosSantri').value;
            document.getElementById('previewJarakTempuhSantri').textContent = document.getElementById('JarakTempuhSantri').value;
            document.getElementById('previewTransportasiSantri').textContent = document.getElementById('TransportasiSantri').value;
            document.getElementById('previewWaktuTempuhSantri').textContent = document.getElementById('WaktuTempuhSantri').value;
            document.getElementById('previewTitikKoordinatSantri').textContent = document.getElementById('TitikKoordinatSantri').value;
        } else {
            dataAlamatSantri.style.display = 'none';
        }
        // Tampilkan modal
        $('#previewModal').modal('show');
    }

    function validateFormBeforePreview() {
        // Implementasi validasi form
        if (!validateAndNext('alamat-part')) {
            return false;
        }
        copyAlamatAyahKeIbu();
        copyAlamatSantri();
        return true; // Ganti dengan logika validasi yang sesuai
    }

    function submitForm() {
        document.getElementById('santriForm').submit();
    }

    /* ===== Region: Print PDF Data Santri ===== */
    function printPDF() {
        // Tampilkan loading spinner
        Swal.fire({
            title: 'Sedang menyiapkan PDF...',
            allowOutsideClick: true, // Izinkan klik di luar modal
            showCloseButton: true, // Tampilkan tombol close
            showConfirmButton: false, // Sembunyikan tombol konfirmasi
            didOpen: () => {
                Swal.showLoading();
            }
        }).then((result) => {
            // Jika user menutup modal loading
            if (result.dismiss === Swal.DismissReason.close) {
                // Batalkan request jika masih berjalan
                // Dan bersihkan resources
                console.log('PDF generation cancelled by user');
            }
        });

        // Kumpulkan data dari modal preview
        const previewData = {
            // Data Santri
            printIdTpq: document.getElementById('previewIdTpq').textContent,
            printIdKelas: document.getElementById('previewIdKelas').textContent,
            printNikSantri: document.getElementById('previewNikSantri').textContent,
            printNoKkSantri: document.getElementById('previewNoKkSantri').textContent,
            printNamaSantri: document.getElementById('previewNamaSantri').textContent,
            printTempatTTL: document.getElementById('previewTtl').textContent,
            printNamaKepalaKeluarga: document.getElementById('previewNamaKepalaKeluarga').textContent,
            printJenisKelamin: document.getElementById('previewJenisKelamin').textContent,
            printAnakKe: document.getElementById('previewAnakKe').textContent,
            printJumlahSaudara: document.getElementById('previewJumlahSaudara').textContent,
            printCitaCita: document.getElementById('previewCitaCita').textContent,
            printHobi: document.getElementById('previewHobi').textContent,
            printKebutuhanKhusus: document.getElementById('previewKebutuhanKhusus').textContent,
            printKebutuhanDisabilitas: document.getElementById('previewKebutuhanDisabilitas').textContent,
            printYangMembiayaiSekolah: document.getElementById('previewYangMembiayaiSekolah').textContent,
            printNoHpSantri: document.getElementById('previewNoHpSantri').textContent,
            printEmailSantri: document.getElementById('previewEmailSantri').textContent,
            // Ambil base64 foto dari preview
            printFotoSantri: document.getElementById('previewFotoSantri').src,

            // Data Orang Tua
            printNamaAyah: document.getElementById('previewNamaAyah').textContent,
            printNikAyah: document.getElementById('previewNikAyah').textContent,
            printStatusAyah: document.getElementById('previewStatusAyah').textContent,
            printPendidikanAyah: document.getElementById('previewPendidikanAyah').textContent,
            printPekerjaanAyah: document.getElementById('previewPekerjaanAyah').textContent,
            printPenghasilanAyah: document.getElementById('previewPenghasilanAyah').textContent,
            printNoHpAyah: document.getElementById('previewNoHpAyah').textContent,

            // Data Ibu
            printNamaIbu: document.getElementById('previewNamaIbu').textContent,
            printNikIbu: document.getElementById('previewNikIbu').textContent,
            printStatusIbu: document.getElementById('previewStatusIbu').textContent,
            printPendidikanIbu: document.getElementById('previewPendidikanIbu').textContent,
            printPekerjaanIbu: document.getElementById('previewPekerjaanIbu').textContent,
            printPenghasilanIbu: document.getElementById('previewPenghasilanIbu').textContent,
            printNoHpIbu: document.getElementById('previewNoHpIbu').textContent,

            // Data Alamat
            printAlamatSantri: document.getElementById('previewAlamatSantri').textContent,
            printRTSantri: document.getElementById('previewRTSantri').textContent,
            printRWSantri: document.getElementById('previewRWSantri').textContent,
            printKelurahanDesaSantri: document.getElementById('previewKelurahanDesaSantri').textContent,
            printKecamatanSantri: document.getElementById('previewKecamatanSantri').textContent,
            printKabupatenKotaSantri: document.getElementById('previewKabupatenKotaSantri').textContent,
            printProvinsiSantri: document.getElementById('previewProvinsiSantri').textContent,
            printKodePosSantri: document.getElementById('previewKodePosSantri').textContent,
            printJarakTempuhSantri: document.getElementById('previewJarakTempuhSantri').textContent,
            printTransportasiSantri: document.getElementById('previewTransportasiSantri').textContent,
            printWaktuTempuhSantri: document.getElementById('previewWaktuTempuhSantri').textContent
        };

        // Kirim data ke endpoint PDF menggunakan fetch
        fetch('<?= base_url('backend/santri/generatePDF') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(previewData)
            })
            .then(response => {

                if (!response.ok) {
                    return response.json().then(error => {
                        // Tampilkan log error
                        if (error.logs) {
                            console.log('Error Logs:', error.logs);
                        }
                        throw error;
                    });
                }
                return response.blob();
            })
            .then(blob => {
                // Tutup loading modal
                Swal.close();
                // Handle PDF blob
                const url = window.URL.createObjectURL(blob);
                const namaSantri = previewData.printNamaSantri || 'Santri';
                const cleanNama = namaSantri.replace(/[^a-zA-Z0-9]/g, '_');

                const a = document.createElement('a');
                a.href = url;
                a.download = `Data_${cleanNama}.pdf`;
                document.body.appendChild(a);
                a.click();

                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                // Notifikasi sukses
                Swal.fire({
                    icon: 'success',
                    title: 'PDF berhasil dibuat!',
                    text: 'File PDF telah diunduh ke perangkat Anda.',
                    timer: 2000,
                    showConfirmButton: true
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal membuat PDF',
                    text: error.message || 'Terjadi kesalahan saat membuat file PDF. Silakan coba lagi.',
                    confirmButtonText: 'Tutup'
                });
            });

        // ... kode setelahnya ...
    }
    /* ===== End Region: Print PDF Data Santri ===== */
    /* ===== End Region: Preview Menampilkan Data Santri    ===== */

    /* ===== Region: Inisialisasi Stepper Form =====
     * Fungsi ini dijalankan ketika DOM telah selesai dimuat
     * Menginisialisasi stepper form dan menambahkan validasi pada input required
     * Stepper form digunakan untuk membagi form menjadi beberapa tahap/langkah
     * Validasi input memastikan semua field required telah diisi dengan benar
     */
    document.addEventListener('DOMContentLoaded', function() {
        window.stepper = new Stepper(document.querySelector('.bs-stepper'));

        // Tambahkan event listener untuk semua input required
        document.querySelectorAll('.form-control[required]').forEach(function(field) {
            field.addEventListener('input', function() {
                validateField(this);
            });
        });
    });
    /* ===== End Region: Inisialisasi Stepper Form ===== */

    /* ===== Region: Validasi Input dan Lanjutkan ke Langkah Berikutnya =====
     * Memvalidasi input dan melanjutkan ke langkah berikutnya
     * @param {string} stepId - ID dari langkah yang sedang divalidasi
     */

    /* ===== Region: Validasi Input dan Lanjutkan ke Langkah Berikutnya ===== */
    function validateAndNext(stepId) {
        let isValid = true;
        let firstInvalidField = null;

        // Fungsi untuk set field invalid
        function setInvalidField(field) {
            if (!firstInvalidField) {
                firstInvalidField = field;
                isValid = false;
            }
        }

        // Validasi khusus untuk step santri-part
        if (stepId === 'santri-part') {
            // 1. Validasi foto profil (prioritas pertama)
            const photoProfil = document.getElementById('PhotoProfil');
            const photoPreview = document.getElementById('previewPhotoProfil');
            const photoError = document.getElementById('PhotoProfilError');

            if (!photoProfil.files && photoProfil.hasAttribute('required') || !photoProfil.files[0] && photoProfil.hasAttribute('required')) {
                isValid = false;
                photoError.innerHTML = 'Foto profil harus diupload';
                photoError.style.display = 'block';
                photoPreview.style.border = '2px solid #dc3545';
                setInvalidField(photoProfil);
            } else {
                photoError.style.display = 'none';
                photoPreview.style.border = '2px solid #28a745';
            }
        }

        // Validasi field required lainnya
        const requiredFields = document.querySelectorAll('#' + stepId + ' .form-control[required]');
        requiredFields.forEach(field => {
            // Validasi khusus untuk tanggal lahir
            if (field.id === 'TanggalLahirSantri') {
                if (!field.value.trim()) {
                    validateField(field);
                    setInvalidField(field);
                } else if (!validateTanggalLahir()) {
                    setInvalidField(field);
                }
            }
            // Validasi umum untuk field lainnya
            else if (!field.value.trim()) {
                validateField(field);
                setInvalidField(field);
            }
        });

        // Jika ada field invalid, fokus ke field tersebut
        if (firstInvalidField) {
            // Handle scroll dan fokus
            if (firstInvalidField.id === 'PhotoProfil' && firstInvalidField.hasAttribute('required')) {
                // Scroll ke preview foto
                document.getElementById('previewPhotoProfil').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                // Efek blinking untuk preview foto
                let blinkCount = 0;
                const blinkInterval = setInterval(() => {
                    document.getElementById('previewPhotoProfil').style.border =
                        blinkCount % 2 === 0 ? '2px solid #dc3545' : '2px solid transparent';
                    blinkCount++;
                    if (blinkCount >= 6) {
                        clearInterval(blinkInterval);
                        document.getElementById('previewPhotoProfil').style.border = '2px solid #dc3545';
                    }
                }, 300);
            } else {
                // Scroll ke field invalid
                firstInvalidField.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                // Efek blinking untuk field
                let blinkCount = 0;
                // Tambahkan transisi CSS untuk efek smooth
                firstInvalidField.style.transition = 'background-color 0.3s ease-in-out';

                const blinkInterval = setInterval(() => {
                    firstInvalidField.style.backgroundColor =
                        blinkCount % 2 === 0 ? '#ffd6dc' : 'white';
                    blinkCount++;
                    if (blinkCount >= 4) { // 2x blinking
                        clearInterval(blinkInterval);
                        firstInvalidField.style.backgroundColor = '#ffd6dc'; // Set warna merah muda

                        // Tambahkan event listener untuk menghapus background merah saat input valid
                        firstInvalidField.addEventListener('input', function() {
                            if (this.value.trim() !== '') {
                                // Tambahkan transisi saat menghapus background
                                this.style.transition = 'background-color 0.3s ease-in-out';
                                this.style.backgroundColor = '';

                                // Hapus transisi setelah selesai untuk menghindari efek pada interaksi lainnya
                                setTimeout(() => {
                                    this.style.transition = '';
                                }, 300);
                            }
                        });
                    }
                }, 600);

                // Set focus setelah scroll selesai
                setTimeout(() => {
                    firstInvalidField.focus();
                }, 800);
            }

            return false;
        }

        // Lanjut ke step berikutnya jika semua validasi berhasil
        if (isValid) {
            stepper.next();
            return true;
        }
        return false;
    }
    /* ===== End Region: Validasi Input dan Lanjutkan ke Langkah Berikutnya ===== */

    /* ===== Region: Validasi Input saat Submit Form =====
     * Validasi input saat submit form
     */
    document.getElementById('santriForm').addEventListener('submit', function(event) {
        let isValid = true;
        let allRequiredFields = document.querySelectorAll('.form-control[required]');

        allRequiredFields.forEach(function(field) {
            validateField(field);
            if (field.classList.contains('is-invalid')) {
                isValid = false;
            }
        });

        if (!isValid) {
            event.preventDefault(); // Mencegah pengiriman form jika ada yang tidak valid
            alert('Mohon isi semua bidang yang harus diisi sebelum mengirim formulir.');
        }
    });
    /* ===== End Region: Validasi Input saat Submit Form ===== */

    /* ===== Region: Validasi Input Form =====
     * Validasi input form dan tampilkan error secara dinamis semua input
     * @param {HTMLElement} field - Input yang divalidasi
     */
    function validateField(field) {
        const errorElement = document.getElementById(field.id + 'Error');
        // Jika field adalah input file
        if (field.type === 'file' && field.id !== 'PhotoProfil') {
            // Jika field required dan tidak ada file yang dipilih
            if (field.hasAttribute('required') && (!field.files || !field.files[0])) {
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
                field.style.border = '1px solid #dc3545'; //merah
                if (errorElement) {
                    //errorElement.textContent = 'File KK santri diperlukan';
                    errorElement.classList.remove('d-none');
                }
                return true;
            }
            // Jika ada file yang dipilih, validasi dengan validateFile()
            if (!validateFile(field.id)) {
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
                field.style.border = '1px solid #dc3545'; //merah
                return true;
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
                field.style.border = '1px solid #28a745'; //hijau
                return false;
            }

        }

        // Validasi untuk field non-file
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            field.classList.remove('is-valid');
            field.style.border = '1px solid #dc3545';

            if (errorElement) {
                errorElement.style.display = 'block';
            }
            return false;
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            field.style.border = '1px solid #28a745';

            if (errorElement) {
                errorElement.style.display = 'none';
            }
            return true;
        }
    }
    /* ===== Region: Filter TPQ berdasarkan kelurahan =====
     * Fungsi ini memfilter opsi TPQ berdasarkan kelurahan yang dipilih
     * Menggunakan event listener untuk perubahan pada select kelurahan
     * Menampilkan atau menyembunyikan select TPQ berdasarkan kondisi
     */
    document.addEventListener('DOMContentLoaded', function() {
        const kelurahanSelect = document.getElementById('KelurahanDesaTpq');
        const tpqSelect = document.getElementById('IdTpq');
        const originalTpqOptions = Array.from(tpqSelect.options);

        // Sembunyikan select TPQ saat pertama kali
        tpqSelect.parentElement.style.display = 'none';

        kelurahanSelect.addEventListener('change', function() {
            const selectedKelurahan = this.value;

            // Reset TPQ select
            tpqSelect.innerHTML = '<option value="">Pilih Nama TPQ</option>';

            if (selectedKelurahan) {
                // Tampilkan select TPQ
                tpqSelect.parentElement.style.display = 'block';

                // Filter TPQ berdasarkan kelurahan yang dipilih
                originalTpqOptions.forEach(option => {
                    if (option.value && option.text.includes(selectedKelurahan)) {
                        tpqSelect.add(option.cloneNode(true));
                    }
                });
            } else {
                // Sembunyikan select TPQ jika tidak ada kelurahan yang dipilih
                tpqSelect.parentElement.style.display = 'none';
            }
        });
    });
    /* ===== End Region: Filter TPQ berdasarkan kelurahan ===== */

    /* ===== Region: Menampilkan preview foto profil =====
     * Fungsi ini menampilkan preview foto profil dari input file
     * Validasi file: ukuran maksimal 2MB, tipe file JPG, JPEG, atau PNG
     * Menampilkan preview foto dan menampilkan pesan error jika ada
     */
    function previewPhoto(input) {
        const preview = document.getElementById('previewPhotoProfil');
        const errorDiv = document.getElementById('PhotoProfilError');

        errorDiv.style.display = 'none';

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Validasi ukuran (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                errorDiv.innerHTML = 'Ukuran file ' + file.name + ' (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB) terlalu besar (maksimal 2MB)';
                errorDiv.style.display = 'block';
                input.value = '';
                preview.src = '/images/no-photo.jpg';
                return;
            }

            // Validasi tipe file
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                errorDiv.innerHTML = 'Format file tidak valid (gunakan JPG, JPEG, atau PNG)';
                errorDiv.style.display = 'block';
                input.value = '';
                preview.src = '/images/no-photo.jpg';
                return;
            }

            // Tampilkan preview
            try {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
                errorDiv.style.display = 'none';
                preview.style.border = '2px solid #28a745';
            } catch (error) {
                console.error('Error saat membaca file:', error);
                errorDiv.innerHTML = 'Terjadi kesalahan saat memproses file';
                errorDiv.style.display = 'block';
                preview.src = '/images/no-photo.jpg';
            }
        }
    }
    /* ===== End Region: Menampilkan preview foto profil ===== */

    /* ===== Region: Membuka kamera =====
     * Fungsi ini membuka kamera untuk mengambil foto profil
     * Memastikan browser mendukung getUserMedia
     * Membuat elemen video untuk preview kamera
     */
    function openCamera() {
        // Cek apakah browser mendukung getUserMedia
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            // Buat elemen video untuk preview kamera
            const videoPreview = document.createElement('video');
            videoPreview.autoplay = true;

            // Buat modal untuk menampilkan preview kamera
            const modal = document.createElement('div');
            modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.8);z-index:9999;display:flex;flex-direction:column;justify-content:center;align-items:center;';

            // Tambahkan video ke modal
            modal.appendChild(videoPreview);

            // Tambahkan tombol ambil foto
            const captureBtn = document.createElement('button');
            captureBtn.textContent = 'Ambil Foto';
            captureBtn.className = 'btn btn-primary mt-3';
            modal.appendChild(captureBtn);

            // Tambahkan tombol tutup
            const closeBtn = document.createElement('button');
            closeBtn.textContent = 'Tutup';
            closeBtn.className = 'btn btn-secondary mt-2';
            modal.appendChild(closeBtn);

            // Tambahkan modal ke body
            document.body.appendChild(modal);

            // Minta akses kamera
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(stream => {
                    videoPreview.srcObject = stream;

                    // Handler untuk tombol ambil foto
                    captureBtn.onclick = () => {
                        // Buat canvas untuk mengambil foto
                        const canvas = document.createElement('canvas');
                        canvas.width = videoPreview.videoWidth;
                        canvas.height = videoPreview.videoHeight;
                        canvas.getContext('2d').drawImage(videoPreview, 0, 0);

                        // Konversi ke blob
                        canvas.toBlob(blob => {
                            // Buat file dari blob
                            const file = new File([blob], "camera-photo.jpg", {
                                type: "image/jpeg"
                            });

                            // Buat objek DataTransfer untuk mensimulasikan file input
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);

                            // Update file input dan preview
                            const photoInput = document.getElementById('PhotoProfil');
                            photoInput.files = dataTransfer.files;
                            previewPhoto(photoInput);

                            // Hentikan stream kamera dan tutup modal
                            stream.getTracks().forEach(track => track.stop());
                            document.body.removeChild(modal);
                        }, 'image/jpeg');
                    };

                    // Handler untuk tombol tutup
                    closeBtn.onclick = () => {
                        stream.getTracks().forEach(track => track.stop());
                        document.body.removeChild(modal);
                    };
                })
                .catch(error => {
                    console.error('Error accessing camera:', error);
                    alert('Gagal mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
                    document.body.removeChild(modal);
                });
        } else {
            alert('Browser Anda tidak mendukung akses kamera');
        }
    }
    /* ===== End Region: Membuka kamera ===== */

    /* ===== Region: Validasi Jumlah Saudara dan Anak Ke =====
     * Fungsi ini digunakan untuk memvalidasi input jumlah saudara dan anak ke
     * Memastikan:
     * 1. Input tidak boleh negatif
     * 2. Anak ke tidak boleh lebih besar dari jumlah saudara + 1
     * 3. Menampilkan pesan error jika validasi gagal
     */
    document.addEventListener('DOMContentLoaded', function() {
        const jumlahSaudara = document.getElementById('JumlahSaudara');
        const anakKe = document.getElementById('AnakKe');

        function validateSaudaraAnakKe() {
            const jumlahSaudaraValue = parseInt(jumlahSaudara.value);
            const anakKeValue = parseInt(anakKe.value);

            // Hapus pesan error yang ada
            const existingError = document.getElementById('saudaraAnakKeError');
            if (existingError) {
                existingError.remove();
            }

            // Validasi input harus berupa angka positif
            if (jumlahSaudaraValue < 0 || anakKeValue < 0) {
                showError('Jumlah saudara dan anak ke tidak boleh negatif');
                return false;
            }

            // Validasi maksimal input 10
            if (jumlahSaudaraValue > 10) {
                showError('Sistem saat ini membatasi maksimal 10 saudara. Jika jumlah saudara lebih dari 10, silakan hubungi admin untuk bantuan lebih lanjut.');
                jumlahSaudara.value = '10';
                return false;
            }

            if (anakKeValue > 10) {
                showError('Sistem saat ini membatasi maksimal anak ke-10. Jika nomor urut anak lebih dari 10, silakan hubungi admin untuk bantuan lebih lanjut.');
                anakKe.value = '10';
                return false;
            }

            // Validasi anak ke tidak boleh lebih besar dari jumlah saudara + 1
            if (anakKeValue > (jumlahSaudaraValue + 1)) {
                showError('Anak ke tidak boleh lebih besar dari jumlah saudara + 1');
                return false;
            }

            return true;
        }

        function showError(message) {
            // Hapus pesan error yang ada
            const existingError = document.getElementById('saudaraAnakKeError');
            if (existingError) {
                existingError.remove();
            }

            // Buat elemen error baru
            const errorDiv = document.createElement('div');
            errorDiv.id = 'saudaraAnakKeError';
            errorDiv.className = 'alert alert-danger mt-2';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;

            // Masukkan setelah input anak ke
            anakKe.parentElement.parentElement.appendChild(errorDiv);
        }

        // Tambahkan event listener untuk kedua input
        jumlahSaudara.addEventListener('input', validateSaudaraAnakKe);
        anakKe.addEventListener('input', validateSaudaraAnakKe);
    });
    /* ===== End Region: Validasi Jumlah Saudara dan Anak Ke ===== */

    /* ===== Region: Validasi Nomor Handphone =====
     * Fungsi ini melakukan validasi nomor handphone dengan ketentuan:
     * 1. Hanya menerima input angka (0-9)
     * 2. Panjang nomor antara 10-13 digit
     * 3. Harus diawali dengan 08 atau 62 (format Indonesia)
     * 4. Menampilkan pesan error jika tidak sesuai ketentuan
     * 5. Memformat ulang nomor jika diawali 62 menjadi format 08
     * 6. Diterapkan pada input NoHpAyah, NoHpIbu, dan NoHpWali
     */
    function validatePhoneNumber(input) {
        let phoneNumber = input.value.trim();
        const errorElement = document.getElementById(input.id + 'Error');

        // Cek format awal nomor
        const isValidStart = phoneNumber.startsWith('+62') ||
            phoneNumber.startsWith('08') ||
            phoneNumber.startsWith('8') ||
            phoneNumber.startsWith('62');

        if (!isValidStart) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            if (errorElement) {
                errorElement.textContent = 'Nomor handphone harus diawali dengan +62, 62, 08, atau 8';
                errorElement.style.display = 'block';
            }
            return false;
        }

        // Konversi ke format +62 jika perlu
        if (phoneNumber.startsWith('08')) {
            phoneNumber = '+62' + phoneNumber.substring(1);
        } else if (phoneNumber.startsWith('8')) {
            phoneNumber = '+62' + phoneNumber;
        } else if (phoneNumber.startsWith('62')) {
            phoneNumber = '+' + phoneNumber;
        }

        // Validasi panjang nomor (tidak termasuk +62)
        const numberWithoutCode = phoneNumber.replace(/^\+62/, '');
        const isNumeric = /^\d+$/.test(numberWithoutCode);

        if (!isNumeric || numberWithoutCode.length < 9 || numberWithoutCode.length > 12) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            if (errorElement) {
                errorElement.textContent = 'Panjang nomor handphone harus 9-12 digit setelah kode negara';
                errorElement.style.display = 'block';
            }
            return false;
        }

        // Jika semua validasi berhasil
        input.value = phoneNumber;
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        if (errorElement) {
            errorElement.style.display = 'none';
        }
        return true;
    }

    /* ===== End Region: Validasi Nomor Handphone ===== */

    /* ===== Region: Event listener untuk input nomor handphone =====
     * Event listener untuk input nomor handphone
     */
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInputs = ['NoHpAyah', 'NoHpIbu', 'NoHpWali', 'NoHpSantri'];

        phoneInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                // Tambahkan span untuk pesan error jika belum ada
                let errorElement = document.getElementById(inputId + 'Error');
                if (!errorElement) {
                    errorElement = document.createElement('span');
                    errorElement.id = inputId + 'Error';
                    errorElement.className = 'text-danger';
                    errorElement.style.display = 'none';
                    input.parentNode.appendChild(errorElement);
                }

                // Event untuk input
                input.addEventListener('input', function(e) {
                    // Izinkan tanda + hanya di awal
                    let value = this.value;
                    if (value.length > 1) {
                        value = value.charAt(0) + value.substring(1).replace(/\D/g, '');
                    }
                    if (value.length > 14) { // Batasi panjang total (termasuk +62)
                        value = value.slice(0, 14);
                    }
                    this.value = value;
                    validatePhoneNumber(this);
                });

                // Event untuk keypress - hanya izinkan angka dan + di awal
                input.addEventListener('keypress', function(e) {
                    const char = String.fromCharCode(e.which);
                    if (!(char === '+' && this.value.length === 0) && !/[0-9]/.test(char)) {
                        e.preventDefault();
                    }
                });

                // Event untuk paste
                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    let pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    // Bersihkan teks yang di-paste
                    if (pastedText.startsWith('+')) {
                        pastedText = '+' + pastedText.substring(1).replace(/\D/g, '');
                    } else {
                        pastedText = pastedText.replace(/\D/g, '');
                    }
                    if (pastedText.length > 14) {
                        pastedText = pastedText.slice(0, 14);
                    }
                    this.value = pastedText;
                    validatePhoneNumber(this);
                });
            }
        });
    });
    /* ===== End Region: Event listener untuk input nomor handphone ===== */

    /* ===== Region: Validasi Nomor Handphone dan Email santri ===== 
     * Fungsi ini memvalidasi nomor handphone dan email santri dengan ketentuan:
     * 1. Memastikan input nomor handphone tidak boleh diubah jika checkbox tidak dicentang
     * 2. Menghapus nilai input nomor handphone jika checkbox dicentang
     * 3. Menghapus pesan error jika input nomor handphone valid
     */
    document.getElementById('NoHpSantriBelumPunya').addEventListener('change', function() {
        const noHpInput = document.getElementById('NoHpSantri');
        noHpInput.readOnly = this.checked;
        if (this.checked) {
            noHpInput.value = '';
            noHpInput.classList.remove('is-invalid');
            document.getElementById('NoHpSantriError').style.display = 'none';
        }
    });

    document.getElementById('EmailSantriBelumPunya').addEventListener('change', function() {
        const emailInput = document.getElementById('EmailSantri');
        emailInput.readOnly = this.checked;
        if (this.checked) {
            emailInput.value = '';
            emailInput.classList.remove('is-invalid');
            document.getElementById('EmailSantriError').style.display = 'none';
        }
    });
    /* ===== End Region: Validasi Nomor Handphone dan Email santri ===== */

    /* ===== Region: Validasi Nama Kepala Keluarga =====
     * Fungsi ini memvalidasi input nama kepala keluarga
     * Memastikan:
     */
    document.addEventListener('DOMContentLoaded', function() {
        const namaKepalaKeluargaInput = document.getElementById('NamaKepalaKeluarga');
        const checkbox = document.getElementById('NamaKepalaKeluargaSamaDenganAyah');
        const namaAyahInput = document.getElementById('NamaAyah');
        const errorText = document.getElementById('NamaKepalaKeluargaError');

        // Tambahkan event listener untuk input nama kepala keluarga
        namaKepalaKeluargaInput.addEventListener('input', function() {
            // Jika input tidak kosong, tampilkan checkbox
            if (this.value.trim()) {
                checkbox.style.display = 'block';
                checkbox.parentElement.style.display = 'block';
            } else {
                checkbox.style.display = 'none';
                checkbox.parentElement.style.display = 'none';
                checkbox.checked = false;
                namaAyahInput.readOnly = false;
                namaAyahInput.value = '';
                namaAyahInput.classList.remove('is-valid');
            }
        });

        // Event listener untuk checkbox tetap sama seperti sebelumnya
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                if (!namaKepalaKeluargaInput.value.trim()) {
                    alert('Silakan isi Nama Kepala Keluarga terlebih dahulu');
                    this.checked = false;
                    return;
                }

                namaAyahInput.value = namaKepalaKeluargaInput.value;
                namaAyahInput.classList.remove('is-invalid');
                namaAyahInput.classList.add('is-valid');
                namaAyahInput.readOnly = true;
            } else {
                namaAyahInput.readOnly = false;
                namaAyahInput.value = '';
                namaAyahInput.classList.remove('is-valid');
            }
        });

        // Set tampilan awal checkbox
        if (!namaKepalaKeluargaInput.value.trim()) {
            checkbox.style.display = 'none';
            checkbox.parentElement.style.display = 'none';
        }
    });
    /* ===== End Region: Validasi Nama Kepala Keluarga ===== */

    /* ===== Region: Validasi Input Angka =====
     * Event listener untuk input dengan kelas 'number-only'
     * Validasi input hanya angka
     */
    document.querySelectorAll('.number-only').forEach(function(input) {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        input.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });
    });
    /* ===== End Region: Validasi Input Angka ===== */

    /* ===== Region: Validasi Input Angka =====
     * Fungsi ini memvalidasi input angka dengan ketentuan:
     * 1. Hanya menerima angka dan tanda minus
     * 2. Menghapus karakter non-angka kecuali tanda minus di awal
     * 3. Menghapus angka nol di awal jika bukan angka desimal
     * 4. Diterapkan pada input-input angka lainnya
     */
    function validateNumberInput(input) {
        // Hapus karakter non-angka kecuali tanda minus di awal
        input.value = input.value.replace(/^-?\d*\.?\d*$/, function(match) {
            return match === '' ? '' : Number(match).toString();
        });

        // Hapus angka nol di awal jika bukan angka desimal
        if (input.value.length > 1 && input.value[0] === '0' && input.value[1] !== '.') {
            input.value = input.value.replace(/^0+/, '');
        }
    }

    // Menambahkan event listener untuk semua input dengan type="number"
    document.querySelectorAll('input[type="number"]').forEach(function(input) {
        ['input', 'keydown', 'keyup', 'mousedown', 'mouseup', 'select', 'contextmenu', 'drop'].forEach(function(event) {
            input.addEventListener(event, function() {
                validateNumberInput(this);
            });
        });

        // Mencegah input karakter yang tidak diizinkan
        input.addEventListener('keypress', function(e) {
            var allowedChars = /[0-9\.-]/;
            if (!allowedChars.test(e.key)) {
                e.preventDefault();
            }
        });

        // Mencegah paste konten non-angka
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            var pastedText = (e.clipboardData || window.clipboardData).getData('text');
            this.value = pastedText.replace(/[^\d.-]/g, '');
            validateNumberInput(this);
        });

        // Mencegah wheel event untuk mengubah nilai
        input.addEventListener('wheel', function(e) {
            e.preventDefault();
        });
    });

    /* ===== Region: Validasi Input Nama dan Tempat =====
     * Fungsi untuk memvalidasi input nama dan tempat lahir
     * Hanya menerima huruf, spasi, tanda petik, titik dan tanda hubung
     * @param {HTMLElement} input - Elemen input yang akan dicek validasinya
     */
    function validateNameInput(input) {
        const regex = /^[A-Za-z\s'.-]+$/;
        const errorElement = document.getElementById(input.id + 'Error');

        if (!regex.test(input.value)) {
            input.value = input.value.replace(/[^A-Za-z\s'.-]/g, '');
            errorElement.textContent = 'Hanya huruf, spasi, tanda petik, titik, dan tanda hubung diizinkan.';
            errorElement.style.display = 'block';
            input.classList.add('is-invalid');
        } else if (input.value.trim() === '') {
            errorElement.textContent = 'Bidang ini tidak boleh kosong.';
            errorElement.style.display = 'block';
            input.classList.add('is-invalid');
        } else {
            errorElement.style.display = 'none';
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        }
    }

    // Event listener untuk semua input nama dan tempat
    document.querySelectorAll('.name-input').forEach(function(input) {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^A-Za-z\s'.-]/g, '');
            validateNameInput(this);
        });

        input.addEventListener('keypress', function(e) {
            if (!/[A-Za-z\s'.-]/.test(e.key)) {
                e.preventDefault();
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const cleanedText = pastedText.replace(/[^A-Za-z\s'.-]/g, '');
            document.execCommand('insertText', false, cleanedText);
            validateNameInput(this);
        });

        input.addEventListener('blur', function() {
            validateNameInput(this);
        });
    });


    /* ===== Region: Validasi Input File =====
     * Memastikan setiap input file memiliki elemen error message
     * 
     * Fungsi ini memastikan bahwa setiap input file memiliki elemen error message yang sesuai
     * untuk menampilkan pesan validasi. Jika elemen error belum ada, maka akan dibuat secara otomatis.
     * 
     * Fitur yang disediakan:
     * - Pengecekan elemen error message
     * - Pembuatan elemen error otomatis jika belum ada
     * - Penambahan event listener untuk validasi file
     * - Inisialisasi preview file
     */
    // Pastikan setiap input file memiliki elemen error message
    document.addEventListener('DOMContentLoaded', function() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            const inputId = input.id;
            let errorElement = document.getElementById(inputId + 'Error');

            // Buat elemen error jika belum ada
            if (!errorElement) {
                errorElement = document.createElement('small');
                errorElement.id = inputId + 'Error';
                errorElement.className = 'text-danger'; // Hapus d-none dari class awal
                input.parentNode.parentNode.appendChild(errorElement);
            }

            // Tambahkan event listener
            input.addEventListener('change', function() {
                previewFile(this.id);
            });

            // Buat elemen preview
            createPreviewElements(input.id);
        });
    });
    /* ===== End Region: Validasi Input File ===== */

    /* ===== Region: Menampilkan Preview File Img atau Pdf =====
     * Membuat elemen-elemen preview untuk file yang diupload
     *
     * Fungsi ini membuat dan menginisialisasi elemen-elemen yang diperlukan untuk menampilkan preview file,
     * baik untuk gambar maupun PDF. Elemen yang dibuat meliputi:
     * - Div container untuk preview
     * - Tombol close untuk menghapus preview
     * - Elemen img untuk preview gambar
     * - Elemen embed untuk preview PDF
     *
     * Fitur yang disediakan:
     * - Preview gambar dengan kemampuan zoom (popup) saat diklik
     * - Preview PDF dengan kemampuan zoom (popup) saat diklik
     * - Tombol close untuk membatalkan upload dan menghapus preview
     * - Responsive layout dengan max-height dan width yang sesuai
     *
     * @param {string} inputId - ID dari elemen input file yang akan dibuat previewnya
     * @returns {HTMLElement} Elemen div yang berisi semua elemen preview
     */
    function createPreviewElements(inputId) {
        const baseId = inputId.replace('File', '');
        let previewDiv = document.getElementById('preview' + baseId);

        // Jika div preview belum ada, buat baru
        if (!previewDiv) {
            previewDiv = document.createElement('div');
            previewDiv.id = 'preview' + baseId;
            previewDiv.className = 'mt-2 position-relative';
            previewDiv.style.display = 'none';

            // Buat tombol close
            const closeButton = document.createElement('button');
            closeButton.type = 'button';
            closeButton.className = 'close position-absolute';
            closeButton.style.cssText = 'right: 5px; top: 5px; background: rgba(255,0,0,0.7); border-radius: 80%; width: 25px; height: 25px; padding: 0; border: none; color: #fff;';
            closeButton.innerHTML = '&times;';
            closeButton.onclick = function() {
                cancelFileUpload(inputId);
            };

            // Buat elemen preview untuk gambar
            const previewImage = document.createElement('img');
            previewImage.id = 'previewImage' + baseId;
            previewImage.className = 'img-thumbnail';
            previewImage.alt = 'Preview ' + baseId;
            previewImage.style.cssText = 'width:100%; max-height:200px; overflow-y:auto; object-fit:contain; display:none; cursor:zoom-in;';
            previewImage.onclick = function() {
                // Buat elemen overlay untuk popup
                const overlay = document.createElement('div');
                overlay.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; display:flex; justify-content:center; align-items:center;';

                // Buat elemen gambar untuk popup
                const popupImage = document.createElement('img');
                popupImage.src = this.src;
                popupImage.style.cssText = 'max-width:90%; max-height:90%; object-fit:contain;';

                // Tambahkan gambar ke overlay
                overlay.appendChild(popupImage);

                // Tutup popup saat overlay diklik
                overlay.onclick = function() {
                    document.body.removeChild(overlay);
                };

                // Tambahkan overlay ke body
                document.body.appendChild(overlay);
            };

            // Buat elemen preview untuk PDF
            const previewPdf = document.createElement('embed');
            previewPdf.id = 'previewPdf' + baseId;
            previewPdf.type = 'application/pdf';
            previewPdf.width = '100%';
            previewPdf.height = '200px';
            previewPdf.style.display = 'none';
            previewPdf.style.cursor = 'zoom-in';
            previewPdf.onclick = function() {
                // Buat elemen overlay untuk popup
                const overlay = document.createElement('div');
                overlay.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; display:flex; justify-content:center; align-items:center;';

                // Buat elemen PDF untuk popup
                const popupPdf = document.createElement('embed');
                popupPdf.src = this.src;
                popupPdf.type = 'application/pdf';
                popupPdf.style.cssText = 'width:90%; height:90%;';

                // Tambahkan PDF ke overlay
                overlay.appendChild(popupPdf);

                // Tutup popup saat overlay diklik
                overlay.onclick = function() {
                    document.body.removeChild(overlay);
                };

                // Tambahkan overlay ke body
                document.body.appendChild(overlay);
            };

            // Tambahkan elemen ke div preview
            previewDiv.appendChild(closeButton);
            previewDiv.appendChild(previewImage);
            previewDiv.appendChild(previewPdf);

            // Tambahkan div preview setelah input group
            const inputGroup = document.getElementById(inputId).closest('.input-group');
            inputGroup.parentNode.insertBefore(previewDiv, inputGroup.nextSibling);
        }

        return previewDiv;
    }

    /**
     * Membatalkan upload file
     * @param {string} inputId - ID dari elemen input file
     */
    function cancelFileUpload(inputId) {
        const fileInput = document.getElementById(inputId);
        const previewDiv = document.getElementById('preview' + inputId.replace('File', ''));
        const fileLabel = fileInput.closest('.custom-file').querySelector('.custom-file-label');

        // Reset input file
        fileInput.value = '';
        if (fileLabel) {
            fileLabel.textContent = 'Pilih file';
        }

        // Sembunyikan preview
        if (previewDiv) {
            previewDiv.style.display = 'none';
        }

        // Reset error message jika ada
        const errorElement = document.getElementById(inputId + 'Error');
        if (errorElement) {
            errorElement.classList.add('d-none');
        }
    }

    /**
     * Menampilkan preview file img atau pdf
     * @param {string} inputId - ID dari elemen input file
     */
    function previewFile(inputId) {
        // Validasi file terlebih dahulu
        if (!validateFile(inputId)) {
            return;
        }

        const fileInput = document.getElementById(inputId);
        const previewDiv = createPreviewElements(inputId);
        const previewImage = document.getElementById('previewImage' + inputId.replace('File', ''));
        const previewPdf = document.getElementById('previewPdf' + inputId.replace('File', ''));

        // Reset tampilan preview
        previewDiv.style.display = 'none';
        previewImage.style.display = 'none';
        previewPdf.style.display = 'none';

        if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            const fileType = file.type;

            // Tampilkan preview berdasarkan tipe file
            if (fileType.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    previewDiv.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else if (fileType === 'application/pdf') {
                const url = URL.createObjectURL(file);
                previewPdf.src = url;
                previewPdf.style.display = 'block';
                previewDiv.style.display = 'block';
            }
        }
    }

    // fungsi untuk validasi file
    /**
     * Memvalidasi file yang diupload
     * @param {string} inputId - ID dari elemen input file
     * @returns {boolean} - true jika file valid, false jika tidak valid
     *
     * Validasi yang dilakukan:
     * 1. Ukuran file maksimal 2MB
     * 2. Format file harus JPG, PNG, atau PDF
     * 3. Menampilkan pesan error jika validasi gagal
     * 4. Mengupdate label file jika validasi berhasil
     */
    function validateFile(inputId) {
        const fileInput = document.getElementById(inputId);
        const file = fileInput.files[0];
        const errorElement = document.getElementById(inputId + 'Error');
        const fileLabel = fileInput.closest('.custom-file').querySelector('.custom-file-label');


        if (file) {
            const fileSize = file.size;
            const fileType = file.type;
            const maxSize = 2 * 1024 * 1024; // 2MB
            const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];

            // Validasi ukuran dan tipe file
            if (fileSize > maxSize || !allowedTypes.includes(fileType)) {
                fileInput.value = ''; // Clear input
                if (fileLabel) {
                    fileLabel.textContent = 'Pilih file';
                }

                if (errorElement) {
                    cancelFileUpload(inputId);
                    let errorMsg = [];
                    if (!allowedTypes.includes(fileType)) {
                        errorMsg.push(`Format file yang dipilih "${file.name}" (${fileType}) tidak valid. Format harus JPG, PNG, atau PDF`);
                    }
                    if (fileSize > maxSize) {
                        errorMsg.push(`Ukuran file "${file.name}" (${(fileSize/1024/1024).toFixed(2)}MB) melebihi batas maximal 2MB`);
                    }
                    errorElement.textContent = errorMsg.join(' dan ');
                    errorElement.classList.remove('d-none');
                    errorElement.style.display = 'block';
                }
                return false;
            }

            // File valid
            if (fileLabel) {
                fileLabel.textContent = file.name;
            }
            if (errorElement) {
                errorElement.classList.add('d-none');
            }
            return true;
        }
        return true;
    }


    /* ===== Region: Validasi NIK ===== */
    /**
     * Region Validasi NIK berisi fungsi-fungsi untuk:
     *
     * 1. createNIKValidator(inputId)
     * - Membuat validator untuk input NIK
     * - Parameter: ID elemen input NIK
     * - Menangani validasi format dan keunikan NIK
     *
     * 2. validasiNIK(input)
     * - Memvalidasi format dan nilai NIK
     * - Mengecek:
     * - NIK tidak boleh kosong
     * - NIK tidak boleh 16 angka 0
     * - NIK harus 16 digit dan tidak diawali 0
     * - Untuk NIK Santri: cek duplikasi via AJAX
     *
     * 3. tampilkanError() & sembunyikanError()
     * - Menampilkan/menyembunyikan pesan error
     * - Mengatur styling elemen error
     *
     * Event Listeners:
     * - Input: Validasi realtime saat input
     * - Blur: Validasi saat input kehilangan fokus
     */
    // Fungsi validasi NIK yang dapat digunakan ulang
    function validasiNomorKkNik(inputId, type) {
        const input = document.getElementById(inputId);
        if (!input) return;

        const errorElement = document.getElementById(inputId + 'Error');
        if (!errorElement) {
            console.error(`Error element for ${inputId} not found`);
            return;
        }

        // Fungsi untuk validasi nomor (NIK/KK)
        function validasiNomor(input) {
            const nilai = input.value.replace(/\D/g, ''); // Hapus karakter non-digit
            const pola = /^[1-9]\d{15}$/;
            const docType = type.toUpperCase(); // NIK atau KK

            // Validasi dasar
            if (nilai === '') {
                tampilkanError(`${docType} harus diisi.`);
                return false;
            } else if (nilai === '0000000000000000') {
                tampilkanError(`${docType} tidak boleh terdiri dari 16 angka 0.`);
                return false;
            } else if (!pola.test(nilai)) {
                tampilkanError(`${docType} harus terdiri dari 16 digit angka dan tidak boleh diawali dengan angka 0.`);
                return false;
            }

            // Cek duplikasi hanya untuk NIK Santri
            if (inputId === 'NikSantri' && nilai.length === 16 && pola.test(nilai)) {
                fetch('/backend/santri/getNikSantri/' + nilai, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            tampilkanError(`NIK ${nilai} sudah terdaftar atas nama santri: ${data.data.NamaSantri}. Mohon periksa kembali NIK yang dimasukkan.`);
                            return false;
                        }
                        sembunyikanError();
                        return true;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        tampilkanError('Terjadi kesalahan saat memeriksa NIK');
                        return false;
                    });
            } else {
                // Untuk KK dan NIK lainnya, cukup validasi format
                sembunyikanError();
                return true;
            }
        }

        function tampilkanError(pesan) {
            errorElement.textContent = pesan;
            errorElement.style.display = 'block';
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            input.style.borderColor = '#dc3545';
        }

        function sembunyikanError() {
            errorElement.style.display = 'none';
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            input.style.borderColor = '#28a745';
        }

        // Event listeners
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').slice(0, 16);
            validasiNomor(this);
        });

        input.addEventListener('keypress', function(e) {
            const karakter = String.fromCharCode(e.which);
            if (!/[0-9]/.test(karakter) || (this.value.length === 0 && karakter === '0')) {
                e.preventDefault();
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const teksTempel = (e.clipboardData || window.clipboardData).getData('text');
            const teksBersih = teksTempel.replace(/\D/g, '').slice(0, 16);
            if (teksBersih.length > 0 && teksBersih[0] !== '0') {
                this.value = teksBersih;
                validasiNomor(this);
            }
        });

        input.addEventListener('blur', function() {
            validasiNomor(this);
        });

        // Validasi awal jika ada nilai default
        if (input.value) {
            validasiNomor(this);
        }
    }

    // Inisialisasi validator untuk NIK dan KK
    document.addEventListener('DOMContentLoaded', function() {
        // Validasi untuk NIK
        validasiNomorKkNik('NikSantri', 'NIK');
        validasiNomorKkNik('NikAyah', 'NIK');
        validasiNomorKkNik('NikIbu', 'NIK');

        // Validasi untuk KK
        validasiNomorKkNik('IdKartuKeluarga', 'KK');
    });

    /* ===== End Region: Validasi NIK ===== */

    /* ===== Region: Validasi KK =====
     * 1. updateKKAyahState()
     * - Mengatur status checkbox KK Ayah berdasarkan:
     * a. Keberadaan file KK Santri
     * b. Status hidup Ayah
     * - Menampilkan pesan informasi sesuai kondisi
     *
     * 2. updateKKIbuState()
     * - Mengatur status checkbox KK Ibu berdasarkan:
     * a. Keberadaan file KK Santri/Ayah
     * b. Status hidup Ibu
     * - Menampilkan pesan informasi sesuai kondisi
     *
     * 3. Event Listeners:
     * - DOMContentLoaded: Inisialisasi validasi
     * - change: Memantau perubahan file dan status
     * - click: Menangani interaksi checkbox
     *
     * 4. Fitur Utama:
     * - Validasi upload file KK
     * - Pengecekan status hidup orangtua
     * - Opsi penggunaan KK yang sama
     * - Pesan informasi dinamis
     */
    document.addEventListener('DOMContentLoaded', function() {
        // Logika untuk KK Santri dan KK Ayah
        const FileKkSantri = document.getElementById('FileKkSantri');
        const KkAyahSamaDenganSantri = document.getElementById('KkAyahSamaDenganSantri');
        const fileKKAyahDiv = document.getElementById('FileKKAyahDiv');
        const fileKkAyah = document.getElementById('FileKkAyah');
        const KkIbuSamaDenganAyahAtauSantri = document.getElementById('KkIbuSamaDenganAyahAtauSantri');
        const fileKKIbuDiv = document.getElementById('FileKKIbuDiv');
        const statusAyah = document.getElementById('StatusAyah');
        const statusIbu = document.getElementById('StatusIbu');

        // Nonaktifkan checkbox saat awal
        KkAyahSamaDenganSantri.disabled = true;
        KkIbuSamaDenganAyahAtauSantri.disabled = true;

        // Fungsi untuk mengupdate status checkbox KK Ayah
        function updateKKAyahState() {
            const santriHasKK = FileKkSantri.files.length > 0;
            const ayahHidup = statusAyah.value === 'Masih Hidup';

            // Aktifkan checkbox hanya jika semua kondisi terpenuhi
            KkAyahSamaDenganSantri.disabled = !(santriHasKK && ayahHidup);

            // Reset checkbox dan tampilkan form upload jika kondisi tidak terpenuhi
            if (!santriHasKK || !ayahHidup) {
                KkAyahSamaDenganSantri.checked = false;
                if (fileKKAyahDiv) {
                    fileKKAyahDiv.style.display = 'block';
                }
            }

            // Tambahkan pesan informasi
            const infoElement = document.getElementById('kkAyahInfo');
            if (!infoElement) {
                const info = document.createElement('small');
                info.id = 'kkAyahInfo';
                info.className = 'form-text mt-1';
                KkAyahSamaDenganSantri.parentElement.appendChild(info);
            }

            const info = document.getElementById('kkAyahInfo');
            if (!ayahHidup) {
                info.className = 'form-text text-danger mt-1';
                info.innerHTML = '<i class="fas fa-exclamation-circle"></i> Ayah harus masih hidup untuk menggunakan opsi ini';
            } else if (!santriHasKK) {
                info.className = 'form-text text-warning mt-1';
                info.innerHTML = '<i class="fas fa-info-circle"></i> KK Santri harus diupload terlebih dahulu';
            } else {
                info.className = 'form-text text-primary mt-1';
                info.innerHTML = '<i class="fas fa-check-circle"></i> Anda dapat mencentang ini jika Ayah satu KK dengan Santri';
            }

            // Trigger updateKKIbuState karena status KK Ayah mempengaruhi KK Ibu
            updateKKIbuState();
        }

        // Fungsi untuk mengupdate status checkbox KK Ibu
        function updateKKIbuState() {
            const ibuHidup = statusIbu.value === 'Masih Hidup';
            const santriHasKK = FileKkSantri.files.length > 0;
            const ayahHasKK = fileKkAyah.files.length > 0 || KkAyahSamaDenganSantri.checked;

            // Ubah logika - Ibu bisa menggunakan KK Santri terlepas dari status Ayah
            const canUseSantriKK = santriHasKK && ibuHidup;
            // Untuk KK Ayah, tetap perlu cek status Ayah
            const canUseAyahKK = ayahHasKK && statusAyah.value === 'Masih Hidup' && ibuHidup;

            KkIbuSamaDenganAyahAtauSantri.disabled = !(canUseSantriKK || canUseAyahKK);

            // Reset checkbox dan tampilkan form upload jika kondisi tidak terpenuhi
            if (!canUseSantriKK && !canUseAyahKK) {
                KkIbuSamaDenganAyahAtauSantri.checked = false;
                if (fileKKIbuDiv) {
                    fileKKIbuDiv.style.display = 'block';
                }
            }

            // Update pesan informasi
            const info = document.getElementById('kkIbuInfo') || createKKIbuInfo();

            if (!ibuHidup) {
                info.className = 'form-text text-danger mt-1';
                info.innerHTML = '<i class="fas fa-exclamation-circle"></i> Ibu harus masih hidup untuk menggunakan opsi ini';
            } else if (!santriHasKK && !ayahHasKK) {
                info.className = 'form-text text-warning mt-1';
                info.innerHTML = '<i class="fas fa-info-circle"></i> Anda perlu mengupload KK Santri atau KK Ayah terlebih dahulu';
            } else {
                info.className = 'form-text text-primary mt-1';
                let message = '<i class="fas fa-check-circle"></i> Anda dapat mencentang ini jika Ibu ';

                if (canUseSantriKK && canUseAyahKK) {
                    message += 'satu KK dengan Santri atau Ayah';
                } else if (canUseSantriKK) {
                    message += 'satu KK dengan Santri';
                } else if (canUseAyahKK) {
                    message += 'satu KK dengan Ayah';
                }

                info.innerHTML = message;
            }
        }

        // Helper function untuk membuat elemen info jika belum ada
        function createKKIbuInfo() {
            const info = document.createElement('small');
            info.id = 'kkIbuInfo';
            info.className = 'form-text mt-1';
            KkIbuSamaDenganAyahAtauSantri.parentElement.appendChild(info);
            return info;
        }

        // Event listeners untuk KK Santri dan Ayah
        FileKkSantri.addEventListener('change', updateKKAyahState);
        statusAyah.addEventListener('change', function() {
            updateKKAyahState();
            updateKKIbuState();
        });

        // Event listeners untuk KK Ibu
        fileKkAyah.addEventListener('change', updateKKIbuState);
        statusIbu.addEventListener('change', updateKKIbuState);
        KkAyahSamaDenganSantri.addEventListener('change', updateKKIbuState);

        // Event listener untuk checkbox KK Ayah
        KkAyahSamaDenganSantri.addEventListener('change', function() {
            if (fileKKAyahDiv) {
                fileKKAyahDiv.style.display = this.checked ? 'none' : 'block';
            }
        });
        // Event listener untuk checkbox KK Ibu
        KkIbuSamaDenganAyahAtauSantri.addEventListener('change', function() {
            if (fileKKIbuDiv) {
                fileKKIbuDiv.style.display = this.checked ? 'none' : 'block';
            }
        });

        // Inisialisasi status awal
        updateKKAyahState();
        updateKKIbuState();
    });

    // Perbarui label checkbox untuk mencerminkan opsi baru
    document.addEventListener('DOMContentLoaded', function() {
        const KkIbuSamaDenganAyahAtauSantriLabel = KkIbuSamaDenganAyahAtauSantri.nextElementSibling;
        if (KkIbuSamaDenganAyahAtauSantriLabel) {
            KkIbuSamaDenganAyahAtauSantriLabel.textContent = 'Satu KK dengan Ayah atau Santri';
        }
    });
    /* ===== End Region: Validasi KK ===== */

    /* ===== Region: Validasi Tanggal Lahir Santri ===== */
    // Deklarasikan fungsi validateTanggalLahir di scope global
    window.validateTanggalLahir = function() {
        const kelasSelect = document.getElementById('IdKelas');
        const tanggalLahirInput = document.getElementById('TanggalLahirSantri');

        // Abaikan validasi jika input kosong
        if (!tanggalLahirInput.value || !kelasSelect.value) {
            // Hapus semua class validasi dan pesan error
            tanggalLahirInput.classList.remove('is-invalid', 'is-valid');
            tanggalLahirInput.style.borderColor = ''; // Reset border color
            const existingError = document.getElementById('TanggalLahirSantriError');
            // if (existingError) {
            // existingError.remove();
            // }
            return true;
        }

        const selectedKelasId = parseInt(kelasSelect.value);
        const tanggalLahir = new Date(tanggalLahirInput.value);
        const today = new Date();
        const umur = today.getFullYear() - tanggalLahir.getFullYear();

        // Hapus pesan error yang ada
        const existingError = document.getElementById('TanggalLahirSantriError');
        if (existingError) {
            existingError.remove();
        }

        // Validasi berdasarkan kelas
        let isValid = true;
        let errorMessage = '';

        // Fungsi helper untuk format pesan error
        const formatErrorMessage = (kelasName, minAge, maxAge) => {
            return `Untuk kelas ${kelasName} yang dipilih, usia kisaran antara ${minAge}-${maxAge} tahun (lahir tahun ${today.getFullYear()-maxAge} sampai ${today.getFullYear()-minAge})`;
        };

        // Validasi berdasarkan ID kelas
        switch (selectedKelasId) {
            case 1: // TK
            case 2: // TKA
            case 3: // TKB
                if (umur < 4 || umur > 7) {
                    isValid = false;
                    errorMessage = formatErrorMessage('TK/TKA/TKB', 4, 7);
                }
                break;

            case 4: // TPQ1/SD1
                if (umur < 6 || umur > 7) {
                    isValid = false;
                    errorMessage = formatErrorMessage('TPQ1/SD1', 6, 7);
                }
                break;

            case 5: // TPQ2/SD2
                if (umur < 7 || umur > 8) {
                    isValid = false;
                    errorMessage = formatErrorMessage('TPQ2/SD2', 7, 8);
                }
                break;

            case 6: // TPQ3/SD3
                if (umur < 8 || umur > 9) {
                    isValid = false;
                    errorMessage = formatErrorMessage('TPQ3/SD3', 8, 9);
                }
                break;

            case 7: // TPQ4/SD4
                if (umur < 9 || umur > 10) {
                    isValid = false;
                    errorMessage = formatErrorMessage('TPQ4/SD4', 9, 10);
                }
                break;

            case 8: // TPQ5/SD5
                if (umur < 10 || umur > 11) {
                    isValid = false;
                    errorMessage = formatErrorMessage('TPQ5/SD5', 10, 11);
                }
                break;

            case 9: // TPQ6/SD6
                if (umur < 11 || umur > 12) {
                    isValid = false;
                    errorMessage = formatErrorMessage('TPQ6/SD6', 11, 12);
                }
                break;

            default:
                isValid = false;
                errorMessage = 'Silakan pilih kelas terlebih dahulu';
        }

        if (!isValid) {
            // Tambahkan class is-invalid dan atur border merah
            tanggalLahirInput.classList.add('is-invalid');
            tanggalLahirInput.classList.remove('is-valid');
            tanggalLahirInput.style.borderColor = '#dc3545';

            // Tampilkan pesan error
            const errorDiv = document.createElement('div');
            errorDiv.id = 'TanggalLahirSantriError';
            errorDiv.className = 'alert alert-danger mt-2 small';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${errorMessage}`;
            tanggalLahirInput.parentElement.appendChild(errorDiv);
        } else {
            // Jika valid, tambahkan class is-valid dan atur border hijau
            tanggalLahirInput.classList.remove('is-invalid');
            tanggalLahirInput.classList.add('is-valid');
            tanggalLahirInput.style.borderColor = '#28a745';

            // Hapus pesan error jika ada
            const errorDiv = document.getElementById('TanggalLahirSantriError');
            if (errorDiv) {
                errorDiv.remove();
            }
        }

        return isValid;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const kelasSelect = document.getElementById('IdKelas');
        const tanggalLahirInput = document.getElementById('TanggalLahirSantri');

        // Tambahkan event listener untuk perubahan tanggal lahir
        tanggalLahirInput.addEventListener('change', validateTanggalLahir);
        // Tambahkan event listener untuk perubahan kelas
        kelasSelect.addEventListener('change', validateTanggalLahir);

        // Jalankan validasi awal jika kedua field sudah memiliki nilai
        if (tanggalLahirInput.value && kelasSelect.value) {
            validateTanggalLahir();
        }
    });
    /* ===== End Region: Validasi Tanggal Lahir Santri ===== */

    // Fungsi reusable untuk menangani semua input "Lainnya"
    function toggleLainnyaInput(selectId, inputId, errorId, labelText) {
        const selectElement = document.getElementById(selectId);
        const inputElement = document.getElementById(inputId);
        const inputParent = inputElement.parentElement;
        const errorSpan = document.getElementById(errorId) || createErrorSpan(errorId);
        const isLainnya = selectElement.value === "Lainnya";

        // Toggle display dan required state
        if (isLainnya) {
            inputParent.style.display = "block";
            inputElement.disabled = false;
            inputElement.required = true;
            $(inputParent).show();

            // Validasi jika input kosong
            if (!inputElement.value.trim()) {
                errorSpan.style.display = "block";
                errorSpan.textContent = `${labelText} harus diisi`;
                inputElement.classList.remove('is-valid');
                inputElement.classList.add('is-invalid');
            }
        } else {
            inputParent.style.display = "none";
            inputElement.disabled = true;
            inputElement.required = false;
            inputElement.value = "";
            inputElement.classList.remove('is-valid', 'is-invalid');
            errorSpan.style.display = "none";
            $(inputParent).hide();
        }
    }

    // Fungsi untuk membuat span error
    function createErrorSpan(id) {
        const span = document.createElement("span");
        span.id = id;
        span.className = "text-danger";
        span.style.display = "none";

        const inputId = id.replace('Error', '');
        const parentElement = document.getElementById(inputId).parentElement;
        parentElement.appendChild(span);
        return span;
    }

    // Event listener saat dokumen dimuat
    document.addEventListener('DOMContentLoaded', function() {
        // Konfigurasi untuk semua field yang memiliki opsi "Lainnya"
        const lainnyaFields = [{
                selectId: 'CitaCita',
                inputId: 'CitaCitaLainya',
                errorId: 'CitaCitaLainyaError',
                labelText: 'Cita-cita lainnya'
            },
            {
                selectId: 'Hobi',
                inputId: 'HobiLainya',
                errorId: 'HobiLainyaError',
                labelText: 'Hobi lainnya'
            },
            {
                selectId: 'KebutuhanKhusus',
                inputId: 'KebutuhanKhususLainya',
                errorId: 'KebutuhanKhususLainyaError',
                labelText: 'Kebutuhan khusus lainnya'
            },
            {
                selectId: 'KebutuhanDisabilitas',
                inputId: 'KebutuhanDisabilitasLainya',
                errorId: 'KebutuhanDisabilitasLainyaError',
                labelText: 'Kebutuhan disabilitas lainnya'
            }
        ];

        // Setup event listeners dan inisialisasi untuk semua field
        lainnyaFields.forEach(field => {
            // Setup change event untuk select
            const selectElement = document.getElementById(field.selectId);
            selectElement.addEventListener('change', function() {
                toggleLainnyaInput(field.selectId, field.inputId, field.errorId, field.labelText);
            });

            // Setup input validation
            const inputElement = document.getElementById(field.inputId);
            inputElement.addEventListener('input', function() {
                const errorSpan = document.getElementById(field.errorId);
                if (this.required) {
                    if (!this.value.trim()) {
                        errorSpan.style.display = "block";
                        errorSpan.textContent = `${field.labelText} harus diisi`;
                        this.classList.remove('is-valid');
                        this.classList.add('is-invalid');
                    } else {
                        errorSpan.style.display = "none";
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                }
            });

            // Inisialisasi tampilan awal
            toggleLainnyaInput(field.selectId, field.inputId, field.errorId, field.labelText);
        });

    });


    /* ===== Region: Toggle Address Fields =====
     * Fungsi ini digunakan untuk mengatur visibilitas field alamat berdasarkan status checkbox.
     * Fungsi ini dapat digunakan untuk:
     * 1. Menyembunyikan/menampilkan field alamat luar negeri untuk Ayah dan Ibu ketika checkbox 'Tinggal Di Luar Negeri' dicentang.
     * 2. Menyembunyikan/menampilkan field alamat Ibu ketika checkbox 'Alamat Ibu Sama Dengan Ayah' dicentang.
     * 3. Mengatur visibilitas field alamat berdasarkan status checkbox.
     * 4. Menangani perubahan status ibu dan ayah secara dinamis.
     */
    function toggleDataDanAlamat(elementId, fieldsToToggle, options = {}) {
        const element = document.getElementById(elementId);
        const fields = document.querySelectorAll(fieldsToToggle);

        function toggleFields() {
            let shouldHide = false;

            if (element.type === 'checkbox') {
                // Untuk checkbox, gunakan checked state
                shouldHide = element.checked;
            } else if (element.tagName === 'SELECT') {
                // Untuk select, periksa nilai yang dipilih
                if (options.hideOn) {
                    // Sembunyikan jika nilai cocok dengan hideOn
                    shouldHide = options.hideOn.includes(element.value);
                } else if (options.showOn) {
                    // Sembunyikan jika nilai TIDAK cocok dengan showOn
                    shouldHide = !options.showOn.includes(element.value);
                }
            }

            fields.forEach(field => {
                field.style.display = shouldHide ? 'none' : 'block';
            });
        }

        // Event listener untuk perubahan elemen
        element.addEventListener(element.type === 'checkbox' ? 'change' : 'input', toggleFields);

        // Set status awal saat halaman dimuat
        toggleFields();
    }



    // Inisialisasi semua toggle address fields
    document.addEventListener('DOMContentLoaded', function() {
        // Cek status ayah dan sembunyikan/tampilkan field alamat
        const statusAyah = document.getElementById('StatusAyah');
        const dataAlamatAyahDiv = document.getElementById('DataAlamatAyahDiv');
        const statusIbu = document.getElementById('StatusIbu');
        const dataAlamatIbuDiv = document.getElementById('DataAlamatIbuDiv');
        const alamatIbuSamaDenganAyahDiv = document.getElementById('AlamatIbuSamaDenganAyahDiv');
        const statusTempatTinggalSantri = document.getElementById('StatusTempatTinggalSantri');

        // Event listeners untuk perubahan status
        statusAyah.addEventListener('change', toggleAlamatAyah);
        statusIbu.addEventListener('change', toggleAlamatIbu);
        statusTempatTinggalSantri.addEventListener('change', toggleAlamatSantri);
        // fungsi untuk toggle alamat ayah  
        function toggleAlamatAyah() {
            if (statusAyah.value !== 'Masih Hidup') {
                dataAlamatAyahDiv.style.display = 'none';
                // jika status ibu hidup, maka uncheck alamat ibu sama dengan ayah  
                if (statusIbu.value === 'Masih Hidup') {
                    document.getElementById('AlamatIbuSamaDenganAyah').checked = false;
                    toggleDataDanAlamat('AlamatIbuSamaDenganAyah', '#DataAlamatIbuDetailDiv');
                    alamatIbuSamaDenganAyahDiv.style.display = 'none';
                }
                // sembunyikan data profil ayah
                document.getElementById('DataProfilAyahDetailDiv').style.display = 'none';
            } else {
                dataAlamatAyahDiv.style.display = 'block';
                // jika status ibu bukan hidup, maka check alamat ibu sama dengan ayah  
                if (statusIbu.value === 'Masih Hidup') {
                    alamatIbuSamaDenganAyahDiv.style.display = 'block';
                    document.getElementById('AlamatIbuSamaDenganAyah').checked = true;
                    toggleDataDanAlamat('AlamatIbuSamaDenganAyah', '#DataAlamatIbuDetailDiv');
                }
                // tampilkan data profil ayah
                document.getElementById('DataProfilAyahDetailDiv').style.display = 'block';
            }
        }

        // fungsi untuk toggle alamat ibu
        function toggleAlamatIbu() {
            if (statusIbu.value !== 'Masih Hidup') {
                dataAlamatIbuDiv.style.display = 'none';
                // sembunyikan data profil ibu
                document.getElementById('DataProfilIbuDetailDiv').style.display = 'none';
            } else {
                dataAlamatIbuDiv.style.display = 'block';
                // jika status ayah bukan hidup, maka uncheck alamat ibu sama dengan ayah  
                if (statusAyah.value !== 'Masih Hidup') {
                    document.getElementById('AlamatIbuSamaDenganAyah').checked = false;
                    toggleDataDanAlamat('AlamatIbuSamaDenganAyah', '#DataAlamatIbuDetailDiv');
                    // sembunyikan field alamat ibu
                    alamatIbuSamaDenganAyahDiv.style.display = 'none';
                }
                // tampilkan data profil ibu
                document.getElementById('DataProfilIbuDetailDiv').style.display = 'block';
            }
        }

        // fungsi untuk toggle alamat santri
        function toggleAlamatSantri() {
            if (statusTempatTinggalSantri.value !== 'Lainnya' || statusTempatTinggalSantri.value !== '') {
                // Untuk select dengan hideOn
                toggleDataDanAlamat('StatusTempatTinggalSantri', '#DataAlamatSantriProvinsiDiv', {
                    hideOn: ['Tinggal dengan Ayah Kandung', 'Tinggal dengan Ibu Kandung', '']
                });
            } else {
                // Untuk select dengan showOn
                toggleDataDanAlamat('StatusTempatTinggalSantri', '#DataAlamatSantriProvinsiDiv', {
                    showOn: ['Lainnya']
                });
            }
        }

        // Set status awal
        toggleAlamatAyah();
        toggleAlamatIbu();
        toggleAlamatSantri();

        // Toggle untuk alamat luar negeri
        toggleDataDanAlamat('TinggalDiluarNegeriAyah', '#DataAlamatAyahProvinsiDiv'); // Hanya kolom alamat ayah yang aktif jika luar daerah atau luar negeri
        toggleDataDanAlamat('TinggalDiluarNegeriIbu', '#DataAlamatIbuProvinsiDiv'); // Hanya kolom alamat ibu yang aktif jika luar daerah atau luar negeri

        // Toggle untuk alamat Ibu sama dengan Ayah
        toggleDataDanAlamat('AlamatIbuSamaDenganAyah', '#DataAlamatIbuDetailDiv');

        // toggle untuk status tempat tinggal santri
        toggleDataDanAlamat('StatusTempatTinggalSantri', '#DataAlamatSantriProvinsiDiv');



        // Event listener untuk checkbox AlamatIbuSamaDenganAyah
        document.getElementById('AlamatIbuSamaDenganAyah').addEventListener('change', copyAlamatAyahKeIbu);
    });
    /* ===== End Region: Toggle Address Fields ===== */
    // Fungsi untuk menyalin data alamat ayah ke alamat ibu
    function copyAlamatAyahKeIbu() {
        const alamatIbuSamaDenganAyah = document.getElementById('AlamatIbuSamaDenganAyah');

        if (alamatIbuSamaDenganAyah.checked) {
            // Salin data alamat dari ayah ke ibu
            document.getElementById('StatusKepemilikanRumahIbu').value = document.getElementById('StatusKepemilikanRumahAyah').value;
            document.getElementById('AlamatIbu').value = document.getElementById('AlamatAyah').value;
            document.getElementById('RTIbu').value = document.getElementById('RTAyah').value;
            document.getElementById('RWIbu').value = document.getElementById('RWAyah').value;
            document.getElementById('KelurahanDesaIbu').value = document.getElementById('KelurahanDesaAyah').value;
            document.getElementById('KecamatanIbu').value = document.getElementById('KecamatanAyah').value;
            document.getElementById('KabupatenKotaIbu').value = document.getElementById('KabupatenKotaAyah').value;
            document.getElementById('ProvinsiIbu').value = document.getElementById('ProvinsiAyah').value;
            document.getElementById('KodePosIbu').value = document.getElementById('KodePosAyah').value;
        }
    }

    // Fungsi untuk menyalin alamat berdasarkan status tempat tinggal santri
    function copyAlamatSantri() {
        const StatusTempatTinggalSantri = document.getElementById('StatusTempatTinggalSantri').value;

        // Jika tinggal dengan ayah
        if (StatusTempatTinggalSantri === 'Tinggal dengan Ayah Kandung') {
            document.getElementById('AlamatSantri').value = document.getElementById('AlamatAyah').value;
            document.getElementById('RTSantri').value = document.getElementById('RTAyah').value;
            document.getElementById('RWSantri').value = document.getElementById('RWAyah').value;
            document.getElementById('KelurahanDesaSantri').value = document.getElementById('KelurahanDesaAyah').value;
            document.getElementById('KecamatanSantri').value = document.getElementById('KecamatanAyah').value;
            document.getElementById('KabupatenKotaSantri').value = document.getElementById('KabupatenKotaAyah').value;
            document.getElementById('ProvinsiSantri').value = document.getElementById('ProvinsiAyah').value;
            document.getElementById('KodePosSantri').value = document.getElementById('KodePosAyah').value;
        }
        // Jika tinggal dengan ibu
        else if (StatusTempatTinggalSantri === 'Tinggal dengan Ibu Kandung') {
            document.getElementById('AlamatSantri').value = document.getElementById('AlamatIbu').value;
            document.getElementById('RTSantri').value = document.getElementById('RTIbu').value;
            document.getElementById('RWSantri').value = document.getElementById('RWIbu').value;
            document.getElementById('KelurahanDesaSantri').value = document.getElementById('KelurahanDesaIbu').value;
            document.getElementById('KecamatanSantri').value = document.getElementById('KecamatanIbu').value;
            document.getElementById('KabupatenKotaSantri').value = document.getElementById('KabupatenKotaIbu').value;
            document.getElementById('ProvinsiSantri').value = document.getElementById('ProvinsiIbu').value;
            document.getElementById('KodePosSantri').value = document.getElementById('KodePosIbu').value;
        }
    }
</script>

<?= $this->endSection(); ?>