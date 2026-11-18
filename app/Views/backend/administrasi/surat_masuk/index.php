<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex align-items-center justify-content-between flex-wrap">
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="fas fa-inbox text-success mr-2"></i>Daftar Arsip Surat Masuk
                    </h3>
                    <div class="card-tools ml-auto">
                        <a href="<?= base_url('backend/administrasi/surat-masuk/create') ?>" class="btn btn-success btn-sm font-weight-bold">
                            <i class="fas fa-plus mr-1"></i> Catat Surat Masuk Baru
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover" id="tableSuratMasuk">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th style="width: 110px;">No. Agenda</th>
                                <th>Asal Pengirim</th>
                                <th>Nomor Surat</th>
                                <th style="width: 100px;">Tgl Terima</th>
                                <th>Perihal / Hal</th>
                                <th style="width: 90px;" class="text-center">Disposisi</th>
                                <th style="width: 70px;" class="text-center">Berkas</th>
                                <th style="width: 110px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($suratMasukList as $s): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <span class="badge badge-secondary"><?= esc($s['NoAgenda'] ?: '-') ?></span>
                                    </td>
                                    <td>
                                        <strong><?= esc($s['AsalSurat'] ?: '-') ?></strong>
                                        <?php if ($isAdmin && !empty($s['NamaTpq'])): ?>
                                            <small class="d-block text-muted"><?= esc($s['NamaTpq']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($s['NomorSurat'] ?: '-') ?></td>
                                    <td><?= !empty($s['TanggalTerima']) ? date('d-m-Y', strtotime($s['TanggalTerima'])) : '-' ?></td>
                                    <td><?= esc($s['Hal'] ?: '-') ?></td>
                                    <td class="text-center">
                                        <?php if ($s['TotalDisposisi'] > 0): ?>
                                            <span class="badge badge-success"><i class="fas fa-check"></i> <?= $s['TotalDisposisi'] ?> Disposisi</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning text-dark">Belum Ada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($s['FileScan'])): ?>
                                            <a href="<?= base_url('uploads/surat_masuk/' . $s['FileScan']) ?>" target="_blank" class="btn btn-outline-info btn-xs" title="Lihat Berkas Scan">
                                                <i class="fas fa-file-alt"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('backend/administrasi/surat-masuk/detail/' . $s['Id']) ?>" class="btn btn-info" title="Detail & Disposisi">
                                                <i class="fas fa-share-square"></i> Disposisi
                                            </a>
                                            <button type="button" class="btn btn-danger btn-delete-surat-masuk" 
                                                    data-id="<?= $s['Id'] ?>" 
                                                    data-hal="<?= esc($s['Hal']) ?>" 
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    if ($.fn.DataTable) {
        $('#tableSuratMasuk').DataTable({
            responsive: true,
            language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json' }
        });
    }

    $('.btn-delete-surat-masuk').on('click', function() {
        let id = $(this).data('id');
        let hal = $(this).data('hal');
        Swal.fire({
            title: 'Hapus Surat Masuk?',
            text: 'Surat masuk perihal "' + hal + '" dan seluruh riwayat disposisi akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url('backend/administrasi/surat-masuk/delete') ?>/' + id, function(res) {
                    if (res.success) {
                        Swal.fire('Terhapus', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }, 'json');
            }
        });
    });
});
</script>

<?= $this->endSection(); ?>
