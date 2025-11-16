<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<?php
$helpModel = new \App\Models\HelpFunctionModel();
?>

<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-sm-12">
                    <button class="btn btn-primary" data-toggle="modal"
                        data-target="#GuruKelasNew"><i class="fas fa-edit"></i>Tambah Pengaturan Guru</button>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-filter"></i> Filter Data</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="filterTahunAjaran">Tahun Ajaran</label>
                                        <select id="filterTahunAjaran" class="form-control form-control-sm">
                                            <option value="">Semua Tahun Ajaran</option>
                                            <?php
                                            // Ambil tahun ajaran saat ini untuk default selection
                                            if (!isset($tahunAjaranSaatIni)) {
                                                $tahunAjaranSaatIni = $helpModel->getTahunAjaranSaatIni();
                                            }
                                            $tahunAjaranBerikutnya = $helpModel->getTahuanAjaranBerikutnya($tahunAjaranSaatIni);
                                            $namaTahunAjaranBerikutnya = $helpModel->convertTahunAjaran($tahunAjaranBerikutnya);
                                            ?>
                                            <option value="next"><?= $namaTahunAjaranBerikutnya ?> (Berikutnya)</option>
                                            <?php foreach ($tahunAjaranList as $tahun): ?>
                                                <option value="<?= $tahun['IdTahunAjaran'] ?>" <?= $tahun['IdTahunAjaran'] == $tahunAjaranSaatIni ? 'selected' : '' ?>><?= $tahun['NamaTahunAjaran'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="filterPosisi">Posisi</label>
                                        <select id="filterPosisi" class="form-control form-control-sm">
                                            <option value="">Semua Posisi</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="filterKelas">Kelas</label>
                                        <select id="filterKelas" class="form-control form-control-sm">
                                            <option value="">Semua Kelas</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="button" id="btnFilter" class="btn btn-primary btn-sm btn-block">
                                                <i class="fas fa-filter"></i> Terapkan Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Filter Section -->
            <table id="tabelGuruKelas" class="table table-bordered table-striped">
                <thead>
                    <?= $headerfooter = '
                    <tr>
                        <th>No</th>
                        <th>Tahun Ajaran</th>
                        <th>Nama Guru</th>
                        <th>Posisi & Kelas</th>
                    </tr>'
                    ?>
                </thead>
                <tbody>
                    <?php
                    // Hapus duplikasi record yang sama persis (IdGuru, IdKelas, IdJabatan, IdTahunAjaran sama)
                    // Konversi ke string untuk memastikan konsistensi
                    $uniqueRecords = [];
                    $seenKeys = [];
                    foreach ($guruKelas as $row) {
                        $uniqueKey = (string)$row->IdGuru . '_' . (string)$row->IdKelas . '_' . (string)$row->IdJabatan . '_' . (string)$row->IdTahunAjaran;
                        if (!isset($seenKeys[$uniqueKey])) {
                            $seenKeys[$uniqueKey] = true;
                            $uniqueRecords[] = $row;
                        }
                    }

                    // Kelompokkan data berdasarkan IdGuru dan IdTahunAjaran
                    // Pastikan IdTahunAjaran dikonversi ke string untuk konsistensi
                    $groupedData = [];
                    foreach ($uniqueRecords as $row) {
                        // Konversi ke string untuk memastikan konsistensi
                        $idGuru = (string)$row->IdGuru;
                        $idTahunAjaran = (string)$row->IdTahunAjaran;
                        $key = $idGuru . '_' . $idTahunAjaran;

                        if (!isset($groupedData[$key])) {
                            $groupedData[$key] = [
                                'IdGuru' => $idGuru,
                                'Nama' => $row->Nama,
                                'IdTahunAjaran' => $idTahunAjaran,
                                'TahunAjaran' => $helpModel->convertTahunAjaran($idTahunAjaran),
                                'records' => [] // Simpan semua record untuk aksi edit/delete
                            ];
                        }
                        // Simpan record untuk aksi
                        $groupedData[$key]['records'][] = $row;
                    }

                    $no = 1;
                    foreach ($groupedData as $group) :
                        // Urutkan records berdasarkan posisi, lalu kelas
                        usort($group['records'], function ($a, $b) {
                            $posisiCompare = strcmp($a->NamaJabatan, $b->NamaJabatan);
                            if ($posisiCompare !== 0) {
                                return $posisiCompare;
                            }
                            return strcmp($a->NamaKelas, $b->NamaKelas);
                        });
                        $totalRecords = count($group['records']);
                        $currentPosisi = '';
                        $rowIndex = 0;
                        foreach ($group['records'] as $record) :
                            $rowIndex++;
                            $isFirstRow = ($rowIndex === 1);
                            $showPosisi = ($currentPosisi !== $record->NamaJabatan);
                            if ($showPosisi) {
                                $currentPosisi = $record->NamaJabatan;
                            }
                    ?>
                            <tr data-tahun-ajaran="<?= $group['IdTahunAjaran'] ?>">
                                <?php if ($isFirstRow) : ?>
                                    <td rowspan="<?= $totalRecords ?>" style="vertical-align: top;"><?= $no++; ?></td>
                                    <td rowspan="<?= $totalRecords ?>" style="vertical-align: top;"><?= $group['TahunAjaran'] ?></td>
                                    <td rowspan="<?= $totalRecords ?>" style="vertical-align: top;"><?= $group['Nama'] ?></td>
                                <?php endif; ?>
                                <td>
                                    <?php if ($showPosisi) : ?>
                                        <div class="font-weight-bold mb-1"><?= $record->NamaJabatan ?></div>
                                    <?php endif; ?>
                                    <div class="d-flex align-items-center">
                                        <div class="btn-group btn-group-sm mr-2">
                                            <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                data-target="#GuruKelasEdit<?= $record->Id ?>" title="Edit <?= $record->NamaKelas ?> - <?= $record->NamaJabatan ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm"
                                                onclick="deleteDataGuruKelas('<?= base_url('backend/GuruKelas/delete/' . $record->Id) ?>')"
                                                data-id="<?= $record->Id ?>" title="Hapus <?= $record->NamaKelas ?> - <?= $record->NamaJabatan ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <span><?= $record->NamaKelas ?></span>
                                    </div>
                                </td>
                            </tr>
                    <?php
                        endforeach;
                    endforeach; ?>
                </tbody>
                <tfoot>
                    <?= $headerfooter ?>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>

<!-- Modal Edit Data-->
<?php foreach ($guruKelas as $row) : ?>
    <div class="modal fade" id="GuruKelasEdit<?= $row->Id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content ">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formEdit<?= $row->Id ?>" action="<?= base_url('backend/GuruKelas/store') ?>" method="POST">
                        <div class="form-group">
                            <input type="hidden" name="Id" id="FormGuruKelas" value="<?= $row->Id ?>">
                            <input type="hidden" name="IdTpq" id="FormGuruKelas" value="<?= $row->IdTpq ?>">
                        </div>
                        <div class="form-group">
                            <label for="FormGuruKelas">Tahun Ajaran</label>
                            <select name="IdTahunAjaran" class="form-control" id="FormGuruKelas">
                                <option value="" disabled selected>Pilih Tahun Ajaran</option>
                                <option value="<?= $helpModel->getTahunAjaranSaatIni() ?>" <?= $row->IdTahunAjaran == $helpModel->getTahunAjaranSaatIni() ? 'selected' : ''; ?>>Saat ini <?= $helpModel->convertTahunAjaran($helpModel->getTahunAjaranSaatIni()) ?> </option>
                                <option value="<?= $helpModel->getTahunAjaranSebelumnya($helpModel->getTahunAjaranSaatIni()) ?>" <?= $row->IdTahunAjaran == $helpModel->getTahunAjaranSebelumnya($helpModel->getTahunAjaranSaatIni()) ? 'selected' : ''; ?>>Sebelumnya <?= $helpModel->convertTahunAjaran($helpModel->getTahunAjaranSebelumnya($helpModel->getTahunAjaranSaatIni())) ?> </option>
                                <option value="<?= $helpModel->getTahuanAjaranBerikutnya($helpModel->getTahunAjaranSaatIni()) ?>" <?= $row->IdTahunAjaran == $helpModel->getTahuanAjaranBerikutnya($helpModel->getTahunAjaranSaatIni()) ? 'selected' : ''; ?>>Berikutnya <?= $helpModel->convertTahunAjaran($helpModel->getTahuanAjaranBerikutnya($helpModel->getTahunAjaranSaatIni())) ?> </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="FormGuruKelas">Nama Kelas</label>
                            <select name="IdKelas" class="form-control" id="FormGuruKelas">
                                <option value="" disabled selected>Pilih Nama Kelas</option>
                                <?php foreach ($helpModel->getDataKelas() as $kelas): ?>
                                    <option value="<?= $kelas['IdKelas']; ?>" <?= $row->IdKelas == $kelas['IdKelas'] ? 'selected' : ''; ?>>
                                        <?= $kelas['NamaKelas']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="FormGuruKelas">Nama Guru</label>
                            <select name="IdGuru" class="form-control" id="FormGuruKelas">
                                <option value="" disabled selected>Pilih Nama Guru</option>
                                <?php
                                foreach ($helpModel->getDataGuru($row->IdTpq) as $guru): ?>
                                    <option value="<?= $guru['IdGuru']; ?>" <?= $row->IdGuru == $guru['IdGuru'] ? 'selected' : ''; ?>>
                                        <?= $guru['Nama']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="FormGuruKelas">Posisi</label>
                            <select name="IdJabatan" class="form-control" id="FormGuruKelas">
                                <option value="" disabled selected>Pilih Sebagai</option>
                                <?php
                                foreach ($helpModel->getDataJabatan() as $Jabatan): ?>
                                    <option value="<?= $Jabatan['IdJabatan'] ?>" <?= $row->IdJabatan == $Jabatan['IdJabatan'] ? 'selected' : '' ?>>
                                        <?= $Jabatan['NamaJabatan'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="saveDataGuruKelas(this)"><i class="fas fa-save"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach ?>

<div class="modal fade" id="GuruKelasNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content ">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formNew" action="<?= base_url('backend/GuruKelas/store') ?>" method="POST">
                    <div class="form-group">
                        <input type="hidden" name="IdTpq" id="FormGuruKelas" value="<?= $dataTpq ?>">
                    </div>
                    <div class="form-group" id="formGroupTahunAjaran">
                        <label for="FormGuruKelas">Tahun Ajaran</label>
                        <select name="IdTahunAjaran" class="form-control" id="FormGuruKelas">
                            <?php
                            $tahunAjaranSaatIni = $helpModel->getTahunAjaranSaatIni();
                            ?>
                            <option value="" disabled>Pilih Tahun Ajaran</option>
                            <option value="<?= $tahunAjaranSaatIni ?>" selected>Saat ini <?= $helpModel->convertTahunAjaran($tahunAjaranSaatIni) ?> </option>
                            <option value="<?= $helpModel->getTahunAjaranSebelumnya($tahunAjaranSaatIni) ?>">Sebelumnya <?= $helpModel->convertTahunAjaran($helpModel->getTahunAjaranSebelumnya($tahunAjaranSaatIni)) ?> </option>
                            <option value="<?= $helpModel->getTahuanAjaranBerikutnya($tahunAjaranSaatIni) ?>">Berikutnya <?= $helpModel->convertTahunAjaran($helpModel->getTahuanAjaranBerikutnya($tahunAjaranSaatIni)) ?> </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="FormGuruKelas">Nama Kelas</label>
                        <select name="IdKelas" class="form-control" id="selectNamaKelas">
                            <option value="" disabled selected>Pilih Nama Kelas</option>
                            <?php foreach ($helpModel->getDataKelas() as $kelas): ?>
                                <option value="<?= $kelas['IdKelas']; ?>"><?= $kelas['NamaKelas']; ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" id="formGroupNamaGuru">
                        <label for="FormGuruKelas">Nama Guru</label>
                        <select name="IdGuru" class="form-control" id="FormGuruKelas">
                            <option value="" disabled selected>Pilih Nama Guru</option>
                            <?php
                            foreach ($helpModel->getDataGuru($dataTpq) as $guru): ?>
                                <option value="<?= $guru['IdGuru']; ?>"><?= $guru['Nama']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="FormGuruKelas">Posisi</label>
                        <select name="IdJabatan" class="form-control" id="selectPosisi">
                            <option value="" disabled selected>Pilih Sebagai</option>
                            <?php
                            foreach ($helpModel->getDataJabatan() as $Jabatan): ?>
                                <option value="<?= $Jabatan['IdJabatan'] ?>"><?= $Jabatan['NamaJabatan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="saveDataGuruKelas(this)"><i class="fas fa-save"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
//section scripts
<?= $this->section('scripts'); ?>
<script>
    const helpModel = {
        convertTahunAjaran: function(idTahunAjaran) {
            // Konversi tahun ajaran dari format 20232024 ke 2023/2024
            const str = String(idTahunAjaran);
            if (str.length === 8) {
                return str.substring(0, 4) + '/' + str.substring(4);
            }
            return idTahunAjaran;
        }
    };

    // Flag untuk menandai apakah modal dibuka dari row guru
    let isOpenedFromRow = false;

    // Fungsi untuk me-render tabel dari data
    function renderTable(responseData) {
        const tbody = $('#tabelGuruKelas tbody');
        tbody.empty();

        const data = responseData.data || [];
        const guruWithoutData = responseData.guruWithoutData || [];
        const tahunAjaran = responseData.TahunAjaran || '';
        const idTahunAjaran = responseData.IdTahunAjaran || '';
        let no = 1;

        // Jika ada data guru kelas, tampilkan dengan data mereka
        if (data && data.length > 0) {
            // Hapus duplikasi dan kelompokkan data
            const uniqueRecords = [];
            const seenKeys = {};
            data.forEach(function(row) {
                const uniqueKey = String(row.IdGuru) + '_' + String(row.IdKelas) + '_' + String(row.IdJabatan) + '_' + String(row.IdTahunAjaran);
                if (!seenKeys[uniqueKey]) {
                    seenKeys[uniqueKey] = true;
                    uniqueRecords.push(row);
                }
            });

            // Kelompokkan data berdasarkan IdGuru dan IdTahunAjaran
            const groupedData = {};
            uniqueRecords.forEach(function(row) {
                const idGuru = String(row.IdGuru);
                const idTahunAjaran = String(row.IdTahunAjaran);
                const key = idGuru + '_' + idTahunAjaran;

                if (!groupedData[key]) {
                    groupedData[key] = {
                        IdGuru: idGuru,
                        Nama: row.Nama,
                        IdTahunAjaran: idTahunAjaran,
                        TahunAjaran: helpModel.convertTahunAjaran(idTahunAjaran),
                        records: []
                    };
                }
                groupedData[key].records.push(row);
            });

            // Urutkan dan render
            Object.keys(groupedData).forEach(function(key) {
                const group = groupedData[key];

                // Urutkan records berdasarkan posisi, lalu kelas
                group.records.sort(function(a, b) {
                    const posisiCompare = (a.NamaJabatan || '').localeCompare(b.NamaJabatan || '');
                    if (posisiCompare !== 0) {
                        return posisiCompare;
                    }
                    return (a.NamaKelas || '').localeCompare(b.NamaKelas || '');
                });

                const totalRecords = group.records.length;
                let currentPosisi = '';
                let rowIndex = 0;

                group.records.forEach(function(record) {
                    rowIndex++;
                    const isFirstRow = (rowIndex === 1);
                    const isLastRow = (rowIndex === totalRecords);
                    const showPosisi = (currentPosisi !== record.NamaJabatan);
                    if (showPosisi) {
                        currentPosisi = record.NamaJabatan;
                    }

                    let rowHtml = '<tr data-tahun-ajaran="' + group.IdTahunAjaran + '">';

                    if (isFirstRow) {
                        // Baris pertama: tambahkan semua kolom dengan rowspan
                        rowHtml += '<td rowspan="' + totalRecords + '" style="vertical-align: top;">' + no++ + '</td>';
                        rowHtml += '<td rowspan="' + totalRecords + '" style="vertical-align: top;">' + group.TahunAjaran + '</td>';
                        rowHtml += '<td rowspan="' + totalRecords + '" style="vertical-align: top;">' + group.Nama + '</td>';
                    } else {
                        // Baris berikutnya: DataTable membutuhkan jumlah kolom yang sama
                        // Rowspan sudah menangani kolom sebelumnya, jadi kita tidak perlu menambahkan kolom lagi
                        // Tapi DataTable akan menghitung kolom dari baris pertama, jadi ini seharusnya OK
                    }

                    // Kolom terakhir selalu ada
                    rowHtml += '<td>';
                    if (showPosisi) {
                        rowHtml += '<div class="font-weight-bold mb-1">' + (record.NamaJabatan || '') + '</div>';
                    }
                    rowHtml += '<div class="d-flex align-items-center">';
                    rowHtml += '<div class="btn-group btn-group-sm mr-2">';
                    rowHtml += '<button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#GuruKelasEdit' + record.Id + '" title="Edit ' + (record.NamaKelas || '') + ' - ' + (record.NamaJabatan || '') + '">';
                    rowHtml += '<i class="fas fa-edit"></i></button>';
                    rowHtml += '<button class="btn btn-danger btn-sm" onclick="deleteDataGuruKelas(\'' + '<?= base_url('backend/GuruKelas/delete/') ?>' + record.Id + '\')" data-id="' + record.Id + '" title="Hapus ' + (record.NamaKelas || '') + ' - ' + (record.NamaJabatan || '') + '">';
                    rowHtml += '<i class="fas fa-trash"></i></button>';
                    rowHtml += '</div>';
                    rowHtml += '<span>' + (record.NamaKelas || '') + '</span>';
                    rowHtml += '</div>';
                    // Tambahkan tombol "Tambah Pengaturan" di baris terakhir (paling bawah)
                    if (isLastRow) {
                        rowHtml += '<div class="mt-2">';
                        rowHtml += '<button type="button" class="btn btn-success btn-sm" onclick="openAddModalWithGuru(\'' + group.IdGuru + '\', \'' + group.Nama.replace(/'/g, "\\'") + '\', \'' + group.IdTahunAjaran + '\', \'' + group.TahunAjaran + '\')" title="Tambah Pengaturan untuk ' + group.Nama.replace(/'/g, "\\'") + '">';
                        rowHtml += '<i class="fas fa-plus"></i> Tambah Pengaturan';
                        rowHtml += '</button>';
                        rowHtml += '</div>';
                    }
                    rowHtml += '</td></tr>';

                    tbody.append(rowHtml);
                });
            });
        }

        // Tambahkan guru yang belum punya data dengan tombol +
        if (guruWithoutData && guruWithoutData.length > 0) {
            guruWithoutData.forEach(function(guru) {
                const rowHtml = '<tr>' +
                    '<td>' + no++ + '</td>' +
                    '<td>' + tahunAjaran + '</td>' +
                    '<td>' + guru.Nama + '</td>' +
                    '<td>' +
                    '<button type="button" class="btn btn-success btn-sm" onclick="openAddModalWithGuru(\'' + guru.IdGuru + '\', \'' + guru.Nama.replace(/'/g, "\\'") + '\', \'' + idTahunAjaran + '\', \'' + tahunAjaran + '\')" title="Tambah Pengaturan untuk ' + guru.Nama.replace(/'/g, "\\'") + '">' +
                    '<i class="fas fa-plus"></i> Tambah Pengaturan' +
                    '</button>' +
                    '</td>' +
                    '</tr>';
                tbody.append(rowHtml);
            });
        }

        // Jika tidak ada data sama sekali
        if ((!data || data.length === 0) && (!guruWithoutData || guruWithoutData.length === 0)) {
            tbody.append('<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>');
        }
    }

    // Fungsi untuk membuka modal tambah dengan data yang sudah diisi
    function openAddModalWithGuru(IdGuru, NamaGuru, IdTahunAjaran, TahunAjaran) {
        // Set flag bahwa modal dibuka dari row
        isOpenedFromRow = true;

        // Set nilai form di modal
        $('#GuruKelasNew select[name="IdGuru"]').val(IdGuru);
        $('#GuruKelasNew select[name="IdTahunAjaran"]').val(IdTahunAjaran);

        // Trigger change event untuk memastikan select ter-update
        $('#GuruKelasNew select[name="IdGuru"]').trigger('change');
        $('#GuruKelasNew select[name="IdTahunAjaran"]').trigger('change');

        // Hide field Tahun Ajaran dan Nama Guru
        $('#formGroupTahunAjaran').hide();
        $('#formGroupNamaGuru').hide();

        // Reset form Nama Kelas dan Posisi
        $('#selectNamaKelas').val('').prop('disabled', false);
        $('#selectPosisi').val('').prop('disabled', false);

        // Buka modal
        $('#GuruKelasNew').modal('show');

        // Fokus ke field Posisi setelah modal terbuka
        setTimeout(function() {
            $('#selectPosisi').focus();
        }, 500);
    }

    // Reset form ketika modal ditutup atau dibuka dari tombol header
    $('#GuruKelasNew').on('hidden.bs.modal', function() {
        // Show kembali field yang di-hide
        $('#formGroupTahunAjaran').show();
        $('#formGroupNamaGuru').show();

        // Reset form
        $('#formNew')[0].reset();
    });

    // Ketika modal dibuka dari tombol header (bukan dari row), pastikan semua field terlihat
    $('#GuruKelasNew').on('show.bs.modal', function(e) {
        // Jika tidak dibuka dari openAddModalWithGuru (dari tombol header), show semua field
        if (!isOpenedFromRow) {
            $('#formGroupTahunAjaran').show();
            $('#formGroupNamaGuru').show();
        }
        // Reset flag
        isOpenedFromRow = false;
    });

    // Fungsi untuk mengupdate filter Posisi dan Kelas berdasarkan tahun ajaran
    function updateFilterOptions(IdTahunAjaran) {
        return new Promise(function(resolve, reject) {
            const url = '<?= base_url('backend/GuruKelas/getFilterOptions') ?>';
            const params = IdTahunAjaran ? '?IdTahunAjaran=' + IdTahunAjaran : '';

            fetch(url + params)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update filter Posisi
                        const filterPosisi = $('#filterPosisi');
                        const currentPosisi = filterPosisi.val();
                        filterPosisi.empty();
                        filterPosisi.append('<option value="">Semua Posisi</option>');
                        data.posisi.forEach(function(posisi) {
                            filterPosisi.append('<option value="' + posisi.IdJabatan + '">' + posisi.NamaJabatan + '</option>');
                        });
                        if (currentPosisi) {
                            filterPosisi.val(currentPosisi);
                        }

                        // Update filter Kelas
                        const filterKelas = $('#filterKelas');
                        const currentKelas = filterKelas.val();
                        filterKelas.empty();
                        filterKelas.append('<option value="">Semua Kelas</option>');
                        data.kelas.forEach(function(kelas) {
                            filterKelas.append('<option value="' + kelas.IdKelas + '">' + kelas.NamaKelas + '</option>');
                        });
                        if (currentKelas) {
                            filterKelas.val(currentKelas);
                        }
                        resolve();
                    } else {
                        reject('Failed to load filter options');
                    }
                })
                .catch(error => {
                    console.error('Error loading filter options:', error);
                    reject(error);
                });
        });
    }

    // Fungsi untuk mengambil data dari server
    function loadDataByTahunAjaran(IdTahunAjaran, IdJabatan, IdKelas) {
        // Tampilkan loading
        Swal.fire({
            title: 'Mohon Tunggu',
            html: 'Sedang memuat data...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const url = '<?= base_url('backend/GuruKelas/getDataByTahunAjaran') ?>';
        let params = [];
        if (IdTahunAjaran) params.push('IdTahunAjaran=' + IdTahunAjaran);
        if (IdJabatan) params.push('IdJabatan=' + IdJabatan);
        if (IdKelas) params.push('IdKelas=' + IdKelas);
        const queryString = params.length > 0 ? '?' + params.join('&') : '';

        fetch(url + queryString)
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    renderTable(data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat data'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan pada server'
                });
                console.error('Error:', error);
            });
    }

    // Fungsi untuk apply semua filter
    function applyFilters() {
        const IdTahunAjaran = $('#filterTahunAjaran').val();
        const IdJabatan = $('#filterPosisi').val();
        const IdKelas = $('#filterKelas').val();
        loadDataByTahunAjaran(IdTahunAjaran, IdJabatan, IdKelas);
    }

    // Inisialisasi
    $(document).ready(function() {
        // Load filter options saat halaman pertama kali dimuat
        const defaultTahunAjaran = $('#filterTahunAjaran').val();
        if (defaultTahunAjaran) {
            updateFilterOptions(defaultTahunAjaran).then(function() {
                // Setelah filter options di-load, load data
                applyFilters();
            });
        } else {
            // Jika tidak ada tahun ajaran yang dipilih, load semua data
            applyFilters();
        }

        // Event handler untuk perubahan filter tahun ajaran (hanya update options, tidak load data)
        $('#filterTahunAjaran').on('change', function() {
            const selectedValue = $(this).val();
            console.log('Filter tahun ajaran berubah:', selectedValue);
            // Update filter Posisi dan Kelas
            updateFilterOptions(selectedValue).then(function() {
                // Reset filter Posisi dan Kelas setelah di-update
                $('#filterPosisi').val('');
                $('#filterKelas').val('');
            });
        });

        // Event handler untuk tombol filter
        $('#btnFilter').on('click', function() {
            console.log('Tombol filter diklik');
            applyFilters();
        });
    });

    function saveDataGuruKelas(button) {
        // Dapatkan form terdekat dari tombol yang diklik
        const form = button.closest('form');
        // Dapatkan modal terdekat dari form
        const modal = form.closest('.modal');

        // Tampilkan loading
        Swal.fire({
            title: 'Mohon Tunggu',
            html: 'Sedang memproses data...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil disimpan',
                        showConfirmButton: true,
                        timer: 2000
                    }).then(() => {
                        $(modal).modal('hide');
                        // Reload data dengan filter yang sedang aktif
                        applyFilters();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Terjadi kesalahan saat menyimpan data'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan pada server'
                });
                console.error('Error:', error);
            });
    }

    // Fungsi untuk hapus data
    function deleteDataGuruKelas(url) {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Mohon Tunggu',
                    html: 'Sedang menghapus data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(url, {
                        method: 'GET'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message || 'Data berhasil dihapus',
                                showConfirmButton: true,
                                timer: 2000
                            }).then(() => {
                                // Reload data dengan filter yang sedang aktif
                                applyFilters();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Terjadi kesalahan saat menghapus data'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan pada server'
                        });
                        console.error('Error:', error);
                    });
            }
        });
    }
</script>
<?= $this->endSection(); ?>