<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="col-12">
    <?php echo session()->getFlashdata('pesan'); ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List Santri Prestasi</h3>
        </div>
        <div class="card-body">
            <table id="tabelPrestasiPerKelas" class="table table-bordered table-striped">
                <thead>
                    <?php
                    echo $tableHeaderFooter = '
                    <tr>
                    <th>Aksi</th>
                    <th>Nama Santri</th>
                    <th>Tingkat Kelas</th>
                    <th>Prestasi Terakhir</th>
                    </tr>
                    ';
                    ?>
                </thead>
                <tbody>
                    <?php foreach ($dataSantri as $santri) : ?>
                        <tr>
                            <td>
                                <div class="btn-group btn-group-sm w-100">
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#PrestasiBaru<?= $santri->IdSantri ?>">Baru&nbsp; <i class="fas fa-plus"></i></button>
                                    <button class="btn btn-warning" data-toggle="modal" data-target="#PrestasiEdit<?= $santri->IdSantri ?>">Edit&nbsp; <i class="fas fa-edit"></i></button>
                                    <button class="btn btn-info" data-toggle="modal" data-target="#PrestasiDetail<?= $santri->IdSantri ?>">Detail&nbsp; <i class="fas fa-eye"></i></button>
                                </div>
                            </td>
                            <td><?php echo $santri->NamaSantri; ?></td>
                            <td><?php echo $santri->NamaKelas; ?></td>
                            <td>
                                <?php
                                if (isset($santri->lastPrestasiList)) {
                                    $displayedMateri = [];
                                    $kategoriStatus = [];

                                    // Pertama, periksa apakah ada status Ulang untuk setiap kategori
                                    foreach ($santri->lastPrestasiList as $materi) {
                                        if (!isset($kategoriStatus[$materi->Kategori])) {
                                            $kategoriStatus[$materi->Kategori] = [];
                                        }
                                        $kategoriStatus[$materi->Kategori][] = $materi->Status;
                                    }

                                    foreach ($santri->lastPrestasiList as $materi) {
                                        // Skip jika materi dengan kategori yang sama sudah ditampilkan dan statusnya Selesai
                                        if (isset($displayedMateri[$materi->Kategori]) && $materi->Status == 'Selesai') {
                                            continue;
                                        }

                                        // Jika ada status Ulang dalam kategori yang sama, skip yang Selesai
                                        if (in_array('Ulang', $kategoriStatus[$materi->Kategori]) && $materi->Status == 'Selesai') {
                                            continue;
                                        }

                                        $color = '';
                                        if ($materi->Status == 'Ulang') {
                                            $color = 'orange';
                                        } elseif ($materi->Status == 'Lanjut') {
                                            $color = 'green';
                                        } elseif ($materi->Status == 'Selesai') {
                                            $color = 'red';
                                            // Tandai kategori ini sudah ditampilkan
                                            $displayedMateri[$materi->Kategori] = true;
                                        }

                                        echo '<button class="btn btn-warning btn-xs" data-toggle="modal" data-target="#PrestasiEditSingle' . $santri->IdSantri . $materi->IdMateriPelajaran . '"><i class="fas fa-edit"></i></button>';
                                        echo '&nbsp;&nbsp;' . ucfirst($materi->Kategori) . " | " . ucfirst($materi->NamaMateri) . ' - <span style="color:' . $color . '; font-weight:bold;">' . ucfirst($materi->Status) . '</span><br>';
                                    }
                                }
                                ?>
                            </td>
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

