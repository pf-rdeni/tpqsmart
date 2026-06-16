<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $page_title ?></h1>
        <a href="<?= base_url('backend/luckydraw/kegiatan/create') ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Kegiatan
        </a>
    </div>

    <?php if (session()->getFlashdata('message')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('message') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Kegiatan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kegiatan</th>
                            <th>Tanggal</th>
                            <th>Tempat</th>
                            <th>Range Kupon</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($kegiatan as $k) : ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc($k->nama_kegiatan) ?></td>
                                <td><?= date('d M Y', strtotime($k->tanggal_kegiatan)) ?></td>
                                <td><?= esc($k->tempat_pelaksanaan) ?></td>
                                <td><?= esc($k->kupon_min) ?> - <?= esc($k->kupon_max) ?></td>
                                <td>
                                    <select class="form-control form-control-sm status-selector <?= $k->status == 'active' ? 'bg-success text-white' : 'bg-secondary text-white' ?>" data-id="<?= $k->id ?>">
                                        <option value="active" <?= $k->status == 'active' ? 'selected' : '' ?> class="bg-white text-dark">Aktif</option>
                                        <option value="inactive" <?= $k->status == 'inactive' ? 'selected' : '' ?> class="bg-white text-dark">Tidak Aktif</option>
                                    </select>
                                </td>
                                <td>
                                    <a href="<?= base_url('backend/luckydraw/kegiatan/edit/' . $k->id) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="<?= base_url('backend/luckydraw/kegiatan/delete/' . $k->id) ?>" method="post" class="d-inline">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini? Data terkait mungkin akan terhapus atau kehilangan relasinya.');">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    $('.status-selector').change(function() {
        var selector = $(this);
        var id = selector.data('id');
        var status = selector.val();
        
        // Show loading state by disabling
        selector.prop('disabled', true);

        $.ajax({
            url: '<?= base_url('backend/luckydraw/kegiatan/updateStatus') ?>',
            type: 'POST',
            data: {
                id: id,
                status: status
            },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    // Update classes based on new status
                    if(status === 'active') {
                        selector.removeClass('bg-secondary').addClass('bg-success');
                    } else {
                        selector.removeClass('bg-success').addClass('bg-secondary');
                    }
                    
                    // Show small toast/alert if you have a library for it, or just ignore since visually changed.
                } else {
                    alert('Gagal mengubah status kegiatan.');
                    // Revert selection
                    selector.val(status === 'active' ? 'inactive' : 'active');
                }
            },
            error: function() {
                alert('Terjadi kesalahan koneksi server.');
                // Revert selection
                selector.val(status === 'active' ? 'inactive' : 'active');
            },
            complete: function() {
                selector.prop('disabled', false);
            }
        });
    });
});
</script>
<?= $this->endSection(); ?>
