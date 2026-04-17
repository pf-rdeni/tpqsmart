<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<!-- Memuat library EasyQRCodeJS dari CDN -->
<script src="https://unpkg.com/easyqrcodejs@4.6.1/dist/easy.qrcode.min.js"></script>

<div class="container-fluid">
    <div class="row">
        <!-- Form Settings -->
        <div class="col-md-7">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Pengaturan QR Code</h3>
                </div>
                <div class="card-body">
                    <form id="qrForm" onsubmit="event.preventDefault(); generateQR();">
                        <div class="form-group">
                            <label for="qrContent">Konten (URL / Teks / Link)</label>
                            <textarea class="form-control" id="qrContent" rows="3" placeholder="Masukkan URL, Teks, atau Link..." required>https://</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qrColorDark">Warna QR (Foreground)</label>
                                    <input type="color" class="form-control" id="qrColorDark" value="#000000" style="height: 40px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="qrColorLight">Warna Background</label>
                                    <input type="color" class="form-control" id="qrColorLight" value="#ffffff" style="height: 40px;">
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5>Elemen Tengah (Opsional)</h5>
                        <p class="text-muted text-sm">Tambahkan Logo/Gambar atau Tulisan di tengah QR Code.</p>
                        
                        <div class="form-group">
                            <label>Pilih Tipe Sisipan</label>
                            <select class="form-control" id="insertType" onchange="toggleInsertType()">
                                <option value="none">Tidak Ada</option>
                                <option value="blank">Kosongkan Tengah (Buat menempel logo fisik)</option>
                                <option value="logo">Sematkan Logo / Gambar</option>
                                <option value="text">Sematkan Teks / Tulisan</option>
                            </select>
                        </div>

                        <div id="blankOptions" style="display: none;">
                            <div class="form-group pt-2">
                                <label for="qrBlankSize">Ukuran Ruang Kosong: <span id="blankSizeVal">70</span>px</label>
                                <input type="range" class="form-control-range" id="qrBlankSize" min="40" max="90" value="70" onchange="updateBlankSizeLbl(); generateQR();">
                            </div>
                            <small class="form-text text-muted"><i class="fas fa-info-circle"></i> Ukuran maksimal telah dibatasi ke nilai aman agar QR Code tetap bisa di-scan dengan baik.</small>
                        </div>

                        <div id="logoOptions" style="display: none;">
                            <div class="form-group">
                                <label for="qrLogo">Upload Logo</label>
                                <input type="file" class="form-control-file" id="qrLogo" accept="image/*" onchange="previewLogo(event)">
                                <small class="form-text text-muted">Gambar akan disisipkan di tengah QR Code.</small>
                            </div>
                            
                            <!-- Kontrol Tambahan Logo -->
                            <div class="form-group pt-2">
                                <label for="qrLogoSize">Ukuran Logo: <span id="logoSizeVal">70</span>px</label>
                                <input type="range" class="form-control-range" id="qrLogoSize" min="40" max="90" value="70" onchange="updateLogoSizeLbl(); generateQR();">
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="qrLogoBg" checked onchange="generateQR()">
                                <label class="form-check-label" for="qrLogoBg">Bersihkan titik QR di belakang logo (Dianjurkan)</label>
                                <small class="form-text text-muted d-block mt-1">
                                    <i class="fas fa-info-circle"></i> Sistem otomatis menggunakan ketahanan <i>Error Correction Level Tinggi (30%)</i>, sehingga QR Code akan tetap terbaca walau tertutup logo.
                                </small>
                            </div>
                        </div>

                        <div id="textOptions" style="display: none;">
                            <div class="form-group">
                                <label for="qrTextCenter">Tulisan Tengah</label>
                                <input type="text" class="form-control" id="qrTextCenter" placeholder="Contoh: SCAN ME" oninput="generateQR()">
                            </div>
                            <div class="form-group">
                                <label for="qrTextColor">Warna Tulisan</label>
                                <input type="color" class="form-control" id="qrTextColor" value="#ff0000" style="height: 40px;" onchange="generateQR()">
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="qrTextBg" checked onchange="generateQR()">
                                <label class="form-check-label" for="qrTextBg">Bersihkan titik QR di belakang teks</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3 btn-block"><i class="fas fa-qrcode"></i> Perbarui / Generate QR Code</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview & Download QR -->
        <div class="col-md-5">
            <div class="card card-success card-outline text-center">
                <div class="card-header">
                    <h3 class="card-title">Hasil QR Code</h3>
                </div>
                <div class="card-body">
                    <div id="qrcodePreview" style="min-height: 250px; display: flex; justify-content: center; align-items: center; border: 1px dashed #ccc; padding: 20px; background: #fafafa; border-radius: 6px;">
                        <span class="text-muted" id="emptyStateText">Preview QR akan muncul di sini...</span>
                        <div id="qrcode" style="display: none;"></div>
                    </div>
                    
                    <button class="btn btn-success mt-4 btn-block shadow-sm" id="btnDownload" style="display: none;" onclick="downloadQR()">
                        <i class="fas fa-download"></i> Download QR Image
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentLogoBase64 = null;
    let qrCodeInstance = null;
    
    // Auto generate debounce timer
    let typingTimer;
    document.getElementById('qrContent').addEventListener('input', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(generateQR, 500);
    });

    function toggleInsertType() {
        const type = document.getElementById('insertType').value;
        document.getElementById('blankOptions').style.display = type === 'blank' ? 'block' : 'none';
        document.getElementById('logoOptions').style.display = type === 'logo' ? 'block' : 'none';
        document.getElementById('textOptions').style.display = type === 'text' ? 'block' : 'none';
        
        // Coba regenerate jika ada gambar/teks supaya preview ter-update
        if(currentLogoBase64 || document.getElementById('qrTextCenter').value || type === 'blank') {
            generateQR();
        }
    }
    
    function updateBlankSizeLbl() {
        document.getElementById('blankSizeVal').innerText = document.getElementById('qrBlankSize').value;
    }
    
    function updateLogoSizeLbl() {
        document.getElementById('logoSizeVal').innerText = document.getElementById('qrLogoSize').value;
    }

    function previewLogo(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                currentLogoBase64 = e.target.result;
                generateQR(); // langsung render
            }
            reader.readAsDataURL(file);
        } else {
            currentLogoBase64 = null;
            generateQR();
        }
    }

    function generateQR() {
        const content = document.getElementById('qrContent').value.trim();
        const colorDark = document.getElementById('qrColorDark').value;
        const colorLight = document.getElementById('qrColorLight').value;
        const insertType = document.getElementById('insertType').value;
        
        if (!content) {
            return; // Jangan generate jika kosong, biarkan tunggu ketikan
        }

        // Tampilkan container
        document.getElementById('emptyStateText').style.display = 'none';
        document.getElementById('qrcode').style.display = 'block';
        document.getElementById('qrcode').innerHTML = ""; // Bersihkan QR sebelumnya

        // Opsi dasar Qr code dengan Error Correction tertinggi (Level H = 30% kerusakan diizinkan)
        const options = {
            text: content,
            width: 300,
            height: 300,
            colorDark: colorDark,
            colorLight: colorLight,
            correctLevel: QRCode.CorrectLevel.H, 
            dotScale: 1
        };

        // Tambahan fitur sisipan logo atau teks
        if (insertType === 'logo' && currentLogoBase64) {
            const lSize = parseInt(document.getElementById('qrLogoSize').value);
            const clearBg = document.getElementById('qrLogoBg').checked;
            
            options.logo = currentLogoBase64;
            options.logoWidth = lSize;
            options.logoHeight = lSize;
            options.logoBackgroundTransparent = !clearBg; // jika clearBg=true, transparent=false
            options.logoBackgroundColor = colorLight;
            
        } else if (insertType === 'text') {
            const textCenter = document.getElementById('qrTextCenter').value.trim();
            const textColor = document.getElementById('qrTextColor').value;
            const clearBgTxt = document.getElementById('qrTextBg').checked;
            
            if (textCenter !== '') {
                options.logo = "text"; 
                options.logoText = textCenter;
                options.logoFont = "bold 20px Arial";
                options.logoColor = textColor;
                options.logoBackgroundColor = colorLight;
                options.logoBackgroundTransparent = !clearBgTxt;
                options.logoWidth = textCenter.length * 13; // Perkiraan dinamis
                options.logoHeight = 35;
            }
        } else if (insertType === 'blank') {
            // Memasukkan kotak putih murni
            const bSize = parseInt(document.getElementById('qrBlankSize').value || 70);
            
            // SVG bujur sangkar dengan warna background
            const svgb64 = `data:image/svg+xml;base64,${btoa('<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><rect width="100" height="100" fill="'+colorLight+'"/></svg>')}`;
            
            options.logo = svgb64;
            options.logoWidth = bSize;
            options.logoHeight = bSize;
            options.logoBackgroundTransparent = false;
            options.logoBackgroundColor = colorLight;
        }

        // Generate barcode
        qrCodeInstance = new QRCode(document.getElementById("qrcode"), options);

        // Tampilkan tombol download
        setTimeout(() => {
            document.getElementById('btnDownload').style.display = 'block';
        }, 300); // beri jeda lebih stabil
    }

    function downloadQR() {
        const qrcodeDiv = document.getElementById('qrcode');
        let canvas = qrcodeDiv.querySelector('canvas');
        if (canvas) {
            const dataUrl = canvas.toDataURL("image/png");
            const a = document.createElement('a');
            a.href = dataUrl;
            a.download = 'QR_Code_' + new Date().getTime() + '.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        } else {
            alert('Silakan generate QR Code terlebih dahulu.');
        }
    }

    // Set default view on load
    document.addEventListener("DOMContentLoaded", function() {
        toggleInsertType();
    });
</script>
<?= $this->endSection() ?>