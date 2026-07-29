<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 fw-bold"><i class="fas fa-redo text-warning me-2"></i> Form Pembuatan Jadwal Remedial</h4>
                <small class="text-muted">Membuat jadwal ujian remedial berdasarkan hasil ujian sebelumnya</small>
            </div>
            <a href="<?= base_url('backend/ujian-mdta/jadwal') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Jadwal
            </a>
        </div>
    </div>

    <!-- Info Jadwal Asal -->
    <div class="card card-outline card-warning shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0 text-dark fw-bold"><i class="fas fa-info-circle me-2 text-warning"></i>Jadwal Ujian Utama / Referensi</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td width="140" class="fw-bold">Nama Ujian Asal:</td><td><?= esc($jadwalAsal['NamaUjian']) ?></td></tr>
                        <tr><td class="fw-bold">Nilai Minimum:</td><td><span class="badge bg-warning text-dark"><?= $jadwalAsal['NilaiMinimum'] ?></span></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td width="140" class="fw-bold">Attempt Ujian:</td><td>Percobaan ke-<?= $jadwalAsal['AttemptKe'] ?></td></tr>
                        <tr><td class="fw-bold">Santri Perlu Remedial:</td><td><span class="badge bg-danger"><?= count($santriGagal) ?> Santri</span></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Jadwal Remedial -->
    <div class="card card-outline card-success shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0 text-success fw-bold"><i class="fas fa-calendar-plus me-2"></i>Pengaturan Waktu Remedial Baru</h5>
        </div>
        <div class="card-body">
            <form method="post" action="<?= base_url("backend/ujian-mdta/jadwal/remedial/save/{$jadwalAsal['id']}") ?>">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Waktu Mulai Remedial <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="TanggalMulai" class="form-control" required value="<?= date('Y-m-d\TH:i') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Waktu Selesai Remedial <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="TanggalSelesai" class="form-control" required value="<?= date('Y-m-d\TH:i', strtotime('+3 days')) ?>">
                    </div>
                </div>

                <!-- Daftar Santri Yang Mengikuti Remedial -->
                <div class="mt-4">
                    <h6 class="fw-bold text-danger mb-2"><i class="fas fa-users me-1"></i> Daftar Santri Yang Mengikuti Remedial Ini (Nilai < <?= $jadwalAsal['NilaiMinimum'] ?>):</h6>
                    <?php if (empty($santriGagal)): ?>
                        <div class="alert alert-info py-2 small mb-0">
                            <i class="fas fa-info-circle me-1"></i> Tidak ada santri yang nilainya di bawah treshold pada ujian ini. All passed!
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40" class="text-center">No</th>
                                        <th>ID Santri</th>
                                        <th>Nama Santri</th>
                                        <th class="text-center">Nilai Ujian Asal</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($santriGagal as $idx => $sg): ?>
                                        <tr>
                                            <td class="text-center"><?= $idx + 1 ?></td>
                                            <td><code><?= esc($sg['IdSantri']) ?></code></td>
                                            <td class="fw-bold"><?= esc($sg['NamaSantri'] ?? 'Santri ID ' . $sg['IdSantri']) ?></td>
                                            <td class="text-center text-danger fw-bold"><?= $sg['NilaiAkhir'] ?></td>
                                            <td class="text-center"><span class="badge bg-danger">Remedial Required</span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="<?= base_url('backend/ujian-mdta/jadwal') ?>" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-warning px-4 text-dark fw-bold">
                        <i class="fas fa-save me-1"></i> Terbitkan Jadwal Remedial
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
