<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-gavel mr-1"></i>
                    Panduan Halaman Juri
                </h3>
            </div>
            <div class="card-body">
                <p>Halaman ini khusus digunakan oleh Dewan Juri untuk memberikan penilaian pada saat lomba berlangsung.</p>
                
                <hr>

                <h4>1. Login Juri</h4>
                <ul>
                    <li>Akses halaman login perlombaan (biasanya berbeda dengan login admin, atau gunakan login standar).</li>
                    <li>Username: <code>Juri[Cabang]_[Urutan]</code> (Diberikan oleh Panitia).</li>
                    <li>Password: Sesuai yang diatur panitia.</li>
                </ul>

                <h4>2. Dashboard Penilaian</h4>
                <ul>
                    <li>Setelah login, Juri akan melihat daftar Cabang Lomba yang ditugaskan kepadanya.</li>
                    <li>Klik tombol <strong>Nilai</strong> pada cabang yang sedang berlangsung.</li>
                </ul>

                <h4>3. Form Input Nilai</h4>
                <ul>
                    <li>Juri akan melihat daftar peserta yang <strong>sudah melakukan pengundian</strong>.</li>
                    <li>Peserta diurutkan berdasarkan Nomor Tampil / Nomor Undian.</li>
                    <li>Klik tombol <strong>Input</strong> pada nomor peserta yang tampil.</li>
                    <li>Isi nilai untuk setiap kriteria (misal: Tajwid 0-40, Lagu 0-30).</li>
                    <li>Total Nilai akan terhitung otomatis.</li>
                    <li>Klik <strong>Simpan</strong>.</li>
                </ul>

                <div class="callout callout-danger">
                    <h5><i class="fas fa-lock"></i> Fitur Kunci Nilai (Lock)</h5>
                    <p>Jika penilaian sudah final dan tidak ingin diubah lagi, Juri dapat menekan tombol <strong>Kunci Nilai</strong> (jika tersedia) atau Admin yang akan mengunci dari panel kontrol.</p>
                </div>

                <hr>
                
                <h5>Alur Kerja Juri</h5>
                <div class="mermaid">
stateDiagram-v2
    [*] --> Login
    Login --> Pilih_Cabang
    Pilih_Cabang --> List_Peserta
    
    state List_Peserta {
        [*] --> Tunggu_Peserta_Tampil
        Tunggu_Peserta_Tampil --> Pilih_No_Undian
        Pilih_No_Undian --> Input_Skor
        Input_Skor --> Simpan
        Simpan --> Tunggu_Peserta_Tampil: Peserta Berikutnya
    }

    List_Peserta --> Selesai_Semua
    Selesai_Semua --> Kunci_Nilai
    Kunci_Nilai --> [*]
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
                                    <p>Management: <code>Backend/Perlombaan.php</code></p>
                                    <ul class="text-sm">
                                        <li><code>storeJuri()</code> creates User in <code>users</code> table.</li>
                                        <li><code>group_id: 9</code> (JuriLomba Role)</li>
                                    </ul>
                                    <p>Login Flow: Standard <code>AuthController</code></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="callout callout-warning" style="border-left-color: #ffc107;">
                                    <h5>Database & Model</h5>
                                    <p>Tables:</p>
                                    <ul class="text-sm">
                                        <li><code>users</code> (Auth Credential)</li>
                                        <li><code>tbl_lomba_juri</code> (Assignment to Cabang)</li>
                                        <li><code>tbl_lomba_nilai</code> (Score Storage)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <a href="<?= base_url('backend/dokumentasi/perlombaan/pelaksanaan') ?>" class="btn btn-default"><i class="fas fa-arrow-left"></i> Kembali: Pelaksanaan</a>
                <a href="<?= base_url('backend/dokumentasi/perlombaan') ?>" class="btn btn-default float-right">Ke Menu Utama <i class="fas fa-home"></i></a>
            </div>
        </div>
    </div>
</section>


<?= $this->endSection(); ?>
