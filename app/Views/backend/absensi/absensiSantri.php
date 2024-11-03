<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Absensi</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <?php
                // Kelompokkan santri berdasarkan kelas
                $santriByKelas = [];
                foreach ($santri as $row) {
                    $santriByKelas[$row->NamaKelas][] = $row;
                }

                // Cek apakah ada data santri untuk hari ini
                if (empty($santriByKelas)) {
                    echo "<div class='alert alert-info'>Santri sudah diabsen pada hari ini.</div>";
                } else {
                    // Loop melalui setiap kelas dan buat form terpisah
                    foreach ($santriByKelas as $kelas => $santriList): ?>
                        <form action="<?= base_url('/backend/absensi/simpanAbsensi') ?>" method="post">
                            <label for="tanggal">Tanggal:</label>
                            <?php
                            // Ambil tanggal hari ini dalam format yang sesuai untuk input type="date" (YYYY-MM-DD)
                            $tanggalHariIni = date('Y-m-d');
                            ?>
                            <input type="date" name="tanggal" value="<?= $tanggalHariIni; ?>" required>

                            <h4>Kelas: <?= $kelas ?></h4> <!-- Nama Kelas -->

                            <!-- Tambahkan hidden input untuk menyimpan IdKelas, IdGuru, IdTahunAjaran -->
                            <input type="hidden" name="IdKelas" value="<?= $santriList[0]->IdKelas ?>"> <!-- IdKelas diambil dari santri pertama di list -->
                            <input type="hidden" name="IdGuru" value="<?= session()->get('IdGuru') ?>">
                            <input type="hidden" name="IdTahunAjaran" value="<?= $santriList[0]->IdTahunAjaran ?>">

                            <div class="table-responsive">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Nama Santri</th>
                                            <th>Kehadiran</th>
                                            <th>Keterangan (jika Izin atau Sakit)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($santriList as $row): ?>
                                        <tr>
                                            <td><?= $row->SantriNama ?></td> <!-- Menampilkan nama santri -->
                                            <td>
                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn bg-olive <?= isset($_POST['kehadiran'][$row->IdSantri]) && $_POST['kehadiran'][$row->IdSantri] == 'Hadir' ? 'active' : '' ?>">
                                                        <input type="radio" name="kehadiran[<?= $row->IdSantri ?>]" value="Hadir" autocomplete="off" <?= isset($_POST['kehadiran'][$row->IdSantri]) && $_POST['kehadiran'][$row->IdSantri] == 'Hadir' ? 'checked' : '' ?>> Hadir
                                                    </label>
                                                    <label class="btn bg-olive <?= isset($_POST['kehadiran'][$row->IdSantri]) && $_POST['kehadiran'][$row->IdSantri] == 'Izin' ? 'active' : '' ?>">
                                                        <input type="radio" name="kehadiran[<?= $row->IdSantri ?>]" value="Izin" autocomplete="off" onclick="toggleKeterangan(<?= $row->IdSantri ?>)" <?= isset($_POST['kehadiran'][$row->IdSantri]) && $_POST['kehadiran'][$row->IdSantri] == 'Izin' ? 'checked' : '' ?>> Izin
                                                    </label>
                                                    <label class="btn bg-olive <?= isset($_POST['kehadiran'][$row->IdSantri]) && $_POST['kehadiran'][$row->IdSantri] == 'Sakit' ? 'active' : '' ?>">
                                                        <input type="radio" name="kehadiran[<?= $row->IdSantri ?>]" value="Sakit" autocomplete="off" onclick="toggleKeterangan(<?= $row->IdSantri ?>)" <?= isset($_POST['kehadiran'][$row->IdSantri]) && $_POST['kehadiran'][$row->IdSantri] == 'Sakit' ? 'checked' : '' ?>> Sakit
                                                    </label>
                                                    <label class="btn bg-olive <?= isset($_POST['kehadiran'][$row->IdSantri]) && $_POST['kehadiran'][$row->IdSantri] == 'Alfa' ? 'active' : '' ?>">
                                                        <input type="radio" name="kehadiran[<?= $row->IdSantri ?>]" value="Alfa" autocomplete="off" checked <?= isset($_POST['kehadiran'][$row->IdSantri]) && $_POST['kehadiran'][$row->IdSantri] == 'Alfa' ? 'checked' : '' ?>> Alfa
                                                    </label>
                                                </div>
                                            </td>

                                            <td>
                                                <input type="text" name="keterangan[<?= $row->IdSantri ?>]" id="keterangan-<?= $row->IdSantri ?>" disabled placeholder="Masukkan keterangan">
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan Absensi Kelas <?= $kelas ?></button>
                        </form>
                        <hr>
                    <?php endforeach;
                }
            ?>
        </div>

        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<script>
function toggleKeterangan(id) {
    var keteranganField = document.getElementById('keterangan-' + id);
    keteranganField.disabled = false;
}
</script>

<?= $this->endSection(); ?>
