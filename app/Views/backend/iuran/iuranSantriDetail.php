<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <?php echo session()->getFlashdata('pesan'); ?>
    <div class="card">
        <?php
        // Extracting the first result from $dataIuran (assuming it has at least one result)
        if (!empty($dataIuran)) {
            $firstResult = $dataIuran[0];
            $IdSantri = htmlspecialchars($firstResult->IdSantri, ENT_QUOTES, 'UTF-8');
            $NamaSantri = htmlspecialchars($firstResult->NamaSantri, ENT_QUOTES, 'UTF-8');
            $NamaKelas = htmlspecialchars($firstResult->IdKelas, ENT_QUOTES, 'UTF-8');
            // Format the Tahun with "/"
            $Tahun = $firstResult->IdTahunAjaran;
            if (strlen($Tahun) == 8) {
                $Tahun = substr($Tahun, 0, 4) . '/' . substr($Tahun, 4, 4);
            } else {
                $Tahun = 'Invalid Year Format';
            }
        } else {
            // Default values or handle the case when $dataIuran is empty
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
            <button class="btn btn-primary float-right" onclick="window.history.back();">Kembali</button>
        </div> <!-- /.card-header -->
        <div class="card-body">
            <table id="TableIuranSantri" class="table table-bordered table-striped">
                <thead>
                    <?php
                    $tableHeadersFooter = '
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>Bulan</th>
                                <th>Tanggal Pencatatan</th>
                                <th>Nominal</th>
                                <th>Aksi</th>
                            </tr>';
                    echo $tableHeadersFooter;
                    ?>

                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    // convert number to rupiah format
                    foreach ($dataIuran as $Iuran) :
                    ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $Iuran->Kategori; ?></td>
                            <td><?php echo $Iuran->Bulan; ?></td>
                            <td><?php echo $Iuran->TanggalSerahTerima; ?></td>
                            <td><?php echo 'Rp. ' . number_format($Iuran->Nominal, 0, ',', '.'); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#EditIuran<?= $Iuran->Id  ?>"><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                    <?php
                    endforeach ?>
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

<!-- Modal Edit Data-->
<?php
foreach ($dataIuran as $Iuran) : ?>
    <div class="modal fade" id="EditIuran<?= $Iuran->Id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content ">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Iuran </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('/backend/iuranBulanan/update/' . $Iuran->Id) ?>" method="POST">
                        <input type="hidden" name="Id" value=<?= $Iuran->Id ?>>
                        <input type="hidden" name="IdSantri" value=<?= $Iuran->IdSantri ?>>
                        <input type="hidden" name="IdTahunAjaran" value=<?= $Iuran->IdTahunAjaran ?>>
                        <div class="form-group">
                            <label for="FormProfilTpq">Bulan</label>
                            <span class="form-control" id="FormProfilTpq"><?= $Iuran->Bulan ?></span>
                        </div>

                        <div class="form-group">
                            <label for="Kategori">Kategori</label>
                            <select class="form-control" id="Kategori" name="Kategori" required>
                                <option value="<?= $Iuran->Kategori ?>" selected><?= $Iuran->Kategori ?></option>
                                <option value="Iuran">Iuran</option>
                                <option value="Infaq">Infaq</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="FormProfilTpq">Nominal</label>
                            <input type="text" id="Nominal<?= $Iuran->IdSantri ?>" name="Nominal" class="form-control" id="FormProfilTpq" required oninput="formatRupiah(this); updateTerbilang(this)"
                                placeholder=" Ketik Nominal" value="<?= $Iuran->Nominal ?>">
                            <input type="text" class="form-control" id="Terbilang<?= $Iuran->IdSantri ?>" name="Terbilang<?= $Iuran->IdSantri ?>" placeholder="Terbilang" readonly>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>Simpan</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
<?php endforeach ?>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    initializeDataTableUmum('#TableIuranSantri');

    // Tambahkan event handler untuk modal menggunakan delegasi event
    $(document).on('shown.bs.modal', '[id^="EditIuran"]', function() {
        const modal = $(this);
        const nominalInput = modal.find('[name="Nominal"]')[0];
        if (nominalInput) {
            formatRupiah(nominalInput);
            updateTerbilang(nominalInput);
        }
    });

    function formatRupiah(input) {
        // Hapus semua karakter non-digit
        let value = input.value.replace(/[^\d]/g, '');

        // Format ke format Rupiah
        if (value !== "") {
            value = parseInt(value, 10).toLocaleString('id-ID');
            input.value = 'Rp. ' + value;
        } else {
            input.value = '';
        }
    }

    function updateTerbilang(input) {
        const modal = $(input).closest('.modal');
        const terbilangInput = modal.find('[name^="Terbilang"]')[0];

        // Hapus semua karakter non-digit
        let value = input.value.replace(/[^\d]/g, '');

        if (value === '') {
            terbilangInput.value = '';
            return;
        }

        // Konversi ke angka
        const angka = parseInt(value);

        // Update nilai terbilang
        terbilangInput.value = terbilang(angka) + ' Rupiah';
    }
</script>
<?= $this->endSection(); ?>