<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-purple card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-book-reader mr-1"></i>
                            Dokumentasi Modul Munaqosah
                        </h3>
                    </div>
                    <div class="card-body">
                        <h1>Modul Ujian Munaqosah</h1>
                        <p class="lead">Sistem manajemen ujian akhir santri (Munaqosah) yang mencakup pengelolaan pra-munaqosah (internal) dan munaqosah umum (terpusat/FKPQ).</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-info"><i class="fas fa-tools"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Tahap 1</span>
                                        <span class="info-box-number">Setup & Konfigurasi</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-info" style="width: 100%"></div>
                                        </div>
                                        <span class="progress-description">
                                            Materi, Bobot, & Kriteria
                                        </span>
                                    </div>
                                    <a href="<?= base_url('backend/dokumentasi/munaqosah/setup') ?>" class="small-box-footer text-center p-1" style="z-index: 10; position: inherit; display: block; width: 100%; cursor: pointer;">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Tahap 2</span>
                                        <span class="info-box-number">Registrasi & Antrian</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-warning" style="width: 100%"></div>
                                        </div>
                                        <span class="progress-description">
                                            Pendaftaran, Validasi, Ruangan
                                        </span>
                                    </div>
                                     <a href="<?= base_url('backend/dokumentasi/munaqosah/registrasi') ?>" class="small-box-footer text-center p-1" style="z-index: 10; position: inherit; display: block; width: 100%; cursor: pointer;">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-success"><i class="fas fa-gavel"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Tahap 3</span>
                                        <span class="info-box-number">Penilaian & Hasil</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" style="width: 100%"></div>
                                        </div>
                                        <span class="progress-description">
                                            Juri, Scoring, Kelulusan
                                        </span>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <a href="<?= base_url('backend/dokumentasi/munaqosah/penilaian') ?>" class="small-box-footer text-center p-1 border-top" style="display: block; cursor: pointer;">
                                                Alur Juri <i class="fas fa-arrow-circle-right"></i>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="<?= base_url('backend/dokumentasi/munaqosah/kelulusan-public') ?>" class="small-box-footer text-center p-1 border-top border-left" style="display: block; cursor: pointer;">
                                                Cek Publik <i class="fas fa-globe"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        
                        <h3>Perbedaan Pra-Munaqosah vs Umum</h3>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Aspek</th>
                                    <th>Pra-Munaqosah (Internal)</th>
                                    <th>Munaqosah Umum (FKPQ)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Penyelenggara</strong></td>
                                    <td>Masing-masing TPQ</td>
                                    <td>FKPQ (Terpusat)</td>
                                </tr>
                                <tr>
                                    <td><strong>Bobot Nilai</strong></td>
                                    <td>Diatur Operator TPQ</td>
                                    <td>Diatur Admin Pusat</td>
                                </tr>
                                <tr>
                                    <td><strong>Peserta</strong></td>
                                    <td>Santri Internal</td>
                                    <td>Santri Lulusan Pra-Munaqosah</td>
                                </tr>
                                <tr>
                                    <td><strong>Tujuan</strong></td>
                                    <td>Simulasi & Syarat Daftar Umum</td>
                                    <td>Ujian Akhir & Sertifikasi Resmi</td>
                                </tr>
                            </tbody>
                        </table>

                        <hr>
                        
                        <h3>Diagram Alur Global</h3>
                        <div class="mermaid">
graph TD
    A[Start] --> B{Pilih Jenis Ujian}
    B -- Internal --> C[Pra-Munaqosah]
    B -- Pusat --> D[Munaqosah Umum]
    
    subgraph PROCESS [Proses Utama]
    C --> E[Setup Materi/Bobot]
    D --> E
    E --> F[Registrasi Peserta]
    F --> G[Pelaksanaan Ujian]
    G --> H[Penilaian Juri]
    H --> I[Hasil & Sertifikat]
    end
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?= $this->endSection(); ?>