<!-- Modal Edit Prestasi individual-->
<?php
foreach ($dataSantri as $santri) :
    foreach ($santri->lastPrestasiList as $materi): ?>
        <div class="modal fade" id="PrestasiEditSingle<?= $santri->IdSantri . $materi->IdMateriPelajaran ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Prestasi Santri</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="/backend/prestasi/store" method="post">
                            <input type="hidden" id="IdTahunAjaran" name="IdTahunAjaran" value="<?= $santri->IdTahunAjaran ?>">
                            <input type="hidden" id="IdSantri" name="IdSantri" value="<?= $santri->IdSantri ?>">
                            <input type="hidden" id="IdKelas" name="IdKelas" value="<?= $santri->IdKelas ?>">
                            <input type="hidden" id="IdTpq" name="IdTpq" value="<?= $santri->IdTpq ?>">
                            <input type="hidden" id="IdGuru" name="IdGuru" value="<?= $santri->IdGuru ?>">
                            <input type="hidden" id="IdMateriPelajaran" name="IdMateriPelajaran" value="<?= $materi->IdMateriPelajaran ?>">
                            <div class="form-group">
                                <label for="NamaSantri">Nama Santri</label>
                                <input type="text" class="form-control" id="NamaSantri" name="NamaSantri" value="<?= htmlspecialchars($santri->NamaSantri, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="JenisPrestasi">Jenis Prestasi</label>
                                <input type="text" readonly class="form-control" id="JenisPrestasi" name="JenisPrestasi" value="<?= $materi->JenisPrestasi ?>">
                            </div>
                            <div class="form-group">
                                <div>
                                    <label for="Status">Status</label>
                                </div>
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn bg-olive <?= $materi->Status == 'Ulang' ? 'active' : '' ?>">
                                        <input type="radio" name="Status[<?= $materi->IdMateriPelajaran ?>]" id="Ulang<?= $materi->IdMateriPelajaran ?>" value="Ulang" required autocomplete="off" <?= $materi->Status == 'Ulang' ? 'checked' : '' ?>> Ulang
                                    </label>
                                    <label class="btn bg-olive <?= $materi->Status == 'Lanjut' ? 'active' : '' ?>">
                                        <input type="radio" name="Status[<?= $materi->IdMateriPelajaran ?>]" id="Lanjut<?= $materi->IdMateriPelajaran ?>" value="Lanjut" required autocomplete="off" <?= $materi->Status == 'Lanjut' ? 'checked' : '' ?>> Lanjut
                                    </label>
                                    <label class="btn bg-olive <?= $materi->Status == 'Selesai' ? 'active' : '' ?>">
                                        <input type="radio" name="Status[<?= $materi->IdMateriPelajaran ?>]" id="Selesai<?= $materi->IdMateriPelajaran ?>" value="Selesai" required autocomplete="off" <?= $materi->Status == 'Selesai' ? 'checked' : '' ?>> Selesai
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Keterangan">Keterangan</label>
                                <textarea readonly class="form-control"> Sebelumnya: <?= $materi->Keterangan ?></textarea>
                                <textarea class="form-control" id="Keterangan<?= $materi->IdMateriPelajaran ?>" name="Keterangan[<?= $materi->IdMateriPelajaran ?>]" placeholder="Masukan keterangan baru"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?php
    endforeach;
endforeach;
?>

<!-- Modal Edit Prestasi All-->
<?php foreach ($dataSantri as $santri) : ?>
    <div class="modal fade" id="PrestasiEdit<?= $santri->IdSantri ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Prestasi Santri</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/backend/prestasi/store" method="post">
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
                            <label for="JenisPrestasi">Jenis Prestasi</label>
                            <?php
                            $displayedMateri = [];
                            foreach ($santri->lastPrestasiList as $materi) {
                                if (isset($displayedMateri[$materi->Kategori])) {
                                    continue;
                                }
                            ?>
                                <input type="hidden" id="IdMateriPelajaran" name="IdMateriPelajaran[]" value="<?= $materi->IdMateriPelajaran ?>">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="JenisPrestasi[]" id="JenisPrestasi<?= $materi->IdMateriPelajaran ?>" value="<?= $materi->JenisPrestasi ?>">
                                    <label class="form-check-label" for="JenisPrestasi<?= $materi->IdMateriPelajaran ?>">
                                        <?= ucfirst($materi->Kategori) . " | " . ucfirst($materi->NamaMateri) ?>
                                    </label>
                                </div>
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn bg-olive <?= $materi->Status == 'Ulang' ? 'active' : '' ?>">
                                        <input type="radio" name="Status[<?= $materi->IdMateriPelajaran ?>]" id="Ulang<?= $materi->IdMateriPelajaran ?>" value="Ulang" required autocomplete="off" <?= $materi->Status == 'Ulang' ? 'checked' : '' ?>> Ulang
                                    </label>
                                    <label class="btn bg-olive <?= $materi->Status == 'Lanjut' ? 'active' : '' ?>">
                                        <input type="radio" name="Status[<?= $materi->IdMateriPelajaran ?>]" id="Lanjut<?= $materi->IdMateriPelajaran ?>" value="Lanjut" required autocomplete="off" <?= $materi->Status == 'Lanjut' ? 'checked' : '' ?>> Lanjut
                                    </label>
                                    <label class="btn bg-olive <?= $materi->Status == 'Selesai' ? 'active' : '' ?>">
                                        <input type="radio" name="Status[<?= $materi->IdMateriPelajaran ?>]" id="Selesai<?= $materi->IdMateriPelajaran ?>" value="Selesai" required autocomplete="off" <?= $materi->Status == 'Selesai' ? 'checked' : '' ?>> Selesai
                                    </label>
                                </div>
                                <div>
                                    <input type="text" class="form-control" id="Keterangan<?= $materi->IdMateriPelajaran ?>" name="Keterangan[<?= $materi->IdMateriPelajaran ?>]" placeholder="Masukan keterangan">
                                </div>
                            <?php
                                $displayedMateri[$materi->Kategori] = true;
                            }
                            ?>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach ?>

