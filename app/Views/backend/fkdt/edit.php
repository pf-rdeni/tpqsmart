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
                            <h3 class="card-title">Edit Profil Lembaga FKDT</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('backend/fkdt/update/' . $fkdt['IdFkdt']) ?>" method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="IdFkdt">ID FKDT:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="IdFkdt" 
                                                   name="IdFkdt" 
                                                   value="<?= old('IdFkdt', $fkdt['IdFkdt']) ?>" 
                                                   readonly
                                                   style="background-color: #f8f9fa; cursor: not-allowed;">
                                            <small class="form-text text-muted">ID FKDT tidak dapat diubah</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="NamaFkdt">Nama FKDT:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="NamaFkdt" 
                                                   name="NamaFkdt" 
                                                   value="<?= old('NamaFkdt', $fkdt['NamaFkdt']) ?>" 
                                                   required>
                                            <?php if (session('validation') && session('validation')->getError('NamaFkdt')) : ?>
                                                <div class="text-danger"><?= session('validation')->getError('NamaFkdt') ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="AlamatFkdt">Alamat:</label>
                                            <textarea class="form-control" 
                                                      id="AlamatFkdt" 
                                                      name="AlamatFkdt" 
                                                      rows="3" 
                                                      required><?= old('AlamatFkdt', $fkdt['Alamat']) ?></textarea>
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
                                                   value="<?= old('TanggalBerdiri', $fkdt['TahunBerdiri']) ?>" 
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="NamaKepFkdt">Nama Kepala FKDT:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="NamaKepFkdt" 
                                                   name="NamaKepFkdt" 
                                                   value="<?= old('NamaKepFkdt', $fkdt['KepalaSekolah']) ?>" 
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
                                                   value="<?= old('NoHp', $fkdt['NoHp']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="TempatBelajar">Tempat Belajar:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="TempatBelajar" 
                                                   name="TempatBelajar" 
                                                   value="<?= old('TempatBelajar', $fkdt['TempatBelajar']) ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="Visi">Visi Lembaga:</label>
                                            <textarea class="form-control" 
                                                      id="Visi" 
                                                      name="Visi" 
                                                      rows="4" 
                                                      placeholder="Masukkan Visi Lembaga"><?= old('Visi', $fkdt['Visi'] ?? '') ?></textarea>
                                            <small class="form-text text-muted">Masukkan visi lembaga. Gunakan Enter untuk membuat baris baru.</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="Misi">Misi Lembaga:</label>
                                            <textarea class="form-control" 
                                                      id="Misi" 
                                                      name="Misi" 
                                                      rows="6" 
                                                      placeholder="Masukkan Misi Lembaga"><?= old('Misi', $fkdt['Misi'] ?? '') ?></textarea>
                                            <small class="form-text text-muted">Masukkan misi lembaga. Gunakan Enter untuk membuat baris baru. Untuk format list, gunakan nomor atau bullet (contoh: 1. Misi pertama, 2. Misi kedua).</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Profil
                                    </button>
                                    <a href="<?= base_url('backend/fkdt/profil-lembaga/' . $fkdt['IdFkdt']) ?>" class="btn btn-secondary">
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

