<?= $this->extend('backend/template/template'); ?>

<?php helper('nilai'); ?>

<?= $this->section('content') ?>
<!-- Card untuk Peserta yang Perlu Perbaikan -->
<?php if (!empty($pesertaPerluPerbaikan) && count($pesertaPerluPerbaikan) > 0): ?>
    <div class="col-12 mt-4">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle"></i> Peserta yang Perlu Perbaikan Data
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Santri</th>
                                <th>Nama Santri</th>
                                <th>TPQ</th>
                                <th>Keterangan</th>
                                <th>Tanggal Verifikasi</th>
                                <th>Persetujuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($pesertaPerluPerbaikan as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['IdSantri'] ?? '-' ?></td>
                                    <td><?= $row['NamaSantri'] ?? '-' ?></td>
                                    <td><?= $row['NamaTpq'] ?? '-' ?></td>
                                    <td>
                                        <?php if (!empty($row['keterangan'])): ?>
                                            <span class="text-muted" title="<?= esc($row['keterangan']) ?>">
                                                <?= esc(strlen($row['keterangan']) > 50 ? substr($row['keterangan'], 0, 50) . '...' : $row['keterangan']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['verified_at'])): ?>
                                            <?= formatTanggalIndonesia($row['verified_at'], 'd F Y H:i') ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        // Gunakan fungsi editPeserta yang sama seperti di tabel utama
                                        $idSantri = $row['IdSantri'] ?? '';
                                        $namaSantri = $row['NamaSantri'] ?? 'Tidak Diketahui';
                                        $statusVerifikasi = 'perlu_perbaikan'; // Status selalu perlu_perbaikan di tabel ini
                                        ?>
                                        <button type="button" class="btn btn-info btn-sm"
                                            onclick="editPeserta(<?= $idSantri ?>, '<?= addslashes($namaSantri) ?>', '<?= $statusVerifikasi ?>')"
                                            title="Review & Konfirmasi Perbaikan">
                                            <i class="fas fa-check-double"></i> Review
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="row mb-2">

                <div class="col-sm-12 float-sm-left">
                    <button class="btn btn-primary" data-toggle="modal"
                        data-target="#modalAddPeserta"><i class="fas fa-edit"></i>Tambah Peserta Munaqosah</button>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>

        <!-- /.card-header -->
        <div class="card-body">
            <table id="tabelPesertaMunaqosah" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Santri</th>
                        <th>Nama Santri</th>
                        <th>Tempat Lahir</th>
                        <th>Tanggal Lahir</th>
                        <th>Jenis Kelamin</th>
                        <th>Nama Ayah</th>
                        <th>TPQ</th>
                        <th>Alamat</th>
                        <th>Tahun Ajaran</th>
                        <th>Status Verifikasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($peserta) && count($peserta) > 0): ?>
                        <?php $no = 1; ?>
                        <?php foreach ($peserta as $row): ?>
                            <?php
                            $statusVerifikasi = $row->status_verifikasi ?? null;
                            $statusBadge = '';
                            if ($statusVerifikasi === 'valid') {
                                $statusBadge = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Valid</span>';
                            } elseif ($statusVerifikasi === 'perlu_perbaikan') {
                                $statusBadge = '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Perlu Perbaikan</span>';
                            } elseif ($statusVerifikasi === 'dikonfirmasi') {
                                // Status "dikonfirmasi" tidak lagi digunakan, langsung menjadi "valid"
                                // Tetap tampilkan untuk backward compatibility dengan data lama
                                $statusBadge = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Valid</span>';
                            } else {
                                $statusBadge = '<span class="badge badge-secondary"><i class="fas fa-clock"></i> Belum Dikonfirmasi</span>';
                            }
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row->IdSantri ?? '-' ?></td>
                                <td><?= $row->NamaSantri ?? '-' ?></td>
                                <td><?= $row->TempatLahirSantri ?? '-' ?></td>
                                <td><?= $row->TanggalLahirSantri ? formatTanggalIndonesia($row->TanggalLahirSantri, 'd F Y') : '-' ?></td>
                                <td><?= $row->JenisKelamin ?? '-' ?></td>
                                <td><?= $row->NamaAyah ?? '-' ?></td>
                                <td><?= $row->NamaTpq ?? '-' ?></td>
                                <td><?= $row->KelurahanDesa ?? '-' ?></td>
                                <td><?= $row->IdTahunAjaran ?? '-' ?></td>
                                <td><?= $statusBadge ?></td>
                                <td>
                                    <?php
                                    $statusVerifikasi = $row->status_verifikasi ?? null;
                                    $btnClass = 'btn-warning';
                                    $btnIcon = 'fas fa-edit';
                                    $btnTitle = 'Edit';

                                    if ($statusVerifikasi === 'perlu_perbaikan') {
                                        $btnClass = 'btn-info';
                                        $btnIcon = 'fas fa-check-double';
                                        $btnTitle = 'Review & Konfirmasi Perbaikan';
                                    }
                                    ?>
                                    <button type="button" class="btn <?= $btnClass ?> btn-sm mr-1"
                                        onclick="editPeserta(<?= $row->IdSantri ?>, '<?= $row->NamaSantri ?? 'Tidak Diketahui' ?>', '<?= $statusVerifikasi ?? '' ?>')"
                                        title="<?= $btnTitle ?>">
                                        <i class="<?= $btnIcon ?>"></i> <?= $statusVerifikasi === 'perlu_perbaikan' ? 'Review' : 'Edit' ?>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="deletePeserta(<?= $row->IdSantri ?>, '<?= $row->NamaSantri ?? 'Tidak Diketahui' ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12" class="text-center">Tidak ada data peserta</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Modal Add Peserta -->
<div class="modal fade" id="modalAddPeserta" tabindex="-1" role="dialog" aria-labelledby="modalAddPesertaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalAddPesertaLabel">
                    <i class="fas fa-user-plus"></i> Tambah Peserta Munaqosah
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddPeserta">
                <div class="modal-body">
                    <!-- Step 1: Filter -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-filter"></i> Langkah 1: Pilih Filter</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="IdTpq">TPQ</label>
                                        <select class="form-control select2" id="IdTpq" name="IdTpq">
                                            <?php if (count($dataTpq) > 1): ?>
                                                <option value="">Semua TPQ</option>
                                                <?php foreach ($dataTpq as $tpq): ?>
                                                    <option value="<?= $tpq['IdTpq'] ?>"><?= $tpq['NamaTpq'] ?></option>
                                                <?php endforeach; ?>
                                            <?php elseif (count($dataTpq) == 1): ?>
                                                <?php foreach ($dataTpq as $tpq): ?>
                                                    <option value="<?= $tpq['IdTpq'] ?>" selected><?= $tpq['NamaTpq'] ?></option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="">Tidak ada data TPQ</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="IdKelas">Kelas</label>
                                        <select class="form-control select2" id="IdKelas" name="IdKelas">
                                            <option value="">Semua Kelas</option>
                                            <?php foreach ($dataKelas as $kelas): ?>
                                                <?php if ($kelas['NamaKelas'] !== 'ALUMNI' && $kelas['NamaKelas'] !== 'NA'): ?>
                                                    <option value="<?= $kelas['IdKelas'] ?>"><?= $kelas['NamaKelas'] ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-info" id="btnLoadSantri">
                                    <i class="fas fa-search"></i> Tampilkan Data Santri
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Pilih Santri -->
                    <div class="card mb-3" id="cardPilihSantri" style="display: none;">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-users"></i> Langkah 2: Pilih Santri</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Tahun Ajaran</label>
                                <input type="text" class="form-control" id="IdTahunAjaran" name="IdTahunAjaran" value="<?= $tahunAjaran ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label>Pilih Santri <span class="text-danger">*</span></label>
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;" data-overlayscrollbars>
                                    <table class="table table-sm table-hover mb-0" id="tabelSantri">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="text-center">
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                                    </div>
                                                </th>
                                                <th>ID</th>
                                                <th>Nama Santri</th>
                                                <th>Kelas</th>
                                                <th>TPQ</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodySantri">
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">
                                                    <i class="fas fa-info-circle"></i> Klik "Tampilkan Data Santri" untuk memuat data
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Konfirmasi -->
                    <div class="card" id="cardKonfirmasi" style="display: none;">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> Langkah 3: Konfirmasi</h6>
                        </div>
                        <div class="card-body">
                            <div id="selectedSantriList">
                                <!-- Daftar santri yang dipilih akan muncul di sini -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success" id="btnSimpan" disabled>
                        <i class="fas fa-save"></i> Simpan Peserta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Edit Peserta -->
