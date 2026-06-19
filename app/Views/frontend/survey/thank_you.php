<?= $this->extend('frontend/survey/template/survey_template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card survey-public-card text-center p-5 shadow-lg" style="border-top: 6px solid #28a745;">
            <div class="card-body">
                <!-- Success icon -->
                <div class="thank-you-icon mb-4">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                
                <h1 class="font-weight-bold mb-3">Tanggapan Dikirim</h1>
                <p class="lead text-muted mb-4">
                    <?= esc($survey['confirmation_message'] ?? 'Tanggapan Anda telah berhasil disimpan. Terima kasih atas partisipasi Anda!') ?>
                </p>
                
                <hr class="my-4">

                <div class="d-flex flex-column align-items-center justify-content-center">
                    <?php if ($result_url): ?>
                        <a href="<?= $result_url ?>" class="btn btn-theme px-4 mb-2">
                            <i class="fas fa-chart-pie mr-2"></i> Lihat Hasil Survey
                        </a>
                    <?php endif; ?>

                    <?php if (!$survey['limit_one_response'] && $survey['unique_field_type'] === 'none'): ?>
                        <a href="<?= base_url('survey/' . $survey['survey_key']) ?>" class="btn btn-outline-secondary btn-sm mt-2">
                            Kirim tanggapan lain
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
