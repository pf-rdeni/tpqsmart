<?= $this->extend('frontend/template/index') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Hubungi Kami</h4>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="telepon" class="form-label">No. Telepon</label>
                            <input type="tel" class="form-control" id="telepon" name="telepon">
                        </div>
                        
                        <div class="mb-3">
                            <label for="pesan" class="form-label">Pesan</label>
                            <textarea class="form-control" id="pesan" name="pesan" rows="4" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Kontak -->
    <div class="row mt-5">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-map-marker-alt fa-2x mb-2"></i>
                    <h5>Alamat</h5>
                    <p>Jl. Contoh No. 123<br>Kota, Provinsi 12345</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-phone fa-2x mb-2"></i>
                    <h5>Telepon</h5>
                    <p>+62 812-3456-7890<br>+62 821-9876-5432</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-envelope fa-2x mb-2"></i>
                    <h5>Email</h5>
                    <p>info@tpqonline.com<br>admin@tpqonline.com</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Peta Google Maps -->
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.6664463317306!2d106.82496851476882!3d-6.175392395527383!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5d2e764b12d%3A0x3d2ad6e1e0e9bcc8!2sMonumen%20Nasional!5e0!3m2!1sid!2sid!4v1647831234567!5m2!1sid!2sid" 
                        width="100%" 
                        height="450" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 