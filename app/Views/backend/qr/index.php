<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">QR Label Management</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Generate QR Code</h5>
                                    <p class="card-text">Generate QR code baru untuk label</p>
                                    <a href="<?= base_url('backend/qr/generate') ?>" class="btn btn-primary">Generate QR</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Print QR Label</h5>
                                    <p class="card-text">Print QR label yang sudah ada</p>
                                    <a href="<?= base_url('backend/qr/print') ?>" class="btn btn-success">Print Label</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>