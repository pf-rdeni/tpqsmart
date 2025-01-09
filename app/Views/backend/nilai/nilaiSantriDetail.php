<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <?php echo session()->getFlashdata('pesan'); ?>
    <div class="card">
        <?php
        // Extracting the first result from $dataNilai (assuming it has at least one result)
        $dataNilai = $nilai->getResult();
        if (!empty($dataNilai)) {
            $firstResult = $dataNilai[0];
            $IdSantri = htmlspecialchars($firstResult->IdSantri, ENT_QUOTES, 'UTF-8');
            $NamaSantri = htmlspecialchars($firstResult->NamaSantri, ENT_QUOTES, 'UTF-8');
            $Semester = htmlspecialchars($firstResult->Semester, ENT_QUOTES, 'UTF-8');
            $NamaKelas = htmlspecialchars($firstResult->NamaKelas, ENT_QUOTES, 'UTF-8');
            // Format the Tahun with "/"
            $Tahun = $firstResult->IdTahunAjaran;
            if (strlen($Tahun) == 8) {
                $Tahun = substr($Tahun, 0, 4) . '/' . substr($Tahun, 4, 4);
            } else {
                $Tahun = 'Invalid Year Format';
            }
        } else {
            // Default values or handle the case when $dataNilai is empty
            $NamaSantri = "";
            $Tahun = "";
            $Semester = "";
            $IdSantri = "";
            $NamaKelas = "";
        }
        ?>

        <div class="card-header">
            <h3 class="card-title">
                Data Nilai Santri <strong><?= $IdSantri . ' - ' . $NamaSantri ?></strong> Kelas <?= $NamaKelas ?> Tahun <?= $Tahun ?> Semester <?= $Semester ?>
            </h3>
        </div> <!-- /.card-header -->
        <div class="card-body">
            <table id="TabelNilaiPerSemester" class="table table-bordered table-striped">
                <thead>
                    <?php
                    $tableHeadersFooter =
                        '<tr>';
                    if ($pageEdit) {
                        $tableHeadersFooter .= '<th>Aksi</th>';
                    }
                    $tableHeadersFooter .=
                        '<th>Kategori</th>
                                <th>Id - Nama Materi</th>
                                <th>Nilai</th>
                                </tr>';

                    echo $tableHeadersFooter
                    ?>

                </thead>
                <tbody>
                    <?php
                    $MainDataNilai = $nilai->getResult();
                    foreach ($MainDataNilai as $DataNilai) : ?>

                        <tr>
                            <?php if ($pageEdit) {
                                $isNilaiPositive = $DataNilai->Nilai > 0;
                                $isGuruPendamping4 = $guruPendamping == 4; // Assuming $guruPendamping is the value of $IdJabatan Guru Kelas bukan Wali Kelas

                                $btnClass = $isNilaiPositive ? ($isGuruPendamping4 ? 'btn-success' : 'btn-warning') : 'btn-primary';
                                $faClass = $isNilaiPositive ? ($isGuruPendamping4 ? 'fa-eye' : 'fa-edit') : 'fa-plus';
                                $name = $isNilaiPositive ? ($isGuruPendamping4 ? 'View' : 'Edit') : 'Add';
                                if ($name == 'View') {
                                    // button disabled
                                    $disabled = 'disabled';
                                } else {
                                    $disabled = '';
                                }
                            ?>
                                <td>
                                    <button id="EditNilai-<?= $DataNilai->Id ?>" class="btn <?= $btnClass ?> btn-sm" onclick="showModalEditNilai('<?= $DataNilai->Id ?>')" <?= $disabled ?>>
                                        <i class="fas <?= $faClass ?>"></i><span style=" margin-left: 5px;"></span><?= $name ?>
                                    </button>
                                </td>
                            <?php } ?>
                            <td><?php echo $DataNilai->Kategori; ?></td>
                            <td><?php echo $DataNilai->IdMateri . ' - ' . $DataNilai->NamaMateri; ?></td>
                            <td>
                                <input type="text" name="Nilai-<?= $DataNilai->Id ?>" id="Nilai-<?= $DataNilai->Id ?>" class="form-control" value="<?php echo $DataNilai->Nilai; ?>" readonly
                                    style="border: <?= $DataNilai->Nilai == 0 ? '2px solid red' : '2px solid green' ?>;" />
                            </td>
                        </tr>

                    <?php endforeach ?>
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
$MainDataNilai = $nilai->getResult();
foreach ($MainDataNilai as $DataNilai) : ?>
    <div class="modal fade" id="EditNilai<?= $DataNilai->Id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content ">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Update Nilai </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('backend/nilai/update/' . $pageEdit) ?>" method="POST">
                        <input type="hidden" name="Id" value=<?= $DataNilai->Id ?>>
                        <div class="form-group">
                            <label for="FormProfilTpq">Kategori</label>
                            <span class="form-control" id="FormProfilTpq"><?= $DataNilai->Kategori ?></span>
                        </div>

                        <div class="form-group">
                            <label for="FormProfilTpq">Id-Nama Materi</label>
                            <span class="form-control" id="FormProfilTpq"><?= $DataNilai->IdMateri . ' - ' . $DataNilai->NamaMateri ?></span>
                        </div>

                        <div class="form-group">
                            <label for="FormProfilTpq">Nilai</label>
                            <input type="number" name="Nilai" class="form-control" id="NilaiEditModal-<?= $DataNilai->Id ?>" required
                                placeholder="<?= $DataNilai->Nilai > 0 ? '' : 'Ketik Nilai' ?>" value="<?= $DataNilai->Nilai > 0 ? $DataNilai->Nilai : '' ?>" oninvalid="this.setCustomValidity('Nilai harus antara 50 dan 100')"
                                oninput="this.setCustomValidity('')">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="tutupModal-<?= $DataNilai->Id ?>">
                                <i class="fas fa-times"></i> Keluar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>Simpan
                            </button>
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
    initializeDataTableUmum("#TabelNilaiPerSemester", true, true);

    // Tambahkan variabel untuk menyimpan status perubahan
    let isChanged = false;
    let nilaiLama = 0;

    // Fungsi untuk menampilkan modal edit nilai dan menangani pengiriman form
    function showModalEditNilai(id) {
        $('#EditNilai' + id).modal('show');
        // Ambil nilai lama
        nilaiLama = $('#NilaiEditModal-' + id).val();

        // Tambahkan handler untuk form submission
        $('#EditNilai' + id + ' form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#EditNilai' + form.find('input[name="Id"]').val()).modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data nilai berhasil diperbarui',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        const newValue = response.newValue; // Misalkan response.newValue adalah nilai baru yang ingin diset
                        const idNilai = form.find('input[name="Id"]').val();
                        $('#Nilai-' + idNilai).val(newValue);
                        // Ubah border warna menjadi hijau
                        $('#Nilai-' + idNilai).css({
                            'border': '2px solid green'
                        });
                        // Ubah warna button dan icon dan teks tombol berdasarkan nilai baru
                        if (newValue > 0) {
                            $('#EditNilai-' + id).html('<i class="fas fa-edit"></i><span style="margin-left: 5px;"></span>Edit');
                            $('#EditNilai-' + id).removeClass('btn-primary').addClass('btn-warning'); // Ubah kelas tombol
                        }

                        isChanged = false; // Set status perubahan menjadi false

                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menyimpan data',
                    });
                }
            });
        });

        // Ubah handler untuk tombol Keluar
        $('#tutupModal-' + id).on('click', function() {

            nilaiBaru = $('#NilaiEditModal-' + id).val();
            if (isChanged) {
                Swal.fire({
                    title: 'Perhatian',
                    html: 'Nilai yang sudah berubah <span style="color: red; font-weight: bold;">' + nilaiLama + '</span> menjadi <span style="color: green; font-weight: bold;">' + nilaiBaru + '</span> tidak akan disimpan. Apakah Anda yakin ingin keluar?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya,Keluar',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        isChanged = false; // Set status perubahan menjadi false
                        $('#NilaiEditModal-' + id).val(nilaiLama); // Kembalikan nilai ke nilai lama
                        $('#EditNilai' + id).modal('hide'); // Tutup modal jika OK dipilih
                    }
                });
            } else {
                $('#EditNilai' + id).modal('hide'); // Tutup modal jika tidak ada perubahan
            }
        });

        $('#EditNilai' + id + ' form').on('change', 'input[name="Nilai"]', function() {
            isChanged = true; // Set status perubahan menjadi true jika ada perubahan nilai
        });
    }
</script>
<?= $this->endSection(); ?>