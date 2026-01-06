<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= $page_title ?> - Semester <?= $semester ?></h3>
            <div class="card-tools">
                <a href="<?= base_url('backend/rapor/serah-terima/Ganjil') ?>" class="btn btn-warning btn-sm <?= $semester === 'Ganjil' ? 'active' : '' ?>">
                    Semester Ganjil
                </a>
                <a href="<?= base_url('backend/rapor/serah-terima/Genap') ?>" class="btn btn-info btn-sm <?= $semester === 'Genap' ? 'active' : '' ?>">
                    Semester Genap
                </a>
                <a href="<?= base_url('backend/rapor/Ganjil') ?>" class="btn btn-sm btn-secondary ml-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Rapor
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($dataKelas)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Tidak ada kelas yang tersedia.
                </div>
            <?php else: ?>
                <!-- Tab Navigation -->
                <div class="card card-primary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs flex-wrap justify-content-start justify-content-md-between" id="kelasTab" role="tablist">
                            <?php 
                            $firstKelas = true;
                            $kelasKeys = array_keys($dataKelas);
                            $firstKelasId = !empty($kelasKeys) ? $kelasKeys[0] : null;
                            foreach ($dataKelas as $idKelas => $namaKelas) : 
                            ?>
                                <li class="nav-item flex-fill mx-1 my-md-0 my-1">
                                    <a class="nav-link border-white text-center <?= $firstKelas ? 'active' : '' ?>"
                                        id="tab-<?= $idKelas ?>"
                                        data-toggle="tab"
                                        href="#kelas-<?= $idKelas ?>"
                                        role="tab"
                                        aria-controls="kelas-<?= $idKelas ?>"
                                        aria-selected="<?= $firstKelas ? 'true' : 'false' ?>">
                                        <?= esc($namaKelas) ?>
                                    </a>
                                </li>
                            <?php 
                                $firstKelas = false;
                            endforeach; 
                            ?>
                        </ul>
                    </div>
                    <br>
                    <!-- Tab Content -->
                    <div class="card-body">
                        <div class="tab-content" id="kelasTabContent">
                            <?php 
                            $firstKelas = true;
                            foreach ($dataKelas as $idKelas => $namaKelas) : 
                                $santriList = $santriPerKelas[$idKelas] ?? [];
                            ?>
                                <div class="tab-pane fade <?= $firstKelas ? 'show active' : '' ?>"
                                    id="kelas-<?= $idKelas ?>"
                                    role="tabpanel"
                                    aria-labelledby="tab-<?= $idKelas ?>">
                                    
                                    <?php if (empty($santriList)): ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> Tidak ada santri di kelas ini.
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-hover" id="tblSantri-<?= $idKelas ?>">
                                                <thead>
                                                    <tr>
                                                        <th>Aksi</th>
                                                        <th>Nama Santri</th>
                                                        <th>Penyerahan</th>
                                                        <th>Pengembalian</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    // Urutkan santri berdasarkan nama
                                                    usort($santriList, function($a, $b) {
                                                        $namaA = $a['NamaSantri'] ?? '';
                                                        $namaB = $b['NamaSantri'] ?? '';
                                                        return strcmp($namaA, $namaB);
                                                    });
                                                    
                                                    foreach ($santriList as $santri) : 
                                                        $statusData = $santri['StatusSerahTerima'] ?? null;
                                                        $statusText = 'Belum Diserahkan';
                                                        $badgeClass = 'badge-warning';
                                                        $penyerahanDetail = '-';
                                                        $pengembalianDetail = '-';
                                                        $hasKey = null;

                                                        if ($statusData) {
                                                            $statusText = $statusData['Status'] ?? 'Belum Diserahkan';
                                                            if ($statusText === 'Sudah Diserahkan') {
                                                                $badgeClass = 'badge-info';
                                                            } elseif ($statusText === 'Sudah Dikembalikan') {
                                                                $badgeClass = 'badge-success';
                                                            }

                                                            // Ambil semua transaksi untuk mendapatkan tanggal serah dan terima
                                                            // Query sudah include join dengan tabel guru
                                                            $serahTerimaRaporModel = new \App\Models\SerahTerimaRaporModel();
                                                            $allTransactions = $serahTerimaRaporModel->getBySantri(
                                                                $santri['IdSantri'],
                                                                $IdTahunAjaran,
                                                                $semester
                                                            );

                                                            $transaksiSerah = null;
                                                            $transaksiTerima = null;

                                                            foreach ($allTransactions as $trans) {
                                                                if ($trans['Transaksi'] === 'Serah') {
                                                                    $hasKey = $trans['HasKey'] ?? null;
                                                                    $transaksiSerah = $trans;
                                                                }
                                                                if ($trans['Transaksi'] === 'Terima') {
                                                                    $transaksiTerima = $trans;
                                                                }
                                                            }

                                                            // Format detail Penyerahan (transaksi Serah)
                                                            if ($transaksiSerah) {
                                                                // Ambil nama guru dari hasil join (sudah tersedia di data)
                                                                $namaGuru = isset($transaksiSerah['NamaGuru']) && !empty($transaksiSerah['NamaGuru']) ? toTitleCase($transaksiSerah['NamaGuru']) : '-';
                                                                
                                                                // Ambil nama wali santri
                                                                $namaWaliSantri = $transaksiSerah['NamaWaliSantri'] ?? '-';
                                                                
                                                                // Format detail penyerahan menggunakan helper function
                                                                $tanggalFormatted = formatTanggalIndonesia($transaksiSerah['TanggalTransaksi'], 'l, d F Y');
                                                                $penyerahanDetail = $tanggalFormatted . '<br>';
                                                                $penyerahanDetail .= '<small class="text-muted">Diserahkan Oleh: <strong>' . esc($namaGuru) . '</strong></small><br>';
                                                                $penyerahanDetail .= '<small class="text-muted">Diterima Oleh: <strong>' . esc($namaWaliSantri) . '</strong></small>';
                                                                
                                                                // Tambahkan link foto bukti jika ada
                                                                $fotoBukti = $transaksiSerah['FotoBukti'] ?? null;
                                                                if ($fotoBukti) {
                                                                    $fotoUrl = base_url('uploads/serah_terima_rapor/' . $fotoBukti);
                                                                    $penyerahanDetail .= '<br><a href="#" class="btn btn-sm btn-info mt-1" data-toggle="modal" data-target="#modalFotoBukti" data-foto-url="' . esc($fotoUrl, 'attr') . '">';
                                                                    $penyerahanDetail .= '<i class="fas fa-image"></i> Lihat Foto Bukti';
                                                                    $penyerahanDetail .= '</a>';
                                                                }
                                                            }

                                                            // Format detail Pengembalian (transaksi Terima)
                                                            if ($transaksiTerima) {
                                                                // Ambil nama guru dari hasil join (sudah tersedia di data)
                                                                $namaGuru = isset($transaksiTerima['NamaGuru']) && !empty($transaksiTerima['NamaGuru']) ? toTitleCase($transaksiTerima['NamaGuru']) : '-';
                                                                
                                                                // Ambil nama wali santri
                                                                $namaWaliSantri = $transaksiTerima['NamaWaliSantri'] ?? '-';
                                                                
                                                                // Format detail pengembalian menggunakan helper function
                                                                $tanggalFormatted = formatTanggalIndonesia($transaksiTerima['TanggalTransaksi'], 'l, d F Y');
                                                                $pengembalianDetail = $tanggalFormatted . '<br>';
                                                                $pengembalianDetail .= '<small class="text-muted">Dikembalikan Oleh: <strong>' . esc($namaWaliSantri) . '</strong></small><br>';
                                                                $pengembalianDetail .= '<small class="text-muted">Diterima Oleh: <strong>' . esc($namaGuru) . '</strong></small>';
                                                                
                                                                // Tambahkan link foto bukti jika ada
                                                                $fotoBukti = $transaksiTerima['FotoBukti'] ?? null;
                                                                if ($fotoBukti) {
                                                                    $fotoUrl = base_url('uploads/serah_terima_rapor/' . $fotoBukti);
                                                                    $pengembalianDetail .= '<br><a href="#" class="btn btn-sm btn-info mt-1" data-toggle="modal" data-target="#modalFotoBukti" data-foto-url="' . esc($fotoUrl, 'attr') . '">';
                                                                    $pengembalianDetail .= '<i class="fas fa-image"></i> Lihat Foto Bukti';
                                                                    $pengembalianDetail .= '</a>';
                                                                }
                                                            }
                                                        }

                                                        // Tentukan apakah bisa melakukan Serah atau Terima
                                                        $canSerah = true;
                                                        $canTerima = false;
                                                        $isLocked = false;
                                                        $buttonText = 'Serah';
                                                        $buttonClass = 'btn-success';
                                                        
                                                        if ($statusData) {
                                                            if ($statusData['Status'] === 'Sudah Diserahkan') {
                                                                $canSerah = false;
                                                                $canTerima = true;
                                                                $buttonText = 'Terima';
                                                                $buttonClass = 'btn-info';
                                                            } elseif ($statusData['Status'] === 'Sudah Dikembalikan') {
                                                                $canSerah = false;
                                                                $canTerima = false;
                                                                $isLocked = true;
                                                                $buttonText = 'Dikembalikan';
                                                                $buttonClass = 'btn-warning';
                                                            }
                                                        }

                                                        // Logic untuk tombol hapus
                                                        $targetDeleteId = null;
                                                        if ($canTerima && $transaksiSerah) {
                                                            // Jika status "Sudah Diserahkan", bisa hapus transaksi Serah
                                                            $targetDeleteId = $transaksiSerah['id'];
                                                        } elseif ($isLocked && $transaksiTerima) { 
                                                            // Jika status "Sudah Dikembalikan", bisa hapus transaksi Terima
                                                            $targetDeleteId = $transaksiTerima['id'];
                                                        }
                                                    ?>
                                                        <tr>
                                                            <td style="white-space: nowrap;">
                                                                <button class="btn btn-sm <?= $buttonClass ?> btn-proses-transaksi <?= $isLocked ? 'disabled' : '' ?>"
                                                                        data-id-santri="<?= esc($santri['IdSantri']) ?>"
                                                                        data-id-kelas="<?= esc($idKelas) ?>"
                                                                        data-nama-santri="<?= esc($santri['NamaSantri']) ?>"
                                                                        data-semester="<?= esc($semester) ?>"
                                                                        data-can-serah="<?= $canSerah ? '1' : '0' ?>"
                                                                        data-can-terima="<?= $canTerima ? '1' : '0' ?>"
                                                                        data-status="<?= esc($statusText) ?>"
                                                                        <?= $isLocked ? 'disabled' : '' ?>>
                                                                    <?php if ($isLocked): ?>
                                                                        <i class="fas fa-lock"></i> <?= esc($buttonText) ?>
                                                                    <?php else: ?>
                                                                        <i class="fas fa-exchange-alt"></i> <?= esc($buttonText) ?>
                                                                    <?php endif; ?>
                                                                </button>
                                                                <?php if ($targetDeleteId): ?>
                                                                    <button class="btn btn-sm btn-danger btn-hapus-transaksi ml-1" 
                                                                            data-id="<?= $targetDeleteId ?>"
                                                                            title="Hapus Transaksi Terakhir">
                                                                        <i class="fas fa-trash"></i> Hapus
                                                                    </button>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?= esc($santri['NamaSantri'] ?? '-') ?>
                                                                <br>
                                                                <span class="badge <?= $badgeClass ?> mt-1"><?= esc($statusText) ?></span>
                                                                <?php if (($statusText === 'Sudah Diserahkan' || $statusText === 'Sudah Dikembalikan') && $hasKey): ?>
                                                                    <br>
                                                                    <button class="btn btn-sm btn-primary btn-copy-link mt-1" 
                                                                            data-haskey="<?= esc($hasKey) ?>"
                                                                            data-santri="<?= esc($santri['NamaSantri']) ?>">
                                                                        <i class="fas fa-link"></i> Copy Link
                                                                    </button>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= $penyerahanDetail ?></td>
                                                            <td><?= $pengembalianDetail ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php 
                                $firstKelas = false;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Proses Transaksi -->
