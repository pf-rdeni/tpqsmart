<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<?php
$peserta = $peserta ?? [];
$categoryDetails = $categoryDetails ?? [];
$meta = $meta ?? [];
?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Detail Kelulusan Peserta</h3>
                        <div>
                            <a href="<?= base_url('backend/munaqosah/kelulusan') ?>" class="btn btn-sm btn-secondary mr-1"><i class="fas fa-arrow-left"></i> Kembali</a>
                            <?php if (!empty($peserta['NoPeserta'])): ?>
                                <?php
                                $query = http_build_query([
                                    'NoPeserta' => $peserta['NoPeserta'],
                                    'IdTahunAjaran' => $peserta['IdTahunAjaran'] ?? '',
                                    'TypeUjian' => $peserta['TypeUjian'] ?? '',
                                    'IdTpq' => $peserta['IdTpq'] ?? ''
                                ]);
                                ?>
                                <a href="<?= base_url('backend/munaqosah/printKelulusanPesertaUjian') . '?' . $query ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-file-pdf"></i> Cetak PDF</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon"><i class="fas fa-id-card"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">No Peserta</span>
                                        <span class="info-box-number"><?= esc($peserta['NoPeserta'] ?? '-') ?></span>
                                        <span class="info-box-subtext">Nama: <?= esc($peserta['NamaSantri'] ?? '-') ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-light">
                                    <span class="info-box-icon"><i class="fas fa-school"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">TPQ &amp; Tahun</span>
                                        <span class="info-box-number"><?= esc($peserta['NamaTpq'] ?? '-') ?></span>
                                        <span class="info-box-subtext">Tahun Ajaran: <?= esc($peserta['IdTahunAjaran'] ?? '-') ?> | Type: <?= esc($peserta['TypeUjian'] ?? '-') ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box <?= !empty($peserta['KelulusanMet']) ? 'bg-success' : 'bg-danger' ?>">
                                    <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Status Kelulusan</span>
                                        <span class="info-box-number"><?= esc($peserta['KelulusanStatus'] ?? '-') ?></span>
                                        <span class="info-box-subtext">Total Bobot: <?= number_format((float)($peserta['TotalWeighted'] ?? 0), 2) ?> | Threshold: <?= number_format((float)($peserta['KelulusanThreshold'] ?? 0), 2) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="align-middle" rowspan="2">Kategori</th>
                                        <th class="align-middle text-center" rowspan="2">Materi Ujian</th>
                                        <th class="text-center" colspan="3">Penilaian Juri</th>
                                        <th class="align-middle text-center" rowspan="2">Rata</th>
                                        <th class="align-middle text-center" rowspan="2">Bobot Nilai</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">Juri</th>
                                        <th class="text-center">Nilai</th>
                                        <th class="text-center">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $totalNilai = 0;
                                    $totalBobot = 0;
                                    ?>
                                    <?php foreach ($categoryDetails as $detail): ?>
                                        <?php
                                        $category = $detail['category'] ?? [];
                                        $juriScores = $detail['juri_scores'] ?? [];
                                        $materis = $detail['materi'] ?? [];
                                        $weight = $category['weight'] ?? 0;
                                        $maxJuri = ($category['maxJuri'] ?? null);
                                        if ($maxJuri === null) {
                                            $maxJuri = max(count($juriScores), 1);
                                        } else {
                                            $maxJuri = (int)$maxJuri;
                                        }

                                        // Hitung nilai valid (nilai yang > 0)
                                        $validScores = array_filter($juriScores, function ($score) {
                                            return isset($score['Nilai']) && (float)$score['Nilai'] > 0;
                                        });
                                        $validScores = array_values($validScores);

                                        // Pastikan average konsisten: jika hanya ada satu nilai valid, gunakan nilai tersebut
                                        $average = (float)($detail['average'] ?? 0);
                                        if (count($validScores) === 1 && $average !== (float)$validScores[0]['Nilai']) {
                                            $average = (float)$validScores[0]['Nilai'];
                                        }

                                        $weighted = (float)($detail['weighted'] ?? 0);
                                        $totalNilai += $average;
                                        $totalBobot += $weighted;

                                        $materiList = [];
                                        foreach ($materis as $materi) {
                                            $materiName = $materi['NamaMateri'] ?? '-';
                                            if (!empty($materi['WebLinkAyat'])) {
                                                $materiName .= ' <small><a href="' . esc($materi['WebLinkAyat']) . '" target="_blank">link</a></small>';
                                            }
                                            $materiList[] = $materiName;
                                        }
                                        if (empty($materiList)) {
                                            $materiList[] = '-';
                                        }

                                        // Hitung rowspan berdasarkan jumlah juri yang ada
                                        $rowspan = max($maxJuri, 1);
                                        ?>
                                        <tr>
                                            <td class="align-middle" rowspan="<?= $rowspan ?>"><?= esc($category['name'] ?? '-') ?></td>
                                            <td class="align-middle" rowspan="<?= $rowspan ?>"><?= implode('<br>', $materiList) ?></td>
                                            <?php if (!empty($juriScores)): ?>
                                                <?php $first = array_shift($juriScores); ?>
                                                <td><?= esc($first['label'] ?? 'Juri 1') ?></td>
                                                <td class="text-center"><?= number_format((float)($first['Nilai'] ?? 0), 2) ?></td>
                                                <td><?= nl2br(esc($first['Catatan'] ?? '-')) ?></td>
                                            <?php else: ?>
                                                <td class="text-center" colspan="3">Belum ada penilaian</td>
                                            <?php endif; ?>
                                            <td class="align-middle text-center" rowspan="<?= $rowspan ?>"><?= number_format($average, 2) ?></td>
                                            <td class="align-middle text-center" rowspan="<?= $rowspan ?>"><?= number_format($weighted, 2) ?></td>
                                        </tr>
                                        <?php if (!empty($juriScores)): ?>
                                            <?php foreach ($juriScores as $score): ?>
                                                <tr>
                                                    <td><?= esc($score['label'] ?? 'Juri') ?></td>
                                                    <td class="text-center"><?= number_format((float)($score['Nilai'] ?? 0), 2) ?></td>
                                                    <td><?= nl2br(esc($score['Catatan'] ?? '-')) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <tr class="bg-light font-weight-bold">
                                        <td colspan="2" class="text-right"><strong>JUMLAH</strong></td>
                                        <td colspan="3"></td>
                                        <td class="text-center"><strong><?= number_format($totalNilai, 2) ?></strong></td>
                                        <td class="text-center"><strong><?= number_format($totalBobot, 2) ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <h5>Informasi Tambahan</h5>
                            <ul class="list-unstyled">
                                <li><strong>ID Santri:</strong> <?= esc($peserta['IdSantri'] ?? '-') ?></li>
                                <li><strong>Sumber Bobot:</strong> <?= esc($meta['bobot_source'] ?? '-') ?></li>
                                <li><strong>Selisih terhadap threshold:</strong> <?= number_format((float)($peserta['KelulusanDifference'] ?? 0), 2) ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>