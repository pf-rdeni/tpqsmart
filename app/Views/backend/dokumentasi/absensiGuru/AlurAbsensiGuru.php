<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Alur Proses Absensi Guru (Teacher Attendance Flow)</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <p>Dokumen ini menjelaskan alur teknis dan operasional dari sistem absensi guru di aplikasi TPQ Smart.</p>

                <h4 class="mt-4">1. Persiapan Kegiatan (Admin/Operator)</h4>
                <p>Sebelum absensi dapat dilakukan, kegiatan harus dibuat terlebih dahulu di panel admin.</p>
                <ol>
                    <li>
                        <strong>Buat Kegiatan</strong>: Admin membuat kegiatan baru di menu "Kegiatan Absensi".
                        <ul>
                            <li>Input: Nama Kegiatan, Jenis Jadwal (Sekali, Harian, Mingguan, Bulanan, Tahunan), Jam Mulai, Jam Selesai, Lingkup (Semua Guru / TPQ Tertentu), dan Opsi Pola lainnya.</li>
                        </ul>
                    </li>
                    <li><strong>Generate Token</strong>: Sistem secara otomatis membuat <code>Token</code> unik untuk kegiatan tersebut.</li>
                    <li><strong>Share Link</strong>: Admin membagikan link absensi (yang berisi token) kepada guru melalui WhatsApp atau media lain.</li>
                </ol>

                <h4 class="mt-4">2. Akses Halaman Absensi (Guru)</h4>
                <p>Guru mengklik link yang dibagikan (contoh: <code>.../presensi/[TOKEN]</code>). Request ditangani oleh <code>AbsensiGuru::index</code>.</p>

                <h5>Alur Validasi Sistem:</h5>
                <p>Saat link diakses, sistem melakukan pengecekan bertingkat:</p>
                <ol>
                    <li>
                        <strong>Cek Token</strong>: Apakah token valid dan ada di database?
                        <ul>
                            <li><em>Jika Gagal</em>: Tampilkan Error <code>invalid_token</code>.</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Cek Status Aktif</strong>: Apakah kegiatan di-set "Aktif" oleh admin?
                        <ul>
                            <li><em>Jika Gagal</em>: Tampilkan Error <code>inactive</code>.</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Cek Jadwal (Occurrence)</strong>: Apakah <strong>HARI INI</strong> sesuai dengan pola jadwal kegiatan?
                        <ul>
                            <li><em>Logika</em>: Fungsi <code>calculateCurrentOccurrence</code> mengecek tanggal, hari, dan interval (misal: "Setiap Senin").</li>
                            <li><em>Jika Gagal</em>:
                                <ul>
                                    <li>Cari tanggal kejadian berikutnya (<code>calculateNextOccurrence</code>).</li>
                                    <li>Tampilkan Error <code>no_occurrence</code> dengan info "Sesi Berikutnya: [Tanggal]".</li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <strong>Cek Waktu (Jam)</strong>: Apakah waktu akses berada dalam rentang <code>JamMulai</code> s/d <code>JamSelesai</code>?
                        <ul>
                            <li><em>Jika Sebelum Waktu</em>: Tampilkan Error <code>before_start</code> (Hitung mundur ke jam mulai).</li>
                            <li><em>Jika Setelah Waktu</em>: Tampilkan Error <code>after_end</code>.</li>
                        </ul>
                    </li>
                </ol>

                <h4 class="mt-4">3. Penyiapan Data Absensi (Sistem)</h4>
                <p>Jika semua validasi lolos, sistem menyiapkan data untuk ditampilkan:</p>
                <ol>
                    <li><strong>Cek Data Absensi</strong>: Apakah sudah ada data di tabel <code>tbl_absensi_guru</code> untuk Kegiatan ini pada Tanggal ini?</li>
                    <li><strong>Lazy Loading (Generate Otomatis)</strong>:
                        <ul>
                            <li>Jika data <strong>BELUM ADA</strong>: Sistem mengambil daftar guru yang sesuai lingkup (Semua atau per TPQ).</li>
                            <li>Sistem melakukan <code>INSERT</code> batch ke <code>tbl_absensi_guru</code> dengan status default <code>Alfa</code>.</li>
                            <li>Ini memastikan semua guru terdaftar untuk sesi tersebut.</li>
                        </ul>
                    </li>
                    <li><strong>Tampilkan Halaman</strong>: Menampilkan daftar guru, dipisahkan menjadi tab "Belum Hadir" (Alfa) dan "Sudah Hadir" (Hadir/Izin/Sakit).</li>
                </ol>

                <h4 class="mt-4">4. Proses Absensi (Guru)</h4>
                <p>Guru melakukan absensi pada halaman yang tampil.</p>
                <ol>
                    <li><strong>Pilih Nama</strong>: Guru mencari namanya di daftar "Belum Hadir".</li>
                    <li><strong>Klik Hadir</strong>: Guru menekan tombol aksi (biasanya tombol "Hadir").</li>
                    <li><strong>Request AJAX</strong>: Browser mengirim request POST ke <code>AbsensiGuru::hadir</code>.
                        <ul>
                            <li>Data dikirim: <code>id</code> (ID Absensi), <code>status</code> (Hadir), <code>latitude</code>, <code>longitude</code>.</li>
                        </ul>
                    </li>
                    <li><strong>Update Database</strong>: Sistem mengupdate record guru tersebut:
                        <ul>
                            <li><code>StatusKehadiran</code> -> 'Hadir'</li>
                            <li><code>WaktuAbsen</code> -> Waktu Sekarang</li>
                            <li><code>Latitude/Longitude</code> -> Lokasi Guru</li>
                        </ul>
                    </li>
                    <li><strong>UI Update</strong>: Nama guru pindah dari daftar "Belum Hadir" ke "Sudah Hadir".</li>
                </ol>

                <h4 class="mt-4">Diagram Alur (Flowchart)</h4>
                <div class="mermaid text-center">
                    graph TD
                    A[Start: Guru Akses Link] --> B{Token Valid?}
                    B -- Tidak --> C[Error: Link Invalid]
                    B -- Ya --> D{Kegiatan Aktif?}
                    D -- Tidak --> E[Error: Kegiatan Nonaktif]
                    D -- Ya --> F{Jadwal Hari Ini?}
                    F -- Tidak --> G[Error: Tidak Ada Jadwal]
                    F -- Ya --> H{Cek Waktu}
                    H -- Belum Mulai --> I[Error: Belum Dimulai]
                    H -- Sudah Lewat --> J[Error: Sudah Berakhir]
                    H -- Dalam Rentang --> K{Data Absensi Ada?}
                    K -- Tidak --> L[Generate Data 'Alfa']
                    K -- Ya --> M[Tampilkan Daftar Absensi]
                    L --> M
                    M --> N[Guru Klik 'Hadir']
                    N --> O[Update DB & Lokasi]
                    O --> P[Pindah ke List 'Sudah Hadir']
                </div>

                <div class="alert alert-info mt-4">
                    <h5><i class="icon fas fa-info"></i> Ringkasan Teknis (File Terkait)</h5>
                    <ul>
                        <li><strong>Controller</strong>: <code>app/Controllers/AbsensiGuru.php</code></li>
                        <li><strong>Model</strong>: <code>app/Models/AbsensiGuruModel.php</code> (Data Absensi), <code>app/Models/KegiatanAbsensiModel.php</code> (Config Kegiatan)</li>
                        <li><strong>View (Frontend)</strong>: <code>app/Views/frontend/absensi/index.php</code> (Form Absensi), <code>app/Views/frontend/absensi/error.php</code> (Halaman Error)</li>
                        <li><strong>View (Backend)</strong>: <code>app/Views/backend/absensiGuru/index.php</code> (Manajemen Kegiatan)</li>
                    </ul>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</section>

<script type="module">
    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
    mermaid.initialize({ startOnLoad: true });
</script>

<?= $this->endSection(); ?>