<div class="modal fade" id="modalProsesTransaksi" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proses Serah Terima Rapor</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formProsesTransaksi">
                    <input type="hidden" id="modalIdSantri" name="IdSantri">
                    <input type="hidden" id="modalIdKelas" name="IdKelas">
                    <input type="hidden" id="modalSemester" name="Semester">
                    
                    <div class="form-group">
                        <label>Nama Santri</label>
                        <input type="text" class="form-control" id="modalNamaSantri" readonly>
                    </div>

                    <div class="form-group">
                        <label>Status Saat Ini</label>
                        <input type="text" class="form-control" id="modalStatusSaatIni" readonly>
                    </div>

                    <input type="hidden" id="modalTransaksi" name="Transaksi">

                    <div class="form-group">
                        <label id="labelPilihanWaliSantri">Pilihan Wali Santri <span class="text-danger">*</span></label>
                        <select class="form-control" id="modalPilihanWaliSantri" required>
                            <option value="">Pilih Penerima</option>
                            <option value="Santri Sendiri" id="optionSantriSendiri">Santri Sendiri</option>
                            <option value="Ayah" id="optionAyah">Nama Ayah</option>
                            <option value="Ibu" id="optionIbu">Nama Ibu</option>
                            <option value="Lainya">Lainya</option>
                        </select>
                    </div>

                    <div class="form-group" id="groupNamaWaliSantri" style="display: none;">
                        <label>Nama Wali Santri <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modalNamaWaliSantri" name="NamaWaliSantri" placeholder="Ketik nama wali santri" required>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Transaksi</label>
                        <input type="date" class="form-control" id="modalTanggalTransaksi" name="TanggalTransaksi">
                        <small class="text-muted">Kosongkan untuk menggunakan tanggal saat ini</small>
                    </div>

                    <div class="form-group">
                        <label>Foto Bukti Serah Terima <small class="text-muted">(Opsional)</small></label>
                        <div class="text-center mb-2">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-primary" id="btnUploadFotoBukti">
                                    <i class="fas fa-upload"></i> Upload Foto
                                </button>
                                <button type="button" class="btn btn-success" id="btnAmbilFotoBukti">
                                    <i class="fas fa-camera"></i> Ambil Foto
                                </button>
                            </div>
                            <input type="file" id="inputFotoBuktiSerahTerima" accept=".jpg,.jpeg,.png,image/*" style="display: none;">
                        </div>
                        <small class="text-muted">Format: JPG, PNG, atau GIF. Maksimal 5MB</small>
                        <div id="previewFotoBukti" class="mt-3 text-center" style="display: none;">
                            <img id="previewFotoBuktiImg" src="" alt="Preview Foto" class="img-thumbnail" style="max-width: 300px; max-height: 300px;">
                            <br>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-danger" id="btnHapusPreviewFoto">
                                    <i class="fas fa-times"></i> Hapus Foto
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanTransaksi">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Foto Bukti -->
<div class="modal fade" id="modalFotoBukti" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto Bukti Serah Terima</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalFotoBuktiImg" src="" alt="Foto Bukti" class="img-fluid" style="max-height: 70vh;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <a id="modalFotoBuktiDownload" href="" download class="btn btn-primary">
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Copy Link -->
<div class="modal fade" id="modalCopyLink" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Copy Link Status Rapor</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Link untuk melihat status:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="linkStatus" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" id="btnCopyLink">
                                <i class="fas fa-copy"></i> Copy
                            </button>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Variabel global untuk foto bukti (di luar document.ready agar bisa diakses dari event handler)
