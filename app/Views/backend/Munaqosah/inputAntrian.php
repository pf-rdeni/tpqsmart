<?= $this->extend('backend/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Input Antrian Munaqosah</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('backend/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('backend/munaqosah') ?>">Munaqosah</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('backend/munaqosah/antrian') ?>">Antrian</a></li>
                        <li class="breadcrumb-item active">Input Antrian</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Form Input Antrian Munaqosah</h3>
                        </div>
                        <div class="card-body">
                            <?php if (session()->getFlashdata('errors')): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                            <li><?= $error ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <form action="<?= base_url('backend/munaqosah/save-antrian') ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="NoPeserta">Nomor Peserta <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="NoPeserta" name="NoPeserta" 
                                                   value="<?= old('NoPeserta') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="IdTahunAjaran">Tahun Ajaran <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="IdTahunAjaran" name="IdTahunAjaran" 
                                                   value="<?= old('IdTahunAjaran', '2024/2025') ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="KategoriMateriUjian">Kategori Materi Ujian <span class="text-danger">*</span></label>
                                            <select class="form-control" id="KategoriMateriUjian" name="KategoriMateriUjian" required>
                                                <option value="">Pilih Kategori</option>
                                                <option value="Iqra" <?= old('KategoriMateriUjian') == 'Iqra' ? 'selected' : '' ?>>Iqra</option>
                                                <option value="Qur'an" <?= old('KategoriMateriUjian') == 'Qur\'an' ? 'selected' : '' ?>>Qur'an</option>
                                                <option value="Hafalan" <?= old('KategoriMateriUjian') == 'Hafalan' ? 'selected' : '' ?>>Hafalan</option>
                                                <option value="Tajwid" <?= old('KategoriMateriUjian') == 'Tajwid' ? 'selected' : '' ?>>Tajwid</option>
                                                <option value="Praktik" <?= old('KategoriMateriUjian') == 'Praktik' ? 'selected' : '' ?>>Praktik</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="Keterangan">Keterangan</label>
                                            <textarea class="form-control" id="Keterangan" name="Keterangan" rows="3"><?= old('Keterangan') ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                    <a href="<?= base_url('backend/munaqosah/antrian') ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
