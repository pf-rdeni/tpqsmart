<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Berkas Lampiran Guru (Teacher Documents)</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <p>Dokumen ini menjelaskan fitur pengelolaan berkas lampiran guru termasuk KTP, KK, Buku Rekening, dan Foto Profil di aplikasi TPQ Smart.</p>

                <h4 class="mt-4">1. Akses Halaman</h4>
                <p>Halaman Berkas Lampiran dapat diakses melalui menu <strong>Guru → Berkas Lampiran</strong> atau URL <code>/backend/guru/showBerkasLampiran</code>.</p>
                <ul>
                    <li><strong>Admin</strong>: Dapat melihat dan mengelola berkas semua guru.</li>
                    <li><strong>Operator</strong>: Hanya dapat mengelola berkas guru di TPQ-nya sendiri.</li>
                </ul>

                <h4 class="mt-4">2. Jenis Berkas yang Dikelola</h4>
                <p>Sistem mendukung 4 jenis berkas:</p>
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Kolom</th>
                            <th>Jenis Berkas</th>
                            <th>Aspect Ratio</th>
                            <th>Lokasi Penyimpanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Profil</strong></td>
                            <td>Foto Profil Guru</td>
                            <td>3:4 (Portrait)</td>
                            <td><code>uploads/profil/user/</code></td>
                        </tr>
                        <tr>
                            <td><strong>KTP</strong></td>
                            <td>Kartu Tanda Penduduk</td>
                            <td>1.585 (ID Card)</td>
                            <td><code>uploads/berkas/</code></td>
                        </tr>
                        <tr>
                            <td><strong>KK</strong></td>
                            <td>Kartu Keluarga</td>
                            <td>1.414 (A4 Landscape)</td>
                            <td><code>uploads/berkas/</code></td>
                        </tr>
                        <tr>
                            <td><strong>Buku Rekening</strong></td>
                            <td>Rekening BPR / BRK</td>
                            <td>0.707 (A4 Portrait)</td>
                            <td><code>uploads/berkas/</code></td>
                        </tr>
                    </tbody>
                </table>

                <h4 class="mt-4">3. Fitur Utama</h4>
                <h5>3.1. Upload Berkas</h5>
                <ol>
                    <li>Klik tombol <span class="btn btn-sm btn-primary"><i class="fas fa-upload"></i> Upload</span> pada kolom yang diinginkan.</li>
                    <li>Pilih file gambar (JPG, PNG) atau PDF.</li>
                    <li>Modal crop akan terbuka dengan aspect ratio sesuai jenis berkas.</li>
                    <li>Gunakan tombol <strong>Putar Kiri/Kanan</strong> untuk memutar gambar jika perlu.</li>
                    <li>Klik <strong>Selesai</strong> untuk menyimpan hasil crop.</li>
                    <li>Klik <strong>Upload</strong> untuk mengirim ke server.</li>
                </ol>

                <h5>3.2. Edit Berkas</h5>
                <ol>
                    <li>Klik tombol <span class="btn btn-sm btn-warning p-1"><i class="fas fa-edit"></i></span> pada berkas yang ada.</li>
                    <li>Pilih file baru atau crop ulang gambar existing.</li>
                    <li>File lama akan otomatis dihapus setelah file baru berhasil disimpan.</li>
                </ol>

                <h5>3.3. Hapus Berkas</h5>
                <ol>
                    <li>Klik tombol <span class="btn btn-sm btn-danger p-1"><i class="fas fa-trash"></i></span> pada berkas yang ingin dihapus.</li>
                    <li>Konfirmasi penghapusan.</li>
                    <li>File fisik dan data di database akan dihapus.</li>
                </ol>

                <h4 class="mt-4">4. Foto Profil Guru</h4>
                <p>Foto profil guru memiliki perlakuan khusus dengan <strong>sinkronisasi 3 tabel</strong>:</p>
                <ul>
                    <li>Disimpan di <code>uploads/profil/user/</code> (sama dengan halaman Profil).</li>
                    <li>Saat upload/edit, sistem akan update ke <strong>3 tabel</strong>:
                        <ul>
                            <li><code>tbl_guru.LinkPhoto</code> - untuk tampilan di aplikasi guru.</li>
                            <li><code>users.user_image</code> - untuk tampilan di halaman profil user (jika guru memiliki akun user).</li>
                            <li><code>tbl_guru_berkas</code> - untuk backup dan tracking dengan NamaBerkas = 'Foto Profil'.</li>
                        </ul>
                    </li>
                    <li>Saat hapus, sistem akan <strong>menghapus</strong> dari semua 3 tabel.</li>
                    <li>Aspect ratio fixed 3:4 (300x400 pixel output).</li>
                    <li>Sinkronisasi 2 arah: upload dari halaman Berkas Lampiran atau halaman Profil akan memperbarui ketiga tabel.</li>
                </ul>

                <h4 class="mt-4">5. Diagram Alur</h4>
                <div class="mermaid text-center">
                    graph TD
                    A[User Buka Halaman Berkas Lampiran] --> B{Pilih Aksi}
                    B --> C[Upload Baru]
                    B --> D[Edit Existing]
                    B --> E[Hapus]
                    
                    C --> F[Pilih File]
                    F --> G[Modal Crop dengan Aspect Ratio]
                    G --> H[Rotate jika perlu]
                    H --> I[Klik Selesai]
                    I --> J[Preview Hasil Crop]
                    J --> K[Klik Upload]
                    K --> L[Simpan ke Server]
                    L --> M{Jenis Berkas?}
                    M -- Profil --> N[Update 3 tabel via Helper]
                    M -- KTP/KK/Rekening --> O[Insert/Update tbl_guru_berkas]
                    
                    D --> P[Ambil Data Existing via AJAX]
                    P --> Q[Tampilkan Modal Edit]
                    Q --> F
                    
                    E --> R[Konfirmasi Hapus]
                    R --> S[Hapus File Fisik]
                    S --> T[Hapus/Update Database]
                </div>

            </div>
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
                            <p>Controller: <code>app/Controllers/Backend/Guru.php</code></p>
                            <ul class="text-sm">
                                <li><code>showBerkasLampiran()</code> - Tampil halaman</li>
                                <li><code>uploadBerkas()</code> - Upload KTP/KK/Rekening</li>
                                <li><code>deleteBerkas($id)</code> - Hapus berkas</li>
                                <li><code>getBerkasById($id)</code> - Get data berkas</li>
                                <li><code>uploadProfilPhoto()</code> - Upload foto profil</li>
                                <li><code>deleteProfilPhoto($id)</code> - Hapus foto profil</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-warning" style="border-left-color: #ffc107;">
                            <h5>Database & Model</h5>
                            <p>Tables (Profil sync via helper ke 3 tabel):</p>
                            <ul class="text-sm">
                                <li><code>tbl_guru.LinkPhoto</code> - Foto profil guru</li>
                                <li><code>users.user_image</code> - Foto profil user</li>
                                <li><code>tbl_guru_berkas</code> - KTP, KK, Rekening + Foto Profil</li>
                            </ul>
                            <p>Models: <code>GuruModel</code>, <code>GuruBerkasModel</code>, <code>UserModel</code></p>
                            <p>Helper: <code>HelpFunctionModel</code></p>
                            <ul class="text-sm">
                                <li><code>saveGuruProfilPhoto()</code></li>
                                <li><code>deleteGuruProfilPhoto()</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="callout callout-success" style="border-left-color: #28a745;">
                            <h5>View File</h5>
                            <p><code>app/Views/backend/guru/berkasLampiran.php</code></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="callout callout-danger" style="border-left-color: #dc3545;">
                            <h5>Routes</h5>
                            <ul class="text-sm">
                                <li><code>GET backend/guru/showBerkasLampiran</code></li>
                                <li><code>POST backend/guru/uploadBerkas</code></li>
                                <li><code>POST backend/guru/deleteBerkas/(:num)</code></li>
                                <li><code>POST backend/guru/uploadProfilPhoto</code></li>
                                <li><code>POST backend/guru/deleteProfilPhoto/(:any)</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>
