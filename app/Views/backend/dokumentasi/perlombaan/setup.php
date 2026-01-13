<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cogs mr-1"></i>
                    Fase 1: Persiapan (Setup)
                </h3>
            </div>
            <div class="card-body">
                <p>Tahap ini dilakukan oleh <strong>Admin</strong> (atau Operator Pusat) untuk menyiapkan kerangka perlombaan sebelum pendaftaran dibuka.</p>
                
                <hr>

                <h4>1. Buat Lomba (Event)</h4>
                <ul>
                    <li>Masuk ke menu <strong>Perlombaan -> Daftar Lomba</strong>.</li>
                    <li>Klik <strong>Tambah</strong>.</li>
                    <li>Isi Nama Lomba (misal: "Porsadin 2024"), Tanggal Pelaksanaan, dan Lokasi.</li>
                    <li>Pastikan status <strong>Aktif</strong> agar muncul di menu operator.</li>
                </ul>

                <h4>2. Buat Cabang Lomba</h4>
                <ul>
                    <li>Di halaman detail Lomba, masuk ke tab <strong>Cabang</strong>.</li>
                    <li>Klik <strong>Tambah Cabang</strong>.</li>
                    <li>Isi parameter penting:
                        <ul>
                            <li><strong>Nama Cabang</strong>: Misal "Tahfidz Juz 30".</li>
                            <li><strong>Tipe Peserta</strong>: 
                                <ul>
                                    <li><em>Individu</em>: Satu santri per pendaftaran.</li>
                                    <li><em>Kelompok</em>: Grup santri (misal: Cerdas Cermat).</li>
                                </ul>
                            </li>
                            <li><strong>Kategori Gender</strong>: Putra/Putri/Campuran.</li>
                            <li><strong>Kuota</strong>: Batasi jumlah peserta per TPQ (opsional).</li>
                        </ul>
                    </li>
                </ul>

                <h4>3. Atur Kriteria Penilaian</h4>
                <ul>
                    <li>Setiap cabang <strong>WAJIB</strong> memiliki kriteria penilaian agar Juri bisa memberi nilai.</li>
                    <li>Klik tombol <strong>Kriteria</strong> pada baris Cabang.</li>
                    <li>Tambahkan aspek penilaian, contoh untuk Tahfidz:
                        <ul>
                            <li>Tajwid (Bobot: 40)</li>
                            <li>Fashohah (Bobot: 30)</li>
                            <li>Kelancaran (Bobot: 30)</li>
                        </ul>
                    </li>
                    <li>Total bobot idealnya 100, namun sistem mendukung skor murni (tanpa bobot persentase).</li>
                </ul>

                <h4>4. Atur Juri</h4>
                <ul>
                    <li>Klik tombol <strong>Juri</strong> pada baris Cabang.</li>
                    <li>Tambahkan Juri Baru.</li>
                    <li>Sistem akan meminta <strong>Nama Juri</strong> dan otomatis membuatkan:
                        <ul>
                            <li><strong>Username</strong>: Format <code>Juri[Cabang]_[Urutan]</code>.</li>
                            <li><strong>Password</strong>: Default yang ditentukan saat pembuatan.</li>
                        </ul>
                    </li>
                    <li>Berikan kredensial ini kepada Juri untuk login saat pelaksanaan.</li>
                </ul>

                <hr>
                <h5>Diagram Alur Setup</h5>
                <div class="mermaid">
graph LR
    A[Start: Daftar Lomba] --> B[Buat Lomba Baru]
    B --> C[Detail Lomba]
    C --> D[Tambah Cabang]
    D --> E{Konfigurasi Cabang}
    E --> F[Kriteria Penilaian]
    E --> G[Akun Juri]
    E --> H[Sertifikat Template]
    F --> I[Selesai]
    G --> I
    H --> I
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
                                        <li><code>storeLomba()</code> - Create Event</li>
                                        <li><code>setCabang()</code>, <code>storeCabang()</code></li>
                                        <li><code>setKriteria()</code>, <code>storeKriteria()</code></li>
                                        <li><code>setJuri()</code>, <code>storeJuri()</code> - Auto-create User</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="callout callout-warning" style="border-left-color: #ffc107;">
                                    <h5>Database & Model</h5>
                                    <p>Tables (Hierarchy):</p>
                                    <ul class="text-sm">
                                        <li><code>tbl_lomba_master</code> (Event)</li>
                                        <li>-> <code>tbl_lomba_cabang</code> (Category)</li>
                                        <li>--> <code>tbl_lomba_kriteria</code> (Scoring Rules)</li>
                                        <li>--> <code>tbl_lomba_juri</code> (Linked to <code>users</code>)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <a href="<?= base_url('backend/dokumentasi/perlombaan') ?>" class="btn btn-default">Kembali</a>
                <a href="<?= base_url('backend/dokumentasi/perlombaan/pelaksanaan') ?>" class="btn btn-primary float-right">Lanjut: Pelaksanaan <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>


<?= $this->endSection(); ?>
