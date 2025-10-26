<?= $this->extend('backend/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Input Nilai Munaqosah</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('backend/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('backend/munaqosah') ?>">Munaqosah</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('backend/munaqosah/nilai') ?>">Data Nilai</a></li>
                        <li class="breadcrumb-item active">Input Nilai</li>
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
                            <h3 class="card-title">Form Input Nilai Munaqosah</h3>
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

                            <form action="<?= base_url('backend/munaqosah/save-nilai') ?>" method="post">
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
                                            <label for="IdSantri">Santri <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="IdSantri" name="IdSantri" required>
                                                <option value="">Pilih Santri</option>
                                                <?php foreach ($santri as $s): ?>
                                                    <option value="<?= $s->IdSantri ?>" <?= old('IdSantri') == $s->IdSantri ? 'selected' : '' ?>>
                                                        <?= $s->NamaSantri ?> (<?= $s->NoInduk ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="IdTpq">TPQ <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="IdTpq" name="IdTpq" required>
                                                <option value="">Pilih TPQ</option>
                                                <?php foreach ($tpq as $t): ?>
                                                    <option value="<?= $t->IdTpq ?>" <?= old('IdTpq') == $t->IdTpq ? 'selected' : '' ?>>
                                                        <?= $t->NamaTpq ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="IdJuri">Juri <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="IdJuri" name="IdJuri" required>
                                                <option value="">Pilih Juri</option>
                                                <?php foreach ($guru as $g): ?>
                                                    <option value="<?= $g->IdGuru ?>" <?= old('IdJuri') == $g->IdGuru ? 'selected' : '' ?>>
                                                        <?= $g->NamaGuru ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="IdTahunAjaran">Tahun Ajaran <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="IdTahunAjaran" name="IdTahunAjaran" 
                                                   value="<?= old('IdTahunAjaran', '2024/2025') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="IdMateri">Materi <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="IdMateri" name="IdMateri" required>
                                                <option value="">Pilih Materi</option>
                                                <?php foreach ($materi as $m): ?>
                                                    <option value="<?= $m->id ?>" <?= old('IdMateri') == $m->id ? 'selected' : '' ?>>
                                                        <?= $m->KategoriMateriUjian ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
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
                                            <label for="TypeUjian">Tipe Ujian <span class="text-danger">*</span></label>
                                            <select class="form-control" id="TypeUjian" name="TypeUjian" required>
                                                <option value="">Pilih Tipe Ujian</option>
                                                <option value="munaqosah" <?= old('TypeUjian') == 'munaqosah' ? 'selected' : '' ?>>Munaqosah</option>
                                                <option value="pra-munaqosah" <?= old('TypeUjian') == 'pra-munaqosah' ? 'selected' : '' ?>>Pra-Munaqosah</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="Nilai">Nilai <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="Nilai" name="Nilai" 
                                                   step="0.01" min="0" max="100" 
                                                   value="<?= old('Nilai') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="Catatan">Catatan</label>
                                            <textarea class="form-control" id="Catatan" name="Catatan" rows="3"><?= old('Catatan') ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                    <a href="<?= base_url('backend/munaqosah/nilai') ?>" class="btn btn-secondary">
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

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4'
    });
});
</script>
<?= $this->endSection() ?>
