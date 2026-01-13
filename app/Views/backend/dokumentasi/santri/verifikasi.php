<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<section class="content">
    <div class="container-fluid">
        <!-- Card 1: Alur Verifikasi -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-check-circle mr-2"></i>Panduan Verifikasi Data Santri</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <h5 class="text-primary"><i class="fas fa-list-ol mr-1"></i> Langkah-langkah Verifikasi</h5>
                
                <div class="timeline">
                    <!-- Step 1 -->
                    <div class="time-label">
                        <span class="bg-primary">Langkah 1</span>
                    </div>
                    <div>
                        <i class="fas fa-sign-in-alt bg-info"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Akses Halaman Verifikasi</h3>
                            <div class="timeline-body">
                                Login sebagai <strong>Operator</strong>, kemudian navigasi ke menu sidebar: <br>
                                <code>Kesiswaan > Input Data Santri > Verifikasi Data</code>
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
                            <h3 class="timeline-header">Review Daftar Santri</h3>
                            <div class="timeline-body">
                                Pada halaman daftar, Anda dapat melihat status verifikasi santri:
                                <ul class="mt-2">
                                    <li><span class="badge badge-success">Valid</span> : Data sudah diverifikasi dan benar.</li>
                                    <li><span class="badge badge-warning">Pending</span> : Data belum diperiksa (Baru).</li>
                                    <li><span class="badge badge-danger">Perlu Perbaikan</span> : Data telah diperiksa namun ada kekurangan.</li>
                                </ul>
                                <p>Gunakan fitur <strong>Search</strong> atau <strong>Filter Status</strong> untuk menemukan santri tertentu.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="time-label">
                        <span class="bg-primary">Langkah 3</span>
                    </div>
                    <div>
                        <i class="fas fa-search-plus bg-primary"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Periksa Detail & Kartu Keluarga</h3>
                            <div class="timeline-body">
                                Klik tombol <span class="badge badge-info"><i class="fas fa-search"></i> Detail</span> pada baris santri yang ingin diperiksa.
                                <br><br>
                                Halaman Detail terbagi menjadi dua kolom:
                                <div class="row mt-2">
                                    <div class="col-md-6 border-right">
                                        <strong>Kiri: Data Inputan</strong><br>
                                        Berisi form data diri santri yang bisa diedit langsung jika ada kesalahan ketik minor.
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Kanan: File Kartu Keluarga (KK)</strong><br>
                                        Menampilkan lampiran KK. Anda dapat melakukan:
                                        <ul>
                                            <li>Zoom In/Out</li>
                                            <li>Rotate (Putar)</li>
                                            <li>Crop (Potong) bagian tertentu</li>
                                        </ul>
                                    </div>
                                </div>
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
                            <h3 class="timeline-header">Update Status Verifikasi</h3>
                            <div class="timeline-body">
                                Setelah membandingkan data:
                                <br>
                                <ul>
                                    <li>Jika data <strong>SESUAI</strong>: Klik tombol <button class="btn btn-sm btn-success">Data Valid</button>. Status berubah menjadi hijau.</li>
                                    <li>Jika data <strong>TIDAK SESUAI</strong>: Klik tombol <button class="btn btn-sm btn-danger">Perlu Perbaikan</button>. Status berubah menjadi merah.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <i class="fas fa-check bg-gray"></i>
                    </div>
                </div>

                <hr>

                <!-- WhatsApp Feature -->
                <h5 class="text-success mt-4"><i class="fab fa-whatsapp mr-1"></i> Fitur Integrasi WhatsApp</h5>
                <div class="callout callout-success">
                    <p>Memudakan komunikasi dengan Wali Santri untuk konfirmasi data.</p>
                    <strong>Cara Penggunaan:</strong>
                    <ol>
                        <li>Pada halaman daftar, klik tombol <button class="btn btn-sm btn-success"><i class="fab fa-whatsapp"></i></button> pada kolom No. HP Ayah/Ibu.</li>
                        <li>Akan muncul popup <strong>Kirim Pesan WhatsApp</strong>.</li>
                        <li>Pilih <strong>Template Pesan</strong>:
                            <ul>
                                <li><code>Info: Data Valid</code> : Mengirim kabar bahwa data sudah valid.</li>
                                <li><code>Info: Perlu Perbaikan</code> : Mengirim rincian data yang salah.</li>
                                <li><code>Manual</code> : Tulis pesan bebas.</li>
                            </ul>
                        </li>
                        <li>
                            Jika memilih <strong>Perlu Perbaikan</strong>, Anda dapat mencentang bagian yang salah (KK, Foto, Data Diri, dll). Pesan akan otomatis ter-generate.
                        </li>
                        <li>Klik <strong>Kirim</strong> untuk membuka WhatsApp Web/App.</li>
                    </ol>
                    <p class="text-muted text-sm mt-2">
                        * Pesan otomatis menyertakan tanda tangan operator yang sedang login (Contoh: <em>Operator Lembaga : Juliana Lubis</em>).
                    </p>
                </div>

                <!-- Flowchart -->
                <h5 class="text-info mt-4"><i class="fas fa-project-diagram mr-1"></i> Diagram Alur Verifikasi</h5>
                <div class="mermaid text-center">
                    graph TD
                    A[Start: Operator Buka Halaman Verifikasi] --> B{Cek Status Santri}
                    B -- "Valid (Hijau)" --> C[Data Sudah Oke]
                    C --> D[Kirim Info Valid via WA]:::optional
                    B -- "Pending / Perlu Perbaikan" --> E[Buka Detail Verifikasi]
                    E --> F[Bandingkan Data vs Scan KK]
                    F --> G{Datanya Sesuai?}
                    
                    G -- Ya --> H[Klik Tombol 'Data Valid']
                    H --> I[Update Status DB: 'Sudah Diverifikasi']
                    I --> D
                    
                    G -- Tidak --> J[Klik Tombol 'Perlu Perbaikan']
                    J --> K[Update Status DB: 'Perlu Perbaikan']
                    K --> L[Kirim Pesan WA: Template Perlu Perbaikan]
                    L --> M[Pilih Checklist Kesalahan]
                    M --> N[Kirim ke Wali Santri]
                    
                    D --> O[Selesai]
                    N --> O
                    
                    classDef optional stroke-dasharray: 5 5;
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
                            <p>Controller: <code>app/Controllers/Backend/Santri.php</code></p>
                            <ul class="text-sm">
                                <li><code>verifikasiDataSantri()</code> - List Page</li>
                                <li><code>perbandinganDataSantri($id)</code> - Detail Page</li>
                                <li><code>processVerifikasi()</code> - Update Status API</li>
                                <li><code>updateFileKk()</code> - Handle Image Crop/Upload</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-warning">
                            <h5>Database & Model</h5>
                            <p>Table: <code>tbl_santri_baru</code></p>
                            <ul class="text-sm">
                                <li><code>Status</code>: ENUM('Sudah Diverifikasi', 'Belum Diverifikasi', 'Perlu Perbaikan')</li>
                                <li><code>Status</code> (Old): 0 (Pending), 1 (Valid), 2 (Revisi) handled by mapper in Controller.</li>
                            </ul>
                            <p>Model: <code>App\Models\Backend\Santri\VerifikasiSantriModel</code></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<?= $this->endSection(); ?>

