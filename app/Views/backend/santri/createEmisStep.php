<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <?php echo session()->getFlashdata('pesan');
    $required = 'required'; //required
    ?>
    <div class="card">
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">Formulir Data Santri</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Perhatian..!</strong> Kolom isian dengan tanda <span class="text-danger font-weight-bold">*</span> merah adalah wajib diisi.
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
                                            <div class="form-group">
                                                <label for="IdTpq">Nama TPQ<span class="text-danger font-weight-bold">*</span></label>
                                                <select class="form-control" id="IdTpq" name="IdTpq" <?= $required ?>>
                                                    <option value="">Pilih Nama TPQ sesuai Desa/Kelurahan</option>
                                                    <?php foreach ($dataTpq as $tpq): ?>
                                                        <option value="<?= $tpq['IdTpq'] ?>"><?= $tpq['NamaTpq'] ?> - <?= $tpq['Alamat'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span id="tpqNameError" class="text-danger" style="display:none;">Nama TPQ diperlukan.</span>
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
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="bg-success p-2">
                                                        <h5 class="mb-0 text-white">Data Santri</h5>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label>Photo Profil</label>
                                                        <div class="text-left">
                                                            <input type="file" id="PhotoProfil" name="PhotoProfil" accept=".jpg,.jpeg,.png,.png,image/*;capture=camera" style="display: none;" onchange="previewPhoto(this)">
                                                            <div class="position-relative d-inline-block text-left">
                                                                <img id="previewPhotoProfil" src="/images/no-photo.jpg" alt="Preview Photo"
                                                                    class="img-thumbnail" style="width: 215px; height: 280px; object-fit: cover; cursor: pointer; float: left;"
                                                                    onclick="showPhotoOptions()">
                                                            </div>
                                                            <div class="mt-2">
                                                                <button type="button" class="btn btn-sm btn-primary mr-2" onclick="document.getElementById('PhotoProfil').click()">
                                                                    <i class="fas fa-upload"></i> Upload Foto
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-success" onclick="openCamera()">
                                                                    <i class="fas fa-camera"></i> Ambil Foto
                                                                </button>
                                                                <small class="text-muted d-block mt-2">Format: JPG, JPEG, PNG. Maximal: 2MB</small>
                                                                <div id="PhotoProfilError" class="text-danger mt-1" style="display: none;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                // Fungsi untuk menampilkan preview foto profil
                                                function previewPhoto(input) {
                                                    const preview = document.getElementById('previewPhotoProfil');
                                                    const errorDiv = document.getElementById('PhotoProfilError');

                                                    errorDiv.style.display = 'none';

                                                    if (input.files && input.files[0]) {
                                                        const file = input.files[0];

                                                        // Validasi ukuran (max 2MB)
                                                        if (file.size > 2 * 1024 * 1024) {
                                                            errorDiv.innerHTML = 'Ukuran file terlalu besar (maksimal 2MB)';
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
                                                        } catch (error) {
                                                            console.error('Error saat membaca file:', error);
                                                            errorDiv.innerHTML = 'Terjadi kesalahan saat memproses file';
                                                            errorDiv.style.display = 'block';
                                                            preview.src = '/images/no-photo.jpg';
                                                        }
                                                    }
                                                }

                                                // Fungsi untuk membuka kamera
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
                                            </script>
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <label for="NikSantri">NIK Santri<span class="text-danger font-weight-bold">*</span></label>
                                                    <input type="text" class="form-control" id="NikSantri" name="NikSantri"
                                                        placeholder="Masukkan NIK 16 digit" <?= $required ?> pattern="^[1-9]\d{15}$"
                                                        title="NIK harus terdiri dari 16 digit angka dan tidak boleh diawali dengan angka 0">
                                                    <span id="NikSantriError" class="text-danger" style="display:none;">NIK diperlukan dan harus 16 digit.</span>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="NamaSantri">Nama Santri<span class="text-danger font-weight-bold">*</span></label>
                                                    <input type="text" class="form-control name-input" id="NamaSantri" name="NamaSantri" placeholder="Masukkan nama lengkap" <?= $required ?>>
                                                    <span id="NamaSantriError" class="text-danger" style="display:none;">Nama Santri diperlukan.</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <label for="NISN">NISN</label>
                                                    <input type="text" class="form-control" id="NISN" name="NISN" placeholder="Masukkan NISN"
                                                        pattern="[0-9]{10}" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                    <small class="text-muted">NISN harus 10 digit angka</small>
                                                    <span id="NISNError" class="text-danger" style="display:none;">NISN harus 10 digit angka</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="Agama">Agama<span class="text-danger font-weight-bold">*</span></label>
                                                    <input type="text" class="form-control" id="Agama" name="Agama" value="Islam" readonly <?= $required ?>>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="JenisKelamin">Jenis Kelamin<span class="text-danger font-weight-bold">*</span></label>
                                                    <div class="d-flex">
                                                        <div class="d-inline-block custom-control custom-radio mr-3">
                                                            <input class="custom-control-input" type="radio" id="Laki-Laki" name="JenisKelamin" value="Laki-laki" <?= $required ?>>
                                                            <label for="Laki-Laki" class="custom-control-label" style="font-weight: normal;">Laki-Laki</label>
                                                        </div>
                                                        <div class="d-inline-block custom-control custom-radio">
                                                            <input class="custom-control-input" type="radio" id="Perempuan" name="JenisKelamin" value="Perempuan" <?= $required ?>>
                                                            <label for="Perempuan" class="custom-control-label" style="font-weight: normal;">Perempuan</label>
                                                        </div>
                                                    </div>
                                                    <span id="JenisKelaminError" class="text-danger" style="display:none;">Pilih jenis kelamin.</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="TempatLahirSantri">Tempat Lahir<span class="text-danger font-weight-bold">*</span></label>
                                                        <input type="text" class="form-control name-input" id="TempatLahirSantri" name="TempatLahirSantri" placeholder="Ketik Tempat Lahir Santri" <?= $required ?> pattern="[A-Za-z\s'.-]+" title="Hanya huruf, spasi, tanda petik, titik, dan tanda hubung diizinkan">
                                                        <span id="TempatLahirSantriError" class="text-danger" style="display:none;"></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="TanggalLahirSantri">Tanggal Lahir<span class="text-danger font-weight-bold">*</span></label>
                                                        <input type="date" class="form-control" id="TanggalLahirSantri" name="TanggalLahirSantri" <?= $required ?>>
                                                        <span id="TanggalLahirSantriError" class="text-danger" style="display:none;"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="JumlahSaudara">Jumlah Saudara<span class="text-danger font-weight-bold">*</span></label>
                                                        <input type="text" class="form-control" id="JumlahSaudara" name="JumlahSaudara" placeholder="Masukkan angka jumlah saudara" <?= $required ?> oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="AnakKe">Anak Ke<span class="text-danger font-weight-bold">*</span></label>
                                                        <input type="text" class="form-control" id="AnakKe" name="AnakKe" placeholder="Masukkan angka anak ke berapa" <?= $required ?> oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                // Fungsi ini digunakan untuk memvalidasi input jumlah saudara dan anak ke
                                                // Memastikan:
                                                // 1. Input tidak boleh negatif
                                                // 2. Anak ke tidak boleh lebih besar dari jumlah saudara + 1
                                                // 3. Menampilkan pesan error jika validasi gagal
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
                                            </script>
                                            <div class="form-group">
                                                <div class="row">
                                                    <!-- Bagian Cita-Cita -->
                                                    <div class="col-md-6">
                                                        <label for="CitaCita">Cita-Cita<span class="text-danger font-weight-bold">*</span></label>
                                                        <select class="form-control" id="CitaCita" name="CitaCita" onchange="toggleCitaCitaLainya()" <?= $required ?>>
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
                                                    </div>
                                                    <!-- Bagian Hobi -->
                                                    <div class="col-md-6">
                                                        <label for="Hobi">Hobi<span class="text-danger font-weight-bold">*</span></label>
                                                        <select class="form-control" id="Hobi" name="Hobi" onchange="toggleHobiLainya()" <?= $required ?>>
                                                            <option value="">Pilih Hobi</option>
                                                            <option value="Olahraga">Olahraga</option>
                                                            <option value="Kesenian">Kesenian</option>
                                                            <option value="Membaca">Membaca</option>
                                                            <option value="Menulis">Menulis</option>
                                                            <option value="Jalan-jalan">Jalan-jalan</option>
                                                            <option value="Lainnya">Lainnya</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <label for="CitaCitaLainya">Cita-Cita Lainnya</label>
                                                        <input type="text" class="form-control" id="CitaCitaLainya" name="CitaCitaLainya" placeholder="Ketik cita-cita lainnya" disabled>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="HobiLainya">Hobi Lainnya</label>
                                                        <input type="text" class="form-control" id="HobiLainya" placeholder="Ketik hobi lainnya" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="NoHpSantri">No Handphone</label>
                                                        <input type="text" class="form-control" id="NoHpSantri" name="NoHpSantri" placeholder="Masukkan nomor handphone">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="EmailSantri">Alamat Email</label>
                                                        <input type="email" class="form-control" id="EmailSantri" name="EmailSantri" placeholder="Contoh: nama@email.com">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="KebutuhanKhusus">Kebutuhan Khusus</label>
                                                        <select class="form-control" id="KebutuhanKhusus" name="KebutuhanKhusus" onchange="toggleKebutuhanKhususLainya()">
                                                            <option value="Tidak Ada">Tidak Ada</option>
                                                            <option value="Lamban Belajar">Lamban Belajar</option>
                                                            <option value="Kesulitan Belajar Spesific">Kesulitan Belajar Spesific</option>
                                                            <option value="Berbakat/Memiliki Kemampuan dan Kecerdasan Luar Biasa">Berbakat/Memiliki Kemampuan dan Kecerdasan Luar Biasa</option>
                                                            <option value="Lainya">Lainya</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="KebutuhanDisabilitas">Kebutuhan Disabilitas</label>
                                                        <select class="form-control" id="KebutuhanDisabilitas" name="KebutuhanDisabilitas" onchange="toggleKebutuhanDisabilitasLainya()">
                                                            <option value="Tidak Ada">Tidak Ada</option>
                                                            <option value="Tuna Netra">Tuna Netra</option>
                                                            <option value="Tuna Wicara">Tuna Wicara</option>
                                                            <option value="Tuna Rungu">Tuna Rungu</option>
                                                            <option value="Tuna Laras">Tuna Laras</option>
                                                            <option value="Tuna Grahita">Tuna Grahita</option>
                                                            <option value="Tuna Daksa">Tuna Daksa</option>
                                                            <option value="Lainya">Lainya</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="KebutuhanKhususLainya">Kebutuhan Khusus Lainya</label>
                                                        <input type="text" class="form-control" id="KebutuhanKhususLainya" name="KebutuhanKhususLainya" placeholder="Masukkan kebutuhan khusus lainnya" disabled>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="KebutuhanDisabilitasLainya">Kebutuhan Disabilitas Lainya</label>
                                                        <input type="text" class="form-control" id="KebutuhanDisabilitasLainya" placeholder="Masukkan kebutuhan disabilitas lainnya" disabled>
                                                    </div>
                                                </div>
                                                <script>
                                                    function toggleKebutuhanKhususLainya() {
                                                        const kebutuhanKhusus = document.getElementById("KebutuhanKhusus").value;
                                                        const kebutuhanKhususLainya = document.getElementById("KebutuhanKhususLainya");
                                                        kebutuhanKhususLainya.disabled = kebutuhanKhusus !== "Lainya";
                                                    }

                                                    function toggleKebutuhanDisabilitasLainya() {
                                                        const kebutuhanDisabilitas = document.getElementById("KebutuhanDisabilitas").value;
                                                        const kebutuhanDisabilitasLainya = document.getElementById("KebutuhanDisabilitasLainya");
                                                        kebutuhanDisabilitasLainya.disabled = kebutuhanDisabilitas !== "Lainya";
                                                    }
                                                </script>
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
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="NamaKepalaKeluarga">Nama Kepala Keluarga<span class="text-danger font-weight-bold">*</span></label>
                                                        <input type="text" class="form-control name-input" id="NamaKepalaKeluarga" name="NamaKepalaKeluarga" placeholder="Masukkan nama kepala keluarga" <?= $required ?>>
                                                        <span id="NamaKepalaKeluargaError" class="text-danger" style="display:none;">Nama Kepala Keluarga diperlukan.</span>
                                                        <div class="form-check mt-2">
                                                            <input type="checkbox" class="form-check-input" id="NamaKepalaKeluargaSamaDenganAyah" name="NamaKepalaKeluargaSamaDenganAyah">
                                                            <label class="form-check-label small text-success" for="NamaKepalaKeluargaSamaDenganAyah">
                                                                Checklist Jika Nama Kepala Keluarga Sama Dengan Ayah Kandung
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <script>
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
                                                    </script>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="IdKartuKeluarga">No Kartu Keluarga (KK)</label>
                                                        <input type="text" class="form-control" id="IdKartuKeluarga" name="IdKartuKeluarga" placeholder="Masukkan nomor KK"
                                                            pattern="[0-9]{16}" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                        <small class="text-muted">Nomor KK harus 16 digit angka</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="FileKkSantri">Upload KK Santri</label>
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input type="file" class="form-control" id="FileKkSantri" name="FileKkSantri" onchange="validateFile('FileKkSantri')" accept=".pdf,.jpg,.jpeg,.png">
                                                                <label class="custom-file-label" for="FileKkSantri">Unggah KK</label>
                                                            </div>
                                                        </div>
                                                        <small id="FileKKError" class="text-danger d-none"></small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="no_kip">No Kartu Indonesia Pintar (KIP)</label>
                                                        <input type="text" class="form-control" id="no_kip" name="no_kip" placeholder="Masukkan Nomor KIP"
                                                            pattern="[0-9]{16}" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                        <small class="text-muted">Nomor KIP harus 16 digit angka</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="FileKIP">Upload KIP</label>
                                                        <div class="input-group mb-3">
                                                            <div class="custom-file">
                                                                <input type="file" class="form-control" id="FileKIP" name="FileKIP" onchange="previewFile('FileKIP')" accept=".pdf,.jpg,.jpeg,.png">
                                                                <label class="custom-file-label" for="FileKIP">Unggah KIP</label>
                                                            </div>
                                                        </div>
                                                        <small id="FileKIPError" class="text-danger d-none"></small>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary" onclick="stepper.previous()">Sebelumnya</button>
                                            <button type="button" class="btn btn-primary" onclick="validateAndNext('santri-part')">Selanjutnya</button>
                                        </div>
                                        <!-- Bagian Profil Orang Tua atau Wali -->
                                        <div id="ortu-part" class="content" role="tabpanel" aria-labelledby="ortu-part-trigger">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="bg-success p-2">
                                                        <h5 class="mb-0 text-white">Data Ayah Kandung</h5>
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    const statusAyah = document.getElementById('StatusAyah');
                                                    const fields = [
                                                        'NikAyah',
                                                        'KewarganegaraanAyah',
                                                        'TempatLahirAyah',
                                                        'TanggalLahirAyah',
                                                        'PendidikanAyah',
                                                        'PekerjaanUtamaAyah',
                                                        'PenghasilanUtamaAyah',
                                                        'NoHpAyah',
                                                        'FileKKAyahDiv',
                                                        'TinggalDiluarNegeriAyah',
                                                        'StatusKepemilikanRumahAyah',
                                                        'ProvinsiAyah',
                                                        'KabupatenKotaAyah',
                                                        'KecamatanAyah',
                                                        'KelurahanDesaAyah',
                                                        'RWAyah',
                                                        'RTAyah',
                                                        'KodePosAyah',
                                                        'AlamatAyah'
                                                    ].map(id =>
                                                        document.getElementById(id).parentElement
                                                    );

                                                    const headerAlamatAyah = document.getElementById('HeaderDataAlamatAyah');
                                                    const infoAyah = document.createElement('div');
                                                    infoAyah.className = 'alert alert-info mt-2';
                                                    infoAyah.style.display = 'none';
                                                    headerAlamatAyah.parentNode.insertBefore(infoAyah, headerAlamatAyah.nextSibling);

                                                    function toggleFields() {
                                                        const display = statusAyah.value === 'Masih Hidup' ? 'block' : 'none';
                                                        fields.forEach(field => field.style.display = display);

                                                        // Update info message
                                                        if (statusAyah.value === '') {
                                                            infoAyah.className = 'small text-danger';
                                                            infoAyah.innerHTML = '<i class="fas fa-info-circle"></i> Silakan pilih status ayah terlebih dahulu tekan tombol sebelumnya.';
                                                            infoAyah.style.display = 'block';
                                                        } else if (statusAyah.value === 'Sudah Meninggal') {
                                                            infoAyah.className = 'small text-success';
                                                            infoAyah.innerHTML = '<i class="fas fa-exclamation-circle"></i> Ayah telah meninggal dunia, data alamat tidak perlu diisi.';
                                                            infoAyah.style.display = 'block';
                                                        } else if (statusAyah.value === 'Tidak Diketahui') {
                                                            infoAyah.className = 'small text-success';
                                                            infoAyah.innerHTML = '<i class="fas fa-question-circle"></i> Status ayah tidak diketahui, data alamat tidak perlu diisi.';
                                                            infoAyah.style.display = 'block';
                                                        } else {
                                                            infoAyah.style.display = 'none';
                                                        }
                                                    }

                                                    statusAyah.addEventListener('change', toggleFields);
                                                    toggleFields();
                                                });

                                                document.addEventListener('DOMContentLoaded', function() {
                                                    const statusIbu = document.getElementById('StatusIbu');
                                                    const fields = [
                                                        'NikIbu',
                                                        'KewarganegaraanIbu',
                                                        'TempatLahirIbu',
                                                        'TanggalLahirIbu',
                                                        'PendidikanIbu',
                                                        'PekerjaanUtamaIbu',
                                                        'PenghasilanUtamaIbu',
                                                        'NoHpIbu',
                                                        'KkIbuSamaDenganAyahAtauSantri',
                                                        'FileKKIbuDiv',
                                                        'AlamatIbuSamaDenganAyah',
                                                        'TinggalDiluarNegeriIbu',
                                                        'StatusKepemilikanRumahIbu',
                                                        'ProvinsiIbu',
                                                        'KabupatenKotaIbu',
                                                        'KecamatanIbu',
                                                        'KelurahanDesaIbu',
                                                        'RWIbu',
                                                        'RTIbu',
                                                        'KodePosIbu',
                                                        'AlamatIbu'
                                                    ].map(id =>
                                                        document.getElementById(id).parentElement
                                                    );

                                                    const headerAlamatIbu = document.getElementById('HeaderDataAlamatIbu');
                                                    const infoIbu = document.createElement('div');
                                                    infoIbu.className = 'alert alert-info mt-2';
                                                    infoIbu.style.display = 'none';
                                                    headerAlamatIbu.parentNode.insertBefore(infoIbu, headerAlamatIbu.nextSibling);

                                                    function toggleFields() {
                                                        const display = statusIbu.value === 'Masih Hidup' ? 'block' : 'none';
                                                        fields.forEach(field => field.style.display = display);

                                                        // Update info message
                                                        if (statusIbu.value === '') {
                                                            infoIbu.className = 'small text-danger';
                                                            infoIbu.innerHTML = '<i class="fas fa-info-circle"></i> Silakan pilih status ibu terlebih dahulu tekan tombol sebelumnya.';
                                                            infoIbu.style.display = 'block';
                                                        } else if (statusIbu.value === 'Sudah Meninggal') {
                                                            infoIbu.className = 'small text-success';
                                                            infoIbu.innerHTML = '<i class="fas fa-exclamation-circle"></i> Ibu telah meninggal dunia, data alamat tidak perlu diisi.';
                                                            infoIbu.style.display = 'block';
                                                        } else if (statusIbu.value === 'Tidak Diketahui') {
                                                            infoIbu.className = 'small text-success';
                                                            infoIbu.innerHTML = '<i class="fas fa-question-circle"></i> Status ibu tidak diketahui, data alamat tidak perlu diisi.';
                                                            infoIbu.style.display = 'block';
                                                        } else {
                                                            infoIbu.style.display = 'none';
                                                        }
                                                    }

                                                    statusIbu.addEventListener('change', toggleFields);
                                                    toggleFields();
                                                });
                                            </script>
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
                                                    </div>
                                                </div>
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
                                                        </div>
                                                    </div>
                                                </div>
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
                                                    <label for="Wali">Wali</label>
                                                    <select class="form-control" id="Wali" name="StatusWali" <?= $required ?>>
                                                        <option value="">Pilih Wali</option>
                                                    </select>

                                                    <script>
                                                        document.addEventListener('DOMContentLoaded', function() {
                                                            const waliSelect = document.getElementById('Wali');
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
                                                                waliSelect.innerHTML += '<option value="Lainnya">Lainnya</option>';

                                                                // Tambahkan info berdasarkan status yang dipilih
                                                                const infoDiv = document.createElement('small');
                                                                infoDiv.className = 'form-text text-success';

                                                                if (statusAyah.value === 'Masih Hidup' && statusIbu.value === 'Masih Hidup') {
                                                                    infoDiv.innerHTML = '<i class="fas fa-info-circle"></i> Anda dapat memilih ayah atau ibu kandung sebagai wali';
                                                                } else if (statusAyah.value === 'Masih Hidup') {
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
                                                                <label for="NIKWali">NIK Wali<span class="text-danger font-weight-bold">*</span></label>
                                                                <input type="text" class="form-control" id="NIKWali" name="NIKWali" placeholder="Masukkan NIK wali">
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
                                                document.getElementById('Wali').addEventListener('change', function() {
                                                    var dataWali = document.getElementById('dataWali');
                                                    if (this.value === 'Lainnya') {
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
                                                        <label for="fileKKS">Unggah File KKS</label>
                                                        <div class="input-group mb-3">
                                                            <div class="custom-file">
                                                                <input type="file" class="form-control" id="fileKKS" name="fileKKS" onchange="previewFile('fileKKS')" accept=".pdf,.jpg,.jpeg,.png">
                                                                <label class="custom-file-label" for="fileKKS">Pilih file KKS</label>
                                                            </div>
                                                            <!-- div input-group-append dihapus -->
                                                        </div>
                                                        <div class="mt-2" id="previewKKS" style="display:none;">
                                                            <img id="previewImageKKS" src="" alt="Preview KKS" style="max-width:200px; max-height:200px; display:none;" class="img-thumbnail">
                                                            <embed id="previewPdfKKS" src="" type="application/pdf" width="100%" height="200px" style="display:none;">
                                                        </div>
                                                        <small id="FileKKSError" class="text-danger d-none"></small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="filePKH">Unggah File PKH</label>
                                                        <div class="input-group mb-3">
                                                            <div class="custom-file">
                                                                <input type="file" class="form-control" id="filePKH" name="filePKH" onchange="previewFile('filePKH')" accept=".pdf,.jpg,.jpeg,.png">
                                                                <label class="custom-file-label" for="filePKH">Pilih file PKH</label>
                                                            </div>
                                                            <!-- div input-group-append dihapus -->
                                                        </div>
                                                        <small id="FilePKHError" class="text-danger d-none"></small>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary" onclick="stepper.previous()">Sebelumnya</button>
                                            <button type="button" class="btn btn-primary" onclick="validateAndNext('ortu-part')">Selanjutnya</button>
                                        </div>
                                        <!-- Bagian Alamat Orang Tua dan Santri beserta jarak tempat tinggal santri ke lembaga-->
                                        <div id="alamat-part" class="content" role="tabpanel" aria-labelledby="alamat-part-trigger">
                                            <script>
                                                // Fungsi untuk menangani checkbox "Tinggal Diluar Negeri"
                                                function toggleAddressFields(checkboxId, fieldsToToggle) {
                                                    const checkbox = document.getElementById(checkboxId);
                                                    const fields = document.querySelectorAll(fieldsToToggle);

                                                    function toggleFields() {
                                                        fields.forEach(field => {
                                                            if (checkbox.checked) {
                                                                field.style.display = 'none';
                                                            } else {
                                                                field.style.display = 'block';
                                                            }
                                                        });
                                                    }

                                                    checkbox.addEventListener('change', toggleFields);
                                                    // Panggil fungsi saat halaman dimuat untuk mengatur status awal
                                                    toggleFields();
                                                }

                                                // Panggil fungsi untuk setiap bagian (Ayah, Ibu, Santri)
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    toggleAddressFields('TinggalDiluarNegeriAyah', '#StatusKepemilikanRumahAyah, #ProvinsiAyah, #KabupatenKotaAyah, #KecamatanAyah, #KelurahanDesaAyah, #RWAyah, #RTAyah, #KodePosAyah');
                                                    toggleAddressFields('TinggalDiluarNegeriIbu', '#StatusKepemilikanRumahIbu, #ProvinsiIbu, #KabupatenKotaIbu, #KecamatanIbu, #KelurahanDesaIbu, #RWIbu, #RTIbu, #KodePosIbu');
                                                });

                                                // Fungsi untuk menangani checkbox "Alamat Ibu Sama Dengan Ayah"
                                                function toggleIbuAddressFields(checkboxId, fieldsToToggle) {
                                                    const checkbox = document.getElementById(checkboxId);
                                                    const fields = document.querySelectorAll(fieldsToToggle);

                                                    function toggleFields() {
                                                        fields.forEach(field => {
                                                            if (checkbox.checked) {
                                                                field.style.display = 'none';
                                                            } else {
                                                                field.style.display = 'block';
                                                            }
                                                        });
                                                    }

                                                    checkbox.addEventListener('change', toggleFields);
                                                    // Panggil fungsi saat halaman dimuat untuk mengatur status awal
                                                    toggleFields();
                                                }

                                                // Panggil fungsi untuk checkbox AlamatIbuSamaDenganAyah saat halaman dimuat
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    toggleIbuAddressFields('AlamatIbuSamaDenganAyah', '#TinggalDiluarNegeriIbu, #StatusKepemilikanRumahIbu, #ProvinsiIbu, #KabupatenKotaIbu, #KecamatanIbu, #KelurahanDesaIbu, #RWIbu, #RTIbu, #KodePosIbu, #AlamatIbu');
                                                });
                                            </script>
                                            <!-- bagian data alamat ayah -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="bg-success p-2">
                                                        <h5 class="mb-0 text-white" id="HeaderDataAlamatAyah">Data Alamat Ayah Kandung</h5>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="DataAlamatAyahDiv">
                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="TinggalDiluarNegeriAyah" name="TinggalDiluarNegeriAyah">
                                                            <label class="form-check-label" for="TinggalDiluarNegeriAyah">
                                                                Tinggal Di Luar Daerah atau Luar Negeri
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
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
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="ProvinsiAyah">
                                                            <label for="ProvinsiAyah">Provinsi</label>
                                                            <input type="text" class="form-control" id="ProvinsiAyah" name="ProvinsiAyah" value="Kepulauan Riau" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="KabupatenKotaAyah">
                                                            <label for="KabupatenKotaAyah">Kabupaten/Kota</label>
                                                            <input type="text" class="form-control" id="KabupatenKotaAyah" name="KabupatenKotaAyah" value="Bintan" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="KecamatanAyah">
                                                            <label for="KecamatanAyah">Kecamatan</label>
                                                            <input type="text" class="form-control" id="KecamatanAyah" name="KecamatanAyah" value="Seri Kuala Lobam" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="KelurahanDesaAyah">
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
                                                        <div class="form-group" id="RWAyah">
                                                            <label for="RWAyah">RW</label>
                                                            <input type="text" class="form-control" id="RWAyah" name="RWAyah" placeholder="Masukkan RW">
                                                            <span id="RWAyahError" class="text-danger" style="display:none;">RW diperlukan.</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="RTAyah">
                                                            <label for="RTAyah">RT</label>
                                                            <input type="text" class="form-control" id="RTAyah" name="RTAyah" placeholder="Masukkan RT">
                                                            <span id="RTAyahError" class="text-danger" style="display:none;">RT diperlukan.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label for="AlamatAyah">Alamat</label>
                                                            <input type="text" class="form-control" id="AlamatAyah" name="AlamatAyah" placeholder="Masukkan Alamat">
                                                            <span id="AlamatAyahError" class="text-danger" style="display:none;">Alamat diperlukan.</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="KodePosAyah">
                                                            <label for="KodePosAyah">Kode Pos</label>
                                                            <input type="text" class="form-control number-only" id="KodePosAyah" name="KodePosAyah" value="29152" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- bagian data alamat ibu -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="bg-success p-2">
                                                        <h5 id="HeaderDataAlamatIbu" class="mb-0 text-white">Data Alamat Ibu Kandung</h5>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="DataAlamatIbuDiv">
                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="AlamatIbuSamaDenganAyah" name="AlamatIbuSamaDenganAyah">
                                                            <label class="form-check-label" for="AlamatIbuSamaDenganAyah">Sama Dengan Ayah Kandung</label>
                                                            <small class="form-text text-success">
                                                                <i class="fas fa-info-circle"></i> Centang jika ibu dan ayah tinggal dalam satu rumah yang sama
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="TinggalDiluarNegeriIbu" name="TinggalDiluarNegeriIbu">
                                                            <label class="form-check-label" for="TinggalDiluarNegeriIbu">Tinggal Di Luar Daerah atau Luar Negeri</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group" id="StatusKepemilikanRumahIbu">
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
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="ProvinsiIbu">
                                                            <label for="ProvinsiIbu">Provinsi</label>
                                                            <input type="text" class="form-control" id="ProvinsiIbu" name="ProvinsiIbu" value="Kepulauan Riau" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="KabupatenKotaIbu">
                                                            <label for="KabupatenKotaIbu">Kabupaten/Kota</label>
                                                            <input type="text" class="form-control" id="KabupatenKotaIbu" name="KabupatenKotaIbu" value="Bintan" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="KecamatanIbu">
                                                            <label for="KecamatanIbu">Kecamatan</label>
                                                            <input type="text" class="form-control" id="KecamatanIbu" name="KecamatanIbu" value="Seri Kuala Lobam" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="KelurahanDesaIbu">
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
                                                        <div class="form-group" id="RWIbu">
                                                            <label for="RWIbu">RW</label>
                                                            <input type="text" class="form-control" id="RWIbu" name="RWIbu" placeholder="Masukkan RW">
                                                            <span id="RWIbuError" class="text-danger" style="display:none;">RW diperlukan.</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="RTIbu">
                                                            <label for="RTIbu">RT</label>
                                                            <input type="text" class="form-control" id="RTIbu" name="RTIbu" placeholder="Masukkan RT">
                                                            <span id="RTIbuError" class="text-danger" style="display:none;">RT diperlukan.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label for="AlamatIbu">Alamat</label>
                                                            <input type="text" class="form-control" id="AlamatIbu" name="AlamatIbu" placeholder="Masukkan Alamat">
                                                            <span id="AlamatIbuError" class="text-danger" style="display:none;">Alamat diperlukan.</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group" id="KodePosIbu">
                                                            <label for="KodePosIbu">Kode Pos</label>
                                                            <input type="text" class="form-control number-only" id="KodePosIbu" name="KodePosIbu" value="29152" readonly>
                                                        </div>
                                                    </div>
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
                                            <div id="DataAlamatSantriDiv">
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
                                                            <label for="StatusTempatTinggal">Status Tempat Tinggal</label>
                                                            <select class="form-control" id="StatusTempatTinggal" name="StatusTempatTinggal">
                                                                <option value="">Pilih Status Tempat Tinggal</option>
                                                                <script>
                                                                    // Fungsi ini digunakan untuk memperbarui pilihan status tempat tinggal santri
                                                                    // berdasarkan status orang tua (hidup/meninggal) dan lokasi tinggal mereka
                                                                    //
                                                                    // Alur kerja:
                                                                    // 1. Mengambil elemen select status tempat tinggal
                                                                    // 2. Mengambil status ayah dan ibu (hidup/meninggal)
                                                                    // 3. Mengambil status wali yang dipilih
                                                                    // 4. Memperbarui pilihan berdasarkan:
                                                                    //    - Jika ayah masih hidup dan tidak di luar negeri, tambah opsi tinggal dengan ayah
                                                                    //    - Jika ibu masih hidup dan tidak di luar negeri/tidak tinggal dengan ayah, tambah opsi tinggal dengan ibu  
                                                                    //    - Jika wali sudah dipilih, tambah opsi tinggal dengan wali
                                                                    //    - Tambah pilihan statis (asrama pesantren dan lainnya)
                                                                    // 5. Memperbarui pilihan saat ada perubahan status orang tua/wali
                                                                    // 6. Memperbarui pilihan saat ada perubahan lokasi tinggal orang tua
                                                                    document.addEventListener('DOMContentLoaded', function() {
                                                                        const statusTempatTinggal = document.getElementById('StatusTempatTinggal');
                                                                        const statusAyah = document.getElementById('StatusAyah');
                                                                        const statusIbu = document.getElementById('StatusIbu');
                                                                        const wali = document.getElementById('Wali');

                                                                        function updateOptions() {
                                                                            // Reset pilihan
                                                                            statusTempatTinggal.innerHTML = '<option value="">Pilih Status Tempat Tinggal</option>';

                                                                            // Tambahkan pilihan tempat tinggal berdasarkan status orang tua (hidup/meninggal) dan lokasi tinggal
                                                                            // Tambahkan opsi tinggal dengan ayah jika ayah masih hidup dan tidak tinggal di luar negeri
                                                                            if (statusAyah.value === 'Masih Hidup' && !document.getElementById('TinggalDiluarNegeriAyah').checked) {
                                                                                statusTempatTinggal.innerHTML += '<option value="Tinggal dengan Ayah Kandung">Tinggal dengan Ayah Kandung</option>';
                                                                            }
                                                                            // Tambahkan opsi tinggal dengan ibu jika ibu masih hidup dan tidak tinggal di luar negeri
                                                                            // atau jika ibu tidak tinggal bersama ayah
                                                                            if ((statusIbu.value === 'Masih Hidup' && !document.getElementById('TinggalDiluarNegeriIbu').checked) || (statusIbu.value === 'Masih Hidup' && document.getElementById('AlamatIbuSamaDenganAyah').checked)) {
                                                                                statusTempatTinggal.innerHTML += '<option value="Tinggal dengan Ibu Kandung">Tinggal dengan Ibu Kandung</option>';
                                                                            }
                                                                            // jika wali sudah diisi
                                                                            if (wali.value && wali.value !== '') {
                                                                                statusTempatTinggal.innerHTML += '<option value="Tinggal dengan Wali">Tinggal dengan Wali</option>';
                                                                            }

                                                                            // Tambahkan pilihan statis lainnya
                                                                            statusTempatTinggal.innerHTML += `
                                                                            <option value="Tinggal di Asrama Pesantren">Tinggal di Asrama Pesantren</option>
                                                                            <option value="Lainnya">Lainnya</option>
                                                                        `;
                                                                        }

                                                                        // Perbarui pilihan saat status orang tua berubah
                                                                        statusAyah.addEventListener('change', updateOptions);
                                                                        statusIbu.addEventListener('change', updateOptions);
                                                                        wali.addEventListener('change', updateOptions);

                                                                        // Initial update
                                                                        updateOptions();

                                                                        // Update saat checkbox tinggal di luar negeri berubah
                                                                        document.getElementById('TinggalDiluarNegeriAyah').addEventListener('change', updateOptions);
                                                                        document.getElementById('TinggalDiluarNegeriIbu').addEventListener('change', updateOptions);
                                                                        // Update saat checkbox alamat ibu sama dengan ayah berubah
                                                                        document.getElementById('AlamatIbuSamaDenganAyah').addEventListener('change', updateOptions);
                                                                    });

                                                                    // Fungsi untuk menangani perubahan status tempat tinggal
                                                                    document.addEventListener('DOMContentLoaded', function() {
                                                                        const statusTempatTinggal = document.getElementById('StatusTempatTinggal');
                                                                        const alamatSantriFields = [
                                                                            'ProvinsiSantri',
                                                                            'KabupatenKotaSantri',
                                                                            'KecamatanSantri',
                                                                            'KelurahanDesaSantri',
                                                                            'RWSantri',
                                                                            'RTSantri',
                                                                            'AlamatSantri',
                                                                            'KodePosSantri'
                                                                        ].map(id => document.getElementById(id).parentElement);

                                                                        function toggleAlamaSantriFields() {
                                                                            const selectedValue = statusTempatTinggal.value;
                                                                            const display = (selectedValue === 'Tinggal dengan Ayah Kandung' ||
                                                                                selectedValue === 'Tinggal dengan Ibu Kandung') ? 'none' : 'block';

                                                                            alamatSantriFields.forEach(field => {
                                                                                field.style.display = display;
                                                                            });
                                                                        }

                                                                        // Tambahkan event listener untuk perubahan status tempat tinggal
                                                                        statusTempatTinggal.addEventListener('change', toggleAlamaSantriFields);

                                                                        // Panggil fungsi saat halaman dimuat untuk mengatur status awal
                                                                        toggleAlamaSantriFields();
                                                                    });
                                                                </script>
                                                            </select>
                                                            <span id="StatusTempatTinggalError" class="text-danger" style="display:none;">Status tempat tinggal diperlukan.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="ProvinsiSantri">Provinsi</label>
                                                            <input type="text" class="form-control" id="ProvinsiSantri" name="ProvinsiSantri" value="Kepulauan Riau" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="KabupatenKotaSantri">Kabupaten/Kota</label>
                                                            <input type="text" class="form-control" id="KabupatenKotaSantri" name="KabupatenKotaSantri" value="Bintan" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="KecamatanSantri">Kecamatan</label>
                                                            <input type="text" class="form-control" id="KecamatanSantri" name="KecamatanSantri" value="Seri Kuala Lobam" readonly>
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
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="KodePosSantri">Kode Pos</span></label>
                                                            <input type="text" class="form-control number-only" id="KodePosSantri" name="KodePosSantri" value="29152" readonly>
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
                                                        <small class="form-text text-muted">Klik tombol untuk mendapatkan koordinat otomatis</small>

                                                        <script>
                                                            document.getElementById('getLocationBtn').addEventListener('click', function() {
                                                                if (navigator.geolocation) {
                                                                    navigator.geolocation.getCurrentPosition(function(position) {
                                                                        var lat = position.coords.latitude;
                                                                        var lng = position.coords.longitude;
                                                                        document.getElementById('TitikKoordinatSantri').value = lat + ', ' + lng;
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
                                            <button type="button" class="btn btn-primary" onclick="stepper.previous()">Sebelumnya</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            Melihat <a href="#">data yang sudah masuk</a>.
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.row -->
            </div>
        </div>
    </div>

    <script>
        // Event listener untuk DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function() {
            window.stepper = new Stepper(document.querySelector('.bs-stepper'));

            // Tambahkan event listener untuk semua input required
            document.querySelectorAll('.form-control[required]').forEach(function(field) {
                field.addEventListener('input', function() {
                    validateField(this);
                });
            });
        });

        /* ===== Region: Validasi Nomor Handphone =====
        Fungsi ini melakukan validasi nomor handphone dengan ketentuan:
        1. Hanya menerima input angka (0-9)
        2. Panjang nomor antara 10-13 digit
        3. Harus diawali dengan 08 atau 62 (format Indonesia)
        4. Menampilkan pesan error jika tidak sesuai ketentuan
        5. Memformat ulang nomor jika diawali 62 menjadi format 08
        6. Diterapkan pada input NoHpAyah, NoHpIbu, dan NoHpWali
        */
        function validatePhoneNumber(input) {
            let phoneNumber = input.value.trim();
            const errorElement = document.getElementById(input.id + 'Error');

            // Cek format awal nomor
            const isValidStart = phoneNumber.startsWith('+62') ||
                phoneNumber.startsWith('08') ||
                phoneNumber.startsWith('8');

            if (!isValidStart) {
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                if (errorElement) {
                    errorElement.textContent = 'Nomor handphone harus diawali dengan +62, 08, atau 8';
                    errorElement.style.display = 'block';
                }
                return false;
            }

            // Konversi ke format +62 jika perlu
            if (phoneNumber.startsWith('08')) {
                phoneNumber = '+62' + phoneNumber.substring(1);
            } else if (phoneNumber.startsWith('8')) {
                phoneNumber = '+62' + phoneNumber;
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

        // Event listener untuk input nomor handphone
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
        //=== Region: Validasi Input Angka ===

        // Event listener untuk input dengan kelas 'number-only'
        // Validasi input hanya angka
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

        /* ===== Region: Validasi Input Angka =====
        Fungsi ini memvalidasi input angka dengan ketentuan:
        1. Hanya menerima angka dan tanda minus
        2. Menghapus karakter non-angka kecuali tanda minus di awal
        3. Menghapus angka nol di awal jika bukan angka desimal
        4. Diterapkan pada input-input angka lainnya
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

        // Fungsi untuk memvalidasi input nama dan tempat (hanya huruf)
        /**
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

        /**
         * Validasi input form dan tampilkan error
         * @param {HTMLElement} field - Input yang divalidasi
         */
        function validateField(field) {
            let errorField = document.getElementById(field.id + "Error");
            if (!errorField) {
                errorField = document.createElement('span');
                errorField.id = field.id + "Error";
                errorField.className = 'text-danger';
                field.parentNode.insertBefore(errorField, field.nextSibling);
            }

            if (field.type === 'radio') {
                let radioGroup = document.getElementsByName(field.name);
                let isChecked = Array.from(radioGroup).some(radio => radio.checked);
                if (!isChecked) {
                    document.getElementById('JenisKelaminError').style.display = 'block';
                    radioGroup.forEach(radio => radio.classList.add('is-invalid'));
                } else {
                    document.getElementById('JenisKelaminError').style.display = 'none';
                    radioGroup.forEach(radio => {
                        radio.classList.remove('is-invalid');
                        radio.classList.add('is-valid');
                    });
                }
            } else {
                // Validasi untuk input lainnya tetap sama
                if (!field.value.trim()) {
                    errorField.textContent = 'Kolom ini harus diisi.';
                    errorField.style.display = 'block';
                    field.classList.remove('is-valid');
                    field.classList.add('is-invalid');
                } else {
                    errorField.textContent = '';
                    errorField.style.display = 'none';
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                }
            }
        }

        /**
         * Memvalidasi input dan melanjutkan ke langkah berikutnya
         * @param {string} stepId - ID dari langkah yang sedang divalidasi
         */
        function validateAndNext(stepId) {
            let isValid = true;
            let fields = document.querySelectorAll('#' + stepId + ' .form-control[required], #' + stepId + ' input[type="radio"][required]');

            fields.forEach(function(field) {
                validateField(field);
                if (field.type === 'radio') {
                    let radioGroup = document.getElementsByName(field.name);
                    let isChecked = Array.from(radioGroup).some(radio => radio.checked);
                    if (!isChecked) isValid = false;
                } else if (field.classList.contains('is-invalid')) {
                    isValid = false;
                }
            });

            if (isValid) {
                stepper.next();
            } else {
                // Fokus ke input pertama yang tidak valid
                fields[0].focus();
            }
        }

        // Fungsi untuk validasi saat submit form
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
                alert('Mohon isi semua bidang yang wajib diisi sebelum mengirim formulir.');
            }
        });

        // Start Fungsi menampilkan preview file img atau pdf 
        /**
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
                        let errorMsg = [];
                        if (!allowedTypes.includes(fileType)) {
                            errorMsg.push(`Format file yang dipilih "${file.name}" (${fileType}) tidak valid. Format harus JPG, PNG, atau PDF`);
                        }
                        if (fileSize > maxSize) {
                            errorMsg.push(`Ukuran file "${file.name}" (${(fileSize/1024/1024).toFixed(2)}MB) melebihi batas maximal 2MB`);
                        }
                        errorElement.textContent = errorMsg.join(' dan ');
                        errorElement.classList.remove('d-none');
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

        /* ===== Region: Validasi NIK ===== */
        /**
         * Region Validasi NIK berisi fungsi-fungsi untuk:
         * 
         * 1. createNIKValidator(inputId)
         *    - Membuat validator untuk input NIK
         *    - Parameter: ID elemen input NIK
         *    - Menangani validasi format dan keunikan NIK
         * 
         * 2. validasiNIK(input) 
         *    - Memvalidasi format dan nilai NIK
         *    - Mengecek:
         *      - NIK tidak boleh kosong
         *      - NIK tidak boleh 16 angka 0
         *      - NIK harus 16 digit dan tidak diawali 0
         *    - Untuk NIK Santri: cek duplikasi via AJAX
         *
         * 3. tampilkanError() & sembunyikanError()
         *    - Menampilkan/menyembunyikan pesan error
         *    - Mengatur styling elemen error
         *
         * Event Listeners:
         * - Input: Validasi realtime saat input
         * - Blur: Validasi saat input kehilangan fokus
         */
        // Fungsi validasi NIK yang dapat digunakan ulang
        function createNIKValidator(inputId) {
            const nikInput = document.getElementById(inputId);
            if (!nikInput) return; // Pastikan elemen input ada

            const nikError = document.getElementById(inputId + 'Error');
            if (!nikError) {
                console.error(`Error element for ${inputId} not found`);
                return;
            }
            // fungsi untuk validasi NIK 
            function validasiNIK(input) {
                const nilai = input.value.replace(/\D/g, ''); // Hapus semua karakter non-digit
                const pola = /^[1-9]\d{15}$/;

                if (nilai === '') {
                    tampilkanError(`${inputId.replace('Nik', '')} NIK wajib diisi.`);
                    return false;
                } else if (nilai === '0000000000000000') {
                    tampilkanError(`${inputId.replace('Nik', '')} NIK tidak boleh terdiri dari 16 angka 0.`);
                    return false;
                } else if (!pola.test(nilai)) {
                    tampilkanError(`${inputId.replace('Nik', '')} NIK harus terdiri dari 16 digit angka dan tidak boleh diawali dengan angka 0.`);
                    return false;
                } else if (inputId === 'NikSantri' && nilai.length === 16 && pola.test(nilai)) {
                    // Buat AJAX request untuk cek NIK
                    fetch('/backend/santri/getNikSantri/' + nilai, { // Menggunakan endpoint yang sesuai
                            method: 'GET', // Ubah ke GET karena mengambil data
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                // Jika NIK sudah ada, tampilkan pesan error
                                tampilkanError(`NIK ${nilai} sudah terdaftar atas nama santri : ${data.data.NamaSantri}. Mohon periksa kembali NIK yang dimasukkan.`);
                                return false;
                            }
                            // Jika NIK belum digunakan
                            sembunyikanError();
                            return true;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            tampilkanError('Terjadi kesalahan saat memeriksa NIK');
                            return false;
                        });
                } else {
                    sembunyikanError();
                    return true;
                }
            }

            function tampilkanError(pesan) {
                nikError.textContent = pesan;
                nikError.style.display = 'block';
                nikInput.classList.add('is-invalid');
                nikInput.classList.remove('is-valid');
            }

            function sembunyikanError() {
                nikError.style.display = 'none';
                nikInput.classList.remove('is-invalid');
                nikInput.classList.add('is-valid');
            }

            // Event listener untuk input event
            nikInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\D/g, '').slice(0, 16); // Batasi input hanya angka dan maksimal 16 digit
                validasiNIK(this);
            });

            // Event listener untuk keypress event
            nikInput.addEventListener('keypress', function(e) {
                const karakter = String.fromCharCode(e.which);
                if (!/[0-9]/.test(karakter) || (this.value.length === 0 && karakter === '0')) {
                    e.preventDefault();
                }
            });

            // Fungsi untuk menangani paste dari clipboard
            nikInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const teksTempel = (e.clipboardData || window.clipboardData).getData('text');
                const teksBersih = teksTempel.replace(/\D/g, '').slice(0, 16);
                if (teksBersih.length > 0 && teksBersih[0] !== '0') {
                    this.value = teksBersih;
                    validasiNIK(this);
                }
            });

            // Event listener untuk blur event
            nikInput.addEventListener('blur', function() {
                validasiNIK(this);
            });

            // Validasi awal jika ada nilai default
            if (nikInput.value) {
                validasiNIK(nikInput);
            }
        }

        // Tunggu sampai DOM sepenuhnya dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Validasi untuk NIK Santri
            createNIKValidator('NikSantri');

            // Validasi untuk NIK Ayah
            createNIKValidator('NikAyah');

            // Validasi untuk NIK Ibu 
            createNIKValidator('NikIbu');
        });

        /* ===== End Region: Validasi NIK ===== */

        /* ===== Region: Validasi KK ===== */
        /**
         * 1. updateKKAyahState()
         *    - Mengatur status checkbox KK Ayah berdasarkan:
         *      a. Keberadaan file KK Santri
         *      b. Status hidup Ayah
         *    - Menampilkan pesan informasi sesuai kondisi
         * 
         * 2. updateKKIbuState() 
         *    - Mengatur status checkbox KK Ibu berdasarkan:
         *      a. Keberadaan file KK Santri/Ayah
         *      b. Status hidup Ibu
         *    - Menampilkan pesan informasi sesuai kondisi
         *
         * 3. Event Listeners:
         *    - DOMContentLoaded: Inisialisasi validasi
         *    - change: Memantau perubahan file dan status
         *    - click: Menangani interaksi checkbox
         *
         * 4. Fitur Utama:
         *    - Validasi upload file KK
         *    - Pengecekan status hidup orangtua
         *    - Opsi penggunaan KK yang sama
         *    - Pesan informasi dinamis
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Logika untuk KK Santri dan KK Ayah
            const fileKkSantri = document.getElementById('FileKkSantri');
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
                const santriHasKK = fileKkSantri.files.length > 0;
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
                    info.className = 'form-text text-success mt-1';
                    info.innerHTML = '<i class="fas fa-check-circle"></i> Anda dapat mencentang ini jika Ayah satu KK dengan Santri';
                }

                // Trigger updateKKIbuState karena status KK Ayah mempengaruhi KK Ibu
                updateKKIbuState();
            }

            // Fungsi untuk mengupdate status checkbox KK Ibu
            function updateKKIbuState() {
                const ibuHidup = statusIbu.value === 'Masih Hidup';
                const santriHasKK = fileKkSantri.files.length > 0;
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
                    info.className = 'form-text text-success mt-1';
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
            fileKkSantri.addEventListener('change', updateKKAyahState);
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
        document.addEventListener('DOMContentLoaded', function() {
            const kelasSelect = document.getElementById('IdKelas');
            const tanggalLahirInput = document.getElementById('TanggalLahirSantri');

            function validateTanggalLahir() {
                const selectedKelasId = parseInt(kelasSelect.value); // Mengambil ID kelas sebagai number

                // Abaikan validasi jika input kosong
                if (!tanggalLahirInput.value) {
                    // Hapus semua class validasi dan pesan error
                    tanggalLahirInput.classList.remove('is-invalid', 'is-valid');
                    const existingError = document.getElementById('TanggalLahirError');
                    if (existingError) {
                        existingError.remove();
                    }
                    return true;
                }

                const tanggalLahir = new Date(tanggalLahirInput.value);
                const today = new Date();
                const umur = today.getFullYear() - tanggalLahir.getFullYear();

                // Hapus pesan error yang ada
                const existingError = document.getElementById('TanggalLahirError');
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
                        // Untuk TK/TKA/TKB: usia 4-7 tahun
                        if (umur < 4 || umur > 7) {
                            isValid = false;
                            const minTahun = today.getFullYear() - 7; // Tahun untuk usia maksimal (7 tahun)
                            const maxTahun = today.getFullYear() - 4; // Tahun untuk usia minimal (4 tahun) 
                            errorMessage = formatErrorMessage('TK/TKA/TKB', 4, 7);
                        }
                        break;

                    case 4: // TPQ1/SD1
                        // Untuk TPQ1/SD1: usia 6-7 tahun
                        if (umur < 6 || umur > 7) {
                            isValid = false;
                            const minTahun = today.getFullYear() - 7;
                            const maxTahun = today.getFullYear() - 6;
                            errorMessage = formatErrorMessage('TPQ1/SD1', 6, 7);
                        }
                        break;

                    case 5: // TPQ2/SD2
                        // Untuk TPQ2/SD2: usia 7-8 tahun
                        if (umur < 7 || umur > 8) {
                            isValid = false;
                            const minTahun = today.getFullYear() - 8;
                            const maxTahun = today.getFullYear() - 7;
                            errorMessage = formatErrorMessage('TPQ2/SD2', 7, 8);
                        }
                        break;

                    case 6: // TPQ3/SD3
                        // Untuk TPQ3/SD3: usia 8-9 tahun
                        if (umur < 8 || umur > 9) {
                            isValid = false;
                            const minTahun = today.getFullYear() - 9;
                            const maxTahun = today.getFullYear() - 8;
                            errorMessage = formatErrorMessage('TPQ3/SD3', 8, 9);
                        }
                        break;

                    case 7: // TPQ4/SD4
                        // Untuk TPQ4/SD4: usia 9-10 tahun
                        if (umur < 9 || umur > 10) {
                            isValid = false;
                            const minTahun = today.getFullYear() - 10;
                            const maxTahun = today.getFullYear() - 9;
                            errorMessage = formatErrorMessage('TPQ4/SD4', 9, 10);
                        }
                        break;

                    case 8: // TPQ5/SD5
                        // Untuk TPQ5/SD5: usia 10-11 tahun
                        if (umur < 10 || umur > 11) {
                            isValid = false;
                            const minTahun = today.getFullYear() - 11;
                            const maxTahun = today.getFullYear() - 10;
                            errorMessage = formatErrorMessage('TPQ5/SD5', 10, 11);
                        }
                        break;

                    case 9: // TPQ6/SD6
                        // Untuk TPQ6/SD6: usia 11-12 tahun
                        if (umur < 11 || umur > 12) {
                            isValid = false;
                            const minTahun = today.getFullYear() - 12;
                            const maxTahun = today.getFullYear() - 11;
                            errorMessage = formatErrorMessage('TPQ6/SD6', 11, 12);
                        }
                        break;

                    default:
                        isValid = false;
                        errorMessage = 'Silakan pilih kelas terlebih dahulu';
                }

                // ... existing code ...

                if (!isValid) {
                    // Tambahkan class is-invalid pada input
                    tanggalLahirInput.classList.add('is-invalid');
                    tanggalLahirInput.classList.remove('is-valid');

                    // Tampilkan pesan error
                    const errorDiv = document.createElement('div');
                    errorDiv.id = 'TanggalLahirError';
                    errorDiv.className = 'alert alert-danger mt-2 small';
                    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${errorMessage}`;
                    tanggalLahirInput.parentElement.appendChild(errorDiv);
                    return false;
                } else {
                    // Jika valid, hapus class is-invalid dan tambahkan is-valid
                    tanggalLahirInput.classList.remove('is-invalid');
                    tanggalLahirInput.classList.add('is-valid');
                    return true;
                }

                return true;
            }

            // Tambahkan event listener untuk perubahan tanggal lahir dan kelas
            tanggalLahirInput.addEventListener('change', validateTanggalLahir);
            kelasSelect.addEventListener('change', validateTanggalLahir);
            // Tambahkan validasi tanggal lahir saat tombol next ditekan
            document.querySelector('button[onclick="validateAndNext(\'santri-part\')"]').addEventListener('click', function() {
                // Validasi tanggal lahir
                validateTanggalLahir();
            });

        });
        /* ===== End Region: Validasi Tanggal Lahir Santri ===== */

        // Fungsi reusable untuk menangani semua input "Lainnya"
        function toggleLainnyaInput(selectId, inputId, errorId, labelText) {
            const selectElement = document.getElementById(selectId);
            const inputElement = document.getElementById(inputId);
            const inputParent = inputElement.parentElement;
            const errorSpan = document.getElementById(errorId) || createErrorSpan(errorId);
            const isLainnya = selectElement.value === "Lainnya" || selectElement.value === "Lainya"; // Handle kedua kemungkinan spelling

            // Toggle display dan required state
            if (isLainnya) {
                inputParent.style.display = "block";
                inputElement.disabled = false;
                inputElement.required = true;
                $(inputParent).show();

                // Validasi jika input kosong
                if (!inputElement.value.trim()) {
                    errorSpan.style.display = "block";
                    errorSpan.textContent = `${labelText} wajib diisi`;
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
                            errorSpan.textContent = `${field.labelText} wajib diisi`;
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

            // Tambahkan CSS untuk styling validasi
            if (!document.getElementById('validationStyles')) {
                const style = document.createElement('style');
                style.id = 'validationStyles';
                style.textContent = `
                    .is-valid {
                        border-color: #28a745 !important;
                        padding-right: calc(1.5em + 0.75rem) !important;
                        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e") !important;
                        background-repeat: no-repeat !important;
                        background-position: right calc(0.375em + 0.1875rem) center !important;
                        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
                    }
                    .is-invalid {
                        border-color: #dc3545 !important;
                        padding-right: calc(1.5em + 0.75rem) !important;
                        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23dc3545' viewBox='-2 -2 7 7'%3e%3cpath stroke='%23dc3545' d='M0 0l3 3m0-3L0 3'/%3e%3ccircle r='.5'/%3e%3ccircle cx='3' r='.5'/%3e%3ccircle cy='3' r='.5'/%3e%3ccircle cx='3' cy='3' r='.5'/%3e%3c/svg%3E") !important;
                        background-repeat: no-repeat !important;
                        background-position: right calc(0.375em + 0.1875rem) center !important;
                        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
                    }
                `;
                document.head.appendChild(style);
            }
        });
    </script>

    <?= $this->endSection(); ?>