<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="card card-purple card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-certificate mr-1"></i>
                    Panduan Pembuatan Sertifikat
                </h3>
            </div>
            <div class="card-body">
                <p>Fitur ini memungkinkan Admin untuk membuat template sertifikat kustom dan mencetaknya secara otomatis dengan data pemenang.</p>
                
                <hr>

                <h4>1. Siapkan Template Gambar</h4>
                <ul>
                    <li>Siapkan desain sertifikat kosong (tanpa nama/juara) dalam format gambar (JPG/PNG).</li>
                    <li>Pastikan resolusi cukup tinggi (misal: A4 @ 300dpi).</li>
                </ul>

                <h4>2. Upload & Atur Template</h4>
                <ul>
                    <li>Masuk ke menu <strong>Perlombaan -> Template Sertifikat</strong> (atau via tombol di baris Cabang).</li>
                    <li><strong>Upload Gambar</strong> background sertifikat.</li>
                    <li><strong>Konfigurasi Field (Posisi Teks)</strong>:
                        <ul>
                            <li>Tentukan posisi X dan Y (koordinat pixel) untuk setiap data (Nama Peserta, Nomor, Juara, dll).</li>
                            <li>Atur Ukuran Font, Warna (HEX), dan Jenis Font.</li>
                            <li>Gunakan fitur <em>Preview</em> untuk melihat hasil sementara.</li>
                        </ul>
                    </li>
                </ul>

                <h4>3. Generate Sertifikat</h4>
                <ul>
                    <li>Masuk ke halaman <strong>Peringkat</strong> pada Cabang Lomba.</li>
                    <li>Di tabel peringkat, akan muncul tombol <strong>Cetak Sertifikat</strong> di sebelah kanan setiap juara.</li>
                    <li>Klik cetak untuk mengunduh PDF sertifikat yang sudah terisi data.</li>
                    <li>Bisa juga menggunakan tombol <strong>Cetak Semua</strong> untuk mendownload arsip (ZIP) seluruh sertifikat.</li>
                </ul>

                <hr>
                
                <h5>Alur Pembuatan Sertifikat</h5>
                <div class="mermaid">
flowchart TD
    A[Desain Sertifikat JPG/PNG] --> B[Upload Template ke Sistem]
    B --> C{Konfigurasi Tata Letak}
    C --> D[Atur Posisi Nama]
    C --> E[Atur Posisi Juara/Kategori]
    C --> F[Preview Hasil]
    F -- Belum Pas --> C
    F -- Pas --> G[Simpan Template]
    
    subgraph Cetak
    H[Buka Halaman Peringkat] --> I[Klik Cetak]
    G --> I
    I --> J[Sistem Generate PDF]
    J --> K[Download File]
    end
                </div>

                <!-- Card: Technical Info -->
                <div class="card collapsed-card mt-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-code mr-2"></i>Informasi Teknis (Untuk Developer)</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Expand">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="callout callout-info" style="border-left-color: #17a2b8;">
                                    <h5>Backend Components</h5>
                                    <p>Controller: <code>Backend/Perlombaan.php</code></p>
                                    <ul class="text-sm">
                                        <li><code>templateSertifikat()</code> - Manage Template</li>
                                        <li><code>configureFields()</code> - X,Y Coordinates</li>
                                        <li><code>downloadSertifikat()</code> - PDF Generation</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="callout callout-warning" style="border-left-color: #ffc107;">
                                    <h5>Database & Model</h5>
                                    <p>Models:</p>
                                    <ul class="text-sm">
                                        <li><code>LombaSertifikatTemplateModel</code></li>
                                        <li><code>LombaSertifikatFieldModel</code></li>
                                    </ul>
                                    <p>Tables:</p>
                                    <ul class="text-sm">
                                        <li><code>tbl_lomba_sertifikat_template</code></li>
                                        <li><code>tbl_lomba_sertifikat_field</code></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <a href="<?= base_url('backend/dokumentasi/perlombaan/juri') ?>" class="btn btn-default"><i class="fas fa-arrow-left"></i> Kembali: Halaman Juri</a>
                <a href="<?= base_url('backend/dokumentasi/perlombaan') ?>" class="btn btn-default float-right">Ke Menu Utama <i class="fas fa-home"></i></a>
            </div>
        </div>
    </div>
</section>


<?= $this->endSection(); ?>
