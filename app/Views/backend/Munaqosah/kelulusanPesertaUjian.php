<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<?php
$peserta = $peserta ?? [];
$categoryDetails = $categoryDetails ?? [];
$meta = $meta ?? [];
$kesalahanMap = $kesalahanMap ?? [];

// Helper untuk format catatan munaqosah yang rapi
$formatCatatan = function ($rawCatatan, $kesalahanMap) {
    if (empty($rawCatatan) || trim($rawCatatan) === '') {
        return '<span class="text-muted">-</span>';
    }

    $rawCatatan = trim($rawCatatan);
    $decoded = json_decode($rawCatatan, true);

    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $html = [];
        $kategoriCodes = [];
        if (!empty($decoded['Kategori'])) {
            if (is_array($decoded['Kategori'])) {
                $kategoriCodes = $decoded['Kategori'];
            } else {
                $kategoriCodes = explode(',', (string)$decoded['Kategori']);
            }
        }

        $kategoriCodes = array_filter(array_map('trim', $kategoriCodes));
        if (!empty($kategoriCodes)) {
            $badges = [];
            foreach ($kategoriCodes as $code) {
                $label = $kesalahanMap[$code] ?? $code;
                $badges[] = '<span class="badge badge-danger-soft mr-1 mb-1" title="' . esc($code) . '"><i class="fas fa-exclamation-circle mr-1"></i>' . esc($label) . '</span>';
            }
            $html[] = '<div class="d-flex flex-wrap align-items-center">' . implode('', $badges) . '</div>';
        }

        if (!empty($decoded['Catatan']) && trim($decoded['Catatan']) !== '') {
            $html[] = '<div class="catatan-text mt-1"><i class="far fa-comment-dots text-muted mr-1"></i>' . nl2br(esc(trim($decoded['Catatan']))) . '</div>';
        }

        if (empty($html)) {
            return '<span class="text-muted">-</span>';
        }

        return implode('', $html);
    }

    return nl2br(esc($rawCatatan));
};
?>
<style>
    .card-detail {
        border-radius: 12px;
        box-shadow: 0 4px 18px rgba(30, 60, 120, 0.08);
        border: none;
    }

    .info-stat-card {
        border-radius: 10px;
        padding: 16px;
        position: relative;
        overflow: hidden;
        margin-bottom: 16px;
        border: 1px solid #e2e8f0;
        background: #fff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .info-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }

    .info-stat-card .icon-bg {
        position: absolute;
        right: 14px;
        bottom: 10px;
        font-size: 2.6rem;
        opacity: 0.12;
    }

    .info-stat-card .stat-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .info-stat-card .stat-value {
        font-size: 1.35rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 4px;
    }

    .info-stat-card .stat-sub {
        font-size: 0.82rem;
        color: #718096;
    }

    .table-nilai-detail {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-nilai-detail thead th {
        background-color: #f1f5f9;
        color: #334155;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border-bottom: 2px solid #cbd5e1;
        border-top: 1px solid #cbd5e1;
        vertical-align: middle;
        padding: 10px 12px;
    }

    .table-nilai-detail tbody td {
        vertical-align: middle;
        padding: 10px 12px;
        font-size: 0.9rem;
        border-color: #e2e8f0;
    }

    .table-nilai-detail tbody tr:hover {
        background-color: #f8fafc;
    }

    .badge-danger-soft {
        background-color: #fee2e2;
        color: #dc2626;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #fecaca;
    }

    .badge-status-lulus {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
        font-size: 0.85rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 700;
        display: inline-block;
    }

    .badge-status-belum {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        font-size: 0.85rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 700;
        display: inline-block;
    }

    .catatan-text {
        font-size: 0.82rem;
        color: #475569;
        background: #f8fafc;
        padding: 4px 8px;
        border-radius: 4px;
        border-left: 3px solid #cbd5e1;
    }

    .score-badge {
        font-weight: 700;
        font-size: 0.95rem;
    }

    .score-zero {
        color: #dc2626;
        background: #fee2e2;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.82rem;
    }

    .materi-item {
        margin-bottom: 2px;
        line-height: 1.35;
    }

    .materi-item a {
        color: #2563eb;
        text-decoration: underline;
        font-weight: 600;
    }
</style>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-detail">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-graduation-cap text-primary fa-2x mr-3"></i>
                            <div>
                                <h4 class="card-title font-weight-bold mb-0 text-dark">Detail Kelulusan Peserta</h4>
                                <div class="text-muted small">Rekapitulasi lengkap hasil penilaian juri dan kelulusan santri</div>
                            </div>
                        </div>
                        <div>
                            <a href="<?= base_url('backend/munaqosah/kelulusan') ?>" class="btn btn-sm btn-outline-secondary mr-2">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                            <?php if (!empty($peserta['NoPeserta'])): ?>
                                <?php
                                $query = http_build_query([
                                    'NoPeserta' => $peserta['NoPeserta'],
                                    'IdTahunAjaran' => $peserta['IdTahunAjaran'] ?? '',
                                    'TypeUjian' => $peserta['TypeUjian'] ?? '',
                                    'IdTpq' => $peserta['IdTpq'] ?? ''
                                ]);
                                ?>
                                <a href="<?= base_url('backend/munaqosah/printKelulusanPesertaUjian') . '?' . $query ?>" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-print mr-1"></i> Cetak PDF
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- STAT CARDS -->
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="info-stat-card border-left-primary" style="border-left: 4px solid #3b82f6;">
                                    <div class="stat-label text-primary">Peserta Ujian</div>
                                    <div class="stat-value text-dark"><?= esc($peserta['NamaSantri'] ?? '-') ?></div>
                                    <div class="stat-sub">
                                        <i class="fas fa-id-badge text-muted mr-1"></i> <strong>No. Peserta:</strong> <?= esc($peserta['NoPeserta'] ?? '-') ?>
                                        <br>
                                        <i class="fas fa-fingerprint text-muted mr-1"></i> <strong>ID Santri:</strong> <?= esc($peserta['IdSantri'] ?? '-') ?>
                                    </div>
                                    <i class="fas fa-user-graduate icon-bg text-primary"></i>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="info-stat-card border-left-info" style="border-left: 4px solid #06b6d4;">
                                    <div class="stat-label text-info">Lembaga &amp; Ujian</div>
                                    <div class="stat-value text-dark"><?= esc($peserta['NamaTpq'] ?? '-') ?></div>
                                    <div class="stat-sub">
                                        <i class="fas fa-calendar-alt text-muted mr-1"></i> <strong>Tahun Ajaran:</strong> <?= esc($peserta['IdTahunAjaran'] ?? '-') ?>
                                        <br>
                                        <i class="fas fa-tasks text-muted mr-1"></i> <strong>Type Ujian:</strong> <span class="badge badge-light border text-uppercase"><?= esc($peserta['TypeUjian'] ?? '-') ?></span>
                                    </div>
                                    <i class="fas fa-school icon-bg text-info"></i>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-12">
                                <?php $isLulus = !empty($peserta['KelulusanMet']); ?>
                                <div class="info-stat-card" style="border-left: 4px solid <?= $isLulus ? '#22c55e' : '#ef4444' ?>;">
                                    <div class="stat-label <?= $isLulus ? 'text-success' : 'text-danger' ?>">Status Kelulusan</div>
                                    <div class="stat-value">
                                        <span class="<?= $isLulus ? 'badge-status-lulus' : 'badge-status-belum' ?>">
                                            <i class="fas <?= $isLulus ? 'fa-check-circle' : 'fa-times-circle' ?> mr-1"></i>
                                            <?= esc($peserta['KelulusanStatus'] ?? '-') ?>
                                        </span>
                                    </div>
                                    <div class="stat-sub">
                                        <strong>Total Nilai Bobot:</strong> <span class="font-weight-bold text-dark"><?= number_format((float)($peserta['TotalWeighted'] ?? 0), 2) ?></span>
                                        <br>
                                        <strong>Standar Kelulusan:</strong> <?= number_format((float)($peserta['KelulusanThreshold'] ?? 0), 2) ?>
                                        (Selisih: <span class="<?= ((float)($peserta['KelulusanDifference'] ?? 0) >= 0) ? 'text-success font-weight-bold' : 'text-danger font-weight-bold' ?>"><?= number_format((float)($peserta['KelulusanDifference'] ?? 0), 2) ?></span>)
                                    </div>
                                    <i class="fas <?= $isLulus ? 'fa-award' : 'fa-clipboard-check' ?> icon-bg <?= $isLulus ? 'text-success' : 'text-danger' ?>"></i>
                                </div>
                            </div>
                        </div>

                        <!-- TABEL DETAIL PENILAIAN -->
                        <div class="table-responsive mt-3 border rounded">
                            <table class="table table-bordered table-hover table-nilai-detail">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" rowspan="2" style="width: 18%;">Kategori</th>
                                        <th class="text-center align-middle" rowspan="2" style="width: 25%;">Materi Ujian</th>
                                        <th class="text-center" colspan="3" style="width: 37%;">Penilaian Juri</th>
                                        <th class="text-center align-middle" rowspan="2" style="width: 10%;">Rata-rata</th>
                                        <th class="text-center align-middle" rowspan="2" style="width: 10%;">Bobot Nilai</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="width: 90px;">Juri</th>
                                        <th class="text-center" style="width: 85px;">Nilai</th>
                                        <th class="text-center">Catatan &amp; Kesalahan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $totalNilai = 0;
                                    $totalBobot = 0;
                                    ?>
                                    <?php if (!empty($categoryDetails)): ?>
                                        <?php foreach ($categoryDetails as $catId => $detail): ?>
                                            <?php
                                            $category = $detail['category'] ?? [];
                                            $juriScores = $detail['juri_scores'] ?? [];
                                            $materis = $detail['materi'] ?? [];
                                            $average = (float)($detail['average'] ?? 0);
                                            $weighted = (float)($detail['weighted'] ?? 0);
                                            $totalNilai += $average;
                                            $totalBobot += $weighted;

                                            // Daftar materi tanpa duplikasi
                                            $materiHtmlList = [];
                                            foreach ($materis as $materi) {
                                                $mName = esc($materi['NamaMateri'] ?? '-');
                                                if (!empty($materi['WebLinkAyat'])) {
                                                    $mName .= ' <a href="' . esc($materi['WebLinkAyat']) . '" target="_blank" class="ml-1" title="Buka Ayat"><i class="fas fa-external-link-alt fa-xs"></i> link</a>';
                                                }
                                                $materiHtmlList[] = '<div class="materi-item">' . $mName . '</div>';
                                            }
                                            if (empty($materiHtmlList)) {
                                                $materiHtmlList[] = '<span class="text-muted">-</span>';
                                            }

                                            // Hitung rowspan presisi berdasarkan jumlah juri yang ada
                                            $rowCount = !empty($juriScores) ? count($juriScores) : 1;
                                            ?>
                                            <?php if (!empty($juriScores)): ?>
                                                <?php foreach ($juriScores as $idx => $score): ?>
                                                    <tr>
                                                        <?php if ($idx === 0): ?>
                                                            <td class="align-middle font-weight-bold text-dark" rowspan="<?= $rowCount ?>">
                                                                <?= esc($category['name'] ?? '-') ?>
                                                                <?php if (!empty($category['weight'])): ?>
                                                                    <div class="small text-muted font-weight-normal mt-1">Bobot: <?= (float)$category['weight'] ?>%</div>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="align-middle" rowspan="<?= $rowCount ?>">
                                                                <?= implode('', $materiHtmlList) ?>
                                                            </td>
                                                        <?php endif; ?>

                                                        <!-- Kolom Penilaian Juri -->
                                                        <td class="text-center align-middle font-weight-bold text-secondary">
                                                            <?= esc($score['label'] ?? ('Juri ' . ($idx + 1))) ?>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <?php $nScore = (float)($score['Nilai'] ?? 0); ?>
                                                            <?php if ($nScore > 0): ?>
                                                                <span class="score-badge text-dark"><?= number_format($nScore, 2) ?></span>
                                                            <?php else: ?>
                                                                <span class="score-zero font-weight-bold">0.00</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="align-middle">
                                                            <?= $formatCatatan($score['Catatan'] ?? '', $kesalahanMap) ?>
                                                        </td>

                                                        <?php if ($idx === 0): ?>
                                                            <td class="align-middle text-center font-weight-bold" rowspan="<?= $rowCount ?>">
                                                                <?php if ($average > 0): ?>
                                                                    <span class="badge badge-info px-2 py-1" style="font-size:0.95rem; font-weight:700;"><?= number_format($average, 2) ?></span>
                                                                <?php else: ?>
                                                                    <span class="badge badge-secondary px-2 py-1" style="font-size:0.95rem;">0.00</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="align-middle text-center font-weight-bold" rowspan="<?= $rowCount ?>">
                                                                <?php if ($weighted > 0): ?>
                                                                    <span class="text-primary" style="font-size:1rem;"><?= number_format($weighted, 2) ?></span>
                                                                <?php else: ?>
                                                                    <span class="text-muted">0.00</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        <?php endif; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <!-- Baris jika belum ada juri -->
                                                <tr>
                                                    <td class="align-middle font-weight-bold text-dark">
                                                        <?= esc($category['name'] ?? '-') ?>
                                                        <?php if (!empty($category['weight'])): ?>
                                                            <div class="small text-muted font-weight-normal mt-1">Bobot: <?= (float)$category['weight'] ?>%</div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?= implode('', $materiHtmlList) ?>
                                                    </td>
                                                    <td class="text-center text-muted align-middle" colspan="3">
                                                        <em>Belum ada penilaian juri</em>
                                                    </td>
                                                    <td class="align-middle text-center text-muted">0.00</td>
                                                    <td class="align-middle text-center text-muted">0.00</td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="fas fa-info-circle mr-1"></i> Data rincian kategori ujian tidak ditemukan.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light" style="font-size: 1rem; border-top: 2px solid #cbd5e1;">
                                        <td colspan="2" class="text-right font-weight-bold text-dark py-3">JUMLAH KESELURUHAN</td>
                                        <td colspan="3" class="bg-light"></td>
                                        <td class="text-center font-weight-bold text-dark py-3">
                                            <span class="badge badge-dark px-2 py-1" style="font-size: 1rem;"><?= number_format($totalNilai, 2) ?></span>
                                        </td>
                                        <td class="text-center font-weight-bold text-primary py-3" style="font-size: 1.15rem;">
                                            <?= number_format($totalBobot, 2) ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- INFORMASI FOOTER -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light border-0" style="border-radius: 8px;">
                                    <div class="card-body py-3">
                                        <div class="row text-muted small">
                                            <div class="col-md-4">
                                                <i class="fas fa-database mr-1"></i> <strong>Sumber Bobot:</strong> <?= esc($meta['bobot_source'] ?? '-') ?>
                                            </div>
                                            <div class="col-md-4">
                                                <i class="fas fa-sliders-h mr-1"></i> <strong>Ambang Kelulusan TPQ:</strong> <?= number_format((float)($peserta['KelulusanThreshold'] ?? 0), 2) ?>
                                            </div>
                                            <div class="col-md-4">
                                                <i class="fas fa-info-circle mr-1"></i> <strong>Keterangan:</strong> Nilai akhir dihitung dari akumulasi rata-rata nilai juri per materi dikali bobot kategori.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>