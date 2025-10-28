<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Nilai Munaqosah</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('backend/munaqosah/input-nilai') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Input Nilai Baru
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('success') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table id="tableNilai" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Peserta</th>
                                        <th>Nama Santri</th>
                                        <th>TPQ</th>
                                        <th>Juri</th>
                                        <th>Kategori Materi</th>
                                        <th>Tipe Ujian</th>
                                        <th>Nilai</th>
                                        <th>Catatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($nilai as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row->NoPeserta ?></td>
                                            <td><?= $row->NamaSantri ?? '-' ?></td>
                                            <td><?= $row->NamaTpq ?? '-' ?></td>
                                            <td><?= $row->NamaGuru ?? '-' ?></td>
                                            <td><?= $row->KategoriMateriUjian ?></td>
                                            <td>
                                                <span class="badge badge-<?= $row->TypeUjian == 'munaqosah' ? 'primary' : 'warning' ?>">
                                                    <?= ucfirst($row->TypeUjian) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $row->Nilai >= 80 ? 'success' : ($row->Nilai >= 60 ? 'warning' : 'danger') ?>">
                                                    <?= number_format($row->Nilai, 2) ?>
                                                </span>
                                            </td>
                                            <td><?= $row->Catatan ?? '-' ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('backend/munaqosah/edit-nilai/' . $row->id) ?>"
                                                        class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= base_url('backend/munaqosah/delete-nilai/' . $row->id) ?>"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#tableNilai').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#tableNilai_wrapper .col-md-6:eq(0)');
    });
</script>
<?= $this->endSection() ?>