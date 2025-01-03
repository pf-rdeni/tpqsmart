<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="col-12">
    <?php echo session()->getFlashdata('pesan'); ?>
    <div class="card">
        <?php
        // Extracting the first result from $dataTabungan (assuming it has at least one result)
        if (!empty($dataTabungan)) {
            $firstResult = $dataTabungan[0];
            $IdSantri = htmlspecialchars($firstResult->IdSantri, ENT_QUOTES, 'UTF-8');
            $NamaSantri = htmlspecialchars($firstResult->NamaSantri, ENT_QUOTES, 'UTF-8');
            $NamaKelas = htmlspecialchars($firstResult->NamaKelas, ENT_QUOTES, 'UTF-8');
            // Format the Tahun with "/"
            $Tahun = $firstResult->IdTahunAjaran;
            if (strlen($Tahun) == 8) {
                $Tahun = substr($Tahun, 0, 4) . '/' . substr($Tahun, 4, 4);
            } else {
                $Tahun = 'Invalid Year Format';
            }
        } else {
            // Default values or handle the case when $dataTabungan is empty
            $NamaSantri = "";
            $Tahun = "";
            $IdSantri = "";
            $NamaKelas = "";
        }
        ?>

        <div class="card-header">
            <h3 class="card-title">
                Nama Santri <strong><?= $IdSantri . ' - ' . $NamaSantri ?></strong> Kelas <?= $NamaKelas ?> T.A <?= $Tahun ?>
            </h3>

            <?php if (!empty($dataTabungan)) : ?>
                <h3 class="card-title float-right bg-warning text-white p-2 rounded">
                    Saldo: Rp. <?= number_format($dataTabungan[0]->Saldo, 0, ',', '.'); ?>
                </h3>
            <?php endif; ?>

            <button class="card-title float-right btn btn-success btn-sm" data-toggle="modal" data-target="#TransaksiTabungan<?= $IdSantri ?>">Transaksi <i class="fas fa-edit"></i></button>
        </div> <!-- /.card-header -->
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <?php
                    $tableHeadersFooter = '
                            <tr>
                                <th>No</th>
                                <th>Tanggal | Jam</th>
                                <th>Jenis Transaksi</th>
                                <th>Nominal</th>
                                <th>Saldo</th>
                                <th>Keterangan</th>
                            </tr>';
                    echo $tableHeadersFooter;
                    ?>

                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    // convert number to rupiah format
                    foreach ($dataTabungan as $transaksi) : ?>
                        <tr>
                            <td><?= $i++; ?></td>
                            <td><?= date('d-m-Y | H:i:s', strtotime($transaksi->UpdatedAt)); ?></td>
                            <td><?= htmlspecialchars($transaksi->JenisTransaksi, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>Rp. <?= number_format($transaksi->Nominal, 0, ',', '.'); ?></td>
                            <td>Rp. <?= number_format($transaksi->Saldo, 0, ',', '.'); ?></td>
                            <td><?= htmlspecialchars($transaksi->Keterangan, ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <?= $tableHeadersFooter ?>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>

<!-- Modal Transaksi Tabungan-->
<?php
// check if $dataTabungan is not empty
if (!empty($dataTabungan)) {
    $santri = $dataTabungan[0];
    // Check if $dataSantri is not empty
} else {
    $santri = null;
}

// Check if $dataSantri is not empty
if (!empty($santri)) {
?>
    <div class="modal fade" id="TransaksiTabungan<?= $santri->IdSantri ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Tabungan Santri</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/backend/tabungan/create/IdSantri" method="post">
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
                            <label for="JenisTransaksi">JenisTransaksi</label>
                            <select class="form-control" id="JenisTransaksi" name="JenisTransaksi" required onchange="updateNominal(this)">
                                <option value="">-- Pilih JenisTransaksi --</option>
                                <option value="Setoran">Setoran</option>
                                <option value="Penarikan">Penarikan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Nominal">Nominal</label>
                            <input type="text" class="form-control" id="Nominal<?= $santri->IdSantri ?>" name="Nominal" placeholder="Masukan Nominal" required oninput="formatRupiah(this); updateTerbilang(this)" min="100" max="1000000">
                            <input type="text" class="form-control" id="Terbilang<?= $santri->IdSantri ?>" name="Terbilang" placeholder="Terbilang" readonly>
                        </div>
                        <!-- Added Keterangan field -->
                        <div class="form-group">
                            <label for="Keterangan">Keterangan</label>
                            <textarea class="form-control" id="Keterangan<?= $santri->IdSantri ?>" name="Keterangan" placeholder="Masukan keterangan"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php } ?>
<script>
    function updateNominal(selectElement) {
        var kategori = selectElement.value;
        var modal = $(selectElement).closest('.modal');
        var nominalInput = modal.find('[id^=Nominal]'); // Select nominal input in current modal

        nominalInput.val(''); // Clear value

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

    function terbilang(angka) {
        const bilangan = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
            'sepuluh', 'sebelas'
        ];
        let temp;
        let hasil = '';

        if (angka < 12) {
            hasil = ' ' + bilangan[angka];
        } else if (angka < 20) {
            hasil = terbilang(angka - 10) + ' belas ';
        } else if (angka < 100) {
            temp = Math.floor(angka / 10);
            hasil = terbilang(temp) + ' puluh ' + terbilang(angka % 10);
        } else if (angka < 200) {
            hasil = ' seratus ' + terbilang(angka - 100);
        } else if (angka < 1000) {
            temp = Math.floor(angka / 100);
            hasil = terbilang(temp) + ' ratus ' + terbilang(angka % 100);
        } else if (angka < 1000000) {
            temp = Math.floor(angka / 1000);
            hasil = terbilang(temp) + ' ribu ' + terbilang(angka % 1000);
        } else if (angka < 1000000000) {
            temp = Math.floor(angka / 1000000);
            hasil = terbilang(temp) + ' juta ' + terbilang(angka % 1000000);
        }
        return capitalizeEachWord(hasil.trim());
    }

    function capitalizeEachWord(string) {
        return string.split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }
</script>
<?= $this->endSection(); ?>