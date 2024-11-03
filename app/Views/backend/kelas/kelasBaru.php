<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Set Santri Baru</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
        <form action="<?= base_url('kelas/setKelasSantriBaru') ?>" method="POST">
            <table id="kenaikanKelas" class="table table-bordered table-striped">
                <?php
                $tableHeaders = '
                    <tr>
                        <th>IdSantri</th>
                        <th>Nama Santri</th>
                        <th>Jenis Kelamin</th>
                        <th>kelas Diajukan</th>
                        <th>Nama Ayah</th>
                        <th>kelas Baru Rekomendasi</th>
                    </tr>
                ';
                ?>
                <thead>
                <?= $tableHeaders ?>
                </thead>
                <tbody>
                    <?php foreach ($dataSantri as $santri) : ?>
                        <tr>
                            <td><?= $santri['IdSantri']; ?></td>
                            <td><?= $santri['Nama']; ?></td>
                            <td><?= $santri['JenisKelamin']; ?></td>
                            <td><?= $santri['NamaKelas']; ?></td>
                            <td><?= $santri['NamaAyah']; ?></td>
                            <td>
                                <input type="hidden" name="IdTpq[<?= $santri['IdSantri']; ?>]" value="<?= $santri['IdTpq']; ?>">
                                <select name="IdKelas[<?= $santri['IdSantri']; ?>]" class="form-control select2" id="FormProfilTpq" required>
                                    <option value="" disabled selected>Pilih kelas</option>
                                    <?php 
                                    foreach ($dataKelas as $kelas): ?>
                                        <option value="<?= $kelas['IdKelas'] ?>">
                                            <?= $kelas['NamaKelas'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <?= $tableHeaders ?>
                </tfoot>
            </table>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>Simpan</button>
            </div>
        </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<?= $this->endSection(); ?>
