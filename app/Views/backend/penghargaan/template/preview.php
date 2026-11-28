<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<?php
$isPortrait = ($template['Orientasi'] ?? 'landscape') === 'portrait';
$canvasW    = $isPortrait ? 794 : 1123;
$canvasH    = $isPortrait ? 1123 : 794;
$sampleData = [
    'LOGO_LEMBAGA'      => '[LOGO]',
    'NAMA_LEMBAGA'      => 'TPQ SMART AL-FALAH',
    'JUDUL_SERTIFIKAT'  => 'SERTIFIKAT PENGHARGAAN',
    'NOMOR_SERTIFIKAT'  => 'No: TPQ/SNRT/2026/001',
    'NAMA_SANTRI'       => 'Ahmad Fauzi Ramadhan',
    'KELAS'             => 'Kelas 3A',
    'JENIS_PENGHARGAAN' => 'Juara Umum Kelas',
    'PERINGKAT'         => 'Peringkat 1',
    'PERIODE'           => 'Semester Ganjil · TA 2025/2026',
    'NILAI_RATA_RATA'   => 'Nilai Rata-rata: 94.50',
    'PERSEN_KEHADIRAN'  => 'Kehadiran: 100%',
    'TANGGAL_CETAK'     => 'Diberikan pada: ' . date('d F Y'),
    'TTD_1_JABATAN'     => 'Wali Kelas',
    'TTD_1_QR'          => '[QR #1]',
    'TTD_1_NAMA'        => 'Ustadz Ridwan',
    'TTD_2_JABATAN'     => 'Kepala TPQ',
    'TTD_2_QR'          => '[QR #2]',
    'TTD_2_NAMA'        => 'H. Abdullah, S.Pd.I',
    'TTD_3_JABATAN'     => 'Ketua Yayasan',
    'TTD_3_QR'          => '[QR #3]',
    'TTD_3_NAMA'        => 'KH. Ahmad Yani',
];
?>

<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h3 class="card-title font-weight-bold"><i class="fas fa-eye text-info mr-2"></i>Preview Template: <?= esc($template['NamaTemplate']) ?></h3>
            <a href="<?= base_url('backend/penghargaan/template') ?>" class="btn btn-secondary btn-sm ml-auto">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
        <div class="card-body bg-dark text-center p-3" style="overflow: auto;">
            <div style="position: relative; display: inline-block; width: <?= $canvasW ?>px; height: <?= $canvasH ?>px; background: #fff url('<?= base_url($template['BackgroundFile']) ?>') center/cover no-repeat; box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
                <?php foreach ($fields as $f): ?>
                    <div style="
                        position: absolute;
                        left: <?= (int) $f['PosisiX'] ?>px;
                        top: <?= (int) $f['PosisiY'] ?>px;
                        width: <?= (int) $f['Lebar'] ?>px;
                        height: <?= (int) $f['Tinggi'] ?>px;
                        font-size: <?= (int) $f['FontSize'] ?>px;
                        font-family: <?= esc($f['FontFamily']) ?>, sans-serif;
                        font-weight: <?= !empty($f['FontBold']) ? 'bold' : 'normal' ?>;
                        color: <?= esc($f['FontColor']) ?>;
                        text-align: <?= esc($f['TextAlign']) ?>;
                        display: flex;
                        align-items: center;
                        justify-content: <?= $f['TextAlign'] === 'left' ? 'flex-start' : ($f['TextAlign'] === 'right' ? 'flex-end' : 'center') ?>;
                    ">
                        <?= esc($sampleData[$f['NamaField']] ?? $f['NamaField']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
