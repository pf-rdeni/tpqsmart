<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>

<div class="col-12">
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= $page_title; ?></h3>
            <div class="card-tools">
                <a href="<?= base_url('backend/absensi/link/new') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Buat Link Baru
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <?php if ($isAdmin): ?><th>Lembaga (TPQ)</th><?php endif; ?>
                        <?php if ($isAdmin): ?><th>Kel/Desa</th><?php endif; ?>
                        <th>Tahun Ajaran</th>
                        <th>Link Absensi</th>
                        <th>Dibuat Pada</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($links as $row) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <?php if ($isAdmin): ?><td><?= esc($row['NamaTpq']); ?></td><?php endif; ?>
                            <?php if ($isAdmin): ?><td><?= esc($row['KelurahanDesa'] ?? '-'); ?></td><?php endif; ?>
                            <td><?= esc($row['IdTahunAjaran']); ?></td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" value="<?= base_url('absensi/haskey/' . $row['HashKey']) ?>" readonly id="link-<?= $row['Id'] ?>">
                                    <span class="input-group-append">
                                        <!-- Copy -->
                                        <button type="button" class="btn btn-info btn-flat" onclick="copyToClipboard('link-<?= $row['Id'] ?>')" title="Salin Link">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <!-- Open Link -->
                                        <a href="<?= base_url('absensi/haskey/' . $row['HashKey']) ?>" target="_blank" class="btn btn-primary btn-flat" title="Buka Link">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <!-- Send WhatsApp -->
                                        <a href="https://wa.me/?text=<?= urlencode('Link Absensi Santri ' . $row['NamaTpq'] . ' (' . $row['IdTahunAjaran'] . '): ' . base_url('absensi/haskey/' . $row['HashKey'])) ?>" target="_blank" class="btn btn-success btn-flat" title="Kirim via WhatsApp" style="background-color: #25D366; border-color: #25D366;">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    </span>
                                </div>
                            </td>
                            <td><?= $row['CreatedAt'] ?></td>
                            <td>
                                <!-- Edit -->
                                <a href="<?= base_url('backend/absensi/link/edit/' . $row['Id']) ?>" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Regenerate Key (Optional Safety) -->
                                <a href="<?= base_url('backend/absensi/link/regenerate/' . $row['Id']) ?>" class="btn btn-secondary btn-sm" title="Reset Key" onclick="return confirm('Apakah anda yakin ingin me-reset Key? Link lama tidak akan bisa digunakan lagi.');">
                                    <i class="fas fa-sync-alt"></i>
                                </a>

                                <!-- Delete -->
                                <a href="<?= base_url('backend/absensi/link/delete/' . $row['Id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?');" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function copyToClipboard(elementId) {
  var copyText = document.getElementById(elementId);
  copyText.select();
  copyText.setSelectionRange(0, 99999); /* For mobile devices */
  document.execCommand("copy");
  alert("Link berhasil disalin: " + copyText.value);
}
</script>
<?= $this->endSection(); ?>
