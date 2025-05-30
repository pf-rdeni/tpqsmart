<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Set Santri Baru TPQ <?= $dataTpq[0]['NamaTpq'] ?></h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <form action="<?= base_url('backend/kelas/setKelasSantriBaru') ?>" method="POST">
                <table id="kenaikanKelas" class="table table-bordered table-striped">
                    <?php
                    $tableHeaders = '
                    <tr>
                        <th>Nama Santri</th>
                        <th>Jenis Kelamin</th>
                        <th>Nama Ayah</th>
                        <th>Kelas Diajukan</th>
                        <th>Kelas Koreksi</th>
                        <th>Nama TPQ</th>
                        <th>Nama Kel/Desa</th>
                    </tr>
                ';
                    ?>
                    <thead>
                        <?= $tableHeaders ?>
                    </thead>
                    <tbody>
                        <?php foreach ($dataSantri as $santri) : ?>
                            <tr>
                                <td><?= $santri['NamaSantri']; ?></td>
                                <td><?= $santri['JenisKelamin']; ?></td>
                                <td><?= $santri['NamaAyah']; ?></td>
                                <td><?= $santri['NamaKelas']; ?></td>
                                <td>
                                    <input type="hidden" name="IdTpq[<?= $santri['IdSantri']; ?>]" value="<?= $santri['IdTpq']; ?>">
                                    <select name="IdKelas[<?= $santri['IdSantri']; ?>]" class="form-control select2" id="FormProfilTpq" required>
                                        <option value="" disabled selected>Pilih kelas</option>
                                        <?php
                                        foreach ($dataKelas as $kelas): ?>
                                            <option value="<?= $kelas['IdKelas'] ?>"
                                                <?= ($kelas['NamaKelas'] == $santri['NamaKelas']) ? 'selected' : '' ?>>
                                                <?= $kelas['NamaKelas'] ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                                <td><?= $santri['NamaTpq']; ?></td>
                                <td><?= $santri['NamaKelDesa']; ?></td>
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
<?= $this->section('scripts'); ?>
<script>
    initializeDataTableUmum("#kenaikanKelas", true, true);
</script>
<?= $this->endSection(); ?>