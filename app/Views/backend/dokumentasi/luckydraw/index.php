<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<section class="content">
    <div class="container-fluid">
        
        <!-- Card 1: Gambaran Umum Sistem -->
        <div class="card card-purple card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-eye mr-2"></i>Gambaran Umum Sistem Lucky Draw</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <p>
                    Modul <strong>Lucky Draw (Undian Berhadiah)</strong> dirancang untuk mengelola proses pengundian hadiah secara transparan dan dinamis. Sistem terbagi menjadi tiga fase utama: Inisialisasi Data oleh Admin, Pelaksanaan Undian & Input Hadiah oleh Panitia, dan Penyerahan Hadiah Fisik kepada Pemenang.
                </p>
                
                <h5 class="text-purple mt-4"><i class="fas fa-project-diagram mr-1"></i> Visualisasi Alur Sistem Terintegrasi</h5>
                <div class="mermaid text-center my-4">
                    graph TD
                        %% Node Styling Definitions %%
                        classDef adminNode fill:#8b5cf6,stroke:#a78bfa,stroke-width:2px,color:#fff,font-weight:bold;
                        classDef dbNode fill:#1e1b4b,stroke:#4f46e5,stroke-width:2px,color:#fff,font-weight:bold;
                        classDef inputNode fill:#d97706,stroke:#f59e0b,stroke-width:2px,color:#fff,font-weight:bold;
                        classDef tvNode fill:#0891b2,stroke:#06b6d4,stroke-width:2px,color:#fff,font-weight:bold;
                        classDef verifyNode fill:#059669,stroke:#10b981,stroke-width:2px,color:#fff,font-weight:bold;
                        classDef winNode fill:#db2777,stroke:#ec4899,stroke-width:2px,color:#fff,font-weight:bold;

                        subgraph AdminSection ["⚙️ 1. PENGATURAN AWAL (ADMIN)"]
                            A["👑 Administrator Utama"]
                            A -->|"📝 Buat Kegiatan & Kupon"| DB[("🗄️ Database Utama")]
                            A -->|"👥 Tugaskan Akun Panitia"| DB
                        end

                        subgraph PanitiaSection ["🎰 2. OPERASIONAL ACARA (PANITIA INPUT)"]
                            P1["🎁 Panitia Pemenang (Input)"]
                            P1 -->|"📦 Daftarkan Stok Hadiah"| DB
                            P1 -->|"🏆 Input Nomor Pemenang"| DB
                            
                            DB -.->|"⚡ Sinkronisasi Instan"| TV["📺 Layar TV / Monitor Publik"]
                        end

                        subgraph PenerimaSection ["🤝 3. SERAH TERIMA (PANITIA VERIFIKASI)"]
                            P2["✅ Panitia Verifikasi (Serah Terima)"]
                            P2 -->|"🔍 Cari Nomor Kupon"| DB
                            DB -->|"🎁 Serahkan Hadiah Fisik"| W["🎉 Pemenang Hadiah"]
                        end

                        class A adminNode;
                        class DB dbNode;
                        class P1 inputNode;
                        class TV tvNode;
                        class P2 verifyNode;
                        class W winNode;

                        %% Customize Link Lines %%
                        linkStyle 0,1 stroke:#8b5cf6,stroke-width:2px;
                        linkStyle 2,3 stroke:#d97706,stroke-width:2px;
                        linkStyle 4 stroke:#0891b2,stroke-width:2px,stroke-dasharray: 5 5;
                        linkStyle 5,6 stroke:#059669,stroke-width:2px;
                </div>
            </div>
        </div>

        <!-- Card 2: Panduan Langkah Demi Langkah -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list-ol mr-2"></i>Panduan Langkah Demi Langkah</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                
                <div class="timeline">
                    <!-- Step 1 -->
                    <div class="time-label">
                        <span class="bg-primary">Langkah 1</span>
                    </div>
                    <div>
                        <i class="fas fa-calendar-check bg-info"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Pilih Kegiatan Aktif</h3>
                            <div class="timeline-body">
                                <strong>Admin & Panitia:</strong> Sebelum memulai, Anda wajib memilih kegiatan Lucky Draw yang sedang berlangsung melalui menu:<br>
                                <code>Lucky Draw > Pilih Kegiatan</code><br>
                                Pilihan ini akan disimpan dalam session untuk memfilter data hadiah, pemenang, dan panitia secara otomatis.
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="time-label">
                        <span class="bg-primary">Langkah 2</span>
                    </div>
                    <div>
                        <i class="fas fa-gift bg-purple"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Daftarkan Barang Hadiah (Panitia Pemenang / Panitia Input)</h3>
                            <div class="timeline-body">
                                <strong>Panitia Pemenang:</strong> Sebelum undian dimulai, daftarkan daftar hadiah di menu:<br>
                                <code>Lucky Draw > Data Barang Hadiah</code><br>
                                Masukkan <strong>Nama Barang</strong>, <strong>Kategori/Grup</strong>, <strong>No. Urut Hadiah</strong>, dan <strong>Jumlah (Stok) Hadiah</strong>. Sistem akan menghitung sisa kuota hadiah secara dinamis ketika undian berlangsung.
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="time-label">
                        <span class="bg-primary">Langkah 3</span>
                    </div>
                    <div>
                        <i class="fas fa-trophy bg-warning"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Mengundi & Menginput Pemenang (Panitia Pemenang / Panitia Input)</h3>
                            <div class="timeline-body">
                                <strong>Panitia Pemenang:</strong> Saat acara pengundian berlangsung, buka halaman:<br>
                                <code>Lucky Draw > Input Pemenang</code><br>
                                <ol class="mt-2">
                                    <li>Pilih jenis barang hadiah yang sedang diundi.</li>
                                    <li>Masukkan nomor kupon yang ditarik dari kotak undian fisik.</li>
                                    <li>Klik <strong>Simpan Pemenang</strong>. Sistem akan memverifikasi apakah nomor kupon berada di rentang yang valid dan belum pernah memenangkan hadiah lain.</li>
                                </ol>
                                Pemenang baru akan otomatis muncul di bagian bawah layar secara instan.
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="time-label">
                        <span class="bg-primary">Langkah 4</span>
                    </div>
                    <div>
                        <i class="fas fa-check-double bg-success"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Verifikasi Serah Terima (Panitia Verifikasi)</h3>
                            <div class="timeline-body">
                                <strong>Panitia Verifikasi:</strong> Jika pemenang maju ke panggung untuk mengambil hadiah, buka halaman:<br>
                                <code>Lucky Draw > Verifikasi Serah Terima</code><br>
                                <ol class="mt-2">
                                    <li>Masukkan nomor kupon pemenang di kolom pencarian.</li>
                                    <li>Periksa apakah jenis hadiah fisik sesuai dengan yang tertera di sistem.</li>
                                    <li>Klik tombol <button class="btn btn-sm btn-success">Verifikasi & Serahkan</button> untuk memperbarui status pemenang menjadi <strong>"Sudah Diambil"</strong> beserta waktu pengambilannya.</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <div>
                        <i class="fas fa-clock bg-gray"></i>
                    </div>
                </div>

            </div>
        </div>

        <!-- Card 3: Control Reset -->
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sliders-h mr-2"></i>Fitur Control Reset (Pengosongan Data)</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                
                <?php if (in_groups('Admin')): ?>
                    <h5 class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Panduan Reset untuk Administrator</h5>
                    <p>
                        Sebagai Administrator, Anda memiliki akses ke halaman **Control Reset** yang memungkinkan Anda menghapus atau mereset data modul secara aman dan selektif. Opsi reset yang tersedia meliputi:
                    </p>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="callout callout-danger">
                                <h5>Reset Pemenang Undian</h5>
                                <p>Menghapus seluruh catatan pemenang pada kegiatan terpilih. Sisa kuota stok seluruh barang hadiah akan otomatis kembali terisi penuh.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="callout callout-warning">
                                <h5>Reset Status Pengambilan Hadiah</h5>
                                <p>Mengubah kembali status seluruh pemenang menjadi "Belum Diambil". Data pemenang tidak dihapus, hanya status serah terimanya saja yang dikosongkan.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="callout callout-danger">
                                <h5>Reset Daftar Barang Hadiah</h5>
                                <p>Menghapus semua barang hadiah yang terdaftar pada kegiatan. Tindakan ini juga akan menghapus data pemenang karena dependensi data di database.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="callout callout-danger">
                                <h5>Reset Panitia Kegiatan</h5>
                                <p>Menghapus daftar tugas panitia yang dihubungkan ke kegiatan ini, sehingga akun panitia tersebut tidak dapat memanipulasi data kegiatan ini lagi.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-2">
                        <i class="fas fa-info-circle mr-2"></i><strong>Prosedur Keamanan:</strong> Demi keamanan data, proses reset memerlukan verifikasi ganda di mana Admin harus mengetikkan kata sandi konfirmasi <strong>"RESET"</strong> sebelum perintah dijalankan.
                    </div>
                <?php else: ?>
                    <div class="callout callout-danger mb-0">
                        <h5><i class="fas fa-lock mr-2"></i>Fitur Reset Dibatasi</h5>
                        <p>Fitur <strong>Control Reset</strong> (Pengosongan data pemenang, barang hadiah, dan panitia) dilindungi secara ketat oleh sistem dan hanya dapat diakses oleh pengguna dengan hak akses **Administrator**. Akun Panitia tidak diperkenankan melakukan reset data undian.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Card 4: Informasi Teknis Developer -->
        <div class="card card-secondary collapsed-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-code mr-2"></i>Informasi Teknis (Untuk Developer / Administrator IT)</h3>
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
                            <h5>Modul & Komponen Controller</h5>
                            <p>Lokasi file backend:</p>
                            <ul class="text-sm">
                                <li><strong>Undian & Reset</strong>: <code>app/Controllers/Backend/Luckydraw/LuckydrawUndian.php</code></li>
                                <li><strong>Barang</strong>: <code>app/Controllers/Backend/Luckydraw/LuckydrawBarang.php</code></li>
                                <li><strong>Kegiatan</strong>: <code>app/Controllers/Backend/Luckydraw/LuckydrawKegiatan.php</code></li>
                                <li><strong>Panitia</strong>: <code>app/Controllers/Backend/Luckydraw/LuckydrawPanitia.php</code></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-warning">
                            <h5>Struktur Basis Data (Database)</h5>
                            <p>Tabel-tabel MySQL yang digunakan:</p>
                            <ul class="text-sm">
                                <li><code>tbl_luckydraw_kegiatan</code> - Menyimpan konfigurasi kegiatan & rentang kupon.</li>
                                <li><code>tbl_luckydraw_barang</code> - Menyimpan daftar barang hadiah & jumlah stok.</li>
                                <li><code>tbl_luckydraw_undian</code> - Menyimpan daftar pemenang & status pengambilan.</li>
                                <li><code>tbl_luckydraw_user_kegiatan</code> - Menghubungkan user panitia ke kegiatan.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?= $this->endSection(); ?>
