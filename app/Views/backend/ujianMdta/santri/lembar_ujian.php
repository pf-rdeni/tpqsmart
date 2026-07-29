<?= $this->extend('backend/template/template_ujian'); ?>
<?= $this->section('content'); ?>

<?= $this->include('backend/ujianMdta/common/cbt_sheet', [
    'isPreview'      => false,
    'pageTitle'      => 'CBT Ujian — ' . ($jadwal['NamaUjian'] ?? 'Ujian MDTA'),
    'namaUjian'      => $jadwal['NamaUjian'] ?? 'Ujian MDTA',
    'namaSantri'     => $namaSantri ?? 'Santri',
    'namaKelas'      => $namaKelas ?? '-',
    'namaPaket'      => $namaPaket ?? $paket['NamaPaket'] ?? '-',
    'soalList'       => $distribusi ?? [],
    'jawabanMap'     => $jawabanMap ?? [],
    'sisaWaktuDetik' => $sisaWaktuDetik ?? 3600,
    'token'          => $token ?? '',
    'exitUrl'        => base_url('backend/ujian-mdta/santri'),
    'exitLabel'      => 'Keluar Ujian',
]); ?>

<?= $this->endSection(); ?>