let selectedFileFotoBukti = null;

// Fungsi untuk resize gambar
function resizeImageFile(file, maxWidth = 1200, maxHeight = 1200, quality = 0.85, maxFileSize = 5 * 1024 * 1024) {
    return new Promise((resolve, reject) => {
        if (!file.type.startsWith('image/')) {
            resolve(file);
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                let width = img.width;
                let height = img.height;

                // Hitung dimensi baru dengan mempertahankan aspect ratio
                if (width > maxWidth || height > maxHeight) {
                    const ratio = Math.min(maxWidth / width, maxHeight / height);
                    width = width * ratio;
                    height = height * ratio;
                }

                // Buat canvas untuk resize
                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                ctx.drawImage(img, 0, 0, width, height);

                // Optimasi kualitas berdasarkan ukuran file
                function optimizeQuality(currentQuality) {
                    canvas.toBlob(function(blob) {
                        if (!blob) {
                            reject(new Error('Gagal mengkonversi gambar'));
                            return;
                        }

                        if (blob.size <= maxFileSize || currentQuality <= 0.5) {
                            const resizedFile = new File([blob], file.name.replace(/\.[^/.]+$/, '') + '.jpg', {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            resolve(resizedFile);
                        } else {
                            optimizeQuality(currentQuality - 0.1);
                        }
                    }, 'image/jpeg', currentQuality);
                }

                optimizeQuality(quality);
            };
            img.onerror = function() {
                resolve(file);
            };
            img.src = e.target.result;
        };
        reader.onerror = function() {
            resolve(file);
        };
        reader.readAsDataURL(file);
    });
}

// Fungsi global untuk preview foto bukti dari input file
function previewFotoBuktiSerahTerima(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validasi tipe file
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Tipe file tidak diizinkan. Hanya JPG, PNG, atau GIF'
            });
            $(input).val('');
            return;
        }

        // Tampilkan loading
        Swal.fire({
            title: 'Memproses foto...',
            text: 'Sedang mengoptimalkan ukuran foto...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Resize gambar (max 1200x1200, max 5MB)
        resizeImageFile(file, 1200, 1200, 0.85, 5 * 1024 * 1024)
            .then(function(resizedFile) {
                Swal.close();

                // Simpan file yang sudah di-resize untuk upload nanti
                selectedFileFotoBukti = resizedFile;

                // Preview gambar
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewFotoBuktiImg').attr('src', e.target.result);
                    $('#previewFotoBukti').show();
                };
                reader.readAsDataURL(resizedFile);
            })
            .catch(function(error) {
                Swal.close();
                console.error('Error resizing image:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memproses gambar: ' + (error.message || 'Unknown error')
                });
                $(input).val('');
            });
    }
}

