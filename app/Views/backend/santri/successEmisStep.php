<?php
// Deteksi konteks (public vs admin)
$isPublic = isset($isPublic) ? $isPublic : false;
$templatePath = $isPublic ? 'frontend/template/publicTemplate' : 'backend/template/template';
?>

<?= $this->extend($templatePath); ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
                <h3 class="mt-3">Terima Kasih!</h3>
                <p class="lead">Data santri <b><?= 'ID ' . $dataSantri['IdSantri'] . ' - ' . $dataSantri['NamaSantri'] ?></b> telah berhasil dikirim ke sistem.</p>

                <small class="text-muted"><i class="fas fa-info-circle"></i> Harap tekan tombol print pdf untuk menyimpan sekilas informasi data santri yang sudah dikirim</small>
                <div class="mt-4">
                    <!-- Tombol Print - Sesuaikan route-nya -->
                    <a href="javascript:void(0)" onclick="printPdf(<?= $dataSantri['IdSantri'] ?>)" class="btn btn-warning mr-2">
                        <i class="fas fa-print"></i>&nbsp;Print Pdf
                    </a>
                    <?php if (!$isPublic): ?>
                        <!-- Tombol List Santri Baru (hanya untuk admin) -->
                        <a href="<?= base_url('backend/santri/showSantriBaru') ?>" class="btn btn-info mr-2">
                            <i class="fas fa-list"></i><span class="d-none d-md-inline">&nbsp;Data Santri</span>
                        </a>
                        <!-- Tombol Form Baru (admin) -->
                        <a href="<?= base_url('backend/santri/createEmisStep') ?>" class="btn btn-primary mr-2">
                            <i class="fas fa-plus"></i>&nbsp;Form Baru
                        </a>
                    <?php else: ?>
                        <!-- Tombol Form Baru (public) -->
                        <a href="<?= base_url('pendaftaran') ?>" class="btn btn-primary mr-2">
                            <i class="fas fa-plus"></i>&nbsp;Daftar Lagi
                        </a>
                        <!-- Tombol Kembali ke Beranda -->
                        <a href="<?= base_url('/') ?>" class="btn btn-secondary mr-2">
                            <i class="fas fa-home"></i>&nbsp;Beranda
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    function printPdf(idSantri) {
        Swal.fire({
            title: 'Cetak PDF',
            text: "Apakah anda yakin ingin mencetak data santri ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Cetak!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const pdfUrl = <?= $isPublic ? "'/pendaftaran/generatePDFSantriBaru/'" : "'/backend/santri/generatePDFSantriBaru/'" ?>;
                window.open(pdfUrl + idSantri, '_blank');
            }
        });
    }
</script>
<?= $this->endSection() ?>