<!-- Modal Prestasi Baru-->
<?php foreach ($dataSantri as $santri) : ?>
    <div class="modal fade" id="PrestasiBaru<?= $santri->IdSantri ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Prestasi Santri Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/backend/prestasi/store" method="post">
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
                            <label for="JenisPrestasi">Jenis Prestasi</label>
                            <select class="form-control" id="JenisPrestasi" name="JenisPrestasi" required onchange="updateNominal(this)">
                                <option value="">-- Pilih Jenis Prestasi --</option>
                                <option value="Hafalan">Hafalan</option>
                                <option value="Iqra">Iqra</option>
                                <option value="Al-Quran">Al-Quran</option>
                            </select>
                        </div>
                        <!-- add form grup untuk materi pelajaran -->
                        <div class="form-group">
                            <label for="IdMateriPelajaran">Materi Pelajaran</label>
                            <select class="form-control" id="IdMateriPelajaran" name="IdMateriPelajaran" required>
                                <option value="">-- Pilih Materi Pelajaran --</option>
                                <?php foreach ($dataMateriPelajaran as $materi): ?>
                                    <?php if ($materi->IdKelas == $santri->IdKelas): ?>
                                        <option value="<?= $materi->IdMateri ?>"><?= $materi->Kategori . " | " . $materi->NamaMateri ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <div>
                                <label for="Status">Status</label>
                            </div>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn bg-olive">
                                    <input type="radio" name="Status" id="Ulang" value="Ulang" required autocomplete="off" checked> Ulang
                                </label>
                                <label class="btn bg-olive">
                                    <input type="radio" name="Status" id="Lanjut" value="Lanjut" required autocomplete="off"> Lanjut
                                </label>
                                <label class="btn bg-olive">
                                    <input type="radio" name="Status" id="Selesai" value="Selesai" required autocomplete="off"> Selesai
                                </label>
                            </div>
                        </div>
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
<?php endforeach ?>

<!-- Modal Detail Prestasi-->
<?php foreach ($dataSantri as $santri) : ?>
    <div class="modal fade" id="PrestasiDetail<?= $santri->IdSantri ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Detail Prestasi Santri</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="NamaSantri">Nama Santri</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($santri->NamaSantri, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button class="btn btn-success btn-sm" onclick="closeDetailAndOpenNew('<?= $santri->IdSantri ?>')">
                                <i class="fas fa-plus"></i> Tambah Prestasi Baru
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="tabelPrestasiPerKelasDetail<?= $santri->IdSantri ?>" class=" table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Kategori</th>
                                            <th>Materi</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($santri->lastPrestasiList)) : ?>
                                            <?php foreach ($santri->lastPrestasiList as $materi) : ?>
                                                <tr>
                                                    <td><?= ucfirst($materi->Kategori) ?></td>
                                                    <td><?= ucfirst($materi->NamaMateri) ?></td>
                                                    <td>
                                                        <?php
                                                        $color = '';
                                                        if ($materi->Status == 'Ulang') {
                                                            $color = 'orange';
                                                        } elseif ($materi->Status == 'Lanjut') {
                                                            $color = 'green';
                                                        } elseif ($materi->Status == 'Selesai') {
                                                            $color = 'red';
                                                        }
                                                        ?>
                                                        <button class="btn btn-warning btn-xs" onclick="closeDetailAndOpenEdit('<?= $santri->IdSantri . $materi->IdMateriPelajaran ?>')">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <span style="color:<?= $color ?>; font-weight:bold;"><?= ucfirst($materi->Status) ?></span>
                                                    </td>
                                                    <td><?= date('d-m-Y', strtotime($materi->Tanggal)) ?></td>
                                                    <td><?= $materi->Keterangan ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Belum ada data prestasi</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    initializeDataTableUmum('#tabelPrestasiPerKelas', true, true);

    //looping untuk inisialisasi data table detail
    <?php foreach ($dataSantri as $santri) : ?>
        initializeDataTableUmum('#tabelPrestasiPerKelasDetail<?= $santri->IdSantri ?>');
    <?php endforeach; ?>

    function closeDetailAndOpenEdit(id) {
        // Tutup modal detail
        $('.modal').modal('hide');
        // Buka modal edit setelah modal detail tertutup
        setTimeout(function() {
            $('#PrestasiEditSingle' + id).modal('show');
        }, 500);
    }

    function closeDetailAndOpenNew(id) {
        // Tutup modal detail
        $('.modal').modal('hide');
        // Buka modal baru setelah modal detail tertutup
        setTimeout(function() {
            $('#PrestasiBaru' + id).modal('show');
        }, 500);
    }
</script>
<?= $this->endSection(); ?>