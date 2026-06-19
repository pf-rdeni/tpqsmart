<div class="modal-header bg-light">
    <h5 class="modal-title font-weight-bold" id="responseModalLabel">Detail Tanggapan: <?= esc($response['respondent_name'] ?? 'Anonim') ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
    <!-- Respondent Details Table -->
    <div class="card card-outline card-info shadow-xs mb-4">
        <div class="card-header py-1 px-3">
            <span class="text-info small font-weight-bold"><i class="fas fa-info-circle mr-1"></i> Metadata Responden</span>
        </div>
        <div class="card-body p-2">
            <table class="table table-sm table-borderless small mb-0">
                <tr>
                    <td class="font-weight-bold text-muted" style="width: 25%">Tipe Responden</td>
                    <td class="text-uppercase">: <?= esc($response['respondent_type']) ?></td>
                    <td class="font-weight-bold text-muted" style="width: 25%">IP Address</td>
                    <td>: <?= esc($response['ip_address'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="font-weight-bold text-muted">Lembaga TPQ</td>
                    <td>: <?= esc($response['NamaTpq'] ?? 'Lembaga Lain / Publik') ?></td>
                    <td class="font-weight-bold text-muted">Waktu Kirim</td>
                    <td>: <?= date('d/m/Y H:i:s', strtotime($response['submitted_at'])) ?></td>
                </tr>
                <tr>
                    <td class="font-weight-bold text-muted">Email</td>
                    <td>: <?= esc($response['respondent_email'] ?? '-') ?></td>
                    <td class="font-weight-bold text-muted">No HP / WhatsApp</td>
                    <td>: <?= esc($response['respondent_phone'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="font-weight-bold text-muted">User Agent</td>
                    <td colspan="3" class="text-truncate" style="max-width: 450px;" title="<?= esc($response['user_agent']) ?>">: <?= esc($response['user_agent'] ?? '-') ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Answers Loop -->
    <div class="answers-wrapper">
        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-question-circle mr-1 text-primary"></i> Daftar Jawaban Pertanyaan</h6>
        
        <?php foreach ($questions as $idx => $q): ?>
            <?php 
            if (in_array($q['question_type'], ['image_display', 'video_display'])) continue; 
            $key = 'q_' . $q['id'];
            $ans = $answers[$key] ?? null;
            ?>
            <div class="py-2 border-bottom">
                <div class="font-weight-bold text-dark mb-1 small ql-view"><?= $idx + 1 ?>. <?= $q['question_text'] ?></div>
                <div class="text-dark pl-3 font-weight-normal">
                    <?php if ($ans === null || $ans === '' || $ans === []): ?>
                        <em class="text-muted small">Tidak dijawab / Kosong</em>
                    <?php else: ?>
                        <?php if (is_array($ans)): ?>
                            <!-- For checkboxes -->
                            <?php foreach ($ans as $val): ?>
                                <span class="badge badge-light border px-2 py-1 mr-1"><?= esc($val) ?></span>
                            <?php endforeach; ?>
                        <?php elseif (is_object($ans) || (is_string($ans) && strpos($ans, '{') === 0)): ?>
                            <!-- For grid JSON answers or file upload details -->
                            <?php
                            $parsed = is_string($ans) ? json_decode($ans, true) : (array)$ans;
                            ?>
                            <?php if (isset($parsed['file_name']) && isset($parsed['file_path'])): ?>
                                <!-- File link -->
                                <a href="<?= base_url($parsed['file_path']) ?>" class="btn btn-xs btn-outline-info" target="_blank">
                                    <i class="fas fa-download mr-1"></i> Unduh File: <?= esc($parsed['file_name']) ?>
                                </a>
                            <?php elseif (isset($parsed['rows'])): ?>
                                <!-- Grid row-col layout mapping -->
                                <ul class="pl-3 mb-0 small">
                                    <?php foreach ($parsed['rows'] as $row => $col): ?>
                                        <li><strong><?= esc($row) ?>:</strong> <?= is_array($col) ? implode(', ', $col) : esc($col) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <pre class="small text-muted mb-0"><?= json_encode($parsed, JSON_PRETTY_PRINT) ?></pre>
                            <?php endif; ?>
                        <?php elseif ($q['question_type'] === 'rating'): ?>
                            <!-- Display rating stars -->
                            <?php for ($i = 1; $i <= (int)($q['settings']['max_stars'] ?? 5); $i++): ?>
                                <i class="fa<?= $i <= (int)$ans ? 's' : 'r' ?> fa-star text-warning"></i>
                            <?php endfor; ?>
                            <span class="small font-weight-bold text-muted ml-2">(<?= $ans ?> Bintang)</span>
                        <?php else: ?>
                            <!-- Default short / long text / dropdown -->
                            <?= nl2br(esc($ans)) ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<div class="modal-footer bg-light py-2">
    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
</div>
