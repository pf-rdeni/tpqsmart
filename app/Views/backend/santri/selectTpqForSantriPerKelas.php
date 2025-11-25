<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Pilih TPQ - Daftar Santri Per Kelas
            </h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                <strong>Informasi:</strong> Silakan pilih TPQ untuk melihat daftar santri per kelas.
            </div>
            
            <?php if (!empty($dataTpq)): ?>
                <div class="table-responsive">
                    <table id="tblTpq" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama TPQ</th>
                                <th>Alamat</th>
                                <th>Jumlah Santri</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($dataTpq as $tpq): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($tpq['NamaTpq'] ?? '-') ?></td>
                                    <td><?= esc($tpq['Alamat'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge badge-info"><?= number_format($tpq['JumlahSantri'] ?? 0) ?> Santri</span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('backend/santri/showSantriBaruPerKelasTpq/' . $tpq['IdTpq']) ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> <span class="d-none d-md-inline">Lihat Daftar</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Tidak ada data TPQ yang tersedia.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        if ($('#tblTpq').length > 0) {
            $('#tblTpq').DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "pageLength": 25,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>

