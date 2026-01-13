<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<section class="content">
    <div class="container-fluid">
        <!-- Card 1: Proses Pembuatan Link -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-link mr-2"></i>Proses Pembuatan Link Absensi Public</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <h5 class="text-primary"><i class="fas fa-user-shield mr-1"></i> Langkah-langkah (Admin/Operator)</h5>
                
                <div class="timeline">
                    <div class="time-label">
                        <span class="bg-primary">Langkah 1</span>
                    </div>
                    <div>
                        <i class="fas fa-sign-in-alt bg-info"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Login ke Panel Admin</h3>
                            <div class="timeline-body">
                                Akses panel admin dengan akun Admin atau Operator.
                            </div>
                        </div>
                    </div>
                    <div class="time-label">
                        <span class="bg-primary">Langkah 2</span>
                    </div>
                    <div>
                        <i class="fas fa-mouse-pointer bg-warning"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Akses Menu Link Absensi</h3>
                            <div class="timeline-body">
                                Navigasi ke menu <strong>Santri → Link Absensi Public</strong> di sidebar.
                            </div>
                        </div>
                    </div>
                    <div class="time-label">
                        <span class="bg-primary">Langkah 3</span>
                    </div>
                    <div>
                        <i class="fas fa-plus-circle bg-success"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Buat Link Baru</h3>
                            <div class="timeline-body">
                                <ol>
                                    <li>Klik tombol <span class="badge badge-success"><i class="fas fa-plus"></i> Tambah Link</span></li>
                                    <li>Pilih <strong>Lembaga (TPQ)</strong> dari dropdown</li>
                                    <li>Pilih <strong>Tahun Ajaran</strong> yang sedang berjalan</li>
                                    <li>Klik <span class="badge badge-primary">Simpan</span></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="time-label">
                        <span class="bg-primary">Langkah 4</span>
                    </div>
                    <div>
                        <i class="fas fa-share-alt bg-purple"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Bagikan Link ke Guru</h3>
                            <div class="timeline-body">
                                Gunakan tombol yang tersedia:
                                <ul class="mt-2">
                                    <li><span class="badge badge-info"><i class="fas fa-copy"></i></span> - Salin link ke clipboard</li>
                                    <li><span class="badge badge-primary"><i class="fas fa-external-link-alt"></i></span> - Buka link di tab baru</li>
                                    <li><span class="badge badge-success" style="background-color: #25D366;"><i class="fab fa-whatsapp"></i></span> - Kirim via WhatsApp</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div>
                        <i class="fas fa-check bg-success"></i>
                    </div>
                </div>

                <hr>
                
                <!-- Penjelasan Teknis -->
                <h5 class="text-info mt-4"><i class="fas fa-code mr-1"></i> Penjelasan Teknis</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="callout callout-info">
                            <h5>Backend Process</h5>
                            <p>Controller: <code>Backend/Absensi.php</code></p>
                            <ul class="text-sm">
                                <li><code>linkIndex()</code> - Menampilkan daftar link</li>
                                <li><code>linkNew()</code> - Form tambah link</li>
                                <li><code>linkCreate()</code> - Simpan link baru ke DB</li>
                                <li><code>linkEdit()</code> - Form edit link</li>
                                <li><code>linkUpdate()</code> - Update link di DB</li>
                                <li><code>linkDelete()</code> - Hapus link</li>
                                <li><code>linkRegenerate()</code> - Generate HashKey baru</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-warning">
                            <h5>Database Structure</h5>
                            <p>Tabel: <code>tbl_absensi_santri_link</code></p>
                            <table class="table table-sm table-bordered">
                                <tr><td><code>Id</code></td><td>Primary Key</td></tr>
                                <tr><td><code>IdTpq</code></td><td>Relasi ke TPQ</td></tr>
                                <tr><td><code>IdTahunAjaran</code></td><td>Format: 20252026</td></tr>
                                <tr><td><code>HashKey</code></td><td>32 char random (bin2hex)</td></tr>
                                <tr><td><code>CreatedAt</code></td><td>Timestamp pembuatan</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <h5 class="text-success mt-4"><i class="fas fa-project-diagram mr-1"></i> Flowchart Pembuatan Link</h5>
                <div class="mermaid text-center">
                    graph TD
                    A[Admin/Operator Login] --> B[Akses Menu Link Absensi]
                    B --> C[Klik Tambah Link]
                    C --> D[Pilih TPQ & Tahun Ajaran]
                    D --> E[Klik Simpan]
                    E --> F{Validasi Input}
                    F -- Gagal --> G[Tampilkan Error]
                    G --> D
                    F -- Berhasil --> H[Generate HashKey<br/>bin2hex random_bytes 16]
                    H --> I[Insert ke Database]
                    I --> J[Redirect ke List Link]
                    J --> K[Copy/Share Link ke Guru]
                </div>

                <!-- Card: Technical Info -->
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
                                <div class="callout callout-info" style="border-left-color: #17a2b8;">
                                    <h5>Backend Components</h5>
                                    <p>Controller: <code>app/Controllers/Backend/Absensi.php</code></p>
                                    <ul class="text-sm">
                                        <li><code>linkIndex()</code>, <code>linkNew()</code>, <code>linkCreate()</code></li>
                                        <li><code>linkRegenerate()</code> - HashKey Logic</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="callout callout-warning" style="border-left-color: #ffc107;">
                                    <h5>Database & Model</h5>
                                    <p>Models: <code>AbsensiSantriLinkModel</code></p>
                                    <p>Views:</p>
                                    <ul class="text-sm">
                                        <li><code>backend/absensi/linkIndex.php</code> (List)</li>
                                        <li><code>backend/absensi/linkForm.php</code> (Form)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success mt-3">
                    <i class="fas fa-info-circle"></i> <strong>Tips:</strong> 
                    Pastikan tahun ajaran yang dipilih sesuai dengan tahun ajaran aktif. Link dengan tahun ajaran berbeda akan menampilkan pesan error saat diakses.
                </div>
            </div>
        </div>

        <!-- Card 2: Dokumentasi Absensi Santri Public -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Dokumentasi Absensi Santri Public</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <p>Dokumen ini menjelaskan alur teknis dan operasional dari sistem absensi santri secara publik di aplikasi TPQ Smart.</p>

                <h4 class="mt-4">1. Persiapan Link Absensi (Admin/Operator)</h4>
                <p>Sebelum absensi dapat dilakukan, link harus dibuat terlebih dahulu di panel admin.</p>
                <ol>
                    <li>
                        <strong>Buat Link</strong>: Admin/Operator membuat link baru di menu <code>Santri → Link Absensi Public</code>.
                        <ul>
                            <li>Input: Lembaga (TPQ) dan Tahun Ajaran.</li>
                        </ul>
                    </li>
                    <li><strong>Generate HashKey</strong>: Sistem secara otomatis membuat <code>HashKey</code> unik (32 karakter) untuk link tersebut.</li>
                    <li><strong>Share Link</strong>: Admin membagikan link absensi kepada guru melalui WhatsApp atau media lain menggunakan tombol yang tersedia.</li>
                </ol>

                <h4 class="mt-4">2. Akses Halaman Absensi (Guru)</h4>
                <p>Guru mengklik link yang dibagikan (contoh: <code>.../absensi/haskey/[HASHKEY]</code>). Request ditangani oleh <code>AbsensiSantri::index</code>.</p>

                <h5>Alur Validasi Sistem:</h5>
                <p>Saat link diakses, sistem melakukan pengecekan bertingkat:</p>
                <ol>
                    <li>
                        <strong>Cek HashKey</strong>: Apakah HashKey valid dan ada di database?
                        <ul>
                            <li><em>Jika Gagal</em>: Tampilkan Error <code>invalid_token</code>.</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Cek Tahun Ajaran</strong>: Apakah tahun ajaran link sesuai dengan tahun ajaran saat ini?
                        <ul>
                            <li><em>Jika Gagal</em>: Tampilkan Error <code>tahun_ajaran_mismatch</code> dengan instruksi untuk menghubungi Operator.</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Cek TPQ</strong>: Apakah guru yang login terdaftar di TPQ yang sama dengan link?
                        <ul>
                            <li><em>Jika Gagal</em>: Tampilkan Error <code>tpq_mismatch</code> dan blokir akses.</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Cek Autentikasi</strong>: Apakah guru sudah login atau memiliki device token yang valid?
                        <ul>
                            <li><em>Jika Belum Login</em>: Redirect ke halaman login standar.</li>
                            <li><em>Jika Sudah Login</em>: Buat device token baru untuk akses berikutnya.</li>
                        </ul>
                    </li>
                </ol>

                <h4 class="mt-4">3. Tampilan Halaman Absensi</h4>
                <p>Jika semua validasi lolos, sistem menampilkan halaman absensi dengan fitur:</p>
                <ol>
                    <li><strong>Tab Kelas</strong>: Menampilkan daftar kelas yang diajar oleh guru tersebut.</li>
                    <li><strong>Pilih Tanggal</strong>: Guru dapat memilih tanggal absensi (default: hari ini).</li>
                    <li><strong>Daftar Santri</strong>: Menampilkan santri yang belum diabsen untuk kelas dan tanggal yang dipilih.</li>
                    <li><strong>Tombol "Set Semua Hadir"</strong>: Shortcut untuk set semua santri dengan status "Hadir".</li>
                </ol>

                <h4 class="mt-4">4. Proses Absensi (Guru)</h4>
                <p>Guru melakukan absensi pada halaman yang tampil.</p>
                <ol>
                    <li><strong>Pilih Status</strong>: Guru memilih status kehadiran untuk setiap santri (Hadir/Izin/Sakit/Alfa).</li>
                    <li><strong>Klik Simpan</strong>: Guru menekan tombol simpan.</li>
                    <li><strong>Request AJAX</strong>: Browser mengirim request POST ke <code>AbsensiSantri::simpanAbsensi</code>.</li>
                    <li><strong>Insert Database</strong>: Sistem menyimpan data ke tabel <code>tbl_absensi_santri</code>.</li>
                    <li><strong>UI Update</strong>: Santri yang sudah diabsen tidak muncul lagi di daftar.</li>
                </ol>

                <h4 class="mt-4">5. Informasi Kelas Sudah Diabsen</h4>
                <p>Jika semua santri di kelas sudah diabsen pada tanggal tersebut:</p>
                <ul>
                    <li>Sistem menampilkan pesan informasi: <em>"Semua santri di kelas ini sudah diabsen pada tanggal [TANGGAL]"</em></li>
                    <li>Ditampilkan juga nama guru yang melakukan absensi: <em>"Oleh: Ustadz/Ustadzah [NAMA]"</em></li>
                </ul>

                <h4 class="mt-4">6. Ubah Absensi yang Sudah Tercatat</h4>
                <p>Guru dapat mengubah absensi yang sudah tercatat menggunakan fitur "Ubah Absensi":</p>
                <ol>
                    <li><strong>Klik Tombol "Ubah Absensi"</strong>: Tombol berwarna kuning muncul setelah semua santri sudah diabsen.</li>
                    <li><strong>Section Edit Terbuka</strong>: Menampilkan daftar santri dengan status absensi saat ini.</li>
                    <li><strong>Pilih Status Baru</strong>: Klik tombol status untuk mengubah (Hadir/Izin/Sakit/Alfa).
                        <ul>
                            <li><span class="badge badge-success">Hadir</span> dan <span class="badge badge-danger">Alfa</span>: Langsung tersimpan otomatis.</li>
                            <li><span class="badge badge-warning">Izin</span> dan <span class="badge badge-info">Sakit</span>: Muncul input keterangan opsional.</li>
                        </ul>
                    </li>
                    <li><strong>Keterangan Opsional</strong>: Untuk status Izin/Sakit, guru dapat mengisi alasan.
                        <ul>
                            <li>Input keterangan akan <strong>otomatis tersimpan</strong> saat kursor keluar dari kolom input (blur).</li>
                            <li>Atau klik tombol <span class="badge badge-primary"><i class="fas fa-save"></i></span> untuk menyimpan manual.</li>
                        </ul>
                    </li>
                    <li><strong>Notifikasi Sukses</strong>: Toast notification muncul setiap kali perubahan berhasil disimpan.</li>
                </ol>
                
                <div class="callout callout-success">
                    <h5><i class="fas fa-lightbulb"></i> Fitur Auto-Save</h5>
                    <p>Saat mengisi keterangan untuk status <strong>Izin</strong> atau <strong>Sakit</strong>, data akan otomatis tersimpan saat guru mengklik di luar kolom input. Tidak perlu menekan tombol simpan terlebih dahulu.</p>
                </div>

                <h4 class="mt-4">Diagram Alur (Flowchart)</h4>
                <div class="mermaid text-center">
                    graph TD
                    A[Start: Guru Akses Link] --> B{HashKey Valid?}
                    B -- Tidak --> C[Error: Link Invalid]
                    B -- Ya --> D{Tahun Ajaran Sesuai?}
                    D -- Tidak --> E[Error: Tahun Ajaran Tidak Sesuai]
                    D -- Ya --> F{TPQ Sesuai?}
                    F -- Tidak --> G[Error: Akses Ditolak]
                    F -- Ya --> H{Sudah Login?}
                    H -- Tidak --> I[Redirect ke Login]
                    H -- Ya --> J[Tampilkan Daftar Santri]
                    J --> K{Semua Sudah Diabsen?}
                    K -- Tidak --> L[Guru Pilih Status Absensi]
                    L --> M[Simpan ke Database]
                    M --> N[Update UI]
                    K -- Ya --> O[Tampil Tombol Ubah Absensi]
                    O --> P[Klik Ubah Absensi]
                    P --> Q[Buka Section Edit]
                    Q --> R{Pilih Status}
                    R -- Hadir/Alfa --> S[Auto-save AJAX]
                    R -- Izin/Sakit --> T[Muncul Input Keterangan]
                    T --> U[Blur / Klik Simpan]
                    U --> S
                    S --> V[Toast Notifikasi Sukses]
                </div>

                <!-- Card: Technical Info -->
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
                                <div class="callout callout-info" style="border-left-color: #17a2b8;">
                                    <h5>Backend Components</h5>
                                    <p><strong>Controllers:</strong></p>
                                    <ul class="text-sm">
                                        <li><code>Frontend/AbsensiSantri.php</code> (Public Logic)</li>
                                        <li><code>Backend/Absensi.php</code> (Link Management: <code>linkIndex</code>, <code>linkCreate</code>)</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="callout callout-warning" style="border-left-color: #ffc107;">
                                    <h5>Database & Model</h5>
                                    <p>Models:</p>
                                    <ul class="text-sm">
                                        <li><code>AbsensiSantriLinkModel</code> (Table: <code>tbl_absensi_santri_link</code>)</li>
                                        <li><code>AbsensiDeviceModel</code> (Token Auth: <code>tbl_absensi_device</code>)</li>
                                    </ul>
                                    <p>Views:</p>
                                    <ul class="text-sm">
                                        <li>Frontend: <code>frontend/absensi/index.php</code></li>
                                        <li>Backend: <code>backend/absensi/linkIndex.php</code></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning mt-4">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Catatan Penting</h5>
                    <ul>
                        <li><strong>Device Token</strong>: Setelah login pertama kali, sistem menyimpan device token di cookie (berlaku 1 tahun) sehingga guru tidak perlu login ulang.</li>
                        <li><strong>Tahun Ajaran</strong>: Format tahun ajaran disimpan tanpa <code>/</code> (contoh: <code>20252026</code>).</li>
                        <li><strong>HashKey</strong>: Dapat di-regenerate jika diperlukan, namun link lama menjadi tidak valid.</li>
                    </ul>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</section>



<?= $this->endSection(); ?>
