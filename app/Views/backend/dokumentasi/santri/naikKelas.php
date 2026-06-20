<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<section class="content">
    <div class="container-fluid">
        <!-- Card 1: Panduan Kenaikan Kelas -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-graduation-cap mr-2"></i>Panduan Kenaikan Kelas Santri</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <h5 class="text-primary"><i class="fas fa-list-ol mr-1"></i> Langkah-langkah Kenaikan Kelas</h5>
                
                <div class="timeline">
                    <!-- Step 1 -->
                    <div class="time-label">
                        <span class="bg-primary">Langkah 1</span>
                    </div>
                    <div>
                        <i class="fas fa-sign-in-alt bg-info"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Akses Halaman Kenaikan Kelas</h3>
                            <div class="timeline-body">
                                Login sebagai <strong>Admin</strong> atau <strong>Operator</strong>, kemudian navigasi ke menu sidebar: <br>
                                <code>Kesiswaan > Naik Kelas</code> atau buka langsung URL: <code>backend/kelas/showListSantriPerKelas</code>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="time-label">
                        <span class="bg-primary">Langkah 2</span>
                    </div>
                    <div>
                        <i class="fas fa-filter bg-warning"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Pilih Tahun Ajaran Asal (Fleksibel)</h3>
                            <div class="timeline-body">
                                Gunakan card filter **"Pilih Tahun Ajaran Asal untuk Proses Kenaikan"** di bagian atas halaman:
                                <ul class="mt-2">
                                    <li>Pilih tahun ajaran asal yang santrinya ingin dipromosikan (misal: <code>2025/2026</code>).</li>
                                    <li>Sistem akan otomatis menentukan tahun ajaran target (misal: <code>2026/2027</code>).</li>
                                    <li>Halaman akan memuat ulang secara otomatis untuk menyajikan kelas-kelas pada tahun ajaran asal yang dipilih.</li>
                                </ul>
                                <p class="text-muted text-sm mb-0">* Dengan fitur ini, Anda tidak perlu menunggu kalender sistem berganti ke bulan Juli untuk memproses kenaikan kelas tahun ajaran berikutnya.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="time-label">
                        <span class="bg-primary">Langkah 3</span>
                    </div>
                    <div>
                        <i class="fas fa-tasks bg-purple"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Pilih Kelas dan Klik Proses</h3>
                            <div class="timeline-body">
                                Pada tabel **"List Santri TPQ Per Kelas Tahun Ajaran Asal"**:
                                <br><br>
                                <ol>
                                    <li>Periksa kolom **Jumlah Santri** untuk memastikan data santri di kelas tersebut sudah lengkap.</li>
                                    <li>Klik tombol <span class="badge badge-warning"><i class="fas fa-edit"></i> Proses</span> di kolom paling kanan.</li>
                                    <li>Sistem akan memproses seluruh santri di kelas tersebut secara massal ke kelas satu tingkat di atasnya pada tahun ajaran target.</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="time-label">
                        <span class="bg-primary">Langkah 4</span>
                    </div>
                    <div>
                        <i class="fas fa-check bg-success"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Verifikasi Hasil Kenaikan Kelas</h3>
                            <div class="timeline-body">
                                Setelah proses selesai, periksa tabel bagian bawah:
                                <br>
                                <ul>
                                    <li>Tabel **"List Santri TPQ Per Kelas Tahun Ajaran Target"** akan memuat kelas baru hasil promosi beserta jumlah santrinya.</li>
                                    <li>Pastikan jumlah santri hasil kenaikan kelas di tahun ajaran target sudah sesuai dengan jumlah kelas asal.</li>
                                    <li>Secara otomatis, data pendaftaran santri di kelas lama pada tahun ajaran asal diubah statusnya menjadi tidak aktif (`status = 0`) untuk menghindari kenaikan ganda.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <i class="fas fa-check bg-gray"></i>
                    </div>
                </div>

                <hr>

                <!-- Fitur Kenaikan Kelas Fleksibel -->
                <h5 class="text-success mt-4"><i class="fas fa-star mr-1"></i> Keunggulan Fitur Kenaikan Kelas Fleksibel</h5>
                <div class="callout callout-success">
                    <h5>Kenapa Fitur Ini Memudahkan Anda?</h5>
                    <ul>
                        <li><strong>Bebas Waktu</strong>: Kenaikan kelas dapat diset kapan saja (misalnya akhir Mei atau Juni) tanpa harus menunggu pergantian tahun ajaran baru secara resmi di kalender sistem.</li>
                        <li><strong>Dukungan Multi-Tahun</strong>: Memungkinkan Anda melihat data promosi tahun ajaran lama maupun menyiapkan tahun ajaran mendatang dengan mudah.</li>
                        <li><strong>Otomatisasi Nilai</strong>: Sistem secara otomatis membuat entri daftar nilai (`tbl_nilai`) untuk semester ganjil dan genap bagi setiap santri di kelas barunya.</li>
                        <li><strong>Keamanan Data</strong>: Sistem otomatis me-nonaktifkan status keanggotaan kelas lama santri demi mencegah kesalahan promosi ganda untuk santri yang sama.</li>
                    </ul>
                </div>

                <!-- Flowchart -->
                <h5 class="text-info mt-4"><i class="fas fa-project-diagram mr-1"></i> Diagram Alur Kenaikan Kelas</h5>
                <div class="mermaid text-center">
                    graph TD
                    A[Start: Operator Pilih Tahun Ajaran Asal] --> B[Sistem Hitung Tahun Ajaran Target Asal + 1]
                    B --> C[Tampilkan Daftar Kelas Asal tabel atas]
                    C --> D[Operator Klik Tombol Proses Naik Kelas]
                    D --> E[Ambil Semua Santri Aktif di Kelas Tersebut]
                    E --> F[Cari Kelas Baru Kelas Lama + 1]
                    F --> G[Nonaktifkan Status Kelas Lama di DB status = 0]
                    G --> H[Simpan Pendaftaran Kelas Baru di DB status = 1]
                    H --> I[Generate Otomatis Lembar Nilai Semester Ganjil & Genap]
                    I --> J[Tampilkan Hasil di Tabel Target tabel bawah]
                    J --> K[Selesai]
                </div>

            </div>
        </div>

        <!-- Card 2: Technical Info -->
        <div class="card collapsed-card">
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
                        <div class="callout callout-info">
                            <h5>Backend Components</h5>
                            <p>Controller: <code>app/Controllers/Backend/Kelas.php</code></p>
                            <ul class="text-sm">
                                <li><code>showListSantriPerKelas($idTahunAjaran)</code> - Menampilkan halaman & mengambil data berdasarkan parameter tahun ajaran asal.</li>
                                <li><code>updateNaikKelas($idTahunAjaran, $idKelas)</code> - Memproses kenaikan kelas, update status kelas lama, insert kelas baru, dan input template nilai.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-warning">
                            <h5>Database & Model</h5>
                            <p>Table: <code>tbl_kelas_santri</code> (menyimpan data pendaftaran kelas santri)</p>
                            <p>Table: <code>tbl_nilai</code> (menyimpan record lembar penilaian santri)</p>
                            <ul class="text-sm">
                                <li><code>Status = 1</code>: Aktif (santri terdaftar di kelas & tahun ajaran tersebut).</li>
                                <li><code>Status = 0</code>: Tidak Aktif (kelas lama setelah dipromosikan).</li>
                            </ul>
                            <p>Model: <code>App\Models\KelasModel</code></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?= $this->endSection(); ?>
