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
                            <h3 class="card-title">Edit Profil Lembaga FKPQ</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('backend/fkpq/update/' . $fkpq['IdFkpq']) ?>" method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="IdFkpq">ID FKPQ:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="IdFkpq" 
                                                   name="IdFkpq" 
                                                   value="<?= old('IdFkpq', $fkpq['IdFkpq']) ?>" 
                                                   readonly
                                                   style="background-color: #f8f9fa; cursor: not-allowed;">
                                            <small class="form-text text-muted">ID FKPQ tidak dapat diubah</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="NamaFkpq">Nama FKPQ:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="NamaFkpq" 
                                                   name="NamaFkpq" 
                                                   value="<?= old('NamaFkpq', $fkpq['NamaFkpq']) ?>" 
                                                   required>
                                            <?php if (session('validation') && session('validation')->getError('NamaFkpq')) : ?>
                                                <div class="text-danger"><?= session('validation')->getError('NamaFkpq') ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="AlamatFkpq">Alamat:</label>
                                            <textarea class="form-control" 
                                                      id="AlamatFkpq" 
                                                      name="AlamatFkpq" 
                                                      rows="3" 
                                                      required><?= old('AlamatFkpq', $fkpq['Alamat']) ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="Kecamatan">Kecamatan:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="Kecamatan" 
                                                   name="Kecamatan" 
                                                   value="<?= old('Kecamatan', $fkpq['Kecamatan'] ?? '') ?>" 
                                                   placeholder="Nama Kecamatan">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="TanggalBerdiri">Tahun Berdiri:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="TanggalBerdiri" 
                                                   name="TanggalBerdiri" 
                                                   value="<?= old('TanggalBerdiri', $fkpq['TahunBerdiri']) ?>" 
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="NamaKepFkpq">Nama Kepala FKPQ:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="NamaKepFkpq" 
                                                   name="NamaKepFkpq" 
                                                   value="<?= old('NamaKepFkpq', $fkpq['KetuaFkpq']) ?>" 
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
                                                   value="<?= old('NoHp', $fkpq['NoHp']) ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="TempatBelajar">Tempat Belajar:</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="TempatBelajar" 
                                                   name="TempatBelajar" 
                                                   value="<?= old('TempatBelajar', $fkpq['TempatBelajar']) ?>">
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
                                                      placeholder="Masukkan Visi Lembaga"><?= old('Visi', $fkpq['Visi'] ?? '') ?></textarea>
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
                                                      placeholder="Masukkan Misi Lembaga"><?= old('Misi', $fkpq['Misi'] ?? '') ?></textarea>
                                            <small class="form-text text-muted">Masukkan misi lembaga. Gunakan Enter untuk membuat baris baru. Untuk format list, gunakan nomor atau bullet (contoh: 1. Misi pertama, 2. Misi kedua).</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Profil
                                    </button>
                                    <a href="<?= base_url('backend/fkpq/profil-lembaga/' . $fkpq['IdFkpq']) ?>" class="btn btn-secondary">
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

