<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-play-circle mr-1"></i>
                    Fase 2 & 3: Pendaftaran dan Pelaksanaan
                </h3>
            </div>
            <div class="card-body">
                
                <h3>Fase 2: Pendaftaran (Oleh Operator/Admin)</h3>
                <p>Setelah setup selesai, Operator TPQ dapat mendaftarkan santrinya.</p>
                <ol>
                    <li>Masuk ke <strong>Perlombaan -> Pendaftaran</strong>.</li>
                    <li>Pilih <strong>Cabang Lomba</strong>.</li>
                    <li>Sistem menampilkan daftar santri yang <strong>Memenuhi Syarat</strong> (Usia/Kelas/Gender).</li>
                    <li>Centang santri yang ingin didaftarkan, lalu klik <strong>Daftarkan</strong>.</li>
                    <li>Untuk <strong>Lomba Kelompok</strong>:
                        <ul>
                            <li>Pilih beberapa santri sekaligus (sesuai jumlah anggota tim).</li>
                            <li>Klik "Daftar sebagai Grup".</li>
                        </ul>
                    </li>
                </ol>

                <hr>

                <h3>Fase 3: Pelaksanaan (Hari H)</h3>
                
                <h4>1. Pengundian (Re-gistrasi)</h4>
                <p>Peserta yang hadir harus melakukan daftar ulang untuk mendapat Nomor Undian.</p>
                <ul>
                    <li>Admin masuk ke <strong>Perlombaan -> Pengundian</strong>.</li>
                    <li>Cari nama peserta.</li>
                    <li>Input <strong>Nomor Undian</strong> (Nomor Tampil).</li>
                    <li><em>Catatan: Juri hanya melihat peserta yang SUDAH memiliki Nomor Undian.</em></li>
                </ul>

                <h4>2. Penilaian (Oleh Juri)</h4>
                <p>Juri melakukan penilaian secara digital.</p>
                <ul>
                    <li>Juri login menggunakan username/password yang dibuat di Fase Setup.</li>
                    <li>Juri memilih Cabang Lomba.</li>
                    <li>Akan muncul daftar peserta (Nomor Undian).</li>
                    <li>Klik <strong>Nilai</strong>, input skor per kriteria, lalu Simpan.</li>
                    <li>Jika sudah yakin, Juri dapat <strong>Mengunci Nilai</strong> agar tidak bisa diubah lagi.</li>
                </ul>

                <hr>

                <h3>Fase 4: Finalisasi</h3>
                <ol>
                    <li>Admin memantau nilai masuk di menu <strong>Perlombaan -> Monitor Nilai</strong>.</li>
                    <li>Setelah selesai, buka menu <strong>Peringkat</strong>.</li>
                    <li>Sistem otomatis mengurutkan Juara berdasarkan Total Nilai.</li>
                    <li><strong>Cetak Sertifikat</strong> langsung dari halaman Peringkat.</li>
                </ol>

                <hr>
                <h5>Diagram Alur Pelaksanaan</h5>
                <div class="mermaid">
graph TD
    subgraph Pendaftaran
    A[Pilih Cabang] --> B[Filter Santri Layak]
    B --> C[Daftarkan Peserta]
    end

    subgraph Hari_H
    C --> D[Peserta Hadir]
    D --> E[Pengundian / Daftar Ulang]
    E --> F[Dapat Nomor Tampil]
    F --> G[Juri Login]
    G --> H[Input Nilai per Kriteria]
    H --> I[Kunci Nilai]
    end

    I --> J[Peringkat & Sertifikat]
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
                                        <li>Logic Pendaftaran (Registrasi Peserta)</li>
                                        <li>Logic Pengundian (Nomor Tampil)</li>
                                        <li>Logic Penilaian (Score Input)</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="callout callout-warning" style="border-left-color: #ffc107;">
                                    <h5>Database & Model</h5>
                                    <p>Primary Models:</p>
                                    <ul class="text-sm">
                                        <li><code>LombaRegistrasiModel</code> (<code>tbl_lomba_registrasi</code>)</li>
                                        <li><code>LombaRegistrasiAnggotaModel</code> (Detail Anggota Tim)</li>
                                        <li><code>LombaNilaiModel</code> (<code>tbl_lomba_nilai</code>)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="card-footer">
                <a href="<?= base_url('backend/dokumentasi/perlombaan/setup') ?>" class="btn btn-default"><i class="fas fa-arrow-left"></i> Kembali: Setup</a>
                <a href="<?= base_url('backend/dokumentasi/perlombaan/juri') ?>" class="btn btn-primary float-right">Lanjut: Halaman Juri <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>


<?= $this->endSection(); ?>