// Fungsi global untuk membuka kamera
function openCameraFotoBukti() {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        const videoPreview = document.createElement('video');
        videoPreview.autoplay = true;
        videoPreview.playsInline = true;
        videoPreview.style.cssText = 'max-width: 100%; max-height: 70vh; border-radius: 8px;';

        const modal = document.createElement('div');
        modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);z-index:9999;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:20px;';

        const videoContainer = document.createElement('div');
        videoContainer.style.cssText = 'position:relative;width:100%;max-width:500px;display:flex;flex-direction:column;align-items:center;';
        videoContainer.appendChild(videoPreview);

        const buttonContainer = document.createElement('div');
        buttonContainer.style.cssText = 'display:flex;flex-direction:column;align-items:center;gap:10px;margin-top:20px;width:100%;max-width:500px;';

        const switchCameraBtn = document.createElement('button');
        switchCameraBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Ganti Kamera';
        switchCameraBtn.className = 'btn btn-info';
        switchCameraBtn.style.cssText = 'width:100%;max-width:300px;';

        const captureBtn = document.createElement('button');
        captureBtn.innerHTML = '<i class="fas fa-camera"></i> Ambil Foto';
        captureBtn.className = 'btn btn-primary';
        captureBtn.style.cssText = 'width:100%;max-width:300px;';

        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '<i class="fas fa-times"></i> Tutup';
        closeBtn.className = 'btn btn-secondary';
        closeBtn.style.cssText = 'width:100%;max-width:300px;';

        buttonContainer.appendChild(switchCameraBtn);
        buttonContainer.appendChild(captureBtn);
        buttonContainer.appendChild(closeBtn);

        modal.appendChild(videoContainer);
        modal.appendChild(buttonContainer);
        document.body.appendChild(modal);

        let currentStream = null;
        let currentFacingMode = 'environment';

        function stopStream() {
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
                currentStream = null;
            }
        }

        function startCamera(facingMode) {
            stopStream();
            const constraints = {
                video: {
                    facingMode: facingMode
                }
            };

            navigator.mediaDevices.getUserMedia(constraints)
                .then(stream => {
                    currentStream = stream;
                    currentFacingMode = facingMode;
                    videoPreview.srcObject = stream;
                })
                .catch(error => {
                    console.error('Error accessing camera:', error);
                    if (facingMode === 'environment') {
                        startCamera('user');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengakses kamera. Pastikan Anda memberikan izin akses kamera.'
                        });
                        document.body.removeChild(modal);
                    }
                });
        }

        startCamera('environment');

        switchCameraBtn.onclick = () => {
            const newFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
            startCamera(newFacingMode);
        };

        captureBtn.onclick = () => {
            const canvas = document.createElement('canvas');
            canvas.width = videoPreview.videoWidth;
            canvas.height = videoPreview.videoHeight;
            canvas.getContext('2d').drawImage(videoPreview, 0, 0);

            canvas.toBlob(blob => {
                const file = new File([blob], "camera-photo.jpg", {
                    type: "image/jpeg"
                });

                stopStream();
                document.body.removeChild(modal);
                
                // Tampilkan loading
                Swal.fire({
                    title: 'Memproses foto...',
                    text: 'Sedang mengoptimalkan ukuran foto...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Resize gambar (max 1200x1200, max 5MB)
                resizeImageFile(file, 1200, 1200, 0.85, 5 * 1024 * 1024)
                    .then(function(resizedFile) {
                        Swal.close();

                        // Simpan file yang sudah di-resize untuk upload nanti
                        selectedFileFotoBukti = resizedFile;

                        // Preview gambar
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#previewFotoBuktiImg').attr('src', e.target.result);
                            $('#previewFotoBukti').show();
                        };
                        reader.readAsDataURL(resizedFile);
                    })
                    .catch(function(error) {
                        Swal.close();
                        console.error('Error resizing image:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memproses gambar: ' + (error.message || 'Unknown error')
                        });
                    });
            }, 'image/jpeg', 0.95);
        };

        closeBtn.onclick = () => {
            stopStream();
            document.body.removeChild(modal);
        };
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Browser Anda tidak mendukung akses kamera'
        });
    }
}


