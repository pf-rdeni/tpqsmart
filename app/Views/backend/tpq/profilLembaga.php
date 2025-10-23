<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <?php if (session()->getFlashdata('pesan')) : ?>
            <?= session()->getFlashdata('pesan') ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Profil Lembaga</h3>
                        <?php if (!empty($tpq)) : ?>
                            <div class="card-tools">
                                <a href="<?= base_url('backend/tpq/edit/' . $tpq[0]['IdTpq']) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit Profil
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($tpq)) : ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ID TPQ:</label>
                                        <p class="form-control-static"><?= $tpq[0]['IdTpq'] ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama TPQ:</label>
                                        <p class="form-control-static"><?= $tpq[0]['NamaTpq'] ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Alamat:</label>
                                        <p class="form-control-static"><?= $tpq[0]['Alamat'] ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tempat Belajar:</label>
                                        <p class="form-control-static"><?= $tpq[0]['TempatBelajar'] ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tahun Berdiri:</label>
                                        <p class="form-control-static"><?= $tpq[0]['TahunBerdiri'] ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="alert alert-warning">
                                <h4><i class="icon fa fa-warning"></i> Data Tidak Ditemukan!</h4>
                                Belum ada data TPQ yang tersimpan. Silakan tambahkan data TPQ terlebih dahulu.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logo Lembaga Section -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Logo Lembaga</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <?php if (!empty($tpq) && !empty($tpq[0]['LogoLembaga'])) : ?>
                                <img src="<?= base_url('uploads/logo/' . $tpq[0]['LogoLembaga']) ?>"
                                    alt="Logo Lembaga"
                                    class="img-fluid"
                                    style="max-height: 200px; max-width: 200px;">
                            <?php else : ?>
                                <div class="border p-4 text-muted">
                                    <i class="fas fa-image fa-3x"></i>
                                    <p>Belum ada logo</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <form action="<?= base_url('backend/tpq/uploadLogo') ?>"
                            method="post"
                            enctype="multipart/form-data"
                            class="mt-3">
                            <?php if (!empty($tpq)) : ?>
                                <input type="hidden" name="IdTpq" value="<?= $tpq[0]['IdTpq'] ?>">
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="logo">Upload Logo Baru:</label>
                                <input type="file"
                                    class="form-control-file"
                                    id="logo"
                                    name="logo"
                                    accept="image/*"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Logo
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Kop Lembaga Section -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Kop Lembaga</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <?php if (!empty($tpq) && !empty($tpq[0]['KopLembaga'])) : ?>
                                <img src="<?= base_url('uploads/kop/' . $tpq[0]['KopLembaga']) ?>"
                                    alt="Kop Lembaga"
                                    class="img-fluid"
                                    style="max-height: 200px; max-width: 200px;">
                            <?php else : ?>
                                <div class="border p-4 text-muted">
                                    <i class="fas fa-file-image fa-3x"></i>
                                    <p>Belum ada kop lembaga</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <form action="<?= base_url('backend/tpq/uploadKop') ?>"
                            method="post"
                            enctype="multipart/form-data"
                            class="mt-3">
                            <?php if (!empty($tpq)) : ?>
                                <input type="hidden" name="IdTpq" value="<?= $tpq[0]['IdTpq'] ?>">
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="kop_lembaga">Upload Kop Baru:</label>
                                <input type="file"
                                    class="form-control-file"
                                    id="kop_lembaga"
                                    name="kop_lembaga"
                                    accept="image/*"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload"></i> Upload Kop
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>