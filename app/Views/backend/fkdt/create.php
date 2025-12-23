<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="col-12">
    <?php echo session()->getFlashdata('pesan'); 
    //echo $validation->listErrors();
    ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tambah Data FKDT</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <form action="<?= base_url('/backend/fkdt/save') ?>" method="POST">
                <div class="form-group">
                    <label for="IdFkdt">ID FKDT</label>
                    <input 
                        type="text" name="IdFkdt" required placeholder="Ketik ID FKDT"
                        class="form-control  <?= ($validation->hasError('IdFkdt')) ? 'is-invalid' : ''; ?>" 
                        autofocus value="<?= old('IdFkdt');?>" id="IdFkdt" maxlength="50"
                    >
                    <div class="invalid-feedback">
                        <?= $validation->getError('IdFkdt'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="NamaFkdt">Nama FKDT</label>
                    <input 
                        type="text" name="NamaFkdt" id="NamaFkdt" placeholder="Ketik Nama FKDT"
                        class="form-control  <?= ($validation->hasError('NamaFkdt')) ? 'is-invalid' : ''; ?>"  
                        required value="<?= old('NamaFkdt'); ?>"
                    >
                    <div class="invalid-feedback">
                        <?= $validation->getError('NamaFkdt'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="AlamatFkdt" class="form-control" rows="3" placeholder="Ketik Alamat FKDT"><?= old('AlamatFkdt'); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="Kecamatan">Kecamatan</label>
                    <input type="text" name="Kecamatan" class="form-control" id="Kecamatan" placeholder="Ketik Nama Kecamatan" value="<?= old('Kecamatan'); ?>">
                </div>
                <div class="form-group">
                    <label for="NamaKepFkdt">Nama Kepala FKDT</label>
                    <input type="text" name="NamaKepFkdt" class="form-control" id="NamaKepFkdt" placeholder="Ketik Nama Kepala FKDT" value="<?= old('NamaKepFkdt'); ?>">
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
                    <a href="<?= base_url('/backend/fkdt/show') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<?= $this->endSection(); ?>

