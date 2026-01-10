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

            </div>
            <div class="card-footer">
                <a href="<?= base_url('backend/dokumentasi/perlombaan/juri') ?>" class="btn btn-default"><i class="fas fa-arrow-left"></i> Kembali: Halaman Juri</a>
                <a href="<?= base_url('backend/dokumentasi/perlombaan') ?>" class="btn btn-default float-right">Ke Menu Utama <i class="fas fa-home"></i></a>
            </div>
        </div>
    </div>
</section>

<script type="module">
    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
    mermaid.initialize({ startOnLoad: true });
</script>
<?= $this->endSection(); ?>
