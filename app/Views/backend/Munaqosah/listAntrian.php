<?= $this->extend('backend/template/template') ?>

<?= $this->section('content') ?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Antrian Munaqosah</h3>
                        <div class="card-tools">
                            <a href="<?= base_url('backend/munaqosah/input-antrian') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Antrian
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
                            <table id="tableAntrian" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Peserta</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Kategori Materi</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($antrian as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row['NoPeserta'] ?></td>
                                            <td><?= $row['IdTahunAjaran'] ?></td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?= $row['KategoriMateriUjian'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($row['Status']): ?>
                                                    <span class="badge badge-success">Selesai</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">Belum</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $row['Keterangan'] ?? '-' ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?php if (!$row['Status']): ?>
                                                        <form action="<?= base_url('backend/munaqosah/update-status-antrian/' . $row['id']) ?>"
                                                            method="post" style="display: inline;">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="status" value="1">
                                                            <button type="submit" class="btn btn-success btn-sm"
                                                                onclick="return confirm('Tandai sebagai selesai?')">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form action="<?= base_url('backend/munaqosah/update-status-antrian/' . $row['id']) ?>"
                                                            method="post" style="display: inline;">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="status" value="0">
                                                            <button type="submit" class="btn btn-warning btn-sm"
                                                                onclick="return confirm('Tandai sebagai belum selesai?')">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <a href="<?= base_url('backend/munaqosah/delete-antrian/' . $row['id']) ?>"
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
        $('#tableAntrian').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#tableAntrian_wrapper .col-md-6:eq(0)');
    });
</script>
<?= $this->endSection() ?>