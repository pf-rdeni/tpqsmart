<?= $this->extend('backend/template/template'); ?>
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
                    <!-- Tombol List Santri Baru -->
                    <a href="<?= base_url('backend/santri/showSantriBaru') ?>" class="btn btn-info mr-2">
                        <i class="fas fa-list"></i><span class="d-none d-md-inline">&nbsp;Data Santri</span>
                    </a>
                    <!-- Tombol Form Baru -->
                    <a href="<?= base_url('backend/santri/createEmisStep') ?>" class="btn btn-primary mr-2">
                        <i class="fas fa-plus"></i>&nbsp;Form Baru
                    </a>
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
                window.open(`<?= base_url('backend/santri/generatePDFSantriBaru/') ?>${idSantri}`, '_blank');
            }
        });
    }
</script>
<?= $this->endSection() ?>