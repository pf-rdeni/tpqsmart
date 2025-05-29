<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="col-12">
    <?php echo session()->getFlashdata('pesan'); ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List Santri</h3>
        </div>
        <div class="card-body">
            <table id="ListIuaranSantri" class="table table-bordered table-striped">
                <thead>
                    <?php
                    echo $tableHeaderFooter = '
                    <tr>
                    <th>Aksi</th>
                    <th>Nama Santri</th>
                    <th>Tingkat Kelas</th>
                    <th>Tahun Ajaran</th>
                    <th>Wali Kelas</th>
                    <th>Jenis Kelamin</th>
                    </tr>
                    ';
                    ?>
                </thead>
                <tbody>
                    <?php foreach ($dataSantri as $santri) : ?>
                        <tr>
                            <td>
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#TambahIuran<?= $santri->IdSantri ?>">Tambah <i class="fas fa-edit"></i></button>
                                <a href="<?= base_url('backend/iuranBulanan/showDetail/' . $santri->IdSantri . '/' . $santri->IdTahunAjaran) ?>" class="btn btn-primary btn-sm"> Detail&nbsp;&nbsp;<i class="fas fa-eye"></i></a>
                            </td>
                            <td><?php echo $santri->NamaSantri; ?></td>
                            <td><?php echo $santri->NamaKelas; ?></td>
                            <td><?php echo $santri->IdTahunAjaran; ?></td>
                            <td><?php echo $santri->GuruNama; ?></td>
                            <td><?php echo $santri->JenisKelamin; ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <?= $tableHeaderFooter ?>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Iuran -->
<?php foreach ($dataSantri as $santri) : ?>
    <div class="modal fade" id="TambahIuran<?= $santri->IdSantri ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Iuran Bulanan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/backend/iuranBulanan/create" method="post">
                        <input type="hidden" id="IdTahunAjaran" name="IdTahunAjaran" value="<?= $santri->IdTahunAjaran ?>">
                        <input type="hidden" id="IdSantri" name="IdSantri" value="<?= $santri->IdSantri ?>">
                        <input type="hidden" id="IdKelas" name="IdKelas" value="<?= $santri->IdKelas ?>">
                        <input type="hidden" id="IdTpq" name="IdTpq" value="<?= $santri->IdTpq ?>">
                        <input type="hidden" id="IdGuru" name="IdGuru" value="<?= $santri->IdGuru ?>">
                        <div class="form-group">
                            <label for="NamaSantri">Nama Santri</label>
                            <input type="text" class="form-control" id="NamaSantri" name="NamaSantri" value="<?= htmlspecialchars($santri->NamaSantri, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="Kategori">Kategori</label>
                            <select class="form-control" id="Kategori" name="Kategori" required onchange="updateNominal(this)">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Iuran">Iuran</option>
                                <option value="Infaq">Infaq</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Bulan">Bulan</label>
                            <select class="form-control" id="Bulan" name="Bulan" required>
                                <option value="">-- Pilih Bulan --</option>
                                <?php
                                $currentMonth = date('n');
                                $bulanJuliToJuni = [
                                    '7' => 'Juli',
                                    '8' => 'Agustus',
                                    '9' => 'September',
                                    '10' => 'Oktober',
                                    '11' => 'November',
                                    '12' => 'Desember',
                                    '1' => 'Januari',
                                    '2' => 'Februari',
                                    '3' => 'Maret',
                                    '4' => 'April',
                                    '5' => 'Mei',
                                    '6' => 'Juni'
                                ];

                                foreach ($bulanJuliToJuni as $num => $namaBulan) {
                                    $selected = ($num == $currentMonth) ? 'selected' : '';
                                    echo "<option value='$num' $selected>$namaBulan</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Nominal">Nominal</label>
                            <input type="text" class="form-control" id="Nominal<?= $santri->IdSantri ?>" name="Nominal" placeholder="Masukan Nominal" required oninput="formatRupiah(this); updateTerbilang(this)" min="100" max="1000000">
                            <input type="text" class="form-control" id="Terbilang<?= $santri->IdSantri ?>" name="Terbilang" placeholder="Terbilang" readonly>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach ?>

<?= $this->endSection(); ?>
<?= $this->section('scripts'); ?>
<script>
    initializeDataTableUmum('#ListIuaranSantri', true, true);

    function updateNominal(selectElement) {
        var kategori = selectElement.value;
        var modal = $(selectElement).closest('.modal');
        var nominalInput = modal.find('[id^=Nominal]'); // Select nominal input in current modal

        if (kategori === 'Iuran') {
            nominalInput.val('25000'); // Default value for Iuran
        } else if (kategori === 'Infaq') {
            nominalInput.val(''); // Clear value for Infaq
        }

        formatRupiah(nominalInput[0]); // Format the value as Rupiah
        updateTerbilang(nominalInput[0]); // Update terbilang when category changes
    }

    function formatRupiah(element) {
        var value = element.value.replace(/[^,\d]/g, '').toString();
        var split = value.split(',');
        var sisa = split[0].length % 3;
        var rupiah = split[0].substr(0, sisa);
        var ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            var separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        element.value = 'Rp. ' + rupiah;
    }

    function updateTerbilang(element) {
        var modal = $(element).closest('.modal');
        var nominalInput = modal.find('[id^=Nominal]');
        var terbilangInput = modal.find('[id^=Terbilang]');
        var value = nominalInput.val().replace(/[^,\d]/g, '').toString();
        var nominal = parseInt(value.replace('Rp. ', '').replace('.', '').replace(',', ''), 10); // Get numeric value

        terbilangInput.val(terbilang(nominal) + ' Rupiah');
    }
</script>
<?= $this->endSection(); ?>