<div class="modal fade" id="modalEditPeserta" tabindex="-1" role="dialog" aria-labelledby="modalEditPesertaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPesertaLabel">Edit Data Peserta Munaqosah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditPeserta">
                <div class="modal-body">
                    <input type="hidden" id="editIdSantri" name="IdSantri">

                    <!-- Alert untuk Status Perlu Perbaikan -->
                    <div id="alertPerluPerbaikan" class="alert alert-warning" style="display: none;">
                        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Status: Perlu Perbaikan</h5>
                        <p class="mb-2">
                            <strong>Peserta mengajukan permintaan perbaikan data.</strong>
                            Silakan review perubahan yang diusulkan di bawah ini.
                        </p>
                        <div id="keteranganUser" class="mt-2"></div>
                        <div id="fieldsToFixList" class="mt-3"></div>
                    </div>

                    <!-- Card Informasi Alur Proses Review & Konfirmasi (hanya muncul jika status perlu_perbaikan) -->
                    <div id="cardInfoProsesReview" class="card card-info collapsed-card mb-3" style="display: none;">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle"></i> Panduan Alur Proses Review & Konfirmasi Perbaikan
                            </h5>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="padding: 15px;">
                            <h6 class="mb-2"><i class="fas fa-list-ol text-primary"></i> Alur Proses:</h6>
                            <ol class="mb-3" style="padding-left: 20px; font-size: 13px;">
                                <li class="mb-2">
                                    <strong>Review Data Perbaikan:</strong> Lihat tabel perbaikan data di bawah untuk melihat perbandingan:
                                    <ul style="margin-top: 5px; padding-left: 20px;">
                                        <li>Kolom <span style="background-color: #fff3cd; padding: 2px 5px;"><strong>Kuning</strong></span> = Data Saat Ini (Sebelum)</li>
                                        <li>Kolom <span style="background-color: #d4edda; padding: 2px 5px;"><strong>Hijau</strong></span> = Usulan Perbaikan (Sesudah)</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    <strong>Lakukan Perbaikan:</strong> 
                                    <ul style="margin-top: 5px; padding-left: 20px;">
                                        <li>Gunakan toggle switch <strong>"Terima"</strong> pada tabel untuk menerima usulan perbaikan</li>
                                        <li>Atau edit manual di form fields di bawah tabel</li>
                                        <li>Form fields akan terisi otomatis setelah menggunakan tabel</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    <strong>Konfirmasi Perbaikan:</strong> Setelah selesai review, klik tombol 
                                    <span class="badge badge-success badge-sm"><i class="fas fa-check"></i> Konfirmasi Perbaikan</span> 
                                    di bagian bawah modal untuk menyetujui perbaikan.
                                </li>
                                <li class="mb-2">
                                    <strong>Hasil:</strong> Status akan berubah menjadi <span class="badge badge-success badge-sm">Valid</span> 
                                    dan data tersimpan di data utama santri.
                                </li>
                            </ol>
                            <div class="alert alert-info mb-0" style="padding: 10px; font-size: 12px;">
                                <strong><i class="fas fa-lightbulb"></i> Tips:</strong>
                                <ul class="mb-0" style="padding-left: 20px; margin-top: 5px;">
                                    <li>Gunakan tabel perbaikan untuk review cepat dan terima usulan dengan satu klik</li>
                                    <li>Baca keterangan user untuk memahami alasan perbaikan</li>
                                    <li>Pastikan checkbox konfirmasi dicentang sebelum menyimpan</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Data Santri Card -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-edit"></i> Data Santri
                            </h3>
                        </div>
                        <div class="card-body">
                            <input type="hidden" id="editIdPeserta" name="IdPeserta">
                            <input type="hidden" id="editStatusVerifikasi" name="StatusVerifikasi">

                            <!-- Tabel Perbaikan Data (hanya muncul jika status perlu_perbaikan) -->
                            <div id="tablePerbaikanData" style="display: none;">
                                <h5 class="mb-3"><i class="fas fa-table"></i> Tabel Perbaikan Data</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm" id="tabelPerbaikan">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 20%;">Field</th>
                                                <th style="width: 20%;">Saat Ini (Sebelum)</th>
                                                <th style="width: 20%;">Usulan</th>
                                                <th style="width: 15%;">Persetujuan</th>
                                                <th style="width: 25%;">Perbaikan (Sesudah)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyPerbaikanData">
                                            <!-- Rows will be generated dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="my-4">
                            </div>

                            <!-- Form Fields (normal edit atau setelah menggunakan tabel) -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editIdSantriDisplay">ID Santri</label>
                                        <input type="text" class="form-control" id="editIdSantriDisplay" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editNamaSantri">Nama Santri <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editNamaSantri" name="NamaSantri" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editTempatLahirSantri">Tempat Lahir <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editTempatLahirSantri" name="TempatLahirSantri" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editTanggalLahirSantri">Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="editTanggalLahirSantri" name="TanggalLahirSantri" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editJenisKelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select class="form-control" id="editJenisKelamin" name="JenisKelamin" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="Laki-laki">Laki-laki</option>
                                            <option value="Perempuan">Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editNamaAyah">Nama Ayah <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editNamaAyah" name="NamaAyah" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <p class="text-muted" id="editInfoMessage"><i class="fas fa-info-circle"></i> Perubahan data ini akan disimpan di data utama santri.</p>
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="editConfirmSave" required>
                                <label class="form-check-label" for="editConfirmSave" id="editConfirmLabel">Saya mengerti dan menyetujui perubahan ini.</label>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Keluarga Information -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-id-card"></i> Informasi Kartu Keluarga
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Kartu Keluarga Santri</label>
                                                <div id="kkSantriInfo">
                                                    <span class="text-muted">Tidak ada file</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Kartu Keluarga Ayah</label>
                                                <div id="kkAyahInfo">
                                                    <span class="text-muted">Tidak ada file</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Kartu Keluarga Ibu</label>
                                                <div id="kkIbuInfo">
                                                    <span class="text-muted">Tidak ada file</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Kartu Keluarga Wali</label>
                                                <div id="kkWaliInfo">
                                                    <span class="text-muted">Tidak ada file</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-secondary" disabled>
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    /* Styling untuk field comparison */
    .field-comparison {
        position: relative;
    }

    .comparison-row {
        display: flex;
        gap: 15px;
        margin-bottom: 12px;
        padding: 12px;
        background: linear-gradient(to right, #fff3cd 0%, #fff3cd 48%, #d4edda 52%, #d4edda 100%);
        border-radius: 6px;
        border: 2px solid #dee2e6;
        flex-wrap: wrap;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .current-value,
    .suggested-value {
        flex: 1;
        min-width: 220px;
    }

    .current-value {
        border-right: 3px solid #ffc107;
        padding-right: 15px;
        margin-right: 10px;
    }

    .current-value input {
        background-color: #fff3cd;
        border: 2px solid #ffc107;
        font-weight: 600;
        color: #856404;
    }

    .suggested-value input {
        background-color: #d4edda;
        border: 2px solid #28a745;
        font-weight: 600;
        color: #155724;
    }

    .suggested-value {
        padding-left: 15px;
    }

    .current-value small,
    .suggested-value small {
        font-weight: 600;
        display: block;
        margin-bottom: 5px;
    }

    /* Toggle Switch Styling */
    .switch-toggle {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
        margin-top: 5px;
    }

    .switch-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #28a745;
    }

    input:checked+.slider:before {
        transform: translateX(26px);
    }

    .suggested-value small.text-success {
        font-weight: 600;
    }

    /* Alert styling */
    .alert-warning {
        border-left: 4px solid #ffc107;
    }

    /* Improvement untuk comparison row visibility */
    .comparison-row small {
        font-size: 0.85rem;
    }

    .field-comparison .form-control {
        margin-top: 8px;
    }

    /* Styling untuk field yang perlu diperbaiki */
    .field-to-fix {
        position: relative;
    }

    .field-to-fix::before {
        content: '';
        position: absolute;
        left: -10px;
        top: 0;
        bottom: 0;
        width: 4px;
        background-color: #ffc107;
        border-radius: 2px;
    }

    .field-to-fix label {
        font-weight: 600;
        color: #856404;
    }

    .field-to-fix label::after {
        content: ' *';
        color: #ffc107;
    }

    .field-fix-indicator {
        font-size: 0.75rem;
        padding: 2px 6px;
        margin-left: 5px;
    }

    .badge-sm {
        font-size: 0.75rem;
        padding: 2px 6px;
    }

    /* Styling untuk field yang disabled */
    .form-control:disabled,
    .form-control[readonly][disabled] {
        background-color: #e9ecef !important;
        cursor: not-allowed !important;
        opacity: 0.7 !important;
    }

    .field-disabled-badge {
        display: block;
        margin-top: 5px;
        font-size: 0.85rem;
        padding: 4px 8px;
        background-color: #f8f9fa;
        border-radius: 4px;
        border-left: 3px solid #6c757d;
    }

    .field-disabled-badge i {
        margin-right: 5px;
        color: #6c757d;
    }

    /* Highlight untuk field yang fokus */
    .form-control:focus:not(:disabled) {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Styling untuk label field yang disabled */
    .form-group:has(.form-control:disabled) label {
        opacity: 0.6;
        color: #6c757d;
    }

    /* Pastikan field yang disabled tidak bisa di-focus */
    .form-control:disabled:focus {
        outline: none;
        box-shadow: none;
    }

    /* Styling untuk tabel perbaikan data */
    #tabelPerbaikan {
        font-size: 0.9rem;
    }

    #tabelPerbaikan thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
    }

    #tabelPerbaikan tbody td {
        vertical-align: middle;
    }

    #tabelPerbaikan .field-name {
        font-weight: 600;
        color: #495057;
    }

    #tabelPerbaikan .current-value-cell {
        background-color: #fff3cd;
        color: #856404;
        padding: 8px;
        border-radius: 4px;
        font-weight: 500;
        text-align: center;
    }

    #tabelPerbaikan .usulan-value-cell {
        background-color: #d1ecf1;
        color: #0c5460;
        padding: 8px;
        border-radius: 4px;
        font-weight: 500;
        text-align: center;
    }

    #tabelPerbaikan .perbaikan-input {
        width: 100%;
        padding: 6px 10px;
        border: 2px solid #28a745;
        border-radius: 4px;
        font-size: 0.9rem;
    }

    #tabelPerbaikan .perbaikan-input:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        outline: none;
    }

    /* Toggle Switch Container */
    .toggle-switch-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    /* Toggle Switch */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 80px;
        height: 35px;
        cursor: pointer;
    }

    .toggle-switch-input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-switch-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #dc3545;
        transition: 0.3s;
        border-radius: 35px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 8px;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .toggle-switch-slider:before {
        position: absolute;
        content: "";
        height: 27px;
        width: 27px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .toggle-switch-input:checked + .toggle-switch-slider {
        background-color: #28a745;
    }

    .toggle-switch-input:checked + .toggle-switch-slider:before {
        transform: translateX(45px);
    }

    .toggle-switch-label-on,
    .toggle-switch-label-off {
        font-size: 0.7rem;
        font-weight: bold;
        color: white;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        z-index: 1;
        transition: opacity 0.3s;
    }

    .toggle-switch-label-on {
        opacity: 0;
    }

    .toggle-switch-label-off {
        opacity: 1;
    }

    .toggle-switch-input:checked + .toggle-switch-slider .toggle-switch-label-on {
        opacity: 1;
    }

    .toggle-switch-input:checked + .toggle-switch-slider .toggle-switch-label-off {
        opacity: 0;
    }

    /* Custom styling untuk Select2 agar konsisten dengan form-control */
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2.25rem + 2px) !important;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .select2-container--bootstrap4 .select2-selection--single:hover {
        border-color: #80bdff;
    }

    .select2-container--bootstrap4 .select2-selection--single:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        color: #495057;
        padding: 0;
        line-height: 1.5;
        height: auto;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px);
        right: 0.75rem;
        top: 0;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear {
        height: calc(2.25rem + 2px);
        right: 2rem;
        top: 0;
        width: 16px !important;
        height: 16px !important;
        line-height: 16px !important;
        text-align: center !important;
        background: #dc3545 !important;
        color: white !important;
        border-radius: 50% !important;
        font-size: 12px !important;
        font-weight: bold !important;
        z-index: 10 !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        position: absolute !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear:hover {
        background: #c82333 !important;
    }

    /* Pastikan clear button tidak tertutup scrollbar */
    .select2-container--bootstrap4 .select2-selection--single {
        position: relative !important;
        overflow: visible !important;
    }

    /* Perbaikan untuk dropdown yang memiliki clear button */
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        padding-right: 50px !important;
    }

    /* Tambahan styling untuk memastikan clear button terlihat jelas */
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear::before {
        content: "×" !important;
        font-size: 16px !important;
        font-weight: bold !important;
        line-height: 1 !important;
    }

    /* Pastikan clear button tidak tertutup elemen lain */
    .select2-container--bootstrap4 .select2-selection--single {
        z-index: 1 !important;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear {
        z-index: 2 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
    }

    /* Hover effect untuk clear button */
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__clear:hover {
        transform: translateY(-50%) scale(1.1) !important;
        transition: all 0.2s ease !important;
    }

    /* Dropdown styling */
    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .select2-container--bootstrap4 .select2-results__option {
        padding: 0.375rem 0.75rem;
    }

    .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
        background-color: #007bff;
        color: #fff;
    }

    /* Modal z-index fix */
    .select2-container {
        z-index: 9999;
    }

    .select2-dropdown {
        z-index: 9999;
    }

    /* Konsistensi tinggi untuk semua form control */
    .form-control {
        height: calc(2.25rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
    }

    /* Spacing yang konsisten */
    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    /* Styling untuk checkbox di tabel */
    .table-responsive {
        position: relative;
    }

    .table-responsive .table {
        margin-bottom: 0;
    }

    .table th,
    .table td {
        vertical-align: middle;
        padding: 0.5rem 0.75rem;
    }

    /* Styling khusus untuk kolom checkbox */
    .table th:first-child,
    .table td:first-child {
        width: 40px;
        text-align: center;
        padding: 0.5rem 0.25rem;
        vertical-align: middle;
    }

    /* Checkbox styling yang konsisten */
    .form-check-input {
        margin: 0;
        transform: scale(1.2);
        position: relative;
        top: 0;
        left: 0;
    }

    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Pastikan checkbox di header dan body sejajar */
    .table thead th:first-child {
        text-align: center;
        vertical-align: middle;
        padding: 0.75rem 0.25rem;
    }

    .table tbody td:first-child {
        text-align: center;
        vertical-align: middle;
        padding: 0.5rem 0.25rem;
    }

    /* Override untuk checkbox di header */
    .table thead th:first-child .form-check-input {
        margin: 0;
        display: inline-block;
    }

    /* Override untuk checkbox di body */
    .table tbody td:first-child .form-check-input {
        margin: 0;
        display: inline-block;
    }

    /* Flexbox container untuk alignment yang sempurna */
    .table th:first-child .d-flex,
    .table td:first-child .d-flex {
        height: 100%;
        min-height: 20px;
    }

    /* Pastikan checkbox terpusat sempurna */
    .table th:first-child,
    .table td:first-child {
        position: relative;
    }

    .table th:first-child .d-flex,
    .table td:first-child .d-flex {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
    }

    /* Sticky header untuk tabel */
    .table-responsive .thead-light th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    /* Hover effect untuk row */
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Custom scrollbar untuk overlayScrollbars */
    .table-responsive::-webkit-scrollbar {
        width: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* OverlayScrollbars compatibility */
    .os-scrollbar {
        z-index: 1;
    }

    .os-scrollbar-handle {
        background: #c1c1c1 !important;
        border-radius: 4px !important;
    }

    .os-scrollbar-handle:hover {
        background: #a8a8a8 !important;
    }
</style>
<script>
    $(document).ready(function() {
        console.log('Document ready - initializing...');

        // Inisialisasi DataTables dengan error handling
        if ($('#tabelPesertaMunaqosah').length > 0) {
            try {
                var table = $('#tabelPesertaMunaqosah').DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "dom": 'Bfrtip',
                    "language": {
                        "emptyTable": "Tidak ada data peserta munaqosah",
                        "zeroRecords": "Tidak ada data yang cocok"
                    },
                    // munculkan menu untuk export data ke pdf,excel,print
                    "buttons": [{
                            extend: 'pdf',
                            text: 'PDF',
                            className: 'btn btn-danger btn-sm',
                            title: 'Data Peserta Munaqosah',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] // Include status verifikasi, exclude action column
                            }
                        },
                        {
                            extend: 'excel',
                            text: 'Excel',
                            className: 'btn btn-success btn-sm',
                            title: 'Data Peserta Munaqosah',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] // Include status verifikasi, exclude action column
                            }
                        },
                        {
                            extend: 'print',
                            text: 'Print',
                            className: 'btn btn-info btn-sm',
                            title: 'Data Peserta Munaqosah',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] // Include status verifikasi, exclude action column
                            }
                        }
                    ]
                });

                // Pastikan button container ditambahkan ke wrapper
                table.buttons().container().appendTo('#tabelPesertaMunaqosah_wrapper .col-md-6:eq(0)');
                console.log('DataTables initialized successfully');
            } catch (error) {
                console.error('DataTables initialization error:', error);
                // Fallback: hide table if DataTables fails
                $('#tabelPesertaMunaqosah').hide();
            }
        } else {
            console.log('Table element not found, skipping DataTables initialization');
        }

        // Inisialisasi Select2 untuk dropdown
        $('.select2').select2({
            placeholder: 'Pilih opsi...',
            allowClear: true,
            width: '100%',
            theme: 'bootstrap4',
            language: 'id',
            dropdownParent: $('#modalAddPeserta')
        });
        console.log('Select2 initialized');

        // Event handler untuk tombol Load Santri
        $('#btnLoadSantri').on('click', function() {
            var idTpq = $('#IdTpq').val() || 0;
            var idKelas = $('#IdKelas').val() || 0;

            // Validasi minimal satu filter dipilih (bukan "semua")
            if (idTpq == 0 && idKelas == 0) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Pilih minimal satu filter (TPQ atau Kelas)',
                    icon: 'warning'
                });
                return;
            }

            loadSantriData(idKelas, idTpq);
        });

        // Event handler untuk Select All
        $('#selectAll').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('.santri-checkbox').prop('checked', isChecked);
            updateSelectedSantri();
        });

        // Event handler untuk checkbox individual
        $(document).on('change', '.santri-checkbox', function() {
            updateSelectedSantri();
        });

        // Test apakah elemen ada
        console.log('IdKelas element exists:', $('#IdKelas').length > 0);
        console.log('IdTpq element exists:', $('#IdTpq').length > 0);

        // Re-initialize Select2 saat modal dibuka untuk styling yang konsisten
        $('#modalAddPeserta').on('shown.bs.modal', function() {
            $('.select2').select2('destroy');
            $('.select2').select2({
                placeholder: 'Pilih opsi...',
                allowClear: true,
                width: '100%',
                theme: 'bootstrap4',
                language: 'id',
                dropdownParent: $('#modalAddPeserta')
            });

            // Initialize overlayScrollbars untuk tabel
            if (typeof OverlayScrollbars !== 'undefined') {
                $('[data-overlayscrollbars]').overlayScrollbars({
                    scrollbars: {
                        autoHide: 'leave',
                        autoHideDelay: 200
                    },
                    overflow: {
                        x: 'hidden',
                        y: 'scroll'
                    }
                });
            }
        });

        // Destroy Select2 saat modal ditutup
        $('#modalAddPeserta').on('hidden.bs.modal', function() {
            $('.select2').select2('destroy');

            // Destroy overlayScrollbars
            if (typeof OverlayScrollbars !== 'undefined') {
                $('[data-overlayscrollbars]').overlayScrollbars().destroy();
            }
        });

        // Form Add Peserta
        $('#formAddPeserta').on('submit', function(e) {
            e.preventDefault();

            var selectedSantri = [];
            $('.santri-checkbox:checked').each(function() {
                selectedSantri.push($(this).val());
            });

            if (selectedSantri.length === 0) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Pilih minimal satu santri',
                    icon: 'warning'
                });
                return;
            }

            var tahunAjaran = $('#IdTahunAjaran').val();

            // Kumpulkan IdTpq dari santri yang dipilih
            var selectedTpq = [];
            $('.santri-checkbox:checked').each(function() {
                var idTpq = $(this).data('idtpq');
                if (idTpq && selectedTpq.indexOf(idTpq) === -1) {
                    selectedTpq.push(idTpq);
                }
            });

            // Kirim data multiple
            var dataToSend = {
                santri_ids: selectedSantri,
                IdTahunAjaran: tahunAjaran,
                IdTpq: selectedTpq
            };

            // Show loading
            Swal.fire({
                title: 'Menyimpan Data...',
                text: 'Sedang menyimpan data peserta munaqosah, mohon tunggu',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url('backend/munaqosah/save-peserta-multiple') ?>',
                type: 'POST',
                data: dataToSend,
                dataType: 'json',
                timeout: 60000, // 60 detik timeout untuk save operation
                success: function(response) {
                    // Close loading
                    Swal.close();

                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        // Tampilkan detail errors jika ada
                        var errorMessage = response.message;
                        var detailedErrors = '';

                        if (response.detailed_errors && response.detailed_errors.length > 0) {
                            detailedErrors = '<br><br><div class="alert alert-danger"><strong><i class="fas fa-exclamation-triangle"></i> Detail Error:</strong><br>';
                            response.detailed_errors.forEach(function(error, index) {
                                detailedErrors += '<div class="mt-2"><span class="badge badge-danger">' + (index + 1) + '</span> ' + error + '</div>';
                            });
                            detailedErrors += '</div>';
                        }

                        if (response.error_count) {
                            detailedErrors += '<br><small class="text-muted">Total error: ' + response.error_count + '</small>';
                        }

                        Swal.fire({
                            title: 'Error!',
                            html: errorMessage + detailedErrors,
                            icon: 'error',
                            width: '600px'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Close loading
                    Swal.close();

                    var errorMessage = 'Terjadi kesalahan pada server';
                    var errorTitle = 'Error!';

                    // Determine error message based on status
                    if (status === 'timeout') {
                        errorMessage = 'Koneksi timeout. Proses penyimpanan membutuhkan waktu yang lebih lama.';
                        errorTitle = 'Timeout!';
                    } else if (xhr.status === 404) {
                        errorMessage = 'Endpoint tidak ditemukan. Silakan hubungi administrator.';
                        errorTitle = 'Not Found!';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Terjadi kesalahan server. Silakan hubungi administrator.';
                        errorTitle = 'Server Error!';
                    } else if (xhr.status === 0) {
                        errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                        errorTitle = 'Connection Error!';
                    }

                    // Coba parse response error jika ada
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.detailed_errors && response.detailed_errors.length > 0) {
                            var detailedErrors = '<br><br><div class="alert alert-danger"><strong><i class="fas fa-exclamation-triangle"></i> Detail Error:</strong><br>';
                            response.detailed_errors.forEach(function(error, index) {
                                detailedErrors += '<div class="mt-2"><span class="badge badge-danger">' + (index + 1) + '</span> ' + error + '</div>';
                            });
                            detailedErrors += '</div>';
                            errorMessage += detailedErrors;
                        } else if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        // Jika tidak bisa parse JSON, gunakan error default
                    }

                    Swal.fire({
                        title: errorTitle,
                        html: `
                            <div class="text-left">
                                <p><strong>Pesan Error:</strong> ${errorMessage}</p>
                                <p><strong>Status:</strong> ${status}</p>
                                <p><strong>HTTP Code:</strong> ${xhr.status}</p>
                                <details class="mt-3">
                                    <summary class="text-muted">Detail Teknis</summary>
                                    <small class="text-muted">${error}</small>
                                </details>
                            </div>
                        `,
                        icon: 'error',
                        width: '600px',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

    });

    // Load data santri berdasarkan kelas yang dipilih dan TPQ yang dipilih
    function loadSantriData(idKelas, idTpq) {
        console.log('loadSantriData called with:', idKelas, idTpq);

        // Show SweetAlert2 loading
        Swal.fire({
            title: 'Memuat Data...',
            text: 'Sedang mengambil data santri, mohon tunggu',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Show table loading state
        $('#tbodySantri').html('<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>');
        $('#cardPilihSantri').show();

        // Handle parameter 0 untuk "semua"
        var urlKelas = (idKelas == 0) ? 0 : idKelas;
        var urlTpq = (idTpq == 0) ? 0 : idTpq;

        $.ajax({
            url: '<?= base_url('backend/munaqosah/getSantriData/') ?>' + urlKelas + '/' + urlTpq,
            type: 'GET',
            dataType: 'json',
            timeout: 30000, // 30 detik timeout
            success: function(data) {
                console.log('Data received:', data);

                // Close loading
                Swal.close();

                // Cek apakah response adalah error dari controller
                if (data && data.success === false) {
                    // Show error from controller
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: `
                            <div class="text-left">
                                <p><strong>Pesan Error:</strong> ${data.user_message || data.message}</p>
                                ${data.error_details ? `
                                    <details class="mt-3">
                                        <summary class="text-muted">Detail Teknis</summary>
                                        <small class="text-muted">
                                            <strong>Error:</strong> ${data.error_details.error_message}<br>
                                            <strong>Type:</strong> ${data.error_details.error_type}<br>
                                            <strong>File:</strong> ${data.error_details.file}<br>
                                            <strong>Line:</strong> ${data.error_details.line}
                                        </small>
                                    </details>
                                ` : ''}
                            </div>
                        `,
                        confirmButtonText: 'Coba Lagi',
                        showCancelButton: true,
                        cancelButtonText: 'Tutup',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Retry loading data
                            loadSantriData(idKelas, idTpq);
                        }
                    });
                    return;
                }

                // Cek apakah data adalah array
                if (!Array.isArray(data)) {
                    console.error('Invalid data format:', data);
                    $('#tbodySantri').html('<tr><td colspan="5" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Format data tidak valid</td></tr>');
                    return;
                }

                if (data.length > 0) {
                    var html = '';
                    $.each(data, function(index, item) {
                        html += '<tr>';
                        html += '<td class="text-center">';
                        html += '<div class="d-flex justify-content-center align-items-center">';
                        html += '<input type="checkbox" class="form-check-input santri-checkbox" value="' + item.IdSantri + '" data-nama="' + item.NamaSantri + '" data-kelas="' + (item.NamaKelas || '-') + '" data-tpq="' + (item.NamaTpq || '-') + '" data-idtpq="' + (item.IdTpq || '') + '">';
                        html += '</div>';
                        html += '</td>';
                        html += '<td>' + item.IdSantri + '</td>';
                        html += '<td>' + item.NamaSantri + '</td>';
                        html += '<td>' + (item.NamaKelas || '-') + '</td>';
                        html += '<td>' + (item.NamaTpq || '-') + '</td>';
                        html += '</tr>';
                    });
                    $('#tbodySantri').html(html);

                    // Re-initialize overlayScrollbars setelah data dimuat
                    if (typeof OverlayScrollbars !== 'undefined') {
                        $('[data-overlayscrollbars]').overlayScrollbars().destroy();
                        $('[data-overlayscrollbars]').overlayScrollbars({
                            scrollbars: {
                                autoHide: 'leave',
                                autoHideDelay: 200
                            },
                            overflow: {
                                x: 'hidden',
                                y: 'scroll'
                            }
                        });
                    }

                    // Show success message if data loaded successfully
                    if (data.length > 0) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data Berhasil Dimuat!',
                            text: 'Ditemukan ' + data.length + ' santri',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                } else {
                    $('#tbodySantri').html('<tr><td colspan="5" class="text-center text-muted py-3"><i class="fas fa-info-circle"></i> Tidak ada data santri</td></tr>');

                    // Show info message for no data
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Data',
                        text: 'Tidak ditemukan santri untuk filter yang dipilih',
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);

                // Close loading
                Swal.close();

                // Show error in table
                $('#tbodySantri').html('<tr><td colspan="5" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Error memuat data</td></tr>');

                // Determine error message based on status
                var errorMessage = 'Terjadi kesalahan saat memuat data';
                var errorTitle = 'Error!';

                if (status === 'timeout') {
                    errorMessage = 'Koneksi timeout. Silakan coba lagi.';
                    errorTitle = 'Timeout!';
                } else if (xhr.status === 404) {
                    errorMessage = 'Endpoint tidak ditemukan. Silakan hubungi administrator.';
                    errorTitle = 'Not Found!';
                } else if (xhr.status === 500) {
                    errorMessage = 'Terjadi kesalahan server. Silakan hubungi administrator.';
                    errorTitle = 'Server Error!';
                } else if (xhr.status === 0) {
                    errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                    errorTitle = 'Connection Error!';
                }

                // Show detailed error with SweetAlert2
                Swal.fire({
                    icon: 'error',
                    title: errorTitle,
                    html: `
                        <div class="text-left">
                            <p><strong>Pesan Error:</strong> ${errorMessage}</p>
                            <p><strong>Status:</strong> ${status}</p>
                            <p><strong>HTTP Code:</strong> ${xhr.status}</p>
                            <details class="mt-3">
                                <summary class="text-muted">Detail Teknis</summary>
                                <small class="text-muted">${error}</small>
                            </details>
                        </div>
                    `,
                    confirmButtonText: 'Coba Lagi',
                    showCancelButton: true,
                    cancelButtonText: 'Tutup',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Retry loading data
                        loadSantriData(idKelas, idTpq);
                    }
                });
            }
        });
    }

    // Update daftar santri yang dipilih
    function updateSelectedSantri() {
        var selectedSantri = [];
        var selectedTpq = [];
        $('.santri-checkbox:checked').each(function() {
            var idTpq = $(this).data('idtpq');
            if (idTpq && selectedTpq.indexOf(idTpq) === -1) {
                selectedTpq.push(idTpq);
            }
            selectedSantri.push({
                id: $(this).val(),
                nama: $(this).data('nama'),
                kelas: $(this).data('kelas'),
                tpq: $(this).data('tpq'),
                idtpq: idTpq
            });
        });

        if (selectedSantri.length > 0) {
            $('#cardKonfirmasi').show();
            var html = '<div class="alert alert-info"><strong>Santri yang dipilih (' + selectedSantri.length + '):</strong></div>';
            html += '<div class="row">';

            $.each(selectedSantri, function(index, santri) {
                html += '<div class="col-md-6 mb-2">';
                html += '<div class="card card-body py-2">';
                html += '<div class="d-flex justify-content-between align-items-center">';
                html += '<div>';
                html += '<strong>' + santri.nama + '</strong><br>';
                html += '<small class="text-muted">ID: ' + santri.id + ' | Kelas: ' + santri.kelas + ' | TPQ: ' + santri.tpq + '</small>';
                html += '</div>';
                html += '<button type="button" class="btn btn-sm btn-outline-danger remove-santri" data-id="' + santri.id + '">';
                html += '<i class="fas fa-times"></i>';
                html += '</button>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            });

            html += '</div>';
            $('#selectedSantriList').html(html);
            $('#btnSimpan').prop('disabled', false);
        } else {
            $('#cardKonfirmasi').hide();
            $('#btnSimpan').prop('disabled', true);
        }
    }

    // Event handler untuk remove santri
    $(document).on('click', '.remove-santri', function() {
        var id = $(this).data('id');
        $('.santri-checkbox[value="' + id + '"]').prop('checked', false);
        updateSelectedSantri();
    });

    function deletePeserta(idSantri, namaSantri) {
        // Tampilkan loading saat mengambil data terkait
        Swal.fire({
            title: 'Memeriksa Data...',
            text: 'Sedang memeriksa data terkait peserta',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // Cek data terkait terlebih dahulu
        $.ajax({
            url: '<?= base_url('backend/munaqosah/check-data-terkait/') ?>' + idSantri,
            type: 'GET',
            dataType: 'json',
            timeout: 30000, // 30 detik timeout
            success: function(response) {
                // Tutup loading
                Swal.close();
                if (response.success) {
                    var dataTerkait = response.data_terkait;
                    var totalTerkait = response.total_terkait;

                    if (totalTerkait > 0) {
                        // Ada data terkait, tampilkan konfirmasi detail
                        var detailMessage = 'Peserta <strong>' + namaSantri + '</strong> memiliki data terkait:<br><br>';

                        if (dataTerkait.nilai_munaqosah) {
                            detailMessage += '• <strong>' + dataTerkait.nilai_munaqosah.count + '</strong> data nilai munaqosah<br>';
                        }
                        if (dataTerkait.antrian_munaqosah) {
                            detailMessage += '• <strong>' + dataTerkait.antrian_munaqosah.count + '</strong> data antrian munaqosah<br>';
                        }

                        detailMessage += '<br><span class="text-danger"><strong>Semua data terkait akan dihapus juga!</strong></span>';
                        detailMessage += '<br><br><div class="alert alert-success"><i class="fas fa-info-circle"></i> <strong>Info:</strong> Data santri utama tidak akan dihapus, hanya dihapus dari daftar peserta munaqosah.</div>';

                        Swal.fire({
                            title: 'Konfirmasi Hapus Data Terkait',
                            html: detailMessage,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, Hapus Semua!',
                            cancelButtonText: 'Batal',
                            showLoaderOnConfirm: true,
                            preConfirm: () => {
                                return performDelete(idSantri);
                            }
                        });
                    } else {
                        // Tidak ada data terkait, hapus langsung
                        Swal.fire({
                            title: 'Konfirmasi Hapus',
                            html: `Apakah Anda yakin ingin menghapus peserta <strong>${namaSantri}</strong>?<br><br><span class="text-success">✓ Tidak ada data terkait yang akan terpengaruh.</span><br><br><div class="alert alert-success"><i class="fas fa-info-circle"></i> <strong>Info:</strong> Data santri utama tidak akan dihapus, hanya dihapus dari daftar peserta munaqosah.</div>`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal',
                            showLoaderOnConfirm: true,
                            preConfirm: () => {
                                return performDelete(idSantri);
                            }
                        });
                    }
                } else {
                    // Tampilkan detail errors jika ada
                    var errorMessage = response.message;
                    var detailedErrors = '';

                    if (response.detailed_errors && response.detailed_errors.length > 0) {
                        detailedErrors = '<br><br><div class="alert alert-danger"><strong><i class="fas fa-exclamation-triangle"></i> Detail Error:</strong><br>';
                        response.detailed_errors.forEach(function(error, index) {
                            detailedErrors += '<div class="mt-2"><span class="badge badge-danger">' + (index + 1) + '</span> ' + error + '</div>';
                        });
                        detailedErrors += '</div>';
                    }

                    if (response.error_count) {
                        detailedErrors += '<br><small class="text-muted">Total error: ' + response.error_count + '</small>';
                    }

                    Swal.fire({
                        title: 'Error!',
                        html: errorMessage + detailedErrors,
                        icon: 'error',
                        width: '600px'
                    });
                }
            },
            error: function(xhr, status, error) {
                // Tutup loading jika ada error
                Swal.close();

                var errorMessage = 'Terjadi kesalahan saat mengecek data terkait';
                var errorTitle = 'Error!';

                // Determine error message based on status
                if (status === 'timeout') {
                    errorMessage = 'Koneksi timeout saat mengecek data terkait. Silakan coba lagi.';
                    errorTitle = 'Timeout!';
                } else if (xhr.status === 404) {
                    errorMessage = 'Endpoint tidak ditemukan. Silakan hubungi administrator.';
                    errorTitle = 'Not Found!';
                } else if (xhr.status === 500) {
                    errorMessage = 'Terjadi kesalahan server. Silakan hubungi administrator.';
                    errorTitle = 'Server Error!';
                } else if (xhr.status === 0) {
                    errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                    errorTitle = 'Connection Error!';
                }

                // Coba parse response error jika ada
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.detailed_errors && response.detailed_errors.length > 0) {
                        var detailedErrors = '<br><br><div class="alert alert-danger"><strong><i class="fas fa-exclamation-triangle"></i> Detail Error:</strong><br>';
                        response.detailed_errors.forEach(function(error, index) {
                            detailedErrors += '<div class="mt-2"><span class="badge badge-danger">' + (index + 1) + '</span> ' + error + '</div>';
                        });
                        detailedErrors += '</div>';
                        errorMessage += detailedErrors;
                    }
                } catch (e) {
                    // Jika tidak bisa parse JSON, gunakan error default
                }

                Swal.fire({
                    title: errorTitle,
                    html: `
                        <div class="text-left">
                            <p><strong>Pesan Error:</strong> ${errorMessage}</p>
                            <p><strong>Status:</strong> ${status}</p>
                            <p><strong>HTTP Code:</strong> ${xhr.status}</p>
                            <details class="mt-3">
                                <summary class="text-muted">Detail Teknis</summary>
                                <small class="text-muted">${error}</small>
                            </details>
                        </div>
                    `,
                    icon: 'error',
                    width: '600px',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    function performDelete(idSantri) {
        return $.ajax({
            url: '<?= base_url('backend/munaqosah/delete-peserta-by-santri/') ?>' + idSantri,
            type: 'DELETE',
            dataType: 'json',
            timeout: 30000 // 30 detik timeout
        }).then(function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: response.message,
                    icon: 'success'
                }).then(() => {
                    location.reload();
                });
            } else {
                // Tampilkan detail errors jika ada
                var errorMessage = response.message;
                var detailedErrors = '';

                if (response.detailed_errors && response.detailed_errors.length > 0) {
                    detailedErrors = '<br><br><div class="alert alert-danger"><strong><i class="fas fa-exclamation-triangle"></i> Detail Error:</strong><br>';
                    response.detailed_errors.forEach(function(error, index) {
                        detailedErrors += '<div class="mt-2"><span class="badge badge-danger">' + (index + 1) + '</span> ' + error + '</div>';
                    });
                    detailedErrors += '</div>';
                }

                if (response.error_count) {
                    detailedErrors += '<br><small class="text-muted">Total error: ' + response.error_count + '</small>';
                }

                Swal.fire({
                    title: 'Gagal!',
                    html: errorMessage + detailedErrors,
                    icon: 'error',
                    width: '600px'
                });
            }
        }).catch(function(xhr) {
            var errorMessage = 'Terjadi kesalahan saat menghapus data';
            var errorTitle = 'Error!';
            var status = xhr.statusText || 'Unknown';
            var httpCode = xhr.status || 0;

            // Determine error message based on status
            if (status === 'timeout') {
                errorMessage = 'Koneksi timeout saat menghapus data. Silakan coba lagi.';
                errorTitle = 'Timeout!';
            } else if (httpCode === 404) {
                errorMessage = 'Endpoint tidak ditemukan. Silakan hubungi administrator.';
                errorTitle = 'Not Found!';
            } else if (httpCode === 500) {
                errorMessage = 'Terjadi kesalahan server. Silakan hubungi administrator.';
                errorTitle = 'Server Error!';
            } else if (httpCode === 0) {
                errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                errorTitle = 'Connection Error!';
            }

            // Coba parse response error jika ada
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.detailed_errors && response.detailed_errors.length > 0) {
                    var detailedErrors = '<br><br><div class="alert alert-danger"><strong><i class="fas fa-exclamation-triangle"></i> Detail Error:</strong><br>';
                    response.detailed_errors.forEach(function(error, index) {
                        detailedErrors += '<div class="mt-2"><span class="badge badge-danger">' + (index + 1) + '</span> ' + error + '</div>';
                    });
                    detailedErrors += '</div>';
                    errorMessage += detailedErrors;
                }
            } catch (e) {
                // Jika tidak bisa parse JSON, gunakan error default
            }

            Swal.fire({
                title: errorTitle,
                html: `
                    <div class="text-left">
                        <p><strong>Pesan Error:</strong> ${errorMessage}</p>
                        <p><strong>Status:</strong> ${status}</p>
                        <p><strong>HTTP Code:</strong> ${httpCode}</p>
                    </div>
                `,
                icon: 'error',
                width: '600px',
                confirmButtonText: 'OK'
            });
        });
    }

    // Fungsi untuk edit peserta
    function editPeserta(IdSantri, NamaSantri, statusVerifikasi) {
        // Tampilkan loading
        Swal.fire({
            title: 'Memuat Data...',
            text: 'Sedang mengambil data santri',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // AJAX untuk mengambil detail santri
        $.ajax({
            url: '<?= base_url('backend/munaqosah/get-detail-santri') ?>',
            type: 'POST',
            data: {
                IdSantri: IdSantri
            },
            dataType: 'json',
            timeout: 30000, // 30 detik timeout
            success: function(response) {
                Swal.close();

                if (response.success) {
                    const data = response.data;
                    const verifikasi = response.verifikasi;

                    // Isi form dengan data yang diterima
                    $('#editIdSantri').val(data.IdSantri);
                    $('#editIdSantriDisplay').val(data.IdSantri);
                    $('#editStatusVerifikasi').val(statusVerifikasi || '');

                    // Jika ada data verifikasi dengan status "perlu_perbaikan"
                    if (verifikasi && verifikasi.status_verifikasi === 'perlu_perbaikan') {
                        // Tampilkan alert
                        $('#alertPerluPerbaikan').show();
                        // Tampilkan card informasi proses review
                        $('#cardInfoProsesReview').show();
                        if (verifikasi.keterangan_text) {
                            $('#keteranganUser').html('<strong>Keterangan dari User:</strong><br>' + verifikasi.keterangan_text.replace(/\n/g, '<br>'));
                        } else {
                            $('#keteranganUser').html('<strong>Keterangan:</strong> Tidak ada keterangan tambahan');
                        }

                        // Set ID peserta untuk update status verifikasi
                        if (verifikasi.id_peserta) {
                            $('#editIdPeserta').val(verifikasi.id_peserta);
                        }

                        // Setup tabel perbaikan data
                        if (verifikasi.perbaikan_data) {
                            const perbaikanData = verifikasi.perbaikan_data;

                            // Mapping nama field ke label dan field ID
                            const fieldMap = {
                                'nama': {
                                    label: 'Nama Santri',
                                    fieldId: 'editNamaSantri',
                                    type: 'text'
                                },
                                'tempatLahir': {
                                    label: 'Tempat Lahir',
                                    fieldId: 'editTempatLahirSantri',
                                    type: 'text'
                                },
                                'tanggalLahir': {
                                    label: 'Tanggal Lahir',
                                    fieldId: 'editTanggalLahirSantri',
                                    type: 'date'
                                },
                                'jenisKelamin': {
                                    label: 'Jenis Kelamin',
                                    fieldId: 'editJenisKelamin',
                                    type: 'select'
                                },
                                'namaAyah': {
                                    label: 'Nama Ayah',
                                    fieldId: 'editNamaAyah',
                                    type: 'text'
                                }
                            };

                            // Generate tabel perbaikan data
                            generateTablePerbaikanData(data, perbaikanData, fieldMap);

                            // Tampilkan tabel
                            $('#tablePerbaikanData').show();

                            // Set nilai data asli ke form fields sebelum di-hide (untuk digunakan saat submit)
                            // Form fields akan di-hide, tapi nilai tetap tersimpan untuk submit
                            $('#editNamaSantri').val(data.NamaSantri || '');
                            $('#editTempatLahirSantri').val(data.TempatLahirSantri || '');
                            $('#editTanggalLahirSantri').val(data.TanggalLahirSantri || '');
                            $('#editJenisKelamin').val(data.JenisKelamin || '');
                            $('#editNamaAyah').val(data.NamaAyah || '');

                            // Hide form fields - fokus hanya pada tabel
                            // Form fields di bawah tabel tidak diperlukan karena semua input dilakukan melalui tabel
                            // Tapi nilai tetap disimpan untuk digunakan saat submit
                            $('#editNamaSantri').closest('.form-group').parent().hide();
                            $('#editTempatLahirSantri').closest('.form-group').parent().hide();
                            $('#editTanggalLahirSantri').closest('.form-group').parent().hide();
                            $('#editJenisKelamin').closest('.form-group').parent().hide();
                            $('#editNamaAyah').closest('.form-group').parent().hide();

                            // Update modal title
                            $('#modalEditPesertaLabel').html('<i class="fas fa-check-double"></i> Review & Konfirmasi Perbaikan Data');

                            // Update info message
                            const fieldsToFixCount = Object.keys(perbaikanData).filter(key =>
                                perbaikanData[key] && String(perbaikanData[key]).trim() !== ''
                            ).length;

                            $('#editInfoMessage').html(`
                                <i class="fas fa-info-circle"></i> 
                                <strong>Review perubahan yang diusulkan.</strong><br>
                                Gunakan toggle switch <strong>"Terima"</strong> untuk menggunakan nilai usulan, atau <strong>"Tidak"</strong> untuk mengosongkan kolom perbaikan.
                                Anda juga dapat mengedit manual di kolom "Perbaikan (Sesudah)" jika diperlukan.
                                Hanya <strong>${fieldsToFixCount} field</strong> yang diminta perbaikan yang dapat diubah.
                            `);
                            $('#editConfirmLabel').text('Saya telah mereview dan menyetujui perbaikan data ini.');
                        } else {
                            // Jika tidak ada perbaikan data, sembunyikan tabel
                            $('#tablePerbaikanData').hide();

                            // Set nilai normal dan disable semua
                            $('#editNamaSantri').val(data.NamaSantri || '').prop('disabled', true);
                            $('#editTempatLahirSantri').val(data.TempatLahirSantri || '').prop('disabled', true);
                            $('#editTanggalLahirSantri').val(data.TanggalLahirSantri || '').prop('disabled', true);
                            $('#editJenisKelamin').val(data.JenisKelamin || '').prop('disabled', true);
                            $('#editNamaAyah').val(data.NamaAyah || '').prop('disabled', true);

                            // Update modal title
                            $('#modalEditPesertaLabel').html('<i class="fas fa-user-edit"></i> Edit Data Peserta Munaqosah');

                            // Update info message
                            $('#editInfoMessage').html('<i class="fas fa-info-circle"></i> Perubahan data ini akan disimpan di data utama santri.');
                            $('#editConfirmLabel').text('Saya mengerti dan menyetujui perubahan ini.');
                        }
                    } else {
                        // Tidak ada status perlu perbaikan - edit normal
                        // Hide alert dan tabel perbaikan
                        $('#alertPerluPerbaikan').hide();
                        $('#cardInfoProsesReview').hide();
                        $('#fieldsToFixList').html('');
                        $('#tablePerbaikanData').hide();
                        $('.comparison-row').hide();
                        $('.suggested-value').hide();
                        $('.field-disabled-badge').remove();

                        // Show form fields untuk edit normal
                        $('#editNamaSantri').closest('.form-group').parent().show();
                        $('#editTempatLahirSantri').closest('.form-group').parent().show();
                        $('#editTanggalLahirSantri').closest('.form-group').parent().show();
                        $('#editJenisKelamin').closest('.form-group').parent().show();
                        $('#editNamaAyah').closest('.form-group').parent().show();

                        // Enable semua field untuk edit normal (bukan review perbaikan)
                        $('#editNamaSantri').prop('disabled', false);
                        $('#editTempatLahirSantri').prop('disabled', false);
                        $('#editTanggalLahirSantri').prop('disabled', false);
                        $('#editJenisKelamin').prop('disabled', false);
                        $('#editNamaAyah').prop('disabled', false);

                        // Set nilai normal
                        $('#editNamaSantri').val(data.NamaSantri);
                        $('#editTempatLahirSantri').val(data.TempatLahirSantri);
                        $('#editTanggalLahirSantri').val(data.TanggalLahirSantri);
                        $('#editJenisKelamin').val(data.JenisKelamin);
                        $('#editNamaAyah').val(data.NamaAyah);

                        // Clear ID peserta jika tidak ada status perlu perbaikan
                        $('#editIdPeserta').val('');

                        // Update modal title
                        $('#modalEditPesertaLabel').html('<i class="fas fa-user-edit"></i> Edit Data Peserta Munaqosah');

                        // Update info message
                        $('#editInfoMessage').html('<i class="fas fa-info-circle"></i> Perubahan data ini akan disimpan di data utama santri.');
                        $('#editConfirmLabel').text('Saya mengerti dan menyetujui perubahan ini.');
                    }

                    // Tampilkan informasi kartu keluarga
                    displayKartuKeluargaInfo(data);

                    // Reset checkbox dan button
                    $('#editConfirmSave').prop('checked', false);
                    const submitButton = $('#formEditPeserta button[type="submit"]');
                    submitButton.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
                    console.log('Modal opened - checkbox and button reset. Submit button disabled:', submitButton.prop('disabled'));

                    // Hapus semua styling field-to-fix yang mungkin tersisa
                    $('.form-group').removeClass('field-to-fix');
                    $('.field-disabled-badge').remove();

                    // Remove event handler untuk shown.bs.modal jika ada (untuk menghindari multiple handlers)
                    $('#modalEditPeserta').off('shown.bs.modal');

                    // Cleanup saat modal ditutup
                    $('#modalEditPeserta').on('hidden.bs.modal', function() {
                        // Hide card informasi proses review
                        $('#cardInfoProsesReview').hide();
                        // Reset tabel perbaikan
                        $('#tbodyPerbaikanData').empty();
                        $('#tablePerbaikanData').hide();

                        // Show form fields kembali (untuk edit normal)
                        $('#editNamaSantri').closest('.form-group').parent().show();
                        $('#editTempatLahirSantri').closest('.form-group').parent().show();
                        $('#editTanggalLahirSantri').closest('.form-group').parent().show();
                        $('#editJenisKelamin').closest('.form-group').parent().show();
                        $('#editNamaAyah').closest('.form-group').parent().show();

                        // Remove event handlers untuk menghindari memory leaks
                        $('.toggle-switch-input').off('change');
                        $('.perbaikan-input').off('change input');
                    });

                    // Tampilkan modal
                    $('#modalEditPeserta').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Gagal mengambil data santri'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();

                var errorMessage = 'Terjadi kesalahan saat mengambil data santri';
                var errorTitle = 'Error!';

                // Determine error message based on status
                if (status === 'timeout') {
                    errorMessage = 'Koneksi timeout saat mengambil data santri. Silakan coba lagi.';
                    errorTitle = 'Timeout!';
                } else if (xhr.status === 404) {
                    errorMessage = 'Endpoint tidak ditemukan. Silakan hubungi administrator.';
                    errorTitle = 'Not Found!';
                } else if (xhr.status === 500) {
                    errorMessage = 'Terjadi kesalahan server. Silakan hubungi administrator.';
                    errorTitle = 'Server Error!';
                } else if (xhr.status === 0) {
                    errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                    errorTitle = 'Connection Error!';
                }

                Swal.fire({
                    icon: 'error',
                    title: errorTitle,
                    html: `
                        <div class="text-left">
                            <p><strong>Pesan Error:</strong> ${errorMessage}</p>
                            <p><strong>Status:</strong> ${status}</p>
                            <p><strong>HTTP Code:</strong> ${xhr.status}</p>
                            <details class="mt-3">
                                <summary class="text-muted">Detail Teknis</summary>
                                <small class="text-muted">${error}</small>
                            </details>
                        </div>
                    `,
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    // Fungsi untuk generate tabel perbaikan data
    function generateTablePerbaikanData(data, perbaikanData, fieldMap) {
        const tbody = $('#tbodyPerbaikanData');
        tbody.empty(); // Clear existing rows

        // Mapping data saat ini
        const currentDataMap = {
            'nama': data.NamaSantri || '',
            'tempatLahir': data.TempatLahirSantri || '',
            'tanggalLahir': data.TanggalLahirSantri || '',
            'jenisKelamin': data.JenisKelamin || '',
            'namaAyah': data.NamaAyah || ''
        };

        // Format tanggal untuk display
        const formatDateForDisplay = (dateStr) => {
            if (!dateStr) return '';
            try {
                const date = new Date(dateStr + 'T00:00:00');
                if (!isNaN(date.getTime())) {
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    return `${day}-${month}-${year}`;
                }
            } catch (e) {
                // Ignore error
            }
            return dateStr;
        };

        // Format tanggal untuk input date
        const formatDateForInput = (dateStr) => {
            if (!dateStr) return '';
            try {
                const date = new Date(dateStr + 'T00:00:00');
                if (!isNaN(date.getTime())) {
                    return dateStr; // Return as is if valid date
                }
            } catch (e) {
                // Ignore error
            }
            return dateStr;
        };

        // Iterate through fields that have perbaikan data
        Object.keys(perbaikanData).forEach(key => {
            const usulanValue = perbaikanData[key];
            if (!usulanValue || String(usulanValue).trim() === '') {
                return; // Skip empty values
            }

            const fieldInfo = fieldMap[key];
            if (!fieldInfo) {
                return; // Skip unknown fields
            }

            const currentValue = currentDataMap[key] || '';
            const fieldId = fieldInfo.fieldId;
            const rowId = 'row_' + key;

            // Determine display values
            let currentDisplay = currentValue;
            let usulanDisplay = usulanValue;
            let perbaikanValue = ''; // Default to empty - waiting for user action

            if (key === 'tanggalLahir') {
                currentDisplay = formatDateForDisplay(currentValue);
                usulanDisplay = formatDateForDisplay(usulanValue);
                // perbaikanValue tetap kosong
            } else if (key === 'jenisKelamin') {
                currentDisplay = currentValue || '-';
                usulanDisplay = usulanValue || '-';
                // perbaikanValue tetap kosong
            }

            // Format usulan value for input (for date field)
            let usulanValueForInput = usulanValue;
            if (key === 'tanggalLahir') {
                usulanValueForInput = formatDateForInput(usulanValue);
            }

            // Create table row
            let perbaikanInput = '';
            if (fieldInfo.type === 'select') {
                // For select field, create select dropdown
                perbaikanInput = `
                    <select class="form-control perbaikan-input" id="perbaikan_${key}" data-field="${key}" data-field-id="${fieldId}" data-usulan="${usulanValue}">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                `;
            } else if (fieldInfo.type === 'date') {
                // For date field, create date input
                perbaikanInput = `
                    <input type="date" class="form-control perbaikan-input" id="perbaikan_${key}" 
                           data-field="${key}" data-field-id="${fieldId}" data-usulan="${usulanValueForInput}" value="" placeholder="Pilih tanggal">
                `;
            } else {
                // For text field, create text input
                perbaikanInput = `
                    <input type="text" class="form-control perbaikan-input" id="perbaikan_${key}" 
                           data-field="${key}" data-field-id="${fieldId}" data-usulan="${usulanValue}" value="" placeholder="Kosong">
                `;
            }

            const row = `
                <tr id="${rowId}">
                    <td class="field-name">${fieldInfo.label}</td>
                    <td class="current-value-cell">${currentDisplay || '-'}</td>
                    <td class="usulan-value-cell">${usulanDisplay || '-'}</td>
                    <td class="text-center">
                        <div class="toggle-switch-container">
                            <label class="toggle-switch">
                                <input type="checkbox" class="toggle-switch-input" data-field="${key}" data-usulan="${usulanValueForInput || usulanValue}">
                                <span class="toggle-switch-slider">
                                    <span class="toggle-switch-label-on">TERIMA</span>
                                    <span class="toggle-switch-label-off">TIDAK</span>
                                </span>
                            </label>
                        </div>
                    </td>
                    <td>
                        ${perbaikanInput}
                    </td>
                </tr>
            `;

            tbody.append(row);

            // Set initial toggle state - unchecked (Tidak) karena input kosong
            const rowElement = $(`#${rowId}`);
            const toggleInput = rowElement.find('.toggle-switch-input');
            toggleInput.prop('checked', false);
        });

        // Attach event handlers for toggle switch
        $('.toggle-switch-input').off('change').on('change', function() {
            const isChecked = $(this).is(':checked');
            const field = $(this).data('field');
            const usulanValue = $(this).data('usulan');
            const perbaikanInput = $('#perbaikan_' + field);
            const fieldInfo = fieldMap[field];

            if (isChecked) {
                // Toggle ON = Terima - Set value to usulan
                if (fieldInfo.type === 'date') {
                    // Format date for input
                    const formattedDate = formatDateForInput(usulanValue);
                    perbaikanInput.val(formattedDate);
                } else if (fieldInfo.type === 'select') {
                    perbaikanInput.val(usulanValue);
                } else {
                    perbaikanInput.val(usulanValue);
                }

                // Add visual feedback
                perbaikanInput.css({
                    'background-color': '#d4edda',
                    'border-color': '#28a745'
                });

                setTimeout(() => {
                    perbaikanInput.css({
                        'background-color': '',
                        'border-color': '#28a745'
                    });
                }, 500);
            } else {
                // Toggle OFF = Tidak - Clear value
                perbaikanInput.val('');

                // Add visual feedback
                perbaikanInput.css({
                    'background-color': '#f8d7da',
                    'border-color': '#dc3545'
                });

                setTimeout(() => {
                    perbaikanInput.css({
                        'background-color': '',
                        'border-color': '#28a745'
                    });
                }, 500);
            }

            // Focus on the input
            perbaikanInput.focus();
        });

        // Attach event handlers for perbaikan inputs
        // TIDAK perlu sync dengan form fields karena form fields di-hide
        // Data akan diambil langsung dari tabel saat submit
        $(document).off('change input', '.perbaikan-input').on('change input', '.perbaikan-input', function() {
            const value = $(this).val();
            const field = $(this).data('field');
            const usulanValue = $(this).data('usulan');
            const row = $(this).closest('tr');
            const toggleInput = row.find('.toggle-switch-input');

            // Update toggle state based on input value
            // Normalize values for comparison
            const normalizeValue = (val) => {
                if (!val) return '';
                return String(val).trim();
            };

            const normalizedValue = normalizeValue(value);
            const normalizedUsulan = normalizeValue(usulanValue);

            if (normalizedValue === '') {
                // Input kosong - Toggle OFF (Tidak)
                toggleInput.prop('checked', false);
            } else if (normalizedValue === normalizedUsulan) {
                // Input sama dengan usulan - Ya active
                btnYes.addClass('active');
                btnNo.removeClass('active');
            } else {
                // Input berbeda dari usulan - tidak ada yang active (user edit manual)
                btnYes.removeClass('active');
                btnNo.removeClass('active');
            }
        });

        // Tidak perlu sync dari form fields ke tabel karena form fields di-hide
        // Data hanya diambil dari tabel saat submit
    }

    // Fungsi untuk setup field comparison
    function setupFieldComparison(fieldName, currentValue, suggestedValue) {
        // Normalize values untuk perbandingan
        const normalizeValue = (val) => {
            if (!val) return '';
            return val.toString().trim().toLowerCase();
        };

        const normalizedCurrent = normalizeValue(currentValue);
        const normalizedSuggested = normalizeValue(suggestedValue);

        // Jika tidak ada perbaikan atau sama dengan nilai saat ini, sembunyikan comparison
        if (!suggestedValue || normalizedSuggested === normalizedCurrent || normalizedSuggested === '') {
            hideFieldComparison(fieldName);
            setFieldValue(fieldName, currentValue || '');
            // Field ini tidak perlu diperbaiki, jadi disable
            disableField(fieldName);
            return;
        }

        // Ada perbaikan data untuk field ini
        // Enable field dan tampilkan comparison
        enableField(fieldName);
        showFieldComparison(fieldName, currentValue, suggestedValue);
    }

    // Fungsi untuk menampilkan field comparison
    function showFieldComparison(fieldName, currentValue, suggestedValue) {
        // Define field mapping dengan mainField untuk akses ke input utama
        const fieldMapDefinition = {
            'nama': {
                current: '#editNamaSantriCurrent',
                suggested: '#editNamaSantriSuggested',
                toggle: '#toggleNama',
                container: '#fieldNama .suggested-value',
                comparisonRow: '#fieldNama .comparison-row',
                mainField: '#editNamaSantri'
            },
            'tempatLahir': {
                current: '#editTempatLahirSantriCurrent',
                suggested: '#editTempatLahirSantriSuggested',
                toggle: '#toggleTempatLahir',
                container: '#fieldTempatLahir .suggested-value',
                comparisonRow: '#fieldTempatLahir .comparison-row',
                mainField: '#editTempatLahirSantri'
            },
            'tanggalLahir': {
                current: '#editTanggalLahirSantriCurrent',
                suggested: '#editTanggalLahirSantriSuggested',
                toggle: '#toggleTanggalLahir',
                container: '#fieldTanggalLahir .suggested-value',
                comparisonRow: '#fieldTanggalLahir .comparison-row',
                mainField: '#editTanggalLahirSantri'
            },
            'jenisKelamin': {
                current: '#editJenisKelaminCurrent',
                suggested: '#editJenisKelaminSuggested',
                toggle: '#toggleJenisKelamin',
                container: '#fieldJenisKelamin .suggested-value',
                comparisonRow: '#fieldJenisKelamin .comparison-row',
                mainField: '#editJenisKelamin'
            },
            'namaAyah': {
                current: '#editNamaAyahCurrent',
                suggested: '#editNamaAyahSuggested',
                toggle: '#toggleNamaAyah',
                container: '#fieldNamaAyah .suggested-value',
                comparisonRow: '#fieldNamaAyah .comparison-row',
                mainField: '#editNamaAyah'
            }
        };

        const field = fieldMapDefinition[fieldName];
        if (!field) return;

        // Format tanggal jika field tanggalLahir
        let displayCurrent = currentValue || '';
        let displaySuggested = suggestedValue || '';

        if (fieldName === 'tanggalLahir') {
            if (currentValue) {
                try {
                    const currentDate = new Date(currentValue + 'T00:00:00');
                    if (!isNaN(currentDate.getTime())) {
                        const day = String(currentDate.getDate()).padStart(2, '0');
                        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
                        const year = currentDate.getFullYear();
                        displayCurrent = `${day}-${month}-${year}`;
                    }
                } catch (e) {
                    displayCurrent = currentValue;
                }
            }
            if (suggestedValue) {
                try {
                    const suggestedDate = new Date(suggestedValue + 'T00:00:00');
                    if (!isNaN(suggestedDate.getTime())) {
                        const day = String(suggestedDate.getDate()).padStart(2, '0');
                        const month = String(suggestedDate.getMonth() + 1).padStart(2, '0');
                        const year = suggestedDate.getFullYear();
                        displaySuggested = `${day}-${month}-${year}`;
                    }
                } catch (e) {
                    displaySuggested = suggestedValue;
                }
            }
        }

        // Set nilai untuk display
        $(field.current).val(displayCurrent || '-');
        $(field.suggested).val(displaySuggested || '-');

        // Tampilkan comparison row (yang berisi current dan suggested value)
        $(field.comparisonRow).show();
        // Tampilkan current value container
        $(field.current).closest('.current-value').show();
        // Tampilkan suggested value container
        $(field.container).show();

        // Enable field karena ini field yang perlu diperbaiki
        enableField(fieldName);

        // Reset toggle ke false (default: tidak menggunakan usulan)
        $(field.toggle).prop('checked', false);

        // Set nilai default ke current value
        setFieldValue(fieldName, currentValue);

        // Tambahkan visual indicator bahwa field ini fokus untuk perbaikan
        const mainField = $(fieldMapDefinition[fieldName].mainField);
        if (mainField.length > 0 && !mainField.prop('disabled')) {
            const fieldContainer = mainField.closest('.form-group');
            fieldContainer.addClass('field-to-fix');

            // Apply styling untuk field yang perlu diperbaiki
            mainField.css({
                'border-left': '4px solid #ffc107',
                'background-color': '#fff9e6',
                'border-color': '#ffc107'
            });

            // Update label untuk menunjukkan field ini perlu diperbaiki
            const label = fieldContainer.find('label');
            if (label.length > 0) {
                label.css({
                    'font-weight': '600',
                    'color': '#856404'
                });

                // Tambahkan indicator di label jika belum ada
                if (label.find('.field-fix-indicator').length === 0) {
                    label.append(' <span class="badge badge-warning badge-sm field-fix-indicator"><i class="fas fa-exclamation-circle"></i> Perlu Perbaikan</span>');
                }
            }
        }

        // Event handler untuk toggle (hapus handler lama jika ada)
        // Simpan nilai currentValue dan suggestedValue dalam closure untuk digunakan di event handler
        $(field.toggle).off('change').on('change', function() {
            const isChecked = $(this).is(':checked');
            const mainField = $(fieldMapDefinition[fieldName].mainField);

            if (isChecked) {
                // Gunakan nilai usulan
                setFieldValue(fieldName, suggestedValue);
                // Update visual indicator
                mainField.css({
                    'border-left': '4px solid #28a745',
                    'background-color': '#e8f5e9'
                });
            } else {
                // Gunakan nilai saat ini
                setFieldValue(fieldName, currentValue);
                // Update visual indicator
                mainField.css({
                    'border-left': '4px solid #ffc107',
                    'background-color': '#fff9e6'
                });
            }
        });
    }

    // Fungsi untuk menyembunyikan field comparison
    function hideFieldComparison(fieldName) {
        const fieldMap = {
            'nama': {
                container: '#fieldNama .suggested-value',
                row: '#fieldNama .comparison-row',
                mainField: '#editNamaSantri'
            },
            'tempatLahir': {
                container: '#fieldTempatLahir .suggested-value',
                row: '#fieldTempatLahir .comparison-row',
                mainField: '#editTempatLahirSantri'
            },
            'tanggalLahir': {
                container: '#fieldTanggalLahir .suggested-value',
                row: '#fieldTanggalLahir .comparison-row',
                mainField: '#editTanggalLahirSantri'
            },
            'jenisKelamin': {
                container: '#fieldJenisKelamin .suggested-value',
                row: '#fieldJenisKelamin .comparison-row',
                mainField: '#editJenisKelamin'
            },
            'namaAyah': {
                container: '#fieldNamaAyah .suggested-value',
                row: '#fieldNamaAyah .comparison-row',
                mainField: '#editNamaAyah'
            }
        };

        if (fieldMap[fieldName]) {
            $(fieldMap[fieldName].container).hide();
            $(fieldMap[fieldName].row).hide();

            // Hapus styling field-to-fix jika ada
            const mainField = $(fieldMap[fieldName].mainField);
            if (mainField.length > 0) {
                const fieldContainer = mainField.closest('.form-group');
                fieldContainer.removeClass('field-to-fix');
                mainField.css({
                    'border-left': '',
                    'background-color': '',
                    'border-color': ''
                });

                // Hapus indicator dari label
                const label = fieldContainer.find('label');
                if (label.length > 0) {
                    label.find('.field-fix-indicator').remove();
                    label.css({
                        'font-weight': '',
                        'color': ''
                    });
                }
            }
        }
    }

    // Fungsi untuk menyembunyikan semua field comparison
    function hideAllFieldComparisons() {
        $('.suggested-value').hide();
        $('.comparison-row').hide();
    }

    // Fungsi untuk set nilai field
    function setFieldValue(fieldName, value) {
        const fieldMap = {
            'nama': '#editNamaSantri',
            'tempatLahir': '#editTempatLahirSantri',
            'tanggalLahir': '#editTanggalLahirSantri',
            'jenisKelamin': '#editJenisKelamin',
            'namaAyah': '#editNamaAyah'
        };

        if (fieldMap[fieldName]) {
            const field = $(fieldMap[fieldName]);
            if (field.length > 0) {
                // Jika field adalah select, pastikan option ada
                if (field.is('select')) {
                    const optionValue = value || '';
                    if (optionValue && field.find('option[value="' + optionValue + '"]').length > 0) {
                        field.val(optionValue);
                    } else {
                        field.val('');
                    }
                } else {
                    field.val(value || '');
                }
            }
        }
    }

    // Fungsi untuk disable field
    function disableField(fieldName) {
        const fieldMap = {
            'nama': '#editNamaSantri',
            'tempatLahir': '#editTempatLahirSantri',
            'tanggalLahir': '#editTanggalLahirSantri',
            'jenisKelamin': '#editJenisKelamin',
            'namaAyah': '#editNamaAyah'
        };

        if (fieldMap[fieldName]) {
            const field = $(fieldMap[fieldName]);
            if (field.length > 0) {
                // Disable field
                field.prop('disabled', true);
                field.attr('readonly', true);

                // Apply styling untuk disabled field
                // Hapus class yang mungkin mengganggu
                field.removeClass('field-to-fix-active');

                // Apply styling dengan !important untuk override
                field.attr('style',
                    field.attr('style') +
                    '; background-color: #e9ecef !important;' +
                    'cursor: not-allowed !important;' +
                    'opacity: 0.7 !important;' +
                    'border-left: 4px solid #6c757d !important;' +
                    'color: #6c757d !important;'
                );

                // Tambahkan badge/label untuk menunjukkan field ini tidak bisa diubah
                const fieldContainer = field.closest('.form-group');
                const label = fieldContainer.find('label');

                // Update label styling
                if (label.length > 0) {
                    label.css({
                        'opacity': '0.6',
                        'color': '#6c757d'
                    });
                }

                // Tambahkan badge jika belum ada
                if (fieldContainer.find('.field-disabled-badge').length === 0) {
                    fieldContainer.append('<small class="text-muted field-disabled-badge d-block mt-1"><i class="fas fa-lock"></i> Field ini tidak diminta perbaikan</small>');
                }

                // Hapus styling field-to-fix jika ada
                fieldContainer.removeClass('field-to-fix');
            }
        }
    }

    // Fungsi untuk enable field
    function enableField(fieldName) {
        const fieldMap = {
            'nama': '#editNamaSantri',
            'tempatLahir': '#editTempatLahirSantri',
            'tanggalLahir': '#editTanggalLahirSantri',
            'jenisKelamin': '#editJenisKelamin',
            'namaAyah': '#editNamaAyah'
        };

        if (fieldMap[fieldName]) {
            const field = $(fieldMap[fieldName]);
            if (field.length > 0) {
                // Enable field
                field.prop('disabled', false);
                field.removeAttr('readonly');

                // Hapus semua inline styling yang terkait dengan disabled state
                // Kita akan reset dengan menghapus style attribute dan mengembalikan ke default
                const currentStyle = field.attr('style') || '';
                // Hapus style yang terkait dengan disabled
                const newStyle = currentStyle
                    .replace(/background-color[^;]*;?/gi, '')
                    .replace(/cursor[^;]*;?/gi, '')
                    .replace(/opacity[^;]*;?/gi, '')
                    .replace(/border-left[^;]*;?/gi, '')
                    .replace(/color[^;]*;?/gi, '')
                    .replace(/border-color[^;]*;?/gi, '')
                    .replace(/;{2,}/g, ';')
                    .replace(/^;|;$/g, '')
                    .trim();

                if (newStyle) {
                    field.attr('style', newStyle);
                } else {
                    field.removeAttr('style');
                }

                // Reset kelas yang mungkin mengganggu
                field.removeClass('field-to-fix-active');

                // Hapus badge/label disabled
                const fieldContainer = field.closest('.form-group');
                fieldContainer.find('.field-disabled-badge').remove();

                // Reset label styling (kecuali jika field-to-fix, akan di-set oleh showFieldComparison)
                const label = fieldContainer.find('label');
                if (label.length > 0 && !fieldContainer.hasClass('field-to-fix')) {
                    label.css({
                        'opacity': '',
                        'color': '',
                        'font-weight': ''
                    });
                    // Hapus indicator field-fix jika ada (akan ditambahkan lagi oleh showFieldComparison jika perlu)
                    label.find('.field-fix-indicator').remove();
                }
            }
        }
    }

    // Fungsi untuk fokus ke field
    function focusField(fieldName) {
        const fieldMap = {
            'nama': '#editNamaSantri',
            'tempatLahir': '#editTempatLahirSantri',
            'tanggalLahir': '#editTanggalLahirSantri',
            'jenisKelamin': '#editJenisKelamin',
            'namaAyah': '#editNamaAyah'
        };

        if (fieldMap[fieldName]) {
            const field = $(fieldMap[fieldName]);
            if (field.length > 0 && !field.prop('disabled')) {
                // Scroll ke field (scroll ke comparison row jika ada)
                const comparisonRow = field.closest('.field-comparison').find('.comparison-row');
                if (comparisonRow.length > 0 && comparisonRow.is(':visible')) {
                    $('html, body').animate({
                        scrollTop: comparisonRow.offset().top - 150
                    }, 600);
                } else {
                    $('html, body').animate({
                        scrollTop: field.offset().top - 150
                    }, 600);
                }

                // Fokus ke field setelah animasi
                setTimeout(() => {
                    // Fokus ke comparison row jika ada, atau langsung ke field
                    if (comparisonRow.length > 0 && comparisonRow.is(':visible')) {
                        // Highlight comparison row
                        comparisonRow.css({
                            'border': '3px solid #007bff',
                            'box-shadow': '0 0 15px rgba(0, 123, 255, 0.6)',
                            'background': 'linear-gradient(to right, #fff3cd 0%, #fff3cd 48%, #d1ecf1 52%, #d1ecf1 100%)'
                        });

                        // Hapus highlight setelah 3 detik
                        setTimeout(() => {
                            comparisonRow.css({
                                'border': '2px solid #dee2e6',
                                'box-shadow': '0 2px 4px rgba(0,0,0,0.1)',
                                'background': 'linear-gradient(to right, #fff3cd 0%, #fff3cd 48%, #d4edda 52%, #d4edda 100%)'
                            });
                        }, 3000);
                    }

                    // Fokus ke field input
                    field.focus();

                    // Highlight field dengan border (jika belum di-highlight oleh comparison row)
                    if (comparisonRow.length === 0 || !comparisonRow.is(':visible')) {
                        field.css({
                            'border': '3px solid #007bff',
                            'box-shadow': '0 0 10px rgba(0, 123, 255, 0.5)'
                        });

                        // Hapus highlight setelah 2 detik
                        setTimeout(() => {
                            field.css({
                                'border': '',
                                'box-shadow': ''
                            });
                        }, 2000);
                    }
                }, 600);
            }
        }
    }

    // Format tempat lahir saat mengetik
    $('#editTempatLahirSantri').on('input', function() {
        let value = $(this).val();
        // Convert to title case (huruf kapital di awal setiap kata)
        value = value.toLowerCase().replace(/\b\w/g, function(l) {
            return l.toUpperCase();
        });
        $(this).val(value);
    });

    // Format nama santri saat mengetik
    $('#editNamaSantri').on('input', function() {
        let value = $(this).val();
        // Convert to title case (huruf kapital di awal setiap kata)
        value = value.toLowerCase().replace(/\b\w/g, function(l) {
            return l.toUpperCase();
        });
        $(this).val(value);
    });

    // Format nama ayah saat mengetik
    $('#editNamaAyah').on('input', function() {
        let value = $(this).val();
        // Convert to title case (huruf kapital di awal setiap kata)
        value = value.toLowerCase().replace(/\b\w/g, function(l) {
            return l.toUpperCase();
        });
        $(this).val(value);
    });

    // Event handler untuk checkbox konfirmasi
    // Gunakan event delegation untuk memastikan event handler selalu terikat
    $(document).off('change', '#editConfirmSave').on('change', '#editConfirmSave', function() {
        const submitButton = $('#formEditPeserta button[type="submit"]');
        const isChecked = $(this).is(':checked');

        console.log('Checkbox changed:', isChecked);
        console.log('Submit button before:', submitButton.prop('disabled'));

        if (isChecked) {
            submitButton.prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
            console.log('Submit button enabled');
        } else {
            submitButton.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
            console.log('Submit button disabled');
        }

        console.log('Submit button after:', submitButton.prop('disabled'));
    });

    // Fungsi untuk menampilkan informasi kartu keluarga
    function displayKartuKeluargaInfo(data) {
        // Kartu Keluarga Santri
        if (data.FileKkSantri && data.FileKkSantri.trim() !== '') {
            $('#kkSantriInfo').html(`
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-pdf text-danger mr-2"></i>
                    <a href="<?= base_url('uploads/santri/') ?>${data.FileKkSantri}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download"></i> Lihat File
                    </a>
                </div>
            `);
        } else {
            $('#kkSantriInfo').html('<span class="text-muted">Tidak ada file</span>');
        }

        // Kartu Keluarga Ayah
        if (data.FileKkAyah && data.FileKkAyah.trim() !== '') {
            $('#kkAyahInfo').html(`
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-pdf text-danger mr-2"></i>
                    <a href="<?= base_url('uploads/santri/') ?>${data.FileKkAyah}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download"></i> Lihat File
                    </a>
                </div>
            `);
        } else {
            $('#kkAyahInfo').html('<span class="text-muted">Tidak ada file</span>');
        }

        // Kartu Keluarga Ibu
        if (data.FileKkIbu && data.FileKkIbu.trim() !== '') {
            $('#kkIbuInfo').html(`
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-pdf text-danger mr-2"></i>
                    <a href="<?= base_url('uploads/santri/') ?>${data.FileKkIbu}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download"></i> Lihat File
                    </a>
                </div>
            `);
        } else {
            $('#kkIbuInfo').html('<span class="text-muted">Tidak ada file</span>');
        }

        // Kartu Keluarga Wali
        if (data.FileKkWali && data.FileKkWali.trim() !== '') {
            $('#kkWaliInfo').html(`
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-pdf text-danger mr-2"></i>
                    <a href="<?= base_url('uploads/santri/') ?>${data.FileKkWali}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download"></i> Lihat File
                    </a>
                </div>
            `);
        } else {
            $('#kkWaliInfo').html('<span class="text-muted">Tidak ada file</span>');
        }
    }

    // Event handler untuk form edit
    $('#formEditPeserta').on('submit', function(e) {
        e.preventDefault();

        // Validasi checkbox konfirmasi
        if (!$('#editConfirmSave').is(':checked')) {
            Swal.fire({
                icon: 'warning',
                title: 'Konfirmasi Diperlukan!',
                text: 'Silakan centang kotak konfirmasi untuk melanjutkan penyimpanan data.',
                confirmButtonText: 'OK'
            });
            return false;
        }

        // Tampilkan loading
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Sedang menyimpan perubahan data',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // Siapkan data untuk dikirim (termasuk IdPeserta dan StatusVerifikasi jika ada)
        let dataToSend = {};

        // Cek apakah tabel perbaikan visible
        const tablePerbaikanVisible = $('#tablePerbaikanData').is(':visible');

        // Map field name dari tabel perbaikan ke form field name
        const fieldNameMap = {
            'nama': 'NamaSantri',
            'tempatLahir': 'TempatLahirSantri',
            'tanggalLahir': 'TanggalLahirSantri',
            'jenisKelamin': 'JenisKelamin',
            'namaAyah': 'NamaAyah'
        };

        if (tablePerbaikanVisible) {
            // Jika tabel perbaikan visible, kita perlu mengirim SEMUA field yang diperlukan
            // Gunakan nilai dari kolom "Perbaikan (Sesudah)" jika diisi, atau nilai asli jika kosong

            // Ambil data asli dari hidden fields atau dari data yang sudah di-load sebelumnya
            // Kita akan gunakan nilai dari form fields yang di-hide sebagai fallback
            const originalData = {
                'NamaSantri': $('#editNamaSantri').val() || '',
                'TempatLahirSantri': $('#editTempatLahirSantri').val() || '',
                'TanggalLahirSantri': $('#editTanggalLahirSantri').val() || '',
                'JenisKelamin': $('#editJenisKelamin').val() || '',
                'NamaAyah': $('#editNamaAyah').val() || ''
            };

            // Inisialisasi dengan data asli menggunakan Object.assign
            dataToSend = Object.assign({}, originalData);

            // Override dengan nilai dari kolom "Perbaikan (Sesudah)" jika diisi
            $('.perbaikan-input').each(function() {
                const field = $(this).data('field');
                const value = $(this).val();

                // Jika ada value di kolom perbaikan, gunakan nilai tersebut
                if (fieldNameMap[field] && value && value.trim() !== '') {
                    dataToSend[fieldNameMap[field]] = value.trim();
                }
                // Jika tidak ada value, tetap gunakan nilai asli (sudah di-set di atas)
            });
        } else {
            // Jika tabel perbaikan tidak visible, ambil dari form fields (edit normal)
            const fieldsToCheck = [{
                    name: 'NamaSantri',
                    selector: '#editNamaSantri'
                },
                {
                    name: 'TempatLahirSantri',
                    selector: '#editTempatLahirSantri'
                },
                {
                    name: 'TanggalLahirSantri',
                    selector: '#editTanggalLahirSantri'
                },
                {
                    name: 'JenisKelamin',
                    selector: '#editJenisKelamin'
                },
                {
                    name: 'NamaAyah',
                    selector: '#editNamaAyah'
                }
            ];

            fieldsToCheck.forEach(function(fieldInfo) {
                const field = $(fieldInfo.selector);
                if (field.length > 0 && !field.prop('disabled')) {
                    const value = field.val();
                    if (value !== null && value !== undefined && value !== '') {
                        dataToSend[fieldInfo.name] = value;
                    }
                }
            });
        }

        // Selalu kirim IdSantri, IdPeserta, dan StatusVerifikasi jika ada
        const idSantri = $('#editIdSantri').val();
        if (idSantri) {
            dataToSend['IdSantri'] = idSantri;
        }

        const idPeserta = $('#editIdPeserta').val();
        if (idPeserta) {
            dataToSend['IdPeserta'] = idPeserta;
        }

        const statusVerifikasi = $('#editStatusVerifikasi').val();
        if (statusVerifikasi) {
            dataToSend['StatusVerifikasi'] = statusVerifikasi;
        }

        // AJAX untuk update data
        $.ajax({
            url: '<?= base_url('backend/munaqosah/update-santri') ?>',
            type: 'POST',
            data: dataToSend,
            dataType: 'json',
            timeout: 30000, // 30 detik timeout
            beforeSend: function(xhr) {
                // Debug: Tampilkan data yang akan dikirim
                console.log('Data yang akan dikirim:', dataToSend);
                console.log('Tabel perbaikan visible:', tablePerbaikanVisible);
                console.log('IdSantri:', $('#editIdSantri').val());
                console.log('IdPeserta:', $('#editIdPeserta').val());
                console.log('StatusVerifikasi:', $('#editStatusVerifikasi').val());

                // Validasi: Pastikan semua field required ada
                const requiredFields = ['IdSantri', 'NamaSantri', 'TempatLahirSantri', 'TanggalLahirSantri', 'JenisKelamin', 'NamaAyah'];
                const missingFields = requiredFields.filter(field => !dataToSend[field] || dataToSend[field].toString().trim() === '');

                if (missingFields.length > 0) {
                    console.error('Field yang belum diisi:', missingFields);
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Tidak Lengkap',
                        html: `Field berikut belum diisi atau kosong:<br><strong>${missingFields.join(', ')}</strong><br><br>Silakan lengkapi semua field yang diperlukan.`,
                        confirmButtonText: 'OK'
                    });
                    return false; // Stop AJAX request
                }

                if (tablePerbaikanVisible) {
                    console.log('Data diambil dari tabel perbaikan dan form fields (data asli)');
                    console.log('Data asli dari form fields:', {
                        'NamaSantri': $('#editNamaSantri').val(),
                        'TempatLahirSantri': $('#editTempatLahirSantri').val(),
                        'TanggalLahirSantri': $('#editTanggalLahirSantri').val(),
                        'JenisKelamin': $('#editJenisKelamin').val(),
                        'NamaAyah': $('#editNamaAyah').val()
                    });
                } else {
                    console.log('Data diambil dari form fields (edit normal)');
                }
            },
            success: function(response) {
                Swal.close();

                if (response.success) {
                    // Cek apakah ada perubahan atau tidak
                    if (response.no_changes) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Tidak Ada Perubahan',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Tutup modal
                            $('#modalEditPeserta').modal('hide');
                        });
                    } else {
                        // Ada perubahan, tampilkan detail perubahan dalam tabel
                        var changeMessage = response.message;
                        var changeTable = '';

                        if (response.changes) {
                            // Parse changes dari string ke array
                            var changes = response.changes.split('<br>');
                            changeTable = '<br><br><div class="table-responsive"><table class="table table-bordered table-sm">';
                            changeTable += '<thead class="thead-light"><tr><th style="width: 30%;">Field</th><th style="width: 35%;" class="text-danger">Before</th><th style="width: 35%;" class="text-success">After</th></tr></thead>';
                            changeTable += '<tbody>';

                            changes.forEach(function(change) {
                                if (change.trim()) {
                                    // Parse format: "Field: 'old' → 'new'"
                                    var match = change.match(/^(.+?):\s*'(.+?)'\s*→\s*'(.+?)'$/);
                                    if (match) {
                                        var field = match[1];
                                        var before = match[2];
                                        var after = match[3];

                                        changeTable += '<tr>';
                                        changeTable += '<td><strong>' + field + '</strong></td>';
                                        changeTable += '<td class="text-danger"><span class="badge badge-danger">' + before + '</span></td>';
                                        changeTable += '<td class="text-success"><span class="badge badge-success">' + after + '</span></td>';
                                        changeTable += '</tr>';
                                    }
                                }
                            });

                            changeTable += '</tbody></table></div>';
                        }

                        if (response.change_count) {
                            changeTable += '<div class="alert alert-info mt-2"><i class="fas fa-info-circle"></i> Total <strong>' + response.change_count + '</strong> field yang diperbarui</div>';
                        }

                        let successMessage = changeMessage + changeTable;
                        if (response.verifikasi_confirmed) {
                            successMessage += '<br><div class="alert alert-success mt-3"><i class="fas fa-check-circle"></i> <strong>Status verifikasi telah dikonfirmasi dan diubah menjadi Valid!</strong></div>';
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: successMessage,
                            width: '700px',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Tutup modal
                            $('#modalEditPeserta').modal('hide');
                            // Reload halaman untuk update data
                            location.reload();
                        });
                    }
                } else {
                    var errorMessage = response.message || 'Gagal memperbarui data santri';
                    var detailedErrors = '';

                    if (response.detailed_errors && response.detailed_errors.length > 0) {
                        detailedErrors = '<br><br><div class="alert alert-danger"><strong><i class="fas fa-exclamation-triangle"></i> Detail Error:</strong><br>';
                        response.detailed_errors.forEach(function(error, index) {
                            detailedErrors += '<div class="mt-2"><span class="badge badge-danger">' + (index + 1) + '</span> ' + error + '</div>';
                        });
                        detailedErrors += '</div>';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: errorMessage + detailedErrors,
                        width: '600px'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                var errorMessage = 'Terjadi kesalahan pada server';
                var errorTitle = 'Error!';

                // Determine error message based on status
                if (status === 'timeout') {
                    errorMessage = 'Koneksi timeout saat menyimpan data. Silakan coba lagi.';
                    errorTitle = 'Timeout!';
                } else if (xhr.status === 404) {
                    errorMessage = 'Endpoint tidak ditemukan. Silakan hubungi administrator.';
                    errorTitle = 'Not Found!';
                } else if (xhr.status === 500) {
                    errorMessage = 'Terjadi kesalahan server. Silakan hubungi administrator.';
                    errorTitle = 'Server Error!';
                } else if (xhr.status === 0) {
                    errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                    errorTitle = 'Connection Error!';
                }

                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.detailed_errors && response.detailed_errors.length > 0) {
                        var detailedErrors = '<br><br><div class="alert alert-danger"><strong><i class="fas fa-exclamation-triangle"></i> Detail Error:</strong><br>';
                        response.detailed_errors.forEach(function(error, index) {
                            detailedErrors += '<div class="mt-2"><span class="badge badge-danger">' + (index + 1) + '</span> ' + error + '</div>';
                        });
                        detailedErrors += '</div>';
                        errorMessage += detailedErrors;
                    } else if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    // Jika tidak bisa parse JSON, gunakan error default
                }

                Swal.fire({
                    icon: 'error',
                    title: errorTitle,
                    html: `
                        <div class="text-left">
                            <p><strong>Pesan Error:</strong> ${errorMessage}</p>
                            <p><strong>Status:</strong> ${status}</p>
                            <p><strong>HTTP Code:</strong> ${xhr.status}</p>
                            <details class="mt-3">
                                <summary class="text-muted">Detail Teknis</summary>
                                <small class="text-muted">${error}</small>
                            </details>
                        </div>
                    `,
                    width: '600px',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Fungsi untuk konfirmasi perbaikan data
    function konfirmasiPerbaikan(id, namaSantri, keterangan, perbaikanData, dataSaatIni) {
        // Parse data perbaikan dari keterangan jika tidak ada parameter perbaikanData
        let perbaikanDataParsed = perbaikanData;
        let keteranganText = keterangan || '';

        // Jika perbaikanData null, coba parse dari keterangan
        if (!perbaikanDataParsed && keterangan && keterangan.includes('[Data Perbaikan JSON]')) {
            const jsonMatch = keterangan.match(/\[Data Perbaikan JSON\]\s*\n([\s\S]*)/);
            if (jsonMatch) {
                try {
                    perbaikanDataParsed = JSON.parse(jsonMatch[1].trim());
                    // Ambil hanya bagian text sebelum JSON
                    keteranganText = keterangan.split('[Data Perbaikan JSON]')[0].trim();
                } catch (e) {
                    // Jika gagal parse, gunakan keterangan asli
                }
            }
        }

        // Format HTML untuk menampilkan data perbaikan
        let perbaikanHTML = '';
        if (perbaikanDataParsed && dataSaatIni) {
            // Format tanggal untuk display
            const formatTanggal = (tanggalStr) => {
                if (!tanggalStr) return '-';
                try {
                    const dateObj = new Date(tanggalStr + 'T00:00:00');
                    if (!isNaN(dateObj.getTime())) {
                        const day = String(dateObj.getDate()).padStart(2, '0');
                        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                        const year = dateObj.getFullYear();
                        return `${day}-${month}-${year}`;
                    }
                } catch (e) {}
                return tanggalStr;
            };

            // Jika ada data perbaikan terstruktur, tampilkan dalam tabel
            const rows = [];

            if (perbaikanDataParsed.nama) {
                rows.push(`
                    <tr>
                        <td style="padding: 8px; font-weight: 600;">Nama</td>
                        <td style="padding: 8px; background-color: #fff3cd;">${dataSaatIni.nama || '-'}</td>
                        <td style="padding: 8px; background-color: #d4edda;">${perbaikanDataParsed.nama}</td>
                    </tr>
                `);
            }

            if (perbaikanDataParsed.jenisKelamin) {
                rows.push(`
                    <tr>
                        <td style="padding: 8px; font-weight: 600;">Jenis Kelamin</td>
                        <td style="padding: 8px; background-color: #fff3cd;">${dataSaatIni.jenisKelamin || '-'}</td>
                        <td style="padding: 8px; background-color: #d4edda;">${perbaikanDataParsed.jenisKelamin}</td>
                    </tr>
                `);
            }

            if (perbaikanDataParsed.tempatLahir) {
                rows.push(`
                    <tr>
                        <td style="padding: 8px; font-weight: 600;">Tempat Lahir</td>
                        <td style="padding: 8px; background-color: #fff3cd;">${dataSaatIni.tempatLahir || '-'}</td>
                        <td style="padding: 8px; background-color: #d4edda;">${perbaikanDataParsed.tempatLahir}</td>
                    </tr>
                `);
            }

            if (perbaikanDataParsed.tanggalLahir) {
                const tanggalSebelum = dataSaatIni.tanggalLahirDisplay || dataSaatIni.tanggalLahir || '-';
                const tanggalSesudah = formatTanggal(perbaikanDataParsed.tanggalLahir);
                rows.push(`
                    <tr>
                        <td style="padding: 8px; font-weight: 600;">Tanggal Lahir</td>
                        <td style="padding: 8px; background-color: #fff3cd;">${tanggalSebelum}</td>
                        <td style="padding: 8px; background-color: #d4edda;">${tanggalSesudah}</td>
                    </tr>
                `);
            }

            if (perbaikanDataParsed.namaAyah) {
                rows.push(`
                    <tr>
                        <td style="padding: 8px; font-weight: 600;">Nama Ayah</td>
                        <td style="padding: 8px; background-color: #fff3cd;">${dataSaatIni.namaAyah || '-'}</td>
                        <td style="padding: 8px; background-color: #d4edda;">${perbaikanDataParsed.namaAyah}</td>
                    </tr>
                `);
            }

            if (rows.length > 0) {
                perbaikanHTML = `
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto; margin-bottom: 15px;">
                        <table class="table table-bordered table-sm" style="font-size: 13px; margin-bottom: 0;">
                            <thead class="thead-light">
                                <tr>
                                    <th style="padding: 8px; width: 25%;">Field</th>
                                    <th style="padding: 8px; background-color: #fff3cd; width: 37.5%;">Data Sebelum</th>
                                    <th style="padding: 8px; background-color: #d4edda; width: 37.5%;">Data Sesudah</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rows.join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            }
        }

        Swal.fire({
            title: 'Konfirmasi Perbaikan Data',
            html: `
                <div class="text-left">
                    <p class="mb-3">Apakah Anda yakin ingin mengkonfirmasi perbaikan data untuk peserta <strong>${namaSantri}</strong>?</p>
                    ${perbaikanHTML}
                    ${keteranganText ? `
                        <div class="alert alert-info" style="margin-top: 15px;">
                            <strong>Keterangan dari User:</strong><br>
                            ${keteranganText.replace(/\n/g, '<br>')}
                        </div>
                    ` : ''}
                    <div class="form-group" style="margin-top: 15px;">
                        <label for="keteranganKonfirmasi">Keterangan Konfirmasi (Opsional):</label>
                        <textarea id="keteranganKonfirmasi" class="form-control" rows="3" 
                            placeholder="Tambahkan keterangan konfirmasi jika diperlukan"></textarea>
                    </div>
                    <div class="alert alert-success" style="margin-top: 15px;">
                        <i class="fas fa-info-circle"></i> Status akan diubah menjadi "Valid" setelah konfirmasi.
                    </div>
                </div>
            `,
            icon: 'question',
            width: '700px',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-check"></i> Ya, Konfirmasi',
            cancelButtonText: '<i class="fas fa-times"></i> Batal',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            allowOutsideClick: false,
            allowEscapeKey: false,
            preConfirm: () => {
                return document.getElementById('keteranganKonfirmasi').value.trim();
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang mengkonfirmasi perbaikan data',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Kirim request AJAX
                $.ajax({
                    url: '<?= base_url('backend/munaqosah/konfirmasi-perbaikan-peserta') ?>',
                    type: 'POST',
                    data: {
                        id: id,
                        keterangan: result.value || ''
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan saat mengkonfirmasi perbaikan'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat mengkonfirmasi perbaikan. Silakan coba lagi.'
                        });
                    }
                });
            }
        });
    }
</script>



<?= $this->endSection() ?>