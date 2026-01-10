<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt mr-1"></i>
                            Dokumentasi Modul Perlombaan
                        </h3>
                    </div>
                    <div class="card-body">
                        <h1>Alur Modul Perlombaan</h1>
                        <p class="lead">Modul ini menangani pengelolaan lomba mulai dari persiapan, pendaftaran, hingga penentuan juara.</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-info"><i class="fas fa-cogs"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Fase 1</span>
                                        <span class="info-box-number">Persiapan (Setup)</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-info" style="width: 100%"></div>
                                        </div>
                                        <span class="progress-description">
                                            Buat Lomba, Cabang, Kriteria & Juri
                                        </span>
                                    </div>
                                    <a href="<?= base_url('backend/dokumentasi/perlombaan/setup') ?>" class="small-box-footer text-center p-1" style="z-index: 10; position: inherit; display: block; width: 100%; cursor: pointer;">
                                        Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Fase 2 & 3</span>
                                        <span class="info-box-number">Pendaftaran & Pelaksanaan</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" style="width: 100%"></div>
                                        </div>
                                        <span class="progress-description">
                                            Registrasi, Undian & Penilaian Juri
                                        </span>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                             <a href="<?= base_url('backend/dokumentasi/perlombaan/pelaksanaan') ?>" class="small-box-footer text-center p-1 border-top" style="display: block; cursor: pointer;">
                                                Alur Utama <i class="fas fa-arrow-circle-right"></i>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                             <a href="<?= base_url('backend/dokumentasi/perlombaan/juri') ?>" class="small-box-footer text-center p-1 border-top border-left" style="display: block; cursor: pointer;">
                                                Panduan Juri <i class="fas fa-gavel"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-trophy"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Fase 4</span>
                                        <span class="info-box-number">Finalisasi</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-warning" style="width: 100%"></div>
                                        </div>
                                        <span class="progress-description">
                                            Peringkat & Sertifikat
                                        </span>
                                    </div>
                                    <a href="<?= base_url('backend/dokumentasi/perlombaan/sertifikat') ?>" class="small-box-footer text-center p-1" style="z-index: 10; position: inherit; display: block; width: 100%; cursor: pointer;">
                                        Buat Sertifikat <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <hr>
                        
                        <h3>Diagram Alur Utama</h3>
                        <div class="mermaid">
graph TD
    subgraph SETUP [Persiapan]
    A[Buat Event Lomba] --> B[Buat Cabang Lomba]
    B --> C[Set Kriteria Nilai]
    B --> D[Buat Akun Juri]
    end

    subgraph REG [Pendaftaran]
    E[Operator Login] --> F[Pilih Santri]
    F --> G[Validasi Syarat]
    G --> H[Daftar Peserta]
    end

    subgraph EXEC [Pelaksanaan]
    H --> I[Pengundian Nomor]
    I --> J[Juri Input Nilai]
    J --> K[Kunci Nilai]
    end

    subgraph FINAL [Hasil]
    K --> L[Hitung Peringkat]
    L --> M[Cetak Sertifikat]
    end

    SETUP --> REG
    REG --> EXEC
    EXEC --> FINAL
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="module">
    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
    mermaid.initialize({ startOnLoad: true });
</script>
<?= $this->endSection(); ?>