$(document).ready(function() {
    // Key untuk localStorage
    const storageKey = 'serahTerimaRapor_lastTabKelas_<?= $semester ?>';
    
    // Function untuk initialize DataTable
    function initDataTable(idKelas) {
        const tableId = '#tblSantri-' + idKelas;
        if ($(tableId).length === 0) {
            return;
        }
        
        if ($.fn.DataTable.isDataTable(tableId)) {
            $(tableId).DataTable().destroy();
        }
        
        // Gunakan initializeDataTableScrollX dengan options custom
        const table = initializeDataTableScrollX(tableId, [], {
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            lengthChange: true,
            order: [[1, 'asc']], // Sort by nama santri
            language: {
                "zeroRecords": "Tidak ada data yang ditemukan"
            }
        });
        
        return table;
    }
    
    // Initialize DataTables untuk setiap tabel santri
    <?php foreach ($dataKelas as $idKelas => $namaKelas): ?>
        <?php if (!empty($santriPerKelas[$idKelas])): ?>
            initDataTable('<?= $idKelas ?>');
        <?php endif; ?>
    <?php endforeach; ?>
    
    // Load pilihan tab terakhir dari localStorage
    const lastTabId = localStorage.getItem(storageKey);
    if (lastTabId) {
        const lastTab = $('#kelasTab a[href="' + lastTabId + '"]');
        if (lastTab.length > 0) {
            // Remove active dari semua tab
            $('#kelasTab .nav-link').removeClass('active').attr('aria-selected', 'false');
            $('.tab-pane').removeClass('show active');
            
            // Activate tab yang dipilih
            lastTab.addClass('active').attr('aria-selected', 'true');
            $(lastTabId).addClass('show active');
            
            // Initialize DataTable untuk tab yang aktif
            const idKelas = lastTabId.replace('#kelas-', '');
            setTimeout(function() {
                initDataTable(idKelas);
            }, 200);
        }
    } else {
        // Jika tidak ada pilihan tersimpan, aktifkan tab pertama
        const firstTab = $('#kelasTab .nav-link').first();
        if (firstTab.length > 0) {
            const firstTabId = firstTab.attr('href');
            localStorage.setItem(storageKey, firstTabId);
        }
    }
    
    // Simpan pilihan tab saat tab dipilih
    $('#kelasTab a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        const targetId = $(e.target).attr('href');
        localStorage.setItem(storageKey, targetId);
        
        // Initialize DataTable untuk tab yang baru dipilih dengan delay untuk memastikan tab sudah fully shown
        const idKelas = targetId.replace('#kelas-', '');
        setTimeout(function() {
            initDataTable(idKelas);
        }, 200);
    });

    // Set default tanggal transaksi ke hari ini
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const dateToday = `${year}-${month}-${day}`;
    $('#modalTanggalTransaksi').val(dateToday);

    // Button proses transaksi
    $(document).on('click', '.btn-proses-transaksi', function() {
        const idSantri = $(this).data('id-santri');
        const idKelas = $(this).data('id-kelas');
        const namaSantri = $(this).data('nama-santri');
        const semester = $(this).data('semester');
        const canSerah = $(this).data('can-serah') === '1';
        const canTerima = $(this).data('can-terima') === '1';
        const status = $(this).data('status');

        // Set data ke modal
        $('#modalIdSantri').val(idSantri);
        $('#modalIdKelas').val(idKelas);
        $('#modalSemester').val(semester);
        $('#modalNamaSantri').val(namaSantri);
        $('#modalStatusSaatIni').val(status);
        $('#modalNamaWaliSantri').val('');
        $('#modalPilihanWaliSantri').val('');
        $('#groupNamaWaliSantri').hide();
        $('#modalNamaWaliSantri').prop('required', false);
        
        // Reset dan tampilkan semua option terlebih dahulu
        $('#optionAyah').show().text('Nama Ayah').data('nama', '');
        $('#optionIbu').show().text('Nama Ibu').data('nama', '');

        // Tentukan transaksi otomatis berdasarkan status
        let transaksi = 'Serah';
        
        if (status === 'Sudah Diserahkan') {
            transaksi = 'Terima';
        }

        $('#modalTransaksi').val(transaksi);

        // Update label berdasarkan transaksi
        if (transaksi === 'Serah') {
            $('#labelPilihanWaliSantri').html('Pilih Yang Menerima <span class="text-danger">*</span>');
        } else {
            $('#labelPilihanWaliSantri').html('Pilih Yang Menyerahkan <span class="text-danger">*</span>');
        }
        
        // Tampilkan opsi Santri Sendiri untuk kedua transaksi (Serah dan Terima)
        $('#optionSantriSendiri').show();

        // Ambil data santri untuk nama Ayah dan Ibu
        $.ajax({
            url: '<?= base_url('backend/rapor/getSantriByKelas') ?>/' + idKelas,
            type: 'GET',
            success: function(response) {
                console.log('[Serah Terima] Response getSantriByKelas:', response);
                console.log('[Serah Terima] Response type:', typeof response);
                console.log('[Serah Terima] Response is array?', Array.isArray(response));
                console.log('[Serah Terima] Mencari santri dengan IdSantri:', idSantri);
                
                if (response && Array.isArray(response)) {
                    console.log('[Serah Terima] Total santri dalam response:', response.length);
                    console.log('[Serah Terima] Sample santri (index 0):', response[0]);
                    
                    // Coba beberapa cara pencarian
                    let santri = response.find(s => {
                        const santriId = s.IdSantri || s.idSantri || s.idsantri;
                        console.log('[Serah Terima] Comparing:', santriId, '===', idSantri, '?', santriId === idSantri);
                        return santriId === idSantri;
                    });
                    
                    // Jika tidak ditemukan, coba dengan toString
                    if (!santri) {
                        console.log('[Serah Terima] Mencoba dengan toString()');
                        santri = response.find(s => {
                            const santriId = String(s.IdSantri || s.idSantri || s.idsantri || '');
                            const searchId = String(idSantri);
                            return santriId === searchId;
                        });
                    }
                    
                    console.log('[Serah Terima] Data santri ditemukan:', santri);
                    
                    if (santri) {
                        // Coba beberapa variasi nama property
                        const namaAyah = santri.NamaAyah || santri.namaAyah || santri.nama_ayah || santri.NamaAyahSantri || '';
                        const namaIbu = santri.NamaIbu || santri.namaIbu || santri.nama_ibu || santri.NamaIbuSantri || '';
                        const statusAyah = santri.StatusAyah || santri.statusAyah || santri.status_ayah || '';
                        const statusIbu = santri.StatusIbu || santri.statusIbu || santri.status_ibu || '';
                        
                        console.log('[Serah Terima] Semua property santri:', Object.keys(santri));
                        console.log('[Serah Terima] Nama Ayah dari response (NamaAyah):', santri.NamaAyah);
                        console.log('[Serah Terima] Status Ayah dari response (StatusAyah):', statusAyah);
                        console.log('[Serah Terima] Nama Ayah final:', namaAyah);
                        console.log('[Serah Terima] Nama Ibu dari response (NamaIbu):', santri.NamaIbu);
                        console.log('[Serah Terima] Status Ibu dari response (StatusIbu):', statusIbu);
                        console.log('[Serah Terima] Nama Ibu final:', namaIbu);
                        
                        // Update option Ayah - sembunyikan jika status Meninggal
                        const isAyahMeninggal = statusAyah && statusAyah.toLowerCase().includes('meninggal');
                        if (isAyahMeninggal) {
                            console.log('[Serah Terima] Ayah dengan status Meninggal, menyembunyikan option');
                            $('#optionAyah').hide();
                        } else {
                            $('#optionAyah').show();
                            if (namaAyah) {
                                $('#optionAyah').text('Nama Ayah (' + namaAyah + ')').data('nama', namaAyah);
                                console.log('[Serah Terima] Option Ayah diupdate dengan nama:', namaAyah);
                            } else {
                                $('#optionAyah').text('Nama Ayah').data('nama', '');
                                console.log('[Serah Terima] Option Ayah diupdate tanpa nama (kosong)');
                            }
                        }
                        
                        // Update option Ibu - sembunyikan jika status Meninggal
                        const isIbuMeninggal = statusIbu && statusIbu.toLowerCase().includes('meninggal');
                        if (isIbuMeninggal) {
                            console.log('[Serah Terima] Ibu dengan status Meninggal, menyembunyikan option');
                            $('#optionIbu').hide();
                        } else {
                            $('#optionIbu').show();
                            if (namaIbu) {
                                $('#optionIbu').text('Nama Ibu (' + namaIbu + ')').data('nama', namaIbu);
                                console.log('[Serah Terima] Option Ibu diupdate dengan nama:', namaIbu);
                            } else {
                                $('#optionIbu').text('Nama Ibu').data('nama', '');
                                console.log('[Serah Terima] Option Ibu diupdate tanpa nama (kosong)');
                            }
                        }
                    } else {
                        console.log('[Serah Terima] Data santri tidak ditemukan untuk IdSantri:', idSantri);
                        console.log('[Serah Terima] Daftar semua IdSantri dalam response:', response.map(s => s.IdSantri || s.idSantri || s.idsantri));
                    }
                } else {
                    console.log('[Serah Terima] Response tidak valid atau bukan array');
                    console.log('[Serah Terima] Response value:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('[Serah Terima] Error saat mengambil data santri:', error);
                console.error('[Serah Terima] Status:', status);
                console.error('[Serah Terima] Response:', xhr.responseText);
            }
        });

        // Reset tanggal ke hari ini
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const dateToday = `${year}-${month}-${day}`;
        $('#modalTanggalTransaksi').val(dateToday);

        // Reset foto bukti
        $('#inputFotoBuktiSerahTerima').val('');
        $('#previewFotoBukti').hide();
        $('#previewFotoBuktiImg').attr('src', '');
        selectedFileFotoBukti = null;

        // Show modal
        $('#modalProsesTransaksi').modal('show');
    });

    // Pilihan wali santri handler
    $('#modalPilihanWaliSantri').on('change', function() {
        const pilihan = $(this).val();
        
        if (pilihan === 'Santri Sendiri') {
            // Jika dipilih "Santri Sendiri", isi nama santri otomatis dan tampilkan input (bisa diedit)
            const namaSantri = $('#modalNamaSantri').val();
            $('#modalNamaWaliSantri').val(namaSantri || '');
            $('#groupNamaWaliSantri').show();
            $('#modalNamaWaliSantri').prop('required', true);
        } else if (pilihan === 'Lainya') {
            // Tampilkan input manual, kosongkan
            console.log('[Serah Terima] Pilihan: Lainya - Input manual');
            $('#groupNamaWaliSantri').show();
            $('#modalNamaWaliSantri').val('').prop('required', true);
        } else if (pilihan === 'Ayah') {
            // Ambil nama Ayah dari data, jika ada isi otomatis, jika tidak biarkan kosong
            const namaAyah = $('#optionAyah').data('nama') || '';
            console.log('[Serah Terima] Pilihan: Ayah');
            console.log('[Serah Terima] Nama Ayah dari data:', namaAyah);
            console.log('[Serah Terima] Nama Ayah ada?', namaAyah ? 'Ya' : 'Tidak');
            
            if (namaAyah) {
                console.log('[Serah Terima] Mengisi nama Ayah otomatis:', namaAyah);
                $('#modalNamaWaliSantri').val(namaAyah);
            } else {
                console.log('[Serah Terima] Nama Ayah tidak ada, biarkan kosong untuk diisi manual');
                $('#modalNamaWaliSantri').val('');
            }
            
            $('#groupNamaWaliSantri').show();
            $('#modalNamaWaliSantri').prop('required', true);
        } else if (pilihan === 'Ibu') {
            // Ambil nama Ibu dari data, jika ada isi otomatis, jika tidak biarkan kosong
            const namaIbu = $('#optionIbu').data('nama') || '';
            console.log('[Serah Terima] Pilihan: Ibu');
            console.log('[Serah Terima] Nama Ibu dari data:', namaIbu);
            console.log('[Serah Terima] Nama Ibu ada?', namaIbu ? 'Ya' : 'Tidak');
            
            if (namaIbu) {
                console.log('[Serah Terima] Mengisi nama Ibu otomatis:', namaIbu);
                $('#modalNamaWaliSantri').val(namaIbu);
            } else {
                console.log('[Serah Terima] Nama Ibu tidak ada, biarkan kosong untuk diisi manual');
                $('#modalNamaWaliSantri').val('');
            }
            
            $('#groupNamaWaliSantri').show();
            $('#modalNamaWaliSantri').prop('required', true);
        } else {
            // Kosongkan dan sembunyikan
            console.log('[Serah Terima] Pilihan dikosongkan');
            $('#modalNamaWaliSantri').val('');
            $('#groupNamaWaliSantri').hide();
            $('#modalNamaWaliSantri').prop('required', false);
        }
    });

    // Event handler untuk tombol upload foto
    $('#btnUploadFotoBukti').on('click', function() {
        $('#inputFotoBuktiSerahTerima').click();
    });

    // Event handler untuk tombol ambil foto
    $('#btnAmbilFotoBukti').on('click', function() {
        openCameraFotoBukti();
    });

    // Event handler untuk input file
    $('#inputFotoBuktiSerahTerima').on('change', function() {
        previewFotoBuktiSerahTerima(this);
    });


    // Hapus preview foto
    $('#btnHapusPreviewFoto').on('click', function() {
        $('#inputFotoBuktiSerahTerima').val('');
        $('#previewFotoBukti').hide();
        $('#previewFotoBuktiImg').attr('src', '');
        selectedFileFotoBukti = null;
    });

    // Modal foto bukti - tampilkan foto saat modal dibuka
    $('#modalFotoBukti').on('show.bs.modal', function(e) {
        const button = $(e.relatedTarget);
        const fotoUrl = button.data('foto-url');
        if (fotoUrl) {
            $('#modalFotoBuktiImg').attr('src', fotoUrl);
            $('#modalFotoBuktiDownload').attr('href', fotoUrl);
        }
    });

    // Button simpan transaksi
    $('#btnSimpanTransaksi').on('click', function() {
        let tanggalTransaksi = $('#modalTanggalTransaksi').val();
        
        // Konversi date ke format yang diharapkan backend (Y-m-d H:i:s)
        // Jika ada tanggal, tambahkan waktu default (00:00:00) karena detail jam ada di created_at
        if (tanggalTransaksi) {
            // date format: YYYY-MM-DD
            // Convert to: YYYY-MM-DD 00:00:00
            tanggalTransaksi = tanggalTransaksi + ' 00:00:00';
        }
        
        const pilihanWaliSantri = $('#modalPilihanWaliSantri').val();
        const transaksi = $('#modalTransaksi').val();
        
        // Gunakan FormData untuk support file upload
        const formData = new FormData();
        formData.append('IdSantri', $('#modalIdSantri').val());
        formData.append('IdKelas', $('#modalIdKelas').val());
        formData.append('Semester', $('#modalSemester').val());
        formData.append('NamaWaliSantri', $('#modalNamaWaliSantri').val());
        formData.append('TanggalTransaksi', tanggalTransaksi || '');

        // Tambahkan file foto jika ada (gunakan selectedFileFotoBukti jika ada, jika tidak gunakan dari input)
        let fotoFile = selectedFileFotoBukti;
        if (!fotoFile) {
            fotoFile = $('#inputFotoBuktiSerahTerima')[0].files[0];
        }
        if (fotoFile) {
            formData.append('FotoBukti', fotoFile);
        }

        // Validation - Transaksi tidak perlu karena ditentukan otomatis di controller
        const idSantri = formData.get('IdSantri');
        const idKelas = formData.get('IdKelas');
        const semester = formData.get('Semester');
        const namaWaliSantri = formData.get('NamaWaliSantri');
        
        // Validasi pilihan wali santri
        if (!pilihanWaliSantri) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Field "Pilih Yang ' + (transaksi === 'Serah' ? 'Menerima' : 'Menyerahkan') + '" harus diisi!'
            });
            return;
        }
        
        // Jika pilihan bukan "Santri Sendiri", pastikan nama wali santri terisi
        if (pilihanWaliSantri !== 'Santri Sendiri' && !namaWaliSantri) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Nama Wali Santri harus diisi!'
            });
            return;
        }
        
        // Jika pilihan "Santri Sendiri", pastikan nama santri terisi
        if (pilihanWaliSantri === 'Santri Sendiri') {
            const namaSantri = $('#modalNamaSantri').val();
            if (!namaSantri) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Nama santri tidak ditemukan!'
                });
                return;
            }
            // Nama sudah terisi di input (bisa diedit)
        }
        
        if (!idSantri || !idKelas || !semester) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Semua field yang wajib harus diisi!'
            });
            return;
        }

        // Tampilkan popup progress
        Swal.fire({
            title: 'Menyimpan Data...',
            html: 'Mohon tunggu, data sedang diproses',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('backend/rapor/saveSerahTerima') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Tutup popup progress
                Swal.close();
                
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Reload halaman untuk refresh data
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                // Tutup popup progress
                Swal.close();
                
                let errorMsg = 'Terjadi kesalahan saat menyimpan data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg
                });
            }
        });
    });

    // Button copy link
    // Modal foto bukti - tampilkan foto saat modal dibuka
    $('#modalFotoBukti').on('show.bs.modal', function(e) {
        const button = $(e.relatedTarget);
        const fotoUrl = button.data('foto-url');
        if (fotoUrl) {
            $('#modalFotoBuktiImg').attr('src', fotoUrl);
            $('#modalFotoBuktiDownload').attr('href', fotoUrl);
        }
    });

    $(document).on('click', '.btn-copy-link', function() {
        const hasKey = $(this).data('haskey');
        const namaSantri = $(this).data('santri');
        const link = '<?= base_url('cek-status-rapor') ?>/' + hasKey;
        $('#linkStatus').val(link);
        $('#modalCopyLink').modal('show');
    });

    // Copy to clipboard
    $('#btnCopyLink').on('click', function() {
        const linkInput = $('#linkStatus');
        linkInput.select();
        document.execCommand('copy');
        
        // Show feedback
        const originalText = $(this).html();
        $(this).html('<i class="fas fa-check"></i> Copied!');
        setTimeout(() => {
            $(this).html(originalText);
        }, 2000);
    });
    // Button hapus transaksi
    $(document).on('click', '.btn-hapus-transaksi', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Transaksi?',
            text: "Apakah Anda yakin ingin menghapus status transaksi terakhir ini? Status akan kembali ke tahap sebelumnya.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('backend/rapor/deleteSerahTerima') ?>',
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan sistem'
                        });
                    }
                });
            }
        });
    });

});
</script>


<?= $this->endSection() ?>
