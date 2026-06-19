<?= $this->extend('frontend/survey/template/survey_template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card survey-public-card text-center p-5 shadow-lg" style="border-top: 6px solid #e53e3e;">
            <div class="card-body">
                <!-- Icon based on reason -->
                <div class="closed-icon mb-4">
                    <?php if ($reason === 'not_found'): ?>
                        <i class="fas fa-search text-muted"></i>
                    <?php elseif ($reason === 'already_submitted'): ?>
                        <i class="fas fa-user-check text-warning"></i>
                    <?php elseif ($reason === 'quota_full'): ?>
                        <i class="fas fa-users-slash text-danger"></i>
                    <?php elseif ($reason === 'result_disabled'): ?>
                        <i class="fas fa-lock text-muted"></i>
                    <?php else: ?>
                        <i class="fas fa-lock text-danger"></i>
                    <?php endif; ?>
                </div>
                
                <h2 class="font-weight-bold mb-3"><?= esc($page_title) ?></h2>
                <p class="lead text-muted mb-4">
                    <?= esc($message) ?>
                </p>

                <?php if ($reason === 'already_submitted' && !empty($result_url)): ?>
                    <a href="<?= esc($result_url) ?>" class="btn btn-theme px-4 mb-2">
                        <i class="fas fa-chart-pie mr-2"></i> Lihat Hasil Survey
                    </a>
                <?php endif; ?>
                
                <hr class="my-4">
                
                <a href="<?= base_url() ?>" class="btn btn-theme-outline btn-sm">
                    Kembali ke Halaman Utama
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
