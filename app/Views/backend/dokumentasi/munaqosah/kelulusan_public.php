<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-globe mr-1"></i>
                    Akses Publik: Cek Kelulusan
                </h3>
            </div>
            <div class="card-body">
                <p>Fitur ini memungkinkan santri atau wali santri untuk memeriksa status kelulusan dan nilai transkrip secara mandiri tanpa harus login.</p>
                
                <hr>

                <h4>1. Kunci Akses (Key)</h4>
                <ul>
                    <li>Setiap Kartu Ujian peserta memiliki <strong>QR Code</strong> dan <strong>Kode Unik</strong>.</li>
                    <li>Kode ini bersifat rahasia dan hanya dimiliki oleh peserta yang bersangkutan.</li>
                    <li>Pastikan kartu ujian tidak hilang sampai pengumuman kelulusan.</li>
                </ul>

                <h4>2. Cara Cek Kelulusan</h4>
                <ol>
                    <li>Buka halaman publik TPQ Smart (biasanya di halaman depan).</li>
                    <li>Pilih menu <strong>Cek Kelulusan Munaqosah</strong>.</li>
                    <li>Masukkan <strong>Token / Kode Unik (HashKey)</strong> yang tertera di Kartu Ujian (atau scan QR Code jika menggunakan HP).</li>
                    <li>Klik tombol <strong>Cek Hasil</strong>.</li>
                </ol>

                <h4>3. Validasi Data Santri</h4>
                <ul>
                    <li>Sebelum bisa melihat kelulusan, <strong>Wali Santri wajib memverifikasi data</strong> yang tampil.</li>
                    <li>Jika data benar, klik <strong>"Data Sudah Benar/Valid"</strong>.</li>
                    <li>Jika ada kesalahan (misal: Typo nama, Tanggal Lahir salah), pilih <strong>"Ajukan Perbaikan"</strong> dan tulis catatan perbaikannya. Admin akan menerima notifikasi perbaikan ini.</li>
                </ul>

                <h4>4. Menu Pilihan: Status Proses & Kelulusan</h4>
                <p>Setelah data tervalidasi, Anda akan diminta memilih <strong>Jenis Ujian</strong> (jika peserta mengikuti lebih dari satu jenis ujian, misalnya: Pra-Munaqosah dan Munaqosah). Setelah memilih jenis ujian, tersedia dua menu:</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="callout callout-info">
                            <h5>A. Cek Status Proses</h5>
                            <p>Digunakan untuk memantau progress ujian secara real-time.</p>
                            <ul>
                                <li>Melihat materi apa saja yang sudah diujikan.</li>
                                <li>Melihat jumlah nilai yang sudah masuk dari juri.</li>
                                <li>Status "Selesai" per grup materi.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-success">
                            <h5>B. Lihat Hasil Kelulusan</h5>
                            <p>Digunakan untuk melihat hasil akhir ujian.</p>
                            <ul>
                                <li>Memilih Jenis Ujian: <strong>Pra-Munaqosah</strong> (Internal) atau <strong>Munaqosah</strong> (Umum).</li>
                                <li>Status LULUS / TIDAK LULUS.</li>
                                <li>Detail nilai dan predikat.</li>
                                <li>Download Surat Keterangan Kelulusan (SKK).</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <h4>5. Surat Keterangan Kelulusan</h4>
                <p>Jika santri dinyatakan <strong>LULUS</strong>:</p>
                <ul>
                    <li>Sistem otomatis men-generate <strong>Surat Keterangan Kelulusan (PDF)</strong>.</li>
                    <li><strong>Logika Kop Surat:</strong>
                        <ul>
                            <li><strong>Munaqosah Umum:</strong> Menggunakan Kop & Tanda Tangan <strong>FKPQ</strong>.</li>
                            <li><strong>Pra-Munaqosah:</strong> Menggunakan Kop & Tanda Tangan <strong>TPQ</strong> (atau MDA jika kelas sesuai).</li>
                        </ul>
                    </li>
                    <li>Surat dilengkapi <strong>QR Code Digital Signature</strong> (Tanda Tangan Elektronik) yang valid.</li>
                </ul>

                <hr>
                <h5>Diagram Alur Akses Santri</h5>
                <div class="mermaid">
flowchart TD
    A[Input Token / Scan QR] --> B{Validasi Data}
    B -- Data Salah --> C[Usulan Perbaikan]
    B -- Data Valid --> D{Pilih Jenis Ujian}
    
    D -- Pra-Munaqosah --> E{Pilih Menu}
    D -- Munaqosah --> E
    
    E -- Status Proses --> F[Tabel Progress Nilai]
    F --> E
    
    E -- Cek Kelulusan --> G{Status Akhir?}
    G -- Belum Lulus --> H[Info Remedial]
    G -- Lulus --> I[Download Surat SKK]
    
    I --> J[Auto-Select Kop FKPQ/TPQ]
                </div>

            </div>
            <div class="card-footer">
                <a href="<?= base_url('backend/dokumentasi/munaqosah/penilaian') ?>" class="btn btn-default"><i class="fas fa-arrow-left"></i> Kembali: Penilaian</a>
                <a href="<?= base_url('backend/dokumentasi/munaqosah') ?>" class="btn btn-default float-right">Ke Menu Utama <i class="fas fa-home"></i></a>
            </div>
        </div>
    </div>
</section>

<script type="module">
    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
    mermaid.initialize({ startOnLoad: true });
</script>
<?= $this->endSection(); ?>
