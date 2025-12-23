<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="col-12">
    <?php echo session()->getFlashdata('pesan'); 
    //echo $validation->listErrors();
    ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tambah Data FKPQ</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <form action="<?= base_url('/backend/fkpq/save') ?>" method="POST">
                <div class="form-group">
                    <label for="IdFkpq">ID FKPQ</label>
                    <input 
                        type="text" name="IdFkpq" required placeholder="Ketik ID FKPQ"
                        class="form-control  <?= ($validation->hasError('IdFkpq')) ? 'is-invalid' : ''; ?>" 
                        autofocus value="<?= old('IdFkpq');?>" id="IdFkpq" maxlength="50"
                    >
                    <div class="invalid-feedback">
                        <?= $validation->getError('IdFkpq'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="NamaFkpq">Nama FKPQ</label>
                    <input 
                        type="text" name="NamaFkpq" id="NamaFkpq" placeholder="Ketik Nama FKPQ"
                        class="form-control  <?= ($validation->hasError('NamaFkpq')) ? 'is-invalid' : ''; ?>"  
                        required value="<?= old('NamaFkpq'); ?>"
                    >
                    <div class="invalid-feedback">
                        <?= $validation->getError('NamaFkpq'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="AlamatFkpq" class="form-control" rows="3" placeholder="Ketik Alamat FKPQ"><?= old('AlamatFkpq'); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="NamaKepFkpq">Nama Kepala FKPQ</label>
                    <input type="text" name="NamaKepFkpq" class="form-control" id="NamaKepFkpq" placeholder="Ketik Nama Kepala FKPQ" value="<?= old('NamaKepFkpq'); ?>">
                </div>
                <div class="form-group">
                    <label for="NoHp">No Hp</label>
                    <input type="text" name="NoHp" class="form-control" id="NoHp" placeholder="Ketik No Handphone" value="<?= old('NoHp'); ?>">
                </div>
                <div class="form-group">
                    <label for="TempatBelajar">Tempat Belajar</label>
                    <input type="text" name="TempatBelajar" class="form-control" id="TempatBelajar" placeholder="Ketik Tempat Belajar" value="<?= old('TempatBelajar'); ?>">
                </div>
                <div class="form-group">
                    <label>Tahun Berdiri:</label>
                    <input type="text" name="TanggalBerdiri" class="form-control" placeholder="Tahun Berdiri (contoh: 2020)" value="<?= old('TanggalBerdiri'); ?>">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    <a href="<?= base_url('/backend/fkpq/show') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<?= $this->endSection(); ?>

