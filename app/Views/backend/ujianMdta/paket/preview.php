<?= $this->extend('backend/template/template_ujian'); ?>
<?= $this->section('content'); ?>

<?= $this->include('backend/ujianMdta/common/cbt_sheet', [
    'isPreview'      => true,
    'pageTitle'      => 'PREVIEW PAKET SOAL',
    'namaUjian'      => $paket['NamaPaket'] ?? 'Preview Paket Soal',
    'namaSantri'     => 'FULAN',
    'namaKelas'      => 'PREVIEW',
    'namaPaket'      => $paket['NamaPaket'] ?? 'Paket Soal',
    'soalList'       => $soalList ?? [],
    'jawabanMap'     => [],
    'sisaWaktuDetik' => 3480,
    'token'          => '',
    'exitUrl'        => base_url('backend/ujian-mdta/paket'),
    'exitLabel'      => 'Exit Preview',
]); ?>

<?= $this->endSection(); ?>
