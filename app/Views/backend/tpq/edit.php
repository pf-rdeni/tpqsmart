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
                            <h3 class="card-title">Edit Profil Lembaga</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('backend/tpq/update/' . $tpq['IdTpq']) ?>" method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="IdTpq">ID TPQ:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="IdTpq" 
                                                   name="IdTpq" 
                                                   value="<?= old('IdTpq', $tpq['IdTpq']) ?>" 
                                                   readonly
                                                   style="background-color: #f8f9fa; cursor: not-allowed;">
                                            <small class="form-text text-muted">ID TPQ tidak dapat diubah</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="NamaTpq">Nama TPQ:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="NamaTpq" 
                                                   name="NamaTpq" 
                                                   value="<?= old('NamaTpq', $tpq['NamaTpq']) ?>" 
                                                   required>
                                            <?php if (session('validation') && session('validation')->getError('NamaTpq')) : ?>
                                                <div class="text-danger"><?= session('validation')->getError('NamaTpq') ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="AlamatTpq">Alamat:</label>
                                            <textarea class="form-control" 
                                                      id="AlamatTpq" 
                                                      name="AlamatTpq" 
                                                      rows="3" 
                                                      required><?= old('AlamatTpq', $tpq['Alamat']) ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="TanggalBerdiri">Tahun Berdiri:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="TanggalBerdiri" 
                                                   name="TanggalBerdiri" 
                                                   value="<?= old('TanggalBerdiri', $tpq['TahunBerdiri']) ?>" 
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="NamaKepTpq">Nama Kepala TPQ:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="NamaKepTpq" 
                                                   name="NamaKepTpq" 
                                                   value="<?= old('NamaKepTpq', $tpq['KepalaSekolah']) ?>" 
                                                   required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="NoHp">No. HP:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="NoHp" 
                                                   name="NoHp" 
                                                   value="<?= old('NoHp', $tpq['NoHp']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="TempatBelajar">Tempat Belajar:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="TempatBelajar" 
                                                   name="TempatBelajar" 
                                                   value="<?= old('TempatBelajar', $tpq['TempatBelajar']) ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Profil
                                    </button>
                                    <a href="<?= base_url('backend/tpq/profilLembaga') ?>" class="btn btn-secondary">
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
<?= $this->endSection() ?>
