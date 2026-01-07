<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Kegiatan</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('backend/kegiatan-absensi/new') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Kegiatan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <table class="table table-bordered table-striped" id="table-kegiatan">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kegiatan</th>
                                <th>Tanggal & Waktu</th>
                                <th>Lingkup</th>
                                <th>Status Active</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kegiatan as $key => $item) : ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td><?= esc($item['NamaKegiatan']) ?></td>
                                    <td>
                                        <?= date('d M Y', strtotime($item['Tanggal'])) ?><br>
                                        <small><?= date('H:i', strtotime($item['JamMulai'])) ?> - <?= date('H:i', strtotime($item['JamSelesai'])) ?></small>
                                    </td>
                                    <td>
                                        <?php if($item['Lingkup'] == 'Umum'): ?>
                                            <span class="badge badge-info">Umum</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">TPQ (<?= $item['IdTpq'] ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input switch-active" id="activeSwitch<?= $item['Id'] ?>" data-id="<?= $item['Id'] ?>" <?= $item['IsActive'] ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="activeSwitch<?= $item['Id'] ?>"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('backend/kegiatan-absensi/' . $item['Id'] . '/edit') ?>" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <!-- Form Delete using helper or standard form -->
                                        <form action="<?= base_url('backend/kegiatan-absensi/' . $item['Id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash"></i>
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

    </section>

<script>
    $(function() {
        $('.switch-active').change(function() {
            var id = $(this).data('id');
            var isChecked = $(this).is(':checked');
            
            // If checking (turning ON), others might turn OFF, so we might reload or handle UI.
            // AJAX call
            $.ajax({
                url: '<?= base_url('backend/kegiatan-absensi/active') ?>/' + id,
                type: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Status kegiatan berhasil diubah.');
                        // Reload to reflect that others might have been deactivated
                        setTimeout(function(){ location.reload(); }, 500);
                    } else {
                        toastr.error('Gagal mengubah status.');
                    }
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>
