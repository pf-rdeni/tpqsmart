<?= $this->extend('backend/template/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $page_title ?></h1>
        <a href="<?= base_url('backend/luckydraw/panitia/create') ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Panitia
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
            <h6 class="m-0 font-weight-bold text-primary">Daftar Panitia Lucky Draw</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Peran (Grup)</th>
                            <th>Kegiatan Ditugaskan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($panitia as $p) : ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc($p->fullname) ?></td>
                                <td><?= esc($p->username) ?></td>
                                <td>
                                    <?php if ($p->group_id == 10) : ?>
                                        <span class="badge badge-info">Panitia Pemenang</span>
                                    <?php elseif ($p->group_id == 11) : ?>
                                        <span class="badge badge-primary">Panitia Verifikasi</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($p->assigned_kegiatan) ?></td>
                                <td>
                                    <a href="<?= base_url('backend/luckydraw/panitia/edit/' . $p->id) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="<?= base_url('backend/luckydraw/panitia/delete/' . $p->id) ?>" method="post" class="d-inline">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus panitia ini?');">
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
