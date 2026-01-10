<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt mr-1"></i>
                            Dokumentasi Alur Proses Membuat Absensi
                        </h3>
                    </div>
                    <div class="card-body">
                        <h1>Alur Proses Membuat Absensi (Creating Attendance Flow)</h1>
                        <p class="lead">Dokumen ini menjelaskan alur teknis dan operasional untuk membuat Kegiatan Absensi baru di panel admin.</p>
                        
                        <hr>

                        <h3>1. Akses Menu</h3>
                        <p>Admin atau Operator mengakses menu:</p>
                        <div class="alert alert-info">
                            <i class="fas fa-bars"></i> <strong>Guru -> Absensi Guru -> Tambah Kegiatan</strong>
                        </div>

                        <h3>2. Pengisian Form Utama</h3>
                        <p>Pengguna mengisi informasi dasar kegiatan:</p>
                        <ol>
                            <li><strong>Nama Kegiatan</strong>: Judul kegiatan (contoh: "Rapat Bulanan").</li>
                            <li><strong>Tempat</strong>: Lokasi kegiatan (contoh: "Aula Utama").</li>
                            <li><strong>Waktu</strong>:
                                <ul>
                                    <li><em>Tanggal</em>: Tanggal pelaksanaan (atau tanggal mulai jika rutin).</li>
                                    <li><em>Jam Mulai & Jam Selesai</em>: Rentang waktu aktifnya kegiatan.</li>
                                </ul>
                            </li>
                            <li><strong>Lingkup (Peserta)</strong>:
                                <ul>
                                    <li><strong>Umum</strong>: Untuk semua guru di seluruh lembaga (Hanya Admin).</li>
                                    <li><strong>TPQ</strong>: Spesifik untuk guru di TPQ tertentu (Otomatis terpilih untuk Operator/Guru).</li>
                                </ul>
                            </li>
                        </ol>

                        <h3>3. Penentuan Jadwal (Recurrence)</h3>
                        <p>Sistem mendukung dua jenis jadwal:</p>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="callout callout-success">
                                    <h5>A. Sekali Saja (One-time)</h5>
                                    <p>Kegiatan hanya terjadi satu kali pada tanggal yang dipilih.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="callout callout-warning">
                                    <h5>B. Rutin (Berulang)</h5>
                                    <p>Kegiatan berulang sesuai pola yang ditentukan. Opsi pola:</p>
                                    <ul>
                                        <li><strong>Harian</strong>: Setiap <code>n</code> hari atau Setiap Hari Kerja (Senin-Jumat).</li>
                                        <li><strong>Mingguan</strong>: Setiap <code>n</code> minggu. Pilih Hari: Senin, Selasa, dst.</li>
                                        <li><strong>Bulanan</strong>: Berdasarkan Tanggal (misal: tgl 10) atau Pola Hari (misal: senin pertama).</li>
                                        <li><strong>Tahunan</strong>: Berdasarkan Tanggal atau Pola Hari.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <h4>C. Batas Akhir (End Condition)</h4>
                        <p>Kegiatan rutin bisa berhenti berdasarkan:</p>
                        <ul>
                            <li><strong>Tanpa Batas</strong>: Berjalan selamanya.</li>
                            <li><strong>Kejadian</strong>: Berhenti setelah <code>n</code> kali pelaksanaan.</li>
                            <li><strong>Tanggal</strong>: Berhenti pada tanggal tertentu.</li>
                        </ul>

                        <hr>

                        <h3>4. Proses Simpan (Backend)</h3>
                        <p>Saat tombol "Simpan" ditekan, alur di backend (<code>KegiatanAbsensi::create</code>):</p>
                        <ol>
                            <li><strong>Validasi Input</strong>: Cek kelengkapan data wajib.</li>
                            <li><strong>Penanganan Lingkup</strong>: Jika User = Operator/Guru, paksa lingkup = TPQ user tersebut.</li>
                            <li><strong>Generate Token</strong>: Sistem membuat token acak unik (<code>bin2hex</code>).</li>
                            <li><strong>Simpan Database</strong>: Data disimpan ke <code>tbl_kegiatan_absensi</code>. Termasuk parameter <code>JenisJadwal</code>, <code>Interval</code>, <code>OpsiPola</code>, dll untuk logika <em>recurrence</em>.</li>
                        </ol>

                        <hr>

                        <h3>Diagram Alur (Flowchart)</h3>
                        <div class="mermaid">
graph TD
    A[Start: Akses Menu Tambah] --> B[Isi Form Dasar]
    B --> C{Jenis Jadwal?}
    C -- Sekali --> D[Set Tanggal Tunggal]
    C -- Rutin --> E[Pilih Pola: Harian/Mingguan/Bulanan]
    E --> F[Set Interval & Batas Akhir]
    D --> G[Pilih Lingkup Peserta]
    F --> G
    G --> H[Klik Simpan]
    H --> I{Validasi Server}
    I -- Gagal --> J[Tampilkan Error]
    J --> B
    I -- Sukses --> K[Generate Token Unik]
    K --> L[Simpan ke DB]
    L --> M[Selesai]
                        </div>

                        <hr>

                        <div class="alert alert-secondary">
                            <h5><i class="icon fas fa-code"></i> Ringkasan Teknis</h5>
                            <ul>
                                <li><strong>Controller</strong>: <code>app/Controllers/Backend/KegiatanAbsensi.php</code> (<code>new()</code>, <code>create()</code>)</li>
                                <li><strong>View</strong>: <code>app/Views/backend/absensiGuru/form.php</code></li>
                                <li><strong>Database</strong>: <code>tbl_kegiatan_absensi</code></li>
                            </ul>
                        </div>

                    </div>
                    <div class="card-footer">
                        <a href="<?= base_url('backend/dokumentasi/absensi-guru') ?>" class="btn btn-default"><i class="fas fa-arrow-left"></i> Kembali ke Alur Absensi</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Include Mermaid JS -->
<script type="module">
    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
    mermaid.initialize({ startOnLoad: true });
</script>
<?= $this->endSection(); ?>
