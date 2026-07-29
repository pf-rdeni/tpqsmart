<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12 text-center">
            <h4 class="mb-0 fw-bold"><i class="fas fa-poll text-success me-2"></i> Hasil & Evaluasi Ujian MDTA</h4>
            <small class="text-muted"><?= esc($jadwal['NamaUjian']) ?></small>
        </div>
    </div>

    <!-- Card Result Ringkasan -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-8 col-lg-6">
            <div class="card card-outline <?= $isLulus ? 'card-success' : 'card-danger' ?> shadow-sm text-center">
                <div class="card-body p-4">
                    <div class="mb-2">
                        <?php if ($isLulus): ?>
                            <i class="fas fa-check-circle text-success fa-4x mb-2"></i>
                            <h3 class="fw-bold text-success">SELAMAT! ANDA LULUS</h3>
                            <p class="text-muted small">Nilai Anda mencapai / melampaui standar nilai KKM (<strong><?= $jadwal['NilaiMinimum'] ?></strong>)</p>
                        <?php else: ?>
                            <i class="fas fa-times-circle text-danger fa-4x mb-2"></i>
                            <h3 class="fw-bold text-danger">BELUM LULUS</h3>
                            <p class="text-muted small">Nilai Anda belum mencapai standar nilai KKM (<strong><?= $jadwal['NilaiMinimum'] ?></strong>)</p>
                        <?php endif; ?>
                    </div>

                    <!-- Nilai Besar -->
                    <div class="bg-light rounded p-3 mb-3 border">
                        <span class="text-muted small fw-bold d-block">NILAI AKHIR UJIAN</span>
                        <h1 class="display-3 fw-bold my-1 <?= $isLulus ? 'text-success' : 'text-danger' ?>">
                            <?= number_format($sesi['NilaiAkhir'], 2) ?>
                        </h1>
                        <span class="badge bg-secondary">Skala Maksimum: <?= $paket['SkalaNilai'] ?? 100 ?></span>
                    </div>

                    <!-- Rincian Jawaban -->
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="border rounded p-2 bg-success-subtle border-success">
                                <span class="d-block small text-muted">Jawaban Benar</span>
                                <h4 class="fw-bold text-success mb-0"><i class="fas fa-check me-1"></i><?= $jumlahBenar ?> Soal</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 bg-danger-subtle border-danger">
                                <span class="d-block small text-muted">Jawaban Salah / Kosong</span>
                                <h4 class="fw-bold text-danger mb-0"><i class="fas fa-times me-1"></i><?= $jumlahSalah ?> Soal</h4>
                            </div>
                        </div>
                    </div>

                    <?php if (!$isLulus && $jadwal['BolehRemedial']): ?>
                        <div class="alert alert-warning mt-3 mb-0 small border-start border-4 border-warning text-start">
                            <i class="fas fa-info-circle me-1 text-warning"></i> <strong>Informasi Remedial:</strong><br>
                            Operator / Ustadz akan menjadwalkan ujian remedial untuk Anda. Silakan cek halaman daftar ujian secara berkala.
                        </div>
                    <?php endif; ?>

                    <div class="mt-4">
                        <a href="<?= base_url('backend/ujian-mdta/santri') ?>" class="btn btn-primary px-4">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Ujian
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Evaluasi Pembahasan Per Soal (Disembunyikan jika TampilSoalJawaban = 0) -->
    <?php if (!isset($jadwal['TampilSoalJawaban']) || (int)$jadwal['TampilSoalJawaban'] === 1): ?>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card card-outline card-info shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0 text-info fw-bold"><i class="fas fa-list-check me-2"></i>Rincian Soal & Pembahasan</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $tampilKunci = isset($jadwal['TampilKunciJawaban']) ? ((int)$jadwal['TampilKunciJawaban'] === 1) : true;
                        ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($detail as $idx => $d): ?>
                                <?php
                                $isEsai = ($d['JenisSoal'] ?? 'pilihan_ganda') === 'esai';
                                ?>
                                <div class="card border <?= $d['IsBenar'] == 1 ? 'border-success-subtle' : ($isEsai ? 'border-primary-subtle' : 'border-danger-subtle') ?>">
                                    <div class="card-header py-2 d-flex justify-content-between align-items-center <?= $d['IsBenar'] == 1 ? 'bg-success-subtle' : ($isEsai ? 'bg-light' : 'bg-danger-subtle') ?>">
                                        <strong class="text-dark">
                                            <i class="<?= $isEsai ? 'fas fa-pen-fancy text-primary' : 'fas fa-list-ul text-secondary' ?> me-1"></i>
                                            Soal No. <?= $d['NomorSoal'] ?> <?= $isEsai ? '(Uraian / Esai)' : '(Pilihan Ganda)' ?>
                                        </strong>
                                        <?php if ($isEsai): ?>
                                            <?php if ($d['NilaiEsai'] !== null): ?>
                                                <span class="badge bg-primary text-white fs-6"><i class="fas fa-star me-1"></i> Nilai Esai: <?= number_format((float)$d['NilaiEsai'], 1) ?> / 100</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark fs-6"><i class="fas fa-hourglass-half me-1"></i> Menunggu Koreksi Pengawas</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if ($d['IsBenar'] == 1): ?>
                                                <span class="badge bg-success"><i class="fas fa-check me-1"></i> Benar</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger"><i class="fas fa-times me-1"></i> Salah / Kosong</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3 fs-6"><?= $d['UraianSoal'] ?></div>
                                        <div class="small bg-light p-3 rounded mb-3 border">
                                            <?php if ($isEsai): ?>
                                                <div class="fw-bold text-muted mb-1"><i class="fas fa-comment-alt me-1 text-primary"></i> Jawaban Uraian Anda:</div>
                                                <div class="p-2.5 bg-white rounded border text-dark fs-6 leading-relaxed" style="white-space: pre-wrap;"><?= !empty($d['JawabanEsai']) ? esc($d['JawabanEsai']) : '<em class="text-muted">Tidak Dijawab / Kosong</em>' ?></div>
                                            <?php else: ?>
                                                <div><strong>Jawaban Anda:</strong> <span class="<?= $d['IsBenar'] == 1 ? 'text-success fw-bold' : 'text-danger fw-bold' ?>"><?= $d['JawabanDipilih'] ?? 'Tidak Dijawab' ?></span></div>
                                                <?php if ($tampilKunci): ?>
                                                    <div><strong>Kunci Jawaban Benar:</strong> <span class="text-success fw-bold"><?= $d['JawabanBenar'] ?></span></div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($tampilKunci && !empty($d['Pembahasan'])): ?>
                                            <div class="pembahasan bg-warning-subtle p-3 rounded border-start border-4 border-warning small">
                                                <strong class="text-warning-emphasis"><i class="fas fa-lightbulb text-warning me-1"></i> Kunci / Pembahasan Soal:</strong>
                                                <div class="mt-1 text-dark fs-6"><?= $d['Pembahasan'] ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    </div>
</div>

<?= $this->endSection(); ?>
