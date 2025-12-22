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
                            <h3 class="card-title">Edit Profil Lembaga MDA</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('backend/mda/update/' . $mda['IdTpq']) ?>" method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="IdTpq">ID TPQ:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="IdTpq" 
                                                   name="IdTpq" 
                                                   value="<?= old('IdTpq', $mda['IdTpq']) ?>" 
                                                   readonly
                                                   style="background-color: #f8f9fa; cursor: not-allowed;">
                                            <small class="form-text text-muted">ID TPQ tidak dapat diubah</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="IdMda">ID MDA:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="IdMda" 
                                                   name="IdMda" 
                                                   value="<?= old('IdMda', $mda['IdMda'] ?? '') ?>" 
                                                   required>
                                            <small class="form-text text-muted">ID MDA (contoh: MDA1, MDA2, dll)</small>
                                            <?php if (session('validation') && session('validation')->getError('IdMda')) : ?>
                                                <div class="text-danger"><?= session('validation')->getError('IdMda') ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="NamaTpq">Nama MDA:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="NamaTpq" 
                                                   name="NamaTpq" 
                                                   value="<?= old('NamaTpq', $mda['NamaTpq']) ?>" 
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
                                                      required><?= old('AlamatTpq', $mda['Alamat']) ?></textarea>
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
                                                   value="<?= old('TanggalBerdiri', $mda['TahunBerdiri']) ?>" 
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="NamaKepTpq">Nama Kepala MDA:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="NamaKepTpq" 
                                                   name="NamaKepTpq" 
                                                   value="<?= old('NamaKepTpq', $mda['KepalaSekolah']) ?>" 
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
                                                   value="<?= old('NoHp', $mda['NoHp']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="TempatBelajar">Tempat Belajar:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="TempatBelajar" 
                                                   name="TempatBelajar" 
                                                   value="<?= old('TempatBelajar', $mda['TempatBelajar']) ?>">
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
                                                      placeholder="Masukkan Visi Lembaga"><?= old('Visi', $mda['Visi'] ?? '') ?></textarea>
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
                                                      placeholder="Masukkan Misi Lembaga"><?= old('Misi', $mda['Misi'] ?? '') ?></textarea>
                                            <small class="form-text text-muted">Masukkan misi lembaga. Gunakan Enter untuk membuat baris baru. Untuk format list, gunakan nomor atau bullet (contoh: 1. Misi pertama, 2. Misi kedua).</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Profil MDA
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

