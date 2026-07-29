<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 fw-bold"><i class="fas fa-laptop-code text-success me-2"></i> Ujian Online MDTA (UMBK)</h4>
                <small class="text-muted">Sistem Pengelolaan Bank Soal, Jadwal Ujian, UMBK Santri, dan Raport MDTA</small>
            </div>
        </div>
    </div>

    <!-- Summary Widgets -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-outline card-success shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold">TOTAL PAKET SOAL</span>
                        <h2 class="mb-0 fw-bold text-success"><?= $totalPaket ?></h2>
                    </div>
                    <div class="bg-success text-white p-3 rounded-circle">
                        <i class="fas fa-layer-group fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 text-end">
                    <a href="<?= base_url('backend/ujian-mdta/paket') ?>" class="small text-success text-decoration-none fw-bold">Kelola Paket Soal <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-outline card-info shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold">TOTAL SOAL AKTIF</span>
                        <h2 class="mb-0 fw-bold text-info"><?= $totalSoal ?></h2>
                    </div>
                    <div class="bg-info text-white p-3 rounded-circle">
                        <i class="fas fa-list-ol fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 text-end">
                    <a href="<?= base_url('backend/ujian-mdta/paket') ?>" class="small text-info text-decoration-none fw-bold">Bank Soal <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-outline card-warning shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold">JADWAL UJIAN AKTIF</span>
                        <h2 class="mb-0 fw-bold text-warning"><?= $jadwalAktif ?></h2>
                    </div>
                    <div class="bg-warning text-dark p-3 rounded-circle">
                        <i class="fas fa-calendar-check fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 text-end">
                    <a href="<?= base_url('backend/ujian-mdta/jadwal') ?>" class="small text-warning text-decoration-none fw-bold">Lihat Jadwal <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Quick Access -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card card-outline card-primary shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0 fw-bold text-primary"><i class="fas fa-cogs me-2"></i> Pengelolaan Bank Soal</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Buat dan kelola paket soal per materi pelajaran dan kelas. Tentukan aturan pilihan ganda, tingkat kesulitan, serta uraian pertanyaan berformat rich text.</p>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('backend/ujian-mdta/paket/create') ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus-circle me-1"></i> Buat Paket Soal Baru</a>
                        <a href="<?= base_url('backend/ujian-mdta/paket') ?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-list me-1"></i> Daftar Paket Soal</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-outline card-success shadow-sm h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0 fw-bold text-success"><i class="fas fa-calendar-alt me-2"></i> Jadwal Ujian & Remedial</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Atur jadwal pelaksanaan ujian online per kelas, tentukan durasi, nilai KKM, serta buka sesi ujian remedial untuk santri yang nilainya di bawah KKM.</p>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('backend/ujian-mdta/jadwal/create') ?>" class="btn btn-success btn-sm"><i class="fas fa-calendar-plus me-1"></i> Buat Jadwal Ujian</a>
                        <a href="<?= base_url('backend/ujian-mdta/jadwal') ?>" class="btn btn-outline-success btn-sm"><i class="fas fa-calendar-check me-1"></i> Kelola Jadwal Ujian</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
