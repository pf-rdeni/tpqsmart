<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Jadwal Sholat</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('backend/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Jadwal Sholat</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cari Jadwal Sholat</h3>
                    </div>
                    <div class="card-body">
                        <!-- Tabs untuk memilih metode pencarian -->
                        <ul class="nav nav-tabs mb-3" id="searchTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="city-tab" data-toggle="tab" href="#citySearch" role="tab" aria-controls="citySearch" aria-selected="true">
                                    <i class="fas fa-city"></i> Nama Lokasi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="coordinate-tab" data-toggle="tab" href="#coordinateSearch" role="tab" aria-controls="coordinateSearch" aria-selected="false">
                                    <i class="fas fa-map-marker-alt"></i> Titik Lokasi (GPS)
                                </a>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="searchTabContent">
                            <!-- Tab Pencarian Nama Lokasi -->
                            <div class="tab-pane fade show active" id="citySearch" role="tabpanel" aria-labelledby="city-tab">
                                <form method="GET" action="<?= base_url('backend/jadwal-sholat') ?>" class="mb-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nama Kota / Kecamatan / Desa</label>
                                                <input type="text" name="city" id="inputCity" class="form-control" 
                                                       value="<?= esc($city ?? 'Jakarta') ?>" 
                                                       list="cityList"
                                                       placeholder="Ketik atau pilih kota/kecamatan"
                                                       autocomplete="off">
                                        <datalist id="cityList">
                                            <!-- Kota Besar -->
                                            <option value="Jakarta">Jakarta</option>
                                            <option value="Bandung">Bandung</option>
                                            <option value="Surabaya">Surabaya</option>
                                            <option value="Medan">Medan</option>
                                            <option value="Semarang">Semarang</option>
                                            <option value="Makassar">Makassar</option>
                                            <option value="Palembang">Palembang</option>
                                            <option value="Depok">Depok</option>
                                            <option value="Tangerang">Tangerang</option>
                                            <option value="Bekasi">Bekasi</option>
                                            <option value="Yogyakarta">Yogyakarta</option>
                                            <option value="Malang">Malang</option>
                                            <option value="Surakarta">Surakarta</option>
                                            <option value="Bogor">Bogor</option>
                                            <option value="Batam">Batam</option>
                                            <option value="Pekanbaru">Pekanbaru</option>
                                            <option value="Padang">Padang</option>
                                            <option value="Denpasar">Denpasar</option>
                                            <option value="Banjarmasin">Banjarmasin</option>
                                            <option value="Pontianak">Pontianak</option>
                                            <option value="Bintan">Bintan</option>
                                            <option value="Tanjung Pinang">Tanjung Pinang</option>
                                            
                                            <!-- Kecamatan Populer -->
                                            <option value="Jakarta Selatan">Jakarta Selatan</option>
                                            <option value="Jakarta Utara">Jakarta Utara</option>
                                            <option value="Jakarta Timur">Jakarta Timur</option>
                                            <option value="Jakarta Barat">Jakarta Barat</option>
                                            <option value="Jakarta Pusat">Jakarta Pusat</option>
                                            <option value="Bandung Barat">Bandung Barat</option>
                                            <option value="Surabaya Utara">Surabaya Utara</option>
                                            <option value="Surabaya Selatan">Surabaya Selatan</option>
                                            <option value="Medan Selayang">Medan Selayang</option>
                                            <option value="Semarang Tengah">Semarang Tengah</option>
                                            <option value="Makassar Utara">Makassar Utara</option>
                                            <option value="Palembang Ilir">Palembang Ilir</option>
                                            <option value="Depok Timur">Depok Timur</option>
                                            <option value="Tangerang Selatan">Tangerang Selatan</option>
                                            <option value="Bekasi Timur">Bekasi Timur</option>
                                            <option value="Yogyakarta Utara">Yogyakarta Utara</option>
                                            <option value="Malang Utara">Malang Utara</option>
                                            <option value="Bogor Selatan">Bogor Selatan</option>
                                            <option value="Batam Center">Batam Center</option>
                                            <option value="Pekanbaru Barat">Pekanbaru Barat</option>
                                            <option value="Padang Barat">Padang Barat</option>
                                            <option value="Denpasar Selatan">Denpasar Selatan</option>
                                            <option value="Banjarmasin Utara">Banjarmasin Utara</option>
                                            <option value="Pontianak Utara">Pontianak Utara</option>
                                            
                                            <!-- Kecamatan Bintan & Kepulauan Riau -->
                                            <option value="Seri Kuala Lobam">Seri Kuala Lobam</option>
                                            <option value="Tanjung Uban">Tanjung Uban</option>
                                            <option value="Kijang">Kijang</option>
                                            <option value="Bintan Timur">Bintan Timur</option>
                                            <option value="Bintan Utara">Bintan Utara</option>
                                            <option value="Bintan Pesisir">Bintan Pesisir</option>
                                            <option value="Gunung Kijang">Gunung Kijang</option>
                                            <option value="Teluk Bintan">Teluk Bintan</option>
                                            <option value="Toapaya">Toapaya</option>
                                            <option value="Tambelan">Tambelan</option>
                                            
                                            <!-- Desa/Kelurahan Bintan & Sekitarnya -->
                                            <option value="Lobam">Lobam</option>
                                            <option value="Kuala Lobam">Kuala Lobam</option>
                                            <option value="Seri Kuala Lobam">Seri Kuala Lobam</option>
                                            <option value="Tanjung Uban Selatan">Tanjung Uban Selatan</option>
                                            <option value="Tanjung Uban Utara">Tanjung Uban Utara</option>
                                            <option value="Kijang Kota">Kijang Kota</option>
                                            <option value="Kijang Hilir">Kijang Hilir</option>
                                            <option value="Kijang Hulu">Kijang Hulu</option>
                                            <option value="Bintan Buyu">Bintan Buyu</option>
                                            <option value="Bintan Pesisir">Bintan Pesisir</option>
                                            <option value="Teluk Bakau">Teluk Bakau</option>
                                            <option value="Teluk Sebong">Teluk Sebong</option>
                                            <option value="Sebung Pereh">Sebung Pereh</option>
                                            <option value="Kawal">Kawal</option>
                                            <option value="Gunung Kijang">Gunung Kijang</option>
                                            <option value="Berakit">Berakit</option>
                                            <option value="Bak Serengam">Bak Serengam</option>
                                            <option value="Toapaya">Toapaya</option>
                                            <option value="Toapaya Asri">Toapaya Asri</option>
                                            <option value="Kelam Pagi">Kelam Pagi</option>
                                            <option value="Tambelan">Tambelan</option>
                                            
                                            <!-- Desa/Kelurahan Jakarta -->
                                            <option value="Kebayoran Baru">Kebayoran Baru</option>
                                            <option value="Kebayoran Lama">Kebayoran Lama</option>
                                            <option value="Cilandak">Cilandak</option>
                                            <option value="Pasar Minggu">Pasar Minggu</option>
                                            <option value="Jagakarsa">Jagakarsa</option>
                                            <option value="Pesanggrahan">Pesanggrahan</option>
                                            <option value="Tebet">Tebet</option>
                                            <option value="Setiabudi">Setiabudi</option>
                                            <option value="Mampang Prapatan">Mampang Prapatan</option>
                                            <option value="Kemang">Kemang</option>
                                            <option value="Kuningan">Kuningan</option>
                                            <option value="Senayan">Senayan</option>
                                            <option value="Gambir">Gambir</option>
                                            <option value="Tanah Abang">Tanah Abang</option>
                                            <option value="Menteng">Menteng</option>
                                            <option value="Senen">Senen</option>
                                            <option value="Cempaka Putih">Cempaka Putih</option>
                                            <option value="Kemayoran">Kemayoran</option>
                                            <option value="Sawah Besar">Sawah Besar</option>
                                            
                                            <!-- Desa/Kelurahan Bandung -->
                                            <option value="Coblong">Coblong</option>
                                            <option value="Sukajadi">Sukajadi</option>
                                            <option value="Cicendo">Cicendo</option>
                                            <option value="Andir">Andir</option>
                                            <option value="Astana Anyar">Astana Anyar</option>
                                            <option value="Regol">Regol</option>
                                            <option value="Lengkong">Lengkong</option>
                                            <option value="Bandung Wetan">Bandung Wetan</option>
                                            <option value="Sumur Bandung">Sumur Bandung</option>
                                            <option value="Babakan Ciparay">Babakan Ciparay</option>
                                            
                                            <!-- Desa/Kelurahan Surabaya -->
                                            <option value="Gubeng">Gubeng</option>
                                            <option value="Wonokromo">Wonokromo</option>
                                            <option value="Sawahan">Sawahan</option>
                                            <option value="Tegalsari">Tegalsari</option>
                                            <option value="Simokerto">Simokerto</option>
                                            <option value="Genteng">Genteng</option>
                                            <option value="Bubutan">Bubutan</option>
                                            <option value="Tandes">Tandes</option>
                                            <option value="Sukolilo">Sukolilo</option>
                                            <option value="Rungkut">Rungkut</option>
                                            
                                            <!-- Desa/Kelurahan Medan -->
                                            <option value="Medan Polonia">Medan Polonia</option>
                                            <option value="Medan Baru">Medan Baru</option>
                                            <option value="Medan Timur">Medan Timur</option>
                                            <option value="Medan Selayang">Medan Selayang</option>
                                            <option value="Medan Helvetia">Medan Helvetia</option>
                                            <option value="Medan Petisah">Medan Petisah</option>
                                            <option value="Medan Sunggal">Medan Sunggal</option>
                                            
                                            <!-- Desa/Kelurahan Yogyakarta -->
                                            <option value="Gondokusuman">Gondokusuman</option>
                                            <option value="Jetis">Jetis</option>
                                            <option value="Tegalrejo">Tegalrejo</option>
                                            <option value="Ngampilan">Ngampilan</option>
                                            <option value="Danurejan">Danurejan</option>
                                            <option value="Gedongtengen">Gedongtengen</option>
                                            <option value="Mantrijeron">Mantrijeron</option>
                                            <option value="Kraton">Kraton</option>
                                            <option value="Mergangsan">Mergangsan</option>
                                            
                                            <!-- Desa/Kelurahan Batam -->
                                            <option value="Batu Ampar">Batu Ampar</option>
                                            <option value="Sekupang">Sekupang</option>
                                            <option value="Nongsa">Nongsa</option>
                                            <option value="Bengkong">Bengkong</option>
                                            <option value="Lubuk Baja">Lubuk Baja</option>
                                            <option value="Batam Center">Batam Center</option>
                                            <option value="Sagulung">Sagulung</option>
                                            <option value="Galang">Galang</option>
                                            
                                            <!-- Desa/Kelurahan Tanjung Pinang -->
                                            <option value="Tanjung Pinang Barat">Tanjung Pinang Barat</option>
                                            <option value="Tanjung Pinang Timur">Tanjung Pinang Timur</option>
                                            <option value="Tanjung Pinang Kota">Tanjung Pinang Kota</option>
                                            <option value="Bukit Bestari">Bukit Bestari</option>
                                            <option value="Dompak">Dompak</option>
                                            <option value="Senggarang">Senggarang</option>
                                            <option value="Kampung Bugis">Kampung Bugis</option>
                                            
                                            <!-- Desa/Kelurahan Lainnya -->
                                            <option value="Cikarang">Cikarang</option>
                                            <option value="Karawang">Karawang</option>
                                            <option value="Cibubur">Cibubur</option>
                                            <option value="Cimahi">Cimahi</option>
                                            <option value="Cirebon">Cirebon</option>
                                            <option value="Garut">Garut</option>
                                            <option value="Tasikmalaya">Tasikmalaya</option>
                                            <option value="Purwakarta">Purwakarta</option>
                                            <option value="Subang">Subang</option>
                                            <option value="Indramayu">Indramayu</option>
                                        </datalist>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Ketik nama kota, kecamatan, desa, atau kelurahan. Pilih dari daftar untuk memudahkan pencarian.
                                        </small>
                                    </div>
                                </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Tanggal (Opsional)</label>
                                                <input type="date" name="date" class="form-control" 
                                                       value="<?= esc($date ?? date('Y-m-d')) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="fas fa-search"></i> Cari Jadwal
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Tab Pencarian Koordinat GPS -->
                            <div class="tab-pane fade" id="coordinateSearch" role="tabpanel" aria-labelledby="coordinate-tab">
                                <form method="GET" action="<?= base_url('backend/jadwal-sholat') ?>" id="coordinateForm" class="mb-4">
                                    <input type="hidden" name="lat" id="inputLat">
                                    <input type="hidden" name="long" id="inputLong">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Latitude (Garis Lintang)</label>
                                                <input type="number" step="any" id="latitude" class="form-control" 
                                                       placeholder="Contoh: -6.2088" 
                                                       required>
                                                <small class="form-text text-muted">Range: -90 sampai 90</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Longitude (Garis Bujur)</label>
                                                <input type="number" step="any" id="longitude" class="form-control" 
                                                       placeholder="Contoh: 106.8456" 
                                                       required>
                                                <small class="form-text text-muted">Range: -180 sampai 180</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="button" id="btnGetLocation" class="btn btn-info btn-block mb-2">
                                                    <i class="fas fa-crosshairs"></i> Ambil Lokasi Saya
                                                </button>
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="fas fa-search"></i> Cari Jadwal
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Tanggal (Opsional)</label>
                                                <input type="date" name="date" class="form-control" 
                                                       value="<?= esc($date ?? date('Y-m-d')) ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-info-circle"></i> Cara Menggunakan:</h6>
                                        <ul class="mb-0">
                                            <li>Klik "Ambil Lokasi Saya" untuk mendapatkan koordinat GPS otomatis</li>
                                            <li>Atau masukkan koordinat manual (Latitude dan Longitude)</li>
                                            <li>Contoh koordinat: Jakarta (-6.2088, 106.8456), Bandung (-6.9175, 107.6191)</li>
                                        </ul>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <script>
                            // Autocomplete enhancement
                            document.getElementById('inputCity').addEventListener('input', function() {
                                const value = this.value.toLowerCase();
                                const datalist = document.getElementById('cityList');
                                const options = datalist.querySelectorAll('option');
                                
                                // Highlight matching options (optional visual feedback)
                                options.forEach(option => {
                                    if (option.value.toLowerCase().includes(value)) {
                                        option.style.display = 'block';
                                    }
                                });
                            });
                            
                            // Form koordinat handler
                            document.getElementById('coordinateForm').addEventListener('submit', function(e) {
                                const lat = document.getElementById('latitude').value;
                                const lng = document.getElementById('longitude').value;
                                
                                if (!lat || !lng) {
                                    e.preventDefault();
                                    alert('Silakan isi Latitude dan Longitude');
                                    return false;
                                }
                                
                                // Validasi range
                                if (lat < -90 || lat > 90) {
                                    e.preventDefault();
                                    alert('Latitude harus antara -90 sampai 90');
                                    return false;
                                }
                                
                                if (lng < -180 || lng > 180) {
                                    e.preventDefault();
                                    alert('Longitude harus antara -180 sampai 180');
                                    return false;
                                }
                                
                                // Set hidden inputs
                                document.getElementById('inputLat').value = lat;
                                document.getElementById('inputLong').value = lng;
                                
                                // Redirect ke URL dengan koordinat
                                const date = this.querySelector('input[name="date"]').value;
                                let url = '<?= base_url('backend/jadwal-sholat') ?>/' + lat + '/' + lng;
                                if (date) {
                                    url += '?date=' + date;
                                }
                                window.location.href = url;
                                e.preventDefault();
                                return false;
                            });
                            
                            // Get current location
                            document.getElementById('btnGetLocation').addEventListener('click', function() {
                                const btn = this;
                                const originalText = btn.innerHTML;
                                
                                btn.disabled = true;
                                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengambil lokasi...';
                                
                                if (!navigator.geolocation) {
                                    alert('Geolocation tidak didukung oleh browser Anda');
                                    btn.disabled = false;
                                    btn.innerHTML = originalText;
                                    return;
                                }
                                
                                navigator.geolocation.getCurrentPosition(
                                    function(position) {
                                        const lat = position.coords.latitude;
                                        const lng = position.coords.longitude;
                                        
                                        document.getElementById('latitude').value = lat.toFixed(6);
                                        document.getElementById('longitude').value = lng.toFixed(6);
                                        
                                        btn.disabled = false;
                                        btn.innerHTML = originalText;
                                        
                                        // Show success message
                                        const alert = document.createElement('div');
                                        alert.className = 'alert alert-success alert-dismissible fade show mt-2';
                                        alert.innerHTML = '<i class="fas fa-check-circle"></i> Lokasi berhasil didapatkan! Koordinat: ' + lat.toFixed(6) + ', ' + lng.toFixed(6) + 
                                                         '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>';
                                        btn.parentElement.appendChild(alert);
                                        
                                        setTimeout(() => {
                                            alert.remove();
                                        }, 5000);
                                    },
                                    function(error) {
                                        let errorMsg = 'Gagal mendapatkan lokasi: ';
                                        switch(error.code) {
                                            case error.PERMISSION_DENIED:
                                                errorMsg += 'Akses lokasi ditolak oleh pengguna';
                                                break;
                                            case error.POSITION_UNAVAILABLE:
                                                errorMsg += 'Informasi lokasi tidak tersedia';
                                                break;
                                            case error.TIMEOUT:
                                                errorMsg += 'Waktu permintaan lokasi habis';
                                                break;
                                            default:
                                                errorMsg += 'Error tidak diketahui';
                                                break;
                                        }
                                        alert(errorMsg);
                                        btn.disabled = false;
                                        btn.innerHTML = originalText;
                                    },
                                    {
                                        enableHighAccuracy: true,
                                        timeout: 10000,
                                        maximumAge: 0
                                    }
                                );
                            });
                        </script>

                        <?php if (isset($result) && $result['success']): ?>
                            <div class="alert alert-success">
                                <h5><i class="icon fas fa-check"></i> Jadwal Sholat Ditemukan</h5>
                                <p><strong>Lokasi:</strong> <?= esc($result['city'] ?? ($result['latitude'] ?? '') . ', ' . ($result['longitude'] ?? '')) ?></p>
                                <?php if (isset($result['country'])): ?>
                                    <p><strong>Negara:</strong> <?= esc($result['country']) ?></p>
                                <?php endif; ?>
                                <p><strong>Tanggal:</strong> <?= esc($result['date']) ?></p>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Waktu Sholat</th>
                                            <th>Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Subuh (Fajr)</strong></td>
                                            <td><?= esc($result['prayer_times']['fajr'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Terbit (Shurooq)</strong></td>
                                            <td><?= esc($result['prayer_times']['shurooq'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dzuhur (Dhuhr)</strong></td>
                                            <td><?= esc($result['prayer_times']['dhuhr'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ashar (Asr)</strong></td>
                                            <td><?= esc($result['prayer_times']['asr'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Maghrib</strong></td>
                                            <td><?= esc($result['prayer_times']['maghrib'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Isya (Isha)</strong></td>
                                            <td><?= esc($result['prayer_times']['isha'] ?? '-') ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php elseif (isset($result) && !$result['success']): ?>
                            <div class="alert alert-danger">
                                <h5><i class="icon fas fa-ban"></i> Error</h5>
                                <p><?= esc($result['error'] ?? 'Terjadi kesalahan saat mengambil data jadwal sholat') ?></p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> Informasi</h5>
                                <p>Silakan masukkan nama kota atau kecamatan untuk melihat jadwal sholat.</p>
                                <p><strong>Tips:</strong></p>
                                <ul>
                                    <li>Ketik nama kota, kecamatan, desa, atau kelurahan di kolom pencarian</li>
                                    <li>Pilih dari daftar dropdown yang muncul untuk memudahkan</li>
                                    <li>Contoh: Jakarta, Bandung, Seri Kuala Lobam, Tanjung Uban, Lobam, Kijang</li>
                                    <li>Daftar mencakup kota besar, kecamatan, dan desa/kelurahan populer</li>
                                    <li>Anda juga bisa mencari berdasarkan koordinat (latitude, longitude)</li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>

