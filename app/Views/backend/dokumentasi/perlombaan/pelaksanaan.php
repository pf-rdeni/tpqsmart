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


            </div>
            <div class="card-footer">
                <a href="<?= base_url('backend/dokumentasi/perlombaan/setup') ?>" class="btn btn-default"><i class="fas fa-arrow-left"></i> Kembali: Setup</a>
                <a href="<?= base_url('backend/dokumentasi/perlombaan/juri') ?>" class="btn btn-primary float-right">Lanjut: Halaman Juri <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

<script type="module">
    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
    mermaid.initialize({ startOnLoad: true });
</script>
<?= $this->endSection(); ?>
